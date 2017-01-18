<?php
$PageSecurity = 2;
$datax = array();
$datay = array();
include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();
$db = mysql_connect("localhost", "root","chilaquiles") or die("Could not connect");  
mysql_select_db("base_erp308",$db) or die("Could not select database");
$count=0;
$qty=0;
$price=0;
$count=$count+1;
$SQLorders="SELECT salesorderdetails.orderno,
					salesorderdetails.orderlineno,
					SUM(salesorderdetails.quantity) AS qty,
					salesorderdetails.unitprice,
					salesorderdetails.stkcode
					FROM salesorders,salesorderdetails
			where salesorderdetails.orderno=salesorders.orderno
			        GROUP BY stkcode
					 ORDER BY qty desc limit 10";
$resultord = mysql_query($SQLorders,$db);
//echo '<br>'.$SQLorders;
//$total=0;
while($myroword=mysql_fetch_array($resultord))
{
   $datay[]=$myroword['qty'];
	 $datax[]=$myroword['stkcode'];
	 }
	/*$qty=$myroword['qty'];
	$price=$myroword['unitprice'];
	$total=$total+$qty*$price;
	//echo '<br>qty '.$qty.' price '.$price.' total '.$total.'<br>';
}	
/*$sql="SELECT salesorders.orderno,salesorders.orddate,
	   salesorderdetails.orderlineno,
	   salesorderdetails.orderno,
		salesorderdetails.quantity,
	   salesorderdetails.stkcode,
	   SUM(salesorderdetails.quantity) as totqty
	   from salesorders,salesorderdetails
	   where salesorders.orddate >='$newdate'
		AND salesorders.orddate <='date(y,m,d)'
		AND salesorders.orderno=salesorderdetails.orderno
		GROUP BY salesorderdetails.stkcode 
		ORDER BY totqty DESC LIMIT 15";*/
/*$result=mysql_query($sql,$db);
while($row=mysql_fetch_array($result))
{
	 $datay[]=$row['qty'];
	 $datax[]=$row['stkcode'];
	//$bar->add_link( $row['ordervalue'],"http://localhost/werp308/PDFtop10orderInvoiced.php?customer=$row[name]" );
}

*/
$p=$datay[0];
$g->title( 'Top 10 products', '{font-size: 15px; color: #2693CF}' );
$g->set_data( $datay);
$g->bar( 50, '0x639F45', 'QUANTITY', 14 );
$g->set_x_labels($datax);
$g->set_y_max((floor($p/50)+1)*50);
$g->y_label_steps(5);
$g->set_x_label_style( 10, '#CC3399', 2 );
$g->set_y_legend( 'QTY ORDERED', 14, '0x639F45' );
echo $g->render();
?>