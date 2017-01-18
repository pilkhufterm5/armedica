<script type="text/javascript">
    $(document).on('ready',function() {

        $('#GSInvoiceDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $("#SearchFolio").click(function(event) {
            $("#URLInvoice").hide();
            $(".DelRow").remove();
            $("#GSConcepto2").val("");
            $("#GSImporte").val("");
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/DatosCobranza"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    GetData:{
                        Folio: $("#GSFolio").val(),
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {

                        $('#GSNombres').val(Response.Data.name);
                        $('#GSApellidos').val(Response.Data.apellidos);
                        $('#GSRazonSocial').val(Response.Data.name2);
                        $('#GSProducto').val(Response.Data.stockid);
                        $('#GSFrecPago').val(Response.Data.frecuencia);
                        $('#GSFormaPago').val(Response.Data.paymentname);
                        $('#GSConvenio').val(Response.Data.name2);
                        $('#GSEmpresa').val(Response.Data.empresa);
                        $("#GSIDMetPago").val(Response.Data.paymentid);
                        $("#GSIDFrecPago").val(Response.Data.IDFrecPago);
                        $("#GSCostoTotal").val(Response.Data.costo_total);
                        $("#GSConcepto").val("PAGO ADELANTADO-" + Response.Data.description + "-" + Response.Data.frecuencia + "-" + Response.Data.paymentname + "-MESES ");
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });

        $("#Process").click(function(event) {
            if($("#GSFolio").val() == ""){
                return false;
            }
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/Pagosadelantadosnuevo"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    ProcessData:{
                        Periodos: $('input:text.PeriodoArray').serialize(),
                        Folio: $("#GSFolio").val(),
                        Fecha: $("#GSInvoiceDate").val(),
                        Concepto: $("#GSConcepto2").val(),
                        Importe: $("#GSImporte").val(),
                        MetodoPago: $("#GSIDMetPago").val(),
                        FrecPago: $("#GSIDFrecPago").val(),
                        Producto: $("#GSProducto").val(),
                        Costo_Total: $("#GSCostoTotal").val(),
                        FrecPagoName: $("#GSFrecPago").val(),
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $("#URLInvoice").attr("href", Response.urlInvoice)
                        $("#URLInvoice").show();
                        /*Limpia los Campos*/
                        $(".DelRow").remove();
                        $("#GSConcepto2").val("");

                        $("#GSFolio").val("");
                        $("#GSNombres").val("");
                        $("#GSApellidos").val("");
                        $("#GSRazonSocial").val("");
                        $("#GSProducto").val("");
                        $("#GSFrecPago").val("");
                        $("#GSFormaPago").val("");
                        $("#GSConvenio").val("");
                        $("#GSEmpresa").val("");
                        $("#GSImporte").val("");
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });

        /**
         * Funcion para añadir una nueva columna en la tabla
         */
        $("#add").click(function(){
            // Obtenemos el numero de filas (td) que tiene la primera columna
            // (tr) del id "tabla"
            var tds=$("#tabla tr:first td").length;
            // Obtenemos el total de columnas (tr) del id "tabla"
            var TR_ID = $("#tabla tr").length;
            var nuevaFila="<tr id='row_" + TR_ID +"' class='DelRow' >";
            // Añadimos una columna con el numero total de columnas.
            IDMetPago = $("#GSIDMetPago").val();

           
            if(IDMetPago==9 || IDMetPago==10)
            {
                 DiaEmision = '25';
            }
            else
            {
                 DiaEmision = '01';
            }
            /*alert(DiaEmision);*/
            Mes = $("#GSMes").val();
            Anio = $("#GSAnio").val();

            FechaEmision = Anio + "-" + Mes + "-" + DiaEmision;

            if((Anio == <?=date('Y')?>) && (Mes <= <?=date('m')?>)){
                displayNotify('error', "No Puede Agregar un pago de un periodo Actual ó Anterior");
                return false;
            }

            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/Pagosadelantadosverificar"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                        fechaperiodo: FechaEmision,
                        folio: $("#GSFolio").val(),
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);

                        Monto = $("#GSCostoTotal").val();
                        Producto = $("#GSProducto").val();

                        Concepto = $("#GSConcepto").val();
                        //Concepto = "PAGO ADEL. SERV. " + Producto + " MESES "; //(08-2014)(09-2014)
                        Periodo = "(" + Mes + "-" + Anio + ")";
                        var NuevoConcepto = Concepto + Periodo;
                        $("#GSConcepto").val(NuevoConcepto);
                        $("#GSConcepto2").val($("#GSConcepto").val());

                        //Monto = 100;
                        
                        nuevaFila+= "<td><input type='text' value='" + FechaEmision + "' name='"+ TR_ID + "[fecha_linea]' readonly='readonly' class='PeriodoArray' ></td>";
                        nuevaFila+= "<td><input type='text' id='importe_id_"+ TR_ID + "' name='"+ TR_ID + "[importe_linea]' class='importe_linea PeriodoArray' value='" + Monto + "' readonly='readonly' > </td>";
                        nuevaFila+= "<td><a class='btn btn-success btn-delete' id='"+ TR_ID + "' Periodo='"+ Periodo + "' name='GSDel' onclick='DeleteRow(this)' ><i class='icon-remove icon-white'></i></a></td>";
                        nuevaFila+="</tr>";
                        $("#tabla").append(nuevaFila);

                        calcular_total();
                        
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });

            

        });

    });

    /**
    * Funcion para eliminar la ultima columna de la tabla.
    * Si unicamente queda una columna, esta no sera eliminada
    */
    function DeleteRow(obj){
        importe_total = $("#GSImporte").val();
        var row_id = $(obj).attr('id');
        var Periodo = $(obj).attr('Periodo');
        // Obtenemos el total de columnas (tr) del id "tabla"
        var trs=$("#tabla tr").length;
        if(trs>1){
            // Eliminamos la ultima columna
            importe_total = importe_total - eval($("#importe_id_" + row_id).val());

            ConceptoActual = $("#GSConcepto2").val();
            NuevoConcepto = ConceptoActual.replace(Periodo, '');
            $("#GSConcepto2").val(NuevoConcepto);
            $("#GSConcepto").val($("#GSConcepto2").val());

            $("#GSImporte").val(importe_total);
            $("#row_" + row_id).remove();
        }
    }

    function calcular_total() {
        importe_total = 0
        $(".importe_linea").each(
            function(index, value) {
                importe_total = importe_total + eval($(this).val());
            }
        );
        //alert(importe_total);
        $("#GSImporte").val(importe_total);
    }


