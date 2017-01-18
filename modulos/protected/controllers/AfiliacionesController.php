<?php
//var myVar;
/**
 * @Todo
 * Administracion de Afiliaciones
 * Administra Datos del Titular, Cobranza y Socios
 * Crear y Edita Afiliados, Registra los Pedidos en la bitacora para luego ser Facturados
 * @author erasto@realhost.com.mx
*/
class AfiliacionesController extends Controller {

    public function actionIndex() {



        //include($_SERVER['LocalERP_path'] . '/PHPWsdl/DisponibilidadAlmacenesSoap.php');
        $URL = "http://ar.realhostcloud.com/sainar/PHPWsdl/index.php?WSDL";
        $soapAction = "http://ar.realhostcloud.com/sainar/PHPWsdl/TransferenciasAlmacen";
        require_once ('PHPWsdl/class.phpwsdl.php');
        //$WS = new PhpWsdl();
        // $client = new SoapClient(null, array('location' => $URL,
        //                              'uri' => $soapAction));
        //$client->SalidasAlmacen();

        //PhpWsdl::RunQuickMode ( );

        // header('Content-Type: text/plain');

        // try {
        //     $options = array(
        //         'soap_version'=>SOAP_1_1,
        //         'exceptions'=>true,
        //         'trace'=>1,
        //         'cache_wsdl'=>WSDL_CACHE_NONE,
        //         'uri' => $soapAction
        //     );
        //     $client = new SoapClient($URL, $options);
        //     // Note where 'Get' and 'request' tags are in the XML
        //     } catch (Exception $e) {
        //         echo "<h2>Exception Error!</h2>";
        //         echo $e->getMessage();
        //     }

        //     try {
        //        $response=$client->TransferenciasAlmacen();
        //     }
        //     catch (Exception $e)
        //     {
        //         echo 'Caught exception: ',  $e->getMessage(), "\n";
        //     }
        // FB::INFO($response,'_____________WS');
        // FB::INFO($client,'_____________CLIENT');


        // $to = "erasto@realhost.com.mx";
        // $from = "erasto@realhost.com.mx";
        // $from_name = "Erasto";
        // $subject = "Calis Email";
        // $message = "TEST TEST";
        // $cc = "";
        //$PDF = $this->actionRecordatoriopagopdf('',$Ret = 'S');
        //$attachment =array( array('nombre'=>'Test.pdf','archivo'=>$PDF));
        //$this->EnviarMail($from = 'envio@realhost.com.mx', $To = 'erasto@realhost.com.mx', $Subject = 'TEST', $Mensaje = 'TESTING', $attachment , $BCC = '', $repplyTo = '', $AddCC = '');
        // $Folio = "17919";
        // $Tipo = $this->GetTipoFolio($Folio);
        // FB::INFO($Tipo,'____________GET TIPO');


        // FB::INFO($_POST, '________________________(-_-)');
        // $rawData = Estado::model()->findAll();
        // $ReturnData = $this->KillerDataProvider($rawData);
        // FB::INFO($ReturnData, '____________________________DATA');

        // $dbSchema = Estado::model()->getMetaData()->columns;
        // foreach ($dbSchema as $name => $Data) {
        //     $FieldName[] = $name;
        // }
        // FB::INFO($FieldName, '____________________________$Schema');


        $this->render('index');
    }

    public function actionTest() {



        $WS_CECOM = new WS_CECOM;
        FB::INFO($WS_CECOM,'OK');
        $RESPONSE = $WS_CECOM->GetCatalog('rh_servicios');

        header('Content-Type: application/json');
        //header ("Content-Type:text/xml");
        echo $RESPONSE;
        exit;


        // $date = date_create('2015-01-01');
        // echo date_format($date, 'y');
        // $Quincena = 2;
        // $Quincena2 = $Quincena/2;
        // $Quincena2 = explode(".", $Quincena);

        // if(isset($Quincena2[1])){
        //     $NumeroQuincena = 1;
        //     $NumeroMes = $Quincena2[0] + 1;
        // }else{
        //     $NumeroQuincena = 2;
        //     $NumeroMes = $Quincena2[0];
        // }




        $this->render('test');
    }
// Se agrego funcion para traer informacion del socio en la opcion para cambiar el tipo membresia  Angeles Perez, Daniel Villarreal 2016-07-28

 public function actionSearchfolio($callback = null)
    {
       
        if(!empty($_POST['Search']['string'])){
         // Obtenemos la palabra clave
         $keyword = $_POST['Search']['string'];

         // Buscamos coincidencias
         $ListaFolios = Yii::app()->db
             ->createCommand()
             ->select("rh_titular.folio,
                rh_titular.name,
                rh_titular.apellidos,
                rh_titular.movimientos_afiliacion,
                rh_foliosasignados.tipo_membresia 
                ")
             ->from("rh_titular")
             ->leftJoin("rh_foliosasignados","rh_foliosasignados.folio=rh_titular.folio")
             ->where("rh_titular.folio = :keyword ", 
              array(":keyword" =>"$keyword"))->queryAll();

            

            foreach ($ListaFolios as $Data) {
                $_Data[] = array(
            'value' => $Data['folio']. '-' . $Data['name']. ' ' . $Data['apellidos']. '-' . $Data['movimientos_afiliacion']. '-' . $Data['tipo_membresia'],
            'folio'=> $Data['folio']
                );
                 
            }

        }

        if(empty($_Data)){
            $_Data[] = array(
                'value' => 'No se encontraron resultados.',
                'folio'=> '',
            );
        }

        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'DataList' => $_Data
            ));
        return;

        exit;
    }

// Termina

    public function actionAsignarfolio() {
        if (!empty($_POST)) {
            if (!empty($_POST['folio_inicial']) && !empty($_POST['folio_final']) && !empty($_POST['tipo_membresia']) && !empty($_POST['Asesor_ID'])) {
                for ($folio = $_POST['folio_inicial']; $folio<=$_POST['folio_final']; $folio++) {
                    $Verify = Yii::app()->db->createCommand()->select(' folio ')->from('rh_foliosasignados')->where('rh_foliosasignados.folio = "' . $folio . '"')->queryAll();
                    if (empty($Verify)) {
                        $sql = "insert into rh_foliosasignados (comisionista_id,folio,tipo_membresia,status,created,updated) values (:comisionista_id, :folio, :tipo_membresia, :status, :created, :updated)";
                        $parameters = array(
                            ':comisionista_id' => $_POST['Asesor_ID'],
                            ':folio' => $folio,
                            ':tipo_membresia' => $_POST['tipo_membresia'],
                            ':status' => 'free',
                            ':created' => date('Y-m-d'),
                            ':updated' => date('Y-m-d')
                        );
                        if (Yii::app()->db->createCommand($sql)->execute($parameters)) {
                            Yii::app()->user->setFlash("success", "Los Folios se han Asignado correctamente.");
                        } else {
                            Yii::app()->user->setFlash("error", "No se pudieron Asignar los folios, intente de nuevo.");
                        }
                    } else {
                        Yii::app()->user->setFlash("error", "Este Folio ya fue asignado.");
                    }
                }
            } else {
                Yii::app()->user->setFlash("error", "Ingrese Folios.");
            }
        }

        $ListAsesores = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');
        $ListMotivosC = CHtml::listData(MotivosCancelacion::model()->findAll(), 'id', 'motivo');
        // Agregado por Daniel Villarreal el 27 de julio del 2016, para verificar que tenga acceso a Modificar el Tipo de Membresia
        $VerificarTipoMem = Yii::app()->db->createCommand()->select(' or_cambio_membresia ')->from('www_users')->where('userid = "' . $_SESSION['UserID'] . '"')->queryRow();
        // Termina

        // Agregado para verificar que tenga acceso a Modificar el Tipo de Membresía
        $VerificarTipoMem = Yii::app()->db->createCommand()->select('
        or_cambio_membresia ')->from('www_users')->where('userid = "' .
        $_SESSION['UserID'] . '"')->queryRow();
        // Termina
        
        $this->render('asignarfolio', array(
            'ListAsesores' => $ListAsesores,
            'ListMotivosC' => $ListMotivosC,
            'PermisoTipoMem'=>$VerificarTipoMem
        ));
    }

    public function actionReasignarfolio() {
        FB::INFO($_POST, '________________________POST');

        if (!empty($_POST['FReasignacion']['Rfolio_inicial']) && !empty($_POST['FReasignacion']['Rfolio_final']) && !empty($_POST['FReasignacion']['RAsesor_ID'])) {

            for ($folio = $_POST['FReasignacion']['Rfolio_inicial']; $folio<=$_POST['FReasignacion']['Rfolio_final']; $folio++) {
                $Verify = Yii::app()->db->createCommand()->select(' folio ')->from('rh_foliosasignados')->where('rh_foliosasignados.folio = ' . $folio . ' AND rh_foliosasignados.status = "free"')->queryAll();
                if (!empty($Verify)) {
                    $SQLUpdate = "UPDATE rh_foliosasignados SET comisionista_id = :comisionista_id, updated = :updated WHERE folio = :folio";
                    $parameters = array(
                        ':comisionista_id' => $_POST['FReasignacion']['RAsesor_ID'],
                        ':folio' => $folio,
                        ':updated' => date('Y-m-d')
                    );
                    if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                        Yii::app()->user->setFlash("success", "Los Folios se han Reasignado correctamente.");
                    } else {
                        Yii::app()->user->setFlash("error", "No se pudieron Reasignar los folios, intente de nuevo.");
                    }
                }
            }

        } else {
            Yii::app()->user->setFlash("error", "Para Reasignar Ingrese Folios y Asesor.");
        }
        $this->redirect(array('afiliaciones/asignarfolio'));
    }
    // Se agrego para el cambio de tipo de membresia Angeles Perez  2016-07-28
    public function actionMembresiafolio() {
        FB::INFO($_POST, '________________________POST');

        if (!empty($_POST['FMembresia']['Mfolio_inicial']) && !empty($_POST['FMembresia']['tipo_membresia'])) {

            for ($folio = $_POST['FMembresia']['Mfolio_inicial']; $folio<=$_POST['FMembresia']['Mfolio_inicial']; $folio++) {
                $Verify = Yii::app()->db->createCommand()->select(' folio ')->from('rh_foliosasignados')->where('rh_foliosasignados.folio = ' . $folio)->queryAll();
                if (!empty($Verify)) {
                    $SQLUpdate = "UPDATE rh_foliosasignados SET tipo_membresia = :tipo_membresia, updated = :updated WHERE folio = :folio";
                    $parameters = array(
                        ':tipo_membresia' => $_POST['FMembresia']['tipo_membresia'],
                        ':folio' => $folio,
                        ':updated' => date('Y-m-d')
                    );
                    if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                        Yii::app()->user->setFlash("success", "el Folio se ha Actualizado correctamente.");
                    } else {
                        Yii::app()->user->setFlash("error", "No se pudo actualizar el folio,este ya cuenta con este tipo de membresia, intente de nuevo.");
                    }
                }
            }

        } else {
            Yii::app()->user->setFlash("error", "Para Actualizar Ingrese Folios y Tipo de membresia.");
        }
        $this->redirect(array('afiliaciones/asignarfolio'));
    }
// Termina

    public function actionCancelarfolio() {
        if (!empty($_POST['CFolio']) && !empty($_POST['CMCancelacion'])) {
            $Verify = Yii::app()->db->createCommand()->select(' folio ')->from('rh_foliosasignados')->where('rh_foliosasignados.folio = ' . $_POST['CFolio'])->queryAll();
            if (!empty($Verify)) {
                $SQLUpdate = "UPDATE rh_foliosasignados SET status = :status, updated = :updated,motivo_cancelacion = :motivo_cancelacion  WHERE folio = :folio";
                $parameters = array(
                    ':status' => 'cancel',
                    ':updated' => date('Y-m-d'),
                    ':folio' => $_POST['CFolio'],
                    ':motivo_cancelacion' => $_POST['CMCancelacion']
                );
                if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                    Yii::app()->user->setFlash("success", "El Folio se ha Cancelado.");
                } else {
                    Yii::app()->user->setFlash("error", "No se pudo Cancelar el Folio, Verifique que el Folio sea correcto y esté asignado a este Asesor.");
                }
            }
        } else {
            Yii::app()->user->setFlash("error", "Ingrese Folio.");
        }
        $this->redirect(array('afiliaciones/asignarfolio'));
    }

    /**
    * @todo
    * Obtiene el Numero de Servicion en Total y en el Ultimo Mes.
    **/
    public function GetNServiciosMes($Folio) {

        /*
        $GetDay = explode('-', date('Y-m-d'));
        $LastDay = date("d", (mktime(0, 0, 0, $GetDay['1'] + 1, 1, $GetDay['0']) - 1));
        $FirstDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], 01, $GetDay['0']));
        $LastDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], $LastDay, $GetDay['0']));

        //$Folio = 8662;

        $XmlCountDateRange = ("<row>
                                <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}' AND FECHA BETWEEN '{$FirstDate}' AND '{$LastDate}' \">
                                    <folio weberp='folio' prime='1' >{$Folio}</folio>
                                </CCM_Despachos>
                            </row>");

        $XmlCountAll = ("<row>
                            <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}'\">
                                <folio weberp='folio' prime='1' >{$Folio}</folio>
                            </CCM_Despachos>
                        </row>");

        //$url = "http://zona07.com/myapp/WebService.asmx?wsdl";
        $url = "http://104.130.129.147/wsceom2/WebService.asmx?wsdl";
        $CountAction = "http://tempuri.org/selectcont";
        $SQLServerWS = new SQLServerWS();
        $CountByDateRange = $SQLServerWS->GetCountWS($XmlCountDateRange, $url, array('SOAPAction: ' . $CountAction))->saveXML();
        $CountAll = $SQLServerWS->GetCountWS($XmlCountAll, $url, array('SOAPAction: ' . $CountAction))->saveXML();

        // FB::INFO($CountByDateRange, '_____XML Response CountByDateRange');
        // FB::INFO($CountAll, '_____XML Response CountAll');

        $ObjByDateRange = new xmlToArrayParser($CountByDateRange);
        if ($ObjByDateRange->parse_error) {
            FB::INFO($ObjByDateRange->get_xml_error(), '_____FAIL');
        } else {
            $GetThisMonth = $ObjByDateRange->array;
            //FB::INFO($GetThisMonth, '_____DONE DATE RANGE...!!!');
            $GetThisMonth = $GetThisMonth['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
            //FB::INFO($GetThisMonth, '_____RESULT XML MONTH');
        }

        $ObjAll = new xmlToArrayParser($CountAll);
        if ($ObjAll->parse_error) {
            FB::INFO($ObjAll->get_xml_error(), '_____FAIL');
        } else {
            $GetAll = $ObjAll->array;
            //FB::INFO($GetAll, '_____DONE ALL...!!!');
            $GetAll = $GetAll['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
            //FB::INFO($GetAll, '_____RESULT XML ALL');
        }
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        COMENTADO POR DANIEL VILLARREAL EL 12 DE NOVIEMBRE DEL 2015
        */

       

        /*return array(
            'GetThisMonth' => $GetThisMonth,
            'GetAll' => $GetAll
        );*/
        

/******************* 02/12/2016 01:36:41 p.m. *****************/


        $GetTotals = Yii::app()->db->createCommand()->select('servicios_mes ')
                             ->from('rh_titular')
                             ->where('folio = "' . $Folio . '"')->queryAll();

       //    echo "<pre>";print_r($GetTotals);exit();
        return array(
            'GetThisMonth' => $GetTotals[0]['servicios_mes'],
           // 'GetAll' => $GetServsAcums['TOTAL'],
        );
    }

    /**
    **@Todo
    **Crea y Actualiza informacion del Afiliado (rh_titular)
    **@author erasto@realhost.com.mx
    */
    public function actionAfiliacion() {
        global $db;
        FB::INFO($_POST, '_________________POST');
        if (isset($_POST['Folio']) || isset($_GET['Folio'])) {
            $Folio = $_REQUEST['Folio'];
        } else {
            $Folio = "";
        }
        if (!empty($_POST['Asesor_ID'])) {
            $Asesor = $_POST['Asesor_ID'];
        } else {
            $Asesor = "";
        }

        if ((isset($_POST['Search']) || isset($_GET['Folio'])) && !empty($Folio)) {
            unset($_POST);
            $_POST['Folio'] = $Folio;
            $Verify = Yii::app()->db->createCommand()->select(' folio, comisionista_id,tipo_membresia ')->from('rh_foliosasignados')->where('folio = "' . $Folio . '"')->queryAll();
            if (!empty($Verify)) {
                $Asesor = $Verify[0]['comisionista_id'];
                $_POST['asesor'] = $Asesor;
                Yii::app()->user->setFlash("success", "Folio Correcto .");
                $RSearch = Yii::app()->db->createCommand()->select(' * ')->from('rh_titular')->where('folio = "' . $Folio . '"')->queryAll();
                FB::INFO($RSearch,'_______________SERV');

                if (!empty($RSearch)) {
                    Yii::app()->user->setFlash("success", "{$Verify[0]['tipo_membresia']}: Folio " . $Folio . " - DebtorNo " . $RSearch[0]['debtorno']);
                    $RSearch[0]['servicios_seleccionados'] = json_decode($RSearch[0]['servicios_seleccionados'], 1);
                    $RSearch[0]['Folio'] = $RSearch[0]['folio'];


                    /* Ajuste para Datos Cargados desde Excel*/
                    FB::INFO($RSearch[0]['servicios_seleccionados'],'___________________SSELECCIONADOS');
                    if(!empty($RSearch[0]['servicios_seleccionados'][0])){

                        $servicios_seleccionados = array(
                            '1' =>'Emergencia',
                            '2' => 'Urgencia',
                            '3' => 'CTraumatismo',
                            '4' => 'CPatologia',
                            '5' => 'TProgramado',
                            '6' => 'LMedica',
                            '7' => 'CExterna',
                            '8' => 'CBase',
                            '9' => 'Evento',
                            '10' => 'SEspeciales',
                            '11' => 'CEmpleados');

                        foreach ($RSearch[0]['servicios_seleccionados'] as $idx => $value) {
                            $RSearch[0]['servicios_seleccionados'][$servicios_seleccionados[$value]] = $value;
                            unset($RSearch[0]['servicios_seleccionados'][$idx]);
                        }
                    }

                    $_POST = $RSearch[0];
                    $_POST['Action'] = "UPDATE";
                    $_POST['DebtorNo'] = $RSearch[0]['debtorno'];

                    $GetBalance = $this->GetBalance($RSearch[0]['debtorno']);
                    $_POST['Balance'] = $GetBalance['balance'];
                }
                //* GET #Servicios Acumulados *//
                $NServicionAcumulados = $this->GetNServiciosMes($Folio);

                $_POST['servicios_acumulados'] = $NServicionAcumulados['GetAll'];
                $_POST['servicios_mes'] = $NServicionAcumulados['GetThisMonth'];

            } else {
                Yii::app()->user->setFlash("error", "El Folio ingresado no existe ó no esta asignado a un Asesor.");
                $this->redirect($this->createUrl("afiliaciones/afiliacion"));
            }
        }

        if (!empty($_POST['SaveData'])) {
            /**
             * @Todo Crea Debtor, Titular, BranchCode.
             * */

            /*Crear Debtorno*/
            $_POST['DebtorNo'] = GetNextTransNo(500, $db);
            $_POST['PaymentTerms'] = 30;
            $_POST['creditlimit'] = $_SESSION['DefaultCreditLimit'];
            $_POST['name'] = trim($_POST['name']);
            if(empty($_POST['name'])){
                $_POST['name'] = $_POST['name2'];
            }
            //1000;
            //Pago de Contado.
            $_POST['SalesType'] = "L1";
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
            VALUES ('" . $_POST['DebtorNo'] . "',
                '" . DB_escape_string($_POST['name'] . " " . $_POST['apellidos']) . "',
                '" . DB_escape_string($_POST['name2']) . "',
                '" . DB_escape_string($_POST['address1']) . "',
                '" . DB_escape_string($_POST['address2']) . "',
                '" . DB_escape_string($_POST['address3']) . "',
                '" . DB_escape_string($_POST['address4']) . "',
                '" . DB_escape_string($_POST['address5']) . "',
                '" . DB_escape_string($_POST['address6']) . "',
                '" . DB_escape_string($_POST['address7']) . "',
                '" . DB_escape_string($_POST['address8']) . "',
                'MEXICO',
                '" . DB_escape_string($_POST['address10']) . "',
                '" . $_POST['rh_tel'] . "',
                'MXN',
                '" . $_POST['fecha_ingreso'] . "',
                '" . $_POST['HoldReason'] . "',
                '" . $_POST['PaymentTerms'] . "',
                '" . ($_POST['Discount']) / 100 . "',
                '" . $_POST['DiscountCode'] . "',
                '" . ($_POST['PymtDiscount']) / 100 . "',
                '" . $_POST['CreditLimit'] . "',
                '" . $_POST['SalesType'] . "',
                '" . $_POST['AddrInvBranch'] . "',
                '" . DB_escape_string($_POST['taxref']) . "',
                '" . $_POST['CustomerPOLine'] . "'
                )";
            DB_query($CreateDebtorNo, $db);
            /********************************************************/

            /*Crea Titular*/
            if (!empty($_POST['servicios_seleccionados'])) {
                $_POST['servicios_seleccionados2'] = json_encode($_POST['servicios_seleccionados'], 1);
            }
            $CreateTitular = "INSERT INTO rh_titular (folio,
                debtorno,
                fecha_ingreso,
                fecha_inicial,
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
                OC_Texto,-- Se agrego para la orden de compra alfanumerica, Angeles Perez 2016/01/27
                rh_numproveedor,
                servicios_seleccionados,
                examenes_laboratorio,
                costos_nuevos_socios)
            VALUES (
                '" . $_POST['Folio'] . "',
                '" . $_POST['DebtorNo'] . "',
                '" . $_POST['fecha_ingreso'] . "',
                '" . $_POST['fecha_ingreso'] . "',
                '" . $_POST['fecha_ultaum'] . "',
                '" . $_POST['tipopersona'] . "',
                '" . $_POST['asesor'] . "',
                '" . $_POST['apellidos'] . "',
                '" . $_POST['name'] . "',
                '" . $_POST['sexo'] . "',
                '" . $_POST['name2'] . "',
                '" . $_POST['nombre_empresa'] . "',
                '" . $_POST['taxref'] . "',
                '" . $_POST['curp'] . "',
                '" . $_POST['email'] . "',
                '" . $_POST['contacto'] . "',
                '" . $_POST['rh_tel'] . "',
                '" . $_POST['rh_tel2'] . "',
                '" . $_POST['enfermeria'] . "',
                '" . $_POST['costoenfermeria'] . "',
                '" . $_POST['serviciolimitado'] . "',
                '" . $_POST['serviciosdisponibles'] . "',
                '" . $_POST['costo_servicioextra'] . "',
                '" . $_POST['address1'] . "',
                '" . $_POST['address2'] . "',
                '" . $_POST['address3'] . "',
                '" . $_POST['address4'] . "',
                '" . $_POST['address5'] . "',
                '" . $_POST['address6'] . "',
                '" . $_POST['address7'] . "',
                '" . $_POST['address8'] . "',
                '" . $_POST['address10'] . "',
                '" . $_POST['cuadrante1'] . "',
                '" . $_POST['cuadrante2'] . "',
                '" . $_POST['cuadrante3'] . "',
                '" . $_POST['orderno'] . "',
                '" . $_POST['OC_Texto'] . "',
                '" . $_POST['rh_numproveedor'] . "',
                '" . $_POST['servicios_seleccionados2'] . "',
                '" . $_POST['examenes_laboratorio'] . "',
                '" . $_POST['costos_nuevos_socios'] . "')";
            FB::INFO($CreateTitular, '_______________________INSERT CreateTitular');

            if (DB_query($CreateTitular, $db)) {
                /*Actualizo el Status del Folio a Used */
                $UpdateFolioStatus = "UPDATE rh_foliosasignados SET status = 'used' WHERE folio = '" . $_POST['Folio'] . "'";
                DB_query($UpdateFolioStatus, $db);

                /*Inserto Branch para Facturar*/
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";

                $CreateBranch = "INSERT INTO custbranch ( branchcode,
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
                VALUES ('T-" . $_POST['DebtorNo'] . "',
                    '" . $_POST['Folio'] . "',
                    '" . DB_escape_string($_POST['name'] . " " . $_POST['apellidos']) . "',
                    '" . DB_escape_string($_POST['address1']) . "',
                    '" . DB_escape_string($_POST['address2']) . "',
                    '" . DB_escape_string($_POST['address3']) . "',
                    '" . DB_escape_string($_POST['address4']) . "',
                    '" . DB_escape_string($_POST['address5']) . "',
                    '" . DB_escape_string($_POST['address6']) . "',
                    '" . DB_escape_string($_POST['address7']) . "',
                    '" . DB_escape_string($_POST['address8']) . "',
                    '" . DB_escape_string($_POST['address10']) . "',
                    '" . DB_escape_string($_POST['cuadrante1']) . "',
                    '" . DB_escape_string($_POST['cuadrante2']) . "',
                    '" . DB_escape_string($_POST['cuadrante3']) . "',
                    '" . DB_escape_string($_POST['sexo']) . "',
                    '" . DB_escape_string($_POST['name2']) . "',
                    '" . $_POST['fecha_ingreso'] . "',
                    '" . $_POST['fecha_ultaum'] . "',
                    '" . $_POST['rh_tel'] . "',
                    'Titular',
                    '" . $_POST['DebtorNo'] . "',
                    '" . $Area . "',
                    '" . $Salesman . "',
                    '" . $DefaultLocation . "',
                    '" . $DefaultShipVia . "',
                    '" . $TaxGroupid . "',
                    'Titular'
                    )";
                FB::INFO($CreateBranch, '_______________________$CreateBranch');
                DB_query($CreateBranch, $db);
                /**********************************/
                Yii::app()->user->setFlash("success", "Los datos del Afiliado se han Guardado correctamente.");
                $_POST['Action'] = "UPDATE";

                $_POST['servicios_seleccionados3'] = implode(",", ($_POST['servicios_seleccionados']));

                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001')
                {
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->InsertTitular($_POST);
                }

                //exit;
                $this->redirect($this->createUrl('afiliaciones/cobranza&Folio=' . $_POST['Folio']));
            } else {
                Yii::app()->user->setFlash("error", "No se pudo guardar la Informacion del Afiliado, intente de nuevo.");
            }
        }

        /**
         * @Todo
         * Update Datos Titular, Debtor, CustBranch y CCM_Foltitular
         * */
        if (!empty($_POST['UpdateData'])) {
            if (!empty($_POST['servicios_seleccionados'])) {
                $_POST['servicios_seleccionados2'] = json_encode($_POST['servicios_seleccionados'], 1);
            }

            if (!empty($_POST['servicios_seleccionados'])) {
                $_POST['servicios_seleccionados3'] = implode(",", $_POST['servicios_seleccionados']);
            }

            FB::INFO($_POST, '________________________________POST UPDATE');

            $_POST['costo_total'] = str_replace(",", "", $_POST['costo_total']);
            $_POST['name'] = trim($_POST['name']);
            if(empty($_POST['name'])){
                $_POST['name'] = $_POST['name2'];
            }

            $_POST['email'] = str_replace("'", "", $_POST['email']);
            $SQLUpdate = "UPDATE rh_titular SET fecha_ingreso = '" . $_POST['fecha_ingreso'] . "',
                fecha_ultaum =  '" . $_POST['fecha_ultaum'] . "',
                tipopersona = '" . $_POST['tipopersona'] . "',
                asesor = '" . $_POST['asesor'] . "',
                apellidos = '" . DB_escape_string($_POST['apellidos']) . "',
                name = '" . DB_escape_string($_POST['name']) . "',
                sexo = '" . $_POST['sexo'] . "',
                name2 = '" . DB_escape_string($_POST['name2']) . "',
                nombre_empresa = '" . DB_escape_string($_POST['nombre_empresa']) . "',
                taxref = '" . DB_escape_string($_POST['taxref']) . "',
                curp = '" . DB_escape_string($_POST['curp']) . "',
                email = '" . DB_escape_string($_POST['email']) . "',
                contacto = '" . DB_escape_string($_POST['contacto']) . "',
                rh_tel = '" . DB_escape_string($_POST['rh_tel']) . "',
                rh_tel2 = '" . DB_escape_string($_POST['rh_tel2']) . "',
                enfermeria = '" . $_POST['enfermeria'] . "',
                costoenfermeria = '" . $_POST['costoenfermeria'] . "',
                serviciolimitado = '" . $_POST['serviciolimitado'] . "',
                serviciosdisponibles = '" . $_POST['serviciosdisponibles'] . "',
                costo_servicioextra = '" . $_POST['costo_servicioextra'] . "',
                address1 = '" . DB_escape_string($_POST['address1']) . "',
                address2 = '" . DB_escape_string($_POST['address2']) . "',
                address3 = '" . DB_escape_string($_POST['address3']) . "',
                address4 = '" . DB_escape_string($_POST['address4']) . "',
                address5 = '" . DB_escape_string($_POST['address5']) . "',
                address6 = '" . DB_escape_string($_POST['address6']) . "',
                address7 = '" . DB_escape_string($_POST['address7']) . "',
                address8 = '" . DB_escape_string($_POST['address8']) . "',
                address10 = '" . DB_escape_string($_POST['address10']) . "',
                cuadrante1 = '" . $_POST['cuadrante1'] . "',
                cuadrante2 = '" . $_POST['cuadrante2'] . "',
                cuadrante3 = '" . $_POST['cuadrante3'] . "',
                orderno = '" . $_POST['orderno'] . "',
                OC_Texto = '" . $_POST['OC_Texto'] . "',
                rh_numproveedor = '" . $_POST['rh_numproveedor'] . "',
                servicios_seleccionados = '" . $_POST['servicios_seleccionados2'] . "',
                examenes_laboratorio = '" . $_POST['examenes_laboratorio'] . "',
                servicios_acumulados = '" . $_POST['servicios_acumulados'] . "',
                servicios_mes = '" . $_POST['servicios_mes'] . "',
                costos_nuevos_socios = '" . $_POST['costos_nuevos_socios'] . "',
                costo_total = '" . $_POST['costo_total'] . "',
                facturas_vencidas = '" . $_POST['facturas_vencidas'] . "'
                WHERE folio = '" . $_POST['Folio'] . "'";
               

            $_POST['Action'] = "UPDATE";
            FB::INFO($SQLUpdate, '_______________________UPDATE TITULAR');
            if (DB_query($SQLUpdate, $db)) {
                FB::INFO($SQLUpdate, '_______________________ENTRO');
                /*Actualizo DebtorsMaster*/

                $DebtorNo = $this->GetDebtorNo($_POST['Folio']);
                $UpdateDebtorNo = "UPDATE debtorsmaster SET
                    name = '" . DB_escape_string($_POST['name'] . " " . $_POST['apellidos']) . "',
                    name2 = '" . DB_escape_string($_POST['name2']) . "',
                    address1 = '" . DB_escape_string($_POST['address1']) . "',
                    address2 = '" . DB_escape_string($_POST['address2']) . "',
                    address3 = '" . DB_escape_string($_POST['address3']) . "',
                    address4 = '" . DB_escape_string($_POST['address4']) . "',
                    address5 = '" . DB_escape_string($_POST['address5']) . "',
                    address6 = '" . DB_escape_string($_POST['address6']) . "',
                    address7 = '" . DB_escape_string($_POST['address7']) . "',
                    address8 = '" . DB_escape_string($_POST['address8']) . "',
                    address9 = 'MEXICO',
                    address10 = '" . DB_escape_string($_POST['address10']) . "',
                    rh_tel = '" . $_POST['rh_tel'] . "',
                    taxref = '" . $_POST['taxref'] . "',
                    clientsince = '" . $_POST['fecha_ingreso'] . "'
                WHERE debtorno = '" . $DebtorNo . "'

                                        ";
                FB::INFO($UpdateDebtorNo, '_______________________UPDATE DEBTOR');
                DB_query($UpdateDebtorNo, $db);
                /***************************************************************************************************************************/

                /*Actualizo Branchcode Principal*/
                //Se actualizo agregandole el email, para que al momento de actualizarlo en rh_titular tambien lo actualize en CustBranch Angeles Perez 07-04-2016 Se agrego .' '.$_POST['apellidos'] para que al momento de actualizar en titular actualize el mombre completo del titular en custbranch Angeles Perez 15/08/2016
                $UpdateBranch = "UPDATE custbranch set brname = '" . DB_escape_string($_POST['name'].' '.$_POST['apellidos']) . "', 
                    braddress1 = '" . DB_escape_string($_POST['address1']) . "',
                    braddress2 = '" . DB_escape_string($_POST['address2']) . "',
                    braddress3 = '" . DB_escape_string($_POST['address3']) . "',
                    braddress4 = '" . DB_escape_string($_POST['address4']) . "',
                    braddress5 = '" . DB_escape_string($_POST['address5']) . "',
                    braddress6 = '" . DB_escape_string($_POST['address6']) . "',
                    braddress7 = '" . DB_escape_string($_POST['address7']) . "',
                    braddress8 = '" . DB_escape_string($_POST['address8']) . "',
                    braddress10 = '" . DB_escape_string($_POST['address10']) . "',
                    cuadrante1 = '" . DB_escape_string($_POST['cuadrante1']) . "',
                    cuadrante2 = '" . DB_escape_string($_POST['cuadrante2']) . "',
                    cuadrante3 = '" . DB_escape_string($_POST['cuadrante3']) . "',
                    sexo = '" . DB_escape_string($_POST['sexo']) . "',
                    email = '" . DB_escape_string($_POST['email']) . "',
                    nombre_empresa = '" . DB_escape_string($_POST['name2']) . "',
                    fecha_ultaum = '" . DB_escape_string($_POST['fecha_ultaum']) . "',
                    phoneno = '" . DB_escape_string($_POST['rh_tel']) . "',
                    movimientos_socios = 'Titular',
                    rh_status_captura = 'Titular'
                WHERE branchcode = 'T-" . $DebtorNo . "'
                    AND folio = '" . $_POST['Folio'] . "'
                    AND debtorno = '" . $DebtorNo . "' ";
                FB::INFO($UpdateBranch, '_______________________$CreateBranch');
                DB_query($UpdateBranch, $db);
                /*************************************************************************************************/

                //Actualizo en la tabla de CCM_Foltitular del SQLServer
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->UpdateTitular($_POST);
                }
                Yii::app()->user->setFlash("success", "Los datos del Afiliado se Actualizaron correctamente.");
            } else {
                Yii::app()->user->setFlash("error", "La Informacion no se pudo actualizar, intente de nuevo.");
            }
        }
        $GetType = Yii::app()->db->createCommand()->select(' tipo_membresia ')->from('rh_foliosasignados')->where('folio = "' . $_POST['Folio'] . '"')->queryAll();
        $TipoMembresia = $GetType[0]['tipo_membresia'];
        $ListaComisionistas = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');
        $ListaMunicipios = CHtml::listData(Municipio::model()->findAll(array('order' => 'municipio')), 'id', 'municipio');
        $ListaEstados = CHtml::listData(Estado::model()->findAll(), 'id', 'estado');
        $ListaHospitales = CHtml::listData(Hospital::model()->findAll(), 'id', 'nombre');// Se agrego para mostrar la lista de hospitales Angeles Perez 2016-06-09
        $ListaMotivosCancelacion = array();
        $ListaMotivosCancelacion = CHtml::listData(MotivosCancelacion::model()->findAll(), 'id', 'motivo');

        /*===========OBTIENE SERVICIOS DE CECOM==============*/
        $WS_CECOM = new WS_CECOM;
        $ListaServicios = $WS_CECOM->GetCatalog('rh_servicios');
        /*==================================================*/

        /**
         * SI ESTE ENVIADO EL POST FOLIO, OBTENEMOS TODOS LOS MOVIMIENTOS DE RH_MOVIMIENTOS_AFILIACION Daniel Villarreal 06-05-2016
         */
        if(isset($_POST['Folio']))
        {   
        
            $MovAfiliacion = Yii::app()->db->createCommand()// Se agregaron rh_titular.fecha_ingreso,rh_motivos_cancelacion.motivo, Angeles Perez 13/05/2016
            ->select('rh_titular.fecha_inicial,rh_motivos_cancelacion.motivo,rh_movimientos_afiliacion.* ')
            ->from('rh_movimientos_afiliacion')
            ->join('rh_titular','rh_titular.folio = rh_movimientos_afiliacion.folio')// Se agrego Angeles Perez 12-05-2016
            ->leftJoin('rh_motivos_cancelacion','rh_motivos_cancelacion.id=rh_movimientos_afiliacion.motivos')// Se agrego Angeles Perez 13-05-2016
            ->where('rh_movimientos_afiliacion.folio = "' . $_POST['Folio'] . '" and movetype in ("Cancelado","Suspendido","Activo")')
            ->queryAll();
        }
        /** TERMINA **/

