<?php
class OrdersController extends Controller{

    public $layout = 'webroot.themes.found.views.layouts.main';

    public function actionCreatequotation(){

        FB::INFO($_REQUEST,'__________________________REQ');
        $ListaMetodosdePago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaFrecuenciapagos = CHtml::listData(Frecuenciapago::model()->findAll(), 'id', 'frecuencia');


        $this->render('createquotation', array('ListaMetodosdePago'=>$ListaMetodosdePago, 'ListaFrecuenciapagos'=>$ListaFrecuenciapagos));
    }

    public function actionSearch($debtorno=null) {

         global $db;
        if(!empty($_REQUEST['Search']['Items'])){
            $ParseData2 = parse_str($_REQUEST['Search']['Items'], $ParseData);

            $stockids="''";
            foreach ($ParseData as $Items) {
                if(!empty($Items['stockid'])){
                    $stockids.= ", '".$Items['stockid']. "'";
                }
            }
        }
       FB::INFO($stockids, 'ids');

        $DatosCliente = Yii::app()->db->createCommand()
            ->select('rh_crm_prospecto.debtorno, custbranch.branchcode')
            ->from('rh_crm_prospecto')
            ->leftJoin('custbranch', 'rh_crm_prospecto.debtorno=custbranch.debtorno')
            ->where("rh_crm_prospecto.debtorno='".$debtorno."'")
            ->queryAll();


        $Where = " 1=1 ";
        if (!empty($_REQUEST['Search']['descripcion'])) {
            $Where .= " AND locstock.loccode= 'AFIL' ";
            $Where .= " AND (stockmaster.description LIKE '%" . $_REQUEST['Search']['descripcion'] . "%' OR stockmaster.stockid like '%".$_REQUEST['Search']['descripcion']."%')";
        }

        if(!empty($stockids)){
            $Where .= "AND stockmaster.stockid NOT IN (".$stockids.")";
        }
        FB::INFO($Where);

        $search = Yii::app()->db->createCommand()
        ->select('stockmaster.stockid,stockmaster.description,stockmaster.units, locstock.quantity')
        ->from('stockmaster')
        ->leftJoin('locstock', 'locstock.stockid=stockmaster.stockid')
        ->where($Where)
        ->queryAll();

        $DebtorNO = $DatosCliente[0]['debtorno'];
        $BranchCode = $DatosCliente[0]['branchcode'];
        foreach ($search as $Data) {

            if($_REQUEST['Search']['cotizar_plan']==0){
                ob_start();
                $precio = GetPrice ($Data['stockid'], 'GENERAL', 'GENERAL', $db);
                ob_clean();
            }
            $impuesto = $this->getTaxTotalForItem($BranchCode, $DebtorNO, $Data['stockid'], $precio);

                $_Data[] = array(
                    'value' => $Data['stockid'] . "--" . $Data['description'],
                    'unidad'=> $Data['units'],
                    'stockid'=> $Data['stockid'],
                    'quantity'=> $Data['quantity'],
                    'id' => $DatosCliente[0]['debtorno'],
                    'precio'=>$precio,
                    'impuesto'=>$impuesto
                );
            }

        echo CJSON::encode(array('requestresult' => 'ok', 'DataList' => $_Data));
        return;
    }


    public function actionSavequotation($debtorno = null){

        if(!empty($_POST['ProcessData']['Items'])){

        $ParseData2 = parse_str($_POST['ProcessData']['Items'], $ParseData);

        if(!empty($debtorno)){

            $DatosCliente = Yii::app()->db->createCommand()
            ->select('custbranch.*, debtorsmaster.salestype')
            ->from('custbranch')
            ->leftJoin('debtorsmaster', 'custbranch.debtorno=debtorsmaster.debtorno')
            ->where("custbranch.debtorno = :debtorno", array(":debtorno" => $debtorno))
            ->queryAll();

        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'No se ha encontrado información para este cliente...'
                ));
            return;
        }

