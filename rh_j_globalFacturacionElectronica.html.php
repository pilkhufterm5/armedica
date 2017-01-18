<?php
//Php
$PageSecurity = 2;
include('includes/session.inc');
$title = _('Digital Invoice');
include('includes/header.inc');
?>
<!--Para el lockscreen -->
<div id="hiddenLayer" class="POP_LAYER_NONE" style="height: 800px;"></div>
<?php
switch($_GET['page']){
    case 'altaDeSello':
?>
<div id="divAltaDeSello" align="center">
    <?php if(isSet($_GET['msgAltaDeSello'])) prnMsg($_GET['msgAltaDeSello'], $_GET['msgType']); ?>
    <div id="divLigas">
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeFolio">Administraci&oacute;n de Folios</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=cfd">Reporte Mensual y CFDs emitidos</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=adminSeries">Administraci&oacute;n de Series</a>
        <br />
    </div>
    <br />
    <div id="divFormAltaDeSello">
        <form name="formAltaDeSello" method="POST" enctype="multipart/form-data" action="rh_j_sello.php">
            <input type="hidden" name="request" value="altaDeSello" />
            <div id="divTableAltaDeSello">
                <table id="tableAltaDeSello">
                    <tbody>
                        <tr class="headland">
                            <td colspan="2">
                                <?php echo _('Alta de Sello') ?>
                            </td>
                        </tr>
                        <!-- //noCertificado
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileCertificado">
                                    <?php //echo _('Numero de Serie del Certificado') ?>
                                </label>
                            </td>
                            <td>
                                <input type="text" id="inputTextNoCertificado" name="inputTextNoCertificado" />
                            </td>
                        </tr>
                        -->
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileCertificado">
                                    <?php echo _('Certificado') . ' (.cer)' ?>
                                </label>
                            </td>
                            <td>
                                <input type="file" id="inputFileCertificado" name="inputFileCertificado" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileLlavePrivada">
                                    <?php echo _('Llave Privada') . ' (.key)' ?>
                                </label>
                            </td>
                            <td>
                                <input type="file" id="inputFileLlavePrivada" name="inputFileLlavePrivada" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputPasswordContrasenaDeLlavePrivada">
                                    <?php echo _('Contraseña de Llave Privada') ?>
                                </label>
                            </td>
                            <td>
                                <input type="password" id="inputPasswordContrasenaDeLlavePrivada" name="inputPasswordContrasenaDeLlavePrivada" />
                            </td>
                        </tr>
                        <tr>
                            <td class="center" colspan="2">
                                <input type="button" value="Crear" onclick="createSello()"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <br />
    <br />
    <div id="divTableSello">
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_sello.js"></script>
<script type="text/javascript">
    loadTableSello()
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
    break;
    case 'altaDeFolio':
?>
<div id="divAltaDeFolio" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <div id="divLigas">
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeSello">Administraci&oacute;n de Sellos</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=cfd">Reporte Mensual y CFDs emitidos</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=adminSeries">Administraci&oacute;n de Series</a>
        <br />
    </div>
    <br />
    <div id="divTableAltaDeFolio">
        <table id="tableAltaDeFolio" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Alta de Folio') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextValorInicial">
                            <?php echo _('Valor Inicial') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextValorInicial" name="inputTextValorInicial" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextValorFinal">
                            <?php echo _('Valor Final') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextValorFinal" name="inputTextValorFinal" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="inputTextSerie">
                            <?php echo _('Serie') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextSerie" name="inputTextSerie" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextAnoAprobacion">
                            <?php echo _('A&ntilde;o de Aprobaci&oacute;n') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextAnoAprobacion" name="inputTextAnoAprobacion" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextNoAprobacion">
                            <?php echo _('Numero de Aprobaci&oacute;n') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextNoAprobacion" name="inputTextNoAprobacion" />
                    </td>
                </tr>
                <tr>
                    <td class="center" colspan="2">
                        <input type="button" value="Crear" onclick="createFolio()"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div id="divTableFolio">
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_folio.js"></script>
<script type="text/javascript">
    loadTableFolio()
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
    break;
    case 'cfd':
?>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
    <div id="divLigas">
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeFolio">Administraci&oacute;n de Folios</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeSello">Reporte Mensual y CFDs emitidos</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=adminSeries">Administraci&oacute;n de Series</a>
        <br />
    </div>
<div id="divCfd" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <br />
    <div id="divTableReporteMensual">
        <table id="tableReporteMensual">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Reporte Mensual') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divSelectReporteMensual">
                            <?php echo _('Mes y A&ntilde;o') ?>
                        </label>
                    </td>
                    <td>
                        <div id="divSelectReporteMensual">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="center" colspan="2">
                        <input type="button" value="Descargar" onclick="downloadReporteMensual()"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
    <div id="divTableCfd">
    </div>
</div>
<br />
<br />
<center>
    <div id="reporte">
    </div>
</center>
<br/>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_cfd.js"></script>
<script type="text/javascript">
function loadTableCfd(){
    var yearAndMonth = $('selectReporteMensual').value
    if(!yearAndMonth)
        return
    var r = ajax('rh_j_cfd.php', {
        request: 'loadTableCfd',
        yearAndMonth: yearAndMonth
    });
    document.getElementById('reporte').innerHTML="<table cellpadding='0' cellspacing='0' border='1' > <tr>"+
            "<td>Id</td>"+
            "<td>Folio</td>"+
            "<td>Serie</td>"+
            "<td>Fecha Expedici&oacute;n</td>"+
            "<td>Razon Social</td>"+
            "<td>RFC</td>"+
            "<td>Sub-Total</td>"+
            "<td>Total</td>"+
            "<td>Aduanas</td>"+
            "<td>Aduanas</td>"+
            "<td>Aduanas</td>"+
            "<td>&nbsp;</td>"+
            "<td>&nbsp;</td>"+
            "<td>&nbsp;</td>"+
        "</tr>"+r+"</table>";
}

function loadSelectReporteMensual2(){
    var select = '<select id="selectReporteMensual"><option></option>'
    var r = ajax('rh_j_cfd.php', {
        request: 'loadSelectReporteMensual'
    })
    //Valida si ocurrio un error
    if(!(r instanceof Array)){
        document.getElementById('divSelectReporteMensual').innerHTML = r
        return
    }
    //\Valida si ocurrio un error
    for(var i = 2; i < r.length; i++){
        var text = r[i][0] + '-' + (r[i][1])
        select += '<option value="' + text + '">' + intToMonth(r[i][1]) + ' ' + r[i][0] + '</option>'
    }
    select += '</select>'
    document.getElementById('divSelectReporteMensual').innerHTML = select
}
    loadSelectReporteMensual2();
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
    break;
    case 'adminSeries':
?>
<div id="divAdminSeries" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <div id="divLigas">
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeSello">Administraci&oacute;n de Sellos</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeFolio">Administraci&oacute;n de Folios</a>
        <br />
        <a href="rh_j_globalFacturacionElectronica.html.php?page=cfd">Reporte Mensual y CFDs emitidos</a>
        <br />
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
                            $sqlUpdate = "update rh_cfd__locations__systypes__ws_csd set id_ws_csd = $idWsCsd where id_locations = '$idLocation' and id_systypes = $idSystype and serie = '$serie' limit 1";
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
                       $csds = getWs()->csds()->return;
                       for($i = 2; $i < count($csds); $i++){
                                $CSD[$csds[$i]->item[0]]=$csds[$i]->item[1];

                       }

                       $f = getWs()->folios()->return;
                       for($i = 2; $i < count($f); $i++){
                                $SERIE[$f[$i]->item[3]]=$f[$i]->item[3];

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
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_adminSeries.js"></script>
<script type="text/javascript">
    //loadTableAdminSeries()
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
    break;
    case 'main':
?>
<div id="divMain">
    <div id="divLigas" align="center">
    <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeSello">Administraci&ocute;n de Sellos</a>
    <br />
    <a href="rh_j_globalFacturacionElectronica.html.php?page=altaDeFolio">Administraci&oacute;n de Folios</a>
    <br />
    <a href="rh_j_globalFacturacionElectronica.html.php?page=cfd">Reporte Mensual y CFDs emitidos</a>
    <br />
    </div>
</div>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
    break;
    default:
        echo prnMsg('Consulta invalida', 'error');
    break;
}
include('includes/footer.inc');
?>