//Se agrego para traer las fechas de alerta que se cumplan en el dia actual de la bitacora seguimiento y asi mostrar en color rojo el botón Angeles Perez 2016-08-09
        $BitacoraSeguimiento = Yii::app()->db->createCommand()->select(' * ')->from('wrk_bitacora_seguimiento')
        ->where('fecha_alerta = "' .date('Y-m-d'). '"')
        ->queryAll();
//Termina

        /**
         *  AGREGADO PARA RESTRINGIR LA MODIFICACION AL CAMPO DE COSTO TOTAL
         *  POR DANIEL VILLARREAL EL 20 DE ABRIL DEL 2016
         **/
        $Obteneror_costo_afil = Yii::app()->db->createCommand()->select(' or_costo_afil ')->from('www_users')->where('userid = "' . $_SESSION['UserID'] . '"')->queryRow();
        /** TERMINA **/

        $this->render('afiliacion', array(
            'ListaMunicipios' => $ListaMunicipios,
            'ListaEstados' => $ListaEstados,
            'ListaHospitales' => $ListaHospitales,// Se agrego para mostrar la lista de hospitales Angeles Perez 2016-06-09
            'ListaComisionistas' => $ListaComisionistas,
            'ListaMotivosCancelacion' => $ListaMotivosCancelacion,
            'TipoMembresia' => $TipoMembresia,
            'ListaServicios' => $ListaServicios,
            'or_costo_afil'=>$Obteneror_costo_afil['or_costo_afil'],
            'MovAfiliacion'=>$MovAfiliacion, // Se agrego 13-05-2016
            'BitacoraSeguimiento'=>$BitacoraSeguimiento // Se agrego Angeles Perez 2016-08-09
        ));
    }

    /**
     **@Todo
     * Crea y Edita Contacto en Caso de Emergencia
     * @author erasto@realhost.com.mx
    */
    public function actionCreateemergenciadata(){
        if(!empty($_POST['Save'])){
            $SQLInsert = "INSERT INTO  rh_llamada_emergencia (folio,
                                                              nombre_familiar,
                                                              parentesco_id,
                                                              parentesco_otro,
                                                              telefono_familiar,
                                                              telefono_celular,
                                                              medico_cabecera,
                                                              medico_celular,
                                                              especialidad_id,
                                                              especialidad_otro,
                                                              created)
                                                        VALUES(:folio,
                                                              :nombre_familiar,
                                                              :parentesco_id,
                                                              :parentesco_otro,
                                                              :telefono_familiar,
                                                              :telefono_celular,
                                                              :medico_cabecera,
                                                              :medico_celular,
                                                              :especialidad_id,
                                                              :especialidad_otro,
                                                              :created)";
            $parameters = array(
                ':folio' => $_POST['Save']['folio'],
                ':nombre_familiar' => $_POST['Save']['nombre_familiar'],
                ':parentesco_id' => $_POST['Save']['parentesco_id'],
                ':parentesco_otro' => $_POST['Save']['parentesco_otro'],
                ':telefono_familiar' => $_POST['Save']['telefono_familiar'],
                ':telefono_celular' => $_POST['Save']['telefono_celular'],
                ':medico_cabecera' => $_POST['Save']['medico_cabecera'],
                ':medico_celular' => $_POST['Save']['medico_celular'],
                ':especialidad_id' => $_POST['Save']['especialidad_id'],
                ':especialidad_otro' => $_POST['Save']['especialidad_otro'],
                ':created' => date('Y-m-d H:i:s')
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'El Contacto de Emergencia se agrego Correctamente...',
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo agregar el Contacto, intente de nuevo.",
                ));
            }
        }

        if(!empty($_POST['Update']) && !empty($_POST['Update']['folio'])){
            $SQLUpdate = "UPDATE  rh_llamada_emergencia  SET nombre_familiar = :nombre_familiar,
                                                         parentesco_id = :parentesco_id,
                                                         parentesco_otro = :parentesco_otro,
                                                         telefono_familiar = :telefono_familiar,
                                                         telefono_celular = :telefono_celular,
                                                         medico_cabecera = :medico_cabecera,
                                                         medico_celular = :medico_celular,
                                                         especialidad_id = :especialidad_id,
                                                         especialidad_otro = :especialidad_otro
                                                         WHERE folio = :folio ";
            $parameters = array(
                ':nombre_familiar' => $_POST['Update']['nombre_familiar'],
                ':parentesco_id' => $_POST['Update']['parentesco_id'],
                ':parentesco_otro' => $_POST['Update']['parentesco_otro'],
                ':telefono_familiar' => $_POST['Update']['telefono_familiar'],
                ':telefono_celular' => $_POST['Update']['telefono_celular'],
                ':medico_cabecera' => $_POST['Update']['medico_cabecera'],
                ':medico_celular' => $_POST['Update']['medico_celular'],
                ':especialidad_id' => $_POST['Update']['especialidad_id'],
                ':especialidad_otro' => $_POST['Update']['especialidad_otro'],
                ':folio' => $_POST['Update']['folio']
            );
            if (Yii::app()->db->createCommand($SQLUpdate)->execute($parameters)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => 'El Contacto de Emergencia se Actualizo Correctamente...',
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo Actualizar el Contacto, intente de nuevo.",
                ));
            }
        }
        return;
    }

    /**
     *
     * @Todo
     * Obtiene el Detalle del Balance del Cliente Mediante el AgedDebtorsHTML
     * @Param
     * GetBalance:{
     DebtorNo: DebtorNo,
     Folio: Folio
     },
     * @return void
     * @author erasto@realhost.com.mx
     */
    public function actionGetdetailbalance() {
        global $db;
        if ($_POST['GetBalance']['DebtorNo']) {
            $rootpath = $_SERVER["UrlERP_BASE"];
            $Debtorno = $_POST['GetBalance']['DebtorNo'];
            $Folio = $_POST['GetBalance']['Folio'];
            unset($_POST['GetBalance']);
            $_POST['FromCriteria'] = $Debtorno;
            $_POST['ToCriteria'] = $Debtorno;
            $_POST['CollectionPath'] = '%';
            $_POST['All_Or_Overdues'] = 'All';
            $_POST['Salesman'] = '';
            $_POST['DetailedReport'] = 'Yes';
            $_POST['OrderBy'] = 'Code';
            $_POST['Currency'] = '';
            $_POST['VerReporte'] = 'Ver';
            $_REQUEST = $_POST;
            $External = true;
            ob_start();
            include ($_SERVER['LocalERP_path'] . "/AgedDebtorshtml.php");
            $GetBalance = ob_get_contents();
            ob_clean();

            $GetBalance = str_replace(array(
                "<TABLE BORDER=2 CELLPADDING=3>",
                "bgcolor="
            ), array(
                "<table class='table table-striped table-hover' id='BalanceDTable'>",
                "_bgcolor="
            ), $GetBalance);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Estado de Cuenta del Folio {$Folio}...",
                'Folio' => $Folio,
                'BalanceTableContent' => $GetBalance
            ));
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "No se encontro Folio, intente de nuevo",
            ));
        }
        return;
    }

    /**
     * @Todo
     * Busca Servicios por Folio y Rango de Fechas desde la Pestaña de Afiliacion
     * Consulta Tabla CCM_Despachos de MSSQL del WS
     * @author erasto@realhost.com.mx
    */
    public function actionGetserviciosacumulados(){
        FB::INFO($_POST,'_____________________POST'); //Comentado por Angeles Perez 2016-08-05
        /*if(!empty($_POST['GetServAcum']['StartDate'])){ 
            $StartDate = $_POST['GetServAcum']['StartDate'];
        }else{
            $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
        }

        if(!empty($_POST['GetServAcum']['EndDate'])){
            $EndDate = $_POST['GetServAcum']['EndDate'];
        }else{
            $EndDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
        }

        if(!empty($_POST['GetServAcum']['Folio'])){
            $Folio = $_POST['GetServAcum']['Folio'];
            //$Folio = 8662;
            if($_POST['GetServAcum']['Todos'] == 1){
                $XmlByDateRange = ("<row>
                                    <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}'\">
                                        <folio weberp='folio' prime='1' >{$Folio}</folio>
                                    </CCM_Despachos>
                                </row>");
            }else{
                $XmlByDateRange = ("<row>
                                        <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$Folio}' AND FECHA BETWEEN '{$StartDate} 00:00:00' AND '{$EndDate} 23:59:59' \">
                                            <folio weberp='folio' prime='1' >{$Folio}</folio>
                                        </CCM_Despachos>
                                    </row>");
            }

            //$url = "http://zona07.com/myapp/WebService.asmx?wsdl";
            $url = "http://104.130.129.147/wsceom2/WebService.asmx?wsdl";

            $SelectAction = "http://tempuri.org/select";
            $SQLServerWS = new SQLServerWS();
            $GetDataByDateRange = $SQLServerWS->GetFromWS($XmlByDateRange, $url, array('SOAPAction: ' . $SelectAction))->saveXML();

            FB::INFO($GetDataByDateRange, '_____XML Response GetDataByDateRange');

            $ObjByDateRange = new xmlToArrayParser($GetDataByDateRange);
            if ($ObjByDateRange->parse_error) {
                FB::INFO($ObjByDateRange->get_xml_error(), '_____FAIL');
            } else {
                $_2GetData = $ObjByDateRange->array;
                FB::INFO($_2GetData, '_____DONE DATE RANGE...!!!');
                $GetData = $_2GetData['soap:Envelope']['soap:Body']['selectResponse']['selectResult'];
                $GetData = base64_decode($GetData);
                FB::INFO($GetData, '_____RESULT XML Detalle de Servicios');
            }

            if (!empty($GetData)){
                $ObjData = new xmlToArrayParser($GetData);
                if ($ObjData->parse_error) {
                    FB::INFO($ObjData->get_xml_error(), '_____FAIL');
                } else {
                    $_2GetData = $ObjData->array;
                    FB::INFO($_2GetData, '_____RESULT XML Detalle de Servicios');
                    $TableContent ="";
                    $TableContent .="<table id='Table_Result' class='table table-striped'><thead><tr>";
                    $TableContent .="<th>NOSERV</th>";
                    $TableContent .="<th>FOLIOSERV</th>";
                    $TableContent .="<th>PACIENTE</th>";
                    $TableContent .="<th>SINTOMAS</th>";
                    $TableContent .="<th>SOLICITA</th>";
                    $TableContent .="<th>FECHA</th>";
                    $TableContent .="</tr></thead><tbody>";
                    foreach ($_2GetData['row']['CCM_Despachos'] as $Data) {
                        $TableContent .="<tr>";

                        $TableContent .="<td>{$Data['NOSERV']}</td>";
                        $TableContent .="<td>{$Data['FOLIOSERV']}</td>";
                        $TableContent .="<td>{$Data['PACIENTE']}</td>";
                        $TableContent .="<td>{$Data['SINTOMAS']}</td>";
                        $TableContent .="<td>{$Data['SOLICITA']}</td>";
                        $TableContent .="<td>{$Data['FECHA']}</td>";

                        $TableContent .="</tr>";
                    }
                    $TableContent .="</tbody></table>";
                }
            }*/ //Termina
            /* =============================================
            Por Daniel Villarreal 27/nov/2015, Se agregaron columnas Adicionales por Angeles Perez 12-05-2016
            Obtenemos el detalle desde la bd actual, no se usa el webservice.
            =============================================== */
            //$StartDate, $EndDate, $Folio, $_POST['GetServAcum']['Todos'] 
           /* if ($_SESSION['DatabaseName'] == "chh_erp_001") {
                $plaza_servicio = 'CHI';
            }elseif ($_SESSION['DatabaseName'] == "artorr_erp_001") {
                $plaza_servicio = 'TRN';
            }elseif ($_SESSION['DatabaseName'] == "artorr_erp_001") {
                $plaza_servicio = 'TRN'; */
                $plaza = $_SESSION['DatabaseName'];
                switch ($plaza) {
                    case "chh_erp_001":
                        $plaza_servicio = 'CHI';
                        break;
                    case "artorr_erp_001":
                        $plaza_servicio = 'TRN';
                        break;
                    case "tam_erp_001":
                        $plaza_servicio = 'TAM';
                        break;
                    case "mga_erp_001":
                        $plaza_servicio = 'QRO';
                        break;
                    case "sainar_erp_001":
                        $plaza_servicio = 'MTY';
                        break;
                    }

            if(!empty($_POST['GetServAcum']['StartDate'])){
                $StartDate = $_POST['GetServAcum']['StartDate'];
            }else{
                $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") - 1, date("d"), date("Y")));
            }

            if(!empty($_POST['GetServAcum']['EndDate'])){
                $EndDate = $_POST['GetServAcum']['EndDate'];
            }else{
                $EndDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d"), date("Y")));
            }

            if(!empty($_POST['GetServAcum']['Folio'])){
                $Folio = $_POST['GetServAcum']['Folio'];
            }

            if($_POST['GetServAcum']['Todos']== 1)
            {
                $where = ' cecom_001.VW_SERVICIOSDETALLE.Folio = '.$Folio.' and                 cecom_001.VW_SERVICIOSDETALLE.PlazaDeServicio = "'.$plaza_servicio.'" ';
            }else{
                $where = ' cecom_001.VW_SERVICIOSDETALLE.Folio = '.$Folio.' and  cecom_001.VW_SERVICIOSDETALLE.LlamadaInicio between "'.$StartDate.' 00:00:00" AND "'.$EndDate.' 23:59:59" and                 cecom_001.VW_SERVICIOSDETALLE.PlazaDeServicio = "'.$plaza_servicio.'"';
            }
            $GetTotals = Yii::app()->db->createCommand()->select('
                cecom_001.VW_SERVICIOSDETALLE.ServicioNum,
                cecom_001.VW_SERVICIOSDETALLE.Folio,
                cecom_001.VW_SERVICIOSDETALLE.PacienteID,
                cecom_001.VW_SERVICIOSDETALLE.SocioID,
                cecom_001.VW_SERVICIOSDETALLE.Socio,
                cecom_001.VW_SERVICIOSDETALLE.Cuadrante_1,
                cecom_001.VW_SERVICIOSDETALLE.Cuadrante_2,
                cecom_001.VW_SERVICIOSDETALLE.Cuadrante_3,
                cecom_001.VW_SERVICIOSDETALLE.ServicioID,
                cecom_001.VW_SERVICIOSDETALLE.Equipo,
                cecom_001.VW_SERVICIOSDETALLE.Llamada,
                cecom_001.VW_SERVICIOSDETALLE.Despacho,
                cecom_001.VW_SERVICIOSDETALLE.Arribo,
                cecom_001.VW_SERVICIOSDETALLE.TRASLADO_INICIO,
                cecom_001.VW_SERVICIOSDETALLE.TRASLADO_FIN,
                cecom_001.VW_SERVICIOSDETALLE.t_lugar_destino,
                cecom_001.VW_SERVICIOSDETALLE.TiempoDespacho,
                cecom_001.VW_SERVICIOSDETALLE.TiempoRespuesta,
                cecom_001.VW_SERVICIOSDETALLE.Sintomas,
                cecom_001.VW_SERVICIOSDETALLE.Asesor,
                cecom_001.VW_SERVICIOSDETALLE.LlamadaInicio,
                cecom_001.VW_SERVICIOSDETALLE.ServicioEstatus
                ')->from('cecom_001.VW_SERVICIOSDETALLE')->
                where($where)->queryAll();
                //$bandera;
                $GetTotalsAcums = Yii::app()->db->createCommand()
                ->select('cecom_001.VW_SERVICIOSDETALLE.ServicioNum')
                ->from('cecom_001.VW_SERVICIOSDETALLE')
                ->where('cecom_001.VW_SERVICIOSDETALLE.Folio = '.$Folio.' and                 cecom_001.VW_SERVICIOSDETALLE.PlazaDeServicio = "'.$plaza_servicio.'"')
                ->queryAll();

                $cantServ = count($GetTotalsAcums);
            $TableContent ="";
                    $TableContent .="<table id='Table_Result' class='table table-striped'><thead>
                    <tr><h3><b>Total de Servicios Acumulados: ".$cantServ; "<b><h3><hr></tr>";
                    "<tr>";
                    $TableContent .="<th>NOSERV</th>";
                    $TableContent .="<th>FECHA</th>";
                    $TableContent .="<th>FOLIOSERV</th>";
                    $TableContent .="<th>PACIENTEID</th>";
                    $TableContent .="<th>SOCIOID</th>";
                    $TableContent .="<th>PACIENTE</th>";
                    $TableContent .="<th>CUADRANTES</th>";
                    $TableContent .="<th>CODIGO</th>";
                    $TableContent .="<th>EQUIPO</th>";
                    $TableContent .="<th>LLAMADA</th>";
                    $TableContent .="<th>DESPACHO</th>";
                    $TableContent .="<th>ARRIBO</th>";
                    $TableContent .="<th>TRASLADO INICIO</th>";
                    $TableContent .="<th>TRASLADO FIN</th>";
                    $TableContent .="<th>HOSPITAL</th>";
                    $TableContent .="<th>TIEMPO DESPACHO</th>";
                    $TableContent .="<th>TIEMPO RESPUESTA</th>";
                    $TableContent .="<th>SINTOMAS</th>";
                    $TableContent .="<th>SOLICITA</th>";
                    $TableContent .="<th>ESTATUS LLAMADA</th>";
                    $TableContent .="</tr></thead><tbody>";
                    foreach ($GetTotals as $Data) {

                        if ($Data['ServicioEstatus'] == "CANCELADO") {
                            $color = 'red';
                        }else{
                            $color = '#000';
                        }

                        $TableContent .="<tr>";
                        $TableContent .="<td class='danger'><font color='$color'>{$Data['ServicioNum']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['LlamadaInicio']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Folio']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['PacienteID']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['SocioID']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Socio']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Cuadrante_1']} {$Data['Cuadrante_2']} {$Data['Cuadrante_3']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['ServicioID']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Equipo']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Llamada']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Despacho']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Arribo']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['TRASLADO_INICIO']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['TRASLADO_FIN']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['t_lugar_destino']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['TiempoDespacho']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['TiempoRespuesta']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Sintomas']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['Asesor']}</font></td>";
                        $TableContent .="<td><font color='$color'>{$Data['ServicioEstatus']}</font></td>";
                        //echo "<pre>";print_r($Data);exit();
                        

                        $TableContent .="</tr>";
                    }
                    $TableContent .="</tbody></table>";

            /* ================================================
            TERMINA
            ================================================== */

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => 'Servicios Acumulados para el Folio ' . $_POST['GetServAcum']['Folio'],
                'Folio' => $_POST['GetServAcum']['Folio'],
                'TableContent' => $TableContent
            ));
        }
  
    public function actionBuscarfolio(){

        if(!empty($_POST['Search'])){
            FB::INFO($_POST,'_________________POST');
            $Where = " 1 = 1 ";

            //$Where .= " AND folio = '20000' ";
            if(!empty($_POST['Search']['Folio'])){
                $Where .= " AND folio = '{$_POST['Search']['Folio']}' ";
            }

            if(!empty($_POST['Search']['Nombre'])){
                $Where .= " AND name LIKE '{$_POST['Search']['Nombre']}%' ";
            }

            if(!empty($_POST['Search']['Apellido'])){
                $Where .= " AND apellidos LIKE '{$_POST['Search']['Apellido']}%' ";
            }

            if(!empty($_POST['Search']['Empresa'])){
                $Where .= " AND empresa = '{$_POST['Search']['Empresa']}' ";
            }

            $TitularData = Yii::app()->db->createCommand()->select(' * ')->from(' rh_titular ')->where($Where)->limit(100)->queryAll();
                $TableContent = "";
                foreach ($TitularData as $Data) {
                    switch ($Data['movimientos_afiliacion']) {
                        case 'Activo':
                            $RowColor = " class='success2' ";
                            break;
                        case 'Suspendido':
                            $RowColor = " class='warning' ";
                            break;
                        case 'Cancelado':
                            $RowColor = " class='danger' ";
                            break;
                        default:
                            $RowColor = " class='' ";
                            break;
                    }

                    $TableContent .="<tr id='{$Data['folio']}' {$RowColor} >";

                    $TableContent .="<td style='font-weight: bold;'><a href='" . $this->createUrl("afiliaciones/afiliacion&Folio={$Data['folio']}") . "' >{$Data['folio']}</a></td>";
                    $TableContent .="<td>{$Data['name']} {$Data['apellidos']}</td>";
                    $TableContent .="<td>{$Data['fecha_ingreso']}</td>";
                    $TableContent .="<td>{$Data['address1']}</td>";
                    $TableContent .="<td>{$Data['address2']}</td>";
                    $TableContent .="<td>{$Data['address4']}</td>";
                    $TableContent .="<td>{$Data['address7']}</td>";
                    $TableContent .="<td>{$Data['rh_tel']}</td>";
                    $TableContent .="<td>{$Data['email']}</td>";
                    $TableContent .="<td>{$Data['movimientos_afiliacion']}</td>";

                    $TableContent .="</tr>";
                }


            FB::INFO($Where,'________________________WHERE');
            FB::INFO($TableContent,'_____________________TITULART');

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => 'Buscando Folios...',
                'TableContent' => $TableContent
            ));
            return;

        }
        $this->render('buscarfolio', array('TitularData' => $TitularData));
    }

    //Se agrego esta funcion para buscar por socios Angeles Perez 2016-03-14 --> 
    public function actionBuscarfoliosocio(){

       if(!empty($_POST['Search'])){
            FB::INFO($_POST,'_________________POST');
            $Where = " 1 = 1 ";

            //$Where .= " AND folio = '20000' ";
            if(!empty($_POST['Search']['Folio'])){
                $Where .= " AND folio = '{$_POST['Search']['Folio']}' ";
            }

            if(!empty($_POST['Search']['Nombre'])){
                $Where .= " AND brname LIKE '%{$_POST['Search']['Nombre']}%' ";
            }

// Se agrego filtro por empresa Angeles Perez 29-04-2016
            if(!empty($_POST['Search']['Empresa'])){
                $Where .= " AND nombre_empresa LIKE '%{$_POST['Search']['Empresa']}%' ";
            }
// Termina

            $SocioData = Yii::app()->db->createCommand()->select(' * ')->from(' custbranch ')->where($Where)->limit(100)->queryAll();
                $TableContent = "";
                foreach ($SocioData as $Data) {
                    switch ($Data['movimientos_socios']) {
                        case 'Activo':
                            $RowColor = " class='success2' ";
                            break;
                        case 'Suspendido':
                            $RowColor = " class='warning' ";
                            break;
                        case 'Cancelado':
                            $RowColor = " class='danger' ";
                            break;
                        default:
                            $RowColor = " class='' ";
                            break;
                    }

                    $TableContent .="<tr id='{$Data['folio']}' {$RowColor} >";

                    $TableContent .="<td style='font-weight: bold;'><a href='" . $this->createUrl("afiliaciones/afiliacion&Folio={$Data['folio']}") . "' >{$Data['folio']}</a></td>";
                    $TableContent .="<td>{$Data['brname']}</td>";
                    $TableContent .="<td>{$Data['nombre_empresa']}</td>";// Se agrego para filtrar por empresa Angeles Perez 29-04-2016
                    $TableContent .="<td>{$Data['fecha_ingreso']}</td>";
                    $TableContent .="<td>{$Data['braddress1']}</td>";
                    $TableContent .="<td>{$Data['braddress2']}</td>";
                    $TableContent .="<td>{$Data['braddress4']}</td>";
                    $TableContent .="<td>{$Data['braddress7']}</td>";
                    $TableContent .="<td>{$Data['phoneno']}</td>";
                    $TableContent .="<td>{$Data['email']}</td>";
                    $TableContent .="<td>{$Data['movimientos_socios']}</td>";

                    $TableContent .="</tr>";
                }


            FB::INFO($Where,'________________________WHERE');
            FB::INFO($TableContent,'_____________________SOCIOT');

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => 'Buscando Folios...',
                'TableContent' => $TableContent
            ));
            return;

        }
        $this->render('buscarfoliosocio', array('SocioData' => $SocioData));
    }

    //Termina


    /**
     * Obtiene Fecha de Corte
     * @return Date
     * @author  erasto@realhost.com.mx
     * @param 1 $FrecuenciaPago = id from table rh_frecuenciapago
     * @param 2 $FechaRegistro = date
     * @param 3 $Folio = folio del titular
     */
    public function actionGetfcorte($FrecuenciaPago = null, $FechaRegistro = null, $Folio = null) {
        if (isset($_POST['GetFechaCorte'])) {
            $FrecuenciaPago = $_POST['GetFechaCorte']['frecuencia_pago'];
            $FechaRegistro = $_POST['GetFechaCorte']['fecha_ingreso'];
            $Folio = $_POST['GetFechaCorte']['folio'];

            if (empty($FechaRegistro)) {
                $GetFechaRegistro = Yii::app()->db->createCommand()->select(' fecha_ingreso ')->from('rh_titular')->where('folio = "' . $Folio . '"')->queryAll();
                FB::INFO($GetFechaRegistro, '_________________________');
                $FechaRegistro = $GetFechaRegistro[0]['fecha_ingreso'];
            }
        }
        //$FrecuenciaPago = 4;
        //$FechaRegistro = date('Y-m-d');
        if (!empty($FrecuenciaPago) && !empty($FechaRegistro)) {
            $GetDay = explode('-', $FechaRegistro);
            switch ($FrecuenciaPago) {
                case '1' :
                    if (($GetDay['2'])<16) {
                        $FechaCorte = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'] + 1, 01, $GetDay['0']));
                    } else {
                        $FechaCorte = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'] + 1, $GetDay['2'] - 1, $GetDay['0']));
                    }
                    break;
                case '2' :
                    $FechaCorte = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'] + 6, $GetDay['2'] - 1, $GetDay['0']));
                    break;
                case '3' || '4' :
                    $FechaCorte = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], $GetDay['2'] - 1, $GetDay['0'] + 1));
                    break;
                default :
                    break;
            }
            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "La Fecha de Corte es {$FechaCorte}...",
                'fecha_corte' => $FechaCorte
            ));
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "No se pudo obtener la Fecha de Corte, intente de nuevo",
            ));
            //return $FechaCorte;
        }
        return;
    }

    public function actionCancelarafiliado() {
        global $db;
        FB::INFO($_POST, '_____________________________________POST');
// Se agrego  && !empty($_POST['Cancelacion']['fecha_cancelacion']) && ($_POST['Cancelacion']['fecha_cancelacion']<=date('Y-m-d') Angeles Perez/Daniel Villarreal 2016-06-29
        //if (!empty($_POST['Cancelacion']['folio'])) {
        if (!empty($_POST['Cancelacion']['folio']) && !empty($_POST['Cancelacion']['fecha_cancelacion']) && !empty($_POST['Cancelacion']['mo_cancelacion']) && ($_POST['Cancelacion']['fecha_cancelacion']<=date('Y-m-d'))) {
            $SQLUpdate = "UPDATE rh_titular SET
                                            movimientos_afiliacion = 'Cancelado',
                                            fecha_baja = '" . $_POST['Cancelacion']['fecha_baja'] . "',
                                            fecha_cancelacion = '" . $_POST['Cancelacion']['fecha_cancelacion'] . "',
                                            motivo_cancelacion = '" . $_POST['Cancelacion']['motivo_cancelacion'] . "'
                                            WHERE folio = '" . $_POST['Cancelacion']['folio'] . "'";
            FB::INFO($SQLUpdate, '_______________________UPDATE');
            $SQLServerWS = new SQLServerWS();
            $SQLServerWS->CancelarAfiliados($_POST);

            if (DB_query($SQLUpdate, $db)) {
                $Folio = $GetAfil[0]['folio'];
                $cont1 = "SELECT count(folio) FROM custbranch WHERE Folio = '" . $_POST['Cancelacion']['folio'] . "' AND  movimientos_socios != 'Cancelado'";
                $contRes=DB_query($cont1,$db);
                ///Cancela todos sus socios
                $SQLCancelarSocio = "UPDATE custbranch SET fecha_baja = :fecha_baja,
                                                        motivo_cancelacion = :motivo_cancelacion,
                                                        motivo_movimiento = :motivo_movimiento,
                                                        movimientos_socios = :movimientos_socios,
                                                        rh_status_captura = :rh_status_captura
                                                    WHERE folio = :folio AND movimientos_socios != 'Titular' 
                                                    AND movimientos_socios != 'Cancelado'";
                $parameters = array(
                    ':folio' => $_POST['Cancelacion']['folio'],
                    ':fecha_baja' => date('Y-m-d'),
                    ':motivo_cancelacion' => $_POST['Cancelacion']['motivo_cancelacion'],
                    ':motivo_movimiento' => $_POST['Cancelacion']['motivo_cancelacion'],
                    ':movimientos_socios' => 'Cancelado',
                    ':rh_status_captura' => 'Precapturado'
                );
                Yii::app()->db->createCommand($SQLCancelarSocio)->execute($parameters);

                $SQLServerWS = new SQLServerWS();
                $SQLServerWS->CancelarSocios($_POST);
                ////END Cancela Socios
                $CancelNo = GetNextTransNo(20020, $db);
                $GetAfil = Yii::app()->db->createCommand()->select(' rh_titular.*, cob.stockid, cob.frecuencia_pago, sm.description, pm.paymentname,fp.frecuencia, usr.realname ')->from('rh_titular')->leftJoin('rh_cobranza cob', 'cob.debtorno = rh_titular.debtorno')->leftJoin('stockmaster sm', 'sm.stockid = cob.stockid')->leftJoin('paymentmethods pm', 'pm.paymentid = cob.paymentid')->leftJoin('rh_frecuenciapago fp', 'fp.id = cob.frecuencia_pago')->leftJoin('rh_movimientos_afiliacion ma', 'ma.debtorno = rh_titular.debtorno')->leftJoin('www_users usr', 'usr.userid = ma.userid')->where('rh_titular.folio = "' . $_POST['Cancelacion']['folio'] . '"')->queryAll();

                $Debtorno = $GetAfil[0]['debtorno'];
                $Folio = $GetAfil[0]['folio'];
                $SaveMove = "INSERT INTO rh_movimientos_afiliacion (moveno,
                                            debtorno,
                                            folio,
                                            userid,
                                            movetype,
                                            fecha_baja,
                                            fecha_cancelacion,
                                            motivos)
                                            VALUES(:moveno,
                                            :debtorno,
                                            :folio,
                                            :userid,
                                            :movetype,
                                            :fecha_baja,
                                            :fecha_cancelacion,
                                            :motivos)";
                $parameters = array(
                    ':moveno' => $CancelNo,
                    ':debtorno' => $Debtorno,
                    ':folio' => $Folio,
                    ':userid' => $_SESSION['UserID'],
                    ':movetype' => 'Cancelado',
                    ':fecha_baja' => $GetAfil[0]['fecha_baja'],
                    ':fecha_cancelacion' => $GetAfil[0]['fecha_cancelacion'],
                    ':motivos' => $GetAfil[0]['motivo_cancelacion']
                );
                Yii::app()->db->createCommand($SaveMove)->execute($parameters);

                //$this->Pdfcancel($Folio, $CancelNo, $GetAfil[0]);

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Movimiento se Realizo Correctamente...",
                    'CancelNo' => $CancelNo
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Folio Incorrecto...",
                ));
            }
            //Proceso que actualiza en rh_titular y custbranch menos el estatus esto solo cuando son fechas posteriores al dia de hoy Angeles Perez/Daniel Villarreal 2016-06-29
        }  elseif(!empty($_POST['Cancelacion']['folio']) && !empty($_POST['Cancelacion']['fecha_cancelacion']) && !empty($_POST['Cancelacion']['mo_cancelacion']) && ($_POST['Cancelacion']['fecha_cancelacion']>date('Y-m-d')))
            {
                
                  $SQLUpdate = "UPDATE rh_titular SET
                                            fecha_baja = '" . $_POST['Cancelacion']['fecha_baja'] . "',
                                            fecha_cancelacion = '" . $_POST['Cancelacion']['fecha_cancelacion'] . "',
                                            motivo_cancelacion = '" . $_POST['Cancelacion']['motivo_cancelacion'] . "'
                                            WHERE folio = '" . $_POST['Cancelacion']['folio'] . "'";
                FB::INFO($SQLUpdate, '_______________________UPDATE');

                DB_query($SQLUpdate, $db); {

                ///Cancela todos sus socios
                $SQLCancelarSocio = "UPDATE custbranch SET fecha_baja = :fecha_baja,
                                                        motivo_cancelacion = :motivo_cancelacion,
                                                        motivo_movimiento = :motivo_movimiento
                                                    WHERE folio = :folio AND movimientos_socios != 'Titular' AND movimientos_socios != 'Cancelado' ";
                $parameters = array(
                    ':folio' => $_POST['Cancelacion']['folio'],
                    ':fecha_baja' => date('Y-m-d'),
                    ':motivo_cancelacion' => $_POST['Cancelacion']['motivo_cancelacion'],
                    ':motivo_movimiento' => $_POST['Cancelacion']['motivo_cancelacion']
                );
                Yii::app()->db->createCommand($SQLCancelarSocio)->execute($parameters);

                $CancelNo = GetNextTransNo(20020, $db);
                $GetAfil = Yii::app()->db->createCommand()->select(' rh_titular.*, cob.stockid, cob.frecuencia_pago, 
                sm.description, pm.paymentname,fp.frecuencia, usr.realname ')
                ->from('rh_titular')
                ->leftJoin('rh_cobranza cob', 'cob.debtorno = rh_titular.debtorno')
                ->leftJoin('stockmaster sm', 'sm.stockid = cob.stockid')
                ->leftJoin('paymentmethods pm', 'pm.paymentid = cob.paymentid')
                ->leftJoin('rh_frecuenciapago fp', 'fp.id = cob.frecuencia_pago')
                ->leftJoin('rh_movimientos_afiliacion ma', 'ma.debtorno = rh_titular.debtorno')
                ->leftJoin('www_users usr', 'usr.userid = ma.userid')
                ->where('rh_titular.folio = "' . $_POST['Cancelacion']['folio'] . '"')
                ->queryAll();

                $Debtorno = $GetAfil[0]['debtorno'];
                $Folio = $GetAfil[0]['folio'];
                $SaveMove = "INSERT INTO rh_movimientos_afiliacion (moveno,
                                            debtorno,
                                            folio,
                                            userid,
                                            movetype,
                                            fecha_baja,
                                            fecha_cancelacion,
                                            motivos)
                                            VALUES(:moveno,
                                            :debtorno,
                                            :folio,
                                            :userid,
                                            :movetype,
                                            :fecha_baja,
                                            :fecha_cancelacion,
                                            :motivos)";
                $parameters = array(
                    ':moveno' => $CancelNo,
                    ':debtorno' => $Debtorno,
                    ':folio' => $Folio,
                    ':userid' => $_SESSION['UserID'],
                    ':movetype' => 'Cancelado',
                    ':fecha_baja' => $GetAfil[0]['fecha_baja'],
                    ':fecha_cancelacion' => $GetAfil[0]['fecha_cancelacion'],
                    ':motivos' => $GetAfil[0]['motivo_cancelacion']
                );
                Yii::app()->db->createCommand($SaveMove)->execute($parameters);

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Movimiento se Realizo Correctamente...",
                    'CancelNo' => $CancelNo
                ));
                 }
            }
        else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese todos los datos que se solicitan...",
            ));
        }
        return;
    }
