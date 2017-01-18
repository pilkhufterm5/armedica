<?php


$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
require_once('CFD22Manager.php');

if(isset($_POST['serie'])&&isset($_POST['folioini'])&&isset($_POST['foliofin'])&&isset($_POST['anoauth'])&&isset($_POST['noauth'])){
    $CFDManager = CFD22Manager::getInstance();
    try{
       $CFDManager->addSerie($_POST['serie'],$_POST['anoauth'],$_POST['noauth'],$_POST['folioini'],$_POST['foliofin']);
    }catch(Exception $e){
       echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e . '<p></div>';
    }
}
?>

<form name="Form" method="POST" enctype="multipart/form-data"
	style="width: 100%;" action="">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Registro de Folios</b></td>
	</tr>
	<tr>
		<td>Serie:</td>
		<td><input type="text" name="serie" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>Folio Inicial:</td>
		<td><input type="text" name="folioini" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>Folio Final:</td>
		<td><input type="text" name="foliofin" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>A&ntilde;o de Aprovaci&oacute;n:</td>
		<td><input type="text" name="anoauth" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>No. de Aprovaci&oacute;n:</td>
		<td><input type="text" name="noauth" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" name="download" value="Agregar Folios" style="width: 100%; display:inline;float:left" />
			</td>
    </tr>
</table>
</center>
</form>
<br />
<br />
<br />
<br />
<?php
$CFDManager = CFD22Manager::getInstance();
$dom = new DOMDocument('1.0', 'utf-8');
$dom->loadXML($CFDManager->getSeries());
$rows = $dom->getElementsByTagName('row');
echo "<center><table>
    <tr>
        <td>Serie</td>
        <td>Folio Inicial</td>
        <td>Folio Final</td>
        <td>Siguiente</td>
        <td>Año de Autorizacion</td>
        <td>No. de Autorizacion</td>
    </tr>";
foreach($rows as $row){
   echo "<tr>";
   echo "<td>".$row->getAttribute('serie')."</td>";
   echo "<td align='right'>".$row->getAttribute('ini')."</td>";
   echo "<td align='right'>".$row->getAttribute('fin')."</td>";
   echo "<td align='right'>".$row->getAttribute('folio')."</td>";
   echo "<td align='right'>".$row->getAttribute('aauth')."</td>";
   echo "<td align='right'>".$row->getAttribute('noauth')."</td>";
   echo "</tr>";

}
echo "</table></center>";

include ('includes/footer.inc');
?>