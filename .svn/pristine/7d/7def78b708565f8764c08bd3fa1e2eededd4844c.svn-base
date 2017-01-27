<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListTipos').dataTable( {
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


    <div class="container-fluid">
        <div class="control-group row-fluid">

            <div class="form-legend"><h3>Tipo de Facturas</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">Tipos</a></li>
                <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
            </ul>

            <div class="tab-content"> <!-- Start TABS -->

                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarTipos.modal.php'); ?>
                    <table id="ListTipos" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Estatus</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($GetData as $Data){
                                $Status = "Activo";
                                if($Data['activo'] == 0){
                                    $Status = "Inactivo";
                                }
                            ?>
                            <tr id="<?=$Data['id']?>">
                                <td><?=$Data['tipo']?></td>
                                <td><?=$Status?></td>
                                <td>
                                    <a onclick="EditarTipo('<?=$Data['id']?>');" title="Editar Tipo de Factura" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Data['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar este Tipo de Factura?')) { return; }else{return false;};", "title"=>"Eliminar Tipo de Factura")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("tipofacturas/create"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label">Tipo</label>
                            </div>
                            <div class="span3">
                                <div class="controls">
                                    <input type="text" id="tipo" name="tipo"/>
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





