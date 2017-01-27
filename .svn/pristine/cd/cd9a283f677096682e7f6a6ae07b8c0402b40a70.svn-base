<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');
$title = _('Reporte existencias por ID Agrupador');
include('includes/header.inc');
if(!isset($_REQUEST['almacen']))
	$_REQUEST['almacen']=$_SESSION["UserStockLocation"];
$almacenes=array();
$resalmacenes=new tablas("select loccode, locationname from locations",'locations',$db);
foreach ($resalmacenes as $almacen)
	$almacenes[$almacen['loccode']]=$almacen;

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
	$AlmacenesFarmaciasX=
	$Existencias=
	$AlmacenesFarmacias=
	$AlmacenGrupo=array();
	$NombreTabla="rh_locstock_max_min_agr"; 
	$SQL="select rh_locstock_max_min_agr.loccode,locations.locationname from rh_locstock_max_min_agr join locations on locations.loccode=rh_locstock_max_min_agr.loccode group by loccode";
	$AlmacenesFarmaciasT=new tablas($SQL,$NombreTabla,$db);
	
	foreach($AlmacenesFarmaciasT as $almacen){
		$AlmacenesFarmacias[]=$almacen;
		$AlmacenesFarmaciasX[]=$almacen['loccode'];
	}
	/*
	 * Script generado por rleal para actualizar la tabla de rh_locstock_max_min_agr
	 */
	$sql="INSERT INTO rh_locstock_max_min_agr (loccode, id_agrupador, quantity, maximo,minimo ) ";
	$sql.="SELECT locations.loccode, rh_stock_grupo.clave, 0,0,0 FROM rh_stock_grupo ".
		"CROSS JOIN locations ".
		"left join rh_locstock_max_min_agr on  rh_stock_grupo.clave=rh_locstock_max_min_agr.id_agrupador and locations.loccode=rh_locstock_max_min_agr.loccode ".
		"where locations.loccode in ('".implode("','",$AlmacenesFarmaciasX)."') and rh_locstock_max_min_agr.id_agrupador is null";
	DB_query($sql,$db);
	
	$i=0;
	foreach($AlmacenesFarmacias as $almacen)
	if(!isset($_REQUEST['almacenes'])||in_array($almacen['loccode'],$_REQUEST['almacenes'])){
		$i++;
		$almacen=DB_escape_string($almacen['loccode']);
		$selectAlmacen.=
			"max(case when rh_locstock_max_min_agr.loccode='".$almacen."' then rh_locstock_max_min_agr.maximo else 0 end) maximo_$i, ".
			"max(case when rh_locstock_max_min_agr.loccode='".$almacen."' then rh_locstock_max_min_agr.minimo else 0 end) minimo_$i, ".
			"locstock.loccode loccode_$i, ".
			"sum((case when locstock.loccode='".$almacen."' then locstock.quantity else 0 end)) quantity_$i, ";
		
			$AlmacenGrupo[$i]=$almacenes[$almacen];
	} 
