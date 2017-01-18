<?php
/* $Revision: 385 $ */
ob_start();
include('includes/DefineCartClass.php');
$PageSecurity = 1;
/* Session started in session.inc for password checking and authorisation level check
config.php is in turn included in session.inc*/

include('includes/session.inc');

$afil=unserialize(GetConfig('Afiliaciones'));
$p=$afil['Prefijo'];

if(!isset($PuedeCambiarPrecio))
	$PuedeCambiarPrecio=$_SESSION['AccessLevel']==8||$_SESSION['AccessLevel']==11;
//Administrador y rol de facturacion
if (isset($_GET['ModifyOrderNumber'])) {
    $title = _('Modifying Order') . ' ' . $_GET['ModifyOrderNumber'];
} else {
    $title = _('Select Order Items');
}

include('includes/header.inc');
include('includes/GetPrice.inc');
include('includes/rh_GetDiscount.inc');
include('includes/SQL_CommonFunctions.inc');

function addFleteItems(){
    global $db;
    $newItemQtySet = isSet($NewItemQty);
    $newItemSet = isSet($NewItem);

    $newItemQtyOld = $NewItemQty;
    $newItemOld = $NewItem;

    $NewItem = 'FLETE';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'SEGURO';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'MANIOBRAS';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'AUTOPISTAS';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'DEMORAS';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'CRUCES';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'OTROS';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');
    $NewItem = 'CPAC';
    $NewItemQty = 1;
    include('includes/SelectOrderItems_IntoCart.inc');

    if($newItemSet)
        $NewItem = $newItemOld;
    else
        unSet($NewItem);

    if($newItemQtySet)
        $NewItemQty = $newItemQtyOld;
    else
        unSet($NewItemQty);
}

