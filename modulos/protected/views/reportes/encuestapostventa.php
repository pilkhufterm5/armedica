<script type="text/javascript">

    $(document).on('ready', function() {

        $('#Encuestapostventa').dataTable( {
            "sPaginationType": "bootstrap",
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "collection",
                    "sButtonText": "Exportar",
                    "aButtons": [ "print", "csv", "xls", {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sTitle": "Reporte Encuesta Post-Venta - <?=date('Y-m-d') ?> ",
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
    <form method="POST" action="<?php echo $this->createUrl("reportes/encuestapostventa"); ?>">
        <div class="row">
            <div class="form-legend"><h3>REPORTE ENCUESTA POST-VENTA </h3></div>       
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
                <a href="<?php echo $this->createUrl("reportes/encuestapostventadetalle"); ?>"><input type="button" value="IR AL REPORTE DE POST-VENTA DETALLE" class="btn btn-info"></a>
            </div>
        </div>      
    </div>
        <div class="row">
            <div class="span12">
                <table id="Encuestapostventa" class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>MES</th>
                            <th>AÑO</th>
                            <th>ASESOR</th>
                            <th>ENCUESTAS</th>
                            <th>PROMEDIO_CALIFICACIÓN</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($Encuestapostventa as $Data) 
                            foreach ($TotalesData as $Totales){
                    $MES = "";

                     switch ($Data['fechames']) {

                        case 'January':
                        $Data['fechames'] ='ENERO';
                        break; 
                        case 'February':
                        $Data['fechames'] ='FEBRERO';
                        break; 
                        case 'March':
                        $Data['fechames'] ='MARZO';
                        break; 
                        case 'April':
                        $Data['fechames'] ='ABRIL';
                        break; 
                        case 'May':
                        $Data['fechames'] ='MAYO';
                        break; 
                        case 'June':
                        $Data['fechames'] ='JUNIO';
                        break; 
                        case 'July':
                        $Data['fechames'] ='JULIO';
                        break; 
                        case 'August':
                        $Data['fechames'] ='AGOSTO';
                        break; 
                        case 'September':
                        $Data['fechames'] ='SEPTIEMBRE';
                        break; 
                        case 'October':
                        $Data['fechames'] ='OCTUBRE';
                        break; 
                        case 'November':
                        $Data['fechames'] ='NOVIEMBRE';
                        break; 
                        case 'December':
                        $Data['fechames'] ='DICIEMBRE';
                        break; 

                    default:
                        break;
                                }
                            
                            ?>

                            <tr>
                                <td><?=$Data['fechames']?></td>
                                <td><?=$Data['fechaño']?></td>
                                <td><?=$Data['comisionista']?></td>
                                <td><?=$Data['encuestas']?></td>
                                <td><?=number_format($Data['porcentaje'], 2, ".", ",")?></td>
                                
                            </tr>
                        <?php }
                     ?>
                    </tbody>
                    <tfoot id="Contenido">

                    <?php
                        foreach ($TotalesData as $Totales) {
                    ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td><b><?='No. ENCUESTAS:'.' '.$Totales['encuestas']?></b></td>
                            <td><b><?='PROMEDIO GENERAL:'.' '.number_format($Totales['porcentaje'], 2, ".", ",")?></b></td>
                            
                                
                        </tr>
                    <?php } ?>
                </tfoot>
                
                </table>
            </div>
        </div>
      </form>
</div>



               
  






