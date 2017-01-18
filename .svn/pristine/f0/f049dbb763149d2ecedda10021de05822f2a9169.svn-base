<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-03-15 13:16:28 -0600 (Sat, 15 Mar 2008) $
 * $Rev: 123 $
 */

$PageSecurity = 3;
include('includes/SQL_CommonFunctions.inc');
include ('includes/session.inc');
require_once('Numbers/Words.php');

$InputError=0;

if (!isset($_POST['CheqNumber'])){


     $title = _('Poliza de Cheque');
     include ('includes/header.inc');

     if ($InputError==1){
	prnMsg($msg,'error');
     }
     echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '>';
     echo '<CENTER><TABLE>
     			<TR>
				<TD>
				<SELECT NAME="transtype">
				<OPTION SELECTED VALUE=0>'._('Trans. N&uacute;mero').'
				<OPTION VALUE=1>'._('Cheque').
				'</SELECTED>';
				
				echo ":</TD>
				<TD><INPUT TYPE=text NAME='CheqNumber' MAXLENGTH=10 SIZE=10 VALUE=''></TD>
				</TR>";
     echo '<TR><TD>' . _('Tipo') . ' :</TD><TD><SELECT NAME=tipo>';

			$SQL="SELECT typeid, typename FROM systypes WHERE typeid IN (1,22)";
			$Result = DB_query($SQL,$db);
			while ($myrow = DB_fetch_array($Result)) {

				if ($_POST['tipo']==$myrow['typeid']){
					echo '<OPTION SELECTED VALUE="' . $myrow['typeid'] . '">' . $myrow['typename'];
				} else {
					echo '<OPTION VALUE="' . $myrow['typeid'] . '">' . $myrow['typename'];
				}
			}
			echo '</SELECT></TD></TR>';
		
	/*
     echo '<TR><TD>' . _('Email the report off') . ":</TD><TD><SELECT NAME='Email'>";
     echo "<OPTION SELECTED VALUE='No'>" . _('No');
     echo "<OPTION VALUE='Yes'>" . _('Yes');
     echo "</SELECT></TD></TR>";
     */
     echo "</TABLE><INPUT TYPE=SUBMIT NAME='Go' VALUE='" . _('Create PDF') . "'></CENTER>";

     include('includes/footer.inc');
     exit;
} else {
	include('includes/ConnectDB.inc');
}

$tipo = $_POST['tipo'];
if($_POST['transtype']==0){// busqueda por transaccion
	$searchvar = 'banktrans.transno = ';
}else { // busqueda por numero de cheque
	$searchvar = 'banktrans.rh_chequeno = ';
}


if($tipo==22){ // transaccion
$SQL= "SELECT banktrans.transno,
		banktrans.amount,
		banktrans.banktransid,
		banktrans.ref,
		banktrans.rh_chequeno,
		banktrans.transdate,
		banktrans.banktranstype,
		banktrans.type,
		banktrans.transno,
		chartmaster.accountname,
		supptrans.supplierno,
		suppliers.suppname
	FROM banktrans, chartmaster, supptrans, suppliers
	WHERE ".$searchvar." '".$_POST['CheqNumber']."'
	AND banktrans.type = 22
	AND chartmaster.accountcode = banktrans.bankact
	AND supptrans.type = banktrans.type
	AND supptrans.transno = banktrans.transno
	AND suppliers.supplierid = supptrans.supplierno";
}else if($tipo==1){
	$SQL= "SELECT banktrans.transno,
		banktrans.amount,
		banktrans.banktransid,
		banktrans.ref,
		banktrans.rh_chequeno,
		banktrans.transdate,
		banktrans.banktranstype,
		banktrans.type,
		banktrans.transno,
		chartmaster.accountname
	FROM banktrans, chartmaster
	WHERE ".$searchvar." '".$_POST['CheqNumber']."'
	AND banktrans.type = 1
	AND chartmaster.accountcode = banktrans.bankact";
}

$Result=DB_query($SQL,$db,'','',false,false);
if (DB_error_no($db)!=0){
	$title = _('Poliza de Cheque');
	include('includes/header.inc');
	prnMsg(_('An error occurred getting the payments'),'error');
	if ($Debug==1){
        	prnMsg(_('The SQL used to get the receipt header information that failed was') . ':<BR>' . $SQL,'error');
	}
	include('includes/footer.inc');
  	exit;
} elseif (DB_num_rows($Result)==0){
	$title = _('Poliza de Cheque');
	include('includes/header.inc');
  	prnMsg (_('No se encontraron transacciones con ese Numero de Transaccion y Tipo'), 'error');
	include('includes/footer.inc');
  	exit;
}

