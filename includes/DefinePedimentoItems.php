<?php
/* $Revision: 1.7 $ */
function ValidBundleRefPedimento ($StockID, $LocCode, $BundleRef){
	global $db;

	$SQL = "SELECT quantity
				FROM stockpedimentoitems
				WHERE stockid='" . $StockID . "'
				AND loccode ='" . $LocCode . "'
				AND pedimentoid='" . $BundleRef . "'";
                //echo $SQL;
	$Result = DB_query($SQL, $db);
	if (DB_num_rows($Result)==0){
		return 0;
	} else {
		$myrow = DB_fetch_row($Result);
		return $myrow[0]; /*The quantity in the bundle */
	}
}

class PedimentoItem {

	var $BundleRef;
    var $BundleRef2;
    var $BundleRef3;
    var $BundleRef4;
	var $BundleQty;
    var $lineOrder;

	//Constructor
	function PedimentoItem($BundleRef,$BundleRef2,$BundleRef3, $BundleQty,$orderLine=0){
        global $db;
	    $SQL = "SELECT *
				FROM rh_pedimento
				WHERE pedimentoid='" . $BundleRef . "'";
	    $Result = DB_query($SQL, $db);
	    if ($myrow = DB_fetch_array($Result)){
		    $this->BundleRef2 = $myrow['nopedimento'];
            $this->BundleRef3 = $myrow['fecha'];
            $this->BundleRef4 =$myrow['aduana'];
	    }
        $this->BundleRef= $BundleRef;
		$this->BundleQty = $BundleQty;
        $this->lineOrder =$orderLine;
	}
}//class SerialItem
?>
