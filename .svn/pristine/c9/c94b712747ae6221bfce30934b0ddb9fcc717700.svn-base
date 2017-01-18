<?php 
// SERVIDOR PARA SINCRONIZAR PRECIOS CON OSCOMMERCE
// bowikaxu - clase que guarda la informacion del producto

Class PriceResult {
	
	function PriceResult(){
		$this->price = 0;
		$this->stockid = '';
		$this->debtorno = '';
		$this->tax_rate = 0;
		$this->discount = 0;
		$this->exists = 1;
	}
	
	var $price;
	var $exists;
	var $stockid;
	var $debtorno;
	var $tax_rate;
	var $discount;
	
}

Class CustomerAddress {
	
	function CustomerAddress(){
		$this->name = '';
		$this->dir1 = '';
		$this->dir2 = '';
		$this->dir3 = '';
		$this->dir4 = '';
		$this->dir5 = '';
		$this->dir6 = '';
	}
	
	var $name;
	var $dir1;
	var $dir2;
	var $dir3;
	var $dir4;
	var $dir5;
	var $dir6;
}

function getCustAddress($debtorno,$branch){
	$ret = new CustomerAddress;
	$link = mysql_connect('localhost', 'root', 'chilaquiles');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
if(!mysql_select_db('base_erp308',$link)){
	die('Could not select db: ' . mysql_error());

}

	$sql = "SELECT * FROM custbranch WHERE debtorno = '".$debtorno."' AND branchcode = '".$branch."'";
	
	$result = mysql_query($sql);
	$myrow = mysql_fetch_array($result);
	
	if(mysql_num_rows($result)>0){
		$ret->name = $myrow['brname'];
		$ret->dir1 = $myrow['braddress1'];
		$ret->dir2 = $myrow['braddress2'];
		$ret->dir3 = $myrow['braddress3'];
		$ret->dir4 = $myrow['braddress4'];
		$ret->dir5 = $myrow['braddress5'];
		$ret->dir6 = $myrow['braddress6'];
	}else {
		$ret->name='';
		$ret->dir1='';
		$ret->dir2='';
		$ret->dir3='';
		$ret->dir4='';
		$ret->dir5='';
		$ret->dir6='';
	}
	
	return new SoapParam($ret, 'CustomerAddress');
	
}

