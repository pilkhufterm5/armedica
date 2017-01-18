<?php
// bowikaxu realhost june 2008 - dashboard de comparativa cuentas ejecutado / presupuetado
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO/INGRESOS AL MES
$sql='SELECT accountcode, 
							period, 
							budget, 
							actual
						FROM chartdetails 
						WHERE period ='. $_GET['period'] . ' AND  accountcode = ' . $_GET['account'];

include_once( 'ofc-library/open-flash-chart.php' );

$bar_1 = new bar_glass( 55, '#D54C78', '#C31812' );
$bar_1->key( 'Ejecutado', 10 );
//
// create a 2nd set of bars:
//
$bar_2 = new bar_glass( 55, '#5E83BF', '#424581' );
$bar_2->key( 'Presupuestado', 10 );

//DATA
$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $data[] = $row['period'];
  $bar_1->data[] = $row['acual'];
  $bar_2->data[] = $row['budget'];
}

//
// create the chart:
//
$g = new graph();
$g->title( 'Ejecutado / Presupuestado', '{font-size:20px; color: #bcd6ff; margin:10px; background-color: #5E83BF; padding: 5px 15px 5px 15px;}' );

// add both sets of bars:
$g->data_sets[] = $bar_1;
$g->data_sets[] = $bar_2;

// label the X axis (10 labels for 10 bars):
$g->set_x_labels( array( 'January','February','March','April','May','June','July','August','September','October' ) );

// colour the chart to make it pretty:
$g->x_axis_colour( '#909090', '#D2D2FB' );
$g->y_axis_colour( '#909090', '#D2D2FB' );

$g->set_y_min( -5 );
$g->set_y_max( 10 );
$g->y_label_steps( 6 );
$g->set_y_legend( 'Open Flash Chart', 12, '#736AFF' );
echo $g->render();
?>