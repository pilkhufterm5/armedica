<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListCobradores').dataTable( {
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

            <div class="form-legend"><h3>Cobradores</h3></div>

       <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Cobradores</a></li>
            <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
        </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="Lista" class="tab-pane fade active in">
            <?php include_once('modals/EditarCobrador.modal.php'); ?>
            <table id="ListCobradores" class="table table-hover table-condensed table-striped">
                <thead>
                    <tr>
                        <th><?php echo _('Nombre') ?></th>
                        <th><?php echo _('Comisión') ?></th>
                        <th><?php echo _('Zona') ?></th>
                        <th><?php echo _('Activo') ?></th>
                        <th><?php echo _('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($CobradoresData as $Cobrador){ ?>
                    <tr id='<?=$Cobrador['id']?>' >
                        <td><?=$Cobrador['nombre']?></td>
                        <td><?=$Cobrador['comision']?></td>
                        <td><?=$Cobrador['zona']?></td>
                        <td>
                            <?php if($Cobrador['activo']==1){
                                echo "Activo";
                            }else{
                                echo "Inactivo";
                            }?>
                        </td>
                        <td>
                            <a onclick="EditarCobrador('<?=$Cobrador['id']?>');" title="Editar Cobrador" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Cobrador['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este cobrador?')) { return; }else{return false;};", "title"=>"Desactivar Cobrador")); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">

                <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("cobradores/create/"); ?>" >

                <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label"><?php echo _('Nombre'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="text" id="nombre" name="nombre" />
                    </div>
                </div>

                <div class="span2">
                    <label class="control-label"><?php echo _('Comisión'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="text" id="comision" name="comision" />
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label"><?php echo _('Zona'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="text" id="zona" name="zona" />
                    </div>
                </div>

                <div class="span2">
                    <label class="control-label"><?php echo _('Activo'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="checkbox" id="activo" name="activo" checked="checked" value="1"/>
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label"><?php echo _('Reasigna'); ?></label>
                </div>

                <div class="span3">
                    <div class="controls">
                        <input type="text" id="reasigna" name="reasigna" />
                    </div>
                </div>

                <div class="span2">
                    <label class="control-label"><?php echo _('transfe'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="checkbox" id="transfe" name="transfe" value="1"/>
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label"><?php echo _('cobori'); ?></label>
                </div>

                <div class="span3">
                    <div class="controls">
                        <input type="text" id="cobori" name="cobori" />
                    </div>
                </div>

                <div class="span2">
                    <label class="control-label"><?php echo _('Empresa'); ?></label>
                </div>
                <div class="span3">
                    <div class="controls">
                        <input type="checkbox" id="empresa" name="empresa" value="1"/>
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


