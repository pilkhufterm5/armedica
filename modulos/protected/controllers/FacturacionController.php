<?php

/**
 *Facturacion
 */
class FacturacionController extends Controller {



    public function actionCargasaldos(){
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '5000M');
        /*
        +-----+-------+---------+---------+---------------------+----------+----------+-----------+---------------------+---------+
        | id  | folio | cliente | foliofc | fecgenera           | subtotal | iva      | total     | fecpago             | valpago |
        +-----+-------+---------+---------+---------------------+----------+----------+-----------+---------------------+---------+
        */
        if($_GET['Process'] ==true ){

        }else{
            echo "Falta GET &Process=true";
            FB::INFO($db,"Conection");
            return;
        }
        $n = $_GET['pagina']  * 100;
        $_2GetSaldos = "SELECT * FROM tmp_saldos WHERE cargado = 0 limit 1000";
        $_GetSaldos = DB_query($_2GetSaldos, $db);

        while ( $GetSaldos = DB_fetch_assoc($_GetSaldos)) {

            $_2GetDebtorData = "SELECT folio, debtorno FROM rh_titular WHERE folio = '{$GetSaldos['folio']}' ";
            $_GetDebtorData = DB_query($_2GetDebtorData, $db);
            $GetDebtorData = DB_fetch_assoc($_GetDebtorData);
            if(!empty($GetDebtorData)){

                $TransNo = GetNextTransNo(10, $db);
                $OrderNo = GetNextTransNo(30, $db);
                $PeriodNo = GetPeriod(date($_SESSION['DefaultDateFormat'],strtotime($GetSaldos['fecgenera'])), $db);

/*

*/              /* INGRESO LA FACTURA EN debtortrans*/
                $InsertDebtorTrans ="INSERT INTO debtortrans (transno,
                                                            type,
                                                            debtorno,
                                                            branchcode,
                                                            trandate,
                                                            prd,
                                                            settled,
                                                            reference,
                                                            tpe,
                                                            order_,
                                                            rate,
                                                            ovamount,
                                                            ovgst,
                                                            ovfreight,
                                                            ovdiscount,
                                                            diffonexch,
                                                            alloc,
                                                            invtext,
                                                            shipvia,
                                                            rh_createdate,
                                                            rh_status
                                                            )VALUES(
                                                            '" . $TransNo . "',
                                                            10,
                                                            '" . $GetDebtorData['debtorno'] . "',
                                                            'T-" . $GetDebtorData['debtorno'] . "',
                                                            '" . $GetSaldos['fecgenera'] . "',
                                                            '" . $PeriodNo . "',
                                                            '0',
                                                            '" . $GetSaldos['foliofc'] . "',
                                                            'L1',
                                                            '" . $OrderNo . "',
                                                            '1',
                                                            '" . $GetSaldos['subtotal'] . "',
                                                            '" . $GetSaldos['iva'] . "',
                                                            '0',
                                                            '0',
                                                            '0',
                                                            '0',
                                                            'SALDOS INICIALES',
                                                            '1',
                                                            '" . date("Y-m-d H:i:s") . "',
                                                            'N');";
                                DB_query($InsertDebtorTrans, $db);
                                $id_debtortrans = DB_Last_Insert_ID($db,'debtortrans', 'id');

                /* INGRESO MOVIMIENTO EN rh_cfd__cfd*/
                $InsertStockMoves = "INSERT INTO stockmoves (stockid,
                                                             type,
                                                             transno,
                                                             loccode,
                                                             trandate,
                                                             debtorno,
                                                             branchcode,
                                                             price,
                                                             prd,
                                                             reference,
                                                             qty,
                                                             discountpercent,
                                                             standardcost,
                                                             show_on_inv_crds,
                                                             newqoh,
                                                             narrative,
                                                             description
                                                             )VALUES('SALDOS',
                                                             10,
                                                             '" . $TransNo . "',
                                                             'AFIL',
                                                             '" . $GetSaldos['fecgenera'] . "',
                                                             '" . $GetDebtorData['debtorno'] . "',
                                                             'T-" . $GetDebtorData['debtorno'] . "',
                                                             '" . $GetSaldos['subtotal'] . "',
                                                             '" . $PeriodNo . "',
                                                             '" . $GetSaldos['foliofc'] . "',
                                                             '1',
                                                             '0',
                                                             '0',
                                                             '1',
                                                             '0',
                                                             '  ',
                                                             'SALDOS INICIALES')";
                                DB_query($InsertStockMoves, $db);





                $rootpath = $_SERVER['LocalERP_path'];
                $Comprobante_Moneda = 'MXN';
                include_once($rootpath . "/Numbers/Words.php");
                chdir($rootpath);
                $Letras = new Numbers_Words();

                $tot = explode(".", number_format($GetSaldos['total'], 2, '.', ''));
                $Letra = Numbers_Words::toWords($tot[0], "es");
                if ($Comprobante_Moneda == 'MXN') {
                    $Comprobante_Moneda = 'M.N.';
                }
                if ($tot[1] == 0) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . " 00/100 " . $Comprobante_Moneda;
                } else if (strlen($tot[1]) >= 2) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "/100 " . $Comprobante_Moneda;
                } else {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "0/100 " . $Comprobante_Moneda;
                }
                $ConLetra = strtoupper($ConLetra);
                chdir($rootpath);

                echo "AKI2 <br>";
                /* INGRESO LA FACTURA EN rh_cfd__cfd*/
                $InsertCFD = "INSERT INTO rh_cfd__cfd ( id_systypes,
                                                        id_debtortrans,
                                                        fk_transno,
                                                        total_en_letra,
                                                        fecha,
                                                        serie,
                                                        folio,
                                                        tipo_de_comprobante,
                                                        cuentapago,
                                                        metodopago
                                                       )VALUES(10,
                                                       '" . $id_debtortrans . "',
                                                       '" . $TransNo . "',
                                                       '" . $ConLetra . "',
                                                       '" . $GetSaldos['fecgenera'] . "',
                                                       '',
                                                       '" . $GetSaldos['foliofc'] . "',
                                                       'INGRESO',
                                                       'No Identificado',
                                                       'No Identificado'
                                                       )";
                DB_query($InsertCFD, $db);
                echo "AKI3 <br>";
                /* INGRESO LA FACTURA EN rh_facturacion*/
                $InsertRH_Facturacion = "INSERT INTO rh_facturacion ( folio,
                                                        debtorno,
                                                        userid,
                                                        fecha_corte,
                                                        status,
                                                        tipo,
                                                        systype,
                                                        debtortrans_id,
                                                        transno,
                                                        created,
                                                        orderno
                                                    )VALUES('" . $GetSaldos['folio'] . "',
                                                        '" . $GetDebtorData['debtorno'] . "',
                                                        '" . $_SESSION['UserID'] . "',
                                                        '" . $GetSaldos['fecgenera'] . "',
                                                        'Procesada',
                                                        'SALDOS INICIALES',
                                                        '10',
                                                        '" . $id_debtortrans . "',
                                                        '" . $TransNo . "',
                                                        '" . date("Y-m-d H:i:s") . "',
                                                        '" . $OrderNo . "'
                                                       )";
                DB_query($InsertRH_Facturacion, $db);
                echo "AKI4 <br>";
                /* INSERTS EN SALESORDERS */
                $InsertSalesOrders = "INSERT INTO salesorders (orderno,
                                                            debtorno,
                                                            branchcode,
                                                            orddate,
                                                            fromstkloc,
                                                            shipvia,
                                                            fromstkloc_virtual)
                                                        VALUES(
                                                            '" . $OrderNo . "',
                                                            '" . $GetDebtorData['debtorno'] . "',
                                                            'T-" . $GetDebtorData['debtorno'] . "',
                                                            '" . $GetSaldos['fecgenera'] . "',
                                                            'AFIL',
                                                            '1',
                                                            'AFIL'
                                                            )";
                DB_query($InsertSalesOrders, $db);
/*
               orderno: 27023
              debtorno: 66653
            branchcode: T-66653
               orddate: 2014-06-02 00:00:00
               shipvia: 1
            fromstkloc: MTY
    fromstkloc_virtual: MTY
*/
                echo "AKI5 <br>";
                $UpdateFlag = "UPDATE tmp_saldos set cargado = 1 WHERE id = '{$GetSaldos['id']}' ";
                DB_query($UpdateFlag, $db);
            }

        }
        $this->render("test");


    }


    /**
    *  @Todo
    *  Crea Pedidos Masivos Para Efectivo Cheque y Nomina Frecuencia Mensual
    */
    public function actionCreapedidosmasivosefectivocheque(){

        if($_GET['Process'] ==true){
            // echo "Done...!!!";
            // return;
        }else{
            echo "Falta el &Process=true";
            return;
        }
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');



        //return;
        /*Crea Pedidos para EFECTIVO CHEQUE NOMINA MENSUAL */
        $SQLGetEfectivoCheke = "select rh_cobranza.*, (titular.costo_total) as COSTO_TOTAL  from rh_cobranza
                            left join rh_titular titular on rh_cobranza.folio = titular.folio
                            where rh_cobranza.paymentid in (2,1,11)
                            AND titular.movimientos_afiliacion = 'Activo'
                            AND rh_cobranza.folio_asociado =''
                            AND rh_cobranza.stockid  NOT IN ('AFIL9','AFIL10','AFIL11')   ";
        $_2GetEfectivoCheke = DB_query($SQLGetEfectivoCheke, $db);
        while (($_GetEfectivoCheke = DB_fetch_assoc($_2GetEfectivoCheke))) {
            $GetEfectivoCheke[] = $_GetEfectivoCheke;
        }
        //exit;
        FB::INFO($GetEfectivoCheke,'____________________________RESULT');
        foreach($GetEfectivoCheke as $EfectivoCheke){
            //$Date = date('Y-m-d', mktime(0, 0, 0, date("m") + 1, 01, date("Y")));
            $DebtorNo = $EfectivoCheke['debtorno'];

            $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,cobranza.folio,cobranza.stockid, cobranza.frecuencia_pago,
                titular.fecha_ingreso, cobranza.fecha_corte, fp.frecuencia, pm.paymentname,cobranza.cobro_inscripcion ')
            ->from('debtorsmaster')
            ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

            /*Valida Fechas Segun Frecuencia de Pago*/
            switch ($DebtorData[0]['frecuencia_pago']) {
                case 1:
                    $Frecuencia = 1;
                    $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y")));
                    $CreateOrders = true;
                    $MesAnio = $this->GetMonth($Date, 1);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    break;
                case 2:
                    $Frecuencia = 2;

                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, "Efectivo");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 2);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    break;
                case 3:
                    $Frecuencia = 3;
                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, "Efectivo");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 3);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y") + 1));
                    break;
                case 4:
                    $Frecuencia = 4;
                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, "Efectivo");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;
                    $MesAnio = $this->GetMonth($Date, 4);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y") + 1));
                    break;
                default:
                    //throw new Exception("Error, No se encontro el Metodo de Pago", 1);
                    $CreateOrders = false;
                    break;
            }

            FB::INFO($EfectivoCheke['COSTO_TOTAL'],'______________________Costos');
            if($CreateOrders == true){

                $SO_Details = array(0 => array(
                    'orderlineno' => 0,
                    'stkcode' => $EfectivoCheke['stockid'],
                    'unitprice' => $EfectivoCheke['COSTO_TOTAL'],
                    'quantity' => 1,
                    'discountpercent' => 0,
                    'narrative' => 'Pedido Inicial',
                    'description' => $Description,
                    'poline' => '',
                    'rh_cost' => '0.0000',
                    'itemdue' => date("Y-m-d H:i:s")
                ));
                $OrderNo = $this->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                $CreaBitacoraAgregarSocio = $this->CreaBitacoraAgregarSocio($DebtorData[0], 0, 0, $OrderNo, "Factura Programada",$EfectivoCheke['frecuencia_pago'],01, $Inicial = 1);
            }

        }


        $this->render('test');
    }

    /**
    *  @Todo
    *  Crea Pedidos Masivos Para Tarjetas Mensual
    */
    public function actionCreapedidosmasivostarjetas(){
        if(($_GET['Process'] ==true)){
            // echo "Done...!!!";
            // return;
            //http://184.106.216.217/erp_test/armedica/modulos/index.php?r=facturacion/creapedidosmasivostarjetas&Process=true&Frecuencia=MENSUAL
        }else{
            echo "Falta el &Process=true";
            return;
        }
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        //return;
        $SQLGetCreditoDebito = "SELECT rh_cobranza.*, (titular.costo_total) AS COSTO_TOTAL
            FROM rh_cobranza
            JOIN rh_titular titular ON rh_cobranza.folio = titular.folio
            where rh_cobranza.paymentid IN (9,10)
            AND titular.movimientos_afiliacion = 'Activo'
            AND rh_cobranza.folio_asociado =''
            AND rh_cobranza.stockid  NOT IN ('AFIL9','AFIL10','AFIL11')   ";
        $_2GetCreditoDebito = DB_query($SQLGetCreditoDebito, $db);
        while (($_GetCreditoDebito = DB_fetch_assoc($_2GetCreditoDebito))) {
            $GetCreditoDebito[] = $_GetCreditoDebito;
        }
        //exit;
        //FB::INFO($GetCreditoDebito,'____________________________RESULT');
        foreach($GetCreditoDebito as $CreditoDebito){
            //$Date = date('Y-m-d', mktime(0, 0, 0, date("m") + 1, 25, date("Y")));
            $DebtorNo = $CreditoDebito['debtorno'];


            $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,cobranza.folio,cobranza.stockid, cobranza.frecuencia_pago, titular.fecha_ingreso, cobranza.fecha_corte, fp.frecuencia, pm.paymentname,cobranza.cobro_inscripcion ')
            ->from('debtorsmaster')
            ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

            /*Valida Fechas Segun Frecuencia de Pago*/
            switch ($DebtorData[0]['frecuencia_pago']) {
                case 1:
                    $Frecuencia = 1;
                    $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 25, date("Y")));
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 1, 1);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    break;
                case 2:
                    $Frecuencia = 2;

                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, "Tarjetas");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 25, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 2);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];


                    break;
                case 3:
                    $Frecuencia = 3;
                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, "Tarjetas");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 25, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 3);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y") + 1));
                    break;
                case 4:
                    $Frecuencia = 4;
                    $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, "Tarjetas");
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 25, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 4);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y") + 1));
                    break;
                default:
                    //throw new Exception("Error, No se encontro el Metodo de Pago", 1);
                    $CreateOrders = false;
                    break;
            }

            //Obtengo El Costo del Plan
            //include_once ("AfiliacionesController.php");
            //$_2COSTO = AfiliacionesController::ActualizaCosto($DebtorData[0]['folio'], 'Socio', 1);
            //$UnitPrice = $_2COSTO['CostoTotal'];
            //FB::INFO($_2COSTO,'______________________Costos');
            if($CreateOrders == true){

                $FacturaCFDI = new Facturacion();
                $SO_Details = array(0 => array(
                    'orderlineno' => 0,
                    'stkcode' => $CreditoDebito['stockid'],
                    'unitprice' => $CreditoDebito['COSTO_TOTAL'],
                    'quantity' => 1,
                    'discountpercent' => 0,
                    'narrative' => 'Pedido Inicial',
                    'description' => $Description,
                    'poline' => '',
                    'rh_cost' => '0.0000',
                    'itemdue' => date("Y-m-d H:i")
                ));
                FB::INFO($SO_Details,'_____________________________DETALLE');
                $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                $CreaBitacoraAgregarSocio = $this->CreaBitacoraAgregarSocio($DebtorData[0], 0, 0, $FacturaCFDI->OrderNo, "Factura Programada",$CreditoDebito['frecuencia_pago'],25, $Inicial =1);
            }
        }
        $this->render('test');
    }

  public function CreaPedidoEmision($DebtorNo,$CambiarMesFacturacion){

        if(!empty($DebtorNo)){
            $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,
                cobranza.folio,
                cobranza.stockid,
                cobranza.frecuencia_pago,
                titular.fecha_ingreso,
                (titular.costo_total) as COSTO_TOTAL,
                cobranza.fecha_corte,
                fp.frecuencia,pm.paymentid,
                pm.paymentname,
                cobranza.cobro_inscripcion')
            ->from('debtorsmaster')
            ->join('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->join('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

            /*Valida Fechas Segun Frecuencia de Pago*/
            $Frecuencia = $DebtorData[0]['frecuencia_pago'];
            $Fecha_Ingreso = $DebtorData[0]['fecha_ingreso'];
            $MetodoPago = $DebtorData[0]['paymentid'];
            $Folio = $DebtorData[0]['folio'];


            if($MetodoPago == 9 || $MetodoPago == 10){
                $DiaEmision = 25;
                $TipoEmision = "Tarjetas";
                $IDTipoEmision = 2;
                //$Mas1Mes = 1;
            }else{
                $DiaEmision = '01';
                $TipoEmision = "Efectivo";
                $IDTipoEmision = 1;
                //$Mas1Mes = 0;
            }

            /* ======================================
            =========================================
            OPCION AGREGADA EN BASE A LA OPCION SELECCIONADA PARA LA FECHA , 
                MES ACTUAL - NO SUMA MES
            REALIZADO POR DANIEL VILLARREAL EL 24 DE NOVIEMBRE DEL 2015
            =========================================
            ========================================= */
            // 0 = MesActual y 1 = MesSiguiente 
            $Mas1Mes = $CambiarMesFacturacion;
            /* ======================================
            =========================================
                            TERMINA
            =========================================
            ======================================= */
            
            $CreateOrders = false;
            switch ($Frecuencia) {
                case 1:
                    $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                    $CreateOrders = true;

                    $MesAnio = $this->GetMonth($Date, 1, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    break;
                case 2:
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    /*==============================
                    POR DANIEL VILLARREAL, 04 DIC 2015
                    FUNCION OBSOLETA
                    ================================*/
                    #if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    #}else{
                    #    $Date = $GetFechas['ProximaFactura'];
                    #    //No Le Toca
                    #    $FechaFactura23 = $Date;
                    #}

                    $MesAnio = $this->GetMonth($Date, 2, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];
                    break;
                case 3:
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    /*==============================
                    POR DANIEL VILLARREAL, 04 DIC 2015
                    FUNCION OBSOLETA
                    ================================*/
                    #if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    #}else{
                    #    $Date = $GetFechas['ProximaFactura'];
                    #    //No Le Toca
                    #    $FechaFactura23 = $Date;
                    #}

                    $MesAnio = $this->GetMonth($Date, 3, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];
                    break;
                case 4:
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    /*==============================
                    POR DANIEL VILLARREAL, 04 DIC 2015
                    FUNCION OBSOLETA
                    ================================*/
                    #if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    #}else{
                    #    $Date = $GetFechas['ProximaFactura'];
                    #    //No Le Toca
                    #    $FechaFactura23 = $Date;
                    #}

                    $MesAnio = $this->GetMonth($Date, 4,$Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];
                    break;
                default:
                    //throw new Exception("Error, No se encontro el Metodo de Pago", 1);
                    $CreateOrders = false;
                    break;
            }

            $Fecha_Pedido = date("Y-m-d H:i:s");

            /*Verifica que no tenga un Pedido de Emision para el Dia de Hoy, en caso de tener uno ya no hace el Pedido*/

            $VerificaPedido_Hoy = Yii::app()->db->createCommand()
            ->select("SOD.orderno")
            ->from("salesorderdetails SOD")
            ->join("salesorders SO", "SO.orderno = SOD.orderno")
            ->where("SO.debtorno = :debtorno AND SOD.narrative LIKE :narrative ", array(":debtorno" => $DebtorNo, ":narrative" => "EMISION-" . date("Y-m-d") ." %"))
            ->queryAll();

            if(!empty($VerificaPedido_Hoy)){
                $CreateOrders = false;
            }


            if($CreateOrders == true){

                $FacturaCFDI = new Facturacion();
                $SO_Details = array(0 => array(
                    'orderlineno' => 0,
                    'stkcode' => $DebtorData[0]['stockid'],
                    'unitprice' => $DebtorData[0]['COSTO_TOTAL'],
                    'quantity' => 1,
                    'discountpercent' => 0,
                    'narrative' => 'EMISION-' . $Fecha_Pedido,
                    'description' => $Description,
                    'poline' => '',
                    'rh_cost' => '0.0000',
                    'itemdue' => $Fecha_Pedido
                ));

                $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                FB::INFO($FacturaCFDI->OrderNo,'___________-ORDERNO');

                $FacturaCFDI->FacturaPedido($FacturaCFDI->OrderNo, $Folio, $IDTipoEmision);
                //FacturaPedido($OrderNo, $Folio = null, $TipoFactura = 0) {
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');

                //Timbrar Factura
                $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);

                if ($FacturaCFDI->StatusTimbre = 'Timbrado') {

                    $GetTransNo = Yii::app()->db->createCommand()
                    ->select("transno,order_")
                    ->from("debtortrans")
                    ->where("debtortrans.id = :id", array(':id' => $FacturaCFDI->idDebtortrans))
                    ->queryAll();

                    // Obtenemos la fecha corte
                    // Agregado por Daniel Villarreal el 29 de Diciembre del 2015
                    if($Mas1Mes==1)
                    {
                        $Fecha_Corte = strtotime ("+1 Months", strtotime ($Date));
                        $Fecha_Corte =  date ( 'Y-m-d' , $Fecha_Corte);
                    }else
                    {
                        $Fecha_Corte = $Date;
                    }

                    $LogBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                                  values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                    $LogParameters = array(
                        ':folio' => $Folio,
                        ':debtorno' => $DebtorNo,
                        ':userid' => $_SESSION['UserID'],
                        ':fecha_corte' => $Fecha_Corte,
                        ':status' => "Procesada",
                        ':tipo' => 'EMISION',
                        ':systype' => 10,
                        ':debtortrans_id' => $FacturaCFDI->idDebtortrans,
                        ':transno' => $GetTransNo[0]['transno'],
                        ':created' => date("Y-m-d H:i:s"),
                        ':orderno' => $FacturaCFDI->OrderNo,
                        ':frecuencia_pago' => $Frecuencia,
                        ':prox_factura' => '0000-00-00'
                    );
                    Yii::app()->db->createCommand($LogBitacora)->execute($LogParameters);
                    FB::INFO('_______TIMBRADO OK LOGUEADO OK');
                }

            }

        }
// exit;
    }

    public function LogueaBitacora(){

    }


    public function actionFacturaemision(){

        //FB::INFO($_POST,'__________________________POST');

        /* ======================================
        ========================================
        OPCION AGREGADA EN BASE A LA OPCION SELECCIONADA PARA LA FECHA , 
            MES ACTUAL - NO SUMA MES, MES SIGUIENTE - SUMA 1 MES
        REALIZADO POR DANIEL VILLARREAL EL 1 DE DICIEMBRE DEL 2015
        =========================================
        ========================================= */
        $CambiarMesFacturacion = $_POST['Emision']['CambiarMesFacturacion'];
        /* ======================================
        =========================================
                        TERMINA
        =========================================
        ======================================= */
        

        if(!empty($_POST['Emision']['Folios'])){
            parse_str($_POST['Emision']['Folios'], $ParseDataIDs);

            foreach ($ParseDataIDs['TimbrarFactura'] as $key => $Folio) {
                $DebtorNo = $this->GetDebtorNo($Folio);
                $this->CreaPedidoEmision($DebtorNo,$CambiarMesFacturacion );
            }
            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Facturado y Timbrado Correctamente... "
            ));
            exit;
        }

        if(empty($_POST['Emision']['Folios']) && isset($_POST['Emision']['Folios'])){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Seleccione los Folios a Facturar... "
            ));
            exit;
        }
        FB::INFO('_____END');
    }



    public function actionEmision(){
        FB::INFO($_POST,'_______________POST');

        if(!empty($_POST))
        {

            $WhereString = " stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total > 0 ";

                // AND date_format(date(titular.fecha_ingreso),'%m') !=  date_format(now(),'%m')

            if (!empty($_POST['paymentid'])) {
                $_2MetodoPago = implode(",", $_POST['paymentid']);
                $WhereString .= " AND cobranza.paymentid IN ({$_2MetodoPago}) ";
            }

            if (!empty($_POST['frecuencia_pago'])) {
                $_2FrecuenciaPago = implode(",", $_POST['frecuencia_pago']);
                $WhereString .= " AND cobranza.frecuencia_pago IN ({$_2FrecuenciaPago}) ";
            }

             /* ====================================================
            AGREGADO POR DANIEL VILLARREAL 03 DE DICIEMBRE DEL 2015
            PARA FILTRAR POR COBRADOR
            ===================================================== */
             if (!empty($_POST['cobradorid'])) {
                $_2Cobrador = implode(",", $_POST['cobradorid']);
                $WhereString .= " AND cobranza.cobrador IN ({$_2Cobrador}) ";
            }
            /* ====================================================
            TERMINA 
            ===================================================== */


             /* ====================================================
            AGREGADO POR DANIEL VILLARREAL 03 DE DICIEMBRE DEL 2015
            SE OBTIENE EL VALOR DEL PERIODO ACTUAL O SIGUIENTE
            ===================================================== */
            if (isset($_POST['CambiarMesFacturacion'])) {
                // 0 = Mes Actual o Periodo Actual y 1 = Mes siguiente o Periodo Siguiente
                $CambiarMesFacturacion = $_POST['CambiarMesFacturacion'];
            }else{ $CambiarMesFacturacion = 0; }
            /* ====================================================
            TERMINA 
            ===================================================== */


            FB::INFO($WhereString,'____________________WHERE');
            $GetDebtorData = Yii::app()->db->createCommand()
            ->select("titular.folio,
                titular.debtorno,
                CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
                titular.taxref,
                (titular.address10) as PostalCode,
                (cobranza.stockid) as CPlan,
                (cobranza.paymentid) as CPaymentName,
                (cobranza.frecuencia_pago) as CFrecPago,
                fasignados.tipo_membresia,
                titular.fecha_ingreso,
                titular.costo_total,
                cobradores.nombre")
            ->from("rh_titular titular")
            ->join("rh_cobranza cobranza", "titular.folio = cobranza.folio")
            ->join("rh_foliosasignados fasignados", "titular.folio = fasignados.folio")
            ->join("rh_cobradores cobradores", "cobranza.cobrador = cobradores.id")
            ->join("stockmaster stkm", "cobranza.stockid = stkm.stockid")
            ->where($WhereString)
            //->limit(10)
            ->queryAll();

            foreach ($GetDebtorData as $AfilNo) {
                # code...
                $Frecuencia = $AfilNo['CFrecPago'];
                $MetodoPago = $AfilNo['CPaymentName'];
                $Fecha_Ingreso = $AfilNo['fecha_ingreso'];

                if($MetodoPago == 9 || $MetodoPago == 10){
                    $DiaEmision = 25;
                    $TipoEmision = "Tarjetas";
                }else{
                    $TipoEmision = "Efectivo";
                    $DiaEmision = '01';
                }

                $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                $CreateOrders = true;
                // Verificamos si le toca o no pedido pedido
                /*MODIFICADO POR DANIEL VILLARREAL EL 21 DE FEBRERO DEL 2016*/
                $VerifyOrder = $this->VerificarOrden($Frecuencia,$DiaEmision,$Date,$CambiarMesFacturacion,$AfilNo['folio'],$AfilNo['costo_total'],0);
                if($VerifyOrder['respuesta']){
                   
                    $AfilNo['FechaPedido'] = $VerifyOrder['fechapedido'];
                    $GetDebtorData2[] = $AfilNo;
                }

            }

        }

        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }
        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(' paymentid NOT IN (3,6,7)'), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(/*" sucursal = 'MTY' "*/), 'id', 'frecuencia');


        /* ================================================================================================= 
        AGREGADO PARA MOSTRAR LA LISTA DE COBRADORES,
        POR DANIEL VILLARREAL EL 30 DE DICIEMBRE DEL 2015
        =================================================================================================*/ 
        $ListaCobradores = CHtml::listData(Cobradores::model()->findAll(/*" sucursal = 'MTY' "*/), 'id', 'nombre');
        /* ================================================================================================= 
        TERMINA
        =================================================================================================*/ 
        

        $this->render("emision", array(
            'DebtorData' => $GetDebtorData2,
            'ListaFormasPago' => $ListaFormasPago,
            'ListaFrecuenciaPago' => $ListaFrecuenciaPago,
            'ListaPlanes' => $ListaPlanes,
            'ListaCobradores' => $ListaCobradores
            ));
    }




    public function actionTest() {

        // SELECT SOD.orderno FROM salesorderdetails SOD
        // join salesorders SO ON SO.orderno = SOD.orderno
        // WHERE SO.debtorno = 109165 AND SOD.narrative LIKE 'EMISION-2015-08-25 %'

        $DebtorNo = 109165;
        $VerificaPedido_Hoy = Yii::app()->db->createCommand()
        ->select("SOD.orderno")
        ->from("salesorderdetails SOD")
        ->join("salesorders SO", "SO.orderno = SOD.orderno")
        ->where("SO.debtorno = :debtorno AND SOD.narrative LIKE :narrative ", array(":debtorno" => $DebtorNo, ":narrative" => "EMISION-" . date("Y-m-d") ." %"))
        ->queryAll();

            if(!empty($VerificaPedido_Hoy)){
                $CreateOrders = false;
            }

        FB::INFO($CreateOrders,'____________SELECT');



        FB::INFO($VerificaPedido_Hoy,'____________VERIFICA'); exit;

        $Fecha_Ingreso = "2010-08-05";
        //$Fechas = $this->GetPeriods($Fecha_Ingreso,2);


                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3);
                    FB::INFO($GetFechas,'___________GET FECHAS');
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), 01, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }


        FB::INFO($Date,'____________FECHAS');