        if(!empty($DatosCliente)){

            $OrderNo = GetNextTransNo(30, $db);

            $SalesOrder = "INSERT INTO salesorders(
                orderno,
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
                deladd7,
                deladd8,
                deladd9,
                deladd10,
                contactphone,
                contactemail,
                freightcost,
                fromstkloc_virtual,
                deliverydate,
                quotation,
                deliverblind,
                fromstkloc,
                salesman,
                prospecto_id
                )
            VALUES
                (
                :orderno,
                :debtorno,
                :branchcode,
                :customerref,
                :comments,
                :orddate,
                :ordertype,
                :shipvia,
                :deliverto,
                :deladd1,
                :deladd2,
                :deladd3,
                :deladd4,
                :deladd5,
                :deladd6,
                :deladd7,
                :deladd8,
                :deladd9,
                :deladd10,
                :contactphone,
                :contactemail,
                :freightcost,
                :fromstkloc_virtual,
                :deliverydate,
                :quotation,
                :deliverblind,
                :fromstkloc,
                :salesman,
                :prospecto_id
                )";

                $parameters=array(
                ':orderno' => $OrderNo,
                ':debtorno' => $DatosCliente[0]['debtorno'],
                ':branchcode' =>$DatosCliente[0]['branchcode'],
                ':customerref'=> "",
                ':comments'=> "",
                ':orddate'=> Date("Y-m-d H:i"),
                ':ordertype'=> $DatosCliente[0]['salestype'],
                ':shipvia'=> $DatosCliente[0]['defaultshipvia'],
                ':deliverto' =>  $DatosCliente[0]['brname'],
                ':deladd1'=> $DatosCliente[0]['braddress1'],
                ':deladd2'=> $DatosCliente[0]['braddress2'],
                ':deladd3'=> $DatosCliente[0]['braddress3'],
                ':deladd4'=> $DatosCliente[0]['braddress4'],
                ':deladd5'=> $DatosCliente[0]['braddress5'],
                ':deladd6'=> $DatosCliente[0]['braddress6'],
                ':deladd7'=> $DatosCliente[0]['braddress7'],
                ':deladd8'=> $DatosCliente[0]['braddress8'],
                ':deladd9'=> $DatosCliente[0]['braddress9'],
                ':deladd10'=> $DatosCliente[0]['braddress10'],
                ':contactphone'=> $DatosCliente[0]['phoneno'],
                ':contactemail'=> $DatosCliente[0]['email'],
                ':freightcost'=> "",
                ':fromstkloc_virtual'=> 'MTY',
                ':deliverydate'=>Date("Y-m-d H:i"),
                ':quotation'=>'1',
                ':deliverblind'=> "1",
                ':fromstkloc'=> 'MTY',
                ':salesman'=> '2RC',
                ':prospecto_id'=> 0
                );

            if(Yii::app()->db->createCommand($SalesOrder)->execute($parameters)){
                $orderline = 1;
                foreach ($ParseData as $Items) {
                    $precio=str_replace(",", "", $Items['unitprice']);
                    $importe=str_replace(",", "", $Items['importe']);
                    $stockid=explode("--", $Items['product']);

                    $SalesOrderDetails= "insert into salesorderdetails(
                        orderlineno,
                        orderno,
                        stkcode,
                        qtyinvoiced,
                        unitprice,
                        quantity,
                        discountpercent,
                        completed,
                        narrative,
                        description,
                        poline,
                        rh_cost,
                        itemdue
                        )values(
                        :orderlineno,
                        :orderno,
                        :stkcode,
                        :qtyinvoiced,
                        :unitprice,
                        :quantity,
                        :discountpercent,
                        :completed,
                        :narrative,
                        :description,
                        :poline,
                        :rh_cost,
                        :itemdue
                        )";

                    $parameters=array(
                        ':orderlineno'=> $orderline,
                        ':orderno'=> $OrderNo,
                        ':stkcode'=> $stockid[0],
                        ':qtyinvoiced'=> 0,
                        ':unitprice'=>$Items['unitprice'],
                        ':quantity'=> $Items['qty'],
                        ':discountpercent'=>0,
                        ':completed'=>0,
                        ':narrative'=>'',
                        ':description'=>$stockid[1],
                        ':poline'=>$orderline,
                        ':rh_cost'=> "",
                        ':itemdue'=> Date('Y-m-d')
                        );

                    $orderline++;

                    try {
                        Yii::app()->db->createCommand($SalesOrderDetails)->execute($parameters);
                    } catch (Exception $e) {
                        $Errorlog[] = array('Error'=>$e);
                    }

                }

