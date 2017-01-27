<?php
/* $Revision: 1.9 $ */
/* PDFlib code to set up a new page */

$Perforation = $Page_Width - $RightMargin - 160;

$YPos = $Page_Height - $Top_Margin;

//$pdf->addJpegFromFile('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg',$Page_Width/2 -70,$YPos-80,0,60);
$pdf->Image("companies/" . $_SESSION['DatabaseName'] . "/ARHeader2.jpg", 0, 0, 600, 'L');

// Title
// $FontSize =15;
// $XPos = $Page_Width/2 - 110;
// $pdf->addText($XPos, $YPos,$FontSize, _('Statement') );

// $FontSize = 12;
// $pdf->addText($XPos + 70, $YPos,$FontSize, ' '. _('as of').' ' . Date($_SESSION['DefaultDateFormat']));

// Remittance header

// $YPosR = $YPos;
// $FontSize = 10;
// $LineHeight = 13;
// $LineCountR = 0;
// $Remit1 = $Perforation +2;

// $pdf->addText($Remit1, $YPosR-$LineCountR*$LineHeight,$FontSize, _('Remittance Advice') );
// $LineCountR += 1;
// $pdf->addText($Remit1, $YPosR-$LineCountR*$LineHeight,$FontSize, _('Statement dated').' ' . Date($_SESSION['DefaultDateFormat']));
// $LineCountR += 1;
// $pdf->addText($Remit1, $YPosR-$LineCountR*$LineHeight,$FontSize, _('Page').': ' . $PageNumber);

// /*Also show the page number on the main section */
// $pdf->addText($Perforation-50, $YPos,$FontSize, _('Page').': ' . $PageNumber);

/*Now print out company info at the top left */

$GetAfilData = "SELECT
    (titular.folio) as AfilFolio,
    CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
    (stkm.description) as AfilPlan,
    (pm.paymentname) as AfilMPago,
    (fp.frecuencia) as AfilFPago,
    (com.comisionista) as AfilAsesor,
    (titular.fecha_ingreso) as AfilFIngreso
    FROM rh_titular titular
    LEFT JOIN rh_cobranza cobranza ON titular.folio = cobranza.folio
    LEFT JOIN stockmaster stkm ON cobranza.stockid = stkm.stockid
    LEFT JOIN paymentmethods pm ON cobranza.paymentid = pm.paymentid
    LEFT JOIN rh_frecuenciapago fp ON cobranza.frecuencia_pago = fp.id
    LEFT JOIN rh_comisionistas com ON titular.asesor = com.id
    WHERE titular.debtorno = '{$StmtHeader['debtorno']}'
    ";
    $_2GetAfilData = DB_query($GetAfilData, $db);
    $GetAfilData = DB_fetch_assoc($_2GetAfilData);

    include_once("modulos/protected/models/SQLServerWS.php");
    include_once("modulos/protected/models/xmlToArrayParser.php");

    $CECOMWS = new SQLServerWS();

    /*******************************************************************************************************************************************************/

    $GetDay = explode('-', date('Y-m-d'));
    $LastDay = date("d", (mktime(0, 0, 0, $GetDay['1'] + 1, 1, $GetDay['0']) - 1));
    $FirstDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], 01, $GetDay['0']));
    $LastDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], $LastDay, $GetDay['0']));

    $Folio = $GetAfilData['AfilFolio'];

    $XmlCountDateRange = ("<row>
                            <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}' AND FECHA BETWEEN '{$FirstDate}' AND '{$LastDate}' \">
                                <folio weberp='folio' prime='1' >{$Folio}</folio>
                            </CCM_Despachos>
                        </row>");

    $XmlCountAll = ("<row>
                        <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}'\">
                            <folio weberp='folio' prime='1' >{$Folio}</folio>
                        </CCM_Despachos>
                    </row>");
    $url = "http://104.130.129.147/wsceom2/WebService.asmx?wsdl";
    $CountAction = "http://tempuri.org/selectcont";

        $CountByDateRange = $CECOMWS->GetCountWS($XmlCountDateRange, $url, array('SOAPAction: ' . $CountAction))->saveXML();
        $CountAll = $CECOMWS->GetCountWS($XmlCountAll, $url, array('SOAPAction: ' . $CountAction))->saveXML();

        $ObjByDateRange = new xmlToArrayParser($CountByDateRange);
        if ($ObjByDateRange->parse_error) {

        } else {
            $GetThisMonth = $ObjByDateRange->array;
            $GetThisMonth = $GetThisMonth['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
        }

        $ObjAll = new xmlToArrayParser($CountAll);
        if ($ObjAll->parse_error) {

        } else {
            $GetAll = $ObjAll->array;
            $GetAll = $GetAll['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
        }
    /*******************************************************************************************************************************************************/



    // echo "<pre>";
    // print_r($GetAfilData);
    // echo "</pre>";
    // exit;

$XPos = $Left_Margin;
//$YPos = $Page_Height - $Top_Margin - 20 ;
$YPos = $Page_Height - $Top_Margin - 65 ;

$FontSize = 10;
$LineHeight = 13;
$LineCount = 0;

$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, $_SESSION['CompanyRecord']['coyname']);

$FontSize = 8;
$LineHeight = 10;

if ($_SESSION['CompanyRecord']['regoffice1'] <> '') {
  $LineCount += 1;
  $pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice1'] . ' ' . $_SESSION['CompanyRecord']['regoffice2'] . ' ' . $_SESSION['CompanyRecord']['regoffice3']);
}
if ($_SESSION['CompanyRecord']['regoffice2'] <> '') {
  //$LineCount += 1;
  //$pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, );
}
if (($_SESSION['CompanyRecord']['regoffice4'] <> '') OR ($_SESSION['CompanyRecord']['regoffice5'] <> '')) {
  $LineCount += 1;
  $pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);  // country in 6 not printed
}
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, _('Phone') . ':' . $_SESSION['CompanyRecord']['telephone']);
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight,$FontSize, _('Fax') . ': ' . $_SESSION['CompanyRecord']['fax']);
$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, _('Email') . ': ' . $_SESSION['CompanyRecord']['email']);

