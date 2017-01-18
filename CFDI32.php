<?php
error_reporting(0);
require ("zipfile.php");
require ("QRCode/index.php");
include_once "includes/InvoiceClass.php";
class ZipLibrary {
    public static function decompressZipStream($ZIPContentStr) {
        if (strlen($ZIPContentStr)<102) {
            throw new Exception('error: input data too short');
        }
        $CompressedSize = self::binstrtonum(substr($ZIPContentStr, 18, 4));
        $UncompressedSize = self::binstrtonum(substr($ZIPContentStr, 22, 4));
        $FileNameLen = self::binstrtonum(substr($ZIPContentStr, 26, 2));
        $ExtraFieldLen = self::binstrtonum(substr($ZIPContentStr, 28, 2));
        $Offs = 30 + $FileNameLen + $ExtraFieldLen;
        $ZIPData = substr($ZIPContentStr, $Offs);
        $Data = gzinflate($ZIPData);
        //echo strlen($Data);
        if (strlen($Data)!=$UncompressedSize) {
            //throw new Exception('error: uncompressed data have wrong size');
            return $Data;
        } else
            return $Data;
    }

    private static function binstrtonum($Str) {
        $Num = 0;
        for ($TC1 = strlen($Str) - 1; $TC1>=0; $TC1--) {
            $Num<<=8;
            $Num |= ord($Str[$TC1]);
        }
        return $Num;
    }

}

class XML {
    protected $atributos;
    protected $rules;

    function __construct() {
        $this->atributos = array();
        $this->rules = array();
    }

    public function nl() {
        $LF = 0x0A;
        $CR = 0x0D;
        return sprintf("%c%c", $CR, $LF);
    }

    protected function setSatFormat($value) {
        $aux = trim(strip_tags($value));
        if (!XML::isUtf8($aux)) {
            $aux = utf8_encode($aux);
            if (!XML::isUtf8($aux))
                $aux = utf8_encode($aux);
            //Doble codificacion a iso, comun en sistemas viejos
        }
        $aux = strtoupper($value);
        $value = str_replace(array(
            urldecode("%E2%80%9C"),
            urldecode("%E2%80%9D"),
            urldecode("%93"),
            urldecode("%94")
        ), '"', $value);
        //Se sustituye los caracteres �� por "
        $aux = str_replace('&', '&amp;', $aux);
        $aux = str_replace('"', '&quot;', $aux);
        $aux = str_replace("'", '&apos;', $aux);
        $aux = str_replace("<", '&lt;', $aux);
        $aux = str_replace(">", '&gt;', $aux);
        
        $aux = str_replace(urldecode('%E2%80%93'), '-', $aux);//Se sustituye guiones especiales por signo de guion medio
        $aux = str_replace(urldecode('%E2%80%94'), '-', $aux);
        
        return $aux;
    }

    static function isUtf8($str) {
        return (bool)preg_match('%^(?:
         			[\x09\x0A\x0D\x20-\x7E]            # ASCII
         			| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
         			|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
         			| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
         			|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
         			|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
         			| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
         			|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
         	)*$%xs', $str);
    }

    function setAtribute($attr, $value) {
        if ($attr!='tipoDeComprobante') {
            $this->atributos[$attr] = $this->setSatFormat($value);
        } else {
            $this->atributos[$attr] = $value;
        }
    }

    function getAtributes() {
        $conenido = '';
        foreach ($this->atributos as $key => $value) {
            if (($this->rules[$key]=='R') && (strlen($this->atributos[$key])<=0)) {
                throw new Exception('Atributo ' . $key . ' de ' . get_class($this) . ' es requerido por el SAT');
            } else {
                if ((($this->rules[$key]=='O') || ($this->rules[$key]=='R')) && (strlen($this->atributos[$key])>0))
                    $conenido .= $key . '="' . $value . '" ';
            }
        }
        return $conenido;
    }

}

class Comprobante extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['version'] = '3.2';
        $this->atributos['serie'] = '';
        $this->atributos['folio'] = '';
        $this->atributos['fecha'] = '';
        $this->atributos['sello'] = ' ';
        $this->atributos['formaDePago'] = '';
        $this->atributos['noCertificado'] = '';
        $this->atributos['certificado'] = ' ';
        $this->atributos['condicionesDePago'] = '';
        $this->atributos['subTotal'] = '';
        $this->atributos['descuento'] = '';
        $this->atributos['motivoDescuento'] = '';
        $this->atributos['TipoCambio'] = '';
        $this->atributos['Moneda'] = '';
        $this->atributos['total'] = '';
        $this->atributos['metodoDePago'] = 'No Identificado';
        $this->atributos['tipoDeComprobante'] = '';
        $this->atributos['LugarExpedicion'] = '';
        $this->atributos['NumCtaPago'] = '';
        $this->atributos['FolioFiscalOrig'] = '';
        $this->atributos['SerieFolioFiscalOrig'] = '';
        $this->atributos['FechaFolioFiscalOrig'] = '';
        $this->atributos['MontoFolioFiscalOrig'] = '';
        $this->rules = array();
        $this->rules['version'] = 'R';
        $this->rules['serie'] = 'O';
        $this->rules['folio'] = 'O';
        $this->rules['fecha'] = 'R';
        $this->rules['sello'] = 'R';
        $this->rules['formaDePago'] = 'R';
        $this->rules['noCertificado'] = 'R';
        $this->rules['certificado'] = 'R';
        $this->rules['condicionesDePago'] = 'O';
        $this->rules['subTotal'] = 'R';
        $this->rules['descuento'] = 'O';
        $this->rules['motivoDescuento'] = 'O';
        $this->rules['TipoCambio'] = 'O';
        $this->rules['Moneda'] = 'O';
        $this->rules['total'] = 'R';
        $this->rules['metodoDePago'] = 'R';
        $this->rules['tipoDeComprobante'] = 'R';
        $this->rules['LugarExpedicion'] = 'R';
        $this->rules['NumCtaPago'] = 'O';
        $this->rules['FolioFiscalOrig'] = 'O';
        $this->rules['SerieFolioFiscalOrig'] = 'O';
        $this->rules['FechaFolioFiscalOrig'] = 'O';
        $this->rules['MontoFolioFiscalOrig'] = 'O';
    }

}

