<script type="text/javascript">

    $(document).on('ready', function() {

        var mensaje = 'Forma Pago: <?=$DatosFactura['0']['paymentname']?>                                     '
                +' Frecuencia Pago: <?=$DatosFactura['0']['frecuencia']?>                                     '
                +' Numero de Transaccion Interna: <?=$DatosFactura['0']['transno']?>                                            '
                +' Fecha Emision: <?=$DatosFactura['0']['trandate']?>                                            '
                +' Total: $ <?=number_format($DatosFactura['0']['ovamount'],3)?>                                            '
                +' Socios: <?=$DatosFactura['0']['socios']?>';

        var table = $('#MultiEmpresas').dataTable( {
            "columnDefs": [
                //{ "visible": false, "targets": 2 }
            ],
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Relacion Folios en Factura - <?=@$_POST['NoFactura']?> - <?=date('Y-m-d H:i:s')?>",
                        "sPdfMessage": mensaje
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


    });

</script>


<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("reportes/emisionmultiempresas"); ?>">
        <div class="row">
             <div class="span12"><label><strong>Reporte de emisión de multi empresas</strong></label></div>
        </div>
        <div class="row">
            <div class="span3"> <!--Se cambio el nombre Angeles Perez 25/05/2016-->
                <label>Factura AR:</label>
                <input type="text" id="NoFactura" name="NoFactura" style="max-width:150px;" value="<?=@$_POST['NoFactura']?>" />
            </div>
            <div class="span3">
                
            </div>
            <div class="span3">
                
            </div>
            <div class="span3">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" value="Buscar" >
            </div>
            <hr>
        </div>
    </form>
        <div class="row" id="encabezadodatos" >
        <hr>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Forma Pago</label>
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['paymentname']?></strong>
            </div>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Frecuencia Pago</label>
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['frecuencia']?></strong>
            </div>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Factura AR</label><!--Se agrego Angeles Perez 25/05/2016-->
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['FacturaAR']?></strong>
                <label>No. Transacción</label>
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['transno']?></strong> 
            </div>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Fecha Emisión</label>
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['trandate']?></strong>
            </div>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Total</label>
                <strong style="display:block;font-size:15px;"><?='$ '.number_format($DatosFactura['0']['ovamount'],3)?></strong>
            </div>
            <div class="span2" style="text-align:center;margin-left: 1.564%;">
                <label>Socios</label>
                <strong style="display:block;font-size:15px;"><?=$DatosFactura['0']['socios']?></strong>
            </div>
        </div>
       
    <div class="row"> 
        
        <div class="span12">
            <table id="MultiEmpresas" class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>Folio Asociado</th>
                        <th>Nombre</th>
                        <th>Factura AR</th><!--Se agrego Angeles Perez 25/05/2016-->
                        <th>Tarifa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(count($ListaConceptos)>0){
                    foreach ($ListaConceptos as $Data) { ?>
                    <tr  >
                        <td><?=$Data['folio']?></td>
                        <td><?=$Data['nombre']?></td>
                        <td><?=$Data['FacturaAR']?></td><!--Se agrego Angeles Perez 25/05/2016-->
                        <td><?=$Data['price']?></td>

                    </tr>
                    <?php } 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>






