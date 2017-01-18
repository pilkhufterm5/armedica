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
                        "sTitle": "Reporte de Vigencia Maestros Seccion 50 - <?=date('Y-m-d') ?> ",
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
    <form method="POST" action="<?php echo $this->createUrl("reportes/candidatossuspension"); ?>">

        <div class="row">
            <div class="span3">
                <label>Fecha de Ingreso:</label>
                <input type="text" id="INICIO"  name="INICIO"   placeholder="Inicio"    class="Date2"   value="<?=$_POST['INICIO']?>" style="max-width:85px;" />
                <input type="text" id="FIN"     name="FIN"      placeholder="Fin"       class="Date2"   value="<?=$_POST['FIN']?>" style="max-width:85px;" />
            </div>
            <div class="span3">
                <label>FormaPago:</label>
                <select class="Select2" id="FORMA_PAGO" name="FORMA_PAGO" >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaFormasPago as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#FORMA_PAGO option[value='<?=$_POST['FORMA_PAGO']?>']").attr("selected",true);
                </script>
            </div>
            <div class="span3">
                <label>FrecuenciaPago:</label>
                <select class="Select2" id="FRECUENCIA_PAGO" name="FRECUENCIA_PAGO"  >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaFrecuenciaPago as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#FRECUENCIA_PAGO option[value='<?=$_POST['FRECUENCIA_PAGO']?>']").attr("selected",true);
                </script>
            </div>
            <div class="span3">
                <label>PLAN:</label>
                <select class="Select2" id="PLAN" name="PLAN"  >
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaPlanes as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#PLAN option[value='<?=$_POST['PLAN']?>']").attr("selected",true);
                </script>
            </div>
        </div>

        <div class="row">
            <div class="span3">
                <label>Estatus:</label>
                <select class="Select2" id="STATUS" name="STATUS" >
                    <option value="">SELECCIONE</option>
                    <option value="Activo">ACTIVOS</option>
                    <!-- <option value="Cancelado">Cancelados</option> -->
                    <option value="Suspendido">SUSPENDIDOS</option>
                </select>
                <script type="text/javascript">
                    $("#STATUS option[value='<?=$_POST['STATUS']?>']").attr("selected",true);
                </script>
            </div>

            <div class="span2">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" name="BUSCAR" value="Buscar" >
            </div>

        </div>


        <div class="row">
            <div class="span12">
                <table id="RelacionFacturas" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Nombre</th>
                            <th>FechaIngreso</th>
                            <th>Estatus</th>
                            <th>Plan</th>
                            <th>FormaPago</th>
                            <th>FrecuenciaPago</th>
                            <th>FacturasVencidas</th>
                            <th>SaldoVencido</th>
                            <th>Candidato A</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($Candidatos as $Data) {
                            /*
                            Candidato a:  es un dato calculado = Texto Dependiendo del Número de Facturas Vencidas:
                            SUSPENSIÓN(si esta Activo y tiene 3 ó mas facturas vencidas),
                            ACTIVACIÓN(si esta Suspendido y tiene 2 o Menos facturas vencidas)
                            */
                            $RowColor = "";
                            $CANDIDATO = "NA";
                            if($Data['ESTATUS_TITULAR'] == 'Activo' && $Data['FACTURAS_VENCIDAS'] >= 3){
                                $CANDIDATO = "SUSPENSION";
                                $RowColor = " class='warning' ";
                            }

                            if($Data['ESTATUS_TITULAR'] == 'Suspendido' && $Data['FACTURAS_VENCIDAS'] <= 2){
                                $CANDIDATO = "ACTIVACION";
                                $RowColor = " class='precapturado' ";
                            }

                            if($CANDIDATO != 'NA')
                            {
                            ?>

                            <tr <?=$RowColor?> >
                                <td><?=$Data['NoSOCIO']?></td>
                                <td><?=$Data['NOMBRE']?></td>
                                <td><?=$Data['FECHA_INSCIPCION']?></td>
                                <td><?=$Data['ESTATUS_TITULAR']?></td>
                                <td><?=$Data['PLAN']?></td>
                                <td><?=$Data['FORMA_PAGO']?></td>
                                <td><?=$Data['FRECUENCIA_PAGO']?></td>
                                <td style="text-align:center;"><?=$Data['FACTURAS_VENCIDAS']?></td>
                                <td style="text-align:right;">$<?=number_format($Data['SALDO_VENCIDO'],2, '.', ',')?></td>
                                <td><?=$CANDIDATO?></td>
                            </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>