class RegimenFiscal extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['Regimen'] = '';

        $this->rules = array();
        $this->rules['Regimen'] = 'R';
    }

}

class DomicilioFiscal extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['calle'] = '';
        $this->atributos['noExterior'] = '';
        $this->atributos['noInterior'] = '';
        $this->atributos['colonia'] = '';
        $this->atributos['localidad'] = '';
        $this->atributos['referencia'] = '';
        $this->atributos['municipio'] = '';
        $this->atributos['estado'] = '';
        $this->atributos['pais'] = '';
        $this->atributos['codigoPostal'] = '';
        $this->rules = array();
        $this->rules['calle'] = 'R';
        $this->rules['noExterior'] = 'O';
        $this->rules['noInterior'] = 'O';
        $this->rules['colonia'] = 'O';
        $this->rules['localidad'] = 'O';
        $this->rules['referencia'] = 'O';
        $this->rules['municipio'] = 'R';
        $this->rules['estado'] = 'R';
        $this->rules['pais'] = 'R';
        $this->rules['codigoPostal'] = 'R';
    }

}

class Domicilio extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['calle'] = '';
        $this->atributos['noExterior'] = '';
        $this->atributos['noInterior'] = '';
        $this->atributos['colonia'] = '';
        $this->atributos['localidad'] = '';
        $this->atributos['referencia'] = '';
        $this->atributos['municipio'] = '';
        $this->atributos['estado'] = '';
        $this->atributos['pais'] = '';
        $this->atributos['codigoPostal'] = '';
        $this->rules = array();
        $this->rules['calle'] = 'O';
        $this->rules['noExterior'] = 'O';
        $this->rules['noInterior'] = 'O';
        $this->rules['colonia'] = 'O';
        $this->rules['localidad'] = 'O';
        $this->rules['referencia'] = 'O';
        $this->rules['municipio'] = 'O';
        $this->rules['estado'] = 'O';
        $this->rules['pais'] = 'R';
        $this->rules['codigoPostal'] = 'O';
    }

}

class Emisor extends XML {
    public $domicilioFiscal;
    public $expedidoEn;
    public $regimenFiscal;
    function __construct() {
        $this->atributos = array();
        $this->atributos['rfc'] = '';
        $this->atributos['nombre'] = '';
        $this->rules = array();
        $this->rules['rfc'] = 'R';
        $this->rules['nombre'] = 'R';
        $this->domicilioFiscal = new DomicilioFiscal();
        $this->expedidoEn = new DomicilioFiscal();
        $this->regimenFiscal = new RegimenFiscal();
    }

    public function getNode() {
        $xml = '<cfdi:Emisor ' . $this->getAtributes() . ' >
                        <cfdi:DomicilioFiscal ' . $this->domicilioFiscal->getAtributes() . ' />
                        <cfdi:ExpedidoEn ' . $this->expedidoEn->getAtributes() . ' />
                        <cfdi:RegimenFiscal ' . $this->regimenFiscal->getAtributes() . ' />
                  </cfdi:Emisor>';
        return $xml;
    }

}

class Receptor extends XML {
    public $domicilio;
    function __construct() {
        $this->atributos = array();
        $this->atributos['rfc'] = '';
        $this->atributos['nombre'] = '';
        $this->rules = array();
        $this->rules['rfc'] = 'R';
        $this->rules['nombre'] = 'O';
        $this->domicilio = new Domicilio();
    }

    public function getNode() {
        $xml = '<cfdi:Receptor ' . $this->getAtributes() . ' >
                        <cfdi:Domicilio ' . $this->domicilio->getAtributes() . ' />
                  </cfdi:Receptor>';
        return $xml;
    }

}

