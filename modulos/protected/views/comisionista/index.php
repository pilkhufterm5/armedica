<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListComisionistas').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "Todos"]
            ],
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
    $('#coordina_id').select2();
    });
</script>

<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">

    <div class="container-fluid">
        <div class="control-group row-fluid">
            <div class="form-legend"><h3>Comisionistas</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Comisionistas</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>

            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarComisionista.modal.php'); ?>
                    <table id="ListComisionistas" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('Comisionista') ?></th>
                                <th><?php echo _('Coordinador') ?></th>
                                <th><?php echo _('Estado') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($ComisionistaData as $Comisionista){ ?>
                            <tr id="<?=$Comisionista['id']?>" >
                                <td><?=$Comisionista['comisionista']?></td>
                                <td><?=$Comisionista['coordinador']?></td>
                                <td><?php if($Comisionista['activo']==1) echo "Activo"; else echo "Inactivo"; ?></td>
                                <td>
                                    <a onclick="EditarComisionista('<?=$Comisionista['id']?>');" title="Editar Comisionista" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Comisionista['id']),
                                    array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este comisionista?'))
                                    { return; }else{return false;};", "title"=>"Desactivar Comisionista")); ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("comisionista/create"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Comisionista'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="comisionista" name="comisionista" required="required" />
                                </div>
                            </div>
                            <div class="span2">
                                <label class="control-label"><?php echo _('Coordinador'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <select id="coordina_id" name="coordina_id" required="required" >
                                        <?php
                                            echo "<option></option>";
                                            foreach ($ListaCorrdinadores as $id => $Value) {
                                                echo "<option value='".$id."'>".$Value."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Estado'); ?></label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="checkbox" id="activo" name="activo" value="1" checked="checked"/>
                                </div>
                            </div>
                        </div>
                        <div id="Savebtn" class="control-group row-fluid">
                            <div class="span3">
                                <input type="submit" id="Save" name="Save" class="btn btn-small btn-success" value="Agregar" style="margin-bottom: 0px;" />
                            </div>
                        </div>
                    </form>
                </div><!--End CreateTab -->
            </div><!--End Tabs -->
        </div>
    </div>
</div>









