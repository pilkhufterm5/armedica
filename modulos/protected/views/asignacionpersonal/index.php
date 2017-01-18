<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListAsignacionPersonal').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            },
// se agrego para mostrar todos los registros en la parte del Show 2016-07-26

            "aLengthMenu": [
                [10,25, 50, 100,  -1],
                [10,25, 50, 100,  "Todo"]
            ],
//Termina 
        });
    });

</script>

<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
    <div class="container-fluid">
        <div class="control-group row-fluid">
            <div class="form-legend"><h3>ASIGNACIÓN DE PERSONAL A INCIDENCIAS</h3></div>
            <ul class="nav nav-tabs" id="myTab">
                <li class="active"><a data-toggle="tab" href="#Lista">PERSONAL</a></li>
                <li class=""><a data-toggle="tab" href="#Create">AGREGAR</a></li>
            </ul>

            <div class="tab-content"> <!-- Start TABS -->
                <div id="Lista" class="tab-pane fade active in">
                    <?php include_once('modals/EditarAsignacionPersonal.modal.php'); ?>
                    <table id="ListAsignacionPersonal" class="table table-hover table-condensed table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _('ID') ?></th>
                                <th><?php echo _('Nombre') ?></th>
                                <th><?php echo _('Sucursal') ?></th>
                                <th><?php echo _('Status') ?></th>
                                <th><?php echo _('Acciones') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($AsignacionPersonalData as $AsignacionPersonal){ ?>
                            <tr id='<?=$AsignacionPersonal['id']?>' >
                                <td><?=$AsignacionPersonal['id']?></td>
                                <td><?=$AsignacionPersonal['nombre']?></td>
                                <td><?=$AsignacionPersonal['sucursal']?></td>
                                <td><?php if($AsignacionPersonal['status']==1) echo "Activo"; else echo "Inactivo"; ?></td>
                                <td>
                                    <a onclick="EditarAsignacionPersonal('<?=$AsignacionPersonal['id']?>');" title="Editar Personal para asignacion de incidencias" ><i class="icon-edit"></i></a>
                                    <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$AsignacionPersonal['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};", "title"=>"Desactivar personal para asignacion de inicdencias")); ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div id="Create" class="tab-pane fade active">
                    <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("asignacionpersonal/create/"); ?>" >
                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Nombre:'); ?></label>
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
                                    <option>SELECCIONE</option>
                                    <option value="MTY">MONTERREY</option>
                                    <option value="CHH">CHIHUAHUA</option>
                                    <option value="TAM">TAMPICO</option>
                                    <option value="AGS">AGUASCALIENTES</option>
                                    <option value="QRO">QUERETARO</option>
                                    <option value="TRN">TORREÓN</option>
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="control-group row-fluid">
                            <div class="span2">
                                <label class="control-label"><?php echo _('Status Activo'); ?></label>
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
