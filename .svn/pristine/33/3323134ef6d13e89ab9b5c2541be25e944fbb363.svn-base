<?php

//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");

$SQLorders="SELECT salesorderdetails.orderno,
					salesorderdetails.orderlineno,
					SUM(salesorderdetails.quantity) AS qty,
					salesorderdetails.unitprice,
					salesorderdetails.stkcode,
					stockmaster.description
					FROM salesorders,salesorderdetails
						INNER JOIN stockmaster ON stockmaster.stockid = salesorderdetails.stkcode
			where salesorderdetails.orderno=salesorders.orderno
			        GROUP BY stkcode
					 ORDER BY qty desc limit 8";
$resultord = mysql_query($SQLorders,$db);

$data = array();
$x = array();

while($row = mysql_fetch_array($resultord)){

	$data[] = $row['qty'];
	$x[] = $row['stkcode'];

}

include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();

//
// PIE chart, 60% alpha
//
$g->pie(60,'#505050','{font-size: 12px; color: #404040;');
//
// pass in two arrays, one of data, the other data labels
//
$g->pie_values( $data, $x );
//
// Colours for each slice, in this case some of the colours
// will be re-used (3 colurs for 5 slices means the last two
// slices will have colours colour[0] and colour[1]):
//
$g->pie_slice_colours( array('#E7B7A9','#7A743D','#B8AC65','#E3F5B1','#2E430A','#B6B6A5','#432804','#97A363'));

$g->set_tool_tip( '#val#%' );
$g->bg_colour = '#ffffff';

$g->title( 'TOP 8 Productos', '{font-size:18px; color: #97A363}' );
echo $g->render();
?>