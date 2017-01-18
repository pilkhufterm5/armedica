<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListFrecuencias').dataTable( {
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
            <div class="form-legend"><h3>Frecuencias de pago</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Frecuencias de pago</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>
            <div class="tab-content">
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarFrecuencia.modal.php'); ?>
                    <table id="ListFrecuencias" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('Frecuencia de Pago') ?></th>
                                <th><?php echo _('Dias') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($FrecuenciaPagoData as $Frecuencia){ ?>
                            <tr id="<?=$Frecuencia['id']?>">
                                <td><?=$Frecuencia['frecuencia']?></td>
                                <td><?=$Frecuencia['dias']?></td>
                                <td>
                                    <a onclick="EditarFrecuencia('<?=$Frecuencia['id']?>');" title="Editar Frecuencia" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Frecuencia['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar este frecuencia?')) { return; }else{return false;};", "title"=>"Eliminar Frecuencia de Pago")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("frecuenciapago/create/"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Frecuencia de Pago'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="frecuencia" name="frecuencia" />
                                </div>
                            </div>

                            <div class="span2">
                                <label class="control-label"><?php echo _('Dias'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="dias" name="dias" />
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

