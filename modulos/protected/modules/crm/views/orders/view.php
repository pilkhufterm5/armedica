<script>
    $(document).on('ready', function(){

        calcular_subtotal();
        impuesto_total();
        importe_total();

        $("#convertir").click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("orders/convertir",array('orderno'=>$_REQUEST[orderno])); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });

        $("#add").click(function(){
            // Obtenemos el numero de filas (td) que tiene la primera columna
            // (tr) del id "tabla"
            var tds=$("#tabla tr:first td").length;
            // Obtenemos el total de columnas (tr) del id "tabla"
            var TR_ID = $("#tabla tr").length;
            var nuevaFila="<tr id='row_" + TR_ID +"' class='DelRow' >";
            // Añadimos una columna con el numero total de columnas.

            nuevaFila+= "<td><input type='text' value='' row_id='"+ TR_ID + "' name='"+ TR_ID + "[product]'  class='autoc ItemArray' ><input type='hidden' value=''  name='"+ TR_ID + "[stockid]' class='ItemArray'></td>";
            nuevaFila+= "<td><input type='text' value='' row_id='"+ TR_ID + "' name='"+ TR_ID + "[qty]'  class='ItemArray cantidades' onchange='calcularImporte("+ TR_ID +")'></td>";
            nuevaFila+= "<td><input type='text' value='' name='"+ TR_ID + "[units]'  class='ItemArray' ></td>";
            nuevaFila+= "<td><input type='text' value='' real_value='' name='"+ TR_ID + "[unitprice]'  class='ItemArray cantidades' ></td>";
            nuevaFila+= "<td><input type='text' value='' real_value='' name='"+ TR_ID + "[importe]'  class='ItemArray cantidades importe' ><input type='hidden' value='' name='"+ TR_ID + "[impuesto]'  class='impuesto' ></td>";
            nuevaFila+= "<td><a style='' class='btn-delete' id='"+ TR_ID + "' Periodo='' name='GSDel' onclick='DeleteRow(this)' ><i class='fi-minus-circle' style='font-size: 24px;'></i></a></td>";
            nuevaFila+="</tr>";
            $("#tabla").append(nuevaFila);
            CreateAutoComplete();

        });

        $("#Process").click(function(event) {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("orders/updatequotation",array('orderno'=>$_REQUEST[orderno])); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    ProcessData:{
                        Items: $('.ItemArray').serialize(),
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        });
    });

    function number_format(number, decimals, dec_point, thousands_sep) {
        //number_format(67000, 5, ',', '.');
        //returns 8: '67.000,00000'
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
            .toFixed(prec);
        };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

     function CreateAutoComplete(){
        window.AutoComplete={
            select: function( event, ui ) {
                subtotal_real=ui.item.precio*1;
                subtotal= number_format(subtotal_real, 2, '.', ',');
                precio=number_format(ui.item.precio, 2, '.', ',');
                inputOriginal=$(event.target);
                RowId = inputOriginal.attr('row_id');
                $("input[name='"+ RowId +"[product]']").val(ui.item.value);
                $("input[name='"+ RowId +"[stockid]']").val(ui.item.stockid);
                $("input[name='"+ RowId +"[qty]']").val(1);
                $("input[name='"+ RowId +"[units]']").val(ui.item.unidad);
                $("input[name='"+ RowId +"[unitprice]']").val(precio);
                $("input[name='"+ RowId +"[unitprice]']").attr('real_value', ui.item.precio);
                $("input[name='"+ RowId +"[importe]']").val(subtotal);
                $("input[name='"+ RowId +"[importe]']").attr('real_value', subtotal_real);
                $("input[name='"+ RowId +"[impuesto]']").val(ui.item.impuesto);
                //actualizarCarrito(inputOriginal);
                calcular_subtotal();
                impuesto_total();
                importe_total();
                console.log(ui.item);
                return ui.item.value;
            },
            source: function( request, response ) {
                $.ajax({
                    url: "<?php echo $this->createUrl("orders/Search",array('id'=>$_REQUEST[id])); ?>",
                    dataType: "jsonp",
                    data: {
                        Search:{
                            //cotizar_plan: $("#cotizar_plan").attr("checked") ? 1 : 0,
                            Items: $('.ItemArray').serialize(),
                            descripcion: request.term
                        },
                    }
                    }).complete( function( data ) {
                        Response={};
                        if(data.readyState==4){
                            if (typeof(data.responseText)!='undefined')
                                Response=eval('('+data.responseText+')');
                            if (typeof(Response.requestresult)!='undefined'&&Response.requestresult == 'ok') {

                                response(Response.DataList);

                            }else{
                                //displayNotify('alert', Response);
                            }
                        }
                    });
            }
        };
        $(".autoc").autocomplete(window.AutoComplete);
    }

    function DeleteRow(obj){
        var row_id = $(obj).attr('id');
        // Obtenemos el total de columnas (tr) del id "tabla"
        var trs=$("#tabla tr").length;
        if(trs>1){
            // Eliminamos la ultima columna
            $("#row_" + row_id).remove();
        }
        calcular_subtotal();
        impuesto_total();
        importe_total();
    }

     function calcular_subtotal() {
        var importe_total = 0
        $(".importe").each(
            function(index, value) {
                total=parseFloat($(this).attr('real_value'));
                if(isNaN(total))total=0;
                importe_total = importe_total + total;
            }
        );
        $("#ImporteTotal").text(number_format(importe_total, 2, '.', ','));
        $("#ImporteReal").val(importe_total);
    }

    function impuesto_total() {
        var impuesto_total = 0
        $(".impuesto").each(
            function(index, value) {
                total=parseFloat($(this).val());
                if(isNaN(total))total=0;
                impuesto_total = impuesto_total + total;
            }
        );
        $("#ImpuestoTotal").text(number_format(impuesto_total, 2, '.', ','));
        $("#ImpuestoReal").val(impuesto_total);
    }

    function importe_total() {
        var total=0;
        var subtotal= parseFloat($("#ImporteReal").val());
        var iva= parseFloat($("#ImpuestoReal").val());
        if(isNaN(iva))iva=0;
        if(isNaN(subtotal))subtotal=0;
        total = (iva) + (subtotal);
        $("#Total").text(number_format(total, 2, '.', ','));
        $("#TotalReal").val(total);
    }

    function calcularImporte(tr_id){
        QTY = $("input[name='"+tr_id+"[qty]']").val();
        Precio = $("input[name='"+tr_id+"[unitprice]']").attr('real_value');
        stockid = $("input[name='"+tr_id+"[stockid]']").val();
        Subtotal = parseFloat(QTY) * parseFloat(Precio);
        Subtotal2= number_format(Subtotal, 2, '.', ',');

          var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("orders/gettaxes",array('id'=>$_REQUEST[id]))?>",
            type: "POST",
            dataType : "json",
            data: {
                Search:{
                   qty : QTY,
                   precio : Precio,
                   stockid : stockid,
                },
            },
            success : function(Response, newValue) {
            if (Response.requestresult == 'ok') {
                //displayNotify('success', Response.message);
                $("input[name='"+tr_id+"[importe]']").attr('real_value', Subtotal);
                $("input[name='"+tr_id+"[importe]']").val(Subtotal2);
                $("input[name='"+tr_id+"[impuesto]']").val(Response.impuesto);
                calcular_subtotal();
                impuesto_total();
                importe_total();
            }else{
                displayNotify('error', Response.message);
            }
            },
            error : ajaxError
        });

    }

    function eliminar(tr_id){
        stockid = $("input[name='"+tr_id+"[stockid]']").val();
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("orders/eliminar",array('orderno'=>$_REQUEST[orderno]))?>",
            type: "POST",
            dataType : "json",
            data: {
                Delete:{
                   stockid : stockid,
                },
            },
            success : function(Response, newValue) {
            if (Response.requestresult == 'ok') {
                displayNotify('success', Response.message);
                $("#row_" + tr_id).remove();
                calcular_subtotal();
                impuesto_total();
                importe_total();
            }else{
                displayNotify('error', Response.message);
            }
            },
            error : ajaxError
        });
    }