//Termina

    public function actionReactivarafiliado() {
        global $db;
        if (!empty($_POST['Reactivasion']['RFolio'])) {

            $UpdateMove = "UPDATE rh_titular SET movimientos_afiliacion = :movimientos_afiliacion, fecha_ingreso = :fecha_ingreso
            WHERE folio = :folio";
            $parameters = array(
                ':folio' => $_POST['Reactivasion']['RFolio'],
                ':movimientos_afiliacion' => 'Activo',
                ':fecha_ingreso' => date('Y-m-d')
            );
            if (Yii::app()->db->createCommand($UpdateMove)->execute($parameters)) {

                $MoveNo = GetNextTransNo(20011, $db);
                $SaveMove = "INSERT INTO rh_movimientos_afiliacion (moveno,
                                                                debtorno,
                                                                folio,
                                                                userid,
                                                                movetype,
                                                                motivos,
                                                                monto_recibido,
                                                                tarifa_total,
                                                                fecha_reactivacion -- Se agrego la fecha de reactivación Angeles Perez 13-05-2016
                                                                )
                                                                VALUES(:moveno,
                                                                :debtorno,
                                                                :folio,
                                                                :userid,
                                                                :movetype,
                                                                :motivos,
                                                                :monto_recibido,
                                                                :tarifa_total,
                                                                :fecha_reactivacion -- Se agrego la fecha de reactivación Angeles Perez 13-05-2016
                                                                )";
                $parameters = array(
                    ':moveno' => $MoveNo,
                    ':debtorno' => $_POST['Reactivasion']['RDebtorNo'],
                    ':folio' => $_POST['Reactivasion']['RFolio'],
                    ':userid' => $_SESSION['UserID'],
                    ':movetype' => 'Activo',
                    ':motivos' => 'Reactivacion de Afiliado',
                    ':monto_recibido' => $_POST['Reactivasion']['Rmonto_recibido'],
                    ':tarifa_total' => $_POST['Reactivasion']['Rtarifa_total'],
                    ':fecha_reactivacion' => date('Y-m-d H:i:s') // Se agrego para la fecha de reactivación del folio Angeles Perez 13-05-2016
                );
                Yii::app()->db->createCommand($SaveMove)->execute($parameters);

                $SQLServerWS = new SQLServerWS();
                $SQLServerWS->ReactivarAfiliado($_POST);

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Se ha Levantado la Suspension para el Folio " . $_POST['Reactivasion']['RFolio'] . "...",
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Folio Incorrecto...",
                ));
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese Folio...",
            ));
        }
        return;
    }

    public function actionSuspenderafiliado() {
        global $db;
        FB::INFO($_POST, '__________________________________POST');
        //Validacion de los campos sin datos antes de suspender
        // Eliobeth Ruiz Hernandez 20/12/2016
        if (!empty($_POST['Suspension']['folio']) && !empty($_POST['Suspension']['SFecha_Inicial'])
         && !empty($_POST['Suspension']['SFecha_Final']) && !empty($_POST['Suspension']['SMotivos'])) {

            $SQLUpdateTitular = "UPDATE rh_titular SET movimientos_afiliacion = :movimientos_afiliacion WHERE folio = :folio";
            $parameters = array(
                ':folio' => $_POST['Suspension']['folio'],
                ':movimientos_afiliacion' => 'Suspendido'
            );
            if (Yii::app()->db->createCommand($SQLUpdateTitular)->execute($parameters)) {

                 $_POST['Suspension']['movimientos_afiliacion'] = 3;

                 $SQLServerWS = new SQLServerWS();
                 $SQLServerWS->SuspenderAfiliados($_POST['Suspension']);
                ///Suspende todos sus socios
                $SQLSuspender = "UPDATE custbranch SET sfecha_inicial = :sfecha_inicial, sfecha_final = :sfecha_final, motivo_movimiento = :motivo_movimiento, movimientos_socios = :movimientos_socios, rh_status_captura = :rh_status_captura WHERE folio = :folio AND movimientos_socios != 'Titular' 
                    AND movimientos_socios != 'Cancelado'";
                $parameters = array(
                    ':folio' => $_POST['Suspension']['folio'],
                    ':sfecha_inicial' => $_POST['Suspension']['SFecha_Inicial'],
                    ':sfecha_final' => $_POST['Suspension']['SFecha_Final'],
                    ':motivo_movimiento' => $_POST['Suspension']['SMotivos'],
                    ':movimientos_socios' => 'Suspendido',
                    ':rh_status_captura' => 'Precapturado'
                );

                Yii::app()->db->createCommand($SQLSuspender)->execute($parameters);

                $SQLServerWS = new SQLServerWS();
                $SQLServerWS->SuspenderSocios($_POST['Suspension']);
                ////END Cancela Socios
                $SuspendNo = GetNextTransNo(20021, $db);
                $GetAfil = Yii::app()->db->createCommand()
                    ->select(' rh_titular.*, cob.stockid, cob.frecuencia_pago, sm.description, pm.paymentname,fp.frecuencia, usr.realname ')
                    ->from('rh_titular')
                    ->leftJoin('rh_cobranza cob', 'cob.debtorno = rh_titular.debtorno')
                    ->leftJoin('stockmaster sm', 'sm.stockid = cob.stockid')
                    ->leftJoin('paymentmethods pm', 'pm.paymentid = cob.paymentid')
                    ->leftJoin('rh_frecuenciapago fp', 'fp.id = cob.frecuencia_pago')
                    ->leftJoin('rh_movimientos_afiliacion ma', 'ma.debtorno = rh_titular.debtorno')
                    ->leftJoin('www_users usr', 'usr.userid = ma.userid')
                    ->where('rh_titular.folio = "' . $_POST['Suspension']['folio'] . '"')
                    ->queryAll();
                $Debtorno = $GetAfil[0]['debtorno'];
                $Folio = $GetAfil[0]['folio'];
                $SaveMove = "INSERT INTO rh_movimientos_afiliacion (moveno,
                                                                debtorno,
                                                                folio,
                                                                userid,
                                                                movetype,
                                                                sus_fechainicial,
                                                                sus_fechafinal,
                                                                motivos)
                                                                VALUES(:moveno,
                                                                :debtorno,
                                                                :folio,
                                                                :userid,
                                                                :movetype,
                                                                :sus_fechainicial,
                                                                :sus_fechafinal,
                                                                :motivos)";
                $parameters = array(
                    ':moveno' => $SuspendNo,
                    ':debtorno' => $Debtorno,
                    ':folio' => $Folio,
                    ':userid' => $_SESSION['UserID'],
                    ':movetype' => 'Suspendido',
                    ':sus_fechainicial' => $_POST['Suspension']['SFecha_Inicial'],
                    ':sus_fechafinal' => $_POST['Suspension']['SFecha_Final'],
                    ':motivos' => $_POST['Suspension']['SMotivos']
                );
                Yii::app()->db->createCommand($SaveMove)->execute($parameters);

                //$this->Pdfsuspend($Folio, $SuspendNo, $GetAfil[0]);

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Movimiento se Realizo Correctamente...",
                    'folio' => $Folio,
                    'SuspendNo' => $SuspendNo
                ));
                return;
                //myVar = setTimeout(alertFunc, 3000);

            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese el motivo de la suspención",
                'SuspendNo' => $SuspendNo,
            ));
            return;
        }

    }

