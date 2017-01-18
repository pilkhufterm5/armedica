<?php
/**
 * 
 * REALHOST EXCHANGE PLATFORM
 * TEST EXCHANGE SERVER 
 *
 * bowikaxu
 * Nov 2007
 * @version 1.0
 * 
 */

try {
	
	ini_set("soap.wsdl_cache_enabled", "0");
	
	//"http://localhost/weberp305/rh_exchange/rh_exchange.wsdl"
	//$client = new SoapClient(null,array("location"=>"http://localhost/weberp305/rh_exchange.php","uri"=>"urn:rh_exchange"));
	//$client = new SoapClient("http://127.0.0.1/weberp305/rh_exchange/rh_exchange.wsdl");
	//$client->get_Supplier("demo");
	//var_dump($client->__getFunctions());
	//echo "<HR>";
	//var_dump($client->__getTypes()); 
	//echo "<HR>";
	
	// LOGIN
	//$a = $client->Login('jlopez','123');
	//print_r($a);
	//echo "<HR>";
	
	//$a = $client->get_Supplier("1",'jlopez','123');
	//$a = $client->__soapCall("get_Supplier", array("demo",array("user"=>'jlopez',"pass"=>'123')));
	//print_r($a);
	echo "<HR><BR><BR>";
    //$result = $client->__soapCall('get_Supplier', array(new SoapParam('345','exchange_id')));
 	//$client->username("andres");
 	
 	// ------------------------------------ CONECTAR CON EL EXHANGE MAIN WEB SERVICE 
 	
 	$Exchange = new SoapClient("http://127.0.0.1/exchange/exchangemain.wsdl");
 	//var_dump($Exchange->__getFunctions());
 	echo "<HR><BR><BR>";
 	
 	$b = $Exchange->Login('jlopez','123');
 	print_r($b);
 	echo "<HR><BR><BR>";
 	
 	$b = $Exchange->createOrder(1,2);
 	print_r($b);
 	echo "<HR><BR><BR>";
 	
 	//------------------------------------- FIN CONECTAR CON EL EXCHANGE MAIN WEB SERVICE
 	
 	
}catch(SoapFault $exception){

	printf("Message = %s\n",$exception->__toString());
	
}

?>