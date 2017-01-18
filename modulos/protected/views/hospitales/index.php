<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListHospitales').dataTable( {
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
<?php FB::info($HospitalesData); ?>
<div style="height: 20px;"></div>

  <div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>Hospitales</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Hospitales</a></li>
            <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
            <?php include_once('modals/EditarHospitales.modal.php'); ?>
            <table id="ListHospitales" class="table table-hover table-condensed table-striped">
                <thead>
                    <tr>
                        <th><?php echo _('ID') ?></th>
                        <th><?php echo _('Nombre') ?></th>
                        <th><?php echo _('Calle') ?></th>
                        <th><?php echo _('Numero') ?></th>
                        <th><?php echo _('Colonia') ?></th>
                        <th><?php echo _('Teléfono') ?></th>
                        <th><?php echo _('Entre Calles') ?></th>
                        <th><?php echo _('Municipio') ?></th>
                        <th><?php echo _('Cuadrante 1') ?></th>
                        <th><?php echo _('Cuadrante 2') ?></th>
                        <th><?php echo _('Cuadrante 3') ?></th>
                        <th><?php echo _('Sucursal') ?></th>
                        <th><?php echo _('Status') ?></th>
                        <th><?php echo _('Acciones') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($HospitalesData as $Hospitales){ ?>
                    <tr id='<?=$Hospitales['id']?>' >
                        <td><?=$Hospitales['id']?></td>
                        <td><?=$Hospitales['nombre']?></td>
                        <td><?=$Hospitales['calle']?></td>
                        <td><?=$Hospitales['numero']?></td>
                        <td><?=$Hospitales['colonia']?></td>
                        <td><?=$Hospitales['telefono']?></td>
                        <td><?=$Hospitales['entrecalles']?></td>
                        <td><?=$Hospitales['municipio']?></td>
                        <td><?=$Hospitales['cuadrante1']?></td>
                        <td><?=$Hospitales['cuadrante2']?></td>
                        <td><?=$Hospitales['cuadrante3']?></td>
                        <td><?=$Hospitales['sucursal']?></td>
                        <td><?php if($Hospitales['status']==1) echo "Activo"; else echo "Inactivo"; ?></td>
                        <td>
                            <a onclick="EditarHospitales('<?=$Hospitales['id']?>');" title="Editar Hospitales" ><i class="icon-edit"></i></a>
                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("disable","id"=>$Hospitales['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de desactivar este registro?')) { return; }else{return false;};", "title"=>"Eliminar Hospital")); ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">

            <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("hospitales/create/"); ?>" >

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
                        <label class="control-label"><?php echo _('Calle'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="calle" name="calle" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Número:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="numero" name="numero" />
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Colonia'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="colonia" name="colonia" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Teléfono:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="telefono" name="telefono" />
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Entre calles'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="entrecalles" name="entrecalles" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Municipio:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <select id="municipio" name="municipio" >
                                <option value="0">Seleccione una opcion</option>

                                <?php foreach ($MunicipiosData as $Municipio){ ?>

                                <option value="<?=$Municipio['id']?>"><?=$Municipio['municipio']?></option>
                               <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 1'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="cuadrante1" name="cuadrante1" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 2:'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="cuadrante2" name="cuadrante2" />
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"><?php echo _('Cuadrante 3'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <input type="text" id="cuadrante3" name="cuadrante3" />
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label"><?php echo _('Sucursal'); ?></label>
                    </div>
                    <div class="span3">
                        <div class="controls">
                            <select id="sucursal" name="sucursal" >
                                <option value="MTY">Seleccione una opcion</option>
                                <option value="MTY">Monterrey</option>
                                <option value="CHIH">Chihuahua</option>
                                <option value="QRO">Queretaro</option>
                                <option value="MGA">Aguascalientes</option>
                                <option value="TAM">Tampico</option>
                                <option value="TRN">Torreon</option>
                            </select>
                        </div>
                    </div>

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

