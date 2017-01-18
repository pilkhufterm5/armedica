<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListParentesco').dataTable( {
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
            <div class="form-legend"><h3>Parentesco</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Parentesco</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>
            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarParentesco.modal.php'); ?>
                    <table id="ListParentesco" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('ID') ?></th>
                                <th><?php echo _('Parentesco') ?></th>
                                <th><?php echo _('Sucursal') ?></th>
                                <th><?php echo _('Status') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ParentescoData as $Parentesco){ ?>
                            <tr id='<?=$Parentesco['id']?>' >
                                <td><?=$Parentesco['id']?></td>
                                <td><?=$Parentesco['nombre']?></td>
                                <td><?=$Parentesco['sucursal']?></td>
                                <td><?php if($Parentesco['status']==1) echo "Activo"; else echo "Inactivo"; ?></td>
                                <td>
                                    <a onclick="EditarParentesco('<?=$Parentesco['id']?>');" title="Editar Parentesco" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Parentesco['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};", "title"=>"Eliminar Parentesco")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("parentesco/create/"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Parentesco'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="parentesco" name="parentesco" />
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
