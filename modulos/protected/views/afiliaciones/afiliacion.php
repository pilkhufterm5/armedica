<script type="text/javascript">

function Srecargarpagina(){
        setTimeout(function(){
            location.reload();
        },15000);
}
//alert("Hola");
    $(document).on('ready', function() {
        $('#Fecha_Baja').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        $('#Fecha_Cancelacion_Efectiva').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $('#M_Cancelacion').select2();


/* /////////////////////////////////////////////////////////////////////////////
///////////////////OPCIONES DE CANCELADO //////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
 */
        /*Confirma Cancelacion del Folio*/
        $('#Cancelar').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Cancelar el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#Modal-Cancelar').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });

/*Genera Cancelacion de Socio*/
        $('#Create-Modal-Cancelar').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/cancelarafiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Cancelacion:{
                          folio: $('#Folio').val(),
                          fecha_baja: $('#Fecha_Baja').val(),
                          fecha_cancelacion: $('#Fecha_Cancelacion_Efectiva').val(),
                          mo_cancelacion: $('#M_Cancelacion').val(),
                          motivo_cancelacion: $('select[id="M_Cancelacion"] :selected').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#Modal-Cancelar').modal("toggle");
                        $("#AFStatus").text("Afiliado Cancelado");
                        $("#AFStatus").removeClass("badge-success");
                        $("#AFStatus").addClass("badge-important");
                        $("#UpdateData").hide();

                        ConfirmPDF(data.CancelNo);
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError,
            });
        });
        /*Confirmacion Abrir PDF*/
        function ConfirmPDF(CancelNo){
            Folio = $('#Folio').val();
            $('<div></div>').appendTo('body')
            .html('<div><h6>El Folio: <?=$_POST['Folio']?> ha sido Cancelado Correctamente.<br> Folio de Cancelacion: ' + CancelNo + ' <br> ¿Desea Imprimir el Reporte de Cancelación?</h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            OpenInNewTab("<?php echo $this->createUrl('afiliaciones/viewcancelpdf'); ?>&Folio=" + Folio + "&CancelNo=" + CancelNo);
                            $('#LSuspension').hide();
                            $('#Cancelar').hide();
                            $('#Suspender').hide();
                            $(this).dialog("close");
                            //window.location = window.location;
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        }

        function OpenInNewTab(url ){
            var win = window.open(url, '_blank');
            win.focus();
        }

/* ////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// OPCIONES DE SUSPENDIDO /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////
*/
/*
Confirma suspencion del folio
Eliobeth Ruiz 21/12/2016 10:00am
 */

// recargar la pagina despues de suspender






 $('#EModal_Suspender').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Suspender el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Suspender Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#EModal_SuspenderAfiliado').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });
/*Genera Suspensión de Socio*/
        $('#ESend_Modal_SuspenderAfiliado').click(function(){
            Folio = $('#Folio').val();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/SuspenderAfiliado"); ?>",
                type: "POST",
                dataType : "json",
               // timeout : (120 * 1000),
                timeout : (function(){
                    location.reload();
                }, 4000),
                data: {
                      Suspension:{
                          folio: Folio,
                          SFecha_Inicial: $('#SAfecha_inicial').val(),
                          SFecha_Final: $('#SAfecha_final').val(),
                          SMotivos: $('#SAMotivo_Suspension').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $("#AFStatus").text("Afiliado Suspendido");
                        $("#AFStatus").removeClass("badge-success");
                        $("#AFStatus").addClass("badge-warning");
                        $('#Cancelar').hide();
                        $('#Suspender').hide();
                        $("#UpdateData").hide();
                        if (confirm('El Folio: <?=$_POST['Folio']?> ha sido Suspendido Correctamente. \n ¿Desea realizar la impresion del Reporte de Suspensión?')) {
                            OpenInNewTab("<?php echo $this->createUrl('afiliaciones/viewsuspendpdf'); ?>&Folio=" + Folio + "&SuspendNo=" + data.SuspendNo);
                            //OpenInNewTab("../tmp/suspensiones/SuspendNo-" + data.SuspendNo + ".pdf");
                        } else {
                            // Do nothing!
                        };
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError
            });
        });

        

        function OpenInNewTab(url ){
            var win = window.open(url, '_blank');
            win.focus();
        }

        /*Abre Modal Levantar Suspension*/
        $('#LSuspension').click(function(){
            if (confirm('Esta seguro de Levantar la Suspensión al Folio: <?=$_POST['Folio']?>')) {
                $('#ModalLabelReactivacion').text('Reactivar Folio <?=$_POST['Folio']?>');
                $('#Reac_DebtorNo').val(<?=$_POST['debtorno']?>);
                $('#Reac_Tipo').val('LevantaSuspencion');
                $('#Modal_ReactivarAfiliado').modal('show');
            } else {
                // Do nothing!
            };
        });

        $('#Reactivar').click(function(){
            if (confirm('Esta seguro de Reactivar el Folio: <?=$_POST['Folio']?>')) {
                $('#ModalLabelReactivacion').text('Reactivar Folio <?=$_POST['Folio']?>');
                $('#Reac_DebtorNo').val(<?=$_POST['debtorno']?>);
                $('#Reac_Tipo').val('ReactivacionFolio');
                $('#Modal_ReactivarAfiliado').modal('show');
            } else {
                // Do nothing!
            };
        });

        

        <?php if($TipoMembresia == "Socio"){ ?>
        $('#SaveData').click(function() {
            var Counter = 0;
            $(".SS").each(function(index){
                if ($(this).is(":checked")){
                    Counter++;
                }
            });
            if(Counter == 0){
                alert ('Debe seleccionar un servicio');
                return false;
            }
            $("#AfiliacionForm").valid();
        });
        <?php } ?>


        $('#Limpiar').click(function() {
            //$("#AfiliacionForm").reset();
            $("#AfiliacionForm input").val('');
            $("#AfiliacionForm select option[value='']").attr("selected",true);
            $("#Search").val('Buscar');
            $("#Limpiar").val('Limpiar');
            $("#UpdateData").val('Actualizar');
            $("#SaveData").val('Guardar y Continuar');
        });
    });