/*
        $Fecha_Ingreso = "2010-01-01";
        $Frecuencia = 2;
        $MetodoPago = 9;

        // 9 y 10 Trajetas
        if(($MetodoPago == 9) || ($MetodoPago == 10)){
            $DiaEmision = 25;
        }else{
            $DiaEmision = 01;
        }

        switch ($Frecuencia) {
            case '2':
                //Semestral
                $Meses = 6;
                $Sumar = 6;
                $PagosAlAnio = 12 / $Meses;
                break;
            case '3':
                //Anual
                $Meses = 12;
                $Sumar = 12;
                $PagosAlAnio = 12 / $Meses;
                break;
            case '4':
                //Anual INSEN
                break;
            default:
                # code...
                break;
        }


        $Parts = explode("-", $Fecha_Ingreso);
        $i=0;
        while ( $i <= $PagosAlAnio) {
            $Fecha[] = date('Y-m-d', mktime(0, 0, 0, $Parts[1] + $Meses, $DiaEmision, $Parts[0]));
            $Meses = $Meses + $Sumar;
            $i++;
        }
        FB::INFO($Fecha,'_______________FECHAS');
*/


        /*
        $MSCon = SQLServerWS::MSDBConect();
        FB::INFO($MSCon, '_______sss');
        $GetTables = mssql_query("SELECT TABLE_SCHEMA + '.' + TABLE_NAME, *
                    FROM INFORMATION_SCHEMA.TABLES
                    WHERE TABLE_TYPE = 'BASE TABLE'
                    ORDER BY TABLE_SCHEMA + '.' + TABLE_NAME", $MSCon);
        while (($_2Tables = mssql_fetch_assoc($GetTables))) {
            $Tables[] = $_2Tables;
        }
        FB::INFO($Tables, '___________________________________TABLES;');
        */


        $this->render('test');
    }

    public function actionTitular() {
        $MSCon = SQLServerWS::MSDBConect();

        //$SQLALTER = "ALTER TABLE CCM_Foltitular ADD rh_sinc SMALLINT ";
        //mssql_query($SQLALTER, $MSCon);

        //$SQLCheckFolio = "UPDATE CCM_Foltitular SET rh_sinc = 0";
        //$_2CheckFolio = mssql_query($SQLCheckFolio, $MSCon);

        //$SQLQueryTitular = "SELECT TOP 100 * FROM CCM_Foltitular WHERE rh_sinc = 0";
        $SQLQueryTitular = "SELECT TOP 10 * FROM CCM_Foltitular WHERE rh_sinc = 1 AND TipoFolio = 2 ";
        $GetTitular = mssql_query($SQLQueryTitular, $MSCon);
        while (($_2Titular = mssql_fetch_assoc($GetTitular))) {
            $Titular[] = $_2Titular;
        }
        FB::INFO($Titular, '___________________________________Titular;');

        $SQLQueryTitular2 = "SELECT COUNT(*) FROM CCM_Foltitular WHERE rh_sinc = 1 AND TipoFolio = 2";
        $GetTitular2 = mssql_query($SQLQueryTitular2, $MSCon);
        while (($_2Titular2 = mssql_fetch_assoc($GetTitular2))) {
            $Titular2[] = $_2Titular2;
        }
        FB::INFO($Titular2, '___________________________________COUNT Titular;');

        ///////////////////////////////////////////////////////////////////
        //$SQLQueryCobranza = "SELECT TOP 100 * FROM CCM_FolCobranza";
        /*$FOLIO = 68;
         $SQLQueryCobranza = "SELECT * FROM CCM_FolCobranza WHERE FOLIO = " . $FOLIO;
         $GetCobranza = mssql_query($SQLQueryCobranza, $MSCon);
         $Cobranza = mssql_fetch_assoc($GetCobranza); */
        /*
         while (($_2Cobranza = mssql_fetch_assoc($GetCobranza))) {
         $Cobranza[] = $_2Cobranza;
         }*/
        //FB::INFO($Cobranza, '___________________________________Cobranza;');

        ///////////////////////////////////////////////////////////////////
        //$SQLQuerySocios = "SELECT TOP 100 * FROM CCM_Folsocios";
        //$SQLQuerySocios = "SELECT COUNT(*) FROM CCM_Folsocios";
        /*
         $SQLQuerySocios = "SELECT * FROM CCM_Folsocios WHERE FOLIO = " . $FOLIO;
         $GetSocios = mssql_query($SQLQuerySocios, $MSCon);
         while (($_2Socios = mssql_fetch_assoc($GetSocios))) {
         $Socios[] = $_2Socios;
         }
         FB::INFO($Socios, '___________________________________Socios;');*/

        ///////////////////////////////////////////////////////////////////
        /*
         $SQLQuerySMaestros = "SELECT TOP 100 * FROM CZA_SeccionMaestros";
         //$SQLQuerySMaestros = "SELECT COUNT(*) FROM CZA_SeccionMaestros";

         $GetSMaestros = mssql_query($SQLQuerySMaestros, $MSCon);
         while (($_2SMaestros = mssql_fetch_assoc($GetSMaestros))) {
         $SMaestros[] = $_2SMaestros;
         }
         FB::INFO($SMaestros, '___________________________________SMaestros;'); */

        exit ;
    }

    public function actionGettitular() {

        //echo date("Y-m-d", strtotime('23-Jan-01'));

        global $db;
        $SQLQueryTitular = "SELECT COUNT(*) FROM tmp_titular WHERE rh_sinc = 0 AND empresa = 0 ";
        $SQLQueryTitular2 = "SELECT COUNT(*) FROM tmp_titular WHERE rh_sinc = 1";
        //$SQLQueryTitular = "SELECT * FROM tmp_titular WHERE rh_sinc = 0 LIMIT 10";
        $GetTitular2 = DB_query($SQLQueryTitular2, $db);
        $GetTitular = DB_query($SQLQueryTitular, $db);
        while (($_2Titular = DB_fetch_assoc($GetTitular))) {
            $Titular[] = $_2Titular;
        }
        while (($_2Titular2 = DB_fetch_assoc($GetTitular2))) {
            $Titular2[] = $_2Titular2;
        }
        FB::INFO($Titular,'_______________________________Pending');
        FB::INFO($Titular2,'_______________________________Done');
        $this->render('sincroniza');
    }

    /**
     * @Todo
     * Busca Registros de Titular con rh_sinc = 0 en la table tmp_titular
     * Inserta en debtorsmaster, custbranch, rh_titular, rh_cobranza
     * #NoMover
     * */
    public function actionsincroniza() {
        global $db;
        set_time_limit(0);
        if ($_GET['Process']==true) {

            $MSCon = SQLServerWS::MSDBConect();
            //$SQLQueryTitular = "SELECT TOP 10 * FROM CCM_Foltitular";
            //$SQLQueryTitular = "SELECT * FROM CCM_Foltitular WHERE rh_sinc IS NOT NULL";
            $SQLQueryTitular = "SELECT TOP 100 * FROM CCM_Foltitular WHERE rh_sinc = 0";
            $GetTitular = mssql_query($SQLQueryTitular, $MSCon);
            while (($_2Titular = mssql_fetch_assoc($GetTitular))) {
                $SQLTitular[] = $_2Titular;
            }
            //FB::INFO($SQLTitular, '___________________________________Titular;');

            //exit;
            foreach ($SQLTitular as $Titular) {

                /*Crear Debtorno*/
                $_Debtor['DebtorNo'] = GetNextTransNo(500, $db);
                $_Debtor['PaymentTerms'] = 30;
                $_Debtor['creditlimit'] = $_SESSION['DefaultCreditLimit'];
                /*1000*/
                $_Debtor['SalesType'] = "L1";
                $CreateDebtorNo = "INSERT INTO debtorsmaster (
                                                debtorno,
                                                name,
                                                name2,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address9,
                                                address10,
                                                rh_tel,
                                                currcode,
                                                clientsince,
                                                holdreason,
                                                paymentterms,
                                                discount,
                                                discountcode,
                                                pymtdiscount,
                                                creditlimit,
                                                salestype,
                                                invaddrbranch,
                                                taxref,
                                                customerpoline)
                                            VALUES ('" . $_Debtor['DebtorNo'] . "',
                                            '" . DB_escape_string($Titular['NOMBRES'] . " " . $Titular['APELLIDOS']) . "',
                                            '" . DB_escape_string($Titular['RAZONSOC']) . "',
                                            '" . DB_escape_string($Titular['CALLE']) . "',
                                            '" . DB_escape_string($Titular['NUMERO']) . "',
                                            '" . DB_escape_string($Titular['address3']) . "',
                                            '" . DB_escape_string($Titular['COLONIA']) . "',
                                            '" . DB_escape_string($Titular['SECTOR']) . "',
                                            '" . DB_escape_string($Titular['ENTRECALLE']) . "',
                                            '" . DB_escape_string($Titular['IdMunicipio']) . "',
                                            '" . DB_escape_string($Titular['IdEstado']) . "',
                                            'MEXICO',
                                            '" . DB_escape_string($Titular['CP']) . "',
                                            '" . $Titular['TELEFONO1'] . "',
                                            'MXN',
                                            '" . $Titular['FECHA'] . "',
                                            '" . $Titular['HoldReason'] . "',
                                            '" . $_Debtor['PaymentTerms'] . "',
                                            '" . ($Titular['Discount']) / 100 . "',
                                            '" . $Titular['DiscountCode'] . "',
                                            '" . ($Titular['PymtDiscount']) / 100 . "',
                                            '" . $_Debtor['CreditLimit'] . "',
                                            '" . $_Debtor['SalesType'] . "',
                                            '" . $Titular['AddrInvBranch'] . "',
                                            '" . DB_escape_string($Titular['RFC']) . "',
                                            '" . $Titular['CustomerPOLine'] . "'
                                            )";
                DB_query($CreateDebtorNo, $db);
                /*****************************************************************************************/

                /*Crea Titular*/
                if (!empty($Titular['SINCLUIDOS'])) {
                    $_2Titular['SINCLUIDOS'] = explode(",", $Titular['SINCLUIDOS']);
                    $Titular['SINCLUIDOS'] = json_encode($_2Titular['SINCLUIDOS'], 1);
                }

                if ($Titular['SEXO']=="M") {
                    $Titular['SEXO'] = "MASCULINO";
                } else {
                    $Titular['SEXO'] = "FEMENINO";
                }

                switch ($Titular['STATUS']) {
                    case 1 :
                        $Titular['STATUS'] = "Activo";
                        break;
                    case 2 :
                        $Titular['STATUS'] = "Cancelado";
                        break;
                    case 3 :
                        $Titular['STATUS'] = "Suspendido";
                        break;
                    default :
                        $Titular['STATUS'] = "Activo";
                        break;
                }

                //enum('Activo','Cancelado','Suspendido','Nuevo','Titular')
                $CreateTitular = "insert into rh_titular (folio,
                                        debtorno,
                                        fecha_ingreso,
                                        fecha_ultaum,
                                        tipopersona,
                                        asesor,
                                        apellidos,
                                        name,
                                        sexo,
                                        name2,
                                        nombre_empresa,
                                        taxref,
                                        curp,
                                        email,
                                        contacto,
                                        rh_tel,
                                        rh_tel2,
                                        enfermeria,
                                        costoenfermeria,
                                        serviciolimitado,
                                        serviciosdisponibles,
                                        costo_servicioextra,
                                        address1,
                                        address2,
                                        address3,
                                        address4,
                                        address5,
                                        address6,
                                        address7,
                                        address8,
                                        address10,
                                        cuadrante1,
                                        cuadrante2,
                                        cuadrante3,
                                        orderno,
                                        rh_numproveedor,
                                        servicios_seleccionados,
                                        examenes_laboratorio,
                                        movimientos_afiliacion)
                                        values ('" . $Titular['FOLIO'] . "',
                                        '" . $_Debtor['DebtorNo'] . "',
                                        '" . $Titular['FECHA'] . "',
                                        '" . $Titular['FECHAULTAU'] . "',
                                        '" . $Titular['TIPOPERSON'] . "',
                                        '" . $Titular['IdAsesor'] . "',
                                        '" . DB_escape_string($Titular['APELLIDOS']) . "',
                                        '" . DB_escape_string($Titular['NOMBRES']) . "',
                                        '" . $Titular['SEXO'] . "',
                                        '" . DB_escape_string($Titular['RAZONSOC']) . "',
                                        '" . DB_escape_string($Titular['NOMCOMERC']) . "',
                                        '" . $Titular['RFC'] . "',
                                        '" . $Titular['CURP'] . "',
                                        '" . DB_escape_string($Titular['CORREO']) . "',
                                        '" . DB_escape_string($Titular['CONTACTO']) . "',
                                        '" . $Titular['TELEFONO1'] . "',
                                        '" . $Titular['TELEFONO2'] . "',
                                        '" . $Titular['ENF'] . "',
                                        '" . $Titular['COSTOENF'] . "',
                                        '" . $Titular['LIM_SERV'] . "',
                                        '" . $Titular['LIM_MES'] . "',
                                        '" . $Titular['LIM_COSTEX'] . "',
                                        '" . DB_escape_string($Titular['CALLE']) . "',
                                        '" . $Titular['NUMERO'] . "',
                                        '" . $Titular['address3'] . "',
                                        '" . DB_escape_string($Titular['COLONIA']) . "',
                                        '" . $Titular['SECTOR'] . "',
                                        '" . $Titular['ENTRECALLE'] . "',
                                        '" . $Titular['IdMunicipio'] . "',
                                        '" . $Titular['IdEstado'] . "',
                                        '" . $Titular['CP'] . "',
                                        '" . $Titular['CUADRANTE1'] . "',
                                        '" . $Titular['CUADRANTE2'] . "',
                                        '" . $Titular['CUADRANTE3'] . "',
                                        '" . $Titular['NUMOC'] . "',
                                        '" . $Titular['NUMPROV'] . "',
                                        '" . $Titular['SINCLUIDOS'] . "',
                                        '" . $Titular['ExamenesLaboratorio'] . "',
                                        '" . $Titular['STATUS'] . "')";
                //FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');
                DB_query($CreateTitular, $db);

                /*Inserto Branch para Facturar*/
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";

                $CreateBranch = "insert into custbranch ( branchcode,
                                                folio,
                                                brname,
                                                braddress1,
                                                braddress2,
                                                braddress3,
                                                braddress4,
                                                braddress5,
                                                braddress6,
                                                braddress7,
                                                braddress8,
                                                braddress10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                sexo,
                                                nombre_empresa,
                                                fecha_ingreso,
                                                fecha_ultaum,
                                                phoneno,
                                                movimientos_socios,
                                                debtorno,
                                                area,
                                                salesman,
                                                defaultlocation,
                                                defaultshipvia,
                                                taxgroupid,
                                                rh_status_captura
                                                )
                                                values ('T-" . $_Debtor['DebtorNo'] . "',
                                                '" . $Titular['FOLIO'] . "',
                                                '" . DB_escape_string($Titular['NOMBRES'] . " " . $Titular['APELLIDOS']) . "',
                                                '" . DB_escape_string($Titular['CALLE']) . "',
                                                '" . $Titular['NUMERO'] . "',
                                                '" . $Titular['address3'] . "',
                                                '" . DB_escape_string($Titular['COLONIA']) . "',
                                                '" . $Titular['SECTOR'] . "',
                                                '" . $Titular['ENTRECALLE'] . "',
                                                '" . $Titular['IdMunicipio'] . "',
                                                '" . $Titular['IdEstado'] . "',
                                                '" . $Titular['CP'] . "',
                                                '" . $Titular['CUADRANTE1'] . "',
                                                '" . $Titular['CUADRANTE2'] . "',
                                                '" . $Titular['CUADRANTE3'] . "',
                                                '" . $Titular['SEXO'] . "',
                                                '" . DB_escape_string($Titular['RAZONSOC']) . "',
                                                '" . $Titular['FECHA'] . "',
                                                '" . $Titular['FECHAULTAU'] . "',
                                                '" . $Titular['TELEFONO1'] . "',
                                                'Titular',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . $Area . "',
                                                '" . $Salesman . "',
                                                '" . $DefaultLocation . "',
                                                '" . $DefaultShipVia . "',
                                                '" . $TaxGroupid . "',
                                                'Titular'
                                                )";
                //FB::INFO($CreateBranch, '_______________________$CreateBranch');
                DB_query($CreateBranch, $db);
                /*******************************************************************************************/

                /*************************Busca Registro de Cobranza y lo Inserta**********************************/

                $SQLQueryCobranza = "SELECT * FROM CCM_FolCobranza WHERE FOLIO = " . $Titular['FOLIO'];
                $GetCobranza = mssql_query($SQLQueryCobranza, $MSCon);
                $Cobranza = mssql_fetch_assoc($GetCobranza);

                if (!empty($Cobranza)) {
                    $CreateCobranza = "insert into rh_cobranza (folio,
                                                debtorno,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                rh_tel,
                                                rh_tel2,
                                                email,
                                                enviar_factura,
                                                encargado_pagos,
                                                stockid,
                                                empresa,
                                                frecuencia_pago,
                                                convenio,
                                                paymentid,
                                                zona,
                                                cobrador,
                                                cuenta,
                                                vencimiento,
                                                cuenta_sat,
                                                num_plastico,
                                                metodo_pago,
                                                tipo_tarjeta,
                                                tipo_cuenta,
                                                num_empleado,
                                                identificacion,
                                                fecha_corte,
                                                factura_fisica,
                                                folio_asociado,
                                                dias_cobro,
                                                dias_cobro_dia,
                                                cobro_datefrom,
                                                cobro_dateto,
                                                dias_credito,
                                                dias_revision,
                                                dias_revision_dia,
                                                revision_datefrom,
                                                revision_dateto,
                                                cobro_inscripcion)
                                                values ('" . $Titular['FOLIO'] . "',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . DB_escape_string($Cobranza['Calle']) . "',
                                                '" . $Cobranza['Numero'] . "',
                                                '" . $Cobranza['address3'] . "',
                                                '" . DB_escape_string($Cobranza['Colonia']) . "',
                                                '" . $Cobranza['Sector'] . "',
                                                '" . $Cobranza['EntreCalles'] . "',
                                                '" . $Cobranza['IdMunicipio'] . "',
                                                '" . $Cobranza['IdEstado'] . "',
                                                '" . $Cobranza['CP'] . "',
                                                '" . $Cobranza['Cuadrante1'] . "',
                                                '" . $Cobranza['Cuadrante2'] . "',
                                                '" . $Cobranza['Cuadrante3'] . "',
                                                '" . $Cobranza['Telefono'] . "',
                                                '" . $Cobranza['TelefonoAlternativo'] . "',
                                                '" . $Cobranza['Email'] . "',
                                                '" . $Cobranza['EnviaFacturaPorCorreo'] . "',
                                                '" . $Cobranza['EncargadoDePagos'] . "',
                                                '" . $Titular['IdProducto'] . "',
                                                '" . $Titular['IdEmpresa'] . "',
                                                '" . $Cobranza['IdFrecuenciaPago'] . "',
                                                '" . $Titular['IdConvenio'] . "',
                                                '" . $Cobranza['IdFormaPago'] . "',
                                                '" . $Cobranza['Zona'] . "',
                                                '" . $Cobranza['IdCobrador'] . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['NCuenta']) . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['FechaVencimiento']) . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['CuentaSAT']) . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['NumeroPlastico']) . "',
                                                '" . $Cobranza['metodo_pago'] . "',
                                                '" . $Cobranza['IdTipoTarjeta'] . "',
                                                '" . $Cobranza['TipoCuenta'] . "',
                                                '" . $Titular['NOEMPLEA'] . "',
                                                '" . $Cobranza['IdIdentificacion'] . "',
                                                '" . $Cobranza['FechaCorte'] . "',
                                                '" . $Cobranza['RequiereFacturaFisica'] . "',
                                                '" . $Cobranza['FolioAsociado'] . "',
                                                '" . $Cobranza['IdTipoCobro'] . "',
                                                '" . $Cobranza['DiasDeCobro'] . "',
                                                '" . $Cobranza['HorarioInicioCobro'] . "',
                                                '" . $Cobranza['HorarioFinCobro'] . "',
                                                '" . $Cobranza['DiasDeCredito'] . "',
                                                '" . $Cobranza['IdTipoRevision'] . "',
                                                '" . $Cobranza['DiasDeRevision'] . "',
                                                '" . $Cobranza['HoraInicioRevision'] . "',
                                                '" . $Cobranza['HoraFinRevision'] . "',
                                                '0')";
                    DB_query($CreateCobranza, $db);
                }

                ///////////////////////////////////////////////////////////////////
                $SQLQuerySocios = "SELECT * FROM CCM_Folsocios WHERE FOLIO = " . $Titular['FOLIO'];
                //$SQLQuerySocios = "SELECT TOP 100 * FROM CCM_Folsocios";
                //$SQLQuerySocios = "SELECT COUNT(*) FROM CCM_Folsocios";

                $GetSocios = mssql_query($SQLQuerySocios, $MSCon);
                while (($_2Socios = mssql_fetch_assoc($GetSocios))) {
                    $Socios[] = $_2Socios;
                }

                $branch = 1;
                foreach ($Socios as $Socio) {
                    $Area = "MTY";
                    $Salesman = "2RC";
                    $DefaultLocation = "MTY";
                    $DefaultShipVia = "1";
                    $TaxGroupid = "4";

                    $CreateSocio = "insert into custbranch ( branchcode,
                                            folio,
                                            brname,
                                            braddress1,
                                            braddress2,
                                            braddress3,
                                            braddress4,
                                            braddress5,
                                            braddress6,
                                            braddress7,
                                            braddress8,
                                            braddress10,
                                            cuadrante1,
                                            cuadrante2,
                                            cuadrante3,
                                            sexo,
                                            nombre_empresa,
                                            fecha_nacimiento,
                                            fecha_ingreso,
                                            fecha_ultaum,
                                            phoneno,
                                            antecedentes_clinicos,
                                            movimientos_socios,
                                            debtorno,
                                            area,
                                            salesman,
                                            defaultlocation,
                                            defaultshipvia,
                                            taxgroupid,
                                            rh_status_captura
                                            )
                                            values ('" . $branch . "',
                                            '" . $Titular['FOLIO'] . "',
                                            '" . DB_escape_string($Socio['NOMBRES'] . " " . $Socio['APELLIDOS']) . "',
                                            '" . DB_escape_string($Socio['CALLE']) . "',
                                            '" . $Socio['NUMERO'] . "',
                                            '" . $Socio['braddress3'] . "',
                                            '" . DB_escape_string($Socio['COLONIA']) . "',
                                            '" . $Socio['SECTOR'] . "',
                                            '" . $Socio['ENTRECALLE'] . "',
                                            '" . $Socio['IdMunicipio'] . "',
                                            '" . $Socio['IdEstado'] . "',
                                            '" . $Socio['CP'] . "',
                                            '" . $Socio['CUADRANTE1'] . "',
                                            '" . $Socio['CUADRANTE2'] . "',
                                            '" . $Socio['CUADRANTE3'] . "',
                                            '" . $Socio['SEXO'] . "',
                                            '" . DB_escape_string($Socio['NOMCOMERC']) . "',
                                            '" . $Socio['FECNAC'] . "',
                                            '" . $Socio['FECHA'] . "',
                                            '" . $Socio['FECHAULTAU'] . "',
                                            '" . $Socio['TELEFONO'] . "',
                                            '" . DB_escape_string($_POST['antecedentes_clinicos']) . "',
                                            'Activo',
                                            '" . $_Debtor['DebtorNo'] . "',
                                            '" . $Area . "',
                                            '" . $Salesman . "',
                                            '" . $DefaultLocation . "',
                                            '" . $DefaultShipVia . "',
                                            '" . $TaxGroupid . "',
                                            'Activo'
                                            )";
                    DB_query($CreateSocio, $db);
                    $branch++;
                }/*END SOCIOS FOREACH*/
                /*END ID*/
                //Actualiza el status a 1 para q no vuelva a tomar este registro.
                //$SQLQueryTitular = "SELECT TOP 100 * FROM CCM_Foltitular WHERE rh_sinc != 1";
                $SQLCheckFolio = "UPDATE CCM_Foltitular SET rh_sinc = 1 WHERE FOLIO = " . $Titular['FOLIO'];
                $_2CheckFolio = mssql_query($SQLCheckFolio, $MSCon);

            }/*END FOREACH*/

        }

        $this->render('sincroniza');
    }

    public function actionLoadClientes(){

        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if ($_GET['Process']==true) {

            $SQLQueryClientes = "SELECT * FROM tmp_clientes WHERE rh_sinc = 0 LIMIT 1000 ";
            $GetClientes = DB_query($SQLQueryClientes, $db);
            while (($_2Clientes = DB_fetch_assoc($GetClientes))) {
                $SQLClientes[] = $_2Clientes;
            }
            foreach ($SQLClientes as $Cliente) {

                if(empty($Cliente['nombres'])){
                    $Cliente['nombres'] = $Cliente['razonsoc'];
                }

                /* Traduccion de Catalogo ESTADOS*/
                $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_estados
                LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                WHERE arm.ar_id = '" . $Cliente['estado'] . "'
                AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                $_GetEstadoID = DB_query($_2GetEstado, $db);
                $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                $Cliente['estado'] = $GetEstadoID['estado'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MUNICIPIOS*/
                $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_municipios
                LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                WHERE arm.ar_id = '" . $Cliente['municipio'] . "'
                AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                $_GetMunID = DB_query($_2GetMun, $db);
                $GetMunID = DB_fetch_assoc($_GetMunID);
                $Cliente['municipio'] = $GetMunID['municipio'];
                /*************************************************************************************************************/
                $fecha_tmp = explode("/", $Cliente['fecha']);
                //  27/08/2009
                $Cliente['fecha'] = $fecha_tmp[2] . "-" . $fecha_tmp[1] . "-" . $fecha_tmp[0];
                //$Cliente['fecha'] = date("Y-m-d", strtotime($Cliente['fecha']));


                /*Crear Debtorno*/
                $_Debtor['DebtorNo'] = GetNextTransNo(500, $db);
                $_Debtor['PaymentTerms'] = 30;
                $_Debtor['creditlimit'] = $_SESSION['DefaultCreditLimit'];/*1000*/
                $_Debtor['SalesType'] = "L1";
                $CreateDebtorNo = "INSERT INTO debtorsmaster (
                                                debtorno,
                                                name,
                                                name2,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address9,
                                                address10,
                                                rh_tel,
                                                currcode,
                                                clientsince,
                                                holdreason,
                                                paymentterms,
                                                discount,
                                                discountcode,
                                                pymtdiscount,
                                                creditlimit,
                                                salestype,
                                                invaddrbranch,
                                                taxref,
                                                customerpoline)
                                            VALUES ('" . $_Debtor['DebtorNo'] . "',
                                            '" . DB_escape_string($Cliente['nombres'] . " " . $Cliente['apellidos']) . "',
                                            '" . DB_escape_string($Cliente['razonsoc']) . "',
                                            '" . DB_escape_string($Cliente['calle']) . "',
                                            '" . DB_escape_string($Cliente['numero']) . "',
                                            '" . DB_escape_string($Cliente['address3']) . "',
                                            '" . DB_escape_string($Cliente['colonia']) . "',
                                            '" . DB_escape_string($Cliente['sector']) . "',
                                            '" . DB_escape_string($Cliente['entrecalle']) . "',
                                            '" . DB_escape_string($Cliente['municipio']) . "',
                                            '" . DB_escape_string($Cliente['estado']) . "',
                                            'MEXICO',
                                            '" . DB_escape_string($Titular['cp']) . "',
                                            '" . $Titular['telefono'] . "',
                                            'MXN',
                                            '" . $Titular['fecha'] . "',
                                            '" . $_Debtor['HoldReason'] . "',
                                            '" . $_Debtor['PaymentTerms'] . "',
                                            '" . ($_Debtor['Discount']) / 100 . "',
                                            '" . $_Debtor['DiscountCode'] . "',
                                            '" . ($_Debtor['PymtDiscount']) / 100 . "',
                                            '" . $_Debtor['CreditLimit'] . "',
                                            '" . $_Debtor['SalesType'] . "',
                                            '" . $_Debtor['AddrInvBranch'] . "',
                                            '" . DB_escape_string($Titular['rfc']) . "',
                                            '" . $_Debtor['CustomerPOLine'] . "'
                                            )";
                DB_query($CreateDebtorNo, $db);
                /*Crea Titular*/

                //enum('Activo','Cancelado','Suspendido','Nuevo','Titular')
                $CreateTitular = "insert into rh_titular (folio,
                                        debtorno,
                                        fecha_ingreso,
                                        fecha_ultaum,
                                        tipopersona,
                                        asesor,
                                        apellidos,
                                        name,
                                        sexo,
                                        name2,
                                        nombre_empresa,
                                        taxref,
                                        curp,
                                        email,
                                        contacto,
                                        rh_tel,
                                        rh_tel2,
                                        enfermeria,
                                        costoenfermeria,
                                        serviciolimitado,
                                        serviciosdisponibles,
                                        costo_servicioextra,
                                        address1,
                                        address2,
                                        address3,
                                        address4,
                                        address5,
                                        address6,
                                        address7,
                                        address8,
                                        address10,
                                        cuadrante1,
                                        cuadrante2,
                                        cuadrante3,
                                        orderno,
                                        rh_numproveedor,
                                        servicios_seleccionados,
                                        examenes_laboratorio,
                                        movimientos_afiliacion,
                                        costo_total,
                                        ar_sucursal,
                                        rh_eventual)
                                        values ('" . $Cliente['Folio'] . "',
                                        '" . $_Debtor['DebtorNo'] . "',
                                        '" . $Cliente['fecha'] . "',
                                        '" . $Cliente['fechaultau'] . "',
                                        '" . $Cliente['tipoperson'] . "',
                                        '" . $Cliente['asesor'] . "',
                                        '" . DB_escape_string($Cliente['apellidos']) . "',
                                        '" . DB_escape_string($Cliente['nombres']) . "',
                                        '" . $Cliente['sexo'] . "',
                                        '" . DB_escape_string($Cliente['razonsoc']) . "',
                                        '" . DB_escape_string($Cliente['nomcomerc']) . "',
                                        '" . $Cliente['rfc'] . "',
                                        '" . $Cliente['curp'] . "',
                                        '" . DB_escape_string($Cliente['correo']) . "',
                                        '" . DB_escape_string($Cliente['contacto']) . "',
                                        '" . $Cliente['telefono'] . "',
                                        '" . $Cliente['telefono'] . "',
                                        '" . $Cliente['enf'] . "',
                                        '" . $Cliente['costoenf'] . "',
                                        '" . $Cliente['lim_serv'] . "',
                                        '" . $Cliente['lim_mes'] . "',
                                        '" . $Cliente['lim_costex'] . "',
                                        '" . DB_escape_string($Cliente['calle']) . "',
                                        '" . $Cliente['numero'] . "',
                                        '" . $Cliente['address3'] . "',
                                        '" . DB_escape_string($Cliente['colonia']) . "',
                                        '" . $Cliente['sector'] . "',
                                        '" . $Cliente['entrecalle'] . "',
                                        '" . $Cliente['municipio'] . "',
                                        '" . $Cliente['estado'] . "',
                                        '" . $Cliente['cp'] . "',
                                        '" . $Cliente['cuadrante1'] . "',
                                        '" . $Cliente['cuadrante2'] . "',
                                        '" . $Cliente['cuadrante3'] . "',
                                        '" . $Cliente['numoc'] . "',
                                        '" . $Cliente['numprov'] . "',
                                        '" . $Cliente['sincluidos'] . "',
                                        '" . $Cliente['ExamenesLaboratorio'] . "',
                                        '" . $Cliente['status'] . "',
                                        '" . $Cliente['tarifa'] . "',
                                        'MTY',
                                        1)";
                                        //rh_titular.costo_total = tmp_titular2.tarifa
                //FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');
                DB_query($CreateTitular, $db);

                /*Inserto Branch para Facturar*/
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";

                $CreateBranch = "insert into custbranch ( branchcode,
                                                folio,
                                                brname,
                                                braddress1,
                                                braddress2,
                                                braddress3,
                                                braddress4,
                                                braddress5,
                                                braddress6,
                                                braddress7,
                                                braddress8,
                                                braddress10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                sexo,
                                                nombre_empresa,
                                                fecha_ingreso,
                                                fecha_ultaum,
                                                phoneno,
                                                movimientos_socios,
                                                debtorno,
                                                area,
                                                salesman,
                                                defaultlocation,
                                                defaultshipvia,
                                                taxgroupid,
                                                rh_status_captura
                                                )
                                                values ('T-" . $_Debtor['DebtorNo'] . "',
                                                '" . $Cliente['Folio'] . "',
                                                '" . DB_escape_string($Cliente['nombres'] . " " . $Cliente['apellidos']) . "',
                                                '" . DB_escape_string($Cliente['calle']) . "',
                                                '" . $Cliente['numero'] . "',
                                                '" . $Cliente['address3'] . "',
                                                '" . DB_escape_string($Cliente['colonia']) . "',
                                                '" . $Cliente['sector'] . "',
                                                '" . $Cliente['entrecalle'] . "',
                                                '" . $Cliente['municipio'] . "',
                                                '" . $Cliente['estado'] . "',
                                                '" . $Cliente['cp'] . "',
                                                '" . $Cliente['cuadrante1'] . "',
                                                '" . $Cliente['cuadrante2'] . "',
                                                '" . $Cliente['cuadrante3'] . "',
                                                '" . $Cliente['sexo'] . "',
                                                '" . DB_escape_string($Cliente['razonsoc']) . "',
                                                '" . $Cliente['fecha'] . "',
                                                '" . $Cliente['fechaultau'] . "',
                                                '" . $Cliente['telefono'] . "',
                                                'Titular',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . $Area . "',
                                                '" . $Salesman . "',
                                                '" . $DefaultLocation . "',
                                                '" . $DefaultShipVia . "',
                                                '" . $TaxGroupid . "',
                                                'Titular'
                                                )";
                DB_query($CreateBranch, $db);
                /**************************************************************************************************/

                    /* Traduccion de Catalogo COBRADORES*/
                    $_2GetCatCobradores = "SELECT rh_cobradores.id, rh_cobradores.nombre, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_cobradores
                    LEFT JOIN ar_match arm on rh_cobradores.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cliente['cobrador'] . "'
                    AND arm.nombre_catalogo = 'COBRADORES' AND arm.sucursal = 'MTY' ";
                    $_GetCatCobradores = DB_query($_2GetCatCobradores, $db);
                    $GetCobradorID = DB_fetch_assoc($_GetCatCobradores);
                    $Cliente['cobrador'] = $GetCobradorID['rh_id'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo ESTADOS*/
                    $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_estados
                    LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cliente['estadoe'] . "'
                    AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                    $_GetEstadoID = DB_query($_2GetEstado, $db);
                    $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                    $Cliente['estadoe'] = $GetEstadoID['estado'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo MUNICIPIOS*/
                    $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_municipios
                    LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cliente['municipioe'] . "'
                    AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                    $_GetMunID = DB_query($_2GetMun, $db);
                    $GetMunID = DB_fetch_assoc($_GetMunID);
                    $Cliente['municipioe'] = $GetMunID['municipio'];
                    /*************************************************************************************************************/

                    $CreateCobranza = "insert  into rh_cobranza (folio,
                                                debtorno,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                rh_tel,
                                                rh_tel2,
                                                email,
                                                enviar_factura,
                                                encargado_pagos,
                                                stockid,
                                                empresa,
                                                frecuencia_pago,
                                                convenio,
                                                loccode,
                                                paymentid,
                                                zona,
                                                cobrador,
                                                cuenta,
                                                vencimiento,
                                                cuenta_sat,
                                                num_plastico,
                                                metodo_pago,
                                                tipo_tarjeta,
                                                tipo_cuenta,
                                                num_empleado,
                                                identificacion,
                                                fecha_corte,
                                                factura_fisica,
                                                folio_asociado,
                                                dias_cobro,
                                                dias_cobro_dia,
                                                cobro_datefrom,
                                                cobro_dateto,
                                                dias_credito,
                                                dias_revision,
                                                dias_revision_dia,
                                                revision_datefrom,
                                                revision_dateto,
                                                cobro_inscripcion)
                                                values ('" . $Cliente['Folio'] . "',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . DB_escape_string($Cliente['callee']) . "',
                                                '" . $Cliente['numeroe'] . "',
                                                '" . $Cliente['address3'] . "',
                                                '" . DB_escape_string($Cliente['coloniae']) . "',
                                                '" . $Cliente['sectore'] . "',
                                                '" . $Cliente['entrecallee'] . "',
                                                '" . $Cliente['municipioe'] . "',
                                                '" . $Cliente['estadoe'] . "',
                                                '" . $Cliente['cpe'] . "',
                                                '" . $Cliente['cuadrante1e'] . "',
                                                '" . $Cliente['cuadrante2e'] . "',
                                                '" . $Cliente['cuadrante3e'] . "',
                                                '" . $Cliente['telefonoe'] . "',
                                                '" . $Cliente['telefonoe'] . "',
                                                '" . $Cliente['correocob'] . "',
                                                '" . $Cliente['EnviaFacturaPorCorreo'] . "',
                                                '" . $Cliente['contpaga'] . "',
                                                '" . $Cliente['producto'] . "',
                                                '" . $Cliente['empresa'] . "',
                                                '" . $Cliente['tipopago'] . "',
                                                '" . $Cliente['nconvenio'] . "',
                                                '" . $Cliente['sucursal'] . "',
                                                '" . $Cliente['formapago'] . "',
                                                '" . $Cliente['Zona'] . "',
                                                '" . $Cliente['cobrador'] . "',
                                                '" . $this->OpenSSLEncrypt($Cliente['ncuenta']) . "',
                                                '" . $this->OpenSSLEncrypt($Cliente['vence']) . "',
                                                '" . $this->OpenSSLEncrypt($Cliente['cuenta_sat']) . "',
                                                '" . $this->OpenSSLEncrypt($Cliente['nplastico']) . "',
                                                '" . $Cliente['formapago'] . "',
                                                '" . $Cliente['ntipotarj'] . "',
                                                '" . $Cliente['tipocuenta'] . "',
                                                '" . $Cliente['nempleado'] . "',
                                                '" . $Cliente['identifica'] . "',
                                                '" . $Cliente['FechaCorte'] . "',
                                                '" . $Cliente['facfisica'] . "',
                                                '" . $Cliente['folioas'] . "',
                                                '" . $Cliente['tipocobro'] . "',
                                                '" . $Cliente['diacobro'] . "',
                                                '" . $Cliente['horicob'] . "',
                                                '" . $Cliente['horfcob'] . "',
                                                '" . $Cliente['DiasDeCredito'] . "',
                                                '" . $Cliente['tiporev'] . "',
                                                '" . $Cliente['diarev'] . "',
                                                '" . $Cliente['horirev'] . "',
                                                '" . $Cliente['horfrev'] . "',
                                                '0')";
                    DB_query($CreateCobranza, $db);
                    $SQLCheckFolio = "UPDATE tmp_clientes SET rh_sinc = 1 WHERE Folio = " . $Cliente['Folio'];
                    DB_query($SQLCheckFolio, $db);

            }//End Foreach Cliente

        }
        $this->render('test');

    }

    /**
     * @Todo
     * Busca Registros de Titular con rh_sinc = 0 en la table tmp_titular
     * Inserta en debtorsmaster, custbranch, rh_titular, rh_cobranza
     * #NoMover
     * */
    public function actionSincronizamysql() {
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if ($_GET['Process']==true) {

            $SQLQueryTitular = "SELECT * FROM tmp_titular WHERE rh_sinc = 0 LIMIT 1000 ";
            $GetTitular = DB_query($SQLQueryTitular, $db);
            while (($_2Titular = DB_fetch_assoc($GetTitular))) {
                $SQLTitular[] = $_2Titular;
            }

            foreach ($SQLTitular as $Titular) {
                /* Traduccion de Catalogo COMISIONISTAS*/
                $_2GetCatEmpresas = "SELECT rh_comisionistas.id, rh_comisionistas.comisionista, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_comisionistas
                LEFT JOIN ar_match arm on rh_comisionistas.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['comisionista'] . "'
                AND arm.nombre_catalogo = 'COMISIONISTAS' AND arm.sucursal = 'MTY' ";
                $_GetCatEmpresas = DB_query($_2GetCatEmpresas, $db);
                $GetEmpresaID = DB_fetch_assoc($_GetCatEmpresas);
                $Titular['comisionis'] = $GetEmpresaID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo EMPRESAS*/
                $_2GetCatEmpresas = "SELECT rh_empresas.id, rh_empresas.empresa, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_empresas
                LEFT JOIN ar_match arm on rh_empresas.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['empresa'] . "'
                AND arm.nombre_catalogo = 'EMPRESAS' AND arm.sucursal = 'MTY' ";
                $_GetCatEmpresas = DB_query($_2GetCatEmpresas, $db);
                $GetEmpresaID = DB_fetch_assoc($_GetCatEmpresas);
                $Titular['empresa'] = $GetEmpresaID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MOTIVOS_CANCELACION*/
                $_2GetCatMotivosCanc = "SELECT rh_motivos_cancelacion.id, rh_motivos_cancelacion.motivo, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_motivos_cancelacion
                LEFT JOIN ar_match arm on rh_motivos_cancelacion.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['motivocan'] . "'
                AND arm.nombre_catalogo = 'MOTIVOS_CANCELACION' AND arm.sucursal = 'MTY' ";
                $_GetCatMotivosCanc = DB_query($_2GetCatMotivosCanc, $db);
                $GetMotivoCanID = DB_fetch_assoc($_GetCatMotivosCanc);
                $Titular['motivocan'] = $GetMotivoCanID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo ESTADOS*/
                $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_estados
                LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['estado'] . "'
                AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                $_GetEstadoID = DB_query($_2GetEstado, $db);
                $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                $Titular['estado'] = $GetEstadoID['estado'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MUNICIPIOS*/
                $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_municipios
                LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['municipio'] . "'
                AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                $_GetMunID = DB_query($_2GetMun, $db);
                $GetMunID = DB_fetch_assoc($_GetMunID);
                $Titular['municipio'] = $GetMunID['municipio'];
                /*************************************************************************************************************/

                /*Se Corrigen desde el Excel*/
                // $Titular['fecha'] = date("Y-m-d", strtotime($Titular['fecha']));
                // $Titular['fechaultau'] = date("Y-m-d", strtotime($Titular['fechaultau']));

                /*Cuando es Empresa en el Excel el nombre viene vacio, le ponemos la Razon Social*/
                if(empty($Titular['nombres'])){
                    $Titular['nombres'] = $Titular['razonsoc'];
                }
                /*Se quitan espacios y Guiones del RFC*/
                $Titular['rfc'] = str_replace(" ", "", $Titular['rfc']);
                $Titular['rfc'] = str_replace("-", "", $Titular['rfc']);

                /*Crear Debtorno*/
                $_Debtor['DebtorNo'] = GetNextTransNo(500, $db);
                $_Debtor['PaymentTerms'] = 30;
                $_Debtor['creditlimit'] = $_SESSION['DefaultCreditLimit'];/*1000*/
                $_Debtor['SalesType'] = "L1";
                $CreateDebtorNo = "INSERT INTO debtorsmaster (
                                                debtorno,
                                                name,
                                                name2,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address9,
                                                address10,
                                                rh_tel,
                                                currcode,
                                                clientsince,
                                                holdreason,
                                                paymentterms,
                                                discount,
                                                discountcode,
                                                pymtdiscount,
                                                creditlimit,
                                                salestype,
                                                invaddrbranch,
                                                taxref,
                                                customerpoline)
                                            VALUES ('" . $_Debtor['DebtorNo'] . "',
                                            '" . DB_escape_string($Titular['nombres'] . " " . $Titular['apellidos']) . "',
                                            '" . DB_escape_string($Titular['razonsoc']) . "',
                                            '" . DB_escape_string($Titular['calle']) . "',
                                            '" . DB_escape_string($Titular['numero']) . "',
                                            '" . DB_escape_string($Titular['address3']) . "',
                                            '" . DB_escape_string($Titular['colonia']) . "',
                                            '" . DB_escape_string($Titular['sector']) . "',
                                            '" . DB_escape_string($Titular['entrecalle']) . "',
                                            '" . DB_escape_string($Titular['municipio']) . "',
                                            '" . DB_escape_string($Titular['estado']) . "',
                                            'MEXICO',
                                            '" . DB_escape_string($Titular['cp']) . "',
                                            '" . $Titular['telefono1'] . "',
                                            'MXN',
                                            '" . $Titular['fecha'] . "',
                                            '" . $Titular['HoldReason'] . "',
                                            '" . $_Debtor['PaymentTerms'] . "',
                                            '" . ($Titular['Discount']) / 100 . "',
                                            '" . $Titular['DiscountCode'] . "',
                                            '" . ($Titular['PymtDiscount']) / 100 . "',
                                            '" . $_Debtor['CreditLimit'] . "',
                                            '" . $_Debtor['SalesType'] . "',
                                            '" . $Titular['AddrInvBranch'] . "',
                                            '" . DB_escape_string($Titular['rfc']) . "',
                                            '" . $Titular['CustomerPOLine'] . "'
                                            )";
                DB_query($CreateDebtorNo, $db);
                /*****************************************************************************************/

                /*Crea Titular*/
                if (!empty($Titular['sincluidos'])) {
                    $_2Titular['sincluidos'] = explode("-", $Titular['sincluidos']);
                    $Titular['sincluidos'] = json_encode($_2Titular['sincluidos'], 1);
                }

                if ($Titular['sexo'] == "M") {
                    $Titular['sexo'] = "MASCULINO";
                } else {
                    $Titular['sexo'] = "FEMENINO";
                }

                switch ($Titular['status']) {
                    case 0 :
                        $Titular['status'] = "Cancelado";
                        break;
                    case 1 :
                        $Titular['status'] = "Activo";
                        break;
                    case 2 :
                        $Titular['status'] = "Cancelado";
                        break;
                    case 3 :
                        $Titular['status'] = "Suspendido";
                        break;
                    default :
                        $Titular['status'] = "Cancelado";
                        break;
                }

                //enum('Activo','Cancelado','Suspendido','Nuevo','Titular')
                $CreateTitular = "insert into rh_titular (folio,
                                        debtorno,
                                        fecha_ingreso,
                                        fecha_ultaum,
                                        tipopersona,
                                        asesor,
                                        apellidos,
                                        name,
                                        sexo,
                                        name2,
                                        nombre_empresa,
                                        taxref,
                                        curp,
                                        email,
                                        contacto,
                                        rh_tel,
                                        rh_tel2,
                                        enfermeria,
                                        costoenfermeria,
                                        serviciolimitado,
                                        serviciosdisponibles,
                                        costo_servicioextra,
                                        address1,
                                        address2,
                                        address3,
                                        address4,
                                        address5,
                                        address6,
                                        address7,
                                        address8,
                                        address10,
                                        cuadrante1,
                                        cuadrante2,
                                        cuadrante3,
                                        orderno,
                                        rh_numproveedor,
                                        servicios_seleccionados,
                                        examenes_laboratorio,
                                        movimientos_afiliacion,
                                        costo_total,
                                        ar_sucursal)
                                        values ('" . $Titular['folio'] . "',
                                        '" . $_Debtor['DebtorNo'] . "',
                                        '" . $Titular['fecha'] . "',
                                        '" . $Titular['fechaultau'] . "',
                                        '" . $Titular['tipoperson'] . "',
                                        '" . $Titular['comisionis'] . "',
                                        '" . DB_escape_string($Titular['apellidos']) . "',
                                        '" . DB_escape_string($Titular['nombres']) . "',
                                        '" . $Titular['sexo'] . "',
                                        '" . DB_escape_string($Titular['razonsoc']) . "',
                                        '" . DB_escape_string($Titular['nomcomerc']) . "',
                                        '" . $Titular['rfc'] . "',
                                        '" . $Titular['curp'] . "',
                                        '" . DB_escape_string($Titular['correo']) . "',
                                        '" . DB_escape_string($Titular['contacto']) . "',
                                        '" . $Titular['telefono1'] . "',
                                        '" . $Titular['telefono2'] . "',
                                        '" . $Titular['enf'] . "',
                                        '" . $Titular['costoenf'] . "',
                                        '" . $Titular['lim_serv'] . "',
                                        '" . $Titular['lim_mes'] . "',
                                        '" . $Titular['lim_costex'] . "',
                                        '" . DB_escape_string($Titular['calle']) . "',
                                        '" . $Titular['numero'] . "',
                                        '" . $Titular['address3'] . "',
                                        '" . DB_escape_string($Titular['colonia']) . "',
                                        '" . $Titular['sector'] . "',
                                        '" . $Titular['entrecalle'] . "',
                                        '" . $Titular['municipio'] . "',
                                        '" . $Titular['estado'] . "',
                                        '" . $Titular['cp'] . "',
                                        '" . $Titular['cuadrante1'] . "',
                                        '" . $Titular['cuadrante2'] . "',
                                        '" . $Titular['cuadrante3'] . "',
                                        '" . $Titular['numoc'] . "',
                                        '" . $Titular['numprov'] . "',
                                        '" . $Titular['sincluidos'] . "',
                                        '" . $Titular['ExamenesLaboratorio'] . "',
                                        '" . $Titular['status'] . "',
                                        '" . $Titular['tarifa'] . "',
                                        'MTY')";
                                        //rh_titular.costo_total = tmp_titular2.tarifa
                //FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');
                DB_query($CreateTitular, $db);

                /*Inserto Branch para Facturar*/
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";

                $CreateBranch = "insert into custbranch ( branchcode,
                                                folio,
                                                brname,
                                                braddress1,
                                                braddress2,
                                                braddress3,
                                                braddress4,
                                                braddress5,
                                                braddress6,
                                                braddress7,
                                                braddress8,
                                                braddress10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                sexo,
                                                nombre_empresa,
                                                fecha_ingreso,
                                                fecha_ultaum,
                                                phoneno,
                                                movimientos_socios,
                                                debtorno,
                                                area,
                                                salesman,
                                                defaultlocation,
                                                defaultshipvia,
                                                taxgroupid,
                                                rh_status_captura
                                                )
                                                values ('T-" . $_Debtor['DebtorNo'] . "',
                                                '" . $Titular['folio'] . "',
                                                '" . DB_escape_string($Titular['nombres'] . " " . $Titular['apellidos']) . "',
                                                '" . DB_escape_string($Titular['calle']) . "',
                                                '" . $Titular['numero'] . "',
                                                '" . $Titular['address3'] . "',
                                                '" . DB_escape_string($Titular['colonia']) . "',
                                                '" . $Titular['sector'] . "',
                                                '" . $Titular['entrecalle'] . "',
                                                '" . $Titular['municipio'] . "',
                                                '" . $Titular['estado'] . "',
                                                '" . $Titular['cp'] . "',
                                                '" . $Titular['cuadrante1'] . "',
                                                '" . $Titular['cuadrante2'] . "',
                                                '" . $Titular['cuadrante3'] . "',
                                                '" . $Titular['sexo'] . "',
                                                '" . DB_escape_string($Titular['razonsoc']) . "',
                                                '" . $Titular['fecha'] . "',
                                                '" . $Titular['fechaultau'] . "',
                                                '" . $Titular['telefono1'] . "',
                                                'Titular',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . $Area . "',
                                                '" . $Salesman . "',
                                                '" . $DefaultLocation . "',
                                                '" . $DefaultShipVia . "',
                                                '" . $TaxGroupid . "',
                                                'Titular'
                                                )";
                DB_query($CreateBranch, $db);
                /**************************************************************************************************/






                /*************************Busca Registro de Cobranza y lo Inserta**********************************/

                $SQLQueryCobranza = "SELECT * FROM tmp_cobranza WHERE folio = " . $Titular['folio'];
                $GetCobranza = DB_query($SQLQueryCobranza, $db);
                $Cobranza = DB_fetch_assoc($GetCobranza);

                if (!empty($Cobranza)) {
                    /* Traduccion de Catalogo CONVENIOS*/
                     $_2GetCatConvenios = "SELECT rh_convenios.id, rh_convenios.convenio, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_convenios
                    LEFT JOIN ar_match arm on rh_convenios.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cobranza['nconvenio'] . "'
                    AND arm.nombre_catalogo = 'CONVENIOS' AND arm.sucursal = 'MTY' ";
                    $_GetCatConvenios = DB_query($_2GetCatConvenios, $db);
                    $GetConvenioID = DB_fetch_assoc($_GetCatConvenios);
                    $Cobranza['nconvenio'] = $GetConvenioID['rh_id'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo FRECUENCIA_PAGO*/
                    $_2GetCatFrecPagos = "SELECT rh_frecuenciapago.id, rh_frecuenciapago.frecuencia, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_frecuenciapago
                    LEFT JOIN ar_match arm on rh_frecuenciapago.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cobranza['tipopago'] . "'
                    AND arm.nombre_catalogo = 'FRECUENCIA_PAGO' AND arm.sucursal = 'MTY' ";
                    $_GetCatCobradores = DB_query($_2GetCatFrecPagos, $db);
                    $GetCobradorID = DB_fetch_assoc($_GetCatCobradores);
                    $Cobranza['tipopago'] = $GetCobradorID['rh_id'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo COBRADORES*/
                    $_2GetCatCobradores = "SELECT rh_cobradores.id, rh_cobradores.nombre, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_cobradores
                    LEFT JOIN ar_match arm on rh_cobradores.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cobranza['cobrador'] . "'
                    AND arm.nombre_catalogo = 'COBRADORES' AND arm.sucursal = 'MTY' ";
                    $_GetCatCobradores = DB_query($_2GetCatCobradores, $db);
                    $GetCobradorID = DB_fetch_assoc($_GetCatCobradores);
                    $Cobranza['cobrador'] = $GetCobradorID['rh_id'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo ESTADOS*/
                    $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_estados
                    LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cobranza['estado'] . "'
                    AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                    $_GetEstadoID = DB_query($_2GetEstado, $db);
                    $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                    $Cobranza['estado'] = $GetEstadoID['estado'];
                    /*************************************************************************************************************/

                    /* Traduccion de Catalogo MUNICIPIOS*/
                    $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_municipios
                    LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                    WHERE arm.ar_id = '" . $Cobranza['municipio'] . "'
                    AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                    $_GetMunID = DB_query($_2GetMun, $db);
                    $GetMunID = DB_fetch_assoc($_GetMunID);
                    $Cobranza['municipio'] = $GetMunID['municipio'];
                    /*************************************************************************************************************/

                    $CreateCobranza = "insert into rh_cobranza (folio,
                                                debtorno,
                                                address1,
                                                address2,
                                                address3,
                                                address4,
                                                address5,
                                                address6,
                                                address7,
                                                address8,
                                                address10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                rh_tel,
                                                rh_tel2,
                                                email,
                                                enviar_factura,
                                                encargado_pagos,
                                                stockid,
                                                empresa,
                                                frecuencia_pago,
                                                convenio,
                                                loccode,
                                                paymentid,
                                                zona,
                                                cobrador,
                                                cuenta,
                                                vencimiento,
                                                cuenta_sat,
                                                num_plastico,
                                                metodo_pago,
                                                tipo_tarjeta,
                                                tipo_cuenta,
                                                num_empleado,
                                                identificacion,
                                                fecha_corte,
                                                factura_fisica,
                                                folio_asociado,
                                                dias_cobro,
                                                dias_cobro_dia,
                                                cobro_datefrom,
                                                cobro_dateto,
                                                dias_credito,
                                                dias_revision,
                                                dias_revision_dia,
                                                revision_datefrom,
                                                revision_dateto,
                                                cobro_inscripcion,
                                                sm_clavefiliacion,
                                                sm_depto,
                                                sm_cpresupuestal,
                                                sm_vigencia)
                                                values ('" . $Titular['folio'] . "',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . DB_escape_string($Cobranza['calle']) . "',
                                                '" . $Cobranza['numero'] . "',
                                                '" . $Cobranza['address3'] . "',
                                                '" . DB_escape_string($Cobranza['colonia']) . "',
                                                '" . $Cobranza['sector'] . "',
                                                '" . $Cobranza['entrecalle'] . "',
                                                '" . $Cobranza['municipio'] . "',
                                                '" . $Cobranza['estado'] . "',
                                                '" . $Cobranza['cp'] . "',
                                                '" . $Cobranza['cuadrante1'] . "',
                                                '" . $Cobranza['cuadrante2'] . "',
                                                '" . $Cobranza['cuadrante3'] . "',
                                                '" . $Cobranza['telefono1'] . "',
                                                '" . $Cobranza['telefono2'] . "',
                                                '" . $Cobranza['correocob'] . "',
                                                '" . $Cobranza['EnviaFacturaPorCorreo'] . "',
                                                '" . $Cobranza['contpaga'] . "',
                                                '" . $Titular['producto'] . "',
                                                '" . $Titular['empresa'] . "',
                                                '" . $Cobranza['tipopago'] . "',
                                                '" . $Cobranza['nconvenio'] . "',
                                                '" . $Titular['sucursal'] . "',
                                                '" . $Cobranza['formapago'] . "',
                                                '" . $Cobranza['Zona'] . "',
                                                '" . $Cobranza['cobrador'] . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['ncuenta']) . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['vence']) . "',
                                                '" . $this->OpenSSLEncrypt($Titular['cuenta_sat']) . "',
                                                '" . $this->OpenSSLEncrypt($Cobranza['nplastico']) . "',
                                                '" . $Cobranza['formapago'] . "',
                                                '" . $Cobranza['ntipotarj'] . "',
                                                '" . $Cobranza['tipocuenta'] . "',
                                                '" . $Titular['nempleado'] . "',
                                                '" . $Titular['identifica'] . "',
                                                '" . $Cobranza['FechaCorte'] . "',
                                                '" . $Titular['facfisica'] . "',
                                                '" . $Titular['folioas'] . "',
                                                '" . $Cobranza['tipocobro'] . "',
                                                '" . $Cobranza['diacobro'] . "',
                                                '" . $Cobranza['horicob'] . "',
                                                '" . $Cobranza['horfcob'] . "',
                                                '" . $Cobranza['DiasDeCredito'] . "',
                                                '" . $Cobranza['tiporev'] . "',
                                                '" . $Cobranza['diarev'] . "',
                                                '" . $Cobranza['horirev'] . "',
                                                '" . $Cobranza['horfrev'] . "',
                                                '0',
                                                '" . $Cobranza['filia'] . "',
                                                '" . $Cobranza['depto'] . "',
                                                '" . $Cobranza['clavepres'] . "',
                                                '" . $Cobranza['viginimes'] . '-' . $Cobranza['viginiano'] . "'
                                                )";
                    DB_query($CreateCobranza, $db);
                }
                //tipopago = FrecuenciaPago






                ///////////////////////////////////////////////////////////////////
                $Socios = array();
                $SQLQuerySocios = "SELECT * FROM tmp_socios WHERE folio = " . $Titular['folio'];
                $GetSocios = DB_query($SQLQuerySocios, $db);
                while (($_2Socios = DB_fetch_assoc($GetSocios))) {
                    $Socios[] = $_2Socios;
                }

                if (!empty($Socios)) {
                    $branch = 1;
                    foreach ($Socios as $Socio) {

                        //$Socio['fecnac'] = date("Y-m-d", strtotime($Socio['fecnac']));
                        //$Socio['fecha'] = date("Y-m-d", strtotime($Socio['fecha']));
                        //$Socio['fechaultau'] = date("Y-m-d", strtotime($Socio['fechaultau']));
                        if($Socio['sexo'] == 1){
                            $Socio['sexo'] = 'MASCULINO';
                        }else{
                            $Socio['sexo'] = 'FEMENINO';
                        }


                        /* Traduccion de Catalogo ESTADOS*/
                        $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_estados
                        LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                        WHERE arm.ar_id = '" . $Socio['IdEstado'] . "'
                        AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                        $_GetEstadoID = DB_query($_2GetEstado, $db);
                        $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                        $Socio['IdEstado'] = $GetEstadoID['estado'];
                        /*************************************************************************************************************/

                        /* Traduccion de Catalogo MUNICIPIOS*/
                        $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo FROM rh_municipios
                        LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                        WHERE arm.ar_id = '" . $Socio['municipio'] . "'
                        AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                        $_GetMunID = DB_query($_2GetMun, $db);
                        $GetMunID = DB_fetch_assoc($_GetMunID);
                        $Socio['municipio'] = $GetMunID['municipio'];
                        /*************************************************************************************************************/

                        if(empty($Socio['nombres'])){
                            $Socio['nombres'] = $Socio['nomcomerc'];
                        }

                        switch ($Socio['estatus']) {
                            case 1 :
                                $Socio['estatus'] = "Activo";
                                break;
                            case 2 :
                                $Socio['estatus'] = "Cancelado";
                                break;
                            case 3 :
                                $Socio['estatus'] = "Suspendido";
                                break;
                            default :
                                $Socio['estatus'] = "Activo";
                                break;
                        }

                        $Area = "MTY";
                        $Salesman = "2RC";
                        $DefaultLocation = "MTY";
                        $DefaultShipVia = "1";
                        $TaxGroupid = "4";

                        $CreateSocio = "insert into custbranch ( branchcode,
                                                folio,
                                                brname,
                                                braddress1,
                                                braddress2,
                                                braddress3,
                                                braddress4,
                                                braddress5,
                                                braddress6,
                                                braddress7,
                                                braddress8,
                                                braddress10,
                                                cuadrante1,
                                                cuadrante2,
                                                cuadrante3,
                                                sexo,
                                                nombre_empresa,
                                                fecha_nacimiento,
                                                fecha_ingreso,
                                                fecha_ultaum,
                                                phoneno,
                                                antecedentes_clinicos,
                                                movimientos_socios,
                                                debtorno,
                                                area,
                                                salesman,
                                                defaultlocation,
                                                defaultshipvia,
                                                taxgroupid,
                                                rh_status_captura
                                                )
                                                values ('" . $branch . "',
                                                '" . $Titular['folio'] . "',
                                                '" . DB_escape_string($Socio['nombres'] . " " . $Socio['apellidos']) . "',
                                                '" . DB_escape_string($Socio['calle']) . "',
                                                '" . $Socio['numero'] . "',
                                                '" . $Socio['braddress3'] . "',
                                                '" . DB_escape_string($Socio['colonia']) . "',
                                                '" . $Socio['sector'] . "',
                                                '" . $Socio['entrecalle'] . "',
                                                '" . $Socio['municipio'] . "',
                                                '" . $Socio['IdEstado'] . "',
                                                '" . $Socio['cp'] . "',
                                                '" . $Socio['cuadrante1'] . "',
                                                '" . $Socio['cuadrante2'] . "',
                                                '" . $Socio['cuadrante3'] . "',
                                                '" . $Socio['sexo'] . "',
                                                '" . DB_escape_string($Socio['nomcomerc']) . "',
                                                '" . $Socio['fecnac'] . "',
                                                '" . $Socio['fecha'] . "',
                                                '" . $Socio['fechaultau'] . "',
                                                '" . $Socio['telefono'] . "',
                                                '" . DB_escape_string($_POST['antecedentes_clinicos']) . "',
                                                '" . $Socio['estatus'] . "',
                                                '" . $_Debtor['DebtorNo'] . "',
                                                '" . $Area . "',
                                                '" . $Salesman . "',
                                                '" . $DefaultLocation . "',
                                                '" . $DefaultShipVia . "',
                                                '" . $TaxGroupid . "',
                                                'Activo'
                                                )";
                        DB_query($CreateSocio, $db);
                        $branch++;
                    }/*END SOCIOS FOREACH*/
                }
                /*END ID*/
                //Actualiza el status a 1 para q no vuelva a tomar este registro.
                $SQLCheckFolio = "UPDATE tmp_titular SET rh_sinc = 1 WHERE folio = " . $Titular['folio'];
                $_2CheckFolio = DB_query($SQLCheckFolio, $db);
            }/*END FOREACH*/
        }
        $this->render('sincroniza');
    }






