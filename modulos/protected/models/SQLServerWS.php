<?php

/**
 * @Todo
 * http://104.130.129.147/wsceom2/WebService.asmx?wsdl
 * $url_TEST = "http://zona07.com/myapp/WebService.asmx?wsdl";
 * $SelectAction = "http://tempuri.org/select";
 * $InsertAction = "http://tempuri.org/insert";
 * $UpdateAction = "http://tempuri.org/update";
 * $CountAction = "http://tempuri.org/selectcont";
 * $SELECT = $this->GetFromWS($XmlSelect, $url, array('SOAPAction: ' . $SelectAction))->saveXML();
 * $COUNT = $this->GetCountWS($XmlSelectCountNoFecha, $url, array('SOAPAction: ' . $CountAction))->saveXML();
 * $TESTINSERT = $this->InsertInToWS($XmlInsert, $url, array('SOAPAction: ' . $InsertAction))->saveXML();
 * $TESTUPDATE = $this->UpdateWS($XMLUpdate, $url, array('SOAPAction: ' . $UpdateAction))->saveXML();
 */
class SQLServerWS {


    public $ServiceURL ="http://104.130.129.147/wsceom2/WebService.asmx?wsdl";

    public $_ServiceURL ="http://zona07.com/myapp/WebService.asmx?wsdl";

    public $LogID;

    public $TipoConsulta;

    public $CatalogoName;

    public $CatalogID;

    public $LasRHID;

    public $LasARID;


    public function MSDBConect() {

        //Conexion ala BD de MS SQL
        $MSCon = mssql_connect("s05.winhost.com", "DB_72419_armedica_user", "EEdfgr21fgres");
        if (!$MSCon) {
            die('Something went wrong while connecting to MSSQL');
        }
        $BDAR = mssql_select_db('DB_72419_armedica', $MSCon);
        return $MSCon;
    }

    /**
    * Envia Peticion
    *
    **/
    public function SendToWS($mySOAP, $url, $Actions = array()) {
        
        $headers = array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($mySOAP)
        );
        $headers = array_merge($headers, $Actions);
        // Build the cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($Actions));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mySOAP);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // Send the request and check the response
        if (($result = curl_exec($ch))===false) {
            return null;
        }
        curl_close($ch);
        $xmlobj = simplexml_load_string($result);

