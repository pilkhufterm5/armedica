<?php

/**
 * InvoiceClass  Version 1.0
 * 
 * @Company Realhost S.A de C.V.
 * @copyright (c) 2015, Ulises Carreón
 * @filesource InvoiceClass.php
 * @filesource InvoiceTemplate/Interface
 * @author Ing. Ulises Carreón Alvarez
 * 
 */

interface InvoiceTemplate{
    public function timbrarCFDIOne_Prueba($XML);
    public function timbrarCFDIOne($XML);
    public function cancelaCFDI_OnePruebas($RFC, $UUID, $pfx, $Password);
    public function cancelaCFDI_One($RFC, $UUID, $pfx, $Password);
    public function RecuperaXML_CFDI();
}

class InvoiceClass implements InvoiceTemplate {

//    private static $URLC = "https://invoiceone.mx/TimbreCFDI/TimbreCFDI.asmx?wsdl";
    public static $URLC = "https://invoiceone.mx/TimbreCFDI_PreferenteA/TimbreCFDI.asmx?wsdl";
    public static $USERIO = 'REA08010';
    public static $PASSIO = 'a965$Q5q';
    public static $TRACE = TRUE;
    private $RespuestaIO;
    private $RespuestaIO_Prod;
    private $RespuestaIO_Cancel;
    private $XMLTimbrado;
    private $XMLTimbrado_Prod;
    private $XMLCancelado;

    private static function createSoapObject() {
        return new SoapClient(self::$URLC, array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => self::$TRACE));
    }

    private static function arraySoap($XML) {
        return array('usuario' => self::$USERIO, 'contrasena' => self::$PASSIO, 'xmlComprobante' => $XML);
    }
    private static function arraySoapProd($XML) {
        return array('nombreUsuario' => self::$USERIO, 'contrasena' => self::$PASSIO, 'xmlComprobante' => $XML);
    }

    private static function arraySoapCancel($RFC, $UUID, $pfx, $Password) {
        return array('nombreUsuario' => self::$USERIO, 'contrasena' => self::$PASSIO, 'rfcEmisor' => $RFC, 'listaUuid' => array('guid' => $UUID), 'pfxBase64' => $pfx, 'contrasenaPfx' => $Password);
    }
    
    final protected static function comunicationSoapPrueba($XML = null) {
         return self::createSoapObject()->ObtenerCFDIPrueba(self::arraySoap($XML));
    }
    final protected static function comunicationSoap($XML = null) {
         return self::createSoapObject()->ObtenerCFDI(self::arraySoapProd($XML));
    }
    final protected static function getResponse($Response = null){
         return $Response->ObtenerCFDIPruebaResult->Xml;

    }
    final protected static function getResponseProd($Response = null){
         return $Response->ObtenerCFDIResult->Xml;
    }

    public function timbrarCFDIOne_Prueba($XML) {
        try {
            $this->RespuestaIO = self::comunicationSoapPrueba($XML);
            $this->XMLTimbrado = self::getResponse($this->RespuestaIO);
            return $this->XMLTimbrado;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function timbrarCFDIOne($XML) {
        try {
            $this->RespuestaIO_Prod = self::comunicationSoap($XML);
            $this->XMLTimbrado_Prod = self::getResponseProd($this->RespuestaIO_Prod);
            return $this->XMLTimbrado_Prod;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancelaCFDI_OnePruebas($RFC, $UUID, $pfx, $Password) {
        try {
            $this->RespuestaIO_Cancel = self::createSoapObject()->cancelaCFDIPruebas(self::arraySoapCancel($RFC, $UUID, $pfx, $Password));
            $this->XMLCancelado = $this->RespuestaIO_Cancel->cancelaCFDIResult->XmlAcuse;
            return $UUID;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function cancelaCFDI_One($RFC, $UUID, $pfx, $Password) {
        try {
            $this->RespuestaIO_Cancel = self::createSoapObject()->cancelaCFDI(self::arraySoapCancel($RFC, $UUID, $pfx, $Password));
            $this->XMLCancelado = $this->RespuestaIO_Cancel->cancelaCFDIResult->XmlAcuse;
            return $UUID;
        } catch (Exception $e) {
             throw $e;
        }
    }

    public function RecuperaXML_CFDI() {}

}
