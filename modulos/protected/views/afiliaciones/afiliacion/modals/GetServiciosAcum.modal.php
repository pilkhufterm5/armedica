<script type="text/javascript">
    $(document).on('ready',function() {
        $('#GSFecha_inicio').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $("#GSFecha_fin").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $("#Search-Modal-GetServiciosAcum").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/GetServiciosAcumulados"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    GetServAcum:{
                        Folio: $("#GSFolio").val(),
                        StartDate: $("#GSFecha_inicio").val(),
                        EndDate: $("#GSFecha_fin").val(),
                        Todos: $('#GSTodos').attr("checked") ? 1 : 0,
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        $('#ServiciosAcumTableContent').html(Response.TableContent);

                        oTable2 = $('#Table_Result').dataTable( {
                        "sPaginationType": "bootstrap",
                        "sDom": 'T<"clear">lfrtip',
                        "oTableTools": {
                            "aButtons": [{
                                "sExtends": "collection",
                                "sButtonText": "Exportar",
                                "aButtons": [ "print", "csv", "xls", "pdf" ]
                            }]
                        },
                        "scrollX": true,
                        "fnInitComplete": function(){
                            $(".dataTables_wrapper select").select2({
                                dropdownCssClass: 'noSearch'
                            });
                        }
                    });

                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });

    });

</script>

<div id="Modal_GetServiciosAcum" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 80%; margin-left: -40%;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelGetServiciosAcum">  </h3>
    </div>
    <div class="modal-body">
        <input type="hidden" id="GSFolio">
        <div class="control-group row-fluid">
            <div class="span4">
                <label class="control-label" >Fecha</label>
                <div class="controls">
                    <input type="text" id="GSFecha_inicio" style="width:120px;" value="<?php echo date("Y-m-d");?>">
                </div>
            </div>
            <div class="span4">
                <label class="control-label" >Fecha:</label>
                <div class="controls">
                    <input type="text" id="GSFecha_fin" style="width:120px;" value="<?php echo date("Y-m-d");?>">
                </div>
            </div>
            <div class="span4">
                <label class="control-label" title="Seleccionar Todos los Servicios ignorando el Rango de Fechas." >Todos:</label>
                <div class="controls">
                    <input type="checkbox" id="GSTodos" value="1" title="Seleccionar Todos los Servicios ignorando el Rango de Fechas." >
                </div>
            </div>
        </div>

        <p id="ServiciosAcumTableContent"></p>
    </div>
    <div class="modal-footer">
        <input id="Search-Modal-GetServiciosAcum" class="btn btn-success" value="Buscar" style="margin-top: 6px;" >
        <button id="Close-Modal-GetServiciosAcum" class="btn btn-success" data-dismiss="modal" aria-hidden="true">Cerrar</button>
    </div>
</div>
