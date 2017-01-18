<script type="text/javascript">

    $(document).on('ready', function() {

        var table = $('#RelacionFacturas').dataTable( {
            "columnDefs": [
                { "visible": false, "targets": 2 }
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
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var last=null;
                api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                    if ( last !== group ) {
                        $(rows).eq( i ).before(
                            '<tr class=""><td colspan="19"><span class="badge badge-success">Nombre: '+group+'</span></td></tr>'
                        );
                        last = group;
                    }
                } );
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


        $(".Date2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });


    });

</script>

<style type="text/css">
    tr.group,tr.group:hover, tr.group-rh {
        background-color: #CCEDAB !important;
    }
</style>

<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/relacionfacturas"); ?>">
        <div class="row">
            <div class="span12"><label><strong>Fechas de Facturas</strong></label></div>
        </div>
        <div class="row">
            <div class="span3">
                <label>Fecha Inicial:</label>
                <input type="text" id="FInicial" name="GetInvoice[FInicial]" class="Date2" value="<?=$_POST['GetInvoice']['FInicial']?>" style="max-width:150px;" />
            </div>
            <div class="span3">
                <label>Fecha Final:</label>
                <input type="text" id="FFinal" name="GetInvoice[FFinal]" class="Date2" value="<?=$_POST['GetInvoice']['FFinal']?>" style="max-width:150px;" />
            </div>
            <div class="span6"></div>
        </div>

        <div class="row">
            <div class="span12"><label><strong>Fechas de Pago</strong></label></div>
        </div>

        <div class="row">
            <div class="span3">
                <label>Fecha Inicial:</label>
                <input type="text" id="PInicial" name="GetInvoice[PInicial]" class="Date2" value="<?=$_POST['GetInvoice']['PInicial']?>" style="max-width:150px;" />
            </div>
            <div class="span3">
                <label>Fecha Final:</label>
                <input type="text" id="PFinal" name="GetInvoice[PFinal]" class="Date2" value="<?=$_POST['GetInvoice']['PFinal']?>" style="max-width:150px;" />
            </div>
            <div class="span3">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" value="Buscar" >
            </div>
            <div class="span3"></div>
        </div>
    </form>

    <div class="row">
        <div class="span12">
            <table id="RelacionFacturas" class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>AfilNo</th>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Status</th>
                        <th>Cobrador</th>
                        <th>Asesor</th>
                        <th>F.Ingreso.Socio</th>
                        <th>F.Ultima.Socio</th>
                        <th>Producto</th>
                        <th>Frec.Pago</th>
                        <th>Form.Pago</th>
                        <th>Tipo</th>
                        <th>DOC</th>

                        <!-- ==============================
                        AGREGADO POR DANIEL VILLARREAL PARA MOSTRAR EL MOTIVO DE LA NOTA DE CREDITO, EL 14 DE DICIEMBRE DEL 2015
                        ============================== -->
                        <th>NC</th>
                         <!-- ===============================
                                TERMINA
                        =============================== -->
                        <th>Factura</th>
                        <th>FStatus</th>
                        <th>FechaGenera</th>
                        <th>FechaPago</th>
                        <th>Subtotal</th>
                        <th>Impuesto</th>
                        <th>Total</th>
                        <th>TotalPagado</th>
                        <th>SALDO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($InvoiceData as $Data) { ?>
                    <?php
                    if(isset($Data['FechaGenera'])){
                        $Data['FechaGenera'] = date("Y-m-d", strtotime($Data['FechaGenera']));
                    }
                    if(isset($Data['FechaPago'])){
                        $Data['FechaPago'] = date("Y-m-d", strtotime($Data['FechaPago']));
                    }
                    if($Data['StatusFactura'] == 'C'){
                        $RowColor = "class='danger' ";
                        $Data['StatusFactura'] = 'CANCELADA';
                    }else{
                        $Data['StatusFactura'] = 'ACTIVA';
                        $RowColor = "class='' ";
                    }
                    if($Data['type'] == 10){
                        $Data['type'] = "FACTURA";
                    }
                    if($Data['type'] == 11){

                         $Data['type'] = "NOTA C " . $Data['NoNOTA'];
                        $Data['descripcion'] = $Data['descripcion'];
                    }else{
                        $Data['descripcion'] = '';

                    }
                    ?>
                    <tr <?=$RowColor?> >
                        <td><?=$Data['AfilNo']?></td>
                        <td><?=$Data['TipoFolio']?></td>
                        <td><?=$Data['AfilName']?></td>
                        <td><?=$Data['AfilStatus']?></td>
                        <td><?=$ListaCobradores[$Data['AfilCobrador']]?></td>
                        <td><?=$ListaAsesores[$Data['AfilAsesor']]?></td>
                        <td><?=$Data['fecha_ingreso']?></td>
                        <td><?=$Data['fecha_reactivacion']?></td>
                        <td><?=$ListaPlanes[$Data['AfilProduct']]?></td>
                        <td><?=$ListaFrecuenciapagos[$Data['AfilFrecuenciaPago']]?></td>
                        <td><?=$ListaMetodosdePago[$Data['AfilMetodoPago']]?></td>
                        <td><?=$ListaTipoFacturas[$Data['TipoFactura']]?></td>
                        <td><?=$Data['type']?></td>
                        <!-- ==============================
                        AGREGADO POR DANIEL VILLARREAL PARA MOSTRAR EL MOTIVO DE LA NOTA DE CREDITO, EL 14 DE DICIEMBRE DEL 2015
                        ============================== -->
                        <td><?=$Data['descripcion']?></td>
                        <!-- ===============================
                                TERMINA
                        =============================== -->
                        <td><?=$Data['FolioFactura']?></td>
                        <td><?=$Data['StatusFactura']?></td>
                        <td><?=$Data['FechaGenera']?></td>
                        <td><?=$Data['FechaPago']?></td>
                        <td style="text-align:right;">$<?=number_format($Data['ovamount'],2)?></td>
                        <td style="text-align:right;">$<?=number_format($Data['ovgst'],2)?></td>
                        <td style="text-align:right;">$<?=number_format($Data['total'],2)?></td>
                        <td style="text-align:right;">$<?=number_format($Data['LOPAGADO'],2)?></td>
                        <td style="text-align:right;">$<?=number_format($Data['SALDO'],2)?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>






