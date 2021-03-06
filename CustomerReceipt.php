<?php

/* webERP 3.05 Revision: 14 $ */

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-25 09:37:22 -0500 (Thu, 25 Sep 2008) $
 * $Rev: 413 $
 */
/*
 * Choose
 */
include('includes/DefineReceiptClass.php');

$PageSecurity = 3;
include('includes/session.inc');

$title = _('Receipt Entry');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/DefineCustAllocsClass.php');
/*
if(!isset($_POST['TotalNumberOfAllocs'])){
	$_POST['TotalNumberOfAllocs']=0;
}
*/
?>
<script language="JavaScript" type="text/javascript">

//--> andres amaya diaz bowikaxu@gmail.com - september 2008 <---//
function amount_change(amount, textbox, id){
        var allocated = 0;
	//alert("Value: "+document.getElementById(id).checked);
	if(document.getElementById(id).checked==true){ //-- checked or unchecked? --//

        document.getElementById(textbox).readOnly = true;
		document.getElementById(textbox).value = amount;

                // iJPe
                // realhost     2010-01-24
                // Modificacion para evitar asignacion de pagos negativos y asignaciones negativas
                if (document.getElementById('amount').value < 0)
                {
                    document.getElementById(id).checked = false;
                    document.getElementById(textbox).readOnly = false;
                    document.getElementById(textbox).value = 0;
                    amount = 0;
                }

		var val = amount; //-- current value --//

                if (document.getElementById('allocated_amount').value.length == 0){
                    allocated = 0;
                }else{
                    allocated = document.getElementById('allocated_amount').value; //-- total allocated receipt value --//
                }

		if (document.getElementById('extra_amount').value.length == 0){
                    var extra = 0;
                }else{
		var extra = document.getElementById('extra_amount').value; //-- extra receipt value --//
                }

                if (document.getElementById('amount').value.length == 0){
                    var total = 0;
                }else{
		var total = document.getElementById('amount').value; //-- total receipt value --//
                }

                //  iJPe    realhost    Modificacion realizada a causa de error con javascript en algunas operaciones
                //  Se realizo un trancate ya que el error sucede con los decimales del 13 al 15, traen decimales de mas
                //  asi es que se realizo un truncate ya que los dos primeros decimales los trae correctos
                //allocated = (parseFloat(allocated)*1)-(parseFloat(val)*1);
		allocated = ((parseFloat(allocated)*100)+(parseFloat(val)*100))/100;
                allocated = Math.round(allocated*100)/100;


	}else {
		document.getElementById(textbox).readOnly = false;
		//var val = document.getElementById(textbox).value; //-- current value --//
                var val = amount; //-- current value --//
		document.getElementById(textbox).value = 0;
		allocated = document.getElementById('allocated_amount').value; //-- total allocated receipt value --//
		var extra = document.getElementById('extra_amount').value; //-- extra receipt value --//

                if (document.getElementById('amount').value.length == 0){
                    var total = 0;
                }else{
		var total = document.getElementById('amount').value; //-- total receipt value --//
                }

                //  iJPe    realhost    Modificacion realizada a causa de error con javascript en algunas operaciones
                //  Se realizo un trancate ya que el error sucede con los decimales del 13 al 15, traen decimales de mas
                //  asi es que se realizo un truncate ya que los dos primeros decimales los trae correctos
		//allocated = (parseFloat(allocated)*1)-(parseFloat(val)*1);
                allocated = ((parseFloat(allocated)*100)-(parseFloat(val)*100))/100;
                allocated = Math.round(allocated*100)/100;

	}
	document.getElementById('allocated_amount').value = allocated;
	// -- document.getElementById('amount').value = total; -- //

        // iJPe
        // realhost     2010-01-24
        // Modificacion para evitar asignacion de pagos negativos y asignaciones negativas
        if (!(document.getElementById('amount').value) || ((parseFloat(document.getElementById('amount').value) < parseFloat(document.getElementById('allocated_amount').value)) && ((document.getElementById('allocated_amount').value) != 0)))
        {
       		document.getElementById('amount').value = document.getElementById('allocated_amount').value;
       		total = document.getElementById('allocated_amount').value;
        }

    extra = ((parseFloat(total)*100)-(parseFloat(allocated)*100))/100;
    document.getElementById('extra_amount').value = extra;

}

function total_change(){

    if (document.getElementById('allocated_amount').value > 0 && document.getElementById('amount').value < 0)
    {
    	document.getElementById('amount').value = 0;
    }

	document.getElementById('amount').value = document.getElementById('amount').value.replace(',','');

	//iJPe
	if ((parseFloat(document.getElementById('amount').value) < parseFloat(document.getElementById('allocated_amount').value)) && ((document.getElementById('allocated_amount').value != 0)))
        {
            document.getElementById('amount').value = document.getElementById('allocated_amount').value;
        }

	var allocated = document.getElementById('allocated_amount').value; //-- total allocated receipt value --//
	var extra = document.getElementById('extra_amount').value; //-- extra receipt value --//
	var total = document.getElementById('amount').value; //-- total receipt value --//
	extra = (total*1)-(allocated*1);
	document.getElementById('extra_amount').value = extra;
}



</script>
<?php

$msg='';

// bowikaxu realhost March 2008 - cancel batch
if(isset($_POST['CancelBatch'])){
	unset($_SESSION['ReceiptBatch']);
}

if (isset($_POST['CommitBatch'])){

 /* once all receipts items entered, process all the data in the
  session cookie into the DB creating a single banktrans for the whole amount
  of all receipts in the batch and DebtorTrans records for each receipt item
  all DebtorTrans will refer to a single banktrans. A GL entry is created for
  each GL receipt entry and one for the debtors entry and one for the bank
  account debit

  NB allocations against debtor receipts are a seperate exercice

  first off run through the array of receipt items $_SESSION['ReceiptBatch']->Items and
  if GL integrated then create GL Entries for the GL Receipt items
  and add up the non-GL ones for posting to debtors later,
  also add the total discount total receipts*/

 // bowikaxu - Realhost - debug - 15 september 2008
 //echo $_SESSION['ReceiptBatch']->DateBanked."<BR><BR>";

 $PeriodNo = GetPeriod($_SESSION['ReceiptBatch']->DateBanked,$db);

   if ($_SESSION['CompanyRecord']==0){
	prnMsg(_('The company has not yet been set up properly') . ' - ' . _('this information is needed to process the batch') . '. ' . _('Processing has been cancelled'),'error');
	include('includes/footer.inc');
	exit;
   }

   /*Make an array of the defined bank accounts */
   $SQL = "SELECT accountcode FROM bankaccounts";
   $result = DB_query($SQL,$db);
   $BankAccounts = array();
   $i=0;
   while ($Act = DB_fetch_row($result)){
 	$BankAccounts[$i]= $Act[0];
	$i++;
   }

   /*Start a transaction to do the whole lot inside */
   $SQL = "BEGIN";
   $result = DB_query($SQL,$db);

   $BatchReceiptsTotal = 0;
   $BatchDiscount = 0;
   $BatchDebtorTotal = 0;

   foreach ($_SESSION['ReceiptBatch']->Items as $ReceiptItem) {

	    if ($ReceiptItem->GLCode !=''){
		if ($_SESSION['CompanyRecord']['gllink_debtors']==1){ /* then enter a GLTrans record */
			 $SQL = "INSERT INTO gltrans (type,
			 			typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount) ";
			 $SQL= $SQL . "VALUES (12,
			 			" . $_SESSION['ReceiptBatch']->BatchNo . ",
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						" . $PeriodNo . ",
						" . $ReceiptItem->GLCode . ",
						'" . DB_escape_string($ReceiptItem->Narrative) . "',
						" . -$ReceiptItem->Amount/$_SESSION['ReceiptBatch']->ExRate . ")";
			 $ErrMsg = _('Cannot insert a GL entry for the receipt because');
			 $DbgMsg = _('The SQL that failed to insert the receipt GL entry was');
			 $result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		}

		/*check to see if this is a GL posting to another bank account (or the same one)
		if it is then a matching payment needs to be created for this account too */

		if (in_array($ReceiptItem->GLCode, $BankAccounts)) {

			$PaymentTransNo = GetNextTransNo( 1, $db);
			$SQL="INSERT INTO banktrans (transno,
							type,
							bankact,
							ref,
							exrate,
							transdate,
							banktranstype,
							amount,
							currcode) ";
			$SQL= $SQL . "VALUES (" . $PaymentTransNo . ",
						1,
						" . $ReceiptItem->GLCode . ",
						'" . _('Act Transfer') .' - ' . DB_escape_string($ReceiptItem->Narrative) . "',
						" . $_SESSION['ReceiptBatch']->ExRate . " ,
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						'" . $_SESSION['ReceiptBatch']->ReceiptType . "',
						" . -$ReceiptItem->Amount . ",
						'" . $_SESSION['ReceiptBatch']->Currency . "'
					)";

			$DbgMsg = _('The SQL that failed to insert the bank transaction was');
			$ErrMsg = _('Cannot insert a bank transaction using the SQL');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
		}

	    } else {
		   /*Accumulate the total debtors credit including discount */
		   $BatchDebtorTotal = $BatchDebtorTotal + (($ReceiptItem->Discount + $ReceiptItem->Amount)/$_SESSION['ReceiptBatch']->ExRate);
		   /*Create a DebtorTrans entry for each customer deposit */

		   $SQL = "INSERT INTO debtortrans (transno,
		   					type,
							debtorno,
							branchcode,
							trandate,
							prd,
							reference,
							tpe,
							rate,
							ovamount,
							ovdiscount,
							invtext,
							rh_createdate,
							rh_id,
                            cobrador_id
							) ";
		   $SQL = $SQL . "VALUES (" . $_SESSION['ReceiptBatch']->BatchNo . ",
		   				12,
						'" . $ReceiptItem->Customer . "',
						'',
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						" . $PeriodNo . ",
						'" . DB_escape_string($ReceiptItem->ReceiptTypePartida  . " " . $ReceiptItem->Narrative) . "',
						'',
						" . $_SESSION['ReceiptBatch']->ExRate . ",
						" . -$ReceiptItem->Amount . ",
						" . -$ReceiptItem->Discount . ",
						'" . $ReceiptItem->PayeeBankDetail . "',
						NOW(),
						" . $ReceiptItem->ID . ",
                        '" . $ReceiptItem->Cobrador_id . "'
					)";
		$DbgMsg = _('The SQL that failed to insert the customer receipt transaction was');
		$ErrMsg = _('Cannot insert a receipt transaction against the customer because') ;
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		$SQL = "UPDATE debtorsmaster SET lastpaiddate = '" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						lastpaid=" . $ReceiptItem->Amount ."
					WHERE debtorsmaster.debtorno='" . $ReceiptItem->Customer . "'";

		$DbgMsg = _('The SQL that failed to update the date of the last payment received was');
		$ErrMsg = _('Cannot update the customer record for the date of the last payment received because');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		// --------------------------
		// bowikaxu realhost - do the allocations
		// it seems last insert id is not working on transactions (begin-commit)
		//$rcpt_id = DB_Last_Insert_ID($db,'debtortrans','id');

		$Result=DB_query('COMMIT',$db);
		$Result=DB_query('BEGIN',$db);
