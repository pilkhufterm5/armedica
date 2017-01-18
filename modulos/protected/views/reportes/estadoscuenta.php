<script type="text/javascript">

    $(document).on('ready', function() {

        $('#RelacionFacturas').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Reporte de Vigencia Maestros Seccion 50 - <?=date('Y-m-d') ?> ",
                        }, ]
                }]
            },
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "All"]
            ],
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });

        $(".Select2").select2();
        $(".Date2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });


    });

</script>

<div class="container-fluid">
    <form target="_blank" method="POST" action="<?php echo $this->createUrl("reportes/estadoscuenta"); ?>">

    <div style="height:50px;"></div>
        <div class="row">

            <div class="span3">
                <label>Filtrar Socios por Estatus:</label>
                <select class="Select2" id="STATUS" name="STATUS" >
                    <option value="Activo">Activos</option>
                    <option value="Cancelado">Cancelados</option>
                    <option value="Suspendido">Suspendidos</option>
                    <!-- <option value="">Todos</option>-->
                </select>
                <script type="text/javascript">
                    $("#STATUS option[value='<?=$_POST['STATUS']?>']").attr("selected",true);
                </script>
            </div>

            <div class="span2">
                <label>Fecha Inicio:</label>
                <input type="text" id="FINICIO" name="FINICIO" placeholder="Fecha Inicio" class="Date2" value="<?=$_POST['FINICIO']?>" style="max-width:80px;" />
            </div>

            <div class="span2">
                <label>Fecha Fin:</label>
                <input type="text" id="FFIN" name="FFIN" placeholder="Fecha Final" class="Date2" value="<?=$_POST['FFIN']?>" style="max-width:80px;" />
            </div>

            <div class="span1">
                <label>Folio:</label>
                <input type="text" id="FOLIO" name="FOLIO" placeholder="Folio" value="<?=$_POST['FOLIO']?>" style="max-width:80px;" />
            </div>

            <div class="span2">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" name="IMPRIMIR" value="Imprimir Estados de Cuenta" >
            </div>
            <div class="span1">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-danger" name="SendMail" value="Enviar por Correo" >
            </div>
        </div>
    </form>


</div>