/*CORRECCIONES A rh_titular, DebtorsMaster, CustBranch*/

    public function actionLoadcorreccionclientes(){

        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if ($_GET['Process']==true) {

            $SQLQueryClientes = "SELECT * FROM tmp_clientes WHERE rh_sinc = 0 LIMIT 1000 ";
            $GetClientes = DB_query($SQLQueryClientes, $db);
            while (($_2Clientes = DB_fetch_assoc($GetClientes))) {
                $SQLClientes[] = $_2Clientes;
            }
            foreach ($SQLClientes as $Cliente) {

                if(empty($Cliente['nombres'])){
                    $Cliente['nombres'] = $Cliente['razonsoc'];
                }

                /* Traduccion de Catalogo ESTADOS*/
                $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_estados
                LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                WHERE arm.ar_id = '" . $Cliente['estado'] . "'
                AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                $_GetEstadoID = DB_query($_2GetEstado, $db);
                $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                $Cliente['estado'] = $GetEstadoID['estado'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MUNICIPIOS*/
                $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_municipios
                LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                WHERE arm.ar_id = '" . $Cliente['municipio'] . "'
                AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                $_GetMunID = DB_query($_2GetMun, $db);
                $GetMunID = DB_fetch_assoc($_GetMunID);
                $Cliente['municipio'] = $GetMunID['municipio'];
                /*************************************************************************************************************/

                $Cliente['fecha'] = date("Y-m-d", strtotime($Cliente['fecha']));
                /*Crear Debtorno*/
                //$_Debtor['DebtorNo'] = GetNextTransNo(500, $db);
                $_Debtor['DebtorNo'] = $this->GetDebtorNo2($Cliente['Folio']);
                FB::INFO($_Debtor,'_____________________DEBTORNO');

                /*Valido q no exista el Debtorno*/
                $_SQLGetDebtorNo = "SELECT debtorno FROM rh_titular WHERE debtorno = '{$_Debtor['DebtorNo']}'";
                $__2GetDebtorNo = DB_query($_SQLGetDebtorNo, $db);
                $ExistDebtorNo = DB_fetch_assoc($__2GetDebtorNo);


                $_Debtor['PaymentTerms'] = 30;
                $_Debtor['creditlimit'] = $_SESSION['DefaultCreditLimit'];/*1000*/
                $_Debtor['SalesType'] = "L1";


                if(!empty($_Debtor['DebtorNo']) && empty($ExistDebtorNo)){


                    $CreateDebtorNo = "INSERT INTO debtorsmaster (
                                                    debtorno,
                                                    name,
                                                    name2,
                                                    address1,
                                                    address2,
                                                    address3,
                                                    address4,
                                                    address5,
                                                    address6,
                                                    address7,
                                                    address8,
                                                    address9,
                                                    address10,
                                                    rh_tel,
                                                    currcode,
                                                    clientsince,
                                                    holdreason,
                                                    paymentterms,
                                                    discount,
                                                    discountcode,
                                                    pymtdiscount,
                                                    creditlimit,
                                                    salestype,
                                                    invaddrbranch,
                                                    taxref,
                                                    customerpoline)
                                                VALUES ('" . $_Debtor['DebtorNo'] . "',
                                                '" . DB_escape_string($Cliente['nombres'] . " " . $Cliente['apellidos']) . "',
                                                '" . DB_escape_string($Cliente['razonsoc']) . "',
                                                '" . DB_escape_string($Cliente['calle']) . "',
                                                '" . DB_escape_string($Cliente['numero']) . "',
                                                '" . DB_escape_string($Cliente['address3']) . "',
                                                '" . DB_escape_string($Cliente['colonia']) . "',
                                                '" . DB_escape_string($Cliente['sector']) . "',
                                                '" . DB_escape_string($Cliente['entrecalle']) . "',
                                                '" . DB_escape_string($Cliente['municipio']) . "',
                                                '" . DB_escape_string($Cliente['estado']) . "',
                                                'MEXICO',
                                                '" . DB_escape_string($Titular['cp']) . "',
                                                '" . $Titular['telefono'] . "',
                                                'MXN',
                                                '" . $Titular['fecha'] . "',
                                                '" . $_Debtor['HoldReason'] . "',
                                                '" . $_Debtor['PaymentTerms'] . "',
                                                '" . ($_Debtor['Discount']) / 100 . "',
                                                '" . $_Debtor['DiscountCode'] . "',
                                                '" . ($_Debtor['PymtDiscount']) / 100 . "',
                                                '" . $_Debtor['CreditLimit'] . "',
                                                '" . $_Debtor['SalesType'] . "',
                                                '" . $_Debtor['AddrInvBranch'] . "',
                                                '" . DB_escape_string($Titular['rfc']) . "',
                                                '" . $_Debtor['CustomerPOLine'] . "'
                                                )";
                    FB::INFO($CreateDebtorNo,'__________________--$CreateDebtorNo');
                    DB_query($CreateDebtorNo, $db);
                    /*Crea Titular*/

                    //enum('Activo','Cancelado','Suspendido','Nuevo','Titular')
                    $CreateTitular = "insert into rh_titular (folio,
                                            debtorno,
                                            fecha_ingreso,
                                            fecha_ultaum,
                                            tipopersona,
                                            asesor,
                                            apellidos,
                                            name,
                                            sexo,
                                            name2,
                                            nombre_empresa,
                                            taxref,
                                            curp,
                                            email,
                                            contacto,
                                            rh_tel,
                                            rh_tel2,
                                            enfermeria,
                                            costoenfermeria,
                                            serviciolimitado,
                                            serviciosdisponibles,
                                            costo_servicioextra,
                                            address1,
                                            address2,
                                            address3,
                                            address4,
                                            address5,
                                            address6,
                                            address7,
                                            address8,
                                            address10,
                                            cuadrante1,
                                            cuadrante2,
                                            cuadrante3,
                                            orderno,
                                            rh_numproveedor,
                                            servicios_seleccionados,
                                            examenes_laboratorio,
                                            movimientos_afiliacion,
                                            costo_total,
                                            ar_sucursal,
                                            rh_eventual)
                                            values ('" . $Cliente['Folio'] . "',
                                            '" . $_Debtor['DebtorNo'] . "',
                                            '" . $Cliente['fecha'] . "',
                                            '" . $Cliente['fechaultau'] . "',
                                            '" . $Cliente['tipoperson'] . "',
                                            '" . $Cliente['asesor'] . "',
                                            '" . DB_escape_string($Cliente['apellidos']) . "',
                                            '" . DB_escape_string($Cliente['nombres']) . "',
                                            '" . $Cliente['sexo'] . "',
                                            '" . DB_escape_string($Cliente['razonsoc']) . "',
                                            '" . DB_escape_string($Cliente['nomcomerc']) . "',
                                            '" . $Cliente['rfc'] . "',
                                            '" . $Cliente['curp'] . "',
                                            '" . DB_escape_string($Cliente['correo']) . "',
                                            '" . DB_escape_string($Cliente['contacto']) . "',
                                            '" . $Cliente['telefono'] . "',
                                            '" . $Cliente['telefono'] . "',
                                            '" . $Cliente['enf'] . "',
                                            '" . $Cliente['costoenf'] . "',
                                            '" . $Cliente['lim_serv'] . "',
                                            '" . $Cliente['lim_mes'] . "',
                                            '" . $Cliente['lim_costex'] . "',
                                            '" . DB_escape_string($Cliente['calle']) . "',
                                            '" . $Cliente['numero'] . "',
                                            '" . $Cliente['address3'] . "',
                                            '" . DB_escape_string($Cliente['colonia']) . "',
                                            '" . $Cliente['sector'] . "',
                                            '" . $Cliente['entrecalle'] . "',
                                            '" . $Cliente['municipio'] . "',
                                            '" . $Cliente['estado'] . "',
                                            '" . $Cliente['cp'] . "',
                                            '" . $Cliente['cuadrante1'] . "',
                                            '" . $Cliente['cuadrante2'] . "',
                                            '" . $Cliente['cuadrante3'] . "',
                                            '" . $Cliente['numoc'] . "',
                                            '" . $Cliente['numprov'] . "',
                                            '" . $Cliente['sincluidos'] . "',
                                            '" . $Cliente['ExamenesLaboratorio'] . "',
                                            '" . $Cliente['status'] . "',
                                            '" . $Cliente['tarifa'] . "',
                                            'MTY',
                                            1)";
                                            //rh_titular.costo_total = tmp_titular2.tarifa
                    FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');
                    DB_query($CreateTitular, $db);

                    /*Inserto Branch para Facturar*/
                    $Area = "MTY";
                    $Salesman = "2RC";
                    $DefaultLocation = "MTY";
                    $DefaultShipVia = "1";
                    $TaxGroupid = "4";

                    $CreateBranch = "insert into custbranch ( branchcode,
                                                    folio,
                                                    brname,
                                                    braddress1,
                                                    braddress2,
                                                    braddress3,
                                                    braddress4,
                                                    braddress5,
                                                    braddress6,
                                                    braddress7,
                                                    braddress8,
                                                    braddress10,
                                                    cuadrante1,
                                                    cuadrante2,
                                                    cuadrante3,
                                                    sexo,
                                                    nombre_empresa,
                                                    fecha_ingreso,
                                                    fecha_ultaum,
                                                    phoneno,
                                                    movimientos_socios,
                                                    debtorno,
                                                    area,
                                                    salesman,
                                                    defaultlocation,
                                                    defaultshipvia,
                                                    taxgroupid,
                                                    rh_status_captura
                                                    )
                                                    values ('T-" . $_Debtor['DebtorNo'] . "',
                                                    '" . $Cliente['Folio'] . "',
                                                    '" . DB_escape_string($Cliente['nombres'] . " " . $Cliente['apellidos']) . "',
                                                    '" . DB_escape_string($Cliente['calle']) . "',
                                                    '" . $Cliente['numero'] . "',
                                                    '" . $Cliente['address3'] . "',
                                                    '" . DB_escape_string($Cliente['colonia']) . "',
                                                    '" . $Cliente['sector'] . "',
                                                    '" . $Cliente['entrecalle'] . "',
                                                    '" . $Cliente['municipio'] . "',
                                                    '" . $Cliente['estado'] . "',
                                                    '" . $Cliente['cp'] . "',
                                                    '" . $Cliente['cuadrante1'] . "',
                                                    '" . $Cliente['cuadrante2'] . "',
                                                    '" . $Cliente['cuadrante3'] . "',
                                                    '" . $Cliente['sexo'] . "',
                                                    '" . DB_escape_string($Cliente['razonsoc']) . "',
                                                    '" . $Cliente['fecha'] . "',
                                                    '" . $Cliente['fechaultau'] . "',
                                                    '" . $Cliente['telefono'] . "',
                                                    'Titular',
                                                    '" . $_Debtor['DebtorNo'] . "',
                                                    '" . $Area . "',
                                                    '" . $Salesman . "',
                                                    '" . $DefaultLocation . "',
                                                    '" . $DefaultShipVia . "',
                                                    '" . $TaxGroupid . "',
                                                    'Titular'
                                                    )";
                    FB::INFO($CreateBranch,'________________$CreateBranch');
                    DB_query($CreateBranch, $db);

                    /**************************************************************************************************/


                    $SQLCheckFolio = "UPDATE tmp_clientes SET rh_sinc = 1 WHERE Folio = " . $Cliente['Folio'];
                    DB_query($SQLCheckFolio, $db);
                }//end if
            }//End Foreach Cliente

        }
        $this->render('test');

    }

