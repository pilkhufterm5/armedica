<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-10-02 12:30:15 -0500 (Thu, 02 Oct 2008) $
 * $Rev: 420 $
 * PAGINA PRINCIPAL DEL DASHBOARD
 */
$PageSecurity = 8;
include('includes/session.inc');

$title = _('DashBoard');
include('includes/header.inc');
/****************************************************************************************************************************
* Jorge Garcia
* 30/Ene/2009 Seleccionar mes
****************************************************************************************************************************/
if(isset($_POST['Show'])){
	include_once 'php-ofc-library/open_flash_chart_object.php';
	echo "<center>";
	$online = 'http://'.$_SERVER['SERVER_NAME'].'/erp_test/mangueras_erp_test';
	open_flash_chart_object( 800, 400, $online .'/rh_charts2.php',false); // facturado al mes
	//echo "&nbsp;&nbsp;&nbsp;&nbsp; ";
	//open_flash_chart_object( 500, 250, $online .'/rh_charts11.php',false); // ingresos por ventas al mes
	echo "&nbsp;&nbsp;&nbsp;&nbsp; <br>";
	open_flash_chart_object( 800, 400, $online . '/rh_charts4.php',false); // comprado al mes
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<br>";
	open_flash_chart_object( 500, 250, $online . '/rh_charts3.php',false); // remisionado al mes
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	open_flash_chart_object( 500, 250, $online . '/rh_charts6.php',false); // punto de venta
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<br>";
	open_flash_chart_object( 500, 250, $online . '/rh_charts5.php',false); // facturado-pagado
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<br>";
	open_flash_chart_object( 500, 250, $online . '/rh_charts7.php',false); // top 10 productos
	echo "&nbsp;&nbsp;&nbsp;&nbsp;<br>";
	open_flash_chart_object( 500, 250, $online . '/rh_charts9.php',false); // top 10 productos
	
	echo "</center>";
	unset($_SESSION['rh_year']);
}else{
	echo '<FORM METHOD="POST" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '">';
	echo '<CENTER><TABLE><TR><TD>' . _('Year') . ': </TD><TD><SELECT Name="FromPeriod">';
	$sql = "SELECT MIN(lastdate_in_period) as min, MAX(lastdate_in_period) as max FROM periods";
	$Periods = DB_query($sql,$db);
	$myrow = DB_fetch_array($Periods,$db);
	$rh_min = date('Y',strtotime($myrow['min']));
	$rh_max = date('Y',strtotime($myrow['max']));
	while($rh_min <= $rh_max){
		echo "<OPTION value='".$rh_min."'>".$rh_min;
		$rh_min++;
	}
	echo "</SELECT></TD></TR></TABLE>
	<CENTER>
	<INPUT TYPE=SUBMIT Name='Show' Value='"._('Show')."'>
	</CENTER></FORM>";
}
include('includes/footer.inc');
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
?>
