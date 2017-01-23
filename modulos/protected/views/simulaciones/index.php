<?php
$UsSQLVerify='select simulacion_aum_prec from www_users where userid="'.$_SESSION['UserID'].'"';

//$UsResult=DB_query($UsSQLVerify,$db);
//$Usrow = DB_fetch_row($UsResult);
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
                                Folio: $('.ActualizarTarifa').serialize()
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


</script>

<?php FB::info($GetData); ?>

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
                <div class="control-group row-fluid">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("simulaciones/create"); ?>" >
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
					               
		            <div class="span3" style="display: none;">
	                <label>Fecha de Ingreso:</label>
	                <input type="text" name="INICIO" value=" <?= $_POSTINICIO ?>" style="width:100px;"/>

	                <input type="text" name="FIN" value="<?= $POSTFIN ?>" style="width:100px;"/>
	            </div>
	                            <div align="left">
	                                <input type="submit" id="Save" class="btn btn-success" value="SIMULAR" />
	                            </div>
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
                                         
                                        //echo $nuevafecha;
                        //$_POSTFIN = strtotime ('-1 year' ,strtotime($POSTFIN));
                        //$__POSTFIN = date ( 'Y-m-d' , $_POSTFIN );
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
		                <select class="Select2" id="FRECUENCIA_PAGO" name="FRECUENCIA_PAGO"
		                style="width:100px;" >
		                    <option value="">SELECCIONE</option>
		                    <?php foreach ($ListaFrecuenciaPago as $key => $value) {
		                            echo "<option value='{$key}'>{$value}</option>";
		                    } ?>
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
		                <div align="right">
		                        <label>&nbsp;&nbsp;</label>
		                        <input type="button" id="actualizar_tarifa" name="actualizar_tarifa" value="APLICAR SIMULACIÓN"
		                        onclick="ActualizarTarifa()" class="btn btn-small btn-danger" style=" height: 30px; margin-top: 18px;" />
		                </div>
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