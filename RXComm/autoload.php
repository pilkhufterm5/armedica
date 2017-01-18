<?php
chdir(dirname(dirname(__FILE__)));
$AllowAnyone=true;
$PageSecurity = 1;

function __autoload($class_name){ 
		if(!isset($_GLOBAL["pathLib"])){
			$pathLib=listar_directorios_ruta(dirname(__FILE__).'/',6);
			$_GLOBAL["pathLib"]=$pathLib;
		}
       foreach($_GLOBAL["pathLib"] as $thisPath){
         	if(file_exists($thisPath.$class_name.'.php')){  
           		require_once $thisPath.$class_name.'.php';  
           		return;  
         	}
       }    
 }
 
 function listar_directorios_ruta($ruta,$subLevel=-1,$level=0){
   $pathLib=array();
   $auxLib=array();
   if (is_dir($ruta)) {
      if ($dh = opendir($ruta)) {
	  	 $x=$level+1;
         while (($file = readdir($dh)) !== false) {
            if (is_dir($ruta . $file) && $file!="." && $file!=".."){
			   array_push($pathLib,$ruta.$file.'/');
			   if($subLevel>-1&&$subLevel>$level){
               		$auxLib=listar_directorios_ruta($ruta . $file . "/",$subLevel,$x);
			   }elseif($subLevel==-1){
			   		$auxLib=listar_directorios_ruta($ruta . $file . "/");
			   }
			   foreach($auxLib as $value){
			   		array_push($pathLib,$value);
			   }
			   $auxLib=array();
            }
         }
      closedir($dh);
      }
   } 
	return $pathLib;
}
// include_once 'includes/session.inc';
 chdir(dirname(__FILE__));