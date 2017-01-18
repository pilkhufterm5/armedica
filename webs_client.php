<?php

if(!isset($_POST['stockid'])){

	echo "<FORM method='post' value='webs_client.php'>";
	echo "<INPUT TYPE='text' NAME='stockid'>";
	echo "<INPUT TYPE='submit' value='enviar'>";
	echo "</FORM>";

}else {
  $ini = ini_set("soap.wsdl_cache_enabled","0");
  $client = new SoapClient("stockquote.wsdl");
  try {
    echo "<pre>\n";
	$stock = $_POST['stockid'];
//    print($client->getQuote($stock));
    echo "\n";
    print($client->getQuote('AEA03'));
    //echo "\n";
    //print($client->getQuote("microsoft"));  
    echo "\n</pre>\n";
  } catch (SoapFault $exception) {
    echo $exception;      
  }
}
?>