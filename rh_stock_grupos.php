<?php
$PageSecurity = 1;

include ('includes/session.inc');
include ('includes/tablas.php');
$title = _('Listado de grupos');
include ('includes/header.inc');
$NombreTabla="rh_stock_grupo";
$ClaveTablaPrincipalId='id';
$ClaveTablaPrincipalIdDefault=0;
$Grupo=false;
$CamposEscondidos=array('id');
$UltimoCampo='nombre';
$Encabezados=array(
		'clave'=>'Id Agrupador',
		'nombre'=>'Nombre',
);
if(!isset($_REQUEST['Editar']))
	$Grupos=new tablas("Select * from ".$NombreTabla,$NombreTabla,$Encabezados,$db);
else{
	$Grupos=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."<>'".$_REQUEST['Editar']."'",$NombreTabla,$Encabezados,$db);
	$Grupo=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."='".$_REQUEST['Editar']."'",$NombreTabla,$Encabezados,$db);
}

if(isset($_REQUEST['New'])){
		$_REQUEST['data'][$ClaveTablaPrincipalId]=$ClaveTablaPrincipalIdDefault;
        
        $query = "select null from ".$NombreTabla." where clave=".$_REQUEST['data']['clave'];
        $res=sql_dq($query);
        $row=mysql_fetch_assoc($res);

        if(!$row and $_REQUEST['data']['clave'])
		$Grupos->Save($_REQUEST['data']);

		$Grupos=new tablas("Select * from ".$NombreTabla,$NombreTabla,$Encabezados,$db);
}else
if(isset($_REQUEST['Edith'])){
	if(strlen($_REQUEST['data']['clave'])<21){
		$Grupo=new tablas("Select * from ".$NombreTabla." where ".$ClaveTablaPrincipalId."='".$_REQUEST['data']['id']."'",$NombreTabla,$db);
		$f=$Grupo->first();
		//validacion
		if($f['clave']<>$_REQUEST['data']['clave']){
			$SQL='update stockmaster set id_agrupador="'.$_REQUEST['data']['clave'].'" where id_agrupador="'.$f['clave'].'"';
			DB_query($SQL,$db);
		}
		$Grupo->Save($_REQUEST['data']);
		unset($Grupo);
	}
	
}else
if(isset($_REQUEST['Borrar'])){
		$Grupos->Delete($_REQUEST['Borrar'],$ClaveTablaPrincipalId);
}


echo '<center>';
echo '<form method=post action="'.$_SERVER["SCRIPT_NAME"].'">';
echo '<table>';

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
	echo '<td>';
		echo '<a href="?Borrar='.$val[$ClaveTablaPrincipalId].'">'._('Borrar').'</a>';
		echo '<a href="?Editar='.$val[$ClaveTablaPrincipalId].'">'._('Editar').'</a>';
	echo '</td>';
	echo "</tr>";
}

if(!$Grupo){
	echo '<tr>';
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
		}
		
			echo '</td>';
	}
	echo '</tr>';
}

echo '</table>';
echo '</form>';
echo '</center>';
include ('includes/footer.inc');