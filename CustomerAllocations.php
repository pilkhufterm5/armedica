<?php
/* webERP Revision: 14 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-19 10:50:29 -0500 (Fri, 19 Sep 2008) $
 * $Rev: 402 $
 */
/*This page can be called with

1. A DebtorTrans ID

The page will then show potential allocations for the transaction called with,
this page can be called from the customer enquiry to show the make up and to modify
existing allocations

2. A DebtorNo

The page will show all outstanding receipts or credits yet to be allocated

3. No parameters

The page will show all outstanding credits and receipts yet to be
allocated */
include('includes/DefineCustAllocsClass.php');

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Customer Receipt') . '/' . _('Credit Note Allocations');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

//echo "<hr>";
//print_r($_GET);
//echo "<hr>";
//print_r($_POST);
//echo "<hr>";
//print_r($_SESSION);

if (isset($_POST['UpdateDatabase']) OR isset($_POST['RefreshAllocTotal'])) {

	if (!isset($_SESSION['Alloc'])){
		prnMsg(_('Allocations can not be processed again') . '. ' . _('If you hit refresh on this page after having just processed an allocation') . ', ' . _('try to use the navigation links provided rather than the back button') . ', ' . _('to avoid this message in future'),'info');
		include('includes/footer.inc');
		exit;
	}
	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/*1st off run through and update the array with the amounts allocated
	This works because the form has an input field called the value of
	AllocnItm->ID for each record of the array - and PHP sets the value of
	the form variable on a post*/

	$TotalAllocated=0;
	$TotalDiffOnExch=0;

	for ($AllocCounter=0;$AllocCounter < $_POST['TotalNumberOfAllocs']; $AllocCounter++){

		if (isset($_POST['Amt' . $AllocCounter])){ // only do the below for allocatable charge amounts
						// there will be no Amtxx field for credits/receipt so skip them
			if (!is_numeric($_POST['Amt' . $AllocCounter])){
			$_POST['Amt' . $AllocCounter]=0;
			}
			if ($_POST['Amt' . $AllocCounter]<0){
				prnMsg(_('The entry for the amount to allocate was negative') . '. ' . _('A positive allocation amount is expected') . '.','warn');
				$_POST['Amt' . $AllocCounter]=0;
			}

			if ($_POST['All' . $AllocCounter]==True){
				$_POST['Amt' . $AllocCounter] = $_POST['YetToAlloc' . $AllocCounter];

			}
			/*Now check to see that the AllocAmt is no greater than the
			amount left to be allocated against the transaction under review */
			if ($_POST['Amt' . $AllocCounter] > $_POST['YetToAlloc' . $AllocCounter]){
				$_POST['Amt' . $AllocCounter]=$_POST['YetToAlloc' . $AllocCounter];
			}


			$_SESSION['Alloc']->Allocs[$_POST['AllocID' . $AllocCounter]]->AllocAmt = $_POST['Amt' . $AllocCounter];
			/*recalcuate the new difference on exchange
			(a +positive amount is a gain -ve a loss)*/

			$_SESSION['Alloc']->Allocs[$_POST['AllocID' . $AllocCounter]]->DiffOnExch =  ($_POST['Amt' . $AllocCounter] / $_SESSION['Alloc']->TransExRate) - ($_POST['Amt' . $AllocCounter] / $_SESSION['Alloc']->Allocs[$_POST['AllocID' . $AllocCounter]]->ExRate);

			$TotalDiffOnExch = $TotalDiffOnExch + $_SESSION['Alloc']->Allocs[$_POST['AllocID' . $AllocCounter]]->DiffOnExch;
			$TotalAllocated = $TotalAllocated + $_POST['Amt' . $AllocCounter];
		} // only do the above for allocatable charge amounts

	} /*end of the loop to set the new allocation amounts,
	recalc diff on exchange and add up total allocations */

	If ($TotalAllocated + $_SESSION['Alloc']->TransAmt >0.008){
	   echo '<BR><HR>';
	   prnMsg(_('These allocations cannot be processed because the amount allocated is more than the amount of the').' ' . $_SESSION['Alloc']->TransTypeName  . ' '._('being allocated') . '<BR>' . _('Total allocated').' = ' . $TotalAllocated . ' '._('and the total amount of the') .' ' . $_SESSION['Alloc']->TransTypeName  . ' '._('was').' ' . -$_SESSION['Alloc']->TransAmt,'error');
	   $InputError=1;
	}
}

