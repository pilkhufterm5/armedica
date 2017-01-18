<?php

/**
 *
 */
class Facturacion
{

    public $OrderNo;
    public $idDebtortrans;
    public $StatusTimbre = 'Pendiente';

    /**
     * @Todo Crea Pedido
     * Al crear el Pedido Setear los campos:
     * salesorders.quotation = 3
     * salesorderdetails.completed = 1
     * salesorderdetails.qtyinvoiced = quantity
     *
     * @param  $DebtorData, Datos del Debtorno.
     * @param  $SO_Details, Lineas de detalle del pedido.
     * @return $OrderNo
     * @author erasto@realhost.com.mx
     */
    public function CreaPedido($DebtorData = null, $SO_Details, $DebtorNo = null) {
        set_time_limit(0);
        global $db;

        if (empty($DebtorNo)) {
            $DebtorNo = $DebtorData[0]['debtorno'];
        }

        if (empty($DebtorData)) {
            $DebtorData = Yii::app()->db->createCommand()->select(' debtorsmaster.*,cobranza.folio,cobranza.stockid,cobranza.fecha_corte,fp.frecuencia ')
            ->from('debtorsmaster')
            ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();
        }

        /*Branch con el que se Factura*/
        $Branch = "T-" . $DebtorNo;
        $SalesMan = '2RC';

        if (!empty($DebtorData) && !empty($SO_Details)) {

            DB_query("BEGIN", $db);
            $OrderNo = GetNextTransNo(30, $db);
            FB::INFO($OrderNo,'_______________________ORDERNO');
            try {

                $HeaderSQL = "INSERT INTO salesorders (
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
                        salesman)
                    VALUES (
                        '" . $OrderNo . "',
                        '" . $DebtorNo . "',
                        '" . $Branch . "',
                        '" . DB_escape_string($_SESSION['Items']->CustRef) . "',
                        '',
                        '" . Date("Y-m-d H:i") . "',
                        '" . $DebtorData[0]['salestype'] . "',
                        '" . 1 . "',
                        '" . DB_escape_string($DebtorData[0]['name']) . "',
                        '" . DB_escape_string($DebtorData[0]['address1']) . "',
                        '" . DB_escape_string($DebtorData[0]['address2']) . "',
                        '" . DB_escape_string($DebtorData[0]['address3']) . "',
                        '" . DB_escape_string($DebtorData[0]['address4']) . "',
                        '" . DB_escape_string($DebtorData[0]['address5']) . "',
                        '" . DB_escape_string($DebtorData[0]['address6']) . "',
                        '" . DB_escape_string($DebtorData[0]['address7']) . "',
                        '" . DB_escape_string($DebtorData[0]['address8']) . "',
                        '" . DB_escape_string($DebtorData[0]['address9']) . "',
                        '" . DB_escape_string($DebtorData[0]['address10']) . "',
                        '" . DB_escape_string($DebtorData[0]['rh_tel']) . "',
                        '" . DB_escape_string('') . "',
                        '" . $FreightCost . "',
                        'AFIL',
                        '" . Date("Y-m-d H:i") . "',
                        '3',
                        '" . $DeliverBlind . "',
                        'AFIL',
                        '" . $SalesMan . "')";

                $ErrMsg = _('The order cannot be added because');
                FB::INFO($HeaderSQL, '_______________________________HEADER');
                $InsertQryResult = DB_query($HeaderSQL, $db, $ErrMsg, '', true);

            } catch (Exception $e) {
                DB_query('ROLLBACK',$db); // do a rollback
                throw new Exception("Error al Insertar en salesorders: " . $e, 1);
                exit;
            }


            //Insert rh_translogs
            try {
                $sql1 = "INSERT INTO rh_translogs(type, typeno, date, user, realizo) VALUES (30, " . $OrderNo . ", NOW(), '" . $_SESSION['UserID'] . "','CP')";
                $res1 = DB_query($sql1, $db, 'Imposible insertar el usuario', '', true);

                $sql2 = "INSERT INTO rh_usertrans(type, user_, order_, date_) VALUES (30, '" . $_SESSION['UserID'] . "', " . $OrderNo . ", now())";
                $res2 = DB_query($sql2, $db, 'Imposible insertar el usuario', '', true);
            } catch (Exception $e) {
                DB_query('ROLLBACK',$db); // do a rollback
                throw new Exception("Error al Insertar en rh_translogs: " . $e, 1);
                exit;
            }

            //Insert Details
            foreach ($SO_Details as $Item) {
                try {
                    $LineItemsSQL = "INSERT INTO salesorderdetails (
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
                        itemdue)
                    VALUES (
                        " . $Item['orderlineno'] . ",
                        " . $OrderNo . ",
                        '" . $Item['stkcode'] . "',
                        " . $Item['quantity'] . ",
                        '" . $Item['unitprice'] . "',
                        " . $Item['quantity'] . ",
                        " . $Item['discountpercent'] . ",
                        " . 0 . ",
                        '" . $Item['narrative'] . "',
                        '" . $Item['description'] . "',
                        '" . $Item['poline'] . "',
                        " . $Item['rh_cost'] . ",
                        '" . $Item['itemdue'] . "'
                    )";
                    FB::INFO($LineItemsSQL, '_______________________________ITEMSSSSSSS');
                    $Ins_LineItemResult = DB_query($LineItemsSQL, $db, 'Imposible insertar articulo, SQL que Fallo  ' . $LineItemsSQL, '', true);
                    //* inserted line items into sales order details
                } catch (Exception $e) {
                    DB_query('ROLLBACK',$db); // do a rollback
                    throw new Exception("Error al Insertar en salesorderdetails", 1);
                    exit;
                }

            }
            DB_query("COMMIT", $db);
            FB::INFO($OrderNo, '__________________________________ORDERNO OK');
            $this->OrderNo =  $OrderNo;
        }
    }


    /**
     * @Todo Factura un Pedido
     * Antes de crear la factura setear los campos:
     * salesorders.quotation = 0
     * salesorderdetails.completed = 0
     * salesorderdetails.qtyinvoiced = 0
     * @param $OrderNo, $Folio
     * @return true
     * @author erasto@realhost.com.mx
     */
    public function FacturaPedido($OrderNo, $Folio = null, $TipoFactura = 0) {
        global $db;

        if (!empty($OrderNo)) {

            $VerifyTransNo = "SELECT order_ FROM debtortrans WHERE order_ = '" . $OrderNo . "' ";
            $_VerifyTransNo = DB_query($VerifyTransNo, $db);
            $_2VerifyTransNo = DB_fetch_assoc($_VerifyTransNo);
            if(!empty($_2VerifyTransNo)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Este Pedido ya fue Facturado con Anterioridad...",
                ));
                //return;
                exit;
            }

            if (!empty($Folio)) {
                $CobranzaData = "SELECT pm.paymentid,pm.paymentname, rh_cobranza.cuenta, rh_cobranza.metodo_pago,rh_cobranza.satid FROM rh_cobranza
                LEFT JOIN paymentmethods pm on pm.paymentid = rh_cobranza.paymentid
                WHERE folio = '" . $Folio . "'
              ";
                $_CobranzaData = DB_query($CobranzaData, $db);
                $_2CobranzaData = DB_fetch_assoc($_CobranzaData);
                if (!empty($_2CobranzaData)) {
                    //$MetodoPago = $_2CobranzaData['metodo_pago']; - Modificado por Daniel Villarreal el 11 de Julio del 2016.
                    $MetodoPago = $_2CobranzaData['satid'];
                    $ctaPago = Controller::OpenSSLDecrypt($_2CobranzaData['cuenta']);
                }
            }

            if (empty($MetodoPago)) {
                $MetodoPago = "No Identificado";
            }

            if (empty($ctaPago)) {
                $ctaPago = "No Identificado";
            }

            unset($_SESSION['Items']->LineItems);
            $_POST = array();

            /* <(°-°<) Convertir Cotizacion tipo 1 a Pedido, ponemos completed a 0 y qtyinvoiced a 0 (>°-°)> */
            $Cotizacion = "UPDATE salesorders, salesorderdetails SET salesorders.quotation = 0,
                salesorderdetails.completed = 0,
                salesorderdetails.qtyinvoiced = 0
                WHERE salesorderdetails.orderno = salesorders.orderno AND salesorders.orderno = {$OrderNo}";
            DB_query($Cotizacion, $db);

            /* ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// */

            /*ProcessInvoice*/
            /*$MASIVA. evita las impresiones de ConfirmDispatch_Invoice.php */
            $EMISION = false;
            if($TipoFactura == 1 || $TipoFactura == 2){
                $EMISION = true;
            }
            $MASIVA = true;
            ob_start();

            /****** LLeno el Carrtito con el $OrderNo ********/
            $_GET['OrderNumber'] = $OrderNo;


            include ($_SERVER['LocalERP_path'] . "/ConfirmDispatch_Invoice.php");
            unset($_GET['OrderNumber']);
            $_SESSION['metodoPago'] = $_POST['metodoPago'] = $MetodoPago;
            $_SESSION['cuentaPago'] = $_POST['cuentaPago'] = $ctaPago;

            /*******************************************************************************/
            /* <(°-°<) Factura Pedido (>°-°)> */
            //$idLocation = $_SESSION['Items']->Location;
            $idLocation = "AFIL";
            $sql_location = "select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = '$idLocation' and !isnull(id_ws_csd) and id_systypes = 10";
            $resultLocation = DB_query($sql_location, $db, '', '', false, false);
            $LocationRow = DB_fetch_array($resultLocation);

            $_POST['selectIdFolio'] = $LocationRow[0];

            //$_POST['0_QtyDispatched'] = 1;
            //$_POST['00_TaxRate'] = 16;
            $_POST['ChargeFreightCost'] = 0;
            $_POST['FreightTaxRate0'] = 0;
            $_POST['DispatchDate'] = date('d-m-Y');
            $_POST['descnarr'] = 0;
            $_POST['Consignment'] = 0;
            $_POST['BOPolicy'] = "BO";
            $_POST['InvoiceText'] = "";
            $_POST['ShipVia'] = 1;
            $_POST['tipo_factura'] = $TipoFactura;

            //$_POST['Update'] = "Actualizar";
            //include ($_SERVER['LocalERP_path'] . "/ConfirmDispatch_Invoice.php");
            $_POST['ProcessInvoice'] = "Process Invoice from MTY";
            try {
            include($_SERVER['LocalERP_path'] . "/ConfirmDispatch_Invoice.php");
            } catch (Exception $e) {
            FB::INFO($e,'_____________GET LOCATION2');
            }
            $IdDebtor = $DebtorTransID;
            ob_clean();
            FB::INFO($IdDebtor, '_____________________________ID DebtorTrans');
            $this->idDebtortrans = $IdDebtor;
            //exit;
        }
    }

    /**
     * @Todo Timbra una Factura
     * @param $idDebtortrans
     * @return true
     * @author erasto@realhost.com.mx
     */
    public function Timbrar($idDebtortrans, $Folio) {
        global $db;

        if (!empty($idDebtortrans)) {
            ob_start();
            include_once ($_SERVER['LocalERP_path'] . "/rh_cfdiFunctions32.php");
            include_once ($_SERVER['LocalERP_path'] . "/Numbers/Words.php");
            include_once ($_SERVER['LocalERP_path'] . "/CFDI32.php");
            include_once ($_SERVER['LocalERP_path'] . "/rh_cfdiFunctions32.php");

            if (!empty($Folio)) {
                $CobranzaData = "SELECT pm.paymentid,pm.paymentname,rh_cobranza.cuenta_sat, rh_cobranza.metodo_pago,rh_cobranza.satid FROM rh_cobranza
                LEFT JOIN paymentmethods pm on pm.paymentid = rh_cobranza.paymentid
                WHERE folio = '" . $Folio . "'
              ";
                $_CobranzaData = DB_query($CobranzaData, $db);
                $_2CobranzaData = DB_fetch_assoc($_CobranzaData);
                if (!empty($_2CobranzaData)) {
                    //$MetodoPago = $_2CobranzaData['metodo_pago']; - Modificado por Daniel Villarreal el 11 de Julio del 2016.
                    $MetodoPago = $_2CobranzaData['satid'];
                    $ctaPago = @openssl_decrypt($_2CobranzaData['cuenta_sat'], 'aes128', '12345');
                    //$ctaPago = Controller::OpenSSLDecrypt($_2CobranzaData['cuenta_sat']);


                }
            }

            if (empty($ctaPago)) {
                $ctaPago = "No Identificado";
            }
            if (empty($MetodoPago)) {
                $MetodoPago = "No Identificado";
            }
            $sql = " select * from debtortrans where id=" . $idDebtortrans;
            $rs = DB_query($sql, $db);
            $rw = DB_fetch_assoc($rs);

            $transno = $rw['transno'];
            $type = $rw['type'];

            $sql = "select * from stockmoves where type='" . $type . "' and transno='" . $transno . "' limit 1";
            $rss = DB_query($sql, $db);
            $rsm = DB_fetch_assoc($rss);
            $Location = $rsm['loccode'];
            $sql = 'select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = "' . $rsm['loccode'] . '"  and id_systypes = ' . $type;
            $rscsd = DB_query($sql, $db);
            $idCsdYSerie = DB_fetch_array($rscsd);

            $idCsd = $idCsdYSerie[0];
            $serie = $idCsdYSerie[1];
            $serie = getSerieByBranch($Location, $type, $db);
            $idXsd = '';
            $xmlXsd = '';
            $sql = "select not isnull(v.id_salesorders) is_transportista from debtortrans d join rh_vps__transportista v on d.order_ = v.id_salesorders and d.id = $idDebtortrans";
            $result = DB_query($sql, $db);
            $is_transportista = DB_num_rows($result);

            global $BlockFolio;
            $BlockFolio = false;
            global $MensajesGlobales;
            $MensajesGlobales = array();

            $datosSat2 = cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd, $MetodoPago, $ctaPago, 1);
            FB::INFO($datosSat2, '________________________TIMBRADO');
            ob_clean();
            $this->StatusTimbre = 'Timbrado';
            //return true;
        }
    }


        /*
     *  TIMBRAR SOLO 1 CONCEPTO
     *  POR DANIEL VILLARREAL EL 18 DE ENERO DEL 2016
     *
     */