/*Now the remittance section just company and return postal address */

// $FontSize = 10;
// $LineHeight = 13;

// $LineCountR += 2;
// $pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight,$FontSize, _('Please send with remittance to').':');
// $LineCountR += 1;
// $pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight, $FontSize, $_SESSION['CompanyRecord']['coyname']);
// if ($_SESSION['CompanyRecord']['regoffice1'] <> '') {
//   $LineCountR += 1;
//   $pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice1']);
// }
// if ($_SESSION['CompanyRecord']['regoffice2'] <> '') {
//   $LineCountR += 1;
//   $pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice2']);
// }
// if (($_SESSION['CompanyRecord']['regoffice3'] <> '') OR ($_SESSION['CompanyRecord']['regoffice4'] <> '') OR ($_SESSION['CompanyRecord']['regoffice5'] <> '')) {
//   $LineCountR += 1;
//   $pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight,$FontSize, $_SESSION['CompanyRecord']['regoffice3'] . ' ' . $_SESSION['CompanyRecord']['regoffice4'] . ' ' . $_SESSION['CompanyRecord']['regoffice5']);  // country in 6 not printed
// }

/*Now the customer details and statement address */
$FontSize = 8;
$XPos = $Left_Margin;
//$YPos = $Page_Height - $Top_Margin - 120;
$YPos = $Page_Height - $Top_Margin - 127;

$LineCount = 0;

$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "No. SOCIO: " . $GetAfilData['AfilFolio']);
$pdf->addText($XPos + 400, $YPos-$LineCount*$LineHeight, $FontSize, "FECHA ALTA: " . $GetAfilData['AfilFIngreso']);
$LineCount += 1;

$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "NOMBRE: " . $StmtHeader['name']);
$pdf->addText($XPos + 400, $YPos-$LineCount*$LineHeight, $FontSize, "No. Serv ACUMULADOS: " . $GetAll);
$LineCount += 1;

$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "PLAN: " . $GetAfilData['AfilPlan']);
$pdf->addText($XPos + 400, $YPos-$LineCount*$LineHeight, $FontSize, "No. Serv ACUMULADOS MES: " . $GetThisMonth);
//$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, $StmtHeader['address1'] . ' ' . $StmtHeader['address2'] . ' ' . $StmtHeader['address3']);
$LineCount += 1;
//$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, );
//$LineCount += 1;
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "FORMA PAGO: " . $GetAfilData['AfilMPago']);
$LineCount += 1;