include('includes/PDFStarter.php');

/*PDFStarter.php has all the variables for page size and width set up depending on the users default preferences for paper size */

$pdf->addinfo('Title',_('Poliza de Cheque'));
$pdf->addinfo('Subject',_('Poliza de Cheque') . '  #' . $_POST['CheqNumber']);
$pdf->setFont("Arial","B",12);
$line_height=12;
$PageNumber = 1;

$TotalCheques = 0;

//include ('includes/rh_PDFChequePolizaHeader.inc');

while ($myrow=DB_fetch_array($Result)){
	
		if($PageNumber == 1){
			include ('includes/rh_PDFChequePolizaHeader.inc');			
		}
	
   	//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,number_format(-$myrow['amount'],2), 'right');
	//$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,90,$FontSize,$myrow['ref'], 'left');

	$sql = 'SELECT accountname,
			accountcode,
			amount,
			narrative
		FROM gltrans,
			chartmaster
		WHERE chartmaster.accountcode=gltrans.account
		AND gltrans.typeno =' . $myrow['transno'] . '
		AND gltrans.type=' . $myrow['type'];

	$GLTransResult = DB_query($sql,$db,'','',false,false);
	if (DB_error_no($db)!=0){
		$title = _('Payment Listing');
		include('includes/header.inc');
   		prnMsg(_('An error occurred getting the GL transactions'),'error');
		if ($debug==1){
        		prnMsg( _('The SQL used to get the receipt header information that failed was') . ':<BR>' . $sql, 'error');
		}
		include('includes/footer.inc');
  		exit;
	}
	while ($GLRow=DB_fetch_array($GLTransResult)){
		$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,200,$FontSize,$GLRow['accountname'].' ('.$GLRow['accountcode'].')', 'left');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+210,$YPos,60,$FontSize,number_format($GLRow['amount'],2), 'right');
		$LeftOvers = $pdf->addTextWrap($Left_Margin+330,$YPos,130,$FontSize,$GLRow['narrative'], 'left');
		$YPos -= ($line_height);
		if ($YPos - (2 *$line_height) < $Bottom_Margin){
          		/*Then set up a new page */
              		$PageNumber++;
	      		include ('includes/rh_PDFChequePolizaHeader.inc');
      		} /*end of new page header  */
	}
	DB_free_result($GLTransResult);

      $YPos -= ($line_height);

      if ($YPos - (2 *$line_height) < $Bottom_Margin){
          /*Then set up a new page */
              $PageNumber++;
	      include ('includes/rh_PDFChequePolizaHeader.inc');
      } /*end of new page header  */
} /* end of while there are customer receipts in the batch to print */


$YPos-=$line_height;
//$LeftOvers = $pdf->addTextWrap($Left_Margin,$YPos,60,$FontSize,number_format($TotalCheques,2), 'right');
//$LeftOvers = $pdf->addTextWrap($Left_Margin+65,$YPos,300,$FontSize,_('TOTAL') . ' ' . $Currency . ' ' . _('CHEQUES'), 'left');


$pdfcode = $pdf->output();
$len = strlen($pdfcode);
header('Content-type: application/pdf');
header('Content-Length: ' . $len);
header('Content-Disposition: inline; filename=ChequePoliza.pdf');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

$pdf->stream();
$_POST['Email']='No';
if ($_POST['Email']=='Yes'){
	if (file_exists($_SESSION['reports_dir'] . '/ChequePoliza.pdf')){
		unlink($_SESSION['reports_dir'] . '/ChequePoliza.pdf');
	}
    	$fp = fopen( $_SESSION['reports_dir'] . '/ChequePoliza.pdf','wb');
	fwrite ($fp, $pdfcode);
	fclose ($fp);

	include('includes/htmlMimeMail.php');

	$mail = new htmlMimeMail();
	$attachment = $mail->getFile($_SESSION['reports_dir'] . '/ChequePoliza.pdf');
	$mail->setText(_('Please find herewith payments listing from') . ' ' . $_POST['FromDate'] . ' ' . _('to') . ' ' . $_POST['ToDate']);
	$mail->addAttachment($attachment, 'PaymentListing.pdf', 'application/pdf');
	$mail->setFrom(array('"' . $_SESSION['CompanyRecord']['coyname'] . '" <' . $_SESSION['CompanyRecord']['email'] . '>'));

	/* $ChkListingRecipients defined in config.php */
	$result = $mail->send('bowikaxu@gmail.com');
}

?>