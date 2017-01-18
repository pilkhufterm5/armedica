<?

// bowikaxu - clase para el manejo de articulos en las ordenes de trabajo

include('includes/SQL_CommonFunctions.inc');

class Carrito {
	
	var $elementos;
	var $cantidad;
	var $TotElem;
	var $existia;
	var $NecElementos;
	var $NecCantidad;

	function Carrito(){
		
		$this->elementos = array();
		$this->cantidad = array();
		$this->NecElementos = array();
		$this->NecCantidad = array(); 
		$this->TotElem = 0;
		$this->existia=0;
		
	}
	
	
	function addItem($ItemCode,$Cant){
			
		for($i=0;$i<$this->TotElem;$i++){
			
			if($this->elementos[$i]==$ItemCode){
				
				$this->cantidad[$i] += $Cant;
				$this->existia=1;		
			
			}
		
		}
		
		if($this->existia==0){
		
			$this->elementos[]=$ItemCode;
			$this->cantidad[]=$Cant;
			$this->TotElem = $this->TotElem+1;
		
		}
		$this->existia=0;
	}
	
	function getItemQty($Index){
		
		if($this->cantidad[$Index]!=0)
		return $this->cantidad[$Index];
		else return false;
		
	}
	
	function removeItem($ItemCode){
		
		for($i=0;$i<$this->TotElem;$i++){
			
			if($this->elementos[$i]==$ItemCode){
				
				unset($this->elementos[$i]);
				unset($this->cantidad[$i]);
				
			}
			
		}
	}
	
	function removeNecItem($ItemCode,$Num){
		
		for($i=0;$i<$this->TotElem;$i++){
			
			
			if($this->elementos[$i]==$ItemCode){
					
						$this->cantidad[$i]=$this->cantidad[$i]-$Num;
						
			}
			
			if($this->cantidad[$i]==0){
				
				unset($this->elementos[$i]);
				unset($this->cantidad[$i]);
				
			}
				
		}
	}
		
		
	function getTotElem(){
		
		
		return $this->TotElem;
		
	}
}

?>