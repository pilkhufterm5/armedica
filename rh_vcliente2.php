<?php
$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte Ventas - Cliente-Marca' );
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
	style="width: 100%;" action="Reporte_VentasCliente2.php" target="_blank">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Filtro de seleccion</b></td>
	</tr>
	<tr>
		<td width="20%">Fecha inicial:</td>
		<td width="80%"><input type="text" name="fecha_ini" id="fecha_ini"
			style="width: 100%" value="<?php
			echo $_POST ['fecha_ini']?>" /></td>
	</tr>
	<tr>
		<td>Fecha Final:</td>
		<td><input type="text" name="fecha_fin" id="fecha_fin"
			style="width: 100%" value="<?php
			echo $_POST ['fecha_fin']?>" /></td>
	</tr>
	<tr>
		<td>Cliente:</td>
		<td><select name="sucursal[]" style="width: 100%"  multiple="multiple" size="6">
<?php
$sql = "select debtorno,name from debtorsmaster;";
$result = DB_query ( $sql, $db );
while ( $myrow = DB_fetch_array ( $result ) ) {
	echo "<option " . (($myrow ['debtorno'] == $_POST ['sucursal'] ? "selected='selected'" : " ")) . "value='" . $myrow ['debtorno'] . "'>" . $myrow ['name'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td>Marca:</td>
		<td><select name="marca[]" id="marca" style="width: 100%" multiple="multiple" size="6">
<?php
$sql = "select id,nombre from rh_marca;";
$result = DB_query ( $sql, $db );
while ( $myrow = DB_fetch_array ( $result ) ) {

	echo "<option " . ((in_array($myrow ['id'], $_POST ['marca'])  ? "selected='selected'" : " ")) . "value='" . $myrow ['id'] . "'>" . $myrow ['nombre'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" name="PrintPDF"
			value="Mostrar Detalle" style="width: 100%;" /></td>
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