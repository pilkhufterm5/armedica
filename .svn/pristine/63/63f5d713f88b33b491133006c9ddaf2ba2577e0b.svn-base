<?php
$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
require_once('CFD22Manager.php');

if(isset($_POST['download'])){
    $CFDManager = CFD22Manager::getInstance();
    try{
       $CFDManager->addKeys(base64_encode(file_get_contents($_FILES['cert']['tmp_name'])),base64_encode(file_get_contents($_FILES['keys']['tmp_name'])),$_POST['pass']);
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
		<td colspan="2" align="center"><b>Registro de CSD</b></td>
	</tr>
	<tr>
		<td>Certificado de CSD:</td>
		<td><input type="file" name="cert" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>Key de CSD:</td>
		<td><input type="file" name="keys" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td>Contrase&ntilde;a:</td>
		<td><input type="text" name="pass" style="width:100%;" />
        </td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" name="download" value="Agregar CSD" style="width:100%;display:inline;float:left" />
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
$dom->loadXML($CFDManager->getCertificates());
$rows = $dom->getElementsByTagName('row');
echo "<center><table>
    <tr>
        <td>No. Certificado</td>
        <td>Valido desde</td>
        <td>Valido hasta</td>
        <td>Status</td>
    </tr>";
foreach($rows as $row){
   echo "<tr>";
   echo "<td>".$row->getAttribute('nocert')."</td>";
   echo "<td align='right'>".$row->getAttribute('from')."</td>";
   echo "<td align='right'>".$row->getAttribute('to')."</td>";
   echo "<td align='right'>".$row->getAttribute('status')."</td>";
   echo "</tr>";
}
echo "</table></center>";

include ('includes/footer.inc');
?>
