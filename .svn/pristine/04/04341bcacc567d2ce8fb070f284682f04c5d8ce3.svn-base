<?php
/* $Revision: 1.7 $ */
/* pdf-php by R&OS code to set up a new sales order page */
if ($PageNumber>1){
	$pdf->newPage();
}

$XPos = $Page_Width/2 - 85;
$pdf->addJpegFromFile(dirname(__FILE__).'/../companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',$Left_Margin,$Page_Height-70,0,60);
$FontSize=10;
$XPos = 46;
$pdf->addText($XPos, $Page_Height-90,$FontSize, _('Quotation'));//.' '.$Page_Width.' - '.$Page_Height);

$FontSize=10;
$YPos = $Page_Height-30;
$XPos = 310;
$pdf->addText($XPos, $YPos,$FontSize, utf8_decode($_SESSION['CompanyRecord']['coyname']));
//$pdf->addTextWrap($Left_Margin, $YPos,$Page_Width-$Right_Margin-$Left_Margin,$FontSize, $_SESSION['CompanyRecord']['coyname'],'right',0,0);
$FontSize =8;
/*echo "<pre>";
var_dump($_SESSION['CompanyRecord']);
echo "</pre>"; exit;*/

$pdf->addText($XPos, $YPos-12,$FontSize, $_SESSION['CompanyRecord']['regoffice1'].' '.$_SESSION['CompanyRecord']['regoffice2']);
//$pdf->addText($XPos, $YPos-21,$FontSize, $_SESSION['CompanyRecord']['regoffice2']);
$pdf->addText($XPos, $YPos-21/*30*/,$FontSize,trim($_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']));
$pdf->addText($XPos, $YPos-30/*30*/,$FontSize,utf8_decode(trim($_SESSION['CompanyRecord']['regoffice7'].', '.$_SESSION['CompanyRecord']['regoffice8']).', '.$_SESSION['CompanyRecord']['regoffice9'].' C.P. '.$_SESSION['CompanyRecord']['regoffice10']));
$pdf->addText($XPos, $YPos-39/*39*/,$FontSize, _('Ph') . ': ' . $_SESSION['CompanyRecord']['telephone'] . ' ' .$_SESSION['CompanyRecord']['site']);
$pdf->addText($XPos, $YPos-48/*48*/,$FontSize, $_SESSION['CompanyRecord']['email']);
$pdf->line($Page_Width-$Right_Margin-10, $YPos-65,$Left_Margin+10, $YPos-65);

/*$XPos = 46;
$YPos = $Page_Height-115;
$pdf->addText($XPos, $YPos-12,$FontSize, $myrow['locadd1']);
$pdf->addText($XPos, $YPos-21,$FontSize, $myrow['locadd2']);
$pdf->addText($XPos, $YPos-30,$FontSize, $myrow['locadd3'] . ' ' . $myrow['locadd4'] . ' ' . $myrow['locadd5']);
$pdf->addText($XPos, $YPos-39,$FontSize, _('Ph') . ' ' . $myrow['loctel'] . ' ' . _('Fax'). ': ' . $myrow['locfax']);
$pdf->addText($XPos, $YPos-48,$FontSize, $myrow['locemail']);
*/
/*$XPos = 46;
$YPos = $Page_Height-70;

$FontSize=9;
$pdf->SetFont('Arial','B',12);
$pdf->addText($XPos, $YPos,$FontSize+3, _('Delivery To').':');
$pdf->SetFont('Arial','',10);
$pdf->addText($XPos, $YPos-15,$FontSize, $myrow['deliverto']);
$pdf->addText($XPos, $YPos-30,$FontSize, $myrow['deladd1']);
$pdf->addText($XPos, $YPos-45,$FontSize, $myrow['deladd2']);
$pdf->addText($XPos, $YPos-60,$FontSize, $myrow['deladd3'] . ' ' . $myrow['deladd4'] . ' ' . $myrow['deladd5']);    */
$XPos = 46;
$YPos -= 80;
/*$pdf->SetFont('Arial','B',12);
$pdf->addText($XPos, $YPos,$FontSize+3, _('Quotation For').':');*/
$pdf->SetFont('Arial','B',10);
$pdf->addText($XPos, $YPos-5,$FontSize, utf8_decode('Código cliente: '.$myrow['debtorno']));
$pdf->addText($XPos, $YPos-15,$FontSize, utf8_decode($myrow['name']));
$pdf->addText($XPos, $YPos-25,$FontSize, utf8_decode($myrow['taxref']));
$pdf->SetFont('Arial','',10);
$pdf->addText($XPos, $YPos-35,$FontSize, utf8_decode($myrow['address1'].' '.$myrow['address2']));
$pdf->addText($XPos, $YPos-45,$FontSize, utf8_decode(trim($myrow['address3'] . ' ' . $myrow['address4'] . ' ' . $myrow['address5'])));
$pdf->addText($XPos, $YPos-55 ,$FontSize,utf8_decode(trim($myrow['address7'] . ', ' . $myrow['address8'] . ', ' . $myrow['address9'] . ' C.P. ' . $myrow['address10'])));
$pdf->addText($XPos, $YPos-65,$FontSize, utf8_decode('Tel: '.$myrow['phoneno']));

$pdf->SetFont('Arial','B',10);
$pdf->addText($XPos, $YPos-85,$FontSize, utf8_decode('Atn.  '. $myrow['brname']));

$pdf->SetFont('Arial','',10);
$pdf->addText($XPos, $YPos-105,$FontSize, utf8_decode('A continuación me permito presentarle la siguiente cotización, esperando que cumpla con sus necesidades.'));

$XPos= 50;
$YPos += 25;
/*draw a nice curved corner box around the delivery details */
/*from the top right */
//$pdf->partEllipse($XPos+270,$YPos+60,0,90,10,10); // bowikaxu
/*line to the top left */
//$pdf->line($XPos+270, $YPos+70,$XPos, $YPos+70); // bowikaxu
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+60,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+60,$XPos-10, $YPos);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($XPos, $YPos,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($XPos, $YPos-10,$XPos+270, $YPos-10); // bowikaxu
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+270, $YPos,270,360,10,10); // bowikaxu
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+280, $YPos,$XPos+280, $YPos+60); // bowikaxu


