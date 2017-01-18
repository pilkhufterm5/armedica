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
    case 'satLoadTableCfd':
        $idUser = $_POST['idUser'];
        $yearAndMonth = $_POST['yearAndMonth'];
        $year = explode('-', $yearAndMonth);
        $year = $year[0];
        $month = explode('-', $yearAndMonth);
        $month = $month[1];
        $csds = getWs()->satCfds(array('idUser' => $idUser, 'mes' => $month, 'anio' => $year))->return;
        for($i = 0; $i < count($csds); $i++)
            $arreglo[] = $csds[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'satLoadSelectReporteMensual':
        $idUser = $_POST['idUser'];
        $months = getWs()->satMonthsWithCfds(array("idUser" => $idUser))->return;
        for($i = 0; $i < count($months); $i++)
            $arreglo[] = $months[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'satDownloadXml':
        $idWsCfd = $_POST['idWsCfd'];
        $xml = getWs()->satXml(array('idCfd' => $idWsCfd))->return;
        $fileName = "XMLFacturacionElectronica/tmp/sat.xml";
        File::createFile($xml, $fileName);
        return;
    break;
    case 'satGetRfcsLike':
        $rfc = $_POST['rfc'];
        $rfcs = getWs()->satGetRfcsLike(array('rfc' => $rfc))->return;
        for($i = 0; $i < count($rfcs); $i++)
            $arreglo[] = $rfcs[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'satGetNombresLike':
        $nombre = $_POST['nombre'];
        $nombres = getWs()->satGetNombresLike(array('nombre' => $nombre))->return;
        for($i = 0; $i < count($nombres); $i++)
            $arreglo[] = $nombres[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'satInformacionEstadistica':
        $idUser = $_POST['idUser'];
        $info = getWs()->satInformacionEstadistica(array('idUser' => $idUser))->return;
        for($i = 0; $i < count($info); $i++)
            $arreglo[] = $info[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'satLoadTableUser':
        $limit = $_POST['limit'];
        $users = getWs()->satUsers(array('limit' => $limit))->return;
        for($i = 0; $i < count($users); $i++)
            $arreglo[] = $users[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'getNumberOfUsers':
        echo '[{numberOfUsers:' . getWs()->satGetNumberOfUsers()->return . '}]';
        return;
    break;
    case 'getPageSize':
        echo '[{pageSize:' . getWs()->getPageSize()->return . '}]';
        return;
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