?>
<script language='javascript'>
function create_ajaxOb(){
    var xmlHttp;
    try{// Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    }
    catch(e){
        // Internet Explorer
        try{
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        }
        catch(e){
            try{
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e){
                alert("Your browser does not support AJAX!");
                return false;
            }
        }
    }
    return xmlHttp;
}
function nombre_producto(clave, n){
    xmlhttp = create_ajaxOb();
    var url = "rh_ajax_nombre_producto.php";
    var posts = "clave=" + clave;
    xmlhttp.onreadystatechange = function(){
        if(xmlhttp.readyState == 4){//cuando la respuesta llegue
            respuesta = xmlhttp.responseText;
            document.getElementById("prod_" + n).value = respuesta;
        }
    }
    xmlhttp.open("post", url, true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.setRequestHeader("Content-length", posts.length);
    xmlhttp.setRequestHeader("Connection", "close");
    xmlhttp.send(posts);
}
</script>

<?php
/**
 * @Todo
 * @JotaOwen
 * Busqueda por Folio de Afiliación
 * */
if (isset($_REQUEST['AfilFolio']) && $_REQUEST['AfilFolio']!=''){

   $FolioTitular = $_REQUEST['AfilFolio'];

   $GetDebtorNo ="SELECT debtorno FROM rh_titular WHERE folio = '" . $FolioTitular . "'";
   $_GetDebtorNo = DB_query($GetDebtorNo, $db);
   $_2GetDebtorNo = DB_fetch_assoc($_GetDebtorNo);

   $_POST['CustCode'] = $_2GetDebtorNo['debtorno'];
   $_POST['SearchCust'] = "Search Now";

   if(!empty($_GET['AfilFolio'])){
       $_POST['Select'] = $_2GetDebtorNo['debtorno'] . " - " . $_2GetDebtorNo['debtorno'];
   }

}

if (isset($_POST['QuickEntry'])){
   unset($_POST['PartSearch']);
}
/****************************************************************************************************************************/

if (isset($_POST['order_items'])){
    $ji = 1;
    while($ji < $_POST['jjjjj']){
        $NewItem_array[$_POST['itm'.$ji]] = trim($_POST['val'.$ji]);
        $ji++;
    }
}
/****************************************************************************************************************************/

if (isset($_GET['NewItem'])){
    $NewItem = trim($_GET['NewItem']);
}

// bowikaxu realhost sept 07 - price auth.
?>
<script type="text/javascript">
function validate(F)
{

    if (F.value.length > 40){
        window.document.forms['form'].DeliveryDetails.disabled = false;
    }else{
        window.document.forms['form'].DeliveryDetails.disabled = true;
    }
}
</script>
<?php

if (isset($_GET['NewOrder'])){
    if(isSet($_GET['isCartaPorte']) && $_GET['isCartaPorte']==true)
        $_SESSION['isCartaPorte'] = true;
    else
        unSet($_SESSION['isCartaPorte']);
  /*New order entry - clear any existing order details from the Items object and initiate a newy*/
     if (isset($_SESSION['Items'])){
        unset ($_SESSION['Items']->LineItems);
        $_SESSION['Items']->ItemsOrdered=0;
        unset ($_SESSION['Items']);
    }
    /*
    Session_register('Items');
    Session_register('RequireCustomerSelection');
    Session_register('CreditAvailable');
    Session_register('ExistingOrder');
    Session_register('PrintedPackingSlip');
    Session_register('DatePackingSlipPrinted');
    */

    $_SESSION['ExistingOrder']=0;
    $_SESSION['Items'] = new cart;

    if (count($_SESSION['AllowedPageSecurityTokens'])==1){ //its a customer logon
        $_SESSION['Items']->DebtorNo=$_SESSION['CustomerID'];
        $_SESSION['RequireCustomerSelection']=0;
    } else {
        $_SESSION['Items']->DebtorNo='';
        $_SESSION['RequireCustomerSelection']=1;
    }

}

echo '<A HREF="'. $rootpath . '/SelectSalesOrder.php?' . SID . '">'. _('Back to Sales Orders'). '</A><BR>';


if (isset($_GET['ModifyOrderNumber'])
    AND $_GET['ModifyOrderNumber']!=''){


/* The delivery check screen is where the details of the order are either updated or inserted depending on the value of ExistingOrder */

    if (isset($_SESSION['Items'])){
        unset ($_SESSION['Items']->LineItems);
        unset ($_SESSION['Items']);
    }
    /*
    Session_register('Items');
    Session_register('RequireCustomerSelection');
    Session_register('CreditAvailable');
    Session_register('ExistingOrder');
    Session_register('PrintedPackingSlip');
    Session_register('DatePackingSlipPrinted');
    */
    $_SESSION['ExistingOrder']=$_GET['ModifyOrderNumber'];
    $_SESSION['RequireCustomerSelection'] = 0;
    $_SESSION['Items'] = new cart;

/*read in all the guff from the selected order into the Items cart  */

    if ($_SESSION['vtiger_integration']==1){
        // bowikaxu realhost - may 2007 - rh_status
        $OrderHeaderSQL = 'SELECT salesorders.debtorno,
                debtorsmaster.name,
                salesorders.branchcode,
                salesorders.vtiger_accountid,
                salesorders.customerref,
                salesorders.comments,
                salesorders.orddate,
                salesorders.ordertype,
                salestypes.sales_type,
                salesorders.shipvia,
                salesorders.deliverto,
                salesorders.deladd1,
                salesorders.deladd2,
                salesorders.deladd3,
                salesorders.deladd4,
                salesorders.deladd5,
                salesorders.deladd6,
                salesorders.deladd7,
                salesorders.deladd8,
                salesorders.deladd9,
                salesorders.deladd10,
                salesorders.contactphone,
                salesorders.contactemail,
                salesorders.freightcost,
                salesorders.deliverydate,
                salesorders.rh_status=0,
                debtorsmaster.currcode,
                salesorders.fromstkloc,
                salesorders.printedpackingslip,
                salesorders.datepackingslipprinted,
                salesorders.quotation,
                salesorders.deliverblind,
                debtorsmaster.customerpoline,
                custbranch.estdeliverydays
            FROM salesorders,
                debtorsmaster,
                salestypes,
                custbranch
            WHERE salesorders.ordertype=salestypes.typeabbrev
            AND salesorders.debtorno = debtorsmaster.debtorno
            AND salesorders.rh_status = 0
            AND salesorders.debtorno = custbranch.debtorno
            AND salesorders.branchcode = custbranch.branchcode
            AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];

    } else {
        // bowikaxu realhost - may 2007 - rh_status
        $OrderHeaderSQL = 'SELECT salesorders.debtorno,
                debtorsmaster.name,
                salesorders.branchcode,
                salesorders.customerref,
                salesorders.comments,
                salesorders.orddate,
                salesorders.ordertype,
                salestypes.sales_type,
                salesorders.shipvia,
                salesorders.deliverto,
                salesorders.deladd1,
                salesorders.deladd2,
                salesorders.deladd3,
                salesorders.deladd4,
                salesorders.deladd5,
                salesorders.deladd6,
                salesorders.deladd7,
                salesorders.deladd8,
                salesorders.deladd9,
                salesorders.deladd10,
                salesorders.contactphone,
                salesorders.contactemail,
                salesorders.freightcost,
                salesorders.deliverydate,
                salesorders.rh_status,
                debtorsmaster.currcode,
                salesorders.fromstkloc,
                salesorders.printedpackingslip,
                salesorders.datepackingslipprinted,
                salesorders.quotation,
                salesorders.deliverblind,
                debtorsmaster.customerpoline,
                custbranch.estdeliverydays
            FROM salesorders,
                debtorsmaster,
                salestypes,
                custbranch
            WHERE salesorders.ordertype=salestypes.typeabbrev
            AND salesorders.debtorno = debtorsmaster.debtorno
            AND salesorders.rh_status = 0
            AND salesorders.debtorno = custbranch.debtorno
            AND salesorders.branchcode = custbranch.branchcode
            AND salesorders.orderno = ' . $_GET['ModifyOrderNumber'];
    }

    $ErrMsg =  _('The order cannot be retrieved because');
    $GetOrdHdrResult = DB_query($OrderHeaderSQL,$db,$ErrMsg);

    if (DB_num_rows($GetOrdHdrResult)==1) {

        $myrow = DB_fetch_array($GetOrdHdrResult);
        $_SESSION['Items']->OrderNo = $_GET['ModifyOrderNumber'];
        $_SESSION['Items']->DebtorNo = $myrow['debtorno'];
/*CustomerID defined in header.inc */
        $_SESSION['Items']->Branch = $myrow['branchcode'];
        $_SESSION['Items']->CustomerName = $myrow['name'];
        $_SESSION['Items']->CustRef = $myrow['customerref'];
        $_SESSION['Items']->Comments = $myrow['comments'];

        $_SESSION['Items']->DefaultSalesType =$myrow['ordertype'];
        $_SESSION['Items']->SalesTypeName =$myrow['sales_type'];
        $_SESSION['Items']->DefaultCurrency = $myrow['currcode'];
        $_SESSION['Items']->ShipVia = $myrow['shipvia'];
        $BestShipper = $myrow['shipvia'];
        $_SESSION['Items']->DeliverTo = $myrow['deliverto'];
        $_SESSION['Items']->DeliveryDate = ConvertSQLDate($myrow['deliverydate']);
        $_SESSION['Items']->DelAdd1 = $myrow['deladd1'];
        $_SESSION['Items']->DelAdd2 = $myrow['deladd2'];
        $_SESSION['Items']->DelAdd3 = $myrow['deladd3'];
        $_SESSION['Items']->DelAdd4 = $myrow['deladd4'];
        $_SESSION['Items']->DelAdd5 = $myrow['deladd5'];
        $_SESSION['Items']->DelAdd6 = $myrow['deladd6'];
        $_SESSION['Items']->DelAdd7 = $myrow['deladd7'];
        $_SESSION['Items']->DelAdd8 = $myrow['deladd8'];
        $_SESSION['Items']->DelAdd9 = $myrow['deladd9'];
        $_SESSION['Items']->DelAdd10 = $myrow['deladd10'];
        $_SESSION['Items']->PhoneNo = $myrow['contactphone'];
        $_SESSION['Items']->Email = $myrow['contactemail'];
        $_SESSION['Items']->Location = $myrow['fromstkloc'];
        $_SESSION['Items']->Quotation = $myrow['quotation'];
        $_SESSION['Items']->FreightCost = $myrow['freightcost'];
        $_SESSION['Items']->Orig_OrderDate = $myrow['orddate'];
        $_SESSION['PrintedPackingSlip'] = $myrow['printedpackingslip'];
        $_SESSION['DatePackingSlipPrinted'] = $myrow['datepackingslipprinted'];
        $_SESSION['Items']->DeliverBlind = $myrow['deliverblind'];
        $_SESSION['Items']->DefaultPOLine = $myrow['customerpoline'];
        $_SESSION['Items']->DeliveryDays = $myrow['estdeliverydays'];

/*need to look up customer name from debtors master then populate the line items array with the sales order details records */

        if ($_SESSION['vtiger_integration']==1){

            // bowikaxu realhost - 9 july 2008 - get item description from previus saved order
            $LineItemsSQL = "SELECT salesorderdetails.orderlineno,
                salesorderdetails.stkcode,
                stockmaster.vtiger_productid,
                stockmaster.description AS description2,
                stockmaster.volume,
                stockmaster.kgs,
                stockmaster.units,
                (stockmaster.materialcost+stockmaster.overheadcost+stockmaster.labourcost) as cost,
                salesorderdetails.unitprice,
                salesorderdetails.quantity,
                salesorderdetails.discountpercent,
                salesorderdetails.actualdispatchdate,
                salesorderdetails.qtyinvoiced,
                salesorderdetails.narrative,
                salesorderdetails.description,
                salesorderdetails.itemdue,
                salesorderdetails.poline,
                salesorderdetails.rh_cost,
                locstock.quantity as qohatloc,
                stockmaster.mbflag,
                stockmaster.discountcategory,
                stockmaster.decimalplaces,
                salesorderdetails.completed=0,
                if(isnull(D1.descuento),0.00,D1.descuento*100) as discount1,
                if(isnull(D2.descuento),0.00,D2.descuento*100) as discount2
                FROM salesorderdetails INNER JOIN stockmaster
                ON salesorderdetails.stkcode = stockmaster.stockid
                INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
                left join rh_descuentos D1 on salesorderdetails.orderno=D1.transno and salesorderdetails.orderlineno = D1.orderlineno and D1.type=30 and D1.tipo_descuento=1
                left join rh_descuentos D2 on salesorderdetails.orderno=D2.transno and salesorderdetails.orderlineno = D2.orderlineno and D1.type=30 and D2.tipo_descuento=2
                WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
                AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'] . "
                ORDER BY salesorderdetails.orderlineno";
        } else {

            $LineItemsSQL = "SELECT salesorderdetails.orderlineno,
                salesorderdetails.stkcode,
                stockmaster.description AS description2,
                stockmaster.volume,
                stockmaster.kgs,
                stockmaster.units,
                (stockmaster.materialcost+stockmaster.overheadcost+stockmaster.labourcost) as cost,
                salesorderdetails.unitprice,
                salesorderdetails.quantity,
                salesorderdetails.discountpercent,
                salesorderdetails.actualdispatchdate,
                salesorderdetails.qtyinvoiced,
                salesorderdetails.narrative,
                salesorderdetails.description,
                salesorderdetails.itemdue,
                salesorderdetails.poline,
                salesorderdetails.rh_cost,
                locstock.quantity as qohatloc,
                stockmaster.mbflag,
                stockmaster.discountcategory,
                stockmaster.decimalplaces,
                salesorderdetails.completed,
                if(isnull(D1.descuento),0.00,D1.descuento*100) as discount1,
                if(isnull(D2.descuento),0.00,D2.descuento*100) as discount2
                FROM salesorderdetails INNER JOIN stockmaster
                ON salesorderdetails.stkcode = stockmaster.stockid
                INNER JOIN locstock ON locstock.stockid = stockmaster.stockid
                left join rh_descuentos D1 on salesorderdetails.orderno=D1.transno and salesorderdetails.orderlineno = D1.orderlineno and D1.type=30 and D1.tipo_descuento=1
                left join rh_descuentos D2 on salesorderdetails.orderno=D2.transno and salesorderdetails.orderlineno = D2.orderlineno and D1.type=30 and D2.tipo_descuento=2
                WHERE  locstock.loccode = '" . $myrow['fromstkloc'] . "'
                AND salesorderdetails.orderno =" . $_GET['ModifyOrderNumber'] . "
                ORDER BY salesorderdetails.orderlineno";
        }

        $ErrMsg = _('The line items of the order cannot be retrieved because');
        $LineItemsResult = db_query($LineItemsSQL,$db,$ErrMsg);
        if (db_num_rows($LineItemsResult)>0) {

            while ($myrow=db_fetch_array($LineItemsResult)) {
                    $sql ="select factor from rh_sales_factors,stockmaster where rh_sales_factors.id=stockmaster.rh_sales_factor and stockmaster.stockid='".$myrow['stkcode']."'";
                    //echo $sql;
                    $result = DB_query($sql, $db);
                    if (DB_num_rows($result)>0 && ($_SESSION['Items']->DefaultCurrency=='MXN')){
                        $factor=DB_fetch_row($result);
                    }else{
                        $factor[0]=1;
                        if(DB_num_rows($result)==0) {
                            prnMsg(_('El articulo ').$myrow['stkcode']._(' no se le encontro factor, verifique'),'warn');
                        }
                    }

                    if ($myrow['completed']==0){
                        $_SESSION['Items']->add_to_cart($myrow['stkcode'],
                                $myrow['quantity'],
                                $myrow['description'],
                                number_format(($myrow['unitprice']/$factor[0]),4,'.',''),
                                $myrow['discountpercent'],
                                $myrow['units'],
                                $myrow['volume'],
                                $myrow['kgs'],
                                $myrow['qohatloc'],
                                $myrow['mbflag'],
                                $myrow['actualdispatchdate'],
                                $myrow['qtyinvoiced'],
                                $myrow['discountcategory'],
                                0,  /*Controlled*/
                                0,  /*Serialised */
                                $myrow['decimalplaces'],
                                $myrow['narrative'],
                                'No', /* Update DB */
                                $myrow['orderlineno'],
                                0,
                                '',
                                $myrow['itemdue'],
                                $myrow['poline'],
                                $factor[0],
                                $myrow['discount1'],
                                $myrow['discount2']
                                );
                /*Just populating with existing order - no DBUpdates */
                                // bowikaxu realhost 4 august 2008
                                // use the saved cost or the actual cost
                                if($myrow['rh_cost']>0){
                                    $_SESSION['Items']->LineItems[($_SESSION['Items']->LineCounter -1)]->StandardCost = $myrow['rh_cost'];
                                }else {
                                    $_SESSION['Items']->LineItems[($_SESSION['Items']->LineCounter -1)]->StandardCost = $myrow['cost'];
                                }
                    }
                    $LastLineNo = $myrow['orderlineno'];
            } /* line items from sales order details */
             $_SESSION['Items']->LineCounter = $LastLineNo+1;
        } //end of checks on returned data set
    }
}
if(isset($_POST['ItemDescription'])){
    foreach ($_POST['ItemDescription'] as $key => $value) {
       $_SESSION['Items']->LineItems[$key]->ItemDescription=$value;
    }
}
$locsql = "SELECT locationname
           FROM locations
           WHERE loccode='" . $_SESSION['Items']->Location ."'";
$locresult = db_query($locsql, $db);
$locrow = db_fetch_array($locresult);
$location = $locrow[0];

if (!isset($_SESSION['Items'])){
    /* It must be a new order being created $_SESSION['Items'] would be set up from the order
    modification code above if a modification to an existing order. Also $ExistingOrder would be
    set to 1. The delivery check screen is where the details of the order are either updated or
    inserted depending on the value of ExistingOrder */

    Session_register('Items');
    Session_register('RequireCustomerSelection');
    Session_register('CreditAvailable');
    Session_register('ExistingOrder');
    Session_register('PrintedPackingSlip');
    Session_register('DatePackingSlipPrinted');

    $_SESSION['ExistingOrder']=0;
    $_SESSION['Items'] = new cart;
    $_SESSION['PrintedPackingSlip'] =0; /*Of course cos the order aint even started !!*/

    if (in_array(2,$_SESSION['AllowedPageSecurityTokens']) AND ($_SESSION['Items']->DebtorNo=='' OR !isset($_SESSION['Items']->DebtorNo))){

    /* need to select a customer for the first time out if authorisation allows it and if a customer
     has been selected for the order or not the session variable CustomerID holds the customer code
     already as determined from user id /password entry  */
        $_SESSION['RequireCustomerSelection'] = 1;
    } else {
        $_SESSION['RequireCustomerSelection'] = 0;
    }
}

if (isset($_POST['ChangeCustomer']) AND $_POST['ChangeCustomer']!=''){

    if ($_SESSION['Items']->Any_Already_Delivered()==0){
        $_SESSION['RequireCustomerSelection']=1;
    } else {
        prnMsg(_('The customer the order is for cannot be modified once some of the order has been invoiced'),'warn');
    }
}

$msg='';

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// bowikaxu realhost - add rfc search
if (isset($_POST['SearchCust']) && in_array(2,$_SESSION['AllowedPageSecurityTokens'])){

    If (($_POST['CustKeywords']!='') AND (($_POST['CustCode']!='') OR ($_POST['CustPhone']!=''))) {
        $msg= _('Customer name keywords have been used in preference to the customer code or phone entered');
    }
    If (($_POST['CustCode']!='') AND ($_POST['CustPhone']!='')) {
        $msg=_('Customer code has been used in preference to the customer phone entered') . '.';
    }
    If (($_POST['CustRFC']!='') AND ($_POST['CustCode']!='')) {
        $msg=_('Customer RFC has been used in preference to the customer code entered') . '.';
    }
    If (($_POST['CustKeywords']=='') AND ($_POST['CustCode']=='')  AND ($_POST['CustPhone']=='') AND ($_POST['CustRFC']=='')) {
        $msg=_('At least one Customer Name keyword OR an extract of a Customer Code or phone or RFC number must be entered for the search');
        //echo "--> ".$_POST['CustKeywords']."<BR><BR>";
    } else {
        If (strlen($_POST['CustKeywords'])>0) {
        //insert wildcard characters in spaces
            $_POST['CustKeywords'] = strtoupper(trim($_POST['CustKeywords']));
            $i=0;
            $SearchString = '%';
            while (strpos($_POST['CustKeywords'], ' ', $i)) {
                $wrdlen=strpos($_POST['CustKeywords'],' ',$i) - $i;
                $SearchString=$SearchString . substr($_POST['CustKeywords'],$i,$wrdlen) . '%';
                $i=strpos($_POST['CustKeywords'],' ',$i) +1;
            }
            $SearchString = $SearchString. substr($_POST['CustKeywords'],$i).'%';

            $SQL = "SELECT custbranch.brname,
                    custbranch.contactname,
                    custbranch.phoneno,
                    custbranch.faxno,
                    custbranch.branchcode,
                    custbranch.debtorno,
                    debtorsmaster.name,
                    debtorsmaster.taxref
                FROM custbranch, debtorsmaster
                WHERE debtorsmaster.name " . LIKE . " '$SearchString'
                AND custbranch.debtorno = debtorsmaster.debtorno
                AND custbranch.branchcode LIKE '{$p}%'
                AND custbranch.disabletrans=0
                ORDER BY custbranch.brname limit 100";
        } elseif (strlen($_POST['CustCode'])>0){
            //$_POST['CustCode'] = strtoupper(trim($_POST['CustCode']));
            $debtorno = strtoupper(trim($_POST['CustCode']));
            if(strlen($_POST['AfilFolio']) == 0){
                $debtorno = "%$debtorno%";
            }
            // OR custbranch.branchcode " . LIKE . " '%" . $_POST['CustCode'] . "%'
           $SQL = "SELECT custbranch.brname,
                    custbranch.contactname,
                    custbranch.phoneno,
                    custbranch.faxno,
                    custbranch.branchcode,
                    custbranch.debtorno,
                    debtorsmaster.taxref
                FROM custbranch,debtorsmaster
                WHERE custbranch.debtorno " . LIKE . " '{$debtorno}'
                AND debtorsmaster.debtorno = custbranch.debtorno
                AND custbranch.branchcode LIKE '{$p}%'
                AND custbranch.disabletrans=0
                ORDER BY custbranch.debtorno limit 100";
        } elseif (strlen($_POST['CustPhone'])>0){
            $SQL = "SELECT custbranch.brname,
                    custbranch.contactname,
                    custbranch.phoneno,
                    custbranch.faxno,
                    custbranch.branchcode,
                    custbranch.debtorno,
                    debtorsmaster.taxref
                FROM custbranch, debtorsmaster
                WHERE custbranch.phoneno " . LIKE . " '%" . $_POST['CustPhone'] . "%'
                AND custbranch.disabletrans=0
                AND custbranch.debtorno = debtorsmaster.debtorno
                AND custbranch.branchcode LIKE '{$p}%'
                ORDER BY custbranch.brname limit 100";
        }elseif (strlen($_POST['CustRFC'])>0){

            $SQL = "SELECT custbranch.brname,
                    custbranch.contactname,
                    custbranch.phoneno,
                    custbranch.faxno,
                    custbranch.branchcode,
                    custbranch.debtorno,
                    debtorsmaster.taxref
                FROM custbranch, debtorsmaster
                WHERE custbranch.disabletrans=0
                AND debtorsmaster.debtorno = custbranch.debtorno
                AND custbranch.branchcode LIKE '{$p}%'
                AND debtorsmaster.taxref LIKE '%".$_POST['CustRFC']."%'
                ORDER BY custbranch.brname limit 100";
        }

        $ErrMsg = _('The searched customer records requested cannot be retrieved because');
        $result_CustSelect = DB_query($SQL,$db,$ErrMsg);

        if (DB_num_rows($result_CustSelect)==1){
            $myrow=DB_fetch_array($result_CustSelect);
            $_POST['Select'] =  ' 0 - T -' . $myrow['debtorno'] ;
            if ($p!='T-')
            	$_POST['Select'] = $myrow['debtorno'] . ' - ' . $myrow['branchcode'];
        } elseif (DB_num_rows($result_CustSelect)==0){
            prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
        }
    } /*one of keywords or custcode was more than a zero length string */
} /*end of if search for customer codes/names */


// will only be true if page called from customer selection form or set because only one customer
// record returned from a search so parse the $Select string into customer code and branch code */
if (isset($_POST['Select']) AND $_POST['Select']!='') {

    $_DebtorNo = explode('-',$_POST['Select']);
    if ($p!='T-'){
    	$_POST['Select'] =trim(array_shift($_DebtorNo));
    	$_SESSION['Items']->Branch = trim(implode('-',$_DebtorNo));
    }else{
    	$_SESSION['Items']->Branch = "{$p}" . $_DebtorNo[2];
	    $_POST['Select'] = $_DebtorNo[2];
    }


    // Now check to ensure this account is not on hold */
    $sql = "SELECT debtorsmaster.name,
            holdreasons.dissallowinvoices,
            debtorsmaster.salestype,
            salestypes.sales_type,
            debtorsmaster.currcode,
            debtorsmaster.customerpoline,
                       debtorsmaster.holdreason
        FROM debtorsmaster
        LEFT JOIN rh_titular ON rh_titular.debtorno = debtorsmaster.debtorno,
            holdreasons,
            salestypes
        WHERE debtorsmaster.salestype=salestypes.typeabbrev
        AND debtorsmaster.holdreason=holdreasons.reasoncode
        AND debtorsmaster.debtorno = '" . $_POST['Select'] . "'";
    if ($p!='')$sql .= " AND rh_titular.movimientos_afiliacion = 'Activo' ";

    $ErrMsg = _('The details of the customer selected') . ': ' .  $_POST['Select'] . ' ' . _('cannot be retrieved because');
    $DbgMsg = _('The SQL used to retrieve the customer details and failed was') . ':';
    $result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

    $myrow = DB_fetch_row($result);
    if ($myrow[1] != 1){
        if ($myrow[1]==2){
            prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently flagged as an account that needs to be watched. Please contact the credit control personnel to discuss'),'warn');
        }

        $_SESSION['Items']->DebtorNo=$_POST['Select'];
        $_SESSION['RequireCustomerSelection']=0;
        $_SESSION['Items']->CustomerName = $myrow[0];

        # the sales type determines the price list to be used by default the customer of the user is
        # defaulted from the entry of the userid and password.

        $_SESSION['Items']->DefaultSalesType = $myrow[2];
        $_SESSION['Items']->SalesTypeName = $myrow[3];
        $_SESSION['Items']->DefaultCurrency = $myrow[4];
        $_SESSION['Items']->DefaultPOLine = $myrow[5];

                //iJPe 2010-04-16 holdreason
                $_SESSION['Items']->Holdreason = $myrow[6];



        # the branch was also selected from the customer selection so default the delivery details from the customer branches table CustBranch. The order process will ask for branch details later anyway
        $sql = "SELECT custbranch.brname,
                custbranch.braddress1,
                custbranch.braddress2,
                custbranch.braddress3,
                custbranch.braddress4,
                custbranch.braddress5,
                custbranch.braddress6,
                custbranch.braddress7,
                custbranch.braddress8,
                custbranch.braddress9,
                custbranch.braddress10,
                custbranch.phoneno,
                custbranch.email,
                custbranch.defaultlocation,
                custbranch.defaultshipvia,
                custbranch.deliverblind,
                custbranch.specialinstructions,
                custbranch.estdeliverydays
            FROM custbranch
            WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
            AND custbranch.debtorno = '" . $_POST['Select'] . "'";

        $ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
        $DbgMsg = _('SQL used to retrieve the branch details was') . ':';
        $result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

        if (DB_num_rows($result)==0){

            prnMsg(_('The branch details for branch code') . ': ' . $_SESSION['Items']->Branch . ' ' . _('against customer code') . ': ' . $_POST['Select'] . ' ' . _('could not be retrieved') . '. ' . _('Check the set up of the customer and branch'),'error');

            if ($debug==1){
                echo '<BR>' . _('The SQL that failed to get the branch details was') . ':<BR>' . $sql;
            }
            include('includes/footer.inc');
            exit;
        }

        $myrow = DB_fetch_row($result);
        $_SESSION['Items']->DeliverTo = $myrow[0];
        $_SESSION['Items']->DelAdd1 = $myrow[1];
        $_SESSION['Items']->DelAdd2 = $myrow[2];
        $_SESSION['Items']->DelAdd3 = $myrow[3];
        $_SESSION['Items']->DelAdd4 = $myrow[4];
        $_SESSION['Items']->DelAdd5 = $myrow[5];
        $_SESSION['Items']->DelAdd6 = $myrow[6];
        $_SESSION['Items']->DelAdd7 = $myrow[7];
        $_SESSION['Items']->DelAdd8 = $myrow[8];
        $_SESSION['Items']->DelAdd9 = $myrow[9];
        $_SESSION['Items']->DelAdd10 = $myrow[10];
        $_SESSION['Items']->PhoneNo = $myrow[11];
        $_SESSION['Items']->Email = $myrow[12];
        $_SESSION['Items']->Location = $myrow[13];
        $_SESSION['Items']->ShipVia = $myrow[14];
        $_SESSION['Items']->DeliverBlind = $myrow[15];
        $_SESSION['Items']->SpecialInstructions = $myrow[16];
        $_SESSION['Items']->DeliveryDays = $myrow[17];

        $locsql = "SELECT locationname ".
        " FROM locations ".
        " WHERE loccode='" . $_SESSION['Items']->Location ."'";
        $locresult = db_query($locsql, $db);
        $locrow = db_fetch_array($locresult);
        $location = $locrow[0];

                if(isSet($_SESSION['isCartaPorte']) && $_SESSION['isCartaPorte']==true)
                    addFleteItems();
        if ($_SESSION['Items']->SpecialInstructions)
          prnMsg($_SESSION['Items']->SpecialInstructions,'warn');

        if ($_SESSION['CheckCreditLimits'] > 0){  /*Check credit limits is 1 for warn and 2 for prohibit sales */
            $_SESSION['Items']->CreditAvailable = GetCreditAvailable($_POST['Select'],$db);

            if ($_SESSION['CheckCreditLimits']==1 AND $_SESSION['Items']->CreditAvailable <=0){
                prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently at or over their credit limit'),'warn');
            } elseif ($_SESSION['CheckCreditLimits']==2 AND $_SESSION['Items']->CreditAvailable <=0){
                prnMsg(_('No more orders can be placed by') . ' ' . $myrow[0] . ' ' . _(' their account is currently at or over their credit limit'),'warn');
                include('includes/footer.inc');
                exit;
            }
        }

    } else {
        prnMsg(_('The') . ' ' . $myrow[0] . ' ' . _('account is currently on hold please contact the credit control personnel to discuss'),'warn');
    }

} elseif (!$_SESSION['Items']->DefaultSalesType OR $_SESSION['Items']->DefaultSalesType=='')    {

#Possible that the check to ensure this account is not on hold has not been done
#if the customer is placing own order, if this is the case then
#DefaultSalesType will not have been set as above

    $sql = "SELECT debtorsmaster.name,
            holdreasons.dissallowinvoices,
            debtorsmaster.salestype,
            debtorsmaster.currcode,
            debtorsmaster.customerpoline,
                       debtorsmaster.holdreason
        FROM debtorsmaster, holdreasons
        WHERE debtorsmaster.holdreason=holdreasons.reasoncode
        AND debtorsmaster.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

    if (isset($_POST['Select'])) {
        $ErrMsg = _('The details for the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
    } else {
        $ErrMsg = '';
    }
    $DbgMsg = _('SQL used to retrieve the customer details was') . ':<BR>' . $sql;
    $result =DB_query($sql,$db,$ErrMsg,$DbgMsg);

    $myrow = DB_fetch_row($result);
    if ($myrow[1] == 0){

        $_SESSION['Items']->CustomerName = $myrow[0];

# the sales type determines the price list to be used by default the customer of the user is
# defaulted from the entry of the userid and password.

        $_SESSION['Items']->DefaultSalesType = $myrow[2];
        $_SESSION['Items']->DefaultCurrency = $myrow[3];
	if($_SESSION['Items']->Branch =="" && $_SESSION['UserBranch']!="")
	        $_SESSION['Items']->Branch = $_SESSION['UserBranch'];
        $_SESSION['Items']->DefaultPOLine = $myrow[4];

                //iJPe 2010-04-16 holdreason
                $_SESSION['Items']->Holdreason = $myrow[5];


    // the branch would be set in the user data so default delivery details as necessary. However,
    // the order process will ask for branch details later anyway

        $sql = "SELECT custbranch.brname,
            custbranch.braddress1,
            custbranch.braddress2,
            custbranch.braddress3,
            custbranch.braddress4,
            custbranch.braddress5,
            custbranch.braddress6,
            custbranch.braddress7,
            custbranch.braddress8,
            custbranch.braddress9,
            custbranch.braddress10,
            custbranch.phoneno,
            custbranch.email,
            custbranch.defaultlocation,
            custbranch.deliverblind,
            custbranch.estdeliverydays
            FROM custbranch
            WHERE custbranch.branchcode='" . $_SESSION['Items']->Branch . "'
            AND custbranch.debtorno = '" . $_SESSION['Items']->DebtorNo . "'";

        if (isset($_POST['Select'])) {
            $ErrMsg = _('The customer branch record of the customer selected') . ': ' . $_POST['Select'] . ' ' . _('cannot be retrieved because');
        } else {
            $ErrMsg = '';
        }
        $DbgMsg = _('SQL used to retrieve the branch details was');
        $result =DB_query($sql,$db,$ErrMsg, $DbgMsg);

        $myrow = DB_fetch_row($result);
        $_SESSION['Items']->DeliverTo = $myrow[0];
        $_SESSION['Items']->DelAdd1 = $myrow[1];
        $_SESSION['Items']->DelAdd2 = $myrow[2];
        $_SESSION['Items']->DelAdd3 = $myrow[3];
        $_SESSION['Items']->DelAdd4 = $myrow[4];
        $_SESSION['Items']->DelAdd5 = $myrow[5];
        $_SESSION['Items']->DelAdd6 = $myrow[6];
        $_SESSION['Items']->DelAdd7 = $myrow[7];
        $_SESSION['Items']->DelAdd8 = $myrow[8];
        $_SESSION['Items']->DelAdd9 = $myrow[9];
        $_SESSION['Items']->DelAdd10 = $myrow[10];
        $_SESSION['Items']->PhoneNo = $myrow[11];
        $_SESSION['Items']->Email = $myrow[12];
        $_SESSION['Items']->Location = $myrow[13];
        $_SESSION['Items']->ShipVia = $myrow[14];
        $_SESSION['Items']->DeliverBlind = $myrow[15];
        $_SESSION['Items']->SpecialInstructions = $myrow[16];
        $_SESSION['Items']->DeliveryDays = $myrow[17];

        $locsql = "SELECT locationname ".
        " FROM locations ".
        " WHERE loccode='" . $_SESSION['Items']->Location ."'";
        $locresult = db_query($locsql, $db);
        $locrow = db_fetch_array($locresult);
        $location = $locrow[0];

    } else {
        prnMsg(_('Sorry, your account has been put on hold for some reason, please contact the credit control personnel.'),'warn');
        include('includes/footer.inc');
        exit;
    }
}

if ($_SESSION['RequireCustomerSelection'] ==1
    OR !isset($_SESSION['Items']->DebtorNo)
    OR $_SESSION['Items']->DebtorNo=='') {
    ?>

    <BR><BR><FONT SIZE=3><B><?php echo _('Customer Selection') . "</b> " . _('Search for the Customer.') ; ?></B></FONT>

    <FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' .SID; ?>" METHOD=POST>
    <B><?php echo '<BR>' . $msg; ?></B>
    <TABLE CELLPADDING="3" COLSPAN="4">
    <TR>
    <TD>
        <FONT SIZE="1"><?php echo _('Part of the Customer Name'); ?>:</FONT><br />
        <INPUT TABINDEX="1" TYPE="Text" NAME="CustKeywords" SIZE="20"    MAXLENGTH="25">
    </TD>

    <TD><FONT SIZE="3"><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <FONT SIZE="1"><?php echo _('Part of the Customer Code'); ?>:</FONT><br />
        <INPUT TABINDEX="2" TYPE="Text" NAME="CustCode" style="width: 150px;" >
    </TD>

    <TD><FONT SIZE="3"><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <FONT SIZE="1"><?php echo _('Part of the phone'); ?>:</FONT><br />
        <INPUT TYPE="Text" NAME="CustPhone" SIZE="15" MAXLENGTH="18">
    </TD>

    <?php// bowikaxu realhost - june 07 ?>

    <TD><FONT SIZE="3"><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <FONT SIZE="1"><?php echo _('Folio Afiliado'); ?>:</FONT><br />
        <INPUT TYPE="Text" NAME="AfilFolio" style="width: 150px;" >
    </TD>

    <TD><FONT SIZE="3"><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <FONT SIZE="1"><?php echo _('Part of the').' '._('Tax Authority Reference'); ?>:</FONT><br />
        <INPUT TYPE="Text" NAME="CustRFC" SIZE="15" MAXLENGTH="18">
    </TD>

    </TR>
    </TABLE>
    <CENTER>
        <INPUT TYPE="SUBMIT" class="btn btn-small" NAME="SearchCust" VALUE="<?php echo _('Search Now'); ?>">
        <INPUT TYPE="SUBMIT" class="btn btn-small" ACTION="RESET" VALUE="<?php echo _('Reset'); ?>">

    </CENTER>

    <script language='JavaScript' type='text/javascript'>
        //<![CDATA[
            <!--
            document.forms[1].CustCode.select();
            document.forms[1].CustCode.focus();
            //-->
        //]]>
    </script>
    <?php



    If (isset($result_CustSelect)) {

        echo '<TABLE class="table"';

        $TableHeader = '<thead>
                        <TR>
                            <TH>' . _('Folio-Codigo') . '</TH>
                            <TH>' . _('Branch') . '</TH>
                            <TH>' . _('Contact') . '</TH>
                            <TH>' . _('Phone') . '</TH>
                            <TH>' . _('Fax') . '</TH>
                            <TH>' . _('TaxRef') . '</TH>
                        </TR>
                        </thead>
                        <tbody>';
        echo $TableHeader;

        $j = 1;
        $k = 0; //row counter to determine background colour

        while ($myrow=DB_fetch_array($result_CustSelect)) {
            $GetFolioAfil ="SELECT folio FROM rh_titular WHERE debtorno = '" . $myrow['debtorno'] . "'";
            $_2GetFolioAfil = DB_query($GetFolioAfil, $db);
            if(DB_num_rows($_2GetFolioAfil)>0)
            	$_GetFolioAfil = DB_fetch_assoc($_2GetFolioAfil);
            else
            	$_GetFolioAfil=array('folio'=>$myrow['debtorno']);
            if ($k==1){
                echo '<tr class="EvenTableRows">';
                $k=0;
            } else {
                echo '<tr class="OddTableRows">';
                $k=1;
            }

            printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s - %s'</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                </tr>",
                $_GetFolioAfil['folio'],
                $myrow['branchcode'],
                $myrow['brname'],
                $myrow['contactname'],
                $myrow['phoneno'],
                $myrow['faxno'],
                $myrow['taxref']);

            $j++;
            If ($j == 11){
                $j=1;
                echo $TableHeader;
            }
        }

        echo '</tbody></TABLE>';

    }//end if results to show

//end if RequireCustomerSelection
} else { //dont require customer selection
// everything below here only do if a customer is selected
    if (isset($_POST['CancelOrder'])) {
        $OK_to_delete=1;    //assume this in the first instance

        if($_SESSION['ExistingOrder']!=0) { //need to check that not already dispatched

            $sql = "SELECT qtyinvoiced
                FROM salesorderdetails
                WHERE orderno=" . $_SESSION['ExistingOrder'] . "
                AND qtyinvoiced>0";

            $InvQties = DB_query($sql,$db);

            if (DB_num_rows($InvQties)>0){

                $OK_to_delete=0;

                prnMsg( _('There are lines on this order that have already been invoiced. Please delete only the lines on the order that are no longer required') . '<P>' . _('There is an option on confirming a dispatch/invoice to automatically cancel any balance on the order at the time of invoicing if you know the customer will not want the back order'),'warn');
            }
        }

        if ($OK_to_delete==1){
            if($_SESSION['ExistingOrder']!=0){

                // bowikaxu - may 2007 - realhost - dont delete the order
                $SQL = 'DELETE FROM salesorderdetails WHERE salesorderdetails.orderno =' . $_SESSION['ExistingOrder'];
                $ErrMsg =_('The order detail lines could not be deleted because');
                //$DelResult=DB_query($SQL,$db,$ErrMsg);

                $SQL = 'DELETE FROM salesorders WHERE salesorders.orderno=' . $_SESSION['ExistingOrder'];
                $ErrMsg = _('The order header could not be deleted because');
                //$DelResult=DB_query($SQL,$db,$ErrMsg);

                $SQL = 'UPDATE salesorders SET rh_status=1 WHERE salesorders.orderno =' . $_SESSION['ExistingOrder'];
                $ErrMsg =_('The order detail lines could not be deleted because');
                $DelResult=DB_query($SQL,$db,$ErrMsg);

                $_SESSION['ExistingOrder']=0;
            }

            unset($_SESSION['Items']->LineItems);
            $_SESSION['Items']->ItemsOrdered=0;
            unset($_SESSION['Items']);
            $_SESSION['Items'] = new cart;

            if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
                $_SESSION['RequireCustomerSelection'] = 1;
            } else {
                $_SESSION['RequireCustomerSelection'] = 0;
            }
            echo '<BR><BR>';
            prnMsg(_('This sales order has been cancelled as requested'),'success');
            include('includes/footer.inc');
            exit;
        }
    } else { /*Not cancelling the order */

                //iJPe 2010-04-16 Modificacion pora mostrar leyenda acerca de la calificacion del cliente

                switch ($_SESSION['Items']->Holdreason)
                {
                     case 0:
                        $Msg='Excelente Historial';
                        $Class = 'excelente';
                        $colorHR = '#088A08';
                        break;

                     case 1:
                        $Msg='Buen Historial';
                        $Class = 'buen';
                        $colorHR = '#088A08';
                        break;

                     case 20:
                        $Msg='Precauci&oacute;n';
                        $Class = 'precaucion';
                        $colorHR = '#F4FA58';
                        break;

                     case 51:
                        $Msg='No Vender M&aacute;s';
                        $Class = 'noVender';
                        $colorHR = '#FA5858';
                        break;
                }

                echo '<DIV class="'.$Class.'"><B>' .$Msg . '</B></DIV>';

        echo '<BR><BR><CENTER><FONT SIZE=4><B>';

        if ($_SESSION['Items']->Quotation==1){
            echo _('Cotización para ') . ' ';
        } else {
            echo _('Pedido para ') . ' ';
        }

        /*Obtengo Datos del Afiliado*/
        $_2GetAfilData = "SELECT ti.folio, cobranza.cobrador, fa.tipo_membresia, ti.movimientos_afiliacion
                               FROM rh_titular ti
                               LEFT JOIN rh_cobranza cobranza ON cobranza.folio = ti.folio
                               LEFT JOIN rh_foliosasignados fa ON ti.folio = fa.folio
                               WHERE ti.debtorno = '{$_SESSION['Items']->DebtorNo}'";
        $_GetAfilData=DB_query($_2GetAfilData,$db);
        $GetAfilData = DB_fetch_assoc($_GetAfilData);


        echo  $GetAfilData['tipo_membresia'] . ' - ' . $GetAfilData['folio'] . ' - ' . $_SESSION['Items']->DebtorNo;
        echo ' - ' . $_SESSION['Items']->CustomerName;
        echo '</B><BR>' . _('Deliver To') . ': ' . $_SESSION['Items']->DeliverTo;
        echo '&nbsp;&nbsp;' . _('From Location') . ': ' . $location;
        echo '<BR>' . _('Sales Type') . '/' . _('Price List') . ': ' . $_SESSION['Items']->SalesTypeName;
        // bowikaxu realhost january 2008 - show the debtor currency
        echo '<BR>' . _('Currency') . ' ' . $_SESSION['Items']->DefaultCurrency;
        echo '</B></FONT>';

        if(($GetAfilData['movimientos_afiliacion'] != 'Activo') && $GetAfilData['tipo_membresia'] == 'Socio'){

        echo "
            <div style='width:50%;'>
                    <div class='alert alert-danger'>
                        <button data-dismiss='alert' class='close' type='button'>×</button>
                        <h4>Alerta! Socio {$GetAfilData['movimientos_afiliacion']}</h4>
                        No puede generar pedidos a Socios/Clientes en status {$GetAfilData['movimientos_afiliacion']}.
                    </div>
            </div>
            <div style='height:50px;'></div>";
            include('includes/footer.inc');
            exit;
        }

        echo '</CENTER>';

    }


///////HERE
    If (isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Prev'])){

        If (isset($_POST['Keywords']) AND isset($_POST['StockCode'])) {
            $msg='<BR>' . _('Stock description keywords have been used in preference to the Stock code extract entered') . '.';
        }

        If (isset($_POST['Keywords']) AND strlen($_POST['Keywords'])>0) {
            //insert wildcard characters in spaces
            $_POST['Keywords'] = strtoupper($_POST['Keywords']);

            $i=0;
            $SearchString = '%';
            while (strpos($_POST['Keywords'], ' ', $i)) {
                $wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
                $SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
                $i=strpos($_POST['Keywords'],' ',$i) +1;
            }
            $SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

            if ($_POST['StockCat']=='All'){
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster,
                        stockcategory
                    WHERE stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.description " . LIKE . " '$SearchString'
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
            } else {
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster, stockcategory
                    WHERE  stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.description " . LIKE . " '" . $SearchString . "'
                    AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
            }

        } elseif (strlen($_POST['StockCode'])>0){

            $_POST['StockCode'] = strtoupper($_POST['StockCode']);
            $SearchString = '%' . $_POST['StockCode'] . '%';

            if ($_POST['StockCat']=='All'){
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster, stockcategory
                    WHERE stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
            } else {
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster, stockcategory
                    WHERE stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
                    AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
            }

        } else {
            if ($_POST['StockCat']=='All'){
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster, stockcategory
                    WHERE  stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
            } else {
                $SQL = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units
                    FROM stockmaster, stockcategory
                    WHERE stockmaster.categoryid=stockcategory.categoryid
                    AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D')
                    AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                    AND stockmaster.discontinued = 0
                    ORDER BY stockmaster.stockid";
              }
        }

        if (isset($_POST['Next'])) {
            $Offset = $_POST['nextlist'];
        }
        if (isset($_POST['Prev'])) {
            $Offset = $_POST['previous'];
        }
        if (!isset($Offset) or $Offset<0) {
            $Offset=0;
        }
        $SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'].' OFFSET '.number_format($_SESSION['DisplayRecordsMax']*$Offset);

        $ErrMsg = _('There is a problem selecting the part records to display because');
        $DbgMsg = _('The SQL used to get the part selection was');
        $SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

        if (DB_num_rows($SearchResult)==0 ){
            prnMsg (_('There are no products available meeting the criteria specified'),'info');

            if ($debug==1){
                prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
            }
        }
        if (DB_num_rows($SearchResult)==1){
            $myrow=DB_fetch_array($SearchResult);
            $NewItem = $myrow['stockid'];
            DB_data_seek($SearchResult,0);
        }
        if (DB_num_rows($SearchResult)<$_SESSION['DisplayRecordsMax']){
            $Offset=0;
        }

    } //end of if search

#Always do the stuff below if not looking for a customerid

    echo '<FORM NAME = "form" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';

    /*Process Quick Entry */

     If (isset($_POST['order_items']) or isset($_POST['QuickEntry']) or isset($_POST['Recalculate'])){ // if enter is pressed on the quick entry screen, the default button may be Recalculate
         /* get the item details from the database and hold them in the cart object */


         //SAINTS variables de sesión para descuentos 01/02/2011
         $i=0;
         foreach ($_SESSION['Items']->LineItems as $OrderLine) {
                $desc_gral = $_POST['desc_gral_' . $OrderLine->LineNumber];
                $desc_copago = $_POST['desc_copago_' . $OrderLine->LineNumber];
                $OrderLine->Discount1=$desc_gral;
                $OrderLine->Discount2=$desc_copago;
                $i=$i+1;
                }
        //SAINTS fin
        /*Discount can only be set later on  -- after quick entry -- so default discount to 0 in the first place */
         $Discount = 0;

         $i=1;
          while ($i<$_SESSION['QuickEntries'] and $_POST['part_' . $i]!='') {
            $QuickEntryCode = 'part_' . $i;
            $QuickEntryQty = 'qty_' . $i;
            $QuickEntryPOLine = 'poline_' . $i;
            $QuickEntryItemDue = 'itemdue_' . $i;

            $i++;

            if (isset($_POST[$QuickEntryCode])) {
                $NewItem = strtoupper($_POST[$QuickEntryCode]);
            }
            if (isset($_POST[$QuickEntryQty])) {
                $NewItemQty = $_POST[$QuickEntryQty];
            }
            if (isset($_POST[$QuickEntryCode])) {
                $NewItemDue = $_POST[$QuickEntryItemDue];
            } else {
                $NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
            }
            if (isset($_POST[$QuickEntryPOLine])) {
                $NewPOLine = $_POST[$QuickEntryPOLine];
            } else {
                $NewPOLine = 0;
            }

            if (!isset($NewItem)){
                unset($NewItem);
                break;    /* break out of the loop if nothing in the quick entry fields*/
            }

            if(!Is_Date($NewItemDue)) {
                    prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $NewItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
                //Attempt to default the due date to something sensible?
                $NewItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
            }
            /*Now figure out if the item is a kit set - the field MBFlag='K'*/
            $sql = "SELECT stockmaster.mbflag
                    FROM stockmaster
                    WHERE stockmaster.stockid='". $NewItem ."'";

            $ErrMsg = _('Could not determine if the part being ordered was a kitset or not because');
            $KitResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);

            // bowikaxu realhost - bug fix March 2008
            if (DB_num_rows($KitResult)==0 AND strlen($NewItem)>0){
                prnMsg( _('The item code') . ' ' . $NewItem . ' ' . _('could not be retrieved from the database and has not been added to the order'),'warn');
            } elseif ($myrow=DB_fetch_array($KitResult)){
                if ($myrow['mbflag']=='K'){ /*It is a kit set item */
                    $sql = "SELECT bom.component,
                            bom.quantity
                            FROM bom
                            WHERE bom.parent='" . $NewItem . "'
                            AND bom.effectiveto > '" . Date("Y-m-d") . "'
                            AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

                    $ErrMsg =  _('Could not retrieve kitset components from the database because') . ' ';
                    $KitResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

                    $ParentQty = $NewItemQty;
                    while ($KitParts = DB_fetch_array($KitResult,$db)){
                        $NewItem = $KitParts['component'];
                        $NewItemQty = $KitParts['quantity'] * $ParentQty;
                        include('includes/SelectOrderItems_IntoCart.inc');
                    }

                } else { /*Its not a kit set item*/
                    include('includes/SelectOrderItems_IntoCart.inc');
                }
            }
         }
         unset($NewItem);
     } /* end of if quick entry */


     /*Now do non-quick entry delete/edits/adds */

    If ((isset($_SESSION['Items'])) OR isset($NewItem)){

        If(isset($_GET['Delete'])){
            //page called attempting to delete a line - GET['Delete'] = the line number to delete
            if($_SESSION['Items']->Some_Already_Delivered($_GET['Delete'])==0){
                $_SESSION['Items']->remove_from_cart($_GET['Delete'], 'Yes');  /*Do update DB */
            } else {
                prnMsg( _('This item cannot be deleted because some of it has already been invoiced'),'warn');
            }
        }

        foreach ($_SESSION['Items']->LineItems as $OrderLine) {

            if (isset($_POST['Quantity_' . $OrderLine->LineNumber])){
                    $sql ="select factor from rh_sales_factors,stockmaster where rh_sales_factors.id=stockmaster.rh_sales_factor and stockmaster.stockid='".$OrderLine->StockID."'";
                    $result = DB_query($sql, $db);
                    if (DB_num_rows($result)>0&& ($_SESSION['Items']->DefaultCurrency=='MXN')){
                        $factor=DB_fetch_row($result);
                    }else{
                        $factor[0]=1;
                       if(DB_num_rows($result)==0) {
                            prnMsg(_('El articulo ').$OrderLine->StockID._(' no se le encontro factor, verifique'),'warn');
                        }
                    }

                $Quantity = $_POST['Quantity_' . $OrderLine->LineNumber];
                $Price = $_POST['Price_' . $OrderLine->LineNumber];
                $D1 = $_POST['desc_gral_' . $OrderLine->LineNumber];
                $D2 = $_POST['desc_copago_' . $OrderLine->LineNumber];
                $DiscountPercentage = $_POST['Discount_' . $OrderLine->LineNumber];
                if ($_SESSION['AllowOrderLineItemNarrative'] == 1) {
                    $Narrative = $_POST['Narrative_' . $OrderLine->LineNumber];
                } else {
                    $Narrative = '';
                }
                $ItemDue = $_POST['ItemDue_' . $OrderLine->LineNumber];
                $POLine = $_POST['POLine_' . $OrderLine->LineNumber];

                if (!isset($OrderLine->Disc)) {
                    $OrderLine->Disc = 0;
                }

                if(!Is_Date($ItemDue)) {
                    prnMsg(_('An invalid date entry was made for ') . ' ' . $NewItem . ' ' . _('The date entry') . ' ' . $ItemDue . ' ' . ('must be in the format') . ' ' . $_SESSION['DefaultDateFormat'],'warn');
                    //Attempt to default the due date to something sensible?
                    $ItemDue = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
                }
                If ($Quantity<0 OR ($Price <0 && $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->MBflag!='D') OR $DiscountPercentage >100 OR $DiscountPercentage <0){
                    prnMsg(_('The item could not be updated because you are attempting to set the quantity ordered to less than 0 or the price less than 0 or the discount more than 100% or less than 0%'),'warn');
                    
                } elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->Price != $Price) {

                    prnMsg(_('The item you attempting to modify the price for has already had some quantity invoiced at the old price the items unit price cannot be modified retrospectively'),'warn');

                } elseif($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)!=0 AND $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->DiscountPercent != ($DiscountPercentage/100)) {

                    prnMsg(_('The item you attempting to modify has had some quantity invoiced at the old discount percent the items discount cannot be modified retrospectively'),'warn');

                } elseif ($_SESSION['Items']->LineItems[$OrderLine->LineNumber]->QtyInv > $Quantity){
                    prnMsg( _('You are attempting to make the quantity ordered a quantity less than has already been invoiced') . '. ' . _('The quantity delivered and invoiced cannot be modified retrospectively'),'warn');
                } elseif ($OrderLine->Quantity !=$Quantity OR $OrderLine->Price != $Price OR ABS($OrderLine->Disc -$DiscountPercentage/100) >0.001 OR $OrderLine->Narrative != $Narrative OR $OrderLine->ItemDue != $ItemDue OR $OrderLine->POLine != $POLine) {
                    $_SESSION['Items']->update_cart_item($OrderLine->LineNumber,
                                        $Quantity,
                                        $Price,
                                        ($DiscountPercentage/100),
                                        $Narrative,
                                        'Yes', /*Update DB */
                                        $ItemDue, /*added line 8/23/2007 by Morris Kelly to get line item due date*/
                                        $POLine,
                                        $factor[0],
                                        $D1,
                                        $D2);
                }
            } //page not called from itself - POST variables not set
        }
    }

    if (isset($_POST['DeliveryDetails'])){
        /*
            Sept  2006 RealHost
            bowikaxu - if an items order price is less than the material cost is possible just in some cases

        */
            if(isset($_POST['PriceLess'])){
                echo "<INPUT TYPE=HIDDEN NAME='PriceLess' VALUE=1>";
            }else{
                foreach($_SESSION['Items']->LineItems as $OrderLine){
                //echo "Cost-> ".$OrderLine->StandardCost." Price-> ".$OrderLine->Price;
                /***************************************************************************/
				$FactorDescuento=1;
				if($OrderLine->MBflag=='D'&&$OrderLine->Price<0)
					$FactorDescuento=-1;
                /*
                 * iJPe
                 * realhost
                 * 2009-12-08
                 *
                 * Modificacion realizada debido a que como el cliente manejaba moneda diferente a
                 * la establecida para la compañia en ocasiones mostraba que el precio era mas bajo que
                 * el costo cuando no es asi por la conversion de monedas.
                 */
                if ($_SESSION['Items']->DefaultCurrency != $_SESSION['CompanyRecord']['currencydefault'])
                {
                    //print_r($CurrencyRates = GetECBCurrencyRates());
                    $CurrencyRates = GetECBCurrencyRates();
                    $RH_Price = $OrderLine->Price* (1-$OrderLine->DiscountPercent) * ($CurrencyRates[$_SESSION['CompanyRecord']['currencydefault']]/$CurrencyRates[$_SESSION['Items']->DefaultCurrency])*$FactorDescuento;
                    //echo $priceItem;
                }else
                {
                    $RH_Price = $OrderLine->Price * (1-$OrderLine->DiscountPercent)*$FactorDescuento;
                }

            if(($RH_Price*$OrderLine->Factor) < $OrderLine->StandardCost && $OrderLine->rh_Sample==0){ // el precio es menor
/***************************************************************************/
                                            if($PuedeCambiarPrecio && $PriceLessThanOrder==1){

                                            echo "<H1><STRONG>"._('El precio del articulo ').$OrderLine->StockID._(' es menor que su costo');
                                            echo "<BR>"._('usted tiene autorizacion para hacer la operacion, Realmente desea hacerlo?');
                                            echo "<BR>"._('si no, seleccione no y modifique el precio')."</STRONG></H1>";
                                            //echo '<A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&PriceLess=' ."1". '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">' . _('Set Price Less than Material Cost?') . '</A>';
                                            echo "<FORM NAME='authform' ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
                                            echo "<BR><CENTER>
                                            <TEXTAREA NAME='rh_comments' cols=40 rows=5 onkeyup='validate(this)'></TEXTAREA><BR>
                                            <INPUT TYPE=SUBMIT NAME='DeliveryDetails' VALUE='" . _('SI') . "' disabled=true>";
                                            echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('NO')."'>";
                                            echo "<INPUT TYPE=HIDDEN NAME='PriceLess' VALUE=1>";
                                            echo "</CENTER></FORM>";
                                            include('includes/footer.inc');
                                            exit;

                                            }elseif($PuedeCambiarPrecio && $PriceLessThanOrder==0){

                                                    echo "<H1><STRONG>"._('El precio del articulo ').$OrderLine->StockID._(' es menor que su costo');
                                                    echo "<BR>"._('usted tiene autorizacion para hacer la operacion, Pero la opcion se encuentra deshabilitada');
                                                    echo "<BR>"._('cheque el archivo de configuracion si desea activarla')."</STRONG></H1>";
                                                    echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
                                                    echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('Ir Atras')."'>";
                                                    echo "</CENTER></FORM>";
                                                    include('includes/footer.inc');
                                                    exit;

                                            }else{

                                                    echo "<H1><STRONG>"._('El precio del articulo ').$OrderLine->StockID._(' es menor que su costo');
                                                    echo "<BR>"._('usted no tiene permisos para hacer esta operacion');
                                                    echo "<BR>"._('seleccione Ir Atras y modifique el precio')."</STRONG></H1>";
                                                    echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . $SID . "' METHOD=POST>";
                                                    echo "<INPUT TYPE=SUBMIT NAME='PriceLessNo' VALUE= '"._('Ir Atras')."'>";
                                                    echo "</CENTER></FORM>";
                                                    include('includes/footer.inc');
                                                    exit;

                                            }
                                    }
                            }

            }

            session_register('rh_comments');
            $_SESSION['rh_comments']=$_POST['rh_comments'];

            header('location: '.$rootpath . '/DeliveryDetails.php?' . SID );
            echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/DeliveryDetails.php?' . SID . '">';
            prnMsg(_('You should automatically be forwarded to the entry of the delivery details page') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
            '<a href="' . $rootpath . '/DeliveryDetails.php?' . SID . '">' . _('click here') . '</a> ' . _('to continue') . 'info');
            exit;
    }


    If (isset($NewItem)){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
        $sql = "SELECT stockmaster.mbflag
                FROM stockmaster
                WHERE stockmaster.stockid='". $NewItem ."'";

        $ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

        $KitResult = DB_query($sql, $db,$ErrMsg);

        $NewItemQty = 1; /*By Default */
        $Discount = 0; /*By default - can change later or discount category overide */

        if ($myrow=DB_fetch_array($KitResult)){
            if ($myrow['mbflag']=='K'){ /*It is a kit set item */
                $sql = "SELECT bom.component,
                        bom.quantity
                    FROM bom
                    WHERE bom.parent='" . $NewItem . "'
                    AND bom.effectiveto > '" . Date('Y-m-d') . "'
                    AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

                $ErrMsg = _('Could not retrieve kitset components from the database because');
                $KitResult = DB_query($sql,$db,$ErrMsg);

                $ParentQty = $NewItemQty;
                while ($KitParts = DB_fetch_array($KitResult,$db)){
                    $NewItem = $KitParts['component'];
                    $NewItemQty = $KitParts['quantity'] * $ParentQty;
                    include('includes/SelectOrderItems_IntoCart.inc');
                }

            } else { /*Its not a kit set item*/

                 include('includes/SelectOrderItems_IntoCart.inc');
            }

        } /* end of if its a new item */

    } /*end of if its a new item */

    If (isset($NewItem_array) && isset($_POST['order_items'])){
/* get the item details from the database and hold them in the cart object make the quantity 1 by default then add it to the cart */
/*Now figure out if the item is a kit set - the field MBFlag='K'*/
        foreach($NewItem_array as $NewItem => $NewItemQty)
        {
                if($NewItemQty > 0)
                {
                    $sql = "SELECT stockmaster.mbflag
                            FROM stockmaster
                            WHERE stockmaster.stockid='". $NewItem ."'";

                    $ErrMsg =  _('Could not determine if the part being ordered was a kitset or not because');

                    $KitResult = DB_query($sql, $db,$ErrMsg);

                    //$NewItemQty = 1; /*By Default */
                    $Discount = 0; /*By default - can change later or discount category overide */

                    if ($myrow=DB_fetch_array($KitResult)){
                        if ($myrow['mbflag']=='K'){ /*It is a kit set item */
                            $sql = "SELECT bom.component,
                                bom.quantity
                                FROM bom
                                WHERE bom.parent='" . $NewItem . "'
                                AND bom.effectiveto > '" . Date('Y-m-d') . "'
                                AND bom.effectiveafter < '" . Date('Y-m-d') . "'";

                            $ErrMsg = _('Could not retrieve kitset components from the database because');
                            $KitResult = DB_query($sql,$db,$ErrMsg);

                            $ParentQty = $NewItemQty;
                            while ($KitParts = DB_fetch_array($KitResult,$db)){
                                $NewItem = $KitParts['component'];
                                $NewItemQty = $KitParts['quantity'] * $ParentQty;
                                include('includes/SelectOrderItems_IntoCart.inc');
                            }

                        } else { /*Its not a kit set item*/

                        include('includes/SelectOrderItems_IntoCart.inc');
                        }

                    } /* end of if its a new item */

                } /*end of if its a new item */

        }

    }


    /* Run through each line of the order and work out the appropriate discount from the discount matrix */
    $DiscCatsDone = array();
    $counter =0;
    foreach ($_SESSION['Items']->LineItems as $OrderLine) {

        if ($OrderLine->DiscCat !="" AND ! in_array($OrderLine->DiscCat,$DiscCatsDone)){
            $DiscCatsDone[$counter]=$OrderLine->DiscCat;
            $QuantityOfDiscCat =0;

            foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
                /* add up total quantity of all lines of this DiscCat */
                if ($StkItems_2->DiscCat==$OrderLine->DiscCat){
                    $QuantityOfDiscCat += $StkItems_2->Quantity;
                }
            }
            $result = DB_query("SELECT MAX(discountrate) AS discount
                        FROM discountmatrix
                        WHERE salestype='" .  $_SESSION['Items']->DefaultSalesType . "'
                        AND discountcategory ='" . $OrderLine->DiscCat . "'
                        AND quantitybreak <" . $QuantityOfDiscCat,$db);
            $myrow = DB_fetch_row($result);
            if ($myrow[0]!=0){ /* need to update the lines affected */
                foreach ($_SESSION['Items']->LineItems as $StkItems_2) {
                    /* add up total quantity of all lines of this DiscCat */
                    if ($StkItems_2->DiscCat==$OrderLine->DiscCat AND $StkItems_2->DiscountPercent == 0){
                        $_SESSION['Items']->LineItems[$StkItems_2->LineNumber]->DiscountPercent = $myrow[0];
                    }
                }
            }
        }
    } /* end of discount matrix lookup code */

    if (count($_SESSION['Items']->LineItems)>0){ /*only show order lines if there are any */

/* This is where the order as selected should be displayed  reflecting any deletions or insertions*/
        ?>
        <script language="javascript">
        function link_popup(enlace) {
              features='width=400, height=400,status=0, menubar=0,toolbar=0, scrollbars=0';
              window.open(enlace.getAttribute('href'), '', features);

        }
        </script>
        <?php
        $Almacenes='';
        /*
         * Thermopartes no tiene esta modificacion, si se requiere falta revisar
        $BusquedaAlmacen=array();
        foreach($_SESSION["rh_permitionlocation"] as $id=>$Almacen){
            $BusquedaAlmacen[]=$id;
            $Almacenes.="<td class=\"tableheader\" colspan=2 >{$Almacen}</td>";
            $ExistenciaAlmacen[$id]=0;
        }
        $BusquedaAlmacen="'".implode("', '",$BusquedaAlmacen)."'";
         */
        if(isset($_SESSION['StorageBins'])&&$_SESSION['StorageBins']==1)
            if($Almacenes==''){
                $Almacenes.="<td class=\"tableheader\">"._('Storage Bins')."</td>";
        }

        echo '<CENTER>
            <TABLE class="table table-bordered table-hover">
            <tr BGCOLOR=#800000><thead>';
        if($_SESSION['Items']->DefaultPOLine == 1){
            echo '<TD class="tableheader">' . _('PO Line') . '</TD>';
        }
        echo '<TD class="tableheader">' . _('Item Code') . '</TD>
            <TD class="tableheader" style="width:1%">' . _('Image') . '</TD>
            <TD class="tableheader">' . _('Item Description') . '</TD>
            <TD class="tableheader">' . _('Quantity') . '</TD>
            <TD class="tableheader">' . _('QOH') . '</TD>'.$Almacenes.'
            <TD class="tableheader">' . _('Unit') . '</TD>';
        echo '<TD class="tableheader"';
        if($_SESSION['AllowShowPriceList']==1)
            echo ' colspan=2 ';
        echo '>' . _('Price') . '</TD>';
            //<TD class="tableheader">' . _('Discount') . '</TD>
        if(!$OcultarDescuentoPedido){
            echo '<TD class="tableheader">' . _('Descuento %') . '</TD>
            <TD class="tableheader">' . _('Descuento %') . '</TD>';
        }
            echo '<TD class="tableheader">' . _('Total') . '</TD>
            <TD class="tableheader">' . _('Due Date') . '</TD>
            <TD class="tableheader" colspan=2 >&nbsp;</TD>
            </tr></thead><tbody>';

        $_SESSION['Items']->total = 0;
        $_SESSION['Items']->totalVolume = 0;
        $_SESSION['Items']->totalWeight = 0;
        $k =0;  //row colour counter
        $conta=0;
        foreach ($_SESSION['Items']->LineItems as $contador => $OrderLine) {
            $ExistenciaAlmacen=array(''=>$OrderLine->QOHatLoc);
            $Almacenes='';
            /*
             * Thermopartes no tiene esta modificacion, si se requiere falta revisar
             $ExistenciaAlmacen=array();
              $qohsql = "SELECT sum(quantity) total, loccode ".
                    "FROM locstock ".
                    "WHERE stockid='" .$OrderLine->StockID . "' AND ".
                    "loccode in({$BusquedaAlmacen}) group by loccode";
            $qohresult =  DB_query($qohsql,$db);
            while($qohrow = DB_fetch_assoc($qohresult)){
                $ExistenciaAlmacen[$qohrow['loccode']]=$qohrow['total'];
            }
             */
            if(isset($_SESSION['StorageBins'])&&$_SESSION['StorageBins']==1){
                foreach($ExistenciaAlmacen as $id=> $totales){
                    //$Almacenes.="<td style=\"text-align:center\">{$totales}</td>";
                    $SBins=getStorageBins($OrderLine->StockID,$id);
                    $Almacenes.="<td style=\"text-align:center\">";
                    if(count($SBins)>0){
                        foreach($SBins as $d){
                            unset($d['id']);
                            unset($d['stockid']);
                            unset($d['active']);
                            unset($d['location']);
                            //$d['description'].=' ';
                            //$d=implode("",$d);
                            $d=$d['description'];
                            $Almacenes.=$d."<br />";
                        }
                    }
                    $Almacenes.="</td>";
                    $ExistenciaAlmacen[$id]=0;
                }
            }


            $LineTotal = $OrderLine->Quantity * $OrderLine->Price *$OrderLine->Factor* (1 - $OrderLine->DiscountPercent);
            $DisplayLineTotal = number_format($LineTotal,2);
            $DisplayDiscount = number_format(($OrderLine->DiscountPercent * 100),2);
            $QtyOrdered = $OrderLine->Quantity;
            $QtyRemain = $QtyOrdered - $OrderLine->QtyInv;

            if ($OrderLine->QOHatLoc < $OrderLine->Quantity AND ($OrderLine->MBflag=='B' OR $OrderLine->MBflag=='M')) {
                /*There is a stock deficiency in the stock location selected */
                $RowStarter = '<tr bgcolor="#EEAABB">';
            } elseif ($k==1){
                $RowStarter = '<tr bgcolor="#CCCCCC">';
                $k=0;
            } else {
                $RowStarter = '<tr bgcolor="#EEEEEE">';
                $k=1;
            }

            echo $RowStarter;
            if($_SESSION['Items']->DefaultPOLine ==1){ //show the input field only if required
                echo '<TD><INPUT TABINDEX=1 TYPE=TEXT NAME="POLine_' . $OrderLine->LineNumber . '" SIZE=20 MAXLENGTH=20 VALUE=' . $OrderLine->POLine . '></TD>';
            } else {
                echo '<input type="hidden" name="POLine_' .  $OrderLine->LineNumber . '" value="">';
            }

            $ImageSource ="";
                if(file_exists($_SERVER['DOCUMENT_ROOT'] . $rootpath. '/' . $_SESSION['part_pics_dir'] . '/' . $OrderLine->StockID . '.jpg')){
                    if (function_exists('imagecreatefrompng') )
                        $ImageSource = '<IMG SRC="GetStockImage.php?SID&automake=1&textcolor=FFFFFF&bgcolor=CCCCCC&StockID=' . urlencode($OrderLine->StockID). '&text=&width=32&height=32">';
                    else
                        $ImageSource = '<IMG SRC="' .$_SERVER['DOCUMENT_ROOT'] . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $OrderLine->StockID . '.jpg">';
                }
                if($ImageSource != ""){
                    $ImageSource="<a href=\""
                    .$rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $OrderLine->StockID . '.jpg'
                    ."\" target=_blank >{$ImageSource}</a>";
                }else
                    $ImageSource = _('No Image');

            echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockStatus.php?' . SID . '&StockID=' . $OrderLine->StockID . '&DebtorNo=' . $_SESSION['Items']->DebtorNo . '">' . $OrderLine->StockID . '</A></TD>';
            echo '<TD>'.$ImageSource.'</TD>';
            echo '<TD><A target="_blank" onclick="link_popup(this); return false;" href="' . $rootpath . '/rh_history_price.php?' . SID . '&stock=' . $OrderLine->StockID . '&debtorno=' . $_SESSION['Items']->DebtorNo . '">x</A>';
            echo "<input type='text' name='ItemDescription[{$OrderLine->LineNumber}]' value= '{$OrderLine->ItemDescription}'>";
            echo '</TD>';

            echo "<TD><INPUT TABINDEX='2' TYPE='TEXT' NAME='Quantity_" . $OrderLine->LineNumber . "' SIZE='6' MAXLENGTH='6' VALUE='" . $OrderLine->Quantity . "' style='width:70px;'  >";
            if ($QtyRemain != $QtyOrdered){
                echo '<br>'.$OrderLine->QtyInv.' of '.$OrderLine->Quantity.' invoiced';
            }
            echo '</TD>
        <TD>' . $OrderLine->QOHatLoc . '</TD>'.$Almacenes.'
        <TD>' . $OrderLine->Units . '</TD>';

                /*
                 * SnowTeam
                 * 12-Sept-2009
                 * realhost
                 */

                if(isset($_POST['Discount_'.$contador])){
                    //SAINTS Descuentos 31/01/2011
                       $descuento=$_POST['Discount_'.$contador];
                       $d_gral=$_POST['desc_gral_'.$contador];
                       $d_copago=$_POST['desc_copago_'.$contador];
                    //SAINTS fin
                       if($descuento < 0 || $descuento > 100){
                           $descuento = $OrderLine->DiscountPercent * 100;
                       }else{
                           $descuento = $descuento;
                           $OrderLine->DiscountPercent = $descuento/100;
                       }
                }else{
                       $descuento = ($OrderLine->DiscountPercent * 100);
                }

            if (

                    in_array(8,$_SESSION['AllowedPageSecurityTokens'])||
                    in_array(2,$_SESSION['AllowedPageSecurityTokens'])
                ){
                /*OK to display with discount if it is an internal user with appropriate permissions */
                    $rh_PriceResult = DB_query(
                    "SELECT ".
                        "salestypes.sales_type as typeabbrev, (prices.price*rh_sales_factors.factor)as price2, ".
                        "(prices.price) as price, currencies.currabrev, currencies.rate ".
                        "FROM prices left join stockmaster on stockmaster.stockid=prices.stockid ".
                                "left join rh_sales_factors on stockmaster.rh_sales_factor=rh_sales_factors.id ".
                                "left join currencies on currencies.currabrev=prices.currabrev ".
                                "left join salestypes on salestypes.typeabbrev=prices.typeabbrev ".
                    "WHERE prices.stockid='".$OrderLine->StockID."'".
                    ($_SESSION['Items']->DefaultCurrency=="USD"||$_SESSION['Items']->DefaultCurrency=="GAS"?
                    " and currencies.currabrev in ('USD','GAS')":"")
                        ,$db);
                    if($_SESSION['AllowShowPriceList']==1){
                        echo '<TD>';
                        echo '<select class ="ListaPrecio" idLn="' . $OrderLine->LineNumber . '">';
                        $Select=false;
                        while ($PriceRow = DB_fetch_row($rh_PriceResult)){
                            if($_SESSION['Items']->DefaultCurrency!="USD"&&
                                $_SESSION['Items']->DefaultCurrency!="GAS"
                                &&($PriceRow[3]=='USD'||$PriceRow[3]=='GAS'))
                                    $PriceRow[2]=$PriceRow[2]*$_SESSION['TCACTUAL'];
                            echo '<option value="'.number_format ($PriceRow[2],4,".","").'"';
                            if(!$Select&&number_format($OrderLine->Price,2,".","")==number_format ($PriceRow[2],2,".","")){ echo " selected=selected ";$Select=true;}
                            echo ">";
                            echo $PriceRow[0]." --&gt; ".number_format ($PriceRow[2],4,".",",");
                            echo '</option>';
                        }
                        echo '</select>';
                        echo'</TD>';
                    }
                if($PuedeCambiarPrecio){
                    echo '<TD><INPUT TYPE=TEXT id="Price_' . $OrderLine->LineNumber . '" style="width:100px;"  NAME="Price_' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . number_format ($OrderLine->Price,4,".","") . '>
                            <INPUT TYPE=hidden id="Price__' . $OrderLine->LineNumber . '" style="width:100px;"  NAME="Price__' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . $OrderLine->Price . '></TD>';
                }else{
                    echo '<TD>
                        <INPUT TYPE=TEXT readonly=true id="Price__' . $OrderLine->LineNumber . '" style="width:100px;" NAME="Price__' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . number_format ($OrderLine->Price,4,".","") . '>
                        <INPUT TYPE=hidden id="Price_' . $OrderLine->LineNumber . '" style="width:100px;" NAME="Price_' . $OrderLine->LineNumber . '" SIZE=16 MAXLENGTH=16 VALUE=' . $OrderLine->Price . '>
                    </TD>';
                }
                echo '<INPUT TYPE=hidden id="Discount_' . $OrderLine->LineNumber . '" NAME="Discount_' . $OrderLine->LineNumber . '"  VALUE="' . $descuento . '" style="width:150px;" >';

            //SAINTS se agregó descuento general y copago, 31/01/2011
            if(!$OcultarDescuentoPedido){
            echo '<TD><INPUT TYPE="text" id="desc_gral_'.$OrderLine->LineNumber.'" NAME="desc_gral_'.$OrderLine->LineNumber.'" SIZE=5 MAXLENGTH=4 VALUE="'./*$_SESSION['desc_gral'][$conta]*/$OrderLine->Discount1.'" onblur="descuentoGral('.$OrderLine->LineNumber.');"  style="width:70px;"  >%</TD>
                  <TD><INPUT TYPE="text" id="desc_copago_' .$OrderLine->LineNumber.'" NAME="desc_copago_' .$OrderLine->LineNumber.'" SIZE=5 MAXLENGTH=4 VALUE="'./*$_SESSION['desc_copago'][$conta]*/$OrderLine->Discount2.'" onblur="descuentoCopago('.$OrderLine->LineNumber.');"  style="width:70px;"  >%</TD>';
            }
            $conta=$conta+1;
            //SAINTS fin

            } else {
                echo '<TD ALIGN=RIGHT>' . $OrderLine->Price . '</TD><TD></TD>';
                echo '<INPUT TYPE=HIDDEN NAME="Price_' . $OrderLine->LineNumber . '" VALUE=' . $OrderLine->Price . '>';
            }
            if ($_SESSION['Items']->Some_Already_Delivered($OrderLine->LineNumber)){
                $RemTxt = _('Clear Remaining');
            } else {
                $RemTxt = _('Delete');
            }
            echo '</TD><TD ALIGN=RIGHT>' . $DisplayLineTotal . '</FONT></TD>';
            $LineDueDate = $OrderLine->ItemDue;
            if (!Is_Date($OrderLine->ItemDue)){
                // bowikaxu realhost BUG BUG BUG - no toma la fecha de la BD si esta en otro formato que Y-m-d
                //$LineDueDate = DateAdd (Date($_SESSION['DefaultDateFormat']),'d', $_SESSION['Items']->DeliveryDays);
                                $LineDueDate = Date($_SESSION['DefaultDateFormat']);
                $_SESSION['Items']->LineItems[$OrderLine->LineNumber]->ItemDue= $LineDueDate;
            }

            echo '<TD><INPUT TYPE=TEXT NAME="ItemDue_' . $OrderLine->LineNumber . '" style="width:100px;" SIZE=10 MAXLENGTH=10 VALUE=' . $LineDueDate . '></TD>';

            // bowikaxu realhost - sale or sample item
            echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=' . $OrderLine->LineNumber . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">' . $RemTxt . '</A></TD>
            <TD>
            <SELECT NAME="rh_sample_'.$OrderLine->LineNumber.'">';

            if($_POST['rh_sample_'.$OrderLine->LineNumber]==1){
                echo "<OPTION SELECTED VALUE=1>"._('Sample');
                echo "<OPTION VALUE=0>"._('Sale');
                $OrderLine->rh_Sample=1;
            }else {
                echo "<OPTION VALUE=1>"._('Sample');
                echo "<OPTION SELECTED VALUE=0>"._('Sale');
                $OrderLine->rh_Sample=0;
            }

            echo '</SELECT>
            </TD>
            </TR>';

            if ($_SESSION['AllowOrderLineItemNarrative'] == 1){
                //echo $RowStarter;
                echo '<TR><TD COLSPAN=15><TEXTAREA  NAME="Narrative_' . $OrderLine->LineNumber . '" style="width:80%;" rows="1" >' . $OrderLine->Narrative . '</TEXTAREA><BR><HR></TD></TR>';
            } else {
                echo '<INPUT TYPE=HIDDEN NAME="Narrative" VALUE="">';
            }

            $_SESSION['Items']->total = $_SESSION['Items']->total + $LineTotal;
            $_SESSION['Items']->totalVolume = $_SESSION['Items']->totalVolume + $OrderLine->Quantity * $OrderLine->Volume;
            $_SESSION['Items']->totalWeight = $_SESSION['Items']->totalWeight + $OrderLine->Quantity * $OrderLine->Weight;

        } /* end of loop around items */

        $DisplayTotal = number_format($_SESSION['Items']->total,2);
        echo '<TR><TD></TD><TD></TD><TD><B>' . _('TOTAL Excl Tax/Freight') . '</B></TD><TD COLSPAN=8 ALIGN=RIGHT>' . $DisplayTotal . '</TD></TR></TABLE>';

        $DisplayVolume = number_format($_SESSION['Items']->totalVolume,2);
        $DisplayWeight = number_format($_SESSION['Items']->totalWeight,2);
        echo '<TABLE BORDER=1><TR><TD>' . _('Total Weight') . ':</TD>
                         <TD>' . $DisplayWeight . '</TD>
                         <TD>' . _('Total Volume') . ':</TD>
                         <TD>' . $DisplayVolume . '</TD>
                       </TR></tbody></TABLE>';


        echo '<BR><INPUT TYPE=SUBMIT NAME="Recalculate" Value="' . _('Re-Calculate') . '">
                <INPUT TYPE=SUBMIT NAME="DeliveryDetails" VALUE="' . _('Enter Delivery Details and Confirm Order') . '"><HR>';
        echo "</center></form>";
        echo '<FORM NAME = "form" ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST><center>';

    } # end of if lines

