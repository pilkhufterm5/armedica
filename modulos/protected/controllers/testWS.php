<?php
class CActiveRecord {
}
class FB{
	static function INFO(){}
}
include "../models/ISSTELEONWS.php";
$Total=0;

$_Fecha=
$_FechaIni = '2015-09-20';
$id_Agrupador=2919;
$i=0;
//while(date('Ymd')>date('Ymd',strtotime($_Fecha)))
{

$_Fecha=date('Y-m-d',strtotime('+1 Days',strtotime($_Fecha)));
echo $_Fecha.'|';
$consumirs = new ISSTELEONWS("http://ar.isssteleon.gob.mx/recetas/Medix.asmx?WSDL", array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => false
        ));
$AlmacenIsssteleonWS = "046";
$params = array(
            'fecha' => $_Fecha,
            'almacen' => $AlmacenIsssteleonWS,
            'reporte' => '1',
	    'movimientoid'=>1
        );


$stotal=0;

$ObtenerSalidasDiariasArray = $consumirs->consumir('ObtenSalidasDiarias', $params);
var_dump($ObtenerSalidasDiariasArray);exit;
IF (is_array($ObtenerSalidasDiariasArray) && isset($ObtenerSalidasDiariasArray['NewDataSet'])&&is_array($ObtenerSalidasDiariasArray['NewDataSet'])) {
var_dump($ObtenerSalidasDiariasArray['NewDataSet']);
foreach($ObtenerSalidasDiariasArray['NewDataSet'] as $fila)
foreach($fila as $fil)
{
	if($fil['RecetaId']=='2653068'){
		var_dump($fil);
	}
	if(false&&$fil['MedicamentoId']==$id_Agrupador){
		$stotal+=$fil["UnidadesSurtidas"];
		$Total+=$fil["UnidadesSurtidas"];
	}
}
} 
echo  $stotal.'|';

echo "\r\n";

}
echo $Total;
