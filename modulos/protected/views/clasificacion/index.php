<script type="text/javascript">
    $(document).on('ready',function() {
        $('#List').dataTable( {
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
            <div class="form-legend"><h3>Clasificacion de Empresas</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Clasificacion</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>
            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarClasificacion.modal.php'); ?>
                    <table id="List" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('Clasificacion') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ListData as $dato){ ?>
                            <tr id="<?=$dato['id']?>">
                                <td><?=$dato['descripcion']?></td>
                                <td>
                                    <a onclick="EditarClasificacion('<?=$dato['id']?>');" title="Editar Clasificacion" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$dato['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta Clasificacion?')) { return; }else{return false;};", "title"=>"Eliminar clasificacion")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("clasificacion/create"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Clasificacion'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="descripcion" name="descripcion" required="required" />
                                </div>
                            </div>
                        </div>

                        <div id="Savebtn" class="control-group row-fluid">
                            <div class="span3">
                                <input type="submit" id="Save" class="btn btn-small btn-success" value="Agregar" style="margin-bottom: 0px;" />
                            </div>
                        </div>
                    </form>
                </div>
            </div> <!-- End Tabs -->
        </div>
    </div>
</div>

