<?php
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// REMISIONADO
$sql = "SELECT SUM(ovamount+ovgst) as fact, MONTH(rh_createdate) as mes FROM debtortrans 
WHERE type=20000 AND MONTH(rh_createdate)>0 
AND MONTH(rh_createdate)<13 
AND YEAR(rh_createdate)=YEAR(NOW())
AND rh_status != 'C'
GROUP BY MONTH(rh_createdate)";

$data = array();
$x = array();
$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['fact'];
  $x[] = $row['mes'];
}

include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();
$g->title( 'Remisionado al Mes', '{font-size: 20px;}' );

//
// BAR CHART:
//
$g->set_data( $data );
$g->bar_glass( 50, '0x639F45', '0x639F45', '$ M.N.', 10 );
//
// ------------------------
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
$g->set_y_legend( 'REMISIONADO', 12, '#736AFF' );
echo $g->render();
?>