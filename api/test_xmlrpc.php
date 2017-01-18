<?php

/*
 * Recordar cambiar el database name en api_php.php
 * debe ser el nombre de la base de datos correcta de
 * donde se obtendar o insertara informacion.
 * andres amaya diaz
 */

include("../xmlrpc/lib/xmlrpc.inc");
include("../xmlrpc/lib/xmlrpcs.inc");
include("rh_api_functions.php");

if(!isset($_POST['search'])){

	echo "<FORM METHOD=POST ACTION=test_xmlrpc.php>";
	echo "Search Codigo de: ";
	echo '<input type="radio" name="searchfor" value="0" checked> Clientes<br>
		<input type="radio" name="searchfor" value="1"> Articulos<br>';
	echo "<input type=text size=20 maxlength=21 name='searchstr'><br>";
	echo "<INPUT TYPE=SUBMIT NAME='search' value='Buscar'></FORM>";
	
	exit;

}else {

	echo "Creando la clase ...<br>";
	//$myerp = new xmlrpc_API('www.softwareservicio.com','/erp_test/fortapack_erp/api/api_xml-rpc.php',80,'http11','admin','realhost');
	$myerp = new xmlrpc_API('localhost','/BASE_ERP308/api/api_xml-rpc.php',90,'http11','demo','weberp');
	
	echo "Configuracion ... <font color=blue>";
	print_r($myerp->getInfo());
	echo "</font><br>
	Busqueda: ".$_POST['searchstr']."<br>
	<a href='test_xmlrpc.php'> Otra Busqueda</a>
	<br><hr>";
	
	if($_POST['searchfor']==0) { // buscar clientes

		$clients = $myerp->api_SearchCustomers('name', $_POST['searchstr']);
		//print_r($clients);
		//echo "<br><hr><br>";
		foreach ($clients AS $code){
		
		$details = $myerp->api_GetCustomer($code);
		print_r($details);
		echo "<br><br>";
		
		}
	
	}else { // buscar en inventario
	
		$items = $myerp->api_SearchStockItems('stockid', $_POST['searchstr']);
		//print_r($items);
		//echo "<br><hr><br>";
		foreach ($items AS $code){
		
		$details = $myerp->api_GetStockItem($code);
		print_r($details);
		//echo "<br>".$code."<br>";
		echo "<br><br>";
		
		}
	
	}
	
}

?>