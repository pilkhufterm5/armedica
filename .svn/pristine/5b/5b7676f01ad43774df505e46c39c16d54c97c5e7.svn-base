<script type="text/javascript">
    $(document).on('ready', function() {
        $('#PurchOrdersList').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
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
    
</script>

<div style="height: 20px"></div>
<h3>Ordenes de Compra Pendientes por Facturar</h3>
<input type="button" class="btn btn-small btn-success" onclick="tableToExcel('PurchOrdersList', 'Listado de Pendientes por Facturar')" value="Exportar a Excel" />
<table id="PurchOrdersList" class="table table-bordered" >
    <thead>
        <tr>
            <th>Pedido</th>
            <th>Proveedor</th>
            <th>Cant. Ordenada</th>
            <th>Cant. Recibida</th>
            <th>Cant. Facturada</th>
            <th style="text-align: right;" >Precio</th>
            <th style="text-align: right;" >Impuesto</th>
            <th style="text-align: right;" >Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($OrderRowData as $Order){ ?>
            <tr><?php  //$Tax = (($Order['qty_ord'] * $Order['price']) * $Order['tax'])/100  ?>
                <td><?=$Order['orderno']?></td>
                <td><?=$ListaSuppliers[$Order['supplierid']]?></td>
                <td style="text-align: right;" ><?=$Order['qty_ord']?></td>
                <td style="text-align: right;" ><?=$Order['qty_recib']?></td>
                <td style="text-align: right;" ><?=$Order['qty_fact']?></td>
                <td style="text-align: right;" ><?=number_format($Order['price'],2)?></td>
                <td style="text-align: right;" ><?=number_format($Order['tax'],2)?></td>
                <td style="text-align: right;" ><?=number_format(($Order['qty_ord'] * $Order['price']) + $Order['tax'],2)?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>