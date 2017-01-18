<?php

/*****************************************************************************************************************************
* Jorge Garcia
* 16/Dic/2008 Archivo para aplicar descuento
*****************************************************************************************************************************/
function GetDiscount ($StockID, $DebtorNo, $db){

	$Discount = 0;
	
	$sqlarti = "SELECT categoryid, rh_marca FROM stockmaster WHERE stockid = '".$StockID."'";
	$resultarti = DB_query($sqlarti, $db);
	$rowarti = DB_fetch_row($resultarti);
	$marca = $rowarti[1];
	$categoria = $rowarti[0];
	$rh_array = array();
	$q = 0;
	$sql = "SELECT id_marca, categoryid, stockid, descuento FROM rh_reglaprecios WHERE debtorno = '".$DebtorNo."'";
	$result = DB_query($sql, $db);
	if (DB_num_rows($result) > 0){
		while($myrow = DB_fetch_array($result)){
			$rh_array[$q] = $myrow['id_marca']."||".$myrow['categoryid']."||".$myrow['stockid']."||".$myrow['descuento'];
			$q++;
		}
	}
	
	$rh_array2 = array();
	$q2 = 0;
	$sql2 = "SELECT descuento, id_marca, categoryid, stockid FROM rh_reglaprecios WHERE debtorno = 'ALL'";
	$result2 = DB_query($sql2, $db);
	if (DB_num_rows($result2) > 0){
		while($myrow2 = DB_fetch_array($result2)){
			$rh_array2[$q2] = $myrow2['id_marca']."||".$myrow2['categoryid']."||".$myrow2['stockid']."||".$myrow2['descuento'];
			$q2++;
		}
	}
	
	$descu = 0;
	foreach($rh_array2 as $arreglo2){
		$rh_separa = explode("||", $arreglo2);
		if($rh_separa[0] == $marca AND $rh_separa[1] == $categoria AND $rh_separa[2] == $StockID){
			if($descu < ($rh_separa[3])/100){
				$descu = ($rh_separa[3])/100;
			}
		}else{
			if($rh_separa[0] == $marca AND $rh_separa[1] == $categoria AND $rh_separa[2] == 'ALL'){
				if($descu < ($rh_separa[3])/100){
					$descu = ($rh_separa[3])/100;
				}
			}else{
				if($rh_separa[0] == 'ALL' AND $rh_separa[1] == $categoria AND $rh_separa[2] == 'ALL'){
					if($descu < ($rh_separa[3])/100){
						$descu = ($rh_separa[3])/100;
					}
				}else{
					if($rh_separa[0] == $marca AND $rh_separa[1] == 'ALL' AND $rh_separa[2] == 'ALL'){
						if($descu < ($rh_separa[3])/100){
							$descu = ($rh_separa[3])/100;
						}
					}
				}
			}
		}
	}
	foreach($rh_array as $arreglo){
		$rh_separa = explode("||", $arreglo);
		if($rh_separa[0] == $marca AND $rh_separa[1] == $categoria AND $rh_separa[2] == $StockID){
			if($descu < ($rh_separa[3])/100){
				$descu = ($rh_separa[3])/100;
			}
		}else{
			if($rh_separa[0] == $marca AND $rh_separa[1] == $categoria AND $rh_separa[2] == 'ALL'){
				if($descu < ($rh_separa[3])/100){
					$descu = ($rh_separa[3])/100;
				}
			}else{
				if($rh_separa[0] == 'ALL' AND $rh_separa[1] == $categoria AND $rh_separa[2] == 'ALL'){
					if($descu < ($rh_separa[3])/100){
						$descu = ($rh_separa[3])/100;
					}
				}else{
					if($rh_separa[0] == $marca AND $rh_separa[1] == 'ALL' AND $rh_separa[2] == 'ALL'){
						if($descu < ($rh_separa[3])/100){
							$descu = ($rh_separa[3])/100;
						}
					}else{
						if($rh_separa[0] == 'ALL' AND $rh_separa[1] == 'ALL' AND $rh_separa[2] == 'ALL'){
							if($descu < ($rh_separa[3])/100){
								$descu = ($rh_separa[3])/100;
							}
						}
					}
				}
			}
		}
	}
	Return $descu;
}

/*****************************************************************************************************************************
* Fin Jorge Garcia
*****************************************************************************************************************************/
?>