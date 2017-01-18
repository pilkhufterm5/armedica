<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FacturaElectronica
 *
 * @author roberto
 */
function v($text){
    if(isSet($text) && trim($text)!='')
        return true;
    else
        return false;
}

class Comprobante{

    public static function espanol($texto) //REPARA CUALQUIER TEXTO A UTF-8
    {
        $texto = htmlentities($texto , ENT_QUOTES); //No permite codigo HTML
        $texto = str_replace("\r","<br />",$texto); //Asignar codigo espacios
        $texto = utf8_encode($texto); //ENCODE A UTF-8
        $texto = iconv("ISO-8859-1" , "UTF-8", $texto); // Convierte ISO-8859-1 UTF-8
        return $texto;
    }
    //atributos requeridos
    public $folio;
    public $fecha;
    public $sello;
    public $noAprobacion;
    public $anoAprobacion;
    public $formaDePago;
    public $noCertificado;
    public $subTotal;
    public $total;
    public $tipoDeComprobante;
    //public $sello; este se agrega al .XML despues de calcular el sello con la funcion en utils/FacturaElectronica::calculateCadenaOriginal(...)
    //objectos requeridos
    public $Emisor;
    public $Receptor;
    public $Conceptos;
    public $Impuestos;
    //atributos opcionales
    public $serie;
    public $certificado;
    public $condicionesDePago;
    public $descuento;
    public $motivoDescuento;
    public $metodoDePago;
    function __construct($folio, $fecha, $sello, $noAprobacion, $anoAprobacion, $formaDePago, $noCertificado, $subTotal, $total, $tipoDeComprobante, Emisor $Emisor, Receptor $Receptor, Conceptos $Conceptos, Impuestos $Impuestos) {
        $this->folio = $folio;
        $this->fecha = $fecha;
        $this->sello = $sello;
        $this->noAprobacion = $noAprobacion;
        $this->anoAprobacion = $anoAprobacion;
        $this->formaDePago = $formaDePago;
        $this->noCertificado = $noCertificado;
        $this->subTotal = $subTotal;
        $this->total = $total;
        $this->tipoDeComprobante = $tipoDeComprobante;
        $this->Emisor = $Emisor;
        $this->Receptor = $Receptor;
        $this->Conceptos = $Conceptos;
        $this->Impuestos = $Impuestos;
    }
    public function setSerie($serie) {
        $this->serie = $serie;
    }
    
