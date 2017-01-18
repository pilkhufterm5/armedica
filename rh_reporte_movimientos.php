<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Reporte de movimientos');
include('includes/header.inc');
$title = _('Reporte de movimientos');
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
			<form name="FormaEnvio" enctype="multipart/form-data" method="post" >
			<div style="height: 20px;"></div>
			<div class="container-fluid">
			    <div class="control-group row-fluid">
			        <div class="span12">
			                <table class="table">
			                	<tbody>
			                		<tr>
			                			<td>Almacen</td>
			                			<td>
			                				<select name="almacen[]">
			                				<option value="">Todos</option>
			                				<?php 
			                				foreach ($almacenes as $alm){
												echo '<option ';
												if(isset($_REQUEST['almacen'])&&in_array($alm['loccode'],$_REQUEST['almacen']))
													echo ' selected=selected ';
												echo 'value="';
												echo htmlentities($alm['loccode']);
												echo '">';
												echo htmlentities($alm['locationname']);
												echo '</option>';
			                				}
			                				?>
			                				</select>
			                			</td>
			                			<td>Tipo transferencia</td>
			                			<td>
			                				<select name="Tipo[]">
			                				<option value="">Todos</option>
			                				<?php 
			                				foreach ($tipos as $alm){
												IF($alm['typeid']==25)$alm['typename']='Orden de Compra';
												echo '<option ';
												if(isset($_REQUEST['Tipo'])&&in_array($alm['typeid'],$_REQUEST['Tipo']))
													echo ' selected=selected ';
												echo 'value="';
												echo htmlentities($alm['typeid']);
												echo '">';
												echo htmlentities($alm['typename']);
												echo '</option>';
			                				}
			                				?>
			                				</select>
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>Desde</td>
			                			<td>
			                				<input name="Desde" type="date" value="<?=$_REQUEST['Desde']?>">
			                			</td>
			                			<td>Hasta</td>
			                			<td>
			                				<input name="Hasta" type="date" value="<?=$_REQUEST['Hasta']?>">
			                			</td>
			                		</tr>
			                		<tr>
			                			<td>Agrupar por Id Agrupador</td>
			                			<td>
			                				<input name="Agrupar" type="checkbox" value="1" <?php echo (isset($_REQUEST['Agrupar'])?' checked=checked':'');?>>
			                			</td>
			                			<td></td>
			                			<td>
			                				
			                			</td>
			                		</tr>
			                		<tr>
				                		<td COLSPAN=4>
				                			<table style="width: 100%">
				                				<tr><?php
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	while($fila=DB_fetch_assoc($res)){
		echo '<td>Categoria '.htmlentities($fila['categoria']).' </td>';
	}
	echo '</tr><tr>';
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	while($fila=DB_fetch_assoc($res)){
		$SQL="Select * from rh_familia where categoria='".DB_escape_string($fila['categoria'])."'";
		$res2=DB_query($SQL,$db);
		echo '<td><select name="Categoria['.htmlentities($fila['categoria']).']">';
		echo '<option value="">Todos</option>';
		while($fila2=DB_fetch_assoc($res2)){
			echo '<option';
			echo ' value="';
			echo htmlentities($fila2['clave']);
			echo '"';
			if($_REQUEST['Categoria'][$fila['categoria']]==$fila2['clave'])
				echo ' selected=selected ';
			echo '>';
			echo htmlentities('( '.$fila2['clave'].' ) '.$fila2['nombre']);
			echo '</option>';
		}
		echo '</select></td>';	
	} 
				                					?>
				                				</tr>
				                			</table>
				                		</td>
			                		</tr>
			                    </tbody>
			                    <thead><tr>
			                                <th colspan="4"><?=$title?>s</th>
			                            </tr>
			                        
			                    </thead>
			                    
			                    <tfoot>
			                        <tr>
			                            <td colspan="4">
			                            <center>
			                            	<input type="submit" name="Buscar" value="<?=_('Refrescar')?>">
			                            	<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
			                            	<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
			                            	<script type="text/javascript">
			                            	$(function(){
			                            		$('csv, pdf').show();
			                            	})
			                            	</script>
			                            	<csv style="display:none" target="Reporte Movimientos(<?=date('Y-m-d')?>)" title=".TablaAgrupadorExistencia"><button>Excel</button></csv>
			                            	<pdf style="display:none" target="Reporte Movimientos(<?=date('Y-m-d')?>)" title=".TablaAgrupadorExistencia"><button>Pdf</button></pdf>
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
$Existencias=array();
if(isset($_REQUEST['Buscar']))
{	
	$SQL="select stockmoves.type,".
			"stockmoves.transno, ".
			"sum(stockmoves.qty)qty,".
			" stockmoves.reference, ".
			"stockmoves.narrative, ".
			"stockmoves.loccode, ".
			"stockmoves.trandate, ".
			"stockmoves.stockid, ".
			"stockmoves.newqoh ".
			", stockmaster.description description2 ".
			", stockmoves.narrative ".
			", stockmoves.description ".
			", stockmaster.barcode  ".
			", stockmaster.id_agrupador ".
			", stockmoves.rh_orderline ".
			", newqoh-qty as prev ".
			", group_concat(stockmoves.stockid) familias ";
	
	$SQL.=
		"from stockmoves "
		." left join stockmaster on stockmaster.stockid=stockmoves.stockid ";
	
	$SQLFamiliaWhere='';
	foreach($_REQUEST['Categoria'] as $categoria=>$valor)
	if(trim($valor)!=''){
		$categoria=(int)$categoria;
		$SQL.=" left join rh_familia_stock rh_familiaCatStock".$categoria." on rh_familiaCatStock".$categoria.".stockid=stockmoves.stockid and rh_familiaCatStock".$categoria.".categoria='$categoria' ";
		$SQL.=" left join rh_familia rh_familiaCat".$categoria." on rh_familiaCatStock".$categoria.".clave=rh_familiaCat".$categoria.".clave and rh_familiaCat".$categoria.".categoria='$categoria' ";
		$SQLFamiliaWhere.=" and rh_familiaCat".$categoria.".clave='".DB_escape_string($valor)."' ";
		$SQLFamiliaWhere.=" and rh_familiaCatStock".$categoria.".clave='".DB_escape_string($valor)."' ";
	}
	$SQL.=" where ";
	$SQL.=" show_on_inv_crds=1  ".$SQLFamiliaWhere;
	if(isset($_REQUEST['Tipo'])&&$_REQUEST['Tipo'][0]!='')
		$SQL.=" and stockmoves.type in('".implode("','",$_REQUEST['Tipo'])."') ";
	else
		$SQL.=" and stockmoves.type in(".$Types.") ";
	
	if(isset($_REQUEST['Desde'])&&$_REQUEST['Desde']!=''&&strtotime($_REQUEST['Desde'])!=0)
		$SQL.=" and trandate>='".date('Y-m-d',strtotime($_REQUEST['Desde']))."' ";
	if(isset($_REQUEST['Hasta'])&&$_REQUEST['Hasta']!=''&&strtotime($_REQUEST['Hasta'])!=0)
		$SQL.=" and trandate<='".date('Y-m-d',strtotime($_REQUEST['Hasta']))."' ";
	if(isset($_REQUEST['almacen'])&&$_REQUEST['almacen'][0]!='')
		$SQL.=" and loccode in ('".implode("','",$_REQUEST['almacen'])."') ";
	
	$Titulos=array(
		'barcode'=>_('Codigo Barras'),
		'id_agrupador'=>_('Id Agrupador'),
		'familia'=>_('Familia'),
		'stockid'=>_('Stockid'),
		'description'=>_('Articulo'),
		'transno'=>_('N&uacute;mero'),
		'trandate'=>_('Fecha'),
		'loccode'=>_('Almacen'),
		'reference'=>_('Referencia'),
		'prev'=>_('Existencia Previa'),
		'qty'=>_('Cantidad'),
		'newqoh'=>_('Existencia Final')
	);	
	if(isset($_REQUEST['Agrupar'])){
		$SQL.=" group by id_agrupador,loccode ";
		$Titulos=array(
			'id_agrupador'=>_('Id Agrupador'),
			'description'=>_('Articulo'),
			'loccode'=>_('Almacen'),
			'qty'=>_('Cantidad')
		);
		$SQL.=" order by type, loccode ";	
	}else
	$SQL.=" group by type, stkmoveno ";
	
	$Existencias=new tablas($SQL,'stockmoves',$db);
	
	
}
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
			                	foreach($Existencias as $linea){
			                		if($tipoMovimiento!=$linea['type']){
										$tipoMovimiento=$linea['type'];
			                		?>
			                		<tr src="<?=$linea['type']?>"><th colspan=11 style="background-color: #cccce5;"><span><?=$tipos[$linea['type']]['typename']?></span></th></tr>
			                		<?php 
			                		}
			                		if($linea['type']==20010){
										$linea['id_agrupador']=$linea['rh_orderline'];
										list($linea['narrative'],$cb)=explode('::.',$linea['narrative']);
										$cb=trim($cb,"[] \t\r\0\n\x0B");
										if($cb!='') $linea['barcode']=$cb;
			                		}
			                		if(trim($linea['description'])=='')$linea['description']=$linea['description2'];
			                	?>
				                <tr src="<?=$linea['type']?>">
				                <?php
				                	foreach($Titulos as $clave=>$Titulo){
										if($clave=='familia'){
											$SQL="select group_concat(concat(' ',fam.nombre) separator '<br >\n ')nom from rh_familia fam join rh_familia_stock fst on fst.clave=fam.clave and fst.categoria=fam.categoria where fst.stockid in('".
											str_replace(',',"','", DB_escape_string($linea['familias']))
											."') order by fam.clave";
											$res=DB_query($SQL,$db);
											if($fil=DB_fetch_assoc($res))
											$linea[$clave]=$fil['nom'];
										}
										echo '<td ';
										if($clave!='loccode'&&$clave!='reference')
											echo 'alingn="center"';
										if($clave=='description')
											echo ' style="width:450px;display: block;"';
										echo ' ><span>';
										if($clave=='familia')
											echo $linea[$clave];
										else
											echo htmlentities($linea[$clave]);
										echo '</span></td>';	
									}
								?>
				                </tr>
				                <?php
				                	if(isset($Titulos['stockid'])&&trim($linea['narrative']!='')){
									?><tr>
									<td colspan=2></td>
									<td><b><?=_('Comentario:')?></b></td>
										<td colspan=8 alingn='center' ><span><?=$linea['narrative']?></span></td>
										</tr>
									<?php 
				                	} 
				                ?>
				                
				                <?php }?>
			                </tbody>
			                </table>
			        </div>
			    </div>
			</div>
		</div>
	</div>
</div>
<?php 
include ('includes/footer.inc');
