<?php

/**** Variables para la coneccion a la base de datos ****/
global $AlmacenesHabilitados;
$PageSecurity = 1;

$AllowAnyone = true;

$DatabaseName='sme_erp_001';
$AlmacenesHabilitados=array('030','031');
$Local=realpath((dirname(realpath(__FILE__))));
//$PathPrefix=$Local;

chdir('../');
include(dirname(__FILE__).'/../includes/session.inc');
addToIncludePath(dirname(__FILE__));
/**** Variables para el manejo del servicio SOAP ****/

require_once ( 'class.phpwsdl.php' );
require_once ( 'ModeloProductoBase.php' );


$soap=PhpWsdl::CreateInstance(
        null,   // Set this to your namespace or let PhpWsdl find one
        null,   // Set this to your SOAP endpoint or let PhpWsdl determine it
        null,   // Set this to a writeable folder to enable caching
        array ( // Set this to the filename or an array of filenames of your 
            'class.DisponibilidadAlmacenSoap',
            'class.ModeloProductoBase',
            // 'class.DisponibilidadProducto',
            // 'class.SalidaProducto',
            // 'class.TransferenciaProducto'
            ),   
                        // webservice handler class(es) (be sure to add the file that 
                        // contains the handler class as first class definition at 
                        // first)
        null,   // Set this to the webservice handler class name or let 
                        // PhpWsdl determine it
        null,   // If you want to define some methods from code, give an array 
                        // of PhpWsdlMethod here
        null,   // If you want to define some types from code, give an array of 
                        // PhpWsdlComplex here
        false,  // Set this to TRUE to output WSDL on request and exit after 
                        // WSDL has been sent
        false   // Set this to TRUE to run the SOAP server and exit
);

$wsdl=$soap->CreateWsdl();

$soap->RunServer();

/**
 * Servicio SOAP para consulta de ISSTE
 */
class DisponibilidadAlmacenSoap
{
    /**
     * La URI del archivo WSDL
     *
     * @var string
     */
    public static $_WsdlUri='localhost/armedica/armedica/PHPWsdl/DisponibilidadAlmacenSoap.php?WSDL';
    
    /**
     * El objecto cliente de Soap
     *
     * @var object
     */
    public static $_Server=null;
    
    /**
     * Ultima actualizacion forzada de la base de datos
     * 
     * @var date
     */
     public static $last_update;

    /**
     * Send a SOAP request to the server
     *
     * @param string $method The method name
     * @param array $param The parameters
     * @return mixed The server response
     */
    public static function _Call($method,$param){
        if(is_null(self::$_Server))
            self::$_Server=new SoapClient(self::$_WsdlUri);
        return self::$_Server->__soapCall($method,$param);
    }
    
    /**
     * Comprueba la ultima fecha en que se realizo la ultima actualizacion 
     * de la base de datos para evitar llamadas.
     * 
     * @ignore
     */
    private function is_data_updated($loccode)
    {
         global $db;
         
         $sql = "call update_alllocstock('".DB_escape_string($loccode)."');";
         
         if ($result = DB_query($sql,$db,'','',false,false))
            return true;
        else
            return false;
     }
    
    /**
     * Responde con un archivo valido de SOAP con todas las disponibilidades 
     * de un almacen
     * 
     * @param string $loccode Codigo del almacen a consultar
     * @return DisponibilidadProducto[] Disponibilidades
     */
    public function DisponibilidadAlmacen($loccode)
    {
    	global $AlmacenesHabilitados;
        if(!is_int($loccode))
            return;
            
        global $db;
        if(!in_array($loccode,$AlmacenesHabilitados)) return array('AlmacenesPermitidos'=>$AlmacenesHabilitados);
        $disponibilidadProductoArray = array();
        
        $sql = "SELECT locstock.stockid, stockmaster.description, stockmaster.barcode, locations.loccode, locations.locationname, locstock.quantity
        FROM sme_erp_001.locations
        JOIN locstock ON locations.loccode=locstock.loccode
        JOIN stockmaster ON stockmaster.stockid=locstock.stockid
        WHERE locations.loccode=".$loccode.";";
        
        $result = DB_query($sql,$db,'','',false,false);
        
        while ($parentGroupRow = DB_fetch_assoc($result))
        {
            $disponibilidadProducto = new DisponibilidadProducto();
            foreach($parentGroupRow as $llave=>$valor)
             	$disponibilidadProducto->$llave=$valor;
            
            $disponibilidadProductoArray[] = $disponibilidadProducto;
        };
        
        return $disponibilidadProductoArray;
    }
    