    public function saveAsXML($xmlFileName){
        //con paths a archivos locales
        /*$Comprobante = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sat.gob.mx/cfd/2" xsi:schemaLocation="http://www.sat.gob.mx/cfd/2 ../satFiles/cfdv2.xsd" version="2.0"></Comprobante>');*/
        //con los url al servidor del SAT, al parecer aun asi funciona offline, dado a como hago la validacion entre el xml y el xsd
        $Comprobante = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Comprobante xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sat.gob.mx/cfd/2" xsi:schemaLocation="http://www.sat.gob.mx/cfd/2 http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv2.xsd" version="2.0"></Comprobante>');
        if(v($this->folio))$Comprobante->addAttribute('folio', $this->folio);
        if(v($this->fecha))$Comprobante->addAttribute('fecha', $this->fecha);
        if(v($this->sello))$Comprobante->addAttribute('sello', $this->sello);
        if(v($this->noAprobacion))$Comprobante->addAttribute('noAprobacion', $this->noAprobacion);
        if(v($this->anoAprobacion))$Comprobante->addAttribute('anoAprobacion', $this->anoAprobacion);
        if(v($this->formaDePago))$Comprobante->addAttribute('formaDePago', $this->formaDePago);
        if(v($this->noCertificado))$Comprobante->addAttribute('noCertificado', $this->noCertificado);
        if(v($this->subTotal))$Comprobante->addAttribute('subTotal', $this->subTotal);
        if(v($this->total))$Comprobante->addAttribute('total', $this->total);
        if(v($this->tipoDeComprobante))$Comprobante->addAttribute('tipoDeComprobante', $this->tipoDeComprobante);
        //atributos opcionales
        if(v($this->serie))$Comprobante->addAttribute('serie', $this->serie);
        if(v($this->certificado))$Comprobante->addAttribute('certificado', $this->certificado);
        if(v($this->condicionesDePago))$Comprobante->addAttribute('condicionesDePago', $this->condicionesDePago);
        if(v($this->descuento))$Comprobante->addAttribute('descuento', $this->descuento);
        if(v($this->motivoDescuento))$Comprobante->addAttribute('motivoDescuento', $this->motivoDescuento);
        if(v($this->metodoDePago))$Comprobante->addAttribute('metodoDePago', $this->metodoDePago);
        //termina atributos opcionales
        
        $Emisor = $Comprobante->addChild('Emisor');
        if(v($this->Emisor->rfc))$Emisor->addAttribute('rfc', $this->Emisor->rfc);
        if(v($this->Emisor->nombre))$Emisor->addAttribute('nombre', $this->Emisor->nombre);
        $DomicilioFiscal = $Emisor->addChild('DomicilioFiscal');
        if(v($this->Emisor->DomicilioFiscal->calle))$DomicilioFiscal->addAttribute('calle', $this->Emisor->DomicilioFiscal->calle);
        if(v($this->Emisor->DomicilioFiscal->municipio))$DomicilioFiscal->addAttribute('municipio', $this->Emisor->DomicilioFiscal->municipio);
        if(v($this->Emisor->DomicilioFiscal->estado))$DomicilioFiscal->addAttribute('estado', $this->Emisor->DomicilioFiscal->estado);
        if(v($this->Emisor->DomicilioFiscal->pais))$DomicilioFiscal->addAttribute('pais', $this->Emisor->DomicilioFiscal->pais);
        if(v($this->Emisor->DomicilioFiscal->codigoPostal))$DomicilioFiscal->addAttribute('codigoPostal', $this->Emisor->DomicilioFiscal->codigoPostal);
        //atributos opcionales
        if(v($this->Emisor->DomicilioFiscal->noExterior))$DomicilioFiscal->addAttribute('noExterior', $this->Emisor->DomicilioFiscal->noExterior);
        if(v($this->Emisor->DomicilioFiscal->noInterior))$DomicilioFiscal->addAttribute('noInterior', $this->Emisor->DomicilioFiscal->noInterior);
        if(v($this->Emisor->DomicilioFiscal->colonia))$DomicilioFiscal->addAttribute('colonia', $this->Emisor->DomicilioFiscal->colonia);
        if(v($this->Emisor->DomicilioFiscal->localidad))$DomicilioFiscal->addAttribute('localidad', $this->Emisor->DomicilioFiscal->localidad);
        if(v($this->Emisor->DomicilioFiscal->referencia))$DomicilioFiscal->addAttribute('referencia', $this->Emisor->DomicilioFiscal->referencia);
        //termina atributos opcionales
        $ExpedidoEn = $Emisor->addChild('ExpedidoEn');
        if(v($this->Emisor->ExpedidoEn->pais))$ExpedidoEn->addAttribute('pais', $this->Emisor->ExpedidoEn->pais);
        //atributos opcionales
        if(v($this->Emisor->ExpedidoEn->calle))$ExpedidoEn->addAttribute('calle', $this->Emisor->ExpedidoEn->calle);
        if(v($this->Emisor->ExpedidoEn->noExterior))$ExpedidoEn->addAttribute('noExterior', $this->Emisor->ExpedidoEn->noExterior);
        if(v($this->Emisor->ExpedidoEn->noInterior))$ExpedidoEn->addAttribute('noInterior', $this->Emisor->ExpedidoEn->noInterior);
        if(v($this->Emisor->ExpedidoEn->colonia))$ExpedidoEn->addAttribute('colonia', $this->Emisor->ExpedidoEn->colonia);
        if(v($this->Emisor->ExpedidoEn->localidad))$ExpedidoEn->addAttribute('localidad', $this->Emisor->ExpedidoEn->localidad);
        if(v($this->Emisor->ExpedidoEn->referencia))$ExpedidoEn->addAttribute('referencia', $this->Emisor->ExpedidoEn->referencia);
        if(v($this->Emisor->ExpedidoEn->municipio))$ExpedidoEn->addAttribute('municipio', $this->Emisor->ExpedidoEn->municipio);
        if(v($this->Emisor->ExpedidoEn->estado))$ExpedidoEn->addAttribute('estado', $this->Emisor->ExpedidoEn->estado);
        if(v($this->Emisor->ExpedidoEn->codigoPostal))$ExpedidoEn->addAttribute('codigoPostal', $this->Emisor->ExpedidoEn->codigoPostal);
        //termina atributos opcionales

        $Receptor = $Comprobante->addChild('Receptor');
        if(v($this->Receptor->rfc))$Receptor->addAttribute('rfc', $this->Receptor->rfc);
        //atributos opcionales
        if(v($this->Receptor->nombre))$Receptor->addAttribute('nombre', $this->Receptor->nombre);
        //termina atributos opcionales
        $Domicilio = $Receptor->addChild('Domicilio');
        if(v($this->Receptor->Domicilio->pais))$Domicilio->addAttribute('pais', $this->Receptor->Domicilio->pais);
        //atributos opcionales
        if(v($this->Receptor->Domicilio->calle))$Domicilio->addAttribute('calle', $this->Receptor->Domicilio->calle);
        if(v($this->Receptor->Domicilio->noExterior))$Domicilio->addAttribute('noExterior', $this->Receptor->Domicilio->noExterior);
        if(v($this->Receptor->Domicilio->noInterior))$Domicilio->addAttribute('noInterior', $this->Receptor->Domicilio->noInterior);
        if(v($this->Receptor->Domicilio->colonia))$Domicilio->addAttribute('colonia', $this->Receptor->Domicilio->colonia);
        if(v($this->Receptor->Domicilio->localidad))$Domicilio->addAttribute('localidad', $this->Receptor->Domicilio->localidad);
        if(v($this->Receptor->Domicilio->referencia))$Domicilio->addAttribute('referencia', $this->Receptor->Domicilio->referencia);
        if(v($this->Receptor->Domicilio->municipio))$Domicilio->addAttribute('municipio', $this->Receptor->Domicilio->municipio);
        if(v($this->Receptor->Domicilio->estado))$Domicilio->addAttribute('estado', $this->Receptor->Domicilio->estado);
        if(v($this->Receptor->Domicilio->codigoPostal))$Domicilio->addAttribute('codigoPostal', $this->Receptor->Domicilio->codigoPostal);
        //termina atributos opcionales
        
        $Conceptos = $Comprobante->addChild('Conceptos');
        $iteratorConceptos = $this->Conceptos->getIterator();
        while($iteratorConceptos->valid()){
            $c = $iteratorConceptos->current();
            $Concepto = $Conceptos->addChild('Concepto');
            if(v($c->cantidad))$Concepto->addAttribute('cantidad', $c->cantidad);
            if(v($c->descripcion))$Concepto->addAttribute('descripcion', $c->descripcion);
            if(v($c->valorUnitario))$Concepto->addAttribute('valorUnitario', $c->valorUnitario);
            if(v($c->importe))$Concepto->addAttribute('importe', $c->importe);
            //atributos opcionales
            if(v($c->unidad))if($c->unidad)$Concepto->addAttribute('unidad', $c->unidad);
            if(v($c->noIdentificacion))if($c->noIdentificacion)$Concepto->addAttribute('noIdentificacion', $c->noIdentificacion);
            //termina atributos opcionales
            if($c->InformacionesAduaneras && $c->InformacionesAduaneras->count()>0){
                $iteratorInformacionesAduaneras = $c->InformacionesAduaneras->getIterator();
                while($iteratorInformacionesAduaneras->valid()){
                    $InformacionAduanera = $Concepto->addChild('InformacionAduanera');
                    $ia = $iteratorInformacionesAduaneras->current();
                    if(v($ia->numero))$InformacionAduanera->addAttribute('numero', $ia->numero);
                    if(v($ia->fecha))$InformacionAduanera->addAttribute('fecha', $ia->fecha);
                    if(v($ia->aduana))$InformacionAduanera->addAttribute('aduana', $ia->aduana);
                    $iteratorInformacionesAduaneras->next();
                }
                $iteratorConceptos->next();
                continue;
            }
            if($c->Partes && $c->Partes->count()>0){
                $iteratorPartes = $c->Partes->getIterator();
                while($iteratorPartes->valid()){
                    $Parte = $Concepto->addChild('Parte');
                    $p = $iteratorPartes->current();
                    if(v($p->cantidad))$Parte->addAttribute('cantidad', $p->cantidad);
                    if(v($p->descripcion))$Parte->addAttribute('descripcion', $p->descripcion);
                    //atributos opcionales
                    if(v($p->unidad))$Parte->addAttribute('unidad', $p->unidad);
                    if(v($p->noIdentificacion))$Parte->addAttribute('noIdentificacion', $p->noIdentificacion);
                    if(v($p->valorUnitario))$Parte->addAttribute('valorUnitario', $p->valorUnitario);
                    if(v($p->importe))$Parte->addAttribute('importe', $p->importe);
                    //termina atributos opcionales
                    if($p->InformacionesAduaneras && $p->InformacionesAduaneras->count()>0){
                        $iteratorInformacionesAduaneras = $p->InformacionesAduaneras->getIterator();
                        while($iteratorInformacionesAduaneras->valid()){
                            $InformacionAduanera = $Parte->addChild('InformacionAduanera');
                            $ia = $iteratorInformacionesAduaneras->current();
                            if(v($ia->numero))$InformacionAduanera->addAttribute('numero', $ia->numero);
                            if(v($ia->fecha))$InformacionAduanera->addAttribute('fecha', $ia->fecha);
                            if(v($ia->aduana))$InformacionAduanera->addAttribute('aduana', $ia->aduana);
                            $iteratorInformacionesAduaneras->next();
                        }
                    }
                    $iteratorPartes->next();
                }
                $iteratorConceptos->next();
                continue;
            }
            if($c->CuentaPredial){
                $CuentaPredial = $Concepto->addChild('CuentaPredial');
                if(v($c->CuentaPredial->numero))$CuentaPredial->addAttribute('numero', $c->CuentaPredial->numero);
                $iteratorConceptos->next();
                continue;
            }
            $iteratorConceptos->next();
        }

        $Impuestos = $Comprobante->addChild('Impuestos');
        //atributos opcionales
        if(v($this->Impuestos->totalImpuestosRetenidos))$Impuestos->addAttribute('totalImpuestosRetenidos', $this->Impuestos->totalImpuestosRetenidos);
        if(v($this->Impuestos->totalImpuestosTrasladados))$Impuestos->addAttribute('totalImpuestosTrasladados', $this->Impuestos->totalImpuestosTrasladados);
        //termina atributos opcionales
        if($this->Impuestos->Retenciones && $this->Impuestos->Retenciones->count()>0){
            $Retenciones = $Impuestos->addChild('Retenciones');
            $iteratorRetenciones = $this->Impuestos->Retenciones->getIterator();
            while($iteratorRetenciones->valid()){
                $Retencion = $Retenciones->addChild('Retencion');
                $ir = $iteratorRetenciones->current();
                if(v($ir->impuesto))$Retencion->addAttribute('impuesto', $ir->impuesto);
                if(v($ir->importe))$Retencion->addAttribute('importe', $ir->importe);
                $iteratorRetenciones->next();
            }
        }

        if($this->Impuestos->Traslados && $this->Impuestos->Traslados->count()>0){
            $Traslados = $Impuestos->addChild('Traslados');
            $iteratorTraslados = $this->Impuestos->Traslados->getIterator();
            while($iteratorTraslados->valid()){
                $Traslado = $Traslados->addChild('Traslado');
                $it = $iteratorTraslados->current();
                if(v($it->impuesto))$Traslado->addAttribute('impuesto', $it->impuesto);
                if(v($it->tasa))$Traslado->addAttribute('tasa', $it->tasa);
                if(v($it->importe))$Traslado->addAttribute('importe', $it->importe);
                $iteratorTraslados->next();
            }
        }

        //Solo necesario para indentar el XML (podemos quitarlo si no queremos indentar)
        $dom = dom_import_simplexml($Comprobante)->ownerDocument;
        $dom->formatOutput = true;
        $Comprobante = $dom->saveXML();
        $Comprobante = str_replace('|', '-', $Comprobante);
        //se concatena el caracter BOM
        $Comprobante = "\xEF\xBB\xBF" . $Comprobante;
        //Termina se concatena el caracter BOM
        $Comprobante = new SimpleXMLElement($Comprobante);
        //Termina Solo necesario para identar el XML (podemos quitarlo si no queremos identar)
        //regresa 1 si se pudo grabar
        $Comprobante->asXML($xmlFileName);
    }

