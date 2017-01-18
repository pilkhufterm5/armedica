<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ListDeliveryAddress').dataTable( {
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
        $("#deladd7").select2();
        $("#deladd8").select2();
    });

    function Elimiar(){
        if (confirm("¿Esta seguro de Eliminar la Dirección?")){
            return;
        }else{
            return false;
        }
    }
</script>

<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">

<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>Direcciones de Envio</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Direcciones de Envio</a></li>
            <li class=""><a data-toggle="tab" href="#Create">Agregar</a></li>
        </ul>

        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <?php include_once('modals/EditAddress.modal.php'); ?>
                <table id="ListDeliveryAddress" class="table table-hover table-condensed table-striped" style="float: 0;">
                    <thead>
                        <tr>
                            <th><?php echo _('Code') ?></th>
                            <th><?php echo _('Name') ?></th>
                            <th style="width: 100px;" ><?php echo _('Acciones') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($GetPaginationData as $Data){ ?>
                        <tr id="<?=$Data['loccode']?>" >
                            <td><?=$Data['loccode']?></td>
                            <td><?=$Data['locationname']?></td>
                            <td>
                                <a onclick="EditAddress('<?=$Data['loccode']?>');" title="Editar Dirección" ><i class="icon-edit"></i></a>
                                <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("delete","id"=>$Data['loccode']),array('onclick' =>"javascript:if(confirm('¿Esta seguro de ELiminar esta Dirección?')) { return; }else{return false;};","title"=>"Eliminar Dirección")); ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="Create" class="tab-pane fade active">

                <form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("deliveryaddress/create/"); ?>" >

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Codigo'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="loccode" name="loccode" style="width: 100px;" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Nombre'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="locationname" name="locationname" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Calle'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd1" name="deladd1" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Num. Exterior'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd2" name="deladd2" style="width: 100px;"  />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Num. Interior'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd3" name="deladd3" style="width: 100px;" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Colonia'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd4" name="deladd4" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Localidad'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd5" name="deladd5" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Referencia'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd6" name="deladd6" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Municipio'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <select id="deladd7" name="deladd7" >
                                    <option value="">&nbsp;</option>
                                    <?php foreach($ListaMunicipios as $id => $Municipio){ ?>
                                        <option value="<?=$Municipio?>"><?=$Municipio?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Estado'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <select id="deladd8" name="deladd8" >
                                    <option value="">&nbsp;</option>
                                    <?php foreach($ListaEstados as $id => $Estado){ ?>
                                        <option value="<?=$Estado?>"><?=$Estado?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Pais'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd9" name="deladd9" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Cod. Postal'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="deladd10" name="deladd10" style="width: 100px;" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('Telefono'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="tel" name="tel" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Fax'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="fax" name="fax" style="width: 100px;" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span2">
                            <label class="control-label"><?php echo _('E-Mail'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="email" name="email" />
                            </div>
                        </div>

                        <div class="span2">
                            <label class="control-label"><?php echo _('Contacto'); ?></label>
                        </div>
                        <div class="span3">
                            <div class="controls">
                                <input type="text" id="contact" name="contact" />
                            </div>
                        </div>
                        <div class="span2"></div>
                    </div>

                    <div class="control-group row-fluid">
                        <div class="span12">
                            <input type="submit" id="Save" class="btn btn-large btn-success" value="Agregar"  />
                            <div style="height: 20px;"></div>
                        </div>
                    </div>

                </form>
            </div>

        </div><!-- End Tabs -->

    </div>
</div>
</div>

