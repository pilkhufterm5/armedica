<?php
/* $Revision: 385 $ */

include('includes/DefineCartClass.php');
$PageSecurity = 1;
/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc*/

include('includes/session.inc');

?>
<script type="text/javascript">
function realizarNCargo(orden)
{
    window.location = "rh_ConfirmDispatch_Invoice_NC.php?&OrderNumber="+orden;
}
</script>

<?php

if (isset($_GET['ModifyOrderNumber'])) {
	$title = _('Modifying Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
	$title = _('Select Order Items');
}

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/rh_GetDiscount.inc');
include('includes/SQL_CommonFunctions.inc');

//echo "<hr>";
//print_r($_POST);
//echo "<hr>";
//print_r($_SESSION);
//echo "<hr>";


//$_GET['NewItem'] = $rh_ArtCargo;
//$_SESSION['CustomerID'] = $_GET['CustomerID'];
$articulo = $rh_ArtCargo;
$transInt = $_GET['InvoiceNumber'];

$getT = 'SELECT day(debtortrans.trandate) AS dia, month(debtortrans.trandate) AS mes, year(debtortrans.trandate) AS anio, rh_invoicesreference.ref, rh_invoicesreference.extinvoice, debtortrans.*, custbranch.branchcode FROM debtortrans inner join custbranch ON debtortrans.debtorno = custbranch.debtorno inner join rh_invoicesreference ON debtortrans.transno = rh_invoicesreference.intinvoice WHERE type = 10 AND transno = '.$transInt;
$resT = DB_query($getT,$db);

$myrow = DB_fetch_array($resT);

$transExt = $myrow['extinvoice'];

$totalFac = $myrow['ovamount'];

switch ($myrow['mes'])
{
	case 1:
	{
		$mes = 'Enero';
		break;
	}
	case 2:
	{
		$mes = 'Febrero';
		break;
	}
	case 3:
	{
		$mes = 'Marzo';
		break;
	}
	case 4:
	{
		$mes = 'Abril';
		break;
	}
	case 5:
	{
		$mes = 'Mayo';
		break;
	}
	case 6:
	{
		$mes = 'Junio';
		break;
	}
	case 7:
	{
		$mes = 'Julio';
		break;
	}
	case 8:
	{
		$mes = 'Agosto';
		break;
	}
	case 9:
	{
		$mes = 'Septiembre';
		break;
	}
	case 10:
	{
		$mes = 'Octubre';
		break;
	}
	case 11:
	{
		$mes = 'Noviembre';
		break;
	}
	case 12:
	{
		$mes = 'Diciembre';
		break;
	}
}

$fecha = $myrow['dia'].' de '.$mes. ' de '.$myrow['anio'];

//iJPe
$porNC = .01;

$totalFac = $totalFac * $porNC;


$sqlA = "SELECT * FROM stockmaster WHERE stockid = '".$articulo."'";

DB_query("BEGIN",$db);
$HeaderSQL = "INSERT INTO salesorders (
                        debtorno,
                        branchcode,
                        customerref,
                        comments,
                        orddate,
                        ordertype,
                        shipvia,
                        deliverto,
                        deladd1,
                        deladd2,
                        deladd3,
                        deladd4,
                        deladd5,
                        deladd6,
                        contactphone,
                        contactemail,
                        freightcost,
                        fromstkloc,
                       fromstkloc_virtual,
                        deliverydate,
                        quotation,
        deliverblind)
                 SELECT debtorno,
                        branchcode,
                        customerref,
                        '' AS comments,
                        NOW() AS orddate,
                        ordertype,
                        shipvia,
                        deliverto,
                        deladd1,
                        deladd2,
                        deladd3,
                        deladd4,
                        deladd5,
                        deladd6,
                        contactphone,
                        contactemail,
                        freightcost,
                        fromstkloc,
                       fromstkloc,
                        deliverydate,
                        quotation,
                        deliverblind
                        FROM salesorders
                        WHERE orderno = ".$myrow['order_']."
                        ";

$ErrMsg = _('The order cannot be added because');
$InsertQryResult = DB_query($HeaderSQL,$db,$ErrMsg,'',true);

$OrderNo = GetNextTransNo(30, $db);
//if(strlen($_SESSION['rh_comments'])>20){
//                $sql = "INSERT INTO rh_priceauth (user_, date_, comments, order_) VALUES (
//        '".$_SESSION['UserID']."',
//        '".date('Y-m-d')."',
//        '".$_SESSION['rh_comments']."',
//        ".$OrderNo.")";
//        $Auth_Res = DB_query($sql,$db,'ERROR: Imposible insertar los datos de autorizacion','',true);
//}
//echo $OrderNo = DB_Last_Insert_ID($db,'salesorders','orderno');
// bowikaxu realhost - 9 july 2008 - save item description
// bowikaxu reslhost - 4 august 2008 - save the actual cost
$LineItemsSQL = "INSERT INTO salesorderdetails (
                                        orderlineno,
                                        orderno,
                                        stkcode,
                                        unitprice,
                                        quantity,                                        
                                        narrative,
                                        description,
                                        poline,
                                        rh_cost,
                                        itemdue)
                                 SELECT 0 AS line, ".$OrderNo." AS orderno,
                                stockid, ".$totalFac." AS price, 1 AS quantity, 'Fact. $transExt - $fecha' AS narrative, description,
                                0 AS poline, materialcost, NOW() FROM stockmaster WHERE stockid = '".$articulo."'";

                        
        $Ins_LineItemResult = DB_query($LineItemsSQL,$db,'Imposible insertar articulo','',true);
 /* inserted line items into sales order details */

DB_query("COMMIT",$db);

echo "<div><script type='text/javascript'>realizarNCargo($OrderNo);</script></div>";

//$_GET['OrderNumber'] =
//include('rh_ConfirmDispatch_Invoice_NC.php?');


?>
