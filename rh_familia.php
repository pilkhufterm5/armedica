<?php

$PageSecurity = 1;

include ('includes/session.inc');
include ('includes/tablas.php');
$title = _('Listado de familias');
include ('includes/header.inc');
$where='';
$NombreTabla="rh_familia";
$ClaveTablaPrincipalId='id';
$ClaveTablaPrincipalIdDefault=0;
$Grupo=false;
$CamposEscondidos=array('id');
$UltimoCampo='nombre';
$Encabezados=array(
		'clave'=>'Id Familia',
		'categoria'=>'Numero Categoria',
		'nombre'=>'Nombre',
		
);
if(isset($_REQUEST['categorias'])){
	$_REQUEST['categoria']=(array_pop(array_keys($_REQUEST['categorias'])));
}
IF(!(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!='')){
	$_REQUEST['categoria']=1;
}
IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!=''){
	unset($Encabezados['categoria']);
	$where="categoria=".DB_escape_string($_REQUEST['categoria']);
}

if(!isset($_REQUEST['Editar'])){
	if($where!='')$where=' where '.$where;
	$Grupos=new tablas("Select * from ".$NombreTabla.$where,$NombreTabla,$Encabezados,$db);
}else{
	if($where!='')$where=' and '.$where;
	$Grupos=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."<>'".$_REQUEST['Editar']."'".$where,$NombreTabla,$Encabezados,$db);
	$Grupo=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."='".$_REQUEST['Editar']."'".$where,$NombreTabla,$Encabezados,$db);
}

if(isset($_REQUEST['New'])){
		//if($where!='')$where=' where '.$where;
		$_REQUEST['data'][$ClaveTablaPrincipalId]=$ClaveTablaPrincipalIdDefault;
		$Grupos->Save($_REQUEST['data']);
		$Grupos=new tablas("Select * from ".$NombreTabla.$where,$NombreTabla,$Encabezados,$db);
}else
if(isset($_REQUEST['Edith'])){
	if(strlen($_REQUEST['data']['clave'])<21){
		$Grupo=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."='".$_REQUEST['data']['id']."'",$NombreTabla,$db);
		$f=$Grupo->first();
		//validacion
		if($f['clave']<>$_REQUEST['data']['clave']){
			$SQL='update stockmaster set rh_familia="'.$_REQUEST['data']['clave'].'" where rh_familia="'.$f['clave'].'"';
			DB_query($SQL,$db);
		}
		$Grupo->Save($_REQUEST['data']);
		unset($Grupo);
	}
	
}else
if(isset($_REQUEST['Borrar'])){
		$SQL='select count(*) t, group_concat(rh_familia_stock.stockid) stockid from rh_familia join rh_familia_stock on rh_familia.clave=rh_familia_stock.clave where id='.DB_escape_string($_REQUEST['Borrar']);
		$res=DB_query($SQL,$db);
		$fila=DB_fetch_assoc($res);
		if($fila['t']==0)
			$Grupos->Delete($_REQUEST['Borrar'],$ClaveTablaPrincipalId);
		else
			prnMsg('No se pudo borrar, hay registros que estan asociados con los articulos '.$fila['stockid'],'error');
}


echo '<center>';
?>
<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
<script type="text/javascript">
$(function(){$('csv').show();})
</script>
<csv style="display:none" target="Reporte Familia Categoria <?php 
IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!='') echo htmlentities($_REQUEST['categoria']);
?>(<?=date('Y-m-d')?>)" title=".TablaCategoria"><button>Excel</button></csv>

<?php 
echo '<form method=post action="'.$_SERVER["SCRIPT_NAME"].'">';
$Agrupado=new tablas("Select * from ".$NombreTabla."  group by categoria asc",$NombreTabla,$db);
$NuevaCategoria=0;
foreach($Agrupado as $categorias){
	echo '<input type="submit" name="categorias['.$categorias['categoria'].']" value="Categoria '.$categorias['categoria'].'">';
	$NuevaCategoria=max($NuevaCategoria,$categorias['categoria']);
}


//Nueva Categoria
//echo '<input type="submit" name="categorias['.($NuevaCategoria+1).']" value="Nueva Categoria">';




echo '<table class="TablaCategoria">';

$d=$Grupos->getHead();
echo '<tr>';
foreach($d as $id=>$val){
	echo '<th>';
	echo $val;
	echo '</th>';
}

echo '</tr>';
if($Grupo){
	echo '<tr>';
	$d=$Grupo->first();
	foreach($Encabezados as $id=>$val){
		$valor=$d[$id];
		$Mostrar=false;
		$Mostrar=!in_array($val,$CamposEscondidos);
		
		echo '<td>';
		echo '<input type="';
		if($Mostrar)
			echo'text';
		else
			echo'hidden'; 
		echo '" value="'.htmlentities($valor).'"  name="data[';
		echo $id;
		echo ']">';
		if($id==($UltimoCampo)){
			echo '<input type=hidden name="data['.$ClaveTablaPrincipalId.']" value="'.htmlentities($d[$ClaveTablaPrincipalId]).'">';
			echo '<input type=submit name=Edith value="Guardar">';
			
			IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!='')
				echo '<input type="hidden" name="categoria" value="'.htmlentities($_REQUEST['categoria']).'">';
			}
			echo '</td>';
	}
	echo '</tr>';
}
foreach($Grupos as $id=>$val){
	echo "<tr>";
	foreach($Encabezados as $cve=>$valor){
		$valor=$val[$cve];
		echo '<td>';
			echo '<';
			echo $cve;
			echo '>';
			if($cve==$NombreCampoEsVisible)echo '<a href="?Change='.$val[$ClaveTablaPrincipalId].'&'.$NombreCampoEsVisible.'='.$valor.'">';
			if($cve=='password')$valor='******';
				echo $valor;
			if($cve==$NombreCampoEsVisible)echo '</a>';
			echo '</';
			echo $cve;
			echo '>';
		echo '</td>';
	}
	echo '<td class="no_print">';
		echo '<a href="?Borrar='.$val[$ClaveTablaPrincipalId];
		IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!='')
			echo '&categoria='.urlencode($_REQUEST['categoria']);
		echo '">'._('Borrar').'</a> ';
		echo '<a href="?Editar='.$val[$ClaveTablaPrincipalId];
		IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!='')
			echo '&categoria='.urlencode($_REQUEST['categoria']);
		echo '">'._('Editar').'</a>';
	echo '</td>';
	echo "</tr>";
}

if(!$Grupo){
	echo '<tr class="no_print">';
	foreach($d as $id=>$val){
		$Mostrar=false;
		$Mostrar=!in_array($val,$CamposEscondidos);
		
		echo '<td>';
		echo '<input type="';
		if($Mostrar)
			echo'text';
		else
			echo'hidden'; 
		echo '" value=""  name="data[';
		echo $id;
		echo ']">';
		if($id==($UltimoCampo)){
			echo '<input type=submit name=New value="Nuevo">';
			IF(isset($_REQUEST['categoria'])&&$_REQUEST['categoria']!=''){
				echo '<input type="hidden" name="categoria" value="'.htmlentities($_REQUEST['categoria']).'">';
				echo '<input type="hidden" name="data[categoria]" value="'.htmlentities($_REQUEST['categoria']).'">';
			}
		}
		
			echo '</td>';
	}
	echo '</tr>';
}

echo '</table>';
echo '</form>';
echo '</center>';
include ('includes/footer.inc');