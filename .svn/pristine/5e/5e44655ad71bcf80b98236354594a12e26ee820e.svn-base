<?php

$link = mysql_connect('localhost', 'root', 'chilaquiles');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
if(!mysql_select_db('reynagarza_erp_001',$link)){
	die('Could not select db: ' . mysql_error());

}

$ini = ini_set("soap.wsdl_cache_enabled","0");
class QuoteService {
  private $quotes = array("ibm" => 98.42, 'hola' => 12.5);  

  function getQuote($symbol) {

	$sql = "SELECT price FROM prices WHERE stockid ='".$symbol."'";
	$res = mysql_query($sql);
	$price = mysql_fetch_array($res);
	return $price["price"];
	/*
    if (isset($this->quotes[$symbol])) {
      return $this->quotes[$symbol];
    } else {
      throw new SoapFault("Server","Unknown Symbol '$symbol'.");
    }
	*/
  }

}

$server = new SoapServer("stockquote.wsdl");
$server->setClass("QuoteService");
$server->handle();
mysql_close($link);

?>