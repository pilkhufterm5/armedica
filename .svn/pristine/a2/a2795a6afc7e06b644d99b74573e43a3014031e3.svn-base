<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('class/fpdf/fpdf.php');
include_once("class/PHPJasperXML.inc");
include_once('setting.php');


$xml =  simplexml_load_file("rh_NC.jrxml");

$transno=$_GET['transno'];
$rootpath=dirname($_SERVER['SCRIPT_FILENAME']);
$rootpath = substr($rootpath, 0, strrpos( $rootpath, "/")) . "/companies/$db";
$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->debugsql=false;
$PHPJasperXML->arrayParameter=array("transno"=>$transno, "rootpath"=>$rootpath);
$PHPJasperXML->xml_dismantle($xml);

$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
//Jaime (agregado) si es CFD guardalo en un archivo temporal para luego enviarlo por email
if (isSet($isCfd)){
    $filePath = $fileBasePath . '/' . $cfdName . '.pdf';
    $this->arrayPageSetting['name'] = $filePath;
    $PHPJasperXML->outpage("F", $filePath);
}
else
//Termina Jaime (agregado) si es CFD guardalo en un archivo temporal para luego enviarlo por email
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file


?>
