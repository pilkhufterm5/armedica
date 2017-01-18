<?php
$PageSecurity = 2;
$datax = array();
$datay = array();
include_once( 'php-ofc-library/open-flash-chart.php' );
$g = new graph();
$db = mysql_connect("localhost", "root","chilaquiles") or die("Could not connect");  
mysql_select_db("base_erp308",$db) or die("Could not select database");

/*$sql="SELECT salesorders.orderno,
                      salesorders.debtorno,
                      salesorders.branchcode,
                      salesorders.customerref,
                      salesorders.orddate,
                      salesorders.fromstkloc,
                      salesorders.printedpackingslip,
                      salesorders.datepackingslipprinted,
                      salesorderdetails.stkcode,
                      stockmaster.description,
                      stockmaster.units,
                      stockmaster.decimalplaces,
                      SUM(salesorderdetails.quantity) AS totqty,
                      SUM(salesorderdetails.qtyinvoiced) AS totqtyinvoiced
                  FROM salesorders
                     INNER JOIN salesorderdetails
                     ON salesorders.orderno = salesorderdetails.orderno
                     INNER JOIN stockmaster
                     ON salesorderdetails.stkcode = stockmaster.stockid
                 GROUP BY salesorders.orderno,
				salesorders.debtorno,
				salesorders.branchcode,
				salesorders.customerref,
				salesorders.orddate,
				salesorders.fromstkloc,
				salesorderdetails.stkcode,
				stockmaster.description,
				stockmaster.units,
				stockmaster.decimalplaces";


*/
$sql="SELECT salesorders.orderno, salesorders.debtorno, 
debtorsmaster.debtorno, debtorsmaster.name,
 salesorderdetails.orderno, salesorderdetails.orderlineno, 
 salesorderdetails.unitprice, salesorderdetails.quantity,
 SUM( salesorderdetails.unitprice * salesorderdetails.quantity * ( 1 - salesorderdetails.discountpercent ) ) AS ordervalue
FROM salesorders, salesorderdetails, debtorsmaster
WHERE salesorders.debtorno = debtorsmaster.debtorno
AND salesorders.orderno = salesorderdetails.orderno
GROUP BY salesorders.debtorno
ORDER BY ordervalue DESC LIMIT 10";

$result=mysql_query($sql,$db);
while($row=mysql_fetch_array($result))
{
	 $datay[]=$row['ordervalue'];
	 $datax[]=$row['name'];
	
}
$p=$datay[0];
$g->title( 'Top 10 Customers', '{font-size: 15px; color: #2693CF}' );
$g->set_data( $datay);
$g->bar( 50, '0x639F45', 'QUANTITY', 14 );
$g->set_x_labels($datax);
$g->set_y_max((floor($p/500)+1)*500);
$g->y_label_steps(5);
$g->set_x_label_style( 2, '#CC3399', 2 );
$g->set_y_legend( 'INVOICE VALUE',14, '0x639F45' );
echo $g->render();
?>