?>
			<form name="FormaEnvio" enctype="multipart/form-data" method="post" >
			<div style="height: 20px;"></div>
			<div class="container-fluid">
			    <div class="control-group row-fluid">
			        <div class="span12">
			                <table class="table">
			                	<tbody>
				                    <?php 
				                    
				                    foreach($AlmacenesFarmacias as $almacen){
										echo '<tr><td colspan=12>';
										echo '<input type="checkbox" name="almacenes[]" ';
										if(!isset($_REQUEST['almacenes'])||isset($_REQUEST['almacenes'])&&in_array($almacen['loccode'],$_REQUEST['almacenes']))
											echo ' checked=checked ';
										echo 'value="';
										echo DB_escape_string($almacen['loccode']);
										echo '">';
										echo DB_escape_string($almacen['locationname']);
										echo '</td></tr>';
				                    }
				                    echo '<tr>';
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	while($fila=DB_fetch_assoc($res)){
		echo '<td>Categoria '.htmlentities($fila['categoria']).' </td>';
	}
	echo '</tr><tr>';
				                    echo '<tr>';
	$Familias="";
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	$i=0;
	if(!isset($_REQUEST['Categoria']))$_REQUEST['Categoria']=array();
	while($fila=DB_fetch_assoc($res)){
		$i++;
		$Familias.="<th alingn='center' style=\"border:BR;\"><span>"._('Categoria')." $i</span></th>";
		if(!isset($_REQUEST['Categoria'][$i]))$_REQUEST['Categoria'][$i]='';
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
	echo '</tr>';
	
				                    ?>
				                    
			                    </tbody>
			                    <thead><tr>
			                                <th colspan="12"><?=$title?></th>
			                            </tr>
			                        
			                    </thead>
			                    
			                    <tfoot>
			                        <tr>
			                            <td colspan="12">
			                            <center>
			                            	<input type="submit" name="Buscar" value="<?=_('Refrescar')?>">
			                            	<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
			                            	<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
			                            	<script type="text/javascript">
			                            	$(function(){
			                            		$('csv, pdf').show();
			                            		
			                            	})
			                            	</script>
			                            	<csv style="display:none" target="Existencias por ID(<?=date('Y-m-d')?>)" title=".TablaAgrupadorExistencia"><button>Excel</button></csv>
			                            	<pdf style="display:none" target="Existencias por ID(<?=date('Y-m-d')?>)" title=".TablaAgrupadorExistencia"><button>Pdf</button></pdf>
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

//if(isset($_REQUEST['Buscar']))
{
	
	$SQL="select ".
			"sum(locstock.quantity) quantity, ".
			"rh_stock_grupo.nombre nombre_agrupador, ".
			$selectAlmacen.
			"rh_locstock_max_min_agr.id_agrupador ";
	$SQLMAinleft=$SQLFamiliaWhere='';
	$and='';
	foreach($_REQUEST['Categoria'] as $categoria=>$valor){
		$categoria=(int)$categoria;
		$SQLMAinleft.=" left join rh_familia_stock rh_familiaCatStock".$categoria." on rh_familiaCatStock".$categoria.".stockid=stockmaster.stockid and rh_familiaCatStock".$categoria.".categoria='$categoria' ";
		$SQLMAinleft.=" left join rh_familia rh_familiaCat".$categoria." on rh_familiaCatStock".$categoria.".clave=rh_familiaCat".$categoria.".clave and rh_familiaCat".$categoria.".categoria='$categoria' ";
		$SQL.=", group_concat(distinct rh_familiaCat".$categoria.".nombre) Categoria".$categoria." ";
		if(trim($valor)!=''){
			$SQLFamiliaWhere.=$and." rh_familiaCat".$categoria.".clave='".DB_escape_string($valor)."' ";
			$SQLFamiliaWhere.=" and rh_familiaCatStock".$categoria.".clave='".DB_escape_string($valor)."' ";
			$and=' and ';
		}
	}
	
	$SQL.="from locstock ".
		"join stockmaster on locstock.stockid=stockmaster.stockid ".
		"join rh_locstock_max_min_agr on stockmaster.id_agrupador=rh_locstock_max_min_agr.id_agrupador and locstock.loccode=rh_locstock_max_min_agr.loccode ".
		"left join rh_stock_grupo on stockmaster.id_agrupador=rh_stock_grupo.clave ".$SQLMAinleft
		;
		
	//$SQL.=" where locstock.loccode in('".DB_escape_string($_REQUEST['almacen'])."'";
	if($SQLFamiliaWhere!='')
		$SQL.=' where '.$SQLFamiliaWhere;
	$SQL.=" group by id_agrupador ";
	$Existencias=new tablas($SQL,$NombreTabla,$db);
}  

			   ?>             
			
			<div class="container-fluid">
			    <div class="control-group row-fluid">
			        <div class="span12">
			            <table class="TablaAgrupadorExistencia table table-striped table-hover">
			                <thead>
				                <tr>
				                	<td></td>
				                	<td></td>
				                	<td colspan='<?=$i?>'></td>
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
			                		<th alingn='center'><span><?=_('ID')?></span></th>
			                		<th alingn='center' style="border:BR;"><span><?=_('Agrupador')?></span></th>
			                		<?php
			                		echo $Familias;
				                	foreach($AlmacenGrupo as $grupo){
				                	?>
				                	<th alingn='center'><span><?=_('Real')?></span></th>
				                	<th alingn='center'><span><?=_('M&aacute;ximo')?></span></th>
				                	<th alingn='center' style="border:BR;"><span><?=_('M&iacute;nimo')?></span></th>
				                	<?php
				                	}
				                	?>
				                	<th alingn='center'><?=_('Total Existencia')?></th>
			                	</tr>
			                </thead>
			                <tbody>
			                	<?php foreach($Existencias as $linea){?>
				                <tr>
				                	<td alingn='center' ><span><?=$linea['id_agrupador']?></span></td>
				                	<td alingn='center' ><span><?=$linea['nombre_agrupador']?></span></td>
				                	
				                	<?php
				                	foreach($linea as $llave=>$valor){
				                		if(strpos(' '.$llave,'Categoria')){?>
				                			<td><span><?=$linea[$llave]?></span></td>
				                		<?php 
										}
									}
				                	foreach($AlmacenGrupo as $i=>$grupo){?>
				                		<td alingn='center'><span><?=$linea['quantity_'.$i]?></span></td>
					                	<td alingn='center'><span><?=$linea['maximo_'.$i]?></span></td>
					                	<td alingn='center'><span><?=$linea['minimo_'.$i]?></span></td>
					                	
				                	<?php }
				                	?>
				                	<td alingn='center' ><span><?=$linea['quantity']?></span></td>
				                </tr>
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
