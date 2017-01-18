<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListPaymentmethod').dataTable( {
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

        <div class="form-legend"><h3>Empresas</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Empresas</a></li>
            <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
        </ul>

        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <?php include_once('modals/EditarEmpresa.modal.php'); ?>
                <table id="ListPaymentmethod" class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th><?php echo _('Empresa') ?></th>
                            <th><?php echo _('Folio') ?></th>
                            <th><?php echo _('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($EmpresasData as $Empresa){ ?>
                        <tr id="<?=$Empresa['id']?>">
                            <td><?=$Empresa['empresa']?></td>
                            <td><?=$Empresa['folio']?></td>
                            <td>
                                <a onclick="EditarEmpresa('<?=$Empresa['id']?>');" title="Editar Empresa" ><i class="icon-edit"></i></a>
                                <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Empresa['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta empresa?')) { return; }else{return false;};", "title"=>"Eliminar empresa")); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="Create" class="tab-pane fade active">
                <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("empresas/create"); ?>" >
                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Empresa'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="empresa" name="empresa"/>
                            </div>
                        </div>
                    </div>
                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Folio'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="folio" name="folio"/>
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