    //demo, es necesario hacer lo de este metodo con los valores de $_POST en php
    public static function create($xmlFilePathCreate, $idFacturaElectronica, $idFolio, $siguienteFolio, $serie, $noCertificado, $noAprobacion, $anoAprobacion, $rfc, $cerFilePath, $tmpPemFilePath){
        global $db;
        $transno = $idFacturaElectronica;
        
        //Uso:
        //Emisor
        //companies
        $sql = "select regoffice1 Emisor_DomicilioFiscal_calle, regoffice2 Emisor_DomicilioFiscal_noExterior, regoffice3 Emisor_DomicilioFiscal_noInterior, regoffice4 Emisor_DomicilioFiscal_colonia, regoffice5 Emisor_DomicilioFiscal_localidad, regoffice6 Emisor_DomicilioFiscal_referencia, regoffice7 Emisor_DomicilioFiscal_municipio,  regoffice8 Emisor_DomicilioFiscal_estado,  regoffice9 Emisor_DomicilioFiscal_pais, regoffice10 Emisor_DomicilioFiscal_codigoPostal from companies limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
        }
        $row = DB_fetch_array($result);
        $Emisor_DomicilioFiscal_calle = $row['Emisor_DomicilioFiscal_calle'];//1
        //$Emisor_DomicilioFiscal_calle = "     á, é, í, ó, ú,  á, é, í, ó, ú, á, é, í, ó, ú,      ᴹḼⁿỜⱣｉṟṲá, é, í, ó, ú, Ṩṭᴆဘံ္ Ju ||| <>?:|  }{   | !@#$^%&%$#@!&*($&#@  an & Jo||JKKHPFO{!$@! sé & “Niño”";
        $Emisor_DomicilioFiscal_noExterior = $row['Emisor_DomicilioFiscal_noExterior'];//2
        $Emisor_DomicilioFiscal_noInterior = $row['Emisor_DomicilioFiscal_noInterior'];//3
        $Emisor_DomicilioFiscal_colonia = $row['Emisor_DomicilioFiscal_colonia'];//4
        $Emisor_DomicilioFiscal_localidad = $row['Emisor_DomicilioFiscal_localidad'];//5
        $Emisor_DomicilioFiscal_referencia = $row['Emisor_DomicilioFiscal_referencia'];//6
        $Emisor_DomicilioFiscal_municipio = $row['Emisor_DomicilioFiscal_municipio'];//7
        $Emisor_DomicilioFiscal_estado = $row['Emisor_DomicilioFiscal_estado'];//8
        $Emisor_DomicilioFiscal_pais = $row['Emisor_DomicilioFiscal_pais'];//9
        $Emisor_DomicilioFiscal_codigoPostal = $row['Emisor_DomicilioFiscal_codigoPostal'];//10
        $DomicilioFiscal = new DomicilioFiscal($Emisor_DomicilioFiscal_calle, $Emisor_DomicilioFiscal_municipio, $Emisor_DomicilioFiscal_estado, $Emisor_DomicilioFiscal_pais, $Emisor_DomicilioFiscal_codigoPostal);
        $DomicilioFiscal->noExterior = $Emisor_DomicilioFiscal_noExterior;
        $DomicilioFiscal->noInterior = $Emisor_DomicilioFiscal_noInterior;
        $DomicilioFiscal->colonia = $Emisor_DomicilioFiscal_colonia;
        $DomicilioFiscal->localidad = $Emisor_DomicilioFiscal_localidad;
        $DomicilioFiscal->referencia = $Emisor_DomicilioFiscal_referencia;
        //locations
        $sql = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('Aun no esta configurada la informacion de la ubicacion', 1);
        }
        $row = DB_fetch_array($result);
        $Emisor_ExpedidoEn_calle = $row['Emisor_ExpedidoEn_calle'];//1
        $Emisor_ExpedidoEn_noExterior = $row['Emisor_ExpedidoEn_noExterior'];//2
        $Emisor_ExpedidoEn_noInterior = $row['Emisor_ExpedidoEn_noInterior'];//3
        $Emisor_ExpedidoEn_colonia = $row['Emisor_ExpedidoEn_colonia'];//4
        $Emisor_ExpedidoEn_localidad = $row['Emisor_ExpedidoEn_localidad'];//5
        $Emisor_ExpedidoEn_referencia = $row['Emisor_ExpedidoEn_referencia'];//6
        $Emisor_ExpedidoEn_municipio = $row['Emisor_ExpedidoEn_municipio'];//7
        $Emisor_ExpedidoEn_estado = $row['Emisor_ExpedidoEn_estado'];//8
        $Emisor_ExpedidoEn_pais = $row['Emisor_ExpedidoEn_pais'];//9
        $Emisor_ExpedidoEn_codigoPostal = $row['Emisor_ExpedidoEn_codigoPostal'];//10
        $ExpedidoEn = new ExpedidoEn($Emisor_ExpedidoEn_pais);
        $ExpedidoEn->calle = $Emisor_ExpedidoEn_calle;
        $ExpedidoEn->noExterior = $Emisor_ExpedidoEn_noExterior;
        $ExpedidoEn->noInterior = $Emisor_ExpedidoEn_noInterior;
        $ExpedidoEn->colonia = $Emisor_ExpedidoEn_colonia;
        $ExpedidoEn->localidad = $Emisor_ExpedidoEn_localidad;
        $ExpedidoEn->referencia = $Emisor_ExpedidoEn_referencia;
        $ExpedidoEn->municipio = $Emisor_ExpedidoEn_municipio;
        $ExpedidoEn->estado = $Emisor_ExpedidoEn_estado;
        $ExpedidoEn->codigoPostal = $Emisor_ExpedidoEn_codigoPostal;
        //rfc y nombre, debe validarse que el rfc del noCertificado y de companies sean el mismo
        $sql = "select gstno Emisor_rfc, coyname Emisor_nombre from companies limit 1";
        //$result = mysql_query($sql);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se pudo obtener el RFC y Nombre del Emisor', 1);
        }
        //$row = mysql_fetch_array($result, MYSQLI_ASSOC);
        $row = mysql_fetch_array($result);
        $Emisor_rfc = $row['Emisor_rfc'];
        $Emisor_nombre = $row['Emisor_nombre'];
        $Emisor = new Emisor($Emisor_rfc, $Emisor_nombre, $DomicilioFiscal, $ExpedidoEn);
        //Se valida que el rfc del noCertificado y de companies sean igual
        $sql = "select certificado_rfc from rh_factura_electronica_certificado where certificado_no_certificado = '$noCertificado' limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se pudo obtener el RFC de la DB del certificado que se usara', 1);
        }
        $row = mysql_fetch_array($result);
        if($row['certificado_rfc']!=$Emisor_rfc || $rfc!=$row['certificado_rfc'] || $rfc!=$Emisor_rfc)
            throw new Exception('El RFC de la empresa no coincide con el emisor');
        //Termina Se valida que el rfc del noCertificado y de companies sean igual
        
        //sql Comprobante e Impuestos
        $sql = "select cast(debtortrans.ovdiscount as decimal(10,2)) Comprobante_descuento, cast((((debtortrans.ovamount-debtortrans.ovdiscount)+debtortrans.ovgst)/debtortrans.rate) as decimal(10,2)) Comprobante_total, debtortrans.rh_createdate Comprobante_fecha, cast((debtortrans.ovamount/debtortrans.rate) as decimal(10,2)) Comprobante_subtotal, cast((debtortrans.ovgst/debtortrans.rate) as decimal(10,2)) Traslado_importe, rh_transaddress.taxref Receptor_rfc, rh_transaddress.name Receptor_nombre, rh_transaddress.address1 Receptor_calle, rh_transaddress.address2 Receptor_noExterior, rh_transaddress.address3 Receptor_noInterior, rh_transaddress.address4 Receptor_colonia, rh_transaddress.address5 Receptor_localidad, rh_transaddress.address6 Receptor_referencia, rh_transaddress.address7 Receptor_municipio,  rh_transaddress.address8 Receptor_estado,  rh_transaddress.address9 Receptor_pais, rh_transaddress.address10 Receptor_codigoPostal FROM debtortrans, rh_transaddress WHERE rh_transaddress.type = 10 AND debtortrans.type=10 and rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=$transno limit 1";
        //$result = mysql_query($sql);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
        }
        //$row = mysql_fetch_array($result, MYSQLI_ASSOC);
        $row = mysql_fetch_array($result);
        $Comprobante_descuento = $row['Comprobante_descuento'];
        $Comprobante_total = $row['Comprobante_total'];
        $Comprobante_fecha = str_replace(' ', 'T', $row['Comprobante_fecha']);
        $Comprobante_subtotal = $row['Comprobante_subtotal'];
        $Traslado_importe = $row['Traslado_importe'];
        //Receptor
        $Receptor_rfc = $row['Receptor_rfc'];
        $Receptor_nombre = $row['Receptor_nombre'];
        $Receptor_calle = $row['Receptor_calle'];//1
        $Receptor_noExterior = $row['Receptor_noExterior'];//2
        $Receptor_noInterior = $row['Receptor_noInterior'];//3
        $Receptor_colonia = $row['Receptor_colonia'];//4
        $Receptor_localidad = $row['Receptor_localidad'];//5
        $Receptor_referencia = $row['Receptor_referencia'];//6
        $Receptor_municipio = $row['Receptor_municipio'];//7
        $Receptor_estado = $row['Receptor_estado'];//8
        $Receptor_pais = $row['Receptor_pais'];//9
        $Receptor_codigoPostal = $row['Receptor_codigoPostal'];//10
        $Domicilio = new Domicilio($Receptor_pais);
        $Domicilio->calle = $Receptor_calle;
        $Domicilio->noExterior = $Receptor_noExterior;
        $Domicilio->noInterior = $Receptor_noInterior;
        $Domicilio->colonia = $Receptor_colonia;
        $Domicilio->localidad = $Receptor_localidad;
        $Domicilio->referencia = $Receptor_referencia;
        $Domicilio->municipio = $Receptor_municipio;
        $Domicilio->estado = $Receptor_estado;
        $Domicilio->codigoPostal = $Receptor_codigoPostal;
        $Receptor = new Receptor($Receptor_rfc, $Domicilio);
        $Receptor->nombre = $Receptor_nombre;

        //Conceptos
        //sin redondeo a 2 decimales (4 decimales por default)
        //$sql = "SELECT stockmoves.price Concepto_valorUnitario, stockmaster.description Concepto_descripcion, -stockmoves.qty Concepto_cantidad, stockmaster.units Concepto_unidad, (-stockmoves.qty * stockmoves.price) as Concepto_importe FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //con redondeo a 2 decimales
        $sql = "SELECT cast(price as decimal(10,2)) Concepto_valorUnitario, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, stockmaster.units Concepto_unidad, cast((-stockmoves.qty * stockmoves.price) as decimal(10,2)) as Concepto_importe FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //$result = mysql_query($sql);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)<1){
            throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica', 1);
        }
        $Conceptos = new Conceptos();
        //while($row = mysql_fetch_array($result, MYSQLI_ASSOC)){
        while($row = DB_fetch_array($result)){
            $Concepto_unidad = $row['Concepto_unidad'];
            $Concepto_cantidad = $row['Concepto_cantidad'];
            $Concepto_descripcion = $row['Concepto_descripcion'];
            $Concepto_valorUnitario = $row['Concepto_valorUnitario'];
            $Concepto_importe = $row['Concepto_importe'];
            $Concepto = new Concepto($Concepto_cantidad, $Concepto_descripcion, $Concepto_valorUnitario, $Concepto_importe);
            $Concepto->unidad = $Concepto_unidad;
            $Conceptos->append($Concepto);
        }

        //Impuestos
        $Impuestos = new Impuestos();
        $Traslados = new Traslados();
        $Traslado = new Traslado('IVA', '16', $Traslado_importe);
        $Traslados->append($Traslado);
        $Impuestos->Traslados = $Traslados;

        //Comprobante
        $sql = "select extinvoice from rh_invoicesreference where intinvoice = $transno limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante para la Factura Electronica', 1);
        }
        $row = mysql_fetch_array($result);
        $Comprobante_folio = $row['extinvoice'];
        for($i = strlen($Comprobante_folio); $i < 20; $i++)
            $Comprobante_folio = '0'. $Comprobante_folio;

        $sql = "select p.terms Comprobante_condicionesDePago from paymentterms p, debtorsmaster dm, debtortrans dt where p.termsindicator = dm.paymentterms and dm.debtorno = dt.debtorno and dt.type=10 and dt.transno = $idFacturaElectronica limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se pudo obtener informacion sobre el Comprobante (condiciones de pago) para la Factura Electronica', 1);
        }
        $row = mysql_fetch_array($result);
        $Comprobante_condicionesDePago = $row['Comprobante_condicionesDePago'];

        $Comprobante_folio = $siguienteFolio;
        $Comprobante_serie = $serie;
        $Comprobante_noCertificado = $noCertificado;
        $Comprobante = new Comprobante($Comprobante_folio, $Comprobante_fecha, 'sello', $noAprobacion, $anoAprobacion, 'PAGO EN UNA SOLA EXHIBICION', $Comprobante_noCertificado, $Comprobante_subtotal, $Comprobante_total, 'traslado', $Emisor, $Receptor, $Conceptos, $Impuestos);
        $Comprobante->serie = $Comprobante_serie;
        //modificar para que obtenga el certificado en base 64 del .cer utilizado
        $Comprobante->certificado = FacturaElectronica::getCertificadoEnBase64($cerFilePath, $tmpPemFilePath);
        //$Comprobante->certificado = 'MIID9DCCAtygAwIBAgIUMDAwMDEwMDAwMDAxMDEyNzA0NzkwDQYJKoZIhvcNAQEFBQAwggE2MTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExHzAdBgkqhkiG9w0BCQEWEGFjb2RzQHNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEzARBgNVBAcMCkN1YXVodGVtb2MxMzAxBgkqhkiG9w0BCQIMJFJlc3BvbnNhYmxlOiBGZXJuYW5kbyBNYXJ0w61uZXogQ29zczAeFw0xMDAzMDUxNTI2NDRaFw0xMjAzMDQxNTI2NDRaMIGUMREwDwYDVQQDEwhSRUFMSE9TVDERMA8GA1UEKRMIUkVBTEhPU1QxETAPBgNVBAoTCFJFQUxIT1NUMSUwIwYDVQQtExxSRUEwODAxMDhGUzAgLyBMRUdSNzUxMTA5STkxMR4wHAYDVQQFExUgLyBMRUdSNzUxMTA5SE5MTFJCMDgxEjAQBgNVBAsTCU1PTlRFUlJFWTCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEA5Yuh/CQjm88uYfrMFnWHIPlwB+TlqXZvIYzgrJ/7E913jlpUgkZiN2A+dkvAUlO3vw+ZHQckUO5e3xTmibwq3RUd+nvN1e3wNULXcIK/FEWoyjW/xJTBlUtlAWdL3AGfhhoha5MxhrwxY8Llm/CMp+Jlc4ElZOpQLjCPlKMk990CAwEAAaMdMBswDAYDVR0TAQH/BAIwADALBgNVHQ8EBAMCBsAwDQYJKoZIhvcNAQEFBQADggEBACzzy9t/f5FfwPTzbJzAJFOiCEvRnys2Z56N26JMl3YCIi++S5GzLJR9YHPYYjHjNemB3357Zzq1Q896KlyEeGAEBkDYC7zRzW198AZo3J0VqXnZj/OEjQjgrF3CrUuUyYMVHpYGpRWBeVb0A028br7ZQTM+E7Fn31eBmN+QJgCL5lFDG9jR79sC+itPc1c25MwkhxvAYRne7dnlKf3NlMqQ+EeEtkikuMPGjkq4fvk+MiWIkIIFFtCns3fPn1BpZsVOgBy1bLqnICVZenoEeSJTsMK3V2eNREO3pOstQ7Cslq9/+XS3K9qJgcvnQWJBpOY9SeOBfs4IusKJ1CeMXJY=';
        $Comprobante->condicionesDePago = $Comprobante_condicionesDePago;
        $Comprobante->descuento = $Comprobante_descuento;
        $Comprobante->motivoDescuento = '';
        $Comprobante->metodoDePago = '';
        $Comprobante->saveAsXML($xmlFilePathCreate);
    }
    
    public static function createBackup($xmlFilePathCreate){
        //Uso:
        //Emisor
        $DomicilioFiscal = new DomicilioFiscal('calle', 'municipio', 'estado', 'pais', 'Stri5');
        $DomicilioFiscal->noExterior = 'noExterior';
        $DomicilioFiscal->noInterior = 'noInterior';
        $DomicilioFiscal->colonia = 'colonia';
        $DomicilioFiscal->localidad = 'localidad';
        $DomicilioFiscal->referencia = 'referencia';
        $ExpedidoEn = new ExpedidoEn('pais');
        $ExpedidoEn->calle = 'calle';
        $ExpedidoEn->noExterior = 'noExterior';
        $ExpedidoEn->noInterior = 'noInterior';
        $ExpedidoEn->colonia = 'colonia';
        $ExpedidoEn->localidad = 'localidad';
        $ExpedidoEn->referencia = 'referencia';
        $ExpedidoEn->municipio = 'municipio';
        $ExpedidoEn->estado = 'estado';
        $ExpedidoEn->codigoPostal = 'codigoPostal';
        $Emisor = new Emisor('Striiing12o13', 'nombre', $DomicilioFiscal, $ExpedidoEn);

        //Receptor
        $Domicilio = new Domicilio('pais');
        $Domicilio->calle = 'calle';
        $Domicilio->noExterior = 'noExterior';
        $Domicilio->noInterior = 'noInterior';
        $Domicilio->colonia = 'colonia';
        $Domicilio->localidad = 'localidad';
        $Domicilio->referencia = 'referencia';
        $Domicilio->municipio = 'municipio';
        $Domicilio->estado = 'estado';
        $Domicilio->codigoPostal = 'codigoPostal';
        $Receptor = new Receptor('Striiing12o13', $Domicilio);
        $Receptor->nombre = 'nombre';
        //Conceptos
        $Conceptos = new Conceptos();
        $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
        $Concepto->unidad = 'unidad';
        $Concepto->noIdentificacion = 'noIdentificacion';
        $Conceptos->append($Concepto);

        $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
        $Concepto->unidad = 'unidad';
        $Concepto->noIdentificacion = 'noIdentificacion';
        $Concepto->CuentaPredial = new CuentaPredial('numero');
        $Conceptos->append($Concepto);

        $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
        $Concepto->unidad = 'unidad';
        $Concepto->noIdentificacion = 'noIdentificacion';
        $InformacionesAduaneras = new InformacionesAduaneras();
        $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
        $InformacionesAduaneras->append($InformacionAduanera);
        $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
        $InformacionesAduaneras->append($InformacionAduanera);
        $Concepto->InformacionesAduaneras = $InformacionesAduaneras;
        $Conceptos->append($Concepto);

        $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
        $Partes = new Partes();
        $Parte = new Parte('100000.000001', 'descripcion');
        $Parte->unidad = 'unidad';
        $Parte->noIdentificacion = 'noIdentificacion';
        $Parte->valorUnitario = '200000.000002';
        $Parte->importe = '200000.000002';
        $Partes->append($Parte);
        $Parte = new Parte('100000.000001', 'descripcion');
        $Parte->unidad = 'unidad';
        $Parte->noIdentificacion = 'noIdentificacion';
        $Parte->valorUnitario = '200000.000002';
        $Parte->importe = '200000.000002';
        $Partes->append($Parte);
        $Parte = new Parte('100000.000001', 'descripcion');
        $InformacionesAduaneras = new InformacionesAduaneras();
        $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
        $InformacionesAduaneras->append($InformacionAduanera);
        $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
        $InformacionesAduaneras->append($InformacionAduanera);
        $Parte->InformacionesAduaneras = $InformacionesAduaneras;
        $Partes->append($Parte);
        $Concepto->Partes = $Partes;
        $Conceptos->append($Concepto);
        //Impuestos
        $Impuestos = new Impuestos();
        $Impuestos->totalImpuestosRetenidos = '200000.000002';
        $Impuestos->totalImpuestosTrasladados = '200000.000002';
        $Retenciones = new Retenciones();
        $Retencion = new Retencion('ISR', '100000.000001');
        $Retenciones->append($Retencion);
        $Retencion = new Retencion('IVA', '100000.000001');
        $Retenciones->append($Retencion);
        $Impuestos->Retenciones = $Retenciones;
        $Traslados = new Traslados();
        $Traslado = new Traslado('IVA', '100000.000001', '100000.000001');
        $Traslados->append($Traslado);
        $Traslado = new Traslado('IEPS', '100000.000001', '100000.000001');
        $Traslados->append($Traslado);
        $Traslado = new Traslado('IVA', '100000.000001', '100000.000001');
        $Traslados->append($Traslado);
        $Impuestos->Traslados = $Traslados;
        //Comprobante
        $Comprobante = new Comprobante('0000000000000000001', '2005-09-02T16:30:00', 'sello', '1000', '1000', 'formaDePago', '10001200000000022517', '100000.000001', '100000.000001', 'traslado', $Emisor, $Receptor, $Conceptos, $Impuestos);
        $Comprobante->serie = 'serie';
        $Comprobante->certificado = 'certificado';
        $Comprobante->condicionesDePago = 'condicionesDePago';
        $Comprobante->descuento = '200000.000002';
        $Comprobante->motivoDescuento = 'motivoDescuento';
        $Comprobante->metodoDePago = 'metodoDePago';
        $Comprobante->saveAsXML($xmlFilePathCreate);
    }
}

