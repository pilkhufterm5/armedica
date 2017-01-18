<script type="text/javascript">
    $(document).on('ready',function() {
        
        $('#fecha_incidencia').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#fecha_cierre').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

    //$("#motivo_incidencia").select2();
    //$("#cierre").select2();
    //$("#asignado").select2();
    

    // Se agrego para validar que el socio no tenga incidencia registrada 
   $(window).on('ready',function() {

    $(".SearchbyName").autocomplete({
            select: function( event, ui ) {
                $("#folio_socio").val(ui.item.folio_socio);
                return ui.item.value;
            },
            source: function( request, response ) {

                $.ajax({
                    url: "<?php echo $this->createUrl("incidencias/Searchfolio"); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        Search: {
                            string: request.term,
                        },
                    }
                }).complete( function( data ) {
                    Response={};
                    if(data.readyState==4){
                        if (typeof(data.responseText)!='undefined'){
                            Response=eval('('+data.responseText+')');
                        }
                        if (typeof(Response.requestresult)!='undefined'&&Response.requestresult == 'ok') {
                            response(Response.DataList.slice(0, 10));
                        }else{
                            
                        }
                    }
                });

            },
            minLength: 2,
            messages: {
                noResults: '',
                results: function() {}
            }
        });
 });
//Termina

        $('#ListIncidencias').dataTable( {
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
// se agrego para mostrar todos los registros en la parte del Show 2016-07-08

            "aLengthMenu": [
                [10,25, 50, 100,  -1],
                [10,25, 50, 100,  "Todo"]
            ],
//Termina 
        });

        $(".Select2").select2();
        $(".Date2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });
    });