/* Now show the stock item selection search stuff below */

     if (isset($_POST['PartSearch']) && $_POST['PartSearch']!='' || !isset($_POST['QuickEntry'])){

        echo '<input type="hidden" name="PartSearch" value="' .  _('Yes Please') . '">';

        $SQL="SELECT categoryid,
                categorydescription
            FROM stockcategory
            WHERE stocktype='F' OR stocktype='D'
            ORDER BY categorydescription";
        $result1 = DB_query($SQL,$db);

        echo '<B>' . $msg . '</B><BR><CENTER><b>' . _('Search for Order Items') . '</b><TABLE><TR><TD><FONT SIZE=2>' . _('Select a Stock Category') . ':</FONT><SELECT TABINDEX=1 NAME="StockCat">';

        if (!isset($_POST['StockCat'])){
            echo "<OPTION SELECTED VALUE='All'>" . _('All');
            $_POST['StockCat'] ='All';
        } else {
            echo "<OPTION VALUE='All'>" . _('All');
        }

        while ($myrow1 = DB_fetch_array($result1)) {

            if ($_POST['StockCat']==$myrow1['categoryid']){
                echo '<OPTION SELECTED VALUE=' . $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
            } else {
                echo '<OPTION VALUE='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
            }
        }

        ?>

        </SELECT>
        <TD><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Description'); ?>:</FONT></TD>
        <TD><INPUT TABINDEX=2 TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25 VALUE="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>"></TD></TR>
        <TR><TD></TD>
        <TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Stock Code'); ?>:</FONT></TD>
        <TD><INPUT TABINDEX=3 TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18 VALUE="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>"></TD>
        </TR>
        </TABLE>
        <CENTER><INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
        <INPUT TYPE=SUBMIT Name="QuickEntry" VALUE="<?php echo _('Use Quick Entry'); ?>">

        <script language='JavaScript' type='text/javascript'>

                //document.forms[0].StockCode.select();
                //document.forms[0].StockCode.focus();

                //function open(){

                    //alert("Testeando");
                    //window.open('rh_SelectWO.php',null,"height=480,width=500,status=no,toolbar=no,menubar=no,location=no,dependent=yes");

                //}

        </script>

        <?php
        if (in_array(2,$_SESSION['AllowedPageSecurityTokens'])){
            echo '<INPUT TABINDEX=6 TYPE=SUBMIT Name="ChangeCustomer" VALUE="' . _('Change Customer') . '">';
            echo '<BR><BR><a TABINDEX=7 target="_blank" href="' . $rootpath . '/Stocks.php?' . SID . '"><B>' . _('Add a New Stock Item') . '</B></a>';
        }

        // bowikaxu realhost - 21 july 2008 - agregar work order al pedido
        echo '<BR><a TABINDEX=8 target="_blank" onclick="javascript:open()" href="javascript:open();"><B>' . _('Add').' '._('Work Order') . '</B></a>';
        echo '</CENTER>';

        if (isset($SearchResult)) {

            echo '<CENTER><form name="orderform"><TABLE CELLPADDING=2 COLSPAN=7 >';
            $TableHeader = '<TR><TD class="tableheader">' . _('Code') . '</TD>
                                    <TD class="tableheader">' . _('Description') . '</TD>
                                    <TD class="tableheader">' . _('Units') . '</TD>
                                    <TD class="tableheader">' . _('On Hand') . '</TD>
                                    <TD class="tableheader">' . _('On Demand') . '</TD>
                                    <TD class="tableheader">' . _('On Order') . '</TD>
                                    <TD class="tableheader">' . _('Available') . '</TD>
                                    <TD class="tableheader">' . _('Imagen') . '</TD>
                                    <TD class="tableheader">' . _('Quantity') . '</TD></TR>';
            echo $TableHeader;
            $j = 1;
            $k=0; //row colour counter

            while ($myrow=DB_fetch_array($SearchResult)) {
// This code needs sorting out, but until then :

                $ImageSource ="";
                if(file_exists($_SERVER['DOCUMENT_ROOT'] . $rootpath. '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg')){
                    if (function_exists('imagecreatefrompng') )
                        $ImageSource = '<IMG SRC="GetStockImage.php?SID&automake=1&textcolor=FFFFFF&bgcolor=CCCCCC&StockID=' . urlencode($myrow['stockid']). '&text=&width=64&height=64">';
                    else
                        $ImageSource = '<IMG SRC="' .$_SERVER['DOCUMENT_ROOT'] . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg">';
                }
                if($ImageSource != ""){
                    $ImageSource="<a href=\""
                    .$rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] . '.jpg'
                    ."\" target=_blank >{$ImageSource}</a>";
                }else
                    $ImageSource = _('No Image');
/**/

                // Find the quantity in stock at location
                $qohsql = "SELECT sum(quantity)
                           FROM locstock
                           WHERE stockid='" .$myrow['stockid'] . "' AND
                           loccode = '" . $_SESSION['Items']->Location . "'";
                $qohresult =  DB_query($qohsql,$db);
                $qohrow = DB_fetch_row($qohresult);
                $qoh = $qohrow[0];

                // Find the quantity on outstanding sales orders
                $sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                             FROM salesorderdetails,
                                salesorders
                             WHERE salesorders.orderno = salesorderdetails.orderno AND
                             salesorders.fromstkloc='" . $_SESSION['Items']->Location . "' AND
                            salesorderdetails.completed=0 AND
                            salesorders.quotation=0 AND
                            salesorderdetails.stkcode='" . $myrow['stockid'] . "'";

                $ErrMsg = _('The demand for this product from') . ' ' . $_SESSION['Items']->Location . ' ' .
                     _('cannot be retrieved because');
                $DemandResult = DB_query($sql,$db,$ErrMsg);

                $DemandRow = DB_fetch_row($DemandResult);
                if ($DemandRow[0] != null){
                  $DemandQty =  $DemandRow[0];
                } else {
                  $DemandQty = 0;
                }

                // Find the quantity on purchase orders
                $sql = "SELECT SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS dem
                             FROM purchorderdetails
                             WHERE purchorderdetails.completed=0 AND
                            purchorderdetails.itemcode='" . $myrow['stockid'] . "'";

                $ErrMsg = _('The order details for this product cannot be retrieved because');
                $PurchResult = db_query($sql,$db,$ErrMsg);

                $PurchRow = db_fetch_row($PurchResult);
                if ($PurchRow[0]!=null){
                  $PurchQty =  $PurchRow[0];
                } else {
                  $PurchQty = 0;
                }

                // Find the quantity on works orders
                $sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm
                       FROM woitems
                       WHERE stockid='" . $myrow['stockid'] ."'";
                $ErrMsg = _('The order details for this product cannot be retrieved because');
                $WoResult = db_query($sql,$db,$ErrMsg);

                $WoRow = db_fetch_row($WoResult);
                if ($WoRow[0]!=null){
                  $WoQty =  $WoRow[0];
                } else {
                  $WoQty = 0;
                }

                if ($k==1){
                    echo '<tr class="EvenTableRows">';
                    $k=0;
                } else {
                    echo '<tr class="OddTableRows">';
                    $k=1;
                }
                $OnOrder = $PurchQty + $WoQty;

                $Available = $qoh - $DemandQty + $OnOrder;
/****************************************************************************************************************************/
                echo sprintf('<TD><FONT SIZE=1>%s</FONT></TD>
                    <TD><FONT SIZE=1>%s</FONT></TD>
                    <TD><FONT SIZE=1>%s</FONT></TD>
                    <TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
                    <TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
                    <TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
                    <TD style="text-align:center"><FONT SIZE=1>%s</FONT></TD>
                    <TD>%8$s</TD>
                    <TD><FONT SIZE=1><input tabindex='.number_format($j+7).' type="textbox" size=6 name="val'.$j.'" value=0>
                    <input type="hidden" size=6 name="itm'.$j.'" value="'.$myrow['stockid'].'">
                    </FONT></TD>
                    </TR>',
                    $myrow['stockid'],
                    $myrow['description'],
                    $myrow['units'],
                    $qoh,
                    $DemandQty,
                    $OnOrder,
                    $Available,
                    $ImageSource,
                    $rootpath,
                    SID,
                    $myrow['stockid']);
                $j++;
/****************************************************************************************************************************/
    #end of page full new headings if
            }
    #end of while loop
            echo '<tr><td align=center><input type="hidden" name="previous" value='.number_format($Offset-1).'><input tabindex='.number_format($j+7).' type="submit" name="Prev" value="'._('Prev').'"></td>';
            echo '<td align=center colspan=6><input type="hidden" name="order_items" value=1><input tabindex='.number_format($j+8).' type="submit" value="'._('Order').'"></td>';
            echo '<td align=center><input type="hidden" name="nextlist" value='.number_format($Offset+1).'><input tabindex='.number_format($j+9).' type="submit" name="Next" value="'._('Next').'"></td></tr>';
/****************************************************************************************************************************/
            echo '</TABLE><input type="hidden" name="jjjjj" value='.$j.'>';
/****************************************************************************************************************************/
            echo '</form>';

        }#end if SearchResults to show
    } /*end of PartSearch options to be displayed */
       else { /* show the quick entry form variable */
          /*FORM VARIABLES TO POST TO THE ORDER  WITH PART CODE AND QUANTITY */

            echo '<br><center><font size=4 color=blue><b>' . _('Quick Entry') . '</b></font><br>
                    <table border=1>
                    <tr>';
            /*do not display colum unless customer requires po line number by sales order line*/
            if($_SESSION['Items']->DefaultPOLine ==1){
                echo    '<TD class="tableheader">' . _('PO Line') . '</td>';
            }
            echo '<TD class="tableheader">' . _('Quantity') . '</TD>
                <TD class="tableheader">' . _('Part Code') . '</TD>
                <TD class="tableheader">' . _('Due Date') . '</TD>
            </tr>';
            //$DefaultDeliveryDate = DateAdd(Date($_SESSION['DefaultDateFormat']),'d',$_SESSION['Items']->DeliveryDays);
                        $DefaultDeliveryDate = Date($_SESSION['DefaultDateFormat']);
            for ($i=1;$i<=$_SESSION['QuickEntries'];$i++){

                echo '<tr bgcolor="#CCCCCC">';
                /* Do not display colum unless customer requires po line number by sales order line*/
                if($_SESSION['Items']->DefaultPOLine > 0){
                    echo '<td><input type="text" name="poline_' . $i . '" size=21 maxlength=20></td>';
                }               
                echo '<td><input type="text" name="qty_' . $i . '" size=6 maxlength=6></td>
                <td><input type="text" name="part_' . $i . '" size=21 maxlength=20 onblur="javascript: nombre_producto(this.value,' . $i . ')"></td>
                <td><input type="text" name="itemdue_' . $i . '" size=25 maxlength=25 value="' . $DefaultDeliveryDate . '"></td>
                <TD><INPUT TYPE="text" readonly=true name="prod_' . $i . '" size=60 maxlength=400 id="prod_' . $i . '"></TD>
                </tr>';
            }

            echo '</table><input type="submit" name="QuickEntry" value="' . _('Quick Entry') . '">
                     <input type="submit" name="PartSearch" value="' . _('Search Parts') . '">';

?>
<script language='JavaScript' type='text/javascript'>

        if ("undefined" == typeof(document.forms[0].poline_1) ) {
        document.forms[0].part_1.select();
        document.forms[0].part_1.focus();
        } else{
            document.forms[0].poline_1.select();
        document.forms[0].poline_1.focus();
    }

</script>
<?php

    }
    if ($_SESSION['Items']->ItemsOrdered >=1){
            echo '<CENTER><BR><INPUT TYPE=SUBMIT NAME="CancelOrder" VALUE="' . _('Cancel Whole Order') . '" onclick="return confirm(\'' . _('Are you sure you wish to cancel this entire order?') . '\');"></CENTER>';
    }

}   #end of else not selecting a customer
echo '</FORM>';
include('includes/footer.inc');

