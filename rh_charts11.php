<?php
/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 * INGRESO POR VENTAS
 */
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO AL MES
$sql = "SELECT SUM(-(ovamount/rate)) as ingreso, MONTH(rh_createdate) as mes FROM debtortrans 
WHERE type=12 AND MONTH(rh_createdate)>0 
AND MONTH(rh_createdate)<13 
AND YEAR(rh_createdate)=YEAR(NOW())
GROUP BY MONTH(rh_createdate)";

$data = array();
$x = array();
$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['ingreso'];
  $x[] = $row['mes'];
}

include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();
$g->title( 'Ingreso por Ventas', '{font-size: 20px;}' );

//
// BAR CHART:
//8010A0
$g->set_data( $data );
$g->bar_glass( 50, '#9933CC', '#778899', '$ M.N.', 10 );
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
$g->set_y_legend( 'INGRESO', 12, '#736AFF' );
echo $g->render();
?>