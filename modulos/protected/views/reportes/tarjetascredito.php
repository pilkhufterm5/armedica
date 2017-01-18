<script type="text/javascript">

    $(document).on('ready', function() {

        var table = $('#RelacionTarjetas').dataTable( {
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
                        "sTitle": "Relacion Tarjetas - <?=date('Y-m-d')?>",
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


        $(".Date2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });


    });

</script>


<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/relacionfacturas"); ?>">
<!--         <div class="row">
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
        </div> -->
    </form>
    <h3>Reporte de Tarjetas Cr&eacute;dito</h3>
    <div class="row">
        <div class="span12">
            <table id="RelacionTarjetas" class="table table-striped table-hover table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>BLANCO</th>
                        <th>BANCOREC</th>
                        <th>TIPOCTA</th>
                        <th>CUENTA</th>
                        <th>VENCIMIENTO</th>
                        <th>FACTURA</th> <!--Se agrego por Angeles Perez 30/03/2016-->
                        <th>FECHA FACTURA</th> <!--Se agrego por Angeles Perez 30/03/2016-->
                        <th>IMPORTE</th>
                        <th>NOMBRE</th>
                        <th>EMP1</th>
                        <th>EMP2</th>
                        <th>FOLIO</th>
                        <th>EMP3</th>
                        <th>DESTATUS</th>
                        <th>DCOBRO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($GetData as $Data) { ?>
                    <?php
                    /*
                    1)      Blanco:  espacio en blanco
                    2)      BANCOREC:  el campo de Convenio en pestaña de cobranza dentro de Afiliaciones
                    3)      TIPOCTA=Dato fijo siempre 3
                    4)      CUENTA:  Campo No. De Cuenta en pestaña de cobranza dentro de Afiliaciones
                    5)      IMPORTE=Campo Tarifa del Folio
                    6)      NOMBRE=Nombre y/o Razón Social del Folio

                    7)      EMP1=SERVICIOS MEDICOS DE EMERGENCIAS (FIJO)
                    8)      EMP2=AR EMERGENCIAS(FIJO)
                    9)      FOLIO=Campo de Número de Folio del Titular
                    10)  EMP3=AR EMERGENCIAS (FIJO)
                    11)  DESTATUS=Campo ESTATUS DEL FOLIO
                    12)  DCOBRO=Al día de cobro o día de la semana de cobro del folio
                    */
                    $CUENTA = Controller::OpenSSLDecrypt($Data['CUENTA']);
                    $VENCIMIENTO = Controller::OpenSSLDecrypt($Data['VENCIMIENTO']);
                    $DiaSemana= array(
                        "1"=>"Lunes",
                        "2"=>"Martes",
                        "3"=>"Miercoles",
                        "4"=>"Jueves",
                        "5"=>"Viernes",
                        "6"=>"Sabado"
                        );
                        if($Data['TIPO_DIA_COBRO']=="Por Dia"){
                            $DiaCobro=$DiaSemana[$Data['DCOBRO']];
                        }
                        if($Data['TIPO_DIA_COBRO']=="Por Numero"){
                            $DiaCobro = $Data['DCOBRO'];
                        }
                        ?>
                    <tr <?=$RowColor?> >
                        <td><?=$Data['BLANCO']?></td>
                        <td><?=$Data['BANCOREC']?></td>
                        <td><?=$Data['TIPOCTA']?></td>
                        <td><?=$CUENTA?>&nbsp&nbsp</td>
                        <td><?=$VENCIMIENTO;?></td>
                        <td><?=$Data['FACTURA']?></td> <!--Se agrego por Angeles Perez 30/03/2016-->
                        <td><?=$Data['FECHA_FACTURA']?></td> <!--Se agrego por Angeles Perez 30/03/2016-->
                        <td style="text-align:right;" ><?=$Data['IMPORTE']?></td>
                        <td><?=$Data['NOMBRE']?></td>
                        <td><?=$Data['EMP1']?></td>
                        <td><?=$Data['EMP2']?></td>
                        <td><?=$Data['FOLIO']?></td>
                        <td><?=$Data['EMP3']?></td>
                        <td><?=$Data['DESTATUS']?></td>
                        <td><?=$DiaCobro?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>