if (isset($_POST['UpdateDatabase'])){

	If ($InputError==0){ /* ie all the traps were passed */
	/* actions to take having checked that the input is sensible
	1st set up a transaction on this thread*/

	// bowikaxu realhost - obtener el periodo y fecha antes del loop sino hay error
	$PeriodNo = GetPeriod($_SESSION['Alloc']->TransDate, $db);
	$_SESSION['Alloc']->TransDate = FormatDateForSQL($_SESSION['Alloc']->TransDate);

	$Result=DB_query('BEGIN',$db);

		foreach ($_SESSION['Alloc']->Allocs as $AllocnItem) {

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
				$next_trans=array();
				//$NewAllocTotal = $AllocnItem->PrevAlloc + $AllocnItem->AllocAmt;
				// bowikaxu realhost may 2007 - insert de los taxes, ya sean proporcionales o no
// 				$sql = "SELECT (typeno+1) AS typeno FROM systypes WHERE typeid = 0";
// 				$next_trans = DB_fetch_array(DB_query($sql,$db));

// 				$sql = "UPDATE systypes SET typeno = typeno+1 WHERE typeid = 0";
//            		DB_query($sql,$db);
					$next_trans['typeno']=GetNextTransNo( 0, $db);

                                /*
                                 * Juan Mtz 0.o
                                 * realhost
                                 * 01-Sep-2009
                                 *
                                 * Modificacion para que al matar un deposito no se realize un asiento
                                 * Se verifican los tipos de las transacciones a asignar entre si, si estas
                                 * son de el mismo tipo no se realiza un asiento
                                 */

                                /*
                                 * Query para verificar el tipo de transaccion que se va a asignar
                                 */
                                $sqlType1 = "Select type FROM debtortrans WHERE id = ".$_SESSION['Alloc']->AllocTrans;
                                $verifyType1 = DB_query($sqlType1,$db);
                                $fetchType1 = DB_fetch_array($verifyType1); //Tipo de transaccion que se va a asignar

                                /*
                                 * Query para verificar el tipo de transaccion a la cual se le asignara una cantidad
                                 */
                                $sqlType2 = "Select type FROM debtortrans WHERE id = ".$AllocnItem->ID;
                                $verifyType2 = DB_query($sqlType2,$db);
                                $fetchType2 = DB_fetch_array($verifyType2); //Tipo de transaccion a la que se esta asignando


				if($_SESSION['CashBase']==1 && ($fetchType2['type'] != 12)){
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

					//Se obtiene el monto total de la factura sin iva
					$sql_monto = 'select ovamount as total_fact_sin_iva from debtortrans
					  where type=10 and transno='.$AllocnItem->TypeNo;
					$rs_monto = DB_query($sql_monto,$db);
					$rw_monto = DB_fetch_array($rs_monto);
					$total_factura_sin_iva = $rw_monto['total_fact_sin_iva'];

					$AllocTotal = $AllocnItem->AllocAmt-$AllocnItem->OrigAlloc;
					$sql = 'SELECT 	stockmovestaxes.taxauthid,
					stockmovestaxes.taxrate,
					SUM(stockmovestaxes.taxrate * stockmoves.price*-stockmoves.qty*(1-stockmoves.discountpercent)) AS impuesto,
					SUM(stockmoves.price * -stockmoves.qty) AS tot
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

							/*// bowikaxu - si el anticipo es mayor al subtotal + impuesto, asignar su parte proporcional
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

								$factor = ($item['impuesto']/($total_factura_sin_iva+$item['impuesto']));
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
			// bowikaxu realhsot - fin insert de los taxes

				$sql = "SELECT rate FROM currencies WHERE currabrev = '".$_SESSION['CountryOfOperation']."'";
				$res2 = DB_query($sql,$db);
				$curr_rate = DB_fetch_array($res2);

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

				// bowikaxu realhost - Feb 2008 - matar el asiento de la cuenta de anticipo
				// de tipo 0 Journal - GL

                                if ($fetchType1['type']!=$fetchType2['type'])
                                {
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
                                }
//echo "1<br>";
                                /*
                                 * Juan Mtz 0.o
                                 * realhost
                                 * 03-Sept-2009
                                 *
                                 * Se agrego el siguiente query para obtener la cantidad que se tiene hasta el momento asignada, para
                                 * despues verificar si ahora se reasigno con monto 0 y si es asi realizar el query... (comentado mas abajo)
                                 */

                                $queryAmount= "SELECT alloc/rate as alloc FROM debtortrans, systypes WHERE debtortrans.type = systypes.typeid
                                               AND debtortrans.rh_status != 'C' AND debtorno='" . $_SESSION['Alloc']->DebtorNo . "'
                                               AND debtortrans.id = '".$AllocnItem->ID."' ORDER BY debtortrans.trandate";

                                $sqlAmount = DB_query($queryAmount, $db);
                                $fetchAmount = DB_fetch_array($sqlAmount);

                                if (($fetchAmount['alloc'] > 0) && ($AllocnItem->AllocAmt == 0) && ($fetchType2['type']!=12))
                                {
                                /*
                                 * Juan Mtz 0.o
                                 * realhost
                                 * 03-Sept-2009
                                 *
                                 * Se agrego el siguiente query para que se cree un asiento el cual no se realizaba, este asiento
                                 * se realiza cuando ocurre una desasignacion de cantidades, el asiento se hace cancelando la cantidad asignada
                                 * que se tenia anteriormente
                                 */
                                    /*
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
                                                                        " . -$fetchAmount['alloc'] . ')';

                                                                   */

                                    /*
                                     * rleal
                                     * Ene 29 2011
                                     * Se quita el insert de arriba
                                     */


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
//echo "2<br>";
                                }

                                //echo $fetchType1['type']."<br>";
                                //echo $fetchType2['type']."<br>";
                                //echo $AllocnItem->AllocAmt."<br>";

                                /*
                                 * Juan Mtz 0.o
                                 * realhost
                                 * 01-Sept-2009
                                 *
                                 * Se agrego la segunda condicion del if ($fetchType1['type']!=$fetchType2['type']), para que
                                 * no se realize asiento en caso de que las transacciones sean del mismo tipo, ya que
                                 * lo mas seguro es que se este cancelando entre transacciones
                                 */
                                if (($AllocnItem->AllocAmt >0) && ($fetchType1['type']!=$fetchType2['type'])){


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
//echo "3<br>".abs($NewAllocTotal-$AllocnItem->TransAmount);
                                }
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

                                //DB_query('rollback', $db);exit;


                //Relacion asignación - deposito
                $SQL = "INSERT INTO rh_asignaciones_debtors (debtortransid, gltrans,transactiondate) VALUES (
							".$AllocnItem->ID.",
							'".$next_trans['typeno']."',
							now())";
							DB_query($SQL,$db,'ERROR: AL intentar guardar los la relacion de asignaciones','',true);

            } /*end if the new allocation is different to what it was before */

		}  /*end of the loop through the array of allocations made */

		/*Now update the receipt or credit note with the amount allocated
		and the new diff on exchange */


		if (abs($TotalAllocated+$_SESSION['Alloc']->TransAmt)<0.01){
		   $Settled = 1;
		} else {
		   $Settled = 0;
		}

		$SQL = 'UPDATE debtortrans SET alloc = ' .  -$TotalAllocated . ', diffonexch = ' . -$TotalDiffOnExch . ', settled=' . $Settled . ' WHERE id = ' . $_POST['AllocTrans'];

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
      		$Result=DB_query('COMMIT',$db,$ErrMsg,$DbgMsg,true);
	/*finally delete the session variables holding all the previous data */
		unset($_SESSION['Alloc']);
		unset($_POST['AllocTrans']);
	} /* end of processing required if there were no input errors trapped */
}

