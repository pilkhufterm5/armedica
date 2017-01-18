<?php 

if(!isset($_POST['stockid'])){
	
	echo "<FORM method=post value=webs_client2.php>";
	echo "StockID: <INPUT TYPE=text name='stockid'><BR>";
	echo "Codigo Cliente: <INPUT TYPE=text name='debtorno'><BR>";
	echo "<INPUT TYPE=submit value='enviar'>";
	echo "</FORM>";
	
}else{

try {    

    $client = new SoapClient(null,
    array('location' => 'http://localhost:90/BASE_ERP308/webs_server2.php',
    'uri' => 'Realhost:bowikaxu'));
    $result = $client->__soapCall('Price', array(new SoapParam($_POST['stockid'], 'stockid'), new SoapParam($_POST['debtorno'],'debtorno'))); 
    //printf("Result = %s\n", $result);
    //print_r($result);
    /*
    $res = explode('@',$result);
    echo "<BR>Price: ".$res[0];
    echo "<BR>Debtor: ".$res[1];
    echo "<BR>Tax: ".$res[2];
    echo "<BR>Discount: ".$res[3];
    */
    echo "Precio: ".$result->price."<BR>";
    echo "Disc: ".$result->discount."<BR>";
    echo "Tax: ".$result->tax_rate."<BR>";
    echo "Stockid: ".$result->stockid."<BR>";
	
	// just for testing, change debtorno value C1 to one that really exists on your webERP database
	$result = $client->__soapCall('getCustAddress', array(new SoapParam('C1', 'debtorno'), new SoapParam('C1','branch')));
	echo "Dir 1: ".$result->dir1."<BR>";
	echo "Dir 2: ".$result->dir2."<BR>";
	echo "Dir 3: ".$result->dir3."<BR>";
	echo "Dir 4: ".$result->dir4."<BR>";

} catch (Exception $e) {
    printf("Message = %s\n",$e->__toString());
}

echo "<BR><a href='webs_client2.php'>Buscar otro precio</a>";
}
?>