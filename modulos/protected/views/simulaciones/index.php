<?php
$UsSQLVerify='select simulacion_aum_prec from www_users where userid="'.$_SESSION['UserID'].'"';
$Usrow = Yii::app()->db->createCommand($UsSQLVerify)->queryAll();

if ($Usrow[0]['simulacion_aum_prec']!=1){
    prnMsg('Usted no tiene permitido realizar la simulacion de aumentos de precios','error');
?>
        <div class="container-fluid"></div>
<?php
}else{ 

//echo "<pre>";print_r($fecha_inicio);exit();
    ?>
<script type="text/javascript">
$(document).on('ready',function() {
        $('#fecha').datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+5'
        });

        $(".Date2").datepicker({
                        dateFormat : 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '-100:+5'
                });

        
        $(".Select2").select2();
        $('#ListSimulaciones').dataTable( {
                "sPaginationType": "bootstrap",
                "sDom": 'T<"clear">lfrtip',
                "oTableTools": {
                        "aButtons": [{
                                "sExtends": "collection",
                                "sButtonText": "Exportar",
                                "aButtons": [ "print", "csv", "xls", "pdf" ]
                        }]
                },
                "fnInitComplete": function(){
                        $(".dataTables_wrapper select").select2({
                                dropdownCssClass: 'noSearch'
                        });
                },
                "aLengthMenu": [
                        [10,25, 50, 100, 200, -1],
                        [10,25, 50, 100, 200,"Todo"]
                ],
        });

        $("#CheckAll").click(function(event) {
                if(this.checked){
                        $('.ActualizarTarifa').attr('checked','checked')
                }else{
                        $('.ActualizarTarifa').removeAttr('checked');
                }
        });
        $("#redondear").click(function(event) {
                if(this.checked){
                        $('#Redondeartxt').val('SiAplica');
                }else{
                        $('#Redondeartxt').val('NoAplica');
                }
        });
});
function ActualizarTarifa(){
        if (confirm('¿Desea Actualizar la Tarifa de los folios seleccionados?')) {
                var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("simulaciones/Actualizar"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (0),
                data: {
                        Actualizar:{
                                Folio: $('.ActualizarTarifa').serialize(),
                                Redondeo: $('#Redondeartxt').val()
                        },
                },
                success : function(Response, newValue) {
                        console.log(Response);
                        if (Response.requestresult == 'ok') {
                                displayNotify('success', Response.message);
                                window.setTimeout(function() {
                                document.location.href = document.location.href;
                                }, 1000);}
                        else{
                                displayNotify('error', Response.message);
                        }
                },
                error : ajaxError
                });
        }
}
/* function RedondearTarifa(){
                var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("simulaciones/Actualizar"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (0),
                data: {
                        Redondear:{
                                CheckRedondeo: $('#redondeo').val(),
                        },
                },
                
                });
} */

</script>

<?php FB::info($GetData); ?>
<style>


h1 {
    color: #eee;
    font: 30px Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    text-shadow: 0px 1px black;
    text-align: center;
    margin-bottom: 50px;
}

/*input[type=checkbox] {
    visibility: hidden;
}*/
.redondear {
    width: 28px;
    height: 28px;
    background: #fcfff4;

    background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
    margin: 20px auto;

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;

    -webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    -moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    position: relative;
}

