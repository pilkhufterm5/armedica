<?php
// bowikaxu realhost june 2008 - dashboard de compras
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO AL MES
$sql = "SELECT SUM(qtyinvoiced) AS invoiced, SUM(quantityord) AS ordered, SUM(quantityrecd) AS received, orderno
FROM purchorderdetails
WHERE qtyinvoiced < quantityord
OR quantityrecd < quantityord
GROUP BY orderno";

$data = array();

include_once( 'php-ofc-library/open-flash-chart.php' );

//
// BAR CHART:
//
$bar_1 = new bar( 50, '#0066CC' );
$bar_1->key( 'Facturado', 10 );

$bar_2 = new bar( 50, '#9933CC' );
$bar_2->key( 'Ordenado', 10 );

$bar_3 = new bar( 50, '#639F45' );
$bar_3->key( 'Recibido', 10 );

$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['orderno'];
  $bar_1->data[] = $row['invoiced'];
  $bar_2->data[] = $row['ordered'];
  $bar_3->data[] = $row['received'];
}
// obtener el valor maximo
$max1 = array();
$max1[0] = max($bar_1->data);
$max1[1] = max($bar_2->data);
$max1[2] = max($bar_3->data);
$maxfinal = max($max1);

$g = new graph();
$g->title( 'Diferencias en Compras', '{font-size: 18px;}' );
//
// ------------------------
//
// add the 3 bar charts to it:
$g->data_sets[] = $bar_1;
$g->data_sets[] = $bar_2;
$g->data_sets[] = $bar_3;
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
$g->set_y_max( $maxfinal+3 );
$g->y_label_steps( 2 );
$g->set_y_legend( 'COMPRAS', 12, '#736AFF' );
echo $g->render();
?>