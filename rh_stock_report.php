<?php
$PageSecurity = 2;
	
include('includes/session.inc');
include ('includes/tablas.php');
$Titulos=array();
	$Titulos[]='stockid';
	$Titulos[]='Descripci&oacute;n';
	$Titulos[]='Descripci&oacute;n Larga';
	$Titulos[]='id_categoria';
	$Titulos[]='Categor&iacute;a';
	$Titulos[]='id_Marca';
	$Titulos[]='Marca';
	$Titulos[]='is_cortesia';
	$Titulos[]='is_farmacia';
	$Titulos[]='id_agrupador';
	$Titulos[]='Nombre Agrupador';
	$Titulos[]='Sustanca Act&iacute;va';
	$Titulos[]='Laboratorio';	
	$JoinCategoria='';
	$sql="select * from rh_familia group by categoria";
	$rh_familia=new tablas($sql,'rh_familia',$db);
	$Categoria=0;
	unset($Titulos[20]);
	foreach($rh_familia as $familia){
		$Categoria=(int)$familia['categoria'];
		$JoinCategoria.=" left join rh_familia_stock FamRelacion{$Categoria} on ".
			"FamRelacion{$Categoria}.categoria={$Categoria} ".
			" and FamRelacion{$Categoria}.stockid = stockmaster.stockid ".
		" left join rh_familia FamRelacion_{$Categoria} on ".
			"FamRelacion_{$Categoria}.clave=FamRelacion{$Categoria}.clave ";
		$SQLFamilia.=" FamRelacion_{$Categoria}.nombre 'Categoria {$Categoria}',";
		$Titulos[]="Categoria {$Categoria}";
	}
	$Titulos[]='Cantidad Econ&oacute;mica a reordenar eoq';
	$Titulos[]='Nivel mas bajo';
	$Titulos[]='Nivel mas bajo producci&oacute;n';
	$Titulos[]='volume';
	$Titulos[]='kgs';
	$Titulos[]='units';
	$Titulos[]='Tipo Art&iacute;culo';
	$Titulos[]='Estatus';
	$Titulos[]='Controlado';
	$Titulos[]='Serializado';
	$Titulos[]='Perecedero';
	$Titulos[]='Decimales';
	$Titulos[]='C&oacute;digo de Barras';
	$Titulos[]='Categor&iacute;a descuento';
	$Titulos[]='Categor&iacute;a Impuestos';
if(isset($_REQUEST['Descargar'])){
	$filename='StockMaster('.date('Y_m_d').').csv';
	header("Expires: Tue, 28 Nov 20014 00:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");

    // force download  
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
    
	$SQL="select * from tmp_stockmaster";
	$Existencias=DB_query($SQL,$db);
	echo implode(",",array_map(function($data){return '"'.str_replace('"','""',html_entity_decode($data,ENT_COMPAT | ENT_HTML401,'UTF-8')).'"';},$Titulos))."\r\n";
	
	while($file=DB_fetch_assoc($Existencias)){
		echo implode(",",array_map(function($data){return '"'.str_replace('"','""',html_entity_decode($data,ENT_COMPAT | ENT_HTML401,'UTF-8')).'"';},$file))."\r\n";
	}
	exit;
}

if(!isset($_REQUEST['almacen']))
	$_REQUEST['almacen']=$_SESSION["UserStockLocation"];
$almacenes=array();
$resalmacenes=new tablas("select loccode, locationname from locations",'locations',$db);
foreach ($resalmacenes as $almacen)
	$almacenes[$almacen['loccode']]=$almacen;
$Types="17,20010,16,25";
$tipos=array();
$res=new tablas("select typeid, typename from systypes where typeid in($Types)",'systypes',$db);
foreach ($res as $tipo)
	$tipos[$tipo['typeid']]=$tipo;

if(!isset($_REQUEST['Desde']))
	$_REQUEST['Desde']=date('Y-m-d');
if(!isset($_REQUEST['Hasta']))
	$_REQUEST['Hasta']=date('Y-m-d');