.redondear label {
    cursor: pointer;
    position: absolute;
    width: 10px;
    height: 20px;

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    left: 4px;
    top: 4px;

    -webkit-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
    -moz-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
    box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);

    background: -webkit-linear-gradient(top, #222 0%, #45484d 100%);
    background: -moz-linear-gradient(top, #222 0%, #45484d 100%);
    background: -o-linear-gradient(top, #222 0%, #45484d 100%);
    background: -ms-linear-gradient(top, #222 0%, #45484d 100%);
    background: linear-gradient(top, #222 0%, #45484d 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#222', endColorstr='#45484d',GradientType=0 );
}

.redondear label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
    filter: alpha(opacity=0);
    opacity: 0;
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    background: #00bf00;

    background: -webkit-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -moz-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -o-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -ms-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: linear-gradient(top, #00bf00 0%, #009400 100%);

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    top: 2px;
    left: 2px;

    -webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    -moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
}

.redondear label:hover::after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
    filter: alpha(opacity=30);
    opacity: 0.3;
}

.redondear input[type=checkbox]:checked + label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
    filter: alpha(opacity=100);
    opacity: 1;
}
</style>
<div class="container-fluid">
        <div class="control-group row-fluid">
                <div align="right">
                        <a href="<?php echo $this->createUrl("reportes/simulacionpreciosaplicada"); ?>">
                                <input type="button" value="IR A REPORTE DE SIMULACION DE AUMENTOS DE PRECIO" class="btn btn-info">
                        </a>
                </div>
        </div>
        <div class="control-group row-fluid">
                <div class="form-legend">
                        <h3>SIMULACIÓN Y APLICACIÓN DE AUMENTOS DE PRECIO</h3>
                </div>
                <div class="control-group row-fluid"><br>
                <?PHP 
                    $_POSTINICIO = $_POST['INICIO']; $POSTFIN =  $_POST['FIN'];
                    if (empty($_POSTINICIO) && empty($fecha_inicio)) {
                        $_POSTINICIO = "1990-01-01";
                    }elseif (empty($_POSTINICIO) && !empty($fecha_inicio)) {
                        $_POSTINICIO = $fecha_inicio;
                    }
                     
                    if (empty($POSTFIN)) {
                        $fecha = date('Y-m-d');
                        $nuevafecha = strtotime ('-1 year', strtotime($fecha));
                        $POSTFIN = date('Y-m-d', $nuevafecha );
                    } 
                ?>
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("simulaciones/create"); ?>">
                        <div class="control-group row-fluid">
                            <table width="100%">
                            <!-- <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("simulaciones/create"); ?>" > -->
                                 <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><label class="redondeartxt"><b>Redondear</b></label></th>
                                </tr>
                                <tr>
                                    <th>
                                        <div class="span12">
                                            <label class="control-label"><?php echo _('% DE AUMENTO:'); ?></label>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="span12">
                                            <div class="controls">
                                                <input type="text" id="prc_aumento_tarifa" name="prc_aumento_tarifa" />
                                            </div>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="span12">
                                            <label class="control-label"><?php echo _('FECHA NUEVO AUMENTO:'); ?></label>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="span12">
                                            <div class="controls">
                                                <input type="date" id="fecha_aumento_tarifa" name="fecha_aumento_tarifa" value="<?php echo date("Y-m-d");?>"/>
                                            </div>
                                            <div class="span3" style="display: none;">
                                                <label>Fecha de Ingreso:</label>
                                                <input type="text" name="INICIO" value=" <?= $_POSTINICIO ?>" style="width:100px;"/>

                                                <input type="text" name="FIN" value="<?= $POSTFIN ?>" style="width:100px;"/>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div align="left">
                                            <input type="submit" id="Save" class="btn btn-success" value="SIMULAR" />
                                        </div>
                                    </th>
                                    <th><br>
                                         <div class="redondear">
                                            <input type="checkbox" value="None" id="redondear" name="check" />
                                            <label for="redondear"></label>
                                        </div>

                                        <div class="span2">
                                            <input type="text" name="Redondeartxt" id="Redondeartxt" class="success " value="NoAplica" style="display: none;">
                                        </div>
                                    </th>
                                </tr> 
                                <!--</form>-->
                            </table>
                            <!--<div class="span2">
                                <label class="control-label"><?php //echo _('% DE AUMENTO:'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="prc_aumento_tarifa" name="prc_aumento_tarifa" />
                                </div>
                            </div>
                            <div class="span2">
                                <label class="control-label"><?php //echo _('FECHA NUEVO AUMENTO:'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="date" id="fecha_aumento_tarifa" name="fecha_aumento_tarifa" value="<?php //echo date("Y-m-d");?>"/>
                                </div>
                            </div>                     
                            <div class="span3" style="display: none;">
                                <label>Fecha de Ingreso:</label>
                                <input type="text" name="INICIO" value=" <?= $_POSTINICIO ?>" style="width:100px;"/>

                                <input type="text" name="FIN" value="<?= $POSTFIN ?>" style="width:100px;"/>
                            </div> 
                            
                            <div align="left">
                                <input type="submit" id="Save" class="btn btn-success" value="SIMULAR" />
                            </div>
                            <div class="span3">
                                <input type="checkbox" id = "redondear" name="redondear" value="si" class="success Redondear">
                    Redondear&nbsp;&nbsp;
                            </div> -->
                        </div>
                    </form>
                </div>
        </div>
        <br>
        <div class="control-group row-fluid">
            <form method="POST" action="<?php echo $this->createUrl("simulaciones/index/"); ?>" >
                <div class="span2">
                    <label class="control-label" >FOLIO:</label>
                    <div class="controls">
                        <input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width:100px;">
                    </div>
                </div>

                    <?PHP 
                        $_POSTINICIO = $_POST['INICIO']; $POSTFIN =  $_POST['FIN'];
                        if (empty($_POSTINICIO) && empty($fecha_inicio)) {
                                $_POSTINICIO = "1990-01-01";
                        }elseif (empty($_POSTINICIO) && !empty($fecha_inicio)) {
                                $_POSTINICIO = $fecha_inicio;
                            }
                         
                        if (empty($POSTFIN)) {
                            $fecha = date('Y-m-d');
                            $nuevafecha = strtotime ('-1 year', strtotime($fecha));
                            $POSTFIN = date('Y-m-d', $nuevafecha );
                        }
                    ?>

                <div class="span3">
                    <label>Fecha de Ingreso:</label>
                    <input type="text" id="INICIO" name="INICIO" placeholder="Inicio" 
                            value=" <?= $_POSTINICIO ?>" style="width:100px;" class="Date2"/>

                    <input type="text" id="FIN" name="FIN" placeholder="Fin" 
                    value="<?= $POSTFIN ?>" style="width:100px;" class="Date2"/>
                </div>

                <div class="span2">
                    <label>FormaPago:</label>
                    <select class="Select2" id="FORMA_PAGO" name="FORMA_PAGO" style="width:100px;">
                        <option value="">SELECCIONE</option>
                        <?php foreach ($ListaFormasPago as $key => $value) {echo "<option value='{$key}'>{$value}</option>";
                        } ?>
                    </select>
                    <script type="text/javascript">
                    $("#FORMA_PAGO option[value='<?=$_POST['FORMA_PAGO']?>']").attr("selected",true);
                    </script>
                </div>

                <div class="span2">
                    <label>FrecuenciaPago:</label>
                    <select class="Select2" id="FRECUENCIA_PAGO" name="FRECUENCIA_PAGO" style="width:100px;" >
                        <option value="">SELECCIONE</option>
                        <?php foreach ($ListaFrecuenciaPago as $key => $value) {
                                echo "<option value='{$key}'>{$value}</option>"; } ?>
                    </select>
                    <script type="text/javascript">
                    $("#FRECUENCIA_PAGO option[value='<?=$_POST['FRECUENCIA_PAGO']?>']").attr("selected",true);
                    </script>
                </div>

                <div class="span2">
                    <label>Plan:</label>
                    <select class="Select2" id="PLAN" name="PLAN" style="width:100px;" >
                        <option value="">SELECCIONE</option>
                        <?php foreach ($ListaPlanes as $key => $value) {
                            echo "<option value='{$key}'>{$value}</option>";
                        } ?>
                    </select>
                </div>

                    <script type="text/javascript">
                        $("#PLAN option[value='<?=$_POST['PLAN']?>']").attr("selected",true);
                    </script>

                <div align="left">
                    <label>&nbsp;&nbsp;</label>
                    <input type="submit" class="btn btn-success" name="BUSCAR" value="BUSCAR" >
                </div>
                <!--<div class="span2">
                    <input type="text" name="Redondeartxt" id="Redondeartxt" class="success Redondear" value="NoAplica" style="display: none;">
                </div> -->

                <div align="right">
                    <label>&nbsp;&nbsp;</label>
                    <input type="button" id="actualizar_tarifa" name="actualizar_tarifa" value="APLICAR SIMULACIÓN" onclick="ActualizarTarifa()" class="btn btn-small btn-danger" style=" height: 30px; margin-top: 18px;" />&nbsp;&nbsp;&nbsp;&nbsp;
                   <!-- <input type="checkbox" id = "redondear" name="redondear" value="si" class="success Redondear">
                    Redondear&nbsp;&nbsp;
                </div> -->

            </form>
        </div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active">
                <a data-toggle="tab" href="#Lista">CANDIDATOS PARA APLICAR AUMENTO DE PRECIO</a>
            </li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                        <?php include_once('modals/EditarSimulaciones.modal.php'); ?>
                        <table id="ListSimulaciones" class="table table-hover table-condensed table-striped">
                        <thead>
                        <tr>
                        <th><?php echo _('Folio') ?></th>
                        <th><?php echo _('Nombre') ?></th>
                        <th><?php echo _('FechaIngreso') ?></th>
                        <th><?php echo _('Estatus') ?></th>
                        <th><?php echo _('#Socios') ?></th>
                        <th><?php echo _('Empresa') ?></th>
                        <th><?php echo _('Plan') ?></th>
                        <th><?php echo _('FormaPago') ?></th>
                        <th><?php echo _('FrecuenciaPago') ?></th>
                        <th><?php echo _('ServiciosMes') ?></th>
                        <th><?php echo _('ServiciosAcumulados') ?></th>
                        <th><?php echo _('FechaUltimoAumento') ?></th>
                        <th><?php echo _('TarifaActual') ?></th>
                        <th><?php echo _('%DeAumento') ?></th>
                        <th><?php echo _('FechaAumento') ?></th>
                        <th><?php echo _('TarifaNueva') ?></th>
                        <th><?php echo _('TarifaRedondeada/Modificada') ?></th>
                        <th><?php echo _('Usuario') ?></th>
                        <th title="Actualizar Tarifa">ActualizarTarifa<input type='checkbox' id='CheckAll'></th>
                        <th><?php echo _('Acciones') ?></th>
                        </tr>
                        </thead>
                                <tbody>
                                <?php foreach ($AumentosprecioData as $Aumentosprecio){
                                $InvoiceBadge = "";
                                if(!empty($Aumentosprecio['CostoAfiliacion'])){
                                $InvoiceBadge = "success";
                                }else{
                                $InvoiceBadge = "info";
                                }
                                if($Aumentosprecio['CostoAfiliacion'] == 'Error'){
                                $InvoiceBadge = "danger";
                                }
                                if(!empty($Aumentosprecio['FECHA_ULTIMO_AUMENTO'])){
                                $InvoiceBadge = "success";
                                }else{
                                $InvoiceBadge = "info";
                                }
                                if($Aumentosprecio['FECHA_ULTIMO_AUMENTO'] == 'Error'){
                                $InvoiceBadge = "danger";
                                }
                                ?>
                                <tr id='<?=$Data['Folio']?>' >
                                <td><?=$Aumentosprecio['Folio']?></td>
                                <td><?=$Aumentosprecio['NOMBRE']?></td>
                                <td><?=$Aumentosprecio['FECHA_INSCIPCION']?></td>
                                <td><?=$Aumentosprecio['ESTATUS_TITULAR']?></td>
                                <td><?=$Aumentosprecio['NumSocios']?></td>
                                <td><?=$Aumentosprecio['Empresa']?></td>
                                <td><?=$Aumentosprecio['PLAN']?></td>
                                <td><?=$Aumentosprecio['FORMA_PAGO']?></td>
                                <td><?=$Aumentosprecio['FRECUENCIA_PAGO']?></td>
                                <td><?=$Aumentosprecio['ServiciosMes']?></td>
                                <td><?=$Aumentosprecio['ServiciosAcum']?></td>
                                <td>
                                    <span class='badge badge-<?=$InvoiceBadge?>'>
                                        <?=$Aumentosprecio['FECHA_ULTIMO_AUMENTO']?>
                                    </span>
                                </td>
                                <td style="text-align:center;">
                                    <span class='badge badge-<?=$InvoiceBadge?>'>
                                        <?=number_format($Aumentosprecio['costo_total'],2,'.', ',')?>
                                    </span>
                                </td>
                                <td><?=$Aumentosprecio['prc_aumento_tarifa']?></td>
                                <td><?=$Aumentosprecio['FechaAumento']?></td>
                                <td><?=number_format($Aumentosprecio['CostoNuevo'],2,'.', ',')?></td>
                                <td><?=number_format($Aumentosprecio['tarifa_redondeada'],2,'.', ',')?></td>
                                <td><?=$Aumentosprecio['Usuario']?></td>
                                <td style="text-align:center;">
                                        <input type="checkbox" id="ActualizarTarifa<?=$Aumentosprecio['id']?>" name="ActualizarTarifa[]" value="<?=$Aumentosprecio['folio']?>" title="Actualizar Tarifa Si/NO" class="success ActualizarTarifa" />
                                </td>
                                <td>
                                <a onclick="EditarSimulaciones('<?=$Aumentosprecio['id']?>');" title="Editar simulación de aumentos de precio" ><i class="icon-edit"></i></a>
                                </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                        </table>
                </div>
        </div> <!-- End Tabs -->
</div>
</div>
<?php } ?>