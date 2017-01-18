<?php

include('includes/SQL_CommonFunctions.inc');
$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte Abonos - Cliente /Periodos' );
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
	style="width: 100%;" action="Reporte_Abonoperiodo.php" target="_blank">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Filtro de seleccion</b></td>
	</tr>
	<tr>
		<td>Periodo:</td>
		<td><select name="prd" style="width: 100%">
<?php
$sql = "select periodno,lastdate_in_period from periods order by periodno";
$result = DB_query ( $sql, $db );
while ( $myrow = DB_fetch_array ( $result ) ) {
	echo "<option " . (($myrow ['periodno'] == $_POST ['prd'] ? "selected='selected'" : " ")) . "value='" . $myrow ['periodno'] . "'>"._(MonthAndYearFromSQLDate($myrow ['lastdate_in_period'])) . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td>Desde el Cliente:</td>
		<td><select name="cliente_ini" style="width: 100%">
<?php
$sql = "select debtorno,name from debtorsmaster order by debtorno;";
$result = DB_query ( $sql, $db );
echo "<option value='%'>---------------------------------Todos---------------------------------</option>";
while ( $myrow = DB_fetch_array ( $result ) ) {
	echo "<option " . ((in_array($myrow ['debtorno'],$_POST ['cliente'])? "selected='selected'" : " ")) . "value='" . $myrow ['debtorno'] . "'>".$myrow ['debtorno'].' - '. $myrow ['name'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td>Hasta el Cliente:</td>
		<td><select name="cliente_fin" style="width: 100%">
<?php
$sql = "select debtorno,name from debtorsmaster order by debtorno;";
$result = DB_query ( $sql, $db );
echo "<option value='%'>---------------------------------Todos---------------------------------</option>";
while ( $myrow = DB_fetch_array ( $result ) ) {
	echo "<option " . ((in_array($myrow ['debtorno'],$_POST ['cliente'])? "selected='selected'" : " ")) . "value='" . $myrow ['debtorno'] . "'>".$myrow ['debtorno'].' - '. $myrow ['name'] . "</option>";
}

?>
                </select></td>
	</tr>
	<tr>
		<td align="center" colspan="2"><input type="submit" name="PrintPDF"
			value="Mostrar" style="width: 100%;" /></td>
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
      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.setLanguage('es');
      cal.manageFields("fecha_ini", "fecha_ini", "%Y-%m-%d");

    //]]>
</script>
<?php
include ('includes/footer.inc');
?>