/***********************************************************************************************************************/
		$sql = "SELECT id FROM debtortrans WHERE type = 12 and transno = ".$_SESSION['ReceiptBatch']->BatchNo." AND debtortrans.debtorno = '".$ReceiptItem->Customer."' AND debtortrans.rh_id = '".$ReceiptItem->ID."'";
/***********************************************************************************************************************/
		$id_res = DB_query($sql,$db);
		$id_info = DB_fetch_array($id_res);

		$_SESSION['Alloc'] = new Allocation;
		// bowikaxu realhost - may 2007 - not allowed to allocate a cancelled invoice
		$SQL= "SELECT systypes.typename,
				debtortrans.type,
				debtortrans.transno,
				debtortrans.trandate,
				debtortrans.debtorno,
				debtorsmaster.name,
				rate,
				(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) as total,
				debtortrans.diffonexch,
				debtortrans.alloc,
				debtortrans.rh_id
			FROM debtortrans,
				systypes,
				debtorsmaster
			WHERE debtortrans.type = systypes.typeid
			AND debtortrans.debtorno = debtorsmaster.debtorno
			AND debtortrans.rh_status != 'C'
			AND debtortrans.id=" . $id_info['id'];
		$ErrMsg = _('There was a problem retrieving the information relating the transaction selected') . '. ' . _('Allocations are unable to proceed') . '.';
		$DbgMsg = _('The following SQL to delete the allocation record was used');
		$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

		$myrow = DB_fetch_array($Result);
		// bowikaxu realhost Feb 2008 - not a valid transaction or not conciliated
		if(DB_num_rows($Result)<1){
			prnMsg(_('There was a problem retrieving the information relating the transaction selected').'<BR>'._("Transaction not found or Not conciliated") ,'error');
		   $InputError=1;
		   //include('includes/footer.inc');
		   //exit;
		}

		$_SESSION['Alloc']->AllocTrans = $id_info['id'];
		$_SESSION['Alloc']->DebtorNo = $myrow['debtorno'];
		$_SESSION['Alloc']->CustomerName = $myrow['name'];
		$_SESSION['Alloc']->TransType = $myrow['type'];
		$_SESSION['Alloc']->TransTypeName = $myrow['typename'];
		$_SESSION['Alloc']->TransNo = $myrow['transno'];
		$_SESSION['Alloc']->TransExRate = $myrow['rate'];
		$_SESSION['Alloc']->TransAmt = $myrow['total'];
		$_SESSION['Alloc']->PrevDiffOnExch = $myrow['diffonexch'];
		$_SESSION['Alloc']->TransDate = ConvertSQLDate($myrow['trandate']);
/***********************************************************************************************************************/
		$_SESSION['Alloc']->rh_ID = $myrow['rh_id'];
