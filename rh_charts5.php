<?php
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO MENOS LO ASIGNADO
$sql = "SELECT SUM(ovamount+ovgst-alloc) as fact, MONTH(rh_createdate) as mes FROM debtortrans 
WHERE type=10 AND MONTH(rh_createdate)>0 
AND MONTH(rh_createdate)<13 
AND YEAR(rh_createdate)=YEAR(NOW())
GROUP BY MONTH(rh_createdate)";
//
// NOTE: how we are filling 3 arrays full of data,
//       one for each line on the graph
//
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
$g->title( 'Facturado - Asignado', '{font-size: 20px; color: #736AFF}' );

// we add 3 sets of data:
$g->set_data( $data );

// we add the 3 line types and key labels
$g->line_hollow( 2, 4, '0x80a033', 'Facturado-Asignado', 10 );

$g->set_x_labels( $x );
$g->set_x_label_style( 10, '0x000000', 0, 1 );
$g->bg_colour = '#ffffff';
$g->set_y_max( max($data) );
$g->y_label_steps( 10 );
$g->set_y_legend( 'Facturado-Asignado', 12, '#736AFF' );
echo $g->render();
?>