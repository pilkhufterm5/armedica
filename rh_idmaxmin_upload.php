<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Importar Inventario inicial de CSV');

include('includes/header.inc');

	$path=$SessionSavePath;
	$flag=is_dir($path)? 1 : 0;
	
	foreach($_FILES as $nombre=>$val){
		$tamano = $_FILES[$nombre]['size'];
		$tipo = $_FILES[$nombre]["type"];
		$Numero="/[^\\d\\-\\.]/";
		$filename=$path.'/'.$_FILES[$nombre]['name'];
		ini_set('display_errors', 1);error_reporting (1);
		if(($tamano < 3000000)
			&&move_uploaded_file($_FILES[$nombre]['tmp_name'],$filename)
			&&@$fh_in = fopen($filename,"r")){
			$separador='|';
			$SQL=array();
			$SQL[]="delete from rh_locstock_max_min_agr where loccode='".DB_escape_string($_REQUEST['Almacen'])."'";
			while(($line = fgetcsv($fh_in,0,$separador))!==false){
	        	if(count($line)==1){
					$line=$line[0];
					if(strpos($line,'|')){
						$separador='|';
					}else
					if(strpos($line,';')){
						$separador=';';
					}else if(strpos($line,',')){
						$separador=',';
					}
					$line=array_map(function($d){return trim($d,"\"' \r\n\t\0\x0b");},explode($separador,$line));
	        	}
	        	$Loccode=$_REQUEST['Almacen'];
	        	$IdAgrupador=$line[0];//IdAgrupador
	        	$Minimo=$line[1];//Minimo
	        	$Maximo=$line[2];//Maximo
	        	
				$Maximo=preg_replace($Numero, '', trim($Maximo));
				$Minimo=preg_replace($Numero, '', trim($Minimo));
				if($Minimo!=''&&$Maximo!='')
	        	$SQL[]="insert into rh_locstock_max_min_agr(loccode, id_agrupador, quantity, maximo, minimo)values".
		        	 "('".DB_escape_string($Loccode)."','".DB_escape_string($IdAgrupador)."','0','".
		        		 DB_escape_string($Maximo)."','".DB_escape_string($Minimo)."')";
			}
			if(count($SQL)>1){
				DB_Txn_Begin($db);
				foreach($SQL as $sqlx)
					DB_query($sqlx,$db);
				prnMsg(_('El archivo fue cargado con exito'),'success');
				DB_Txn_Commit($db);
			}
		}
	}
	$almacenes=new tablas("select * from locations",'locations',$db);
	?>
	<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
	<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
	<script type="text/javascript">
		$(function(){$('csv').show();})
	</script>
	<center>
	<form method="post" enctype='multipart/form-data'>
		<table>
			<tr>
				<td><?=_('Almacen')?></td>
				<td>
					<select name="Almacen">
						<?php
						foreach($almacenes as $almacen){ 
						?>
						<option value="<?=$almacen['loccode']?>"<?=
							$almacen['loccode']== $_REQUEST['Almacen']?" selected=selected ":""
						?>><?=htmlentities(($almacen['locationname']),ENT_COMPAT | ENT_HTML401,'UTF-8')?></option>
						<?php
						}	 
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><?=_('Archivo')?></td>
				<td>
					<csv style="cursor: pointer;" target="Layout Carga Maximos y Minimos id (<?=date('Y-m-d')?>)" title=".MaximosMinimos" style="display:none"><img alt="" src="img/icon_download.gif"></csv>
					<input type="file" name="input">
					
				</td>
			</tr>
			<tr><td colspan="2"><center>
				<table class="MaximosMinimos" >
					<tr>
						<td>Id Agrupador</td>
						<td class="no_print">|</td>
						<td>Minimo</td>
						<td class="no_print">|</td>
						<td>Maximo</td>
					</tr>
				</table>
				</center>
			</td></tr>
			<tr>
				<td colspan="2">
				<center><input type="submit"></center>
				</td>
			</tr>
		</table>
	</form>
	</center>
<?php 
	
	
include ('includes/footer.inc');