                if(empty($Errorlog)){
                    echo CJSON::encode(array(
                        'requestresult' => 'ok',
                        'message' => 'La cotización se registró correctamente...'
                        ));
                }else{
                    echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => 'Ocurrió un error al registrar cotización. Intente de nuevo...',
                        'error'=>$Errorlog
                        ));
                }
                return;
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'Ocurrió un error al registrar cotización. Intente de nuevo...'
                    ));
            }
            return;
         }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'No se ha encontrado información para este cliente...'
                ));
        }
        return;
        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'Seleccione un producto...'
                ));
        }
        return;

    }

    public function actionView($orderno=null){

        if(!empty($orderno)){
            $OrderDetails=Yii::app()->db->createCommand()
            ->select('salesorderdetails.*, stockmaster.units, salesorders.debtorno, salesorders.branchcode')
            ->from('salesorderdetails')
            ->leftJoin('stockmaster', 'stockmaster.stockid=salesorderdetails.stkcode')
            ->leftJoin('salesorders', 'salesorders.orderno=salesorderdetails.orderno')
            ->where("salesorderdetails.orderno='".$orderno."'")
            ->queryAll();
        }

        $this->render('view', array('OrderDetails'=>$OrderDetails));

    }

    public function actionGettaxes($id=null){

        if(!empty($_POST['Search']['stockid'])){

         $DatosCliente = Yii::app()->db->createCommand()
            ->select('rh_crm_prospecto.debtorno, custbranch.branchcode')
            ->from('rh_crm_prospecto')
            ->leftJoin('custbranch', 'rh_crm_prospecto.debtorno=custbranch.debtorno')
            ->where("idProspecto='".$id."'")
            ->queryAll();

        $DebtorNO = $DatosCliente[0]['debtorno'];
        $BranchCode = $DatosCliente[0]['branchcode'];
        $StockID = $_POST['Search']['stockid'];
        $Price = $_POST['Search']['precio'];
        $Cantidad = $_POST['Search']['qty'];

        $impuesto = $this->getTaxTotalForItem($BranchCode, $DebtorNO, $StockID, $Price, $Cantidad);

        echo CJSON::encode(array(
                'requestresult'=>'ok',
                'impuesto'=>$impuesto
            ));
        }
        return;
    }

    public function actionUpdatequotation($orderno=null){

        if(!empty($orderno)){
            $OrdenLine = Yii::app()->db->createCommand()->select('max(orderlineno) as lineno')->from('salesorderdetails')->where("orderno='".$orderno."'")->queryAll();
            $orderlineno = $OrdenLine[0]['lineno'] + 1;

            $ParseData2 = parse_str($_POST['ProcessData']['Items'], $ParseData);



                foreach ($ParseData as $Producto) {

                    FB::INFO($ParseData, 'data' );

                    $WhereProducto="";
                    $WhereProducto="1=1";

                    if(!empty($Producto['stockid'])){
                        $WhereProducto.=" AND stkcode='".$Producto['stockid']."' ";
                    }

                    if(!empty($orderno)){
                        $WhereProducto.=" AND orderno='".$orderno."' ";
                    }
                         FB::INFO($WhereProducto, 'WhereProducto');
                    $ProductoExistente=Yii::app()->db->createCommand()
                    ->select('stkcode')
                    ->from('salesorderdetails')
                    ->where($WhereProducto)
                    ->queryAll();

                      FB::INFO($ProductoExistente, 'producto existente');


                    if(!empty($ProductoExistente[0]['stkcode'])){
                        $UpdateOrderLine="update salesorderdetails set
                        unitprice=:unitprice,
                        quantity=:qty
                        where orderno=:orderno and stkcode=:stockid
                        ";

                        $parameters=array(
                                ':unitprice'=>$Producto['unitprice'],
                                ':qty'=>$Producto['qty'],
                                ':stockid'=>$Producto['stockid'],
                                ':orderno'=>$orderno
                            );
                        try {
                            Yii::app()->db->createCommand($UpdateOrderLine)->execute($parameters);
                        } catch (Exception $e) {
                            $ListErrors['update'][] = array('Nombre'=>$Producto['product'], 'error' => $e->errorInfo[2]);
                        }

                    }else{
                        $description = explode("--", $Producto['product']);
                        // fb::info('ok');
                        $InsertOrderLine = "insert into salesorderdetails(
                        orderlineno,
                        orderno,
                        stkcode,
                        qtyinvoiced,
                        unitprice,
                        quantity,
                        discountpercent,
                        completed,
                        narrative,
                        description,
                        poline,
                        rh_cost,
                        itemdue
                        )values(
                        :orderlineno,
                        :orderno,
                        :stkcode,
                        :qtyinvoiced,
                        :unitprice,
                        :quantity,
                        :discountpercent,
                        :completed,
                        :narrative,
                        :description,
                        :poline,
                        :rh_cost,
                        :itemdue
                        )";

                    $parameters2=array(
                        ':orderlineno'=> $orderlineno,
                        ':orderno'=> $orderno,
                        ':stkcode'=> $Producto['stockid'],
                        ':qtyinvoiced'=> 0,
                        ':unitprice'=>$Producto['unitprice'],
                        ':quantity'=> $Producto['qty'],
                        ':discountpercent'=>0,
                        ':completed'=>0,
                        ':narrative'=>'',
                        ':description'=>$description[1],
                        ':poline'=>$orderlineno,
                        ':rh_cost'=> "",
                        ':itemdue'=> Date('Y-m-d')
                        );

                    try {
                        Yii::app()->db->createCommand($InsertOrderLine)->execute($parameters2);
                    } catch (Exception $e) {
                        $ListErrors['insert'][] = array('Nombre'=>$Producto['product'], 'error' => $e->errorInfo[2]);
                    }
                    $orderlineno++;
                    }
                }
                if(($ListErrors['update']==null)&&($ListErrors['insert']==null)){
                    echo CJSON::encode(array(
                        'requestresult'=>'ok',
                        'message'=>'Los productos se actualizaron correctamente',

                        ));
                }else{
                    echo CJSON::encode(array(
                        'requestresult'=>'fail',
                        'message'=>'Ocurrió un error al actualizar los productos....',
                        'errores'=>$ListErrors
                    ));
                }

            return;
        }
    }

    public function actionEliminar(){

        if((!empty($_POST['Delete']['stockid']))&&(!empty($_REQUEST['orderno']))){

            $DeleteOrderLine="delete from salesorderdetails where stkcode = :stockid AND orderno = :orderno";

            $parameters = array(
                'stockid' => $_POST['Delete']['stockid'],
                'orderno' => $_REQUEST['orderno']
                );

            if(Yii::app()->db->createCommand($DeleteOrderLine)->execute($parameters)){
                echo CJSON::encode(array(
                        'requestresult'=>'ok',
                        'message'=>'El producto se ha eliminado...'
                    ));
            }else{
                echo CJSON::encode(array(
                        'requestresult'=>'fail',
                        'message'=>'Ocurrió un error al eliminar la información del producto...'
                    ));
            }

        }else{
            echo CJSON::encode(array(
                    'requestresult'=>'fail',
                    'message'=>'Ocurrió un error al eliminar la información del producto...'
                ));
        }
    }

    public function actionConvertir($orderno){
        if(!empty($orderno)){
            $ConvertirPedido="update salesorders set quotation = :quotation where orderno = :orderno";

            $parameters = array(
                    ':quotation' => 0,
                    ':orderno' => $orderno
                );

            if(Yii::app()->db->createCommand($ConvertirPedido)->execute($parameters)){
                echo CJSON::encode(array(
                        'requestresult' => 'ok',
                        'message' => 'La cotización se ha convertido en pedido'
                    ));
            }else{
                echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => 'Ocurrió un error, o esta cotización ya es un pedido. intente de nuevo',
                        'parametros'=>$parameters
                ));
            }
        }
    }
}