</script>
<style>
    input.mayusculas{
        text-transform:uppercase;
    }

    input[type="text"]{
        margin-bottom: 0px;
     }
    .btnSearch{
        height: 35px;
        margin-top: 10px;
    }
</style>

<?php
FB::INFO($_POST, 'POST');
?>

<div id="Modal-Cancelar" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Cancelación de Folio: <?=$_POST['Folio']?></h3>
    </div>
    <div class="modal-body">
        <p>
        <div class="row span12">
            <div class="span6">
                <label>Fecha Baja:</label>
                <input id="Fecha_Baja" type="text" class="span6" required="true" 
                    value="<?php echo date("Y-m-d"); ?>" />
            </div>
            <div class="span6">
                <label>Cancelación Efectiva a partir de:</label>
                <input id="Fecha_Cancelacion_Efectiva" type="text" class="span6" required="true" 
                value="<?php echo date("Y-m-d"); ?>"/>
            </div>
        </div>

        <div style="height: 80px;"></div>
        <div class="row span12">
            <label>Motivo Cancelación: (<font color="red">* Requerido</font>)</label>
            <select id="M_Cancelacion" class="span6" >
            <option value="" selected="true">-- SELECCIONE --</option>
            <?php foreach($ListaMotivosCancelacion as $id => $Name){ ?>
                <option value="<?=$id?>"><?=$Name?></option>
            <?php } ?>
            </select>
        </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-Cancelar" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-Cancelar" class="btn btn-danger">Aceptar</button>
    </div>
</div>

<!-- ///////////////////////////////////////////////////////////////////////////////////////// 
/* Creacion de ventana flotante para suspender folio
Eliobeth Ruiz 21/12/2016 */
-->
<div id="EModal_SuspenderAfiliado" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelSuspensionA"> </h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <div class="row span12">
                <div class="span6 controls">
                    <label class="control-label">Fecha Inicial: </label>
                    <input type="text" id="SAfecha_inicial" class="span6" 
                    value="<?php echo date("Y-m-d"); ?>" />
                </div>
                <div class="span6 controls">
                    <label class="control-label">Fecha Final: </label>
                    <input type="text" id="SAfecha_final" class="span6" 
                    value="<?php echo date("Y-m-d"); ?>" />
                </div>
            </div>

            <div class="span12" style="margin-top: 20px;">
                    <label class="control-label">Motivo de Suspensión: (<font color="red">* Requerido</font>)</label>
                    <textarea id="SAMotivo_Suspension" required="required" placeholder="Ingresa el motivo de la suspensión..."></textarea>
                    <span class="field-annotation"></span>
                    <script>
                        $("#SAMotivo_Suspension").charCount({
                            allowed: 200,
                            warning: 50,
                            counterText: 'Caracteres restantes: '
                        });
                    </script>
            </div>
            <div style="height: 20px;"></div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-SuspenderAfiliado" class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
        <button id="ESend_Modal_SuspenderAfiliado" class="btn btn-warning" onclick="Srecargarpagina();">Suspender</button>
    </div>
</div>


 <!-- /////////////////////////////////////////////////////////////////////////////////////////
 /* Fin de creacion de ventana modal para la suspension del folio */
  -->


<?php include_once("afiliacion/modals/Afiliado_Suspender.php"); ?>
<?php include_once("afiliacion/modals/Afiliado_Activar.php"); ?>