/***********************************************************************************************************************/
		//print_r($_SESSION['Alloc']);

		    $SQL= "SELECT debtortrans.id,
				typename,
				transno,
				trandate,
				rate,
				ovamount+ovgst+ovfreight+ovdiscount as total,
				diffonexch,
				alloc
			FROM debtortrans,
				systypes
			WHERE debtortrans.type = systypes.typeid
			AND debtortrans.settled=0

			AND debtortrans.rh_status != 'C'

			AND debtorno='" . $_SESSION['Alloc']->DebtorNo . "'
			AND type = 10
			AND debtortrans.id IN (";
		    $do_query = 0;
/***********************************************************************************************************************/
		    foreach($_SESSION['ReceiptBatch']->Items AS $AllocInvoice){
				if($ReceiptItem->Customer == $AllocInvoice->Customer AND $ReceiptItem->ID == $AllocInvoice->ID){
					foreach($AllocInvoice->Invoices AS $invno){
						$SQL .= $invno.", ";
						$do_query++;
					}
				}
/***********************************************************************************************************************/
		    }
		    $SQL  = substr($SQL, 0, strlen($SQL)-2);

			$SQL .= ") ORDER BY debtortrans.trandate";
			//echo $SQL."<br>";

		    $ErrMsg = _('There was a problem retrieving the transactions available to allocate to');
		    $DbgMsg = _('The following SQL to delete the allocation record was used');
		    if($do_query>0){
		    	$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				while ($myrow=DB_fetch_array($Result)){

					// bowikaxu realhost - get external invoice number
					$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
					    		WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
					    		AND locations.loccode = rh_invoicesreference.loccode";
					    		$res3 = DB_query($sql,$db);
					    		$ext = DB_fetch_array($res3);

					$_SESSION['Alloc']->add_to_AllocsAllocn ($myrow['id'],
										$myrow['typename'],
										$myrow['transno'],
										ConvertSQLDate($myrow['trandate']),
										0,
										$myrow['total'],
										$myrow['rate'],
										$myrow['diffonexch'],
										$myrow['diffonexch'],
										$myrow['alloc'],
										'NA',
										$ext['rh_serie'].$ext['extinvoice']);
				}
		}

			// bowikaxu realhost - obtener el periodo y fecha antes del loop sino hay error
			//echo "DATE:   ".$_SESSION['Alloc']->TransDate ." DATE2: ".FormatDateForSQL($_SESSION['Alloc']->TransDate)."<br><br>";
		$PeriodNo = GetPeriod($_SESSION['Alloc']->TransDate, $db);
		$_SESSION['Alloc']->TransDate = FormatDateForSQL($_SESSION['Alloc']->TransDate);

		//$Result=DB_query('BEGIN',$db);
		$TotalAllocated = 0;
			foreach ($_SESSION['Alloc']->Allocs as $AllocnItem) {
				foreach($_SESSION['ReceiptBatch']->Items AS $AllocInvoice){
					//if($AllocInvoice->Inv_Amounts[$AllocnItem->ID]>0){
						//$allocamt = $AllocInvoice->Inv_Amounts[$myrow['id']];
/***********************************************************************************************************************/
					if($_SESSION['Alloc']->DebtorNo == $AllocInvoice->Customer AND $_SESSION['Alloc']->rh_ID == $AllocInvoice->ID){
						$AllocnItem->AllocAmt = $AllocInvoice->Inv_Amounts[$AllocnItem->ID];
					}
/***********************************************************************************************************************/
					//}
				}

				//echo "<hr>";
				//print_r($_SESSION['Alloc']);
				//echo "<hr>";

				 if ($AllocnItem->OrigAlloc >0 AND ($AllocnItem->OrigAlloc != $AllocnItem->AllocAmt)){
				  /*Orignial allocation was not 0 and it has now changed
				    need to delete the old allocation record */

					$SQL = 'DELETE FROM custallocns WHERE id = ' . $AllocnItem->PrevAllocRecordID;
	        			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The existing allocation for').' '. $AllocnItem->TransType .' '. $AllocnItem->TypeNo. ' ' . _('could not be deleted because');
	        			$DbgMsg = _('The following SQL to delete the allocation record was used');
					$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				 }
				 if ($AllocnItem->OrigAlloc != $AllocnItem->AllocAmt){
				 /*Only when there has been a change to the allocated amount
				 do we need to insert a new allocation record and update
				 the transaction with the new alloc amount and diff on exch */
					if ($AllocnItem->AllocAmt >0){
						$SQL = "INSERT INTO custallocns (datealloc,
						     					amt,
											transid_allocfrom,
											transid_allocto)
									VALUES ('" . FormatDateForSQL(date('d/m/Y')) . "',
										" . $AllocnItem->AllocAmt . ',
										' . $_SESSION['Alloc']->AllocTrans . ',
										' . $AllocnItem->ID . ')';

	           				$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The customer allocation record for').' '. $AllocnItem->TransType .' '. $AllocnItem->TypeNo. ' ' . _('could not be inserted because');
	           				$DbgMsg = _('The following SQL to delete the allocation record was used');
			         		$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
					}

					//$NewAllocTotal = $AllocnItem->PrevAlloc + $AllocnItem->AllocAmt;
					// bowikaxu realhost may 2007 - insert de los taxes, ya sean proporcionales o no
					$sql = "SELECT (typeno+1) AS typeno FROM systypes WHERE typeid = 0";
					$next_trans = DB_fetch_array(DB_query($sql,$db));

					$sql = "UPDATE systypes SET typeno = typeno+1 WHERE typeid = 0";
	           		DB_query($sql,$db);

					if($_SESSION['CashBase']==1){
					if($AllocnItem->AllocAmt-$AllocnItem->OrigAlloc >= $AllocnItem->TransAmount){	// se asigno el total o mas del valor total de la factura

						$sql = 'SELECT 	stockmovestaxes.taxauthid,
						stockmovestaxes.taxrate,
						SUM(stockmovestaxes.taxrate * stockmoves.price*-stockmoves.qty*(1-stockmoves.discountpercent)) AS impuesto
						FROM stockmoves,
						stockmovestaxes
						WHERE stockmoves.type=10
						AND stockmoves.transno=' . $AllocnItem->TypeNo . '
						AND stockmoves.show_on_inv_crds=1
						AND stockmovestaxes.stkmoveno = stockmoves.stkmoveno
						GROUP BY stockmovestaxes.taxrate';
						$res = DB_query($sql,$db);

						while($item = DB_fetch_array($res)){

							// obtiene las cuentas para el insert a gltrans
							$sql = "SELECT taxauthid, description AS taxname, taxontax,
									 taxauthorities.rh_purchtaxglcoderec, taxauthorities.rh_taxglcodepaid,
									 taxauthorities.purchtaxglaccount, taxauthorities.taxglcode
							FROM taxgrouptaxes INNER JOIN taxauthorities ON taxgrouptaxes.taxauthid=taxauthorities.taxid
							WHERE taxauthorities.taxid = ".$item['taxauthid']."
							ORDER BY calculationorder";
							$actsRes = DB_query($sql,$db);
							$acts = DB_fetch_array($actsRes);

							// insert a gltrans
							$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount, posted) VALUES (
							0,
							".$next_trans['typeno'].",
							'".$_SESSION['Alloc']->TransDate."',
							".$PeriodNo.",
							".$acts['taxglcode'].",
							'".$AllocnItem->TransType.' '. $AllocnItem->ExtInv.'('.$AllocnItem->TypeNo.') - '._('Quantity').' '.number_format($AllocnItem->AllocAmt-$AllocnItem->OrigAlloc,2,'.','').' - '._('Tax').' '.$acts['taxname'].' '.$item['impuesto']."',
							".$item['impuesto'].",1)";
							DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);

							// insert a gltrans
							$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount, posted) VALUES (
							0,
							".$next_trans['typeno'].",
							'".$_SESSION['Alloc']->TransDate."',
							".$PeriodNo.",
							".$acts['rh_taxglcodepaid'].",
							'".$AllocnItem->TransType.' '. $AllocnItem->ExtInv.'('.$AllocnItem->TypeNo.') - '._('Quantity').' '.number_format(($AllocnItem->AllocAmt-$AllocnItem->OrigAlloc),2,'.','').' - '._('Tax').' '.$acts['taxname'].' '.$item['impuesto']."',
							".-$item['impuesto'].",1)";
							DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);
							//echo "<HR>Se realizo el insert total<HR>".$SQL."<HR>";

							DB_free_result($actsRes);
						}
						DB_free_result($res);
					}else { // no se cubre el total de la factura

						$AllocTotal = $AllocnItem->AllocAmt-$AllocnItem->OrigAlloc;
						$sql = 'SELECT 	stockmovestaxes.taxauthid,
						stockmovestaxes.taxrate,
						SUM(stockmovestaxes.taxrate * stockmoves.price*-stockmoves.qty*(1-stockmoves.discountpercent)) AS impuesto,
						SUM(stockmoves.price * -stockmoves.qty*(1-stockmoves.discountpercent)) AS tot
						FROM stockmoves,
							stockmovestaxes
						WHERE stockmoves.type=10
						AND stockmoves.transno=' . $AllocnItem->TypeNo . '
						AND stockmoves.show_on_inv_crds=1
						AND stockmovestaxes.stkmoveno = stockmoves.stkmoveno
						GROUP BY stockmovestaxes.taxrate';
						$res = DB_query($sql,$db);

						while($item = DB_fetch_array($res)){

							// bowikaxu - get the partial part of tax to pay if taxrate > 0
							if($item['taxrate']>0){
								// obtiene las cuentas para el insert a gltrans
								$sql = "SELECT taxauthid, description AS taxname, calculationorder, taxontax,
										 taxauthorities.rh_purchtaxglcoderec, taxauthorities.rh_taxglcodepaid,
										 taxauthorities.purchtaxglaccount, taxauthorities.taxglcode
								FROM taxgrouptaxes INNER JOIN taxauthorities ON taxgrouptaxes.taxauthid=taxauthorities.taxid
								WHERE taxauthorities.taxid = ".$item['taxauthid']."
								ORDER BY calculationorder";
								$actsRes = DB_query($sql,$db);
								$acts = DB_fetch_array($actsRes);

								/*
								 * // bowikaxu - Realhost - 15 september 2008 - if the receipt is bigger than the subtotal + taxes, allocate proportional part
								if($AllocTotal >= ($item['tot']+$item['impuesto'])){

									// insert a gltrans
									$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (
									".$_SESSION['Alloc']->TransType.",
									".$_SESSION['Alloc']->TransNo.",
									'".$_SESSION['Alloc']->TransDate."',
									".$PeriodNo.",
									".$acts['rh_purchtaxglcoderec'].",
									'".$AllocnItem->TransType.' '. $AllocnItem->TypeNo.' - '._('Quantity').' '.$AllocTotal.' - '._('Tax').' '.$acts['taxname'].' '.$item['impuesto']."',
									".$item['impuesto'].")";
									DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);

									$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (
									".$_SESSION['Alloc']->TransType.",
									".$_SESSION['Alloc']->TransNo.",
									'".$_SESSION['Alloc']->TransDate."',
									".$PeriodNo.",
									".$acts['purchtaxglaccount'].",
									'".$AllocnItem->TransType.' '. $AllocnItem->TypeNo.' - '._('Quantity').' '.$AllocTotal.' - '._('Tax').' '.$acts['taxname'].' '.$item['impuesto']."',
									".-$item['impuesto'].")";
									//DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);
									echo "<HR>Se realizo el insert por el subtotal mas el iva<HR>".$SQL."<HR>";
									DB_free_result($actsRes);

							}else { */// bowikaxu - no se alcanzo a pagar el subtotal + iva, se pone la parte proporcional

									$factor = ($item['impuesto']/($item['tot']+$item['impuesto']));
									$ProportionalTax = ($factor*($AllocnItem->AllocAmt-$AllocnItem->OrigAlloc));
								// insert a gltrans
								$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount,posted) VALUES (
								0,
								".$next_trans['typeno'].",
								'".$_SESSION['Alloc']->TransDate."',
								".$PeriodNo.",
								".$acts['taxglcode'].",
								'".$AllocnItem->TransType.' '. $AllocnItem->ExtInv.'('.$AllocnItem->TypeNo.') - '._('Quantity').' '.number_format($AllocTotal,2,'.','').' - '._('Tax').' '.$acts['taxname'].' '.$ProportionalTax."',
								".$ProportionalTax.",1)";
								DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);

								$SQL = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount,posted) VALUES (
								0,
								".$next_trans['typeno'].",
								'".$_SESSION['Alloc']->TransDate."',
								".$PeriodNo.",
								".$acts['rh_taxglcodepaid'].",
								'".$AllocnItem->TransType.' '. $AllocnItem->ExtInv.'('.$AllocnItem->TypeNo.') - '._('Quantity').' '.number_format($AllocTotal,2,'.','').' - '._('Tax').' '.$acts['taxname'].' '.$ProportionalTax."',
								".-$ProportionalTax.",1)";
								DB_query($SQL,$db,'ERROR: AL intentar guardar los impuestos','',true);
								//echo "<HR>Se realizo el insert por la parte proporcional<HR>".$SQL."<HR>";
								DB_free_result($actsRes);
								$ProportionalTax = number_format($ProportionalTax,2);
							//}
						}
					}
					DB_free_result($res);
				}
				 }
				// bowikaxu realhost - 15 september 2008 - end of tax insert

					$sql = "SELECT rate FROM currencies WHERE currabrev = '".$_SESSION['CountryOfOperation']."'";
					$res2 = DB_query($sql,$db);
					$curr_rate = DB_fetch_array($res2);

					// TODO bowikaxu - verificar el ingreso de AllocAmt que sea el valor del textbox, no el total de la transaccion
					// bowikaxu realhost - 17 september 2008 - Allocation Amount
					$NewAllocTotal = $AllocnItem->PrevAlloc + $AllocnItem->AllocAmt;
					$AllocTotal = $AllocnItem->AllocAmt-$AllocnItem->OrigAlloc;
					$AllocTotal = $AllocTotal*($curr_rate['rate']/$AllocnItem->ExRate);
					$rh_narrative = _('Clearance').' '.$_SESSION['Alloc']->TransTypeName.' '.$_SESSION['Alloc']->TransNo.' '._('To').' '.$AllocnItem->TransType.' Id:'.$AllocnItem->ID;
					// bowikaxu realhost Feb 2008 - si es desasignacion, hacer inserts por el total anterior
					if($NewAllocTotal <= 0){
						$alloc = $AllocnItem->PrevAlloc;
					}else {
						$alloc = $AllocnItem->AllocAmt;
					}

					// bowikaxu realhost - February 2008 - kill gl entry, advanced debtors account
					// de tipo 0 Journal - GL
					$SQL = 'INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (0,
								' . $next_trans['typeno'] . ",
								'" . FormatDateForSQL(Date($_SESSION['DefaultDateFormat'])) . "',
								" . $PeriodNo . ',
								' . $_SESSION['CompanyRecord']['debtorsact'] . ",
								'".$rh_narrative."',
								" . -$AllocTotal . ')';

	           			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Matar el Asiento de Anticipo');
	           			$DbgMsg = _('The following SQL to delete the allocation record was used');
	           			$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	           			$SQL = 'INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (0,
								' . $next_trans['typeno'] . ",
								'" . FormatDateForSQL(Date($_SESSION['DefaultDateFormat'])) . "',
								" . $PeriodNo . ',
								' . $_SESSION['CompanyRecord']['rh_advdebtorsact'] . ",
								'".$rh_narrative."',
								" . $AllocTotal . ')';

	           			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('El Asiento de Anticipo no se pudo insertar');
	           			$DbgMsg = _('The following SQL to delete the allocation record was used');
	           			$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

					if (abs($NewAllocTotal-$AllocnItem->TransAmount) < 0.005){
						$Settled =1;
					} else {
						$Settled =0;
					}

					$SQL = 'UPDATE debtortrans SET diffonexch=' . $AllocnItem->DiffOnExch . ',
									alloc = ' . $NewAllocTotal . ',
									settled = ' . $Settled . '
							WHERE id = ' . $AllocnItem->ID;

					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor transaction record could not be modified for the allocation against it because');
	        			$DbgMsg = _('The following SQL to delete the allocation record was used');
	        			$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				 } /*end if the new allocation is different to what it was before */

				 // bowikaxu - Realhost - 15 september 2008 - calculate total allocated, from the allocations amounts
				 $TotalAllocated += $AllocnItem->AllocAmt;
			}  /*end of the loop through the array of allocations made */

			/*Now update the receipt or credit note with the amount allocated
			and the new diff on exchange */

			if (abs($TotalAllocated+$_SESSION['Alloc']->TransAmt)<0.01){
			   $Settled = 1;
			} else {
			   $Settled = 0;
			}

			$SQL = 'UPDATE debtortrans SET alloc = ' .  -$TotalAllocated . ', diffonexch = ' . -$TotalDiffOnExch . ', settled=' . $Settled . ' WHERE id = ' . $_SESSION['Alloc']->AllocTrans;

	     		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The debtor receipt or credit note transaction could not be modified for the new allocation and exchange difference because');
	     		$DbgMsg = _('The following SQL to delete the allocation record was used');
	     		$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			/*Almost there ... if there is a change in the total diff on exchange
			 and if the GLLink to debtors is active - need to post diff on exchange to GL */
			$MovtInDiffOnExch = -$_SESSION['Alloc']->PrevDiffOnExch - $TotalDiffOnExch;

			if ($MovtInDiffOnExch !=0){

				if ($_SESSION['CompanyRecord']['gllink_debtors']==1){

					//$PeriodNo = GetPeriod($_SESSION['Alloc']->TransDate, $db);

					//$_SESSION['Alloc']->TransDate = FormatDateForSQL($_SESSION['Alloc']->TransDate);

			    		$SQL = 'INSERT INTO gltrans (type,
			      					typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount)
						VALUES (' . $_SESSION['Alloc']->TransType . ',
							' . $_SESSION['Alloc']->TransNo . ",
							'" . $_SESSION['Alloc']->TransDate . "',
							" . $PeriodNo . ',
							' . $_SESSION['CompanyRecord']['exchangediffact'] . ",
							'',
							" . $MovtInDiffOnExch . ')';

	           			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The GL entry for the difference on exchange arising out of this allocation could not be inserted because');
	           			$DbgMsg = _('The following SQL to delete the allocation record was used');
	           			$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			      		$SQL = 'INSERT INTO gltrans (type,
									typeno,
									trandate,
									periodno,
									account,
									narrative,
									amount)
							VALUES (' . $_SESSION['Alloc']->TransType . ',
								' . $_SESSION['Alloc']->TransNo . ",
								'" . $_SESSION['Alloc']->TransDate . "',
								" . $PeriodNo . ',
								' . $_SESSION['CompanyRecord']['debtorsact'] . ",
								'',
								" . -$MovtInDiffOnExch . ')';

	           			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The GL entry for the difference on exchange arising out of this allocation could not be inserted because');
	           			$DbgMsg = _('The following SQL to delete the allocation record was used');
	           			$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
				}

			}

		 /* OK Commit the transaction */
			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The updates and insertions arising from this allocation could not be committed to the database');
	      		$DbgMsg = _('The following SQL to delete the allocation record was used');
	      		//$Result=DB_query('COMMIT',$db,$ErrMsg,$DbgMsg,true);
		/*finally delete the session variables holding all the previous data */
			unset($_SESSION['Alloc']);
			unset($_POST['AllocTrans']);
		// bowikaxu - fin allocations
		// ------------------------------------------------------------------------------
			// ------------------------------------------------------------------------------
				// ------------------------------------------------------------------------------
					// ------------------------------------------------------------------------------
						// ------------------------------------------------------------------------------


	    }
	    $BatchDiscount = $BatchDiscount + $ReceiptItem->Discount/$_SESSION['ReceiptBatch']->ExRate;
	    $BatchReceiptsTotal = $BatchReceiptsTotal + $ReceiptItem->Amount/$_SESSION['ReceiptBatch']->ExRate;
   }

   if ($_SESSION['CompanyRecord']['gllink_debtors']==1){ /* then enter GLTrans records for discount, bank and debtors */

	if ($BatchReceiptsTotal!=0){

		// bowikaxu realhost
		if($_SESSION['ReceiptBatch']->Currency != $_SESSION['CompanyRecord']['currencydefault']){
/*************************************************************************************************************************/
			if($_SESSION['ReceiptBatch']->Currency == $_SESSION['ReceiptBatch']->BankAccountCur){
				$sql = "SELECT rh_acctcomplementary FROM bankaccounts WHERE accountcode = '".$_SESSION['ReceiptBatch']->Account."'";
				$res = DB_query($sql,$db);
				if(DB_num_rows($res)<=0){
					prnMsg("ERROR: No existe la cuenta complementaria para esta cuenta de banco, imposible realizar el pago.",'error');
					DB_query('ROLLBACK',$db);
					include('includes/footer.inc');
				}
				$complem = DB_fetch_array($res);

				$SQL="INSERT INTO gltrans (type,
						typeno,
						trandate,
						periodno,
						account,
						narrative,
						amount)
				VALUES (12,
					" . $_SESSION['ReceiptBatch']->BatchNo . ",
					'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
					" . $PeriodNo . ",
					" . $complem['rh_acctcomplementary'] . ",
					'" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative.'1') . "',
					" .($BatchReceiptsTotal-($BatchReceiptsTotal * $_SESSION["ReceiptBatch"]->ExRate)) . "
				)";
				$DbgMsg = _('The SQL that failed to insert the GL transaction fro the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

				/* Bank account entry first */
				$SQL="INSERT INTO gltrans (type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount)
					VALUES (12,
						" . $_SESSION['ReceiptBatch']->BatchNo . ",
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						" . $PeriodNo . ",
						" . $_SESSION['ReceiptBatch']->Account . ",
						'" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative.'2') . "',
						" . ($BatchReceiptsTotal * $_SESSION["ReceiptBatch"]->ExRate) . "
					)";
				$DbgMsg = _('The SQL that failed to insert the GL transaction fro the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}else{
				/* Bank account entry first */
				$SQL="INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (12, " . $_SESSION['ReceiptBatch']->BatchNo . ", '" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "', " . $PeriodNo . ", " . $_SESSION['ReceiptBatch']->Account . ", '" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative." "._('Exchange Rate').": ".$_SESSION["ReceiptBatch"]->ExRate) . "', " .$BatchReceiptsTotal. ")";
				$DbgMsg = _('The SQL that failed to insert the GL transaction fro the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
			}
/*************************************************************************************************************************/
		}else {

				/* Bank account entry first */
				$SQL="INSERT INTO gltrans (type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount)
					VALUES (12,
						" . $_SESSION['ReceiptBatch']->BatchNo . ",
						'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
						" . $PeriodNo . ",
						" . $_SESSION['ReceiptBatch']->Account . ",
						'" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative) . "',
						" . $BatchReceiptsTotal . "
					)";
				$DbgMsg = _('The SQL that failed to insert the GL transaction fro the bank account debit was');
				$ErrMsg = _('Cannot insert a GL transaction for the bank account debit');
				$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

          }
                /*now enter the BankTrans entry */

                $SQL="INSERT INTO banktrans (type,
   				transno,
				bankact,
				ref,
				exrate,
				transdate,
				banktranstype,
				amount,
				currcode)
                VALUES (12,
                      " . $_SESSION['ReceiptBatch']->BatchNo . ",
                      " . $_SESSION['ReceiptBatch']->Account . ",
                      '" . $_SESSION['ReceiptBatch']->Narrative . "',
                      " . $_SESSION['ReceiptBatch']->ExRate . ",
                      '" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
                      '" . $_SESSION['ReceiptBatch']->ReceiptType . "',
                      " . ($BatchReceiptsTotal * $_SESSION["ReceiptBatch"]->ExRate) . ",
                      '" . $_SESSION['ReceiptBatch']->Currency . "'
                        )";
              $DbgMsg = _('The SQL that failed to insert the bank account transaction was');
              $ErrMsg = _('Cannot insert a bank transaction');
              $result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
      }
      if ($BatchDebtorTotal!=0){
		/* Now Credit Debtors account with receipts + discounts */
		// bowikaxu realhost Feb 2008 - insert a cuenta de anticipo no a clientes
		//$_SESSION['CompanyRecord']['debtorsact']
		$SQL="INSERT INTO gltrans ( type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (12,
				" . $_SESSION['ReceiptBatch']->BatchNo . ",
				'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
				" . $PeriodNo . ",
				" . $_SESSION['CompanyRecord']['rh_advdebtorsact'] . ",
					'" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative." "._('Exchange Rate').": ".$_SESSION["ReceiptBatch"]->ExRate) . "',
					" . -$BatchDebtorTotal . "
				)";
			$DbgMsg = _('The SQL that failed to insert the GL transaction for the debtors account credit was');
			$ErrMsg = _('Cannot insert a GL transaction for the debtors account credit');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

      }

      if ($BatchDiscount!=0){
			/* Now Debit Discount account with discounts allowed*/
		$SQL="INSERT INTO gltrans ( type,
					typeno,
					trandate,
					periodno,
					account,
					narrative,
					amount)
			VALUES (12,
				" . $_SESSION['ReceiptBatch']->BatchNo . ",
				'" . FormatDateForSQL($_SESSION['ReceiptBatch']->DateBanked) . "',
				" . $PeriodNo . ",
				" . $_SESSION['CompanyRecord']['pytdiscountact'] . ",
					'" . DB_escape_string($_SESSION['ReceiptBatch']->Narrative) . "',
				" . $BatchDiscount . "
			)";
		$DbgMsg = _('The SQL that failed to insert the GL transaction for the payment discount debit was');
		$ErrMsg = _('Cannot insert a GL transaction for the payment discount debit');
		$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
	}
   }

   $ErrMsg = _('Cannot commit the changes');
   $DbgMsg = _('The SQL that failed was');
   $result = DB_query('COMMIT',$db,$ErrMsg,$DbgMsg,true);

   // bowikaxu realhost - september 2008 - if fails
   if(DB_error_no($db) != 0){
   		// database error - delete debtortrans insert by id
   		if(isset($id_info['id']) && $id_info['id']>0){
   			$sql = "DELETE FROM debtortrans WHERE id = ".$id_info['id']."";
   			DB_query($sql,$db);
   		}
   }
   // bowikaxu - end if transaction fails

   echo '<P>';

   // bowikaxu realhost 26 june 2008 - removed $_SESSION['ReceiptBatch']->BatchNo
   echo "<FONT SIZE=3 COLOR=BLUE>" . $_SESSION['ReceiptBatch']->ReceiptType . " " . _('Batch') . ": <STRONG>" . $_SESSION['ReceiptBatch']->BatchNo . '</STRONG> - ' . _('Banked into the') . " " . $_SESSION['ReceiptBatch']->BankAccountName . " " . _('on') . " " . $_SESSION['ReceiptBatch']->DateBanked . "</FONT><BR><BR>";
   echo "<CENTER><TABLE><TR>
   			<td  class='tableheader'>"._('Customer Name')."</td>
   			<td  class='tableheader'>"._('Amount')."</td>
   			<tr>";
   $tot = 0;
   foreach ($_SESSION['ReceiptBatch']->Items as $ReceiptItem){

   		echo "<tr><td>" . $ReceiptItem->CustomerName . "</td><td align=right>" .  number_format($ReceiptItem->Amount,2) . "</td></tr>";
   		$tot += $ReceiptItem->Amount;

   }
   echo "<tr><td>"._('Total')."</td><td align=right><strong>".number_format($tot,2)."</strong></td></tr>";
   echo "</TABLE></CENTER><BR>";
   // bowikaxu realhost - END deposit notificacion

   prnMsg( _('Receipt batch') . ' ' . $_SESSION['ReceiptBatch']->BatchNo . ' ' . _('has been successfully entered into the database'),'success');

   echo "<BR><A target='_blank' HREF='" . $rootpath . "/PDFBankingSummary.php?BatchNo=" . $_SESSION['ReceiptBatch']->BatchNo . "'>" . _('Print PDF Batch Summary') . "</A>";

   // bowikaxu show upload link to this transaction
   echo "<BR><A HREF='".$rootpath."/rh_upload.php?".SID."&type=12&typeno=".$_SESSION['ReceiptBatch']->BatchNo."&comments=".$_SESSION['ReceiptBatch']->Narrative."'>"._('Upload File').' 	'."<IMG BORDER=0 width=24 height=24 SRC='".$rootpath.'/css/'.$theme.'/images/upload.gif'."'></A>";

   unset($_SESSION['ReceiptBatch']);
   unset($_SESSION['Alloc_Counter']);

} elseif (isset($_POST['BatchInput'])){ //submitted a new batch

/*Need to do a reality check on exchange rate entered initially to ensure sensible to proceed */
	if ($_POST['Currency']!=$_SESSION['CompanyRecord']['currencydefault'] AND $_POST['ExRate']==1){
		prnMsg(_('An exchange rate of 1 is only appropriate for receipts in the companies functional currency - enter an appropriate exchange rate'),'error');

	} else {

		$_POST['BatchNo'] = GetNextTransNo(12,$db);

		/*if the session already has a $_SESSION['ReceiptBatch'] set up ... lose it
		and start a fresh! */
		if (isset($_SESSION['ReceiptBatch'])){
			unset($_SESSION['ReceiptBatch']);
		}
		/*
		if(isset($_SESSION['Alloc_Counter'])){
			unset($_SESSION['Alloc_Counter']);
			echo "unset 1";
		}
		*/
		$_SESSION['ReceiptBatch'] = new Receipt_Batch;
		$_SESSION['ReceiptBatch']->BatchNo = $_POST['BatchNo'];
		$_SESSION['ReceiptBatch']->Account = $_POST['BankAccount'];
			if (!Is_Date($_POST['DateBanked'])){
			$_POST['DateBanked'] = Date($_SESSION['DefaultDateFormat']);
			}
		$_SESSION['ReceiptBatch']->DateBanked = $_POST['DateBanked'];
		$_SESSION['ReceiptBatch']->ExRate = $_POST['ExRate'];
		$_SESSION['ReceiptBatch']->ReceiptType = $_POST['ReceiptType'];
        $_SESSION['ReceiptBatch']->Cobrador_id = $_POST['cobrador_id'];
		$_SESSION['ReceiptBatch']->Currency = $_POST['Currency'];
		$_SESSION['ReceiptBatch']->Narrative = $_POST['BatchNarrative'];
		$_SESSION['ReceiptBatch']->ID = 1;

		$SQL = "SELECT bankaccountname, currcode FROM bankaccounts WHERE accountcode=" . $_POST['BankAccount'];
		$result= DB_query($SQL,$db,'','',false,false);

		if (DB_error_no($db) !=0) {
			prnMsg(_('The bank account name cannot be retrieved because') . ' - ' . DB_error_msg($db),'error');
			if ($debug==1) {
				echo '<BR>' . _('SQL used to retrieve the bank account name was') . '<BR>' . $sql;
			}
			include ('includes/footer.inc');
			exit;
		} elseif (DB_num_rows($result)==1){
			$myrow = DB_fetch_row($result);
			$_SESSION['ReceiptBatch']->BankAccountName = $myrow[0];
			$_SESSION['ReceiptBatch']->BankAccountCur = $myrow[1];
			unset($result);
		} elseif (DB_num_rows($result)==0){
			prnMsg( _('The bank account number') . ' ' . $_POST['BankAccount'] . ' ' . _('is not set up as a bank account'),'error');
			include ('includes/footer.inc');
			exit;
		}
	}
//HERE
} elseif (isset($_GET['Delete'])){
  /* User hit delete the receipt entry from the batch */
   $_SESSION['ReceiptBatch']->remove_receipt_item($_GET['Delete']);
} elseif (isset($_POST['Process'])){ //user hit submit a new entry to the receipt batch

   $counter = $_SESSION['ReceiptBatch']->add_to_batch($_POST['amount'],
   						$_POST['CustomerID'],
						$_POST['discount'],
						$_POST['Narrative'],
						$_POST['GLCode'],
						$_POST['PayeeBankDetail'],
						$_POST['CustomerName'],
                        $_POST['ReceiptTypePartida'],
                        $_POST['cobrador_id']);

	// bowikaxu realhost - add 1 to allocations counter
	for($i=0;$i<=$_POST['TotalNumberOfAllocs'];$i++){
		if($_POST['Amt'.$i]!=0){
			$_SESSION['ReceiptBatch']->add_invoice($counter,$_POST['AllocID'.$i],$_POST['Amt'.$i]);
		}
	}
	$_SESSION['Alloc_Counter']++;
	//echo "counter ++";

   /*Make sure the same receipt is not double processed by a page refresh */
   $Cancel = 1;

}