</script>

<?php FB::INFO($OrderDetails, 'order details ');
?>

<h1><a>Editar Cotización No. <?=$OrderDetails[0]['orderno']?></a></h1>
        <hr/>

    <div class="large-12 small-12 columns">
        <label class="control-label" >&nbsp;</label>
        <div class="controls">
            <input type="button" id="add" name="Search[GSAdd]" class="btn btn-success" value="Agregar producto" >
            <input type="button" id="convertir" class="btn btn-danger" value="Convertir a pedido" >
            <a class="btn btn-primary" target="_blank" href="<?=$rootpath?>/armedica/rh_PrintCustOrder.php?&TransNo=<?=$_REQUEST['orderno']?>">Ver PDF</a>
        </div>
    </div>

    <div class="large-12 small-12 columns">
        <table id="tabla" style="width:100%;">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                    <th>Precio</th>
                    <th>Total</th>
                    <th style="width:100px;"></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i=1;
                foreach ($OrderDetails as $Details) {
                    $total= $Details['quantity']*$Details['unitprice'];
                    $impuesto = $this->getTaxTotalForItem($Details['branchcode'], $Details['debtorno'], $Details['stkcode'], $Details['unitprice'], $Details['quantity']);
                ?>
                    <tr id = "row_<?=$i?>">
                        <td><input value="<?=$Details['stkcode']."--".$Details['description']?>" name="<?=$i?>[product]" type="text" readonly="readonly" class="ItemArray"><input type='hidden' value='<?=$Details['stkcode']?>'  name='<?=$i?>[stockid]' class='ItemArray'></td>
                        <td><input value="<?=$Details['quantity']?>" name="<?=$i?>[qty]" type="text" class="ItemArray cantidades"  onchange='calcularImporte(<?=$i?>)'></td>
                        <td><input value="<?=$Details['units']?>" name="<?=$i?>[units]" type="text" readonly="readonly" class="ItemArray"></td>
                        <td><input value="<?=number_format($Details['unitprice'], 2)?>" name="<?=$i?>[unitprice]" real_value="<?=$Details['unitprice']?>" type="text" class="ItemArray cantidades" readonly="readonly"></td>
                        <td>
                            <input value="<?=number_format($total, 2)?>" name="<?=$i?>[importe]" real_value="<?=$total?>" type="text" class="ItemArray cantidades importe" readonly="readonly">
                            <input type='hidden' value='<?=$impuesto?>' name="<?=$i?>[impuesto]" class='impuesto' >
                        </td>
                        <td><a style='' class='eliminar' name='eliminar' onclick='eliminar(<?=$i?>)' ><i class='fi-minus-circle' style='font-size: 24px;'></i></a></td>
                    </tr>
                <?php
                    $i++;
                } ?>
            </tbody>
            <tfoot>
                 <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="cantidades">Subtotal:</td>
                    <td class="cantidades">
                        $ <span  class="cantidades" id="ImporteTotal">0</span>
                        <input type="hidden" id="ImporteReal">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="cantidades">Impuesto:</td>
                    <td class="cantidades">
                        $ <span id="ImpuestoTotal">0</span>
                        <input type="hidden" id="ImpuestoReal">
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="cantidades">Total:</td>
                    <td class="cantidades">
                        $ <span id="Total">0</span>
                        <input type="hidden" id="TotalReal">
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <input type="button" id="Process" class="btn-success btn" value="Guardar" >
    </div>
