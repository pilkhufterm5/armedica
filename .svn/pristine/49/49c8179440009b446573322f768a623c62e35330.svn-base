<?php

function mask_cuenta_contable($cuenta){
	
	// Feb 2007 bowikaxu - Patron de Separacion de Cuentas Contables
	$cuentas_contables='333';
	
	$len1 = substr($cuentas_contables,0,1);
	$len2 = substr($cuentas_contables,1,1);
	//$len3 = substr($cuentas_contables,2,1);
	//$len4 = substr($cuentas_contables,3,1);
	
	$aux = substr($cuenta,0,$len1);
	$aux .= "-";
	$aux .= substr($cuenta,$len1,$len2);
	//$aux .= "-";
	//$aux .= substr($cuenta,$len1+$len2,$len3);
	//$aux .= "-";
	//$aux .= substr($cuenta,$len1+$len2+$len3,$len4);
/******************************************************************************************************************************
* Jorge Garcia
* 12/Dic/2008
******************************************************************************************************************************/
	$rh_tchar = strlen($cuenta);
	$n = 1;
	$n1 = 0;
	$n2 = 1;
	$aux = '';
	while($n <= $rh_tchar){		
		if($n2 == 4){
			$n2 = 2;
			$aux = $aux."-".substr($cuenta,$n1,1);
		}else{
			$n2++;
			$aux = $aux.substr($cuenta,$n1,1);
		}
		$n++;
		$n1++;
	}
/******************************************************************************************************************************
* Fin Jorge Garcia
******************************************************************************************************************************/
	return $aux;
}

// Feb 2007 bowikaxu - Verificar si la cuenta termina en ceros
function cuenta_contable_termina0($cuenta){
	
	// Feb 2007 bowikaxu - Patron de Separacion de Cuentas Contables
	$cuentas_contables='1233';
	
	$len1 = substr($cuentas_contables,0,1);
	$len2 = substr($cuentas_contables,1,1);
	$len3 = substr($cuentas_contables,2,1);
	$len4 = substr($cuentas_contables,3,1);
	
	$aux = substr($cuenta,$len1+$len2+$len3,$len4);
	if($aux == 0){
		return true;
	}else {
		return false;
	}
}

?>
