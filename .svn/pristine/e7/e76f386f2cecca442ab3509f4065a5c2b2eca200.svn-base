<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
$PageSecurity = 2;
include ('includes/session.inc');


$sql = "SELECT stockid, (materialcost*1.3) as costo from stockmaster";
$res = DB_query($sql,$db);

while($item = DB_fetch_array($res)){
	
	$sql = "insert into prices (stockid,typeabbrev,currabrev,price) VALUES ('".$item['stockid']."', 'P1', 'MN',".$item['costo'].")";
	DB_query($sql,$db);
	echo "STOCKID: ".$item['stockid']." PRECIO: ".$item['costo']."<BR>";
	
}

?>