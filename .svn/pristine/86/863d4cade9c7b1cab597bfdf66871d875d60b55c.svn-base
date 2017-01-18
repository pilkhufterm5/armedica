<?php

/**
   * RUGenerator
   * 
   * Clase de generacion de referencia unica para el servicio de Paynet
   *
   * @version 1.0.0
   * @author     Sergio Serrano <sserrano@diestel.com.mx>
   */
class RUGenerator {

	/**
	* 
	* Regresa la referencia unica con los datos especificados
	*
	* @param string $issuer  Codigo issuer de la referencia
	* @param string $referencia  Referencia de pago del emisor
	* @return string
	*/
	public function createRU($issuer, $referencia) {
		if (!$this->validParams($issuer, $referencia) == true) {	
			return "";
		}
		
		$resultRef = "";
		$resultRef .= trim($issuer);
		$resultRef .= str_pad($referencia,6,"0", STR_PAD_LEFT);

		return $resultRef . $this->getDB10Digit($resultRef);
	}

	/**
	* 
	* Valida los parametros
	*
	* @param string $issuer  Codigo issuer de la referencia
	* @param string $referencia  Referencia de pago del emisor
	* @return string
	*/
	private function validParams($issuer, $referencia) {
		if ($issuer == null || strlen(trim($issuer)) == 0) {
			return false;
		}
		if (strlen(trim($issuer)) != 6) {
			return false;
		}
		if ($referencia == null || strlen(trim($referencia)) == 0) {
			return false;
		}
		
		return true;
	}

	/**
	* 
	* Regresa el digito verificador Base 10 de la referencia
	*
	* @param string $ref  Referencia unica generada
	* @return integer
	*/
	private function getDB10Digit($ref) {
		$multiplicador = 2;
		$caracteres = array_reverse(str_split($ref));

		$suma = 0;
		foreach ($caracteres as &$currChar) {
			$valor= 0;

			if ($currChar == "A" || $currChar == "J" || $currChar == "1") {
				$valor = 1;
			} else if ($currChar == "B" || $currChar == "K" || $currChar == "S"
					|| $currChar == "2") {
				$valor = 2;
			} else if ($currChar == "C" || $currChar == "L" || $currChar == "T"
					|| $currChar == "3") {
				$valor = 3;
			} else if ($currChar == "D" || $currChar == "M" || $currChar == "U"
					|| $currChar == "4") {
				$valor = 4;
			} else if ($currChar == "E" || $currChar == "N" || $currChar == "V"
					|| $currChar == "5") {
				$valor = 5;
			} else if ($currChar == "F" || $currChar == "O" || $currChar == "W"
					|| $currChar == "6") {
				$valor = 6;
			} else if ($currChar == "G" || $currChar == "P" || $currChar == "X"
					|| $currChar == "7") {
				$valor = 7;
			} else if ($currChar == "H" || $currChar == "Q" || $currChar == "Y"
					|| $currChar == "8") {
				$valor = 8;
			} else if ($currChar == "I" || $currChar == "R" || $currChar == "Z"
					|| $currChar == "9") {
				$valor = 9;
			} else if ($currChar == "0") {
				$valor = 0;
			}

			$sumando = $multiplicador * $valor;
			if ($sumando > 9) {
				$sumando = (int)(($sumando / 10) + ($sumando - 10));
			}

			$suma += $sumando;
			
			if ($multiplicador == 2) {
				$multiplicador = 1;
			} else {
				$multiplicador = 2;
			}

		}

		$result = ((int)($suma / 10) + 1) * 10 - $suma;
				
		if ($result == 10) {
			$result = 0;
		}

		return $result;
	}
}
		
?>