    /**
     * Responde con un archivo valido de SOAP con todas las salidas 
     * de producto de un almacen.
     * 
     * @param string $loccode Codigo del almacen a consultar
     * @param string $date Fecha desde la cual traer la consulta, puede ser null
     *                      y tomara como referencia desde el dia de hoy.
     * @return SalidaProducto[] Salidas
     */
    public function SalidasAlmacen($loccode,$date)
    {
         global $db,$AlmacenesHabilitados;
         
        $salidasProductoArray = array();
        if(!is_int($loccode))
            return;
        
        if(!in_array($loccode,$AlmacenesHabilitados)) return array('AlmacenesPermitidos'=>$AlmacenesHabilitados);
        
         if(!$date || $date == '')
         {
             $date = date('Y-m-d');
         }
         
         $sql = "SELECT stockmoves.stockid, stockmaster.description, stockmaster.barcode, locations.loccode, locations.locationname, stockmoves.qty, stockmoves.stkmoveno 
                 FROM stockmoves 
                 JOIN stockmaster ON stockmaster.stockid=stockmoves.stockid
                 JOIN locations ON locations.loccode=stockmoves.loccode
                 WHERE stockmoves.qty < 0 AND stockmoves.loccode = '".DB_escape_String($loccode)."' AND date(stockmoves.trandate) >= date('".DB_escape_String($date)."');";
                 
         $result = DB_query($sql,$db,'','',false,false);
         
         while ($parentGroupRow = DB_fetch_assoc($result))
         {
             $salidaProducto = new SalidaProducto();
             foreach($parentGroupRow as $llave=>$valor)
             	$salidaProducto->$llave=$valor;
             
             $salidasProductoArray[] = $salidaProducto;
         };
         
         return $salidasProductoArray;
         
     }
     
    /**
      * Devuelve una lista de los productos que se transfirieron entre almacenes
      * Dependiendo de la peticion del reporte sera la informacion devuelta.
      * 
      * Si el reporte es igual a 1 entonces se entregaran las entradas
      * Si el reporte es igual a 3 entonces se entregaran las salidas
      * 
      * @param integer $report Numero del reporte a consultar
      * @param string $loccode Codigo del almacen a consultar
      * @param string $date Fecha desde la cual traer la consulta
      * 
      * @return TransferenciaProducto[] Lista de movimientos de productos
      */
    public function TransferenciasAlmacen($report,$loccode,$date)
    {
    		
          global $db,$AlmacenesHabilitados;
          if(!in_array($loccode,$AlmacenesHabilitados)) return array('AlmacenesPermitidos'=>$AlmacenesHabilitados);
          
          $transferenciasProductoArray = array();
          if(!$date || $date == '')
          {
              $date = date('Y-m-d');
          }
          
          $sql = "SELECT stockmoves.stockid, stockmoves.type, stockmaster.description, stockmaster.barcode, 
                    systypes.typename, stockmoves.loccode, locations.locationname, 
                    stockmoves.qty, stockmoves.trandate, stockmoves.stkmoveno
                  FROM stockmoves
                  JOIN systypes ON systypes.typeid=stockmoves.type
                  JOIN locations ON locations.loccode=stockmoves.loccode
                  JOIN stockmaster ON stockmaster.stockid=stockmoves.stockid
                  WHERE stockmoves.loccode = '".DB_escape_String($loccode)."' 
                  AND date(stockmoves.trandate) >= date('".DB_escape_String($date)."') ";
                  
          // Dependiendo del reporte pedido agregar las condicionales addicionales al sql
          switch($report)
          {
              case 1: 
                  $sql .= "AND stockmoves.type = 18 AND stockmoves.qty > -1;";
                  break;
              default:
              case 2: 
                  //$sql .= "AND stockmoves.type = 16 AND stockmoves.qty < 0;";
                  break;
              case 3: 
                  $sql .= "AND stockmoves.type = 16 AND stockmoves.qty < 0;";
                  break;
          }
          
         $result = DB_query($sql,$db,'','',false,false);
         
         while ($parentGroupRow = DB_fetch_assoc($result))
         {
             $transferenciaProducto = new DisponibilidadProducto();
             foreach($parentGroupRow as $llave=>$valor)
             	$transferenciaProducto->$llave=$valor;
             
             $transferenciasProductoArray[] = $transferenciaProducto;
         };
         
         return $transferenciasProductoArray;
          
      }
      
    /**
     * Devuelve una lista de todos los articulos dentro de la tabla de etiquetas.
     * 
     * @return Etiquetas[] Lista de etiquetas en el sistema
     */
    public function ConsultaEtiquetas()
    {
        global $db;
        
        $etiquetasArray = array();
        
        $sql = "SELECT * FROM rh_etiquetas";
                
        $result = DB_query($sql,$db,'','',false,false);
        
        while ($parentGroupRow = DB_fetch_assoc($result))
        {
            $etiqueta = new Etiquetas();
            foreach($parentGroupRow as $llave=>$valor)
             	$etiqueta->$llave=$valor;
            
            $etiquetasArray[] = $etiqueta;
        }
        
        return $etiquetasArray;
    }
      
}
