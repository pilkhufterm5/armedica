<?php
// bowikaxu realhost june 2008 - dashboard de compras / gastos al mes
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO/INGRESOS AL MES
$sql = "SELECT 
SUM(
(CASE WHEN type = 20 THEN
	ovamount
ELSE
	0
END)
) AS fact,
SUM(
(CASE WHEN type = 22 THEN
	-ovamount
ELSE
	0
END)
) AS gasto,
DATE_FORMAT(trandate,'%b') as mes FROM supptrans 
WHERE type IN (20,22) AND MONTH(trandate)>0 
AND MONTH(trandate)<13 
AND YEAR(trandate)=YEAR(NOW())
GROUP BY MONTH(trandate)";

$data = array();

include_once( 'php-ofc-library/open-flash-chart.php' );

//
// BAR CHART:
//
$bar_1 = new bar_glass( 50, '#385E0F', '#385E0F', 'Factura', 10 );
$bar_1->key( 'Factura', 10 );

$bar_2 = new bar_glass( 50, '#CD9B1D', '#CD9B1D', 'Gasto', 10 );
$bar_2->key( 'Gastos', 10 );

$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['mes'];
  $bar_1->data[] = $row['fact'];
  $bar_2->data[] = $row['gasto'];
}
// obtener el valor maximo
$max1 = array();
$max1[0] = max($bar_1->data);
$max1[1] = max($bar_2->data);
$maxfinal = max($max1);

$g = new graph();
$g->title( 'Factura/Gasto por Compras al Mes', '{font-size: 18px;}' );
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
$g->set_x_label_style( 10, '#691F01', 0, 1 );
//
// and tick every second value:
$g->set_x_axis_steps( 2 );
//

$g->bg_colour = '#ffffff';
$g->set_y_max( $maxfinal+10 );
$g->y_label_steps( 2 );
$g->set_y_legend( 'Factura/Gastos por Compras', 12, '#691F01' );
echo $g->render();
?>