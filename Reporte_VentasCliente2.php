<?php
set_time_limit(0);

$PageSecurity = 2;
include('includes/session.inc');
require_once("Excel/excel.php");
require_once("Excel/excel-ext.php");




If (isset($_POST['PrintPDF'])){
    $text="";
    if(strlen($_POST['fecha_ini'])<10){
        $fecha_ini='date(now())';
        $text.=Date($_SESSION['DefaultDateFormat']).' hasta ';
    }else {
        $fecha_ini= "'".$_POST['fecha_ini']."'";
        $text.=' '.$_POST['fecha_ini'].' hasta';
    }

    if(strlen($_POST['fecha_fin'])<10){
        $fecha_fin='date(now())';
        $text.=' '.Date($_SESSION['DefaultDateFormat']);
    }else {
        $fecha_fin= "'".$_POST['fecha_fin']."'";
        $text.=' '.$_POST['fecha_fin'].' ';
    }

    $arrayCondicional = array();
    if(count($_POST['marca'])>0){
        foreach ($_POST['marca'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['marca'][$key]=$value;
            }else{
                $_POST['marca'][$key]="'".$value."'";
            }
        }
    }

    if(count($_POST['sucursal'])>0){
        foreach ($_POST['sucursal'] as $key=>$value){
               $_POST['sucursal'][$key]="'".$value."'";
        }
    }

    if(isset($_POST['marca'])&&count($_POST['marca'])>0 ){
        array_push($arrayCondicional,'rh_marca.id in('.implode(',',$_POST['marca']).')');
    }

    if(isset($_POST['sucursal'])&&count($_POST['sucursal'])>0 ){
        array_push($arrayCondicional,'debtorsmaster.debtorno in('.implode(',',$_POST['sucursal']).')');
    }


    $finalCondicion="";
    if(count($arrayCondicional)>0){
      $finalCondicion=implode(' and ',$arrayCondicional).' and ';
    }

    $SQL="select concat(rh_cfd__cfd.serie,rh_cfd__cfd.folio)as Factura,debtortrans.trandate as Fecha, debtortrans.debtorno as `NoCliente` ,debtorsmaster.name as Cliente,rh_marca.nombre as Marca, stockmoves.transno as Transaccion, stockmoves.stockid as Codigo,stockmaster.description as Articulo,stockmaster.units as Unidad,(stockmoves.qty*-1) as Cantidad, CONVERT(stockmoves.price, DECIMAL(8,2)) as PrecioUnitario, CONVERT(((1-stockmoves.discountpercent)*stockmoves.price*stockmoves.qty*-1),DECIMAL(8,2)) as Importe,custbranch.salesman as Vendedor
	        from stockmoves
		        join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmoves.type=10
		        join rh_marca on stockmaster.rh_marca = rh_marca.id
		        join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type=debtortrans.type
                left join rh_cfd__cfd on debtortrans.id = rh_cfd__cfd.id_debtortrans
		        join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
                join custbranch on  debtortrans.debtorno = custbranch.debtorno and  debtortrans.branchcode =  custbranch.branchcode
	        where  ".$finalCondicion." (date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin.") and stockmoves.type=10 and debtortrans.type=10 AND debtortrans.rh_status != 'C' order by debtorsmaster.debtorno, rh_marca,stockmoves.transno;";
    // echo $SQL;
	$Result = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('The inventory valuation could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}

while($datatmp = DB_fetch_assoc($Result)) {
    $data[] = $datatmp;
}
createExcel("Reporte_ventasClientes2_".date('Y-m-d').".xls", $data);
}
?>