/*CORRECCIONES A rh_titular, DebtorsMaster, CustBranch*/

    /**
     * @Todo
     *
     * Inserta en debtorsmaster, custbranch, rh_titular, rh_cobranza
     * #NoMover
     * */
    public function actionCorrecciontitular() {
        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if ($_GET['Process']==true) {

            $SQLQueryTitular = "SELECT * FROM tmp_titular WHERE rh_sinc = 0 LIMIT 1000 ";
            $GetTitular = DB_query($SQLQueryTitular, $db);
            while (($_2Titular = DB_fetch_assoc($GetTitular))) {
                $SQLTitular[] = $_2Titular;
            }

            foreach ($SQLTitular as $Titular) {
                /* Traduccion de Catalogo COMISIONISTAS*/
                $_2GetCatEmpresas = "SELECT rh_comisionistas.id, rh_comisionistas.comisionista, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_comisionistas
                LEFT JOIN ar_match arm on rh_comisionistas.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['comisionista'] . "'
                AND arm.nombre_catalogo = 'COMISIONISTAS' AND arm.sucursal = 'MTY' ";
                $_GetCatEmpresas = DB_query($_2GetCatEmpresas, $db);
                $GetEmpresaID = DB_fetch_assoc($_GetCatEmpresas);
                $Titular['comisionis'] = $GetEmpresaID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo EMPRESAS*/
                $_2GetCatEmpresas = "SELECT rh_empresas.id, rh_empresas.empresa, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_empresas
                LEFT JOIN ar_match arm on rh_empresas.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['empresa'] . "'
                AND arm.nombre_catalogo = 'EMPRESAS' AND arm.sucursal = 'MTY' ";
                $_GetCatEmpresas = DB_query($_2GetCatEmpresas, $db);
                $GetEmpresaID = DB_fetch_assoc($_GetCatEmpresas);
                $Titular['empresa'] = $GetEmpresaID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MOTIVOS_CANCELACION*/
                $_2GetCatMotivosCanc = "SELECT rh_motivos_cancelacion.id, rh_motivos_cancelacion.motivo, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_motivos_cancelacion
                LEFT JOIN ar_match arm on rh_motivos_cancelacion.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['motivocan'] . "'
                AND arm.nombre_catalogo = 'MOTIVOS_CANCELACION' AND arm.sucursal = 'MTY' ";
                $_GetCatMotivosCanc = DB_query($_2GetCatMotivosCanc, $db);
                $GetMotivoCanID = DB_fetch_assoc($_GetCatMotivosCanc);
                $Titular['motivocan'] = $GetMotivoCanID['rh_id'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo ESTADOS*/
                $_2GetEstado = "SELECT rh_estados.id, rh_estados.estado, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_estados
                LEFT JOIN ar_match arm on rh_estados.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['estado'] . "'
                AND arm.nombre_catalogo = 'ESTADOS' AND arm.sucursal = 'MTY' ";
                $_GetEstadoID = DB_query($_2GetEstado, $db);
                $GetEstadoID = DB_fetch_assoc($_GetEstadoID);
                $Titular['estado'] = $GetEstadoID['estado'];
                /*************************************************************************************************************/

                /* Traduccion de Catalogo MUNICIPIOS*/
                $_2GetMun = "SELECT rh_municipios.id, rh_municipios.municipio, arm.rh_id,arm.ar_id, arm.nombre_catalogo
                FROM rh_municipios
                LEFT JOIN ar_match arm on rh_municipios.id = arm.rh_id
                WHERE arm.ar_id = '" . $Titular['municipio'] . "'
                AND arm.nombre_catalogo = 'MUNICIPIOS' AND arm.sucursal = 'MTY' ";
                $_GetMunID = DB_query($_2GetMun, $db);
                $GetMunID = DB_fetch_assoc($_GetMunID);
                $Titular['municipio'] = $GetMunID['municipio'];
                /*************************************************************************************************************/

                /*Se Corrigen desde el Excel*/
                // $Titular['fecha'] = date("Y-m-d", strtotime($Titular['fecha']));
                // $Titular['fechaultau'] = date("Y-m-d", strtotime($Titular['fechaultau']));

                /*Cuando es Empresa en el Excel el nombre viene vacio, le ponemos la Razon Social*/
                if(empty($Titular['nombres'])){
                    $Titular['nombres'] = $Titular['razonsoc'];
                }
                /*Se quitan espacios y Guiones del RFC*/
                $Titular['rfc'] = str_replace(" ", "", $Titular['rfc']);
                $Titular['rfc'] = str_replace("-", "", $Titular['rfc']);

                /*Crear Debtorno*/
                //$_Debtor['DebtorNo'] = GetNextTransNo(500, $db);
                $_Debtor['DebtorNo'] = $this->GetDebtorNo2($Titular['folio']);
                FB::INFO($_Debtor,'_____________________DEBTORNO');
                $_Debtor['PaymentTerms'] = 30;
                $_Debtor['creditlimit'] = $_SESSION['DefaultCreditLimit'];/*1000*/
                $_Debtor['SalesType'] = "L1";


                if(!empty($_Debtor['DebtorNo'])){


                    $CreateDebtorNo = "INSERT INTO debtorsmaster (
                                                    debtorno,
                                                    name,
                                                    name2,
                                                    address1,
                                                    address2,
                                                    address3,
                                                    address4,
                                                    address5,
                                                    address6,
                                                    address7,
                                                    address8,
                                                    address9,
                                                    address10,
                                                    rh_tel,
                                                    currcode,
                                                    clientsince,
                                                    holdreason,
                                                    paymentterms,
                                                    discount,
                                                    discountcode,
                                                    pymtdiscount,
                                                    creditlimit,
                                                    salestype,
                                                    invaddrbranch,
                                                    taxref,
                                                    customerpoline)
                                                VALUES ('" . $_Debtor['DebtorNo'] . "',
                                                '" . DB_escape_string($Titular['nombres'] . " " . $Titular['apellidos']) . "',
                                                '" . DB_escape_string($Titular['razonsoc']) . "',
                                                '" . DB_escape_string($Titular['calle']) . "',
                                                '" . DB_escape_string($Titular['numero']) . "',
                                                '" . DB_escape_string($Titular['address3']) . "',
                                                '" . DB_escape_string($Titular['colonia']) . "',
                                                '" . DB_escape_string($Titular['sector']) . "',
                                                '" . DB_escape_string($Titular['entrecalle']) . "',
                                                '" . DB_escape_string($Titular['municipio']) . "',
                                                '" . DB_escape_string($Titular['estado']) . "',
                                                'MEXICO',
                                                '" . DB_escape_string($Titular['cp']) . "',
                                                '" . $Titular['telefono1'] . "',
                                                'MXN',
                                                '" . $Titular['fecha'] . "',
                                                '" . $Titular['HoldReason'] . "',
                                                '" . $_Debtor['PaymentTerms'] . "',
                                                '" . ($Titular['Discount']) / 100 . "',
                                                '" . $Titular['DiscountCode'] . "',
                                                '" . ($Titular['PymtDiscount']) / 100 . "',
                                                '" . $_Debtor['CreditLimit'] . "',
                                                '" . $_Debtor['SalesType'] . "',
                                                '" . $Titular['AddrInvBranch'] . "',
                                                '" . DB_escape_string($Titular['rfc']) . "',
                                                '" . $Titular['CustomerPOLine'] . "'
                                                )";
                    //FB::INFO($CreateDebtorNo,'_________________________$CreateDebtorNo');
                    DB_query($CreateDebtorNo, $db);
                    /*****************************************************************************************/

                    /*Crea Titular*/
                    if (!empty($Titular['sincluidos'])) {
                        $_2Titular['sincluidos'] = explode("-", $Titular['sincluidos']);
                        $Titular['sincluidos'] = json_encode($_2Titular['sincluidos'], 1);
                    }

                    if ($Titular['sexo'] == "M") {
                        $Titular['sexo'] = "MASCULINO";
                    } else {
                        $Titular['sexo'] = "FEMENINO";
                    }

                    switch ($Titular['status']) {
                        case 0 :
                            $Titular['status'] = "Cancelado";
                            break;
                        case 1 :
                            $Titular['status'] = "Activo";
                            break;
                        case 2 :
                            $Titular['status'] = "Cancelado";
                            break;
                        case 3 :
                            $Titular['status'] = "Suspendido";
                            break;
                        default :
                            $Titular['status'] = "Cancelado";
                            break;
                    }

                    //enum('Activo','Cancelado','Suspendido','Nuevo','Titular')
                    $CreateTitular = "insert into rh_titular (folio,
                                            debtorno,
                                            fecha_ingreso,
                                            fecha_ultaum,
                                            tipopersona,
                                            asesor,
                                            apellidos,
                                            name,
                                            sexo,
                                            name2,
                                            nombre_empresa,
                                            taxref,
                                            curp,
                                            email,
                                            contacto,
                                            rh_tel,
                                            rh_tel2,
                                            enfermeria,
                                            costoenfermeria,
                                            serviciolimitado,
                                            serviciosdisponibles,
                                            costo_servicioextra,
                                            address1,
                                            address2,
                                            address3,
                                            address4,
                                            address5,
                                            address6,
                                            address7,
                                            address8,
                                            address10,
                                            cuadrante1,
                                            cuadrante2,
                                            cuadrante3,
                                            orderno,
                                            rh_numproveedor,
                                            servicios_seleccionados,
                                            examenes_laboratorio,
                                            movimientos_afiliacion,
                                            costo_total,
                                            ar_sucursal)
                                            values ('" . $Titular['folio'] . "',
                                            '" . $_Debtor['DebtorNo'] . "',
                                            '" . $Titular['fecha'] . "',
                                            '" . $Titular['fechaultau'] . "',
                                            '" . $Titular['tipoperson'] . "',
                                            '" . $Titular['comisionis'] . "',
                                            '" . DB_escape_string($Titular['apellidos']) . "',
                                            '" . DB_escape_string($Titular['nombres']) . "',
                                            '" . $Titular['sexo'] . "',
                                            '" . DB_escape_string($Titular['razonsoc']) . "',
                                            '" . DB_escape_string($Titular['nomcomerc']) . "',
                                            '" . $Titular['rfc'] . "',
                                            '" . $Titular['curp'] . "',
                                            '" . DB_escape_string($Titular['correo']) . "',
                                            '" . DB_escape_string($Titular['contacto']) . "',
                                            '" . $Titular['telefono1'] . "',
                                            '" . $Titular['telefono2'] . "',
                                            '" . $Titular['enf'] . "',
                                            '" . $Titular['costoenf'] . "',
                                            '" . $Titular['lim_serv'] . "',
                                            '" . $Titular['lim_mes'] . "',
                                            '" . $Titular['lim_costex'] . "',
                                            '" . DB_escape_string($Titular['calle']) . "',
                                            '" . $Titular['numero'] . "',
                                            '" . $Titular['address3'] . "',
                                            '" . DB_escape_string($Titular['colonia']) . "',
                                            '" . $Titular['sector'] . "',
                                            '" . $Titular['entrecalle'] . "',
                                            '" . $Titular['municipio'] . "',
                                            '" . $Titular['estado'] . "',
                                            '" . $Titular['cp'] . "',
                                            '" . $Titular['cuadrante1'] . "',
                                            '" . $Titular['cuadrante2'] . "',
                                            '" . $Titular['cuadrante3'] . "',
                                            '" . $Titular['numoc'] . "',
                                            '" . $Titular['numprov'] . "',
                                            '" . $Titular['sincluidos'] . "',
                                            '" . $Titular['ExamenesLaboratorio'] . "',
                                            '" . $Titular['status'] . "',
                                            '" . $Titular['tarifa'] . "',
                                            'MTY')";
                                            //rh_titular.costo_total = tmp_titular2.tarifa
                    //FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');
                    DB_query($CreateTitular, $db);

                    /*Inserto Branch para Facturar*/
                    $Area = "MTY";
                    $Salesman = "2RC";
                    $DefaultLocation = "MTY";
                    $DefaultShipVia = "1";
                    $TaxGroupid = "4";

                    $CreateBranch = "insert into custbranch ( branchcode,
                                                    folio,
                                                    brname,
                                                    braddress1,
                                                    braddress2,
                                                    braddress3,
                                                    braddress4,
                                                    braddress5,
                                                    braddress6,
                                                    braddress7,
                                                    braddress8,
                                                    braddress10,
                                                    cuadrante1,
                                                    cuadrante2,
                                                    cuadrante3,
                                                    sexo,
                                                    nombre_empresa,
                                                    fecha_ingreso,
                                                    fecha_ultaum,
                                                    phoneno,
                                                    movimientos_socios,
                                                    debtorno,
                                                    area,
                                                    salesman,
                                                    defaultlocation,
                                                    defaultshipvia,
                                                    taxgroupid,
                                                    rh_status_captura
                                                    )
                                                    values ('T-" . $_Debtor['DebtorNo'] . "',
                                                    '" . $Titular['folio'] . "',
                                                    '" . DB_escape_string($Titular['nombres'] . " " . $Titular['apellidos']) . "',
                                                    '" . DB_escape_string($Titular['calle']) . "',
                                                    '" . $Titular['numero'] . "',
                                                    '" . $Titular['address3'] . "',
                                                    '" . DB_escape_string($Titular['colonia']) . "',
                                                    '" . $Titular['sector'] . "',
                                                    '" . $Titular['entrecalle'] . "',
                                                    '" . $Titular['municipio'] . "',
                                                    '" . $Titular['estado'] . "',
                                                    '" . $Titular['cp'] . "',
                                                    '" . $Titular['cuadrante1'] . "',
                                                    '" . $Titular['cuadrante2'] . "',
                                                    '" . $Titular['cuadrante3'] . "',
                                                    '" . $Titular['sexo'] . "',
                                                    '" . DB_escape_string($Titular['razonsoc']) . "',
                                                    '" . $Titular['fecha'] . "',
                                                    '" . $Titular['fechaultau'] . "',
                                                    '" . $Titular['telefono1'] . "',
                                                    'Titular',
                                                    '" . $_Debtor['DebtorNo'] . "',
                                                    '" . $Area . "',
                                                    '" . $Salesman . "',
                                                    '" . $DefaultLocation . "',
                                                    '" . $DefaultShipVia . "',
                                                    '" . $TaxGroupid . "',
                                                    'Titular'
                                                    )";
                    //FB::INFO($CreateBranch,'_________________________________-$CreateBranch');
                    DB_query($CreateBranch, $db);
                    /**************************************************************************************************/

                    //Actualiza el status a 1 para q no vuelva a tomar este registro.
                    $SQLCheckFolio = "UPDATE tmp_titular SET rh_sinc = 1 WHERE folio = " . $Titular['folio'];
                    $_2CheckFolio = DB_query($SQLCheckFolio, $db);
                }
            }/*END FOREACH*/
        }
        $this->render('sincroniza');
    }




    public function actionIndex() {
        global $db;

        FB::INFO($_POST, '________________________POST');

        if (!empty($_POST['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
        }

        if (!empty($_POST['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!isset($_POST['type']) OR $_POST['type']=='all') {
            $SelType = "LIKE '%'";
        } else {
            $SelType = '= ' . $_POST['type'];
        }

        if (!isset($_POST['branch']) OR $_POST['branch']=='all') {
            $SelBranch = "LIKE '%'";
        } else {
            $SelBranch = "= '" . $_POST['branch'] . "' ";
        }

        if (isset($_REQUEST['bytype'])) {
            $BYTYPE = "group by debtortrans.type ";
            $SUM = "SUM( ";
            $GROUP = TRUE;
            $SUMC = " )";
        }

        $SQL = "SELECT if(isnull(v.id_salesorders),if(isnull(cp.id_salesorders),systypes.typename,if(c.tipo_de_comprobante='ingreso','Carta Porte Ingreso', 'Carta Porte Traslado')),'Transportista') typename,
        debtortrans.id,
        debtortrans.debtorno,
        (titular.folio) as TFolio,
        (titular.name) as TName,
        (titular.apellidos) as TApellidos,
        (fasignados.tipo_membresia) as FATipo,
        (pm.paymentname) as CPaymentName,
        (fp.frecuencia) as CFrecPago,
        debtortrans.type,
        debtortrans.transno,
        debtortrans.branchcode,
        debtortrans.trandate,
        debtortrans.reference,
        debtortrans.invtext,
        debtortrans.order_,
        debtortrans.rate,
        debtortrans.rh_status,
        $SUM (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount) $SUMC AS totalamount,
        debtortrans.alloc AS allocated,
                not isnull(c.id) is_cfd,
                c.serie,
                c.folio,
                c.uuid,
                c.no_certificado,
                c.fk_transno,
                (c.fecha) as FechaFactura,
                c.xml,
                c.cadena_original,
                not isnull(cp.id_salesorders) is_carta_porte,
                not isnull(v.id_salesorders) is_transportista
            FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                left join rh_carta_porte cp on debtortrans.order_ = cp.id_salesorders
                left join rh_vps__transportista v on debtortrans.order_ = v.id_salesorders
                left join rh_titular titular on debtortrans.debtorno = titular.debtorno
                left join rh_cobranza cobranza on titular.folio = cobranza.folio
                left join paymentmethods pm on cobranza.paymentid = pm.paymentid
                left join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
                left join rh_foliosasignados fasignados on titular.folio = fasignados.folio,
                systypes
            WHERE debtortrans.type = systypes.typeid
            AND debtortrans.type " . $SelType . "
            AND debtortrans.branchcode " . $SelBranch . "
            AND debtortrans.debtorno NOT IN (21,30)
            AND (debtortrans.trandate >= '$DateAfterCriteria 00:00:00' and debtortrans.trandate <= '$DateBeforeCriteria 23:59:59' )
            $BYTYPE   ORDER BY  debtortrans.trandate, debtortrans.transno   ";

        FB::INFO($SQL, '__________________________SQL');

        $ErrMsg = _('No transactions were returned by the SQL because');
        $TransResult = DB_query($SQL, $db, $ErrMsg);

        while ($Rows = DB_fetch_assoc($TransResult)) {
            $domObj = new xmlToArrayParser($Rows['xml']);
            $GetXML = $domObj->array;
            $FechaTimbrado = $GetXML['cfdi:Comprobante']['cfdi:Complemento']['tfd:TimbreFiscalDigital']['attrib']['FechaTimbrado'];
            $Rows['FechaTimbrado'] = $FechaTimbrado;
            $InvoiceData[] = $Rows;
        }
        FB::INFO($InvoiceData, '_____________________________$InvoiceData');
        $this->render('index', array('InvoiceData' => $InvoiceData));
    }

    /**
     * @Todo
     * Bitacora de Facturacion
     * @author erasto@realhost.com.mx
     */
    public function actionPendientesfacturar() {

        set_time_limit(0);
        ini_set('memory_limit', '2048M');
        global $db;
        FB::INFO($_POST, '____________________________POST');
        if (!empty($_POST['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-1, date("Y")));
        }

        if (!empty($_POST['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!empty($_POST['StatusFactura'])) {
            $StatusFactura = $_POST['StatusFactura'];
        } else {
            $StatusFactura = "%";
        }

        if (!empty($_POST['paymentid'])) {
            $_2MetodoPago = implode(",", $_POST['paymentid']);
            $MetodoPago = " AND pm.paymentid IN ({$_2MetodoPago}) ";
        }

        if(!empty($_POST['TipoSerie'])){
            $TipoSerie .= " AND rh_facturacion.serie = '{$_POST['TipoSerie']}' ";
        }

        if (!empty($_POST['frecuencia_pago'])) {
            $_2FrecuenciaPago = implode(",", $_POST['frecuencia_pago']);
            $FrecuenciaPago = " AND fp.id IN ({$_2FrecuenciaPago}) ";
        }

        //MODIFICADO POR CAROLINA CASTILLO 28-06-2016
        
        if (isset($_POST['Requierefactura']) && $_POST['Requierefactura'] != '') {
            $RequiereFactura = " AND cobranza.factura_fisica = '{$_POST['Requierefactura']}' ";

        }
        else {
            $RequiereFactura ="";
        }


        $_GetInvoiceData = "SELECT rh_facturacion.*,
                            (ST.typename) as TypeName,
                            (titular.folio) as TFolio,
                            (titular.name) as TName,
                            (titular.apellidos) as TApellidos,
                            (cobradores.nombre) as Cobrador,-- Se agrego el cobrador Angeles Perez 28-06-2016
                            (cobranza.factura_fisica) as RequiereFactura,-- Se agrego el Requiere Factura Angeles Perez 28-06-2016
                            (dtm.taxref) as TaxRef,
                            (dtm.address10) as PostalCode,
                            (cobranza.fecha_corte) as FechaCorte,
                            (fasignados.tipo_membresia) as FATipo,
                            (cobranza.stockid) as CPlan,
                            (pm.paymentname) as CPaymentName,
                            (fp.frecuencia) as CFrecPago,
                            dtrans.rh_status,
                            (dtrans.id) as DebtorTransID,
                            (dtrans.ovamount + dtrans.ovgst + dtrans.ovfreight + dtrans.ovdiscount) as TotalInvoice,
                            (cfd.folio) as FolioFactura,
                            (cfd.fecha) as FechaTimbrado,
                            cfd.uuid,
                            (SELECT SUM(SOD.quantity*SOD.unitprice) as OrderTotal FROM salesorderdetails as SOD WHERE SOD.orderno = rh_facturacion.orderno ) as OrderTotal2
                          from rh_facturacion
                          join systypes ST on rh_facturacion.systype = ST.typeid
                          join rh_titular titular on rh_facturacion.debtorno = titular.debtorno
                          join rh_cobranza cobranza on titular.folio = cobranza.folio
                          join stockmaster stkm on cobranza.stockid= stkm.stockid
                          join debtorsmaster dtm on rh_facturacion.debtorno = dtm.debtorno
                          join paymentmethods pm on cobranza.paymentid = pm.paymentid
                          join rh_cobradores cobradores on cobranza.cobrador = cobradores.id -- Se agrego el join para poder traer el cobrador Angeles Perez 28-06-2016
                          left join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
                          left join rh_foliosasignados fasignados on titular.folio = fasignados.folio
                          left join debtortrans dtrans on rh_facturacion.debtortrans_id = dtrans.id
                          left join rh_cfd__cfd cfd on cfd.id_debtortrans = dtrans.id
                          WHERE
                          (rh_facturacion.fecha_corte >= '{$DateAfterCriteria} 00:00:00'
                          AND rh_facturacion.fecha_corte <= '{$DateBeforeCriteria} 23:59:59' )
                          AND titular.movimientos_afiliacion = 'Activo'
                          AND stkm.is_cortesia = 0
                          AND rh_facturacion.status LIKE '{$StatusFactura}'
                          {$MetodoPago}
                          {$FrecuenciaPago}
                          {$TipoSerie}
                          {$RequiereFactura}

                          ";
        FB::INFO($_GetInvoiceData, '______________________________________$_GetInvoiceData');
        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);
        while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
            $InvoiceData[] = $Rows;
        }


        // select * from stockmaster where categoryid = 'AFIL';
        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }
        //FB::INFO($InvoiceData, '__________________________$GetInvoiceData');

        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(' paymentid NOT IN (3,4,6,7)'), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');

        $this->render('pendientesfacturar', array(
            'InvoiceData' => $InvoiceData,
            'ListaFormasPago' => $ListaFormasPago,
            'ListaFrecuenciaPago' => $ListaFrecuenciaPago,
            'ListaPlanes' => $ListaPlanes
        ));
    }

    /**
     * @Todo
     * Facturacion Masiva, Cada hilo de Facturacion ejecuta este Metodo
     * @author erasto@realhost.com.mx
     */
    public function actionCron() {

        FB::INFO($_POST, '______________________OK POST');

        global $db;
        set_time_limit(0);
        ini_set('memory_limit', '2048M');

        if (!empty($_POST['Check']['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['Check']['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
        }

        if (!empty($_POST['Check']['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['Check']['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!empty($_POST['Check']['StatusFactura'])) {
            $StatusFactura = $_POST['Check']['StatusFactura'];
        } else {
            $StatusFactura = "%";
        }

        if (!empty($_POST['Check']['paymentid'])) {
            $_2MetodoPago = implode(",", $_POST['Check']['paymentid']);
            $MetodoPago = " AND pm.paymentid IN ({$_2MetodoPago}) ";
        }

        if (!empty($_POST['Check']['frecuencia_pago'])) {
            $_2FrecuenciaPago = implode(",", $_POST['Check']['frecuencia_pago']);
            $FrecuenciaPago = " AND fp.id IN ({$_2FrecuenciaPago}) ";
        }

        $ThreadNo = $_POST['Check']['ThreadNo'];

        $InvoiceData = array();
        $_GetInvoiceData = "SELECT rh_facturacion.*, pm.paymentname
                          from rh_facturacion
                          left join systypes ST on rh_facturacion.systype = ST.typeid
                          left join rh_titular titular on rh_facturacion.debtorno = titular.debtorno
                          left join rh_cobranza cobranza on titular.folio = cobranza.folio
                          left join stockmaster stkm on cobranza.stockid= stkm.stockid
                          left join paymentmethods pm on cobranza.paymentid = pm.paymentid
                          left join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
                          left join debtortrans dtrans on rh_facturacion.debtortrans_id = dtrans.id
                          WHERE
                          (rh_facturacion.fecha_corte >= '{$DateAfterCriteria} 00:00:00' and rh_facturacion.fecha_corte <= '{$DateBeforeCriteria} 23:59:59' )
                          AND titular.movimientos_afiliacion = 'Activo'
                          AND stkm.is_cortesia = 0
                          AND rh_facturacion.status LIKE '{$StatusFactura}'
                          {$MetodoPago}
                          {$FrecuenciaPago}
                          AND rh_facturacion.facturar = 1
                          AND rh_facturacion.threadno = 0
                          LIMIT 100
                          ";
                          // echo $_GetInvoiceData; exit;

        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);
        FB::INFO($_GetInvoiceData, '______________________________________$_GetInvoiceData');

        $IDs = "0";
        $Rows = array();
        while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
            //$InvoiceData[] = $Rows;
            $IDs .= ", " . $Rows['id'];
        }

        if(!empty($IDs)){
            //Coloco Status
            $SQLCheckOrder = "UPDATE rh_facturacion SET threadno = " . $ThreadNo . "  WHERE id IN (" . $IDs . ") AND threadno = 0 ";
            $_2CheckOrder = DB_query($SQLCheckOrder, $db);

            $SelectOrders = "SELECT * from rh_facturacion WHERE threadno = " . $ThreadNo . "  AND status = 'Programada' AND id IN (" . $IDs . ") ";
            $_2SelectOders = DB_query($SelectOrders, $db);

            $Orders = array();
            while ($_2Order = DB_fetch_assoc($_2SelectOders)) {
                $Orders[] = $_2Order;
            }

            FB::INFO($Orders,'______________________ORDERS');

            $QtyInvoiced = 0;
            $ErrorMSG = "";
            $FacturaCFDI = new Facturacion();
            foreach ($Orders as $Data) {
                $DebtorNo = $Data['debtorno'];
                $OrderNo = $Data['orderno'];
                $Folio = $Data['folio'];
                $BitacoraID = $Data['id'];
                $Tipo = $Data['tipo'];
                $FechaProxFactura = $Data['prox_factura'];

                FB::INFO($OrderNo,'_________ORDERNO');
                FB::INFO($Folio,'_________FOLIO');

                $FacturaCFDI->FacturaPedido($OrderNo, $Folio);
                //$DebtorTransID = $this->FacturaPedido($OrderNo, $Folio);
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');

                //Timbrar Factura
                $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);

                if ($FacturaCFDI->StatusTimbre = 'Timbrado' && !empty($BitacoraID)) {
                    FB::INFO($FacturaCFDI->StatusTimbre, '_____________________FacturaTimbrada');
                    $_GetTransNo = "SELECT transno,order_ FROM debtortrans WHERE debtortrans.id = {$FacturaCFDI->idDebtortrans} AND debtortrans.type = 10";
                    $_2GetTransNo = DB_query($_GetTransNo, $db);
                    $GetTransNo = DB_fetch_assoc($_2GetTransNo);

                    //SELECT SUM(quantity*unitprice) FROM salesorderdetails WHERE orderno = 2214;
                    // $_GetStatus = "SELECT status FROM rh_facturacion WHERE id = " . $BitacoraID;
                    // $_2GetOrderTotal = DB_query($_GetStatus, $db);
                    // $GetStatus = DB_fetch_assoc($_2GetStatus);
                    // if($GetStatus['status'] != 'Error')
                    {
                        $SQLProcesada = "UPDATE rh_facturacion SET status = 'Procesada', debtortrans_id = '{$FacturaCFDI->idDebtortrans}', transno = '{$GetTransNo['transno']}' WHERE id = " . $BitacoraID;
                        $_2Proccess = DB_query($SQLProcesada, $db);
                        //FB::INFO($Tipo, '__________________TIPO FACTURA');
                        if ($Tipo=="Factura Programada") {
                            $this->CreateNextInvoice($DebtorNo, $FechaProxFactura);
                        }
                    }
                }
                FB::INFO('__OKOK');
                $QtyInvoiced++;
            }
        }
        if($QtyInvoiced == 0 || $QtyInvoiced == null){
            $QtyInvoiced = "Cero";
        }
        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'message' => "ThreadNo " . $ThreadNo . " ha Terminado el Timbrado, Cantidad Timbrada: <strong>" . $QtyInvoiced . "</strong>",
            'ThreadNo' => $ThreadNo,
            'ErrorMSG' => $ErrorMSG
        ));
        exit;;
    }

    /**
    * @todo
    * Obtengo el Error de timbrado mediante el DebtorTransID
    * @author erasto@realhost.com.mx
    * */
    public function actionGetinvoiceerror(){
        global $db;
        FB::INFO($_POST,'_____________________POST');
        if(!empty($_POST['GetInvoice']['debtortrans_id'])){
            $idDebtortrans = $_POST['GetInvoice']['debtortrans_id'];
            $DebtorNo = $_POST['GetInvoice']['DebtorNo'];
            $_2GetInvoiceError = "SELECT * FROM rh_logfacturacion WHERE id_debtortrans = '{$idDebtortrans}'";
            $_GetInvoiceError = DB_query($_2GetInvoiceError, $db);
            $GetInvoiceError = DB_fetch_assoc($_GetInvoiceError, $db);


            $XML = str_replace("ID DebtorTrans: " . $idDebtortrans . " -> ERROR: error en comunicacion: ", "", $GetInvoiceError['error']);
            FB::INFO(strip_tags($XML),'____________________');
            FB::INFO($XML,'_____________________XML');


            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Mostrando Errores Transaccion " . $idDebtortrans,
                'URL' => "../Customers.php?DebtorNo=" . $DebtorNo,
                'ErrorData' => strip_tags($XML)
            ));
        }
        return;

    }

    /**
     * @Todo
     * Crea ProximaFactura
     *
    */
    public function CreateNextInvoice($DebtorNo, $FechaProxFactura = null) {

        if (!empty($DebtorNo)) {

            $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,cobranza.folio,cobranza.stockid,cobranza.fecha_corte,fp.frecuencia,pm.paymentname, cobranza.paymentid,cobranza.frecuencia_pago, (titular.costo_total) as COSTO_TOTAL ')
            ->from('debtorsmaster')
            ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->leftJoin('rh_titular titular', 'cobranza.folio = titular.folio')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

            $FechasProximasFacturas = $this->FechaProximaFactura(date("Y-m-d"),$DebtorData[0]['paymentid'], $DebtorData[0]['frecuencia_pago']);

            $_Frecuencia = $DebtorData[0]['frecuencia_pago'];

            $MesAnio = $this->GetMonth($Date, $_Frecuencia);
            $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . "-" . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

            $SO_Details = array(0 => array(
                    'orderlineno' => 0,
                    'stkcode' => $DebtorData[0]['stockid'],
                    'unitprice' => $DebtorData[0]['COSTO_TOTAL'],
                    'quantity' => 1,
                    'discountpercent' => 0,
                    'narrative' => 'Factura Programada',
                    'description' => $Description,
                    'poline' => '',
                    'rh_cost' => '0.0000',
                    'itemdue' => date("Y-m-d H:i")
                ));
            FB::INFO($SO_Details, '__________N I DETAIL');
            $FacturaCFDI = new Facturacion();
            $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
            //$OrderNo = $this->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
            FB::INFO($FacturaCFDI->OrderNo, '__________________ NI RET ORDER NO _ PEDIDO PROGAMADO');

            if($DebtorData[0]['paymentid'] == 9 || $DebtorData[0]['paymentid'] == 10){
                $Dias = 25;
            }else{
                $Dias = 01;
            }

            /*Guarda el Registro en la Bitacora*/
            $CreaBitacoraAgregarSocio = $this->CreaBitacoraAgregarSocio($DebtorData[0], 0, 0, $FacturaCFDI->OrderNo, "Factura Programada",$DebtorData[0]['frecuencia_pago'],$Dias);

            if ($CreaBitacoraAgregarSocio) {
                /*UpdateFlag*/
                $SQLUpdateFlag = "UPDATE rh_cobranza SET cobro_inscripcion = :cobro_inscripcion WHERE debtorno = :debtorno ";
                $parameters = array(
                    ':cobro_inscripcion' => 0,
                    ':debtorno' => $_POST['GetInvoice']['DebtorNo']
                );
                Yii::app()->db->createCommand($SQLUpdateFlag)->execute($parameters);
            }
        }
    }

    /**
    * @Todo
    * Inserta en la Bitacora el  Proximo Pedido a Facturar.
    *
    */
    public function CreaBitacoraAgregarSocio($Data, $debtortrans_id = null, $TransNo = null, $OrderNo = null, $Tipo = null, $Frecuencia = null, $Dia = null, $Inicial = null, $Fecha_Ingreso = null) {
        if (!empty($Data)) {

            if($Dia == 25){
                $GroupType = "Tarjetas";
            }else{
                $GroupType = "Efectivo";
            }
            switch ($Frecuencia) {
                case 1:
                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m") 1, $Dia, date("Y")));
                    if(!empty($Inicial)){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y")));
                    }else{
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m") + 1, $Dia, date("Y")));
                    }
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 1, $Dia, $GetDate[0]));
                    break;
                case 2:

                    $Fecha_Ingreso = $Data['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, $GroupType);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    FB::INFO($GetTodayParts,'_____________________PARTS');
                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }

                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m") + 6, $Dia, date("Y")));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 6, $Dia, $GetDate[0]));
                    break;
                case 3:

                    $Fecha_Ingreso = $Data['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $GroupType);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    FB::INFO($Date,'______FECHA ANUAL');
                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y") + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                case 4:

                    $Fecha_Ingreso = $Data['fecha_ingreso'];
                    $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $GroupType);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y")));
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }
                    FB::INFO($Date,'______FECHA ANUAL INSEN');
                    //$Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $Dia, date("Y") + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                default:
                    break;
            }

            $SQLInsert = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                          values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
            $parameters = array(
                ':folio' => $Data['folio'],
                ':debtorno' => $Data['debtorno'],
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => $Date,
                ':status' => "Programada",
                ':tipo' => $Tipo,
                ':systype' => 10,
                ':debtortrans_id' => $debtortrans_id,
                ':transno' => $TransNo,
                ':created' => date("Y-m-d H:i:s"),
                ':orderno' => $OrderNo,
                ':frecuencia_pago' => $Frecuencia,
                ':prox_factura' => $NextDate
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                FB::INFO('_______________________OK Save Bitacora');
                return true;
            }
        }
    }

    public function actionCheckfacturar() {
        global $db;
        FB::INFO($_POST, '____________________POST');
        if (!empty($_POST['Check']['BitacoraID'])) {

            $SQLCheck = "UPDATE rh_facturacion SET facturar = '{$_POST['Check']['Value']}' WHERE id = " . $_POST['Check']['BitacoraID'];
            FB::INFO($SQLCheck, '_____________________SQL');
            if (DB_query($SQLCheck, $db)) {
                if ($_POST['Check']['Value']==1) {
                    $MSG = "Esta Factura sera Procesada...";
                } else {
                    $MSG = "Esta Factura NO sera Procesada...";
                }
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => $MSG,
                    'Cheked' => $_POST['Check']['Value'],
                    'idBitacora' => $_POST['Check']['BitacoraID']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo hacer el Movimiento, intente de nuevo.",
                ));
            }
        }
        return;
    }

    /**
    * @Todo
    * Reporte de Revision de Facturas
    * */
    public function actionRevisionFactura() {
        global $db;

        if (!empty($_POST['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
        }

        if (!empty($_POST['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!empty($_POST['porDia']) and !empty($_POST['porNumero'])) {
            $complemento = "  and ((cobranza.dias_revision='Por Dia' and cobranza.dias_revision_dia = '{$_POST['porDia']}') || (cobranza.dias_revision='Por Numero' and cobranza.dias_revision_dia = '{$_POST['porNumero']}'))";
        } elseif (!empty($_POST['porDia'])) {
            $_POST['porDia'] = (int)$_POST['porDia'];
            $complemento = "  and (cobranza.dias_revision='Por Dia' and cobranza.dias_revision_dia = '{$_POST['porDia']}') ";
        } elseif (!empty($_POST['porNumero'])) {
            $_POST['porNumero'] = (int)$_POST['porNumero'];
            $complemento = " and (cobranza.dias_revision='Por Numero' and cobranza.dias_revision_dia = '{$_POST['porNumero']}') ";
        } else {
            $complemento = "";
        }

        $_GetInvoiceData = "SELECT
            (ST.typename) as TypeName,
            (titular.folio) as TFolio,
            (titular.name) as TName,
            (titular.apellidos) as TApellidos,
            (cobranza.fecha_corte) as FechaCorte,
            (fasignados.tipo_membresia) as FATipo,
            (cobranza.stockid) as CPlan,
            (pm.paymentname) as CPaymentName,
            (fp.frecuencia) as CFrecPago,
            CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura,
            (dtrans.ovamount + dtrans.ovgst + dtrans.ovfreight + dtrans.ovdiscount) as TotalInvoice,
            cobranza.dias_revision,
            cobranza.dias_revision_dia,
            cobranza.dias_cobro,
            cobranza.dias_cobro_dia,
            cobranza.revision_datefrom,
            cobranza.revision_dateto,
            cobradores.nombre cobradorName,
            (dtrans.id) as idDebtorTrans,
            (dtrans.rh_revisionfactura) as revisado,
            (dtrans.rh_fecha_revisionfactura) as fecha_revisado,
            (dtrans.rh_coment_revisionfactura) as comentarios,
            (dtrans.trandate) as FechaFactura
        FROM rh_cfd__cfd
        JOIN debtortrans dtrans on rh_cfd__cfd.id_debtortrans = dtrans.id
        JOIN systypes ST on dtrans.type = ST.typeid
        JOIN rh_titular titular on dtrans.debtorno = titular.debtorno
        LEFT JOIN rh_cobranza cobranza on titular.folio = cobranza.folio
        LEFT JOIN paymentmethods pm on cobranza.paymentid = pm.paymentid
        LEFT JOIN rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
        LEFT JOIN rh_foliosasignados fasignados on titular.folio = fasignados.folio
        LEFT JOIN rh_cobradores cobradores on cobradores.id = cobranza.cobrador
        WHERE
        (rh_cfd__cfd.fecha >= '{$DateAfterCriteria} 00:00:00' AND rh_cfd__cfd.fecha <= '{$DateBeforeCriteria} 23:59:59' )
        AND dtrans.rh_status = 'N'
        AND dtrans.type = 10
        {$complemento}
        ";
        FB::INFO($_GetInvoiceData,'_____________________SQL: ');
        //echo $_GetInvoiceData;  rh_revisionfactura rh_fecha_revisionfactura
        // left join rh_facturacion ON dtrans.id = rh_facturacion.debtortrans_id
        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);

        // while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
        //     $InvoiceData[] = $Rows;
        // }

        $this->render('revisionfactura', array('InvoiceData' => $_2GetInvoiceData));
    }

    /**
    * @Todo
    * Check desde Revision de facturas
    * */
    public function actionRevisar() {
        global $db;
        FB::INFO($_POST,'_________________________-POST');
        if (!empty($_POST['GetInvoice'])) {
            $id = (int)$_POST['GetInvoice']['idDebtorTrans'];
            $estado = (int)$_POST['GetInvoice']['estado'];
            if ($estado==0){
                $mensaje = 'La Factura ha sido exitosamente liberada...';
                $FechaRevision = "";
            }
            elseif ($estado==1){
                $mensaje = 'La Factura ha sido exitosamente marcada como revisada...';
                $FechaRevision = date("Y-m-d H:i:s");
            }

            $_update = "UPDATE debtortrans SET
                rh_revisionfactura ='{$estado}',
                rh_fecha_revisionfactura = '{$FechaRevision}'
                WHERE id='{$id}' ";

            FB::INFO($_update, '_________________________________$_update SQL');
            $_2GetInvoiceData = DB_query($_update, $db);

            $DebtorData[0] = DB_fetch_assoc($_2GetInvoiceData);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => $mensaje,
                'id' => $id,
                'FechaRevision' => $FechaRevision
            ));
            return;
        }
        exit ;
    }

    /**
    * @todo
    * Comentarios desde Revision de Facturas
    * */
    public function actionComentario() {
        global $db;
        FB::INFO($_POST,'_______________________POST');
        if (!empty($_POST['GetInvoice'])) {
            $id = (int)$_POST['GetInvoice']['idDebtorTrans'];
            $comentario = $_POST['GetInvoice']['comentario'];

            $_update = "update debtortrans set rh_coment_revisionfactura ='{$comentario}' where id='{$id}'";

            FB::INFO($_update, '_________________________________$_update SQL');
            try {
                $_2GetInvoiceData = DB_query($_update, $db);
                $DebtorData[0] = DB_fetch_assoc($_2GetInvoiceData);
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El comentario ha sido actualizado...",
                    'id' => $id
                ));
                return;
            } catch (Exception $e) {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error al Actualizar: " . $e->getMessage(),
                    'id' => $id
                ));
                return;
            }


        }
        exit ;
    }

    /**
    * @Todo
    * Reporte CobrarFactura
    * */
    public function actionCobrarFactura() {
        global $db;

        FB::INFO($_POST, '____________________________POST');
        if (!empty($_POST['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
        }

        if (!empty($_POST['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!empty($_POST['porDia']) and !empty($_POST['porNumero'])) {
            $complemento = "  and ((cobranza.dias_cobro='Por Dia' and cobranza.dias_cobro_dia = '{$_POST['porDia']}') || (cobranza.dias_cobro='Por Numero' and cobranza.dias_cobro_dia = '{$_POST['porNumero']}'))";
        } elseif (!empty($_POST['porDia'])) {
            $_POST['porDia'] = (int)$_POST['porDia'];
            $complemento = "  and (cobranza.dias_cobro='Por Dia' and cobranza.dias_cobro_dia = '{$_POST['porDia']}') ";
        } elseif (!empty($_POST['porNumero'])) {
            $_POST['porNumero'] = (int)$_POST['porNumero'];
            $complemento = " and (cobranza.dias_cobro='Por Numero' and cobranza.dias_cobro_dia = '{$_POST['porNumero']}') ";
        } else {
            $complemento = "";
        }

        $_GetInvoiceData = "SELECT
            (ST.typename) as TypeName,
            (titular.folio) as TFolio,
            (titular.name) as TName,
            (titular.apellidos) as TApellidos,
            (cobranza.fecha_corte) as FechaCorte,
            (fasignados.tipo_membresia) as FATipo,
            (cobranza.stockid) as CPlan,
            (pm.paymentname) as CPaymentName,
            (fp.frecuencia) as CFrecPago,
            CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura,
            (dtrans.ovamount + dtrans.ovgst + dtrans.ovfreight + dtrans.ovdiscount) as TotalInvoice,
            cobranza.dias_revision,
            cobranza.dias_revision_dia,
            cobranza.dias_cobro,
            cobranza.dias_cobro_dia,
            cobranza.revision_datefrom,
            cobranza.revision_dateto,
            cobradores.nombre cobradorName,
            (dtrans.id) as idDebtorTrans,
            (dtrans.rh_revisionfactura) as revisado,
            (dtrans.rh_coment_cobranzafactura) as comentarios_cobranza,
            (dtrans.trandate) as FechaFactura,
            if(dtrans.settled=0 AND dtrans.rh_status='N',0,1) as pagado
        FROM rh_cfd__cfd
        JOIN debtortrans dtrans ON rh_cfd__cfd.id_debtortrans = dtrans.id
        JOIN systypes ST ON dtrans.type = ST.typeid
        JOIN rh_titular titular ON dtrans.debtorno = titular.debtorno
        LEFT JOIN rh_cobranza cobranza ON titular.folio = cobranza.folio
        LEFT JOIN paymentmethods pm ON cobranza.paymentid = pm.paymentid
        LEFT JOIN rh_frecuenciapago fp ON cobranza.frecuencia_pago = fp.id
        LEFT JOIN rh_foliosasignados fasignados ON titular.folio = fasignados.folio
        LEFT JOIN rh_cobradores cobradores ON cobradores.id = cobranza.cobrador
        WHERE
        (rh_cfd__cfd.fecha >= '{$DateAfterCriteria} 00:00:00' AND rh_cfd__cfd.fecha <= '{$DateBeforeCriteria} 23:59:59' )
        AND dtrans.rh_status = 'N'
        AND dtrans.type = 10
        {$complemento}
        AND dtrans.rh_revisionfactura = 1
        ";
        FB::INFO($_GetInvoiceData,'_____________________GetInvoiceData');
        //echo $_GetInvoiceData;
        // (rh_facturacion.created >= '{$DateAfterCriteria} 00:00:00' and rh_facturacion.created <= '{$DateBeforeCriteria} 23:59:59' )
        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);

        // while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
        //     $InvoiceData[] = $Rows;
        // }

        $this->render('cobrarfactura', array('InvoiceData' => $_2GetInvoiceData));
    }

    /**
    * @todo
    * Guarda Comentario desde la pantalla de CobrarFactura
    */
    public function actionComentarioCobranza() {
        global $db;
        if (!empty($_POST['GetInvoice'])) {
            $id = (int)$_POST['GetInvoice']['id'];
            $comentario = $_POST['GetInvoice']['comentario'];

            $_update = "update debtortrans set rh_coment_cobranzafactura ='{$comentario}' where id='{$id}'";

            FB::INFO($_update, '_________________________________UPDATE SQL');
            $_2GetInvoiceData = DB_query($_update, $db);

            $DebtorData[0] = DB_fetch_assoc($_2GetInvoiceData);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "El comentario ha sido actualizado...",
                'id' => $_POST['GetInvoice']['id']
            ));
            return;
        }
        exit ;
    }

    /**
    * @todo
    * Reporte de PresupuestoCobranza
    */
    public function actionPresupuestoCobranza() {
        global $db;

        FB::INFO($_POST, '____________________________POST');
        if (!empty($_POST['TransAfterDate'])) {
            $DateAfterCriteria = $_POST['TransAfterDate'];
        } else {
            $DateAfterCriteria = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
        }

        if (!empty($_POST['TransBeforeDate'])) {
            $DateBeforeCriteria = $_POST['TransBeforeDate'];
        } else {
            $DateBeforeCriteria = date('Y-m-d');
        }

        if (!empty($_POST['porDia']) and !empty($_POST['porNumero'])) {
            $complemento = "  and ((cobranza.dias_cobro='Por Dia' and cobranza.dias_cobro_dia = '{$_POST['porDia']}') || (cobranza.dias_cobro='Por Numero' and cobranza.dias_cobro_dia = '{$_POST['porNumero']}'))";
        } elseif (!empty($_POST['porDia'])) {
            $_POST['porDia'] = (int)$_POST['porDia'];
            $complemento = "  and (cobranza.dias_cobro='Por Dia' and cobranza.dias_cobro_dia = '{$_POST['porDia']}') ";
        } elseif (!empty($_POST['porNumero'])) {
            $_POST['porNumero'] = (int)$_POST['porNumero'];
            $complemento = " and (cobranza.dias_cobro='Por Numero' and cobranza.dias_cobro_dia = '{$_POST['porNumero']}') ";
        } else {
            $complemento = "";
        }

        $_GetInvoiceData = "SELECT rh_facturacion.*,
            (ST.typename) as TypeName,
            (titular.folio) as TFolio,
            (titular.name) as TName,
            (titular.apellidos) as TApellidos,
            (cobranza.fecha_corte) as FechaCorte,
            (fasignados.tipo_membresia) as FATipo,
            (cobranza.stockid) as CPlan,
            (pm.paymentname) as CPaymentName,
            (fp.frecuencia) as CFrecPago,
            (dtrans.ovamount + dtrans.ovgst + dtrans.ovfreight + dtrans.ovdiscount) as TotalInvoice,
            cobranza.dias_revision,
            cobranza.dias_revision_dia,
            cobranza.dias_cobro,
            cobranza.dias_cobro_dia,
            cobranza.revision_datefrom,
            cobranza.revision_dateto,
            cobradores.nombre cobradorName,
            rh_facturacion.revisado,
            (cfdi.fecha) as FechaFactura,
            if(dtrans.settled=0 and dtrans.rh_status='N',0,1) as pagado
            from rh_facturacion
            join debtortrans dtrans on rh_facturacion.debtortrans_id = dtrans.id
            join rh_cfd__cfd cfdi on dtrans.id = cfdi.id_debtortrans
            join systypes ST on rh_facturacion.systype = ST.typeid
            join rh_titular titular on rh_facturacion.debtorno = titular.debtorno
            join rh_cobranza cobranza on titular.folio = cobranza.folio
            join paymentmethods pm on cobranza.paymentid = pm.paymentid
            join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
            left join rh_foliosasignados fasignados on titular.folio = fasignados.folio
            left join rh_cobradores cobradores on cobradores.id = cobranza.cobrador
            WHERE
            (cfdi.fecha >= '{$DateAfterCriteria} 00:00:00'
            AND cfdi.fecha <= '{$DateBeforeCriteria} 23:59:59' )
            AND rh_facturacion.status = 'Procesada' {$complemento}
            having pagado = 0
            ";
        //AND rh_facturacion.revisado = 1
        //echo $_GetInvoiceData;
        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);

        // while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
        //     $InvoiceData[] = $Rows;
        // }

        $this->render('presupuestocobranza', array('InvoiceData' => $_2GetInvoiceData));
    }

    /**
    * @todo
    * NO SE USA
    **/
    public function actionFacturacion() {
        global $db;
        $_GetInvoiceData = "SELECT
                            (titular.folio) as TFolio,
                            (titular.name) as TName,
                            (titular.apellidos) as TApellidos,
                            (cobranza.fecha_corte) as FechaCorte,
                            (fasignados.tipo_membresia) as FATipo,
                            (cobranza.stockid) as CPlan,
                            (pm.paymentname) as CPaymentName,
                            (fp.frecuencia) as CFrecPago
                            FROM rh_cobranza cobranza
                          left join rh_titular titular on cobranza.folio = titular.folio
                          left join paymentmethods pm on cobranza.paymentid = pm.paymentid
                          left join rh_frecuenciapago fp on cobranza.frecuencia_pago = fp.id
                          left join rh_foliosasignados fasignados on titular.folio = fasignados.folio
                          ";
        FB::INFO($_GetInvoiceData, '______________________________________$_GetInvoiceData');
        $_2GetInvoiceData = DB_query($_GetInvoiceData, $db);

        while ($Rows = DB_fetch_assoc($_2GetInvoiceData)) {
            $InvoiceData[] = $Rows;
        }

        FB::INFO($InvoiceData, '______________________________________$InvoiceData');

        $this->render('facturacion', array('InvoiceData' => $InvoiceData));
    }

    /**
     * @todo Factura Pedido
     * Se Activa desde el Icono de Facturar que esta en la Bitacora de Pendientes Facturar
     * @return void
     * @author erasto@realhost.com.mx
     * GetInvoice:{
     *     DebtorNo: debtorno,
     *     Folio: folio,
     *     OrderNo: orderno,
     *     BitacoraID: idBitacora,
     *     Tipo: Tipo
     * },
     */
    public function actionCreateinvoice() {
        global $db;
        FB::INFO($_POST, '______________________________________POST');

        if (!empty($_POST['GetInvoice'])) {
            $DebtorNo = $_POST['GetInvoice']['DebtorNo'];
            $OrderNo = $_POST['GetInvoice']['OrderNo'];
            $Folio = $_POST['GetInvoice']['Folio'];
            $BitacoraID = $_POST['GetInvoice']['BitacoraID'];
            $Tipo = $_POST['GetInvoice']['Tipo'];
            FB::INFO($Tipo,'____________--TIPO FACTURA');
            unset($_POST);

            switch ($Tipo) {
                case 'ReactivacionSocio':
                    //MEMBRESIA
                    $TipoFacturas = 7;
                    break;
                case 'NuevosSocios':
                    //INSCRIPCION Actualizado por Angeles Perez 09/06/2016
                    $TipoFacturas = 6; 
                    break;
                default:
                    # code...
                    break;
            }

            $FacturaCFDI = new Facturacion();
            $FacturaCFDI->FacturaPedido($OrderNo, $Folio, $TipoFacturas);
            FB::INFO($FacturaCFDI->idDebtortrans, '__________________$DebtorTransID NEW');

            $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);
            FB::INFO($FacturaCFDI->StatusTimbre, '__________________StatusTimbre NEW');

            if ($FacturaCFDI->StatusTimbre = 'Timbrado' && !empty($BitacoraID)) {

                $_GetTransNo = "SELECT transno,order_ FROM debtortrans WHERE debtortrans.id = {$FacturaCFDI->idDebtortrans} AND debtortrans.type = 10";
                $_2GetTransNo = DB_query($_GetTransNo, $db);
                $GetTransNo = DB_fetch_assoc($_2GetTransNo);

                $SQLProcesada = "UPDATE rh_facturacion SET status = 'Procesada', debtortrans_id = '{$FacturaCFDI->idDebtortrans}', transno = '{$GetTransNo['transno']}' WHERE id = " . $BitacoraID;
                $_2Proccess = DB_query($SQLProcesada, $db);
                FB::INFO($FacturaCFDI->StatusTimbre, '_____________________OK ALL');

            }
            //exit;

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "La Factura para el folio: " . $Folio . " se ha Generado y Timbrado correctamente...",
                'BitacoraID' => $BitacoraID,
                'actions_td' => "<a target= '_blank' href='../PHPJasperXML/sample1.php?transno={$GetTransNo['transno']}&afil=true' ><IMG src='../css/silverwolf/images/pdf.gif' ></a>",
                'OrderTotal' => number_format($GetOrderTotal['OrderTotal'], 2)
            ));
            return;
        }
        // exit ;
    }

    /**
     * @Todo
     * NO SE USA
     * Bitacora: Registra Proximo Pedido
     * Crea Registro Tipo NuevosSocios
     * @Param $Data = array(folio=>123,debtorno=>123)
     * @return void
     * @author
     */
    public function CreaBitacoraProxFacturas($Data, $debtortrans_id = null, $TransNo = null, $OrderNo = null) {
        if (!empty($Data)) {
            $Tipo = "Factura Programada";

            switch ($Data['frecuencia']) {
                case 'MENSUAL' :
                    $Created = date('Y-m-d', mktime(0, 0, 0, date("m") + 1, date("d"), date("Y")));
                    break;
                case 'SEMESTRAL' :
                    $Created = date('Y-m-d', mktime(0, 0, 0, date("m") + 6, date("d"), date("Y")));
                    break;
                case 'ANUAL' :
                    $Created = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1));
                    break;
                case 'ANUAL INSEN' :
                    $Created = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y") + 1));
                    break;
                default :
                    $Created = date("Y-m-d");
                    break;
            }

            $SQLInsert = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno) values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno)";
            $parameters = array(
                ':folio' => $Data['folio'],
                ':debtorno' => $Data['debtorno'],
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => date('Y-m-d'),
                ':status' => 'Programada',
                ':tipo' => $Tipo,
                ':systype' => 10,
                ':debtortrans_id' => $debtortrans_id,
                ':transno' => $TransNo,
                ':created' => $Created,
                ':orderno' => $OrderNo,
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                FB::INFO($Data, '_______________________OK Programada');
                return true;
            }
        }
    }

    /**
     * @Todo Timbra Factura
     * GetInvoice:{
     DebtorNo: debtorno,
     Folio: folio,
     OrderNo: orderno,
     BitacoraID: idBitacora
     },
     * @return void
     * @author
     */
    public function actionProcess() {

        FB::INFO($_POST, '______________________________POST');
        global $db;
        if (!empty($_POST['GetInvoice']['debtortrans_id'])) {
            $FacturaCFDI = new Facturacion();
            $FacturaCFDI->FacturaPedido($OrderNo, $Folio);
            $FacturaCFDI->Timbrar($_POST['GetInvoice']['debtortrans_id']);
            if ($FacturaCFDI->StatusTimbre = 'Timbrado' && !empty($_POST['GetInvoice']['BitacoraID'])) {
                FB::INFO($TimbrarFactura, '_____________________$TimbrarFactura');
                $SQLProcesada = "UPDATE rh_facturacion SET status = 'Procesada' WHERE id = " . $_POST['GetInvoice']['BitacoraID'];
                $_2Proccess = DB_query($SQLProcesada, $db);

                $_GetTransNo = "SELECT transno,order_ FROM debtortrans WHERE debtortrans.id = {$_POST['GetInvoice']['debtortrans_id']} AND debtortrans.type = 10";
                $_2GetTransNo = DB_query($_GetTransNo, $db);
                $GetTransNo = DB_fetch_assoc($_2GetTransNo);

                //SELECT SUM(quantity*unitprice) FROM salesorderdetails WHERE orderno = 2214;
                $_GetOrderTotal = "SELECT SUM(quantity*unitprice) as OrderTotal FROM salesorderdetails WHERE orderno = {$GetTransNo['order_']} ";
                $_2GetOrderTotal = DB_query($_GetOrderTotal, $db);
                $GetOrderTotal = DB_fetch_assoc($_2GetOrderTotal);
            }

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "La Factura para el folio: " . $_POST['GetInvoice']['Folio'] . " se ha Timbrado correctamente...",
                'BitacoraID' => $_POST['GetInvoice']['BitacoraID'],
                'actions_td' => "<a target= '_blank' href='../PHPJasperXML/sample1.php?transno={$GetTransNo['transno']}&afil=true' ><IMG src='../css/silverwolf/images/pdf.gif' ></a>"
            ));
            return;
        }
        exit ;
    }


    public function actionBuscarfacturas(){
        $title = _('AgedDebtorshtml');
        global $db;
        if(!empty($_POST['GetInvoice'])){
            FB::INFO($_POST,'____________________POST');
            $Where = "";
            if(!empty($_POST['GetInvoice']['DebtorNo'])){
                $_2GetDebtorNo = "SELECT debtorno from rh_titular WHERE folio = '{$_POST['GetInvoice']['DebtorNo']}' ";
                $_2GetDebtorNoResult = DB_query($_2GetDebtorNo, $db);
                $GetDebtorNo = DB_fetch_assoc($_2GetDebtorNoResult);
                $Where .= " AND debtortrans.debtorno='" . $GetDebtorNo['debtorno'] . "' ";
            }

            if(!empty($_POST['GetInvoice']['Folio'])){
                $Where .= " AND  rh_cfd__cfd.folio = '".$_POST['GetInvoice']['Folio'] . "' ";
            }

            if(!empty($_POST['GetInvoice']['StartDate']) && !empty($_POST['GetInvoice']['EndDate'])){
                $Where .= " AND  date(debtortrans.trandate)>= '".$_POST['GetInvoice']['StartDate'] . "' ";
                $Where .= " AND  date(debtortrans.trandate)<= '".$_POST['GetInvoice']['EndDate'] . "' ";
            }else{
                $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
                $EndDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));

                // $Where .= " AND  date(debtortrans.trandate)>= '" . $StartDate . "' ";
                // $Where .= " AND  date(debtortrans.trandate)<= '" . $EndDate . "' ";

            }

            $_2GetInvoiceData = 'SELECT "true"as is_cfd, rh_cfd__cfd.no_certificado,
                debtortrans.rh_status,
                rh_cfd__cfd.fk_transno,
                rh_cfd__cfd.serie,
                rh_cfd__cfd.uuid,
                rh_cfd__cfd.folio,
                debtorsmaster.name,
                debtortrans.trandate,
                rh_cfd__cfd.id_debtortrans,
                ovamount/rate as ovamount,
                ovgst/rate as ovgst,
                (ovamount+ovgst)/rate as total
                FROM rh_cfd__cfd
                join debtortrans on rh_cfd__cfd.id_debtortrans = debtortrans.id
                join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
            where 1=1 ' . $Where;
            $_2GetInvoiceDataResult = DB_query($_2GetInvoiceData, $db);
            while ( $Data = DB_fetch_assoc($_2GetInvoiceDataResult)) {
                $InvoiceData[] = $Data;
            }
            FB::INFO($_2GetInvoiceData,'____________________________2GetInvoiceData');
            $TableContent = "";
            foreach ($InvoiceData as $Data) {
                if($Data['rh_status'] =='C'){
                    $RowColor = "class='danger' ";
                    $Status = "CANCELADA";
                }
                if($Data['rh_status'] =='N'){
                    $RowColor = "class='' ";
                    $Status = "ACTIVA";
                }
                $TableContent .= "<tr {$RowColor}>";
                $rh_ovamount+=$Data['ovamount'];
                $rh_ovgst+=$Data['ovgst'];
                $rh_total+=$Data['total'];

                //if(strlen($Data['uuid'])>0){

                    if($Data['is_cfd']){
                     if($Data['is_carta_porte']){
                        $CPorte = "isCartaPorte=true";
                    }else{
                        $CPorte = "";
                    }

                    if($Data['rh_status']=='C'){
                        $RHStatus = "&isCfdCancelado=true";
                    }else{
                        $RHStatus = "";
                    }
                        $TableContent .= "<td>".$Data['serie'] . $Data['folio']."</td>";
                        $TableContent .= "<td>".$Status."</td>";
                        $TableContent .= "<td>".$Data['name']."</td>";
                        $TableContent .= "<td>".$Data['trandate']."</td>";
                        $TableContent .= "<td style='text-align: right;'>".number_format($Data['ovamount'],2)."</td>";
                        $TableContent .= "<td style='text-align: right;'>".number_format($Data['ovgst'],2)."</td>";
                        $TableContent .= "<td style='text-align: right;'>".number_format($Data['total'],2)."</td>";
                        $TableContent .= "<td><a target='_blank' href='../rh_j_downloadFacturaElectronicaXML_CFDI.php?downloadPath=" . ('XMLFacturacionElectronica/xmlbycfdi/' . $Data['uuid']. '.xml') . "'><IMG SRC='../images/xml.gif' TITLE='" . _('Click to download the invoice') . _(' (XML)') . "'></a> ";
                        $TableContent .= " <a target='_blank' href='../PHPJasperXML/sample1.php?transno=" . $Data['fk_transno'] . '&' . $CPorte . $RHStatus . "&afil=true'><IMG SRC='../css/silverwolf/images/pdf.gif' TITLE = 'Click to preview the invoice PDF' ></a> ";
                        $TableContent .= " <a target='_blank' href='../rh_PrintCustTrans.php?idDebtortrans=2&FromTransNo={$Data['fk_transno']}&InvOrCredit=Invoice&isCfd=1'><IMG SRC='../css/silverwolf/images/preview.gif' TITLE='" . _('Click to View the invoice') . _(' (XML)') . "'></a></td>";
                    }
                    $TableContent .="</tr>";
                //}
            }
                // FB::INFO($TableContent,'____________________TableContent');
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'Buscando...',
                    'TableContent' => $TableContent
                ));
            return;
        }
        $this->render('buscarfacturas');
    }

    /**
     * @Todo
     * Obtengo los Datos de Cobranza para Mostrarlos en la pantalla de pagos adelantados
     * @author erasto@realhost.com.mx
    */
    public function actionDatoscobranza(){
        FB::INFO($_POST,'_______________________________POST');
        if(!empty($_POST['GetData']['Folio'])){
            $GetData = Yii::app()->db->createCommand()->select(' titular.folio,
                                                                titular.name,
                                                                titular.apellidos,
                                                                titular.name2,
                                                                titular.costo_total,
                                                                cobranza.stockid,
                                                                cobranza.paymentid,
                                                                cobranza.convenio,
                                                                stockmaster.description,
                                                                EMP.empresa,
                                                                FP.frecuencia,
                                                                (FP.id) as IDFrecPago,
                                                                PM.paymentname,
                                                                Con.convenio
                                                                 ')
            ->from('rh_cobranza cobranza')
            ->leftJoin('rh_titular titular', 'cobranza.folio = titular.folio')
            ->leftJoin('stockmaster stockmaster', 'cobranza.stockid = stockmaster.stockid')
            ->leftJoin('rh_frecuenciapago FP', 'cobranza.frecuencia_pago = FP.id')
            ->leftJoin('paymentmethods PM', 'cobranza.paymentid = PM.paymentid')
            ->leftJoin('rh_empresas EMP', 'cobranza.empresa = EMP.id')
            ->leftJoin('rh_convenios Con', 'cobranza.convenio = Con.id')
            ->where('cobranza.folio = "' . $_POST['GetData']['Folio'] . '"')->queryAll();

            FB::INFO($GetData,'___________________________DATA');

            if(!empty($GetData)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'Folio ' . $_POST['GetData']['Folio'],
                    'Data' => $GetData[0]
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => 'No se encontro Informacion para el Folio ' . $_POST['GetData']['Folio']
                ));
            }
        }else{
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => 'Debe ingresar un Folio...'
            ));
        }
        return;
    }

    /**
     * @Todo
     * Procesa Pagos Adelantados
     * @author erasto@realhost.com.mx
     *
    *array(
        *['ProcessData'] =>
        *array(
        *    ['Periodos'] =>
        *        array(
        *            [1] =>
        *                array(
        *                    ['fecha_linea'] =>'2014-07-01'
        *                    ['importe_linea'] =>100
        *                )
        *            [2] =>
        *                array(
        *                    ['fecha_linea'] =>'2014-08-01'
        *                    ['importe_linea'] =>100
        *                )
        *            [3] =>
        *                array(
        *                    ['fecha_linea'] =>'2014-09-01'
        *                    ['importe_linea'] =>100
        *                )
        *            [4] =>
        *                array(
        *                    ['fecha_linea'] =>'2014-10-01'
        *                    ['importe_linea'] =>100
        *                )
        *        )
        *    ['Fecha'] =>'2014-07-25'
        *    ['Concepto'] =>'PAGO ADEL. SERV. CORTESIA MESES (07-2014)(08-2014)(09-2014)(10-2014)'
        *    ['Importe'] =>400
        *)
    *)
     *
     *
    */
    public function actionPagosadelantados(){
        global $db;
        FB::INFO($_POST,'_______________________________POST1');

        if(!empty($_POST['ProcessData']['Periodos'])){
            $ParsePeriods = parse_str($_POST['ProcessData']['Periodos'], $Periods);
            //$_POST['ProcessData']['Periodos'] = $Periods;
            FB::INFO($_POST,'_______________________________POST2');

            $AdelantaPeriodos = $Periods;;
            $Folio = $_POST['ProcessData']['Folio'];
            $Fecha = $_POST['ProcessData']['Fecha'];
            $Concepto = $_POST['ProcessData']['Concepto'];
            $Importe = $_POST['ProcessData']['Importe'];
            $MetodoPago = $_POST['ProcessData']['MetodoPago'];
            $Frecuencia = $_POST['ProcessData']['FrecPago'];
            $Producto = $_POST['ProcessData']['Producto'];
            $Costo_Total = $_POST['ProcessData']['Costo_Total'];
            $FrecPagoName = $_POST['ProcessData']['FrecPagoName'];
            $DebtorNo = $this->GetDebtorNo($_POST['ProcessData']['Folio']);

            FB::INFO($DebtorNo,'_____________________DEBTORNO');

            $SO_Details = array(0 => array(
                'orderlineno' => 0,
                'stkcode' => $Producto,
                'unitprice' => $Importe,
                'quantity' => 1,
                'discountpercent' => 0,
                'narrative' => 'Facturacion de Periodos Adelantados',
                'description' => $Concepto,
                'poline' => '',
                'rh_cost' => '0.0000',
                'itemdue' => date("Y-m-d H:i")
            ));
            /*Crea Pedido con los Pagos Adelantados*/
            $FacturaCFDI = new Facturacion();
            $FacturaCFDI->CreaPedido(null, $SO_Details, $DebtorNo);
            //$OrderNo = $this->CreaPedido(null, $SO_Details, $DebtorNo);
            FB::INFO($FacturaCFDI->OrderNo,'___________________ORDERNO');

            /*Factura Pedido con los Pagos Adelantados*/
            if(!empty($FacturaCFDI->OrderNo)){
                $FacturaCFDI->FacturaPedido($FacturaCFDI->OrderNo, $Folio);
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');
            }

            FB::INFO($_POST,'_______________________________________POST3');
            /*Timbrar Factura con los Pagos Adelantados*/
            $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);
            if($FacturaCFDI->StatusTimbre = 'Timbrado'){
                $_GetTransNo = "SELECT transno,order_ FROM debtortrans WHERE debtortrans.id = {$FacturaCFDI->idDebtortrans} AND debtortrans.type = 10";
                $_2GetTransNo = DB_query($_GetTransNo, $db);
                $GetTransNo = DB_fetch_assoc($_2GetTransNo);
                $TransNo = $GetTransNo['transno'];

                /*Logueo en la Bitacora el Pedido que se Facturo*/
                $SQLInsertBitacoraPA = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                              values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                $parameters = array(
                    ':folio' => $Folio,
                    ':debtorno' => $DebtorNo,
                    ':userid' => $_SESSION['UserID'],
                    ':fecha_corte' => $Fecha,
                    ':status' => "PagoAdelantado",
                    ':tipo' => 'Pago Adelantado',
                    ':systype' => 10,
                    ':debtortrans_id' => $DebtorTransID,
                    ':transno' => $TransNo,
                    ':created' => date("Y-m-d H:i:s"),
                    ':orderno' => $OrderNo,
                    ':frecuencia_pago' => $Frecuencia,
                    ':prox_factura' => '0000-00-00'
                );
                Yii::app()->db->createCommand($SQLInsertBitacoraPA)->execute($parameters);
            }

            /*Por cada periodo Adelantado Logeo en la Bitacora con el OrderNo y el TransNo del pedido con Los periodos Adelantados*/
            foreach ($AdelantaPeriodos as $Periodo) {
                /*Busco Pedidos pendientes para las fechas que se estan adelantando*/
                $Existentes = Yii::app()->db->createCommand()
                    ->select(' id, fecha_corte,orderno ')
                    ->from(' rh_facturacion ')
                    ->where(" folio = '{$Folio}' AND fecha_corte = '{$Periodo['fecha_linea']}' ")
                    ->queryAll();
                if(!empty($Existentes[0]['id']) && !empty($Existentes[0]['orderno']) ){
                    /*Seteo el Pedido a Quotation 3 para q no se muestre en listado de pedidos*/
                    $UpdateOrder = "UPDATE salesorders SET quotation = 3 WHERE orderno = :orderno";
                    $parameters = array(
                        ':orderno' => $OrderNo
                    );
                    Yii::app()->db->createCommand($UpdateOrder)->execute($parameters);
                    /*Elimino los pedidos de la bitacora*/
                    $DeleteLog = "DELETE FROM rh_facturacion WHERE folio = :folio AND id = :id";
                    $parameters = array(
                        ':folio' => $Folio,
                        ':id' => $Existentes[0]['id']
                    );
                    Yii::app()->db->createCommand($DeleteLog)->execute($parameters);
                }

                /*Logueo en la Bitacora por cada Periodo que se Adelanta todos hacen Referencia al Mismo OrderNo y TransNo*/
                $SQLInsertBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                              values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                $parameters = array(
                    ':folio' => $Folio,
                    ':debtorno' => $DebtorNo,
                    ':userid' => $_SESSION['UserID'],
                    ':fecha_corte' => $Periodo['fecha_linea'],
                    ':status' => "Procesada",
                    ':tipo' => 'Pago Adelantado',
                    ':systype' => 10,
                    ':debtortrans_id' => $DebtorTransID,
                    ':transno' => $TransNo,
                    ':created' => date("Y-m-d H:i:s"),
                    ':orderno' => $OrderNo,
                    ':frecuencia_pago' => $Frecuencia,
                    ':prox_factura' => '0000-00-00'
                );
                Yii::app()->db->createCommand($SQLInsertBitacora)->execute($parameters);
                $LastOrderDate = $Periodo['fecha_linea'];
            }
            /***************************************************************************************/

            if($MetodoPago == 9 || $MetodoPago == 10){
                $Dia = 25;
            }else{
                $Dia = 01;
            }

            /*Saco la Fecha Correspondiente a el Pedido que se generara despues del ultimo periodo Adelantado*/
            $LastOrderDate = explode('-', $LastOrderDate);
            switch ($Frecuencia) {
                case 1:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1] + 1, $Dia, $LastOrderDate[0]));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 1, $Dia, $GetDate[0]));
                    break;
                case 2:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1] + 6, $Dia, $LastOrderDate[0]));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 6, $Dia, $GetDate[0]));
                    break;
                case 3:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1], $Dia, $LastOrderDate[0] + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                case 4:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1], $Dia, $LastOrderDate[0] + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                default:
                    break;
            }/*End Switch*/



            // select * from stockmaster where categoryid = 'AFIL';
            $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
            $ListaPlanes = array();
            foreach ($_ListaPlanes as $Planes) {
                $ListaPlanes[$Planes['stockid']] = $Planes['description'];
            }

            /*Genero el Pedido que sigue a el Ultimo Periodo Adelantado*/
            $CostoPlan = Yii::app()->db->createCommand()->select(' costo_total ')->from(' rh_titular ')->where(" folio = '{$Folio}' ")->queryAll();
            $SO_Details2 = array(0 => array(
                'orderlineno' => 0,
                'stkcode' => $Producto,
                'unitprice' => $CostoPlan[0]['costo_total'],
                'quantity' => 1,
                'discountpercent' => 0,
                'narrative' => 'Factura Emision',
                'description' => "SERVICIO MEDICO-" . $this->GetMonth($GetDate) . " - " . $ListaPlanes[$Producto] . " - " . $FrecPagoName,
                'poline' => '',
                'rh_cost' => '0.0000',
                'itemdue' => date("Y-m-d H:i")
            ));

            $NewOrderNo = $this->CreaPedido(null, $SO_Details2, $DebtorNo);
            FB::INFO($NewOrderNo, '__________________RET ORDER NO _ PEDIDO PROGAMADO');


            /*Logueo en la Bitacora El pedido siguiente al ultimo periodo Adelantado*/
            $SQLInsertBitacoraNext = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                          values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
            $parameters = array(
                ':folio' => $Folio,
                ':debtorno' => $DebtorNo,
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => $Date,
                ':status' => "Programada",
                ':tipo' => 'Factura Programada',
                ':systype' => 10,
                ':debtortrans_id' => $DebtorTransID,
                ':transno' => $TransNo,
                ':created' => date("Y-m-d H:i:s"),
                ':orderno' => $NewOrderNo,
                ':frecuencia_pago' => $Frecuencia,
                ':prox_factura' => $NextDate
            );
            Yii::app()->db->createCommand($SQLInsertBitacoraNext)->execute($parameters);

            if($TimbrarFactura && !empty($TransNo)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'La Factura ha sido Generada con el numero de Transaccion ' . $TransNo,
                    'urlInvoice' => "../PHPJasperXML/sample1.php?transno={$TransNo}&afil=true"
                ));
            }
            return;
        }


        $this->render('pagosadelantados');
    }

    public function actionRefacturar(){

        $this->render('refacturar');
    }

