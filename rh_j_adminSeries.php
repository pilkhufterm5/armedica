<?php
//Php
$PageSecurity = 2;
include('includes/session.inc');
$query;

switch($_POST["request"]){
    case 'loadTableAdminSeries':
        $query = "select l.loccode id_locations, l.locationname location, s.typename systype, s.typeid id_systypes from systypes s join locations l where typeid in (10, 11, 20002)  order by locationname, typeid";
    break;
    case 'getSeries':
        $f = getWs()->folios()->return;
        for($i = 0; $i < count($f); $i++)
            $arreglo[] = $f[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'getCertificados':
        $csds = getWs()->csds()->return;
        for($i = 0; $i < count($csds); $i++)
            $arreglo[] = $csds[$i]->item;
        print json_encode($arreglo);
        return;
    break;
    case 'loadSeriesYCertificadosInTableAdminSeries':
        $query = "select id_locations,id_systypes,id_ws_csd,serie,id_locations,id_systypes from rh_cfd__locations__systypes__ws_csd order by serie";
    break;
    case 'updateCertificado':
        try{
            if(!DB_query('begin',$db,'','',false,false)){
                throw new Exception('Error al efectuar el begin' , 1);
            }
            $idWsCsd=$_POST['idWsCsd'];
            $serie=$_POST['serie'];
            $idLocation=$_POST['idLocation'];
            $idSystype=$_POST['idSystype'];
            $sqlUpdate = "update rh_cfd__locations__systypes__ws_csd set id_ws_csd = $idWsCsd where id_locations = '$idLocation' and id_systypes = $idSystype and serie = '$serie' limit 1";
            $result = DB_query($sqlUpdate,$db,'','',false,false);
            if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                throw new Exception('No se pudo actualizar el Certificado de la Serie', 1);
            }
            if(!DB_query('commit',$db,'','',false,false)){
                throw new Exception('Error al efectuar el commit' , 1);
            }
        }
        catch(Exception $exception){
            $msg = $exception->getMessage();
            if($exception->getCode()==1){
                $error = mysql_error();
                $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
            }
            if(!DB_query('rollback',$db,'','',false,false)){
                $msg .= ' (Error al efectuar el rollback)';
            }
            echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
            return;
        }
        echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se actualizo el Certificado de la Serie con exito"}]';
        return;
    break;
    case 'addSerie':
        try{
            if(!DB_query('begin',$db,'','',false,false)){
                throw new Exception('Error al efectuar el begin' , 1);
            }
            $serie=$_POST['serie'];
            $idLocation=$_POST['idLocation'];
            $idSystype=$_POST['idSystype'];

            $sqlQuery = "select count(serie) count from (select serie from rh_cfd__locations__systypes__ws_csd where id_systypes != $idSystype group by serie having serie = '$serie') as tmp";
            $row = DB_fetch_row(DB_query($sqlQuery,$db,'','',false,false));
            if($row[0] > 0)
                throw new Exception("La serie ya esta asignada a otro Tipo de CFD");

            $sqlInsert = "insert into rh_cfd__locations__systypes__ws_csd(id_locations, id_systypes, serie) values('$idLocation', $idSystype, '$serie')";
            $result = DB_query($sqlInsert,$db,'','',false,false);
            if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                throw new Exception('No se pudo crear la Serie para la Localidad', 1);
            }
            if(!DB_query('commit',$db,'','',false,false)){
                throw new Exception('Error al efectuar el commit' , 1);
            }
        }
        catch(Exception $exception){
            $msg = $exception->getMessage();
            if($exception->getCode()==1){
                $error = mysql_error();
                $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
            }
            if(!DB_query('rollback',$db,'','',false,false)){
                $msg .= ' (Error al efectuar el rollback)';
            }
            echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
            return;
        }
        echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se agrego la Serie con exito"}]';
        return;
    break;
    case 'deleteSerie':
        try{
            if(!DB_query('begin',$db,'','',false,false)){
                throw new Exception('Error al efectuar el begin' , 1);
            }
            $serie=$_POST['serie'];
            $idLocation=$_POST['idLocation'];
            $idSystype=$_POST['idSystype'];
            $sqlDelete = "delete from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and id_systypes = $idSystype and serie = '$serie' limit 1";
            $result = DB_query($sqlDelete,$db,'','',false,false);
            if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                throw new Exception('No se pudo quitar la Serie de la Localidad', 1);
            }
            if(!DB_query('commit',$db,'','',false,false)){
                throw new Exception('Error al efectuar el commit' , 1);
            }
        }
        catch(Exception $exception){
            $msg = $exception->getMessage();
            if($exception->getCode()==1){
                $error = mysql_error();
                $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
            }
            if(!DB_query('rollback',$db,'','',false,false)){
                $msg .= ' (Error al efectuar el rollback)';
            }
            echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
            return;
        }
        echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se quito la Serie con exito"}]';
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
return;
?>