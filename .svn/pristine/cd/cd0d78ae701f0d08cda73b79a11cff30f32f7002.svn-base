<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
*	
*	ANDRES AMAYA DIAZ bowikaxu@gmail.com
*	WEBSERVICE webERP & synPOS
*	BOWIKAXU - SCRIPT TO VERIFY TODAYS DISCOUNTS, AND APPLY RATES TO PRODUCTS
*	THIS SCRIPT IS PART OF THE webERP - synPOS webservices
*
*/


$DBHost = "localhost";
$DBName = "reynagarza_erp_001";
$DBUser = "root";
$DBPass = "chilaquiles";

$DBConn = mysql_connect($DBHost,$DBUser,$DBPass) or die('Imposible Conectarse con MySQL.' );
echo "Se conecto a la B.D.<br>";
mysql_select_db($DBName,$DBConn) or die('Imposible Seleccionar la B.D.' );
echo "Se selecciono la Tabla<br>";
mysql_query("BEGIN",$DBConn);
$sql = "SELECT rh_discounts.discountid, 
			rh_discounts.itmcategoryid,
			rh_discounts.itemlike,
			discountmatrix.discountcategory FROM rh_discounts 
		INNER JOIN discountmatrix ON discountmatrix.discountcategory = rh_discounts.discountid 
		WHERE rh_discounts.fromdate <= '".date('Y-m-d')."'
		AND rh_discounts.todate >= '".date('Y-m-d')."' ORDER BY rh_discounts.discountid";
echo $sql."<BR>";

mysql_query("UPDATE stockmaster SET discountcategory = ''",$DBConn);

$res = mysql_query($sql,$DBConn);
while ($DSC = mysql_fetch_array($res)){

	if(strlen($DSC['itemlike'])>=1){ // insert by items like
		
		$sqlIns = "UPDATE stockmaster SET discountcategory = '".$DSC['discountcategory']."' 
							WHERE stockid LIKE '".$DSC['itemlike']."%'";
		
	}else { // insert by items category
		
		$sqlIns = "UPDATE stockmaster SET discountcategory = '".$DSC['discountcategory']."' 
							WHERE categoryid = '".$DSC['itmcategoryid']."'";
		
	}
	echo $sqlIns."<BR>";
	mysql_query($sqlIns,$DBConn);
	
}

// DELETE THE OLD DISCOUNTS CATEGORIES
$sql = "DELETE FROM discountmatrix WHERE discountcategory IN (SELECT discountid FROM rh_discounts WHERE todate > fromdate AND todate < '".date('Y-m-d')."')";
mysql_query($sql,$DBConn);
$sql = "DELETE FROM rh_discounts WHERE todate > fromdate AND todate < '".date('Y-m-d')."'";
mysql_query($sql,$DBConn);

mysql_query("COMMIT",$DBConn);

mysql_close($DBConn);
echo "Se termino la actualizacion de descuentos<br>";
// END UPDATING DISCOUNT RATES
?>