//iJPe 2010-04-16 Modificacion pora mostrar leyenda acerca de la calificacion del cliente

echo '<style type="text/css">';
echo 'div.'.$Class.' {
    background-color:'.$colorHR.';
    color: navy;
    border: 1px solid navy;
        font-size: 15px;
        text-align: center;
}';
echo '</style>';
?>
<script language='JavaScript' type='text/javascript' src="javascripts/jquery.js"></script>
<script language='JavaScript' type='text/javascript'>
$(function(){
    $('.ListaPrecio').change(function(){
        id=$(this).attr("idLn");
        $('#Price_'+id+', #Price__'+id).val($(this).val());
    });
});</script>

<!-- Saints función para obtener el porcentaje de descuento 31/01/2011-->
<script language='JavaScript' type='text/javascript'>
  function descuentoGral(id)
    {var desc_gral=document.getElementById('desc_gral_'+id).value;
     var dc=document.getElementById('desc_copago_'+id).value;

        if(desc_gral=="")
            desc_gral=0;

        if(Number(dc)>0)
            descuentoCopago(id);

        else
            document.getElementById('Discount_'+id).value=desc_gral;
    }

//Corrección del descuento Copago 10/02/2011
  function descuentoCopago(id)
    {var dg=document.getElementById('desc_gral_'+id).value;
     var precio=document.getElementById('Price_'+id).value;
     var dc=document.getElementById('desc_copago_'+id).value;

     dg=Number(dg)/100;
     var st=Number(precio)*dg;
     var monto=Number(precio)-Number(st);
     dc=Number(dc)/100;
     dc=Number(monto)*Number(dc);
     var porDC=(dc/Number(precio))*100;
     var desc=(Number(dg)*100)+Number(porDC);

     document.getElementById('Discount_'+id).value=desc;
    }

</script>
