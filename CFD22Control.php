<?php

 class CFD22Control{
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
        $this->Request['verifyXML']="http://ws.rh.com/ResignCFD/VerifyXMLRequest";
        $this->Request['ResignXML']="http://ws.rh.com/ResignCFD/SignXMLRequest";

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

    private function xml_verifyXML($xml){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                    <soapenv:Header/>
                        <soapenv:Body>
                            <ws:VerifyXML>
                                <xml>'.$xml.'</xml>
                            </ws:VerifyXML>
                        </soapenv:Body>
                </soapenv:Envelope>';
               return $xmlData;
    }

     private function xml_SignXML($user,$pass,$certificate,$xml){
      	       $xmlData ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ws="http://ws.rh.com/">
                            <soapenv:Header/>
                                <soapenv:Body>
                                    <ws:SignXML>
			                            <user>'.$user.'</user>
			                            <pass>'.$pass.'</pass>
			                            <certificate>'.$certificate.'</certificate>
		                        	    <xml>'.$xml.'</xml>
                                    </ws:SignXML>
                                </soapenv:Body>
                            </soapenv:Envelope';
               return $xmlData;
    }

    private function sendSoap($method,$xmlData){
            $tuCurl = curl_init();
            curl_setopt($tuCurl, CURLOPT_URL, "http://50.56.95.111/RHCFDControl/ResignCFD");
            //curl_setopt($tuCurl, CURLOPT_URL, "http://184.106.216.217/RHCFDControl/ResignCFD");
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

    public function verifyXML($xml){
        return $this->processResponse($this->sendSoap("verifyXML",$this->xml_verifyXML($xml)));
    }

    public function ResignXML($certificate,$xml_base64){
        $aux =$this->processResponse($this->sendSoap("ResignXML",$this->xml_SignXML($this->user,$this->pass,$certificate,$xml_base64)));
        $this->processXML($aux);
        return $aux;
    }


    public static function getInstance(){
        global $ws22User,$ws22Pass;
        return  new CFD22Control($ws22User,$ws22Pass);
    }
 }

 //$Manager = new CFD22Manager("DEMO","DEMO");
 //echo $Manager->reporteMensual("6","2012");
 //echo $Manager->cancelCFD("B","2");   */
?>