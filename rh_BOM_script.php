<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
$PageSecurity = 8; /*viewing possible with inquiries but not mods */

include('includes/session.inc');
$title = _('BOM cost updates');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

// bowikaxu - abril 2007
// modify the parent material cost
	
	$sql = "SELECT bom.parent FROM bom GROUP BY parent";
	$res = DB_query($sql,$db,'','',true);
	DB_query("BEGIN",$db);
	echo "<TABLE>";
	echo "<TR><TD>codigo</TD><TD>costo correcto</TD><TD>costoactual</TD><TD>materialcost</TD><TD>overheadcost</TD><TD>labourcost</TD></TR>";
	while ($Parent = DB_fetch_array($res)){
		
		$TotalCost = 0;
		$sql = "SELECT bom.component, bom.quantity, (stockmaster.materialcost) AS costo
				FROM bom, stockmaster 
				WHERE bom.parent='".$Parent['parent']."' 
				AND stockmaster.stockid=bom.component";
		$res2 = DB_query($sql,$db,'Imposible obtener su costo','',true);
		while ($Cost = DB_fetch_array($res2)){
	
			$TotalCost += ($Cost['costo']*$Cost['quantity']);
		
		}
		$sql = "SELECT actualcost, materialcost, overheadcost, labourcost, mbflag from stockmaster where stockid = '".$Parent['parent']."'";
		$res3 = DB_query($sql,$db);
		$actual = DB_fetch_array($res3);
		echo "<TR><TD>".$Parent['parent']."</TD><TD>".$TotalCost."</TD><TD>".$actual['actualcost']."</TD><TD>".$actual['materialcost']."</TD><TD>".$actual['overheadcost']."</TD><TD>".$actual['labourcost']."</TD></TR>";
		$sql = "UPDATE stockmaster SET materialcost = ".$TotalCost.", actualcost = ".$TotalCost." WHERE stockid = '".$Parent['parent']."'";
		DB_query($sql,$db,'Imposible Actualizar su costo','',true);
		//echo $sql."<HR>";
	}
	echo "</TABLE>";
	DB_query("COMMIT",$db);	
?>