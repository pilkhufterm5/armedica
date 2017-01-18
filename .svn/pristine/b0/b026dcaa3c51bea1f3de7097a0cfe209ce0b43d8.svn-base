<?php

 class CFD22Manager{
    private $Request=array();
    private $dom;
    private $user;
    private $pass;

    private $serie=null;
    private $folio=null;
    private $noauth=null;
    private $anoauth=null;
    private $sello=null;
    private $noCert=null;
    private $fecha=null;
    private $metodoPago=null;
    private $cuentaPago=null;

    function __construct($user,$pass){
        $this->user = $user;
        $this->pass = $pass;
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->Request['getSeries']="http://ws.rh.com/SignCFD/getSeriesRequest";
        $this->Request['getCertificates']="http://ws.rh.com/SignCFD/getCertificatesRequest";
        $this->Request['reporteMensual']="http://ws.rh.com/SignCFD/reporteMensualRequest";
        $this->Request['getCadena']="http://ws.rh.com/SignCFD/getCadenaRequest";
        $this->Request['cancelCFD']="http://ws.rh.com/SignCFD/cancelCFDRequest";
        $this->Request['addSerie']="http://ws.rh.com/SignCFD/addSerieRequest";
        $this->Request['addKeys']="http://ws.rh.com/SignCFD/addKeysRequest";
        $this->Request['SignXML']="http://ws.rh.com/SignCFD/SignXMLRequest";

        $this->sello=null;
        $this->folio=null;
        $this->serie=null;
        $this->noauth=null;
        $this->anoauth=null;
        $this->noCert=null;
        $this->fecha=null;
        $this->metodoPago=null;
        $this->cuentaPago=null;
    }

    private function xml_getSeries($user,$pass){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                    <soapenv:Header/>
                        <soapenv:Body>
                            <ws:getSeries>
                                <user>'.$user.'</user>
                                <pass>'.$pass.'</pass>
                            </ws:getSeries>
                        </soapenv:Body>
                    </soapenv:Envelope>';
               return $xmlData;
    }

     private function xml_getCertificate($user,$pass){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:getCertificates>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                    </ws:getCertificates>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

    private function xml_reporteMensual($user,$pass,$month,$year){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:reporteMensual>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                        <mes>'.$month.'</mes>
                                        <anho>'.$year.'</anho>
                                    </ws:reporteMensual>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

    private function xml_getCadena($user,$pass,$serie,$folio){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:getCadena>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                        <serie>'.$serie.'</serie>
                                        <folio>'.$folio.'</folio>
                                    </ws:getCadena>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

    private function xml_cancelCFD($user,$pass,$serie,$folio){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:cancelCFD>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                        <serie>'.$serie.'</serie>
                                        <folio>'.$folio.'</folio>
                                    </ws:cancelCFD>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

    private function xml_addSerie($user,$pass,$serie,$anoauth,$noauth,$folioini,$foliofin){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:addSerie>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                        <serie>'.$serie.'</serie>
                                        <anoauth>'.$anoauth.'</anoauth>
                                        <noauth>'.$noauth.'</noauth>
                                        <folioini>'.$folioini.'</folioini>
                                        <foliofin>'.$foliofin.'</foliofin>
                                    </ws:addSerie>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

     private function xml_addKeys($user,$pass,$certificate,$key,$passkey){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:addKeys>
                                        <user>'.$user.'</user>
                                        <pass>'.$pass.'</pass>
                                        <cerificate>'.$certificate.'</cerificate>
                                        <key>'.$key.'</key>
                                        <passkey>'.$passkey.'</passkey>
                                    </ws:addKeys>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

     private function xml_SignXML($user,$pass,$serie,$certificate,$xml){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
		                            <ws:SignXML>
			                            <user>'.$user.'</user>
			                            <pass>'.$pass.'</pass>
			                            <serie>'.$serie.'</serie>
			                            <certificate>'.$certificate.'</certificate>
		                        	    <xml>'.$xml.'</xml>
		                            </ws:SignXML>
                                </soapenv:Body>
                            </soapenv:Envelope>';
               return $xmlData;
    }

    private function sendSoap($method,$xmlData){
            $tuCurl = curl_init();
            curl_setopt($tuCurl, CURLOPT_URL, "http://50.56.95.111/RHCFDSign/SignCFD");
            curl_setopt($tuCurl, CURLOPT_PORT ,8080);
            curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
            curl_setopt($tuCurl, CURLOPT_HEADER, 0);
            curl_setopt($tuCurl, CURLOPT_POST, 1);
            //curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
            //curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($tuCurl, CURLOPT_POSTFIELDS,$xmlData);
            curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8","SOAPAction: \"".$this->Request[$method]."\"", "Content-length: ".strlen($xmlData)));
            $tuData = curl_exec($tuCurl);
            if(curl_errno($tuCurl)){
                throw new Exception('error en comunicacion: ' . curl_error($tuData));
            }
            curl_close($tuCurl);
            return $tuData;
    }

    private function processResponse($XML){
        $this->dom->loadXML($XML);
        $messages = $this->dom->getElementsByTagName('message');
        foreach($messages as $message){
            throw new Exception('Error: ' . $message->nodeValue);
        }
         $returns = $this->dom->getElementsByTagName('return');
        foreach($returns as $return){
            return $return->nodeValue;
        }
        $messages = $this->dom->getElementsByTagName('faultstring');
        foreach($messages as $message){
            throw new Exception('Error: ' . $message->nodeValue);
        }
    }

     private function processXML($XML){
        $this->dom->loadXML($XML);
        $returns = $this->dom->getElementsByTagName('Comprobante');
        foreach($returns as $return){
            try{
                $this->serie = $return->getAttribute("serie");
                $this->folio = $return->getAttribute("folio");
                $this->fecha = $return->getAttribute("fecha");
                $this->noauth = $return->getAttribute("noAprobacion");
                $this->anoauth = $return->getAttribute("anoAprobacion");
                $this->sello = $return->getAttribute("sello");
                $this->metodoPago = $return->getAttribute("metodoDePago");
                $this->cuentaPago = $return->getAttribute("NumCtaPago");
            }catch(Exception $e){
                throw new Exception('Error: al leer el XML');
            }
        }

    }

    public function getXMLSerie(){
        return $this->serie;
    }

    public function getXMLFolio(){
        return $this->folio;
    }

    public function getXMLSello(){
        return $this->sello;
    }

    public function getMetodoPago(){
        return $this->metodoPago;
    }

    public function getCuentaPago(){
        return $this->cuentaPago;
    }
    public function getXMLFecha(){
        return str_replace ('T',' ',$this->fecha);
    }

    public function getXMLnoAuth(){
        return $this->noauth;
    }

    public function getXMLanoAuth(){
        return $this->anoauth;
    }

    public function getSeries(){
        return $this->processResponse($this->sendSoap("getSeries",$this->xml_getSeries($this->user,$this->pass)));
    }

    public function getCertificates(){
        return $this->processResponse($this->sendSoap("getCertificates",$this->xml_getCertificate($this->user,$this->pass)));
    }

    public function reporteMensual($month,$year){
        return $this->processResponse($this->sendSoap("reporteMensual",$this->xml_reporteMensual($this->user,$this->pass,$month,$year)));
    }

    public function getCadena($serie,$folio){
        return $this->processResponse($this->sendSoap("getCadena",$this->xml_getCadena($this->user,$this->pass,$serie,$folio)));
    }

    public function cancelCFD($serie,$folio){
        return $this->processResponse($this->sendSoap("cancelCFD",$this->xml_cancelCFD($this->user,$this->pass,$serie,$folio)));
    }

    public function addSerie($serie,$anoauth,$noauth,$folioini,$foliofin){
        return $this->processResponse($this->sendSoap("addSerie",$this->xml_addSerie($this->user,$this->pass,$serie,$anoauth,$noauth,$folioini,$foliofin)));
    }

    public function addKeys($certificate_base64,$key_base64,$passkey){
        return $this->processResponse($this->sendSoap("addKeys",$this->xml_addKeys($this->user,$this->pass,$certificate_base64,$key_base64,$passkey)));
    }

    public function SignXML($serie,$certificate,$xml_base64){
        $aux =$this->processResponse($this->sendSoap("SignXML",$this->xml_SignXML($this->user,$this->pass,$serie,$certificate,$xml_base64)));
        $this->processXML($aux);
        return $aux;
    }


    public static function getInstance(){
        global $ws22User,$ws22Pass;
        return  new CFD22Manager($ws22User,$ws22Pass);
    }
 }

 /*$Manager = new CFD22Manager("DEMO","DEMO");
 echo $Manager->reporteMensual("6","2012");
 echo $Manager->cancelCFD("B","2");   */
?>