class Concepto extends XML {
    public $pedimentos;
    function __construct() {
        $this->pedimentos = array();
        $this->atributos = array();
        $this->atributos['cantidad'] = '';
        $this->atributos['unidad'] = '';
        $this->atributos['noIdentificacion'] = '';
        $this->atributos['descripcion'] = '';
        $this->atributos['valorUnitario'] = '';
        $this->atributos['importe'] = '';
        $this->rules = array();
        $this->rules['cantidad'] = 'R';
        $this->rules['unidad'] = 'O';
        $this->rules['noIdentificacion'] = 'O';
        $this->rules['descripcion'] = 'R';
        $this->rules['valorUnitario'] = 'R';
        $this->rules['importe'] = 'R';
    }

    public function getNode() {
        $xml = '<cfdi:Concepto ' . $this->getAtributes();
        $Pedimentos = $this->getPedimentosNode();
        if ($Pedimentos!='')
            $xml .= '>' . $Pedimentos . '</cfdi:Concepto>';
        else
            $xml .= ' />';
        return $xml;
    }

    public function setPedimentos(InformacionAduanera $xml) {
        $this->pedimentos[] = $xml;
        return $this;
    }

    public function getPedimentosNode() {
        $xml = '';
        $nl = $this->nl();
        if (count($this->pedimentos)>0) {
            $xml .= $nl;
            foreach ($this->pedimentos as $Pedimento) {
                $xml .= $Pedimento->getNode() . $nl;
            }
        }
        return $xml;
    }

}

class InformacionAduanera extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['numero'] = '';
        $this->atributos['fecha'] = '';
        $this->atributos['aduana'] = '';
        $this->rules = array();
        $this->rules['numero'] = 'R';
        $this->rules['fecha'] = 'R';
        $this->rules['aduana'] = 'R';
    }

    function getAtributes() {
        $conenido = '';
        foreach ($this->atributos as $key => $value) {
            if (($this->rules[$key]=='R') && (strlen($this->atributos[$key])<=0)) {
                throw new Exception('Atributo ' . $key . ' de ' . get_class($this) . ' es requerido por el SAT');
            } else {
                if ((($this->rules[$key]=='O') || ($this->rules[$key]=='R')) && (strlen($this->atributos[$key])>0))
                    $conenido .= $key . '="' . $value . '" ';
            }
        }
        return $conenido;
    }

    function setAtribute($attr, $value) {

        if (isset($this->atributos[$attr])) {
            if ($attr!='tipoDeComprobante') {

                $this->atributos[$attr] = $this->setSatFormat($value);
            } else {
                $this->atributos[$attr] = $value;
            }
        }
    }

    public function getNode() {
        $xml = '<cfdi:InformacionAduanera ' . $this->getAtributes();
        $xml .= ' />';
        return $xml;
    }

}

class Conceptos extends XML {
    public $concepto;
    private $idx = 0;
    function __construct() {
        $this->concepto = array();
    }

    public function addConcepto() {
        $this->idx++;
        $this->concepto[$this->idx] = new Concepto();
        return $this->concepto[$this->idx];
    }

    public function getNode() {
        $nl = $this->nl();
        if (count($this->concepto)>0) {
            $xml = '<cfdi:Conceptos>' . $nl;
            for ($idx = 1; $idx<=$this->idx; $idx++) {
                $xml .= $this->concepto[$idx]->getNode() . $nl;
            }
            $xml .= '</cfdi:Conceptos>';
            return $xml;
        } else {
            throw new Exception('nodo concepto de conceptos es requerido por el SAT');
        }
    }

}

class Retencion extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['impuesto'] = '';
        $this->atributos['importe'] = '';
        $this->rules = array();
        $this->rules['impuesto'] = 'R';
        $this->rules['importe'] = 'R';
    }

    public function getNode() {
        $xml = '<cfdi:Retencion ' . $this->getAtributes() . ' />';
        return $xml;
    }

}

class Traslado extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['impuesto'] = '';
        $this->atributos['tasa'] = '';
        $this->atributos['importe'] = '';
        $this->rules = array();
        $this->rules['impuesto'] = 'R';
        $this->rules['tasa'] = 'R';
        $this->rules['importe'] = 'R';
    }

    public function getNode() {
        $xml = '<cfdi:Traslado ' . $this->getAtributes() . ' />';
        return $xml;
    }

}

class Impuestos extends XML {
    public $Retenciones;
    public $Traslados;
    private $idxR = 0;
    private $idxT = 0;
    function __construct() {
        $this->atributos = array();
        $this->atributos['totalImpuestosRetenidos'] = '';
        $this->atributos['totalImpuestosTrasladados'] = '';
        $this->rules = array();
        $this->rules['totalImpuestosRetenidos'] = 'O';
        $this->rules['totalImpuestosTrasladados'] = 'O';
    }

    public function addRetencion() {
        $this->idxR++;
        $this->Retenciones[$this->idxR] = new Retencion();
        return $this->Retenciones[$this->idxR];
    }

    public function addTraslado() {
        $this->idxT++;
        $this->Traslados[$this->idxT] = new Traslado();
        return $this->Traslados[$this->idxT];
    }

