<?php

/* $Revision: 14 $ */
/* $Revision: 14 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Liberaci&oacute;n de cheques');
include('includes/header.inc');
include('includes/DefinePaymentClass.php');

if(isset($_POST['sndFrm'])){
	$_SESSION['PaymentDetail'] = new Payment;

    DB_query("BEGIN",$db);
    $Transtype = 22;

    $SQL="update rh_cheque_nofree set Free='".$_POST['codigo']."' where type=22 and transno='".$_POST['orderno']."'";
    DB_query($SQL,$db);
    $SQL="select * from rh_cheque_nofree where transno='".$_POST['orderno']."' and type=22";
    $rs=DB_query($SQL,$db);
    while($rw=DB_fetch_array($rs)){
        $_SESSION['PaymentDetail']->SupplierID = $rw['SupplierID'];
        $_SESSION['PaymentDetail']->Currency  = $rw['Currency'];
        $_SESSION['PaymentDetail']->Account = $rw['Account'];
        $_SESSION['PaymentDetail']->AccountCurrency = $rw['AccountCurrency'];
        $_SESSION['PaymentDetail']->CompAccount  = $rw['CompAccount'];
        if($rw['DatePaid']==FormatDateForSQL($_POST['dateFree'])){
            $_SESSION['PaymentDetail']->DatePaid  = $rw['DatePaid'];
            $PeriodNo=$rw['PeriodNo'];
        }else{
            $PeriodNo = GetPeriod($_POST['dateFree'],$db);
            $_SESSION['PaymentDetail']->DatePaid  = FormatDateForSQL($_POST['dateFree']);
        }
        $_SESSION['PaymentDetail']->ExRate  = $rw['ExRate'];
        $_SESSION['PaymentDetail']->FunctionalExRate  = $rw['FunctionalExRate'];
        $_SESSION['PaymentDetail']->Paymenttype  = $rw['Paymenttype'];
        $_SESSION['PaymentDetail']->Narrative = $rw['Narrative'];
        $_SESSION['PaymentDetail']->Amount   = $rw['Amount'];
        $_SESSION['PaymentDetail']->Discount = $rw['Discount'];
        $CreditorTotal=$rw['CreditorTotal'];
        $ChequeNum=$rw['ChequeNum'];
        $TransNo=$rw['transno'];
    }


		$SQL = "INSERT INTO supptrans (transno,
						type,
						supplierno,
						trandate,
						suppreference,
						rate,
						ovamount,
						transtext,
                        rh_enTransito) ";
		$SQL = $SQL . 'VALUES (' . $TransNo . ",
					22,
					'" . $_SESSION['PaymentDetail']->SupplierID . "',
					'" . $_SESSION['PaymentDetail']->DatePaid . "',
					'" . $_SESSION['PaymentDetail']->Paymenttype . "',
					" . ($_SESSION['PaymentDetail']->ExRate/$_SESSION['PaymentDetail']->FunctionalExRate) . ",
					" . (-$_SESSION['PaymentDetail']->Amount-$_SESSION['PaymentDetail']->Discount) . ",
					'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
                    0
				)";
		$result = DB_query($SQL,$db);

	$SQL = "UPDATE suppliers SET
		    lastpaiddate = '" . $_SESSION['PaymentDetail']->DatePaid . "',
		    lastpaid=" . $_SESSION['PaymentDetail']->Amount ."
			  WHERE suppliers.supplierid='" . $_SESSION['PaymentDetail']->SupplierID . "'";
	$result = DB_query($SQL,$db);

    $_SESSION['PaymentDetail']->Narrative = $_SESSION['PaymentDetail']->SupplierID . "-" . $_SESSION['PaymentDetail']->Narrative;

    if ($_SESSION['CompanyRecord']['gllink_creditors']==1){
			$SQL="INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
			$SQL=$SQL . "VALUES (
						22,
						" . $TransNo . ",
						'" . $_SESSION['PaymentDetail']->DatePaid . "',
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']['creditorsact'] . ",
						'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
						" . $CreditorTotal . "
					)";
			$ErrMsg = _('Cannot insert a GL transaction for the creditors account debit because');
			$DbgMsg = _('Cannot insert a GL transaction for the creditors account debit using the SQL');
			$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

			if ($_SESSION['PaymentDetail']->Discount !=0){
				$SQL="INSERT INTO gltrans ( type,
								typeno,
								trandate,
								periodno,
								account,
								narrative,
								amount) ";
				$SQL=$SQL . "VALUES (22,
						" . $TransNo . ",
						'" . $_SESSION['PaymentDetail']->DatePaid . "',
						" . $PeriodNo . ",
						" . $_SESSION['CompanyRecord']["pytdiscountact"] . ",
						'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
						" . (-$_SESSION['PaymentDetail']->Discount/$_SESSION['PaymentDetail']->ExRate/$_SESSION['PaymentDetail']->FunctionalExRate) . "
					  )";
				$result = DB_query($SQL,$db);
			}
		}

if ($_SESSION['CompanyRecord']['gllink_creditors']==1){
  	  if ($_SESSION['PaymentDetail']->Amount/$_SESSION['PaymentDetail']->ExRate !=0){
		  if($_SESSION['PaymentDetail']->Currency != $_SESSION['CompanyRecord']['currencydefault'] AND $_SESSION['PaymentDetail']->Currency == $_SESSION['PaymentDetail']->AccountCurrency){
			  if(isset($_SESSION['PaymentDetail']->FunctionalExRate)){
			  	$Frate = $_SESSION['PaymentDetail']->FunctionalExRate;
			  }else {
			  	$sql = "SELECT rate FROM currencies WHERE currabrev = '".$_SESSION['PaymentDetail']->Currency."'";
			  	$rate = DB_fetch_array(DB_query($sql,$db));
			  	$Frate = $rate['rate'];
			  }
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . $_SESSION['PaymentDetail']->DatePaid . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->Account . ",
						'" . $_SESSION['PaymentDetail']->Narrative . "',
						" . -($_SESSION['PaymentDetail']->Amount) . "
					)";
		  	$result = DB_query($SQL,$db);

		  	$CompNarrative = $_SESSION['PaymentDetail']->Amount . '/' . $Frate . '-' . $_SESSION['PaymentDetail']->Amount;
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . $_SESSION['PaymentDetail']->DatePaid . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->CompAccount . ",
						'" . $CompNarrative . " CC',
						" . -(($_SESSION['PaymentDetail']->Amount/$Frate)-$_SESSION['PaymentDetail']->Amount) . "
					)";
		  	$result = DB_query($SQL,$db);
		  }else {
		  	$SQL = "INSERT INTO gltrans ( type,
							typeno,
							trandate,
							periodno,
							account,
							narrative,
							amount) ";
		  	$SQL = $SQL . "VALUES (" . $Transtype . ",
						" . $TransNo . ",
						'" . $_SESSION['PaymentDetail']->DatePaid . "',
						" . $PeriodNo . ",
						" . $_SESSION['PaymentDetail']->Account . ",
						'" . $_SESSION['PaymentDetail']->Narrative . "',
						" . (-$_SESSION['PaymentDetail']->Amount/$_SESSION['PaymentDetail']->ExRate) . "
					)";
		  	$result = DB_query($SQL,$db);
		  }
	  }
  }

  $narrative = $_SESSION['PaymentDetail']->Narrative;
  $SQL="INSERT INTO banktrans (transno,
					type,
					bankact,
					ref,
					exrate,
					functionalexrate,
					transdate,
					banktranstype,
					amount,
					currcode,
					rh_chequeno) ";
  $SQL= $SQL . "VALUES (" . $TransNo . ",
				" . $Transtype . ",
				" . $_SESSION['PaymentDetail']->Account . ",
				'" . DB_escape_string($_SESSION['PaymentDetail']->Narrative) . "',
				" . $_SESSION['PaymentDetail']->ExRate . " ,
				" . $_SESSION['PaymentDetail']->FunctionalExRate . ",
				'" . $_SESSION['PaymentDetail']->DatePaid . "',
				'" . $_SESSION['PaymentDetail']->Paymenttype . "',
				" . -$_SESSION['PaymentDetail']->Amount . ",
				'" . $_SESSION['PaymentDetail']->Currency . "',
				'".DB_escape_string($ChequeNum)."'
			)";
	$result = DB_query($SQL,$db);

    DB_query("COMMIT",$db);
    unset($_SESSION['PaymentDetail']);
}

	if (true) {
		
			// bowikaxu realhost - may 2007 - rh_status
			$SQL = "Select transno,(DatePaid) as trandate,(Amount)as monto,Concat('Cheque',' (',ChequeNum,') ',Narrative) as transtext from rh_cheque_nofree where Free=0 and type=22 and SupplierID='".$_SESSION['SupplierID']."';";
	}

	$ErrMsg = _('No orders or quotations were returned by the SQL because');
	$SalesOrdersResult = DB_query($SQL,$db,$ErrMsg);

	/*show a table of the orders returned by the SQL */
    echo "<br /><center><h2>Liberaci&oacute;n de cheques</h2><center><br /><br />";
    if (isset($_SESSION['SupplierID'])){
	$SupplierName = '';
	$SQL = "SELECT suppliers.suppname
		FROM suppliers
		WHERE suppliers.supplierid ='" . $_SESSION['SupplierID'] . "'";

	    $SupplierNameResult = DB_query($SQL,$db);
	    if (DB_num_rows($SupplierNameResult)==1){
	        $myrow = DB_fetch_row($SupplierNameResult);
	        $SupplierName = $myrow[0];
	    }
    	echo '<FONT SIZE=3><P><B>' . $_SESSION['SupplierID']  . "-$SupplierName</B> ".'<BR><P></FONT>';
    }

	echo '<TABLE CELLPADDING=2 COLSPAN=7 WIDTH=100%>';

	if (true){
		$tableheader = "<TR>
				<TD class='tableheader'>" . _('Transaci&oacute;n') . "</TD>
                <TD class='tableheader'>" . _('Descripci&oacute;n') . "</TD>
                <TD class='tableheader'>" . _('Fecha') . "</TD>
                <TD class='tableheader'>" . _('Monto') . "</TD>
                <TD class='tableheader'>" . _('Accion') . "</TD></TR>";
	}
	
	echo $tableheader;

	$j = 1;
	$k=0; //row colour counter

	while ($myrow=DB_fetch_array($SalesOrdersResult)) {
        if ($k==1){
		    $color= '#CCCCCC';
			$k=0;
		} else {
		    $color= '#EEEEEE';
			$k++;
		}
        echo "<tr bgcolor='$color'>";

		$FormatedDelDate = ConvertSQLDate($myrow['trandate']);
		$FormatedOrderValue = number_format($myrow['monto'],2);

        $Cmb='';
        $Cmb.='<select onchange="setCheque(\''.$myrow['transno'].'\',this.value);" style="width:100%;" >';
        $Cmb.='<option value="%%%%%">Seleccione una opci&oacute;n</option>';
        $Cmb.='<option value="1">Liberar</option>';
        $Cmb.='</select>';

        $txt="<input name='Fecha' id='fecha_".$myrow['transno']."' value='".$FormatedDelDate."' style='width:100%;' />";

		if (true){
			printf("<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
                <td>%s</td>
				</tr>",
				$myrow['transno'],
    			$myrow['transtext'],
				$txt,
				$FormatedOrderValue,
                $Cmb);
		}

		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop

	echo '</TABLE>';

?>
<form method="post" name="frm">
    <input type="hidden" name="orderno" id="orderno" value="" />
    <input type="hidden" name="codigo" id="codigo" value="" />
    <input type="hidden" name="dateFree" id="dateFree" value="" />
    <input type="submit" name="sndFrm" id="sndFrm" value="Enviar" style="visibility:hidden;" />
</form>

<script>
    function setCheque(orderno,codigo){
        if(codigo!='%%%%%'){
            document.getElementById('orderno').value=orderno;
            document.getElementById('codigo').value=codigo;
            document.getElementById('dateFree').value=document.getElementById('fecha_'+orderno).value;
            document.getElementById('sndFrm').click();
        }
    }
</script>
<?php
include('includes/footer.inc');
?>