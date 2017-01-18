<?php
class ISSTELEONWS extends CActiveRecord {

    /**
     * @Todo
     * http://ar.isssteleon.gob.mx/recetas/Medix.asmx
     * @var string $WSDLURL "URL of the WSDL"
     * @var array $options "Option for __constructor SoapClient";
     */

    var $WSDLURL = "";
    var $options = array(
        'cache_wsdl' => WSDL_CACHE_NONE,
        'trace' => TRUE
    );
    var $client = "";

    public function __construct($WSDLURL, $options = "") {
        if (!empty($options)) $this->options = $options;
        $this->WSDLURL = $WSDLURL;
        FB::INFO($this->WSDLURL,'____________________________MODEL WS');
    }

    /**
     * @name consumir
     * @param array $param "Param send"
     * @param string $method "method to call of ws"
     */
    public function consumir($method, $param) {
        try {
            $this->client = new SoapClient($this->WSDLURL, $this->options);
            $methodResult = $method . "Result";
            $ready = $this->client->{$method}($param)->{$methodResult};
            $arrayReturn = $this->xml2Array($ready);
            return $arrayReturn;
        }
        catch(Exception $e) {
            trigger_error($e->getMessage() , E_USER_WARNING);
        }
    }

    /**
     * @Todo
     * Parsea el XML
     */
    public function xml2Array($XML) {
        $json = json_encode($XML);
        $ready = json_decode($json, TRUE);

        $archivoSimpleXML = simplexml_load_string($ready['any']);
        $json = json_encode($archivoSimpleXML);
        return json_decode($json, TRUE);
    }
}
