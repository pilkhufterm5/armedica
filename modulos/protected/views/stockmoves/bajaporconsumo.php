<?php
global $rootpath; 
?>
<style>
.form-horizontal input, .form-horizontal textarea, .form-horizontal select, .form-horizontal .help-inline, .form-horizontal .uneditable-input, .form-horizontal .input-prepend, .form-horizontal .input-append {
    display: block;
    *display: inline;
    *zoom: 1;
    /*margin-bottom: -30px;*/
    margin-bottom: 5px;
    vertical-align: middle
}
</style>

<script type="text/javascript" charset="UTF-8" >
    $(document).on('ready',function() {
        $('[name=FormaEnvio]').submit(function(){
        	$('.AjusteIngreso tbody tr input.cantidad').map(function(){
            	if($(this).val()==''||$(this).val()==0)
            		$(this).closest('tr').remove();
            });
        });
        $("#intostocklocation").select2();
        $(".acselect").select2();
        
        $('input[name="SaveItemLine"]').click(function() {
            //alert($(this).attr('stockid'));
            var StockID = $(this).attr('stockid');
            
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("stockmoves/updatecartlines"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      UpdateCartLines:{
                          stockid: StockID,
                          qty: $("#QTY" + StockID).val(),
                          coments: $("#Coments" + StockID).val(),
                          action: 'Update'
                      },
                },
                success: function(response, newValue) {
                    window.location = window.location;
                    //displayNotify('success', response.msg);
                },
                error: function(response, newValue) {
                    //displayNotify('error', response.msg);
                },
            });
        });
        
        $('input[name="DeleteItemLine"]').click(function() {
            //alert($(this).attr('stockid'));
            var StockID = $(this).attr('stockid');
            
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("stockmoves/updatecartlines"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      UpdateCartLines:{
                          stockid: StockID,
                          action: 'Delete'
                      },
                },
                success: function(response, newValue) {
                    window.location = window.location;
                    //displayNotify('success', response.msg);
                },
                error: function(response, newValue) {
                    //displayNotify('error', response.msg);
                },
            });
        });
        
          /*$(function(){
            window.autocompletar = new Array();
            <?php
             //for($p = 0;$p < count($ListDescriptions); $p++){  
                ?>
               autocompletar.push('<?php //echo $ListDescriptions[$p]; ?>');
             <?php //} ?>
             $(".Keywords").autocomplete({
                source: window.autocompletar, 
             });
          });*/
        
    });