if (isset($Cancel)){
   unset($_SESSION['CustomerRecord']);
   unset($_POST['CustomerID']);
   unset($_POST['CustomerName']);
   unset($_POST['amount']);
   unset($_POST['discount']);
   unset($_POST['Narrative']);
   unset($_POST['PayeeBankDetail']);
}

if (isset($_POST['Search'])){
/*Will only be true if clicked to search for a customer code */


    /*Se agrega para buscar Por Folio de Asociado*/
    if (isset($_POST['FolioAfil']) || isset($_GET['FolioAfil'])){
       if(!empty($_POST['FolioAfil'])){
           $FolioTitular = $_POST['FolioAfil'];
       }else{
           $FolioTitular = $_GET['FolioAfil'];
       }

       $GetDebtorNo ="SELECT debtorno FROM rh_titular WHERE folio = '" . $FolioTitular . "'";
       $_GetDebtorNo = DB_query($GetDebtorNo, $db);
       $_2GetDebtorNo = DB_fetch_assoc($_GetDebtorNo);

       $_POST['Folio'] = $FolioTitular;
       $_POST['CustCode'] = $_2GetDebtorNo['debtorno'];
       $_POST['Search'] = "Buscar";
    }/*Termina Busqueda por folio*/

	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered');
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
	} else {
		If (strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name
				FROM debtorsmaster
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'
				AND debtorsmaster.currcode= '" . $_SESSION['ReceiptBatch']->Currency . "'";

		} elseif (strlen($_POST['CustCode'])>0){

            $debtorno = strtoupper(trim($_POST['CustCode']));
            if(strlen($_POST['Folio']) == 0){
                $debtorno = "%$debtorno%";
            }

            $SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name
				FROM debtorsmaster
				WHERE debtorsmaster.debtorno " . LIKE . " '{$debtorno}'
				AND debtorsmaster.currcode= '" . $_SESSION['ReceiptBatch']->Currency . "'";
		}

		$result = DB_query($SQL,$db,'','',false,false);
		if (DB_error_no($db) !=0) {
			prnMsg(_('The searched customer records requested cannot be retrieved because') . ' - ' . DB_error_msg($db),'error');
			if ($debug==1){
				prnMsg(_('SQL used to retrieve the customer details was') . '<BR>' . $sql,'error');
			}
		} elseif (DB_num_rows($result)==1){
			$myrow=DB_fetch_array($result);
			$Select = $myrow["debtorno"];
			unset($result);
		} elseif (DB_num_rows($result)==0){
			prnMsg( _('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
		}

	} //one of keywords or custcode was more than a zero length string
} //end of if search

If (isset($_POST['Select'])){
	$Select = $_POST['Select'];
}

If (isset($Select)) {
/*will only be true if a customer has just been selected by clicking on the customer or only one
customer record returned by the search - this record is then auto selected */

	$_POST['CustomerID']=$Select;
	/*need to get currency sales type - payment discount percent and GL code
	as well as payment terms and credit status and hold the lot as session variables
	the receipt held entirely as session variables until the button clicked to process*/


	if (isset($_SESSION['CustomerRecord'])){
	   unset($_SESSION['CustomerRecord']);
	}

	$SQL = "SELECT debtorsmaster.name,
			debtorsmaster.pymtdiscount,
			debtorsmaster.currcode,
			currencies.currency,
			currencies.rate,
			paymentterms.terms,
			debtorsmaster.creditlimit,
			holdreasons.dissallowinvoices,
			holdreasons.reasondescription,
			SUM(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc) AS balance,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0  THEN
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate)) >= paymentterms.daysbeforedue  THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= 0 THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS due,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue	AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays1'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1', 'MONTH') ."), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))', 'DAY') . ")) >= " . $_SESSION['PastDueDays1'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS overdue1,
			SUM(CASE WHEN paymentterms.daysbeforedue > 0 THEN
				CASE WHEN TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) > paymentterms.daysbeforedue AND TO_DAYS(Now()) - TO_DAYS(debtortrans.trandate) >= (paymentterms.daysbeforedue + " . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			ELSE
				CASE WHEN (TO_DAYS(Now()) - TO_DAYS(DATE_ADD(DATE_ADD(debtortrans.trandate, " . INTERVAL('1','MONTH') . "), " . INTERVAL('(paymentterms.dayinfollowingmonth - DAYOFMONTH(debtortrans.trandate))','DAY') . ")) >= " . $_SESSION['PastDueDays2'] . ") THEN debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight - debtortrans.ovdiscount - debtortrans.alloc ELSE 0 END
			END) AS overdue2
			FROM debtorsmaster,
				paymentterms,
				holdreasons,
				currencies,
				debtortrans
			WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
			AND debtorsmaster.currcode = currencies.currabrev
			AND debtorsmaster.holdreason = holdreasons.reasoncode
			AND debtorsmaster.debtorno = '" . $_POST['CustomerID'] . "'
			AND debtorsmaster.debtorno = debtortrans.debtorno
			GROUP BY debtorsmaster.name,
				debtorsmaster.pymtdiscount,
				debtorsmaster.currcode,
				currencies.currency,
				currencies.rate,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				paymentterms.daysbeforedue,
				paymentterms.dayinfollowingmonth,
				debtorsmaster.creditlimit,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription";


	$ErrMsg = _('The customer details could not be retrieved because');
	$DbgMsg = _('The SQL that failed was');
	$CustomerResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

	if (DB_num_rows($CustomerResult)==0){

		/*Because there is no balance - so just retrieve the header information about the customer - the choice is do one query to get the balance and transactions for those customers who have a balance and two queries for those who don't have a balance OR always do two queries - I opted for the former */

		$NIL_BALANCE = True;

		$SQL = "SELECT debtorsmaster.name,
				debtorsmaster.pymtdiscount,
				currencies.currency,
				currencies.rate,
				paymentterms.terms,
				debtorsmaster.creditlimit,
				debtorsmaster.currcode,
				holdreasons.dissallowinvoices,
				holdreasons.reasondescription
			FROM debtorsmaster,
				paymentterms,
				holdreasons,
				currencies
			WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
			AND debtorsmaster.currcode = currencies.currabrev
			AND debtorsmaster.holdreason = holdreasons.reasoncode
			AND debtorsmaster.debtorno = '" . $_POST['CustomerID'] . "'";

		$ErrMsg = _('The customer details could not be retrieved because');
		$DbgMsg = _('The SQL that failed was');
		$CustomerResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

	} else {
		$NIL_BALANCE = False;
	}

	$_SESSION['CustomerRecord'] = DB_fetch_array($CustomerResult);

	if ($NIL_BALANCE==True){
		$_SESSION['CustomerRecord']['balance']=0;
		$_SESSION['CustomerRecord']['due']=0;
		$_SESSION['CustomerRecord']['overdue1']=0;
		$_SESSION['CustomerRecord']['overdue2']=0;
	}
} /*end of if customer has just been selected  all info required read into $_SESSION['CustomerRecord']*/

