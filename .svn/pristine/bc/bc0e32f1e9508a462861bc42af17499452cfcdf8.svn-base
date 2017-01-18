<script>
    $(document).on('ready', function(){
        var oTable1=CrearTabla($);
        //$('#status').select2();
        $('#fecha_baja_inicio').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        $('#fecha_baja_fin').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $('#fecha_cancelacion_inicio').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        $('#fecha_cancelacion_fin').datepicker({
            dateFormat : 'yy-mm-dd'
        });

        $("#status").change(function(){
            if($(this).val()=='Cancelado'){
                $("#fecha_cancelación").show();
            }
        });

        $("#Buscar").click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/ReporteCancelacion"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                     Search:{
                            fecha_cancelacion_inicio: $("#fecha_cancelacion_inicio").val(),
                            fecha_cancelacion_fin: $("#fecha_cancelacion_fin").val(),
                            status: $("#status").val(),
                      },
                },
                success : function(Response, newValue) {
                    $.unblockUI();
                     oTable1.fnDestroy();
                    if (Response.requestresult == 'ok') {
                        //displayNotify('success', Response.message);
                        $('#ReloadBody').html(Response.Tbody);
                         oTable1 =CrearTabla($);
                    }else{
                        displayNotify('error', Response.message);
                        $('#ReloadBody').html(Response.Tbody);
                         oTable1 =CrearTabla($);
                    }
                },
                error : ajaxError
            });
        });
    });

function CrearTabla($){
ret=
    $('#MovimientosData').dataTable({
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
return ret;
}
</script>

<?php
FB::INFO($MovimientosAfiliacion, 'Movimientos afiliacion');
?>
<h3>Movimientos de Afiliaciones</h3>

<form class="form-inline" role="form">
    <div class="form-group">


        <label for="fecha_baja" class="sr-only">Fecha de cancelación: </label>
        <input type="text" class="form-control" id="fecha_cancelacion_inicio" name="fecha_cancelacion_inicio" value="<?=date('Y-m-d')?>">
        <input type="text" class="form-control" id="fecha_cancelacion_fin" name="fecha_cancelacion_fin" value="<?=date('Y-m-d')?>">

        <label for="status" class="sr-only">Estatus: </label>
        <select class="form-control" id="status" name="status">
            <option value="">Seleccione una Opción</option>
            <option value="Activo">Activo</option>
            <option value="Cancelado">Cancelado</option>
            <option value="Suspendido">Suspendido</option>
        </select>

         <input type="button" class="btn btn-success" value="Buscar" id="Buscar">
    </div>

</form>


<table id="MovimientosData" class="table table-hover table-condensed table-striped">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Folio</th>
            <th>Num. de  Mov.</th>
            <th>Tipo de Mov.</th>
            <th>Fecha de baja</th>
            <th>Fecha de Cancelación</th>
            <th>Motivos</th>
            <th>Fecha inicial Sup</th>
            <th>Fecha Final sup</th>
            <th>Monto Recibido</th>
            <th>Tarifa total</th>
        </tr>
    </thead>
    <tbody id="ReloadBody">
        <?php foreach($MovimientosAfiliacion as $Movimientos){ ?>
            <tr>
                <td><?=$Movimientos['debtorno']?></td>
                <td><?=$Movimientos['folio']?></td>
                <td><?=$Movimientos['moveno']?></td>
                <td><?=$Movimientos['movetype']?></td>
                <td><?=$Movimientos['fecha_baja']?></td>
                <td><?=$Movimientos['fecha_cancelacion']?></td>
                <td><?=$Movimientos['motivos']?></td>
                <td><?=$Movimientos['sus_fechainicial']?></td>
                <td><?=$Movimientos['sus_fechafinal']?></td>
                <td><?=$Movimientos['monto_recibido']?></td>
                <td><?=$Movimientos['tarifa_total']?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
