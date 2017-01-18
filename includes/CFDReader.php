<?php
//*********************************************************************************************************************************
//*********************************************************************************************************************************
//**************	Definicion de la clase: Lectura de CFD basico intermedio para la creacion de CFD en tiempo		***************
//**************	de ejecucion.																					***************
//**************	Autor:	Angel Cruijff - el ricky no hizo nada ¬¬																		***************
//**************	Open Source																						***************
//**************	No Gratuita																						***************														***************
//*********************************************************************************************************************************
//*********************************************************************************************************************************		
class CFDReader{
	private $CFD;
	
	function __construct($direccion,$comprobante,$conceptos,$general){
 		$this->CFD['comprobante']	= $comprobante;
 		$this->CFD['emisor_DF']		= $direccion['emisor_DF'];
 		$this->CFD['emisor']		= $general['emisor'];
 		$this->CFD['emisor_EE']		= $direccion['emisor_EE'];
 		$this->CFD['receptor_DF']	= $direccion['receptor_DF'];
 		$this->CFD['receptor']		= $general['receptor'];
 		$this->CFD['concepto']		= $conceptos;
 		$this->CFD['conceptos']['cantidad'] = count($conceptos['unidad']);
 		$this->CFD['general']		= $general;
 	}
 	
 	public function getCFDInfo(){
		return $this->CFD;
	}
	
    function __destruct(){
       
    } 
	
}
//---------------------------------------------FIN DE LECTURA DE DATOS CFD-------------------------------------------------------
?>
