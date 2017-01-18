<?php
$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte Ventas - Cobranza' );
include ('includes/header.inc');
?>
<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/steel/steel.css" />
<form name="Form" method="POST" enctype="multipart/form-data"
	style="width: 100%;" action="Reporte_ClienteXRuta.php" target="_blank">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Filtro de seleccion</b></td>
	</tr>
	<tr>
		<td>Municipio:</td>
		<td><select name="muni[]" style="width: 100%"  multiple="multiple" size="6">
<?php
$sql = "select DISTINCT  braddress7 from custbranch order by braddress7;";
$result = DB_query ( $sql, $db );
while ( $myrow = DB_fetch_array ( $result ) ) {
	echo "<option " . (($myrow ['braddress7'] == $_POST ['braddress7'] ? "selected='selected'" : " ")) . "value='" . $myrow ['braddress7'] . "'>" . $myrow ['braddress7'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td>Ruta:</td>
		<td><select name="ruta[]" id="ruta" style="width: 100%" multiple="multiple" size="6">
<?php
$sql = "select id,descripcion from rh_rutas;";
$result = DB_query ( $sql, $db );
while ( $myrow = DB_fetch_array ( $result ) ) {

	echo "<option " . ((in_array($myrow ['id'], $_POST ['descripcion'])  ? "selected='selected'" : " ")) . "value='" . $myrow ['id'] . "'>" . $myrow ['descripcion'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td align="center" colspan="2">Mostrar Detalle<br />
			<input type="submit" name="PrintPDF" value="Pdf" style="width: 50%; display:inline;float:left" />
			<input type="submit" name="PrintXls" value="Excel" style="width: 50%;display:inline;float:left" />
			</td>
    </tr>
</table>
</center>
</form>
<br />
<br />
<br />
<br />
<br />
<script type="text/javascript">//<![CDATA[
      var cal2 = Calendar.setup({
          onSelect: function(cal2) { cal2.hide() },
          showTime: false
      });

      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.setLanguage('es');
      cal.manageFields("fecha_ini", "fecha_ini", "%Y-%m-%d");

      cal2.setLanguage('es');
      cal2.manageFields("fecha_fin", "fecha_fin", "%Y-%m-%d");
    //]]>
</script>
<?php
include ('includes/footer.inc');
?>