/*
array(
    ['GetInvoice'] =>
    array(
        ['FInicial'] => 12
        ['FFinal'] => 12
        ['PInicial'] => 12
        ['PFinal'] => 12
    )
)
*/
    public function actionRelacionfacturas(){
        global $db;

        FB::INFO($_POST, '____________________POST');

        $Where = " ";
        if(!empty($_POST['GetInvoice']['DebtorNo'])){
            $_2GetDebtorNo = "SELECT debtorno from rh_titular WHERE folio = '{$_POST['GetInvoice']['DebtorNo']}' ";
            $_2GetDebtorNoResult = DB_query($_2GetDebtorNo, $db);
            $GetDebtorNo = DB_fetch_assoc($_2GetDebtorNoResult);
            $Where .= " AND debtortrans.debtorno='" . $GetDebtorNo['debtorno'] . "' ";
        }

        // if(!empty($_POST['GetInvoice']['Folio'])){
        //     $Where .= " AND  rh_cfd__cfd.folio = '".$_POST['GetInvoice']['Folio'] . "' ";
        // }

        /*Para Fechas de Facturas {$DateAfterCriteria} 00:00:00 */
        if(!empty($_POST['GetInvoice']['FInicial']) && !empty($_POST['GetInvoice']['FFinal'])){
            $Where .= " AND  debtortrans.trandate >= '".$_POST['GetInvoice']['FInicial'] . " 00:00:00' ";
            $Where .= " AND  debtortrans.trandate <= '".$_POST['GetInvoice']['FFinal'] . " 23:59:59' ";
        }else{
            $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
            $EndDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));

            // $Where .= " AND  rh_cfd__cfd.fecha >= '" . $StartDate . "' ";
            // $Where .= " AND  rh_cfd__cfd.fecha <= '" . $EndDate . "' ";
        }

        /*Para Fechas de Pagos*/
        if(!empty($_POST['GetInvoice']['PInicial']) && !empty($_POST['GetInvoice']['PFinal'])){
            $Where .= " AND  dtrans2.trandate >= '".$_POST['GetInvoice']['PInicial'] . " 00:00:00' ";
            $Where .= " AND  dtrans2.trandate <= '".$_POST['GetInvoice']['PFinal'] . " 23:59:59' ";
        }else{
            $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
            $EndDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            // $Where .= " AND  date(debtortrans.trandate)>= '" . $StartDate . "' ";
            // $Where .= " AND  date(debtortrans.trandate)<= '" . $EndDate . "' ";
        }

        //$Where .=  " limit 10";

        $_2GetInvoiceData = "";
        if(!empty($_POST)){

             $_2GetInvoiceData = "SELECT 'true' as is_cfd,
                    (rh_titular.folio) as AfilNo,
                    CONCAT(rh_titular.name, ' ', rh_titular.apellidos) as AfilName,
                    (rh_titular.movimientos_afiliacion) as AfilStatus,
                    (rh_cobranza.cobrador) as AfilCobrador,
                    (rh_titular.asesor) as AfilAsesor,
                    (rh_titular.fecha_ingreso) as fecha_ingreso,
                    (rh_titular.fecha_ultaum) as fecha_ultaum,
                    (rh_cobranza.stockid) as AfilProduct,
                    (rh_cobranza.frecuencia_pago) as AfilFrecuenciaPago,
                    (rh_cobranza.paymentid) as AfilMetodoPago,
                    CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura,
                    (rh_cfd__cfd.fecha) as FechaGenera,
                    max(dtrans2.trandate) as FechaPago,
                    (debtortrans.tipo_factura) as TipoFactura,
                    (debtortrans.rh_status) as StatusFactura,
                    (debtortrans.alloc) as LOPAGADO,
                    (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) as SALDO,
                    (rh_foliosasignados.tipo_membresia) as TipoFolio,
                    rh_cfd__cfd.no_certificado,
                    rh_cfd__cfd.fk_transno,
                    rh_cfd__cfd.uuid,
                    debtorsmaster.name,
                    debtortrans.trandate,
                    rh_cfd__cfd.id_debtortrans,
                    debtortrans.ovamount/debtortrans.rate as ovamount,
                    debtortrans.ovgst/debtortrans.rate as ovgst,
                    (debtortrans.ovamount+debtortrans.ovgst)/debtortrans.rate as total,
                    debtortrans.id,
                    debtortrans.type,
                    or_motivosnotascredito.descripcion
                    FROM debtortrans
                    LEFT JOIN  rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtortrans.id
                    JOIN debtorsmaster ON debtortrans.debtorno = debtorsmaster.debtorno
                    LEFT JOIN custallocns ca1 on debtortrans.id = ca1.transid_allocto
                    LEFT JOIN debtortrans dtrans2 on ca1.transid_allocfrom = dtrans2.id
                    LEFT JOIN rh_titular ON debtortrans.debtorno = rh_titular.debtorno
                    LEFT JOIN rh_cobranza ON rh_titular.folio = rh_cobranza.folio
                    LEFT JOIN rh_foliosasignados on rh_foliosasignados.folio = rh_titular.folio
                    LEFT JOIN  or_motivosnotascredito ON or_motivosnotascredito.id = debtortrans.shipvia
                WHERE 1=1 " . $Where . " AND debtortrans.type in(10) GROUP BY debtortrans.id";
/*  AND rh_titular.movimientos_afiliacion = 'Activo'  */

                $_2GetInvoiceDataResult = DB_query($_2GetInvoiceData, $db);
                $_InvoiceQTY = DB_num_rows($_2GetInvoiceDataResult);
        }
        $i=0;
        while ( $_InvoiceData = DB_fetch_assoc($_2GetInvoiceDataResult)) {
            $InvoiceData[] = $_InvoiceData;
             $_2GetCreditNotes = "SELECT
                    (debtortrans.rh_status) AS StatusFactura,
                    (debtortrans.alloc) AS LOPAGADO,
                    (debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount- debtortrans.alloc) AS SALDO,
                    debtortrans.ovamount/debtortrans.rate AS ovamount,
                    debtortrans.ovgst/debtortrans.rate AS ovgst,
                    (debtortrans.ovamount+debtortrans.ovgst)/debtortrans.rate AS total,
                    debtortrans.id,
                    debtortrans.type,
                        or_motivosnotascredito.descripcion,
                    CONCAT(if(rh_cfd__cfd.serie is null,'',rh_cfd__cfd.serie),if(rh_cfd__cfd.folio is null,'',rh_cfd__cfd.folio),'[',debtortrans.transno,']') AS NoNOTA,
                    (debtortrans.trandate) as FechaGenera,
                   '".$_InvoiceData['TipoFactura']."' as TipoFactura
                     FROM debtortrans
            JOIN custallocns ca1 on debtortrans.id = ca1.transid_allocfrom
            LEFT JOIN  rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtortrans.id
            LEFT JOIN  or_motivosnotascredito ON or_motivosnotascredito.id = debtortrans.shipvia
            WHERE type = 11 and ca1.transid_allocto = ".$_InvoiceData['id'];

            $_2GetInvoiceDataResultNC = DB_query($_2GetCreditNotes, $db);

            $query = "SELECT 
                    fecha_reactivacion from rh_movimientos_afiliacion where folio=".$_InvoiceData["AfilNo"]." order by id desc limit 1";
            $res = DB_query($query, $db);
            $data = DB_fetch_assoc($res);
            $InvoiceData[$i]["fecha_reactivacion"]=$data["fecha_reactivacion"];
            $i++;

             while ( $_InvoiceDataNC = DB_fetch_assoc($_2GetInvoiceDataResultNC)) {
                $InvoiceData[] = array_merge($_InvoiceData,$_InvoiceDataNC);
            $i++;
            }
        }
        //exit();
        //Cobrador  Asesor  Producto    Frec.Pago   Form.Pago
        $ListaCobradores = CHtml::listData(Cobradores::model()->findAll(), 'id', 'nombre');
        $ListaAsesores = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');
        $_2ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = CHtml::listData($_2ListaPlanes, 'stockid', 'description');
        $ListaFrecuenciapagos = CHtml::listData(Frecuenciapago::model()->findAll(), 'id', 'frecuencia');
        $ListaMetodosdePago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaTipoFacturas = CHtml::listData(Tipofacturas::model()->findAll(), 'id', 'tipo');

        //FB::INFO($_2GetInvoiceData,'____________________________2GetInvoiceData');