<?php
switch ($_POST['movimientos_afiliacion']) {
    case 'Activo':
        $BadgeType = "success";
        break;
    case 'Cancelado':
        $BadgeType = "important";
        break;
    case 'Suspendido':
        $BadgeType = "warning";
        break;
    default:
        //$BadgeType = "info";
        break;
}
?>

<div style="height: 10px;"></div>
<div class="container-fluid">
    <a href="<?php echo $this->createUrl("afiliaciones/cobranza&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Cobranza" /></a>
    <?php if($TipoMembresia == "Socio"){ ?>
    <a href="<?php echo $this->createUrl("afiliaciones/socios&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Socios" /></a>
    <?php } ?>
</div>
<form class="form-horizontal" id="AfiliacionForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/afiliacion/"); ?>" >

    <div class="container-fluid">
        <div class="form-legend">&nbsp;<?php if(!empty($_POST['movimientos_afiliacion'])){ echo "<span id='AFStatus' class='badge badge-{$BadgeType}'>" . $TipoMembresia . " " . $_POST['movimientos_afiliacion']; } ?></span></div>
        <div class="control-group row-fluid">
            <div class="span12">
                <div class="controls">
                    <div class="span1">
                        <label class="control-label"><?php echo _('Folio'); ?></label>
                    </div>
                    <div class="span10">
                        <div class="controls">
                            <input type="text" class="required" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width: 100px;" />
                            <input type="submit" id="Search" name="Search" class="btn btn-small btnSearch" value="Buscar"  />
                            <a href="<?php echo $this->createUrl("afiliaciones/buscarfolio/"); ?>"><input type="button" id="SearchByName" name="SearchByName" class="btn btn-small btnSearch" value="Buscar por Nombre"  /></a>
                             <!--Se agrego boton para buscar por socios Angeles Perez 2016-03-14 --> 
                            <a href="<?php echo $this->createUrl("afiliaciones/buscarfoliosocio/"); ?>"><input type="button" id="SearchByName" name="SearchByName" class="btn btn-small btnSearch" value="Buscar por Socio" /></a>
                            <!--Termina -->
<!--Se agrego boton para Bitacora de Seguimiento Angeles Perez 2016-07-11--> 
<?php if(count($BitacoraSeguimiento)>0){ 
 echo '<a href="'.$this->createUrl("seguimiento/index/").'"><input type="button" id="SearchByName" name="SearchByName" style="background:#F00; color:white" class="btn btn-small btnSearch" value="Bitacora Seguimiento"/></a>';
 } 
else{
 echo '<a href="'.$this->createUrl("seguimiento/index/").'"><input type="button" id="SearchByName" name="SearchByName" class="btn btn-small btnSearch" value="Bitacora Seguimiento"/></a>';
}
?>
<!--Termina -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 10px;"></div>
    <div id="Accordion" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseOne">


                    <?php echo _('Datos del Titular'); ?>

                    <?php echo _('Datos del Titular Titular'); ?>


                    <?php echo _('Datos del Titular Titular'); ?>

                </a>
            </div>

            <div id="collapseOne" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <?php include("afiliacion/contact_information.php"); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseTwo">
                    <?php echo _('Dirección'); ?>
                </a>
            </div>
            <div id="collapseTwo" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <?php include("afiliacion/direccion.php"); ?>
                    </p>
                </div>
            </div>
        </div>

        <?php if($TipoMembresia == "Socio"){ ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseThree">
                    <?php echo _('Servicios Seleccionados'); ?>
                </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse ">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
                        <?php include("afiliacion/servicios_seleccionados.php"); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseFour">
                    <?php echo _('Examenes de Laboratorio'); ?>
                </a>
            </div>
            <div id="collapseFour" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <?php include("afiliacion/examenes_laboratorio.php"); ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseFive">
                    <?php echo _('Servicios Impartidos | Costos | Estado de Cuenta'); ?>
                </a>
            </div>
            <div id="collapseFive" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <?php include("afiliacion/servicios_impartidos.php"); ?>
                    </p>
                </div>
            </div>
        </div>


        <?php if(($_SESSION['rh_AdminAfil'] == 1) && ($TipoMembresia == "Socio")){ ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#_Accordion" href="#collapse6">
                    <?php echo _(' Movimientos de Afiliacion'); ?>
                </a>
            </div>
            <div id="collapse6" class="accordion-body ">
                <div class="accordion" style="padding: 0px;" >
                    <div style="height: 10px;"></div>
                    <p style="text-align: center;">
                    <?php  ///PRINT BUTTONS
                    switch ($_POST['movimientos_afiliacion']) {
                        case 'Cancelado':
                            echo '<a id="Reactivar" name="Reactivar" class="btn btn-success btn-large"><i class="icon-star icon-white" style="margin-top: 4px;"></i> Reactivar</a>&nbsp;';
                            break;
                        case 'Suspendido':// Se agrego el echo para cancelar el folio directamente sin levantar la suspención Angeles Perez 29/04/2016
                            echo '<a id="LSuspension" name="LSuspension" class="btn btn-info btn-large">   <i class="icon-star icon-white" style="margin-top: 4px;"></i > Levantar Suspension</a>&nbsp;';
                            echo '<a id="Cancelar"  name="Cancelar"  class="btn btn-danger btn-large" ><i class="icon-remove-sign icon-white" style="margin-top: 4px;"></i> Cancelar</a>&nbsp;';
                            break;
                        case 'Activo':
                            echo '<a id="Cancelar"  name="Cancelar"  class="btn btn-danger btn-large" ><i class="icon-remove-sign icon-white" style="margin-top: 4px;"></i> Cancelar</a>&nbsp;';
                            echo '<a id="EModal_Suspender" name="Suspender" class="btn btn-warning btn-large"><i class="icon-exclamation-sign icon-white" style="margin-top: 4px;"></i> Suspender</a>&nbsp;';
                        break;
                        default:

                            break;
                    }
                    ?>
                    </p>

<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListMotivosCancelacionSuspension').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
    });

