<?php

/**
*
*/
class ReportesController extends Controller {

    public function actionRelacionvmaestros(){


        FB::INFO($_POST,'____________________POST');
        $MaestrosData = array();
        if(!empty($_POST)){

            if(!empty($_POST['FTITULAR'])){
                $_POST['FOLIOS'] = implode(",", $_POST['FTITULAR']);
            }
            FB::INFO($_POST,'_______________________POST2');

            if(isset($_POST['IMPRIMIR'])){
                global $db, $db_, $host, $dbuser, $dbpassword;;
                $db_ = $db;
                FB::INFO($db_,'________CONECTION');
                include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/formasmaestros.php');
                exit;
            }

            $WhereString = " 1 = 1 ";

            if (!empty($_POST['EMPRESA'])) {
                $WhereString .= " AND cobranza.empresa = :empresa";
                $WhereParams[':empresa'] = $_POST['EMPRESA'];
            }

            if (!empty($_POST['STATUS'])) {
                //$Status = implode(",", $_POST['STATUS']);
                $WhereString .= " AND titular.movimientos_afiliacion = :status ";
                $WhereParams[':status'] = $_POST['STATUS'];
            }

            if (!empty($_POST['JUBILADOS'])) {
                $WhereString .= " AND cobranza.sm_clavefiliacion LIKE 'E%'";
                //$WhereParams[':status'] = $_POST['JUBILADOS'];
                $Complemento = " , (sm_clavefiliacion) as num_empleado ";
            }

            if (!empty($_POST['QUINCENA']) && !empty($_POST['ANIO'])) {
                //$Status = implode(",", $_POST['STATUS']);
                $WhereString .= " AND cobranza.sm_vigencia = :vigencia ";
                $WhereParams[':vigencia'] = $_POST['QUINCENA'] . '-' . $_POST['ANIO'];
            }

            // FB::INFO($WhereString, '_____________WHERE');
            // FB::INFO($WhereParams, '_____________PARAMS');
            $MaestrosData = Yii::app()->db->createCommand()->select("
                cobranza.num_empleado,
                cobranza.sm_depto,
                cobranza.sm_vigencia,
                cobranza.sm_vigencia_final,
                (cobranza.sm_clavefiliacion) AS CLAVEFILIACION,
                titular.costo_total,
                CONCAT(titular.name,' ',titular.apellidos) as NombreTitular,
                (titular.folio) FolioTitular,
                cobranza.empresa,
                titular.address1,
                titular.address2,
                titular.address4,
                titular.address7,
                (SELECT COUNT(*) AS QTY1
                FROM custbranch
                WHERE custbranch.folio = titular.folio
                AND movimientos_socios = 'Activo'
                AND rh_status_captura = 'Activo') AS QTY_SOCIOS,
                titular.rh_tel" . $Complemento)
            ->from("rh_titular titular")
            ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
            ->where($WhereString, $WhereParams)
            ->queryAll();

        }

        $this->render("relacionvmaestros", array("MaestrosData" => $MaestrosData));
    }


    public function actionRelacionvmaestros21(){


        FB::INFO($_POST,'____________________POST');
        $MaestrosData = array();
        if(!empty($_POST)){

            if(!empty($_POST['FTITULAR'])){
                $_POST['FOLIOS'] = implode(",", $_POST['FTITULAR']);
            }
            FB::INFO($_POST,'_______________________POST2');

            if(isset($_POST['IMPRIMIR'])){
                global $db, $db_, $host, $dbuser, $dbpassword;;
                $db_ = $db;
                FB::INFO($db_,'________CONECTION');
                include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/formasmaestros.php');
                exit;
            }

            $WhereString = " 1 = 1 ";

            if (!empty($_POST['EMPRESA'])) {
                $WhereString .= " AND cobranza.empresa = :empresa";
                $WhereParams[':empresa'] = $_POST['EMPRESA'];
            }

            if (!empty($_POST['STATUS'])) {
                //$Status = implode(",", $_POST['STATUS']);
                $WhereString .= " AND titular.movimientos_afiliacion = :status ";
                $WhereParams[':status'] = $_POST['STATUS'];
            }

            if (!empty($_POST['QUINCENA']) && !empty($_POST['ANIO'])) {
                //$Status = implode(",", $_POST['STATUS']);
                $WhereString .= " AND cobranza.sm_vigencia = :vigencia ";
                $WhereParams[':vigencia'] = $_POST['QUINCENA'] . '-' . $_POST['ANIO'];

            }


            // FB::INFO($WhereString, '_____________WHERE');
            // FB::INFO($WhereParams, '_____________PARAMS');
            $MaestrosData = Yii::app()->db->createCommand()->select("
                cobranza.num_empleado,
                cobranza.sm_clavefiliacion,
                cobranza.sm_depto,
                cobranza.sm_cpresupuestal,
                cobranza.sm_vigencia,
                cobranza.sm_vigencia_final,
                titular.costo_total,
                CONCAT(titular.name,' ',titular.apellidos) as NombreTitular,
                (titular.folio) FolioTitular,
                cobranza.empresa,
                titular.address1,
                titular.address2,
                titular.address4,
                titular.address7,
                (SELECT COUNT(*) AS QTY1
                FROM custbranch
                WHERE custbranch.folio = titular.folio
                AND movimientos_socios = 'Activo'
                AND rh_status_captura = 'Activo') AS QTY_SOCIOS,
                titular.rh_tel")
            ->from("rh_titular titular")
            ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
            ->where($WhereString, $WhereParams)
            ->queryAll();

        }

        $this->render("relacionvmaestros21", array("MaestrosData" => $MaestrosData));
    }

    public function actionEstadoscuenta(){

        global $db, $db_, $host, $dbuser, $dbpassword, $AddCC;

        $db_ = $db;

        if(empty($_POST['FINICIO'])){
            $_POST['FINICIO'] = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
            $_POST['FFIN'] = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        }

        FB::INFO($_POST,'_______________________POST');

        if(isset($_POST['SendMail']) && isset($_POST['FOLIO'])){
            $attachment = array();
            $Folio = $_POST['FOLIO'];
            $GetEmail = Yii::app()->db->createCommand()->select(' email ')->from('rh_cobranza')->where('folio = "' . $Folio . '"')->queryAll();
            $EmailTo = explode(",", str_replace(array(';',' '), ',', $GetEmail[0]['email']));
            $Para = "";
            $Para= str_replace(",,",",",implode(",",array_map(function($arreglo){
                                                                return trim($arreglo);
                                                            }, $EmailTo)));
            $EmailTo[0] = $Para;
            // $EmailTo = array();
            // $EmailTo[0] = "erasto@realhost.com.mx";

            $BCC ="erasto@realhost.com.mx,rleal@realhost.com.mx";
            FB::INFO($EmailTo,'_______________________EMAIL');

            $from = 'AR MEDICA';
            $To = $EmailTo[0];
            $Subject = 'Estado de Cuenta Folio: ' . $Folio;
            $Mensaje = 'Estados de Cuenta';

            /*Get PDF*/
            $PDF = $this->actionGeneraestadocuenta('',$Ret = 'S', $Folio, $_POST);
            $attachment[] = array('nombre'=>"EstadoCuenta-{$Folio}.pdf",'archivo'=>$PDF);
            try {
                $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '', $AddCC);
                if ($Response == "success") {
                    Yii::app()->user->setFlash("success", "Los Estados de Cuenta se han enviado Correctamente . ");
                }
            } catch (Exception $e) {
                Yii::app()->user->setFlash("error", "Ocurrio un problema al Enviar el E-Mail: " . $e->getMessage());
            }
            $this->redirect($this->createUrl("reportes/estadoscuenta"));
            exit;
        }


        if(isset($_POST['IMPRIMIR'])){
            include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/estadoscuenta.php');
            exit;
        }

        $this->render("estadoscuenta");
    }

    /**
    * @todo
    * Genera PDF Estados de Cuenta para enviar por Email
    * */
    public function actionGeneraestadocuenta($name = '', $Ret = 'S', $Folio = null, $ArrayPOST){
        global $db, $db_, $host, $dbuser, $dbpassword;
        $_POST = $ArrayPOST;

        /*
        array(
            ['STATUS'] =>'Activo'
            ['FINICIO'] =>'2014-12-28'
            ['FFIN'] =>'2015-01-28'
            ['FOLIO'] =>20490
            ['IMPRIMIR'] =>'Imprimir Estados de Cuenta'
        )
        */
        $bufer = true;
        include($_SERVER['LocalERP_path'] . '/PHPJasperXML/estadoscuenta.php');
        return $bufer;
    }



    public function actionAsmxinsert(){
        FB::INFO($_POST,'_______________________POST');
        if(!empty($_POST['SendXML']) && !empty($_POST['XML'])){
            try {
                $SQLServerWS = new SQLServerWS();
                $InsertAction = "http://tempuri.org/insert";
                $ServiceURL = $SQLServerWS->ServiceURL;
                $XMLINSERT = $_POST['XML'];

                $INSERT = $SQLServerWS->InsertInToWS($XMLINSERT, $ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
                $SQLServerWS->UpdateTransLog($SQLServerWS->LogID, $INSERT);
                FB::INFO($INSERT,'____________________RESPONSE');
                Yii::app()->user->setFlash("success", "Enviado Correctamente.");
            } catch (Exception $e) {
                FB::INFO($e->getMessage(), "Error: ");
            }
        }

        $this->render("asmxinsert");
    }



    /*
    2015012007000116
    1)      Blanco:  espacio en blanco
    2)      BANCOREC:  el campo de Convenio en pestaña de cobranza dentro de Afiliaciones
    3)      TIPOCTA=Dato fijo siempre 3
    4)      CUENTA:  Campo No. De Cuenta en pestaña de cobranza dentro de Afiliaciones
    5)      IMPORTE=Campo Tarifa del Folio
    6)      NOMBRE=Nombre y/o Razón Social del Folio

    7)      EMP1=SERVICIOS MEDICOS DE EMERGENCIAS (FIJO)
    8)      EMP2=AR EMERGENCIAS(FIJO)
    9)      FOLIO=Campo de Número de Folio del Titular
    10)  EMP3=AR EMERGENCIAS (FIJO)
    11)  DESTATUS=Campo ESTATUS DEL FOLIO
    12)  DCOBRO=Al día de cobro o día de la semana de cobro del folio
    */
    public function actionTarjetas(){

       /* $GetData = Yii::app()->db->createCommand()->select("
            '' as BLANCO,
            (conv.bcotrans) AS BANCOREC,
            (3) AS TIPOCTA,
            (cobranza.num_plastico) AS CUENTA,
            (titular.costo_total) AS IMPORTE,
            CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
            'SERVICIOS MEDICOS DE EMERGENCIAS' AS EMP1,
            'AR EMERGENCIAS' AS EMP2,
            (titular.folio) AS FOLIO,
            'AR EMERGENCIAS' AS EMP3,
            (titular.movimientos_afiliacion) AS DESTATUS,
            (cobranza.dias_cobro) AS TIPO_DIA_COBRO,
            (cobranza.dias_cobro_dia) AS DCOBRO
            ")
        ->from("rh_titular titular")
        ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
        ->leftjoin("rh_foliosasignados fasig", "fasig.folio = titular.folio")
        ->leftjoin("rh_convenios conv", "conv.id = cobranza.convenio")
        ->where("fasig.tipo_membresia = 'Socio'
            AND titular.movimientos_afiliacion IN ('Activo', 'Suspendido')
            AND cobranza.paymentid IN (10) ")
        ->queryAll();


        $this->render("tarjetas", array("GetData" => $GetData));
    }*/


/* Se agregaro para que el reporte de tarjetas de debito contenga la fecha vencimiento, 
el número de Factura y la Fecha Factura asi como solo traiga lo pendiente de pago Angeles Perez 30/03/2016 */

         $GetData = Yii::app()->db->createCommand("select
            '' as BLANCO,
            (conv.bcotrans) AS BANCOREC,
            (3) AS TIPOCTA,
            (cobranza.num_plastico) AS CUENTA,
            (cobranza.vencimiento) AS VENCIMIENTO,
           CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FACTURA,
            cast(`debtor`.`trandate` as date) AS `FECHA_FACTURA`,
            (debtor.ovamount - debtor.alloc) AS IMPORTE,           
            CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
            'SERVICIOS MEDICOS DE EMERGENCIAS' AS EMP1,
            'AR EMERGENCIAS' AS EMP2,
            (titular.folio) AS FOLIO,
            'AR EMERGENCIAS' AS EMP3,
            (titular.movimientos_afiliacion) AS DESTATUS,
            (cobranza.dias_cobro) AS TIPO_DIA_COBRO,
            (cobranza.dias_cobro_dia) AS DCOBRO
           
        from rh_titular titular
        left join rh_cobranza cobranza on cobranza.folio = titular.folio
        left join rh_foliosasignados fasig on fasig.folio = titular.folio
        left join rh_convenios conv on conv.id = cobranza.convenio
        join debtortrans debtor on debtor.debtorno = titular.debtorno
        left join rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtor.id
    
        where fasig.tipo_membresia = 'Socio'
        and  titular.movimientos_afiliacion IN ('Activo', 'Suspendido')
        and  cobranza.paymentid IN (10)
        and   debtor.`type` = 10
        and (debtor.ovamount <> debtor.alloc)
       
order by cast(titular.folio as integer)")->queryAll();

        $this->render("tarjetas", array("GetData" => $GetData));
    }

//SE AGREGO LA FUNCION PARA LAS TARJETAS DE CREDITO QUE SE ENCONTRABA EN TEST. Angeles Pérez 2015-12-10
    
public function actionTarjetasCredito(){

       /* $GetData = Yii::app()->db->createCommand()->select("
            '' as BLANCO,
            (conv.bcotrans) AS BANCOREC,
            (3) AS TIPOCTA,
            (cobranza.num_plastico) AS CUENTA,
            (cobranza.vencimiento) AS VENCIMIENTO,
            (titular.costo_total) AS IMPORTE,
            CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
            'SERVICIOS MEDICOS DE EMERGENCIAS' AS EMP1,
            'AR EMERGENCIAS' AS EMP2,
            (titular.folio) AS FOLIO,
            'AR EMERGENCIAS' AS EMP3,
            (titular.movimientos_afiliacion) AS DESTATUS,
            (cobranza.dias_cobro) AS TIPO_DIA_COBRO,
            (cobranza.dias_cobro_dia) AS DCOBRO
            ")
        ->from("rh_titular titular")
        ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
        ->leftjoin("rh_foliosasignados fasig", "fasig.folio = titular.folio")
        ->leftjoin("rh_convenios conv", "conv.id = cobranza.convenio")
        ->where("fasig.tipo_membresia = 'Socio'
            AND titular.movimientos_afiliacion IN ('Activo', 'Suspendido')
            AND cobranza.paymentid IN (9) ")
        ->queryAll();


        $this->render("tarjetascredito", array("GetData" => $GetData));
    }*/

/* Se agregaro para que el reporte de tarjetas de credito contenga el número de Factura 
y la Fecha Factura asi como solo traiga lo pendiente de pago Angeles Perez 30/03/2016 */

    $GetData = Yii::app()->db->createCommand("select
            '' as BLANCO,
            (conv.bcotrans) AS BANCOREC,
            (3) AS TIPOCTA,
            (cobranza.num_plastico) AS CUENTA,
            (cobranza.vencimiento) AS VENCIMIENTO,
            CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FACTURA,
            cast(`debtor`.`trandate` as date) AS `FECHA_FACTURA`,
            (debtor.ovamount - debtor.alloc) AS IMPORTE,           
            CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
            'SERVICIOS MEDICOS DE EMERGENCIAS' AS EMP1,
            'AR EMERGENCIAS' AS EMP2,
            (titular.folio) AS FOLIO,
            'AR EMERGENCIAS' AS EMP3,
            (titular.movimientos_afiliacion) AS DESTATUS,
            (cobranza.dias_cobro) AS TIPO_DIA_COBRO,
            (cobranza.dias_cobro_dia) AS DCOBRO
           
        from rh_titular titular
        left join rh_cobranza cobranza on cobranza.folio = titular.folio
        left join rh_foliosasignados fasig on fasig.folio = titular.folio
        left join rh_convenios conv on conv.id = cobranza.convenio
        join debtortrans debtor on debtor.debtorno = titular.debtorno
        left join rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtor.id
       
        where fasig.tipo_membresia = 'Socio'
        and  titular.movimientos_afiliacion IN ('Activo', 'Suspendido')
        and  cobranza.paymentid IN (9)
        and   debtor.`type` = 10
        and (debtor.ovamount <> debtor.alloc)
       
order by cast(titular.folio as integer)")->queryAll();

        $this->render("tarjetascredito", array("GetData" => $GetData));
    }



    /*
    2015012007000125
    No. SOCIO     = Número del Socio de Afiliaciones
    Nombre y/o Razón Social   = Nombre de  Pestaña Afiliaciones
    Fecha de inscripción   = Fecha de Ingreso de Pestaña Afiliaciones
    Estatus del titular   = Estatus de Pestaña Afiliaciones (Activo, Suspendido, Cancelado)
    Plan o producto  (agregar)  = Plan de Pestaña Cobranza
    Forma de pago    = Forma pago de Pestaña Cobranza
    Frecuencia de pago   = Frecuencia de Pago de Pestaña Cobranza
    Facturas Vencidas   = Número de Facturas Vencidas
    Saldo Vencido    = Total de las Facturas Vencidas
    Candidato a:  es un dato calculado = Texto Dependiendo del Número de Facturas Vencidas:
    SUSPENSIÓN(si esta Activo y tiene 3 ó mas facturas vencidas),
    ACTIVACIÓN(si esta Suspendido y tiene 2 o Menos facturas vencidas)
    */
    public function actionCandidatossuspension(){
        FB::INFO($_POST,'_____________________-POST');
        $WhereString = " 1 = 1 ";
        if(isset($_POST['BUSCAR'])){

            if (!empty($_POST['FORMA_PAGO'])) {
                $WhereString .= " AND cobranza.paymentid = :paymentid";
                $WhereParams[':paymentid'] = $_POST['FORMA_PAGO'];
            }

            if (!empty($_POST['FRECUENCIA_PAGO'])) {
                $WhereString .= " AND cobranza.frecuencia_pago = :frecuencia_pago";
                $WhereParams[':frecuencia_pago'] = $_POST['FRECUENCIA_PAGO'];
            }

            if (!empty($_POST['PLAN'])) {
                $WhereString .= " AND cobranza.stockid = :stockid";
                $WhereParams[':stockid'] = $_POST['PLAN'];
            }

            if (!empty($_POST['STATUS'])) {
                $WhereString .= " AND titular.movimientos_afiliacion = :status ";
                $WhereParams[':status'] = $_POST['STATUS'];
            }

            if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
                $WhereString .= " AND (date(titular.fecha_ingreso) >= date(:inicio) AND date(titular.fecha_ingreso) <= date(:fin)) ";
                $WhereParams[':inicio'] = $_POST['INICIO'];
                $WhereParams[':fin'] = $_POST['FIN'];
            }
        }

        FB::INFO($WhereString,'____________WHERE');
        FB::INFO($WhereParams,'____________WHEREPARAMS');

        $GetData = Yii::app()->db->createCommand()->select("
            (titular.folio) AS NoSOCIO,
            CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
            (titular.fecha_ingreso) AS FECHA_INSCIPCION,
            (titular.movimientos_afiliacion) AS ESTATUS_TITULAR,
            (stkm.description) AS PLAN,
            (pm.paymentname) AS FORMA_PAGO,
            (fp.frecuencia) AS FRECUENCIA_PAGO,
            (SELECT sum(1)
                FROM debtortrans dt
                WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + dm.paymentterms))>1
                AND dt.debtorno =dm.debtorno
                AND dt.settled = 0 AND dt.type=10) AS FACTURAS_VENCIDAS,
            (SELECT sum(dt.ovamount + dt.ovgst + dt.ovfreight + dt.ovdiscount- dt.alloc)
                FROM debtortrans dt
                WHERE (TO_DAYS(from_unixtime(unix_timestamp())) - (TO_DAYS(trandate) + dm.paymentterms))>1
                AND dt.debtorno =dm.debtorno
                AND dt.settled = 0) AS SALDO_VENCIDO
            ")
        ->from("rh_titular titular")
        ->leftjoin("rh_foliosasignados fasig", "fasig.folio = titular.folio")
        ->leftjoin("rh_cobranza cobranza", "cobranza.folio = titular.folio")
        ->leftjoin("debtorsmaster dm", "dm.debtorno = titular.debtorno")
        ->leftjoin("stockmaster stkm", "stkm.stockid = cobranza.stockid")
        ->leftjoin("paymentmethods pm", "cobranza.paymentid = pm.paymentid")
        ->leftjoin("rh_frecuenciapago fp", "cobranza.frecuencia_pago = fp.id")
        ->where($WhereString ." AND fasig.tipo_membresia = 'Socio'
            AND titular.movimientos_afiliacion IN ('Activo', 'Suspendido')
            HAVING FACTURAS_VENCIDAS > 0 ", $WhereParams)
        //->limit("10")
        ->queryAll();

        //FB::INFO($GetData,'___________________DATA');

        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }


        $this->render("candidatossuspension", array(
            "Candidatos" => $GetData,
            "ListaFormasPago" => $ListaFormasPago,
            "ListaFrecuenciaPago" => $ListaFrecuenciaPago,
            "ListaPlanes" => $ListaPlanes
            ));
    }

    //Inicia funcion del reporte Encuesta Post-Venta Angeles Perez 31/08/2016

    public function actionEncuestapostventa(){
        FB::INFO($_POST,'_____________________-POST');
        $GetData = array();
        
        $WhereString = " 1 = 1 ";
        $WhereParams=array();

        if(isset($_POST['BUSCAR'])){

            

            if (!empty($_POST['comisionista'])) {
                $WhereString .= " AND rh_encuesta.comisionista_id = :comisionista_id";
                $WhereParams[':comisionista_id'] = $_POST['comisionista'];
            }


            if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
                $WhereString .= " AND (date(rh_encuesta.fecha) >= date(:inicio) AND date(rh_encuesta.fecha) <= date(:fin)) ";
                $WhereParams[':inicio'] = $_POST['INICIO'];
                $WhereParams[':fin'] = $_POST['FIN'];
            }

           
        }

        FB::INFO($WhereString,'____________WHERE');
        FB::INFO($WhereParams,'____________WHEREPARAMS');

        $GetData = Yii::app()->db->createCommand()->select("
                date_format(rh_encuesta.fecha,'%M') as fechames,
                date_format(rh_encuesta.fecha,'%Y') as fechaño,
                rh_comisionistas.comisionista,
                count(rh_comisionistas.id ) as encuestas,
                sum(case p1  when 'SI' then '1'  when 'NO' then '0'end +
                case p2  when 'SI' then '1'  when 'NO' then '0'end +
                case p3  when 'SI' then '1'  when 'NO' then '0'end +
                case p4  when 'SI' then '1'  when 'NO' then '0'end +
                case p5  when 'BUENA' then '1'  when 'MALA' then '0'end + 
                case p6  when 'SI' then '1'  when 'NO' then '0'end) as contador,
                (sum(case p1  when 'SI' then '1'  when 'NO' then '0'end +
                case p2  when 'SI' then '1'  when 'NO' then '0'end +
                case p3  when 'SI' then '1'  when 'NO' then '0'end +
                case p4  when 'SI' then '1'  when 'NO' then '0'end +
                case p5  when 'BUENA' then '1'  when 'MALA' then '0'end + 
                case p6  when 'SI' then '1'  when 'NO' then '0'end) / (6 * count(rh_comisionistas.id ))*100 ) as porcentaje
            ")
        ->from("rh_encuesta")
        ->leftjoin("rh_comisionistas", "rh_encuesta.comisionista_id = rh_comisionistas.id")
        ->leftjoin("rh_foliosasignados", "rh_encuesta.folio = rh_foliosasignados.folio")
        ->join("rh_titular", "rh_foliosasignados.folio = rh_titular.folio")
        ->where($WhereString, $WhereParams)
        ->group('date_format(rh_encuesta.fecha,"%M"),
                date_format(rh_encuesta.fecha,"%Y"), 
                rh_comisionistas.comisionista')
        ->queryAll(); 

        $TotalesData = Yii::app()->db->createCommand()->select("
                date_format(rh_encuesta.fecha,'%M') as fechames,
                date_format(rh_encuesta.fecha,'%Y') as fechaño,
                rh_comisionistas.comisionista,
                count(rh_comisionistas.id ) as encuestas,
                sum(case p1  when 'SI' then '1'  when 'NO' then '0'end +
                case p2  when 'SI' then '1'  when 'NO' then '0'end +
                case p3  when 'SI' then '1'  when 'NO' then '0'end +
                case p4  when 'SI' then '1'  when 'NO' then '0'end +
                case p5  when 'BUENA' then '1'  when 'MALA' then '0'end + 
                case p6  when 'SI' then '1'  when 'NO' then '0'end) as contador,
                (sum(case p1  when 'SI' then '1'  when 'NO' then '0'end +
                case p2  when 'SI' then '1'  when 'NO' then '0'end +
                case p3  when 'SI' then '1'  when 'NO' then '0'end +
                case p4  when 'SI' then '1'  when 'NO' then '0'end +
                case p5  when 'BUENA' then '1'  when 'MALA' then '0'end + 
                case p6  when 'SI' then '1'  when 'NO' then '0'end) / (6 * count(rh_comisionistas.id ))*100 ) as porcentaje
            ")
        ->from("rh_encuesta")
        ->leftjoin("rh_comisionistas", "rh_encuesta.comisionista_id = rh_comisionistas.id")
        ->leftjoin("rh_foliosasignados", "rh_encuesta.folio = rh_foliosasignados.folio")
        ->join("rh_titular", "rh_foliosasignados.folio = rh_titular.folio")
        ->where($WhereString, $WhereParams)
        ->queryAll(); 

        $ListaComisionistas = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');

        $this->render("encuestapostventa", array(
            "Encuestapostventa" => $GetData,
            "TotalesData" => $TotalesData,
            "ListaComisionistas" => $ListaComisionistas
            ));
    } 
    
    //Termina

    //inicia funcion para el detalle del reporte Encuesta Post-Venta Angeles Perez 31/08/2016

    public function actionEncuestapostventadetalle(){
        FB::INFO($_POST,'_____________________-POST');
        $GetData = array();
        
        $WhereString = " 1 = 1 ";
        $WhereParams=array();

        if(isset($_POST['BUSCAR'])){

            

            if (!empty($_POST['comisionista'])) {
                $WhereString .= " AND rh_encuesta.comisionista_id = :comisionista_id";
                $WhereParams[':comisionista_id'] = $_POST['comisionista'];
            }



            if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
                $WhereString .= " AND (date(rh_encuesta.fecha) >= date(:inicio) AND date(rh_encuesta.fecha) <= date(:fin)) ";
                $WhereParams[':inicio'] = $_POST['INICIO'];
                $WhereParams[':fin'] = $_POST['FIN'];
            }

           
        }

        FB::INFO($WhereString,'____________WHERE');
        FB::INFO($WhereParams,'____________WHEREPARAMS');

        $GetData = Yii::app()->db->createCommand()->select("
            rh_encuesta.*,
            rh_comisionistas.comisionista,
            rh_foliosasignados.folio,
            concat(rh_titular.name,' ',rh_titular.apellidos) as NombreTitular,
            rh_encuesta.encuestado,
                case p1  when 'SI' then '1'  when 'NO' then '0'end as p1,
                case p2  when 'SI' then '1'  when 'NO' then '0'end as p2,
                case p3  when 'SI' then '1'  when 'NO' then '0'end as p3,
                case p4  when 'SI' then '1'  when 'NO' then '0'end as p4,
                case p5  when 'BUENA' then '1'  when 'MALA' then '0'end as p5,
                case p6  when 'SI' then '1'  when 'NO' then '0'end as p6,
            rh_encuesta.fecha as fechaingreso
            ")
        ->from("rh_encuesta")
        ->leftjoin("rh_comisionistas", "rh_encuesta.comisionista_id = rh_comisionistas.id")
        ->leftjoin("rh_foliosasignados", "rh_encuesta.folio = rh_foliosasignados.folio")
        ->join("rh_titular", "rh_foliosasignados.folio = rh_titular.folio")
        ->where($WhereString, $WhereParams)
        ->queryAll();      

        $ListaComisionistas = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');

        $this->render("encuestapostventadetalle", array(
            "Encuestapostventadetalle" => $GetData,
            "ListaComisionistas" => $ListaComisionistas
            ));
    } 

public function actionSendmail($Folio = null, $Tipo = null, $_TransNo = null){
    global $db, $AddCC, $BCC;
    FB::INFO($Folio,'____________________-FOLIO');
    //FB::INFO($_POST,'::::::::::::____POST');
    if (!empty($_POST['SendMail']['Folio']) && $_POST['SendMail']['Tipo']) {
        $Folio = $_POST['SendMail']['Folio'];
        $Tipo = $_POST['SendMail']['Tipo'];
        parse_str($_POST['SendMail']['Folio'], $datos);
    }
    FB::INFO($Folio,'_________________SENDTO');
    foreach ($datos['EnviarCarta'] as $key => $folio) {
        $GetEmail = Yii::app()->db->createCommand()->select(' email ')->from('rh_cobranza')->where('folio = "'
        . $folio . '"')->queryAll();
        $EmailTo = explode(",", str_replace(array(';',' '), ',', $GetEmail[0]['email']));
        $Para = "";
        $Para= str_replace(",,",",",implode(",",array_map(function($arreglo){
            return trim($arreglo);
        }, $EmailTo)));

        $EmailTo[0] = $Para;
        if(!isset($BCC))
        $BCC ="mary.angeles.perez@hotmail.com"; // Aquí podría ir algún correo que quisieran ponerle copia oculta para que reciba todos los correos que se envían con la carta de Aviso de Aumento, ojo no olvidar que estos programas son generales para todas las plazas, a lo que quiero entender es que el correo que   pongan ahí recibirá correos de todas las plazas.

        FB::INFO($EmailTo,'_______________________EMAIL');

        Yii::app()->user->setFlash("success", "El Envio de Correos se ha realizado Correctamente...");
        $ActualizarStatusCarta = "update wrk_simulacion_aumentosprecio set enviar_carta = :enviar_carta where
        folio = :folio";
        $Parametros_status_carta = array(
        ':enviar_carta'=>'1',
        ':folio'=>$folio
        );

        Yii::app()->db->createCommand($ActualizarStatusCarta)->execute($Parametros_status_carta);switch ($Tipo) {
                case 'CartaAumentoPrecio':
                    $from = 'AR MEDICA';
                    $To = $EmailTo[0];
                    $Subject = 'AVISO IMPORTANTE PARA EL No. FOLIO : ' . $folio;
                    $Mensaje = 'CARTA AVISO DE AUMENTO DE PRECIO';
                    $PDF = $this->actionAumentopreciopdf('',$Ret = 'S');
                    $attachment =array( array('nombre'=>'carta_aumento_precio.pdf','archivo'=>$PDF));
                    $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '',
                    $AddCC);
                    if ($Response == "success") {
                        echo CJSON::encode(array(
                        'requestresult' => 'ok',
                        'message' => "Se ha enviado un Email ala direccion: ". $To
                        ));
                    }else{
                        echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => "Ocurrio un error inesperado"
                        ));
                    }
                break;
                default:
                break;
        }
    }
}

public function actionAumentopreciopdf($name= '', $Ret = ''){
    chdir(dirname(__FILE__));
    include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
    $pdf = new FPDF('P','mm','A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 7);
    $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] .
    "/carta_aumento_precio.jpg", 0, 0, 210, 'L');
    $pdfcode = $pdf->output($name, $Ret);
        return $pdfcode;
}


public function actionSimulacionprecios(){
    FB::INFO($_POST,'_____________________-POST');

    $GetData = array();
    $WhereString = " 1 = 1 ";
    $WhereParams=array();

    if(isset($_POST['BUSCAR'])){
        if (!empty($_POST['FORMA_PAGO'])) {
            $WhereString .= " AND cobranza.paymentid = :paymentid";
            $WhereParams[':paymentid'] = $_POST['FORMA_PAGO'];
        }

        if (!empty($_POST['Folio'])) {
            $WhereString .= " AND titular.folio = :folio";
            $WhereParams[':folio'] = $_POST['Folio'];
        }

        if (!empty($_POST['FRECUENCIA_PAGO'])) {
            $WhereString .= " AND cobranza.frecuencia_pago = :frecuencia_pago";
            $WhereParams[':frecuencia_pago'] = $_POST['FRECUENCIA_PAGO'];
        }

        if (!empty($_POST['PLAN'])) {
            $WhereString .= " AND cobranza.stockid = :stockid";
            $WhereParams[':stockid'] = $_POST['PLAN'];
        }

        if (!empty($_POST['STATUS'])) {
            $WhereString .= " AND titular.movimientos_afiliacion = :status ";
            $WhereParams[':status'] = $_POST['STATUS'];
        }

        if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
            $WhereString .= " AND (date(titular.fecha_ingreso) >= date(:inicio) AND
            date(titular.fecha_ingreso) <= date(:fin)) ";
            $WhereParams[':inicio'] = $_POST['INICIO'];
            $WhereParams[':fin'] = $_POST['FIN'];
        }
    }


    FB::INFO($WhereString,'____________WHERE');
    FB::INFO($WhereParams,'____________WHEREPARAMS');

    $GetData = Yii::app()->db->createCommand()->select("
        (titular.folio) AS Folio,
        (titular.debtorno) AS DebtorNo,
        CONCAT(titular.name,' ',titular.apellidos) AS NOMBRE,
        (emp.empresa) AS Empresa,
        (titular.movimientos_afiliacion) AS ESTATUS_TITULAR,
        count(custbranch.branchcode) AS NumSocios,
        (titular.fecha_ingreso) AS FECHA_INSCIPCION,
        (stkm.description) AS PLAN,
        (pm.paymentname) AS FORMA_PAGO,
        (fp.frecuencia) AS FRECUENCIA_PAGO,
        (titular.servicios_mes) AS ServiciosMes,
        (titular.servicios_acumulados) AS ServiciosAcum,
        (titular.fecha_ultaum) AS FECHA_ULTIMO_AUMENTO,
        (titular.costo_total) AS CostoAfiliacion ")->from ('rh_titular titular')
        ->leftjoin('rh_cobranza cobranza','cobranza.folio = titular.folio')
        ->leftjoin('custbranch','custbranch.folio = titular.folio
        AND custbranch.movimientos_socios <> "Titular" AND movimientos_socios= "Activo" ')
        ->leftjoin('stockmaster stkm','stkm.stockid = cobranza.stockid')
        ->leftjoin('paymentmethods pm','cobranza.paymentid = pm.paymentid')
        ->leftjoin('rh_frecuenciapago fp','cobranza.frecuencia_pago = fp.id')
        ->leftjoin('rh_empresas emp','cobranza.empresa=emp.id')
        ->where($WhereString ." AND titular.movimientos_afiliacion='Activo'
        AND titular.costo_total <>0
        AND titular.folio<>' '
        AND titular.folio<>0
        AND stkm.is_cortesia = 0 
        AND titular.fecha_ultaum<=CURDATE()
        AND titular.fecha_ultaum <= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)", $WhereParams)
        ->group('titular.folio')
        ->queryAll();

    $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
    $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
    $_ListaPlanes = Yii::app()->db->createCommand()
        ->select(' stockid, description ')
        ->from('stockmaster')
        ->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();

    $ListaPlanes = array();

    foreach ($_ListaPlanes as $Planes) {
        $ListaPlanes[$Planes['stockid']] = $Planes['description'];
    }


    $this->render("simulacionprecios", array(
        "Simulacionprecios" => $GetData,
        "ListaFormasPago" => $ListaFormasPago,
        "ListaFrecuenciaPago" => $ListaFrecuenciaPago,
        "ListaPlanes" => $ListaPlanes
    ));
}


public function actionSimulacionpreciosaplicada(){
    FB::INFO($_POST,'_____________________-POST');

    $GetData = array();
    $WhereString = " 1 = 1 ";
    $WhereParams=array();

    if(isset($_POST['BUSCAR'])){
        if (!empty($_POST['FORMA_PAGO'])) { 
            $WhereString .= " AND wrk.paymentid = :paymentid";
            $WhereParams[':paymentid'] = $_POST['FORMA_PAGO'];
        }

        if (!empty($_POST['Folio'])) {
            $WhereString .= " AND wrk.folio = :folio";
            $WhereParams[':folio'] = $_POST['Folio'];
        }

        if (!empty($_POST['FRECUENCIA_PAGO'])) {
            $WhereString .= " AND wrk.frecuencia_pago = :frecuencia_pago";
            $WhereParams[':frecuencia_pago'] = $_POST['FRECUENCIA_PAGO'];
        }

        if (!empty($_POST['PLAN'])) {
            $WhereString .= " AND wrk.stockid = :stockid";
            $WhereParams[':stockid'] = $_POST['PLAN'];
        }

        if (!empty($_POST['STATUS'])) {
            $WhereString .= " AND wrk.movimientos_afiliacion = :status ";
            $WhereParams[':status'] = $_POST['STATUS'];
        }

        if (!empty($_POST['INICIO']) && !empty($_POST['FIN'])) {
            $WhereString .= " AND (date(wrk.fecha_aumento_tarifa) >= date(:inicio) AND
            date(wrk.fecha_aumento_tarifa) <= date(:fin)) ";
            $WhereParams[':inicio'] = $_POST['INICIO'];
            $WhereParams[':fin'] = $_POST['FIN'];
        }
    }


    FB::INFO($WhereString,'____________WHERE');
    FB::INFO($WhereParams,'____________WHEREPARAMS');

    $GetData = Yii::app()->db->createCommand()
        ->select("wrk.id,
        wrk.folio as Folio,
        CONCAT(wrk.nombre,'',wrk.apellidos) as Nombre,
        wrk.fecha_ingreso,
        wrk.movimientos_afiliacion,
        wrk.fecha_ultimo_aumento,
        wrk.costo_actual,
        stkm.description as stockid,
        pm.paymentname as paymentid,
        fp.frecuencia as frecuencia_pago,
        wrk.prc_aumento_tarifa,
        wrk.fecha_aumento_tarifa,
        wrk.nueva_tarifa,
        wrk.usuario,
        wrk.enviar_carta ")
        ->from ("wrk_simulacion_aumentosprecio wrk")
        ->leftjoin('stockmaster stkm','stkm.stockid = wrk.stockid')
        ->leftjoin('paymentmethods pm','wrk.paymentid = pm.paymentid')
        ->leftjoin('rh_frecuenciapago fp','wrk.frecuencia_pago = fp.id')
        ->where($WhereString ." AND status='1'", $WhereParams)
        ->queryAll();
    $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
    $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
    $_ListaPlanes = Yii::app()->db->createCommand()
        ->select(' stockid, description ')
        ->from('stockmaster')
        ->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();

    $ListaPlanes = array();

    foreach ($_ListaPlanes as $Planes) {
        $ListaPlanes[$Planes['stockid']] = $Planes['description'];
    }

    $this->render("simulacionpreciosaplicada", array(
        "Simulacionpreciosaplicada" => $GetData,
        "ListaFormasPago" => $ListaFormasPago,
        "ListaFrecuenciaPago" => $ListaFrecuenciaPago,
        "ListaPlanes" => $ListaPlanes
    ));
}


    //Termina

/*
     REPORTE DE EMISION MULTIEMPRESAS
     DANIEL VILLARREAL 6 DE ENERO DEL 2016
    */
   // Modificado para filtrar por Factura AR Angeles Perez 25/05/2016 

public function actionEmisionmultiempresas(){

        $Factura = $_POST['NoFactura'];
        if($Factura!='')
        {
            $DatosFactura = Yii::app()->db->createCommand()->select("
                debtortrans.ovamount,
                debtortrans.transno,
                concat(rh_cfd__cfd.serie, rh_cfd__cfd.folio) as FacturaAR,
                debtortrans.trandate,
                paymentmethods.paymentname,
                rh_frecuenciapago.frecuencia,
                MAX(stockmoves.rh_orderline) as socios
                ")
            ->from("debtortrans")
            ->join("rh_titular", "debtortrans.debtorno = rh_titular.debtorno")
            ->join("stockmoves", "stockmoves.transno = debtortrans.transno")
            ->join("rh_cobranza", "debtortrans.debtorno = rh_cobranza.debtorno")
            ->join("paymentmethods", "rh_cobranza.paymentid = paymentmethods.paymentid")
            ->join("rh_frecuenciapago", "rh_cobranza.frecuencia_pago = rh_frecuenciapago.id")
            ->join("rh_cfd__cfd","rh_cfd__cfd.id_debtortrans = debtortrans.id")
            ->where("concat(rh_cfd__cfd.serie,rh_cfd__cfd.folio) = '$Factura'" )
            ->queryAll();

            $ListaConceptos = Yii::app()->db->createCommand()->select("
                rh_facturacion.folio,
                concat(rh_cfd__cfd.serie, rh_cfd__cfd.folio) as FacturaAR,
                CONCAT_WS(' ',rh_titular. NAME,rh_titular.apellidos) AS nombre,
                stockmoves.price")
            ->from (" rh_facturacion")
            ->join ("rh_titular","rh_facturacion.folio = rh_titular.folio")
            ->join ("stockmoves","rh_facturacion.comentarios = stockmoves.rh_orderline
                     AND rh_facturacion.transno = stockmoves.transno ")
            ->join("rh_cfd__cfd","rh_cfd__cfd.fk_transno = rh_facturacion.transno")
             ->where("concat(rh_cfd__cfd.serie,rh_cfd__cfd.folio) = '$Factura' " )
            ->queryAll();

        }

        $this->render("emisionmultiempresas", array(
            "DatosFactura" => $DatosFactura,
            "ListaConceptos" => $ListaConceptos
            ));
    }
    public function actionComisionasesor(){
        $_params=array_merge($_GET,$_POST["GetInvoice"]);
        $id=da_xcess("numeric",$_params["asesor"]);
        $factura=da_xcess("numeric",$_params["folio"]);
        $periodo_mes=da_xcess("numeric",$_params["periodo_mes"]);
        $period=da_xcess("numeric",$_params["period"]);
        $datefrom=da_xcess("string",$_params["datefrom"]);
        $dateuntil=da_xcess("string",$_params["dateuntil"]);

        $month = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio', 7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        $days = array(1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun');

        $current_month = new DateTime(date("Y-m-d"));

        if(!$periodo_mes)
        $periodo_mes = $current_month->format('m');

        $first_day_month = date("Y-m-01", strtotime(date("Y-$periodo_mes-d")));
        $last_day_month = date("Y-m-t", strtotime(date("Y-$periodo_mes-d")));

        $first_day_month_ = new DateTime($first_day_month);
        $last_day_month_ = new DateTime($last_day_month);


        $day_period_start = $first_day_month_->format('D');
        $day_period_end = $last_day_month_->format('D');

        if($day_period_start=="Fri"):
        
            $datefrom=$first_day_month;
            $datefrom_period=$first_day_month;
        
        else:
            $a=1;
            while($days) {
                $day_period_start = new DateTime($day_period_start);
                $day_period_start->modify('-1 day');
                $day_period_start= $day_period_start->format('D');

                if($day_period_start=="Fri")
                break;
                
                $a++;
            }
            $datefrom = $this->DiasFecha($first_day_month,$a,"restar");
            $datefrom_period=$this->DiasFecha($first_day_month,$a,"restar");;

        endif;

        if($day_period_end=="Thu"):
        
            $dateuntil=$last_day_month;
            $dateuntil_period=$last_day_month;
        
        else:
            $a=1;
            while($days) {
                $day_period_end = new DateTime($day_period_end);
                $day_period_end->modify('+1 day');
                $day_period_end= $day_period_end->format('D');

                if($day_period_end=="Thu")
                break;
                
                $a++;
            }
            $dateuntil = $this->DiasFecha($last_day_month,$a,"sumar");
            $dateuntil_period = $this->DiasFecha($last_day_month,$a,"sumar");

        endif;        
        //pr($datefrom_period);
        //pr($dateuntil_period);
        switch($period){
            case 1:
                $dateuntil = $this->DiasFecha($datefrom,6,"sumar");
                break;

            case 2:
                $datefrom = $this->DiasFecha($datefrom,7,"sumar");
                $dateuntil = $this->DiasFecha($datefrom,6,"sumar");
                break;

            case 3:
                $datefrom = $this->DiasFecha($datefrom,14,"sumar");
                $dateuntil = $this->DiasFecha($datefrom,6,"sumar");
                break;

            case 4:
                $datefrom = $this->DiasFecha($datefrom,21,"sumar");
                $dateuntil = $this->DiasFecha($datefrom,6,"sumar");
                break;
            
            case 5:
                $datefrom = $this->DiasFecha($datefrom,28,"sumar");
                break;            


        }
        $days_period = $this->date_expires_info($datefrom_period,$dateuntil_period);
        $days_period = $days_period["days_left"]/7;

        if(!$period and $period!=6)
        $period = ceil($current_month->format('d')/7);

        $QUERY_COND=array();
        $url_query="";
        $ListaAsesores = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');

        if(!$id)
        $id=array_keys($ListaAsesores);
        $regs_order_field=" dtts.trandate desc";

        sql_qb_NEW(array(

            array("rh_t.asesor","=",$id,null,array(","),"or",true),
            //array("rh_t.movimientos_afiliacion","=","Activo",null,array(","),"or",true),
            array("dtts.type","=",10,null,array(","),"or",true),
            array("cfd.serie","="," ",null,array(","),"or",true),
            array("cfd.folio","=",$factura,null,array(","),"or",true),
            ( $datefrom==null ? null : array("dtts.trandate",">=",$datefrom." 00:00:00" ) ),
            ( $dateuntil==null ? null : array("dtts.trandate","<=",$dateuntil." 23:59:59" ) ),

        ),"and",false,$QUERY_COND,$url_query,$FIELDS_AFFECTED);
        
        $regs=array();

          $query = "SELECT
                rh_f.folio,
                rh_t.name,
                rh_t.apellidos,
                (rh_t.movimientos_afiliacion) as AfilStatus,
                (rh_c.cobrador) as AfilCobrador,
                (rh_t.asesor) as AfilAsesor,
                (rh_c.stockid) as AfilProduct,
                (rh_c.frecuencia_pago) as AfilFrecuenciaPago,
                (rh_c.paymentid) as AfilMetodoPago,
                CONCAT(cfd.serie, '', cfd.folio) as FolioFactura,
                (cfd.fecha) as FechaGenera,
                (dtts.tipo_factura) as TipoFactura,
                (dtts.rh_status) as StatusFactura,
                (dtts.alloc) as LOPAGADO,
                (dtts.ovamount + dtts.ovgst + dtts.ovfreight + dtts.ovdiscount- dtts.alloc) as SALDO,
                (rh_fa.tipo_membresia) as TipoFolio,
                cfd.no_certificado,
                cfd.fk_transno,
                cfd.uuid,
                dtms.name,
                dtts.trandate,
                cfd.id_debtortrans,
                dtts.ovamount/dtts.rate as ovamount,
                dtts.ovgst/dtts.rate as ovgst,
                (dtts.ovamount+dtts.ovgst)/dtts.rate as total,                    
                rh_c.stockid,
                rh_t.asesor,
                rh_t.movimientos_afiliacion,
                stkm.description,
                rh_fa.tipo_membresia,
                dtts.id,
                dtts.type,
                dtts.debtorno,
                cfd.folio as foliofactura
                from (select folio,debtortrans_id,fecha_corte,debtorno from rh_facturacion) as rh_f
                RIGHT JOIN (select id_debtortrans, folio, fk_transno, no_certificado, fecha, serie, uuid from rh_cfd__cfd) as cfd on rh_f.debtortrans_id=cfd.id_debtortrans
                LEFT JOIN (select id,type,debtorno,trandate,tipo_factura,transno,ovamount,ovgst,ovfreight,ovdiscount,rate,alloc,rh_status from debtortrans) as dtts on rh_f.debtortrans_id = dtts.id
                LEFT join rh_cobranza as rh_c on rh_f.folio=rh_c.folio
                LEFT join stockmaster as stkm on rh_c.stockid=stkm.stockid
                LEFT JOIN rh_foliosasignados as rh_fa on rh_fa.folio = rh_f.folio
                LEFT JOIN debtorsmaster as dtms on rh_f.debtorno=dtms.debtorno
                LEFT JOIN rh_titular as rh_t on rh_t.folio=rh_f.folio"
                .( $QUERY_COND ? " where ".implode(" and ",$QUERY_COND) : "" ).
                " group by rh_f.folio order by $regs_order_field";

        $res=sql_dq($query);
        $suma_total=0;
        while($row=mysql_fetch_assoc($res)){

            if($row['type'] == 10){
                $row['doc'] = "FACTURA";
            }
            if($row['type'] == 11){

                 $row['doc'] = "NOTA C " . $row['NoNOTA'];
                $row['descripcion'] = $row['descripcion'];
            }else{
                $row['descripcion'] = '';

            }
            $row["name_asesor"]=array_search($row["asesor"],array_flip($ListaAsesores));
            
            //$row["comision"]=comisiones($row["total"]);
            $suma_total+=$row["ovamount"];

            $regs[]=$row;

        }

        $this->render("comisionasesor",array(
            "regs" => $regs,
            "suma_total" => $suma_total,
            "periodo_mes" => $periodo_mes,
            "month" => $month,
            "datefrom" => $datefrom,
            "dateuntil" => $dateuntil,
            "days_period" => $days_period,
            "period" => $period,
        ));

    }

    function DiasFecha($fecha,$dias,$operacion){
      Switch($operacion){
        case "sumar":
        $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
        return $varFecha;
        break;
        case "restar":
        $varFecha = date("Y-m-d", strtotime("$fecha - $dias day"));
        return $varFecha;
        break;
        default:
        $varFecha = date("Y-m-d", strtotime("$fecha + $dias day"));
        break;
      }
    }

    function date_expires_info($date,$date_expires) {

        $timestamp=$date!="0000-00-00" ? strtotime($date) : 0 ;
        $timestamp2=$date_expires!="0000-00-00" ? strtotime($date_expires) : 0 ;
        $days_tmp=$timestamp2 ? (($timestamp2-$timestamp)/86400) : null ;
        $days_tmp=$days_tmp!==null ? ( $days_tmp>-1 ? floor($days_tmp) : ceil($days_tmp) ) : null ;
        $days_left=$days_tmp>0 ? $days_tmp : 0 ;
        $days_expired=$days_tmp<0 ? ($days_tmp * (-1)) : 0 ; /* convert a negative into positive, for visual reasons */

        if($days_tmp===null) {

            $info=array(

                "class"=>"dateExpiresNotApply",
                "title"=>"",
                "text"=>"n/a",
                "days_left"=>$days_left,
                "days_expired"=>$days_expired,

            );

        }
        else if(!$days_expired) {

            $info=array(

                "class"=>( $days_left>0 ? "dateExpiresLeft" : "dateExpiresToday" ),
                "title"=>"$days_left días restantes antes de llegar a fecha de expiración { $date_expires }",
                "text"=>"-".$days_left,
                "days_left"=>$days_left+1,
                "days_expired"=>$days_expired,

            );

        }
        else {

            $info=array(

                "class"=>"dateExpiresExceed",
                "title"=>"+$days_expired días excedidos después de llegar a fecha de expiración { $date_expires }",
                "text"=>"+".$days_expired,
                "days_left"=>$days_left,
                "days_expired"=>$days_expired,

            );

        }

        return $info;

    }
    public function actionAsesor($all=""){
    
        $comisionista=da_xcess("fill",$_GET["comisionista"]);

        $query = "select id, comisionista from rh_comisionistas".($all ? " where " : " where comisionista like '%$comisionista%' and")." activo=1";
        $res=sql_dq($query);
        
        while($row=mysql_fetch_assoc($res))
        $ListaAsesores[]=$row;

        if($all)
        return $ListaAsesores;
        else
        echo json_encode(array("status"=>1,"msg"=>"$ERROR_HEADER, success","data"=>$ListaAsesores));

    }

}
