<?php

/**
*
*/
class RefacturacionChh
{

    public $DebtorsList = array();
    public $Emision = "";

    function __construct($DebtorsList)
    {
        $this->DebtorsList = $DebtorsList;
    }





    public function FacturaFolios(){

        FB::INFO($this->DebtorsList,'__________________________$this->DebtorsList');
        if(!empty($this->DebtorsList)){

            foreach ($this->DebtorsList as $key => $DebtorNo) {
                $this->CreaPedidoEmision($DebtorNo);
            }
            return true;
        }

    }



    private function CreaPedidoEmision($DebtorNo){

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
                /*Ajuste para que no salga con el mes proximo*/
                $Mas1Mes = 0;
            }else{
                $DiaEmision = '01';
                $TipoEmision = "Efectivo";
                $IDTipoEmision = 1;
                $Mas1Mes = 0;
            }

            $CreateOrders = false;
            switch ($Frecuencia) {
                case 1:
                    $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                    $CreateOrders = true;

                    $MesAnio = Controller::GetMonth($Date, 1, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];

                    break;
                case 2:
                    $GetFechas = Controller::GetPeriods($Fecha_Ingreso,2, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);
                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }

                    $MesAnio = Controller::GetMonth($Date, 2, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];
                    break;
                case 3:
                    $GetFechas = Controller::GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }

                    $MesAnio = Controller::GetMonth($Date, 3, $Mas1Mes);
                    $Description = "SERVICIO MEDICO-" . $MesAnio . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'];
                    break;
                case 4:
                    $GetFechas = Controller::GetPeriods($Fecha_Ingreso,3, $TipoEmision);
                    $ToDay = date('Y-m-d');
                    $GetTodayParts = explode("-", $ToDay);

                    if(($GetFechas[0] == $GetTodayParts[1]) || ($GetFechas[1] == $GetTodayParts[1]) ){
                        $Date = date('Y-m-d', mktime(0, 0, 0, date("m"), $DiaEmision, date("Y")));
                        $CreateOrders = true;
                    }else{
                        $Date = $GetFechas['ProximaFactura'];
                        //No Le Toca
                        $FechaFactura23 = $Date;
                    }

                    $MesAnio = Controller::GetMonth($Date, 4,$Mas1Mes);
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
                    'narrative' => 'EMISION-' . $Fecha_Pedido. " _REFACTURA-" . $this->Emision,
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

                    $LogBitacora = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno,frecuencia_pago,prox_factura)
                                  values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno,:frecuencia_pago,:prox_factura)";
                    $LogParameters = array(
                        ':folio' => $Folio,
                        ':debtorno' => $DebtorNo,
                        ':userid' => $_SESSION['UserID'],
                        ':fecha_corte' => $Date,
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

    }









}