/*set up the form whatever */


echo "<FORM NAME='main' ACTION=" . $_SERVER['PHP_SELF'] . " METHOD=POST>";

/*this block of ifs decides whether to show the new batch entry screen or not
based on the settings for $_POST['BatchNo'] and $_POST['BankAccount'] if they have already been
selected ie the form has called itself at least once then the page saves the variable
settings in a cookie. */

/*Lista de Cobradores*/
$SQLCobradores = "SELECT id, nombre FROM rh_cobradores WHERE activo = 1";
$resultCobradores=DB_query($SQLCobradores,$db);
while ( $_2ListaCobradores = DB_fetch_assoc($resultCobradores)) {
    $ListaCobradores[$_2ListaCobradores['id']] = $_2ListaCobradores['nombre'];
}
/*END Lista de Cobradores*/

if (isset($_SESSION['ReceiptBatch'])){

   /*show the batch header details and the entries in the batch so far */

   echo "<FONT SIZE=3 COLOR=BLUE>" . $_SESSION['ReceiptBatch']->ReceiptType . " " . _('Batch') . " " . _('Banked into the') . " " . $_SESSION['ReceiptBatch']->BankAccountName . " " . _('on') . " " . $_SESSION['ReceiptBatch']->DateBanked . "</FONT>";

   echo "<TABLE class='table table-striped table-bordered table-hover'>
    <thead>
    <tr>
   	    <th class='tableheader'>" . _('Amount Received') . "</th>
        <th class='tableheader'>" . _('Discount') . "</th>
        <th class='tableheader'>" . _('Customer') . "</th>
        <th class='tableheader'>" . _('GL Code') . "</th>
        <th class='tableheader'>" . _('Pago') . "</th>
        <th class='tableheader'>" . _('Cobrador') . "</th>
        <th></th>
    </tr>
    </thead>
    <tbody>";

   $BatchTotal = 0;

   foreach ($_SESSION['ReceiptBatch']->Items as $ReceiptItem) {

    echo "<TR>
            <TD ALIGN=RIGHT>" . number_format($ReceiptItem->Amount,2) . "</TD>
            <TD ALIGN=RIGHT>" . number_format($ReceiptItem->Discount,2) . "</TD>
            <TD>" . $ReceiptItem->CustomerName . "</TD>
            <TD>" . $ReceiptItem->GLCode . "</TD>
            <TD>" . $ReceiptItem->ReceiptTypePartida . "</TD>
            <TD>" . $ListaCobradores[$ReceiptItem->Cobrador_id] . "</TD>
            <TD><a href='" . $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=' . $ReceiptItem->ID . "'>" . _('Delete') . '</a></TD>
		</TR>';
        $BatchTotal= $BatchTotal + $ReceiptItem->Amount;
   }

   echo "<TR><TD ALIGN=RIGHT><B>" . number_format($BatchTotal,2) . "</B></TD></TR></tbody></TABLE>";


} else {
  /*need to enter batch no or select a bank account and bank date*/

	// bowikaxu - allocations clases counter
	$_SESSION['Alloc_Counter']=0;

	echo '<CENTER><FONT SIZE=4><B><U>' . _('Set up a New Batch') . '</B></U></FONT>';
	echo "<INPUT TYPE='hidden' name='BatchNo'value='" . $_POST['BatchNo'] . "'>";
	echo "<P><TABLE>";

	$SQL = "SELECT bankaccountname,
			bankaccounts.accountcode
		FROM bankaccounts,
			chartmaster
		WHERE bankaccounts.accountcode=chartmaster.accountcode";

	$ErrMsg = _('The bank accounts could not be retrieved because');
	$DbgMsg = _('The SQL used to retrieve the bank accounts was');
	$AccountsResults = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

	echo '<TR><TD>' . _('Bank Account') . ":</TD><TD><SELECT name='BankAccount'>";

	if (DB_num_rows($AccountsResults)==0){
		echo '</SELECT></TD></TR></TABLE><P>';
		prnMsg(_('Bank Accounts have not yet been defined') . '. ' . _('You must first') . ' ' . "<A HREF='$rootpath/BankAccounts.php'>" . _('define the bank accounts') . '</A>' . _('and general ledger accounts to be affected'),'info');
		include('includes/footer.inc');
		 exit;
	} else {
		while ($myrow=DB_fetch_array($AccountsResults)){
		      /*list the bank account names */
			if ($_POST['BankAccount']==$myrow['accountcode']){
				echo "<OPTION SELECTED VALUE='" . $myrow['accountcode'] . "'>" . $myrow['bankaccountname'];
			} else {
				echo "<OPTION VALUE='" . $myrow['accountcode'] . "'>" . $myrow['bankaccountname'];
			}
		}
		echo "</SELECT></TD></TR>";
	}

	$_POST['DateBanked'] = Date($_SESSION['DefaultDateFormat']);

	echo '<TR><TD>' . _('Date Banked') . ":</TD><TD><INPUT TYPE='text' name='DateBanked' class=date maxlength=10 size=11 value='" . $_POST['DateBanked'] . "'></TD></TR>";
	echo '<TR><TD>' . _('Currency') . ":</TD><TD><SELECT name='Currency'>";

	if (!isset($_POST['Currency'])){
	  /* find out what the functional currency of the company is */

		$SQL = "SELECT currencydefault FROM companies WHERE coycode=1";
		$result=DB_query($SQL,$db);
		$myrow=DB_fetch_row($result);
		$_POST['Currency']=$myrow[0];
		unset($result);
	}

	$SQL = "SELECT currency, currabrev, rate FROM currencies";
	$result=DB_query($SQL,$db);
	if (DB_num_rows($result)==0){
	   echo '</SELECT></TD></TR>';
	   prnMsg(_('No currencies are defined yet') . '. ' . _('Receipts cannot be entered until a currency is defined'),'warn');

	} else {
		while ($myrow=DB_fetch_array($result)){
		    if ($_POST['Currency']==$myrow['currabrev']){
			echo "<OPTION SELECTED value=" . $myrow['currabrev'] . '>' . $myrow['currency'];
		    } else {
			echo "<OPTION value=" . $myrow['currabrev'] . '>' . $myrow['currency'];
		    }
		}
		echo '</SELECT></TD></TR>';
	}

	if (!isset($_POST['ExRate'])){
	     $_POST['ExRate']=1;
	}
	echo '<TR><TD>' . _('Exchange Rate') . ":</TD><TD><INPUT TYPE='text' name='ExRate' maxlength=10 size=12 value='" . $_POST['ExRate'] . "'></TD></TR>";
	echo '<TR><TD>' . _('Receipt Type') . ":</TD><TD><SELECT name=ReceiptType>";
    //HERE
	include('includes/GetPaymentMethods.php');
/* The array ReceiptTypes is defined from the setup tab of the main menu under payment methods - the array is populated from the include file GetPaymentMethods.php */

	foreach ($ReceiptTypes as $RcptType) {
	     if ($_POST['ReceiptType']==$RcptType){
		   echo "<OPTION SELECTED Value='$RcptType'>$RcptType";
	     } else {
		   echo "<OPTION Value='$RcptType'>$RcptType";
	     }
	}
	echo "</SELECT></TD></TR>";
	echo '<TR><TD>' . _('Narrative') . ":</TD><TD><INPUT TYPE='text' name='BatchNarrative' maxlength=249 size=60 value='" . $_POST['BatchNarrative'] . "'></TD></TR>";

	echo "</TABLE>";

	echo "<CENTER><INPUT TYPE=SUBMIT Name='BatchInput' Value='" . _('Accept') . "'></CENTER>";
}
// bowikaxu testing

