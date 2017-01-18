<style>
    .negro{
        color:black;
    }
    .amarillo{
        color:yellow;
    }
    .rojo{
        color:red;
    }
    .alnumbers{
        text-align: right;
    }
</style>
<script type="text/javascript">
    $(document).on('ready',function() {
        $('#CustomerInquiry').dataTable( {
            "sPaginationType": "bootstrap",
            "bPaginate": false,
            "sDom": 'T<"clear">lfrtip',
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
        $("#StatusFactura").select2();
        $("#TipoFactura").select2();

        $('#TransAfterDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });

        $('#TransBeforeDate').datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+5'
        });


        $( "input[type=checkbox]" ).click(function(){
            if($(this).is(':checked')){
                Revisar($(this).val(),1, $(this).attr("idDebtorTrans"));
            }else{
                Revisar($(this).val(),0, $(this).attr("idDebtorTrans"));
            }
            totales();
        });

        $(".coment").blur(function(){
            if($(this).val()==''){
                return false;
            }
            ActualizaComentario($(this).attr('identificador'),$(this).val(), $(this).attr("idDebtorTrans"));
        });

        $('input[type=text][aria-controls="CustomerInquiry"]').on('keypress, blur, click, keyup, keydown' ,function () {
            setTimeout(totales,100);
        });
    });



    var tableToExcell = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,', template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>', base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        }, format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        }
        return function(table, name) {
            if (!table.nodeType)
                table = document.getElementById(table)
            var ctx = {
                worksheet : name || 'Worksheet',
                table : table.innerHTML
            }
            window.location.href = uri + base64(format(template, ctx))
        }
    })()

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,'
        , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
        , base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) }
        , format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
        return function (table, name, filename) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }

            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = filename;
            document.getElementById("dlink").click();

        }
        window.location.href = uri + base64(format(template, ctx))
    })()



    function Revisar(id,estado,idDebtorTrans){

        if (confirm('¿Desea cambiar este estado?')) {

            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/Revisar"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    GetInvoice:{
                        idDebtorTrans: idDebtorTrans,
                        estado: estado
                    },
                },
                success : function(GetInvoice, newValue) {
                    if(estado=='0'){
                        clss = '';
                        $("#FechaRID_" + GetInvoice.id).text("");
                        ActualizaComentario(GetInvoice.id," ",GetInvoice.id)
                        $("#coment_" + GetInvoice.id).val("");
                    }
                    else if(estado=='1'){
                        clss = 'precapturado';
                        $("#FechaRID_" + GetInvoice.id).text(GetInvoice.FechaRevision);
                    }

                    if (GetInvoice.requestresult == 'ok') {
                        displayNotify('success', GetInvoice.message);
                        $('#CustomerInquiry #'+id).removeClass('precapturado');
                        $('#CustomerInquiry #'+id).addClass(clss);
                    }
                },
                error : ajaxError
            });

        }
    }

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

    function totales(){
        Total=0;
        Cuantos = 0;
        $('input[type=checkbox]:not(:checked)').closest('tr').find('td[entregada]').map(function(){
            Total+=parseFloat($(this).attr('valor'));
            Cuantos++;
        });

        Total2=0;
        Cuantos2 = 0
        $('input[type=checkbox]:checked').closest('tr').find('td[entregada]').map(function(){
            Total2+=parseFloat($(this).attr('valor'));
            Cuantos2++;
        });

        TotalCuantos = parseFloat(Cuantos) + parseFloat(Cuantos2);
        TotalTotal = parseFloat(Total) + parseFloat(Total2);

        $( "#divNoEntregadoC" ).html( "<strong>No Entregadas: "+Cuantos+"</strong>" );
        $( "#divNoEntregadoT" ).text( "$ "+number_format(Total,2,'.',','));
        $( "#divEntregadoC" ).html( "<strong>Entregadas: "+Cuantos2+"</strong>");
        $( "#divEntregadoT" ).text( "$ "+number_format(Total2,2,'.',','));
        $( "#divTotalesC" ).html( "<strong>Totales: "+TotalCuantos+"</strong>");
        $( "#divTotalesT" ).text( "$ "+number_format(TotalTotal,2,'.',','));
    }

    function ActualizaComentario(id,comentario,idDebtorTrans){

        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("facturacion/Comentario"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                GetInvoice:{
                    idDebtorTrans: idDebtorTrans,
                    comentario: comentario
                },
            },
            success : function(GetInvoice, newValue) {
                if (GetInvoice.requestresult == 'ok') {
                    displayNotify('success', GetInvoice.message);
                }else{
                    displayNotify('error', GetInvoice.message);
                }
            },
            error : ajaxError
        });
    }