//print_r($InvoiceData);

        $this->render("relacionfacturas",array(
            "InvoiceData" => $InvoiceData,
            "ListaCobradores" => $ListaCobradores,
            "ListaAsesores" => $ListaAsesores,
            "ListaPlanes" => $ListaPlanes,
            "ListaFrecuenciapagos" => $ListaFrecuenciapagos,
            "ListaMetodosdePago" => $ListaMetodosdePago,
            "ListaTipoFacturas" => $ListaTipoFacturas
        ));

    }

      /* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/

    public function actionEmisionMasiva(){
        FB::INFO($_POST,'_______________POST');

        if(!empty($_POST))
        {

            $WhereString = " stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total > 0 ";

                // AND date_format(date(titular.fecha_ingreso),'%m') !=  date_format(now(),'%m')

            if (!empty($_POST['paymentid'])) {
                $_2MetodoPago = implode(",", $_POST['paymentid']);
                $WhereString .= " AND cobranza.paymentid IN ({$_2MetodoPago}) ";
            }

            if (!empty($_POST['frecuencia_pago'])) {
                $_2FrecuenciaPago = implode(",", $_POST['frecuencia_pago']);
                $WhereString .= " AND cobranza.frecuencia_pago IN ({$_2FrecuenciaPago}) ";
            }

            // Agregado para mostrar solo las sucursales de la empresa padre seleccionada
            $EmpresaPadre = $_POST['EmpresaPadre']; // folio de rh_titular
            $WhereString .= " AND or_empresashijo.id_empresapadre in (select id from rh_titular where folio = $EmpresaPadre)";

             /* ====================================================
            AGREGADO POR DANIEL VILLARREAL 03 DE DICIEMBRE DEL 2015
            SE OBTIENE EL VALOR DEL PERIODO ACTUAL O SIGUIENTE
            ===================================================== */
            if (isset($_POST['CambiarMesFacturacion'])) {
                // 0 = Mes Actual o Periodo Actual y 1 = Mes siguiente o Periodo Siguiente
                $CambiarMesFacturacion = $_POST['CambiarMesFacturacion'];
            }else{ $CambiarMesFacturacion = 0; }
            /* ====================================================
            TERMINA 
            ===================================================== */


            FB::INFO($WhereString,'____________________WHERE');
            $GetDebtorData = Yii::app()->db->createCommand()
            ->select("titular.folio,
                titular.debtorno,
                CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
                titular.taxref,
                (titular.address10) as PostalCode,
                (cobranza.stockid) as CPlan,
                (cobranza.paymentid) as CPaymentName,
                (cobranza.frecuencia_pago) as CFrecPago,
                fasignados.tipo_membresia,
                titular.fecha_ingreso,
                titular.costo_total")
            ->from("rh_titular titular")
            ->join("rh_cobranza cobranza", "titular.folio = cobranza.folio")
            ->join("rh_foliosasignados fasignados", "titular.folio = fasignados.folio")
            ->join("stockmaster stkm", "cobranza.stockid = stkm.stockid")
            // Agregado para que solo muestre las hijas de la empresa seleccionada
            ->join("or_empresashijo","or_empresashijo.id_sucursal = titular.id")
            ->where($WhereString)
            //->limit(10)
            ->queryAll();

            foreach ($GetDebtorData as $AfilNo) {
                # code...
                $Frecuencia = $AfilNo['CFrecPago'];
                $MetodoPago = $AfilNo['CPaymentName'];
                $Fecha_Ingreso = $AfilNo['fecha_ingreso'];

                if($MetodoPago == 9 || $MetodoPago == 10){
                    $DiaEmision = 25;
                    $TipoEmision = "Tarjetas";
                }else{
                    $TipoEmision = "Efectivo";
                    $DiaEmision = '01';
                }
                $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                $CreateOrders = true;
                // Verificamos si le toca o no pedido pedido
                /*MODIFICADO POR DANIEL VILLARREAL EL 21 DE FEBRERO DEL 2016*/
                $VerifyOrder = $this->VerificarOrden($Frecuencia,$DiaEmision,$Date,$CambiarMesFacturacion,$AfilNo['folio'],$AfilNo['costo_total'],0);
                if($VerifyOrder['respuesta']){
                   
                    $AfilNo['FechaPedido'] = $VerifyOrder['fechapedido'];
                    $GetDebtorData2[] = $AfilNo;
                }
            }
        }

        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }
        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(' paymentid NOT IN (3,4,6,7)'), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(/*" sucursal = 'MTY' "*/), 'id', 'frecuencia');

        // Lista de empresas padre
        $EmpresasPadre=Yii::app()->db->createCommand()
        ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
        ->from('or_empresaspadre')
        ->join('rh_titular','rh_titular.id = or_empresaspadre.id_empresapadre')
        ->order('name asc')
        ->queryAll();


        $this->render("emisionmasiva", array(
            'DebtorData' => $GetDebtorData2,
            'ListaFormasPago' => $ListaFormasPago,
            'ListaFrecuenciaPago' => $ListaFrecuenciaPago,
            'ListaPlanes' => $ListaPlanes,
            'ListaEmpresasPadre' => $EmpresasPadre
            ));
    } // end actionEmisionMasiva

    /* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/
    public function actionFacturaemisionmasiva(){

        //FB::INFO($_POST,'__________________________POST');

        /* ======================================
        ========================================
        OPCION AGREGADA EN BASE A LA OPCION SELECCIONADA PARA LA FECHA , 
            MES ACTUAL - NO SUMA MES, MES SIGUIENTE - SUMA 1 MES
        REALIZADO POR DANIEL VILLARREAL EL 1 DE DICIEMBRE DEL 2015
        =========================================
        ========================================= */
        $CambiarMesFacturacion = $_POST['Emision']['CambiarMesFacturacion'];
        /* ======================================
        =========================================
                        TERMINA
        =========================================
        ======================================= */

        // Obtenemos el ID de la empresa padre
        $EmpresaPadre =  $_POST['Emision']['EmpresaPadre'];

        if(!empty($_POST['Emision']['Folios'])){
            parse_str($_POST['Emision']['Folios'], $ParseDataIDs);

            $arrayFoliosSucursales = array();
            foreach ($ParseDataIDs['TimbrarFactura'] as $key => $FolioSucursal) {
                $arrayFoliosSucursales[] = array ('FolioSucursal'=>$FolioSucursal);
            }

            $DebtorNo = $this->GetDebtorNo($EmpresaPadre);
            $this->CreaPedidoEmisionMasiva($DebtorNo,$EmpresaPadre,$CambiarMesFacturacion,$arrayFoliosSucursales);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Facturado y Timbrado Correctamente... "
            ));
            exit;
        }

        if(empty($_POST['Emision']['Folios']) && isset($_POST['Emision']['Folios'])){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Seleccione los Folios a Facturar... "
            ));
            exit;
        }
        FB::INFO('_____END');
    } //end actionFacturaemisionMasiva

/* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/
public function CreaPedidoEmisionMasiva($DebtorNo,$EmpresaPadre,$CambiarMesFacturacion,$arrayFoliosSucursales){

    
    // Obtenemos los datos de la empresa padre 
    $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,
                cobranza.folio,
                cobranza.stockid,
                cobranza.frecuencia_pago,
                titular.fecha_ingreso,
                (titular.costo_total) as COSTO_TOTAL,
                cobranza.fecha_corte,
                fp.frecuencia,pm.paymentid,
                pm.paymentname,
                cobranza.cobro_inscripcion')
            ->from('debtorsmaster')
            ->join('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->join('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

    $FolioPadre = $DebtorData[0]['folio'];

    // Obtenemos los datos de la sucursal
    if(count($arrayFoliosSucursales)>0)
    {
        $partida = 1; // numero de partida
        $SO_Details = array(); // arreglo para salesorderdetails
        $arrayRhFacturacion= array(); // arreglo para rhfacturacion

        foreach($arrayFoliosSucursales as $rowdatossucursal){

            // Procesamos los datos de la sucursal
            $DebtorDataSuc = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,
                cobranza.folio,
                cobranza.stockid,
                cobranza.frecuencia_pago,
                titular.fecha_ingreso,
                (titular.costo_total) as COSTO_TOTAL,
                cobranza.fecha_corte,
                fp.frecuencia,pm.paymentid,
                pm.paymentname,
                cobranza.cobro_inscripcion')
            ->from('debtorsmaster')
            ->join('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->join('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->join("or_empresashijo","or_empresashijo.id_sucursal = titular.id")
            ->where('cobranza.folio='.$rowdatossucursal['FolioSucursal'].'')->queryAll();


             /*Valida Fechas Segun Frecuencia de Pago*/
                $Frecuencia = $DebtorDataSuc[0]['frecuencia_pago'];
                $Fecha_Ingreso = $DebtorDataSuc[0]['fecha_ingreso'];
                $MetodoPago = $DebtorDataSuc[0]['paymentid'];
                $Folio = $DebtorDataSuc[0]['folio'];

                if($MetodoPago == 9 || $MetodoPago == 10){
                    $DiaEmision = 25;
                    $TipoEmision = "Tarjetas";
                    $IDTipoEmision = 2;
                    //$Mas1Mes = 1;
                }else{
                    $DiaEmision = '01';
                    $TipoEmision = "Efectivo";
                    $IDTipoEmision = 1;
                //$Mas1Mes = 0;
                }

                 // 0 = MesActual y 1 = MesSiguiente 
                $Mas1Mes = $CambiarMesFacturacion;


                $CreateOrders = false;
                switch ($Frecuencia) {
                    case 1: // mensual
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;

                        $MesAnio = $this->GetMonth($Date, 1, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];

                        
                        break;
                    case 2: // semestral
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;
                        $MesAnio = $this->GetMonth($Date, 2, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    case 3: // anual
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;

                        $MesAnio = $this->GetMonth($Date, 3, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    case 4: // anual insen
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;
                        $MesAnio = $this->GetMonth($Date, 4,$Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    default:
                        $CreateOrders = false;
                        break;


                } // end switch

                if($DebtorDataSuc[0]['tipopersona']=='FISICA')
                {
                    $NombreEmpresa  = $DebtorDataSuc[0]['name'].' '. $DebtorDataSuc[0]['apellidos'];
                }
                else
                {
                    $NombreEmpresa  = $DebtorDataSuc[0]['name'];
                }

                    $Fecha_Pedido = date("Y-m-d H:i:s");    



                        if($CreateOrders == true){

                             // Insertamos en un arreglo los detalles de las partidas de la factura
                            $SO_Details[] =array(
                                'orderlineno' => $partida,
                                'stkcode' => $DebtorDataSuc[0]['stockid'],
                                'unitprice' => $DebtorDataSuc[0]['COSTO_TOTAL'],
                                'quantity' => 1,
                                'discountpercent' => 0,
                                'narrative' => $Folio.'/EMISION-' . $Fecha_Pedido,
                                'description' => $NombreEmpresa.' - '.$Description,
                                'poline' => '',
                                'rh_cost' => '0.0000',
                                'itemdue' => $Fecha_Pedido
                            );

                            // Obtenemos la fecha corte
                            if($Mas1Mes==1)
                            {
                                $Fecha_Corte = strtotime ("+1 Months", strtotime ($Date));
                                $Fecha_Corte =  date ( 'Y-m-d' , $Fecha_Corte);
                            }else
                            {
                                $Fecha_Corte = $Date;
                            }

                            // Arreglo para insertar en rh_facturacion
                            $arrayRhFacturacion[] = array(
                                'folio' => $Folio,
                                'partida' => $partida,
                                'fecha'=>$Fecha_Corte
                            );


                           
                        } // if($CreateOrders == true)


            $partida++;
        } //end foreach($arrayFoliosSucursales as $rowdatosucursal)

        if($CreateOrders == true and $partida>1){

                $FacturaCFDI = new Facturacion();
              

                $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                FB::INFO($FacturaCFDI->OrderNo,'___________-ORDERNO');

                $FacturaCFDI->FacturaPedido($FacturaCFDI->OrderNo, $FolioPadre, $IDTipoEmision);
                //FacturaPedido($OrderNo, $FolioPadre = null, $TipoFactura = 0) {
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');

                //Timbrar Factura
                $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);

                if ($FacturaCFDI->StatusTimbre = 'Timbrado') {

                    $GetTransNo = Yii::app()->db->createCommand()
                    ->select("transno,order_")
                    ->from("debtortrans")
                    ->where("debtortrans.id = :id", array(':id' => $FacturaCFDI->idDebtortrans))
                    ->queryAll();

                    // Obtenemos la fecha corte
                    if($Mas1Mes==1)
                    {
                        $Fecha_Corte = strtotime ("+1 Months", strtotime ($Date));
                        $Fecha_Corte =  date ( 'Y-m-d' , $Fecha_Corte);
                    }else
                    {
                        $Fecha_Corte = $Date;
                    }

                    foreach ($arrayRhFacturacion as $rowfacturacion)
                    {
                        $DebtorNoHijo = $this->GetDebtorNo($rowfacturacion['folio']);

                        $LogBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura,comentarios)
                                  values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura,:comentarios)";

                        $LogParameters = array(
                            ':folio' => $rowfacturacion['folio'],
                            ':debtorno' => $DebtorNoHijo,
                            ':userid' => $_SESSION['UserID'],
                            ':fecha_corte' => $rowfacturacion['fecha'],
                            ':status' => "Procesada",
                            ':tipo' => 'EMISION',
                            ':systype' => 10,
                            ':debtortrans_id' => $FacturaCFDI->idDebtortrans,
                            ':transno' => $GetTransNo[0]['transno'],
                            ':created' => date("Y-m-d H:i:s"),
                            ':orderno' => $FacturaCFDI->OrderNo,
                            ':frecuencia_pago' => $Frecuencia,
                            ':prox_factura' => '0000-00-00',
                            ':comentarios' => $rowfacturacion['partida']
                        );

                        Yii::app()->db->createCommand($LogBitacora)->execute($LogParameters);
                    } // end foreach ($arrayRhFacturacion as $rowfacturacion)
                   
                    

                    FB::INFO('_______TIMBRADO OK LOGUEADO OK');

               } // end if ($FacturaCFDI->StatusTimbre = 'Timbrado') 

            }// end if($CreateOrders == true and $partida>1)

        } //end if(count($arrayFoliosSucursales)>0)


    }// end CreaPedidoEmisionMasiva


    /* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/

    public function actionEmisionMasiva1concepto(){
        FB::INFO($_POST,'_______________POST');

        if(!empty($_POST))
        {

            $WhereString = " stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total >= 0 ";

                // AND date_format(date(titular.fecha_ingreso),'%m') !=  date_format(now(),'%m')

            if (!empty($_POST['paymentid'])) {
                $_2MetodoPago = implode(",", $_POST['paymentid']);
                $WhereString .= " AND cobranza.paymentid IN ({$_2MetodoPago}) ";
            }

            if (!empty($_POST['frecuencia_pago'])) {
                $_2FrecuenciaPago = implode(",", $_POST['frecuencia_pago']);
                $WhereString .= " AND cobranza.frecuencia_pago IN ({$_2FrecuenciaPago}) ";
            }

            // Agregado para mostrar solo las sucursales de la empresa padre seleccionada
            $EmpresaPadre = $_POST['EmpresaPadre']; // folio de rh_titular
            $WhereString .= " AND or_empresashijo.id_empresapadre in (select id from rh_titular where folio = $EmpresaPadre)";

             /* ====================================================
            AGREGADO POR DANIEL VILLARREAL 03 DE DICIEMBRE DEL 2015
            SE OBTIENE EL VALOR DEL PERIODO ACTUAL O SIGUIENTE
            ===================================================== */
            if (isset($_POST['CambiarMesFacturacion'])) {
                // 0 = Mes Actual o Periodo Actual y 1 = Mes siguiente o Periodo Siguiente
                $CambiarMesFacturacion = $_POST['CambiarMesFacturacion'];
            }else{ $CambiarMesFacturacion = 0; }
            /* ====================================================
            TERMINA 
            ===================================================== */


            FB::INFO($WhereString,'____________________WHERE');
            $GetDebtorData = Yii::app()->db->createCommand()
            ->select("titular.folio,
                titular.debtorno,
                CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
                titular.taxref,
                (titular.address10) as PostalCode,
                (cobranza.stockid) as CPlan,
                (cobranza.paymentid) as CPaymentName,
                (cobranza.frecuencia_pago) as CFrecPago,
                fasignados.tipo_membresia,
                titular.fecha_ingreso,
                titular.costo_total")
            ->from("rh_titular titular")
            ->join("rh_cobranza cobranza", "titular.folio = cobranza.folio")
            ->join("rh_foliosasignados fasignados", "titular.folio = fasignados.folio")
            ->join("stockmaster stkm", "cobranza.stockid = stkm.stockid")
            // Agregado para que solo muestre las hijas de la empresa seleccionada
            ->join("or_empresashijo","or_empresashijo.id_sucursal = titular.id")
            ->where($WhereString)
            //->limit(10)
            ->queryAll();
           

            foreach ($GetDebtorData as $AfilNo) {
                # code...
                $Frecuencia = $AfilNo['CFrecPago'];
                $MetodoPago = $AfilNo['CPaymentName'];
                $Fecha_Ingreso = $AfilNo['fecha_ingreso'];

                if($MetodoPago == 9 || $MetodoPago == 10){
                    $DiaEmision = 25;
                    $TipoEmision = "Tarjetas";
                }else{
                    $TipoEmision = "Efectivo";
                    $DiaEmision = '01';
                }
                $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                $CreateOrders = true;
                // Verificamos si le toca o no pedido pedido
                /*MODIFICADO POR DANIEL VILLARREAL EL 21 DE FEBRERO DEL 2016*/
                $VerifyOrder = $this->VerificarOrden($Frecuencia,$DiaEmision,$Date,$CambiarMesFacturacion,$AfilNo['folio'],$AfilNo['costo_total'],0);
                if($VerifyOrder['respuesta']){
                   
                    $AfilNo['FechaPedido'] = $VerifyOrder['fechapedido'];
                    $GetDebtorData2[] = $AfilNo;
                }

            }

        }

        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }
        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(' paymentid NOT IN (3,4,6,7)'), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(/*" sucursal = 'MTY' "*/), 'id', 'frecuencia');

        // Lista de empresas padre
        $EmpresasPadre=Yii::app()->db->createCommand()
        ->select('rh_titular.id,rh_titular.name,rh_titular.apellidos,rh_titular.tipopersona,rh_titular.taxref,rh_titular.folio')
        ->from('or_empresaspadre')
        ->join('rh_titular','rh_titular.id = or_empresaspadre.id_empresapadre')
        ->order('name asc')
        ->queryAll();


        $this->render("emisionmasiva1concepto", array(
            'DebtorData' => $GetDebtorData2,
            'ListaFormasPago' => $ListaFormasPago,
            'ListaFrecuenciaPago' => $ListaFrecuenciaPago,
            'ListaPlanes' => $ListaPlanes,
            'ListaEmpresasPadre' => $EmpresasPadre
            ));
    } // end actionEmisionMasiva1concepto
    /* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/
    public function actionFacturaemisionMasiva1Concepto(){

        //FB::INFO($_POST,'__________________________POST');

        /* ======================================
        ========================================
        OPCION AGREGADA EN BASE A LA OPCION SELECCIONADA PARA LA FECHA , 
            MES ACTUAL - NO SUMA MES, MES SIGUIENTE - SUMA 1 MES
        REALIZADO POR DANIEL VILLARREAL EL 1 DE DICIEMBRE DEL 2015
        =========================================
        ========================================= */
        $CambiarMesFacturacion = $_POST['Emision']['CambiarMesFacturacion'];
        /* ======================================
        =========================================
                        TERMINA
        =========================================
        ======================================= */

        // Obtenemos el ID de la empresa padre
        $EmpresaPadre =  $_POST['Emision']['EmpresaPadre'];

        if(!empty($_POST['Emision']['Folios'])){
            parse_str($_POST['Emision']['Folios'], $ParseDataIDs);

            $arrayFoliosSucursales = array();
            foreach ($ParseDataIDs['TimbrarFactura'] as $key => $FolioSucursal) {
                $arrayFoliosSucursales[] = array ('FolioSucursal'=>$FolioSucursal);
            }

            $DebtorNo = $this->GetDebtorNo($EmpresaPadre);
            $this->CreaPedidoEmisionMasiva1Concepto($DebtorNo,$EmpresaPadre,$CambiarMesFacturacion,$arrayFoliosSucursales);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Facturado y Timbrado Correctamente... "
            ));
            exit;
        }

        if(empty($_POST['Emision']['Folios']) && isset($_POST['Emision']['Folios'])){
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Seleccione los Folios a Facturar... "
            ));
            exit;
        }
        FB::INFO('_____END');
    } //end actionFacturaemisionMasiva1Concepto

/* ===========================================
    METODO AGREGADO PARA FACTURAR N CANTIDAD DE SUCURSALES EN 1 FACTURA,
    POR DANIEL VILLARREAL EL 23 DE DICIEMBRE DEL 2015
    ==============================================*/