</script>
<form name="FormaEnvio" enctype="multipart/form-data" method="post" action="<?php echo $this->createUrl("stockmoves/bajaporconsumo/"); ?>" >
<div style="height: 20px;"></div>
<div class="container-fluid">
    <!-- <div class="form-legend">Baja por Consumo</div> -->
    <div class="control-group row-fluid">
        <div class="span12">
                <table class="table" >
                    <thead>
                        <tr>
                            <tr>
                                <th colspan="2">Baja por Consumo</th>
                            </tr>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 25%;" >Ajuste de Inventario en Almacen</td>
                            <td>
                                <select id="intostocklocation" name="intostocklocation" >
                                    <?php foreach ($_SESSION['rh_permitionlocation'] as $value => $name){ 
                                        if(isset($_POST['intostocklocation']) && $_POST['intostocklocation'] == $value){
                                           echo '<option selected=selected value="'.$value.'">'.$name.'</option>';
                                        }
                                    ?>
                                        <option value="<?=$value?>"><?=$name?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Comentarios</td>
                            <td><input type="text" id="Comentarios" name="Comentarios" value="<?=$_POST['Comentarios']?>" style="width: 500px;" /></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan=2><input type="file" name="csv" id="csv">
                            <input type="submit" name="subir" value="Subir">
                            <h3>Formato: stockid,Unidades</h3>

                           	<script type="text/javascript" src="<?=$rootpath?>/javascript/descargar/Blob.js"></script>
							<script type="text/javascript" src="<?=$rootpath?>/javascript/descargar/canvas-toBlob.js"></script>
							<script type="text/javascript" src="<?=$rootpath?>/javascript/descargar/FileSaver.js"></script>
                            <!--<script type="text/javascript" src="<?//=$rootpath?>/javascript/descargar/csvExporter.js"></script>-->
                            <!--<script type="text/javascript">$(function(){
                                $('<csv title="#template" target="Layout Baja por consumo(<?//=date('Y-m-d')?>)"><button name="xls" value="xls">Layout</button></csv><br />').insertBefore($('#template'));
                            });</script>
                            

                            <table id="template" style="display:none">
                            	<tr>
                            		<td>CodigoBarras</td>
                            		<td>Unidades</td>
                            		<td>stockid</td>
                            		<td>lote</td>
                            		<td>Comentarios</td>
                            	</tr>
                            	<tr>
                            		<td></td>
                            		<td></td>
                            		<td></td>
                            		<td></td>
                            	</tr>
                            </table>-->
                            </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
    </div>
</div>

<div style="height: 50px;"></div>
<?php if(!empty($_SESSION['AdjustmentCart']['BPC'])) { ?>
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Almacén</th>
                        <th>Cantidad</th>
                        <th>Comentarios</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach($_SESSION['AdjustmentCart']['BPC'] as $StockID=>$_2Item){
					?>
                    <tr>
                        <td><?=$_2Item['StockID']?><?php 
                        if(isset($_2Item['lote'])) echo ' ['.htmlentities($_2Item['lote']).']';
                        ?></td>
                        <td><?=$_2Item['Description']?></td>
                        <td><?=$_2Item['Almacen']?></td>
                        <td><input type="text" id="QTY<?=$StockID?>" value="<?=$_2Item['QTY']?>" style="width: 100px;" />  </td>
                        <!--<td><input type="text" name="Coments" id="Coments<?=$StockID?>" value="<?//=$_2Item['Coments']?>"  />  </td>-->
                        <td><input type="button" class="btn btn-small" stockid="<?=$StockID?>" name="SaveItemLine" id="SaveItemLine<?=$StockID?>" value="Actualizar">
                            <input type="button" class="btn btn-small" stockid="<?=$StockID?>" name="DeleteItemLine" id="DeleteItemLine<?=$StockID?>" value="Eliminar">
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">  <input type="submit" class="btn btn-small" value="Procesar" id="Commit" name="Commit" />  </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php } ?>


<div style="height: 50px;"></div>
    
    
<div class="container-fluid">
    <div class="control-group row-fluid">
        <div class="span12">
            <!-- <form class="form-horizontal" method="post" action=""> -->
            <div style="text-align: right;">
                <input class="btn btn-small" type="submit" name="AddItems" value="Agregar">
            </div>
            <br>
            <table class="AjusteIngreso table table-striped table-hover">
                <thead>
                    <tr>
                        <!-- <th class="tableheader">Cod. Barras</th> -->
                        <th class="tableheader">Cantidad</th>
                        <th class="tableheader" style="min-width: 300px;" >Descipción ó Código de Barras</th>
                        <th class="tableheader">Código</th>
                        <th class="tableheader"><lote style="display:none">Lote/Serie</lote></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $DefaultDeliveryDate = Date($_SESSION['DefaultDateFormat']);
                for ($i=1;$i<=$_SESSION['QuickEntries'];$i++){ ?>
                <tr>
                    <td><input type="text" class="cantidad" name="<?=$i?>[qty]" style="width: 100px;" ></td>
                    <td><input type="text" class="Keywords" name="<?=$i?>[description]" id="descr_<?=$i?>" value="" style="width: 100%;" onblur="javascript: GetItemData(this.value,<?=$i?>)" ></td>
                    <td><input type="text" name="<?=$i?>[stockid]" id="prod_<?=$i?>" style="width: 120px;" ></td>
                    <td><input type="hidden" name="<?=$i?>[lote]" id="lote_<?=$i?>" style="width: 120px;" ></td>
                </tr>
                <?php } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><input class="btn btn-small" type="submit" name="AddItems" value="Agregar"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

</form>


<script language='javascript'>
function create_ajaxOb(){
    var xmlHttp;
    try{// Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    }
    catch(e){
        // Internet Explorer
        try{
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e){
            try{
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e){
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    return xmlHttp;
}

function strip_tags (input, allowed) {
  allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
  var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
    commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
  return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
    return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
  });
}

function GetItemData(searchkey, n){
    var jqxhr = $.ajax({
        url: "<?php echo $this->createUrl("stockmoves/GetItem/"); ?>",
        type: "POST",
        dataType : "json",
        timeout : (120 * 1000),
        data: {
            action: 'GetStockID',
            searchkey : searchkey,
            Fila:n
        },
        success: function(response, newValue) {
            console.log(response);
            Fila=0;
            if(response.length>0){
            	Fila=parseInt(response[0].Fila);
            	FilasaAgregar=response.length-($('.AjusteIngreso tbody tr').length-Fila-1);
            	Ultima=$('.AjusteIngreso tbody tr:last');
            	Consecutivo=(parseInt(Ultima.find('td').first().find('input').attr('name').split('[')[0]))+1;
            	for(i=0; i<FilasaAgregar; i++){
            		Clon=Ultima.clone();
            		Clon.appendTo(Ultima.parent());
            		Clon.find('input').map(function(){
                		//Input=$('<input type="'+this.attributes['type'].textContent+'">');
                		Input=$(this);
            			for(attr in this.attributes){
            				Valor=this.attributes[attr].textContent;
            				Nombre=this.attributes[attr].nodeName;
            				if(Nombre=='value'){
                    			Valor='';
                    		}else
                			if(Nombre=='name'){
                    			Valor=Valor.split('[');
                    			Valor[0]=Consecutivo;
                    			Valor=Valor.join('[');
                    		}else
                    			if(Nombre=='id'){
                        			Valor=Valor.split('_');
                        			Valor[1]=Consecutivo;
                        			Valor=Valor.join('_');
                        		}else
                        			if(Nombre=='onblur'){
                            			Valor=Valor.split(',');
                            			Valor[1]=Consecutivo+')';
                            			Valor=Valor.join(',');
                            		}
            				if(typeof(Nombre)&&Nombre!='type')
            					Input.attr(Nombre,Valor);
                		}
//             			Input.appendTo($(this).parent());
//             			$(this).remove();
                	});
            		Consecutivo++;
	            	$(".Keywords").autocomplete({
	                    source: window.autocompletar, 
	                });
                }
	            for(id in response){
		            n=Fila+parseInt(id);
		            $('#prod_'+n).val(strip_tags(response[id].stockid));
		            $('#descr_'+n).val(strip_tags(response[id].desc));
		            if(typeof(response[id].series)!='undefined'){
		            	$('lote').show();
		            	if(response[id].series.length==1){
		            		serie=response[id].series[0].serialno;
		            		$('#lote_'+n).val(strip_tags(response[id].series[0].serialno));
		            	}else{
		            		anterior=$('#lote_'+n).val();
		            		td=$('#lote_'+n).closest('td');
		            		if(td.find('select').length==0){
		            			$('<select></select>').appendTo(td);
		            			select=td.find('select');
		            			select.change(function(){
		            				$(this).closest('td').find('input').val($(this).val());
		            			});
		            		}else select=td.find('select');
		            		
		            		select.find('option').remove();
		            		for(i in response[id].series){
		            			opcion='<option value="'+strip_tags(response[id].series[i].serialno)+'"'
		            			if(response[id].series[i].serialno==anterior)
		            			opcion+=' selected=selected ';
		            			opcion+='>'+strip_tags(response[id].series[i].serialno)+'</option>';
		            			$(opcion).appendTo(select);
		            		}
		            		select.change();
		            	}
		            }
	            }
            }else{
                //Ambiguo
            }
//             document.getElementById("prod_" + n).value = strip_tags(response.stockid);
//             document.getElementById("descr_" + n).value = strip_tags(response.desc);
            //window.location = window.location;
        },
        error: function(response, newValue) {
            //alert(response);
        },
    });
}

</script>
