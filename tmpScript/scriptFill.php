<?php

	$PathPrefix = "../";
	$PageSecurity = 1;
	
	include('../includes/session.inc');
	//include('../includes/SQL_CommonFunctions.inc');

	$file = file("fileMang.csv");
	//echo "<pre>";	
	//print_r($file);
	//echo "</pre>";

	foreach ($file as $num => $row)
	{
		//DB_query("BEGIN",$db);
		$column = explode(",",$row);
		$column[0] = str_replace('"','',$column[0]); 
		$column[1] = str_replace('"','',$column[1]);
		
		$cont = 0;
		foreach ($file as $number => $reg)
		{			
			$col = explode(",",$reg);
			$col[0] = str_replace('"','',$col[0]);
			$col[1] = str_replace('"','',$col[1]);
			
			if (($column[0]==$col[0]) && ($column[1]==$col[1])) 
			{				
				unset($file[$number]);
			}

		}	
		
		$sqlS = "SELECT debtorno from rh_reglaprecios WHERE debtorno=".$column[0]." AND id_marca=".$column[1];
		$sel = DB_query($sqlS,$db);
		$regSel = DB_fetch_array($sel);

		if (count($regSel['debtorno'])==0)
		{
			$sql = "INSERT INTO rh_reglaprecios (debtorno,id_marca,categoryid,stockid,descuento) VALUES ('".$column[0]."','".$column[1]."','ALL','ALL','".$column[2]."')";	
	
			DB_query($sql,$db);
	
			echo "<hr>#".$num."--".$sql."<hr>";
		}		
		
		
	}	
	//DB_query("COMMIT",$db);
	echo "<hr>NOTA: Si no se muestra ningun query es porque ya se han realizado todos los registros del archivo<hr>";
?>

