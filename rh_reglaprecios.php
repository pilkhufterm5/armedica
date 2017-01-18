<?php
/* $Revision: 1 $ */

/**************************************************************************
* Jorge Garcia
* 08/Dic/2008 Archivo creado para la regla de precios
***************************************************************************/
//Seguridad de la pagina
$PageSecurity = 15;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Regla de Descuentos');
include('includes/header.inc');

echo "<CENTER><H1>"._('Regla de Descuentos')."</H1></CENTER>";
?>
<script type="text/javascript" src="rh_java/rh_reglaprecios.js"></script>
<?

$sqlcount = 'SELECT COUNT(debtorno) AS paginas FROM rh_reglaprecios';
$resultcount = DB_query($sqlcount,$db);
$rowcount = DB_fetch_array($resultcount);
$tpagarray = explode('.',($rowcount['paginas']/20));
if($tpagarray[1] > 0){
	$tpag = $tpagarray[0] + 1;
}else{
	if($tpagarray[1] == 0){
		$tpag = 1;
	}else{
		$tpag = $tpagarray[0];
	}
}
echo "
<BR>
<span id='msn'></span>
<CENTER>
<FORM name='forma'>
<input type='hidden' id='actulpagina' value='1'><input type='hidden' id='totalpagina' value='".$tpag."'><input type='hidden' id='ordenado' value='debtorno'><input type='hidden' id='ad' value='ASC'>
</FORM>
<span id='datagrid'>
<TABLE width='90%'>
<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=3>pagina: 1 / ".$tpag."</TD><TD align=right colspan=3><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
<TR>
<TD CLASS='tableheader' onClick=ordenar('debtorno')>Cliente</TD>
<TD CLASS='tableheader' width='18%' onClick=ordenar('id_marca')>Marca</TD>
<TD CLASS='tableheader' width='18%' onClick=ordenar('categoryid')>Categor&iacute;a</TD>
<TD CLASS='tableheader' width='24%' onClick=ordenar('stockid')>Art&iacute;culo</TD>
<TD CLASS='tableheader' width='4%'>Descuento(%)</TD>
<TD CLASS='tableheader' width='4%'></TD>
<TD CLASS='tableheader' width='4%'></TD>
</TR>";
$sql = 'SELECT * FROM rh_reglaprecios ORDER BY debtorno ASC LIMIT 0,20';
$result = DB_query($sql,$db);
$k=0;
while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		$color = "BGCOLOR='#CCCCCC'";
		$k=0;
	} else {
		$color = "BGCOLOR='#EEEEEE'";
		$k=1;
	}
	if($myrow['debtorno'] == 'ALL'){
		$name = 'Todos';
	}else{
		$sql2 = "SELECT name FROM debtorsmaster WHERE debtorno = '".$myrow['debtorno']."'";
		$result2 = DB_query($sql2,$db);
		$myrow2 = DB_fetch_array($result2);
		$name = $myrow2['name'];
	}
	if($myrow['id_marca'] == 'ALL'){
		$nombre = 'Todos';
	}else{
		$sql2 = "SELECT nombre FROM rh_marca WHERE id = '".$myrow['id_marca']."'";
		$result2 = DB_query($sql2,$db);
		$myrow2 = DB_fetch_array($result2);
		$nombre = $myrow2['nombre'];
	}
	if($myrow['categoryid'] == 'ALL'){
		$categorydescription = 'Todos';
	}else{
		$sql2 = "SELECT categorydescription FROM stockcategory WHERE categoryid = '".$myrow['categoryid']."'";
		$result2 = DB_query($sql2,$db);
		$myrow2 = DB_fetch_array($result2);
		$categorydescription = $myrow2['categorydescription'];
	}
	if($myrow['stockid'] == 'ALL'){
		$description = 'Todos';
	}else{
		$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
		$result2 = DB_query($sql2,$db);
		$myrow2 = DB_fetch_array($result2);
		$description = $myrow2['description'];
	}
	echo "<TR ".$color.">
	<TD align=left>"."[".$myrow['debtorno']."] ".$name."</TD>
	<TD>"."[".$myrow['id_marca']."] ".$nombre."</TD>
	<TD>"."[".$myrow['categoryid']."] ".$categorydescription."</TD>
	<TD>"."[".$myrow['stockid']."] ".$description."</TD>
	<TD align=right>".$myrow['descuento']." %</TD>
	<TD align=right><a href=# onClick=\"edita('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Edit')."</a></TD>
	<TD align=right><a href=# onclick=\"borra('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Delete')."</a></TD>
	</TR>";
}
echo "</TABLE>
</span>
</CENTER>
<P>
<CENTER>
<FORM NAME='forma2'>
<input type='hidden' id='pros' value='0'>
<TABLE BORDER=1>
<TR>
<TD>
<TABLE>
<TR>
<TD>"._('Cliente').":</TD>
<TD><SELECT name='cliente'>
<OPTION value='ALL'>"._('All')."</OPTION>";
$sqlclie = 'SELECT debtorno, name FROM debtorsmaster ORDER BY name ASC';
$resultclie = DB_query($sqlclie,$db);
while($rowclie = DB_fetch_array($resultclie)){
	echo "<OPTION value='".$rowclie['debtorno']."'>[".$rowclie['debtorno']."] ".$rowclie['name']."</OPTION>";
}
echo "</SELECT></TD>
</TR>
<TR>
<TD>"._('Marca').":</TD>
<TD><SELECT name='marca' onChange=cambio()>
<OPTION value='ALL'>"._('All')."</OPTION>";
$sqlclie = 'SELECT id, nombre FROM rh_marca ORDER BY nombre ASC';
$resultclie = DB_query($sqlclie,$db);
while($rowclie = DB_fetch_array($resultclie)){
	echo "<OPTION value='".$rowclie['id']."'>".$rowclie['nombre']."</OPTION>";
}
echo "</SELECT></TD>
</TR>
<TR>
<TD>"._('Categor&iacute;a').":</TD>
<TD><SELECT name='categoria' onChange=cambio()>
<OPTION value='ALL'>"._('All')."</OPTION>";
$sqlclie = 'SELECT categoryid, categorydescription FROM stockcategory ORDER BY categorydescription ASC';
$resultclie = DB_query($sqlclie,$db);
while($rowclie = DB_fetch_array($resultclie)){
	echo "<OPTION value='".$rowclie['categoryid']."'>".$rowclie['categorydescription']."</OPTION>";
}
echo "</SELECT></TD>
</TR>
<TR>
<TD colspan=2>
<span id='arti'>
<input type='hidden' id='actulpagina2' value='1'><input type='hidden' id='totalpagina2' value='1'><input type='hidden' id='ordenado2' value='stockid'><input type='hidden' id='ad2' value='ASC'><input type='hidden' id='mostrar' value='0'><input type='hidden' id='buscacion' value='SINARTICULOS'><input type='hidden' id='articulo' value='ALL'><a href=# onClick=marticulos()>"._('Mostrar Art&iacute;culos')."</a>
</span></TD>
</TR>
<TR>
<TD><span id='descuen'>"._('Descuento')."(%):</span></TD>
<TD>
<span id='descuen2'>
<INPUT TYPE='text' SIZE=2 MAXLENGTH=2 NAME='descuento'>
</span>
</TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
</FORM>
<p>
<span id='btns'>
<INPUT TYPE='button' VALUE="._('Accept')." onclick=\"graba()\">
<INPUT TYPE='button' VALUE="._('Cancel')." onclick=\"cancela()\">
</span>
</CENTER>";

/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/

include('includes/footer.inc');
?>