class Emisor{
    //atributos requeridos
    public $rfc;
    public $nombre;
    //objectos requeridos
    public $DomicilioFiscal;
    public $ExpedidoEn;
    function __construct($rfc, $nombre, DomicilioFiscal $DomicilioFiscal, ExpedidoEn $ExpedidoEn) {
        $this->rfc = $rfc;
        $this->nombre = $nombre;
        $this->DomicilioFiscal = $DomicilioFiscal;
        $this->ExpedidoEn = $ExpedidoEn;
    }
}


class DomicilioFiscal{
    public $calle;
    public $municipio;
    public $estado;
    public $pais;
    public $codigoPostal;
    //opcionales
    public $noExterior;
    public $noInterior;
    public $colonia;
    public $localidad;
    public $referencia;
    function __construct($calle, $municipio, $estado, $pais, $codigoPostal) {
        $this->calle = $calle;
        $this->municipio = $municipio;
        $this->estado = $estado;
        $this->pais = $pais;
        $this->codigoPostal = $codigoPostal;
    }
}


class ExpedidoEn{
    public $pais;
    //opcional
    public $calle;
    public $noExterior;
    public $noInterior;
    public $colonia;
    public $localidad;
    public $referencia;
    public $municipio;
    public $estado;
    public $codigoPostal;
    function __construct($pais) {
        $this->pais = $pais;
    }
}