</script>

<input type="hidden" id="GSIDFrecPago">
<input type="hidden" id="GSConcepto" value="">
<input type="hidden" id="GSCostoTotal">
<input type="hidden" id="GSIDMetPago">
<div style="height: 20px;"></div>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="form-legend"><h3>Pagos Adelantados</h3></div>
        <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a data-toggle="tab" href="#Lista">Datos Afiliado</a></li>
        </ul>
        <div class="tab-content"> <!-- Start TABS -->
            <div id="Lista" class="tab-pane fade active in">
                <div class="control-group row-fluid">
                    <div class="span6">
                        <div class="input-append controls">
                            <input class="span2" id="GSFolio" placeholder="Folio" type="text">
                            <button id="SearchFolio" class="btn btn-success" type="button" style="margin-top:5px;" >Buscar</button>
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span6">
                        <label class="control-label" >Nombres:</label>
                        <div class="controls">
                            <input type="text" id="GSNombres" name="Search[GSNombres]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                    <div class="span6">
                        <label class="control-label" >Apellidos:</label>
                        <div class="controls">
                            <input type="text" id="GSApellidos" name="Search[GSApellidos]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span12">
                        <label class="control-label" >Razón Social:</label>
                        <div class="controls">
                            <input type="text" id="GSRazonSocial" name="Search[GSRazonSocial]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span6">
                        <label class="control-label" >Producto:</label>
                        <div class="controls">
                            <input type="text" id="GSProducto" name="Search[GSProducto]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                    <div class="span6">
                        <label class="control-label" >Frec. Pago:</label>
                        <div class="controls">
                            <input type="text" id="GSFrecPago" name="Search[GSFrecPago]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span6">
                        <label class="control-label" >Forma Pago:</label>
                        <div class="controls">
                            <input type="text" id="GSFormaPago" name="Search[GSFormaPago]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                    <div class="span6">
                        <label class="control-label" >Convenio:</label>
                        <div class="controls">
                            <input type="text" id="GSConvenio" name="Search[GSConvenio]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                </div>

                <div class="control-group row-fluid">
                    <div class="span12">
                        <label class="control-label" >Empresa:</label>
                        <div class="controls">
                            <input type="text" id="GSEmpresa" name="Search[GSEmpresa]" class="span6" readonly="readonly" >
                        </div>
                    </div>
                </div>

                <div style="height:30px;"></div>

                <div class="control-group row-fluid">

                    <div class="span6">
                        <div class="noty_bar noty_theme_simpla noty_layout_topRight noty_success" id="noty_error_1405719528188" style="cursor: pointer; display: block;">
                            <div class="noty_message">
                                <span class="noty_text">Facturas Pagadas por Adelantado</span>
                            </div>
                        </div>

                        <div class="span12">
                            <div class="span4">
                                <label class="control-label" >Mes:</label>
                                <div class="controls">
                                    <select id="GSMes" name="Search[GSMes]" style="width: 100px;">
                                        <?php
                                        //$CurrentMonth = date("m") + 1;
                                        //FB::INFO($CurrentMonth,'_____________THIS');
                                        for($Mes = 1; $Mes<=12; $Mes++) {
                                            echo "<option value =" . str_pad($Mes,2,"0",STR_PAD_LEFT) . ">" . str_pad($Mes,2,"0",STR_PAD_LEFT) . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <script type="text/javascript">
                                        $("#GSMes option[value='<?php echo date('m'); ?>']").attr("selected",true);
                                    </script>
                                </div>
                            </div>

                            <div class="span4">
                                <label class="control-label" >Año:</label>
                                <div class="controls">
                                    <!--<input type="text" id="GSAnio" name="Search[GSAnio]" class="span6">-->
                                    <select id="GSAnio" name="Search[GSAnio]" style="width: 100px;">
                                        <?php
                                        for($year=(date("Y")+10); date("Y")<=$year; $year--) {
                                            echo "<option value =" . $year . ">" . $year . "</option>";
                                        }
                                        ?>
                                    </select>
                                    <script type="text/javascript">
                                        $("#GSAnio option[value='<?php echo date('Y'); ?>']").attr("selected",true);
                                    </script>
                                </div>
                            </div>

                            <div class="span4">
                                <label class="control-label" >&nbsp;</label>
                                <div class="controls">
                                    <input type="button" id="add" name="Search[GSAdd]" class="span6 btn btn-success" value="Agregar" >
                                </div>
                            </div>
                        </div>

                        <div class="span12">
                            <table id="tabla" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Emisión</th>
                                        <th>Monto</th>
                                        <th style="width:100px;"></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>


                    <div class="span6">
                        <div class="noty_bar noty_theme_simpla noty_layout_topRight noty_success" id="noty_error_1405719528188" style="cursor: pointer; display: block;">
                            <div class="noty_message">
                                <span class="noty_text">Facturación</span>
                            </div>
                        </div>

                        <div class="span12">
                            <div class="span6">
                                <label class="control-label" >Fecha:</label>
                                <div class="controls">
                                    <input type="text" id="GSInvoiceDate" name="Search[GSInvoiceDate]" value="<?=date("Y-m-d")?>" class="span6">
                                </div>
                            </div>

                            <div class="span6">
                                <label class="control-label" >Importe:</label>
                                <div class="controls">
                                    <input type="text" id="GSImporte" name="Search[GSImporte]" class="span6">
                                </div>
                            </div>
                        </div>

                        <div class="span12">
                            <div class="span10">
                                <label class="control-label" >Concepto:</label>
                                <div class="controls">
                                    <textarea id="GSConcepto2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="span12">
                            <div class="span4">
                                <input type="button" id="Process" value="Adelantar Pagos" class="btn btn-success ">
                            </div>
                            <div class="span4">
                                <a id="URLInvoice" target = "_blank" href="" title="Ver Factura PDF" ><input type="button" id="GSInvoice" value="Ver Factura PDF" class="btn btn-success "></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!-- End Tabs -->
    </div>
</div>
