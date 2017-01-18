<script type="text/javascript">
    $(document).on('ready',function() {
        
        /*$('#fecha_registro').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });*/

          $('#fecha_alerta').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#Ffecha_registro').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#Ffecha_alerta').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#ListSeguimiento').dataTable( {
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
    });
</script>
<?php FB::info($SeguimientoData); ?>
<div style="height: 20px;"></div>
  <div class="container-fluid">
    <a href="<?php echo $this->createUrl("afiliaciones/afiliacion"); ?>"><input type="button" value="ir a Afiliaciones" class="btn btn-info"></a>
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>BITACORA SEGUIMIENTO SOCIO</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">BITACORA</a></li>
            <li class=""><a data-toggle="tab" href="#Create">AGREGAR SEGUIMIENTO AL SOCIO </a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <form  method="POST" action="<?php echo $this->createUrl("seguimiento/index/"); ?>" >
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label" >Folio:</label>
                        <div class="controls">
                            <input type="text" id="folio" name="folio" value="<?=$_POST['folio']?>" style="width:100px;">
                        </div>
                    </div>
                     <div class="span2">
                        <label class="control-label" >Usuario:</label>
                        <div class="controls">
                            <input type="text" id="usuario" name="usuario" value="<?=$_POST['usuario']?>" style="width:100px;">
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label" >Fecha Registro:</label>
                        <div class="controls">
                            <input type="text" id="Ffecha_registro" name="Ffecha_registro" value="<?=$_POST['Ffecha_registro']?>" style="width:100px;">
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label" >Fecha Alerta:</label>
                        <div class="controls">
                            <input type="text" id="Ffecha_alerta" name="Ffecha_alerta" value="<?=$_POST['Ffecha_alerta']?>" style="width:100px;">
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"  >&nbsp;</label>
                        <div class="controls">
                            <input type="submit" class="btn btn-success" name="BUSCAR" value="Buscar" style="height: 35px;" >
                        </div>
                    </div>
                </div>
                </form>
            <?php include_once('modals/EditarSeguimiento.modal.php'); ?>
            <table id="ListSeguimiento" class="table table-hover table-condensed table-striped">
                <thead>
                    <tr>
                        <th><?php echo _('ID') ?></th>
                        <th><?php echo _('Folio') ?></th>
                        <th><?php echo _('Fecha Registro') ?></th>
                        <th><?php echo _('Fecha Alerta') ?></th>
                        <th><?php echo _('Descripcion') ?></th>
                        <th><?php echo _('Usuario') ?></th>
                        <th><?php echo _('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($SeguimientoData as $Seguimiento){
                        $RowColor = "";
                        $InvoiceBadge = "";

                        switch ($Seguimiento['fecha_alerta']) {
                     case date('Y-m-d'):
                        $RowColor = "class= 'danger'";
                        $Seguimiento['fecha_alerta'] = date('Y-m-d');
                        break; 

                    default:
                        break;
                }

                     ?>
                    <tr <?=$RowColor?> id='<?=$Seguimiento['id']?>' >
                        <td><?=$Seguimiento['id']?></td>
                        <td><?=$Seguimiento['folio']?></td>
                        <td><?=$Seguimiento['fecha_registro']?></td>
                        <td><?=$Seguimiento['fecha_alerta']?></td>
                        <td><?=$Seguimiento['descripcion']?></td>
                        <td><?=$Seguimiento['usuario']?></td>
                        <td>
                            <a onclick="EditarSeguimiento('<?=$Seguimiento['id']?>');" title="Editar Seguimiento" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Seguimiento['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de eliminar este registro?')) { return; }else{return false;};", "title"=>"Eliminar Seguimiento")); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">

            <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("seguimiento/create/"); ?>" >

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Folio:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="folio" name="folio"/>
                        </div>
                    </div> 
                </div>
                    <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Registro:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="datetime" id="fecha_registro" name="fecha_registro" value="<?php echo date("Y-m-d H:i:s");?>" readonly="readonly"/>
                            <!--<input type="text" id="fecha_registro" name="fecha_registro"/>-->
                        </div>
                    </div>

                    <div class="span2">
                        <label class="control-label"><?php echo _('Fecha Alerta'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                           <input type="text" id="fecha_alerta" name="fecha_alerta" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Descripcion:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <textarea name="descripcion" id="descripcion" class="form-control" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Usuario'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="usuario" name="usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
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

