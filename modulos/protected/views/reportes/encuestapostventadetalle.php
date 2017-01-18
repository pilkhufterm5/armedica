<script type="text/javascript">

    $(document).on('ready', function() {

        $('#Encuestapostventadetalle').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Reporte Encuesta Post-Venta Detalle - <?=date('Y-m-d') ?> ",
                        }, ]
                }]
            },
            "aLengthMenu": [
                [10,25, 50, 100, 200, -1],
                [10,25, 50, 100, 200, "Todos"]
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
    <form method="POST" action="<?php echo $this->createUrl("reportes/encuestapostventadetalle"); ?>">
        <div class="row">
            <div class="form-legend"><h3>REPORTE ENCUESTA POST-VENTA DETALLE</h3></div>       
        <br>
                <div class="span3">
                <label>Asesor:</label>
                <select class="Select2" id="comisionista" name="comisionista" style="width:100px;">
                    <option value="">SELECCIONE</option>
                    <?php foreach ($ListaComisionistas as $key => $value) {
                        echo "<option value='{$key}'>{$value}</option>";
                    } ?>
                </select>
                <script type="text/javascript">
                    $("#comisionista_id option[value='<?=$_POST['comisionista_id']?>']").attr("selected",true);
                </script>
            </div>
            <div class="span3">
                <label>Fecha:</label>
                <input type="text" id="INICIO"  name="INICIO"   placeholder="Inicio"    class="Date2"   value="<?=$_POST['INICIO']?>" style="width:100px;" />
                <input type="text" id="FIN"     name="FIN"      placeholder="Fin"       class="Date2"   value="<?=$_POST['FIN']?>" style="width:100px;" />
            </div>
            
             <div class="span2">
                <label>&nbsp;</label>
                <input type="submit" class="btn btn-success" name="BUSCAR" value="Buscar" >
             </div>
                 <div class="span2">
                <label>&nbsp;</label>
                <a href="<?php echo $this->createUrl("reportes/encuestapostventa"); ?>"><input type="button" value="IR AL REPORTE DE POST-VENTA" class="btn btn-info"></a>
                <label>&nbsp;</label>
                <a href="<?php echo $this->createUrl("afiliaciones/encuesta"); ?>"><input type="button" value="IR A ENCUESTA" class="btn btn-info"></a>
    </div>
        </div>
            
        

    </div>
        <div class="row">
            <div class="span12">
                <table id="Encuestapostventadetalle" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ASESOR</th>
                            <th>FECHA</th>
                            <th>FOLIO</th>
                            <th>NOMBRE_TITULAR</th>
                            <th>PERSONA_ENCUESTADA</th>
                            <th>RESULTADO_ENCUESTA</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($Encuestapostventadetalle as $Data) {
                            
                            ?>

                            <tr>
                                <td><?=$Data['comisionista']?></td>
                                <td><?=$Data['fechaingreso']?></td>
                                <td><?=$Data['folio']?></td>
                                <td><?=$Data['NombreTitular']?></td>
                                <td><?=$Data['encuestado']?></td>
                                <td><?=number_format(((($Data['p1']+$Data['p2']+$Data['p3']+$Data['p4']+$Data['p5']+$Data['p6'])*100)/6), 2, ".", ",")?></td>
                                
                            </tr>
                        <?php }
                     ?>
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>






