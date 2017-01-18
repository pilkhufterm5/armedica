<script type="text/javascript">
    $(document).on('ready', function() {
        $('#ListaSucuraslesTable').dataTable({
            "sPaginationType" : "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", "pdf" ]
                }]
            },
            "aLengthMenu" : [[10, 25, 50, 100, 200, -1], [10, 25, 50, 100, 200, "All"]],
            "fnInitComplete" : function() {
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass : 'noSearch'
                });
            }
        });
        
        var availableTags = [
                    <?php foreach($ListaSucursalesDisponibles as $rowListaSucursalesDisponibles){ 
                        if($rowListaSucursalesDisponibles['tipopersona']=='FISICA')
                    {
                        $NombreEmpresa  = $rowListaSucursalesDisponibles['name'].' '. $rowListaSucursalesDisponibles['apellidos'];
                    }
                    else
                    {
                        $NombreEmpresa  = $rowListaSucursalesDisponibles['name'];
                    } ?>
                        {
                            value: <?=$rowListaSucursalesDisponibles['id']?>,
                            label: "<?=$rowListaSucursalesDisponibles['folio'].' - '.$NombreEmpresa.' - '.$rowListaSucursalesDisponibles['taxref'] ?>"
                        },
                     <?php } ?>
                ];

               
        $('#tags').autocomplete({
            source: function(request, response) {
                var results = $.ui.autocomplete.filter(availableTags, request.term);

                response(results.slice(0, 10));
            },
             select: function(event, ui) {
                $('#tags').val(ui.item.label);
                $('#id').val(ui.item.value);
                return false; // Prevent the widget from inserting the value.
            },
            focus: function(event, ui) {
                $("#tags").val(ui.item.label);
                return false; // Prevent the widget from inserting the value.
            }
        });
    });



</script>

<div style="height: 20px;"></div>

<div class="container-fluid">
 <a href="<?=Yii::app()->createUrl('relacionempresas/empresaspadre')?>" >
    <input type="button" class="btn btn-small" value="Ir a Empresas " />
 </a>
    <div class="form-legend">

    <p>
        Empresa: <strong><?php if($DatosEmpresaPadre[0]['tipopersona']=='FISICA')
                    {
                       echo $EmpresaPadre = $DatosEmpresaPadre[0]['name'].' '. $DatosEmpresaPadre[0]['apellidos'];
                    }
                    else
                    {
                       echo $EmpresaPadre =  $DatosEmpresaPadre[0]['name'];
                    } ?></strong><br>
        RFC: <strong><?php echo  $DatosEmpresaPadre[0]['taxref']; ?> </strong><br>
        Folio: <strong><?php echo  $DatosEmpresaPadre[0]['folio']; ?> </strong>
    </p>
    <h3>Sucursales de <?php echo $EmpresaPadre?></h3>
    </div>
    <!--Tabs begin-->
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#ListDTable">Lista de Sucursales</a></li>
        <li class=""><a data-toggle="tab" href="#Create">Agregar sucursal</a></li>
    </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="ListDTable" class="tab-pane fade active in">
            <table id="ListaSucuraslesTable" class="table table-hover table-condensed table-striped"  >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Folio</th>
                        <th>Nombre</th>
                        <th>TipoPersona</th>
                        <th>RFC</th>
                        <th>Empresa padre </th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($ListaSucursalesActuales as $rowListaSucursalesActuales){ 
                    if($rowListaSucursalesActuales['tipopersona']=='FISICA')
                    {
                        $NombreEmpresa  = $rowListaSucursalesActuales['name'].' '. $rowListaSucursalesActuales['apellidos'];
                    }
                    else
                    {
                        $NombreEmpresa  = $rowListaSucursalesActuales['name'];
                    }
                    ?>
                    <tr id="<?=$rowListaSucursalesActuales['id']?>" >
                        <td><?=$rowListaSucursalesActuales['id'] ?></td>
                        <td><?=$rowListaSucursalesActuales['folio'] ?></td>
                        <td><?=$NombreEmpresa?></td>
                        <td><?=$rowListaSucursalesActuales['tipopersona'] ?></td>
                        <td><?=$rowListaSucursalesActuales['taxref'] ?></td>
                        <td><?=$EmpresaPadre ?></td>
                        <td style="width: 50px;">

                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("EliminarSucursal","id_empresa"=>$rowListaSucursalesActuales['id_empresapadre'],"id_sucursal"=>$rowListaSucursalesActuales['id_sucursal']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta Empresa?')) { return; }else{return false;};", "title"=>"Eliminar Combinación")); ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">
            <form method="post" action="<?php echo $this->createUrl("relacionempresas/CrearSucursal/"); ?>">
            <div class="ui-widget">
              <label for="tags">Seleccione sucursal:  </label>
              <input id="tags" class="form-control" type="text">
              <input id="id" name="id_sucursal" class="form-control" type="hidden">
            </div>
            <input type="submit" value="Generar" class="btn btn-default">
            <input type="hidden" value="<?php echo $DatosEmpresaPadre[0]['id'] ?>" name="id_empresa">
            <input type="hidden" value="<?php echo $DatosEmpresaPadre[0]['folio'] ?>" name="folioempresapadre">
            </form>
        </div>
    </div><!-- END TAB CONTENT -->
</div>