class Receptor{
    public $rfc;
    //opcional
    public $nombre;
    //objeto requerido
    public $Domicilio;
    function __construct($rfc, Domicilio $Domicilio) {
        $this->rfc = $rfc;
        $this->Domicilio = $Domicilio;
    }
}


class Domicilio{
    public $pais;
    //opcional
    public $calle;
    public $noExterior;
    public $noInterior;
    public $colonia;
    public $localidad;
    public $referencia;
    public $municipio;
    public $estado;
    public $codigoPostal;

    function __construct($pais) {
        $this->pais = $pais;
    }
}


class Concepto{
    public $cantidad;
    public $descripcion;
    public $valorUnitario;
    public $importe;
    //objectos opcionales
    public $InformacionesAduaneras;
    public $CuentaPredial;
    public $Partes;
    //atributos opcionales
    public $unidad;
    public $noIdentificacion;
    function __construct($cantidad, $descripcion, $valorUnitario, $importe) {
        $this->cantidad = $cantidad;
        $this->descripcion = $descripcion;
        $this->valorUnitario = $valorUnitario;
        $this->importe = $importe;
    }
}

class InformacionAduanera{
    public $numero;
    public $fecha;
    public $aduana;

    function __construct($numero, $fecha, $aduana) {
        $this->numero = $numero;
        $this->fecha = $fecha;
        $this->aduana = $aduana;
    }
}