</script>

<div style="width: 100%; ">

    <form method="POST" action="<?php echo $this->createUrl("facturacion/revisionfactura/"); ?>" >
        <center>
        <table>
        <tr><td colspan = '2'><center><b>Fecha de Facturas</b></center></td><td colspan='2'><center><b>Filtros</b></center></td><td></td></tr>
            <tr>
                <td><label>Fecha de:</label>
                    <input id="TransAfterDate" name="TransAfterDate" type="text" value="<?=$_POST['TransAfterDate']?>" class="span8" />
                </td>
                <td>
                    <label>Fecha a:</label>
                    <input id="TransBeforeDate" name="TransBeforeDate" type="text" value="<?=$_POST['TransBeforeDate']?>" class="span8" />
                </td>

                <td>
                <?php
                $arrayDia= array("1"=>"Lunes","2"=>"Martes","3"=>"Mi&eacute;rcoles","4"=>"Jueves","5"=>"Viernes");
                echo '<label>D&iacute;a seleccionado:</label>
					<select name="porDia" id ="porDia">';
                echo "<option value=''>Seleccione un dia</option>";
                foreach ($arrayDia as $key => $value) {
					$selected ="";
					if($_POST['porDia']==$key)
						$selected = " selected='selected' ";
                	echo "<option {$selected} value='{$key}'>{$value}</option>";
                }
                echo "</select>";
                ?>
                </td>
                 <td>
                 <label>N&uacute;mero seleccionado:</label>
                  <input id="porNumero" name="porNumero" type="text" value="<?=$_POST['porNumero']?>" class="span8" />
                </td>
                 <td>
                    <input type="submit" name="Search" value="Buscar" class="btn btn-small btn-success" style=" height: 30px; margin-top: 18px;" />
                </td>
            </tr>
        </table>
        </center>
    </form>
        <table id="CustomerInquiry" class="table table-hover table-condensed table-striped table-bordered" style="float: 0;" >
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Fecha Factura</th>
                    <th>Factura</th>
                    <th>Total</th>
                    <th>Fecha Revisi&oacute;n</th>
                    <th>Dia</th>
                    <th>Status</th>
                    <th>Cobrador</th>
                    <th>Comentario</th>
                    <th>Revisada</th>
                </tr>
            </thead>
            <tbody>
                <?php
                	$arrayTotales['entregadas']=0;
                	$arrayTotales['entregadasCuantas']=0;
                	$arrayTotales['noEntregadas']=0;
                	$arrayTotales['noEntregadasCuantas']=0;

                	$RowColor = "class= 'precapturado'";

                	//foreach($InvoiceData as $Data)
                    while ($Data = DB_fetch_assoc($InvoiceData)) {
					$checked = " ";
					if($Data['revisado']=='0'){
						$arrayTotales['noEntregadas']+=$Data['TotalInvoice'];
						$arrayTotales['noEntregadasCuantas']++;
						$RowColor = "";
						}
					elseif($Data['revisado']=='1'){
						$arrayTotales['entregadas']+=$Data['TotalInvoice'];
						$arrayTotales['entregadasCuantas']++;
						$RowColor = "class= 'precapturado'";
						$checked = " checked = 'checked' ";
						}

                    if($Data['dias_revision']=="Por Dia")
                    	$dia=$arrayDia[$Data['dias_revision_dia']];
                    if($Data['dias_revision']=="Por Numero")
                    	$dia = $Data['dias_revision_dia'];

                    $Data['TotalInvoice']=($Data['TotalInvoice']=='')?0:$Data['TotalInvoice'];

                    if($Data['fecha_revisado'] == "0000-00-00 00:00:00"){
                        $Data['fecha_revisado'] = "";
                    }
                ?>
                <tr <?=$RowColor?> class id="<?=$Data['idDebtorTrans']?>" >
                    <td><?=$Data['TFolio']?></td>
                    <td><?=$Data['FATipo']?></td>
                    <td><?=$Data['TName'] . " " .  $Data['TApellidos']?></td>
	                <td><?=$Data['FechaFactura']?></td>
	                <td><?=$Data['FolioFactura']?></td>
                    <td style="text-align: right;" entregada='<?=$Data['revisado']?>' valor='<?=$Data['TotalInvoice']?>'>$ <?=number_format($Data['TotalInvoice'],2)?></td>
                    <td id="FechaRID_<?=$Data['idDebtorTrans']?>"><?=$Data['fecha_revisado']?></td>
                    <td class="alnumbers"><?=$dia?></td>
                    <td><?=($Data['revisado']==0)?"No entregado":"Entregado";?></td>
	                <td><?=$Data['cobradorName']?></td>
	                <td><textarea id="coment_<?=$Data['idDebtorTrans']?>" name='comentarios' class='coment' rows='1' identificador='<?=$Data['id']?>'  type='textarea' idDebtorTrans="<?=$Data['idDebtorTrans']?>" /><?=$Data['comentarios']?></textarea></td>
	                <td ><input name='revisado[<?=$Data['idDebtorTrans']?>]' <?=$checked?> class='chkb'  type='checkbox' value='<?=$Data['idDebtorTrans']?>' idDebtorTrans="<?=$Data['idDebtorTrans']?>" /></td>
                </tr>
                <?php }

                $total= $arrayTotales['entregadas']+$arrayTotales['noEntregadas'];
                $totalCuantas= $arrayTotales['entregadasCuantas']+$arrayTotales['noEntregadasCuantas'];
                ?>
            </tbody>
            </table>
            <table class="table table-hover table-condensed table-striped" style="float: 0;" >
                <tr>
                    <td></td>
                    <td><strong>Cantidad</strong></td>
                    <td><strong>Monto</strong></td>
                </tr>
                <tr >
                    <td><strong>No Entregadas: </strong></td>
                    <td><strong><div id='divNoEntregadoC'><?=$arrayTotales['noEntregadasCuantas']?></div></strong></td>
                    <td><strong><div id='divNoEntregadoT'>$<?=number_format($arrayTotales['noEntregadas'],2)?></div></strong></td>
                </tr>
                <tr >
                    <td><strong>Entregadas: </strong></td>
                    <td><div id='divEntregadoC'><strong><?=$arrayTotales['entregadasCuantas']?></strong></div></td>
                    <td><strong><div id='divEntregadoT'>$<?=number_format($arrayTotales['entregadas'],2)?></div></strong></td>
                </tr>
                <tr >
                    <td>Totales: </td>
                    <td id='divTotalesC'><strong>Totales: <?=$totalCuantas?></strong></td>
                    <td><strong><div id='divTotalesT'>$<?=number_format($total,2)?></div></strong></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><a id="dlink"  style="display:none;"></a><input type="button" class="btn btn-small btn-success" onclick="tableToExcel('CustomerInquiry', 'coments', 'FacturasRevision.xls' )" value="Exportar a Excel" /></td>
                </tr>
        </table>

</div>