/*this next block of ifs deals with what information to display for input into the form
the info depends on where the user is up to ie the first stage is to select a bank
account, currency being banked and a batch number - or start a new batch by leaving the batch no blank
and a date for the banking. The second stage is to select a customer or GL account.
Finally enter the amount */


/*if a customer has been selected (and a receipt batch is underway)
then set out the customers account summary */

if (isset($_SESSION['CustomerRecord']) AND isset($_POST['CustomerID']) AND $_POST['CustomerID']!="" AND isset($_SESSION['ReceiptBatch'])){
/*a customer is selected HERE */

    /*Obtengo Datos del Afiliado*/
    $_2GetAfilData = "SELECT cobranza.folio, cobranza.cobrador
                           FROM rh_cobranza cobranza
                           WHERE cobranza.debtorno = '{$_POST['CustomerID']}'";
    $_GetAfilData=DB_query($_2GetAfilData,$db);
    $GetAfilData = DB_fetch_assoc($_GetAfilData);



	echo "<BR><CENTER><FONT SIZE=4>" . $GetAfilData['folio'] . ' - ' . $_POST['CustomerID'] .' - ' . $_SESSION['CustomerRecord']['name'] . ' </FONT></B> - (' . _('All amounts stated in') . ' ' . $_SESSION['CustomerRecord']['currency'] . ')</CENTER><BR><B><FONT COLOR=BLUE>' . _('Terms') . ': ' . $_SESSION['CustomerRecord']['terms'] . "<BR>" . _('Credit Limit') . ": </B></FONT> " . number_format($_SESSION['CustomerRecord']['creditlimit'],0) . '  <B><FONT COLOR=BLUE>' . _('Credit Status') . ':</B></FONT> ' . $_SESSION['CustomerRecord']['reasondescription'];

	if ($_SESSION['CustomerRecord']['dissallowinvoices']!=0){
	   echo '<BR><FONT COLOR=RED SIZE=4><B>' . _('ACCOUNT ON HOLD') . '</FONT></B><BR>';
	}



	echo "<TABLE class='table table-striped table-bordered table-hover' >
        <thead>
            <TR>
                <th class='tableheader'>" . _('Total Balance') . "</th>
                <th class='tableheader'>" . _('Current') . "</th>
                <th class='tableheader'>" . _('Now Due') . "</th>
                <th class='tableheader'>" . $_SESSION['PastDueDays1'] . '-' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . "</th>
                <th class='tableheader'>" . _('Over') . ' ' . $_SESSION['PastDueDays2'] . ' ' . _('Days Overdue') . '</th>
    		</TR>
        <thead>
        <tbody>';
	echo "<TR>
            <TD ALIGN=RIGHT>" . number_format($_SESSION['CustomerRecord']['balance'],2) . "</TD>
            <TD ALIGN=RIGHT>" . number_format(($_SESSION['CustomerRecord']['balance'] - $_SESSION['CustomerRecord']['due']),2) . "</TD>
            <TD ALIGN=RIGHT>" . number_format(($_SESSION['CustomerRecord']['due']-$_SESSION['CustomerRecord']['overdue1']),2) . "</TD>
            <TD ALIGN=RIGHT>" . number_format(($_SESSION['CustomerRecord']['overdue1']-$_SESSION['CustomerRecord']['overdue2']) ,2) . "</TD>
            <TD ALIGN=RIGHT>" . number_format($_SESSION['CustomerRecord']['overdue2'],2) . "</TD>
        </TR>
        </tbody>
	</TABLE>";
    echo "<div style='height:30px;'></div>";
	echo "<CENTER><TABLE>";

        $DisplayDiscountPercent = number_format($_SESSION['CustomerRecord']['pymtdiscount']*100,2) . "%";

	echo "<INPUT TYPE='hidden' name='CustomerID' value=" . $_POST['CustomerID'] . ">";
	echo "<INPUT TYPE='hidden' name='CustomerName' value='" . $_SESSION['CustomerRecord']['name'] . "'>";

}

