<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListCoordinadores').dataTable( {
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
            <div class="form-legend"><h3>Coordinadores</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Coordinadores</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>

            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                <?php include_once('modals/EditarCoordinador.modal.php'); ?>
                <table id="ListCoordinadores" class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th><?php echo _('Coordinador ID') ?></th>
                            <th><?php echo _('Coordinador') ?></th>
                            <th><?php echo _('Status') ?></th>
                            <th><?php echo _('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coordinadoresData as $coordinador){ ?>
                        <tr id="<?=$coordinador['id']?>">
                            <td><?=$coordinador['id']?></td>
                            <td><?=$coordinador['coordinador']?></td>
                            <td>
                                <?php if($coordinador['activo']==1){
                                    echo "Activo";
                                }else{
                                    echo "Inactivo";
                                }
                                ?>
                            </td>
                            <td>
                                <a onclick="EditarCoordinador('<?=$coordinador['id']?>');" title="Editar Coordinador" ><i class="icon-edit"></i></a>
                                <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$coordinador['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este coordinador?')) { return; }else{return false;};", "title"=>"Desactivar coordinador")); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="Create" class="tab-pane fade active">
                <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("coordinadores/create"); ?>" >
                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Coordinador'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="coordinador" name="coordinador" />
                            </div>
                        </div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Status'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="checkbox" id="activo" name="activo" value="1"/>
                            </div>
                        </div>
                    </div>
                    <div id="Savebtn" class="control-group row-fluid">
                        <div class="span3">
                            <input type="submit" id="Save" name="Save" class="btn btn-small btn-success" value="Agregar" style="margin-bottom: 0px;" />
                        </div>
                    </div>
                </form>
            </div>
        </div> <!-- End Tabs -->
    </div>
</div>

