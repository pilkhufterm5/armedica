<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-01 09:33:18 -0600 (Tue, 01 Apr 2008) $
 * $Rev: 138 $
 */

$PageSecurity = 11;

include('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');

$title = _('Receive Purchase Orders');
include('includes/header.inc');

$sql = "SELECT orderno, supplierno, orddate FROM purchorders WHERE YEAR(orddate)=2007 AND MONTH(orddate)=9";
$res = DB_query($sql,$db,'error al obtener las ordenes','sql usado:'.$sql);
echo "<BR><H1>ACTUALIZANDO COSTOS DE ARTICULOS</H1><BR>";
while($order = DB_fetch_array($res)){
	
	$sql = "SELECT itemcode, unitprice FROM purchorderdetails WHERE quantityord = quantityrecd AND orderno = '".$order['orderno']."' AND unitprice > 0";
	$res2  = DB_query($sql,$db,'ERROR: imposible obtener detalles del pedido de compra','');
	
	while($orderdetail = DB_fetch_array($res2)){
		
		$sql = "UPDATE stockmaster SET lastcost = actualcost WHERE stockid = '".$orderdetail['itemcode']."'";
		DB_query($sql,$db);
		
		$sql = "UPDATE stockmaster SET actualcost  = '".$orderdetail['unitprice']."', materialcost = '".$orderdetail['unitprice']."' 
				WHERE stockid = '".$orderdetail['itemcode']."'";
		DB_query($sql,$db);
		
		if($_SESSION['CostHistory']==1){
						// bowikaxu realhost - get old cost
							$sqlold = "SELECT (materialcost+labourcost+overheadcost) AS cost
								FROM stockmaster
								WHERE stockmaster.stockid='".$orderdetail['itemcode']."'";
							$resold = DB_query($sqlold,$db);
							$OldCost = DB_fetch_array($resold);
						// bowikaxu realhost - historial del costo
							$sqlcost = "INSERT INTO rh_costhistory (stockid,
									cost, lastcost, trandate, user_) VALUES (
									'".$orderdetail['itemcode']."',
									'".$orderdetail['unitprice']."',
									'".$OldCost['cost']."',
									'".Date('Y-m-d H:m:s')."',
									'".$_SESSION['UserID']."')";
						// bowikaxu realhost - insert the price history
								DB_query($sqlcost,$db,'error al insertar el historial e costos','',true);
					}
		
		echo $orderdetail['itemcode']."<br>";
		
	}
	
}
echo "<BR><H1>COSTO DE LOS ARTICULOS ACTUALIZADOS CON EXITO</H1>";
include('includes/footer.inc');

?>