if (isset($_POST['GLEntry']) AND isset($_SESSION['ReceiptBatch'])){
/* Set up a heading for the transaction entry for a GL Receipt */

	echo '<BR><CENTER><FONT SIZE=4>' . _('General Ledger Receipt Entry') . '</FONT><TABLE>';

	/*now set up a GLCode field to select from avaialble GL accounts */
	echo '<TR><TD>' . _('GL Account') . ":</TD><TD><SELECT name='GLCode'>";
	$SQL = "SELECT accountcode, accountname FROM chartmaster ORDER BY accountcode";
	$result=DB_query($SQL,$db);
	if (DB_num_rows($result)==0){
	   echo '</SELECT>' . _('No General ledger accounts have been set up yet') . ' - ' . _('receipts cannot be entered against GL accounts until the GL accounts are set up') . '</TD></TR>';
	} else {
		while ($myrow=DB_fetch_array($result)){
		    if ($_POST['GLCode']==$myrow["accountcode"]){
			echo "<OPTION SELECTED value=" . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' - ' . $myrow['accountname'];
		    } else {
			echo '<OPTION value=' . $myrow['accountcode'] . '>' . $myrow['accountcode'] . ' - ' . $myrow['accountname'];
		    }
		}
		echo '</SELECT></TD></TR>';
	}

}

/*if either a customer is selected or its a GL Entry then set out
the fields for entry of receipt amt, disc, payee details, narrative */

