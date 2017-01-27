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
.clearable{
  background:url(http://172.16.1.97/armedica/images/x.png) no-repeat right -10px center;
  border:1px solid #999;
  padding:3px 18px 3px 4px; /* USE the same right padding in jQ! */
  border-radius:3px;
  /*transition: background 0.4s; /*Remove this line if issues in Chrome (02.2014)*/
}
/* (jQ addClass:) if input has value: */
.clearable.x{
  background-position: right 5px center;
}
/* (jQ addClass:) if mouse is over the 'x' input area*/
.clearable.onX{
  cursor:pointer;
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
            "oTableTools": {
                "aButtons": [{
                    "sExtends": "pdf",
                    "sPdfOrientation": "landscape",
                    "sTitle": "Presupuesto de Cobranza (<?=date('Y-m-d') ?>) ",
                    "bShowAll": false
                }]
            },
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
        	if($(this).is(':checked'))
        		Revisar($(this).val(),1);
        	else
        		Revisar($(this).val(),0);

        	//setTimeout ("totales();", 5000);
        	totales();
        	//alert('hola');
            });

        $(".coment").blur(function(){
            if($(this).val()=='')
                return false;
            //alert($(this).attr('identificador')+'   ---  '+$(this).val())
        	ActualizaComentario($(this).attr('identificador'),$(this).val());

            });
        $('input[type=text][aria-controls="CustomerInquiry"]').on('keypress, blur, click, keyup, keydown' ,function () {
    		setTimeout(totales,100);
        });

        //$('input[type=text][aria-controls="CustomerInquiry"]').addClass('clearable');


    });

    function tog(v){return v?'addClass':'removeClass';}

    $(document).on('input', '.clearable', function(){
        $(this)[tog(this.value)]('x');
    }).on('mousemove', '.x', function( e ){
        $(this)[tog(this.offsetWidth-18 < e.clientX-this.getBoundingClientRect().left)]('onX');
    }).on('click', '.onX', function(){
        $(this).removeClass('x onX').val('');
        setTimeout(totales,100);
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


	function Revisar(id,estado){

	        if (confirm('¿Desea cambiar este estado?')) {
	            // Timbra la Factura
	            $.blockUI();
	            var jqxhr = $.ajax({
	                url: "<?php echo $this->createUrl("facturacion/Revisar"); ?>",
	                type: "POST",
	                dataType : "json",
	                timeout : (120 * 1000),
	                data: {
	                      GetInvoice:{
	                          id: id,
	                          estado: estado
	                      },
	                },
	                success : function(GetInvoice, newValue) {
	                    $.unblockUI();
	                    if(estado=='0')
		                    clss = '';
	                    else if(estado=='1')
	                    	clss = 'precapturado';

	                    if (GetInvoice.requestresult == 'ok') {
	                        displayNotify('success', GetInvoice.message);
	                        //$('#CustomerInquiry #'+id).removeClass('danger');
	                        $('#CustomerInquiry #'+id).removeClass('precapturado');
	                        $('#CustomerInquiry #'+id).addClass(clss);
	                        //totales();
	                    }
	                    //totales();
	                },
	                error : ajaxError
	            });//End Timbrado

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
//
		Total=0;
    	Cuantos = 0;
    	Total2=0;
    	Cuantos2 = 0
		Total3=0;
    	Cuantos3 = 0;
		Total4=0;
    	Cuantos4 = 0;
		Total5=0;
    	Cuantos5 = 0;



    	$(".csem").map(function(){
	    		Total+=parseFloat($(this).attr('valor'));
	    		if($(this).attr('valor')!="0")
	    		Cuantos++;
       	});

    	$(".c814").map(function(){
    		Total2+=parseFloat($(this).attr('valor'));
    		if($(this).attr('valor')!="0")
    		Cuantos2++;
   		});

    	$(".c1521").map(function(){
    		Total3+=parseFloat($(this).attr('valor'));
    		if($(this).attr('valor')!="0")
    		Cuantos3++;
   		});

    	$(".c2230").map(function(){
    		Total4+=parseFloat($(this).attr('valor'));
    		if($(this).attr('valor')!="0")
    		Cuantos4++;
   		});

    	$(".c30").map(function(){
    		Total5+=parseFloat($(this).attr('valor'));
    		if($(this).attr('valor')!="0")
    		Cuantos5++;
   		});


        TotalCuantos = parseFloat(Cuantos) + parseFloat(Cuantos2) + parseFloat(Cuantos3) + parseFloat(Cuantos4) + parseFloat(Cuantos5);
        TotalTotal = parseFloat(Total) + parseFloat(Total2) + parseFloat(Total3) + parseFloat(Total4) + parseFloat(Total5);


//divCuantasS1
    	$( "#divCuantasS1" ).text( Cuantos );
    	$( "#divTotalesS1" ).text( "$ "+number_format(Total,2,'.',','));

    	$( "#divCuantasS2" ).text( Cuantos2);
    	$( "#divTotalesS2" ).text( "$ "+number_format(Total2,2,'.',','));

    	$( "#divCuantasS3" ).text( Cuantos3 );
    	$( "#divTotalesS3" ).text( "$ "+number_format(Total3,2,'.',','));

    	$( "#divCuantasS4" ).text( Cuantos4 );
    	$( "#divTotalesS4" ).text( "$ "+number_format(Total4,2,'.',','));

    	$( "#divCuantasS5" ).text( Cuantos5 );
    	$( "#divTotalesS5" ).text( "$ "+number_format(Total5,2,'.',','));

    	$( "#divTotalesC" ).text( TotalCuantos);
    	$( "#divTotalesT" ).text( "$ "+number_format(TotalTotal,2,'.',','));


		}

	function ActualizaComentario(id,comentario){
        //if (confirm('¿Desea cambiar este estado?')) {
            $.blockUI();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("facturacion/ComentarioCobranza"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetInvoice:{
                    	  id: id,
                    	  comentario: comentario
                      },
                },
                success : function(GetInvoice, newValue) {
                    $.unblockUI();

                    if (GetInvoice.requestresult == 'ok') {
                        displayNotify('precapturado', GetInvoice.message);

                    }else{
                        displayNotify('alert', GetInvoice.message);
                    }
                },
                error : ajaxError
            });
        //}
    }


</script>


<div class="container-fluid">
    <form method="POST" action="<?php echo $this->createUrl("facturacion/presupuestocobranza/"); ?>" >
        <table>
            <tr>
                <td colspan = '2'><b>Fecha de Facturas</b></td>
                <td colspan='2'><b>Filtros</b></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <label>Fecha de:</label>
                    <input id="TransAfterDate" name="TransAfterDate" type="text" value="<?=$_POST['TransAfterDate']?>" class="span8" />
                </td>
                <td>
                    <label>Fecha a:</label>
                    <input id="TransBeforeDate" name="TransBeforeDate" type="text" value="<?=$_POST['TransBeforeDate']?>" class="span8" />
                </td>

                <td>
                    <?php
                    $arrayDia= array("2"=>"Lunes","3"=>"Martes","4"=>"Mi&eacute;rcoles","5"=>"Jueves","6"=>"Viernes");
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
    </form>

        <table id="CustomerInquiry" class="table table-hover table-condensed table-striped" style="width: 100%;" >
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Tipo</th>
                    <th>Nombre</th>
                    <th>Fecha Factura</th>
                    <th>Factura</th>
                    <th>Fecha Revisi&oacute;n</th>
                    <th>Dia de Cobro</th>
                    <th>Impte. Fact.<br />Sem(1-7)</th>
                    <th>Impte. Fact.<br />Sem(8-14)</th>
                    <th>Impte. Fact.<br />Sem(15-21)</th>
                    <th>Impte. Fact.<br />Sem(22-30)</th>
                    <th>Impte. Fact.<br />Sem(>30)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $arrayTotales['entregadas']=0;
                    $arrayTotales['entregadasCuantas']=0;
                    $arrayTotales['noEntregadas']=0;
                    $arrayTotales['noEntregadasCuantas']=0;
                    $RowColor = "class= 'precapturado'";

                    //FB::INFO(date("w"), '_______________________________Dia de la Semana');
                    //FB::INFO(date('Y-m-d',strtotime('next monday')), '_______________________________Dia de la Semana');
                    while ($Data = DB_fetch_assoc($InvoiceData)) {
                    //foreach($InvoiceData as $Data){
                        $checked = " ";
                        if($Data['pagado']=='0'){
                            $arrayTotales['noEntregadas']+=$Data['TotalInvoice'];
                            $arrayTotales['noEntregadasCuantas']++;
                            $RowColor = "";
                        }elseif($Data['pagado']=='1'){
                            $arrayTotales['entregadas']+=$Data['TotalInvoice'];
                            $arrayTotales['entregadasCuantas']++;
                            $RowColor = "class= 'precapturado'";
                            $checked = " checked = 'checked' ";
                        }
                        //$RowColor = "class= 'warning'";
                        //$RowColor = "class= 'success'";

                        if($Data['dias_cobro']=="Por Dia")
                            $dia=$arrayDia[$Data['dias_cobro_dia']];
                        if($Data['dias_cobro']=="Por Numero")
                            $dia = $Data['dias_cobro_dia'];
                        $Data['TotalInvoice']=($Data['TotalInvoice']=='')?0:$Data['TotalInvoice'];

                        $DiaSemana = array(
                                0 => 'sunday',
                                1 => 'monday',
                                2 => 'tuesday',
                                3 => 'wednesday',
                                4 => 'thursday',
                                5 => 'friday',
                                6 => 'saturday');

                        if($Data['dias_cobro']=='Por Dia'){
                            $PorDia = $DiaSemana[$Data['dias_cobro_dia']];
                            $Data['dias_cobro_dia'] = date('d',strtotime('next ' . $PorDia));
                            $Data['dias_cobro'] = 'Por Numero';
                        }
                        //FB::INFO($Data,'_____________________________________DATA');
                ?>
                    <tr <?=$RowColor?> class id="<?=$Data['id']?>" >
                        <td><?=$Data['folio']?></td>
                        <td><?=$Data['FATipo']?></td>
                        <td><?=$Data['TName'] . " " .  $Data['TApellidos']?></td>
                        <td><?=$Data['FechaFactura']?></td>
                        <td><?=$Data['transno']?></td>
                        <td><?=$Data['fecha_revisado']?></td>
                        <td><?=$dia?></td>
                        <td class='csem'
                            <?=($Data['dias_cobro']=='Por Numero' and $Data['dias_cobro_dia']>=1 and $Data['dias_cobro_dia']<=7 )?" valor='".$Data['TotalInvoice']."'>$ ".number_format($Data['TotalInvoice'],2):" valor='0'>0";?>
                        </td>

                        <td class='c814'
                            <?=($Data['dias_cobro']=='Por Numero' and $Data['dias_cobro_dia']>=8 and $Data['dias_cobro_dia']<=14 )?" valor='".$Data['TotalInvoice']."'>$ ".number_format($Data['TotalInvoice'],2):" valor='0'>0";?>
                        </td>

                        <td class='c1521'
                            <?=($Data['dias_cobro']=='Por Numero' and $Data['dias_cobro_dia']>=15 and $Data['dias_cobro_dia']<=21 )?" valor='".$Data['TotalInvoice']."'>$ ".number_format($Data['TotalInvoice'],2):" valor='0'>0";?>
                        </td>

                        <td class='c2230'
                            <?=($Data['dias_cobro']=='Por Numero' and $Data['dias_cobro_dia']>=22 and $Data['dias_cobro_dia']<=30 )?" valor='".$Data['TotalInvoice']."'>$ ".number_format($Data['TotalInvoice'],2):" valor='0'>0";?>
                        </td>

                        <td class='c30'
                            <?=($Data['dias_cobro']=='Por Numero' and $Data['dias_cobro_dia']>30)?" valor='".$Data['TotalInvoice']."'>$ ".number_format($Data['TotalInvoice'],2):" valor='0'>0";?>
                        </td>
                </tr>
                <?php }
                $total= $arrayTotales['entregadas']+$arrayTotales['noEntregadas'];
                $totalCuantas= $arrayTotales['entregadasCuantas']+$arrayTotales['noEntregadasCuantas'];
                ?>
            </tbody>
        </table>

        <table class="table table-hover table-condensed table-striped" style="float: 0;" >
            <tr>
                <td>PRONOSTICO</td>
                <td><strong>Cantidad</strong></td>
                <td><strong>Monto</strong></td>
            </tr>
            <tr >
                <td><strong>Monto Semana 1: </strong></td>
                <td><div id='divCuantasS1'><?=$arrayTotales['noEntregadasCuantas']?></div></strong></td>
                <td><strong><div id='divTotalesS1'>$<?=number_format($arrayTotales['noEntregadas'],2)?></div></strong></td>
            </tr>
            <tr >
                <td><strong>Monto Semana 2: </strong></td>
                <td><div id='divCuantasS2'><strong><?=$arrayTotales['entregadasCuantas']?></strong></div></td>
                <td><strong><div id='divTotalesS2'>$<?=number_format($arrayTotales['entregadas'],2)?></div></strong></td>
            </tr>
            <tr >
                <td><strong>Monto Semana 3: </strong></td>
                <td><div id='divCuantasS3'><strong><?=$arrayTotales['entregadasCuantas']?></strong></div></td>
                <td><strong><div id='divTotalesS3'>$<?=number_format($arrayTotales['entregadas'],2)?></div></strong></td>
            </tr>
            <tr >
                <td><strong>Monto Semana 4: </strong></td>
                <td><div id='divCuantasS4'><strong><?=$arrayTotales['entregadasCuantas']?></strong></div></td>
                <td><strong><div id='divTotalesS4'>$<?=number_format($arrayTotales['entregadas'],2)?></div></strong></td>
            </tr>
            <tr >
                <td><strong>Monto Semana 5: </strong></td>
                <td><div id='divCuantasS5'><strong><?=$arrayTotales['entregadasCuantas']?></strong></div></td>
                <td><strong><div id='divTotalesS5'>$<?=number_format($arrayTotales['entregadas'],2)?></div></strong></td>
            </tr>
            <tr >
                <td>Totales: </td>
                <td id='divTotalesC'><strong><?=$totalCuantas?></strong></td>
                <td><strong><div id='divTotalesT'>$<?=number_format($total,2)?></div></strong></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <a id="dlink"  style="display:none;"></a><input type="button" class="btn btn-small" onclick="tableToExcel('CustomerInquiry', 'coments', 'FacturasRevision.xls' )" value="Exportar a Excel" />
                </td>
            </tr>
        </table>

    </div>

</div>
