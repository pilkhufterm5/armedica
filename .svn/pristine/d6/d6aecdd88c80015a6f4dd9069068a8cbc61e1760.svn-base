<?php

/*
 * Bowikaxu Realhost - bowikaxu@gmail.com
 * webERP API Connector
 *
 * This class uses webERP API to be used as a simple class, and be able to
 * communicate with another webERP.
 * This class might work as a connector to webERP, but still needs little modifications.
 *
 * With this class you can do:
 * $fiends_erp = new xmlrpc_API('www.myfriend.com','/erp/api/api_xml-rpc.php',80,'http11','user','pass');
 * $friends_erp->api_GetCurrencyList();
 *
 */

/*
 * Class Variables
 * @ $domain - api server domain ex. www.domain.com
 * @ $domain_path - api server path to xmlrpc ex. /erp/my_erp/api/api_xml-rpc.php
 * @ $domain_port - 80 'default 80 (http) 443 FOR https
 * @ $domain_protocol - protocol 'default: http - http11 FOR HTTP1.1 http FOR HTTP 1.0 https FOR HTTPS
 * @ $username - username to be used on function calls
 * @ $password - password to be used on fucntion calls
 *
 */

Class xmlrpc_API {

// just as an example
var $domain = 'www.weberp.org';
var $domain_path = '/weberp/api/api_xml-rpc.php';
var $domain_port = 80;
var $domain_protocol = 'http11';
var $username = 'demo';
var $password = 'weberp';

function xmlrpc_API($domain, $path, $port=80, $protocol='http', $user, $pass){

// this function needs some validation and maybe username and password test
$this->domain = $domain;
$this->domain_path = $path;
$this->domain_port = $port;
$this->domain_protocol = $protocol;
$this->username = $user;
$this->password = $pass;

}

function getInfo(){

$info = array();

$info[0] = $this->domain;
$info[1] = $this->domain_path;
$info[2] = $this->domain_port;
$info[3] = $this->domain_protocol;
$info[4] = $this->username;
$info[5] = $this->password;

return $info;

}

/**
 * This function takes an associative array containing the details of a customer to
 to be inserted, where the keys of the array are the field names in the table debtorsmaster.
 * @param array $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_InsertCustomer ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_InsertCustomer');
$p1 =& php_xmlrpc_encode($p1);
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function takes an associative array containing the details of a customer to
 to be updated, where the keys of the array are the field names in the table debtorsmaster.
 * @param array $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_ModifyCustomer ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_ModifyCustomer');
$p1 =& php_xmlrpc_encode($p1);
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the customer
 whose account number is passed to it.
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetCustomer ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetCustomer');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing the account numbers of those customers
 that meet the criteria given. Any field in debtorsmaster can be search on.
 * @param string $p1
 * @param string $p2
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_SearchCustomers ($p1, $p2, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_SearchCustomers');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($p2, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p3);
$p4 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p4);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing a list of all currencies setup on webERP
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetCurrencyList ($debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetCurrencyList');
$p1 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p2);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the currency
 sent as a parameter
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetCurrencyDetails ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetCurrencyDetails');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing a list of all sales types setup on webERP
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetSalesTypeList ($debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetSalesTypeList');
$p1 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p2);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the sales type
 sent as a parameter
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetSalesTypeDetails ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetSalesTypeDetails');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing a list of all hold reason codes setup on webERP
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetHoldReasonList ($debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetHoldReasonList');
$p1 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p2);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the hold reason
 sent as a parameter
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetHoldReasonDetails ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetHoldReasonDetails');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing a list of all payment terms setup on webERP
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetPaymentTermsList ($debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetPaymentTermsList');
$p1 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p2);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the payment terms
 sent as a parameter
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetPaymentTermsDetails ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetPaymentTermsDetails');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function takes an associative array containing the details of a stock item to
 to be inserted, where the keys of the array are the field names in the table stockmaster.
 * @param array $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_InsertStockItem ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_InsertStockItem');
$p1 =& php_xmlrpc_encode($p1);
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function takes an associative array containing the details of a stock item to
 to be updated, where the keys of the array are the field names in the table stockmaster.
 * @param array $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_ModifyStockItem ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_ModifyStockItem');
$p1 =& php_xmlrpc_encode($p1);
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an associative array containing the details of the item
 whose stockid is passed to it.
 * @param string $p1
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetStockItem ($p1, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetStockItem');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p3);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns an array containing the account numbers of those items
 that meet the criteria given. Any field in stockmaster can be search on.
 * @param string $p1
 * @param string $p2
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_SearchStockItems ($p1, $p2, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_SearchStockItems');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($p2, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p3);
$p4 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p4);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}

/**
 * This function returns the quantity of stock on hand a the location given
 * @param string $p1
 * @param string $p2
 * @param int $debug when 1 (or 2) will enable debugging of the underlying xmlrpc call (defaults to 0)
 * @return array (or an xmlrpcresp obj instance if call fails)
 */
function api_GetStockBalance ($p1, $p2, $debug=0) {
$client =& new xmlrpc_client($this->domain_path, $this->domain, $this->domain_port);
$client->return_type = 'xmlrpcvals';
$client->setDebug($debug);
$msg =& new xmlrpcmsg('weberp.xmlrpc_GetStockBalance');
$p1 =& new xmlrpcval($p1, 'string');
$msg->addparam($p1);
$p2 =& new xmlrpcval($p2, 'string');
$msg->addparam($p2);
$p3 =& new xmlrpcval($this->username, 'string');
$msg->addparam($p3);
$p4 =& new xmlrpcval($this->password, 'string');
$msg->addparam($p4);
$res =& $client->send($msg, 0, $this->domain_protocol);
if ($res->faultcode()) return $res; else return php_xmlrpc_decode($res->value());
}
}

?>