/*The main logic determines whether the page is called with a customer code
a specific transaction or with no parameters ie else
If with a customer code show just that customer's receipts and credits for allocating
If with a specific receipt or credit show the invoices and credits available
for allocating to  */

If (isset($_GET['AllocTrans'])){

	/*page called with a specific transaction ID for allocating
	DebtorNo may also be set but this is the logic to follow
	the DebtorNo logic is only for showing the receipts and credits to allocate


	So need to set up the session variables and populate from the DB
	subsequent calls do not need this until the page is called with another
	AllocTrans from a link ie a GET
	Calls from a POST assume the SESSION vbles are already set up from the GET*/

	/*The logic is:
	- read in the transaction into a session class variable
	- read in the invoices available for allocating to into a session array of allocs object
	- Display the customer name the transaction being allocated amount and trans no
	- Display the invoices for allocating to with a form entry for each one
	for the allocated amount to be entered */

	if (isset($_SESSION['Alloc'])){
		unset($_SESSION['Alloc']->Allocs);
		unset($_SESSION['Alloc']);
	}

	$_SESSION['Alloc'] = new Allocation;
	/*The session varibale AllocTrans is set from the passed variable AllocTrans
	on the first pass */
	$_POST['AllocTrans']=$_GET['AllocTrans'];

	if($_SESSION['AllowAllocate']==1){

		// bowikaxu realhost - may 2007 - not allowed to allocate a cancelled invoice
		$SQL= "SELECT systypes.typename,
				debtortrans.type,
				debtortrans.transno,
				debtortrans.trandate,
				debtortrans.debtorno,
				debtorsmaster.name,
				rate,
				(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) as total2,
				debtortrans.diffonexch,
				debtortrans.alloc,
				-banktrans.amountcleared AS total
			FROM debtortrans,
				systypes,
				debtorsmaster,
				banktrans
			WHERE debtortrans.type = systypes.typeid
			AND debtortrans.debtorno = debtorsmaster.debtorno

			AND debtortrans.rh_status != 'C'

			AND banktrans.type = debtortrans.type
			AND banktrans.transno = debtortrans.transno
			AND banktrans.amountcleared > 0

			AND debtortrans.id=" . $_POST['AllocTrans'];

	}else {
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
				debtortrans.alloc
			FROM debtortrans,
				systypes,
				debtorsmaster
			WHERE debtortrans.type = systypes.typeid
			AND debtortrans.debtorno = debtorsmaster.debtorno

			AND debtortrans.rh_status != 'C'

			AND debtortrans.id=" . $_POST['AllocTrans'];
	}

	$ErrMsg = _('There was a problem retrieving the information relating the transaction selected') . '. ' . _('Allocations are unable to proceed') . '.';
	$DbgMsg = _('The following SQL to delete the allocation record was used');
	$Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	$myrow = DB_fetch_array($Result);

	// bowikaxu realhost Feb 2008 - not a valid transaction or not conciliated
	if(DB_num_rows($Result)<1){
		prnMsg(_('There was a problem retrieving the information relating the transaction selected').'<BR>'._("Transaction not found or Not conciliated") ,'error');
	   $InputError=1;
	   //include('includes/footer.inc');
	   exit;
	}

	$_SESSION['Alloc']->AllocTrans = $_POST['AllocTrans'];
	$_SESSION['Alloc']->DebtorNo = $myrow['debtorno'];
	$_SESSION['Alloc']->CustomerName = $myrow['name'];
	$_SESSION['Alloc']->TransType = $myrow['type'];
	$_SESSION['Alloc']->TransTypeName = $myrow['typename'];
	$_SESSION['Alloc']->TransNo = $myrow['transno'];
	$_SESSION['Alloc']->TransExRate = $myrow['rate'];
	$_SESSION['Alloc']->TransAmt = $myrow['total'];
	$_SESSION['Alloc']->PrevDiffOnExch = $myrow['diffonexch'];
	$_SESSION['Alloc']->TransDate = ConvertSQLDate($myrow['trandate']);

	/* Now populate the array of possible (and previous actual) allocations for this customer */
	/*First get the transactions that have outstanding balances ie Total-Alloc >0 */

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
		ORDER BY debtortrans.trandate";

    $ErrMsg = _('There was a problem retrieving the transactions available to allocate to');
    $DbgMsg = _('The following SQL to delete the allocation record was used');
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

	/* Now get trans that might have previously been allocated to by this trans
	NB existing entries where still some of the trans outstanding entered from
	above logic will be overwritten with the prev alloc detail below */

	$SQL= "SELECT debtortrans.id,
			typename,
			transno,
			trandate,
			rate,
			ovamount+ovgst+ovfreight+ovdiscount AS total,
			diffonexch,
			debtortrans.alloc-custallocns.amt AS prevallocs,
			amt,
			custallocns.id AS allocid
		FROM debtortrans,
			systypes,
			custallocns
		WHERE debtortrans.type = systypes.typeid
		AND debtortrans.id=custallocns.transid_allocto
		AND custallocns.transid_allocfrom=" . $_POST['AllocTrans'] . "
		AND debtorno='" . $_SESSION['Alloc']->DebtorNo . "'
		ORDER BY debtortrans.trandate";

    $ErrMsg = _('There was a problem retrieving the previously allocated transactions for modification');
    $DbgMsg = _('The following SQL to delete the allocation record was used');
    $Result=DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

	while ($myrow=DB_fetch_array($Result)){

		// bowikaxu realhost - get external invoice number
		$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
		    		WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
		    		AND locations.loccode = rh_invoicesreference.loccode";
		    		$res3 = DB_query($sql,$db);
		    		$ext = DB_fetch_array($res3);

		$DiffOnExchThisOne = ($myrow['amt']/$myrow['rate']) - ($myrow['amt']/$_SESSION['Alloc']->TransExRate);

		$_SESSION['Alloc']->add_to_AllocsAllocn ($myrow['id'],
							$myrow['typename'],
							$myrow['transno'],
							ConvertSQLDate($myrow['trandate']),
							$myrow['amt'],
							$myrow['total'],
							$myrow['rate'],
							$DiffOnExchThisOne,
							($myrow['diffonexch'] - $DiffOnExchThisOne),
							$myrow['prevallocs'],
							$myrow['allocid'],
							$ext['rh_serie'].$ext['extinvoice']);

	}
}

