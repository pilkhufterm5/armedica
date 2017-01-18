<?php
ob_start();
ini_set("memory_limit","512M");
$PageSecurity = 2;
ini_set("display_errors",1);
include('includes/session.inc');

$title = _('Aged Supplier Balances/Overdues Report');
//include('includes/header.inc');
include('XMLFacturacionElectronica/utils/Php.php');
require_once 'includes/dompdf-master/dompdf_config.inc.php';
//require_once 'includes/dompdf-master/dompdf_config.inc.php';
function nf($cantidad){
    return number_format($cantidad ,2);
}

?>

<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/steel/steel.css" />



<?php
ob_clean();
if(!function_exists("pre_var_dump")){
    function pre_var_dump($variable){
        echo "<pre>";
        var_dump($variable);
        echo "</pre>";
    }
}


if(isset($_GET['ver']) || isset($_GET['XLS']) || isset($_GET['PDF'])) {

    $BetWeenDebtor = "";
    /**
    * Cuando se hace un filtro entre clave y clave de proveedor
    */
    if(isset($_GET['DebtorFrom']) && !empty($_GET['DebtorFrom']) && isset($_GET['DebtorTo']) && !empty($_GET['DebtorTo'])){
        $DebtorFrom = mysql_real_escape_string($_GET['DebtorFrom']);
        $DebtorTo = mysql_real_escape_string($_GET['DebtorTo']);
        $BetWeenDebtor = " AND rh_titular.folio between {$DebtorFrom} and {$DebtorTo} ";
    }

    /**
    * Cuando se selecciona entre Clientes con mora y clientes con saldo
    */
    $soloMora = "";
    if(isset($_POST['cualesSaldos']) && !empty($_POST['cualesSaldos'])){
        //$soloMora = "  HAVING porVencer is NULL ";
        $soloMora = " ";
    }



    /***
    *Query Principal
    *
    */
    $SQL = "SELECT (rh_foliosasignados.tipo_membresia) as TipoCliente,
                   (rh_titular.folio) as FolTitular,
                   (rh_titular.movimientos_afiliacion) as TitularStatus,
                   (rh_cobradores.nombre) as Cobrador, d.debtorno, d.name, d.lastpaiddate, d.paymentterms, sum(t.ovamount+t.ovgst - alloc) AS saldo,
        (SELECT sum(ovamount+ovgst - alloc) AS vener
            FROM debtortrans
            WHERE (TO_DAYS(trandate) + d.paymentterms)>= TO_DAYS(from_unixtime(unix_timestamp()))
            AND debtorno =t.debtorno
            AND settled = 0) AS porVencer,
        (SELECT sum(1)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms))>1
            AND debtorno =t.debtorno
            AND settled = 0) AS Vencidos,
        (SELECT sum(ovamount+ovgst - alloc)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 1 AND 29
            AND debtorno =t.debtorno
            AND settled = 0) AS v1a30,
        (SELECT sum(ovamount+ovgst - alloc)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 30 AND 59
            AND debtorno =t.debtorno
            AND settled = 0) AS v31a60,
        (SELECT sum(ovamount+ovgst - alloc)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 60 AND 89
            AND debtorno =t.debtorno
            AND settled = 0) AS v61a90,
        (SELECT sum(ovamount+ovgst - alloc)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) BETWEEN 90 AND 119
            AND debtorno =t.debtorno
            AND settled = 0) AS v91a120,
        (SELECT sum(ovamount+ovgst - alloc)
            FROM debtortrans
            WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + d.paymentterms)) >= 120
            AND debtorno =t.debtorno
            AND settled = 0) AS v91aMas
        FROM debtorsmaster d
        JOIN rh_titular ON d.debtorno = rh_titular.debtorno
        JOIN rh_cobranza ON d.debtorno = rh_cobranza.debtorno
        LEFT JOIN rh_cobradores ON rh_cobranza.cobrador = rh_cobradores.id
        LEFT JOIN rh_foliosasignados ON rh_titular.folio = rh_foliosasignados.folio, debtortrans t
        WHERE d.debtorno = t.debtorno
        AND (t.ovamount+t.ovgst - t.alloc) > 0.5
        AND t.settled = 0
        {$BetWeenDebtor}
        GROUP BY d.debtorno
        {$soloMora}
        ORDER BY debtorno DESC";
        //echo $SQL;

        $rs = DB_query($SQL,$db,'','',False,False);
        $HeadingLine1 = _('Aged Supplier Balances For Customers from') . ' ' . $_POST['FromCriteria'] . ' ' .  _('to') . ' ' . $_POST['ToCriteria'];

        $nomTabla="";
        if($_POST['resumenDetallado']!="detalle"){
            $nomTabla ="myTable";
        }



        require 'includes/PHPExcel/Classes/PHPExcel.php';
        $PHPExcel = new PHPExcel();

        $PHPExcel->getActiveSheet()
        ->setCellValue('A4', _('Tipo'))
        ->setCellValue('B4', _('NoAfiliado'))
        ->setCellValue('C4', _('Codigo'))
        ->setCellValue('D4', _('Nombre'))
        ->setCellValue('E4', _('Ultimo Pago'))
        ->setCellValue('F4', _('Cobrador'))
        ->setCellValue('G4', _('Status'))
        ->setCellValue('H4', _('Vencidas'))
        ->setCellValue('I4', _('Saldo'))
        ->setCellValue('J4', _('Por Vencer'))
        ->setCellValue('K4', _('Vencido'))
        ->setCellValue('L4', _('> 30 dias'))
        ->setCellValue('M4', _('> 60 dias'))
        ->setCellValue('N4', _('> 90 dias'))
        ->setCellValue('O4', _('> 120 dias'));

        // $html = "<table id='{$nomTabla}' width='100%' cellpadding='3' border='2'>";
        // $html .= "<thead><tr><td colspan='6'>{$HeadingLine1}</td><td colspan='3'>"._('Printed').": ".Date("d M Y")." </td></tr>";
        // $html .= "<tr style='background-color:#cccce5; color:#330000; font-weight:bold; cursor:pointer;'>
        //             <th>Tipo</th>
        //             <th><b>NoAfiliado</b></th>
        //             <th><b>Codigo</b></th>
        //             <th><b>Nombre</b></th>
        //             <th><b>Ultimo Pago</b></th>
        //             <th><b>Cobrador</b></th>
        //             <th><b>Status</b></th>
        //             <th><b>Vencidas</b></th>
        //             <!--<th><b>Plazos de pago</b>//</th>-->
        //             <th><b>Saldo</b></th>
        //             <!--<th><b>Por Vencer</b>//</th>-->
        //             <th><b>Por Vencer</b></th>
        //             <th><b>Vencido</b></th>
        //             <th><b>> 30 dias</b></th>
        //             <th><b>> 60 dias</b></th>
        //             <th><b>> 90 dias</b></th>
        //             <th><b>> 120 dias</b></th>
        //         </tr>
        //     </thead><tbody>";
        $plazos ="";
        $bg="CCC";
        $totals= array();
        $i=6;

        $i2 = DB_num_rows($rs);
        // echo $i2;
        // exit;
        while(($row = DB_fetch_assoc($rs,$db))){
            $bg=($bg=="CCC")?"EEE":"CCC";
            if($row['paymentterms']==0 || $row['paymentterms']=="CA")
                $plazos = "CONTADO";
            else
                $plazos = $row['paymentterms']." DIAS";

            $totals['saldo'] += $row['saldo'];
            $totals['porVencer'] += $row['porVencer'];
            $totals['v1a30'] += $row['v1a30'];
            $totals['v31a60'] += $row['v31a60'];
            $totals['v61a90'] += $row['v61a90'];
            $totals['v91a120'] += $row['v91a120'];
            $totals['v91aMas'] += $row['v91aMas'];

            /****************************************************************/
            $PHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $row['TipoCliente'])
            ->setCellValue('B'.$i, $row['FolTitular'])
            ->setCellValue('C'.$i, $row['debtorno'])
            ->setCellValue('D'.$i, $row['name'])
            ->setCellValue('E'.$i, $row['lastpaiddate'])
            ->setCellValue('F'.$i, $row['Cobrador'])
            ->setCellValue('G'.$i, $row['TitularStatus'])
            ->setCellValue('H'.$i, $row['Vencidos'])
            ->setCellValue('I'.$i, $row['saldo'])
            ->setCellValue('J'.$i, $row['porVencer'])
            ->setCellValue('K'.$i, $row['v1a30'])
            ->setCellValue('L'.$i, $row['v31a60'])
            ->setCellValue('M'.$i, $row['v61a90'])
            ->setCellValue('N'.$i, $row['v91a120'])
            ->setCellValue('O'.$i, $row['v91aMas']);
            if($i == ($i2 + 5)){
                $styleArray = array(
                    'borders' => array(
                        'inside' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                        'color' => array(
                            'argb' => '000000'
                            )
                        ),
                        'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'argb' => '000000'
                            )
                        )
                    )
                );
                $PHPExcel->getActiveSheet()->getStyle("A4:P{$i}")->applyFromArray($styleArray);
            }

            /****************************************************************/


            // $html .= "<tr bgcolor='#{$bg}'>
            //             <td>{$row['TipoCliente']}</td>
            //             <td>{$row['FolTitular']}</td>
            //             <td>{$row['debtorno']}</td>
            //             <td>{$row['name']}</td>
            //             <td>{$row['lastpaiddate']}</td>
            //             <td>{$row['Cobrador']}</td>
            //             <td>{$row['TitularStatus']}</td>
            //             <td style='text-align:right;'>{$row['Vencidos']}</td>
            //             <!-- <td>&nbsp;{$plazos}</td> -->
            //             <td align='right'>".$row['saldo']."</td>
            //             <td align='right'>".$row['porVencer']."</td><!-- Por VEncer -->
            //             <td align='right'>&nbsp;".$row['v1a30']."</td><!-- Vencidos -->
            //             <td align='right'>&nbsp;".$row['v31a60']."</td>
            //             <td align='right'>&nbsp;".$row['v61a90']."</td>
            //             <td align='right'>&nbsp;".$row['v91a120']."</td>
            //             <td align='right'>&nbsp;".$row['v91aMas']."</td>
            //         </tr>";
            /**
             * Resumen detallado
            */
            if($_GET['resumenDetallado']=="detalle"){
                //<td> Dias Vencidos</td><td>(',(TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(t.trandate))),')</td>
                $SQL2 = "SELECT d.debtorno,
                    t.transno,
                    d.name,
                    d.paymentterms,
                    (t.ovamount+t.ovgst - alloc) AS saldo,
                    (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(t.trandate))) AS DIAS,
                    concat('<table width=\'100%\' height=\'100%\' ><tr><td> ',st.typename,' </td><td> <folio /> </td><td> (',t.transno,') </td><td>  ',t.trandate,' </td></tr></table>') AS des,
                    concat(cfd.serie,cfd.folio) FolioFactura,
                    (SELECT sum(ovamount+ovgst - alloc) AS vener
                        FROM debtortrans
                        WHERE (TO_DAYS(trandate) + d.paymentterms)>= TO_DAYS(from_unixtime(unix_timestamp()))
                        AND transno =t.transno
                        AND settled = 0) AS porVencer,
                        (CASE WHEN (paymentterms.daysbeforedue > 0 )
                            THEN ( CASE WHEN (TO_DAYS(NOW()) - TO_DAYS(t.trandate)) >= paymentterms.daysbeforedue
                                THEN(TO_DAYS(NOW()) - TO_DAYS(t.trandate ) - paymentterms.daysbeforedue   )
                                ELSE 0
                                END )
                                ELSE 0
                        END )  as diasVencidos ,
                    (SELECT sum(ovamount+ovgst - alloc)
                        FROM debtortrans
                        WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 1 AND 29
                        AND transno =t.transno
                        AND settled = 0) AS v1a30,
                    (SELECT sum(ovamount+ovgst - alloc)
                        FROM debtortrans
                        WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 30 AND 59
                        AND transno =t.transno
                        AND settled = 0) AS v31a60,
                    (SELECT sum(ovamount+ovgst - alloc)
                        FROM debtortrans
                        WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 60 AND 89
                        AND transno =t.transno
                        AND settled = 0) AS v61a90,
                    (SELECT sum(ovamount+ovgst - alloc)
                        FROM debtortrans
                        WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) BETWEEN 90 AND 119
                        AND transno =t.transno
                        AND settled = 0) AS v91a120,
                    (SELECT sum(ovamount+ovgst - alloc)
                        FROM debtortrans
                        WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (d.paymentterms + TO_DAYS(trandate))) >= 120
                        AND transno =t.transno
                        AND settled = 0) AS v91aMas
                    FROM debtorsmaster d,paymentterms,
                    debtortrans t
                    left join rh_cfd__cfd cfd on cfd.id_debtortrans=t.id,
                    systypes st
                    WHERE d.debtorno = t.debtorno
                    AND st.typeid = t.type
                    AND t.settled=0
                    AND abs(t.ovamount+t.ovgst+t.ovfreight+t.ovdiscount-t.alloc) > 0.5
                    AND d.debtorno = '{$row['debtorno']}'
                    and d.paymentterms = paymentterms.termsindicator
                    {$soloMora}
                    ORDER BY d.debtorno DESC";

                $rs2 = DB_query($SQL2,$db,'','',False,False);
                $plazos2 ="";
                $i2 = $i + 1;
                // echo $i;
                // echo "<br>";
                // echo $i2;
                // echo "<br>";
                while(($row2 = DB_fetch_assoc($rs2,$db))){

                    // echo "<pre>";
                    // print_r($row);
                    // echo "</pre>";

                    if($row2['paymentterms']==0 || $row2['paymentterms']=="CA"){
                        $plazos2 = "CONTADO";
                    }else{
                        $plazos2 = $row2['paymentterms']." DIAS";
                    }
                    //$PHPExcel->setActiveSheetIndex(0)
                    $row2['des']=str_replace('<folio />',$row2['FolioFactura'],$row2['des']);
                    $PHPExcel->getActiveSheet()
                    ->setCellValue('A'.$i2, "")
                    ->setCellValue('B'.$i2, "")
                    ->setCellValue('C'.$i2, "")
                    ->setCellValue('D'.$i2, strip_tags($row2['des']))
                    ->setCellValue('E'.$i2, "")
                    ->setCellValue('F'.$i2, "")
                    ->setCellValue('G'.$i2, "")
                    ->setCellValue('H'.$i2, $row2['diasVencidos'])
                    ->setCellValue('I'.$i2, $row2['saldo'])
                    ->setCellValue('J'.$i2, $row2['porVencer'])
                    ->setCellValue('K'.$i2, $row2['v1a30'])
                    ->setCellValue('L'.$i2, $row2['v31a60'])
                    ->setCellValue('M'.$i2, $row2['v61a90'])
                    ->setCellValue('N'.$i2, $row2['v91a120'])
                    ->setCellValue('O'.$i2, $row2['v91aMas']);

                    /*
                    $html .= "<tr style='font-size:10px;'>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style='padding:0 0 0 20px'>&nbsp;{$row2['des']}</td>
                            <!--<td>&nbsp;{$plazos2}//</td>-->
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>{$row2['diasVencidos']} dias</td>
                            <td align='right'>&nbsp;".nf($row2['saldo'])."</td>
                            <td align='right'>&nbsp;".nf($row2['porVencer'])."</td>
                            <td align='right'>&nbsp;".nf($row2['v1a30'])."</td>
                            <td align='right'>&nbsp;".nf($row2['v31a60'])."</td>
                            <td align='right'>&nbsp;".nf($row2['v61a90'])."</td>
                            <td align='right'>&nbsp;".nf($row2['v91a120'])."</td>
                            <td align='right'>&nbsp;".nf($row2['v91aMas'])."</td>
                        </tr>"; */
                    $i2++;
                }

                $i = $i2 + 1;
            }
            $i++;
                // echo "<br>";
                // echo $i2;
                // echo "<br>";
                // echo $i;
                // echo "<br>";
                //echo "OKOK____ "; exit;
        } /*END WHILE*/
        // $html .= "</tbody>
        //         <tr >
        //             <td><b>Total Final</b></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td></td>
        //             <td><b>{$totals['saldo']}</b></td>
        //             <td><b>{$totals['porVencer']}</b></td>
        //             <td><b>{$totals['v1a30']}</b></td>
        //             <td><b>{$totals['v31a60']}</b></td>
        //             <td><b>{$totals['v61a90']}</b></td>
        //             <td><b>{$totals['v91a120']}</b></td>
        //             <td><b>{$totals['v91aMas']}</b></td>
        //         </tr>";


/***************************************************************************************************************/
    // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="AntiguedadSaldos_'.date('Y-m-d H:i:s').'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $objWriter->save('php://output');

// echo "<pre>";
// print_r($PHPExcel);
// echo "</pre>";
exit;

/***************************************************************************************************************/

        // $html .= "</table>";
        // echo "<center>";
        // echo $html; exit;
        // echo "</center>";

 // header("Content-type: application/octet-stream");
 // header("Content-Disposition: filename=\"SaldosVencidos.xls\"");
 // //header("Content-Disposition: filename=resultado.txt");
 // header("Content-length: ".strlen($html));
 // header("Cache-control: private");
 // echo $html;
 // exit();

}
?>
