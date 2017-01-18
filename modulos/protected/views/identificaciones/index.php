<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListIdentificaciones').dataTable( {
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
            }
        });
    });

</script>

<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
    <div class="container-fluid">
        <div class="control-group row-fluid">
            <div class="form-legend"><h3>Identificaciones</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Identificaciones</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>
            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarIdentificaciones.modal.php'); ?>
                    <table id="ListIdentificaciones" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('ID') ?></th>
                                <th><?php echo _('Identificacion') ?></th>
                                <th><?php echo _('Sucursal') ?></th>
                                <th><?php echo _('Status') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($IdentificacionesData as $Identificaciones){ ?>
                            <tr id='<?=$Identificaciones['id']?>' >
                                <td><?=$Identificaciones['id']?></td>
                                <td><?=$Identificaciones['nombre']?></td>
                                <td><?=$Identificaciones['sucursal']?></td>
                                <td><?php if($Identificaciones['status']==1) echo "Activo"; else echo "Inactivo"; ?></td>
                                <td>
                                    <a onclick="EditarIdentificaciones('<?=$Identificaciones['id']?>');" title="Editar Identificaciones" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Identificaciones['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};", "title"=>"Eliminar Parentesco")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("identificaciones/create/"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Identificacion:'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="nombre" name="nombre" />
                                </div>
                            </div>
                            <div class="span2">
                                <label class="control-label"><?php echo _('Sucursal'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <select id="sucursal" name="sucursal" >
                                        <option>Seleccione una opcion</option>
                                        <option value="MTY">Monterrey</option>
                                        <option value="CHIH">Chihuahua</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Status'); ?></label>
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
</div>
