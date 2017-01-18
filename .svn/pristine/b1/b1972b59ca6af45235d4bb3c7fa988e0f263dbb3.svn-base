<?php
//error_reporting(0);
    class XML{
         protected $atributos;
         protected $rules;

         function __construct(){
            $this->atributos=array();
            $this->rules=array();
         }

         private function setSatFormat($value){
            $aux=strtoupper($value);
            $aux=str_replace('&','&amp;',$aux);
            $aux=str_replace('"','&quot;',$aux);
            $aux=str_replace("'",'&apos;',$aux);
            $aux=str_replace("<",'&lt;',$aux);
            $aux=str_replace(">",'&gt;',$aux);
            return $aux;
         }

         function setAtribute($attr,$value){
           if($attr!='tipoDeComprobante'){
                $this->atributos[$attr]=$this->setSatFormat($value);
           }else{
                $this->atributos[$attr]=$value;
           }
         }

         function getAtributes(){
           $conenido='';
           foreach ($this->atributos as $key=>$value){
                if(($this->rules[$key]=='R')&&(strlen($this->atributos[$key])<=0)){
                    throw new Exception('Atributo '.$key.' de '.get_class($this).' es requerido por el SAT');
                }else{
                  if((($this->rules[$key]=='O')||($this->rules[$key]=='R'))&&(strlen($this->atributos[$key])>0)){
                    $conenido.=$key.'="'.$value.'" ';
                  }else if($key=='sello' || $key=='certificado'){
                    $conenido.=$key.'="" ';
                  }
                }
           }
           return $conenido;
         }
    }

    class Comprobante extends XML{
         function __construct(){
           $this->atributos=array();
                $this->atributos['version']='2.2';
                $this->atributos['serie']='';
                $this->atributos['folio']='';
                $this->atributos['fecha']='';
                $this->atributos['sello']='';
                $this->atributos['noAprobacion']='';
                $this->atributos['anoAprobacion']='';
                $this->atributos['formaDePago']='';
                $this->atributos['noCertificado']='';
                $this->atributos['certificado']='';
                $this->atributos['condicionesDePago']='';
                $this->atributos['subTotal']='';
                $this->atributos['descuento']='';
                $this->atributos['motivoDescuento']='';
                $this->atributos['TipoCambio']='';
                $this->atributos['Moneda']='';
                $this->atributos['total']='';
                $this->atributos['metodoDePago']='';
                $this->atributos['tipoDeComprobante']='';
                $this->atributos['LugarExpedicion']='';
                $this->atributos['NumCtaPago']='';
                $this->atributos['FolioFiscalOrig']='';
                $this->atributos['SerieFolioFiscalOrig']='';
                $this->atributos['FechaFolioFiscalOrig']='';
                $this->atributos['MontoFolioFiscalOrig']='';
           $this->rules=array();
                $this->rules['version']='R';
                $this->rules['serie']='O';
                $this->rules['folio']='O';
                $this->rules['fecha']='R';
                $this->rules['sello']='O';
                $this->rules['noAprobacion']='R';
                $this->rules['anoAprobacion']='R';
                $this->rules['formaDePago']='R';
                $this->rules['noCertificado']='R';
                $this->rules['certificado']='O';
                $this->rules['condicionesDePago']='O';
                $this->rules['subTotal']='R';
                $this->rules['descuento']='O';
                $this->rules['motivoDescuento']='O';
                $this->rules['TipoCambio']='O';
                $this->rules['Moneda']='O';
                $this->rules['total']='R';
                $this->rules['metodoDePago']='R';
                $this->rules['tipoDeComprobante']='R';
                $this->rules['LugarExpedicion']='R';
                $this->rules['NumCtaPago']='O';
                $this->rules['FolioFiscalOrig']='O';
                $this->rules['SerieFolioFiscalOrig']='O';
                $this->rules['FechaFolioFiscalOrig']='O';
                $this->rules['MontoFolioFiscalOrig']='O';
         }
    }

    class RegimenFiscal extends XML{
        function __construct(){
            $this->atributos=array();
                $this->atributos['Regimen']='';

           $this->rules=array();
                $this->rules['Regimen']='R';
        }
    }

    class DomicilioFiscal extends XML{
        function __construct(){
            $this->atributos=array();
                $this->atributos['calle']='';
                $this->atributos['noExterior']='';
                $this->atributos['noInterior']='';
                $this->atributos['colonia']='';
                $this->atributos['localidad']='';
                $this->atributos['referencia']='';
                $this->atributos['municipio']='';
                $this->atributos['estado']='';
                $this->atributos['pais']='';
                $this->atributos['codigoPostal']='';
           $this->rules=array();
                $this->rules['calle']='R';
                $this->rules['noExterior']='O';
                $this->rules['noInterior']='O';
                $this->rules['colonia']='O';
                $this->rules['localidad']='O';
                $this->rules['referencia']='O';
                $this->rules['municipio']='R';
                $this->rules['estado']='R';
                $this->rules['pais']='R';
                $this->rules['codigoPostal']='R';
        }
    }

    class Domicilio extends XML{
        function __construct(){
            $this->atributos=array();
                $this->atributos['calle']='';
                $this->atributos['noExterior']='';
                $this->atributos['noInterior']='';
                $this->atributos['colonia']='';
                $this->atributos['localidad']='';
                $this->atributos['referencia']='';
                $this->atributos['municipio']='';
                $this->atributos['estado']='';
                $this->atributos['pais']='';
                $this->atributos['codigoPostal']='';
           $this->rules=array();
                $this->rules['calle']='O';
                $this->rules['noExterior']='O';
                $this->rules['noInterior']='O';
                $this->rules['colonia']='O';
                $this->rules['localidad']='O';
                $this->rules['referencia']='O';
                $this->rules['municipio']='O';
                $this->rules['estado']='O';
                $this->rules['pais']='R';
                $this->rules['codigoPostal']='O';
        }
    }

    class Emisor extends XML{
        public $domicilioFiscal;
        public $expedidoEn;
        public $regimenFiscal;
        function __construct(){
           $this->atributos=array();
                $this->atributos['rfc']='';
                $this->atributos['nombre']='';
           $this->rules=array();
                $this->rules['rfc']='R';
                $this->rules['nombre']='R';
           $this->domicilioFiscal =new DomicilioFiscal();
           $this->expedidoEn =new DomicilioFiscal();
           $this->regimenFiscal =new RegimenFiscal();
        }

        public function getNode(){
            $xml='<Emisor '.$this->getAtributes().' >
                        <DomicilioFiscal '.$this->domicilioFiscal->getAtributes().' />
                        <ExpedidoEn '.$this->expedidoEn->getAtributes().' />
                        <RegimenFiscal '.$this->regimenFiscal->getAtributes().' />
                  </Emisor>';
            return $xml;
        }
    }

    class Receptor extends XML{
        public $domicilio;
        function __construct(){
           $this->atributos=array();
                $this->atributos['rfc']='';
                $this->atributos['nombre']='';
           $this->rules=array();
                $this->rules['rfc']='R';
                $this->rules['nombre']='O';
           $this->domicilio =new Domicilio();
        }

        public function getNode(){
            $xml='<Receptor '.$this->getAtributes().' >
                        <Domicilio '.$this->domicilio->getAtributes().' />
                  </Receptor>';
            return $xml;
        }
    }

    class InformacionAduanera extends XML{
        function __construct(){
            $this->atributos=array();
                $this->atributos['numero']='';
                $this->atributos['fecha']='';
                $this->atributos['aduana']='';
           $this->rules=array();
                $this->rules['numero']='R';
                $this->rules['fecha']='R';
                $this->rules['aduana']='O';
        }

        public function getNode(){
            $xml='<InformacionAduanera '.$this->getAtributes().' />';
            return $xml;
        }
    }

    class Concepto extends XML{
        public $InformacionAduanera;
        private $idx=0;
        function __construct(){
           $this->InformacionAduanera = array();
           $this->atributos=array();
                $this->atributos['cantidad']='';
                $this->atributos['unidad']='';
                $this->atributos['noIdentificacion']='';
                $this->atributos['descripcion']='';
                $this->atributos['valorUnitario']='';
                $this->atributos['importe']='';
           $this->rules=array();
                $this->rules['cantidad']='R';
                $this->rules['unidad']='O';
                $this->rules['noIdentificacion']='O';
                $this->rules['descripcion']='R';
                $this->rules['valorUnitario']='R';
                $this->rules['importe']='R';
        }

        public function addPedimento(){
            $this->idx ++;
            $this->InformacionAduanera[$this->idx]=new InformacionAduanera();
            return $this->InformacionAduanera[$this->idx];
        }

        public function getNode(){
          	$LF = 0x0A;
	        $CR = 0x0D;
	        $nl = sprintf("%c%c",$CR,$LF);
            if($this->idx==0){
                $xml='<Concepto '.$this->getAtributes().' />';
            }else{
                $xml='<Concepto '.$this->getAtributes().' >';
                    for ($idx=1;$idx<=$this->idx;$idx++){
                         $xml.=$this->InformacionAduanera[$idx]->getNode().$nl;
                    }
                $xml.='</Concepto>';
            }
            return $xml;
        }
    }

    class Conceptos extends XML{
        public $concepto;
        private $idx=0;
        function __construct(){
           $this->concepto = array();
        }

        public function addConcepto(){
            $this->idx ++;
            $this->concepto[$this->idx]=new Concepto();
            return $this->concepto[$this->idx];
        }
        public function getNode(){
          	$LF = 0x0A;
	        $CR = 0x0D;
	        $nl = sprintf("%c%c",$CR,$LF);
            if(count($this->concepto)>0){
                $xml='<Conceptos>'.$nl;
                        for ($idx=1;$idx<=$this->idx;$idx++){
                            $xml.=$this->concepto[$idx]->getNode().$nl;
                        }
                 $xml.='</Conceptos>';
                return $xml;
            }else{
               throw new Exception('nodo concepto de conceptos es requerido por el SAT');
            }
        }
    }

    class Retencion extends XML{
        function __construct(){
           $this->atributos=array();
                $this->atributos['impuesto']='';
                $this->atributos['importe']='';
           $this->rules=array();
                $this->rules['impuesto']='R';
                $this->rules['importe']='R';
        }

        public function getNode(){
            $xml='<Retencion '.$this->getAtributes().' />';
            return $xml;
        }
    }

    class Traslado extends XML{
        function __construct(){
           $this->atributos=array();
                $this->atributos['impuesto']='';
                $this->atributos['tasa']='';
                $this->atributos['importe']='';
           $this->rules=array();
                $this->rules['impuesto']='R';
                $this->rules['tasa']='R';
                $this->rules['importe']='R';
        }

        public function getNode(){
            $xml='<Traslado '.$this->getAtributes().' />';
            return $xml;
        }
    }

    class Impuestos extends XML{
        public $Retenciones;
        public $Traslados;
        private $idxR=0;
        private $idxT=0;
        function __construct(){
           $this->atributos=array();
                $this->atributos['totalImpuestosRetenidos']='';
                $this->atributos['totalImpuestosTrasladados']='';
           $this->rules=array();
                $this->rules['totalImpuestosRetenidos']='O';
                $this->rules['totalImpuestosTrasladados']='O';
        }
        public function addRetencion(){
            $this->idxR ++;
            $this->Retenciones[$this->idxR]=new Retencion();
            return $this->Retenciones[$this->idxR];
        }

        public function addTraslado(){
            $this->idxT ++;
            $this->Traslados[$this->idxT]=new Traslado();
            return $this->Traslados[$this->idxT];
        }
        public function getNode(){
          	$LF = 0x0A;
	        $CR = 0x0D;
	        $nl = sprintf("%c%c",$CR,$LF);
            if(count($this->Retenciones)>0 || count($this->Traslados)>0){
                $xml='<Impuestos '.$this->getAtributes().'>'.$nl;
                       if(count($this->Retenciones)>0){
                            $xml.='<Retenciones>'.$nl;
                            for ($idx=1;$idx<=$this->idxR;$idx++){
                                $xml.=$this->Retenciones[$idx]->getNode().$nl;
                            }
                            $xml.='</Retenciones>'.$nl;
                        }
                       if(count($this->Traslados)>0){
                            $xml.='<Traslados>'.$nl;
                            for ($idx=1;$idx<=$this->idxT;$idx++){
                                $xml.=$this->Traslados[$idx]->getNode().$nl;
                            }
                            $xml.='</Traslados>'.$nl;
                        }
                 $xml.='</Impuestos>';
            }else{
               $xml.='<Impuestos />';
            }
            return $xml;
        }
    }

    class LocalRetencion extends XML{
        function __construct(){
           $this->atributos=array();
                $this->atributos['ImpLocRetenido']='';
                $this->atributos['TasadeRetencion']='';
                $this->atributos['Importe']='';
           $this->rules=array();
                $this->rules['ImpLocRetenido']='R';
                $this->rules['TasadeRetencion']='R';
                $this->rules['Importe']='R';
        }

        public function getNode(){
            $xml='<implocal:RetencionesLocales '.$this->getAtributes().' />';
            return $xml;
        }
    }

    class LocalTraslado extends XML{
        function __construct(){
           $this->atributos=array();
                $this->atributos['ImpLocTrasladado']='';
                $this->atributos['TasadeTraslado']='';
                $this->atributos['Importe']='';
           $this->rules=array();
                $this->rules['ImpLocTrasladado']='R';
                $this->rules['TasadeTraslado']='R';
                $this->rules['Importe']='R';
        }

        public function getNode(){
            $xml='<implocal:TrasladosLocales '.$this->getAtributes().' />';
            return $xml;
        }
    }

    class ImpuestosLocales extends XML{
        public $LocalRetenciones;
        public $LocalTraslados;
        private $idxR=0;
        private $idxT=0;
        function __construct(){
           $this->atributos=array();
                $this->atributos['version']='';
                $this->atributos['TotaldeRetenciones']='';
                $this->atributos['TotaldeTraslados']='';
           $this->rules=array();
                $this->rules['version']='R';
                $this->rules['TotaldeRetenciones']='R';
                $this->rules['TotaldeTraslados']='R';
        }
        public function addRetencion(){
            $this->idxR ++;
            $this->LocalRetenciones[$this->idxR]=new LocalRetencion();
            return $this->LocalRetenciones[$this->idxR];
        }

        public function addTraslado(){
            $this->idxT ++;
            $this->LocalTraslados[$this->idxT]=new LocalTraslado();
            return $this->LocalTraslados[$this->idxT];
        }
        public function getNode(){
          	$LF = 0x0A;
	        $CR = 0x0D;
	        $nl = sprintf("%c%c",$CR,$LF);
            if(count($this->LocalRetenciones)>0 || count($this->LocalTraslados)>0){
                $xml='<implocal:ImpuestosLocales '.$this->getAtributes().'>'.$nl;
                       if(count($this->LocalRetenciones)>0){
                            for ($idx=1;$idx<=$this->idxR;$idx++){
                                $xml.=$this->LocalRetenciones[$idx]->getNode().$nl;
                            }
                        }
                       if(count($this->LocalTraslados)>0){
                            for ($idx=1;$idx<=$this->idxT;$idx++){
                                $xml.=$this->LocalTraslados[$idx]->getNode().$nl;
                            }
                        }
                 $xml.='</implocal:ImpuestosLocales>';
            }else{
               $xml.='<implocal:ImpuestosLocales '.$this->getAtributes().' />';
            }
           return $xml;
        }
    }

    class CFD22 {
        public $comprobante;
        public $emisor;
        public $receptor;
        public $conceptos;
        public $impuestos;
        public $impuestosLocales;
        public $complementos;
        public $complemento;
        public $addenda;

        function __construct(){
           $this->comprobante= new Comprobante();
           $this->emisor= new Emisor();
           $this->receptor=new Receptor();
           $this->conceptos=new Conceptos();
           $this->impuestos=new Impuestos();
           $this->impuestosLocales=new ImpuestosLocales();
           $this->complementos=false;

           $this->impuestos_retencion=array();
           $this->impuestos_traslado=array();
           $this->addenda=array();
        }
         public function getNode(){
           //
            $xml='<?xml version="1.0" encoding="UTF-8"?>
            <Comprobante xmlns="http://www.sat.gob.mx/cfd/2" xmlns:implocal="http://www.sat.gob.mx/implocal" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/2 http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv22.xsd http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd" '.$this->comprobante->getAtributes().' >
                    '.$this->emisor->getNode().'
                    '.$this->receptor->getNode().'
                    '.$this->conceptos->getNode().'
                    '.$this->impuestos->getNode();
            if($this->complementos){
                    $xml.='<Complemento>'.$this->impuestosLocales->getNode().'</Complemento>';
            }
                  $xml.='</Comprobante>';
            return $xml;
        }

        public function getXML(){
            return base64_encode($this->getNode());
        }
}

?>