    public function getNode() {
        $nl = $this->nl();
        if (count($this->Retenciones)>0 || count($this->Traslados)>0) {
            $xml = '<cfdi:Impuestos ' . $this->getAtributes() . '>' . $nl;
            if (count($this->Retenciones)>0) {
                $xml .= '<cfdi:Retenciones>' . $nl;
                for ($idx = 1; $idx<=$this->idxR; $idx++) {
                    $xml .= $this->Retenciones[$idx]->getNode() . $nl;
                }
                $xml .= '</cfdi:Retenciones>' . $nl;
            }
            if (count($this->Traslados)>0) {
                $xml .= '<cfdi:Traslados>' . $nl;
                for ($idx = 1; $idx<=$this->idxT; $idx++) {
                    $xml .= $this->Traslados[$idx]->getNode() . $nl;
                }
                $xml .= '</cfdi:Traslados>' . $nl;
            }
            $xml .= '</cfdi:Impuestos>';
        } else {
            $xml .= '<cfdi:Impuestos />';
        }
        return $xml;
    }

}

class LocalRetencion extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['ImpLocRetenido'] = '';
        $this->atributos['TasadeRetencion'] = '';
        $this->atributos['Importe'] = '';
        $this->rules = array();
        $this->rules['ImpLocRetenido'] = 'R';
        $this->rules['TasadeRetencion'] = 'R';
        $this->rules['Importe'] = 'R';
    }

    public function getNode() {
        $xml = '<implocal:RetencionesLocales ' . $this->getAtributes() . ' />';
        return $xml;
    }

}

class LocalTraslado extends XML {
    function __construct() {
        $this->atributos = array();
        $this->atributos['ImpLocTrasladado'] = '';
        $this->atributos['TasadeTraslado'] = '';
        $this->atributos['Importe'] = '';
        $this->rules = array();
        $this->rules['ImpLocTrasladado'] = 'R';
        $this->rules['TasadeTraslado'] = 'R';
        $this->rules['Importe'] = 'R';
    }

    public function getNode() {
        $xml = '<implocal:TrasladosLocales ' . $this->getAtributes() . ' />';
        return $xml;
    }

}

class ImpuestosLocales extends XML {
    public $LocalRetenciones;
    public $LocalTraslados;
    private $idxR = 0;
    private $idxT = 0;
    function __construct() {
        $this->atributos = array();
        $this->atributos['version'] = '';
        $this->atributos['TotaldeRetenciones'] = '';
        $this->atributos['TotaldeTraslados'] = '';
        $this->rules = array();
        $this->rules['version'] = 'R';
        $this->rules['TotaldeRetenciones'] = 'R';
        $this->rules['TotaldeTraslados'] = 'R';
    }

    public function addRetencion() {
        $this->idxR++;
        $this->LocalRetenciones[$this->idxR] = new LocalRetencion();
        return $this->LocalRetenciones[$this->idxR];
    }

    public function addTraslado() {
        $this->idxT++;
        $this->LocalTraslados[$this->idxT] = new LocalTraslado();
        return $this->LocalTraslados[$this->idxT];
    }

    public function getNode() {
        $nl = $this->nl();
        if (count($this->LocalRetenciones)>0 || count($this->LocalTraslados)>0) {
            $xml = '<implocal:ImpuestosLocales ' . $this->getAtributes() . '>' . $nl;
            if (count($this->LocalRetenciones)>0) {
                for ($idx = 1; $idx<=$this->idxR; $idx++) {
                    $xml .= $this->LocalRetenciones[$idx]->getNode() . $nl;
                }
            }
            if (count($this->LocalTraslados)>0) {
                for ($idx = 1; $idx<=$this->idxT; $idx++) {
                    $xml .= $this->LocalTraslados[$idx]->getNode() . $nl;
                }
            }
            $xml .= '</implocal:ImpuestosLocales>';
        } else {
            $xml .= '<implocal:ImpuestosLocales ' . $this->getAtributes() . ' />';
        }
        return $xml;
    }

}

class CFDI {
    public $comprobante;
    public $emisor;
    public $receptor;
    public $conceptos;
    public $impuestos;
    public $impuestosLocales;
    public $complementos;
    public $complemento;
    public $addenda;
    private $xmlstream;
    private $cOriginal;
    private $cOriginalTFD;
    private $sign;
    private $csd;
    private $key;
    private $timbre;
    private $timeTimbre;
    private $noCertificadoTimbre;
    private $UUID;
    static $PAC;
//Default PAC EDICOM
    function __construct($PAC = 'EDICOM') {
        $this->setPac($PAC);
        $this->comprobante = new Comprobante();
        $this->emisor = new Emisor();
        $this->receptor = new Receptor();
        $this->conceptos = new Conceptos();
        $this->impuestos = new Impuestos();
        $this->impuestosLocales = new ImpuestosLocales();
        $this->complementos = false;

        $this->impuestos_retencion = array();
        $this->impuestos_traslado = array();
        $this->addenda = array();
    }

    public function setPac($PAC = 'EDICOM') {
        CFDI::$PAC = $PAC;
        return $this;
    }

    public function getPac() {
        return CFDI::$PAC;
    }

    public function getNode() {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:implocal="http://www.sat.gob.mx/implocal" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . $this->comprobante->getAtributes() . ' >
                    ' . $this->emisor->getNode() . '
                    ' . $this->receptor->getNode() . '
                    ' . $this->conceptos->getNode() . '
                    ' . $this->impuestos->getNode();
        if ($this->complementos) {
            $xml .= '<cfdi:Complemento>' . $this->impuestosLocales->getNode() . '</cfdi:Complemento>';
        }
        $xml .= '</cfdi:Comprobante>';
        $this->xmlstream = $xml;
        return $xml;
    }