$mbflag=array();
$mbflag['A']=_('Assembly');
$mbflag['E']=_('Assembly').' '._('Costo por Componente');
$mbflag['K']= _('Kit');
$mbflag['M']=_('Manufactured');
$mbflag['B']=_('Purchased');
$mbflag['D']=_('Service');
$mbflag['C']=_('Manufactured').' '._('Costo por Componente');


if(isset($_REQUEST['CrearTabla']))
{
	$SQL="drop table if exists tmp_stockmaster";
	DB_query($SQL,$db);
	$SQL="create table tmp_stockmaster select ";
		$SQL.="stockmaster.stockid,";
		$SQL.="stockmaster.description Descripcion,";
		$SQL.="stockmaster.longdescription 'Descripcion Larga',";
		$SQL.="stockmaster.categoryid id_categoria,";
		$SQL.="stockcategory.categorydescription Categoria,";
		$SQL.="stockmaster.rh_marca id_Marca,";
		$SQL.="rh_marca.nombre Marca,";
		$SQL.="stockmaster.is_cortesia,";
		$SQL.="stockmaster.is_farmacia,";
		$SQL.="stockmaster.id_agrupador,";
		$SQL.="rh_stock_grupo.nombre,";
		$SQL.="rh_gamma.descripcion 'Sustanca Activa',";
		$SQL.="rh_especie.descripcion 'Laboratorio',";
		
		$SQL.=$SQLFamilia;
		$SQL.="stockmaster.eoq 'Cantidad Economica eoq',";
		$SQL.="stockmaster.lowestlevel 'Nivel mas bajo',";
		$SQL.="stockmaster.rh_lowestprod 'Nivel mas bajo produccion',";
		$SQL.="stockmaster.volume,";
		$SQL.="stockmaster.kgs,";
		$SQL.="stockmaster.units,";
		
		
		$SQL.="(case stockmaster.mbflag ";
		foreach($mbflag as $clave=>$flag){
			$SQL.=" when '".DB_escape_string($clave)."' then '".DB_escape_string($flag)."' ";
		}
		$SQL.=" else '".DB_escape_string('Sin Tipo')."' ";
		$SQL.=" end) 'Tipo Articulo', ";
		$SQL.="if(stockmaster.discontinued=0,'".DB_escape_string(_('Current'))."','".DB_escape_string(_('Obsolete'))."') Estatus,";
		$SQL.="if(stockmaster.controlled=1,'".DB_escape_string(_('Controlled'))."','".DB_escape_string(_('No Control'))."') Controlado,";
		$SQL.="if(stockmaster.serialised=1,'".DB_escape_string(_('Si'))."','".DB_escape_string(_('No'))."') Serializado,";
		$SQL.="if(stockmaster.perishable=1,'".DB_escape_string(_('Si'))."','".DB_escape_string(_('No'))."') Perecedero,";
		$SQL.="stockmaster.decimalplaces Decimales,";
		$SQL.="stockmaster.barcode 'Codigo de Barras',";
		$SQL.="stockmaster.discountcategory 'Categoria descuento',";
		
		
		
		$SQL.="taxcategories.taxcatname 'Categoria Impuestos'";
		//$SQL.=",''Botonera ";	

	$SQL.=" from stockmaster ";
	$SQL.=" left join stockcategory on stockcategory.categoryid=stockmaster.categoryid ";
	$SQL.=" left join rh_marca on rh_marca.id=stockmaster.rh_marca ";
	$SQL.=" left join rh_stock_grupo on rh_stock_grupo.clave=stockmaster.id_agrupador ";
	
	$SQL.=" left join rh_gamma_stock on rh_gamma_stock.stockid = stockmaster.stockid ";
	$SQL.=" left join rh_gamma on rh_gamma_stock.idGamma = rh_gamma.id ";
	
	$SQL.=" left join rh_especie_stock on rh_especie_stock.stockid = stockmaster.stockid ";
	$SQL.=" left join rh_especie on rh_especie_stock.idEspecie = rh_especie.id ";
	$SQL.=" left join taxcategories on taxcategories.taxcatid = stockmaster.taxcatid ";
	
	$SQL.=$JoinCategoria;
	$SQL.=" group by stockmaster.stockid ";
	DB_query($SQL,$db);
	echo _('Listo');
	exit;
}