$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "FREC. PAGO: " . $GetAfilData['AfilFPago']);
$LineCount += 1;
//$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, $StmtHeader['address4']  . ' ' . $StmtHeader['address5']  . ' ' . $StmtHeader['address6']);
$pdf->addText($XPos, $YPos-$LineCount*$LineHeight, $FontSize, "ASESOR: " . $GetAfilData['AfilAsesor']);






$YPos = $Page_Height - $Top_Margin - 120;

/*Now note the customer code in the remittance section too */

$FontSize=10;
$LineCountR += 2;
$pdf->addText($Perforation+1, $YPosR-$LineCountR*$LineHeight,$FontSize, _('Customer Code').': ' . $StmtHeader['debtorno']);

$FontSize=8;
$XPos = $Page_Width/2 - 60;
//$pdf->addText($XPos, $YPos,$FontSize, _('All amounts stated in') . ' - ' . $StmtHeader['currency']);
$YPos -= $line_height;
//$pdf->addText($XPos, $YPos,$FontSize, $StmtHeader['terms']);

$YPos = $Page_Height - $Top_Margin - 180;
//$YPos -= $line_height;
$XPos = $Left_Margin;


/*draw a nice curved corner box around the statement details */
/*from the top right */
$pdf->partEllipse($Perforation-10,$YPos-10,0,90,10,10);
/*line to the top left */
$pdf->line($Perforation-10, $YPos,$Left_Margin+10, $YPos);
/*Do top left corner */
$pdf->partEllipse($Left_Margin+10, $YPos-10,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($Left_Margin, $YPos-10,$Left_Margin, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Left_Margin+10, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Left_Margin+10, $Bottom_Margin,$Perforation-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Perforation-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Perforation, $Bottom_Margin+10,$Perforation, $YPos-10);


/*draw a nice curved corner box around the remittance advice section */
$pdf->partEllipse($Page_Width-$Right_Margin-10,$YPos-10,0,90,10,10);
/*line to the top left */
$pdf->line($Page_Width-$Right_Margin-10, $YPos, $Perforation + 11, $YPos);
/*Do top left corner */
$pdf->partEllipse($Perforation+11, $YPos-10,90,180,10,10);
/*Do a line to the bottom left corner */
$pdf->line($Perforation+1, $YPos-10,$Perforation+1, $Bottom_Margin+10);
/*Now do the bottom left corner 180 - 270 coming back west*/
$pdf->partEllipse($Perforation+11, $Bottom_Margin+10,180,270,10,10);
/*Now a line to the bottom right */
$pdf->line($Perforation+11, $Bottom_Margin,$Page_Width-$Right_Margin-10, $Bottom_Margin);
/*Now do the bottom right corner */
$pdf->partEllipse($Page_Width-$Right_Margin-10, $Bottom_Margin+10,270,360,10,10);
/*Finally join up to the top right corner where started */
$pdf->line($Page_Width-$Right_Margin, $Bottom_Margin+10,$Page_Width-$Right_Margin, $YPos-10);




$YPos -= $line_height;
$FontSize =10;
/*Set up headings */
$pdf->addText($Left_Margin+1, $YPos,$FontSize, _('Tipo') );
$pdf->addText($Left_Margin+57, $YPos,$FontSize, _('Number') );
$pdf->addText($Left_Margin+108, $YPos,$FontSize, _('Date') );
$pdf->addText($Left_Margin+178, $YPos,$FontSize, _('Charges') );
$pdf->addText($Left_Margin+239, $YPos,$FontSize, _('Credits') );
$pdf->addText($Left_Margin+289, $YPos,$FontSize, _('Allocated') );
$pdf->addText($Left_Margin+336, $YPos,$FontSize, _('Outstanding') );


/*Set up remittance section headings */
$FontSize=8;
$pdf->addText($Perforation+1, $YPos,$FontSize, _('Trans') );
$pdf->addText($Perforation+35, $YPos,$FontSize, _('Number') );
$pdf->addText($Perforation+80, $YPos,$FontSize, _('Outstanding') );


$YPos -= $line_height;
/*draw a line */
$pdf->line($Page_Width-$Right_Margin, $YPos,$XPos, $YPos);

$YPos -= $line_height;
$XPos = $Left_Margin;

?>