if (isset($_POST['AllocTrans'])){

	echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";
	echo "<INPUT TYPE=HIDDEN NAME='AllocTrans' VALUE=" . $_POST['AllocTrans'] . '>';

	/*Show the transaction being allocated and the potential trans it could be allocated to
        and those where there is already an existing allocation */

        echo '<HR><CENTER><FONT COLOR=BLUE>'._('Allocation of customer'). ' ' . $_SESSION['Alloc']->TransTypeName . ' '._('number').' ' . $_SESSION['Alloc']->TransNo . ' '._('from').' ' . $_SESSION['Alloc']->DebtorNo . ' - <B>' . $_SESSION['Alloc']->CustomerName . '</B>, '._('dated').' ' . $_SESSION['Alloc']->TransDate;

        if ($_SESSION['Alloc']->TransExRate!=1){
	     echo '<BR>'._('Amount in customer currency').' <B>' . number_format(-$_SESSION['Alloc']->TransAmt,2) . '</B><i> ('._('converted into local currency at an exchange rate of'). ' ' . $_SESSION['Alloc']->TransExRate . ')</i><P>';
        } else {
	     echo '<BR>'._('Transaction total'). ': <B>' . -$_SESSION['Alloc']->TransAmt . '</B>';
        }

        echo '<HR>';
   /*Now display the potential and existing allocations put into the array above */

   	$TableHeader = "<TR>
				<TD class='tableheader'>"._('Type')."</TD>
				<TD class='tableheader'>"._('Trans').'<BR>'._('Number')."</TD>
				<TD class='tableheader'>"._('Comments')."</TD>
				<TD class='tableheader'>"._('Trans').'<BR>'._('Date')."</TD>
				<TD class='tableheader'>"._('Total').'<BR>'._('Amount')."</TD>
				<TD class='tableheader'>"._('Yet to').'<BR>'._('Allocate')."</TD>
				<TD class='tableheader'>"._('This').'<BR>'._('Allocation')."</TD>
				<TD class='tableheader'>"._('Running').'<BR>'._('Balance')."</TD>
			</TR>";

        echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=0>';

        $k=0;
        $RowCounter =0;
	    $Counter = 0;
        $TotalAllocated =0;
        foreach ($_SESSION['Alloc']->Allocs as $AllocnItem) {
	    /*Alternate the background colour for each potential allocation line */

	    // bowikaxu realhost - show transactions reference
	    //SAINTS
	    $sql = "SELECT debtortrans.reference,
				c.serie, c.folio
                FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id WHERE
	    		debtorno='" . $_SESSION['Alloc']->DebtorNo . "'
	    		AND debtortrans.id = ".$AllocnItem->ID."";
	    $refres = mysql_query($sql,$db);
	    $ref = mysql_fetch_array($refres);
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
	    $YetToAlloc = ($AllocnItem->TransAmount - $AllocnItem->PrevAlloc);
		// bowikaxu si es factura obtener numero externo
	    if($AllocnItem->TransType == 'Factura'){

			$sql2 = "SELECT rh_invoicesreference.extinvoice, locations.loccode, locations.rh_serie FROM rh_invoicesreference, locations
					WHERE rh_invoicesreference.intinvoice = ".$AllocnItem->TypeNo."
					AND locations.loccode = rh_invoicesreference.loccode";
				$Res = DB_query($sql2,$db);
				$ExtRes = DB_fetch_array($Res);
			//SAINTS
			//$sql3="SELECT serie, folio FROM rh_cfd__cfd WHERE fk_transno=".$AllocnItem->TypeNo;
			/*
			 * rleal
			 * May 31 2011
			 * Se comenta la line a de arriba y se usa el id de debtortrans para evitar errores
			 */
			$sql3="SELECT serie, folio FROM rh_cfd__cfd WHERE id_debtortrans=".$AllocnItem->ID;
			$Res3=DB_query($sql3,$db);
			$ExtRes3=DB_fetch_array($Res3);
			//SAINTS
			if($ExtRes3['serie']!="")
				{echo "<TD>$AllocnItem->TransType</TD>
		<TD>".$ExtRes3['serie']."-".$ExtRes3['folio']." (".$AllocnItem->TypeNo.")</TD>
	    	<TD>".$ref['reference']."</TD>
		<TD ALIGN=RIGHT>".$AllocnItem->TransDate."</TD>
		<TD ALIGN=RIGHT>" . number_format($AllocnItem->TransAmount,2) . '</TD>
		<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2);}

			//SAINTS
			else
				{echo "<TD>$AllocnItem->TransType</TD>
		<TD>".$ExtRes['rh_serie'].$ExtRes['extinvoice']." (".$AllocnItem->TypeNo.")</TD>
	    	<TD>".$ref['reference']."</TD>
		<TD ALIGN=RIGHT>".$AllocnItem->TransDate."</TD>
		<TD ALIGN=RIGHT>" . number_format($AllocnItem->TransAmount,2) . '</TD>
		<TD ALIGN=RIGHT>' . number_format($YetToAlloc,2);}


		}else {//SAINTS
		  if($ref['folio']!="")
			{echo "<TD>$AllocnItem->TransType</TD>
	    	 <TD>$AllocnItem->TypeNo</TD>
	    	 <TD>".$ref['serie'].$ref['folio']."</TD>
			 <TD ALIGN=RIGHT>$AllocnItem->TransDate</TD>
			 <TD ALIGN=RIGHT>" . number_format($AllocnItem->TransAmount,2) . '</TD>
			 <TD ALIGN=RIGHT>' . number_format($YetToAlloc,2);}

		  //SAINTS
		  else{echo "<TD>$AllocnItem->TransType</TD>
	    	 <TD>$AllocnItem->TypeNo</TD>
	    	 <TD>".$ref['reference']."</TD>
			 <TD ALIGN=RIGHT>$AllocnItem->TransDate</TD>
			 <TD ALIGN=RIGHT>" . number_format($AllocnItem->TransAmount,2) . '</TD>
			 <TD ALIGN=RIGHT>' . number_format($YetToAlloc,2);}
		}


	    if ($AllocnItem->TransAmount < 0) {
            	$balance+=$YetToAlloc;
	    	echo '</TD><td></td><td align=right>' . number_format($balance,2) . '<td></TR>';
	    } else {
	    	echo "<input type=hidden name='YetToAlloc" . $Counter . "' value=" . round($YetToAlloc,2) . '></TD>';
	    	echo "<TD ALIGN=RIGHT><input type='checkbox' name='All" .  $Counter . "'";
	    	if (ABS($AllocnItem->AllocAmt-$YetToAlloc)<0.01){
			echo ' VALUE=' . True . '>';
	    	} else {
	    		echo '>';
	    	}
		$balance+=$YetToAlloc-$AllocnItem->AllocAmt;
	    	echo "<input type=text name='Amt" . $Counter ."' maxlength=12 SIZE=13 value=" . round($AllocnItem->AllocAmt,2) . "><input type=hidden name='AllocID" . $Counter . "' value=" . $AllocnItem->ID . '></TD><td align=right>' . number_format($balance,2) . '<td></TR>';
	    }

	    $TotalAllocated =$TotalAllocated + round($AllocnItem->AllocAmt,2);

	    $Counter++;

   }


   echo "<TR>
   		<TD COLSPAN=5 ALIGN=RIGHT><B>"._('Total Allocated').':</B></TD>
		<TD ALIGN=RIGHT><B><U>' . number_format($TotalAllocated,2) . '</U></B></TD>
	</TR>';

   echo '<TR>
   		<TD COLSPAN=5 ALIGN=RIGHT><B>'._('Left to allocate').'</B></TD>
   		<TD ALIGN=RIGHT><B>' . number_format(-$_SESSION['Alloc']->TransAmt - $TotalAllocated,2) . '</B></TD>
	</TR>
	</TABLE>';

   echo "<INPUT TYPE=HIDDEN NAME='TotalNumberOfAllocs' VALUE=$Counter>";

   echo "<INPUT TYPE=SUBMIT NAME='RefreshAllocTotal' VALUE="._('Recalculate Total To Allocate').'>';
   echo "<INPUT TYPE=SUBMIT NAME=UpdateDatabase VALUE="._('Process Allocations').'>';

} elseif(isset($_GET['DebtorNo'])){
  /*page called with customer code  so show the transactions to allocate
  specific to the customer selected */

  /*Clear any previous allocation records */
  unset($_SESSION['Alloc']->Allocs);
  unset($_SESSION['Alloc']);

  // bowikaxu realhost Feb 2008 - ver solo asignaciones conciliadas
  if($_SESSION['AllowAllocate']==1){ // solo si esta conciliada
  	$sql = "SELECT debtortrans.id,
  		debtortrans.transno,
		systypes.typename,
		debtortrans.type,
		debtortrans.debtorno,
		debtorsmaster.name,
		debtortrans.trandate,
		debtortrans.reference,
		rate,
		debtortrans.ovamount+debtortrans.ovgst-debtortrans.ovdiscount+debtortrans.ovfreight AS total2,
		debtortrans.alloc,
		-banktrans.amountcleared AS total
	FROM debtortrans,
		debtorsmaster,
		systypes,
		banktrans
	WHERE debtortrans.type=systypes.typeid
	AND debtortrans.debtorno=debtorsmaster.debtorno
	AND debtortrans.debtorno='" . $_GET['DebtorNo'] . "'
	AND (type=12 or type=11)
	AND settled=0
	AND banktrans.type = debtortrans.type
	AND banktrans.transno = debtortrans.transno
	AND banktrans.amountcleared > 0
	ORDER BY debtortrans.id";
  }else {
  	$sql = "SELECT id,
  		transno,
		typename,
		type,
		debtortrans.debtorno,
		name,
		trandate,
		reference,
		rate,
		ovamount+ovgst-ovdiscount+ovfreight AS total,
		alloc
	FROM debtortrans,
		debtorsmaster,
		systypes
	WHERE debtortrans.type=systypes.typeid
	AND debtortrans.debtorno=debtorsmaster.debtorno
	AND debtortrans.debtorno='" . $_GET['DebtorNo'] . "'
	AND (type=12 or type=11)
	AND settled=0
	ORDER BY debtortrans.id";
  }

  $result = DB_query($sql,$db);
  if (DB_num_rows($result)==0){
  	prnMsg(_('There are no outstanding receipts or credits yet to be allocated for this customer'),'info');
	include('includes/footer.inc');
      	exit;
  }
  echo '<CENTER><table>';
  echo "<tr>
  		<td class='tableheader'>"._('Trans Type')."</td>
		<td class='tableheader'>"._('Customer')."</td>
		<td class='tableheader'>"._('Number')."</td>
		<td class='tableheader'>"._('Date')."</td>
		<td class='tableheader'>"._('Total')."</td>
		<td class='tableheader'>"._('To Alloc')."</td>
		</tr><BR>";
  /* set up table of TransType - Customer - Trans No - Date - Total - Left to alloc  */

  $k=0; //row colour counter

  while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}

	printf("<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td ALIGN=RIGHT>%0.2f</td>
		<td ALIGN=RIGHT>%0.2f</td>
		<td><a href='%sAllocTrans=%s'>%s</td>
		</tr>",
		$myrow['typename'],
		$myrow['name'],
		$myrow['transno'],
		ConvertSQLDate($myrow['trandate']),
		$myrow['total'],
		$myrow['total']-$myrow['alloc'],
		$_SERVER['PHP_SELF'] . '?' . SID,
		$myrow['id'],
		_('Allocate'));
  }
} else { /* show all outstanding receipts and credits to be allocated */
  /*Clear any previous allocation records */
  unset($_SESSION['Alloc']->Allocs);
  unset($_SESSION['Alloc']);

	// bowikaxu realhost March 2008 - debug view allow allocate variable
  //echo "<hr>".$_SESSION['AllowAllocate']."<hr>";

  // bowikaxu realhost Feb 2008 - ver solo asignaciones conciliadas
  if($_SESSION['AllowAllocate']==1){ // solo si esta conciliada

  $sql = "SELECT debtortrans.id,
  		debtortrans.transno,
		systypes.typename,
		debtortrans.type,
		debtortrans.debtorno,
		debtorsmaster.name,
		debtortrans.trandate,
		debtortrans.reference,
		rate,
		debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovdiscount+debtortrans.ovfreight as total2,
		debtortrans.alloc,
		-banktrans.amountcleared AS total
	FROM debtortrans,
		debtorsmaster,
		systypes,
		banktrans
	WHERE debtortrans.type=systypes.typeid
	AND debtortrans.debtorno=debtorsmaster.debtorno
	AND (debtortrans.type=12 or debtortrans.type=11)
	AND SETTLED=0
	AND banktrans.type = debtortrans.type
	AND banktrans.transno = debtortrans.transno
	AND banktrans.amountcleared>0
	AND debtortrans.ovamount<0
	ORDER BY debtortrans.id";
  }else {
  	$sql = "SELECT id,
  		debtortrans.transno,
		typename,
		type,
		debtortrans.debtorno,
		name,
		trandate,
		reference,
		rate,
		ovamount+ovgst+ovdiscount+ovfreight as total,
		alloc
	FROM debtortrans,
		debtorsmaster,
		systypes
	WHERE debtortrans.type=systypes.typeid
	AND debtortrans.debtorno=debtorsmaster.debtorno
	AND (type=12 or type=11)
	AND SETTLED=0
	AND ABS(debtortrans.ovamount+ovgst+ovdiscount+ovfreight-alloc)>".$rh_umbral_asignacion."
	ORDER BY id";
  }

  $result = DB_query($sql,$db);

  echo '<CENTER><table>';
  echo "<tr>
  		<td class='tableheader'>"._('Trans Type')."</td>
  		<td class='tableheader'>"._('Reference')."</td>
		<td class='tableheader'>"._('Customer')."</td>
	    <td class='tableheader'>"._('Código.')."</td>
		<td class='tableheader'>"._('Number')."</td>
		<td class='tableheader'>"._('Date')."</td>
		<td class='tableheader'>"._('Total')."</td>
		<td class='tableheader'>"._('To Alloc')."</td>
	</tr><BR>";
  /* set up table of Tran Type - Customer - Trans No - Date - Total - Left to alloc  */

  $k=0; //row colour counter

  while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}

	printf("<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td ALIGN=RIGHT>%0.2f</td>
		<td ALIGN=RIGHT>%0.2f</td>
		<td><a href='%sAllocTrans=%s'>%s</td>
		</tr>",
		$myrow['typename'],
		substr($myrow['reference'],0,20),
		$myrow['name'],
		$myrow['debtorno'],
		$myrow['transno'],
		ConvertSQLDate($myrow['trandate']),
		$myrow['total'],
		$myrow['total']-$myrow['alloc'],
		$_SERVER['PHP_SELF'] . '?' . SID,
		$myrow['id'],
		_('Allocate'));

  }
  //END WHILE LIST LOOP
  echo '</table></CENTER>';
  if (DB_num_rows($result) == 0) {
	prnMsg(_('There are no allocations to be done'),'info');
  }

} /* end of else if not a debtorno or transaction called with the URL */


include('includes/footer.inc');

?>
