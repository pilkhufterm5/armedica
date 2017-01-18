<?php
// TOP DE CLIENTES
//include('config.php');
$PageSecurity = 1;
include('includes/session.inc');
   $db = mysql_connect($host, $dbuser,$dbpassword) or die("Could not connect");
   mysql_select_db($_SESSION['DatabaseName'],$db) or die("Could not select database");

$SQLorders="SELECT debtortrans.debtorno, SUM(ovamount) as total FROM debtortrans WHERE 
	type = 10
	AND rh_status != 'C'
	GROUP BY debtortrans.debtorno
	ORDER BY total DESC 
	LIMIT 7";
$resultord = mysql_query($SQLorders,$db);

$data = array();
$x = array();
$resto = '';
while($row = mysql_fetch_array($resultord)){

	$data[] = $row['total'];
	$x[] = $row['debtorno'];
	$resto .= "'".$row['debtorno']."', ";

}

$resto = substr($resto, 0, strlen($resto)-2);
$sql_resto = "SELECT SUM(ovamount) AS total FROM debtortrans WHERE
				type = 10
				AND rh_status != 'C'
				AND debtorno NOT IN (".$resto.")
				GROUP BY type";
$r_res = mysql_query($sql_resto,$db);
$r_info = mysql_fetch_array($r_res);
if($r_info['total']<=0){
	$r_info['total']=0;
}
$data[] = $r_info['total'];
$x[] = 'Resto';

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

$g->set_tool_tip( '#val#' );
$g->bg_colour = '#ffffff';

$g->title( 'TOP 8 Clientes', '{font-size:18px; color: #d01f3c}' );
echo $g->render();
?>