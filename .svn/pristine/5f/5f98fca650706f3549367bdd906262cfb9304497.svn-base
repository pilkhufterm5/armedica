<script type="text/javascript">
    $(document).on('ready',function() {
        $('#CustomerInquiry').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "fnInitComplete": function(){
                $(".dataTables_wrapper select").select2({
                    dropdownCssClass: 'noSearch'
                });
            }
        });
        $("#StatusFactura").select2();
        $("#TipoFactura").select2();

        $('#TransAfterDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#TransBeforeDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });
    });

    var tableToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,', template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>', base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        }, format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        }
        return function(table, name) {
            if (!table.nodeType)
                table = document.getElementById(table)
            var ctx = {
                worksheet : name || 'Worksheet',
                table : table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    })()

    function Timbrar(debtorno,folio,debtortrans_id,idBitacora){

        if (confirm('¿Desea Generar la Factura Fiscal?')) {
            // Timbra la Factura
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/Process"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetInvoice:{
                          DebtorNo: debtorno,
                          Folio: folio,
                          debtortrans_id: debtortrans_id,
                          BitacoraID: idBitacora
                      },
                },
                success : function(GetInvoice, newValue) {
                    if (GetInvoice.requestresult == 'ok') {
                        displayNotify('success', GetInvoice.message);
                        $('#CustomerInquiry #status' + GetInvoice.BitacoraID).text('Procesada');
                        $('#CustomerInquiry #actions' + GetInvoice.BitacoraID).html(GetInvoice.actions_td);
                    }else{
                        displayNotify('alert', GetInvoice.message);
                    }
                },
                error : ajaxError
            });//End Timbrado
        }
    }

    function CreateInvoice(debtorno,folio,idBitacora){


        if (confirm('¿Desea Factura Fiscal?')) {
            // Genera la Factura
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/CreateInvoice"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetInvoice:{
                          DebtorNo: debtorno,
                          Folio: folio,
                          BitacoraID: idBitacora
                      },
                },
                success : function(GetInvoice, newValue) {
                    if (GetInvoice.requestresult == 'ok') {
                        displayNotify('success', GetInvoice.message);
                        OpenInNewTab("../PHPJasperXML/sample1.php?isTransportista=0&transno=" + GetInvoice.transno);
                    }else{
                        displayNotify('alert', GetInvoice.message);
                    }
                },
                error : ajaxError
            });//End Genera Factura
        }
    }

    function OpenInNewTab(url ){
        var win = window.open(url, '_blank');
        win.focus();
    }


</script>

<div style="width: 80%; margin-left:10%;">
<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/pendientesfacturar/"); ?>" >
        <table>
            <tr>
                <td><label>Despues de:</label>
                    <input id="TransAfterDate" name="TransAfterDate" type="text" value="<?=$_POST['TransAfterDate']?>" class="span8" />
                </td>
                <td>
                    <label>Antes de:</label>
                    <input id="TransBeforeDate" name="TransBeforeDate" type="text" value="<?=$_POST['TransBeforeDate']?>" class="span8" />
                </td>
                <td>
                    <label style="margin-top: 10px;" >Estatus Factura:</label>
                    <select id="StatusFactura" name="StatusFactura" class="span6" value="<?=$_POST['StatusFactura']?>" >
                        <option value="Procesada">Procesada</option>
                        <option value="PendienteTimbrar">Pendiente Timbrar</option>
                        <option value="Cancelada">Cancelada</option>
                        <option value="%">Todas</option>

                    </select>
                </td>

                <td>
                    <label style="margin-top: 10px;" >Tipo Factura:</label>
                    <select id="TipoFactura" name="TipoFactura" class="span6" value="<?=$_POST['TipoFactura']?>" >
                        <option value="Sin Serie" >Sin Serie</option>
                        <option value="Serie L" >Serie L</option>
                    </select>
                </td>
                <td>
                    <input type="submit" name="Search" value="Buscar" class="btn btn-small btn-success" style=" height: 30px; margin-top: 18px;" />
                </td>
            </tr>
        </table>
    </form>

        <table id="CustomerInquiry" class="table table-hover table-condensed table-striped" style="float: 0;" >
            <thead>
                <tr>
                    <th>Documento</th>
                    <th>Fecha Factura</th>
                    <th>Folio</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Plan</th>
                    <th>FormaPago</th>
                    <th>Frec.Pago</th>
                    <th>Total</th>
                    <th>TipoFactura</th>
                    <th>Estatus</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($InvoiceData as $Data){
                    switch ($Data['rh_status']) {
                        case 'C':
                            $RowColor = "class= 'danger'";
                            $Data['rh_status'] = "Cancelada";
                            break;
                        case 'F':
                            $RowColor = "class= 'warning'";
                            $Data['rh_status'] = "Cancelacion de Factura";
                            break;
                        default:

                            break;
                    }
                ?>
                <tr <?=$RowColor?> id="<?=$Data['id']?>" >
                    <td><?=$Data['TypeName']?></td>
                    <td><?=$Data['created']?></td>
                    <td><?=$Data['TFolio']?></td>
                    <td><?=$Data['TName'] . " " .  $Data['TApellidos']?></td>
                    <td><?=$Data['FATipo']?></td>
                    <td><?=$Data['CPlan']?></td>
                    <td><?=$Data['CPaymentName']?></td>
                    <td><?=$Data['CFrecPago']?></td>
                    <td style="text-align: right;">$ <?=number_format($Data['TotalInvoice'],2)?></td>
                    <td><?=$Data['tipo']?></td>
                    <td id="status<?=$Data['id']?>"><?=$Data['status']?></td>
                    <td id="actions<?=$Data['id']?>">

                        <?php
                        switch ($Data['status']) {
                            case 'PendienteTimbrar': ?>
                                <a id="Pending" name = "Pending" BranchCode="<?=$Data['branchcode']?>" DebtoNo="<?=$Data['debtorno']?>" title="Activar Socio" onclick="Timbrar(<?=$Data['debtorno']?>,<?=$Data['folio']?>,<?=$Data['debtortrans_id']?>,<?=$Data['id']?>)" ><i class="icon-ok"></i></a>
                                <?php
                                break;
                            case 'Procesada': ?>
                                <a target= "_blank" href="../PHPJasperXML/sample1.php?transno=<?=$Data['transno']?>&afil=true" ><IMG src="../css/silverwolf/images/pdf.gif" ></a>
                                <?php
                                break;
                            default:

                                break;
                        }
                        ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>

</div>
