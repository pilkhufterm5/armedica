<script>
    $(document).on('ready',function() {
        $('#ReporteBPC').dataTable( {
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
        $('#StartDate').datepicker({ dateFormat: 'yy-mm-dd' });
        $('#EndDate').datepicker({ dateFormat: 'yy-mm-dd' });
        $("#intostocklocation").select2();


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
<style>
.select2-container {
    display: inline-block;
    margin: 0;
    min-width: 150px;
    position: relative;
    vertical-align: middle;
}
</style>

<?php FB::INFO($ListMovesData,'_______________________$ListMovesData'); ?>

<div style="height: 50px;"></div>
<div class="accordion-group">
    <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-BajasPC" href="#BajasPC"><label class="accordion-header">Bajas por Consumo</label></a>
    </div>
    <div id="BajasPC" class="accordion-body in collapse">
        <div class="accordion-inner">
            <form method="POST">
                De: <input type="text" id="StartDate" name="StartDate" value="<?=$_POST['StartDate']?>" style="width: 120px;" />
                Hasta: <input type="text" id="EndDate" name="EndDate" value="<?=$_POST['EndDate']?>" style="width: 120px;" />
                Almacén: <select id="intostocklocation" name="intostocklocation" >
                    <?php foreach ($_SESSION['rh_permitionlocation'] as $value => $name){
                        if(isset($_POST['intostocklocation']) && $_POST['intostocklocation'] == $value){
                           echo '<option selected=selected value="'.$value.'">'.$name.'</option>';
                        }
                    ?>
                        <option value="<?=$value?>"><?=$name?></option>
                    <?php } ?>
                </select>
                <input type="submit" class="btn btn-small" id="Search" name="Search" value="Buscar" />
                <input type="button" class="btn btn-small" onclick="tableToExcel('ReporteBPC', 'Reporte Baja por Consumo')" value="Exportar a Excel" />
            </form>
            <table class="table table-striped table-hover table-bordered" id="ReporteBPC">
                <thead>
                    <tr>
                        <th>Cod. Barras</th>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Existencia Inicial</th>
                        <th>Consumo</th>
                        <th>Existencia Final</th>
                        <th>Almacen</th>
                        <th>Fecha</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($ListMovesData as $Data){ ?>
                    <tr>
                        <td><?=$Data['barcode']?></td>
                        <td><?=$Data['stockid']?></td>
                        <td><?=$Data['description']?></td>
                        <td style="text-align: right;"><?php echo $Data['newqoh'] - $Data['qty'];?></td>
                        <td style="text-align: right;"><?=$Data['qty']?></td>
                        <td style="text-align: right;"><?=$Data['newqoh']?></td>
                        <td><?=$Data['loccode']?></td>
                        <td><?=$Data['trandate']?></td>
                        <td><?=$Data['narrative']?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
