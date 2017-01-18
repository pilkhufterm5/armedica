<?php


$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
require_once('CFD22Manager.php');

if(isset($_POST['serie'])&&isset($_POST['folioini'])&&isset($_POST['foliofin'])&&isset($_POST['anoauth'])&&isset($_POST['noauth'])){
    $CFDManager = CFD22Manager::getInstance();
    try{
       $CFDManager->addSerie($_POST['serie'],$_POST['anoauth'],$_POST['noauth'],$_POST['folioini'],$_POST['foliofin']);
    }catch(Exception $e){
       echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e . '<p></div>';
    }
}
?>
<div id="divAdminSeries" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <br />
    <div id="divTableAdminSeries">
        <center>
            <table cellpadding="0" cellspacing="0" border="1">
                <tr>
                    <td>Localizaci&oacute;n</td>
                    <td>Tipo CFD</td>
                    <td>Serie y Certificado</td>
                    <td>Agregar Serie</td>
                    <td></td>
                </tr>
                <?php
                        if(isset($_POST['series'])&&$_POST['series']!='-10'){
                                      $serie=$_POST['series'];
                                      $idLocation=$_POST['idLocation'];
                                      $idSystype=$_POST['idSystype'];
                                      $sqlQuery = "select count(serie) count from (select serie from rh_cfd__locations__systypes__ws_csd where id_systypes != $idSystype group by serie having serie = '$serie') as tmp";
                                      $row = DB_fetch_row(DB_query($sqlQuery,$db,'','',false,false));
                                      if($row[0] > 0){
                                            prnMsg(_('La serie no puede ser asignada'), 'error');
                                      }else{
                                        $sqlInsert = "insert into rh_cfd__locations__systypes__ws_csd(id_locations, id_systypes, serie) values('$idLocation', $idSystype, '$serie')";
                                        $result = DB_query($sqlInsert,$db,'','',false,false);
                                        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                                          prnMsg(_('No se pudo crear la Serie para la Localidad'), 'error');
                                            //throw new Exception('No se pudo crear la Serie para la Localidad', 1);
                                        }
                                         if(!DB_query('commit',$db,'','',false,false)){
                                           prnMsg(_('Error al efectuar el commit'), 'error');
                                           // throw new Exception('Error al efectuar el commit' , 1);
                                        }
                                      }
                        } else if($_POST['series']=='-10'){
                            prnMsg(_('Seleccione una serie para esta localidad'), 'warning');
                        }
                        if(isset($_POST['csd'])&&$_POST['csd']!='-10'){
                            if(!DB_query('begin',$db,'','',false,false)){
                                prnMsg(_('Error al efectuar el begin'), 'error');
                                //throw new Exception('Error al efectuar el begin' , 1);
                            }
                            $idWsCsd=$_POST['csd'];
                            $serie=$_POST['serie'];
                            $idLocation=$_POST['idLocation'];
                            $idSystype=$_POST['idSystype'];
                            $sqlUpdate = "update rh_cfd__locations__systypes__ws_csd set id_ws_csd = '$idWsCsd' where id_locations = '$idLocation' and id_systypes = $idSystype and serie = '$serie' limit 1";
                            $result = DB_query($sqlUpdate,$db,'','',false,false);
                            if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                                //throw new Exception('No se pudo actualizar el Certificado de la Serie', 1);
                                prnMsg(_('No se pudo actualizar el Certificado de la Serie'), 'error');
                            }
                            if(!DB_query('commit',$db,'','',false,false)){
                                //throw new Exception('Error al efectuar el commit' , 1);
                                prnMsg(_('Error al efectuar el commit'), 'error');
                             }
                        }else if($_POST['csd']=='-10'){
                            prnMsg(_('Seleccione un certificado para esta localidad'), 'warning');
                        }
                       //$csds = getWs()->csds()->return;
                       $CFDManager = CFD22Manager::getInstance();
                       $dom = new DOMDocument('1.0', 'utf-8');
                       $dom->loadXML($CFDManager->getCertificates());
                       $rows = $dom->getElementsByTagName('row');
                       foreach($rows as $row){
                        $CSD[$row->getAttribute('nocert')]=$row->getAttribute('nocert');
                       }

                       $CFDManager = CFD22Manager::getInstance();
                       $dom = new DOMDocument('1.0', 'utf-8');
                       $dom->loadXML($CFDManager->getSeries());
                       $rows = $dom->getElementsByTagName('row');
                       foreach($rows as $row){
                            $SERIE[$row->getAttribute('serie')]=$row->getAttribute('serie');
                        }

                       if(isset($_POST['button'])){
                            if(!DB_query('begin',$db,'','',false,false)){
                                prnMsg(_('Error al efectuar el begin'), 'error');
                            }
                            $serie=$_POST['serie'];
                            $idLocation=$_POST['idLocation'];
                            $idSystype=$_POST['idSystype'];
                            $sqlDelete = "delete from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and id_systypes = $idSystype and serie = '$serie' limit 1";
                            $result = DB_query($sqlDelete,$db,'','',false,false);
                            if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                                prnMsg(_('No se pudo quitar la Serie de la Localidad'), 'error');
                            }
                            if(!DB_query('commit',$db,'','',false,false)){
                                prnMsg(_('Error al efectuar el commit'), 'error');
                            }
                       }

                  $query = "select l.loccode id_locations, l.locationname location, s.typename systype, s.typeid id_systypes from systypes s join locations l where typeid in (10, 11, 20002)  order by locationname, typeid";
                  $resultado = DB_query($query, $db);
                  while($objeto = mysql_fetch_array($resultado)){
                        $_SQL = "select id_locations,id_systypes,id_ws_csd,serie,id_locations,id_systypes from rh_cfd__locations__systypes__ws_csd where id_locations='".$objeto['id_locations']."' and id_systypes=".$objeto['id_systypes']." order by serie";
                        $resultado2 = DB_query($_SQL, $db);
                        $objeto2 = mysql_fetch_array($resultado2);

                       $selectCsd='<form name="frmcsd'.$objeto['location'].$objeto['id_systypes'].'" method="POST" enctype="application/x-www-form-urlencoded" >';
                       $selectCsd.='<input type="hidden" name="idLocation" value="'.$objeto['id_locations'].'" />';
                       $selectCsd.='<input type="hidden" name="idSystype" value="'.$objeto['id_systypes'].'" />';
                       $selectCsd.='<input type="hidden" name="serie" value="'.$objeto2['serie'].'" />';
                       $selectCsd.='<select name="csd" onchange="submit();">';
                       $selectCsd.='<option value="-10" >Seleccione un Certificado</option>';
                       if(!is_null($CSD)){
                       foreach($CSD as $key=>$value){
                            if($key==$objeto2['id_ws_csd']){
                                $selectCsd.='<option value="'.$key.'" selected="selected" >'.$value.'</option>';
                            }else{
                               $selectCsd.='<option value="'.$key.'" >'.$value.'</option>';
                            }
                       }
                       }
                       $selectCsd.='</select></form>';

                       $selectSerie='<form name="frmseries'.$objeto['location'].$objeto['id_systypes'].'" method="POST" enctype="application/x-www-form-urlencoded" >';
                       $selectSerie.='<input type="hidden" name="idLocation" value="'.$objeto['id_locations'].'" />';
                       $selectSerie.='<input type="hidden" name="idSystype" value="'.$objeto['id_systypes'].'" />';
                       $selectSerie.='<select name="series" onchange="submit();">';
                       $selectSerie.='<option value="-10" >Seleccione una serie</option>';
                       if(!is_null($SERIE)){
                       foreach($SERIE as $key=>$value){
                            if($key==$objeto2['serie']){
                                $selectSerie.='<option value="'.$key.'" selected="selected" >'.$value.'</option>';
                            }else{
                               $selectSerie.='<option value="'.$key.'" >'.$value.'</option>';
                            }
                       }
                       }
                       $selectSerie.='</select></form>';

                       $button='<form name="frmdelete'.$objeto['location'].$objeto['id_systypes'].'" method="POST" enctype="application/x-www-form-urlencoded" >';
                       $button.='<input type="submit" name="button" value="Eliminar" />';
                       $button.='<input type="hidden" name="idLocation" value="'.$objeto['id_locations'].'" />';
                       $button.='<input type="hidden" name="idSystype" value="'.$objeto['id_systypes'].'" />';
                       $button.='<input type="hidden" name="serie" value="'.$objeto2['serie'].'" />';
                       $button.='</form>';
                       echo '<tr>';
                       echo '<td>'.$objeto['location'].'</td>';
                       echo '<td>'.$objeto['systype'].'</td>';
                       echo '<td>'.$selectCsd.'</td>';
                       echo '<td>'.$selectSerie.'</td>';
                       echo '<td>'.$button.'</td>';
                       echo '</tr>';
                  }
                ?>
            </table>
        </center>
    </div>
</div>
<?php
include ('includes/footer.inc');
?>