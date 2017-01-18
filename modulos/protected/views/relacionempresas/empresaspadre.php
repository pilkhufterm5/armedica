<script type="text/javascript">
    $(document).on('ready', function() {
        $('#ListaEmpresasTable').dataTable({
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

        $('.select2').select2();
              
    });


</script>
<script type="text/javascript">

    function GuardarAccion(id_empresa) {

       //alert('id_sucursal'+id_sucursal);
       //alert('id_empresa'+id_empresa);
     
    
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("relacionempresas/CrearEmpresaPadre/"); ?>",
            type: "POST",
            dataType: "json",
            timeout: (120 * 1000),
            data: {
                id: id_empresa
            },
            success: function (Response, newValue) {
                if (Response.requestresult == 'ok') {
                     //displayNotify('success', Response.message);
                     // Si el resultado es correcto, eliminamos la sucursal
                     //$(fila).remove();
                     location.reload(); 

                } else {
                     displayNotify('alert', Response.message);
                }
            },
            error: ajaxError
        });


    }


</script>

<div style="height: 20px;"></div>
<div class="container-fluid">
    <div class="form-legend"><h3>Empresas Padre</h3></div>
    <!--Tabs begin-->
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a data-toggle="tab" href="#ListDTable">Lista de Empresas</a></li>
        <li class=""><a data-toggle="tab" href="#Create">Crear empresa padre</a></li>
    </ul>

    <div class="tab-content"> <!-- Start TABS -->
        <div id="ListDTable" class="tab-pane fade active in">
            <table id="ListaEmpresasTable" class="table table-hover table-condensed table-striped"  >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Folio</th>
                        <th>Nombre</th>
                        <th>TipoPersona</th>
                        <th>RFC</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($EmpresasPadre as $rowEmpresasPadre){ 
                    if($rowEmpresasPadre['tipopersona']=='FISICA')
                    {
                        $NombreEmpresa  = $rowEmpresasPadre['name'].' '. $rowEmpresasPadre['apellidos'];
                    }
                    else
                    {
                        $NombreEmpresa  = $rowEmpresasPadre['name'];
                    }
                    ?>
                    <tr id="<?=$rowEmpresasPadre['id']?>" >
                        <td><?=$rowEmpresasPadre['id'] ?></td>
                        <td><?=$rowEmpresasPadre['folio'] ?></td>
                        <td><?=$NombreEmpresa?></td>
                        <td><?=$rowEmpresasPadre['tipopersona'] ?></td>
                        <td><?=$rowEmpresasPadre['taxref'] ?></td>
                        <td style="width: 50px;">
                            <a href="<?php echo $this->createUrl("relacionempresas/sucursales/",array("empresapadre"=>$rowEmpresasPadre['id'])) ?>" title="Ver Sucursales" ><i class="icon-edit"></i></a>

                            <?php echo CHtml::link("<i class=\"icon-trash\"></i>",array("EliminarEmpresaPadre","id_empresa"=>$rowEmpresasPadre['id']),array('onclick'=>"javascript:if(confirm('¿Esta seguro de Eliminar esta Empresa?')) { return; }else{return false;};", "title"=>"Eliminar Combinación")); ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="Create" class="tab-pane fade active">
            <form method="post" action="<?php echo $this->createUrl("relacionempresas/CrearEmpresaPadre/"); ?>">
           
            <select name="id" id="id" class="form-control select2">
                <option>-- Seleccione empresa --</option> 
                <?php foreach($EmpresasPadreDisponibles as $rowEmpresasPadreDisponibles){ 
                        if($rowEmpresasPadreDisponibles['tipopersona']=='FISICA')
                    {
                        $NombreEmpresa  = $rowEmpresasPadreDisponibles['name'].' '. $rowEmpresasPadreDisponibles['apellidos'];
                    }
                    else
                    {
                        $NombreEmpresa  = $rowEmpresasPadreDisponibles['name'];
                    } ?>
                    <option value="<?=$rowEmpresasPadreDisponibles['id']?>">
                        <?=$rowEmpresasPadreDisponibles['folio'].' - '.$NombreEmpresa.' - '.$rowEmpresasPadreDisponibles['taxref'] ?>
                    </option>
                    <?php } ?>

            </select><br>
             
            <input type="submit" value="Generar" class="btn btn-default">
            </form>
        </div>
    </div><!-- END TAB CONTENT -->
</div>