</script>
<?php FB::info($GetData); ?>
<div style="height: 20px;"></div>
  <div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>REPORTE INCIDENCIAS DEL SERVICIO</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">INCIDENCIAS</a></li>
            <li class=""><a data-toggle="tab" href="#Create">AGREGAR NUEVA INCIDENCIA</a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            
            <div id="Lista" class="tab-pane fade active in">
            <form  method="POST" action="<?php echo $this->createUrl("incidencias/index/"); ?>" >
                <div class="span2">
                <label class="control-label" >Folio:</label>
                <div class="controls">
                    <input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width:100px;">
                </div>
                </div>

                <div class="span3">
                <label>Fecha de Ingreso:</label>
                <input type="text" id="INICIO"  name="INICIO"   placeholder="Inicio"    class="Date2"   value="<?=$_POST['INICIO']?>" style="width:100px;" />
                <input type="text" id="FIN"     name="FIN"      placeholder="Fin"       class="Date2"   value="<?=$_POST['FIN']?>" style="width:100px;" />
                </div>

                <div class="span3">
                <label>Producto:</label>
                <select class="Select2" id="Producto" name="Producto" style="width:100px;" >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaPlanes as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#Producto option[value='<?=$_POST['Producto']?>']").attr("selected",true);
                </script>
                </div>

                 <div class="span3">
                <label>FormaPago:</label>
                <select class="Select2" id="FormaPago" name="FormaPago" style="width:100px;">
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaFormasPago as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#FormaPago option[value='<?=$_POST['FormaPago']?>']").attr("selected",true);
                </script>
                </div>

                <div class="span3">
                <label>FrecuenciaPago:</label>
                <select class="Select2" id="FrecuenciaPago" name="FrecuenciaPago" style="width:100px;" >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaFrecuenciaPago as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#FrecuenciaPago option[value='<?=$_POST['FrecuenciaPago']?>']").attr("selected",true);
                </script>
                </div>
                <div class="span3">
                <label>Cobrador:</label>
                <select class="Select2" id="Cobrador" name="Cobrador" style="width:100px;" >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaCobradores as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#Cobrador option[value='<?=$_POST['Cobrador']?>']").attr("selected",true);
                </script>
                </div>
                <!--Se agrego para filtrar por Asesor Angeles Perez 15/08/2016-->
                <div class="span3">
                <label>Asesor:</label>
                <select class="Select2" id="Asesor" name="Asesor" style="width:100px;" >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaAsesores as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#Asesor option[value='<?=$_POST['Asesor']?>']").attr("selected",true);
                </script>
                </div>
                <!--Termina-->
                <div class="span2">
                <label class="control-label" >ResultadoIncidencias:</label>
                <div class="controls">
                    <select class="Select2" id="Cierre" name="Cierre" style="width:100px;" >
                        <option value="">SELECCIONE</option>
                        <option value="">TODAS</option>
                        <option value="SI">CERRADAS</option>
                        <option value="NO">NO CERRADAS</option>
                    </select>
                </div>
                </div>
             <div class="span2">
                <label>&nbsp;&nbsp;</label>
                <input type="submit" class="btn btn-success" name="BUSCAR" value="Buscar" >
            </div>
   
            </form>

            <?php include_once('modals/EditarIncidencias.modal.php'); ?>
            <table id="ListIncidencias" class="table table-hover table-condensed table-striped">

                <thead>
                    <tr>
                        <th><?php echo _('Folio Incidencia') ?></th>
                        <th><?php echo _('Fecha Incidencia') ?></th>
                        <th><?php echo _('Folio Socio') ?></th>
                        <th><?php echo _('Nombre Socio') ?></th>
                        <th><?php echo _('Estatus Socio') ?></th>
                        <th><?php echo _('Empresa') ?></th>
                        <th><?php echo _('Cobrador') ?></th>
                        <th><?php echo _('Asesor') ?></th>
                        <th><?php echo _('Producto') ?></th>
                        <th><?php echo _('Forma Pago') ?></th>
                        <th><?php echo _('Frecuencia Pago') ?></th>
                        <th><?php echo _('Asignación:') ?></th>
                        <th><?php echo _('Motivo Incidencia') ?></th>
                        <th><?php echo _('Descripción Incidencia') ?></th>
                        <th><?php echo _('Solución Incidencia') ?></th>
                        <th><?php echo _('Cierre Incidencia') ?></th>
                        <th><?php echo _('FechaCierre') ?></th>
                        <th><?php echo _('Status Incidencia') ?></th>
                        <th><?php echo _('Fecha Cancelación') ?></th>
                        <th><?php echo _('Usuario') ?></th>
                        <th><?php echo _('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($IncidenciasservicioData as $Incidenciasservicio){
                        $RowColor = "";
                        
                     switch ($Incidenciasservicio['Cierre']){
                        case 'SI':
                        $RowColor = "class= 'success2'";
                        $Incidenciasservicio['Cierre'] ='SI';
                        break; 

                    default:
                        break;

                    }

                    switch ($Incidenciasservicio['Status']){
                        case '0':
                        $RowColor = "class= 'danger'";
                        $Incidenciasservicio['Status'] ='Cancelado';
                        break; 

                    default:
                        break;

                    }
                ?>

                    <tr <?=$RowColor?> id='<?=$Incidenciasservicio['id']?>' >
                        <td><?=$Incidenciasservicio['id']?></td>
                        <td><?=$Incidenciasservicio['FechaIncidencia']?></td>
                        <td><?=$Incidenciasservicio['FolioSocio']?></td>
                        <td><?=$Incidenciasservicio['Nombre']?></td>
                        <td><?=$Incidenciasservicio['EstatusTitular']?></td>
                        <td><?=$Incidenciasservicio['Empresa']?></td>
                        <td><?=$Incidenciasservicio['Cobrador']?></td>
                        <td><?=$Incidenciasservicio['Asesor']?></td>
                        <td><?=$Incidenciasservicio['Producto']?></td>
                        <td><?=$Incidenciasservicio['FormaPago']?></td>
                        <td><?=$Incidenciasservicio['FrecuenciaPago']?></td>
                        <td><?=$Incidenciasservicio['Asignado']?></td>
                        <td><?=$Incidenciasservicio['motivoincidencia']?></td>
                        <td><?=$Incidenciasservicio['DescripcionIncidencia']?></td>
                        <td><?=$Incidenciasservicio['SolucionIncidencia']?></td>
                        <td ><?=$Incidenciasservicio['Cierre']?></td>
                        <td ><?=$Incidenciasservicio['FechaCierre']?></td>
                        <td><?php if($Incidenciasservicio['Status']==1) echo "Activo"; else echo "Cancelado"; ?></td>
                        <td ><?=$Incidenciasservicio['FechaCancelacion']?></td>
                        <td ><?=$Incidenciasservicio['Usuario']?></td>
                        <td><a onclick="EditarIncidencias('<?=$Incidenciasservicio['id']?>');" title="Editar Incidencias del Servicio" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Incidenciasservicio['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Cancelar este registro?')) { return; }else{return false;};", "title"=>"Cancelar Incidencia del Servicio")); ?>
                         </td>
                    </tr>
                    <?php } ?>
                </tbody>
                    </table>
        </div>


        

        <div id="Create" class="tab-pane fade active">
            <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("incidencias/create/"); ?>" >
                <!--Se agrego campo para ingresar y validar que un folio exista en Afiliaciones -->
                <div class="controls">
                    <input class="input-block-level SearchbyName" type="text" placeholder="INGRESE FOLIO PARA VALIDAR QUE EXISTA EN AFILIACIONES" name="folio" id="folio">
                </div>
                <!--Termina-->
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Folio Socio:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="folio_socio" name="folio_socio"/>
                        </div>
                    </div> 
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Incidencia:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="date" id="fecha_incidencia" name="fecha_incidencia" value="<?php echo date("Y-m-d");?>"/>
                        </div>
                    </div>
                </div>
                <div class="control-group row-fluid">
                <div class="span4">
                <label class="control-label" for="spin2">
                    <?php echo _('Asignado A:'); ?><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select name="asignado"   id="asignado" required>
                        <option value="">SELECCIONE</option>
                                <?php foreach ($ListaAsignacionPersonal as $rows) { ?>
                                <option value="<?=$rows['id']?>">
                                    <?=$rows['nombre']?>
                                </option>
                                <?php } ?>
                    </select>

                    <script type="text/javascript">
                        $("#asignado option[value='<?=$_POST['asignado']?>']").attr("selected",true);
                    </script>
                </div>
            </div>
             <div class="span2">
                        <label class="control-label"><?php echo _('Usuario:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="usuario" name="usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
                        </div>
                    </div>
                </div>
                   
                    
<!--Inicia para seleccionar n motivos de incidencia-->
    <div class="span12">
        <label class="control-label" for="MotivosIncidencias" >Seleccione Motivos de Incidencias del Servicio</label>
    </div>
        <div class="span8">
        <div class="controls">
                    <select  required multiple name="motivo_incidencia[]" id="MotivosIncidencias" >
                                <?php foreach ($ListaMotivosIncidenciasServicio as $rows) { ?>
                                <!--<option value="<?=$rows['id']."-".$rows['motivo']." "?>"><?=$rows['motivo']?></option>-->
                                <option  value="<?=$rows['motivo']?>"><?=$rows['motivo']?></option>
                                <?php } ?>
                    </select>
                    <script type="text/javascript" >
                        $("#motivo_incidencia option[value='<?=$_POST['motivo_incidencia']?>']").attr("selected",true);
                    </script>

                </div>
            </div>
    <div class="clearfix"></div>
    <script>
        $().ready(function(){
            $("#MotivosIncidencias").pickList();
        });
    </script>
    <div style="height: 50px"></div>

<!--Termina-->

<div class="control-group row-fluid">
            <div class="span2">
                        <label class="control-label"><?php echo _('Descripción Incidencia:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <textarea name="descripcion_incidencia" id="descripcion_incidencia" class="form-control" style="width:800px; height:100px;" required></textarea>
                        </div>
                    </div>
</div>
<div class="control-group row-fluid">
                <div class="span4">
                <label class="control-label" for="spin2">
                    <?php echo _('Cierre Incidencia:'); ?><i class="icon-photon info-circle"></i></a>
                </label>
                <div class="controls">
                    <select id="cierre" name="cierre" required>
                                <option value="">SELECCIONE</option>
                                <option value="SI">SI</option>
                                <option value="NO">NO</option>
                    </select>
                </div>
            </div>
                <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Cierre Incidencia:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="date" id="fecha_cierre" name="fecha_cierre" value="<?php echo date("Y-m-d");?>"/>
                        </div>
                    </div>
</div> 
<div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Solución Incidencia:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <textarea name="solucion_incidencia" id="solucion_incidencia" class="form-control" style="width:800px; height:100px;" required></textarea>
                        </div>
                    </div>
</div>
                   
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Status Activo'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input value="1" type="checkbox" id="status" name="status" checked="checked"/>
                                </div>
                            </div>
                        </div>
                <div class="control-group row-fluid">
                    <div class="span12">
                        <input type="submit" id="Save" class="btn btn-large btn-success" value="Agregar"  />
                        <div style="height: 20px;"></div>
                    </div>
                </div>

            </form>

        </div>

    </div> <!-- End Tabs -->
</div>

</div>

