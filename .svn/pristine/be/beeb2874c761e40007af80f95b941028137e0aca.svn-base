<?php
//Php
$PageSecurity = 2;
include('includes/session.inc');
include('XMLFacturacionElectronica/utils/Comprobante.php');
include('XMLFacturacionElectronica/utils/Console.php');
include('XMLFacturacionElectronica/utils/Converter.php');
include('XMLFacturacionElectronica/utils/FacturaElectronica.php');
include('XMLFacturacionElectronica/utils/File.php');
include('XMLFacturacionElectronica/utils/Json.php');
include('XMLFacturacionElectronica/utils/Openssl.php');
include('XMLFacturacionElectronica/utils/Php.php');
$arreglo;
$query;
switch($_POST["request"]){
    case 'loadTableCfd':
        $yearAndMonth = $_POST['yearAndMonth'];
        $year = explode('-', $yearAndMonth);
        $year = $year[0];
        $month = explode('-', $yearAndMonth);
        $month = $month[1];
        $csds = getWs()->cfds(array('mes' => $month, 'anio' => $year))->return;
        $y=0;
        $rs='';
        for($i = 0; $i < count($csds); $i++){
            $rs.='<tr>';
            $y++;
            if($y>2){
                $rs.='<td>'.$csds[$i]->item[0].'</td>';
                $rs.='<td>'.$csds[$i]->item[1].'</td>';
                $rs.='<td>'.$csds[$i]->item[2].'</td>';
                $rs.='<td>'.$csds[$i]->item[3].'</td>';
                $rs.='<td>'.$csds[$i]->item[4].'</td>';
                $rs.='<td>'.$csds[$i]->item[5].'</td>';
                $rs.='<td align="right">'.$csds[$i]->item[6].'</td>';
                $rs.='<td align="right">'.$csds[$i]->item[7].'</td>';
                $rs.='<td>&nbsp;'.$csds[$i]->item[8].'</td>';
                $rs.='<td>&nbsp;'.$csds[$i]->item[9].'</td>';
                $rs.='<td>&nbsp;'.$csds[$i]->item[10].'</td>';
                $rs.='<td onclick="emailCFD(\''.$csds[$i]->item[0].'\')" ><img src="css/silverwolf/images/email.gif" /></td>';
                $rs.='<td onclick="downloadXml(\''.$csds[$i]->item[0].'\',\''.$csds[$i]->item[1].'\',\''.$csds[$i]->item[2].'\')" ><img src="images/xml.gif" /></td>';
                $rs.='<td onclick="downloadPdf(\''.$csds[$i]->item[0].'\')" ><img src="images/pdf.gif" /></td>';
            }
            $rs.='</tr>';
        }
        echo $rs;
        return;
    break;
    case 'loadSelectReporteMensual':
        $months = getWs()->monthsWithCfds()->return;
        for($i = 0; $i < count($months); $i++)
            $arreglo[] = $months[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'downloadReporteMensual':
        $downloadPath;
        try{
            $yearAndMonth = $_POST['yearAndMonth'];
            $year = explode('-', $yearAndMonth);
            $year = $year[0];
            $month = explode('-', $yearAndMonth);
            $month = $month[1];
            $cfds = getWs()->reporteMensual(array('mes' => $month, 'anio' => $year))->return;
            $text = '';
            for($i = 2; $i < count($cfds); $i++)
                if(($i+1) == count($cfds))
                    $text .= $cfds[$i]->item;
                else
                    $text .= $cfds[$i]->item . "\r\n";
            $nombreDelArchivo = $cfds[1]->item;
            $fileName = "./XMLFacturacionElectronica/tmp/$nombreDelArchivo";
            File::createFile($text, $fileName);
            $downloadPath = "rh_j_downloadFacturaElectronicaXML.php?downloadPath=$fileName";
        }
        catch(Exception $exception){
            $msg = $exception->getMessage();
            echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
            return;
        }
        echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se ha descargado el reporte con exito", downloadPath:"' . $downloadPath . '"}]';
        return;
    break;
    case 'emailCFD':
        $idWsCfd = $_POST['idWsCfd'];
        $query = "select fk_transno from rh_cfd__cfd where id_ws_cfd = $idWsCfd limit 1";
    break;
    case 'downloadXml':
        $idWsCfd = $_POST['idWsCfd'];
        $query = "select no_certificado, fk_transno from rh_cfd__cfd where id_ws_cfd = $idWsCfd limit 1";
    break;
    case 'downloadPdf':
        $idWsCfd = $_POST['idWsCfd'];
        $query = "select fk_transno from rh_cfd__cfd where id_ws_cfd = $idWsCfd limit 1";
    break;
    default:
        echo "Consulta invalida";
        return;
    break;
}
$resultado = DB_query($query, $db);
$arreglo = array();
while($objeto = mysql_fetch_object($resultado))
    $arreglo[] = $objeto;
print json_encode($arreglo);
?>