$YPos -= 82;
/*draw a nice curved corner box around the billing details */
/*from the top right */
//$pdf->partEllipse($XPos+270,$YPos+60,0,90,10,10);
/*line to the top left */
//$pdf->line($XPos+270, $YPos+70,$XPos, $YPos+70);
/*Dow top left corner */
//$pdf->partEllipse($XPos, $YPos+60,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($XPos-10, $YPos+60,$XPos-10, $YPos);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($XPos, $YPos,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($XPos, $YPos-10,$XPos+270, $YPos-10);
/*Now do the bottom right corner */
//$pdf->partEllipse($XPos+270, $YPos,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($XPos+280, $YPos,$XPos+280, $YPos+60);



$pdf->addText(46, $Page_Height-105,$FontSize, _('Your Ref'). ':');
$pdf->addText(80, $Page_Height-105,$FontSize, $myrow['customerref']);
$pdf->addText(150, $Page_Height-105,$FontSize, _('Number'). ':');
$pdf->addText(200, $Page_Height-105,$FontSize, $_GET['QuotationNo']);
$pdf->addText(450, $Page_Height-105,$FontSize,  _('Date'). ':');
$pdf->addText(500, $Page_Height-105,$FontSize,  ConvertSQLDate($myrow['orddate']));
$pdf->addText(300, $Page_Height-105,$FontSize,  _('Page'). ':');
$pdf->addText(350, $Page_Height-105,$FontSize,  $PageNumber);

$YPos = 598;
$XPos = 44;

$LeftOvers = $pdf->addTextWrap($XPos,$YPos,100,$FontSize, _('Item Code'),'left');
$LeftOvers = $pdf->addTextWrap(140,$YPos,170,$FontSize, _('Item Description'),'left');
$LeftOvers = $pdf->addTextWrap(310,$YPos,60,$FontSize, _('Quantity'),'right');
$LeftOvers = $pdf->addTextWrap(370,$YPos,60,$FontSize,_('Price'),'right');
$LeftOvers = $pdf->addTextWrap(430,$YPos,60,$FontSize, _('Tax'),'right');
$LeftOvers = $pdf->addTextWrap(490,$YPos,65,$FontSize, _('Total'),'right');


/*draw a box with nice round corner for entering line items */
/*90 degree arc at top right of box 0 degrees starts a bottom */
//$pdf->partEllipse($Page_Width-$Right_Margin-10, 598,0,90,10,10);
/*line to the top left */
//$pdf->line($Page_Width-$Right_Margin-10, 608,$Left_Margin+10, 608);

/*line under headings to top left */
$pdf->line($Page_Width-$Right_Margin, 596,$Left_Margin, 596);


/*Dow top left corner */
//$pdf->partEllipse($Left_Margin+10, 598,90,180,10,10);
/*Do a line to the bottom left corner */
//$pdf->line($Left_Margin, 596,$Left_Margin, 60);
/*Now do the bottom left corner 180 - 270 coming back west*/
//$pdf->partEllipse($Left_Margin+10, 60,180,270,10,10);
/*Now a line to the bottom right */
//$pdf->line($Left_Margin+10, 50,$Page_Width-$Right_Margin-10, 50); // llinea final
/*Now do the bottom right corner */
//$pdf->partEllipse($Page_Width-$Right_Margin-10, 60,270,360,10,10);
/*Finally join up to the top right corner where started */
//$pdf->line($Page_Width-$Right_Margin, 60,$Page_Width-$Right_Margin, 596);

$YPos -= $line_height;

$FontSize =9;

?>