    public function getCadenaOriginal($xmlstream = null) {
        //echo $this->xmlstream;
        $xml = new DomDocument();
        $original = '';
        if ($xmlstream==null) {
            //$this->xmlstream = utf8_decode($this->xmlstream);
            $tmp = str_replace('&amp;', '@1@', $this->xmlstream);
            if ($xml->loadXML($this->xmlstream)) {
                $xsl = new DomDocument;
                if ($xsl->load(realpath("ostring32.xsl"))) {
                    $proc = new xsltprocessor();
                    $proc->importStyleSheet($xsl);
                    $original = $proc->transformToXML($xml);
                }
            }
        } else {
            $tmp = str_replace('&amp;', '@1@', $xmlstream);
            if ($xml->loadXML($tmp)) {
                $xsl = new DomDocument;
                $xsl->load(realpath("ostring32.xsl"));
                $proc = new xsltprocessor();
                $proc->importStyleSheet($xsl);
                $original = $proc->transformToXML($xml);
            }
        }
        if (strlen($original)>0) {
            //$aux=str_replace('@1@','&amp;',$original);
            $this->cOriginal = $original;
            return $original;
        } else {
	    echo $tmp;
            throw new Exception('No fue posible obtener la cadena original');
        }
    }

    public function getCadenaOriginalTFD($xmlstream = null) {
        $xml = new DomDocument();
        $original = '';
        if ($xmlstream==null) {
            //$this->xmlstream = utf8_decode($this->xmlstream);
            if ($xml->loadXML($this->xmlstream)) {
                $xsl = new DomDocument;
                if ($xsl->load(realpath("cadenaoriginalTFD.xsl"))) {
                    $proc = new xsltprocessor();
                    $proc->importStyleSheet($xsl);
                    $original = $proc->transformToXML($xml);
                }
            }
        } else {
            if ($xml->loadXML($xmlstream)) {
                $xsl = new DomDocument;
                $xsl->load($xmlstream);
                $proc = new xsltprocessor();
                $proc->importStyleSheet($xsl);
                $original = $proc->transformToXML($xml);
            }
        }
        if (strlen($original)>0) {
            $this->cOriginalTFD = $original;
            $aux2 = explode('|', $this->cOriginalTFD);
            $this->UUID = $aux2[2];
            $this->timeTimbre = $aux2[3];
            $posI = strpos($this->xmlstream, 'selloSAT="') + strlen('selloSAT="');
            $posF = strpos($this->xmlstream, '"', $posI + strlen('selloSAT="'));
            $this->timbre = substr($this->xmlstream, $posI, $posF - $posI);
            $this->noCertificadoTimbre = $aux2[5];
            $this->cOriginalTFD = '||' . $aux2[1] . '|' . $aux2[2] . '|' . $aux2[3] . '|' . $aux2[4] . '|' . $aux2[5] . '||';
            return '||' . $aux2[1] . '|' . $aux2[2] . '|' . $aux2[3] . '|' . $aux2[4] . '|' . $aux2[5] . '||';
        } else {
            throw new Exception('No fue posible obtener la cadena original TFD');
        }
    }

    public function getUUID() {
        return $this->UUID;
    }

    public function getTimbreTime() {
        return $this->timeTimbre;
    }

    public function getTimbre() {
        return $this->timbre;
    }

    public function getCOriginalTFD() {
        return $this->cOriginalTFD;
    }

    public function getSello() {
        return $this->sign;
    }

    public function getnoCertificadoTimbre() {
        return $this->noCertificadoTimbre;
    }

    public function getQRCode($re = '', $rr = '', $Total = '0.00') {
        $format = 'J';
        $size = '10';
        if ($Total<0) {
            $Total = $Total * -1;
        }
        $data = "?re=" . $re . "&rr=" . $rr . "&tt=" . number_format($Total, 6, '.', '') . "&id=" . $this->UUID;
        qr_code($data, "M", $format, $size, 7, $this->UUID);
    }