class InformacionesAduaneras extends ArrayObject{
//puede contener n objetos tipo InformacionAduanera, puede estar vacio
//    public function append(InformacionAduanera $InformacionAduanera) {
//        parent::append($InformacionAduanera);
//    }
}

class CuentaPredial{
    public $numero;
    function __construct($numero) {
        $this->numero = $numero;
    }
}

class Parte{
    public $cantidad;
    public $descripcion;
    //objeto opcional
    public $InformacionesAduaneras;
    //opcional
    public $unidad;
    public $noIdentificacion;
    public $valorUnitario;
    public $importe;

    function __construct($cantidad, $descripcion) {
        $this->cantidad = $cantidad;
        $this->descripcion = $descripcion;
    }
}

class Partes extends ArrayObject{
//puede contener n objetos tipo Parte, puede estar vacio
//    public function append(Parte $Parte) {
//        parent::append($Parte);
//    }
}

class Conceptos extends ArrayObject{
//debe tener minimo un Concepto
//    public function append(Concepto $Concepto) {
//        parent::append($Concepto);
//    }
}


class Impuestos{
    //opcionales
    public $totalImpuestosRetenidos;
    public $totalImpuestosTrasladados;
    //objectos opcionales
    public $Retenciones;
    public $Traslados;
}


class Retenciones extends ArrayObject{
//debe tener minimo una Retencion
//    public function append(Retencion $Retencion) {
//        parent::append($Retencion);
//    }
}

