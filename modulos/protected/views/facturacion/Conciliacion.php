<!-- 
Vista agregada para desplegar todos los socios a los cuales se les halla emitido una factura en las fechas seleccionadas tanto para las formas de pago sin tarjetas como las de con tarjetas
      Fecha:  Lunes 09 de Enero del 2017
      Autor:  Eliobeth Ruiz (eliobeth.ruiz@armedica.com.mx, eli.obeth@hotmail.com)
Propietario:  ARMedica 
-->
<script>
    $(document).on('ready', function(){
        $("#datos_Conciliacion").dataTable({
            "sPaginationType": "bootstrap",
             dom: 'T<"clear">lfrtip',
            tableTools: {
                aButtons: [{
                    "sExtends": "xls",
                    "sButtonText": "Exporta a Excel",
                }]
            },
            "aLengthMenu": [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "Todo"]
            ],
        });

    });


    $(document).on('ready', function(){
        $("#datosConciliacionSinFactura").dataTable({
            "sPaginationType": "bootstrap",
             dom: 'T<"clear">lfrtip',
            tableTools: {
                aButtons: [{
                    "sExtends": "xls",
                    "sButtonText": "Exporta a Excel",
                }]
            },
            "aLengthMenu": [
                [25, 50, 100, 200, -1],
                [25, 50, 100, 200, "Todo"]
            ],
        });

    });

</script>

<div class="container">
    <div class="row">
        <div class="col-lg-12"> <hr><br>
            <h2 align="center">Conciliaci&oacute;n de Facturas </h2> <hr>
            <div class="row span12">
            <form method="POST" action="<?php echo $this->createUrl("facturacion/ConciliacionFactura"); ?>">
                <div class="span4">
                    <label>Emision de Efectivo (a&ntilde;o-mes):</label>
                    <input id="Fecha_Inicial" name="Fecha_Inicial" type="text" class="span6" required="true" maxlength="7" value="<?php if (empty($_POST['Fecha_Inicial'])) {
                        echo date('Y-m');
                    } else{ echo $_POST['Fecha_Inicial']; }?>" />
                </div>
                <div class="span4">
                    <label>Emision de Tarjetas (a&ntilde;o-mes):</label>
                    <input id="Fecha_Final" name="Fecha_Final" type="text" class="span6" required="true" maxlength="7" value="<?php if (empty($_POST['Fecha_Final'])) {
                        echo date('Y-m');
                    } else{ echo $_POST['Fecha_Final']; }?>" />
                </div>
                <div class="span4">
                    <button type="submit" class="btn btn-large btn-success">Buscar</button>
                </div><br>
            </form>
        </div> 
        </div>
    </div><br><br>
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->  
<!-- =#=#=#=#=#=#    CREACION DE LA TABLA PARA LOS FOLIOS FACTURADOS   =#=#=#=#=#= -->  
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->  
        <!--Tabs begin-->
<ul class="nav nav-tabs" id="myTab">
    <li class="active"><a data-toggle="tab" href="#Facturados">Folios Facturados</a></li>
    <li class=""><a data-toggle="tab" href="#NoFacturados">Folios No Facturados</a></li>
</ul>

<div class="tab-content">
    <div id="Facturados" class="tab-pane fade active in">   
        <div class="table-responsive">
            <table id="datos_Conciliacion" class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr align="center">
                        
                        <th align="center">Folio</th>
                        <th align="center">Nombre</th>
                        <th align="center">Fecha Ingreso</th>
                        <th align="center">Estatus</th>
                        <th align="center">Producto</th>
                        <th align="center">Frecuencia Pago</th>
                        <th align="center">Forma Pago</th>
                        <th align="center">Tarifa</th>
                        <th align="center">No. Factura</th>
                        <th align="center">Fecha Factura</th>
                        <th align="center">Monto Factura</th>
                        <th align="center">Total Pagado</th>
                    </tr>
                </thead>
                <tbody id="Contenido">
                <?php 
                    foreach ($datosConciliacion as $__datos_conciliacion){
                        if (!empty($__datos_conciliacion['tarifa'])) {

            //Formatear la tarifa a moneda nacional                
                            $tarifa_a_ocupar = substr($__datos_conciliacion['tarifa'], 0, -2);
                            $number_tarifa = $tarifa_a_ocupar;
                            setlocale(LC_MONETARY, 'en_US.UTF-8');
                            $__total_tarifa = money_format('%.2n', $number_tarifa);
                            $__tarifa_real = substr($__total_tarifa, 1);

            // Formatear el monto del a factura
                            $factura = $__datos_conciliacion['monto_factura'];
                            setlocale(LC_MONETARY, 'en_US.UTF-8');
                            $__total_factura = money_format('%.2n', $factura);
                            $__factura_real = substr($__total_factura, 1);

                        } // fin del if

                ?>
<!-- Rellenar la tabla -->
                    <tr <?=$RowColor?> >
                        <td><?= $__datos_conciliacion['folio'] ?></td>
                        <td><?= $__datos_conciliacion['NombreTitular'] ?></td>
                        <td><?= $__datos_conciliacion['fecha_ingreso'] ?></td>
                        <td><?= $__datos_conciliacion['estatus'] ?></td>
                        <td><?= $__datos_conciliacion['plan_producto'] ?></td>
                        <td><?= $__datos_conciliacion['frecuencia_pago'] ?></td>
                        <td><?= $__datos_conciliacion['forma_pago'] ?></td>    
                        <td align="right"><?= $__tarifa_real; ?></td>
                        <td><?= $__datos_conciliacion['num_factura'] ?></td>
                        <td><?= $__datos_conciliacion['fecha_factura'] ?></td>
                        <td align="right"><?= $__factura_real; ?></td>
                        <td align="right"><?= number_format($__datos_conciliacion['total_pagado'],2) ?></td>
                    </tr>
                <?php } // fin del foreach?>
                </tbody>
                            
            </table>
            </div>
    </div>
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->  
<!-- =#=#=#=#=#=    CREACION DE LA TABLA PARA LOS FOLIOS NO FACTURADOS   #=#=#=#=#= -->  
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->  

