<?php

//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");

$SQLorders="SELECT
				stockmoves.stockid,
				SUM(stockmoves.price*stockmoves.qty*-1) as totalprice
				FROM stockmoves
				INNER JOIN locations ON locations.loccode = stockmoves.loccode
				WHERE
				stockmoves.type IN (10, 11, 20000)
				AND stockmoves.loccode ".$_GET['location']."
				AND stockmoves.prd >= ".$_GET['FromDate']."
				AND stockmoves.prd <= ".$_GET['ToDate']."
				GROUP BY stockmoves.stockid
				LIMIT 8";

$resultord = mysql_query($SQLorders,$db);

$data = array();
$x = array();

while($row = mysql_fetch_array($resultord)){

	$data[] = $row['totalprice'];
	$x[] = $row['stockid'];

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
$g->pie_slice_colours( array('#d01f3c','#356aa0','#C79810','0x639F45') );

$g->set_tool_tip( '$ #val#' );
$g->bg_colour = '#ffffff';

$g->title( 'TOP 8 Precio', '{font-size:18px; color: #d01f3c}' );
echo $g->render();
?>