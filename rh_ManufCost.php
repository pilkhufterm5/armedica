<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-01 09:33:18 -0600 (Tue, 01 Apr 2008) $
 * $Rev: 138 $
 */
// Actualizar los costos de los productos manufacturados a partir de la suma de costos de 
// sus articulos en su cuenta de materiales

$PageSecurity=1;
include('includes/session.inc');
$title = _('Actualizar Costos de Articulos Manufacturados');
include('includes/header.inc');

	// bowikaxu - abril 2007
	// modify the parent material cost
	
	$sql = "SELECT stockid FROM stockmaster WHERE mbflag='M'";
	$res = DB_query($sql,$db,'Imposible obtener articulos manufacturados','el sql fue: ',true);
	
	while ($Parent = DB_fetch_array($res)){
		
		DB_query("BEGIN",$db);
		$TotalCost = 0;
		$sql = "SELECT bom.component, bom.quantity, (stockmaster.actualcost + stockmaster.materialcost + stockmaster.overheadcost + stockmaster.labourcost) AS costo
				FROM bom, stockmaster 
				WHERE bom.parent='".$Parent['stockid']."' 
				AND stockmaster.stockid=bom.component";
		
		$res2 = DB_query($sql,$db,'Imposible obtener su costo','',true);
		
		while ($TmpCost = DB_fetch_array($res2)){
	
			$TotalCost += ($TmpCost['costo']*$TmpCost['quantity']);
	
		}

		$sql = "UPDATE stockmaster SET materialcost = ".$TotalCost." WHERE stockid = '".$Parent['stockid']."'";
		DB_query($sql,$db,'Imposible Actualizar su costo','',true);
		
		if($_SESSION['CostHistory']==1){
						// bowikaxu realhost - get old cost
							$sqlold = "SELECT (materialcost+labourcost+overheadcost) AS cost
								FROM stockmaster
								WHERE stockmaster.stockid='".$Parent['stockid']."'";
							$resold = DB_query($sqlold,$db);
							$OldCost = DB_fetch_array($resold);
						// bowikaxu realhost - historial del costo
							$sqlcost = "INSERT INTO rh_costhistory (stockid,
									cost, lastcost, trandate, user_) VALUES (
									'".$Parent['stockid']."',
									'".$TotalCost."',
									'".$OldCost['cost']."',
									'".Date('Y-m-d H:m:s')."',
									'".$_SESSION['UserID']."')";
						// bowikaxu realhost - insert the price history
								DB_query($sqlcost,$db,'error al insertar el historial e costos','',true);
					}
		
		DB_query("COMMIT",$db);
		echo "Costo de: <STRONG>".$Parent['stockid']."</STRONG> Actualizado a: ".$TotalCost."<BR>";

   	}

?>