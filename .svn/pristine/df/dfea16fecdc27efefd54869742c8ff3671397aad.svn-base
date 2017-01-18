<?php
$PageSecurity = 1;

include ('includes/session.inc');
include ('includes/tablas.php');
$title = _('Listado de grupos');
include ('includes/header.inc');
$NombreTabla="rh_locstock_max_min_agr";
$ClaveTablaPrincipalId='id';
$ClaveTablaPrincipalIdDefault=0;
$Grupo=false;
$CamposEscondidos=array('loccode','id_agrupador');
$UltimoCampo='minimo';
$Encabezados=array(
		'loccode'=>'Locacion',
		'id_agrupador'=>'Id Agrupador',
		'maximo'=>'Maximo',
		'minimo'=>'Minimo'
);


$id_Agrupador=$almacenes=array();
$Almac=new tablas("select * from locations" ,'locations',$db);
foreach($Almac as $alm)
	$almacenes[$alm['loccode']]=$alm['locationname'];

$Almac=new tablas("select * from rh_stock_grupo" ,'rh_stock_grupo',$db);
foreach($Almac as $alm)
	$id_Agrupador[$alm['clave']]=$alm['nombre'];


$sql="Select * from ".$NombreTabla.' t';


if(!isset($_REQUEST['Editar']))
	$Grupos=new tablas($sql ,$NombreTabla,$Encabezados,$db);
else{
	$Grupos=new tablas("Select * from ".$NombreTabla." where id_agrupador<>'".$_REQUEST['id_agrupador']."' and loccode<>'".$_REQUEST['loccode']."'",$NombreTabla,$Encabezados,$db);
	$Grupo=new tablas("Select * from ".$NombreTabla."  where id_agrupador='".$_REQUEST['id_agrupador']."' and loccode='".$_REQUEST['loccode']."'",$NombreTabla,$Encabezados,$db);
}

if(isset($_REQUEST['New'])){
		$_REQUEST['data'][$ClaveTablaPrincipalId]=$ClaveTablaPrincipalIdDefault;
		$Grupos->Save($_REQUEST['data']);
		$Grupos=new tablas("Select * from ".$NombreTabla,$NombreTabla,$Encabezados,$db);
}else
if(isset($_REQUEST['Edith'])){
	if(strlen($_REQUEST['data']['clave'])<21){
		$Grupo=new tablas("Select * from ".$NombreTabla." where id_agrupador='".$_REQUEST['data']['id_agrupador']."' and loccode='".$_REQUEST['data']['loccode']."'",$NombreTabla,$db);
		$f=$Grupo->first();
		//validacion
// 		if($f['clave']<>$_REQUEST['data']['clave']){
// 			$SQL='update stockmaster set id_agrupador="'.$_REQUEST['data']['clave'].'" where id_agrupador="'.$f['clave'].'"';
// 			DB_query($SQL,$db);
// 		}
		$Grupo->Save($_REQUEST['data']);
		unset($Grupo);
	}
	
}else
if(isset($_REQUEST['Borrar'])){
		$sql="delete from ".$NombreTabla." where id_agrupador='".$_REQUEST['id_agrupador']."' and loccode='".$_REQUEST['loccode']."'";
		db_query($sql,$db);
		//$Grupos->Delete($_REQUEST['Borrar'],$ClaveTablaPrincipalId);
}


echo '<center>';

echo '<form method=post action="'.$_SERVER["SCRIPT_NAME"].'">';
?>
<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
<script type="text/javascript">
$(function(){$('csv').show();})
</script>
<csv style="display:none" target="Reporte Maximos Minimos (<?=date('Y-m-d')?>)" title=".MaximosMinimos"><button>Excel</button></csv>
<br />
<script type="text/javascript">
<!--
$(function(){
	$('[name^=almac]').click(function(){
		data=$(this).attr('data');
		$('tr[name]').addClass('no_print').hide();
		$('[name='+data+']').removeClass('no_print').toggle();
		$('tr[name] input[type=text]').closest('tr').addClass('no_print');
	});
	$('[name^=almac]').first().click();
});
//-->
</script>
<?php 
$botones=new tablas("Select * from ".$NombreTabla." group by loccode",$NombreTabla,$db);
$i=0;
foreach($botones as $boton){
	$i++;
	echo '<input type="button" name="almac'.$i.'" data="'.$boton['loccode'].'" value="'.htmlentities($almacenes[$boton['loccode']]).'">';
}