public function CreaPedidoEmisionMasiva1Concepto($DebtorNo,$EmpresaPadre,$CambiarMesFacturacion,$arrayFoliosSucursales){

    
    // Obtenemos los datos de la empresa padre 
    $DebtorData = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,
                cobranza.folio,
                cobranza.stockid,
                cobranza.frecuencia_pago,
                titular.fecha_ingreso,
                (titular.costo_total) as COSTO_TOTAL,
                cobranza.fecha_corte,
                fp.frecuencia,pm.paymentid,
                pm.paymentname,
                cobranza.cobro_inscripcion')
            ->from('debtorsmaster')
            ->join('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->join('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

    $FolioPadre = $DebtorData[0]['folio'];

    // Obtenemos los datos de la sucursal
    if(count($arrayFoliosSucursales)>0)
    {
        $partida = 1; // numero de partida
        $SO_Details = array(); // arreglo para salesorderdetails
        $arrayRhFacturacion= array(); // arreglo para rhfacturacion

        foreach($arrayFoliosSucursales as $rowdatossucursal){

            // Procesamos los datos de la sucursal
            $DebtorDataSuc = Yii::app()->db->createCommand()
            ->select(' debtorsmaster.*, stkm.description,
                cobranza.folio,
                cobranza.stockid,
                cobranza.frecuencia_pago,
                titular.fecha_ingreso,
                (titular.costo_total) as COSTO_TOTAL,
                cobranza.fecha_corte,
                fp.frecuencia,pm.paymentid,
                pm.paymentname,
                cobranza.cobro_inscripcion')
            ->from('debtorsmaster')
            ->join('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
            ->join('rh_titular titular', 'titular.debtorno = debtorsmaster.debtorno')
            ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
            ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
            ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
            ->join("or_empresashijo","or_empresashijo.id_sucursal = titular.id")
            ->where('cobranza.folio='.$rowdatossucursal['FolioSucursal'].'')->queryAll();


             /*Valida Fechas Segun Frecuencia de Pago*/
                $Frecuencia = $DebtorDataSuc[0]['frecuencia_pago'];
                $Fecha_Ingreso = $DebtorDataSuc[0]['fecha_ingreso'];
                $MetodoPago = $DebtorDataSuc[0]['paymentid'];
                $Folio = $DebtorDataSuc[0]['folio'];

                if($MetodoPago == 9 || $MetodoPago == 10){
                    $DiaEmision = 25;
                    $TipoEmision = "Tarjetas";
                    $IDTipoEmision = 2;
                    //$Mas1Mes = 1;
                }else{
                    $DiaEmision = '01';
                    $TipoEmision = "Efectivo";
                    $IDTipoEmision = 1;
                //$Mas1Mes = 0;
                }

                 // 0 = MesActual y 1 = MesSiguiente 
                $Mas1Mes = $CambiarMesFacturacion;


                $CreateOrders = false;
                switch ($Frecuencia) {
                    case 1: // mensual
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;

                        $MesAnio = $this->GetMonth($Date, 1, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];

                        
                        break;
                    case 2: // semestral
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,2, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;
                        $MesAnio = $this->GetMonth($Date, 2, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    case 3: // anual
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;

                        $MesAnio = $this->GetMonth($Date, 3, $Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    case 4: // anual insen
                        $GetFechas = $this->GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                        $ToDay = date('Y-m-d');
                        $GetTodayParts = explode("-", $ToDay);
                            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                            $CreateOrders = true;
                        $MesAnio = $this->GetMonth($Date, 4,$Mas1Mes);
                        $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorDataSuc[0]['description'] . " - " . $DebtorDataSuc[0]['frecuencia'] . " - " . $DebtorDataSuc[0]['paymentname'];
                        break;
                    default:
                        $CreateOrders = false;
                        break;


                } // end switch

                if($DebtorDataSuc[0]['tipopersona']=='FISICA')
                {
                    $NombreEmpresa  = $DebtorDataSuc[0]['name'].' '. $DebtorDataSuc[0]['apellidos'];
                }
                else
                {
                    $NombreEmpresa  = $DebtorDataSuc[0]['name'];
                }

                    $Fecha_Pedido = date("Y-m-d H:i:s");    



                        if($CreateOrders == true){

                             // Insertamos en un arreglo los detalles de las partidas de la factura
                            $SO_Details[] =array(
                                'orderlineno' => $partida,
                                'stkcode' => $DebtorDataSuc[0]['stockid'],
                                'unitprice' => $DebtorDataSuc[0]['COSTO_TOTAL'],
                                'quantity' => 1,
                                'discountpercent' => 0,
                                'narrative' => $Folio.'/EMISION-' . $Fecha_Pedido,
                                'description' => $NombreEmpresa.' - '.$Description,
                                'poline' => '',
                                'rh_cost' => '0.0000',
                                'itemdue' => $Fecha_Pedido
                            );

                            // Obtenemos la fecha corte
                            if($Mas1Mes==1)
                            {
                                $Fecha_Corte = strtotime ("+1 Months", strtotime ($Date));
                                $Fecha_Corte =  date ( 'Y-m-d' , $Fecha_Corte);
                            }else
                            {
                                $Fecha_Corte = $Date;
                            }

                            // Arreglo para insertar en rh_facturacion
                            $arrayRhFacturacion[] = array(
                                'folio' => $Folio,
                                'partida' => $partida,
                                'fecha'=>$Fecha_Corte
                            );


                           
                        } // if($CreateOrders == true)


            $partida++;
        } //end foreach($arrayFoliosSucursales as $rowdatosucursal)

        if($CreateOrders == true and $partida>1){

                $FacturaCFDI = new Facturacion();

                $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                FB::INFO($FacturaCFDI->OrderNo,'___________-ORDERNO');

                $FacturaCFDI->FacturaPedido($FacturaCFDI->OrderNo, $FolioPadre, $IDTipoEmision);
                //FacturaPedido($OrderNo, $FolioPadre = null, $TipoFactura = 0) {
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');

                //Timbrar Factura
                $FacturaCFDI->Timbrar1Concepto($FacturaCFDI->idDebtortrans, $Folio);

                if ($FacturaCFDI->StatusTimbre = 'Timbrado') {

                    $GetTransNo = Yii::app()->db->createCommand()
                    ->select("transno,order_")
                    ->from("debtortrans")
                    ->where("debtortrans.id = :id", array(':id' => $FacturaCFDI->idDebtortrans))
                    ->queryAll();

                    

                    foreach ($arrayRhFacturacion as $rowfacturacion)
                    {
                        $DebtorNoHijo = $this->GetDebtorNo($rowfacturacion['folio']);

                        $LogBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura,comentarios)
                                  values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura,:comentarios)";

                        $LogParameters = array(
                            ':folio' => $rowfacturacion['folio'],
                            ':debtorno' => $DebtorNoHijo,
                            ':userid' => $_SESSION['UserID'],
                            ':fecha_corte' => $rowfacturacion['fecha'],
                            ':status' => "Procesada",
                            ':tipo' => 'EMISION',
                            ':systype' => 10,
                            ':debtortrans_id' => $FacturaCFDI->idDebtortrans,
                            ':transno' => $GetTransNo[0]['transno'],
                            ':created' => date("Y-m-d H:i:s"),
                            ':orderno' => $FacturaCFDI->OrderNo,
                            ':frecuencia_pago' => $Frecuencia,
                            ':prox_factura' => '0000-00-00',
                            ':comentarios' => $rowfacturacion['partida']
                        );

                        Yii::app()->db->createCommand($LogBitacora)->execute($LogParameters);
                    } // end foreach ($arrayRhFacturacion as $rowfacturacion)
                   
                    

                    FB::INFO('_______TIMBRADO OK LOGUEADO OK');

               } // end if ($FacturaCFDI->StatusTimbre = 'Timbrado') 

            }// end if($CreateOrders == true and $partida>1)

        } //end if(count($arrayFoliosSucursales)>0)


    }// end CreaPedidoEmisionMasiva1Concepto

    public function actionPagosadelantadosnuevo(){
        global $db;
        FB::INFO($_POST,'_______________________________POST1');

        if(!empty($_POST['ProcessData']['Periodos'])){
            $ParsePeriods = parse_str($_POST['ProcessData']['Periodos'], $Periods);
            //$_POST['ProcessData']['Periodos'] = $Periods;
            FB::INFO($_POST,'_______________________________POST2');

            $AdelantaPeriodos = $Periods;
            $Folio = $_POST['ProcessData']['Folio'];
            $Fecha = $_POST['ProcessData']['Fecha'];
            $Concepto = $_POST['ProcessData']['Concepto'];
            $Importe = $_POST['ProcessData']['Importe'];
            $MetodoPago = $_POST['ProcessData']['MetodoPago'];
            $Frecuencia = $_POST['ProcessData']['FrecPago'];
            $Producto = $_POST['ProcessData']['Producto'];
            $Costo_Total = $_POST['ProcessData']['Costo_Total'];
            $FrecPagoName = $_POST['ProcessData']['FrecPagoName'];
            $DebtorNo = $this->GetDebtorNo($_POST['ProcessData']['Folio']);

            FB::INFO($DebtorNo,'_____________________DEBTORNO');

            $SO_Details = array(0 => array(
                'orderlineno' => 0,
                'stkcode' => $Producto,
                'unitprice' => $Importe,
                'quantity' => 1,
                'discountpercent' => 0,
                'narrative' => 'Facturacion de Periodos Adelantados',
                'description' => $Concepto,
                'poline' => '',
                'rh_cost' => '0.0000',
                'itemdue' => date("Y-m-d H:i")
            ));

            /*Crea Pedido con los Pagos Adelantados*/
            $FacturaCFDI = new Facturacion();
            $FacturaCFDI->CreaPedido(null, $SO_Details, $DebtorNo);
            //$OrderNo = $this->CreaPedido(null, $SO_Details, $DebtorNo);
            FB::INFO($FacturaCFDI->OrderNo,'___________________ORDERNO');

            /*Factura Pedido con los Pagos Adelantados*/
            if(!empty($FacturaCFDI->OrderNo)){
                $FacturaCFDI->FacturaPedido($FacturaCFDI->OrderNo, $Folio,1);
                FB::INFO($FacturaCFDI->idDebtortrans, '__________________DebtorTransID');
            }

            FB::INFO($_POST,'_______________________________________POST3');
            /*Timbrar Factura con los Pagos Adelantados*/
            $FacturaCFDI->Timbrar($FacturaCFDI->idDebtortrans, $Folio);
            if($FacturaCFDI->StatusTimbre = 'Timbrado'){

                /* DEFINIDAS POR DANIEL VILLARREAL - 21 DE FEBRERO  */
                $DebtorTransID = $FacturaCFDI->idDebtortrans;
                            $OrderNo = $FacturaCFDI->OrderNo;
                            /* TERMINA */

                $_GetTransNo = "SELECT transno,order_ FROM debtortrans WHERE debtortrans.id = {$FacturaCFDI->idDebtortrans} AND debtortrans.type = 10";
                $_2GetTransNo = DB_query($_GetTransNo, $db);
                $GetTransNo = DB_fetch_assoc($_2GetTransNo);
                $TransNo = $GetTransNo['transno'];

                /*Logueo en la Bitacora el Pedido que se Facturo*/
                $SQLInsertBitacoraPA = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                              values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                $parameters = array(
                    ':folio' => $Folio,
                    ':debtorno' => $DebtorNo,
                    ':userid' => $_SESSION['UserID'],
                    ':fecha_corte' => $Fecha,
                    ':status' => "PagoAdelantado",
                    ':tipo' => 'Pago Adelantado',
                    ':systype' => 10,
                    ':debtortrans_id' => $DebtorTransID,
                    ':transno' => $TransNo,
                    ':created' => date("Y-m-d H:i:s"),
                    ':orderno' => $OrderNo,
                    ':frecuencia_pago' => $Frecuencia,
                    ':prox_factura' => '0000-00-00'
                );
                #Yii::app()->db->createCommand($SQLInsertBitacoraPA)->execute($parameters);
            }

            /*Por cada periodo Adelantado Logeo en la Bitacora con el OrderNo y el TransNo del pedido con Los periodos Adelantados*/
            foreach ($AdelantaPeriodos as $Periodo) {
                /*Busco Pedidos pendientes para las fechas que se estan adelantando*/
                $Existentes = Yii::app()->db->createCommand()
                    ->select(' id, fecha_corte,orderno ')
                    ->from(' rh_facturacion ')
                    ->where(" folio = '{$Folio}' AND fecha_corte = '{$Periodo['fecha_linea']}' ")
                    ->queryAll();
                if(!empty($Existentes[0]['id']) && !empty($Existentes[0]['orderno']) ){
                    /*Seteo el Pedido a Quotation 3 para q no se muestre en listado de pedidos*/
                    $UpdateOrder = "UPDATE salesorders SET quotation = 3 WHERE orderno = :orderno";
                    $parameters = array(
                        ':orderno' => $OrderNo
                    );
                    Yii::app()->db->createCommand($UpdateOrder)->execute($parameters);
                    /*Elimino los pedidos de la bitacora*/
                    $DeleteLog = "DELETE FROM rh_facturacion WHERE folio = :folio AND id = :id";
                    $parameters = array(
                        ':folio' => $Folio,
                        ':id' => $Existentes[0]['id']
                    );
                    Yii::app()->db->createCommand($DeleteLog)->execute($parameters);
                }

                /*Logueo en la Bitacora por cada Periodo que se Adelanta todos hacen Referencia al Mismo OrderNo y TransNo*/
                $SQLInsertBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                              values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                $parameters = array(
                    ':folio' => $Folio,
                    ':debtorno' => $DebtorNo,
                    ':userid' => $_SESSION['UserID'],
                    ':fecha_corte' => $Periodo['fecha_linea'],
                    ':status' => "Procesada",
                    ':tipo' => 'Pago Adelantado',
                    ':systype' => 10,
                    ':debtortrans_id' => $DebtorTransID,
                    ':transno' => $TransNo,
                    ':created' => date("Y-m-d H:i:s"),
                    ':orderno' => $OrderNo,
                    ':frecuencia_pago' => $Frecuencia,
                    ':prox_factura' => '0000-00-00'
                );
                Yii::app()->db->createCommand($SQLInsertBitacora)->execute($parameters);
                $LastOrderDate = $Periodo['fecha_linea'];
            }
            /***************************************************************************************/

            if($MetodoPago == 9 || $MetodoPago == 10){
                $Dia = 25;
            }else{
                $Dia = 01;
            }

            /*Saco la Fecha Correspondiente a el Pedido que se generara despues del ultimo periodo Adelantado*/
            $LastOrderDate = explode('-', $LastOrderDate);
            switch ($Frecuencia) {
                case 1:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1] + 1, $Dia, $LastOrderDate[0]));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 1, $Dia, $GetDate[0]));
                    break;
                case 2:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1] + 6, $Dia, $LastOrderDate[0]));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1] + 6, $Dia, $GetDate[0]));
                    break;
                case 3:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1], $Dia, $LastOrderDate[0] + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                case 4:
                    $Date = date('Y-m-d', mktime(0, 0, 0, $LastOrderDate[1], $Dia, $LastOrderDate[0] + 1));
                    $GetDate = explode('-', $Date);
                    $NextDate = date('Y-m-d', mktime(0, 0, 0, $GetDate[1], $Dia, $GetDate[0] + 1));
                    break;
                default:
                    break;
            }/*End Switch*/



            // select * from stockmaster where categoryid = 'AFIL';
            $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
            $ListaPlanes = array();
            foreach ($_ListaPlanes as $Planes) {
                $ListaPlanes[$Planes['stockid']] = $Planes['description'];
            }

            /*Genero el Pedido que sigue a el Ultimo Periodo Adelantado*/
            $CostoPlan = Yii::app()->db->createCommand()->select(' costo_total ')->from(' rh_titular ')->where(" folio = '{$Folio}' ")->queryAll();
            $SO_Details2 = array(0 => array(
                'orderlineno' => 0,
                'stkcode' => $Producto,
                'unitprice' => $CostoPlan[0]['costo_total'],
                'quantity' => 1,
                'discountpercent' => 0,
                'narrative' => 'Factura Emision',
                'description' => "SERVICIO MEDICO-" . $this->GetMonth($GetDate) . " - " . $ListaPlanes[$Producto] . " - " . $FrecPagoName,
                'poline' => '',
                'rh_cost' => '0.0000',
                'itemdue' => date("Y-m-d H:i")
            ));

            #$NewOrderNo = $this->CreaPedido(null, $SO_Details2, $DebtorNo);
            FB::INFO($NewOrderNo, '__________________RET ORDER NO _ PEDIDO PROGAMADO');


            /*Logueo en la Bitacora El pedido siguiente al ultimo periodo Adelantado*/
            $SQLInsertBitacoraNext = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                          values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
            $parameters = array(
                ':folio' => $Folio,
                ':debtorno' => $DebtorNo,
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => $Date,
                ':status' => "Programada",
                ':tipo' => 'Factura Programada',
                ':systype' => 10,
                ':debtortrans_id' => $DebtorTransID,
                ':transno' => $TransNo,
                ':created' => date("Y-m-d H:i:s"),
                ':orderno' => $NewOrderNo,
                ':frecuencia_pago' => $Frecuencia,
                ':prox_factura' => $NextDate
            );
            #Yii::app()->db->createCommand($SQLInsertBitacoraNext)->execute($parameters);
              /*
            CAMBIAMOS EL TIPO DE FACTURA A 8  Y REFERENCIA
            EMISION-2015-12-01 11:17:38
            */
            $UpdateDebtor = "UPDATE debtortrans SET tipo_factura = 8, reference = :reference WHERE id = :id";
            $parameters = array(
                ':id' =>$FacturaCFDI->idDebtortrans,
                ':reference' =>'EMISION-'.date('Y-m-d H:i:s').' (PAGO ADELANTADO)',
            );
            Yii::app()->db->createCommand($UpdateDebtor)->execute($parameters);
            /*
                TERMINA 18 DE ABRIL DEL 2016
            */
                
            /*CORREGIDO POR DANIEL VILLARREA EL 21 DE FEBRERO DEL 2016*/
            #if($TimbrarFactura && !empty($TransNo)){
            if($FacturaCFDI->StatusTimbre = 'Timbrado' && !empty($TransNo)){
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'La Factura ha sido Generada con el numero de Transaccion ' . $TransNo,
                    'urlInvoice' => "../PHPJasperXML/sample1.php?transno={$TransNo}&afil=true"
                ));
            }
            return;
        }


        $this->render('pagosadelantados');
    }// end     public function actionPagosadelantadosnuevo(){


    public function actionPagosadelantadosverificar(){
        // Obtenemos el folio
        $folio = $_POST['folio'];
        $fechaperiodoadelantado=$_POST['fechaperiodo'];

        if(!empty($folio))
        {

            $WhereString = " stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                #AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total > 0 
                AND titular.folio=$folio
                ";

                // AND date_format(date(titular.fecha_ingreso),'%m') !=  date_format(now(),'%m')

           
            FB::INFO($WhereString,'____________________WHERE');
            $GetDebtorData = Yii::app()->db->createCommand()
            ->select("titular.folio,
                titular.debtorno,
                CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
                titular.taxref,
                (titular.address10) as PostalCode,
                (cobranza.stockid) as CPlan,
                (cobranza.paymentid) as CPaymentName,
                (cobranza.frecuencia_pago) as CFrecPago,
                fasignados.tipo_membresia,
                titular.fecha_ingreso,
                titular.costo_total,
                cobradores.nombre")
            ->from("rh_titular titular")
            ->join("rh_cobranza cobranza", "titular.folio = cobranza.folio")
            ->join("rh_foliosasignados fasignados", "titular.folio = fasignados.folio")
            ->join("rh_cobradores cobradores", "cobranza.cobrador = cobradores.id")
            ->join("stockmaster stkm", "cobranza.stockid = stkm.stockid")
            ->where($WhereString)
            //->limit(10)
            ->queryAll();

                        $AfilNo= $GetDebtorData[0];
           
            # code...
            $Frecuencia = $AfilNo['CFrecPago'];
            $MetodoPago = $AfilNo['CPaymentName'];
            $Fecha_Ingreso = $AfilNo['fecha_ingreso'];

            if($MetodoPago == 9 || $MetodoPago == 10){
                $DiaEmision = 25;
                $TipoEmision = "Tarjetas";
            }else{
                $TipoEmision = "Efectivo";
                $DiaEmision = '01';
            }

            $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
            $CreateOrders = true;
            // Verificamos si le toca o no pedido pedido
            /*MODIFICADO POR DANIEL VILLARREAL EL 21 DE FEBRERO DEL 2016*/
            $VerifyOrder = $this->VerificarOrden($Frecuencia,$DiaEmision,$Date,0,$AfilNo['folio'],$AfilNo['costo_total'],$fechaperiodoadelantado);
            if($VerifyOrder['respuesta']){
              echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Si le toca pedido"
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Ya cuenta con factura.",
                    'fechaperiodoadelantado'=>$fechaperiodoadelantado,
                    /*'VerifyOrder'=>print_r($VerifyOrder)*/
                ));
            }


        }else{
            echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Ocurrio un error inesperado.",
                   
                ));
        }

    }//end public function actionPagosadelantadosverificar(){


    public function actionRegularpagosadelantados()
    {
        $TransNo = (isset($_GET['transno']))?$_GET['transno']:0;
        // En base al trasno obtenemos el debtorno del titular
        $_data = Yii::app()->db->createCommand()
            ->select("id,debtorno,order_ as orderno")
            ->from("debtortrans")
            ->where('transno='.$TransNo)
            ->queryAll();
        if(empty($_data))
        {
            exit;
        }
        $_data = $_data[0];
        $debtorno = $_data['debtorno'];
        $orderno = $_data['orderno'];
        $DebtorTransID = $_data['id'];

        // Cantidad de pagos adelantados por mes
        $pagosadelantados= (isset($_GET['pagosadelantados']))?$_GET['pagosadelantados']:0;

        $GetDebtorData = Yii::app()->db->createCommand()
            ->select("titular.folio,
                titular.debtorno,
                CONCAT(titular.name, ' ', titular.apellidos) as AfilName,
                titular.taxref,
                (titular.address10) as PostalCode,
                (cobranza.stockid) as CPlan,
                (cobranza.paymentid) as CPaymentName,
                (cobranza.frecuencia_pago) as CFrecPago,
                fasignados.tipo_membresia,
                titular.fecha_ingreso,
                titular.costo_total,
                cobradores.nombre")
            ->from("rh_titular titular")
            ->join("rh_cobranza cobranza", "titular.folio = cobranza.folio")
            ->join("rh_foliosasignados fasignados", "titular.folio = fasignados.folio")
            ->join("rh_cobradores cobradores", "cobranza.cobrador = cobradores.id")
            ->join("stockmaster stkm", "cobranza.stockid = stkm.stockid")
            ->where('titular.debtorno ="'.$debtorno.'"')
            ->queryAll();

        $AfilNo= $GetDebtorData[0];
        $Frecuencia = $AfilNo['CFrecPago'];
        $MetodoPago = $AfilNo['CPaymentName'];
        $Fecha_Ingreso = $AfilNo['fecha_ingreso'];


        if($MetodoPago == 9 || $MetodoPago == 10){
            $DiaEmision = 25;
            $TipoEmision = "Tarjetas";
        }else{
            $TipoEmision = "Efectivo";
            $DiaEmision = '01';
        }

        // Fecha de hoy con el dia de emision correspondiente al socio
        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
        $Fecha_Corte = $Date;
        for($i=1;$i<=$pagosadelantados;$i++){
            
            
            $arrayfechacorte = explode('-', $Fecha_Corte);

            $VerifyOrder = $this->VerificarOrden($Frecuencia,$DiaEmision,$Date,0,$AfilNo['folio'],$AfilNo['costo_total'],$Fecha_Corte);
            /*print_r( $VerifyOrder);*/
            if($VerifyOrder['respuesta']){
                echo $arrayfechacorte[1].'-'.$Fecha_Corte.'<br>';        
                /*Logueo en la Bitacora*/
                $sqlinsertbitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                              values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                $parameters = array(
                    ':folio' => $AfilNo['folio'],
                    ':debtorno' => $debtorno,
                    ':userid' => $_SESSION['UserID'],
                    ':fecha_corte' => $Fecha_Corte,
                    ':status' => "Procesada",
                    ':tipo' => 'Ajuste pagos adelantados',
                    ':systype' => 10,
                    ':debtortrans_id' => $DebtorTransID,
                    ':transno' => $TransNo,
                    ':created' => date("Y-m-d H:i:s"),
                    ':orderno' => $orderno,
                    ':frecuencia_pago' => $Frecuencia,
                    ':prox_factura' => ''
                );
                Yii::app()->db->createCommand($sqlinsertbitacora)->execute($parameters);
                
            }     
            $Fecha_Corte = strtotime ("+1 Months", strtotime ($Fecha_Corte));
                $Fecha_Corte =  date ( 'Y-m-d' , $Fecha_Corte);       
        }       
    } //   public function actionRegularpagosadelantados()
    
    /*FUNCION PARA GENERAR LA CONCILIACION DE LAS FACTURAS DEL MES SELECCIONADO*/
    public function actionConciliacionFactura(){

        if (!empty($_POST['Fecha_Inicial'])) {
            
            /*** OBTENER TODAS LAS FACTURAS DE LOS FOLIOS CON PAGOS EN EFECTIVO ***/

                $sql_conciliacion = "
                    SELECT titular.folio, titular.debtorno, titular.fecha_ingreso, 
                    CONCAT(titular.name, ' ', titular.apellidos) as NombreTitular,
                    titular.movimientos_afiliacion as estatus,
                    (stkm.description) as plan_producto,
                    (payfrec.frecuencia) as frecuencia_pago,
                    (payments.paymentname) as forma_pago,
                    titular.costo_total as tarifa,
                    cfd.folio as num_factura,
                    cfd.fecha as fecha_factura,
                    (debtor.ovamount + debtor.ovgst) as monto_factura,
                    debtor.alloc as total_pagado
            FROM rh_titular titular
            JOIN rh_cobranza cobranza ON titular.folio = cobranza.folio
            JOIN rh_foliosasignados fasignados ON titular.folio = fasignados.folio
            JOIN rh_cobradores cobradores ON cobranza.cobrador = cobradores.id
            JOIN stockmaster stkm ON cobranza.stockid = stkm.stockid
            JOIN rh_frecuenciapago payfrec ON cobranza.frecuencia_pago = payfrec.id
            JOIN paymentmethods payments ON cobranza.paymentid = payments.paymentid
            RIGHT JOIN debtortrans debtor ON titular.debtorno = debtor.debtorno
            RIGHT JOIN rh_cfd__cfd cfd ON cfd.id_debtortrans =  debtor.id
            WHERE stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total > 0
                AND (payments.paymentname != 'TARJETA DE CREDITO' and payments.paymentname != 'TARJETA DE DEBITO')
                AND cfd.serie != 'L'
                AND debtor.ovamount > 0
                AND debtor.trandate LIKE '".$_POST['Fecha_Inicial']."%'";
                    $datos_conciliacion = Yii::app()->db->createCommand($sql_conciliacion)->queryAll();

            /*** OBTENER TODAS LAS FACTURAS DE LOS FOLIOS CON PAGOS CON TARJETA (CREDITO O DEBITO) ***/
            $sql_conciliacion_tarjetas = "
                    SELECT titular.folio, titular.debtorno, titular.fecha_ingreso, 
                    CONCAT(titular.name, ' ', titular.apellidos) as NombreTitular,
                    titular.movimientos_afiliacion as estatus,
                    (stkm.description) as plan_producto,
                    (payfrec.frecuencia) as frecuencia_pago,
                    (payments.paymentname) as forma_pago,
                    titular.costo_total as tarifa,
                    cfd.folio as num_factura,
                    cfd.fecha as fecha_factura,
                    (debtor.ovamount + debtor.ovgst) as monto_factura,
                    debtor.alloc as total_pagado
            FROM rh_titular titular
            JOIN rh_cobranza cobranza ON titular.folio = cobranza.folio
            JOIN rh_foliosasignados fasignados ON titular.folio = fasignados.folio
            JOIN rh_cobradores cobradores ON cobranza.cobrador = cobradores.id
            JOIN stockmaster stkm ON cobranza.stockid = stkm.stockid
            JOIN rh_frecuenciapago payfrec ON cobranza.frecuencia_pago = payfrec.id
            JOIN paymentmethods payments ON cobranza.paymentid = payments.paymentid
            RIGHT JOIN debtortrans debtor ON titular.debtorno = debtor.debtorno
            RIGHT JOIN rh_cfd__cfd cfd ON cfd.id_debtortrans =  debtor.id
            WHERE stkm.is_cortesia = 0
                AND titular.movimientos_afiliacion = 'Activo'
                AND fasignados.tipo_membresia = 'Socio'
                AND date_format(date(titular.fecha_ingreso),'%Y%m') != date_format(now(),'%Y%m')
                AND titular.costo_total > 0
                AND (payments.paymentname = 'TARJETA DE CREDITO' || payments.paymentname = 'TARJETA DE DEBITO')
                AND cfd.serie != 'L'
                AND debtor.ovamount > 0
                AND debtor.trandate LIKE '".$_POST['Fecha_Final']."%'";
                    $datos_conciliacion_tarjetas = Yii::app()->db->createCommand($sql_conciliacion_tarjetas)->queryAll();

            /*** UNIR LAS DOS CONSULTAS PARA OBTENER TODOS LOS REGISTROS CON TODOS LOS METODOS DE PAGO ***/
            $union_datos = array_merge($datos_conciliacion, $datos_conciliacion_tarjetas);

            $socio_por_folio=array();
            foreach ($union_datos as $k1 => $v1) {
                    $socio_por_folio[]=$v1['folio'];
            }

            $folio = implode(",", $socio_por_folio);
            $_folio = str_replace(",", "','", $folio);
            //IN ('".$_folio."')

            /*** OBTENER TODAS LAS FACTURAS DE LOS FOLIOS CON PAGOS CON TARJETA (CREDITO O DEBITO) ***/
            $_sql_conciliacion_tarjetas = "
                    SELECT titular.folio, titular.debtorno, titular.fecha_ingreso, 
                    CONCAT(titular.name, ' ', titular.apellidos) as NombreTitular,
                    titular.movimientos_afiliacion as estatus, titular.costo_total,
                    (stkm.description) as plan_producto,
                    (payfrec.frecuencia) as frecuencia_pago,
                    (payments.paymentname) as forma_pago
            FROM rh_titular titular
            JOIN rh_cobranza cobranza ON titular.folio = cobranza.folio
            JOIN stockmaster stkm ON cobranza.stockid = stkm.stockid
            JOIN rh_frecuenciapago payfrec ON cobranza.frecuencia_pago = payfrec.id
            JOIN paymentmethods payments ON cobranza.paymentid = payments.paymentid
            where titular.folio NOT IN ('".$_folio."')
            AND (payments.paymentname != 'TARJETA DE CREDITO' and payments.paymentname != 'TARJETA DE DEBITO')
            AND stkm.is_cortesia = 0 
            AND titular.movimientos_afiliacion = 'Activo'";
            $_datos_conciliacion_tarjetas = Yii::app()->db->createCommand($_sql_conciliacion_tarjetas)->queryAll();
        }
        $this->render('Conciliacion', array(
                    'datosConciliacion' => $union_datos,    
                    'datosConciliacionSinFactura' => $_datos_conciliacion_tarjetas,    
                    //'datosConciliacionTarjetas' => $datos_conciliacion_tarjetas,

            )); 
    } // fin de function actionConciliacionFactura
}//End Class