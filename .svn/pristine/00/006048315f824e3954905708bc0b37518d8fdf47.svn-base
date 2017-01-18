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
switch($_POST["request"]){
    case 'createFolio':
        try{
            $valorInicial = $_POST['valorInicial'];
            $valorFinal = $_POST['valorFinal'];
            $serie = $_POST['serie'];
            $anoAprobacion = $_POST['anoAprobacion'];
            $noAprobacion = $_POST['noAprobacion'];
            getWs()->folio(array('serie' => $serie, 'noAprobacion' => $noAprobacion, 'anoAprobacion' => $anoAprobacion, 'folioInicial' => $valorInicial, 'folioFinal' => $valorFinal));
        }
        catch(Exception $exception){
            $msg = $exception->getMessage();
            echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
            return;
        }
        echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se guardaron los folios con exito"}]';
        return;
    break;
    case 'loadTableFolio':
        $csds = getWs()->folios()->return;
        for($i = 0; $i < count($csds); $i++)
            $arreglo[] = $csds[$i]->item;
    break;
    default:
        echo("Consulta invalida");
        return;
    break;
}
print json_encode($arreglo);
?>