echo '<table class="MaximosMinimos">';

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
		$Mostrar=!in_array($id,$CamposEscondidos);
		
		echo '<td>';
		echo '<input type="';
		if($Mostrar)
			echo'text';
		else
			echo'hidden'; 
		echo '" value="'.htmlentities($valor).'"  name="data[';
		echo $id;
		echo ']">';
		if(!$Mostrar){
			if($id=='loccode')$valor=$almacenes[$d[$id]];
			if($id=='id_agrupador'&&$id_Agrupador[$d[$id]]!='')$valor=$id_Agrupador[$d[$id]];
			echo $valor;
		}
		if($id==($UltimoCampo)){
			echo '<input type=hidden name="data['.$ClaveTablaPrincipalId.']" value="'.htmlentities($d[$ClaveTablaPrincipalId]).'">';
			echo '<input type=submit name=Edith value="Guardar">';
		}
			echo '</td>';
	}
	echo '</tr>';
}
foreach($Grupos as $id=>$val){
	echo "<tr name='".htmlentities($val['loccode'])."'>";
	foreach($Encabezados as $cve=>$valor){
		$valor=$val[$cve];
		echo '<td>';
			echo '<';
			echo $cve;
			echo '>';
			if($cve==$NombreCampoEsVisible)echo '<a href="?Change='.$val[$ClaveTablaPrincipalId].'&'.$NombreCampoEsVisible.'='.$valor.'">';
			if($cve=='password')$valor='******';
			if($cve=='loccode')$valor=$almacenes[$val[$cve]];
			if($cve=='id_agrupador'&&$id_Agrupador[$val[$cve]]!='')$valor='('.$valor.')'.$id_Agrupador[$val[$cve]];
				echo $valor;
			if($cve==$NombreCampoEsVisible)echo '</a>';
			echo '</';
			echo $cve;
			echo '>';
		echo '</td>';
	}
	echo '<td class="no_print">';
		echo '<a href="?Borrar='.$val[$ClaveTablaPrincipalId].'&id_agrupador='.$val['id_agrupador'].'&loccode='.$val['loccode'].'">'._('Borrar').'</a>';
		echo '<a href="?Editar='.$val[$ClaveTablaPrincipalId].'&id_agrupador='.$val['id_agrupador'].'&loccode='.$val['loccode'].'">'._('Editar').'</a>';
	echo '</td>';
	echo "</tr>";
}

if(!$Grupo){
	echo '<tr class="no_print">';
	foreach($d as $id=>$val){
		$Mostrar=false;
		$Mostrar=!in_array($val,$CamposEscondidos);
		
		echo '<td>';
		if($id=='loccode'){
			echo '<select name="data[loccode]">';
			foreach($almacenes as $id=>$val){
				echo '<option value="'.$id.'">';
				echo htmlentities($val);
				echo '</option>';
			}
			echo '</select>';
		}elseif($id=='id_agrupador'){
			echo '<select name="data[id_agrupador]">';
			foreach($id_Agrupador as $id=>$val){
				echo '<option value="'.$id.'">';
				echo htmlentities('( '.$id.' )'.$val);
				echo '</option>';
			}
			echo '</select>';
		}else{
			echo '<input type="';
			if($Mostrar)
				echo'text';
			else
				echo'hidden'; 
			echo '" value=""  name="data[';
			echo $id;
			echo ']">';
		}
		if($id==($UltimoCampo)){
			echo '<input type=submit name=New value="Nuevo">';
		}
		
			echo '</td>';
	}
	echo '</tr>';
}

echo '</table>';
?>
<script type="text/javascript">
<!--
$(function(){
	$('select[name*="id_agrupador"],select[name*="loccode"]').select2();
});
//-->
</script>

<?php 
echo '</form>';
echo '</center>';
include ('includes/footer.inc');