    public function getSign($coriginal = null) {
        if ($coriginal==null) {
            $coriginal = utf8_encode($this->cOriginal);
            $coriginal = $this->cOriginal;
            $array = explode('>', $coriginal);
            $coriginal = trim($array[1]);
            $coriginal = str_replace('&amp;', '&', $coriginal);
            $coriginal = str_replace('&quot;', '"', $coriginal);
            $coriginal = str_replace('&apos;', "'", $coriginal);
            $coriginal = str_replace('&lt;', "<", $coriginal);
            $coriginal = str_replace('&gt;', ">", $coriginal);
        }
        //echo realpath($this->key);
        $fp = fopen(realpath($this->key), "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);
        $pkeyid = openssl_get_privatekey($priv_key);
        openssl_sign($coriginal, $signature, $pkeyid, OPENSSL_ALGO_SHA1);
        openssl_free_key($pkeyid);
        $signature = base64_encode($signature);
        if (strlen($signature)>0) {
            $this->sign = $signature;
            return $signature;
        } else {
            throw new Exception('No fue posible obtener el sello');
        }
    }

    public function setCSD($csd) {
        $this->csd = $csd;
    }

    public function setKEY($key) {
        $this->key = $key;
    }

    private function getCSD() {
        $fp = fopen(realpath($this->csd), "r");
        $cert = fread($fp, 8192);
        return base64_encode($cert);
    }

    public function getXML() {
        try{
            switch($_SESSION['InvoiceService']) {
                case 'INVOICE_ONE':
                    return $this->getXML_InvoiceOne();
                    break;
                case 'FINKOK' :
                    return $this->getXML_Finkok();
                default :
                case 'EDICOM' :
                    return $this->getXML_Edicom();
                break;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getXML_Finkok(){
          return $this->getXML_Edicom();
    }
    public function getXML_InvoiceOne(){
          try{
            $this->getNode();
            $this->getCadenaOriginal();
            $this->getSign();
            $this->xmlstream=str_replace('sello=" "','sello="'.$this->sign.'"',$this->xmlstream);
            $this->xmlstream=str_replace('certificado=" "','certificado="'.$this->getCSD().'"',$this->xmlstream);
            $invoice=new InvoiceClass();
            if($_SESSION['CFDIProduccion']==0){
                $myCFDI=$invoice->timbrarCFDIOne_Prueba($this->xmlstream);
            }else{
                $myCFDI=$invoice->timbrarCFDIOne($this->xmlstream);
            }
            $this->xmlstream=$myCFDI;
            $this->getCadenaOriginalTFD();
            $this->saveXml();
          } catch (Exception $e) {
            throw new Exception('Error en comunicacion: ' . $e->getMessage());
          }
          return $this->xmlstream;
        }
    public function getXML_Edicom(){
        
	//echo $this->xmlstream;
        $this->getNode();
	$this->getCadenaOriginal();
	$this->getSign();
        //$this->xmlstream=utf8_decode($this->xmlstream);
        $this->xmlstream = str_replace('sello=" "', 'sello="' . $this->sign . '"', $this->xmlstream);
        $this->xmlstream = str_replace('certificado=" "', 'certificado="' . $this->getCSD() . '"', $this->xmlstream);
        //$this->xmlstream=utf8_encode($this->xmlstream);
        //echo $this->xmlstream;

        $zipfile = new zipfile();
        $zipfile->add_file($this->xmlstream, "myfac.xml");
        //header("Content-type: application/octet-stream");
        //header("Content-disposition: attachment; filename=zipfile.zip");
        //echo $zipfile->file();
        //echo $this->xmlstream;
 	$myCFDI = $this->timbrarCFDI(base64_encode($zipfile->file()), base64_encode($this->xmlstream));
	//printf($myCFDI);
//echo $this->xmlstream;
        $this->xmlstream = ZipLibrary::decompressZipStream($myCFDI);
        $this->getCadenaOriginalTFD();
        $this->saveXml();
//echo $this->xmlstream;

        return $this->xmlstream;
    }

    private function saveXml($filename = null) {
        $flag = false;
        if (mkdir('XMLFacturacionElectronica/xmlbycfdi', 0777)) {
            $flag = true;
        } else if (chdir('XMLFacturacionElectronica/xmlbycfdi')) {
            $flag = true;
        } else {
            throw new Exception('No se tienen permisos de escritura, asegurece de tener permisos suficientes');
        }
        if ($flag) {
            $p = chdir('XMLFacturacionElectronica/xmlbycfdi');
            $path = realpath($p);
            $path = str_replace('\\', '/', $path);
        }

        if ($flag) {
            if ($filename==null) {
                $file = fopen($path . '/' . $this->UUID . '.xml', 'w');
                fwrite($file, $this->xmlstream);
                fclose($file);
            } else {
                $file = fopen($filename, 'w');
                fwrite($file, $this->xmlstream);
                fclose($file);
            }
        } else {
            throw new Exception('No se puede escribir en el directorio especificado');
        }
    }

    public static function cancelCFDI($rfc, $uuid, $pfx, $pfxPassword) {
        try{
            switch($_SESSION['InvoiceService']) {
                case 'INVOICE_ONE':
                    return self::cancelCFDIInvoiceOne($rfc,$uuid,$pfx,$pfxPassword);
                    break;
                case 'FINKOK' :
                    return self::cancelCFDIFinkok($rfc,$uuid,$pfx,$pfxPassword);
                default :
                case 'EDICOM' :
                    return self::cancelCFDIEdicom($rfc,$uuid,$pfx,$pfxPassword);
                break;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
    public static function cancelCFDIInvoiceOne($rfc,$uuid,$pfx,$pfxPassword){
        try{
            $Object = new InvoiceClass();
            if($_SESSION['CFDIProduccion']==0){
                $uuid = $Object->cancelaCFDI_OnePruebas($rfc,$uuid,$pfx,$pfxPassword);
            }else{
                $uuid = $Object->cancelaCFDI_One($rfc,$uuid,$pfx,$pfxPassword);
            }
        } catch (Exception $e) {
            throw $e;
        }
        return $uuid;
    }

    public static function cancelCFDIFinkok($rfc, $uuid, $pfx, $pfxPassword) {
        return self::cancelCFDIInvoiceOne($rfc,$uuid,$pfx,$pfxPassword);
    }

    public static function cancelCFDIEdicom($rfc,$uuid,$pfx,$pfxPassword){
        if ($_SESSION['CFDIProduccion']==0) {
            return $uuid;
        } else {
            $xmlData = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cfdi="http://cfdi.service.ediwinws.edicom.com">
                <soapenv:Header/>
                    <soapenv:Body>
                        <cfdi:cancelaCFDi>
                            <cfdi:user>REA080108FS0</cfdi:user>
                            <cfdi:password>odtflzrch</cfdi:password>
                            <cfdi:rfc>' . $rfc . '</cfdi:rfc>
                            <cfdi:uuid>' . $uuid . '</cfdi:uuid>
                            <cfdi:pfx>' . $pfx . '</cfdi:pfx>
                            <cfdi:pfxPassword>' . $pfxPassword . '</cfdi:pfxPassword>
                        </cfdi:cancelaCFDi>
                    </soapenv:Body>
                </soapenv:Envelope>';
        }
        //var_dump($xmlData);
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi");
        curl_setopt($tuCurl, CURLOPT_PORT, 443);
        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($tuCurl, CURLOPT_HEADER, 0);
        curl_setopt($tuCurl, CURLOPT_POST, 1);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $xmlData);
        curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array(
            "Content-Type: text/xml",
            "SOAPAction: \"http://cfdi.service.ediwinws.edicom.com/CFDi/getCfdiRequest\"",
            "Content-length: " . strlen($xmlData)
        ));

        $tuData = curl_exec($tuCurl);
        if (curl_errno($tuCurl)) {
            throw new Exception('error en comunicacion: ' . curl_error($tuData));
        }
        curl_close($tuData);
        $pIni = strpos($tuData, '<cancelaCFDiReturn>');
        $pFin = strpos($tuData, '</cancelaCFDiReturn>');
        if (($pIni!==false) && ($pFin!==false)) {
            return $uuid;
        } else {
            throw new Exception('No fue posible cancelar: ' . $tuData);
        }
    }

    private function timbrarCFDI($file64, $xml) {
        switch(CFDI::$PAC) {
            case 'EDICOM' :
                return $this->timbrarEdicom($file64);
                break;
            default :
            case 'FINKOK' :
                return $this->timbrarFinkok($file64, $xml);
        }
    }

    private function timbrarFinkok($Zip, $file64) {
        $username = 'rleal@realhost.com.mx';
        $password = 'ya#uRda@Ga6b';
        if ($_SESSION['CFDIProduccion']!=0)
            $url = "https://facturacion.finkok.com/servicios/soap/stamp.wsdl";
        //else
        $url = "http://demo-facturacion.finkok.com/servicios/soap/stamp.wsdl";
        $client = new SoapClient($url);

        $params = array(
            "xml" => $file64,
            "username" => $username,
            "password" => $password
        );
        $response = $client->__soapCall("stamp", array($params));
        // var_dump($response);
        // exit ;
        return $Respuesta;
    }

    private function timbrarEdicom($file64) {
//echo $this->xmlstream;
        if ($_SESSION['CFDIProduccion']==0) {
            $xmlData = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cfdi="http://cfdi.service.ediwinws.edicom.com">
                <soapenv:Header/>
                    <soapenv:Body>
                        <cfdi:getCfdiTest>
                            <cfdi:user>REA080108FS0</cfdi:user>
                            <cfdi:password>odtflzrch</cfdi:password>
                            <cfdi:file>' . $file64 . '</cfdi:file>
                        </cfdi:getCfdiTest>
                    </soapenv:Body>
                </soapenv:Envelope>';
        } else {
//echo "PROD";
            $xmlData = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cfdi="http://cfdi.service.ediwinws.edicom.com">
                <soapenv:Header/>
                    <soapenv:Body>
                        <cfdi:getCfdi>
                            <cfdi:user>REA080108FS0</cfdi:user>
                            <cfdi:password>odtflzrch</cfdi:password>
                            <cfdi:file>' . $file64 . '</cfdi:file>
                        </cfdi:getCfdi>
                    </soapenv:Body>
                </soapenv:Envelope>';
        }
        $tuCurl = curl_init();
        curl_setopt($tuCurl, CURLOPT_URL, "https://cfdiws.sedeb2b.com/EdiwinWS/services/CFDi");
        curl_setopt($tuCurl, CURLOPT_PORT, 443);
        curl_setopt($tuCurl, CURLOPT_VERBOSE, 0);
        curl_setopt($tuCurl, CURLOPT_HEADER, 0);
        curl_setopt($tuCurl, CURLOPT_POST, 1);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $xmlData);
        if ($_SESSION['CFDIProduccion']==0) {
            curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml",
                "SOAPAction: \"http://cfdi.service.ediwinws.edicom.com/CFDi/getCfdiTestRequest\"",
                "Content-length: " . strlen($xmlData)
            ));
        } else {
            curl_setopt($tuCurl, CURLOPT_HTTPHEADER, array(
                "Content-Type: text/xml",
                "SOAPAction: \"http://cfdi.service.ediwinws.edicom.com/CFDi/getCfdiRequest\"",
                "Content-length: " . strlen($xmlData)
            ));
        }

        $tuData = curl_exec($tuCurl);
//echo $tuData;        
if (curl_errno($tuCurl)) {
            throw new Exception('error en comunicacion: ' . curl_error($tuData));
        }
        curl_close($tuData);
        if ($_SESSION['CFDIProduccion']==0) {
            $pIni = strpos($tuData, '<getCfdiTestReturn>');
            $pFin = strpos($tuData, '</getCfdiTestReturn>');
            if (($pIni!==false) && ($pFin!==false)) {
                $xmlTimbrado = substr($tuData, $pIni + strlen('<getCfdiTestReturn>'), $pFin);
                return base64_decode($xmlTimbrado);
            } else {
                throw new Exception('error en comunicacion: ' . $tuData);
            }
        } else {
            $pIni = strpos($tuData, '<getCfdiReturn>');
            $pFin = strpos($tuData, '</getCfdiReturn>');
            if (($pIni!==false) && ($pFin!==false)) {
                $xmlTimbrado = substr($tuData, $pIni + strlen('<getCfdiReturn>'), $pFin);
		$_XML_TIMBRADO = base64_decode($xmlTimbrado);
		//echo $_XML_TIMBRADO;
		return $_XML_TIMBRADO;
            } else {
                throw new Exception('error en comunicacion: ' . $tuData);
            }
        }
    }

    public function getSHA1() {
        $array = explode('>', $this->cOriginal);
        return sha1(trim($array[1]));
    }

}

/* try{
 $cfdi=new CFDI();
 $cfdi->comprobante->setAtribute('fecha','2011-03-31T00:00:00');
 $cfdi->comprobante->setAtribute('total','1000.0000');
 $cfdi->comprobante->setAtribute('subTotal','1000.0000');
 $cfdi->comprobante->setAtribute('tipoDeComprobante','ingreso');
 $cfdi->comprobante->setAtribute('formaDePago','pago en una sola exhibicion');
 $cfdi->comprobante->setAtribute('noCertificado','00001000000101176539');

 $cfdi->emisor->setAtribute('rfc','ica0212267i1');
 $cfdi->emisor->setAtribute('nombre','Instan Call');
 $cfdi->emisor->domicilioFiscal->setAtribute('calle','privada mercurio');
 $cfdi->emisor->domicilioFiscal->setAtribute('municipio','privada mercurio');
 $cfdi->emisor->domicilioFiscal->setAtribute('estado','sinaloa');
 $cfdi->emisor->domicilioFiscal->setAtribute('pais','mexico');
 $cfdi->emisor->domicilioFiscal->setAtribute('codigoPostal','82180');
 $cfdi->emisor->expedidoEn->setAtribute('calle','privada mercurio');
 $cfdi->emisor->expedidoEn->setAtribute('municipio','privada mercurio');
 $cfdi->emisor->expedidoEn->setAtribute('estado','sinaloa');
 $cfdi->emisor->expedidoEn->setAtribute('pais','mexico');
 $cfdi->emisor->expedidoEn->setAtribute('codigoPostal','82180');

 $cfdi->receptor->setAtribute('rfc','XAXX010101000');
 $cfdi->receptor->domicilio->setAtribute('pais','mexico');

 $Concep=$cfdi->conceptos->addConcepto();
 $Concep->setAtribute('unidad','--');
 $Concep->setAtribute('cantidad','2');
 $Concep->setAtribute('descripcion','Cocacolas');
 $Concep->setAtribute('valorUnitario','7.50');
 $Concep->setAtribute('importe','15.00');
 $Concep=$cfdi->conceptos->addConcepto();
 $Concep->setAtribute('unidad','--');
 $Concep->setAtribute('cantidad','2');
 $Concep->setAtribute('descripcion','Cocacolas');
 $Concep->setAtribute('valorUnitario','7.50');
 $Concep->setAtribute('importe','15.00');
 $Concep=$cfdi->conceptos->addConcepto();
 $Concep->setAtribute('unidad','--');
 $Concep->setAtribute('cantidad','2');
 $Concep->setAtribute('descripcion','Cocacolas');
 $Concep->setAtribute('valorUnitario','7.50');
 $Concep->setAtribute('importe','15.00');

 $traslado=$cfdi->impuestos->addTraslado();
 $traslado->setAtribute('tasa','0.00');
 $traslado->setAtribute('importe','0.00');
 $traslado->setAtribute('impuesto','iva');
 $retencion=$cfdi->impuestos->addRetencion();
 $retencion->setAtribute('importe','1.60');
 $retencion->setAtribute('impuesto','iva');

 $cfdi->setCSD('00001000000101176539.cer');
 $cfdi->setKEY("ica0212267i1_1002101718s.key.pem");
 //$cfdi->getNode();
 // $cfdi->getCadenaOriginal();
 //echo $cfdi->getSHA1();
 $cfdi->getXML();
 }catch(Exception $e){
 echo $e->getMessage();
 }   */
?>
