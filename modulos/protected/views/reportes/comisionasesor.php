<script type="text/javascript">

    $(document).on('ready', function() {

        var table = $('#RelacionFacturas').dataTable( {
            "columnDefs": [
                { "visible": true, "targets": 2 }
            ],
            "order": [[ 2, "desc" ]],
            //"order": [ 2, 'asc' ],
            "sPaginationType": "bootstrap",
            "dom": 'T<"clear">lfrtip',
            "oTableTools": {
                "Buttons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "excel", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Relacion Facturas - <?=date('Y-m-d')?>",
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
            },
            "drawCallback": function ( settings ) {

            }
        });


       // Order by the grouping
        /*$('#RelacionFacturas tbody').on( 'click', 'tr.group', function () {
            var currentOrder = table.order()[0];
            if ( currentOrder[0] === 2 && currentOrder[1] === 'asc' ) {
                table.order( [ 2, 'desc' ] ).draw();
            }
            else {
                table.order( [ 2, 'asc' ] ).draw();
            }
        } );*/

        //Array para dar formato en español
         $.datepicker.regional['es'] = 
         {
         closeText: 'Cerrar', 
         prevText: 'Previo', 
         nextText: 'Próximo',
         
         monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
         'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
         monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
         'Jul','Ago','Sep','Oct','Nov','Dic'],
         monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
         dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
         dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
         dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
         dateFormat: 'yy-mm-dd', firstDay: 6, 
         initStatus: 'Selecciona la fecha', isRTL: false};
         $.datepicker.setDefaults($.datepicker.regional['es']);

        $(".Date2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5',
            //minDate: "-20D",
            //maxDate: "+2M, -10D"            
        });

                    var startDate = new Date("2016-02-15");
                    var selDay = startDate.getDay();


        $("input.comisionista").tokenInput(

            function() {

                return "<?php echo $this->createUrl("reportes/asesor"); ?>";

            },
            {
                queryParam:"comisionista",
                searchDelay:300,
                minChars:1,
                propertyToSearch:"comisionista",
                hintText:"escribe para buscar coincidencias",
                noResultsText:"no hubo coincidencias",
                searchingText:"buscando...",
                deleteText:"<button type='button' class='deleteItem' tabindex=11>×</button>",
                animateDropdown:false,
                /*tokenFormatter:function(item) {},*/
                tokenLimit:1,
                tokenValue:"id",
                preventDuplicates:true,
                onResult:function(results) {
                    return results.data;

                },
                onAdd:null,
                onDelete:null,
                onReady:function(results) {
                },
                onAdd:function(item) {
                    $("#asesor_name").val(item.comisionista);
                    $("#asesor_id").val(item.id);

                },
                onDelete:function(item) {
                    $("#asesor_name").val("");
                    $("#asesor_id").val("");                    
                },
                <?php if($_POST['GetInvoice']['asesor_name']): ?>
                prePopulate:[
                    {id:<?php echo json_encode($_POST['GetInvoice']['asesor_id']); ?>,comisionista:<?php echo json_encode($_POST['GetInvoice']['asesor_name']); ?>,},
                ],
                <?php endif; ?>                  

            }

        );

    });

</script>

<style type="text/css">
    tr.group,tr.group:hover, tr.group-rh {
        background-color: #CCEDAB !important;
    }
</style>
<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("reportes/comisionasesor"); ?>">
        <div class="row">
            <div class="span12"><label><strong>Fechas de facturas pagadas</strong></label></div>
        </div>
        <div class="row">
            <div class="span2">
                <label>Fecha Inicial:</label>
                <input type="text" id="FInicial" name="GetInvoice[datefrom]" class="Date2" value="<?=$datefrom;?>" style="width: 80%" disabled=disabled />
            </div>
            <div class="span2">
                <label>Fecha Final:</label>
                <input type="text" id="FFinal" name="GetInvoice[dateuntil]" class="Date2" value="<?=$dateuntil;?>" style="width: 80%" disabled=disabled />
            </div>
            <div class="span5">
                <label>Asesor</label>
                <input type="text" id="asesor" class="comisionista" name="GetInvoice[asesor]" value="<?=$_POST['GetInvoice']['asesor_name']?>" style="width: 100%"/>
                <input type="hidden" id="asesor_name" name="GetInvoice[asesor_name]" value="<?=$_POST['GetInvoice']['asesor_name']?>" style="width: 80%"/>
                <input type="hidden" id="asesor_id" name="GetInvoice[asesor_id]" value="<?=$_POST['GetInvoice']['asesor_id']?>" style="width: 80%"/>
            </div>
            <div class="span2">
                <label>Factura</label>
                <input type="text" id="folio" class="folio" name="GetInvoice[folio]" value="<?=$_POST['GetInvoice']['folio']?>" style="width: 100%"/>
            </div>
        </div>
        <div class="row">
            <div class="span2">
                <label>Mes</label>
                <select class="span6" name="GetInvoice[periodo_mes]">
                    <option value=""></option>
                    <?php foreach($month as $k => $v): ?>
                        <?php if($k==$periodo_mes): ?>
                        <option selected value="<?php echo ($k<9?"0":"").$k; ?>"><?php echo $v; ?></option>
                        <?php else: ?>
                        <option value="<?php echo ($k<9?"0":"").$k; ?>"><?php echo $v; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="span2">
                <label>Periodo</label>
                <select class="span6" name="GetInvoice[period]">
                <option value="6">Todos</option>
                <?php for($i=1; $i<=$days_period; $i++){ ?>
                    <?php if($i==$period): ?>
                    <option selected value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php else: ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endif; ?>
                <?php } ?>
                </select>
            </div>
            <div class="span3">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" value="Buscar" >
            </div>            
        </div>        
    </form>

    <div class="row">
        <div class="span12">
            <table id="RelacionFacturas" class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Asesor</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Producto</th>
                        <th>DOC</th>
                        <th>Factura</th>
                        <th>Fecha de pago</th>
                        <th>Subtotal</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                        <th>TotalPagado</th>
                        <th>SALDO</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($regs as $k => $v): ?>
                    <tr>
                    <td><?php echo $v["name_asesor"]; ?></td>
                    <td><?php echo $v["name"]; ?></td>
                    <td><?php echo $v["tipo_membresia"]; ?></td>
                    <td><?php echo $v["description"]; ?></td>
                    <td><?php echo $v["doc"]; ?></td>
                    <td><?php echo $v["foliofactura"]; ?></td>
                    <td><?php echo $v["trandate"]; ?></td>
                    <td><?php echo ($v['ovamount'] ? number_format($v['ovamount'],2,',','.'): 0); ?></td>
                    <td><?php echo ($v['ovgst'] ? number_format($v['ovgst'],2,',','.'): 0); ?></td>
                    <td><?php echo ($v['total'] ? number_format($v['total'],2,',','.'): 0); ?></td>
                    <td><?php echo ($v['LOPAGADO'] ? number_format($v['LOPAGADO'],2,',','.'): 0); ?></td>
                    <td><?php echo ($v['SALDO'] ? number_format($v['SALDO'],2,',','.'): 0); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="span4">Total: <?php echo number_format($suma_total,2,',','.'); ?></div>
        </div>
    </div>

</div>






