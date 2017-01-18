<?php
/* $Revision: 1 $ */

/**************************************************************************
* Jorge Garcia 03/Dec/2008
* Archivo creado y modificado para el catalogo de la sustancia activa
***************************************************************************/
//Seguridad de la pagina
$PageSecurity = 15;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Cat&aacute;logo Sustancia Activa');
include('includes/header.inc');

echo "<CENTER><H1>"._('Sustancia Activa')."</H1></CENTER>";
?>
<script type="text/javascript" src="rh_java/rh_sustanciaactiva.js"></script>
<?

$sqlcount = 'SELECT COUNT(id) AS paginas FROM rh_sustanciaactiva';
$resultcount = DB_query($sqlcount,$db);
$rowcount = DB_fetch_array($resultcount);
$tpagarray = explode('.',($rowcount['paginas']/20));
if($tpagarray[1] != ''){
	$tpag = $tpagarray[0] + 1;
}else{
	$tpag = $tpagarray[0];
}
echo "
<BR>
<span id='msn'></span>
<CENTER>
<span id='datagrid'>
<FORM name='forma'>
<input type='hidden' id='actulpagina' value='1'><input type='hidden' id='totalpagina' value='".$tpag."'><input type='hidden' id='ordenado' value='codigo'><input type='hidden' id='ad' value='ASC'>
<input type='text' id='buscacion'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
</FORM>
<TABLE width='45%'>
<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: 1 / ".$tpag."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
<TR>
<TD CLASS='tableheader' width='8%' onClick=ordenar('id')>Codigo</TD>
<TD CLASS='tableheader' onClick=ordenar('nombre')>Nombre</TD>
<TD CLASS='tableheader' width='8%'></TD>
<TD CLASS='tableheader' width='8%'></TD>
</TR>";
$sql = 'SELECT * FROM rh_sustanciaactiva ORDER BY id ASC LIMIT 0,20';
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
	echo "<TR ".$color.">
	<TD align=right>".$myrow['id']."</TD>
	<TD>".$myrow['nombre']."</TD>
	<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
	<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
	</TR>";
}
echo "</TABLE>
</span>
</CENTER>
<P>
<CENTER>
<FORM NAME='forma2'>
<INPUT DISABLED TYPE='hidden' NAME='id'>
<TABLE BORDER=1>
<TR>
<TD>
<TABLE>
<TR>
<TD>"._('Name').":</TD>
<TD><INPUT TYPE='text' SIZE=55 MAXLENGTH=200 NAME='nombre'></TD>
</TR>
</TABLE>
</TD>
</TR>
</TABLE>
</FORM>
<INPUT TYPE='button' VALUE="._('Accept')." onclick=\"graba()\">
<INPUT TYPE='button' VALUE="._('Cancel')." onclick=\"cancela()\">
</CENTER>";

/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/

include('includes/footer.inc');
?>