if (((isset($_SESSION['CustomerRecord'])
	AND isset($_POST['CustomerID'])
	AND $_POST['CustomerID']!="")
		OR isset($_POST['GLEntry']))
		AND isset($_SESSION['ReceiptBatch'])){

	//print_r($_SESSION['ReceiptBatch']);
	// end show unsettled transactions
	//echo "<table>"; // bowikaxu - move table tag

	// bowikaxu realhost - sept 2008

	// total amount
	echo '<TR><TD>' . _('Amount of Receipt') . ":</TD>
		<TD><INPUT TYPE='text' id='amount' name='amount' onChange='total_change()' maxlength='12' size='13' value='" . $_POST['amount'] . "' readonly='readonly' ></TD>
	</TR>";

	// add allocated amount
	echo '<TR><TD>' . _('Amount').' '._('Allocated') . ":</TD>
		<TD><INPUT readonly='readonly' TYPE='text' id='allocated_amount' name='allocated_amount' maxlength=12 size=13 value='" . $_POST['allocated_amount'] . "'></TD>
	</TR>";
	// bowikaxu - add extra amount (anticipos)
	echo '<TR><TD>' . _('Amount').' '._('Extra') . ":</TD>
		<TD><INPUT readonly='readonly' TYPE='text' id='extra_amount' name='extra_amount' maxlength=12 size=13 value='" . $_POST['extra_amount'] . "'></TD>
	</TR>";

	if (!isset($_POST['GLEntry'])){
		echo '<TR><TD>' . _('Amount of Discount') . ":</TD>
			<TD><INPUT TYPE='text' name='discount' maxlength=12 size=13 value='" . $_POST['discount'] . "'> " . _('agreed prompt payment discount is') . ' ' . $DisplayDiscountPercent . '</TD></TR>';
	} else {
		echo "<INPUT TYPE='HIDDEN' NAME='discount' Value=0>";
	}

	echo '<TR><TD>' . _('Payee Bank Details') . ":</TD>
		<TD><INPUT TYPE='text' name='PayeeBankDetail' maxlength=22 size=20 value='" . $_POST['PayeeBankDetail'] . "'></TD></TR>";
	echo '<TR><TD>' . _('Receipt Type') . ":</TD><TD><SELECT name=ReceiptTypePartida>";

	include('includes/GetPaymentMethods.php');
    /* The array ReceiptTypes is defined from the setup tab of the main menu under payment methods - the array is populated from the include file GetPaymentMethods.php */

	foreach ($ReceiptTypes as $RcptType) {
	     if ($_POST['ReceiptType']==$RcptType){
		   echo "<OPTION SELECTED Value='$RcptType'>$RcptType";
	     } else {
		   echo "<OPTION Value='$RcptType'>$RcptType";
	     }
	}
	echo "</SELECT></TD></TR>";


    ?>

    <tr>
        <td>Cobrador: </td>
        <td>
            <select id="cobrador_id" name="cobrador_id">
                <option></option>
                <?php
                foreach ($ListaCobradores as $idx => $Name) {
                    echo '<option value=' . $idx . '>' . $idx . ' - ' . $Name . "</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                $("#cobrador_id option[value='<?=$GetAfilData['cobrador']?>']").attr("selected",true);
            </script>


        </td>
    </tr>

<?php //HERE
	echo '<TR><TD>' . _('Narrative') . ":</TD>
		<TD><INPUT TYPE='text' name='Narrative' maxlength=250 size=32 value='" . $_POST['Narrative'] . "'></TD></TR>";

	echo "</TABLE>"; // bowikaxu - table tag

	// ---------------------------------->

	// BOWIKAXU REALHOST SEPT 2008
		$SQL= "SELECT debtortrans.id,
			typename,
			transno,
			trandate,
			rate,
			c.serie,
            c.folio,
			ovamount+ovgst+ovfreight+ovdiscount as total,
			diffonexch,
			alloc
		FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id,
			systypes
		WHERE debtortrans.type = systypes.typeid
		AND debtortrans.settled=0
		AND debtortrans.type IN (10, 20000)
		AND debtortrans.rh_status != 'C'
		AND debtorno='" . $_POST['CustomerID'] . "'
		ORDER BY debtortrans.trandate";

	    $ErrMsg = _('There was a problem retrieving the transactions available to allocate to');
	    $DbgMsg = _('The following SQL to delete the allocation record was used');
	    $Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	    $TableHeader = "<table>
                        <thead>
                        <TR>
					       <th class='tableheader'>"._('Type')."</th>
					       <th class='tableheader'>"._('Trans').'<BR>'._('Number')."</th>
					       <th class='tableheader'>"._('Trans').'<BR>'._('Date')."</th>
					       <th class='tableheader'>"._('Total').'<BR>'._('Amount')."</th>
					       <th class='tableheader'>"._('Yet to').'<BR>'._('Allocate')."</th>
					       <th class='tableheader'>"._('This').'<BR>'._('Allocation')."</th>
                           <th></th>
				        </TR>
                        </thead>
                    ";

	        //echo "<TABLE CELLPADDING='2' COLSPAN='7' BORDER='0' class='table' >";
            echo "<tbody>";

	        $k=0;
	        $RowCounter =0;
		    $Counter = 0;
	        $TotalAllocated =0;

		while ($myrow=DB_fetch_array($Result)){

			// bowikaxu realhost - get external invoice number
			$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
			    		WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			    		AND locations.loccode = rh_invoicesreference.loccode";
			    		$res3 = DB_query($sql,$db);
			    		$ext = DB_fetch_array($res3);

			    		/*Alternate the background colour for each potential allocation line */

		    // bowikaxu realhost - show transactions reference
		    $sql = "SELECT debtortrans.reference  FROM debtortrans WHERE
		    		debtorno='" . $_SESSION['Alloc'.$_SESSION['Alloc_Counter']]->DebtorNo . "'
		    		AND debtortrans.id = ".$AllocnItem->ID."";
		    $refres = mysql_query($sql,$db);
		    //$ref = mysql_fetch_array($refres);
		    // bowikaxu end transaction reference

		    $RowCounter--;
		    if ($RowCounter<0){
			/*Set up another row of headings to ensure always a heading on the screen of potential allocns*/
			echo $TableHeader;
			$RowCounter=14;
		    }

		    /*Alternate the background colour for each potential allocation line */
		    if ($k==1){
			  echo "<tr bgcolor='#CCCCCC'>";
			  $k=0;
		    } else {
			  echo "<tr bgcolor='#EEEEEE'>";
			  $k=1;
		    }
		    $YetToAlloc = ($myrow['total'] - $myrow['alloc']);
			// bowikaxu si es factura obtener numero externo
		    if($myrow['typename'] == 'Factura'){

				$sql2 = "SELECT rh_invoicesreference.extinvoice, locations.loccode, locations.rh_serie FROM rh_invoicesreference, locations
						WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
						AND locations.loccode = rh_invoicesreference.loccode";

				//SAINTS
				 $sql3="SELECT c.serie, c.folio
            FROM  rh_cfd__cfd c
            WHERE c.fk_transno = ".$myrow['transno']." AND c.id_systypes = 10";

				$Res = DB_query($sql2,$db);
				$ExtRes = DB_fetch_array($Res);
				//SAINTS
				$Res3=DB_query($sql3,$db);
				$ExtRes3=DB_fetch_array($Res3);
				//SAINTS
				if($ExtRes3['serie']!=""){
                    echo "
                    <TD>".$myrow['typename']."</TD>
			    	<TD>".$ExtRes['loccode']." - ".$ExtRes3['serie'].$ExtRes3['folio']." (".$myrow['transno'].")</TD>
					<TD ALIGN='RIGHT'>".$myrow['trandate']."</TD>
					<TD ALIGN='RIGHT'>" . number_format($myrow['total'],2) . "</TD>
					<TD ALIGN='RIGHT'>" . number_format($YetToAlloc,2) . "</td>";
                }
				//SAINTS
				else{
                    echo "
                    <TD>".$myrow['typename']."</TD>
			    	<TD>".$ExtRes['loccode']." - ".$ExtRes3['serie'].$ExtRes3['folio']." (".$myrow['transno'].")</TD>
					<TD ALIGN=RIGHT>".$myrow['trandate']."</TD>
					<TD ALIGN=RIGHT>" . number_format($myrow['total'],2) . '</TD>
					<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2) . "<td>";
                }

					/*echo "<TD>".$myrow['typename']."</TD>
			    		<TD>".$ExtRes['loccode']." - ".$ExtRes['rh_serie'].$ExtRes['extinvoice']." (".$myrow['transno'].")</TD>
					<TD ALIGN=RIGHT>".$myrow['trandate']."</TD>
					<TD ALIGN=RIGHT>" . number_format($myrow['total'],2) . '</TD>
					<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2);*/
			}else {
			  //SAINTS
			  if($myrow['folio']!=""){
                echo "
                <TD>".$myrow['typename']."</TD>
		    	<TD>".$myrow['serie'].$myrow['folio']."</TD>
				<TD ALIGN=RIGHT>".$myrow['trandate']."</TD>
				<TD ALIGN=RIGHT>" . number_format($myrow['total'],2) . '</TD>
				<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2) . "</td>";
            }

			  //SAINTS
			  else{
                echo "
                <TD>".$myrow['typename']."</TD>
		    	<TD>".$myrow['transno']."</TD>
				<TD ALIGN=RIGHT>".$myrow['trandate']."</TD>
				<TD ALIGN=RIGHT>" . number_format($myrow['total'],2) . '</TD>
				<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2) . "</td>";
            }
			}

		    if ($myrow['total'] < 0) {
	            	$balance+=$YetToAlloc;
		    	echo '<td></td>';
		    	//<td align=right>' . number_format($balance,2) . '<td>
		    } else {
		    	echo "<TD ALIGN=RIGHT> <input type=hidden name='YetToAlloc" . $Counter . "' value=" . round($YetToAlloc,2) . '>';
		    	echo "<input type='checkbox' onClick='amount_change(".$YetToAlloc.",\"Amt".$Counter."\",\"All".$Counter."\")' id='All".$Counter."' name='All" .  $Counter . "'";
		    	if (ABS($myrow['total']-$YetToAlloc)<0.01){
				echo "VALUE='" . True . "'>";
		    	} else {
		    		echo '>';
		    	}
			$balance+=$YetToAlloc-$myrow['total'];
				//echo "<INPUT TYPE=hidden name='TransID".$Counter."' value = '".$myrow['id']."'>";
				// bowikaxu - onchange property, to update amount values
		    	echo "<td>
                    <input type='text' id='Amt".$Counter."' name='Amt" . $Counter ."' onBlur='field_change(\"Amt".$Counter."\")'  value='0'  >
		    	    <input type='hidden' name='AllocID" . $Counter . "' value=" . $myrow['id'] . '>
                </TD>';
		    	//<td align=right>' . number_format($balance,2) . '<td>
		    }
            echo "</tr>";
		    $TotalAllocated =$TotalAllocated + round($myrow['total'],2);
		    $Counter++;
		}

	   echo "<INPUT TYPE=HIDDEN NAME='TotalNumberOfAllocs' VALUE=$Counter>";

	// ---------------------------------->
    echo "</tbody>";
	echo "</TABLE>";
	echo   "<INPUT TYPE='SUBMIT' name='Process' value='" . _('Accept') . "' class='btn btn-success'>
            <INPUT TYPE='SUBMIT' name='Cancel' value='" . _('Cancel') . "'  class='btn btn-danger'>";

} elseif (isset($_SESSION['ReceiptBatch']) && !isset($_POST['GLEntry'])){

      /*Show the form to select a customer */
        echo '<B>';

	echo $msg;
	echo '<BR><U>' . _('Select A Customer') . '</U></B>';
	echo '<TABLE CELLPADDING=3 COLSPAN=4>';
	echo '<TR>
            <TD>' . _('Text in the') . ' ' . '<B>' . _('name') . '</B>:</TD>';
	echo   "<TD><INPUT TYPE='Text' NAME='Keywords' SIZE=20 MAXLENGTH=25 ></TD>";
	echo '<TD><FONT SIZE=3><B>OR</B></FONT></TD>';

	echo '<TD>' . _('Text extract in the customer') . ' ' . '<B>' . _('code') . '</B>:</TD>';
	echo "<TD><INPUT TYPE='Text' NAME='CustCode' SIZE=15 MAXLENGTH=18></TD>";
    echo '<TD><FONT SIZE=3><B>OR</B></FONT></TD>';

    echo '<TD>' . _('Folio Asociado') . '</B>:</TD>';
    echo "<TD><INPUT TYPE='Text' NAME='FolioAfil' SIZE=15 MAXLENGTH=18></TD>";
	echo '</TR>
        </TABLE>';

    echo '<CENTER>';

    echo "<div style='height:30px;'></div>";

    echo "<INPUT TYPE='SUBMIT' NAME='Search' VALUE='" . _('Search Now') . "' class='btn btn-success' > ";

    echo "<INPUT TYPE='SUBMIT' NAME='GLEntry' VALUE='" . _('Enter A GL Receipt') . "' class='btn btn-success' > ";

    if (count($_SESSION['ReceiptBatch']->Items) > 0){
        echo "<INPUT TYPE=SUBMIT NAME='CommitBatch' VALUE='" . _('Accept and Process Batch') . "' class='btn btn-success' > ";
    }

    // bowikaxu realhost March 2008 - allow to apply batch
    if(isset($_SESSION['ReceiptBatch'])){
        echo "<INPUT TYPE=SUBMIT NAME='CancelBatch' VALUE='" . _('Cancel').' '._('Batch') . "' class='btn btn-danger' > ";
    }

    echo "<div style='height:30px;'></div>";

	If ($result) {

		echo '<CENTER><TABLE class="table table-striped table-bordered table-hover" style="max-width:600px;">';
		$TableHeader = "<thead><TR>
                            <TD class='tableheader'>" . _('Code') . "</TD>
                            <TD class='tableheader'>" . _('Folio Afiliado') . "</TD>
                            <TD class='tableheader'>" . _('Customer Name') . '</TD>
                        </TR></thead><tbody>';
		echo $TableHeader;
		$j = 1;
		$k = 0; //row counter to determine background colour

		while ($myrow=DB_fetch_array($result)) {

            $GetAfilData2 = "SELECT folio FROM rh_titular WHERE debtorno = '{$myrow['debtorno']}'";
            $_2GetAfilData2 = DB_query($GetAfilData2, $db);
            $_GetAfilData2 = DB_fetch_assoc($_2GetAfilData2);
            echo "<tr>";
            echo "<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='{$myrow['debtorno']}'></td>";
            echo "<td>{$_GetAfilData2['folio']}</td>";
            echo "<td>{$myrow['name']}</td>";
            echo "</tr>";
        //end of page full new headings if
        }//end of while loop

		echo '</tbody></TABLE></CENTER>';

	}
	//end if results to show
}

echo '</form>';
if(!isset($Counter)){
	$Counter=0;
}
?>
<script language="JavaScript" type="text/javascript">
//--> andres amaya diaz bowikaxu@gmail.com <---//
// -- Get Total Amount fields, to do a foreach and calculate total allocated value -- //
function field_change(id){
        // iJPe
        // realhost     2010-01-24
        // Modificacion para evitar asignacion de pagos negativos y asignaciones negativas
        if (document.getElementById(id).value <= 0 || document.getElementById('amount').value < 0){
            document.getElementById(id).value = 0;
            document.getElementById('amount').value = 0;
        }

        var total = document.getElementById('amount').value; //-- total receipt value --//
        var total_allocs = 0;
        if(<?php echo $Counter; ?> >= 1){
                total_allocs = <?php echo $Counter; ?>;
        }
        var i=0;
        var sum=0;
        var field;
        // total number of possible allocations
        //alert(document.getElementById(id).value);
        for(i=0;i<total_allocs;i=i+1){
                // get field allocation value
                field = document.getElementById('Amt'+i).value;
                if((field*1)!=0){ // not equal 0, lets sum
                        sum = sum + (field*1);
                }
        }
        // now we got the sum, do some math
        document.getElementById('allocated_amount').value = (sum*1); //-- set allocated total --//

        // iJPe
        // realhost     2010-01-24
        // Modificacion para evitar asignacion de pagos negativos y asignaciones negativas
        if (!(document.getElementById('amount').value) || (parseFloat(document.getElementById('amount').value) < parseFloat(document.getElementById('allocated_amount').value) && ((document.getElementById('allocated_amount').value) != 0)))
        {
       		document.getElementById('amount').value = document.getElementById('allocated_amount').value;
       		total = document.getElementById('allocated_amount').value;
        }

        document.getElementById('extra_amount').value = (total*1)-(sum*1); //-- set extra amount --//
}
</script>
<?php

include('includes/footer.inc');
?>
