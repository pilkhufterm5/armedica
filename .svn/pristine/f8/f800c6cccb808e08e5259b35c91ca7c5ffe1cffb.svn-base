<?php

/* $Revision: 14 $ */

$PageSecurity = 5;
$PaperSize = 'cheque';
include('includes/DefinePaymentClass.php');
include('includes/session.inc');
include('includes/PDFStarter.php');
include('Numbers/Words.php');

if($_SESSION['PaymentDetail']->Amount == '' || $_SESSION['PaymentDetail']->Amount = 0 ){
$TotalAmount =0;
  foreach ($_SESSION['PaymentDetail']->GLItems AS $PaymentItem) {
	  $TotalAmount += $PaymentItem->Amount;
  }
	$_SESSION['PaymentDetail']->Amount = $TotalAmount;
	
}

$result = db_query("SELECT hundredsname FROM currencies WHERE currabrev='" . $_SESSION['PaymentDetail']->Currency . "'",$db);
$CurrencyRow = db_fetch_row($result);
$HundredsName = $CurrencyRow[0];

$FontSize=10;

$pdf->addinfo('Title', _('Print Cheque'));
$pdf->addinfo('Subject', _('Print Cheque'));

$PageNumber=1;

$line_height=1;

// cheque
$FontSize=10;

$LeftOvers = $pdf->addTextWrap(10.5,-3.8,100,$FontSize,$_GET['ChequeNum'], 'left');					

//$LeftOvers = $pdf->addTextWrap($Page_Width-5,$YPos,475,$FontSize,'amountWords'.$AmountWords, 'left');
$YPos -= 1*$line_height;
$LeftOvers = $pdf->addTextWrap($Page_Width-9.5,-0.8,100,$FontSize,$_SESSION['PaymentDetail']->DatePaid, 'left');

$LeftOvers = $pdf->addTextWrap($Page_Width-11.5,-1.35,100,$FontSize,$_SESSION['PaymentDetail']->Narrative, 'left');

