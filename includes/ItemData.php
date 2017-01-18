<?php
/*16-11-2011
* Rafael Rojas Agregado clase para clasificacion de item y stock
*/
class ItemData{
    private $db;
	public function __construct($db){
		$this->db=$db;
		$this->Location=array();
		$this->ReorderMax=array();
		$this->Reorder=array();
		$this->Item=array();
		$this->clasify=array();
		$this->Pendientes=array();
		$this->PendientesLoc=array();
		$this->Demanda=array();
		$this->DemandaLoc=array();
		return $this;
	}
	public static function getObj($db){
		static $Obj;
		if(!isset($Obj)||$Obj==null)	
			$Obj=new ItemData($db);
		elseif($db&&!$Obj->db)
			$Obj->db=$db;
		return $Obj;
	}
    function getClasify($StockId){
		if(!isset($this->clasify[$StockId])){
			if(function_exists("DB_query")){
    		    $sql="select clasify,clasify1 from rh_clasificacion where stockid='".$StockId."' ;";
				$stockid_res = DB_query($sql,$this->db);
			    $rw = DB_fetch_array($stockid_res);
    	    	$this->clasify[$StockId]=$rw[0];
				$this->getStock($StockId);
			}
		}
        return  $this->clasify[$StockId];
    }
	function getStockReorder($StockId,$Location=''){
		$this->getStockLocation($StockId,$Location);
		return $this->Reorder[$Location][$StockId];
	}
	function getStockReorderMax($StockId){
		$this->getStockReorder($StockId);
		return $this->ReorderMax[$StockId];
	}
	function getStockLocation($StockId,$Location=''){
		global $identifier;
		$this->getStock($StockId);
		if($Location=='')
			$Location=$_SESSION['PO'.$identifier]->Location;
		return $this->Location[$StockId][$Location];
	}
	function getStock($StockId){
		if(!isset($this->Item[$StockId])){
			if(function_exists("DB_query")){
				$sql="select loccode,quantity,reorderlevel from locstock where stockid='".$StockId."' ;";
				$stockid_res = DB_query($sql,$this->db);
				$this->Item[$StockId]=0;
				$this->ReorderMax[$StockId]=0;
		    	while($rw = DB_fetch_assoc($stockid_res)){
					$this->Location[$StockId][$rw['loccode']]=$rw['quantity'];
					$this->Reorder[$StockId][$rw['loccode']]=$rw['reorderlevel'];
					$this->ReorderMax[$StockId]+=$rw['reorderlevel'];
					$this->Item[$StockId]+=$rw['quantity'];
				}
				//$this->getClasify($StockId);
				$this->getPendiente($StockId);
				//$this->getDemanda($StockId);
			}
		}
        return  $this->Item[$StockId];
	}
	function getPendienteLocation($StockId,$Location=''){
		global $identifier;
		$this->getStock($StockId);
		if($Location=='')
			$Location=$_SESSION['PO'.$identifier]->Location;
		return $this->PendientesLoc[$StockId][$Location];
	}
	function getPendiente($StockId){
		if(!isset($this->Pendientes[$StockId])){
			if(function_exists("DB_query")){
				$sql="select ".
					"purchorders.intostocklocation as loccode, purchorderdetails.itemcode, sum(purchorderdetails.quantityord-purchorderdetails.quantityrecd) as quantity ".
					"from purchorderdetails left join purchorders on purchorderdetails.orderno=purchorders.orderno \n".
					"where purchorders.status<>'Cancelled' and  purchorderdetails.quantityrecd<purchorderdetails.quantityord and purchorderdetails.itemcode='".$StockId."' group by itemcode ";
				$stockid_res = DB_query($sql,$this->db);
				$this->Pendientes[$StockId]=0;
		    	while($rw = DB_fetch_assoc($stockid_res)){
					$this->PendientesLoc[$StockId][$rw['loccode']]=$rw['quantity'];
					$this->Pendientes[$StockId]+=$rw['quantity'];
				}
				/*$SQL="SELECT salesorders.fromstkloc as loccode, SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS quantity ".
				"FROM salesorderdetails ".
				"left join salesorders on salesorders.orderno = salesorderdetails.orderno ".
				"left join bom on salesorderdetails.stkcode=bom.parent ".
				"left join stockmaster on stockmaster.stockid=bom.parent ".
				"WHERE purchorders.status<>'Cancelled' and ".
				"salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0 AND ".
				"bom.component='" . $StockID . "' AND  ".
				"(stockmaster.mbflag='A' OR stockmaster.mbflag='E') ".
				"AND salesorders.quotation=0 ".
				"group by bom.parent";
				$stockid_res = DB_query($sql,$this->db);
				$this->Pendientes[$StockId]=0;
		    	while($rw = DB_fetch_assoc($stockid_res)){
					if(!isset($this->PendientesLoc[$StockId][$rw['loccode']]))
						$this->PendientesLoc[$StockId][$rw['loccode']]=0;
					$this->PendientesLoc[$StockId][$rw['loccode']]+=$rw['quantity'];
					$this->Pendientes[$StockId]+=$rw['quantity'];
				}/**/
				$this->getStock($StockId);
			}
		}
		return $this->Pendientes[$StockId];
	}
	function getDemandaLocation($StockId,$Location=''){
		global $identifier;
		$this->getStock($StockId);
		if($Location=='')
			$Location=$_SESSION['PO'.$identifier]->Location;
		return $this->DemandaLoc[$StockId][$Location];
	}
	function getDemanda($StockId){
		if(!isset($this->Demanda[$StockId])){
			if(function_exists("DB_query")){
				$sql = "SELECT salesorders.fromstkloc as loccode, SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS quantity
            			     FROM salesorderdetails,
                      			salesorders
			                 WHERE salesorders.orderno = salesorderdetails.orderno AND
 			                salesorderdetails.completed=0 AND
		 					salesorders.quotation=0 AND
                 			salesorderdetails.stkcode='" . $StockId . "' group by salesorders.fromstkloc ";
				$stockid_res = DB_query($sql,$this->db);
				$this->Demanda[$StockId]=0;
		    	while($rw = DB_fetch_assoc($stockid_res)){
					$this->DemandaLoc[$StockId][$rw['loccode']]=$rw['quantity'];
					$this->Demanda[$StockId]+=$rw['quantity'];
				}
				$this->getStock($StockId);
			}
		}
		return $this->Demanda[$StockId];
	}
}
