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
                        "sTitle": "Reporte de Vigencia Maestros Seccion 21 - <?=date('Y-m-d') ?> ",
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
    <form target="_blank" method="POST" action="<?php echo $this->createUrl("reportes/relacionvmaestros21"); ?>">

        <div class="row">
            <div class="span12">
                <label>Empresa:</label>
                <select class="Select2" id="EMPRESA" name="EMPRESA" required="required" >
                    <option value="38">SECCION 21</option>
                </select>
                <script type="text/javascript">
                    $("#EMPRESA option[value='<?=$_POST['EMPRESA']?>']").attr("selected",true);
                </script>
            </div>
        </div>

        <div class="row">
            <div class="span3">
                <label>Filtrar Socios por:</label>
                <select class="Select2" id="STATUS" name="STATUS" >
                    <option value="Activo">Activos</option>
                    <option value="Cancelado">Cancelados</option>
                    <option value="Suspendido">Suspendidos</option>
                    <!-- <option value="">Todos</option> -->
                </select>
                <script type="text/javascript">
                    $("#STATUS option[value='<?=$_POST['STATUS']?>']").attr("selected",true);
                </script>
            </div>
            <div class="span3">
                <label>Vigencia:</label>
                <input type="text" id="QUINCENA" name="QUINCENA" placeholder="Quincena" value="<?=$_POST['QUINCENA']?>" style="max-width:80px;" />
                <input type="text" id="ANIO" name="ANIO" placeholder="Año" value="<?=$_POST['ANIO']?>" style="max-width:80px;" />
            </div>
            <div class="span3">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" name="BUSCAR" value="Buscar" >
            </div>
            <div class="span3">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-danger" name="IMPRIMIR" value="Imprimir Formas Especiales" >
            </div>
        </div>


    <div class="row">
        <div class="span12">
            <table id="RelacionFacturas" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Filiacion</th>
                        <th>Nombre</th>
                        <th>Clave</th>
                        <th>Ef.Desde</th>
                        <th>Ef.Hasta</th>
                        <th>Importe</th>
                        <th>NSocios</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $TImporte = 0;
                    $TSaldoAnual = 0;
                    $TSaldoActual = 0;
                    foreach ($MaestrosData as $Data) {

                        if($Data['empresa'] == 25 || $Data['empresa'] == 38){
                            $Importe = $Data['costo_total'] / 2;
                            $SaldoAnual = $Importe * 24;
                            $SaldoActual = $SaldoAnual;
                            $VigenciaInicial = explode("-", $Data['sm_vigencia']);
                            $VigenciaFinal = "";
                            if(!empty($VigenciaInicial[0])){
                                if($VigenciaInicial[0] == 1){
                                    $VigenciaInicial[0] = 13;
                                }
                                $VigenciaFinal = ($VigenciaInicial[0] - 1) . "-" . ($VigenciaInicial[1] + 1);
                            }
                        }
                        $TImporte = $TImporte + $Importe;
                        $TSaldoAnual = $TSaldoAnual + $SaldoAnual;
                        $TSaldoActual = $TSaldoActual + $SaldoActual;
                        ?>

                    <tr <?=$RowColor?> >
                        <td><?=$Data['FolioTitular']?></td>
                        <td><?=$Data['sm_clavefiliacion']?></td>
                        <td><?=$Data['NombreTitular']?></td>
                        <td><?=$Data['sm_cpresupuestal']?></td>
                        <td><?=$Data['sm_vigencia']?></td>
                        <td><?=$VigenciaFinal?></td>
                        <td style="text-align:right;">$<?=number_format($Importe,2,'.', '')?></td>
                        <td><?=$Data['QTY_SOCIOS']?></td>
                        <td><input type="checkbox" id="FT_<?=$Data['FolioTitular']?>" name="FTITULAR[]" class="RowTitular" value="<?=$Data['FolioTitular']?>" ></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><B>TOTALES</B></td>
                        <td style="text-align:right; text-bold;"><b><?=number_format($TImporte,2,'.', '')?></b></td>
                        <!--
                        <td style="text-align:right;"><b><?=number_format($TSaldoAnual,2,'.', '')?></b></td>
                        <td style="text-align:right;"><b><?=number_format($TSaldoActual,2,'.', '')?></b></td> -->
                    </tr>
                </tfoot>
            </table>

        </div>
    </div>
    </form>

</div>






