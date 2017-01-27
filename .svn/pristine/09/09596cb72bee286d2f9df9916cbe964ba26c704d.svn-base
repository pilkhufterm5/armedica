<script type="text/javascript">
    $(document).on('ready',function() {

        $('#GSStartDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $("#GSEndDate").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        oTable1 = $('#SearchInvoiceTable').dataTable( {
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

        $("#GSSearch").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/BuscarFacturas"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    GetInvoice:{
                        Folio: $("#GSFolio").val(),
                        DebtorNo: $("#GSDebtorNo").val(),
                        StartDate: $("#GSStartDate").val(),
                        EndDate: $("#GSEndDate").val(),
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        $.unblockUI();
                        oTable1.fnDestroy();
                        $('#BodyContent').html(Response.TableContent);

                        oTable1 = $('#SearchInvoiceTable').dataTable( {
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

<div style="height: 20px;"></div>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>Busqueda de Facturas</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Afiliados</a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <form>
                <div class="control-group row-fluid">
                    <div class="span2">
                        <label class="control-label" >Folio Factura:</label>
                        <div class="controls">
                            <input type="text" id="GSFolio" name="Search[GSFolio]" style="width:100px;">
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label" >Folio Socio:</label>
                        <div class="controls">
                            <input type="text" id="GSDebtorNo" name="Search[GSDebtorNo]" style="width:100px;">
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label" >Fecha Inicio:</label>
                        <div class="controls">
                            <input type="text" id="GSStartDate" name="Search[StartDate]" style="width:120px;" >
                        </div>
                    </div>
                    <div class="span3">
                        <label class="control-label" >Fecha Fin:</label>
                        <div class="controls">
                            <input type="text" id="GSEndDate" name="Search[EndDate]" style="width:120px;" >
                        </div>
                    </div>
                    <div class="span2">
                        <label class="control-label"  >&nbsp;</label>
                        <div class="controls">
                            <input type="button" id="GSSearch" value="Buscar" class="btn btn-success" style="height: 35px;" >
                        </div>
                    </div>
                </div>
                </form>
                <table id="SearchInvoiceTable" class="table table-hover table-condensed table-striped">
                    <thead>
                        <tr>
                            <th><?php echo _('Folio') ?></th>
                            <th><?php echo _('Estatus') ?></th>
                            <th><?php echo _('Nombre') ?></th>
                            <th><?php echo _('Fecha') ?></th>
                            <th><?php echo _('Subtotal') ?></th>
                            <th><?php echo _('Impuesto') ?></th>
                            <th><?php echo _('Total') ?></th>
                            <th style="text-align: center; width: 60px;"><i class="icon-cog" title="Actions"></i></th>
                        </tr>
                    </thead>
                    <tbody id="BodyContent">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div> <!-- End Tabs -->
    </div>
</div>
