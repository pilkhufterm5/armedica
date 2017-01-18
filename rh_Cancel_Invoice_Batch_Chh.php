<?php

$PageSecurity = 8;
$AllowAnyone=true;

/*SELECCIONAR BASE DE DATOS*/
$DatabaseName='chh_erp_001';


include ('includes/session.inc');
include_once('CFDI32.php');
ini_set('memory_limit','256M');
set_time_limit(0);
$title = _('Cancelacion masiva');
include('includes/header.inc');
function getAttr($xml,$attr,$start=0){
		$p1=strpos($xml,'"',strpos($xml,$attr,$start))+1;
		$p2=strpos($xml,'"',$p1);
		$cer=substr($xml,$p1,$p2-$p1);
		return $cer;
}
//      $sql = "SELECT gstno FROM companies LIMIT 1";
//        $rs = DB_query($sql,$db);
//        if($rw = DB_fetch_array($rs)){
//          $RFC=$rw['gstno'];
//        }


  //       $sqlQuery = "SELECT rh_cfd__cfd.uuid,pfx,pfxpass
  //       		FROM
  //       		rh_cfdi rh_cfd__cfd
  //       		JOIN rh_cfd__locations__systypes__ws_csd ON rh_cfd__locations__systypes__ws_csd.serie = rh_cfd__cfd.serie AND rh_cfd__locations__systypes__ws_csd.`id_systypes`=rh_cfd__cfd.`id_systypes`
  //               JOIN rh_csd ON rh_csd.noserie  = rh_cfd__locations__systypes__ws_csd.id_ws_csd
  //                    WHERE fk_transno = " . $_SESSION['ProcessingCredit'] . " AND rh_cfd__cfd.id_systypes = 10 LIMIT 1";
		// $_SESSION['ProcessingCredit']=5382;
		// $sql="select * from tmp_cancelarFacturas";
  //       $result = DB_query($sql,$db,'','',false,false);

        $UUID='';

        /*RFC DE MGA*/
        //$RFC = "MGA141013KU2";

        /*RFC DE CHIHUAHUA ALDUAR*/
        $RFC = "ALD030623249";

        //PFX MGA
        //$pfx = base64_encode(file_get_contents ('XMLFacturacionElectronica/csdandkey/00001000000300922898.pfx'));

        //PFX ALDURA
        $pfx = base64_encode(file_get_contents ('XMLFacturacionElectronica/csdandkey/00001000000300922898.pfx'));
        $pfxPassword = "ALD030623249";

        // echo "<pre>";
        // print_r($pfx);
        // echo "</pre><br><br>";



$CANCELAR=array("618FC7E1-3AC9-4B16-B99A-264CAA83A841");
        // echo "<pre>";
        // print_r($CANCELAR);
        // echo "</pre>";
//        exit;


        echo '<table class="table table-striped table-bordered table-hover" > <tbody>';
        foreach ($CANCELAR as $idx => $UUID){

            try{
                //public static function cancelCFDI($rfc, $uuid, $pfx, $pfxPassword)
                $_uuid = CFDI::cancelCFDI($RFC,$UUID,$pfx,$pfxPassword);
                if($_uuid == $UUID){
                	echo '<tr>';
                	echo '<td>CANCELADO CORRECTAMENTE </td>';
                    echo "<td>{$_uuid}    |     {$UUID}</td>";
                    echo '</tr>';
                }
            }catch(Exception $e){
                echo '<tr>';
                echo "<td>Error:{$UUID} </td>";
                echo "<td>" . $e->getMessage()  . "</td>";
                echo '</tr>';
                //var_dump($e);
            }
        }
        echo '</tbody></table>';
