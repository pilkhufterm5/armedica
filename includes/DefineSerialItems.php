<?php
/* $Revision: 1.7 $ */

function ValidBundleRef ($StockID, $LocCode, $BundleRef){
    global $db;

    $SQL = "SELECT quantity 
                FROM stockserialitems 
                WHERE stockid='" . $StockID . "' 
                AND loccode ='" . $LocCode . "' 
                AND serialno='" . $BundleRef . "'";
    $Result = DB_query($SQL, $db);
    if (DB_num_rows($Result)==0){
        return 0;
    } else {
        $myrow = DB_fetch_row($Result);
        return $myrow[0]; /*The quantity in the bundle */
    }
}

class SerialItem {

    var $BundleRef;
    var $BundleQty;
    var $BundleExpD;

    //Constructor
    function SerialItem($BundleRef, $BundleQty, $BundleExpD=0){
/*********************************************************************************************************************/
        $BundleRef=SerialItem::LimpiarLote($BundleRef);
/*********************************************************************************************************************/
        $this->BundleRef = $BundleRef;
        $this->BundleQty = $BundleQty;
                $this->BundleExpD = $BundleExpD;
    }
    static function LimpiarLote($BundleRef){
    	$BundleRef = trim($BundleRef);
        $BundleRef = stripslashes(stripslashes($BundleRef));
        $BundleRef = str_replace(' ',' ',$BundleRef);
        $BundleRef = str_replace('  ',' ',$BundleRef);
        $BundleRef = str_replace('   ',' ',$BundleRef);
        $BundleRef = str_replace('\'','',$BundleRef);
        $BundleRef = str_replace('+','',$BundleRef);
        $BundleRef = str_replace('\\','',$BundleRef);
        $BundleRef = str_replace('"','',$BundleRef);
        $BundleRef = str_replace('&','',$BundleRef);
        $BundleRef = str_replace('.','',$BundleRef);
        return $BundleRef;
    }
}//class SerialItem

