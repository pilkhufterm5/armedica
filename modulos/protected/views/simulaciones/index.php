<script type="text/javascript">

$(document).on('ready', function(){
        $('#ListSimulaciones').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", "pdf" ]
                }]
        },  });

        $(".Date2").datepicker({
                        dateFormat : 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        yearRange: '-100:+5'
                });

    });

</script>
<script>
    
$(document).on('ready',function() {
        $('#fecha').datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+5'
      }  
$(".Select2").select2();
});  

"fnInitComplete": function(){
        $(".dataTables_wrapper select").select2({
        dropdownCssClass: 'noSearch'
        });
},

"aLengthMenu": [
[10,25, 50, 100, 200, -1],
[10,25, 50, 100, 200,"Todo"]

$("#CheckAll").click(function(event) {
    if(this.checked){
        $('.ActualizarTarifa').attr('checked','checked')
    }else{
        $('.ActualizarTarifa').removeAttr('checked');
    }
});
        
</script>
<script>
    function ActualizarTarifa(){

                if (confirm('¿Desea Actualizar la Tarifa de todas los Folios de la busqueda?')) {
                        var jqxhr = $.ajax({
                                url: "<?php echo $this->createUrl("simulaciones/Actualizar"); ?>",
                                type: "POST",
                                dataType : "json",
                                timeout : (0),
                                data: {
                                        Actualizar:{
                                        Folio: $('.ActualizarTarifa').serialize()
                                        },
                                },
                                success : function(Response, newValue) {
                                        console.log(Response);
                                        if (Response.requestresult == 'ok') {
                                                displayNotify('success', Response.message);
                                                window.setTimeout(function() {
                                                        document.location.href = document.location.href;
                                                }, 1000);
                                }else{
                                        displayNotify('error', Response.message);
                                        }
                                },
                                error : ajaxError
                        });
                }
        }
</script>


<?php FB::info($GetData); ?>

<div style="height: 20px;"></div>
<div class="container"><br>
        <a href="<?php echo $this->createUrl("reportes/simulacionpreciosaplicada"); ?>">
                <input type="button" value="IR A REPORTE DE AUMENTOS DE TARIFA APLICADOS" class="btn btn-info" align="right">
        </a><br><br>
    <div class="control-group row-fluid">
        <div class="form-legend">
            <h3>SIMULACIÓN Y APLICACIÓN DE AUMENTOS DE PRECIO</h3>
        </div>
    </div>
    <div class="control-group row-fluid">
        <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("simulaciones/create/"); ?>" >
        <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label"><?php echo _('% DE AUMENTO:'); ?></label>
                </div>

                <div class="span3">
                    <div class="controls">
                        <input type="text" id="prc_aumento_tarifa" name="prc_aumento_tarifa" />
                    </div>
                </div>

                <div class="span2">
                    <label class="control-label"><?php echo _('FECHA NUEVO AUMENTO:'); ?></label>
                </div>

                <div class="span3">
                <div class="controls">
                        <input type="date" id="fecha_aumento_tarifa" name="fecha_aumento_tarifa" value="<?php echo date("Y-m-d");?>"/>
                </div>
                </div>

        <div align="left">
            <input type="submit" id="Save" class="btn btn-success" value="SIMULAR" />
        </div>

        </form>
    </div>
<!-- Creacion del tap CANDIDATOS PARA APLICAR AUMENTOS DE PRECIO -->    
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"> <a data-toggle="tab" href="#Lista"> CANDIDATOS PARA APLICAR AUMENTO DE PRECIO </a> </li>
    </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="Lista" class="tab-pane fade active in">
            <form method="POST" action="<?php echo $this->createUrl("simulaciones/index/"); ?>" >
                <div class="span2">
                    <label class="control-label" >FOLIO:</label>
                    <div class="controls">
                        <input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width:100px;">
                    </div>
                </div>

                <div class="span3">
                    <label>Fecha de Ingreso:</label>
                    <input type="text" id="INICIO" name="INICIO" placeholder="Inicio"                       value="<?=$_POST['INICIO']?>" style="width:100px;" class="Date2"/>
                    <input type="text" id="FIN" name="FIN" placeholder="Fin" value="<?=$_POST['FIN']?>" style="width:100px;" class="Date2"/>                                
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
                    <select class="Select2" id="FRECUENCIA_PAGO" name="FRECUENCIA_PAGO"
                    style="width:100px;" >
                        <option value="">SELECCIONE</option>
                        <?php foreach ($ListaFrecuenciaPago as $key => $value) {echo "<option value='{$key}'>{$value}</option>";}?>
                    </select>
                    <script type="text/javascript">
                    $("#FRECUENCIA_PAGO
                    option[value='<?=$_POST['FRECUENCIA_PAGO']?>']").attr("selected",true);
                    </script>
                </div>

                <div class="span2">
                    <label>Plan:</label>
                    <select class="Select2" id="PLAN" name="PLAN" style="width:100px;" >
                        <option value="">SELECCIONE</option>
                        <?php foreach ($ListaPlanes as $key => $value) {echo "<option value='{$key}'>{$value}</option>";} ?>
                    </select>
                    <script type="text/javascript">
                    $("#PLAN option[value='<?=$_POST['PLAN']?>']").attr("selected",true);
                    </script>
                </div>

                <div align="left">
                    <label>&nbsp;&nbsp;</label>
                    <input type="submit" class="btn btn-success" name="BUSCAR" value="BUSCAR" >
                </div>


                <div align="right">
                    <label>&nbsp;&nbsp;</label>
                    <input type="button" id="actualizar_tarifa" name="actualizar_tarifa" value="APLICAR SIMULACIÓN" onclick="ActualizarTarifa()" class="btn btn-small btn-danger" style=" height: 30px; margin-top: 18px;" />
                </div>
            </form>
        </div>

        <?php include_once('modals/EditarSimulaciones.modal.php'); ?>
    <hr><br>
        <div class="table-responsive">
            <table id="ListSimulaciones" class="table table-hover table-condensed table-striped">
    <!-- INICIA ENCABEZADOS DE LA TABLA -->        
                <thead>
                    <tr>
                    <th><?php echo _('Folio') ?></th>
                    <th><?php echo _('Nombre') ?></th>
                    <th><?php echo _('Fecha Ingreso') ?></th>
                    <th><?php echo _('Estatus') ?></th>
                    <th><?php echo _('#Socios') ?></th>
                    <th><?php echo _('Empresa') ?></th>
                    <th><?php echo _('Plan') ?></th>
                    <th><?php echo _('Forma Pago') ?></th>
                    <th><?php echo _('Frecuencia Pago') ?></th>
                    <th><?php echo _('Servicios Mes') ?></th>
                    <th><?php echo _('Servicios Acumulados') ?></th>
                    <th><?php echo _('Fecha Ultimo Aumento') ?></th>
                    <th><?php echo _('Tarifa Actual') ?></th>
                    <th><?php echo _('% De Aumento') ?></th>
                    <th><?php echo _('Fecha Aumento') ?></th>
                    <th><?php echo _('Tarifa Nueva') ?></th>
                    <th><?php echo _('Usuario') ?></th><th title="Actualizar Tarifa">Actualizar Tarifa<input type='checkbox' id='CheckAll'></th>
                    <th><?php echo _('Acciones') ?></th>
                    </tr>
                </thead> <!-- TERMINA ENCABEZADOS DE LA TABLA --> 

    <!-- INICIA CONTENIDO DE LA TABLA -->            
                <tbody>
                    <?php foreach ($AumentosprecioData as $Aumentosprecio){
                    $InvoiceBadge = "";

                        if(!empty($Aumentosprecio['CostoAfiliacion'])){
                            $InvoiceBadge = "success";
                        }else{ $InvoiceBadge = "info"; }

                        if($Aumentosprecio['CostoAfiliacion'] == 'Error'){
                            $InvoiceBadge = "danger";
                        }

                        if(!empty($Aumentosprecio['FECHA_ULTIMO_AUMENTO'])){
                            $InvoiceBadge = "success";
                        }else{ $InvoiceBadge = "info"; }

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
                    <td><?=$Aumentosprecio['Empresa']?></td><td><?=$Aumentosprecio['PLAN']?></td>
                    <td><?=$Aumentosprecio['FORMA_PAGO']?></td>
                    <td><?=$Aumentosprecio['FRECUENCIA_PAGO']?></td>
                    <td><?=$Aumentosprecio['ServiciosMes']?></td>
                    <td><?=$Aumentosprecio['ServiciosAcum']?></td>
                    <td>
                        <span class='badge badge- <?=$InvoiceBadge?>'><?=$Aumentosprecio['FECHA_ULTIMO_AUMENTO']?></span>
                    </td>
                    <td style="text-align:center;">
                        <span class='badge badge-<?=$InvoiceBadge?>'><?=number_format($Aumentosprecio['costo_total'],2,'.', ',')?></span>
                    </td>
                    <td><?=$Aumentosprecio['prc_aumento_tarifa']?></td>
                    <td><?=$Aumentosprecio['FechaAumento']?></td>
                    <td><?=number_format($Aumentosprecio['CostoNuevo'],2,'.', ',')?></td>
                    <td><?=$Aumentosprecio['Usuario']?></td>
                    <td style="text-align:center;">
                    <input type="checkbox" id="ActualizarTarifa<?=$Aumentosprecio['id']?>" name="ActualizarTarifa[]"
                    value="<?=$Aumentosprecio['folio']?>" title="Actualizar Tarifa Si/NO" class="success
                    ActualizarTarifa" />
                    </td>
                    <td>
                        <a onclick="EditarSimulaciones('<?=$Aumentosprecio['id']?>');" title="Editar simulación de aumentos de precio" >
                            <i class="icon-edit"></i>
                        </a>
                    </td>
                    </tr>
                    <?php } ?>
                </tbody> <!-- TERMINA CONTENIDO DE LA TABLA --> 
            </table>       
        </div>
    </div> <!-- End Tabs -->
</div>