$title = _('Reporte de articulos');
include('includes/header.inc');

?>
<div class="container" style="padding-top:0">
	<div class="row-fluid">
		<div class="span12">
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
<script type="text/javascript">
<!--
$(function(){
	$('input[type=date]').map(function(){
		if($(this)[0].type!='date'){
			$(this).attr('class','date');
		}
	});
	initial();
});
//-->
</script>  
<?php
	$SQL="select count(*) t from stockmaster";
	$total=DB_query($SQL,$db);
	$TotalRegistros=DB_fetch_assoc($total);
	$TotalRegistros=$TotalRegistros['t'];
	 
?>
			<form name="FormaEnvio" enctype="multipart/form-data" method="post" >
			<div style="height: 20px;"></div>
			<div class="container-fluid">
			    <div class="control-group row-fluid">
			        <div class="span12">
			                <table class="table">
			                	<tbody>
			                    </tbody>
			                    <thead><tr>
			                                <th colspan="4"><?=$title?></th>
			                            </tr>
			                    </thead>
			                    
			                    <tfoot>
			                        <tr>
			                            <td colspan="4">
			                            <center>
			                            	<div class="Loading">
			                            		<img src="modulos/themes/black/images/loader.gif" width="50px"/><br >
			                            		Generando cach&eacute;
			                            	</div>
			                            	<div class="Completo"></div>
			                            	<input type="submit" name="Buscar" value="<?=_('Refrescar')?>">
			                            	<script type="text/javascript">
			                            	$(function(){
			                            		$.ajax({
			                            		url:"?CrearTabla=1",
			                            		success:function(data){
			                            			$('.Loading').hide();
			                            			$('.Completo').html(data);
			                            			$('[name="Descargar"]').click();
			                            		}
			                            		});
			                            	});
			                            	</script>
			                            	<input type="submit" value="Descargar <?php echo $TotalRegistros?> Registros " name="Descargar" >
			                            </center>
			                            </td>
			                        </tr>
			                    </tfoot>
			                </table>
			        </div>
			    </div>
			</div>
			</form>
			<br>
<?php	
	
if(isset($_REQUEST['Descargar'])){
	$SQL="select * from tmp_stockmaster";
	$Existencias=DB_query($SQL,$db);
?>
			<div class="container-fluid">
			    <div class="control-group row-fluid">
			        <div class="span12">
			            <table class="TablaAgrupadorExistencia table table-striped table-hover">
			                <thead>
				                <tr>
				                	<td></td>
				                	<?php
				                	foreach($AlmacenGrupo as $grupo){
										echo '<th colspan=3 alingn="center" style="border:RLT;">';
										echo '<center>';
										echo htmlentities($grupo['locationname']);
										echo '</center>';
										echo '</th>';
				                	} 
				                	?>
				                	<td></td>
				                </tr>
			                	<tr style="border:B;">
			                	<?php
			                	foreach($Titulos as $clave=>$Titulo){
									echo '<th alingn="center"';
									if($clave=='description')
										echo ' style="width:200px;"';
									echo ' ><span>'.($Titulo).'</span></th>';
								} 
			                	?>
			                	</tr>
			                </thead>
			                <tbody>
			                	<?php
			                	$tipoMovimiento='';
			                	while($linea=DB_fetch_assoc($Existencias)){
			                		
			                	?>
				                <tr>
				                <?php
				                	foreach($linea as $clave=>$valor){
										echo '<td ';
										if($clave=='description')
											echo ' style="width:450px;display: block;"';
										echo ' ><span>';
										echo htmlentities($valor);
										echo '</span></td>';	
									}
								?>
				                </tr>
				                <?php }?>
			                </tbody>
			                </table>
			        </div>
			    </div>
			</div>
			<?php 
}?>
		</div>
	</div>
</div>
<?php 
include ('includes/footer.inc');