/*function alertFunc() {
    alert("Hello!");
} */
    /**
    **@Todo
    **Crea y Actualiza informacion de Cobranza del Afiliado (rh_cobranza)
    **@author erasto@realhost.com.mx
    */
    public function actionCobranza() {
        global $db;
        FB::INFO($_POST, '_________________POST');

        if (isset($_POST['Folio']) || isset($_GET['Folio'])) {
            $Folio = $_REQUEST['Folio'];
        } else {
            $Folio = "";
        }
        if (!empty($_POST['Asesor_ID'])) {
            $Asesor = $_POST['Asesor_ID'];
        } else {
            $Asesor = "";
        }
        /*Para Seccion Maestros*/
        if ($_POST['empresa']==25 || $_POST['empresa']==38) {
            $VigenciaInicial = $_POST['SMVInicialWeeks'] . "-" . $_POST['SMVInicialYears'];
        }

        $GetType = Yii::app()->db->createCommand()->select(' tipo_membresia ')->from('rh_foliosasignados')->where('folio = "' . $Folio . '"')->queryAll();
        $TipoMembresia = $GetType[0]['tipo_membresia'];

        if ((isset($_POST['Search']) || isset($_GET['Folio'])) && !empty($Folio)) {


            $Verify = Yii::app()->db->createCommand()->select(' folio, comisionista_id ')->from('rh_foliosasignados')->where('folio = "' . $Folio . '"')->queryAll();
            if (!empty($Verify)) {
                $Asesor = $Verify[0]['comisionista_id'];
                $RSearch = Yii::app()->db->createCommand()->select(' * ')->from('rh_cobranza')->where('folio = "' . $Folio . '"')->queryAll();
                if (!empty($RSearch)) {
                    Yii::app()->user->setFlash("success", "{$TipoMembresia} N° " . $RSearch[0]['debtorno']);
                    $RSearch[0]['Folio'] = $RSearch[0]['folio'];
                    $_POST = $RSearch[0];
                    FB::INFO($_POST,'_______________________RESULT');
                    $_POST['cuenta'] = $this->OpenSSLDecrypt($_POST['cuenta']);
                    $_POST['vencimiento'] = $this->OpenSSLDecrypt($_POST['vencimiento']);
                    $_POST['cuenta_sat'] = $this->OpenSSLDecrypt($_POST['cuenta_sat']);
                    $_POST['num_plastico'] = $this->OpenSSLDecrypt($_POST['num_plastico']);

                    FB::INFO($_POST,'_______________________RESULT2');


                    $VigenciaInicial = explode("-", $_POST['sm_vigencia']);
                    $_POST['SMVInicialWeeks'] = $VigenciaInicial[0];
                    $_POST['SMVInicialYears'] = $VigenciaInicial[1];

                    /* Obtiene la Zona del Cobrador*/
                    $GetCobradorZona = "SELECT * FROM rh_cobradores WHERE id = '{$RSearch[0]['cobrador']}'";
                    $_2GetResultCobZona = DB_query($GetCobradorZona, $db);
                    $GetResultZona = DB_fetch_assoc($_2GetResultCobZona);
                    FB::INFO($GetResultZona,'_____'.$GetCobradorZona);
                    $_POST['zona'] = $GetResultZona['zona'];

                    /* Obtiene la info de Empresa*/
                    $GetEmpresaData = "SELECT * FROM rh_info_empresa WHERE folio = '{$Folio}'";
                    $_2GetResultEmp = DB_query($GetEmpresaData, $db);;
                    $GetResultEmp = DB_fetch_assoc($_2GetResultEmp);
                    FB::INFO($GetResultEmp,'_________________'.$GetEmpresaData);
                    $_POST['Info_Empresa'] = $GetResultEmp;
                    $_POST['Action'] = "UPDATE";

                } else {
                    $RSearchTitular = Yii::app()->db->createCommand()->select(' * ')->from('rh_titular')->where('folio = "' . $Folio . '"')->queryAll();
                    if (($RSearchTitular[0])) {
                        $RSearchTitular[0]['Folio'] = $RSearchTitular[0]['folio'];
                        $_POST = $RSearchTitular[0];
                    }
                    Yii::app()->user->setFlash("info", "Aun no se ha Ingresado informacion de Cobranza. ");
                }
            } else {
                Yii::app()->user->setFlash("error", "El Folio ingresado no existe ó no esta asignado a un Asesor.");
                $this->redirect($this->createUrl("afiliaciones/afiliacion"));
            }
        }

        if(empty($_POST['dias_credito'])){
            $_POST['dias_credito'] = 30;
        }

        if (!empty($_POST['SaveData'])) {
            FB::INFO($_POST, '_________________POST2');
            $sql = "insert into rh_cobranza (folio,
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
                                            sm_vigencia,
                                            rh_banco,
                                            satid)
                                            values ('" . $_POST['Folio'] . "',
                                            '" . $_POST['debtorno'] . "',
                                            '" . $_POST['address1'] . "',
                                            '" . $_POST['address2'] . "',
                                            '" . $_POST['address3'] . "',
                                            '" . $_POST['address4'] . "',
                                            '" . $_POST['address5'] . "',
                                            '" . $_POST['address6'] . "',
                                            '" . $_POST['address7'] . "',
                                            '" . $_POST['address8'] . "',
                                            '" . $_POST['address10'] . "',
                                            '" . $_POST['cuadrante1'] . "',
                                            '" . $_POST['cuadrante2'] . "',
                                            '" . $_POST['cuadrante3'] . "',
                                            '" . $_POST['rh_tel'] . "',
                                            '" . $_POST['rh_tel2'] . "',
                                            '" . $_POST['email'] . "',
                                            '" . $_POST['enviar_factura'] . "',
                                            '" . $_POST['encargado_pagos'] . "',
                                            '" . $_POST['stockid'] . "',
                                            '" . $_POST['empresa'] . "',
                                            '" . $_POST['frecuencia_pago'] . "',
                                            '" . $_POST['convenio'] . "',
                                            'DEFAULT',
                                            '" . $_POST['paymentid'] . "',
                                            '" . $_POST['zona'] . "',
                                            '" . $_POST['cobrador'] . "',
                                            '" . $this->OpenSSLEncrypt($_POST['cuenta']) . "',
                                            '" . $this->OpenSSLEncrypt($_POST['vencimiento']) . "',
                                            '" . $this->OpenSSLEncrypt($_POST['cuenta_sat']) . "',
                                            '" . $this->OpenSSLEncrypt($_POST['num_plastico']) . "',
                                            '" . $_POST['metodo_pago'] . "',
                                            '" . $_POST['tipo_tarjeta'] . "',
                                            '" . $_POST['tipo_cuenta'] . "',
                                            '" . $_POST['num_empleado'] . "',
                                            '" . $_POST['identificacion'] . "',
                                            '" . $_POST['fecha_corte'] . "',
                                            '" . $_POST['factura_fisica'] . "',
                                            '" . $_POST['folio_asociado'] . "',
                                            '" . $_POST['dias_cobro'] . "',
                                            '" . $_POST['dias_cobro_dia'] . "',
                                            '" . $_POST['cobro_datefrom'] . "',
                                            '" . $_POST['cobro_dateto'] . "',
                                            '" . $_POST['dias_credito'] . "',
                                            '" . $_POST['dias_revision'] . "',
                                            '" . $_POST['dias_revision_dia'] . "',
                                            '" . $_POST['revision_datefrom'] . "',
                                            '" . $_POST['revision_dateto'] . "',
                                            '1',
                                            '" . $_POST['sm_clavefiliacion'] . "',
                                            '" . $_POST['sm_depto'] . "',
                                            '" . $_POST['sm_cpresupuestal'] . "',
                                            '" . $VigenciaInicial . "',
                                            '" . $_POST['rh_banco'] . "',
                                            '" . $_POST['satid'] . "')";//Se agrego para insertar el valor sel identificador requerido por el SAT Angeles Perez 2016-07-12
            FB::INFO($sql, '_______________________INSERT');

            if (DB_query($sql, $db)) {
                //$this->ActualizaCosto($_POST['Folio']);
                $LogData = array(
                    'folio' => $_POST['Folio'],
                    'debtorno' => $_POST['debtorno'],
                    'fecha_corte' => $_POST['fecha_corte']
                );
                /* Create Empresa Data*/
                $this->DatosEmpresa($_POST['Info_Empresa']);
                $this->Validapaymentterms($_POST['dias_credito']);

                $_POST['cuenta'] = $this->OpenSSLDecrypt($_POST['cuenta']);
                $_POST['vencimiento'] = $this->OpenSSLDecrypt($_POST['vencimiento']);
                $_POST['cuenta_sat'] = $this->OpenSSLDecrypt($_POST['cuenta_sat']);
                $_POST['num_plastico'] = $this->OpenSSLDecrypt($_POST['num_plastico']);

                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->InsertCobranza($_POST);
                }

                try {
                    $UpdateDebtorsMaster = "UPDATE debtorsmaster SET
                        paymentterms = '{$_POST['dias_credito']}',
                        rh_banco = '{$_POST['rh_banco']}'
                        WHERE debtorno = {$_POST['debtorno']} ";
                    DB_query($UpdateDebtorsMaster, $db);

                    /*Actualizo Branchcode Principal*/ // Se agrego satid para hacer el insert del identificador requerido por el SAT Angeles Perez 2016-07-12
                    $UpdateBranch = "UPDATE custbranch SET
                        metodopago = '" . DB_escape_string($_POST['metodo_pago']) . "',
                        cuentapago = '" . DB_escape_string($_POST['cuenta_sat']) . "',
                        satid = '" . DB_escape_string($_POST['satid']) . "'
                        WHERE branchcode = 'T-" . $_POST['debtorno'] . "'
                        AND folio = '" . $_POST['Folio'] . "'
                        AND debtorno = '" . $_POST['debtorno'] . "' ";
                    FB::INFO($UpdateBranch, '_______________________UpdateBranch');
                    DB_query($UpdateBranch, $db);

                } catch (Exception $e) {
                    FB::INFO($e->getMessage(), 'Error al ACtualizar DebtorsMaster: ');
                }

                $_POST['Action'] = "UPDATE";
                //exit;
                Yii::app()->user->setFlash("success", "Los datos del Afiliado se han Guardado correctamente.");
                if ($TipoMembresia=="Socio") {
                    $this->redirect($this->createUrl('afiliaciones/socios&Folio=' . $_POST['Folio']));
                }
            } else {
                Yii::app()->user->setFlash("error", "No se pudo guardar la Informacion del Afiliado, intente de nuevo.");
            }
        }

        //Movimientos Afiliados
        if (!empty($_POST['UpdateData'])) {
            $SQLUpdate = "UPDATE rh_cobranza SET folio = '" . $_POST['Folio'] . "',
                                                address1 = '" . $_POST['address1'] . "',
                                                address2 = '" . $_POST['address2'] . "',
                                                address3 = '" . $_POST['address3'] . "',
                                                address4 = '" . $_POST['address4'] . "',
                                                address5 = '" . $_POST['address5'] . "',
                                                address6 = '" . $_POST['address6'] . "',
                                                address7 = '" . $_POST['address7'] . "',
                                                address8 = '" . $_POST['address8'] . "',
                                                address10 = '" . $_POST['address10'] . "',
                                                cuadrante1 = '" . $_POST['cuadrante1'] . "',
                                                cuadrante2 = '" . $_POST['cuadrante2'] . "',
                                                cuadrante3 = '" . $_POST['cuadrante3'] . "',
                                                rh_tel = '" . $_POST['rh_tel'] . "',
                                                rh_tel2 = '" . $_POST['rh_tel2'] . "',
                                                email = '" . $_POST['email'] . "',
                                                enviar_factura = '" . $_POST['enviar_factura'] . "',
                                                encargado_pagos = '" . $_POST['encargado_pagos'] . "',
                                                stockid = '" . $_POST['stockid'] . "',
                                                empresa = '" . $_POST['empresa'] . "',
                                                frecuencia_pago = '" . $_POST['frecuencia_pago'] . "',
                                                convenio = '" . $_POST['convenio'] . "',
                                                paymentid = '" . $_POST['paymentid'] . "',
                                                zona = '" . $_POST['zona'] . "',
                                                cobrador = '" . $_POST['cobrador'] . "',
                                                cuenta = '" . $this->OpenSSLEncrypt($_POST['cuenta']) . "',
                                                vencimiento = '" . $this->OpenSSLEncrypt($_POST['vencimiento']) . "',
                                                cuenta_sat = '" . $this->OpenSSLEncrypt($_POST['cuenta_sat']) . "',
                                                num_plastico = '" . $this->OpenSSLEncrypt($_POST['num_plastico']) . "',
                                                metodo_pago = '" . $_POST['metodo_pago'] . "',
                                                tipo_tarjeta = '" . $_POST['tipo_tarjeta'] . "',
                                                tipo_cuenta = '" . $_POST['tipo_cuenta'] . "',
                                                num_empleado = '" . $_POST['num_empleado'] . "',
                                                identificacion = '" . $_POST['identificacion'] . "',
                                                fecha_corte = '" . $_POST['fecha_corte'] . "',
                                                factura_fisica = '" . $_POST['factura_fisica'] . "',
                                                folio_asociado = '" . $_POST['folio_asociado'] . "',
                                                dias_cobro = '" . $_POST['dias_cobro'] . "',
                                                dias_cobro_dia = '" . $_POST['dias_cobro_dia'] . "',
                                                cobro_datefrom = '" . $_POST['cobro_datefrom'] . "',
                                                cobro_dateto = '" . $_POST['cobro_dateto'] . "',
                                                dias_credito = '" . $_POST['dias_credito'] . "',
                                                dias_revision = '" . $_POST['dias_revision'] . "',
                                                dias_revision_dia = '" . $_POST['dias_revision_dia'] . "',
                                                revision_datefrom = '" . $_POST['revision_datefrom'] . "',
                                                revision_dateto = '" . $_POST['revision_dateto'] . "',
                                                sm_clavefiliacion = '" . $_POST['sm_clavefiliacion'] . "',
                                                sm_depto = '" . $_POST['sm_depto'] . "',
                                                sm_cpresupuestal = '" . $_POST['sm_cpresupuestal'] . "',
                                                sm_vigencia = '" . $VigenciaInicial . "',
                                                rh_banco = '" . $_POST['rh_banco'] . "',
                                                satid = '" . $_POST['satid'] . "'
                                                WHERE folio = '" . $_POST['Folio'] . "'
                                                AND debtorno = '" . $_POST['debtorno'] . "' ";// Se agrego el And para que no permita duplicar registros Angeles Perez 30/06/2016
            FB::INFO($SQLUpdate, '_______________________UPDATE');
            FB::INFO($_POST, '_______________________POST');
            if (DB_query($SQLUpdate, $db)) {
                //$this->ActualizaCosto($_POST['Folio']);
                $_POST['Action'] = "UPDATE";
                //Actualizo en la tabla de CCM_FolCobranza del SQLServer
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->UpdateCobranza($_POST);
                }
                /* Update Empresa Data*/
                $this->DatosEmpresa($_POST['Info_Empresa']);
                $this->Validapaymentterms($_POST['dias_credito']);

                try {
                    $UpdateDebtorsMaster = "UPDATE debtorsmaster SET
                        paymentterms = '{$_POST['dias_credito']}',
                        rh_banco = '{$_POST['rh_banco']}'
                        WHERE debtorno = {$_POST['debtorno']} ";
                    DB_query($UpdateDebtorsMaster, $db);

                    /*Actualizo Branchcode Principal*/
                    $UpdateBranch = "UPDATE custbranch SET
                        metodopago = '" . DB_escape_string($_POST['metodo_pago']) . "',
                        cuentapago = '" . DB_escape_string($_POST['cuenta_sat']) . "',
                        satid = '" . DB_escape_string($_POST['satid']) . "'
                        WHERE branchcode = 'T-" . $_POST['debtorno'] . "'
                        AND folio = '" . $_POST['Folio'] . "'
                        AND debtorno = '" . $_POST['debtorno'] . "' ";
                    FB::INFO($UpdateBranch, '_______________________UpdateBranch');
                    DB_query($UpdateBranch, $db);

                } catch (Exception $e) {
                    FB::INFO($e->getMessage(), 'Error al ACtualizar DebtorsMaster: ');
                }

                Yii::app()->user->setFlash("success", "Los datos del Afiliado se Actualizaron correctamente.");
            } else {
                Yii::app()->user->setFlash("error", "La Informacion no se pudo actualizar, intente de nuevo.");
            }
        }

        // select * from stockmaster where categoryid = 'AFIL';
        $_ListaPlanes = Yii::app()->db->createCommand()->select(' stockid, description ')->from('stockmaster')->where('categoryid = "AFIL" ORDER BY stockid ASC')->queryAll();
        $ListaPlanes = array();
        foreach ($_ListaPlanes as $Planes) {
            $ListaPlanes[$Planes['stockid']] = $Planes['description'];
        }


         $_ListaIdentificaciones = Yii::app()->db->createCommand()
         ->select(' id, nombre ')
         ->from('rh_identificaciones')
         ->where('status = 1')
         ->queryAll();

        $ListaFormasPago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaMetodoPago = Metodopago::model()->findAll();// Se agrego para el metodo de pago del SAT Angeles Perez, Daniel Villarreal 12/07/2016
        $ListaTipoTarjetas = CHtml::listData(TipoTarjeta::model()->findAll(), 'id', 'tipotarjeta');
        $ListaFrecuenciaPago = CHtml::listData(FrecuenciaPago::model()->findAll(), 'id', 'frecuencia');
        $ListaConvenios = CHtml::listData(Convenio::model()->findAll(), 'id', 'convenio');
        $ListaComisionistas = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');
        $ListaCobradores = CHtml::listData(Cobradores::model()->findAll(), 'id', 'nombre');
        $ListaEmpresas = CHtml::listData(Empresa::model()->findAll(), 'id', 'empresa');
        $ListaMunicipios = CHtml::listData(Municipio::model()->findAll(array('order' => 'municipio')), 'id', 'municipio');
        $ListaEstados = CHtml::listData(Estado::model()->findAll(), 'id', 'estado');
        $ListaHospitales = CHtml::listData(Hospital::model()->findAll(), 'id', 'nombre');// Se agrego para mostrar la lista de hospitales Angeles Perez 2016-06-09
        $ListaClasificacion = CHtml::listData(Clasificacion::model()->findAll(), 'id', 'descripcion');// Se agrego para mostrar la lista de clasificaciones para la empresa Angeles Perez 2016-06-28
        $this->render('cobranza', array(
            'ListaMunicipios' => $ListaMunicipios,
            'ListaEstados' => $ListaEstados,
            'ListaHospitales' => $ListaHospitales,// Se agrego para mostrar la lista de hospitales Angeles Perez 2016-06-09
            'ListaClasificacion' => $ListaClasificacion,// Se agrego para mostrar la lista de clasificaciones de la empresa Angeles Perez 2016-06-28
            'ListaFrecuenciaPago' => $ListaFrecuenciaPago,
            'ListaConvenios' => $ListaConvenios,
            'ListaFormasPago' => $ListaFormasPago,
            'ListaMetodoPago' => $ListaMetodoPago,// Se agrego para el metodo de pago del SAT Angeles Perez, Daniel Villarreal 12/07/2016
            'ListaComisionistas' => $ListaComisionistas,
            'ListaEmpresas' => $ListaEmpresas,
            'ListaPlanes' => $ListaPlanes,
            'ListaTipoTarjetas' => $ListaTipoTarjetas,
            'ListaCobradores' => $ListaCobradores,
            'TipoMembresia' => $TipoMembresia,
            'ListaIdentificaciones' => $_ListaIdentificaciones,
        ));
    }

    /**/
    public function Validapaymentterms($termsindicator){

        if(!empty($termsindicator)){
            $GetTerms = Yii::app()->db->createCommand()
            ->select("termsindicator")
            ->from("paymentterms")
            ->where("termsindicator = :termsindicator ", array(':termsindicator' => $termsindicator))->queryAll();

            // termsindicator | terms    | daysbeforedue | dayinfollowingmonth
            if(empty($GetTerms)){
                $InsertTerm = "INSERT INTO paymentterms (
                    termsindicator,
                    terms,
                    daysbeforedue,
                    dayinfollowingmonth
                ) VALUES (
                    :termsindicator,
                    :terms,
                    :daysbeforedue,
                    :dayinfollowingmonth
                )";

                $InsertParameters = array(
                    ':termsindicator' => $termsindicator,
                    ':terms' => $termsindicator . ' DIAS',
                    ':daysbeforedue' => 0,
                    ':dayinfollowingmonth' => 1
                    );
                try {
                    Yii::app()->db->createCommand($InsertTerm)->execute($InsertParameters);
                } catch (Exception $e) {
                    FB::INFO($e, '________________________________ERROR al insertar en PaymentTerms: ');
                }
            }
        }

    }


    /**
     * @todo
     * Inserta y Actualiza Datos de la Empresa desde la pantalla de Cobranza
    */
    public function DatosEmpresa($EmpresaData){
        global $db;
        FB::INFO($EmpresaData,'______________EMPRESA DATA');
        if(!empty($EmpresaData) && !empty($EmpresaData['folio'])){

            $GetEmpresaData = "SELECT folio FROM rh_info_empresa WHERE folio = '{$EmpresaData['folio']}'";
            $_2GetResult = DB_query($GetEmpresaData, $db);
            $GetResult = DB_fetch_assoc($_2GetResult);
            if(!empty($GetResult)){
                $UpdateEmpresaData ="UPDATE rh_info_empresa SET clasificacion = '" . $EmpresaData['clasificacion'] . "',
                    medico_planta = '" . $EmpresaData['medico_planta'] . "',
                    visitantes = '" . $EmpresaData['visitantes'] . "',
                    nempleados = '" . $EmpresaData['nempleados'] . "',
                    turnos = '" . $EmpresaData['turnos'] . "',
                    dias_publicos = '" . $EmpresaData['dias_publicos'] . "',
                    tiempo_permanencia = '" . $EmpresaData['tiempo_permanencia'] . "'
                    WHERE folio = '" . $EmpresaData['folio'] . "' ";
                DB_query($UpdateEmpresaData, $db);
            }else{
                $InsertEmpresaData ="INSERT INTO rh_info_empresa (folio,
                    clasificacion,
                    medico_planta,
                    visitantes,
                    nempleados,
                    turnos,
                    dias_publicos,
                    tiempo_permanencia,
                    created)
                VALUES(
                    '" . $EmpresaData['folio'] . "',
                    '" . $EmpresaData['clasificacion'] . "',
                    '" . $EmpresaData['medico_planta'] . "',
                    '" . $EmpresaData['visitantes'] . "',
                    '" . $EmpresaData['nempleados'] . "',
                    '" . $EmpresaData['turnos'] . "',
                    '" . $EmpresaData['dias_publicos'] . "',
                    '" . $EmpresaData['tiempo_permanencia'] . "',
                    '" . date("Y-m-d H:i:s") . "'
                    )";
                FB::INFO($GetResult,'________________'. $InsertEmpresaData);
                DB_query($InsertEmpresaData, $db);
            }
        }
        return true;
    }


    /**
     * GetNextBranchCode
     * Obtiene el Siguiente BranchCode para un DebtorNo
     * @return void
     * @author  erasto@realhost.com.mx
     */
    public function GetNextBranchCode($Debtorno) {
        global $db;
        $GetLastBranchCode = "SELECT * FROM custbranch WHERE debtorno = '" . $Debtorno . "' ORDER BY branchcode + 0 DESC";
        $_GLBResult = DB_query($GetLastBranchCode, $db);
        $GLBResult = DB_fetch_assoc($_GLBResult);
        if (empty($GLBResult)) {
            $BranchCode = 1;
        } else {
            $BranchCode = $GLBResult['branchcode'] + 1;
        }
        if ($BranchCode==$Debtorno) {
            $BranchCode = $BranchCode + 1;
        }
        return $BranchCode;
    }

    public function actionSuspendersocio() {
        global $db;
        //agregar validaciones para los campos solicitados
        //Eliobeth Ruiz 20/12/2016
        if (!empty($_POST['Suspender']['SBranchCode']) && !empty($_POST['Suspender']['SDebtorNo'])
            && !empty($_POST['Suspender']['SMotivos']) ) {

            $socios_restantes = "SELECT count(*) as total__socios FROM custbranch
                    WHERE debtorno = '".$_POST['Suspender']['SDebtorNo']."'
                    AND (movimientos_socios != 'Cancelado' and movimientos_socios != 'Suspendido'  and movimientos_socios != 'Titular')";
            $resultado_socios_restantes = DB_query($socios_restantes, $db); 
            $total_socios_restantes = DB_fetch_assoc($resultado_socios_restantes);

            $_estatus_socios_restantes = "SELECT movimientos_socios FROM custbranch
                    WHERE debtorno = '".$_POST['Suspender']['SDebtorNo']."' 
                    AND branchcode = '".$_POST['Suspender']['SBranchCode']."'";
            $_resultado_estatus_socios_restantes = DB_query($_estatus_socios_restantes, $db); 
            $_total_estatus_socios_restantes = DB_fetch_array($_resultado_estatus_socios_restantes);

                if ($total_socios_restantes['total__socios'] > 1) {
                    $SQLSuspender = "UPDATE custbranch SET
                sfecha_inicial = :sfecha_inicial,
                sfecha_final = :sfecha_final,
                motivo_movimiento = :motivo_movimiento,
                movimientos_socios = :movimientos_socios
            WHERE branchcode = :branchcode AND debtorno = :debtorno ";

            $parameters = array(
                ':branchcode' => $_POST['Suspender']['SBranchCode'],
                ':debtorno' => $_POST['Suspender']['SDebtorNo'],
                ':sfecha_inicial' => $_POST['Suspender']['SFecha_Inicial'],
                ':sfecha_final' => $_POST['Suspender']['SFecha_Final'],
                ':motivo_movimiento' => $_POST['Suspender']['SMotivos'],
                ':movimientos_socios' => 'Suspendido'
            );
            if (Yii::app()->db->createCommand($SQLSuspender)->execute($parameters)) {
                $_POST['Suspender']['movimientos_afiliacion'] = 3;
                $Folio = $this->GetFolio($_POST['Suspender']['SDebtorNo']);
                /******************************ACTUALIZA TARIFA************************************/
                $Tarifa = $this->ActualizaCosto($Folio);
                FB::INFO($Tarifa,'___TARIFA');
                $UpdateTarifa = "UPDATE rh_titular SET costo_total = :costo_total WHERE folio = :folio";
                $UpdateParameters = array(
                    ':costo_total' => $Tarifa['CostoTotal'],
                    ':folio' => $Folio
                    );
                Yii::app()->db->createCommand($UpdateTarifa)->execute($UpdateParameters);
                /******************************ACTUALIZA TARIFA************************************/
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->SuspenderSocio($_POST['Suspender'], $Folio);
                }
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Suspender']['SBranchCode'] . " ha sido Suspendido...",
                    'BranchCode' => $_POST['Suspender']['SBranchCode']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Nose pudo suspender el Socio, intente de nuevo...",
                ));
            }
            }elseif($total_socios_restantes['total__socios'] == 1 && $_total_estatus_socios_restantes['movimientos_socios'] != "Activo") {
                    $SQLSuspender = "UPDATE custbranch SET
                sfecha_inicial = :sfecha_inicial,
                sfecha_final = :sfecha_final,
                motivo_movimiento = :motivo_movimiento,
                movimientos_socios = :movimientos_socios
            WHERE branchcode = :branchcode AND debtorno = :debtorno ";

            $parameters = array(
                ':branchcode' => $_POST['Suspender']['SBranchCode'],
                ':debtorno' => $_POST['Suspender']['SDebtorNo'],
                ':sfecha_inicial' => $_POST['Suspender']['SFecha_Inicial'],
                ':sfecha_final' => $_POST['Suspender']['SFecha_Final'],
                ':motivo_movimiento' => $_POST['Suspender']['SMotivos'],
                ':movimientos_socios' => 'Suspendido'
            );
            if (Yii::app()->db->createCommand($SQLSuspender)->execute($parameters)) {
                $_POST['Suspender']['movimientos_afiliacion'] = 3;
                $Folio = $this->GetFolio($_POST['Suspender']['SDebtorNo']);
                /******************************ACTUALIZA TARIFA************************************/
                $Tarifa = $this->ActualizaCosto($Folio);
                FB::INFO($Tarifa,'___TARIFA');
                $UpdateTarifa = "UPDATE rh_titular SET costo_total = :costo_total WHERE folio = :folio";
                $UpdateParameters = array(
                    ':costo_total' => $Tarifa['CostoTotal'],
                    ':folio' => $Folio
                    );
                Yii::app()->db->createCommand($UpdateTarifa)->execute($UpdateParameters);
                /******************************ACTUALIZA TARIFA************************************/
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->SuspenderSocio($_POST['Suspender'], $Folio);
                }
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Suspender']['SBranchCode'] . " ha sido Suspendido...",
                    'BranchCode' => $_POST['Suspender']['SBranchCode']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Nose pudo suspender el Socio, intente de nuevo...",
                ));
            }
                }else{
                    echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se puede suspender el socio seleccionado puesto que es el último socio activo, si desea suspender dicho socio, puede hacerlo suspendiendo el titular ",
                    ));
                }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese el motivo de suspensión para proceder con la petición... ",
            ));
        }
        return;
    }

    /**
     * @todo
     * Cancela Socio y Recalcula Tarifa
     * @return [type] [description]
     */
    public function actionCancelarsocio() {
        global $db;
        //Validar que se completen todos los campos solicitados
        //Eliobeth Ruiz 20/12/2016
        if (!empty($_POST['Cancelar']['CBranchCode']) && !empty($_POST['Cancelar']['CDebtorNo'])
            && !empty($_POST['Cancelar']['CMotivos'])) {

            $__c__debtono = $_POST['Cancelar']['CDebtorNo'];
             $socios_restantes = "SELECT count(*) as sociostotales FROM custbranch
                    WHERE debtorno = '".$_POST['Cancelar']['CDebtorNo']."'
                    AND (movimientos_socios != 'Cancelado' 
                    and movimientos_socios != 'Suspendido'  
                    and movimientos_socios != 'Titular')";
            $resultado_socios_restantes = DB_query($socios_restantes, $db); 
            $total_socios_restantes = DB_fetch_array($resultado_socios_restantes);

            $estatus_socios_restantes = "SELECT movimientos_socios FROM custbranch
                    WHERE debtorno = '".$_POST['Cancelar']['CDebtorNo']."' 
                    AND branchcode = '".$_POST['Cancelar']['CBranchCode']."'";
            $resultado_estatus_socios_restantes = DB_query($estatus_socios_restantes, $db); 
            $total_estatus_socios_restantes = DB_fetch_array($resultado_estatus_socios_restantes);
//echo "Hola mundo"; $total_socios_restantes['sociostotales']
//echo "<pre>";print_r($total_socios_restantes);exit();
                if ($total_socios_restantes['sociostotales'] > 1) { 
                    $SQLCancelar = "UPDATE custbranch SET
                fecha_baja = :fecha_baja,
                motivo_cancelacion = :motivo_cancelacion,
                motivo_movimiento = :motivo_movimiento,
                movimientos_socios = :movimientos_socios,
                rh_status_captura = :rh_status_captura
            WHERE branchcode = :branchcode AND debtorno = :debtorno ";

            $parameters = array(
                ':branchcode' => $_POST['Cancelar']['CBranchCode'],
                ':debtorno' => $_POST['Cancelar']['CDebtorNo'],
                ':fecha_baja' => $_POST['Cancelar']['CFecha_Baja'],
                ':motivo_cancelacion' => $_POST['Cancelar']['CMotivos'],
                ':motivo_movimiento' => $_POST['Cancelar']['CMotivos'],
                ':movimientos_socios' => 'Cancelado',
                ':rh_status_captura' => 'Activo'
            );
            if (Yii::app()->db->createCommand($SQLCancelar)->execute($parameters)) {

                /******************************ACTUALIZA TARIFA************************************/
                $Tarifa = $this->ActualizaCosto($_POST['Cancelar']['CFolio']);
                FB::INFO($Tarifa,'___TARIFA');
                $UpdateTarifa = "UPDATE rh_titular SET costo_total = :costo_total WHERE folio = :folio";
                $UpdateParameters = array(
                    ':costo_total' => $Tarifa['CostoTotal'],
                    ':folio' => $_POST['Cancelar']['CFolio']
                    );
                Yii::app()->db->createCommand($UpdateTarifa)->execute($UpdateParameters);
                /******************************ACTUALIZA TARIFA************************************/
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->CancelarSocio($_POST['Cancelar']);
                }

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Cancelar']['CBranchCode'] . " ha sido Cancelado...",
                    'BranchCode' => $_POST['Cancelar']['CBranchCode']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo Cancelar el Socio, intente de nuevo...",
                ));
            }
                }elseif ($total_socios_restantes['sociostotales'] == 1 && $total_estatus_socios_restantes['movimientos_socios'] != "Activo") {
                    $SQLCancelar = "UPDATE custbranch SET
                fecha_baja = :fecha_baja,
                motivo_cancelacion = :motivo_cancelacion,
                motivo_movimiento = :motivo_movimiento,
                movimientos_socios = :movimientos_socios,
                rh_status_captura = :rh_status_captura
            WHERE branchcode = :branchcode AND debtorno = :debtorno ";

            $parameters = array(
                ':branchcode' => $_POST['Cancelar']['CBranchCode'],
                ':debtorno' => $_POST['Cancelar']['CDebtorNo'],
                ':fecha_baja' => $_POST['Cancelar']['CFecha_Baja'],
                ':motivo_cancelacion' => $_POST['Cancelar']['CMotivos'],
                ':motivo_movimiento' => $_POST['Cancelar']['CMotivos'],
                ':movimientos_socios' => 'Activo',
                ':rh_status_captura' => 'Cancelado'
            );
            if (Yii::app()->db->createCommand($SQLCancelar)->execute($parameters)) {

                /******************************ACTUALIZA TARIFA************************************/
                $Tarifa = $this->ActualizaCosto($_POST['Cancelar']['CFolio']);
                FB::INFO($Tarifa,'___TARIFA');
                $UpdateTarifa = "UPDATE rh_titular SET costo_total = :costo_total WHERE folio = :folio";
                $UpdateParameters = array(
                    ':costo_total' => $Tarifa['CostoTotal'],
                    ':folio' => $_POST['Cancelar']['CFolio']
                    );
                Yii::app()->db->createCommand($UpdateTarifa)->execute($UpdateParameters);
                /******************************ACTUALIZA TARIFA************************************/
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->CancelarSocio($_POST['Cancelar']);
                }

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Cancelar']['CBranchCode'] . " ha sido Cancelado...",
                    'BranchCode' => $_POST['Cancelar']['CBranchCode']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudo Cancelar el Socio, intente de nuevo...",
                ));
            }
                }else{
                    echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    // Mensaje de validacion (Eliobeth Ruiz 20/12/2016)
                    'message' => "No se puede cancelar el socio seleccionado puesto que es el último socio activo, si desea cancelar dicho socio, puede hacerlo cancelando el titular",
                ));
                } 
            
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                // Mensaje de validacion (Eliobeth Ruiz 20/12/2016)
                'message' => "Seleccione un motivo de cancelacion para proceder con la petición",
            ));
        }
        return;
    }

    public function actionUpdatesocio() {

        global $db;
        if (!empty($_POST['GetData']['BranchCode']) && !empty($_POST['GetData']['DebtorNo'])) {
            $GetData = array();
            $GetData = Yii::app()->db->createCommand()->select(' * ')->from('custbranch')->where('branchcode = "' . $_POST['GetData']['BranchCode'] . '" AND debtorno = "' . $_POST['GetData']['DebtorNo'] . '" ')->queryAll();
            FB::INFO($GetData,'_____________DATA');
            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "Socio " . $_POST['GetData']['BranchCode'] . " seleccionado...",
                'GetData' => $GetData[0],
                'GetAntecedentes' => json_decode($GetData[0]['antecedentes_clinicos'], 1)
            ));
        }

        if (!empty($_POST['Update'])) {

            $Antecedentes = explode(",", $_POST['Update']['antecedentes_clinicos']);
            $_POST['Update']['antecedentes_clinicos'] = json_encode($Antecedentes);

            $SQLUpdate = "UPDATE custbranch SET brname = :brname,
                                                 sexo = :sexo,
                                                 nombre_empresa = :nombre_empresa,
                                                 braddress1 = :braddress1,
                                                 braddress2 = :braddress2,
                                                 fecha_ingreso = :fecha_ingreso,
                                                 fecha_ultaum = :fecha_ultaum,
                                                 fecha_nacimiento = :fecha_nacimiento,
                                                 braddress10 = :braddress10,
                                                 braddress4 = :braddress4,
                                                 braddress5 = :braddress5,
                                                 braddress6 = :braddress6,
                                                 braddress7 = :braddress7,
                                                 braddress8 = :braddress8,
                                                 braddress11 = :braddress11,
                                                 cuadrante1 = :cuadrante1,
                                                 cuadrante2 = :cuadrante2,
                                                 cuadrante3 = :cuadrante3,
                                                 phoneno = :phoneno,
                                                 antecedentes_clinicos = :antecedentes_clinicos,
                                                 otros_padecimientos= :otros_padecimientos
                                             WHERE branchcode = :branchcode AND debtorno = :debtorno ";
            $parameters = array(
                ':branchcode' => $_POST['Update']['branchcode'],
                ':debtorno' => $_POST['Update']['debtorno'],
                ':brname' => $_POST['Update']['brname'],
                ':sexo' => $_POST['Update']['sexo'],
                ':nombre_empresa' => $_POST['Update']['nombre_empresa'],
                ':braddress1' => $_POST['Update']['braddress1'],
                ':braddress2' => $_POST['Update']['braddress2'],
                ':fecha_ingreso' => $_POST['Update']['fecha_ingreso'],
                ':fecha_ultaum' => $_POST['Update']['fecha_ultaum'],
                ':fecha_nacimiento' => $_POST['Update']['fecha_nacimiento'],
                ':braddress10' => $_POST['Update']['braddress10'],
                ':braddress4' => $_POST['Update']['braddress4'],
                ':braddress5' => $_POST['Update']['braddress5'],
                ':braddress6' => $_POST['Update']['braddress6'],
                ':braddress7' => $_POST['Update']['braddress7'],
                ':braddress8' => $_POST['Update']['braddress8'],
                ':braddress11' => $_POST['Update']['braddress11'],
                ':cuadrante1' => $_POST['Update']['cuadrante1'],
                ':cuadrante2' => $_POST['Update']['cuadrante2'],
                ':cuadrante3' => $_POST['Update']['cuadrante3'],
                ':phoneno' => $_POST['Update']['phoneno'],
                ':antecedentes_clinicos' => $_POST['Update']['antecedentes_clinicos'],
                ':otros_padecimientos' => $_POST['Update']['otros_padecimientos']//Se agrego para mostrar lo que se ingresa en el campo Otros Padecimientos Angeles Perez 2016-08-12
            );

            try {
                Yii::app()->db->createCommand($SQLUpdate)->execute($parameters);
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    // $SQLServerWS = new SQLServerWS();
                    // $SQLServerWS->UpdateSocios($_POST['Update']);
                }

                $GetData = array();
                $GetData = Yii::app()->db->createCommand()->select(' * ')->from('custbranch')->where('branchcode = "' . $_POST['Update']['branchcode'] . '" AND debtorno = "' . $_POST['Update']['debtorno'] . '" ')->queryAll();
                $NewRow = "";
                $NewRow .= "
              <td>{$_POST['Update']['branchcode']}</td>
              <td>{$_POST['Update']['brname']}</td>
              <td>{$_POST['Update']['sexo']}</td>
              <td>{$_POST['Update']['nombre_empresa']}</td>
              <td>{$_POST['Update']['fecha_nacimiento']}</td>
              <td>{$_POST['Update']['braddress1']}</td>
              <td>{$_POST['Update']['braddress2']}</td>
              <td>{$_POST['Update']['phoneno']}</td>
              <td>{$_POST['Update']['fecha_ingreso']}</td>
              <td>
                  <select id='Change_Status' name='Change_Status' BranchCode='{$_POST['Update']['branchcode']}' DebtorNo='{$_POST['Update']['debtorno']}' onchange='ChangeStatus(this.value,{$_POST['Update']['debtorno']},{$_POST['Update']['branchcode']},{$_POST['Update']['folio']})' >";

                if (($GetData[0]['movimientos_socios']=="Nuevo") && ($GetData[0]['rh_status_captura']=="Precapturado")) {
                    $MoreIcons = "
                    <a id='ActivarSocio' name = 'ActivarSocio' BranchCode='{$Socio['branchcode']}' DebtoNo='{$Socio['debtorno']}' title='Activar Socio' onclick='ActivarSocio({$_POST['Update']['debtorno']},{$_POST['Update']['branchcode']},{$_POST['Update']['folio']})' ><i class='icon-check'></i></a>
                    <a id='EliminarSocio' name = 'EliminarSocio' BranchCode='{$Socio['branchcode']}' DebtoNo='{$Socio['debtorno']}' title='Eliminar Socio' onclick='EliminarSocio({$Socio['debtorno']},{$Socio['branchcode']},{$Socio['folio']})' ><i class='icon-remove'></i></a>
                    ";
                }

                $NewRow .= "
                      <option SELECTED='SELECTED' value='{$GetData[0]['movimientos_socios']}'>{$GetData[0]['movimientos_socios']}</option>
                      <option value='Activo'>Activo</option>
                      <option value='Cancelado'>Cancelado</option>
                      <option value='Suspendido'>Suspendido</option>
                  </select>
              </td>
              <td>
                 <a id='ViewSocio' name = 'ViewSocio'  BranchCode='{$_POST['Update']['branchcode']}' DebtorNo='{$_POST['Update']['debtorno']}' title='Detalles' onclick='ViewSocio({$_POST['Update']['debtorno']},{$_POST['Update']['branchcode']},{$_POST['Update']['folio']})' ><i class='icon-eye-open'></i></a>&nbsp;
                 <a id='EditSocio'   name = 'EditSocio'   BranchCode='{$_POST['Update']['branchcode']}' DebtorNo='{$_POST['Update']['debtorno']}' title='Editar Socio' onclick='EditSocio({$_POST['Update']['debtorno']},{$_POST['Update']['branchcode']},{$_POST['Update']['folio']})' ><i class='icon-edit'></i></a>&nbsp;
                  $MoreIcons
                </td>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Update']['branchcode'] . " se actualizo correctamente...",
                    'BranchCode' => $_POST['Update']['branchcode'],
                    'NewRow' => $NewRow
                ));
            } catch (Exception $e) {

                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Error: " . $e->getMessage(),
                ));
            }
        }
        return;
    }

    public function actionReactivarsocio() {
        global $db;
        if (!empty($_POST['Reactivar']['RBranchCode']) && !empty($_POST['Reactivar']['RDebtorNo'])) {
            FB::INFO($_POST, '____________________________POST RS');
            //Actualiza Custbranch a Activo
            $SQLSuspender = "UPDATE custbranch SET movimientos_socios = :movimientos_socios WHERE branchcode = :branchcode AND debtorno = :debtorno ";
            $parameters = array(
                ':branchcode' => $_POST['Reactivar']['RBranchCode'],
                ':debtorno' => $_POST['Reactivar']['RDebtorNo'],
                ':movimientos_socios' => 'Activo',
                ':rh_status_captura' => 'Activo'
            );
            if (Yii::app()->db->createCommand($SQLSuspender)->execute($parameters)) {

                $this->ActualizaCosto($_POST['Reactivar']['RFolio']);
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Reactivar']['RBranchCode'] . " ha sido Reactivado...",
                    'DebtorNo' => $_POST['Reactivar']['RDebtorNo'],
                    'BranchCode' => $_POST['Reactivar']['RBranchCode'],
                    'Tarifa_Total' => $_POST['Reactivar']['Rtarifa_total'],
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Nose pudo Reactivar el Socio, intente de nuevo...",
                ));
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese Folio...",
            ));
        }
        return;
    }

    /**
     * Activa/Reactiva Socios
     *
     * @return void
     * @author erasto@realhost.com.mx
     */
    public function actionActivarsocio() {
        global $db;
        if (!empty($_POST['Activar']['BranchCode']) && !empty($_POST['Activar']['DebtorNo'])) {
            FB::INFO($_POST, '____________________________POST RS');
            //Actualiza Custbranch a Activo
            $SQLSuspender = "UPDATE custbranch SET movimientos_socios = :movimientos_socios, rh_status_captura = :rh_status_captura WHERE branchcode = :branchcode AND debtorno = :debtorno ";
            $parameters = array(
                ':branchcode' => $_POST['Activar']['BranchCode'],
                ':debtorno' => $_POST['Activar']['DebtorNo'],
                ':rh_status_captura' => 'Facturar',
                ':movimientos_socios' => 'Activo'
            );
            if (Yii::app()->db->createCommand($SQLSuspender)->execute($parameters)) {
                $COSTO = $this->ActualizaCosto($_POST['Activar']['Folio'], 'NuevosSocios', 1);

                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->ReactivarSocios($_POST);
                }

                FB::INFO($COSTO, '______________________COSTO');

                $GetData = array();
                $GetData = Yii::app()->db->createCommand()->select(' * ')->from('custbranch')->where('branchcode = "' . $_POST['Activar']['BranchCode'] . '" AND debtorno = "' . $_POST['Activar']['DebtorNo'] . '" ')->queryAll();
                $NewRow = "";
                $NewRow .= "
              <td>{$GetData[0]['branchcode']}</td>
              <td>{$GetData[0]['brname']}</td>
              <td>{$GetData[0]['sexo']}</td>
              <td>{$GetData[0]['nombre_empresa']}</td>
              <td>{$GetData[0]['fecha_nacimiento']}</td>
              <td>{$GetData[0]['braddress1']}</td>
              <td>{$GetData[0]['braddress2']}</td>
              <td>{$GetData[0]['phoneno']}</td>
              <td>{$GetData[0]['fecha_ingreso']}</td>
              <td>{$GetData[0]['fecha_baja']}</td>
              <td>{$GetData[0]['fecha_ingreso']}</td>
              <td>
                  <select id='Change_Status' name='Change_Status' BranchCode='{$GetData[0]['branchcode']}' DebtorNo='{$GetData[0]['debtorno']}' onchange='ChangeStatus(this.value,{$GetData[0]['debtorno']},{$GetData[0]['branchcode']},{$GetData[0]['folio']})' >";
                $NewRow .= "
                      <option SELECTED='SELECTED' value='{$GetData[0]['movimientos_socios']}'>{$GetData[0]['movimientos_socios']}</option>
                      <option value='Activo'>Activo</option>
                      <option value='Cancelado'>Cancelado</option>
                      <option value='Suspendido'>Suspendido</option>
                  </select>
              </td>
              <td>
                 <a id='ViewSocio'   name = 'ViewSocio'   BranchCode='{$GetData[0]['branchcode']}' DebtorNo='{$GetData[0]['debtorno']}' title='Detalles' onclick='ViewSocio({$GetData[0]['debtorno']},{$GetData[0]['branchcode']},{$GetData[0]['folio']})' ><i class='icon-eye-open'></i></a>&nbsp;
                   <a id='EditSocio'   name = 'EditSocio'   BranchCode='{$GetData[0]['branchcode']}' DebtorNo='{$GetData[0]['debtorno']}' title='Editar Socio' onclick='EditSocio({$GetData[0]['debtorno']},{$GetData[0]['branchcode']},{$GetData[0]['folio']})' ><i class='icon-edit'></i></a>&nbsp;
                </td>";

                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "El Socio " . $_POST['Activar']['BranchCode'] . " ha sido Activado...",
                    'DebtorNo' => $_POST['Activar']['DebtorNo'],
                    'BranchCode' => $_POST['Activar']['BranchCode'],
                    'message2' => "Costo total: " . number_format($COSTO['CostoTotal'], 2) . " N° Socios Activos: " . $COSTO['QtyNS'],
                    'NewRow' => $NewRow,
                    'btnSaveNews' => "<input type='button' id='SaveNews' value='Guardar y Generar Factura' class='btn-success' onclick='ConfirmSaveNSocios({$GetData[0]['debtorno']},{$GetData[0]['folio']})' style='margin-top: -35px;' >",
                    'CostoTotal' => $COSTO['CostoTotal'],
                    'CostoTodosSocios' => $COSTO['CostoTodosSocios'],
                    'TipoFactura' => $_POST['Activar']['Tipo']
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "Este socio ya se encuentra activo.",
                ));
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese Folio...",
            ));
        }
        return;
    }

    public function actionEliminarsocio() {
        global $db;
        if (!empty($_POST['Activar']['BranchCode']) && !empty($_POST['Activar']['DebtorNo'])) {
            FB::INFO($_POST, '____________________________POST RS');
            //Elimina Custbranch

            $sql = "SELECT COUNT(*) FROM debtortrans WHERE debtortrans.branchcode='{$_POST['Activar']['BranchCode']})' AND debtorno = '{$_POST['Activar']['DebtorNo']}'";
            $result = DB_query($sql, $db);
            $myrow = DB_fetch_row($result);
            if ($myrow[0]>0) {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => _("Cannot delete this branch because customer transactions have been created to this branch There are {$myrow[0]} transactions with this Branch Code")
                ));
                return;
            } else {
                $sql = "SELECT COUNT(*) FROM salesanalysis WHERE salesanalysis.custbranch='{$_POST['Activar']['BranchCode']}' AND salesanalysis.cust = '{$_POST['Activar']['DebtorNo']}'";
                $result = DB_query($sql, $db);
                $myrow = DB_fetch_row($result);
                if ($myrow[0]>0) {
                    echo CJSON::encode(array(
                        'requestresult' => 'fail',
                        'message' => _("Cannot delete this branch because sales analysis records exist for it. There are {$myrow[0]} sales analysis records with this Branch Code/customer")
                    ));
                    return;
                } else {
                    $sql = "SELECT COUNT(*) FROM salesorders WHERE salesorders.branchcode='{$_POST['Activar']['BranchCode']}' AND salesorders.debtorno = '{$_POST['Activar']['DebtorNo']}'";
                    $result = DB_query($sql, $db);
                    $myrow = DB_fetch_row($result);
                    if ($myrow[0]>0) {

                        echo CJSON::encode(array(
                            'requestresult' => 'fail',
                            'message' => _("Cannot delete this branch because sales orders exist for it. Purge old sales orders first. There are {$myrow[0]} sales orders for this Branch/customer")
                        ));
                        return;
                    } else {
                        // Check if there are any users that refer to this branch code
                        $sql = "SELECT COUNT(*) FROM www_users WHERE www_users.branchcode='{$_POST['Activar']['BranchCode']}' AND www_users.customerid = '{$_POST['Activar']['DebtorNo']}'";
                        $result = DB_query($sql, $db);
                        $myrow = DB_fetch_row($result);
                        if ($myrow[0]>0) {

                            echo CJSON::encode(array(
                                'requestresult' => 'fail',
                                'message' => _("Cannot delete this branch because users exist that refer to it. Purge old users first. There are {$myrow[0]}  users referring to this Branch/customer")
                            ));
                            return;

                        } else {
                            DB_query("SET FOREIGN_KEY_CHECKS=0", $db);
                            $sql = "DELETE FROM custbranch WHERE branchcode='" . $_POST['Activar']['BranchCode'] . "' AND debtorno='" . $_POST['Activar']['DebtorNo'] . "'";
                            $ErrMsg = _('The branch record could not be deleted') . ' - ' . _('the SQL server returned the following message');
                            $result = DB_query($sql, $db, $ErrMsg);

                            if (DB_error_no($db)==0) {
                                DB_query("SET FOREIGN_KEY_CHECKS=1", $db);
                                $COSTO = $this->ActualizaCosto($_POST['Activar']['Folio'], 'NuevosSocios', 1);
                                FB::INFO($COSTO, '______________________COSTO');
                                echo CJSON::encode(array(
                                    'requestresult' => 'ok',
                                    'message' => "El Socio " . $_POST['Activar']['BranchCode'] . " ha sido Eliminado...",
                                    'DebtorNo' => $_POST['Activar']['DebtorNo'],
                                    'BranchCode' => $_POST['Activar']['BranchCode'],
                                    'message2' => "Costo total: " . number_format($COSTO['CostoTotal'], 2) . " N° Socios Activos: " . $COSTO['QtyNS'],
                                    'btnSaveNews' => "<input type='button' id='SaveNews' value='Guardar y Generar Factura' class='btn-success' onclick='ConfirmSaveNSocios({$GetData[0]['debtorno']},{$GetData[0]['folio']})' style='margin-top: -35px;' >",
                                    'CostoTotal' => $COSTO['CostoTotal'],
                                ));
                                return;
                            }
                        }
                    }
                }//end ifs to test if the branch can be deleted
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "Ingrese Folio...",
            ));
        }
        return;
    }

    public function actionTestfactura() {

        //FB::INFO(Yii::app()->controller->id,'______________________ID CONT');

        set_time_limit(0);
        $DebtorNo = 30;
        //$Branch = 21;
        $SalesMan = '2RC';
        $DebtorData = Yii::app()->db->createCommand()
        ->select(' debtorsmaster.*, stkm.description,cobranza.folio,pm.paymentname,cobranza.stockid,cobranza.fecha_corte,fp.frecuencia ')
        ->from('debtorsmaster')
        ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
        ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
        ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
        ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
        ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

        $SO_Details = array(0 => array(
                'orderlineno' => 0,
                'stkcode' => 'FAMILIA',
                'unitprice' => 10,
                'quantity' => 1,
                'discountpercent' => 0,
                'narrative' => $this->GetMonth(date('Y-m-d')) . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'],
                'description' => 'Description',
                'poline' => '',
                'rh_cost' => '0.0000',
                'itemdue' => date("Y-m-d H:i")
            ));
        FB::INFO($SO_Details, '__________DETAIL');

        switch ($_GET['ProcessNo']) {
            case '1' :
                $inicio = 3779;
                $Fin = 3779 + 250;
                break;
            case '2' :
                $inicio = 4029;
                $Fin = 4029 + 250;
                break;
            case '3' :
                $inicio = 4279;
                $Fin = 4279 + 250;
                break;
            case '4' :
                $inicio = 4529;
                $Fin = 4529 + 250;
                break;
            case '5' :
                $inicio = 4779;
                $Fin = 4779 + 250;
                break;
            case '6' :
                $inicio = 4533;
                $Fin = 4533 + 150;
                break;
            case '7' :
                $inicio = 4684;
                $Fin = 4684 + 150;
                break;
            case '8' :
                $inicio = 4835;
                $Fin = 4835 + 150;
                break;
            case '9' :
                $inicio = 4986;
                $Fin = 4986 + 150;
                break;
            case '10' :
                $inicio = 5037;
                $Fin = 5037 + 150;
                break;
            default :
                break;
        }

        for ($i = $inicio; $i<$Fin; $i++) {

            //$_2OrderNo = $this->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
            //FB::INFO($_2OrderNo, '__________________$OrderNo');

            //$DebtorTransID = $this->FacturaPedido($_2OrderNo, $DebtorData[0]['folio']);
            //FB::INFO($DebtorTransID, '__________________$DebtorTransID');
            $TimbrarFactura = $this->Timbrar($i, $DebtorData[0]['folio']);

        }

        $this->render('test');
    }

    /**
    * @Todo Crea Pedido/Cotizacion sin Facturar,
    * Guarda Registro en la Bitacora,
    * Actualiza el Costo Nuevos Socios,
    * Actualiza Costo Total
    * @Param array(
    *['GetInvoice'] =>
        *array(
            *['BranchCode'] =>25
            *['DebtorNo'] =>21
            *['Folio'] =>12382
            *['TotalInvoice'] =>410.0000,
            *['TotalAllSocios'] =>410.0000,
            *['Tipo'] =>ReactivacionFolio
        *)
    *)
    * @return void
    * @author erasto@realhost.com.mx
    */
    public function actionCreafacturanuevossocios() {
        FB::INFO($_POST, '______________________________POST');

        if (!empty($_POST['GetInvoice'])) {

                $DebtorNo = $_POST['GetInvoice']['DebtorNo'];
                //$Branch = 21;
                $SalesMan = '2RC';
                $DebtorData = Yii::app()->db->createCommand()
                ->select(' debtorsmaster.*,
                    stkm.description,
                    cobranza.folio,
                    cobranza.stockid,
                    cobranza.fecha_corte,
                    pm.paymentname,
                    cobranza.frecuencia_pago,
                    cobranza.paymentid,
                    fp.frecuencia,
                    cobranza.cobro_inscripcion ')
                ->from('debtorsmaster')
                ->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')
                ->leftJoin('rh_frecuenciapago fp', 'fp.id = cobranza.frecuencia_pago')
                ->leftJoin('stockmaster stkm', 'cobranza.stockid = stkm.stockid')
                ->leftJoin('paymentmethods pm', 'pm.paymentid = cobranza.paymentid')
                ->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();

            /*
            array(
                ['CostoTodosSocios'] =>680.0000
                ['MontoRecibido'] =>0
                ['NuevosSocios'] =>
                ['CostoTotal'] =>0
                ['QtyNS'] =>0
                ['CostoInscripcion'] =>
                ['FacturaProporcional'] =>
                ['DiasGracia'] =>
            )
            */

            /*Actualizo Costo Total y Nuevos Socios*/
            $__GetCost = $this->ActualizaCosto($_POST['GetInvoice']['Folio'], 'NuevosSocios');
            $SQLUpdateCostoNuevosSocios = "UPDATE rh_titular SET costo_total = :costo_total, costos_nuevos_socios = :costos_nuevos_socios WHERE debtorno = :debtorno ";
            $parameters = array(
                ':costo_total' => $__GetCost['CostoTodosSocios'],
                ':costos_nuevos_socios' => $__GetCost['CostoTotal'],
                ':debtorno' => $_POST['GetInvoice']['DebtorNo']
            );
            Yii::app()->db->createCommand($SQLUpdateCostoNuevosSocios)->execute($parameters);


            /**
            * Si son dias de Gracia Solo Genero la Factura para la Proxima Emision.
            * No se Genera Factura para el mes actual NI Registro en la Bitacora
            */
            $FacturaCFDI = new Facturacion();
            if($__GetCost['DiasGracia'] == 1){

                //$FechasProximasFacturas = $this->FechaProximaFactura(date("Y-m-d"),$DebtorData[0]['paymentid'], $DebtorData[0]['frecuencia_pago']);
                $SO_Details = array(0 => array(
                        'orderlineno' => 0,
                        'stkcode' => $DebtorData[0]['stockid'],
                        'unitprice' => $__GetCost['CostoTodosSocios'],
                        'quantity' => 1,
                        'discountpercent' => 0,
                        'narrative' => "Factura Afiliacion",
                        'description' => "SERVICIO MEDICO-" . $this->GetMonth($FechasProximasFacturas['Proxima'], $DebtorData[0]['frecuencia_pago']) . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'],
                        'poline' => '',
                        'rh_cost' => '0.0000',
                        'itemdue' => date("Y-m-d H:i:s")
                    ));
                FB::INFO($SO_Details, '__________DETAIL');
                //$FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);

                //Loguear Fecha Proxima Factura
                //$CreaBitacoraAgregarSocio = $this->CreaBitacoraAgregarSocio($DebtorData[0], 0, 0, $FacturaCFDI->OrderNo, "Factura Programada",$FechasProximasFacturas['Proxima']);
                $MSG = "No se ha Generado Factura por ser Dias de Gracia para el Afiliado...";
            }else{

                $SO_Details = array(0 => array(
                        'orderlineno' => 0,
                        'stkcode' => $DebtorData[0]['stockid'],
                        'unitprice' => $_POST['GetInvoice']['TotalInvoice'],
                        'quantity' => 1,
                        'discountpercent' => 0,
                        'narrative' => "Factura Afiliacion",
                        'description' => "SERVICIO MEDICO-" . $this->GetMonth(date('Y-m-d'), $DebtorData[0]['frecuencia_pago']) . " - " . $DebtorData[0]['description'] . " - " . $DebtorData[0]['frecuencia'] . " - " . $DebtorData[0]['paymentname'],
                        'poline' => '',
                        'rh_cost' => '0.0000',
                        'itemdue' => date("Y-m-d H:i:s")
                    ));
                FB::INFO($SO_Details, '__________DETAIL');
                $FacturaCFDI->CreaPedido($DebtorData, $SO_Details, $DebtorNo);
                FB::INFO($FacturaCFDI->OrderNo, '__________________RET ORDER NO');

                /*Guarda el Registro en la Bitacora*/
                if (empty($_POST['GetInvoice']['Tipo'])) {
                    $_POST['GetInvoice']['Tipo'] = "NuevosSocios";
                }

                if ($DebtorData[0]['cobro_inscripcion'] == 0) {
                    //$_POST['GetInvoice']['Tipo'] = "Factura Programada";
                    //FB::INFO($_POST['GetInvoice']['Tipo'], '________________________________TIPO');
                }

                //Loguear Fecha Proxima Factura
                $CreaBitacoraAgregarSocio = $this->CreaBitacoraAgregarSocio($DebtorData[0], 0, 0, $FacturaCFDI->OrderNo, $_POST['GetInvoice']['Tipo']);
                $MSG = "La Factura para el folio: " . $_POST['GetInvoice']['BranchCode'] . " se ha generado correctamente...";

            }

            //if ($CreaBitacoraAgregarSocio)
            {
                /*Actualizo Status de Socios*/
                $SQLActivar = "UPDATE custbranch SET
                    movimientos_socios = :movimientos_socios,
                    rh_status_captura = :rh_status_captura
                    WHERE rh_status_captura = 'Facturar' AND debtorno = :debtorno ";
                $parameters = array(
                    ':movimientos_socios' => 'Activo',
                    ':rh_status_captura' => 'Activo',
                    ':debtorno' => $_POST['GetInvoice']['DebtorNo']
                );
                Yii::app()->db->createCommand($SQLActivar)->execute($parameters);

                /* UpdateFlag */
                $SQLUpdateFlag = "UPDATE rh_cobranza SET cobro_inscripcion = :cobro_inscripcion WHERE debtorno = :debtorno ";
                $parameters = array(
                    ':cobro_inscripcion' => 0,
                    ':debtorno' => $_POST['GetInvoice']['DebtorNo']
                );
                Yii::app()->db->createCommand($SQLUpdateFlag)->execute($parameters);
            }

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => $MSG,
                'DebtorNo' => $_POST['GetInvoice']['DebtorNo'],
                'BranchCode' => $_POST['GetInvoice']['BranchCode'],
                'Tarifa_Total' => $_POST['GetInvoice']['Tarifa_Total'],
                'transno' => $GetTransNo[0]['transno']
            ));
            return;
        }
    }

    /**
     * Agregar Socios
     * Crea Registro Tipo NuevosSocios
     * @Param $Data = array(folio=>123,debtorno=>123)
     * @return void
     * @author erasto@realhost.com.mx
     */
    public function CreaBitacoraAgregarSocio($Data, $debtortrans_id = null, $TransNo = null, $OrderNo = null, $Tipo = null, $Fecha_Corte = null) {
        if (!empty($Data)) {

            if ($Tipo=="Factura Programada") {
                $Status = "Programada";
            } else {
                $Status = "PendienteFacturar";
            }

            if(empty($Fecha_Corte)){
                $Fecha_Corte = date("Y-m-d");
            }

            $SQLInsert = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype,debtortrans_id,transno,created,orderno) values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype,:debtortrans_id,:transno,:created,:orderno)";
            $parameters = array(
                ':folio' => $Data['folio'],
                ':debtorno' => $Data['debtorno'],
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => $Fecha_Corte,
                ':status' => $Status,
                ':tipo' => $Tipo,
                ':systype' => 10,
                ':debtortrans_id' => $debtortrans_id,
                ':transno' => $TransNo,
                ':created' => date('Y-m-d'),
                ':orderno' => $OrderNo,
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                FB::INFO($Data, '_______________________OK LOGUEADO EN BITACORA');
                return true;
            }
        }
    }

    public function actionFacturareactivacion() {

        if (!empty($_POST['GetInvoice'])) {
            $DebtorNo = $_POST['GetInvoice']['DebtorNo'];
            //$Branch = 21;
            $SalesMan = '2RC';
            $DebtorData = Yii::app()->db->createCommand()->select(' debtorsmaster.*,cobranza.folio,cobranza.stockid,cobranza.fecha_corte ')->from('debtorsmaster')->leftJoin('rh_cobranza cobranza', 'cobranza.debtorno = debtorsmaster.debtorno')->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')->queryAll();
            $SO_Details = array(0 => array(
                    'orderlineno' => 0,
                    'stkcode' => $DebtorData[0]['stockid'],
                    'unitprice' => $_POST['GetInvoice']['Tarifa_Total'],
                    'quantity' => 1,
                    'discountpercent' => 0,
                    'narrative' => 'Reactivacion de Socio',
                    'description' => 'Reactivacion de Socio',
                    'poline' => '',
                    'rh_cost' => '0.0000',
                    'itemdue' => date("Y-m-d H:i")
                ));
            $ReturnInvoince = $this->Facturar($DebtorData, $SO_Details, $DebtorNo);
            $TimbrarFactura = $this->Timbrar($ReturnInvoince);
            $GetTransNo = Yii::app()->db->createCommand()->select(' transno ')->from('debtortrans')->where('debtortrans.id = "' . $ReturnInvoince . '" AND debtortrans.type = 10')->queryAll();

            /*Guarda el Registro en la Bitacora*/
            $this->CreaBitacoraReactivacionSocio($DebtorData[0]);

            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'message' => "La Factura para la reactivacion del folio: " . $_POST['Reactivar']['RBranchCode'] . " se ha generado correctamente...",
                'DebtorNo' => $_POST['GetInvoice']['DebtorNo'],
                'BranchCode' => $_POST['GetInvoice']['BranchCode'],
                'Tarifa_Total' => $_POST['GetInvoice']['Tarifa_Total'],
                'transno' => $GetTransNo[0]['transno']
            ));
            return;
        }
        exit ;
    }

    /** NO SE USA
     * Alta de Alta Afiliado
     * Crea Registro Tipo Afiliacion
     * @Param $Data = array(folio=>123,debtorno=>123,fecha_corte=>'2014-04-22')
     * @return void
     * @author
     */
    public function CreaBitacoraAfiliacion($Data) {
        FB::INFO($Data, '_______________________DATA');
        if (!empty($_Data)) {

            $SQLDelete = "delete from rh_facturacion where folio = :folio and status = :status and systype = :systype";
            $parameters = array(
                ':folio' => $Data['folio'],
                ':status' => 'Pendiente',
                ':systype' => '10'
            );
            Yii::app()->db->createCommand($SQLDelete)->execute($parameters);

            unset($parameters);
            $SQLInsert = "insert into rh_facturacion (folio,debtorno,userid,fecha_corte,status,tipo,systype) values (:folio,:debtorno,:userid,:fecha_corte,:status,:tipo,:systype)";
            $parameters = array(
                ':folio' => $Data['folio'],
                ':debtorno' => $Data['debtorno'],
                ':userid' => $_SESSION['UserID'],
                ':fecha_corte' => $Data['fecha_corte'],
                ':status' => 'Pendiente',
                ':tipo' => 'Afiliacion',
                ':systype' => '10'
            );
            if (Yii::app()->db->createCommand($SQLInsert)->execute($parameters)) {
                FB::INFO($Data, '_______________________OK Saved');
                return true;
            }
        }
    }

    /**
     * @Todo
     * Envia Cartas por Email
    */
    public function actionSendmail($Folio = null, $Tipo = null, $_TransNo = null){
        global $db, $AddCC, $BCC;
        FB::INFO($Folio,'____________________-FOLIO');
        //FB::INFO($_POST,'::::::::::::____POST');
        if (!empty($_POST['SendMail']['Folio']) && $_POST['SendMail']['Tipo']) {
            $Folio = $_POST['SendMail']['Folio'];
            $Tipo = $_POST['SendMail']['Tipo'];
        }
        FB::INFO($Folio,'_________________SENDTO');

        if (!empty($Folio)) {

            $GetEmail = Yii::app()->db->createCommand()->select(' email ')->from('rh_cobranza')->where('folio = "' . $Folio . '"')->queryAll();
            $EmailTo = explode(",", str_replace(array(';',' '), ',', $GetEmail[0]['email']));

            $Para = "";
            $Para= str_replace(",,",",",implode(",",array_map(function($arreglo){
                                                                return trim($arreglo);
                                                            }, $EmailTo)));


            $EmailTo[0] = $Para;
            //$EmailTo[0] = "erasto@realhost.com.mx";

			if(!isset($BCC))
				$BCC ="";
            FB::INFO($EmailTo,'_______________________EMAIL');

            switch ($Tipo) {
                case 'CartaBienvenida':

                    $from = 'AR MEDICA';
                    $To = $EmailTo[0];
                    $Subject = 'BIENVENIDO No. FOLIO : ' . $Folio;
                    $Mensaje = 'CARTA DE BIENVENIDA';
                    $PDF = $this->actionBienvenidapdf('',$Ret = 'S');
                    $PDF2 = $this->actionSociodistinguido('',$Ret = 'S');

                    $attachment[] = array('nombre'=>'CartaBienvenida.pdf','archivo'=>$PDF);
                    $attachment[] = array('nombre'=>'SocioDistinguido.pdf','archivo'=>$PDF2);

                    $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '', $AddCC);
                    if ($Response == "success") {
                        echo CJSON::encode(array(
                            'requestresult' => 'ok',
                            'message' => "Se ha enviado un Email ala direccion: ". $To
                        ));
                    }
                    break;
                case 'RecordatorioPago':

                    $from = 'AR MEDICA';
                    $To = $EmailTo[0];
                    $Subject = 'RECORDATORIO DE PAGO No. FOLIO : ' . $Folio;
                    $Mensaje = 'RECORDATORIO DE PAGO';
                    $PDF = $this->actionRecordatoriopagopdf('',$Ret = 'S', $Folio);
                    $attachment =array( array('nombre'=>'RecordatorioPago.pdf','archivo'=>$PDF));

                    $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '', $AddCC);
                    if ($Response == "success") {
                        // echo CJSON::encode(array(
                        //     'requestresult' => 'ok',
                        //     'message' => "Se ha enviado un Email ala direccion: ". $To
                        // ));
                    }
                    break;
                case 'DunningLetter':

                    $from = 'AR MEDICA';
                    $To = $EmailTo[0];
                    $Subject = 'RECORDATORIO DE PAGO No. FOLIO : ' . $Folio;
                    $Mensaje = 'RECORDATORIO DE PAGO';
                    $PDF = $this->actionGeneraDunninletter('',$Ret = 'S', $Folio);
                    $attachment =array( array('nombre'=>'DunningLetter.pdf','archivo'=>$PDF));

                    $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '', $AddCC);
                    if ($Response == "success") {
                        // echo CJSON::encode(array(
                        //     'requestresult' => 'ok',
                        //     'message' => "Se ha enviado un Email ala direccion: ". $To
                        // ));
                    }
                    break;
                case 'InvoiceANDXML':

                    /*Get XML*/
                    $CFDIData = Yii::app()->db->createCommand()->select(' c.uuid, c.serie, c.folio, c.fecha, c.xml ')->from('rh_cfd__cfd c')->where('c.fk_transno = :fk_transno', array(':fk_transno' => $_TransNo) )->queryAll();

                    $from = 'AR MEDICA';
                    $To = $EmailTo[0];
                    $Subject = 'CFDI: ' . $Folio . ' ' . $CFDIData[0]['serie'].$CFDIData[0]['folio'] . ' ' . $CFDIData[0]['fecha'];
                    $Mensaje = 'CFDI';
                    //$CFDIData = DB_fetch_assoc(DB_query("select c.uuid, c.serie, c.folio, c.xml from rh_cfd__cfd c where c.fk_transno = '{$_TransNo}' ", $db, _("Error retrieving invoice data")));
                    $cfdName = $CFDIData[0]['uuid'];
                    $xmlFile = $CFDIData[0]['xml'];
                    /*Get PDF*/
                    $PDF = $this->actionGenerapdfinvoice('',$Ret = 'S', $Folio, $_TransNo);

                    $attachment[] = array('nombre'=>"{$cfdName}.pdf",'archivo'=>$PDF);
                    if(!empty($xmlFile)){
                        $attachment[] = array('nombre'=>"{$cfdName}.xml",'archivo'=>$xmlFile);
                    }

                   

                    /*
                    Verificamos que el correo en copias correo no este vacio
                    */
                    if($_SESSION['CompanyRecord']['or_copia_enviofactura']!='')
                    {
                        $To.= ', '.$_SESSION['CompanyRecord']['or_copia_enviofactura'];
                    }

                    /* TERMINA - POR DANIEL VILLARREAL EL 18 DE MAYO DEL 2016 */
                    // Insertamos en el log de correos enviados
                    $sql = "insert into rh_facturas_enviadas (id_factura, fecha,usuario,nombre_usuario,correos) values (:id_factura, :fecha,:usuario,:nombre_usuario,:correos)";
                    $parameters = array(
                        ":id_factura"=>$_TransNo,
                        ':fecha' => date('Y-m-d H:i:s'),
                        ':usuario'=>'',
                        ':nombre_usuario'=>$_SESSION['UserID'],
                        ':correos'=>$To,
                    );
                    Yii::app()->db->createCommand($sql)->execute($parameters);
                    // Termina - Daniel Villarreal el 19 de Mayo 2016
                    /*MODIFICAMOS EL ASUNTO DEL CORREO POR DANIEL VILLARREAL EL 10 DE JUNIO DEL 2016*/
                    $SQL = "SELECT folio,brname
                            FROM custbranch INNER JOIN debtortrans
                                ON custbranch.debtorno= debtortrans.debtorno
                                AND custbranch.branchcode=debtortrans.branchcode
                        WHERE debtortrans.type=10
                        AND debtortrans.transno=" .$_TransNo;

                    $ErrMsg = _('There was a problem retrieving the contact details for the customer');
                    $ContactResult=DB_query($SQL,$db,$ErrMsg);
                    $ContactResult= DB_fetch_array($ContactResult);
                    
                    $Subject = 'Folio:'.$ContactResult['folio'].' | Titular:'.$ContactResult['brname'].' | No. Factura: '.$CFDIData[0]['serie'].$CFDIData[0]['folio'];
                    /* TERMINA */

                    $Response = $this->EnviarMail($from, $To, $Subject, $Mensaje, $attachment , $BCC, $repplyTo = '', $AddCC);
                    if ($Response == "success") {
                        // echo CJSON::encode(array(
                        //     'requestresult' => 'ok',
                        //     'message' => "Se ha enviado un Email ala direccion: ". $To
                        // ));
                    }
                    break;
                default:
                    # code...
                    break;
            }
            return;
        }
    }


    public function actionSocios() {

        global $db;
        if (isset($_POST['Folio']) || isset($_GET['Folio'])) {
            $Folio = $_REQUEST['Folio'];
        } else {
            $Folio = "";
        }
        if (!empty($_POST['Asesor_ID'])) {
            $Asesor = $_POST['Asesor_ID'];
        } else {
            $Asesor = "";
        }

        if ((isset($_POST['Search']) || isset($_GET['Folio'])) && !empty($Folio)) {
            $Verify = Yii::app()->db->createCommand()->select(' folio, comisionista_id ')->from('rh_foliosasignados')->where('folio = "' . $Folio . '"')->queryAll();
            if (!empty($Verify)) {
                $Asesor = $Verify[0]['comisionista_id'];
                $RSearch = Yii::app()->db->createCommand()
                    ->select(' rh_cobranza.*,titular.costo_total,titular.movimientos_afiliacion, titular.email ')
                    ->from('rh_cobranza')
                    ->leftJoin('rh_titular titular', 'titular.folio = rh_cobranza.folio')
                    ->where('rh_cobranza.folio = "' . $Folio . '"')->queryAll();
                if (!empty($RSearch)) {
                    Yii::app()->user->setFlash("success", "Ingresar Socios para el Afiliado  " . $Folio);
                    $RSearch[0]['Folio'] = $RSearch[0]['folio'];
                    //$RSearch[0]['email'] = $RSearch[0]['email'];
                    $_POST = $RSearch[0];
                    //$CartaBienvenida = $_POST['cobro_inscripcion'];
                } else {
                    $RSearchTitular = Yii::app()->db->createCommand()->select(' * ')->from('rh_titular')->where('folio = "' . $Folio . '"')->queryAll();
                    if (($RSearchTitular[0])) {
                        $RSearchTitular[0]['Folio'] = $RSearchTitular[0]['folio'];
                        $_POST = $RSearchTitular[0];
                    }
                    Yii::app()->user->setFlash("error", "No se ha Ingresado informacion de Cobranza . ");
                    $this->redirect($this->createUrl("afiliaciones/cobranza", array("Folio" =>  $_POST['Folio'])));
                }
            } else {
                Yii::app()->user->setFlash("error", "El Folio ingresado no existe ó no esta asignado a un Asesor.");
                $this->redirect($this->createUrl("afiliaciones/afiliacion"));
            }
        }

        if (!empty($_POST['SaveData']) && !empty($_POST['Folio'])) {
            $VerifyFolio = Yii::app()->db->createCommand()->
            select(' * ')
            ->from('rh_titular')
            ->where('folio = "' . $_POST['Folio'] . '"')->queryAll();
            FB::INFO($VerifyFolio, '__________________________________VERY');
            if (!empty($VerifyFolio[0]['debtorno'])) {
                $BranchCode = $this->GetNextBranchCode($VerifyFolio[0]['debtorno']);
                $Debtorno = $VerifyFolio[0]['debtorno'];
                $Area = "MTY";
                $Salesman = "2RC";
                $DefaultLocation = "MTY";
                $DefaultShipVia = "1";
                $TaxGroupid = "4";
                $_POST['debtorno'] = $VerifyFolio[0]['debtorno'];
                $_POST['Folio'] = $VerifyFolio[0]['folio'];
            } else {
                Yii::app()->user->setFlash("error", "No Existe un Afiliado con ese N° de Folio.");
                $this->redirect($this->createUrl('afiliaciones/socios/'));
            }
            $_POST['antecedentes_clinicos'] = json_encode($_POST['antecedentes_clinicos'], true);// Se agrego otros_padecimientos Angeles Perez 2016-08-12
            $sql = "insert into custbranch ( branchcode,
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
                                            braddress11,
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
                                            otros_padecimientos,
                                            movimientos_socios,
                                            debtorno,
                                            area,
                                            salesman,
                                            defaultlocation,
                                            defaultshipvia,
                                            taxgroupid,
                                            rh_status_captura
                                            )
                                            values ('" . $BranchCode . "',
                                            '" . $_POST['Folio'] . "',
                                            '" . $_POST['brname'] . "',
                                            '" . $_POST['braddress1'] . "',
                                            '" . $_POST['braddress2'] . "',
                                            '" . $_POST['braddress3'] . "',
                                            '" . $_POST['braddress4'] . "',
                                            '" . $_POST['braddress5'] . "',
                                            '" . $_POST['braddress6'] . "',
                                            '" . $_POST['braddress7'] . "',
                                            '" . $_POST['braddress8'] . "',
                                            '" . $_POST['braddress10'] . "',
                                            '" . $_POST['braddress11'] . "',
                                            '" . $_POST['cuadrante1'] . "',
                                            '" . $_POST['cuadrante2'] . "',
                                            '" . $_POST['cuadrante3'] . "',
                                            '" . $_POST['sexo'] . "',
                                            '" . $_POST['nombre_empresa'] . "',
                                            '" . $_POST['fecha_nacimiento'] . "',
                                            '" . $_POST['fecha_ingreso'] . "',
                                            '" . $_POST['fecha_ultaum'] . "',
                                            '" . $_POST['phoneno'] . "',
                                            '" . DB_escape_string($_POST['antecedentes_clinicos']) . "',
                                            '" . $_POST['otros_padecimientos'] . "',
                                            'Nuevo',
                                            '" . $Debtorno . "',
                                            '" . $Area . "',
                                            '" . $Salesman . "',
                                            '" . $DefaultLocation . "',
                                            '" . $DefaultShipVia . "',
                                            '" . $TaxGroupid . "',
                                            'Precapturado'
                                            )";
            FB::INFO($sql, '_______________________INSERT');
            if (DB_query($sql, $db)) {
                $_POST['movimientos_afiliacion'] = "Activo";
                $_POST['branchcode']=$BranchCode;
                //$COSTO = $this->ActualizaCosto($_POST['Folio']);
                //FB::INFO($COSTO, '_____________________COSTO');
                $Tipo = $this->GetTipoFolio($_POST['Folio']);
                if($Tipo == 'Socio' && $_SESSION['DatabaseName'] == 'sainar_erp_001'){
                    $SQLServerWS = new SQLServerWS();
                    $SQLServerWS->InsertSocios($_POST);
                }

                Yii::app()->user->setFlash("success", "Los datos del Socio se han Guardado correctamente.");
                // Se agrego para mostrar mensaje al usuario de que no a Activado el nuevo Socio 17-05-2016
                Yii::app()->user->setFlash("error", " NO OLVIDE ACTIVAR EL NUEVO SOCIO.");
                //Termina
            } else {
                Yii::app()->user->setFlash("error", "No se pudo guardar la Informacion del Socio, intente de nuevo.");
            }
        }

        $consultacancelpor = "SELECT * FROM rh_movimientos_afiliacion
                    WHERE debtorno = '114450'
                    and id in(SELECT max(id) from rh_movimientos_afiliacion
                    where debtorno = '114450')";
        $resultadoconsultacancelpor = DB_query($consultacancelpor, $db);
        $Cancelpor = DB_fetch_assoc($resultadoconsultacancelpor);
        //echo "<pre>";print_r($_cancelpor['userid']);exit();

        $GetSocios = array();
        if (!empty($_POST['Folio'])) {
            $GetSocios = Yii::app()->db->createCommand()->select(' * ')->from('custbranch')->where("folio = '" . $_POST['Folio'] . "' AND movimientos_socios != 'Titular' ")->queryAll();
            FB::INFO($GetSocios, ':__________________________________SOCIOs');
        }
        $LIstAntecedentesClinicos = array(
            'ALCOHOLISMO',
            'ALERGIA A ANALGESICO',
            'ALERGIA A ANTIBIOTICO',
            'ALERGIA RESPIRATORIA',
            'ANGINA DE PECHO',
            'ASMA',
            'CANCER',
            'CIRUGIAS PREVIAS',
            'CRISIS CONVULSIVA',
            'DIABETES',
            'EMBOLIA CEREBRAL',
            'ENF. INFECCIOSA',
            'ENF. PULMONARES',
            'FUMADOR',
            'HIPERTENCION ARTERIAL',
            'INFARTO PREVIO',
            'MEDICAMENTOS HAB.',
            'NINGUNO',
            'OTROS',
            'PROBLEMA AUDITIVO',
            'PROBLEMA OCULAR',
            'PROBLEMAS CARDIACOS',
            'PROBLEMAS CONGENITOS',
            'PROBLEMAS PSIQUIATRICOS',
            'PROBLEMAS RENALES',
            'SANGRADOS FRECUENTES',
            'SIDA'
        );
        $_2GetEmergenciaData = Yii::app()->db->createCommand()->select(' *')->from('rh_llamada_emergencia')->where('folio = "' . $_POST['Folio'] . '"')->queryAll();
        $GetEmergenciaData = $_2GetEmergenciaData[0];

        FB::INFO($GetEmergenciaData,'________________________EMERGENCIA');

        $_ListaEspecialidades = Yii::app()->db->createCommand()->select(' id, nombre ')->from('rh_especialidades')->where('status = "1" ')->queryAll();
        $ListaEspecialidades = array();
        foreach ($_ListaEspecialidades as $Especialidad) {
            $ListaEspecialidades[$Especialidad['id']] = $Especialidad['nombre'];
        }

        $_ListaParentescos = Yii::app()->db->createCommand()->select(' id, nombre ')->from('rh_parentescos')->where('status = "1" ')->queryAll();
        $ListaParentescos = array();
        foreach ($_ListaParentescos as $Parentesco) {
            $ListaParentescos[$Parentesco['id']] = $Parentesco['nombre'];
        }

        $ListaMunicipios = CHtml::listData(Municipio::model()->findAll(), 'id', 'municipio');
        $ListaEstados = CHtml::listData(Estado::model()->findAll(), 'id', 'estado');
        $ListaHospitales = CHtml::listData(Hospital::model()->findAll(), 'id', 'nombre');// Se agrego para mostrar la lista de hospitales Angeles Perez 2016-06-09
        $ListaMotivosCancelacion = array();
        $ListaMotivosCancelacion = CHtml::listData(MotivosCancelacion::model()->findAll(), 'id', 'motivo');
        $this->render('socios', array(
            'LIstAntecedentesClinicos' => $LIstAntecedentesClinicos,
            'ListaMunicipios' => $ListaMunicipios,
            'ListaEstados' => $ListaEstados,
            'ListaHospitales' => $ListaHospitales,
            'ListaMotivosCancelacion' => $ListaMotivosCancelacion,
            'Cancelpor' => $Cancelpor,
            'GetSocios' => $GetSocios,
            'GetEmergenciaData' => $GetEmergenciaData,
            'ListaEspecialidades' => $ListaEspecialidades,
            'ListaParentescos' => $ListaParentescos
        ));
    }

    /**
     * @Todo
     * Verifica que la Combinacion Producto,Frecuencia de Pago y Metodo de Pago Exista en
     *
     * */
    public function actionVerificaplan() {
        global $db;
        FB::INFO($_POST, '_________________________POST');
        if (!empty($_POST['Verifica'])) {

            $Where = "";
            $Where .= " MP.nafiliados = '1' AND MP.stockid = '" . $_POST['Verifica']['stockid'] . "' AND MP.frecpagoid = '" . $_POST['Verifica']['frecuencia_pago'] . "' AND MP.paymentid = '" . $_POST['Verifica']['paymentid'] . "'  ";

            if (!empty($_POST['Verifica']['empresa'])) {
                $Where .= " AND MP.empresa = '{$_POST['Verifica']['empresa']}' ";
            }

            $SQLGetPlan = "SELECT costouno from rh_matrizprecios as MP  WHERE " . $Where;
            $res_plandata = DB_query($SQLGetPlan, $db);
            $_GetPlanData = DB_fetch_assoc($res_plandata);
            FB::INFO($_GetPlanData,'____________________RES_'.$SQLGetPlan);

            if (empty($_GetPlanData)) {
                $Where2 = "";
                $Where2 .= " PC.dproducto = '" . $_POST['Verifica']['stockid'] . "' AND PC.dtipopago = '" . $_POST['Verifica']['frecuencia_pago'] . "' AND PC.dformap = '" . $_POST['Verifica']['paymentid'] . "'  ";
                if (!empty($_POST['Verifica']['empresa'])) {
                    $Where2 .= " AND PC.empresa = '{$_POST['Verifica']['empresa']}' ";
                }

                $GetComisData = "SELECT PC.tarifa from rh_preciocomisionista as PC
                WHERE
                " . $Where2 . "
                ";
                $_2GetComisData = DB_query($GetComisData, $db);
                $_GetPlanData = DB_fetch_assoc($_2GetComisData);
                FB::INFO($_GetPlanData,'____________________RES_'.$GetComisData);
            }

            if (!empty($_GetPlanData)) {
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Combianacion de Plan Correcta "
                ));
            }else{
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "La Combinacion Producto -> Forma de Pago -> Frecuencia de Pago NO Existe ó NO se ha Fijado una Tarifa... "
                ));
            }

            return;
        }

    }

    /**
     * Actualiza el Costo Total del Plan del Afiliado
     *
     * @return void
     * @author  erasto@realhost.com.mx
     * @Param   $Folio, Folio del Afiliado
     */
    public function ActualizaCosto($Folio, $Tipo = null, $Update = 0) {
        global $db;
        if (!empty($Folio)) {
            /* Obtengo el status si incluye inscripcion o no
             * Este campo debe actualizarse a cero cuando se genere la primer factura para que ala segunda ya no se incluya este costo
             * */
            $FlagInscription = "SELECT cobro_inscripcion from rh_cobranza where folio = '" . $Folio . "'";
            $flag_status = DB_query($FlagInscription, $db);
            $_flag_status = DB_fetch_assoc($flag_status);

            //* Obtengo la Cantidad de Socios */
            if ($Tipo=='NuevosSocios') {
                $GetQtySocios = "SELECT COUNT(*) as QTY from custbranch where folio = '" . $Folio . "' AND rh_status_captura = 'Facturar' ";
                $res_qty = DB_query($GetQtySocios, $db);
                $_GetQTY = DB_fetch_assoc($res_qty);
                FB::INFO($_GetQTY, '______________________FACTURAR NUEVOSS ' . $GetQtySocios);
            } else {
                $GetQtySocios = "SELECT COUNT(*) as QTY from custbranch where folio = '" . $Folio . "' AND movimientos_socios = 'Activo' AND rh_status_captura = 'Activo' ";
                $res_qty = DB_query($GetQtySocios, $db);
                $_GetQTY = DB_fetch_assoc($res_qty);
                FB::INFO($_GetQTY, '______________________TOTAL');
            }

            if (empty($_GetQTY['QTY'])) {
                $_GetQTY['QTY'] = 0;
            }

            //* Obtengo los Datos del Plan-> las combinaciones  Plan->Frecuencia de Pago->Metodo de Pago */
            $GetPlanData = "SELECT stockid,frecuencia_pago,paymentid,empresa from rh_cobranza where folio = '" . $Folio . "'";
            $res_plandata = DB_query($GetPlanData, $db);
            $_GetPlanData = DB_fetch_assoc($res_plandata);

            /*   Obtengo el Costo de Inscripcion del  Plan CostoInscripcion = costouno, CostoNuevosSocios = costodos   */
            $GetCostInscrip = "SELECT costouno from rh_matrizprecios as MP
            WHERE
            MP.nafiliados = '" . $_GetQTY['QTY'] . "'
            AND MP.stockid = '" . $_GetPlanData['stockid'] . "'
            AND MP.frecpagoid = '" . $_GetPlanData['frecuencia_pago'] . "'
            AND MP.paymentid = '" . $_GetPlanData['paymentid'] . "'
            ";
            $res_costinscip = DB_query($GetCostInscrip, $db);
            $_GetCostInscrip = DB_fetch_assoc($res_costinscip);
            /*###########################################################################*/
            FB::INFO($_GetCostInscrip, '_________INSCIPCION*_ ' . $GetCostInscrip);

            /*///Obtengo el COSTO PLAN///*/
            $GetCost = "SELECT costodos from rh_matrizprecios as MP
            WHERE
            MP.nafiliados = '" . $_GetQTY['QTY'] . "'
            AND MP.stockid = '" . $_GetPlanData['stockid'] . "'
            AND MP.frecpagoid = '" . $_GetPlanData['frecuencia_pago'] . "'
            AND MP.paymentid = '" . $_GetPlanData['paymentid'] . "'
            ";
            $res_cost = DB_query($GetCost, $db);
            $_GetCost = DB_fetch_assoc($res_cost);
            /*/////////////////////////////////////////////////////////////////*/
            FB::INFO($_GetCost, '_________COSTO PLAN*');

            /*///Obtengo el COSTO NUEVOS SOCIOS///*/
            if ($Tipo=='NuevosSocios') {
                //Todos Los Socios Actuales y por Agregar
                $GetQtyAllSocios = "SELECT COUNT(*) as QTY from custbranch where folio = '" . $Folio . "' AND (movimientos_socios = 'Activo' OR rh_status_captura = 'Facturar') ";
                $res_qtyAll = DB_query($GetQtyAllSocios, $db);
                $_GetQTYAll = DB_fetch_assoc($res_qtyAll);
                FB::INFO($GetQtyAllSocios, '______________________TOTAL ALL');
                if ($_GetQTYAll['QTY']>0) {
                    $GetCostAll = "SELECT costodos from rh_matrizprecios as MP
                    WHERE
                    MP.nafiliados = '" . $_GetQTYAll['QTY'] . "'
                    AND MP.stockid = '" . $_GetPlanData['stockid'] . "'
                    AND MP.frecpagoid = '" . $_GetPlanData['frecuencia_pago'] . "'
                    AND MP.paymentid = '" . $_GetPlanData['paymentid'] . "'
                    ";
                    $res_costAll = DB_query($GetCostAll, $db);
                    $_GetCostAll = DB_fetch_assoc($res_costAll);
                    $CostoAllSocios = $_GetCostAll['costodos'];
                }
                FB::INFO($CostoAllSocios, '___________________$CostoAllSocios');
            }
            /*/////////////////////////////////////////////////////////////////*/
            FB::INFO($_flag_status['cobro_inscripcion'], '____________________FLAG NUEVO');
            if ($_flag_status['cobro_inscripcion']==0) {
                $CostoTotal = $_GetCost['costodos'];
            } else {
                $CostoTotal = $_GetCostInscrip['costouno'] + $_GetCost['costodos'];
            }
            FB::INFO($CostoTotal, '__________________GET COSTO TOTAL');
            //unset($CostoTotal);

            if (empty($CostoTotal)) {
                /* si no se encontro la combinacion en la matriz de precios
                 * Obtengo el Costo del Plan   de la Tabla rh_preciocomisionista   */
                if (!empty($_GetPlanData['empresa'])) {
                    $Empresa = " AND PC.empresa = '" . $_GetPlanData['empresa'] . "'";
                }

                $GetPrecioComis = "SELECT PC.tarifa from rh_preciocomisionista as PC
                WHERE
                PC.dproducto = '" . $_GetPlanData['stockid'] . "'
                AND PC.dtipopago = '" . $_GetPlanData['frecuencia_pago'] . "'
                AND PC.dformap = '" . $_GetPlanData['paymentid'] . "'
                " . $Empresa . "
                ";

                $_2GetPrecioComis = DB_query($GetPrecioComis, $db);
                $_GetPrecioComis = DB_fetch_assoc($_2GetPrecioComis);

                $CostoTotal = $_GetPrecioComis['tarifa'] * $_GetQTY['QTY'];
                if(empty($CostoAllSocios)){
                    $CostoAllSocios = $_GetPrecioComis['tarifa'] * $_GetQTYAll['QTY'];
                }

                FB::INFO($GetPrecioComis, '_________________________________________$_GetPrecioComis SQL');
                FB::INFO($_GetPrecioComis, '_________________________________________$_GetPrecioComis');
            }

            //* Actualizo el Costo en la Tabla de rh_titular */
            if (!empty($CostoTotal) && ($Update==1)) {
                $UpdateCostoTotal = "UPDATE rh_titular SET costo_total = '" . $CostoAllSocios . "' WHERE folio = '" . $Folio . "' ";
                DB_query($UpdateCostoTotal, $db);
                FB::INFO($UpdateCostoTotal,'________________________UPDATE COSTO');
            }

            if (!empty($CostoTotal) && ($Tipo=='NuevosSocios')) {

                //$CostoTotal = 200;
                if (!empty($CostoTotal)) {
                    /* Cuando es una alta de titular  o un agregado de algun socio y si la fecha de registro es del 20 al 28 de cada mes en adelante se debe generar
                    una factura proporcional en base a los dias transcurridos de ese dia en adelante y en la emision estos socios no deben generar factura */
                    /*Ejemplo:
                          Tarifa calculada = 200
                          Fecha de registro 20 de junio
                          Días que contiene el mes de junio 30
                          Días a cobrar = diferencia entre los días del mes en este caso 30 – el día de registro 20
                          Días a cobrar = (30 – 20 )+1 = 11 días proporcionales
                          Tarifa proporcional = (200/30)* días a cobrar (11)
                    */
                    // 240.00       8 dias
                    $CurrentDate = date('Y-m-d');
                    //$CurrentDate = '2014-06-20';
                    $_Proporcional_de = date('Y-m-d', mktime(0, 0, 0, date("m"), 20, date("Y")));
                    $_Proporcional_a = date('Y-m-d', mktime(0, 0, 0, date("m"), 28, date("Y")));
                    if(($CurrentDate >= $_Proporcional_de) && ($CurrentDate <= $_Proporcional_a) ){
                        $_CurrentDateParts = explode("-", $CurrentDate);
                        $DiasMes = cal_days_in_month(CAL_GREGORIAN, $_CurrentDateParts[1], $_CurrentDateParts[0]);
                        FB::INFO($DiasMes,'_________________________DAYS_N');
                        $DiasACobrar = $DiasMes - $_CurrentDateParts[2];
                        FB::INFO($DiasACobrar,'_________________________DAYS_Cobrar');
                        $CostoTotal = ($CostoTotal/$DiasMes)*$DiasACobrar;
                        FB::INFO($CostoTotal,'_________________________TOTAL');
                        $FProporcional = 1;
                    }
                     if(($CurrentDate > $_Proporcional_a)){
                        $CostoTotal = 0;
                        $DiasGracia = 1;
                     }
                }
            }

            return array(
                'CostoTodosSocios' => $CostoAllSocios,
                'MontoRecibido' => $CostoTotal,
                'NuevosSocios' => $_GetCostInscrip['costodos'],
                'CostoTotal' => $CostoTotal,
                'QtyNS' => $_GetQTY['QTY'],
                'CostoInscripcion' => $_GetCostInscrip['costouno'],
                'FacturaProporcional' => $FProporcional,
                'DiasGracia' => $DiasGracia
            );
        }
    }

    public function actionGetsocio() {
        FB::INFO($_POST, '________________________<(·_·<)~');

        if (!empty($_POST['GetSocioData']['SBranchCode']) && !empty($_POST['GetSocioData']['SDebtorNo'])) {
            $GetData = Yii::app()->db->createCommand()->select(' * ')->from('custbranch')->where('branchcode = "' . $_POST['GetSocioData']['SBranchCode'] . '" AND debtorno = "' . $_POST['GetSocioData']['SDebtorNo'] . '" ')->queryAll();
            if (!empty($GetData)) {
                $PrintData = "
                        <table class ='table table-striped '>
                            <tr>
                                <td><label>Socio: {$GetData[0]['brname']}</label></td>
                                <td><label >Sexo: {$GetData[0]['sexo']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label >Nombre Empresa: {$GetData[0]['nombre_empresa']}</label></td>
                                <td><label >Fecha Nacimiento: {$GetData[0]['fecha_nacimiento']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label >Calle: {$GetData[0]['braddress1']}</label></td>
                                <td><label >N°: {$GetData[0]['braddress2']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label >Fecha Ingreso: {$GetData[0]['fecha_ingreso']}</label></td>
                                <td><label >Fecha ult. aum.: {$GetData[0]['fecha_ultaum']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label >Cod. postal: {$GetData[0]['braddress10']}</label></td>
                                <td><label >Colonia: {$GetData[0]['braddress4']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label >Sector: {$GetData[0]['braddress5']}</label></td>
                                <td><label >Entre calles: {$GetData[0]['braddress6']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td><label '>Municipio: {$GetData[0]['braddress7']}</label></td>
                                <td><label >Estado: {$GetData[0]['braddress8']}</label></td>
                                <td><label >Hospital: {$GetData[0]['braddress11']}</label></td>
                                <td></td>
                            </tr>

                            <tr>
                                <td colspan='3'>
                                    <label >CuadranteA: {$GetData[0]['cuadrante1']}</label>
                                    <label >CuadranteB: {$GetData[0]['cuadrante2']}</label>
                                    <label >CuadranteC: {$GetData[0]['cuadrante3']}</label>
                                </td>
                            </tr>

                            <tr>
                                <td><label >Telefono: {$GetData[0]['phoneno']}</label></td>
                                <td></td>
                            </tr>

                        ";
                echo CJSON::encode(array(
                    'requestresult' => 'ok',
                    'message' => "Detalle del Socio " . $_POST['GetSocioData']['SBranchCode'],
                    'GetData' => $PrintData
                ));
            } else {
                echo CJSON::encode(array(
                    'requestresult' => 'fail',
                    'message' => "No se pudieron obtener los Datos del Socio...",
                ));
            }
        } else {
            echo CJSON::encode(array(
                'requestresult' => 'fail',
                'message' => "BranchCode/DebtorNo Incorrectos...",
            ));
        }
        return;
    }

    public function actionVcerradas() {

        $this->render('vcerradas');
    }

    public function GetBalance($DebtorNo) {
        global $db;

        $_POST['FromCriteria'] = $DebtorNo;
        $_POST['ToCriteria'] = $DebtorNo;

        if (trim($_POST['Salesman'])!='') {
            $SalesLimit = " and debtorsmaster.debtorno in (SELECT DISTINCT debtorno FROM custbranch where salesman = '" . $_POST['Salesman'] . "') ";
        } else {
            $SalesLimit = "";
        }

        $SQL = "SELECT debtortrans.consignment,debtorsmaster.debtorno,
                    debtorsmaster.name,
                    currencies.currency,
                    currencies.currabrev,
                    currencies.rate,
                    paymentterms.terms,
                    debtorsmaster.creditlimit,
                    holdreasons.dissallowinvoices,
                    holdreasons.reasondescription,
                    SUM((debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)/currencies.rate) as balance
                    FROM debtorsmaster,
                    paymentterms,
                    holdreasons,
                    currencies,
                    debtortrans
                    " . $rh_rutas_debtors . "
                    WHERE debtorsmaster.paymentterms = paymentterms.termsindicator
                    AND debtorsmaster.currcode = currencies.currabrev
                    AND debtorsmaster.holdreason = holdreasons.reasoncode
                    AND debtorsmaster.debtorno = debtortrans.debtorno
                    AND debtorsmaster.debtorno >= '" . $_POST['FromCriteria'] . "'
                    AND debtorsmaster.debtorno <= '" . $_POST['ToCriteria'] . "'
                    $SalesLimit
                    AND debtortrans.type = 10
                    GROUP BY debtorsmaster.debtorno,
                    debtorsmaster.name,
                    currencies.currency,
                    paymentterms.terms,
                    paymentterms.daysbeforedue,
                    paymentterms.dayinfollowingmonth,
                    debtorsmaster.creditlimit,
                    holdreasons.dissallowinvoices,
                    holdreasons.reasondescription
                    HAVING
                    ABS(Sum(debtortrans.ovamount + debtortrans.ovgst + debtortrans.ovfreight + debtortrans.ovdiscount - debtortrans.alloc)) > 1";

        $_GLBResult = DB_query($SQL, $db);
        $GLBResult = DB_fetch_assoc($_GLBResult);
        FB::INFO($GLBResult, '________________________________________________AgeDebtors');
        return $GLBResult;
    }

    public function actionPrecaptura() {
        FB::INFO($_POST, '________________________<(°-°<)');
        if (!empty($_POST['folio'])) {
            $Verify = Yii::app()->db->createCommand()->select(' folio ')->from('rh_foliosasignados')->where('rh_foliosasignados.folio = ' . $_POST['folio'] . ' AND rh_foliosasignados.status = "free"')->queryAll();
            if (!empty($Verify)) {
                $Oportunidad = new Oportunidad;
                if (!empty($_POST)) {
                    $_Oportunidad['Oportunidad'] = $_POST;
                    $Oportunidad->attributes = $_Oportunidad['Oportunidad'];
                    if ($Oportunidad->save()) {
                        $SQLUpdateFolio = "UPDATE rh_foliosasignados SET rh_foliosasignados.status = 'used' WHERE folio = :folio";
                        $parameters = array(":folio" => $Oportunidad->attributes['folio']);
                        Yii::app()->db->createCommand($SQLUpdateFolio)->execute($parameters);
                        Yii::app()->user->setFlash("success", "La informacion del contacto se ha guardado correctamente.");
                    }
                }
            } else {
                Yii::app()->user->setFlash("error", "Este Folio no existe o ya fue usado.");
                $this->redirect('precaptura');
            }
        }
        $ALL = Yii::app()->db->createCommand()->select(' * ')->from('rh_oportunidades')->queryAll();
        $ListaMunicipios = CHtml::listData(Municipio::model()->findAll(), 'id', 'municipio');
        $ListaMetodosdePago = CHtml::listData(Paymentmethod::model()->findAll(), 'paymentid', 'paymentname');
        $ListaConvenios = CHtml::listData(Convenio::model()->findAll(), 'id', 'convenio');
        $ListaFrecuenciapagos = CHtml::listData(Frecuenciapago::model()->findAll(), 'id', 'frecuencia');

        $this->render('precaptura', array(
            'ListaMunicipios' => $ListaMunicipios,
            'ListaMetodosdePago' => $ListaMetodosdePago,
            'ListaConvenios' => $ListaConvenios,
            'ListaFrecuenciapagos' => $ListaFrecuenciapagos
        ));
    }

    public function actionEncuesta() {
        FB::INFO($_POST, '________________________<(°-°<)');
        if (!empty($_POST['Folio'])) {
            $Folio = $_POST['Folio'];
        } else {
            $Folio = "";
        }
        if (!empty($_POST['Asesor_ID'])) {
            $Asesor = $_POST['Asesor_ID'];
        } else {
            $Asesor = "";
        }

        if (isset($_POST['Search']) && !empty($Folio)) {
            $Verify = Yii::app()->db->createCommand()->select(' folio, comisionista_id, tipo_membresia ')->from('rh_foliosasignados')->where('folio = "' . $Folio . '"')->queryAll();

            if (!empty($Verify) && $Verify[0]['tipo_membresia']=='Cliente') {
                Yii::app()->user->setFlash("error", "El Folio ingresado es de Tipo Cliente.");
                $this->redirect(array('afiliaciones/encuesta'));
            }

            if (!empty($Verify[0]['comisionista_id'])) {
                $Asesor = $Verify[0]['comisionista_id'];
            }

            if (!empty($Verify)) {
                $RSearch = Yii::app()->db->createCommand()->select(' * ')->from('rh_encuesta')->where('folio = "' . $Folio . '"')->queryAll();
                //Yii::app()->user->setFlash("success", "La Encuesta se ha guardado Correctamente.");
            } else {
                Yii::app()->user->setFlash("error", "El Folio ingresado no existe ó no esta asignado a este Asesor.");
                $this->redirect(array('afiliaciones/encuesta'));
            }
        }

        if (isset($_POST['Save'])) {

            if (empty($_POST['P1'])) {
                $_POST['P1'] = "SI";
            }

            if (empty($_POST['P2'])) {
                $_POST['P2'] = "SI";
            }

            if (empty($_POST['P3'])) {
                $_POST['P3'] = "SI";
            }

            if (empty($_POST['P4'])) {
                $_POST['P4'] = "SI";
            }

            if (empty($_POST['P5'])) {
                $_POST['P5'] = "BUENA";
            }

            if (empty($_POST['P6'])) {
                $_POST['P6'] = "SI";
            }
            // Se agrego fecha y encuestado Angeles Perez 2016-08-31
            $sql = "insert into rh_encuesta (folio, comisionista_id, p1, p2, p3, p4, p5, p5pq, p5otro, p6, fecha, encuestado) 
            values (:FOLIO, :COMISIONISTA, :P1, :P2, :P3, :P4, :P5, :P5PQ, :P5OTRO, :P6, :FECHA, :ENCUESTADO)";
            $parameters = array(
                ':FOLIO' => $_POST['Folio'],
                ':COMISIONISTA' => $_POST['Asesor'],
                ':P1' => $_POST['P1'],
                ':P2' => $_POST['P2'],
                ':P3' => $_POST['P3'],
                ':P4' => $_POST['P4'],
                ':P5' => $_POST['P5'],
                ':P5PQ' => $_POST['P5pq'],
                ':P5OTRO' => $_POST['P5otro'],
                ':P6' => $_POST['P6'],
                ':ENCUESTADO' => $_POST['ENCUESTADO'],
                ':FECHA' => date('Y-m-d'),
            );
            Yii::app()->db->createCommand($sql)->execute($parameters);
            Yii::app()->user->setFlash("success", "La Encuesta se ha guardado Correctamente.");
        }

        if (!isset($RSearch)) {
            $RSearch = array();
        }

        $ListAsesores = CHtml::listData(Comisionista::model()->findAll('activo=1'), 'id', 'comisionista');
        $this->render('encuesta', array(
            'ListAsesores' => $ListAsesores,
            'Folio' => $Folio,
            'Asesor' => $Asesor,
            'Search' => $RSearch
        ));
    }

    public function actionViewsuspendpdf(){
        // $Folio = 500000;
        // $CancelNo = 2;
        if(!empty($_GET['Folio']) && !empty($_GET['SuspendNo'])){
            $Folio = $_GET['Folio'];
            $SuspendNo = $_GET['SuspendNo'];
            return $this->Pdfsuspend($Folio, $SuspendNo);
        }
        return;
    }

    /**
     * Genera Reporte de Suspensión
     *
     * @return int
     * @param $Folio
     * @param $SuspendNo, numero del movimiento
     * @param $AfilData, Datos del Titular
     * @author  erasto@realhost.com.mx
     */
    public function Pdfsuspend($Folio = null, $SuspendNo = null, $AfilData = null, $name = '', $ret = '') {
        global $db;

        if(empty($AfilData)){
            $GetAfil = Yii::app()->db->createCommand()
                ->select(' rh_titular.*, cob.stockid,
                            cob.frecuencia_pago,
                            sm.description,
                            pm.paymentname,
                            fp.frecuencia,
                            usr.realname,
                            ma.userid,
                            ma.motivos')
                ->from('rh_titular')
                ->leftJoin('rh_cobranza cob', 'cob.debtorno = rh_titular.debtorno')
                ->leftJoin('stockmaster sm', 'sm.stockid = cob.stockid')
                ->leftJoin('paymentmethods pm', 'pm.paymentid = cob.paymentid')
                ->leftJoin('rh_frecuenciapago fp', 'fp.id = cob.frecuencia_pago')
                ->leftJoin('rh_movimientos_afiliacion ma', 'ma.folio = rh_titular.folio ')
                ->leftJoin('www_users usr', 'usr.userid = ma.userid')
                ->where('rh_titular.folio = "' . $Folio . '" ')
                ->queryAll();
            $AfilData = $GetAfil[0];
        }

        $GetBranchCodes = "SELECT * FROM custbranch WHERE debtorno = '" . $AfilData['debtorno'] . "' ORDER BY branchcode DESC";
        $_GLBResult = DB_query($GetBranchCodes, $db);
        while ($GLBResult = DB_fetch_assoc($_GLBResult)) {
            $_2GLBData[] = $GLBResult;
        }
        // Se agrego el  "' AND folio= '". $Folio."'" ya que nos estaba trayendo mal las fechas y motivo Angeles Perez 2016-06-29
        $GetAfilMove = "SELECT userid,motivos,sus_fechainicial,sus_fechafinal FROM rh_movimientos_afiliacion WHERE moveno = '" . $SuspendNo . "'AND folio= '". $Folio."' ";
        $_2GetAfilMove = DB_query($GetAfilMove, $db);
        $_GetAfilMove = DB_fetch_assoc($_2GetAfilMove);

        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        include ($_SERVER['LocalERP_path'] . '/barcode2/barcode.inc.php');

        //$dirfile = $_SERVER['LocalERP_path'] . "/tmp/suspensiones";
        $dirfile = "/tmp/";
        $file = "Suspension_" . $SuspendNo . ".jpeg";
        if (!is_file($dirfile . "/" . $file)) {
            $bar = new BARCODE();
            $bar->setSymblogy("CODE39");
            $bar->setHeight(30);
            $bar->setScale(2);
            $bar->setHexColor("#00000", "#FFFFFF");
            $return = $bar->genBarCode($SuspendNo, "jpeg", $dirfile . "/Suspension_" . $SuspendNo);
        }

        include ($_SERVER['LocalERP_path'] . '/includes/FPDF.php');
        $pdf = new FPDF;
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);
        $y = 5;

        $pdf->SetXY(12, $y);
        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/logo.jpg", 5, $y, 30, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetX(80);
        $pdf->Cell(30, 10, utf8_decode('Reporte de Suspensión'), 0, 0, 'C');

        $y += 18;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(139, $y);
        $pdf->Cell(0, 0, utf8_decode("FOLIO SUSPENSIÓN:") . " " . str_pad($SuspendNo, 11, "0", STR_PAD_LEFT), 0, 0, "L");

        $y += 6;
        $pdf->SetXY(170, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, 'Fecha:', 0, 0, "L");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(184, $y);
        $pdf->Cell(0, 0, date('Y-m-d'), 0, 0, "L");

        ///////////////////////////////////////////////////////////////////////////////####### HEADER 1 ########
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Datos de Afiliación'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('N° Socio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(20, $y);
        $pdf->Cell(0, 0, $AfilData['folio'], 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('N° de Socios Afiliados: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, count($_2GLBData), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Titular: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(20, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['name'] . ' ' . $AfilData['apellidos']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Plan de Afiliacion: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(35, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['description']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Forma de Pago: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['paymentname']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L4
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Frecuencia de Pago: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(35, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['frecuencia']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Convenio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['convenio']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////######HEADER 2########
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Dirección'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Calle: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address1']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Numero: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(110, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address2']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Colonia: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address4']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Municipio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address7']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('C.P.: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(110, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address10']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(145, $y);
        $pdf->Cell(0, 0, utf8_decode('Telefono: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(160, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['rh_tel']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////#######HEADER 3 #######
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Suspensión'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Tipo de Suspensión: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode("AFILIACION"), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('N° Socios Suspendidos: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, count($_2GLBData), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Motivo de Suspensión: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['motivos']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Suspendido por: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['userid']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L4
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Socios Suspendidos: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        foreach ($_2GLBData as $Socio) {
            $pdf->SetXY(40, $y);
            $pdf->Cell(0, 0, utf8_decode($Socio['brname']), 0, 0, "L");
            $y += 6;
        }
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L5
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Suspensión efectiva del: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['sus_fechainicial'] . ' al ' . $_GetAfilMove['sus_fechafinal']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        $pdf->SetDrawColor(75, 96, 0);
        $pdf->SetXY(72, $y + 15);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 0, $dir, 0, 0, "L");

        $y += 20;
        $pdf->Image($dirfile . "/" . $file, 167, $y, 38, 'L');
        $pdf->SetXY(169, $y + 7);
        $pdf->Cell(30, 10, 'No:  ' . $SuspendNo, 0, 0, 'C');

        $y += 35;
        //aki y es = 273
        $pdf->Line(3, 273, 205, 273);
        $pdf->Line(3, 273.5, 205, 273.5);
        $pdf->SetY($y += 22);
        // $pdfcode = $pdf->output($dirfile . "/SuspendNo-" . $SuspendNo . ".pdf", 'F');
        // return true;
        $pdfcode = $pdf->output($name, $ret);
        return $pdfcode;
    }


    public function actionViewcancelpdf(){
        //$Folio = 500000;
        //$CancelNo = 2;
        if(!empty($_GET['Folio']) && !empty($_GET['CancelNo'])){
            $Folio = $_GET['Folio'];
            $CancelNo = $_GET['CancelNo'];
            return $this->Pdfcancel($Folio, $CancelNo);
        }
        return;
    }

    /**
     * Genera Reporte de Cancelación.
     *
     * @return int
     * @param $Folio
     * @param $CancelNo, Numero del movimiento
     * @param $AfilData, Datos del Titular
     * @author  erasto@realhost.com.mx
     */
    public function Pdfcancel($Folio = null, $CancelNo = null, $AfilData = null, $name = '', $ret = '') {
        global $db;
        $fecha__baja = date("Y-m-d");
        if(empty($AfilData)){
            $GetAfil = Yii::app()->db->createCommand()
                ->select(' rh_titular.*, cob.stockid,
                            cob.frecuencia_pago,
                            sm.description,
                            pm.paymentname,
                            fp.frecuencia,
                            usr.realname,
                            ma.userid,
                            ma.motivos')
                ->from('rh_titular')
                ->leftJoin('rh_cobranza cob', 'cob.debtorno = rh_titular.debtorno')
                ->leftJoin('stockmaster sm', 'sm.stockid = cob.stockid')
                ->leftJoin('paymentmethods pm', 'pm.paymentid = cob.paymentid')
                ->leftJoin('rh_frecuenciapago fp', 'fp.id = cob.frecuencia_pago')
                ->leftJoin('rh_movimientos_afiliacion ma', 'ma.folio = rh_titular.folio ')
                ->leftJoin('www_users usr', 'usr.userid = ma.userid')
                ->where('rh_titular.folio = "' . $Folio . '"')
                ->queryAll();
            $AfilData = $GetAfil[0];
        }

        $ListMotivosC = CHtml::listData(MotivosCancelacion::model()->findAll(), 'id', 'motivo');

        $GetBranchCodes = "SELECT * FROM custbranch WHERE debtorno = '" . $AfilData['debtorno'] . "' 
        AND (fecha_baja = '$fecha__baja')
        ORDER BY branchcode DESC";
        $_GLBResult = DB_query($GetBranchCodes, $db);
        
        $cant1 = "SELECT count(*) as socios_cancelados FROM custbranch WHERE folio = '$Folio' AND fecha_baja = '$fecha__baja' ";
        $cant2 = DB_query($cant1, $db);
        $soc_cancel = DB_fetch_assoc($cant2);

       

            //echo "<pre>";print_r($total_socios_restantes['sociostotales']);exit();
        while ($GLBResult = DB_fetch_assoc($_GLBResult)) {
            $_2GLBData[] = $GLBResult;
        }
// Se agrego para mostrar las fechas de cancelacion ademas se le agrego  "' AND folio= '". $Folio."'" ya que nos estaba trayendo mal las fechas y motivo Angeles Perez 2016-06-29
        $GetAfilMove = "SELECT userid,motivos,fecha_baja,fecha_cancelacion FROM rh_movimientos_afiliacion WHERE moveno = '" . $CancelNo . "' AND folio= '". $Folio."'";
        $_2GetAfilMove = DB_query($GetAfilMove, $db);
        $_GetAfilMove = DB_fetch_assoc($_2GetAfilMove);
// Termina

        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        include ($_SERVER['LocalERP_path'] . '/barcode2/barcode.inc.php');

        //$dirfile = $_SERVER['LocalERP_path'] . "/tmp/cancelaciones";
        $dirfile = "/tmp/";
        $file = "Cancelacion_" . $CancelNo . ".jpeg";
        if (!is_file($dirfile . "/" . $file)) {
            $bar = new BARCODE();
            $bar->setSymblogy("CODE39");
            $bar->setHeight(30);
            $bar->setScale(2);
            $bar->setHexColor("#00000", "#FFFFFF");
            $return = $bar->genBarCode($CancelNo, "jpeg", $dirfile . "/Cancelacion_" . $CancelNo);
        }

        include ($_SERVER['LocalERP_path'] . '/includes/FPDF.php');
        $pdf = new FPDF;
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);
        $y = 5;

        $pdf->SetXY(12, $y);
        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/logo.jpg", 5, $y, 30, 'L');

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetX(80);
        $pdf->Cell(30, 10, utf8_decode('Reporte de Cancelación'), 0, 0, 'C');

        $y += 18;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(139, $y);
        $pdf->Cell(0, 0, "FOLIO CANCELACION:" . " " . str_pad($CancelNo, 11, "0", STR_PAD_LEFT), 0, 0, "L");

        $y += 6;
        $pdf->SetXY(170, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, 'Fecha:', 0, 0, "L");
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(184, $y);
        $pdf->Cell(0, 0, date('Y-m-d'), 0, 0, "L");

        ///////////////////////////////////////////////////////////////////////////////####### HEADER 1 ########
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Datos de Afiliación'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('N° Socio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(20, $y);
        $pdf->Cell(0, 0, $AfilData['folio'], 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('N° de Socios Afiliados: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, count($_2GLBData), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Titular: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(20, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['name'] . ' ' . $AfilData['apellidos']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Plan de Afiliacion: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(35, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['description']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Forma de Pago: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['paymentname']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L4
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Frecuencia de Pago: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(35, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['frecuencia']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Convenio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['convenio']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////######HEADER 2########
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Dirección'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Calle: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address1']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('Numero: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(110, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address2']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Colonia: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address4']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Municipio: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(25, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address7']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('C.P.: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(110, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['address10']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(145, $y);
        $pdf->Cell(0, 0, utf8_decode('Telefono: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(160, $y);
        $pdf->Cell(0, 0, utf8_decode($AfilData['rh_tel']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        ///////////////////////////////////////////////////////////////////////////////#######HEADER 3 #######
        $y += 13;
        $pdf->SetXY(5, $y);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(0, 0, utf8_decode('Cancelación'), 0, 0, "L");

        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L1
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Tipo de Cancelación: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode("AFILIACION"), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(95, $y);
        $pdf->Cell(0, 0, utf8_decode('N° Socios Cancelados: '), 0, 0, "L");
        $datarr = mysqli_affected_rows();
        $_data = count($datarr);
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(130, $y);
        $pdf->Cell(0, 0, count($_2GLBData), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L2
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Motivo de Cancelacion: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        // Se cambio $AfilData por $_GetAfilMove para mostrar la descripcion del motivo de cancelación en lugar del id Angeles Perez 2016-06-29
        $pdf->Cell(0, 0, utf8_decode($ListMotivosC[$_GetAfilMove['motivos']]), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L3
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Cancelado por: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['userid']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////

        ///////////////////////////////////////////////////////////////////////////////L4
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Socios Cancelados: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        foreach ($_2GLBData as $Socio) {
            if ($Socio['branchcode'] != 0 ) {
               $_Socio = $Socio['brname'];
               }
            $pdf->SetXY(40, $y);
            $pdf->Cell(0, 0, utf8_decode($_Socio), 0, 0, "L");
            $y += 6;
        }
        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Fecha Baja: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['fecha_baja']), 0, 0, "L");

        $y += 6;
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->SetXY(5, $y);
        $pdf->Cell(0, 0, utf8_decode('Fecha Efectiva: '), 0, 0, "L");

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(40, $y);
        $pdf->Cell(0, 0, utf8_decode($_GetAfilMove['fecha_cancelacion']), 0, 0, "L");
        ///////////////////////////////////////////////////////////////////////////////
        // Termina


        $y += 3;
        $pdf->Line(3, $y, 205, $y);
        $pdf->Ln(1);

        $pdf->SetDrawColor(75, 96, 0);
        $pdf->SetXY(72, $y + 15);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(0, 0, $dir, 0, 0, "L");

        $y += 20;
        $pdf->Image($dirfile . "/" . $file, 167, $y, 38, 'L');
        $pdf->SetXY(169, $y + 7);
        $pdf->Cell(30, 10, $y . '-No:  ' . $CancelNo, 0, 0, 'C');

        $y += 35;
        //aki y es = 273
        $pdf->Line(3, 273, 205, 273);
        $pdf->Line(3, 273.5, 205, 273.5);
        $pdf->SetY($y += 22);
        //$pdfcode = $pdf->output($dirfile . "/CancelNo-" . $CancelNo . ".pdf", 'F');
        //return true;
        $pdfcode = $pdf->output($name, $ret);
        return $pdfcode;
    }

    /**
    *@Todo
    * Genera Carta de Bienvenida para los Socios
    */
    public function actionBienvenidapdf($name= '', $Ret = ''){

        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        $pdf = new FPDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);

        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/carta_bienvenida.jpg", 0, 0, 210, 'L');
        $pdfcode = $pdf->output($name, $Ret);
        return $pdfcode;
    }

    /**
    *@Todo
    * Genera Carta de Cliente Distinguido
    */
    public function actionSociodistinguido($name= '', $Ret = ''){

        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        $pdf = new FPDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);

        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/cliente_distinguido.jpg", 0, 0, 210, 'L');
        $pdfcode = $pdf->output($name, $Ret);
        return $pdfcode;
    }

    public function actionSendrecordatoriopago(){
        //FB::INFO($_POST,'___________________--POST');
        set_time_limit(0);
        parse_str($_POST['SendMail']['Customers'], $Folios);
        FB::INFO($Folios,'___________FOLIOS');

        foreach ($Folios['SendMail'] as $key => $DebtorNo) {
            $Folio = $this->GetFolio($DebtorNo);
            $this->actionSendmail($Folio, 'RecordatorioPago');
        }
        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'message' => "El Envio de Correos se ha realizado Correctamente... "
        ));
        return;
    }

    public function actionSenddunningletter(){
        FB::INFO($_POST,'___________________--POST');
        set_time_limit(0);
        parse_str($_POST['SendMail']['Customers'], $Folios);
        FB::INFO($Folios,'___________FOLIOS');

        foreach ($Folios['SendMailDunning'] as $key => $DebtorNo) {
            $Folio = $this->GetFolio($DebtorNo);
            $this->actionSendmail($Folio, 'DunningLetter');
        }
        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'message' => "El Envio de Correos se ha realizado Correctamente... "
        ));
        return;
    }

    /**
    * @todo
    * Genera PDF para enviar por Email
    * */
    public function actionGeneraDunninletter($name = '', $Ret = 'S', $Folio =  null){
        global $db;
        $DebtorNo = $this->GetDebtorNo($Folio);
        //$_GET['DebtorNo'] = 73174;
        $_GET['DebtorNo'] = $DebtorNo;
        $_GET['SendMail'] = true;
        $_GET['Ret'] = $Ret;
        $_GET['name'] = $name;

        $buf=include($_SERVER['LocalERP_path'] . '/rh_DunningLetter.php');
        //$pdf->stream();
        return $buf;
    }


    /**
    * @todo
    * Envia Facturas por Email Masivo
    *
    * */
    public function actionSendinvoicexml(){
        global $db, $db_;

        FB::INFO($_POST,'___________________--POST');
        set_time_limit(0);
        parse_str($_POST['SendMail2']['TransNo'], $Folios);
        FB::INFO($Folios,'___________FOLIOS');

        foreach ($Folios['SendEmail'] as $key => $TransNo) {
            /*Obtener Folio Mediante el TransNo*/
            $DebtorNo = $this->GetDebtorNoByTransNo($TransNo);
            $Folio = $this->GetFolio($DebtorNo);
            $this->actionSendmail($Folio, 'InvoiceANDXML', $TransNo);
        }
        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'message' => "El Envio de Correos se ha realizado Correctamente... "
        ));
        return;
    }


    /**
    * @todo
    * Genera PDF para enviar por Email
    * */
    public function actionGenerapdfinvoice($name = '', $Ret = 'S', $Folio = null, $TransNo){
        global $db, $db_, $host, $dbuser, $dbpassword;
        $DebtorNo = $this->GetDebtorNo($Folio);
        //$_GET['DebtorNo'] = 73174;

        $_GET['isTransportista'] = 0;
        $_GET['transno'] = $TransNo;
        //$_GET['transno'] = 81398;
        $_GET['afil'] = true;
        $isCfd = true;
        $fileBasePath = "/tmp";
        $cfdName = $_GET['transno'];
        $_GET['DebtorNo'] = $DebtorNo;
        $_GET['SendMail'] = true;
        $_GET['Ret'] = $Ret;
        $_GET['name'] = $name;
        $ErrMsg = "";
        $DbgMsg = "";
        //PHPJasperXML/sample1.php?isTransportista=0&transno=76262&&afil=true
        include($_SERVER['LocalERP_path'] . '/PHPJasperXML/sample1.php');
        $buf = file_get_contents("/tmp/". $_GET['transno'] . ".pdf");
        //FB::INFO($buf,'_______________________BUF');
        //$pdf->stream();
        return $buf;
    }

    public function actionCreatezipfile() {

        if(isset($_POST) && !empty($_POST[''])){

            $zip = new ZipArchive();
            $dir = "../XMLFacturacionElectronica/xmlbycfdi";
            $filename = "EMISION_" . $FechaEsmision;
            $destination = $dir . "/" . $filename . ".zip";
            if ($zip->open($destination, ZIPARCHIVE::CREATE) !== true) {
                return false;
            }

            $zip->addFile($filelist['SA'], 'archivo_sa.xls');

            $zip->close();


            header('Content-Type: application/zip');
            header('Content-Length: ' . filesize($destination));
            header('Content-Disposition: attachment; filename="' . $filename . '.zip"');
            readfile($destination);
            unlink($destination);
            exit();

        }
    }



    /**
    *@Todo
    * Genera Recordatorio de pago para los Socios
    */
    public function actionRecordatoriopagopdf($name= '', $Ret = '', $Folio = null){
        global $db;

        //$Folio= 500000;
        if(!empty($_GET['Folio'])){
            $Folio = $_GET['Folio'];
        }

        if(!empty($Folio)){
            $DebtorNo = $this->GetDebtorNo($Folio);

            $_2GetLastInvoice = "SELECT *,
                (debtortrans.ovamount+debtortrans.ovgst)/debtortrans.rate as Total,
                CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura
                FROM debtortrans
                LEFT JOIN rh_cfd__cfd ON debtortrans.id = rh_cfd__cfd.id_debtortrans
                WHERE type=10
                AND debtorno = '{$DebtorNo}'
                AND CAST((ovamount + ovgst + ovfreight + ovdiscount - alloc) AS DECIMAL(20,4)) <> 0
                ORDER BY trandate DESC
                /*LIMIT 1*/";
                //FB::INFO($_2GetLastInvoice,'___SQL:  ');
            $_GetLastInvoice = DB_query($_2GetLastInvoice, $db);
            //$GetLastInvoice = DB_fetch_assoc($_GetLastInvoice);

            $_2GetInvoiceQTY = "SELECT t.debtorno, (rh_titular.folio) as FolTitular,
                (SELECT sum(1)
                    FROM debtortrans
                    WHERE  (TO_DAYS(Now()) - (TO_DAYS(debtortrans.trandate) + d.paymentterms))> 1
                    AND debtortrans.debtorno =t.debtorno
                    AND settled = 0) AS Vencidas
                FROM debtorsmaster d
                LEFT JOIN rh_titular ON d.debtorno = rh_titular.debtorno
                LEFT JOIN rh_cobranza ON d.debtorno = rh_cobranza.debtorno, debtortrans t
                WHERE d.debtorno = t.debtorno
                AND CAST((t.ovamount + t.ovgst + t.ovfreight + t.ovdiscount - t.alloc) AS DECIMAL(20,4)) <> 0
                /*AND (t.ovamount+t.ovgst - t.alloc) <> 0*/
                AND t.settled = 0
                AND t.debtorno = '{$DebtorNo}'
                GROUP BY d.debtorno
                ORDER BY debtorno DESC";
            $_GetInvoiceQTY = DB_query($_2GetInvoiceQTY, $db);
            $GetInvoiceQTY = DB_fetch_assoc($_GetInvoiceQTY);
            //FB::INFO($_2GetInvoiceQTY,'___SQL:  ');exit;
        }


        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        $pdf = new FPDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);

        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/Recordatorio_pago2.jpg", 0, 0, 210, 'L');

        if($_SESSION['DatabaseName'] != 'sainar_erp_001'){
            $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/HEADER_OTROS.jpg", 0, 0, 210, 'L');
        }



        $sqlCompany = "SELECT coyname,
            gstno AS rfc,
            companynumber,
            regoffice1,
            regoffice2,
            regoffice3,
            regoffice4,
            regoffice5,
            regoffice6,
            telephone,
            fax,
            email,
            dtranferencia_nombre_responsable,
            dtranferencia_correo_responsable,
            dtranferencia_telefono_responsable
            FROM companies
        WHERE coycode=1";
        $rescompany = DB_query($sqlCompany,$db,'','');
        $company = DB_fetch_assoc($rescompany);

        $sqlBankData = "SELECT *
            FROM rh_dunning_banks
        WHERE coycode=1";
        $resBankData = DB_query($sqlBankData,$db,'','');

        while ($BankData = DB_fetch_assoc($resBankData)) {
            # code...
            if($BankData['tipo'] == 'Transferencia'){
                $Transferencia[] = $BankData;
            }

            if($BankData['tipo'] == 'Deposito'){
                $Deposito[] = $BankData;
            }
        }



        $y = 60;
        $y += 6;

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(85, $y);
        $pdf->Cell(0, 0, utf8_decode($company['dtranferencia_telefono_responsable']), 0, 0, "L");

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(110, $y);
        $pdf->Cell(0, 0, utf8_decode(' con '.$company['dtranferencia_nombre_responsable']), 0, 0, "L");

        $y += 7;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(19, $y);
        $pdf->Cell(0, 0, utf8_decode($company['dtranferencia_correo_responsable']), 0, 0, "L");

        /******************************************************************************************/


        // echo "<pre>";
        // print_r($Deposito);
        // echo "</pre>";
        // exit;

        /******************************************************************************************/
        $y = 90;
        ///////////////////////////////////////////////////////////////////////////////L3

        while ( $Invoices = DB_fetch_assoc($_GetLastInvoice)) {
            # code...
            $y += 6;
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetXY(21, $y);
            $pdf->Cell(0, 0, utf8_decode($Folio), 0, 0, "L");

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(60, $y);
            $pdf->Cell(0, 0, "$ " . number_format($Invoices['Total'],2), 0, 0, "L");

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(100, $y);
            $pdf->Cell(0, 0, utf8_decode($Invoices['FolioFactura']), 0, 0, "L");

            $Month = $this->GetMonth($Invoices['trandate'],1);

            $pdf->SetFont('Arial', '', 8);
            $pdf->SetXY(130, $y);
            $pdf->Cell(0, 0, utf8_decode($Month), 0, 0, "L");
        }

        $pdf->SetFont('Arial', '', 8);
        $pdf->SetXY(170, 96);
        $pdf->Cell(0, 0, utf8_decode($GetInvoiceQTY['Vencidas']), 0, 0, "L");

        ///////////////////////////////////////////////////////////////////////////////



        //$y = 123.5;
        $y2 = $y + 10;
        $y = $y + 10;

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(19, $y);
        $pdf->Cell(0, 0, utf8_decode("Datos Transferencia"), 0, 0, "L");
        $y += 5;
        $pdf->SetXY(19, $y);
        $pdf->Cell(0, 0, utf8_decode($Transferencia[0]['nombre_cuenta']/*"Sainar Medica S.C."*/), 0, 0, "L");
        $y += 5;

        foreach ($Transferencia as $Trans) {

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(19, $y);
            $pdf->Cell(0, 0, utf8_decode($Trans['banco']), 0, 0, "L");
            $y += 5;

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(19, $y);
            $pdf->Cell(0, 0, utf8_decode("Número de Cuenta Clabe " . $Trans['clabe']), 0, 0, "L");
            $y += 8;
        }

        //$y = 123.5;
        $y = $y2;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(102, $y);
        $pdf->Cell(0, 0, utf8_decode("Datos Deposito"), 0, 0, "L");
        $y += 5;

        $pdf->SetXY(102, $y);
        $pdf->Cell(0, 0, utf8_decode($Deposito[0]['nombre_cuenta']/*"Sainar Medica S.C."*/), 0, 0, "L");
        $y += 5;
        foreach ($Deposito as $Dep) {

            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(102, $y);
            $pdf->Cell(0, 0, utf8_decode($Dep['banco']), 0, 0, "L");
            $y += 5;
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetXY(102, $y);
            $pdf->Cell(0, 0, utf8_decode("Número de Cuenta: " . $Dep['clabe']), 0, 0, "L");
            $y += 8;
        }
        $y += 15;
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(150, $y);
        $pdf->Cell(0, 0, utf8_decode("Reciba un Cordial Saludo."), 0, 0, "L");

        $pdfcode = $pdf->output($name, $Ret);
        return $pdfcode;
    }


/** GENERACION DE PAGO SOLO PARA LOLOS CLIENTES DE TORREON **/
/**Creado por Eliobeth Ruiz el Jueves 8 de Diciembre del 2016 **/


///////////////////////////////////////////////////////////////////////////////////////
///////////////// CREACION DE UNA NUEVA FUNCION PARA GENERAR REPORTE ///////////////////
///////////////// DE PERSONAS QUE PAGAN EXCLUSIVAMENTE CON TARJETA Y //////////////////
///////////////// ADEMAS PRESENTAN ADEUDO O ERROR EN LOS DATOS DE LA MISMA ////////////
// -----------------------------------------------------------------------------------//
///////////////// Eliobeth Ruiz Hernández 09/12/2016 10:16:00 am    ///////////////////
///////////////////////////////////////////////////////////////////////////////////////
    public function actionRecordatoriopagoTorreonpdf($name= '', $Ret = '', $Folio = null){
        global $db;

        //$Folio= 500000;
        if(!empty($_GET['Folio'])){
            $Folio = $_GET['Folio'];
        }

        if(!empty($Folio)){
            $DebtorNo = $this->GetDebtorNo($Folio);

            $_2GetLastInvoice = "SELECT *,
                (debtortrans.ovamount+debtortrans.ovgst)/debtortrans.rate as Total,
                CONCAT(rh_cfd__cfd.serie, '', rh_cfd__cfd.folio) as FolioFactura
                FROM debtortrans
                LEFT JOIN rh_cfd__cfd ON debtortrans.id = rh_cfd__cfd.id_debtortrans
                WHERE type=10
                AND debtorno = '{$DebtorNo}'
                AND CAST((ovamount + ovgst + ovfreight + ovdiscount - alloc) AS DECIMAL(20,4)) <> 0
                ORDER BY trandate DESC
                /*LIMIT 1*/";
            $_GetLastInvoice = DB_query($_2GetLastInvoice, $db);

            $_2GetInvoiceQTY = "SELECT t.debtorno, (rh_titular.folio) as FolTitular,
                (SELECT sum(1)
                    FROM debtortrans
                    WHERE  (TO_DAYS(Now()) - (TO_DAYS(debtortrans.trandate) + d.paymentterms))> 1
                    AND debtortrans.debtorno =t.debtorno
                    AND settled = 0) AS Vencidas
                FROM debtorsmaster d
                LEFT JOIN rh_titular ON d.debtorno = rh_titular.debtorno
                LEFT JOIN rh_cobranza ON d.debtorno = rh_cobranza.debtorno, debtortrans t
                WHERE d.debtorno = t.debtorno
                AND CAST((t.ovamount + t.ovgst + t.ovfreight + t.ovdiscount - t.alloc) AS DECIMAL(20,4)) <> 0
                /*AND (t.ovamount+t.ovgst - t.alloc) <> 0*/
                AND t.settled = 0
                AND t.debtorno = '{$DebtorNo}'
                GROUP BY d.debtorno
                ORDER BY debtorno DESC";
            $_GetInvoiceQTY = DB_query($_2GetInvoiceQTY, $db);
            $GetInvoiceQTY = DB_fetch_assoc($_GetInvoiceQTY);

        }

        chdir(dirname(__FILE__));
        include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
        $pdf = new FPDF('P','mm','A4');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 7);
        $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/Recordatorio_pago2.jpg", 0, 0, 210, 'L');

        if($_SESSION['DatabaseName'] != 'sainar_erp_001'){
            $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/carta_adeudo_trn.jpg", 0, 0, 210, 'L');
        }

        $sqlCompany = "SELECT coyname,
            gstno AS rfc,
            companynumber,
            regoffice1,
            regoffice2,
            regoffice3,
            regoffice4,
            regoffice5,
            regoffice6,
            regoffice7,
            regoffice8,
            regoffice9,
            regoffice10,
            telephone,
            fax,
            email,
            dtranferencia_nombre_responsable,
            dtranferencia_correo_responsable,
            dtranferencia_telefono_responsable
            FROM companies
        WHERE coycode=1";
        $rescompany = DB_query($sqlCompany,$db,'','');
        $company = DB_fetch_assoc($rescompany);

        $Folio = $_GET['Folio'];
        $ConsultaCuenta = "SELECT * 
                                FROM rh_cobranza
                                WHERE folio =  '$Folio'";
        $resultadoConsultaCuenta = DB_query($ConsultaCuenta,$db,'','');
        $DatosCuentaCliente = DB_fetch_assoc($resultadoConsultaCuenta);

        $ConsultaInfoCliente = "SELECT name, apellidos, nombre_empresa
                            FROM rh_titular
                            WHERE folio = '$Folio'";
        $resultadoConsultaInfoCliente = DB_query($ConsultaInfoCliente,$db,'','');
        $DatosInfoCliente = DB_fetch_assoc($resultadoConsultaInfoCliente);

        $sqlBankData = "SELECT *
            FROM rh_dunning_banks
        WHERE coycode=1";
        $resBankData = DB_query($sqlBankData,$db,'','');

        while ($BankData = DB_fetch_assoc($resBankData)) {
            if($BankData['tipo'] == 'Transferencia'){
                $Transferencia[] = $BankData;
            }

            if($BankData['tipo'] == 'Deposito'){
                $Deposito[] = $BankData;
            }
        }

        //$y2 = $y + 50;
        $yDireccion = $yDireccion + 133;
        $y_Direccion = $y_Direccion + 139;
        $yFolio = $yFolio + 176;
        $yTargeta = $yTargeta + 184;
        $yCuenta = $yCuenta + 192;
        $yBanco = $yBanco + 199;
        $yVenciminto = $yVenciminto + 206;

        foreach ($Transferencia as $Trans) {

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(8, $yDireccion);
            //$pdf->SetTextColor(90,100,220);
            $pdf->Cell(0, 0, utf8_decode(
                $company['regoffice1']." No. ".$company['regoffice2']).", Col. ".$company['regoffice4']." ".$company['regoffice10'], 0, 0, "L");

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetXY(8, $y_Direccion);
            $pdf->Cell(0, 0, utf8_decode(
                $company['regoffice7'].", ".$company['regoffice8']).". ".$company['regoffice9'], 0, 0, "L");

            if (empty($DatosInfoCliente['nombre_empresa'])) {
                $_DatosInfoCliente = $DatosInfoCliente['name']." ".$DatosInfoCliente['apellidos'];
            }else{
                $_DatosInfoCliente = $DatosInfoCliente['nombre_empresa'];
            }

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(70, $yFolio);
            $pdf->Cell(100, 0, utf8_decode($DatosCuentaCliente['folio'].". ".$_DatosInfoCliente), 0, 0, "L");

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(70, $yTargeta);
            $pdf->Cell(100, 0, utf8_decode($DatosCuentaCliente['metodo_pago']), 0, 0, "L");

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(70, $yCuenta);
            $pdf->Cell(100, 0, $this->OpenSSLDecrypt($DatosCuentaCliente['cuenta_sat']), 0, 0, "L");

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(70, $yBanco);
            $pdf->Cell(100, 0, utf8_decode($DatosCuentaCliente['rh_banco']), 0, 0, "L");           

            $pdf->SetFont('Arial', '', 12);
            $pdf->SetXY(70, $yVenciminto);
            $pdf->Cell(0, 0, $this->OpenSSLDecrypt($DatosCuentaCliente['vencimiento']), 0, 0, "L");
            //$y += 5;      
        }

        $pdfcode = $pdf->output($name, $Ret);
        return $pdfcode;
    }
///////////////////////////////////////////////////////////////////////////////////////
///////////////// MODIFICACIONES AL NUEVO REPORTE PDF DE TORREON    ///////////////////
///////////////// Eliobeth Ruiz Hernández 09/12/2016 10:16:00 am    ///////////////////
///////////////////////////////////////////////////////////////////////////////////////


    public function actionMovimientosAfiliacion(){

         $MovimientosAfiliacion = Yii::app()->db->createCommand()
        ->select('*')
        ->from('rh_movimientos_afiliacion')
        ->queryAll();

        $this->render('movimientosafiliacion', array('MovimientosAfiliacion' => $MovimientosAfiliacion));
    }


    public function actionReportecancelacion(){

        $where = " 1 = 1 ";

         if (!empty($_POST['Search']['fecha_baja_inicio']) && !empty($_POST['Search']['fecha_baja_fin'])) {
            $where .=  "( AND fecha_baja > '" . $_POST['Search']['fecha_baja_inicio'] . " 00:00:00' AND fecha_baja < '" . $_POST['Search']['fecha_baja_fin'] . " 23:59:59' )";
        }

        if (!empty($_POST['Search']['fecha_cancelacion_inicio']) && !empty($_POST['Search']['fecha_cancelacion_fin'])) {
            $where .=  " AND (fecha_cancelacion > '" . $_POST['Search']['fecha_cancelacion_inicio'] . " 00:00:00' AND fecha_cancelacion < '" . $_POST['Search']['fecha_cancelacion_fin'] . " 23:59:59' )";
        }

        if(!empty($_POST['Search']['status'])){
            $where .= " AND movetype = '".$_POST['Search']['status']."'";
        }

        FB::INFO($where, 'where');

        $BuscarMovimientos=Yii::app()->db->createCommand()
        ->select('*')
        ->from('rh_movimientos_afiliacion')
        ->where($where)
        ->queryAll();

        $Tbody = "";

        if(!empty($BuscarMovimientos[0]['id'])){
            foreach ($BuscarMovimientos as $Movimientos) {
                $Tbody.="
                     <tr>
                        <td>{$Movimientos['debtorno']}</td>
                        <td>{$Movimientos['folio']}</td>
                        <td>{$Movimientos['moveno']}</td>
                        <td>{$Movimientos['movetype']}</td>
                        <td>{$Movimientos['fecha_baja']}</td>
                        <td>{$Movimientos['fecha_cancelacion']}</td>
                        <td>{$Movimientos['motivos']}</td>
                        <td>{$Movimientos['sus_fechainicial']}</td>
                        <td>{$Movimientos['sus_fechafinal']}</td>
                        <td>{$Movimientos['monto_recibido']}</td>
                        <td>{$Movimientos['tarifa_total']}</td>
                    </tr>
                ";
            }
            echo CJSON::encode(array(
                'requestresult' => 'ok',
                'Tbody' => $Tbody,
                //'message' => ''
            ));
        }else{
            echo CJSON::encode(array(
                'requestresult'=>'fail',
                'message' => 'No se encontraron registros que coincidan con la busqueda',
                'Tbody' => $Tbody,
            ));
        }
        return;
    }

	public function actionImpresionMasiva(){
        global $db, $db_;

        set_time_limit(0);
        parse_str($_REQUEST['SendMail2']['TransNo'], $Folios);
        if(count($Folios['SendEmail'])==0){
        	echo CJSON::encode(array(
            	'requestresult' => 'error',
	            'message' => "Favor de elegir que facturas requiere imprimir, utilice los criterios y los check box"
	        ));
        	return true;
        }
       	$FechaEsmision=date('Y-m-d His');
		$zip = new ZipArchive();
        $dir = "../XMLFacturacionElectronica/xmlbycfdi";
        $filename = "EMISION_" . $FechaEsmision;
        $destination = $dir . "/" . $filename . ".zip";
        if ($zip->open($_SERVER['LocalERP_path'].'/tmp/'.$destination, ZIPARCHIVE::CREATE) !== true) {
        	echo CJSON::encode(array(
            	'requestresult' => 'error',
	            'message' => "No se pudo crear el archivo zip "
	        ));
        	return true;
        }
        global $FoliosAsignados;
        $FoliosAsignados=array();
		$i=0;
        foreach ($Folios['SendEmail'] as $key => $TransNo) {
            /*Obtener Folio Mediante el TransNo*/
            $DebtorNo = $this->GetDebtorNoByTransNo($TransNo);
            $Folio = $this->GetFolio($DebtorNo);
            $archivo=$this->actionGenerarPDF($Folio, 'GenerateZipPDF', $TransNo);

            if(is_array($archivo)&&trim($archivo['PDF']['archivo'])!=''){
            	$i++;
            	$zip->addFromString($archivo['XML']['nombre'],$archivo['XML']['archivo']);
	            $zip->addFromString($archivo['PDF']['nombre'],$archivo['PDF']['archivo']);
            }
        }
        $zip->close();
        if($i==0){
        	echo CJSON::encode(array(
            	'requestresult' => 'error',
	            'message' => "No se pudieron incluir facturas en el archivo zip"
	        ));
        	return true;
        }
        echo CJSON::encode(array(
            'requestresult' => 'ok',
            'message' => "El Archivo se ha generado Correctamente con {$i} facturas<br >espere la <a href=\"".trim($destination,'./')."\">descarga</a>",
        	'url'=>trim($destination,'./')
        ));
        return true;
    }

	public function actionGenerarPDF($Folio = null, $Tipo = null, $_TransNo = null){
        global $db;
        if (!empty($Folio)) {
            switch ($Tipo) {
                case 'GenerateZipPDF':
					$attachment=array();
                    /*Get XML*/
                    $CFDIData = Yii::app()->db->createCommand()->select(' c.uuid, c.serie, c.folio, c.xml ')->from('rh_cfd__cfd c')->where('c.fk_transno = :fk_transno', array(':fk_transno' => $_TransNo) )->queryAll();
                    $UUID = $CFDIData[0]['uuid'];
                    $cfdName = $CFDIData[0]['serie'].''.$CFDIData[0]['folio'];
                    $xmlFile = $CFDIData[0]['xml'];
                    /*Get PDF*/

                    $PDF = $this->actionGenerapdfinvoice('',$Ret = 'S', $Folio, $_TransNo);
                            FB::INFO($Folios['SendEmail'],'________________SEND');
                    if(!isset($FoliosAsignados[$archivo['PDF']['folio'].$archivo['PDF']['serie']]))
            			$FoliosAsignados[$archivo['PDF']['folio'].$archivo['PDF']['serie']]=0;
                    else
                    	$cfdName.=' ('.$FoliosAsignados[$archivo['PDF']['folio'].$archivo['PDF']['serie']].')';
                    	$FoliosAsignados[$archivo['PDF']['folio'].$archivo['PDF']['serie']]++;
                    $attachment['PDF'] = array(
                    		'nombre'=>"{$cfdName}.pdf",
	                    	'archivo'=>$PDF,
		                    'uuid'=>$UUID,
		                    'serie'=>$CFDIData[0]['serie'],
		                    'folio'=>$CFDIData[0]['folio']
	                    );
                    if(!empty($xmlFile)){
                        $attachment['XML'] = array(
                        	'nombre'=>"{$cfdName}.xml",
                        	'archivo'=>$xmlFile,
                        	'uuid'=>$UUID,
		                    'serie'=>$CFDIData[0]['serie'],
		                    'folio'=>$CFDIData[0]['folio']
                        	);
                    }

                    return $attachment;
                    break;
            }
            return;
        }
    }

/***  Función agregada para desplegar reporte de historial de los usuarios cancelados o suspendidos
        Autor: Eliobeth Ruiz
        Propietario: ARMedica  
***/
    public function actionHistorialPersonas(){
        //pr($_POST);
       /*
        $sql_hist = "SELECT tt.*, datediff(tt.fecha_cancelacion, tt.fecha_ingreso) as permanencia,
        mc.motivo
        FROM rh_titular as tt, rh_motivos_cancelacion AS mc
        WHERE tt.motivo_cancelacion = mc.id
        AND tt.movimientos_afiliacion != 'Activo' AND tt.folio != '0'
        AND tt.fecha_cancelacion BETWEEN '" . $_POST['Fecha_Inicial'] . "' 
        and '" . $_POST['Fecha_Final'] . "'
        ORDER BY permanencia DESC"; */
        $sql_hist = "
        SELECT  rt.folio, concat(rt.name, ' ', rt.apellidos) AS nombre,
        rt.movimientos_afiliacion AS estatus, rt.fecha_ingreso, 
        CASE rm.sus_fechainicial
            WHEN '0000-00-00' THEN mc.motivo
            ELSE rm.motivos
            END AS motivo,
        CASE rm.sus_fechainicial
            WHEN '0000-00-00' THEN MAX(rm.fecha_cancelacion)
            ELSE MAX(rm.sus_fechainicial)
            END AS fecha_cancel_susp,
        CASE rm.sus_fechainicial
            WHEN '0000-00-00' THEN datediff(rm.fecha_cancelacion, rt.fecha_ingreso)
            ELSE datediff(rm.sus_fechainicial, rt.fecha_ingreso)
            END AS permanencia
    FROM rh_titular AS rt, 
        rh_movimientos_afiliacion AS rm,
        rh_motivos_cancelacion as mc
    WHERE rt.folio = rm.folio
        AND rt.motivo_cancelacion = mc.id
        AND  rt.folio IN
            (
            SELECT n2.folio FROM rh_movimientos_afiliacion AS n2 
            WHERE n2.id IN 
                (
                    SELECT MAX(n1.id) FROM rh_movimientos_afiliacion AS n1
                    WHERE n1.movetype != 'Activo'
                    GROUP BY n1.folio
                )
            ORDER BY n2.folio ASC
        )
        AND movimientos_afiliacion != 'Activo' 
        AND rt.motivo_cancelacion = mc.id
        AND rm.id IN (
                    SELECT MAX(mi.id) FROM rh_movimientos_afiliacion AS mi
                    WHERE mi.movetype != 'Activo'
                    GROUP BY mi.folio
                    ) AND
            CASE rm.sus_fechainicial
                WHEN '0000-00-00' 
                    THEN rm.fecha_cancelacion BETWEEN '" . $_POST['Fecha_Inicial'] . "' and '" . $_POST['Fecha_Final'] . "'
                ELSE rm.sus_fechainicial BETWEEN '" . $_POST['Fecha_Inicial'] . "' 
                        and '" . $_POST['Fecha_Final'] . "'
            END
    GROUP BY folio
    ORDER BY permanencia ASC";
        $datos_hist = Yii::app()->db->createCommand($sql_hist)->queryAll();
//echo "<pre>";print_r($datos_hist);exit();
        $this->render('HistorialLista', array(
                    'datosHistorial' => $datos_hist,

            ));

        
    }
}
?>