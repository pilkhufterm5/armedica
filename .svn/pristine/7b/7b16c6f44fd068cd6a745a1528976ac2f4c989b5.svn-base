<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListConvenios').dataTable( {
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
            <div class="form-legend"><h3>Convenios</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Convenios</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>
            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarConvenios.modal.php'); ?>
                    <table id="ListConvenios" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('Convenio') ?></th>
                                <th><?php echo _('bcotrans') ?></th>
                                <th><?php echo _('Status') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ConveniosData as $Convenios){ ?>
                            <tr id="<?=$Convenios['id']?>">
                                <td><?=$Convenios['convenio']?></td>
                                <td><?=$Convenios['bcotrans']?></td>
                                <td>
                                    <?php if($Convenios['activo']==1){
                                        echo "Activo";
                                    }else{
                                        echo"Inactivo";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a onclick="EditarConvenios('<?=$Convenios['id']?>');" title="Editar Convenio" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Convenios['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este convenio?')) { return; }else{return false;};", "title"=>"Desactivar Convenios")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("convenios/create/"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Convenio'); ?></label>
                            </div>
                            <div class="span4">
                                <div class="controls">
                                    <input type="text" id="convenio" name="convenio" />
                                </div>
                            </div>

                            <div class="span2">
                                <label class="control-label"><?php echo _('bcotrans'); ?></label>
                            </div>
                            <div class="span4">
                                <div class="controls">
                                    <input type="text" id="bcotrans" name="bcotrans" />
                                </div>
                            </div>
                        </div>
                        <div class="control-group row-fluid">
                             <div class="span2">
                                <label class="control-label"><?php echo _('Activo'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="checkbox" id="activo" name="activo" value="1"/>
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


