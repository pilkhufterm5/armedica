<?php
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");

$sql = "SELECT SUM(rh_possales.total) AS total, rh_possales.user FROM rh_possales, salesorders
WHERE salesorders.orderno = rh_possales.trans
AND MONTH(orddate)>0 
AND MONTH(orddate)<13 
AND YEAR(orddate)=YEAR(NOW()) 
GROUP BY rh_possales.user";

$data = array();
$x = array();
$res = mysql_query($sql,$db) or die("Bad SQL 1");
if(mysql_num_rows($res)>0){
	while( $row = mysql_fetch_array($res) )
	{
	  $data[] = $row['total'];
	  $x[] = $row['user'];
	}
}else {
	echo "NO HAY DATOS";
	exit;
}

include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();
$g->title( 'Punto de Venta/Usuario', '{font-size: 20px;}' );

//
// BAR CHART:
//
$g->set_data( $data );
$g->bar_glass( 50, '0x0066CC', '0x0066CC', '$ M.N.', 10 );
//
// ------------------------
//

//
// X axis tweeks:
//array( 'January,February,March,April,May,June,July,August,September' )
$g->set_x_labels( $x );
//
// set the X axis to show every 2nd label:
//
$g->set_x_label_style( 10, '#9933CC', 0, 1 );
//
// and tick every second value:
//
$g->set_x_axis_steps( 1 );
//
$g->bg_colour = '#ffffff';
$g->set_y_max( max($data) );
$g->y_label_steps( 10 );
$g->set_y_legend( 'Punto de Venta', 12, '#736AFF' );
echo $g->render();
?>