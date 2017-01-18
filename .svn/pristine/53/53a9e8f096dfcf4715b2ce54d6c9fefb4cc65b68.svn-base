<?php
// bowikaxu realhost june 2008 - dashboard de facturas / ingresos al mes
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO/INGRESOS AL MES
$sql = "SELECT SUM((CASE WHEN type = 10 THEN ovamount+ovgst ELSE 0 END)) AS fact, SUM((CASE WHEN type = 12 THEN -ovamount-ovdiscount ELSE 0 END)) AS ingreso, DATE_FORMAT(trandate,'%b') as mes FROM debtortrans WHERE type IN (10,12) AND MONTH(trandate)>0 AND MONTH(trandate)<13 AND YEAR(trandate)=YEAR(NOW()) AND rh_status != 'C' GROUP BY MONTH(trandate)";
$data = array();

include_once( 'php-ofc-library/open-flash-chart.php' );

//
// BAR CHART:
//
$bar_1 = new bar_glass( 50, '#0066CC', '#0066CC', 'Facturado', 10 );
$bar_1->key( 'Facturado', 10 );

$bar_2 = new bar_glass( 50, '#9933CC', '#9933CC', 'Ingreso', 10 );
$bar_2->key( 'Ingresos', 10 );

$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['mes'];
  $bar_1->data[] = $row['fact'];
  $bar_2->data[] = $row['ingreso'];
}
// obtener el valor maximo
$max1 = array();
$max1[0] = max($bar_1->data);
$max1[1] = max($bar_2->data);
$maxfinal = max($max1);

$g = new graph();
$g->title( 'Facturado / Ingresado', '{font-size: 18px;}' );
//
// ------------------------
//
// add the 3 bar charts to it:
$g->data_sets[] = $bar_1;
$g->data_sets[] = $bar_2;
//
// X axis tweeks:
//array( 'January,February,March,April,May,June,July,August,September' )
$g->set_x_labels( $data );
//
// set the X axis to show every 2nd label:
//
$g->set_x_label_style( 10, '#9933CC', 0, 1 );
//
// and tick every second value:
$g->set_x_axis_steps( 2 );
//

$g->bg_colour = '#ffffff';
$g->set_y_max( $maxfinal+10 );
$g->y_label_steps( 2 );
$g->set_y_legend( 'Facturado / Ingresado', 12, '#736AFF' );
echo $g->render();
?>