</script>
<!--Termina-->
<!--Se agrego para mostrar los movimientos de cancelacion y suspension Daniel Villarreal 06-05-2016-->
        <?php if(count($MovAfiliacion)>0)
        {?>
        <!--Se agregarom para poder exportar a excel la tabla  Angeles Perez 13-05-2016 -->
                <div class="tab-content"> 
                <div id="Lista" class="tab-pane fade active in">
                <table id="ListMotivosCancelacionSuspension" class="table table-hover table-condensed table-striped">     
            <!--Termina -->  
                <thead>
                    <tr>
                        <th>Fecha Ingreso</th><!--Se agrego Angeles Perez 12-05-2016-->
                        <th>Afiliado</th>
                        <th>Usuario</th>
                        <th>No. Folio Estatus</th>
                        <th>Movimiento</th>
                        <th>Motivo</th>
                        <th>Fecha Baja</th>
                        <th>Fecha Efectiva Cancelación</th>
                        <th>Fecha Inicial Suspensión </th>
                        <th>Fecha Fecha Final Suspensión</th>
                        <th>Fecha Reactivación</th>
                        <th>Tarifa</th><!--Se agrego Angeles Perez 12-05-2016-->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($MovAfiliacion as $rows){ ?>
                    <tr>
                        <td><?=$rows['fecha_inicial']?></td><!--Se agrego Angeles Perez 12-05-2016-->
                        <td><?=$rows['folio']?></td>
                        <td><?=$rows['userid']?></td>
                        <td><?=$rows['moveno']?></td>
                        <td><?=$rows['movetype']?></td>


                        <td><?=$rows['motivos'].' '.'['.$rows['motivo'].']'?></td>

                        <td><?=$rows['motivos'].' '.'['.$rows['motivo'].']'?></td><!--Se concateno el motivo para mostrarla descripcion del motivo de cancelación Angeles Perez 13-05-2016-->


                        <td><?=$rows['motivos'].' '.'['.$rows['motivo'].']'?></td><!--Se concateno el motivo para mostrarla descripcion del motivo de cancelación Angeles Perez 13-05-2016-->

                        <td><?=$rows['fecha_baja']?></td>
                        <td><?=$rows['fecha_cancelacion']?></td>
                        <td><?=$rows['sus_fechainicial']?></td>
                        <td><?=$rows['sus_fechafinal']?></td>
                        <td><?=$rows['fecha_reactivacion']?></td>
                        <td><?=$rows['tarifa_total']?></td><!--Se agrego Angeles Perez 12-05-2016-->

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?> 
<!--Termina-->
                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    <div id="Savebtn" class="control-group row-fluid">
        <div class="span2">
            <?php if($_POST['Action'] == "UPDATE"){
                if($_POST['movimientos_afiliacion'] == "Cancelado" || $_POST['movimientos_afiliacion'] == "Suspendido"){
                    $Display = "display: none;";
                }
                ?>
                <input type="submit" id="UpdateData" name="UpdateData" class="btn btn-large btn-success" value="Actualizar" style="margin-bottom: 0px; <?=$Display?>" />
            <?php }else{ ?>
                <input type="submit" id="SaveData" name="SaveData" class="btn btn-large btn-success" value="Guardar y Continuar" style="margin-bottom: 0px;" />&nbsp;
            <?php } ?>
        </div>
        <div class="span2">
                <a href="<?php echo $this->createUrl("afiliaciones/afiliacion"); ?>"><input type="button" id="_Limpiar" name="_Limpiar" class="btn btn-large btn-success" value="Limpiar" style="margin-bottom: 0px;" /></a>
        </div>
    </div>
</form>