$YPos -= 1*$line_height; 
//$LeftOvers = $pdf->addTextWrap(5,$YPos,300,$FontSize,'SuppName'.$_SESSION['PaymentDetail']->SuppName, 'left');
$LeftOvers = $pdf->addTextWrap($Page_Width-3,-1,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');
//$YPos -= 1*$line_height;
//$LeftOvers = $pdf->addTextWrap(75,$YPos,300,$FontSize,$_SESSION['PaymentDetail']->Address1, 'left');
//$YPos -= 1*$line_height;
//$LeftOvers = $pdf->addTextWrap(75,$YPos,300,$FontSize,$_SESSION['PaymentDetail']->Address2, 'left');
//$YPos -= 1*$line_height;
//$Address3 = $_SESSION['PaymentDetail']->Address3 . ' ' . $_SESSION['PaymentDetail']->Address4 . ' ' . $_SESSION['PaymentDetail']->Address5 . ' ' . $_SESSION['PaymentDetail']->Address6;
//$LeftOvers = $pdf->addTextWrap(75,$YPos,300,$FontSize, $Address3, 'left');

if($_SESSION['PaymentDetail']->Currency=='MN'){
        $curr = ' pesos ';
}else if($_SESSION['PaymentDetail']->Currency=='USD'){

        $curr = ' dolares ';
}

$sql = "SELECT currency FROM currencies WHERE currabrev = '".$_SESSION['PaymentDetail']->Currency."'";
$curr_res = DB_query($sql,$db);
$currencystr = DB_fetch_array($curr_res);


$tot = explode(".",$_SESSION['PaymentDetail']->Amount);
//$pdf->addTextWrap(250,81,81,10,$Total, 'right');
$Letra = Numbers_Words::toWords($tot[0],"es");

if($tot[1]==0){
$ConLetra = $Letra.' '.$currencystr['currency']." 00/100 ".$myrow['currcode'];
}else if(strlen($tot[1])>=2){
$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."/100 ".$myrow['currcode'];
}else {
$ConLetra = $Letra.' '.$currencystr['currency'].' '.$tot[1]."0/100 ".$myrow['currcode'];
}


//$Locale = 'es';
//$AmountWords = Numbers_Words::toWords(intval($_SESSION['PaymentDetail']->Amount),$Locale);
//$AmountWords .= ' ' . _('and') . ' ' .  Numbers_Words::toWords(intval(($_SESSION['PaymentDetail']->Amount - intval($_SESSION['PaymentDetail']->Amount))*100),$Locale);// . ' ' . $HundredsName;

$YPos -= 2*$line_height;
$LeftOvers = $pdf->addTextWrap(75,$YPos+7,300,$FontSize,'hola mundo'. $ConLetra, 'left');
//$LeftOvers = $pdf->addTextWrap(375,$YPos,100,$FontSize, number_format($_SESSION['PaymentDetail']->Amount,2), 'right');


$LeftOvers = $pdf->addTextWrap(2,-2.1,475,$FontSize,$ConLetra, 'left');
$YPos -= 1*$line_height;



// remittance advice 1
$YPos -= 14*$line_height;
//$LeftOvers = $pdf->addTextWrap(0,$YPos,$Page_Width,$FontSize,_('Remittance Advice'), 'center');
$YPos -= 2*$line_height;
//$LeftOvers = $pdf->addTextWrap(25,$YPos,75,$FontSize,_('DatePaid'), 'left');
//$LeftOvers = $pdf->addTextWrap(100,$YPos,100,$FontSize,_('Vendor No.'), 'left');
//$LeftOvers = $pdf->addTextWrap(250,$YPos,75,$FontSize,_('Cheque No.'), 'left');
//$LeftOvers = $pdf->addTextWrap(350,$YPos,75,$FontSize,_('Amount'), 'left');
$YPos -= 2*$line_height;
//$LeftOvers = $pdf->addTextWrap(25,$YPos,75,$FontSize,$_SESSION['PaymentDetail']->DatePaid, 'left');
//$LeftOvers = $pdf->addTextWrap(100,$YPos,100,$FontSize,$_SESSION['PaymentDetail']->SupplierID, 'left');
$LeftOvers = $pdf->addTextWrap(12,-3.8,75,$FontSize,$_SESSION['PaymentDetail']->Account, 'left');
$LeftOvers = $pdf->addTextWrap(5,$YPos-3,150,$FontSize,$_SESSION['PaymentDetail']->SuppName, 'left');
$LeftOvers = $pdf->addTextWrap(7,$YPos-3,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');
$LeftOvers = $pdf->addTextWrap(8,$YPos-3,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');

$LeftOvers = $pdf->addTextWrap(7,$YPos,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');
$LeftOvers = $pdf->addTextWrap(5,$YPos,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');

//// remittance advice 2
//$YPos -= 15*$line_height;
//$LeftOvers = $pdf->addTextWrap(0,$YPos,$Page_Width,$FontSize,_('Remittance Advice'), 'center');
//$YPos -= 2*$line_height;
//$LeftOvers = $pdf->addTextWrap(25,$YPos,75,$FontSize,_('DatePaid'), 'left');
//$LeftOvers = $pdf->addTextWrap(100,$YPos,100,$FontSize,_('Vendor No.'), 'left');
//$LeftOvers = $pdf->addTextWrap(250,$YPos,75,$FontSize,_('Cheque No.'), 'left');
//$LeftOvers = $pdf->addTextWrap(350,$YPos,75,$FontSize,_('Amount'), 'left');
//$YPos -= 2*$line_height;
//$LeftOvers = $pdf->addTextWrap(25,$YPos,75,$FontSize,$_SESSION['PaymentDetail']->DatePaid, 'left');
//$LeftOvers = $pdf->addTextWrap(100,$YPos,100,$FontSize,$_SESSION['PaymentDetail']->SupplierID, 'left');
//$LeftOvers = $pdf->addTextWrap(250,$YPos,75,$FontSize,$_GET['ChequeNum'], 'left');
//$LeftOvers = $pdf->addTextWrap(350,$YPos,75,$FontSize,number_format(round($_SESSION['PaymentDetail']->Amount,2),2), 'left');

$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=1){
	$title = _('Print Check Error');
	include('includes/header.inc');
	prnMsg(_('Could not print the cheque'),'warn');
	include('includes/footer.inc');
	exit;
} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=Cheque.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	$pdf->Stream();
}
?>