class Retencion{
    public $impuesto;
    public $importe;
    function __construct($impuesto, $importe) {
        $this->impuesto = $impuesto;
        $this->importe = $importe;
    }
}

class Traslados extends ArrayObject{
//debe tener minimo un Traslado
//    public function append(Traslado $Traslado) {
//        parent::append($Traslado);
//    }
}

class Traslado{
    public $impuesto;
    public $tasa;
    public $importe;
    function __construct($impuesto, $tasa, $importe) {
        $this->impuesto = $impuesto;
        $this->tasa = $tasa;
        $this->importe = $importe;
    }
}
//    //Uso:
//    //Emisor
//    $DomicilioFiscal = new DomicilioFiscal('calle', 'municipio', 'estado', 'pais', 'Stri5');
//    $DomicilioFiscal->noExterior = 'noExterior';
//    $DomicilioFiscal->noInterior = 'noInterior';
//    $DomicilioFiscal->colonia = 'colonia';
//    $DomicilioFiscal->localidad = 'localidad';
//    $DomicilioFiscal->referencia = 'referencia';
//    $ExpedidoEn = new ExpedidoEn('pais');
//    $ExpedidoEn->calle = 'calle';
//    $ExpedidoEn->noExterior = 'noExterior';
//    $ExpedidoEn->noInterior = 'noInterior';
//    $ExpedidoEn->colonia = 'colonia';
//    $ExpedidoEn->localidad = 'localidad';
//    $ExpedidoEn->referencia = 'referencia';
//    $ExpedidoEn->municipio = 'municipio';
//    $ExpedidoEn->estado = 'estado';
//    $ExpedidoEn->codigoPostal = 'codigoPostal';
//    $Emisor = new Emisor('Striiing12o13', 'nombre', $DomicilioFiscal, $ExpedidoEn);
//
//    //Receptor
//    $Domicilio = new Domicilio('pais');
//    $Domicilio->calle = 'calle';
//    $Domicilio->noExterior = 'noExterior';
//    $Domicilio->noInterior = 'noInterior';
//    $Domicilio->colonia = 'colonia';
//    $Domicilio->localidad = 'localidad';
//    $Domicilio->referencia = 'referencia';
//    $Domicilio->municipio = 'municipio';
//    $Domicilio->estado = 'estado';
//    $Domicilio->codigoPostal = 'codigoPostal';
//    $Receptor = new Receptor('Striiing12o13', $Domicilio);
//    $Receptor->nombre = 'nombre';
//    //Conceptos
//    $Conceptos = new Conceptos();
//    $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
//    $Concepto->unidad = 'unidad';
//    $Concepto->noIdentificacion = 'noIdentificacion';
//    $Conceptos->append($Concepto);
//
//    $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
//    $Concepto->unidad = 'unidad';
//    $Concepto->noIdentificacion = 'noIdentificacion';
//    $Concepto->CuentaPredial = new CuentaPredial('numero');
//    $Conceptos->append($Concepto);
//
//    $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
//    $Concepto->unidad = 'unidad';
//    $Concepto->noIdentificacion = 'noIdentificacion';
//    $InformacionesAduaneras = new InformacionesAduaneras();
//    $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
//    $InformacionesAduaneras->append($InformacionAduanera);
//    $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
//    $InformacionesAduaneras->append($InformacionAduanera);
//    $Concepto->InformacionesAduaneras = $InformacionesAduaneras;
//    $Conceptos->append($Concepto);
//
//    $Concepto = new Concepto('1.123', 'descripcion', '100000.000001', '100000.000001');
//    $Partes = new Partes();
//    $Parte = new Parte('100000.000001', 'descripcion');
//    $Parte->unidad = 'unidad';
//    $Parte->noIdentificacion = 'noIdentificacion';
//    $Parte->valorUnitario = '200000.000002';
//    $Parte->importe = '200000.000002';
//    $Partes->append($Parte);
//    $Parte = new Parte('100000.000001', 'descripcion');
//    $Parte->unidad = 'unidad';
//    $Parte->noIdentificacion = 'noIdentificacion';
//    $Parte->valorUnitario = '200000.000002';
//    $Parte->importe = '200000.000002';
//    $Partes->append($Parte);
//    $Parte = new Parte('100000.000001', 'descripcion');
//    $InformacionesAduaneras = new InformacionesAduaneras();
//    $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
//    $InformacionesAduaneras->append($InformacionAduanera);
//    $InformacionAduanera = new InformacionAduanera('numero', '2009-12-31', 'aduana');
//    $InformacionesAduaneras->append($InformacionAduanera);
//    $Parte->InformacionesAduaneras = $InformacionesAduaneras;
//    $Partes->append($Parte);
//    $Concepto->Partes = $Partes;
//    $Conceptos->append($Concepto);
//    //Impuestos
//    $Impuestos = new Impuestos();
//    $Impuestos->totalImpuestosRetenidos = '200000.000002';
//    $Impuestos->totalImpuestosTrasladados = '200000.000002';
//    $Retenciones = new Retenciones();
//    $Retencion = new Retencion('ISR', '100000.000001');
//    $Retenciones->append($Retencion);
//    $Retencion = new Retencion('IVA', '100000.000001');
//    $Retenciones->append($Retencion);
//    $Impuestos->Retenciones = $Retenciones;
//    $Traslados = new Traslados();
//    $Traslado = new Traslado('IVA', '100000.000001', '100000.000001');
//    $Traslados->append($Traslado);
//    $Traslado = new Traslado('IEPS', '100000.000001', '100000.000001');
//    $Traslados->append($Traslado);
//    $Traslado = new Traslado('IVA', '100000.000001', '100000.000001');
//    $Traslados->append($Traslado);
//    $Impuestos->Traslados = $Traslados;
//    //Comprobante
//    $Comprobante = new Comprobante('0000000000000000001', '2005-09-02T16:30:00', 'sello', '1000', '1000', 'formaDePago', '10001200000000022517', '100000.000001', '100000.000001', 'traslado', $Emisor, $Receptor, $Conceptos, $Impuestos);
//    $Comprobante->serie = 'serie';
//    $Comprobante->certificado = 'certificado';
//    $Comprobante->condicionesDePago = 'condicionesDePago';
//    $Comprobante->descuento = '200000.000002';
//    $Comprobante->motivoDescuento = 'motivoDescuento';
//    $Comprobante->metodoDePago = 'metodoDePago';
//    $Comprobante->saveAsXML('../userSatFiles/xml.xml');
?>