        return $xmlobj;
    }

    /*Crea Log de la peticion*/
    public function CreaTransLog($tipo = null, $xmlsend = null){
        $this->TipoConsulta = $tipo;
        if(!empty($xmlsend)){
            $InsertLog = "INSERT INTO rh_ws__cecom_log (
                tipo,
                userid,
                status,
                xmlsend,
                xmlresponse,
                created
            )VALUES(
                :tipo,
                :userid,
                :status,
                :xmlsend,
                :xmlresponse,
                :created
            )";
            $InsertLogParameters = array(
                ':tipo' => $this->TipoConsulta,
                ':userid' => $_SESSION['UserID'],
                ':status' => '',
                ':xmlsend' => $xmlsend,
                ':xmlresponse' => '',
                ':created' => date("Y-m-d H:i:s")
            );
            try {
                Yii::app()->db->createCommand($InsertLog)->execute($InsertLogParameters);
                $LogID = Yii::app()->db->getLastInsertID();
                return $LogID;
            } catch (Exception $e) {
                FB::INFO($e->getMessage(),'___ERROR INSERTAR LOG: ');
            }
        }
    }

    /* Update Log de la peticion*/
    public function UpdateTransLog($id = null, $xmlresponse = null){
        FB::INFO($id, '________________OKOKOKOKOK LOG');
        if(!empty($id) && !empty($xmlresponse)){


            switch ($this->TipoConsulta) {
                case 'UPDATE':
                    $domObj = new xmlToArrayParser($xmlresponse);
                    $GetB64 = $domObj->array;

                    if ($domObj->parse_error) {
                        FB::INFO($domObj->get_xml_error(), '____________________FAIL');
                    } else {
                        FB::INFO($GetB64, '_____________________DONE...!!!');
                        $NewXML = $GetB64['soap:Envelope']['soap:Body']['updateResponse']['updateResult'];
                        $Result = explode("-", $NewXML);
                        if($Result[0] == 'SUCCESS'){
                            $LogStatus = $Result[0];
                            Yii::app()->user->setFlash("alert", "El Registro se Actualizo correctamente en la Base de Datos de CECOM.");
                        }else{
                            $LogStatus = 'FAIL';
                            $Exception = $NewXML;
                            Yii::app()->user->setFlash("errorlog", "Ocurrio un problema al Actualizar el Registro en la Base de Datos de CECOM.");
                        }
                        FB::INFO($NewXML, '_____________________RESULT XML');
                    }
                    break;
                case 'INSERT':
                    $domObj = new xmlToArrayParser($xmlresponse);
                    $GetB64 = $domObj->array;

                    if ($domObj->parse_error) {
                        FB::INFO($domObj->get_xml_error(), '____________________FAIL');
                    } else {
                        FB::INFO($GetB64, '_____________________DONE...!!!');
                        $NewXML = $GetB64['soap:Envelope']['soap:Body']['insertResponse']['insertResult'];
                        $Result = explode("-", $NewXML);
                        if($Result[0] == 'SUCCESS'){
                            $LogStatus = $NewXML;

                            if(!empty($Result[1]) && !empty($this->CatalogID)){
                                $this->LasARID = $Result[1];
                            }

                            Yii::app()->user->setFlash("alert", "El Registro se Inserto correctamente en la Base de Datos de CECOM.");
                        }else{
                            $LogStatus = 'FAIL';
                            $Exception = $NewXML;
                            Yii::app()->user->setFlash("errorlog", "Ocurrio un problema al Insertar el Registro en la Base de Datos de CECOM.");
                        }
                        FB::INFO($NewXML, '_____________________RESULT XML');
                    }
                    break;
                default:
                    # code...
                    break;
            }

            $UpdateLog = "UPDATE rh_ws__cecom_log SET
                userid = :userid,
                status = :status,
                xmlresponse = :xmlresponse,
                exception = :exception,
                updated = :updated
            WHERE id = :id";
            $UpdateLogParameters = array(
                ':userid' => $_SESSION['UserID'],
                ':status' => $LogStatus,
                ':xmlresponse' => $xmlresponse,
                ':exception' => $Exception,
                ':updated' => date("Y-m-d H:i:s"),
                ':id' => $id
            );
            try {
                Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
            } catch (Exception $e) {
                FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR LOG: ');
            }
        }
    }



    /**
     * @Todo Ejecuta SELECT * dela Tabla Especificada en $xml
     * @return XML
     * @author erasto@realhost.com.mx
     */
    public function GetFromWS($xml, $url, $Actions = array()) {

        $mySOAP = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <select xmlns="http://tempuri.org/">
                        <Sxml>
                            <![CDATA[' . base64_encode($xml) . ']]>
                        </Sxml>
                    </select>
                </soap:Body>
            </soap:Envelope>';
        return $this->SendToWS($mySOAP, $url, $Actions);
    }

    public function GetCountWS($xml, $url, $Actions = array()) {
        $mySOAP = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <selectcont xmlns="http://tempuri.org/">
                        <Sxml>
                            <![CDATA[' . base64_encode($xml) . ']]>
                        </Sxml>
                    </selectcont>
                </soap:Body>
            </soap:Envelope>';
        return $this->SendToWS($mySOAP, $url, $Actions);
    }


    public function InsertInToWS($xml, $url, $Actions = array()) {
        FB::INFO($xml, '______________________________________________INSERT RESPONSE');
        $this->LogID = $this->CreaTransLog('INSERT', $xml);
        $mySOAP = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <insert xmlns="http://tempuri.org/">
                        <Sxml>
                            <![CDATA[' . base64_encode($xml) . ']]>
                        </Sxml>
                    </insert>
                </soap:Body>
            </soap:Envelope>';
        $WSResponse = $this->SendToWS($mySOAP, $url, $Actions);
        // $this->UpdateTransLog($LogID, $WSResponse);
        return $WSResponse;
    }

    public function UpdateWS($xml, $url, $Actions = array()) {
        $this->LogID = $this->CreaTransLog('UPDATE', $xml);
        $mySOAP = '
            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                <soap:Body>
                    <update xmlns="http://tempuri.org/">
                        <Sxml>
                            <![CDATA[' . base64_encode($xml) . ']]>
                        </Sxml>
                    </update>
                </soap:Body>
            </soap:Envelope>';
        $WSResponse = $this->SendToWS($mySOAP, $url, $Actions);
        //FB::INFO($WSResponse, '______________________________________________UPDATE RESPONSE');
        //$this->UpdateTransLog($LogID, $WSResponse);
        return $WSResponse;
    }



    /*Actualiza Datos de la Tabla de Titular mediante el XML de $this->XMLUpdateTitular($Data);*/
    public function UpdateTitular($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = $this->GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLUpdateTitular($Data);
        FB::INFO($XMLUpdate, '______________________________XML SEND LUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();

        $this->UpdateTransLog($this->LogID, $UPDATE);
        FB::INFO($UPDATE, '____________________________________________RESPONSE UPDATE TITULAR');
    }

    /*Actualiza Datos Faltantes de la Tabla de Titular mediante el XML de $this->XMLUpdateTitularComplemento($Data);*/
    public function UpdateTitularComplemento($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLUpdateTitularComplemento($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULARComplemento');
    }

    public function CancelarAfiliados($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = SQLServerWS::XMLCancelarAfiliados($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function SuspenderAfiliados($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLSuspenderAfiliados($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function ReactivarAfiliado($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLReactivarAfiliado($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }


    public function ReactivarSocios($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLReactivarSocios($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function CancelarSocios($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLCancelarSocios($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function CancelarSocio($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLCancelarSocio($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function SuspenderSocios($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLSuspenderSocios($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    public function SuspenderSocio($Data, $Folio) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLSuspenderSocio($Data, $Folio);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdate');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________UPDATE TITULAR');
    }

    /*Actualiza Datos de la Tabla de Cobranza mediante el XML de $this->XMLUpdateCobranza*/
    public function UpdateCobranza($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLUpdateCobranza($Data);
        FB::INFO($XMLUpdate, '______________________________$XMLUpdateCOBRANZA');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________XML SEND UpdateCOBRANZA');
        //Actualiza Complementos dela Tabla Titular
        $XMLUpdateTitular = $this->XMLUpdateTitularComplemento($Data);
        $UPDATETitular = $this->UpdateWS($XMLUpdateTitular, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATETitular, '_______________________________________XML RESPONSE COMP');
    }


    /*Inserta Datos de la Tabla de Titular mediante el XML de $this->XMLInsertTitular*/
    public function InsertTitular($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');
        //$WSURL = SQLServerWS::GetURL();
        $InsertAction = "http://tempuri.org/insert";
        $XMLINSERT = $this->XMLInsertTitular($Data);
        FB::INFO($XMLINSERT, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XMLINSERT, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT TITULAR RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);
    }

    public function InsertCobranza($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA INSERT COBRANZA');
        //$WSURL = SQLServerWS::GetURL();
        $InsertAction = "http://tempuri.org/insert";
        $XMLInsertCobranza = $this->XMLInsertCobranza($Data);
        FB::INFO($XMLInsertCobranza, '______________________________$XML SEND InsertCobranza');
        $InsertCobranza = $this->InsertInToWS($XMLInsertCobranza, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($InsertCobranza, '____________________________________________XML RESPONSE');

        /*Actualiza Complementos dela Tabla Titular*/
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdateTitular = $this->XMLUpdateTitularComplemento($Data);
        $UPDATETitular = $this->UpdateWS($XMLUpdateTitular, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATETitular, '_______________________________________XML RESPONSE COMPLEMENTO');
    }


    public function InsertSocios($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA INSERT COBRANZA');
        //$WSURL = SQLServerWS::GetURL();
        $InsertAction = "http://tempuri.org/insert";
        $XMLInsertSocios = $this->XMLInsertSocios($Data);
        FB::INFO($XMLInsertSocios, '______________________________$XML SEND InsertSocios');
        $InsertSocios = $this->InsertInToWS($XMLInsertSocios, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($InsertSocios, '____________________________________________XML RESPONSE');
    }

    public function UpdateSocios($Data) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA SOCIOS');
        //$WSURL = SQLServerWS::GetURL();
        $UpdateAction = "http://tempuri.org/update";
        $XMLUpdate = $this->XMLUpdateSocios($Data);
        FB::INFO($XMLUpdate, '______________________________$XML SEND Update Socios');
        $UPDATE = $this->UpdateWS($XMLUpdate, $this->ServiceURL, array('SOAPAction: ' . $UpdateAction))->saveXML();
        FB::INFO($UPDATE, '____________________________________________XML RESPONSE');
    }




    /**
     * @todo
     * Inserta Datos de PaymentMethod $this->isCatalogo mediante el XML
     *
     * **/
    public function InsertPaymentMethod($Data, $Table, $TableID, $LasRHID) {
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        FB::INFO($Data, '________________________ARRAY DATA');

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="paymentmethods">
                <Descripcion weberp="paymentname" prime="1" >' . $Data['paymentname'] . '</Descripcion>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT PaymentMethod RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE paymentmethods SET
            arid = :arid
        WHERE paymentid = :paymentid";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':paymentid' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }

    }


    /**
     * @todo
     * Inserta Datos de rh_tipotarjetas $this->isCatalogo mediante el XML
    **/
    public function InsertTipoTarjetas($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_TipoTarjetaCredito

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_tipotarjetas">
                <Descripcion weberp="tipotarjeta" prime="1" >' . $Data['tipotarjeta'] . '</Descripcion>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT TIPO TARJETAS RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_tipotarjetas SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }


    /**
     * @todo
     * Inserta Datos de rh_cobradores $this->isCatalogo mediante el XML
     * | id | nombre                     | comision | zona  | activo | empresa | reasigna | cobori |
    **/
    public function InsertCobradores($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        return true;
        //CZA_TipoTarjetaCredito

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_cobradores">
                <Nombre weberp="nombre" prime="1" >' . $Data['nombre'] . '</Nombre>
                <Zona weberp="zona" >' . $Data['zona'] . '</Zona>
                <Comision weberp="comision" >' . $Data['comision'] . '</Comision>
                <IdUsuario weberp="userid" >2</IdUsuario>
                <Activo weberp="activo" >' . $Data['activo'] . '</Activo>
                <reasigna weberp="reasigna" >' . $Data['reasigna'] . '</reasigna>
                <coborig weberp="cobori" >' . $Data['cobori'] . '</coborig>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT COBRADOR RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_cobradores SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }

    /**
     * @todo
     * Inserta Datos de rh_comisionistas $this->isCatalogo mediante el XML
     * | id | comisionista                 | coordina_id | activo |
    **/
    public function InsertAsesores($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_TipoTarjetaCredito

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_comisionistas">
                <Nombre weberp="comisionista" prime="1" >' . $Data['comisionista'] . '</Nombre>
                <IdCoordinador weberp="coordina_id" >1</IdCoordinador>
                <IdEsquemaComision weberp="" >1</IdEsquemaComision>
                <MetaVentasMes weberp="" >1</MetaVentasMes>
                <MetaVidasMes weberp="" >1</MetaVidasMes>
                <MetaProspeccion weberp="" >1</MetaProspeccion>
                <IdUsuario weberp="userid" >2</IdUsuario>
                <Activo weberp="activo" >' . $Data['activo'] . '</Activo>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT ASESOR RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_comisionistas SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }


    /**
     * @todo
     * Inserta Datos de rh_frecuenciapago $this->isCatalogo mediante el XML
     * | id | comisionista                 | coordina_id | activo |
    **/
    public function InsertFrecuenciaPago($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_FrecuenciaPago

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_frecuenciapago">
                <Descripcion weberp="frecuencia" prime="1" >' . $Data['frecuencia'] . '</Descripcion>
                <Dias weberp="dias" >' . $Data['dias'] . '</Dias>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT FRECUENCIA RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_frecuenciapago SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }


    /**
     * @todo
     * Inserta Datos de rh_estados $this->isCatalogo mediante el XML
     * | id | comisionista                 | coordina_id | activo |
    **/
    public function InsertEstados($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_Estado

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_estados">
                <Descripcion weberp="estado" prime="1" >' . $Data['estado'] . '</Descripcion>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT ESTADOS RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_estados SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }


    /**
     * @todo
     * Inserta Datos de rh_municipios $this->isCatalogo mediante el XML
     * | id | comisionista                 | coordina_id | activo |
    **/
    public function InsertMunicipios($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_Municipio

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_municipios">
                <Descripcion weberp="municipio" prime="1" >' . $Data['municipio'] . '</Descripcion>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT ESTADOS RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_municipios SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }

    /**
     * @todo
     * Inserta Datos de rh_municipios $this->isCatalogo mediante el XML
     * | id | comisionista                 | coordina_id | activo |
    **/
    public function InsertMotivosCancelacion($Data, $Table, $TableID, $LasRHID){
        /* REGRESAMOS TRUE POR PROBLEMAS CON EL WS - DANIEL VILLARREAL 1 DE JUNIO DEL 2016 */
        return true;
        /* TERMINA */
        //CZA_Municipio

        $this->CatalogoName = $Table;
        $this->CatalogID = $TableID;
        $this->LasRHID = $LasRHID;

        $XmlInsert = ('
        <row>
            <'. $this->CatalogoName .' id="'. $this->CatalogID .'" weberp="rh_motivos_cancelacion">
                <Descripcion weberp="motivo" prime="1" >' . $Data['motivo'] . '</Descripcion>
                <IdUsuario weberp="userid" >2</IdUsuario>
           </'. $this->CatalogoName .'>
        </row>');
        $InsertAction = "http://tempuri.org/insert";

        FB::INFO($XmlInsert, '______________________________$XML SEND INSERT');
        $INSERT = $this->InsertInToWS($XmlInsert, $this->ServiceURL, array('SOAPAction: ' . $InsertAction))->saveXML();
        FB::INFO($INSERT, '____________________________________________INSERT ESTADOS RESPONSE');
        $this->UpdateTransLog($this->LogID, $INSERT);

        $UpdateLog = "UPDATE rh_motivos_cancelacion SET
            arid = :arid
        WHERE id = :id";
        $UpdateLogParameters = array(
            ':arid' => $this->LasARID,
            ':id' => $this->LasRHID
        );
        try {
            Yii::app()->db->createCommand($UpdateLog)->execute($UpdateLogParameters);
        } catch (Exception $e) {
            FB::INFO($e->getMessage(),'___ERROR ACTUALIZAR CATALOGO: ');
        }
    }

    /************************TRADUCCION CATALOGOS******************************/


    /**
     * @todo
     * Obtiene ID Municipio de AR
    **/
    public function GetMotivoCancelacionAR($IdMotivoCancelacion){
        
        //$this->GetMotivoCancelacionAR($IdMotivoCancelacion)
        $_IdMotivoCancelacion = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_motivos_cancelacion")
        ->where("id = :id", array(":id" => $IdMotivoCancelacion))->queryAll();
        FB::INFO('OK FUNCTION');
        return $_IdMotivoCancelacion[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID Municipio de AR
    **/
    public function GetMunicipioAR($IdMunicipio){
        FB::INFO($IdMunicipio,'____MUN');
        //$this->GetMunicipioAR($IdMunicipio)
        $_IdMunicipio = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_municipios")
        ->where("municipio = :municipio", array(":municipio" => $IdMunicipio))->queryAll();
        FB::INFO($_IdMunicipio,'OK FUNCTION');
        return $_IdMunicipio[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID Estado de AR
    **/
    public function GetEstadoAR($IdEstado){
        //$this->GetEstadoAR($IdEstado)
        FB::INFO($IdEstado,'____ESTADO');
        $_IdEstado = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_estados")
        ->where("estado = :estado", array(":estado" => $IdEstado))->queryAll();
        FB::INFO($_IdEstado,'OK FUNCTION');
        return $_IdEstado[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID FrecuenciaPago de AR
    **/
    public function GetFrecuenciaPagoAR($IdFrecuenciaPago){
        //$this->GetFrecuenciaPagoAR($IdFrecuenciaPago)
        $_IdFrecuenciaPago = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_frecuenciapago")
        ->where("id = :id", array(":id" => $IdFrecuenciaPago))->queryAll();
        FB::INFO('OK FUNCTION');
        return $_IdFrecuenciaPago[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID Asesor de AR
    **/
    public function GetAsesorAR($IdAsesor){
        $_IdAsesor = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_comisionistas")
        ->where("id = :id", array(":id" => $IdAsesor))->queryAll();
        FB::INFO('OK FUNCTION');
        return $_IdAsesor[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID FormaPago de AR
    **/
    public function GetFormaPagoAR($IdFormaPago){
        $_IdFormaPago = Yii::app()->db->createCommand()->select("arid")
        ->from("paymentmethods")
        ->where("paymentid = :paymentid", array(":paymentid" => $IdFormaPago))->queryAll();
        FB::INFO('OK FUNCTION');
        return $_IdFormaPago[0]['arid'];
    }

    /**
     * @todo
     * Obtiene ID Cobrador de AR
    **/
    public function GetCobradorAR($IdCobrador){
        //$this->GetCobradorAR($Data['cobrador']);
        $_IdCobrador = Yii::app()->db->createCommand()->select("arid")
        ->from("rh_cobradores")
        ->where("id = :id", array(":id" => $IdCobrador))->queryAll();
        return $_IdCobrador[0]['arid'];
    }

    /************************TRADUCCION CATALOGOS******************************/



    /*Inseta Registro en la Tabla del Titular*/
    public function XMLInsertTitular($Data){

        if($Data['sexo'] == 'MASCULINO'){
            $Data['sexo'] = "M";
        }else{
            $Data['sexo'] = "F";
        }

        switch ($Data['movimientos_afiliacion']) {
            case "Activo" :
                $Status = 1;
                break;
            case "Cancelado" :
                $Status = 2;
                break;
            case "Suspendido" :
                $Status = 3;
                break;
            default :
                $Status = 1;
                break;
        }

        if (!empty($Data['email'])) {
            $Email = explode(",", $Data['email']);
        }

        if(!empty($Data['tipopersona'])){
            if($Data['tipopersona']=='FISICA'){
                $Data['tipopersona']=2;
                $RAZONSOC = "";
                $NOMCOMERC = "";
            }else{
                $Data['tipopersona']=1;
                $Data['name'] = "";
                $RAZONSOC = $Data['name2'];
                $NOMCOMERC = $Data['nombre_empresa'];
                if(empty($RAZONSOC)){
                    $RAZONSOC = $Data['name'];
                }
            }
        }

        if(!empty($Data['costo_total'])){
            $Data['costo_total'] = 0;
        }

        $XmlInsert = ('
        <row>
            <CCM_Foltitular weberp="rh_titular">
                <folio weberp="folio" prime="1" >' . $Data['Folio'] . '</folio>
                <fecha weberp="fecha_ingreso" >' . $Data['fecha_ingreso'] . '</fecha>
                <IdProducto weberp="null">1</IdProducto>
                <IdAsesor weberp="t.asesor">' . $this->GetAsesorAR($Data['asesor']) . '</IdAsesor>
                <IdCoordinador weberp="null">1</IdCoordinador>
                <IdAsesorLaboratorio weberp=" ">1</IdAsesorLaboratorio>
                <APELLIDOS weberp="t.apellidos">' . $Data['apellidos'] . '</APELLIDOS>
                <NOMBRES weberp="t.name">' . $Data['name'] . '</NOMBRES>
                <RAZONSOC weberp="t.name2">' . $RAZONSOC . '</RAZONSOC>
                <NOMCOMERC weberp="t.nombre_empresa">' . $NOMCOMERC . '</NOMCOMERC>
                <SEXO weberp="t.sexo">' . $Data['sexo'] . '</SEXO>
                <RFC weberp="t.taxref">' . $Data['taxref'] . '</RFC>
                <CORREO weberp="t.email">' . $Email[0] . '</CORREO>
                <TELEFONO1 weberp="t.rh_tel">' . $Data['rh_tel'] . '</TELEFONO1>
                <TELEFONO2 weberp="t.rh_tel2">' . $Data['rh_tel2'] . '</TELEFONO2>
                <CALLE weberp="t.address1">' . $Data['address1'] . '</CALLE>
                <NUMERO weberp="t.address2">' . $Data['address3'] . '</NUMERO>
                <COLONIA weberp="t.address4">' . $Data['address4'] . '</COLONIA>
                <SECTOR weberp="t.address5">' . $Data['address5'] . '</SECTOR>
                <ENTRECALLE weberp="t.address6">' . $Data['address6'] . '</ENTRECALLE>
                <IdMunicipio weberp="t.address7">' . $this->GetMunicipioAR($Data['address7']) . '</IdMunicipio>
                <IdEstado weberp="t.address8">' . $this->GetEstadoAR($Data['address8']) . '</IdEstado>
                <CUADRANTE1 weberp="t.cuadrante1 ">' . $Data['cuadrante1'] . '</CUADRANTE1>
                <CUADRANTE2 weberp="t.cuadrante2">' . $Data['cuadrante2'] . '</CUADRANTE2>
                <CUADRANTE3 weberp="t.cuadrante3">' . $Data['cuadrante3'] . '</CUADRANTE3>
                <IdMotivoCancelacion weberp="t.motivo_cancelacion">7</IdMotivoCancelacion>
                <COSTO weberp="t.costo">' . $Data['costo_total'] . '</COSTO>
                <IdIdentificacion weberp="c.identificacion">1</IdIdentificacion>
                <TIPOPERSON weberp="t.tipopersona">' .$Data['tipopersona']. '</TIPOPERSON>
                <FECHASUSP1 weberp="">1900-01-01 </FECHASUSP1>
                <FECHASUSP2 weberp="">1900-01-01 </FECHASUSP2>
                <NOMFAM weberp=""></NOMFAM>
                <PARENTESCO weberp="">0</PARENTESCO>
                <TELFAM weberp="">0</TELFAM>
                <ESP weberp="">0</ESP>
                <TARIFA weberp="">0</TARIFA>
                <TARIFAINS weberp="">0.00</TARIFAINS>
                <FECHAULTAU weberp="t.fecha_ultaum">' . $Data['fecha_ultaum'] . '</FECHAULTAU>
                <TIPTARIFA weberp="">0</TIPTARIFA>
                <AUMENTO weberp="">0</AUMENTO>
                <CP weberp="t.address10">' . $Data['address10'] . '</CP>
                <STATUS weberp="">' . $Status . '</STATUS>
                <MONTORECIB weberp="">0</MONTORECIB>
                <PROMOCION weberp="">0</PROMOCION>
                <SINCLUIDOS weberp="t.servicios_seleccionados">' . $Data['servicios_seleccionados3'] . '</SINCLUIDOS>
                <ACT weberp="">0</ACT>
                <CONTACTO weberp="">' . $Data['contacto'] . '</CONTACTO>
                <FECHAUC weberp="">1900-01-01 </FECHAUC>
                <CONTACTA weberp="">0</CONTACTA>
                <FCANAUT weberp="">1900-01-01 </FCANAUT>
                <FMCANAUT weberp="">1900-01-01 </FMCANAUT>
                <CANAUT weberp="">0</CANAUT>
                <NOEMPLEA weberp="">0</NOEMPLEA>
                <FULTREA weberp="">1900-01-01 </FULTREA>
                <VIGINIMES weberp="">0</VIGINIMES>
                <VIGINIANO weberp="">0</VIGINIANO>
                <VIGFINMES weberp="">0</VIGFINMES>
                <VIGFINANO weberp="">0</VIGFINANO>
                <CLAVEPROMO weberp=" ">0</CLAVEPROMO>
                <DIASCTO weberp=" ">0</DIASCTO>
                <FOLIOCAN weberp=" ">0</FOLIOCAN>
                <FECULAUMAN weberp=" ">1900-01-01</FECULAUMAN>
                <TARIFAANT weberp=" ">0</TARIFAANT>
                <IdUsuario weberp=" ">2</IdUsuario>
                <LIM_SERV weberp="t.serviciolimitado">0</LIM_SERV>
                <LIM_MES weberp=" ">0</LIM_MES>
                <LIM_COSTEX weberp="t.costo_servicioextra">' . $Data['costo_servicioextra'] . '</LIM_COSTEX>
                <ENF weberp="t.enfermeria">' . $Data['enfermeria'] . '</ENF>
                <COSTOENF weberp="t.costoenfermeria">' . intval($Data['costoenfermeria']) . '</COSTOENF>
                <CUENTA_SAT weberp=" "></CUENTA_SAT>
                <CORREO2 weberp=" ">' . $Email[1] . '</CORREO2>
                <CORREO3 weberp=" ">' . $Email[2] . '</CORREO3>
                <CORREO4 weberp=" ">' . $Email[3] . '</CORREO4>
                <METODOPAGO weberp=" "></METODOPAGO>
                <IdFrecuenciaPago weberp=" ">1</IdFrecuenciaPago>
                <IdFormaPago weberp=" ">1</IdFormaPago>
                <TipoFolio weberp=" ">1</TipoFolio>
           </CCM_Foltitular>
        </row>');

        return $XmlInsert;

    }


    /*XMl que se envia al WS con la Info a Actualizar*/
    public function XMLUpdateTitular($Data) {

        if($Data['sexo'] == 'MASCULINO'){
            $Data['sexo'] = "M";
        }else{
            $Data['sexo'] = "F";
        }

        switch ($Data['movimientos_afiliacion']) {
            case "Activo" :
                $Status = 1;
                break;
            case "Cancelado" :
                $Status = 2;
                break;
            case "Suspendido" :
                $Status = 3;
                break;
            default :
                $Status = 1;
                break;
        }

        if (!empty($Data['email'])) {
            $Email = explode(",", $Data['email']);
        }


        if(!empty($Data['tipopersona'])){
            if($Data['tipopersona']=='FISICA'){
                $Data['tipopersona']=2;
                $RAZONSOC = "";
                $NOMCOMERC = "";
            }else{
                $Data['tipopersona']=1;
                $Data['name'] = "";
                $RAZONSOC = $Data['name2'];
                $NOMCOMERC = $Data['nombre_empresa'];
                if(empty($RAZONSOC)){
                    $RAZONSOC = $Data['name'];
                }
            }
        }



        if($Data['fecha_ultaum']=='0000-00-00'){
            $Data['fecha_ultaum']='1900-01-01';
        }


        $Data['motivo_cancelacion'] = $this->GetMotivoCancelacionAR($Data['motivo_cancelacion']);
        if(empty($Data['motivo_cancelacion'])){
            $Data['motivo_cancelacion']=0;
        }


        $XMLUpdate = ('
        <row>
         <CCM_Foltitular weberp="rh_titular
         " where="folio=' . $Data['Folio'] . '">
            <fecha weberp="fecha_ingreso" >' . $Data['fecha_ingreso'] . '</fecha>
            <IdAsesor weberp="t.asesor">' . $this->GetAsesorAR($Data['asesor']) . '</IdAsesor>
            <APELLIDOS weberp="t.apellidos">' . $Data['apellidos'] . '</APELLIDOS>
            <NOMBRES weberp="t.name">' . $Data['name'] . '</NOMBRES>
            <RAZONSOC weberp="t.name2">' . $RAZONSOC . '</RAZONSOC>
            <NOMCOMERC weberp="t.nombre_empresa">' . $NOMCOMERC . '</NOMCOMERC>
            <SEXO weberp="t.sexo">' . $Data['sexo'] . '</SEXO>
            <RFC weberp="t.taxref">' . $Data['taxref'] . '</RFC>
            <CORREO weberp="t.email">' . $Data['email'] . '</CORREO>
            <TELEFONO1 weberp="t.rh_tel">' . $Data['rh_tel'] . '</TELEFONO1>
            <TELEFONO2 weberp="t.rh_tel2">' . $Data['rh_tel2'] . '</TELEFONO2>
            <CALLE weberp="t.address1">' . $Data['address1'] . '</CALLE>
            <NUMERO weberp="t.address2">' . $Data['address2'] . '</NUMERO>
            <COLONIA weberp="t.address4">' . $Data['address4'] . '</COLONIA>
            <SECTOR weberp="t.address5">' . $Data['address5'] . '</SECTOR>
            <ENTRECALLE weberp="t.address6">' . $Data['address6'] . '</ENTRECALLE>
            <IdMunicipio weberp="t.address7">' . $this->GetMunicipioAR($Data['address7']) . '</IdMunicipio>
            <IdEstado weberp="t.address8">' . $this->GetEstadoAR($Data['address8']) . '</IdEstado>
            <CUADRANTE1 weberp="t.cuadrante1 ">' . $Data['cuadrante1'] . '</CUADRANTE1>
            <CUADRANTE2 weberp="t.cuadrante2">' . $Data['cuadrante2'] . '</CUADRANTE2>
            <CUADRANTE3 weberp="t.cuadrante3">' . $Data['cuadrante3'] . '</CUADRANTE3>
            <IdMotivoCancelacion weberp="t.motivo_cancelacion">7</IdMotivoCancelacion>
            <COSTO weberp="t.costo">' . $Data['costo_total'] . '</COSTO>
            <TIPOPERSON weberp="t.tipopersona">' . $Data['tipopersona'] . '</TIPOPERSON>
            <FECHAULTAU weberp="t.fecha_ultaum">' . $Data['fecha_ultaum'] . '</FECHAULTAU>
            <CP weberp="t.address10">' . $Data['address10'] . '</CP>
            <STATUS weberp="t.movimientos_afiliacion">' . $Status . '</STATUS>
            <SINCLUIDOS weberp="t.servicios_seleccionados">' . $Data['servicios_seleccionados3'] . '</SINCLUIDOS>
            <CONTACTO weberp="contacto">' . $Data['contacto'] . '</CONTACTO>
            <IdUsuario weberp=" ">2</IdUsuario>
            <LIM_SERV weberp="t.serviciolimitado"></LIM_SERV>
            <LIM_COSTEX weberp="t.costo_servicioextra">' . $Data['costo_servicioextra'] . '</LIM_COSTEX>
            <ENF weberp="t.enfermeria">' . $Data['enfermeria'] . '</ENF>
            <COSTOENF weberp="t.costoenfermeria">' . intval($Data['costoenfermeria']) . '</COSTOENF>
          </CCM_Foltitular>
        </row>');

        return $XMLUpdate;
    }

    /**
     * @Todo
     * Complementa los Datos de rh_titular
     * los datos faltantes estan en la table de rh_cobranza
     * $Data = array('Folio' => 123,
     *               'stockid' => 'FAMILIA',
     *               'cuenta_sat' => '12345',
     *               'identificacion' => '1',
     *               'frecuencia_pago' => '1',
     *               'paymentid' => '1');
    */
    public function XMLUpdateTitularComplemento($Data) {

        $ProductsT = array(
            "AFIL15" => 1,
            "AFIL18" => 10,
            "CONTINUACION HOGAR" => 11,
            "GNP PORVENIR" => 12,
            "CONT. FAMILIA PROTEG" => 16,
            "AFIL22" => 13,
            "AFIL16" => 17,
            "AFIL30" => 2,
            "AFIL17" => 21,
            "AFIL8" => 22,
            "AFIL24" => 23,
            "AFIL23" => 24,
            "AFIL3" => 27,
            "AFIL2" => 28,
            "AFIL10" => 29,
            "AFIL5" => 3,
            "AFIL25" => 62,
            "AFIL21" => 4,
            "AFIL13" => 5,
            "AFIL9" => 6,
            "AFIL11" => 7,
            "AFIL27" => 8,
            "AFIL25" => 59,
            "AFIL19" => 9,
            "AFIL26" => 61,
            "AFIL5" => 32,
            "AFIL14" => 31
            );
/*
+-----------------+---------------------+
| stockid         | description         |
+-----------------+---------------------+
| AFIL1           | ANUAL INSEN 71+     |
| AFIL10          | CORTESIA BEBES      |
| AFIL11          | CORTESIA MEDICOS    |
| AFIL13          | CUENTA COMPAÑIA     |
| AFIL14          | ENFERMERIAS         |
| AFIL15          | FAMILIA             |
| AFIL16          | FAMILIA 5 X 4       |
| AFIL17          | FAMILIA BASICO      |
| AFIL18          | HOGAR PROTEGIDO     |
| AFIL19          | INTERCAMBIO         |
| AFIL2           | ANUAL INSEN 60 A 70 |
| AFIL20          | OMNIBUS PROTEGIDO   |
| AFIL21          | PLAN INSTITUCIONAL  |
| AFIL22          | PLAN PEQ. COMERCIOS |
| AFIL23          | PROMOCION 2 X 1     |
| AFIL24          | PROMOCION 3 X 2     |
| AFIL25          | PROMOCION DE MEDIOS |
| AFIL26          | PROMOCION DICIEMBRE |
| AFIL27          | PROMOCION MADRES    |
| AFIL29          | TAXI PROTEGIDO      |
| AFIL3           | ANUAL INSEN 71+     |
| AFIL30          | ZONA PROTEGIDA      |
| AFIL4           | AR SALUD INTEGRAL   |
| AFIL5           | AUTO  PROTEGIDO     |
| AFIL8           | C-1                 |
| AFIL9           | CORTESIA            |
| AUTO  PROTEGIDO | AUTO  PROTEGIDO     |
+-----------------+---------------------+
*/



        $XMLUpdateTitular = ('
        <row>
            <CCM_Foltitular weberp="rh_titular" where="folio=' . $Data['Folio'] . '">
                <IdProducto weberp="c.stockid" >' . $ProductsT[$Data['stockid']]  . '</IdProducto>
                <CUENTA_SAT weberp="c.cuenta_sat">' . $Data['cuenta_sat'] . '</CUENTA_SAT>
                <IdIdentificacion weberp="c.identificacion">1</IdIdentificacion>
                <IdFrecuenciaPago weberp="c.frecuencia_pago">1</IdFrecuenciaPago>
                <IdFormaPago weberp="c.paymentid">' . $this->GetFormaPagoAR($Data['paymentid']) . '</IdFormaPago>
            </CCM_Foltitular>
        </row>');

        return $XMLUpdateTitular;
    }

    public function XMLUpdateCobranza($Data) {

        if ($Data['dias_cobro']=='Por Dia') {
            $IdTipoCobro = 1;
            $DiaSemanaCobro = $Data['dias_cobro_dia'];
            $DiasDeCobro = "";
        } else {
            $IdTipoCobro = 2;
            $DiasDeCobro = $Data['dias_cobro_dia'];
            $DiaSemanaCobro = "";
        }

        if ($Data['dias_revision']=='Por Dia') {
            $IdTipoRevision = 1;
            $DiasSemanaRevision = $Data['dias_revision_dia'];
            $DiasDeRevision = "";
        } else {
            $IdTipoRevision = 2;
            $DiasDeRevision = $Data['dias_revision_dia'];
            $DiasSemanaRevision = "";
        }

         if (!empty($Data['email'])) {
            $Email = explode(",", $Data['email']);
        }


        $XMLUpdate = ('
        <row>
         <CCM_FolCobranza weberp="rh_cobranza" where="FOLIO=' . $Data['Folio'] . '">
            <Calle weberp="c.address1">' . $Data['address1'] . '</Calle>
            <Numero weberp="c.address2">' . $Data['address2'] . '</Numero>
            <Colonia weberp="c.address4">' . $Data['address4'] . '</Colonia>
            <Sector weberp="c.address5">' . $Data['address5'] . '</Sector>
            <EntreCalles weberp="c.address6">' . $Data['address6'] . '</EntreCalles>
            <IdMunicipio weberp="c.address7">' . $this->GetMunicipioAR($Data['address7']) . '</IdMunicipio>
            <IdEstado weberp="c.address8">' . $this->GetEstadoAR($Data['address8']) . '</IdEstado>
            <CP weberp="c.address10">' . $Data['address10'] . '</CP>
            <Cuadrante1 weberp="c.cuadrante1 ">' . $Data['cuadrante1'] . '</Cuadrante1>
            <Cuadrante2 weberp="c.cuadrante2">' . $Data['cuadrante2'] . '</Cuadrante2>
            <Cuadrante3 weberp="c.cuadrante3">' . $Data['cuadrante3'] . '</Cuadrante3>
            <Telefono weberp="c.rh_tel">' . $Data['rh_tel'] . '</Telefono>
            <TelefonoAlternativo weberp="c.rh_tel2">' . $Data['rh_tel2'] . '</TelefonoAlternativo>
            <Email weberp="c.email">' . $Email[0] . '</Email>
            <Email1 weberp=" ">' . $Email[1] . '</Email1>
            <Email2 weberp=" ">' . $Email[2] . '</Email2>
            <Email3 weberp=" ">' . $Email[3] . '</Email3>
            <EncargadoDePagos weberp="c.encargado_pagos">' . $Data['encargado_pagos'] . '</EncargadoDePagos>
            <IdFrecuenciaPago weberp="c.frecuencia_pago">' . $this->GetFrecuenciaPagoAR($Data['frecuencia_pago']) . '</IdFrecuenciaPago>
            <IdFormaPago weberp="c.paymentid">' . $this->GetFormaPagoAR($Data['paymentid']) . '</IdFormaPago>
            <Zona weberp="c.zona">' . $Data['zona'] . '</Zona>
            <IdCobrador weberp="c.cobrador">' . $this->GetCobradorAR($Data['cobrador']) . '</IdCobrador>
            <NCuenta weberp="c.cuenta">' . $Data['cuenta'] . '</NCuenta>
            <FechaVencimiento weberp="c.vencimiento"></FechaVencimiento>
            <CuentaSAT weberp="c.cuenta_sat">' . $Data['cuenta_sat'] . '</CuentaSAT>
            <NumeroPlastico weberp="c.num_plastico"></NumeroPlastico>
            <IdTipoTarjeta weberp="c.tipo_tarjeta"></IdTipoTarjeta>
            <TipoCuenta weberp="c.tipo_cuenta"></TipoCuenta>
            <IdIdentificacion weberp="c.identificacion">1</IdIdentificacion>
            <FechaCorte weberp="c.fecha_corte">' . $Data['fecha_corte'] . '</FechaCorte>
            <FolioAsociado weberp="c.folio_asociado">' . $Data['folio_asociado'] . '</FolioAsociado>

            <IdTipoCobro weberp="c.dias_cobro">' . $IdTipoCobro . '</IdTipoCobro>
            <DiasDeCobro weberp="c.dias_cobro_dia">' . $DiasDeCobro . '</DiasDeCobro>
            <DiaSemanaCobro weberp="c.dias_cobro_dia">' . $DiaSemanaCobro . '</DiaSemanaCobro>
            <HorarioInicioCobro weberp="c.cobro_datefrom">' . $Data['cobro_datefrom'] . '</HorarioInicioCobro>
            <HorarioFinCobro weberp="c.cobro_dateto">' . $Data['cobro_dateto'] . '</HorarioFinCobro>

            <IdTipoRevision weberp="c.dias_revision">' . $IdTipoRevision . '</IdTipoRevision>
            <DiasDeRevision weberp="c.dias_revision_dia">' . $DiasDeRevision . '</DiasDeRevision>
            <DiasSemanaRevision weberp="c.dias_revision_dia">' . $DiasSemanaRevision . '</DiasSemanaRevision>
            <HoraInicioRevision weberp="c.revision_datefrom">' . $Data['revision_datefrom'] . '</HoraInicioRevision>
            <HoraFinRevision weberp="c.revision_dateto">' . $Data['revision_dateto'] . '</HoraFinRevision>
          </CCM_FolCobranza>
        </row>');
        return $XMLUpdate;
    }


     public function XMLInsertCobranza($Data){


        if (!empty($Data['email'])) {
            $Email = explode(",", $Data['email']);
        }

         if ($Data['dias_cobro']=='Por Dia') {
            $IdTipoCobro = 1;
            $DiaSemanaCobro = $Data['dias_cobro_dia'];
            $DiasDeCobro = "";
        } else {
            $IdTipoCobro = 2;
            $DiasDeCobro = $Data['dias_cobro_dia'];
            $DiaSemanaCobro = "";
        }

        if ($Data['dias_revision']=='Por Dia') {
            $IdTipoRevision = 1;
            $DiasSemanaRevision = $Data['dias_revision_dia'];
            $DiasDeRevision = "";
        } else {
            $IdTipoRevision = 2;
            $DiasDeRevision = $Data['dias_revision_dia'];
            $DiasSemanaRevision = "";
        }


        $XmlInsert = ('
        <row>
            <CCM_FolCobranza weberp="rh_cobranza">
            <FOLIO weberp="folio" prime="1" >' . $Data['Folio'] . '</FOLIO>
            <Calle weberp="c.address1">' . $Data['address1'] . '</Calle>
            <Telefono weberp="c.rh_tel">' . $Data['rh_tel'] . '</Telefono>
            <TelefonoAlternativo weberp="c.rh_tel2">' . $Data['rh_tel2'] . '</TelefonoAlternativo>
            <EntreCalles weberp="c.address6">' . $Data['address6'] . '</EntreCalles>
            <Numero weberp="c.address2">' . $Data['address2'] . '</Numero>
            <CP weberp="c.address10">' . $Data['address10'] . '</CP>
            <Colonia weberp="c.address4">' . $Data['address4'] . '</Colonia>
            <Sector weberp="c.address5">' . $Data['address5'] . '</Sector>
            <IdMunicipio weberp="c.address7">' . $this->GetMunicipioAR($Data['address7']) . '</IdMunicipio>
            <IdEstado weberp="c.address8">' . $this->GetEstadoAR($Data['address8']) . '</IdEstado>
            <Cuadrante1 weberp="c.cuadrante1">' . $Data['cuadrante1'] . '</Cuadrante1>
            <Cuadrante2 weberp="c.cuadrante2">' . $Data['cuadrante2'] . '</Cuadrante2>
            <Cuadrante3 weberp="c.cuadrante3">' . $Data['cuadrante3'] . '</Cuadrante3>
            <IdCobrador weberp="c.cobrador">' . $this->GetCobradorAR($Data['cobrador']) . '</IdCobrador>
            <IdFormaPago weberp="c.paymentid">' . $this->GetFormaPagoAR($Data['paymentid']) . '</IdFormaPago>
            <NCuenta weberp="c.cuenta">' . $Data['cuenta'] . '</NCuenta>
            <IdFrecuenciaPago weberp="c.frecuencia_pago">' . $this->GetFrecuenciaPagoAR($Data['frecuencia_pago']) . '</IdFrecuenciaPago>
            <RequiereFacturaFisica weberp="c.factura_fisica">' . $Data['factura_fisica'] . '</RequiereFacturaFisica>
            <Email weberp="c.email">' . $Email[0] . '</Email>
            <Email1 weberp="">' . $Email[1] . '</Email1>
            <Email2 weberp="">' . $Email[2] . '</Email2>
            <Email3 weberp="">' . $Email[3] . '</Email3>
            <EncargadoDePagos weberp="c.encargado_pagos">' . $Data['encargado_pagos'] . '</EncargadoDePagos>
            <Zona weberp="c.zona">' . $Data['zona'] . '</Zona>
            <FechaVencimiento weberp=""></FechaVencimiento>
            <NumeroPlastico weberp=""></NumeroPlastico>
            <IdTipoTarjeta weberp=""></IdTipoTarjeta>
            <TipoCuenta weberp="c.tipo_cuenta">' . $Data['tipo_cuenta'] . '</TipoCuenta>
            <CuentaSAT weberp="c.cuenta_sat">' . $Data['cuenta_sat'] . '</CuentaSAT>
            <IdIdentificacion weberp="c.identificacion">1</IdIdentificacion>
            <FechaCorte weberp="c.fecha_corte">' . $Data['fecha_corte'] . '</FechaCorte>
            <FolioAsociado weberp="c.folio_asociado">' . $Data['folio_asociado'] . '</FolioAsociado>
            <IdTipoCobro weberp="c.dias_cobro">' . $IdTipoCobro . '</IdTipoCobro>
            <IdTipoRevision weberp="c.dias_revision">' . $IdTipoRevision . '</IdTipoRevision>
            <HorarioInicioCobro weberp="c.cobro_datefrom">' . $Data['cobro_datefrom'] . '</HorarioInicioCobro>
            <HorarioFinCobro weberp="c.cobro_dateto">' . $Data['cobro_dateto'] . '</HorarioFinCobro>
            <DiasDeCredito weberp="c.dias_credito">' . $Data['dias_credito'] . '</DiasDeCredito>
            <NumeroDiasCobro weberp=""></NumeroDiasCobro>
            <IdUsuario weberp="">2</IdUsuario>
            <DiasDeRevision weberp="c.dias_revision">' . $DiasDeRevision . '</DiasDeRevision>
            <DiasDeCobro weberp="c.dias_cobro">' . $DiasDeCobro . '</DiasDeCobro>
            <DiaSemanaCobro weberp="c.dias_cobro_dia">' . $DiaSemanaCobro. '</DiaSemanaCobro>
            <DiasSemanaRevision weberp="c.dias_revision_dia">' . $DiasSemanaRevision . '</DiasSemanaRevision>
            <EnviaFacturaPorCorreo weberp=""></EnviaFacturaPorCorreo>
           </CCM_FolCobranza>
        </row>');
        return $XmlInsert;

     }

    public function XMLInsertSocios($Data){

        switch ($Data['movimientos_afiliacion']) {
            case "Activo" :
                $Status = 1;
                break;
            case "Cancelado" :
                $Status = 2;
                break;
            case "Suspendido" :
                $Status = 3;
                break;
            default :
                $Status = 1;
                break;
        }

        $Padecimientos = array(
                'CRISIS CONVULSIVA'=>1,
                'PROBLEMAS RENALES'=>2,
                'PROBLEMAS CONGENITOS'=>3,
                'PROBLEMAS CARDIACOS'=>4,
                'PROBLEMAS PSIQUIATRICOS'=>5,
                'HIPERTENSION ARTERIAL'=>6,
                'ASMA'=>7,
                'ALCOHOLISMO'=>8,
                'EMBOLIA CEREBRAL'=>9,
                'ALERGIA A ANALGESICO'=>10,
                'ALERGIA A ANTIBIOTICO'=>11,
                'ANGINA DE PECHO'=>12,
                'INFARTO PREVIO'=>13,
                'FUMADOR'=>14,
                'DIABETES'=>15,
                'CANCER'=>16,
                'SIDA'=>17,
                'ENF. INFECCIOSA'=>18,
                'ENF. PULMONARES'=>19,
                'ENF. DEL HIGADO.'=>20,
                'ALERGIA RESPIRATORIA'=>21,
                'CIRUGIAS PREVIAS'=>22,
                'PROBLEMA OCULAR'=>23,
                'PROBLEMA AUDITIVO'=>24,
                'SANGRADOS FRECUENTES'=>25,
                'MEDICAMENTOS HAB.'=>26,
                'OTROS'=>27,
                'NINGUNO'=>28,
            );

        if($Data['sexo'] == 'MASCULINO'){
            $Data['sexo'] = "1";
        }else{
            $Data['sexo'] = "2";
        }

        $AntecedentesClinicos=json_decode($Data['antecedentes_clinicos'], 1);
        FB::INFO($Data['antecedentes_clinicos'], 'antecedentes_clinicos 1');
        FB::INFO($AntecedentesClinicos, 'antecedentes_clinicos 2');

        $Padece = "0";

        foreach($Padecimientos as $nombre  => $id){
            if(in_array($nombre, $AntecedentesClinicos)){
                $Padece .= "-".$id;
            }
        }

        if(!empty($AntecedentesClinicos['otros'])){
            $Padece .= "-27";
        }
         FB::INFO($Padece, 'Padece');

        $XmlInsert = ('
        <row>
            <CCM_Folsocios weberp="custbranch">
                <FOLIO weberp="folio" prime="1" >' . $Data['Folio'] . '</FOLIO>
                <TIPOSOCIO weberp="null">i</TIPOSOCIO>
                <APELLIDOS weberp="null"></APELLIDOS>
                <NOMBRES weberp="brname">' . $Data['brname'] . '</NOMBRES>
                <NOMCOMERC weberp="nombre_empresa">' . $Data['nombre_empresa'] . '</NOMCOMERC>
                <SEXO weberp="sexo">' . $Data['sexo'] . '</SEXO>
                <CALLE weberp="braddress1">' . $Data['braddress1'] . '</CALLE>
                <NUMERO weberp="braddress2">' . $Data['braddress2'] . '</NUMERO>
                <COLONIA weberp="braddress4">' . $Data['braddress4'] . '</COLONIA>
                <IdMunicipio weberp="braddress7">' . $this->GetMunicipioAR($Data['braddress7']) . '</IdMunicipio>
                <CP weberp="braddress10">' . $Data['braddress10'] . '</CP>
                <SECTOR weberp="braddress5">' . $Data['braddress5'] . '</SECTOR>
                <FECNAC weberp="fecha_nacimiento">' . $Data['fecha_nacimiento'] . '</FECNAC>
                <PADECE weberp="antecedentes_clinicos">' . $Padece . '</PADECE>
                <OTROSPAD weberp="null">' .$AntecedentesClinicos['otros'] . '</OTROSPAD>
                <IdHospital weberp="null">1</IdHospital>
                <ESTATUS weberp="movimientos_afiliacion">' . $Status . '</ESTATUS>
                <FECBAJA weberp="fecha_baja">' . $Data['fecha_baja'] . '</FECBAJA>
                <MOTBAJA></MOTBAJA>
                <ENTRECALLE weberp="braddress6">' . $Data['braddress6'] . '</ENTRECALLE>
                <CUADRANTE1 weberp="cuadrante1">' . $Data['cuadrante1'] . '</CUADRANTE1>
                <CUADRANTE2 weberp="cuadrante2">' . $Data['cuadrante2'] . '</CUADRANTE2>
                <CUADRANTE3 weberp="cuadrante3">' . $Data['cuadrante3'] . '</CUADRANTE3>
                <TELEFONO weberp="phoneno">' . $Data['phoneno'] . '</TELEFONO>
                <NOSOCIO weberp="branchcode">' . $Data['branchcode'] . '</NOSOCIO>
                <FECHA weberp="fecha_ingreso" >' . $Data['fecha_ingreso'] . '</FECHA>
                <FECHASUSP1 weberp=""></FECHASUSP1>
                <FECHASUSP2 weberp=""></FECHASUSP2>
                <MOTIVOSUSP weberp=""></MOTIVOSUSP>
                <PORCCOM weberp=""></PORCCOM>
                <TARIFAZ weberp=""></TARIFAZ>
                <FECHAULTAU weberp="fecha_ultaum">' . $Data['fecha_ultaum'] . '</FECHAULTAU>
                <EMAIL weberp="email">' . $Data['email'] . '</EMAIL>
                <HPROTEGE weberp=""></HPROTEGE>
                <TRANSFE weberp=""></TRANSFE>
                <NUEVO weberp=""></NUEVO>
                <EDITA weberp=""></EDITA>
                <EXPEDIENTE weberp=""></EXPEDIENTE>
                <FECALTAEXP weberp=""></FECALTAEXP>
                <USERALTAEX weberp=""></USERALTAEX>
                <FOLIOCAN weberp=""></FOLIOCAN>
                <IdUsuario weberp="">2</IdUsuario>
                <IdEstado weberp="braddress8">' . $this->GetEstadoAR($Data['braddress8']) . '</IdEstado>
            </CCM_Folsocios>
        </row>');

        return $XmlInsert;
    }


    public function XMLUpdateSocios($Data){


        $Padecimientos = array(
            'CRISIS CONVULSIVA'=>1,
            'PROBLEMAS RENALES'=>2,
            'PROBLEMAS CONGENITOS'=>3,
            'PROBLEMAS CARDIACOS'=>4,
            'PROBLEMAS PSIQUIATRICOS'=>5,
            'HIPERTENSION ARTERIAL'=>6,
            'ASMA'=>7,
            'ALCOHOLISMO'=>8,
            'EMBOLIA CEREBRAL'=>9,
            'ALERGIA A ANALGESICO'=>10,
            'ALERGIA A ANTIBIOTICO'=>11,
            'ANGINA DE PECHO'=>12,
            'INFARTO PREVIO'=>13,
            'FUMADOR'=>14,
            'DIABETES'=>15,
            'CANCER'=>16,
            'SIDA'=>17,
            'ENF. INFECCIOSA'=>18,
            'ENF. PULMONARES'=>19,
            'ENF. DEL HIGADO.'=>20,
            'ALERGIA RESPIRATORIA'=>21,
            'CIRUGIAS PREVIAS'=>22,
            'PROBLEMA OCULAR'=>23,
            'PROBLEMA AUDITIVO'=>24,
            'SANGRADOS FRECUENTES'=>25,
            'MEDICAMENTOS HAB.'=>26,
            'OTROS'=>27,
            'NINGUNO'=>28,
        );

        if($Data['sexo'] == 'MASCULINO'){
            $Data['sexo'] = "1";
        }else{
            $Data['sexo'] = "2";
        }

        $AntecedentesClinicos=json_decode($Data['antecedentes_clinicos'], 1);
        FB::INFO($Data['antecedentes_clinicos'], 'antecedentes_clinicos 1');
        FB::INFO($AntecedentesClinicos, 'antecedentes_clinicos 2');

        $Padece = "0";

        foreach($Padecimientos as $nombre  => $id){
            if(in_array($nombre, $AntecedentesClinicos)){
                $Padece .= "-".$id;
            }
        }

        if(!empty($AntecedentesClinicos['otros'])){
            $Padece .= "-27";
        }

        if($Data['fecha_ultaum']=='0000-00-00'){
            $Data['fecha_ultaum']='1900-01-01';
        }

        $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="FOLIO= ' . $Data['folio'] . ' AND NOSOCIO=' . $Data['branchcode'] . '">
                    <NOMBRES weberp="brname">' . $Data['brname'] . '</NOMBRES>
                    <SEXO weberp="sexo">' . $Data['sexo'] . '</SEXO>
                    <NOMCOMERC weberp="nombre_empresa">' . $Data['nombre_empresa'] . '</NOMCOMERC>
                    <CALLE weberp="braddress1">' . $Data['braddress1'] . '</CALLE>
                    <NUMERO weberp="braddress2">' . $Data['braddress2'] . '</NUMERO>
                    <COLONIA weberp="braddress4">' . $Data['braddress4'] . '</COLONIA>
                    <SECTOR weberp="braddress5">' . $Data['braddress5'] . '</SECTOR>
                    <ENTRECALLE weberp="braddress6">' . $Data['braddress6'] . '</ENTRECALLE>
                    <IdMunicipio weberp="braddress7">' . $this->GetMunicipioAR($Data['braddress7']) . '</IdMunicipio>
                    <IdEstado weberp="braddress8">' . $this->GetEstadoAR($Data['braddress8']) . '</IdEstado>
                    <CP weberp="braddress10">' . $Data['braddress10'] . '</CP>
                    <CUADRANTE1 weberp="cuadrante1">' . $Data['cuadrante1'] . '</CUADRANTE1>
                    <CUADRANTE2 weberp="cuadrante2">' . $Data['cuadrante2'] . '</CUADRANTE2>
                    <CUADRANTE3 weberp="cuadrante3">' . $Data['cuadrante3'] . '</CUADRANTE3>
                    <TELEFONO weberp="phoneno">' . $Data['phoneno'] . '</TELEFONO>
                    <FECHA weberp="fecha_ingreso">' . $Data['fecha_ingreso'] . '</FECHA>
                    <FECHAULTAU weberp="fecha_ultaum">' . $Data['fecha_ultaum'] . '</FECHAULTAU>
                    <PADECE weberp="antecedentes_clinicos">' . $Padece . '</PADECE>
                    <OTROSPAD weberp="null">' . $AntecedentesClinicos['otros'] . '</OTROSPAD>
                </CCM_Folsocios>
            </row>');

        return $XMLUpdate;
    }

    public function XMLCancelarAfiliados($Data){

        $Data['Cancelacion']['movimientos_afiliacion'] = 2;
        /* Traduccion de Catalogo MOTIVOS_CANCELACION*/
        $Data['Cancelacion']['motivo_cancelacion'] = $this->GetMotivoCancelacionAR($Data['Cancelacion']['motivo_cancelacion']);
        if(empty($Data['Cancelacion']['motivo_cancelacion'])){
            $Data['Cancelacion']['motivo_cancelacion']=0;
        }

        $XMLUpdate = ('
            <row>
                <CCM_Foltitular weberp="rh_titular" where="FOLIO = ' . $Data['Cancelacion']['folio'] . '">
                    <STATUS weberp="movimientos_afiliacion">' . $Data['Cancelacion']['movimientos_afiliacion'] . '</STATUS>
                    <FECHACAN weberp="fecha_cancelacion">' . $Data['Cancelacion']['fecha_cancelacion'] . '</FECHACAN>
                    <IdMotivoCancelacion weberp="t.motivo_cancelacion">' . $Data['Cancelacion']['motivo_cancelacion'] . '</IdMotivoCancelacion>
                </CCM_Foltitular>
            </row>
            ');
         return $XMLUpdate;
    }

    public function XMLCancelarSocios($Data){

        $Data['Cancelacion']['movimientos_afiliacion'] = 2;

        /* Traduccion de Catalogo MOTIVOS_CANCELACION*/
        $Data['Cancelacion']['motivo_cancelacion'] = $this->GetMotivoCancelacionAR($Data['Cancelacion']['motivo_cancelacion']);
        if(empty($Data['Cancelacion']['motivo_cancelacion'])){
            $Data['Cancelacion']['motivo_cancelacion']=0;
        }

         $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="FOLIO = ' . $Data['Cancelacion']['folio'] . ' ">
                    <ESTATUS weberp="movimientos_afiliacion">' . $Data['Cancelacion']['movimientos_afiliacion'] . '</ESTATUS>
                    <FECBAJA weberp="fecha_cancelacion">' . $Data['Cancelacion']['fecha_cancelacion'] . '</FECBAJA>
                    <MOTBAJA weberp="t.motivo_cancelacion">' . $Data['Cancelacion']['motivo_cancelacion'] . '</MOTBAJA>
                </CCM_Folsocios>
            </row>
            ');
        return $XMLUpdate;
    }


    public function XMLCancelarSocio($Data){

        $Data['movimientos_afiliacion'] = 2;

        /* Traduccion de Catalogo MOTIVOS_CANCELACION*/
        $Data['CMotivos'] = $this->GetMotivoCancelacionAR($Data['CMotivos']);
        if(empty($Data['CMotivos'])){
            $Data['CMotivos']=0;
        }

         $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="NOSOCIO=' . $Data['CBranchCode'] . ' AND FOLIO = ' . $Data['CFolio'] . ' ">
                    <ESTATUS weberp="movimientos_afiliacion">' . $Data['movimientos_afiliacion'] . '</ESTATUS>
                    <FECBAJA weberp="fecha_cancelacion">' . $Data['CFecha_Baja'] . '</FECBAJA>
                    <MOTBAJA weberp="t.motivo_cancelacion">' . $Data['CMotivos'] . '</MOTBAJA>
                </CCM_Folsocios>
            </row>
            ');
        return $XMLUpdate;
    }

    public function XMLReactivarAfiliado($Data){

        $Data['Reactivasion']['movimientos_afiliacion'] = 1;

         $XMLUpdate = ('
            <row>
                <CCM_Foltitular weberp="rh_titular" where="FOLIO = ' . $Data['Reactivasion']['RFolio'] . ' ">
                    <STATUS weberp="movimientos_afiliacion">' . $Data['Reactivasion']['movimientos_afiliacion'] . '</STATUS>
                </CCM_Foltitular>
            </row>
            ');
        return $XMLUpdate;
    }

    public function XMLReactivarSocios($Data){

        $Data['Reactivasion']['movimientos_afiliacion'] = 1;

         $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="NOSOCIO = ' . $Data['Activar']['BranchCode'] . ' and FOLIO = ' . $Data['Activar']['Folio'] . ' ">
                    <ESTATUS weberp="movimientos_afiliacion">' . $Data['Reactivasion']['movimientos_afiliacion'] . '</ESTATUS>
                </CCM_Folsocios>
            </row>
            ');
        return $XMLUpdate;
    }

    public function XMLSuspenderAfiliados($Data){

         $XMLUpdate = ('
            <row>
                <CCM_Foltitular weberp="rh_titular" where="FOLIO = ' . $Data['folio'] . ' ">
                    <STATUS weberp="movimientos_afiliacion">' . $Data['movimientos_afiliacion'] . '</STATUS>
                </CCM_Foltitular>
            </row>
            ');
        return $XMLUpdate;
    }

    public function XMLSuspenderSocios($Data){

         $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="FOLIO = ' . $Data['folio'] . ' ">
                    <ESTATUS weberp="movimientos_afiliacion">' . $Data['movimientos_afiliacion'] . '</ESTATUS>
                    <FECHASUSP1 weberp="">' . $Data['SFecha_Inicial'] . '</FECHASUSP1>
                    <FECHASUSP2 weberp="">' . $Data['SFecha_Final'] . '</FECHASUSP2>
                </CCM_Folsocios>
            </row>
            ');
        return $XMLUpdate;
    }


    public function XMLSuspenderSocio($Data, $Folio){

         $XMLUpdate = ('
            <row>
                <CCM_Folsocios weberp="custbranch" where="NOSOCIO= ' . $Data['SBranchCode'] . ' AND FOLIO = ' . $Folio . ' ">
                    <ESTATUS weberp="movimientos_afiliacion">' . $Data['movimientos_afiliacion'] . '</ESTATUS>
                    <FECHASUSP1 weberp="">' . $Data['SFecha_Inicial'] . '</FECHASUSP1>
                    <FECHASUSP2 weberp="">' . $Data['SFecha_Final'] . '</FECHASUSP2>
                </CCM_Folsocios>
            </row>
            ');
        return $XMLUpdate;
    }
 }