function Price($stockid,$debtor) {    

	$ret = new PriceResult;
	$link = mysql_connect('localhost', 'root', 'chilaquiles');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
if(!mysql_select_db('base_erp308',$link)){
	die('Could not select db: ' . mysql_error());

}
	// inicio de busqueda de precio
	
	$sql = "SELECT currcode, salestype
			FROM debtorsmaster
			WHERE debtorno = '".$debtor."'";

	$result = mysql_query($sql);
	$myrow = mysql_fetch_array($result);

		$salestype = $myrow['salestype'];
		$currcode = $myrow['currcode'];
	
	$sql = "SELECT prices.price,
				prices.debtorno,
				prices.branchcode
			FROM prices,
				debtorsmaster
			WHERE debtorsmaster.salestype=prices.typeabbrev
			AND debtorsmaster.debtorno='" . $debtor . "'
			AND prices.stockid = '" . $stockid . "'
			AND prices.currabrev = debtorsmaster.currcode
			AND prices.debtorno=debtorsmaster.debtorno
			AND prices.branchcode=''";

	$result = mysql_query($sql);
	if (mysql_num_rows($result)>0){
		/*There is a price from one of the above so return that */

		$myrow=mysql_fetch_row($result);
		$Price = $myrow[0];
		//Return $myrow[0];
	} else {
		
		//$ret->exists = 0;
		$sql="SELECT prices.price,
		prices.debtorno,
		prices.branchcode
		FROM prices
		WHERE prices.stockid = '" . $stockid . "'
		AND prices.typeabbrev ='".$salestype."'
		AND prices.debtorno = ''
		LIMIT 1";
		$result2 = mysql_query($sql);
		if(mysql_num_rows($result2)>0){
			$myrow2 = mysql_fetch_row($result2);
			$Price = $myrow2[0];
		}else {
			$sql="SELECT prices.price,
			prices.debtorno,
			prices.branchcode
			FROM prices
			WHERE prices.stockid = '" . $stockid . "'
			AND prices.debtorno = ''
			LIMIT 1";
			$result3 = mysql_query($sql);
			$myrow3 = mysql_fetch_row($result3);
			$Price = $myrow3[0];
		}
	}
	
	$sql = "SELECT stockmaster.description,
				stockmaster.longdescription,
				stockmaster.stockid,
				stockmaster.mbflag,
				stockmaster.discountcategory,
				(materialcost+labourcost+overheadcost) AS standardcost,
				stockmaster.barcode,
				stockmaster.taxcatid
				FROM stockmaster
				WHERE stockmaster.stockid = '".$stockid."'
				AND stockmaster.discontinued=0";
	$result1 = mysql_query($sql);
	$myrow1 = mysql_fetch_array($result1);
	$discount = 0;
					// bowikaxu - get the discount rate for this product
					if ( $myrow1['discountcategory'] != "" ){
						$result = mysql_query("SELECT MAX(discountrate) AS discount
								FROM discountmatrix
								WHERE salestype='" .  $salestype . "'
									AND discountcategory ='" . $myrow1['discountcategory'] . "'
									AND quantitybreak = -1");
									//AND quantitybreak = 1",$db);
						$myrow = mysql_fetch_row($result);
						if ($myrow[0] != "" && $myrow[0] > 0) {
							// bowikaxu - send the discount % not the rate
							$discount = $myrow[0] * 100;
						}
					}	
	if($Price > 0){
		$price = $Price;
		$sql = "SELECT taxgrouptaxes.calculationorder,
									taxauthorities.description,
									taxgrouptaxes.taxauthid,
									taxauthorities.taxglcode,
									taxgrouptaxes.taxontax,
									taxauthrates.taxrate
								FROM custbranch, locations, taxauthrates, taxgrouptaxes, taxauthorities
								WHERE taxgrouptaxes.taxgroupid=custbranch.taxgroupid
								AND taxauthrates.taxauthority=taxgrouptaxes.taxauthid
								AND taxauthrates.taxauthority=taxauthorities.taxid
								AND taxauthrates.dispatchtaxprovince=locations.taxprovinceid
								AND taxauthrates.taxcatid = '" . $myrow1['taxcatid'] . "'
								AND custbranch.debtorno ='".$debtor."'
								AND locations.loccode=custbranch.defaultlocation
								ORDER BY taxgrouptaxes.calculationorder
								LIMIT 1";

						$result = mysql_query($sql,$db);

						while ($myrow = mysql_fetch_array($result)){
							$taxes[$myrow['calculationorder']] = new Tax($myrow['calculationorder'],
							$myrow['taxauthid'],
							$myrow['description'],
							$myrow['taxrate'],
							$myrow['taxontax'],
							$myrow['taxglcode']);
						}

						$tax_total =0;

						foreach ($taxes AS $tax) {
							if ($tax->TaxOnTax ==1){
								$tax_total += ($tax->TaxRate * ($price + $tax_total));
							} else {
								$tax_total += ($tax->TaxRate * $price);
							}
						}

						$tax_rate = 0;
						if($price > 0) {
							$tax_rate = 100.0 * $tax_total / $price;
						}
	}
	// fin de buscar precio
	
    $retval = $Price."@".$debtor.'@'.$tax_rate.'@'.$discount;
	
	if($ret->exists){
    	$ret->price = $Price;
    	$ret->debtorno = $debtor;
    	$ret->discount = $discount;
    	$ret->tax_rate = $tax_rate;
    	$ret->stockid = $stockid;    	
    }else {
    	$ret->price = $Price;
    	$ret->debtorno = '';
    	$ret->discount = $discount;
    	$ret->tax_rate = $tax_rate;
    	$ret->stockid = '';
    }
    
    return new SoapParam($ret, 'PriceReturn');
	
}
include('includes/DefineCartClass.php');

$server = new SoapServer(null, array('uri' => 'Realhost:bowikaxu'));

$server->addFunction('Price');
$server->addFunction('getCustAddress');
$server->handle();
?> 