<?php
/**
 * Clase de control principal, aqui se crean los objetos template
 * @author LAPTOP
 * @version 1.0
 * @updated 25-Sep-2010 03:50:10 p.m.
 */
 /**
  * Servicio de Comunicacion RXCComm
  */
  
class LoadRXCCommServer{
    public $app;
	private $postArray=null;
    private $getArray=null;
    private $bodyContent;
	/**
	 * atributo de informaci�n del arreglo $_GET
	 */
	function LoadRXCCommServer(){
        $fp = fopen('php://input','r+');
        $contenido = stream_get_contents($fp);
        fclose($fp);
        //if(isset($_REQUEST['Protocolo'])){$contenido=urldecode($_REQUEST['Protocolo']);}
	file_put_contents(dirname(dirname(dirname(__FILE__)))."/tmp/Rxcom.xml",$contenido);
        $Objects = array();
        $ObjectsMethod = array();
        $xdoc = new DomDocument;
        $xdoc->LoadXML($contenido);
        $rxc = $xdoc->getElementsByTagName('rxcProtocol');
        $Action="";
        foreach($rxc as $rxc1){
            $Action= $rxc1->getAttribute('RXCAction');
            $BAction= $rxc1->getAttribute('RXCBAction');
        }

        $objects = $xdoc->getElementsByTagName('object');
        foreach($objects as $object){

            $name =  $object->getAttribute('name');
            if(class_exists($name) && $name!='XMLClass'){
                $sign =  $object->getAttribute('sign');
                $node =  base64_decode($object->nodeValue);
                if($sign===sha1($node)){
                    if(class_exists($name)){
                        $Objects[$name] = unserialize($node);
                    }
                }
            }else{
              $sign =  $object->getAttribute('sign');
              $XmlClass =  base64_decode($object->nodeValue);
              if($sign===md5($object->nodeValue)){
                    $auxDom = new DOMDocument;
                    $auxDom->loadXml($XmlClass);
                    $mainClass = $auxDom->getElementsByTagName('XMLClass');
                      foreach($mainClass as $mainClassAux){
                        $VarClass = $mainClassAux->getAttribute('classname');
                        //if(class_exists($VarClass))
                        {
                            $Objects[$VarClass] = new $VarClass();
                            $atributes = $auxDom->getElementsByTagName('atribute');
                            foreach($atributes as $atribute){
                               $AttName =  $atribute->getAttribute('name');
                               $AttVal =  $atribute->getAttribute('value');
                               $Objects[$VarClass]->$AttName = $AttVal;
                            }
                            $methods = $auxDom->getElementsByTagName('methodRun');
                            foreach($methods as $method){
                                $ObjectsMethod[$VarClass]=$method->nodeValue;
                            }
                        }
                    }
              }
            }
        }

        if(strlen($Action)>0 && $Action!='XMLClass') {
                $response='<rxcProtocol version="1.0" propietary="RSSD" type="object" RXCAction="'.$BAction.'">'."\n";
                foreach($Objects as $Object){
                    if(method_exists($Object,$Action)){
                        $Object->$Action();
                        $response.='<object name="'.get_class($Object).'" sign="'.sha1(serialize($Object)).'">'.base64_encode(serialize($Object)).'</object>'."\n";
                    }
                }
                $response.='</rxcProtocol>';
                echo $response;
        }else if(strlen($Action)>0 && $Action=='XMLClass'){
                $response='<rxcProtocol version="1.0" propietary="RSSD" type="objectXML" RXCAction="'.$BAction.'">'."\n";
                foreach($Objects as $objname=>$Object){
                    if(method_exists($Object,$ObjectsMethod[$objname])){
                        $Res=$Object->$ObjectsMethod[$objname]();
                        $response.='<object name="'.get_class($Object).'" sign="'.md5($Res).'">'.base64_encode($Res).'</object>'."\n";
                    }else{
                        $response.="<data>".$ObjectsMethod[$objname]." no existe</data>";
                    }
                }
                $response.='</rxcProtocol>';
                echo $response;
        }else{
                $response='<rxcProtocol version="1.0" propietary="RSSD" type="object" RXCAction="'.$BAction.'">'."\n";
                foreach($Objects as $Object){
                    if(method_exists($Object,$Action)){
                        $Object->$Action();
                        $response.='<object name="'.get_class($Object).'" sign="'.sha1(serialize($Object)).'">'.base64_encode(serialize($Object)).'</object>'."\n";
                    }
                }
                $response.='</rxcProtocol>';
                echo $response;
        }
	}
}
?>