<!-- Creacion de las columnas de la tabla -->  
    <div id="NoFacturados" class="tab-pane fade active in">
        <div class="table-responsive">
            <table id="datosConciliacionSinFactura" class="table table-striped table-bordered table-condensed">
                <thead>
                    <tr align="center">
                        
                        <th align="center">Folio</th>
                        <th align="center">Nombre</th>
                        <th align="center">Fecha Ingreso</th>
                        <th align="center">Estatus</th>
                        <th align="center">Producto</th>
                        <th align="center">Frecuencia Pago</th>
                        <th align="center">Forma Pago</th>
                        <th align="center">Tarifa</th>
                        <th align="center">No. Factura</th>
                        <th align="center">Fecha Factura</th>
                        <th align="center">Monto Factura</th>
                        <th align="center">Total Pagado</th>
                    </tr>
                </thead>
                <tbody id="Contenido">
                <?php 
                    foreach ($datosConciliacionSinFactura as $__datos_conciliacion_sinfactura){

// Extraido de la base de datos
                        $fecha_inicial_db = $__datos_conciliacion_sinfactura['fecha_ingreso'];

// Extraido de la caja de texto
                        $fecha_inicial_seleccionada = $_POST['Fecha_Inicial']; 
                        $fecha_final_seleccionada = $_POST['Fecha_Final'];

// Dividir la fecha extraida de la base de datos en partes
                        $fecha_ingreso_db = explode('-', $fecha_inicial_db);

// Dividir las fechas extraidas de las cajas de texto en partes
                        $fechainicialseleccionada = explode('-', $fecha_inicial_seleccionada);
                        $fechafinalseleccionada = explode('-', $fecha_final_seleccionada);

// Sumar meses para poder validar los tipos de pagos semestrales
                        $semestral = $fechafinalseleccionada[1] + 6;
                        switch ($fechafinalseleccionada[1]) {
                            case 01: $semestral = 07; break;
                            case 02: $semestral = 08; break;
                            case 03: $semestral = 09; break;
                            case 04: $semestral = 10; break;
                            case 05: $semestral = 11; break;
                            case 06: $semestral = 12; break;
                            case 07: $semestral = 01; break;
                            case 08: $semestral = 02; break;
                            case 09: $semestral = 03; break;
                            case 10: $semestral = 04; break;
                            case 11: $semestral = 05; break;
                            case 12: $semestral = 06; break;
                                # code...
                                break;
                        }
/****** Validar los tipos de pagos *************************************/

                       if ($__datos_conciliacion_sinfactura['frecuencia_pago'] == "ANUAL") {
                            if ($fecha_ingreso_db[0] >= $fechainicialseleccionada[0] || $fecha_ingreso_db[1] != $fechainicialseleccionada[1]) {                              
                                continue;
                            }
                       }elseif ($__datos_conciliacion_sinfactura['frecuencia_pago'] == "ANUAL INSEN") {
                           if ($fecha_ingreso_db[0] >= $fechainicialseleccionada[0] || $fecha_ingreso_db[1] != $fechainicialseleccionada[1]) {                              
                                continue;
                            }
                       }elseif ($__datos_conciliacion_sinfactura['frecuencia_pago'] == "SEMESTRAL") {
                                if ($fecha_ingreso_db[0] >= $fechainicialseleccionada[0] || $fecha_ingreso_db[1] != $fechafinalseleccionada[1] || $fecha_ingreso_db[1] != $semestral) {
                                    continue;
                                }
                       }elseif ($__datos_conciliacion_sinfactura['frecuencia_pago'] == "MENSUAL") 
                            {
                                if ($fecha_ingreso_db[0] > $fechainicialseleccionada[0] || $fecha_ingreso_db[1] > $fechainicialseleccionada[1]) {
                                    continue;
                                }
                           
                       }
/****** Termina validacion de los tipos de pagos *************************************/
                       //echo "<pre>";print_r($semestral);
                ?>
<!-- Imprimir los datos en la tabla -->
                    <tr <?=$RowColor?> >
                        <td><?= $__datos_conciliacion_sinfactura['folio'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['NombreTitular'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['fecha_ingreso'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['estatus'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['plan_producto'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['frecuencia_pago'] ?></td>
                        <td><?= $__datos_conciliacion_sinfactura['forma_pago'] ?></td> 
                        <td><?= number_format($__datos_conciliacion_sinfactura['costo_total'],2) ?></td>
                        <td><font color="red">Sin Facturar</font></td>
                        <td><font color="red">N/A</font></td>
                        <td><font color="red">N/A</font></td>
                        <td><font color="red">N/A</font></td>
                    </tr>
                <?php } // fin del foreach      ?>
                </tbody>
                            
            </table>
            </div>
    </div>
</div>
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->
<!-- =#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#=#= -->
                        
        
</div>
