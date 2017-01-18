<?php
// bowikaxu realhost june 2008 - Stock Usage Graph
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");
// FACTURADO AL MES
if($_GET['StockLocation']=='All'){
	$sql = "SELECT periods.periodno, 
			periods.lastdate_in_period, 
			SUM(-stockmoves.qty) AS qtyused 
		FROM stockmoves INNER JOIN periods 
			ON stockmoves.prd=periods.periodno 
		WHERE (stockmoves.type=10 OR stockmoves.type=11 OR stockmoves.type=28) 
		AND stockmoves.hidemovt=0 
		AND stockmoves.stockid = '" . trim(strtoupper($_GET['StockID'])) . "' 
		GROUP BY periods.periodno, 
			periods.lastdate_in_period 
		ORDER BY periodno  LIMIT 24";
} else {
	$sql = "SELECT periods.periodno, 
			periods.lastdate_in_period, 
			SUM(-stockmoves.qty) AS qtyused 
		FROM stockmoves INNER JOIN periods 
			ON stockmoves.prd=periods.periodno 
		WHERE (stockmoves.type=10 Or stockmoves.type=11 OR stockmoves.type=28) 
		AND stockmoves.hidemovt=0 
		AND stockmoves.loccode='" . $_GET['StockLocation'] . "' 
		AND stockmoves.stockid = '" . trim(strtoupper($_GET['StockID'])) . "' 
		GROUP BY periods.periodno, 
			periods.lastdate_in_period 
		ORDER BY periodno  LIMIT 24";
}
//echo $sql;
$date = array();
$qty = array();
include_once( 'php-ofc-library/open-flash-chart.php' );

$res = mysql_query($sql,$db) or die("Bad SQL 1");
while( $row = mysql_fetch_array($res) )
{
  $date[] = $row['lastdate_in_period'];
  $qty[] = $row['qtyused'];
}

$g = new graph();
$g->title( 'Uso del Producto', '{font-size: 18px;}' );
// we add 3 sets of data:
$g->set_data( $qty );
$g->line_hollow( 2, 4, '0x80a033', 'Cantidad', 10 );

$g->set_x_labels( $date );

$g->set_x_label_style( 8, '0x000000', 0, 1 );

$g->bg_colour = '#ffffff';
$g->set_y_max( max($qty)+10);
$g->y_label_steps( 1 );
$g->set_y_legend( 'Cantidad', 12, '#736AFF' );
echo $g->render();
?>