/**
     * @Todo Timbra una Factura
     * @param $idDebtortrans
     * @return true
     * @author erasto@realhost.com.mx
     */
    public function Timbrar1Concepto($idDebtortrans, $Folio) {
        global $db;

        
        if (!empty($idDebtortrans)) {

       
            ob_start();
            include_once ($_SERVER['LocalERP_path'] . "/rh_cfdiFunctions32.php");
            include_once ($_SERVER['LocalERP_path'] . "/Numbers/Words.php");
            include_once ($_SERVER['LocalERP_path'] . "/CFDI32.php");
            include_once ($_SERVER['LocalERP_path'] . "/rh_cfdiFunctions32.php");

            if (!empty($Folio)) {
                $CobranzaData = "SELECT pm.paymentid,pm.paymentname,rh_cobranza.cuenta_sat, rh_cobranza.metodo_pago,rh_cobranza.satid FROM rh_cobranza
                LEFT JOIN paymentmethods pm on pm.paymentid = rh_cobranza.paymentid
                WHERE rh_cobranza.folio = '" . $Folio . "'
              ";
                $_CobranzaData = DB_query($CobranzaData, $db);
                $_2CobranzaData = DB_fetch_assoc($_CobranzaData);
                if (!empty($_2CobranzaData)) {
                    //$MetodoPago = $_2CobranzaData['metodo_pago']; - Modificado por Daniel Villarreal el 11 de Julio del 2016.
                    $MetodoPago = $_2CobranzaData['satid'];
                    $ctaPago = @openssl_decrypt($_2CobranzaData['cuenta_sat'], 'aes128', '12345');
                    //$ctaPago = Controller::OpenSSLDecrypt($_2CobranzaData['cuenta_sat']);
                }
            }

            if (empty($ctaPago)) {
                $ctaPago = "No Identificado";
            }
            if (empty($MetodoPago)) {
                $MetodoPago = "No Identificado";
            }
            $sql = " select * from debtortrans where id=" . $idDebtortrans;
            $rs = DB_query($sql, $db);
            $rw = DB_fetch_assoc($rs);

            $transno = $rw['transno'];
            $type = $rw['type'];

            $sql = "select * from stockmoves where type='" . $type . "' and transno='" . $transno . "' limit 1";
            $rss = DB_query($sql, $db);
            $rsm = DB_fetch_assoc($rss);
            $Location = $rsm['loccode'];
            $sql = 'select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = "' . $rsm['loccode'] . '"  and id_systypes = ' . $type;
            $rscsd = DB_query($sql, $db);
            $idCsdYSerie = DB_fetch_array($rscsd);

            $idCsd = $idCsdYSerie[0];
            $serie = $idCsdYSerie[1];
            $serie = getSerieByBranch($Location, $type, $db);
            $idXsd = '';
            $xmlXsd = '';
            $sql = "select not isnull(v.id_salesorders) is_transportista from debtortrans d join rh_vps__transportista v on d.order_ = v.id_salesorders and d.id = $idDebtortrans";
            $result = DB_query($sql, $db);
            $is_transportista = DB_num_rows($result);

            global $BlockFolio;
            $BlockFolio = false;
            global $MensajesGlobales;
            $MensajesGlobales = array();

            
            $datosSat2 = cfdMultiEmpresas($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd, $MetodoPago, $ctaPago, 1);
            
            FB::INFO($datosSat2, '________________________TIMBRADO');
            ob_clean();
            $this->StatusTimbre = 'Timbrado';
            //return true;
        }
    }

}
?>
