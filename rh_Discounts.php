<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Discount Matrix Maintenance');
include('includes/header.inc');

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	
	if (!is_numeric($_POST['DiscountRate'])){
		prnMsg( _('The discount rate must be entered as a positive number'),'warn');
		$InputError =1;
	}
	if ($_POST['DiscountRate']<=0 OR $_POST['DiscountRate']>=100){
		prnMsg( _('The discount rate applicable for this record is either less than 0% or greater than 100%') . '. ' . _('Numbers between 1 and 69 are expected'),'warn');
		$InputError =1;
	}
	list($yy,$mm,$dd)=explode("-",$_POST['FromDate']);
	if (!is_numeric($yy) | !is_numeric($mm) | !is_numeric($dd))
	{
		prnMsg( _('The From Date is invalid'),'warn');
		$InputError =1;
	}
	list($yy,$mm,$dd)=explode("-",$_POST['ToDate']);
	if (!is_numeric($yy) | !is_numeric($mm) | !is_numeric($dd))
	{
		prnMsg( _('The To Date is invalid'),'warn');
		$InputError =1;
	}

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if ($InputError !=1) {

		$sql = "INSERT INTO discountmatrix (salestype, 
							discountcategory, 
							quantitybreak, 
							discountrate) 
					VALUES('" . $_POST['SalesType'] . "', 
						'" . $_POST['DiscountCategory'] . "', 
						" . $_POST['QuantityBreak'] . ", 
						" . ($_POST['DiscountRate']/100) . ')';

		$result = DB_query($sql,$db);
		
		// bowikaxu - now add the discount code to the selected items
		if($_POST['ItmCode']==''){
			// apply to all the selected category
			prnMsg( _('Descuento aplicado a la categoria de articulos'),'warn');
			$sqldsc = "INSERT INTO rh_discounts (discountid, itmcategoryid, fromdate, todate) VALUES (
			'".$_POST['DiscountCategory']."',
			'".$_POST['Category']."',
			'".$_POST['FromDate']."',
			'".$_POST['ToDate']."')";
		}else {
			// apply only to the selected items
			prnMsg( _('Descuento aplicado al codigo de articulos'),'warn');
			$sqldsc = "INSERT INTO rh_discounts (discountid, itemlike, fromdate, todate) VALUES (
			'".$_POST['DiscountCategory']."',
			'".$_POST['ItmCode']."',
			'".$_POST['FromDate']."',
			'".$_POST['ToDate']."')";
		}
		
		DB_query($sqldsc,$db);
		// bowikaxu - debe mandar llamar al script que verifica la tabla rh_discounts y actualiza los productos con sus debidos descuentos
		// este script sera usado en un cron.
		$today = strtotime(Date('Y-m-d')); 
		$from_date = strtotime($_POST['FromDate']);
		$to_date = strtotime($_POST['ToDate']);
		
		if(($from_date<=$today && $to_date>=$today)){
			// la fecha si empieza o termina hoy, hacer el insert
		
			if($_POST['ItmCode']==''){
				// apply to the category
				$sqlIns = "UPDATE stockmaster SET discountcategory = '".$_POST['DiscountCategory']."' 
							WHERE categoryid = '".$_POST['Category']."'";
			
			}else {
				// apply to the item like
				$sqlIns = "UPDATE stockmaster SET discountcategory = '".$_POST['DiscountCategory']."' 
							WHERE stockid LIKE '".$_POST['ItmCode']."%'";
			
			}
			DB_query($sqlIns,$db);
			prnMsg( _('A partir de este momento el descuento sera tomado en cuenta'),'success');
		}
		// fin de script para actualizar los descuentos
		
		prnMsg( _('The discount matrix record has been added'),'success');
		
		unset($_POST['DiscountCategory']);
		unset($_POST['SalesType']);
		unset($_POST['QuantityBreak']);
		unset($_POST['DiscountRate']);
	}
} elseif ($_GET['Delete']=='yes') {
/*the link to delete a selected record was clicked instead of the submit button */

	$sql2 = "DELETE FROM rh_discounts WHERE discountid = '".$_GET['DiscountCategory']."'";
	$result = DB_query($sql2,$db);

	$sql="DELETE FROM discountmatrix
		WHERE discountcategory='" .$_GET['DiscountCategory'] . "'
		AND salestype='" . $_GET['SalesType'] . "'
		AND quantitybreak=" . $_GET['QuantityBreak'];

	$result = DB_query($sql,$db);
	
	$sql = "SELECT stockid FROM stockmaster WHERE discountcategory = '".$_GET['DiscountCategory']."'";
	$res = DB_query($sql,$db);
	
	while ($itms = DB_fetch_array($res)){
		$sql = "UPDATE stockmaster SET discountcategory = '' WHERE stockid ='".$itms['stockid']."'";
		$result = DB_query($sql,$db);
	}
	prnMsg( _('The discount matrix record has been deleted'),'success');
}

$sql = 'SELECT sales_type,
		salestype,
		discountcategory,
		quantitybreak,
		rh_discounts.fromdate,
		rh_discounts.todate,
		rh_discounts.todate,
		rh_discounts.itmcategoryid,
		rh_discounts.itemlike,
		discountrate
	FROM discountmatrix INNER JOIN salestypes
		ON discountmatrix.salestype=salestypes.typeabbrev
		INNER JOIN rh_discounts
		ON discountmatrix.discountcategory=rh_discounts.discountid
	ORDER BY salestype,
		discountcategory,
		quantitybreak';

$result = DB_query($sql,$db);

echo '<CENTER><table>';
echo "<tr><td class='tableheader'>" . _('Sales Type') . "</td>
	<td class='tableheader'>" . _('Discount Category') . "</td>
	<td class='tableheader'>" . _('Item') . "(s)</td>
	<td class='tableheader'>" . _('Category') . "</td>
	<td class='tableheader'>" . _('From Date') . "</td>
	<td class='tableheader'>" . _('To Date') . "</td>
	<td class='tableheader'>" . _('Discount Rate') . ' %' . "</td></TR>";

$k=0; //row colour counter

while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}
	$DeleteURL = $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=yes&SalesType=' . $myrow['salestype'] . '&DiscountCategory=' . $myrow['discountcategory'] . '&QuantityBreak=' . $myrow['quantitybreak'];

	printf("<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href='%s'>" . _('Delete') . '</td>
		</tr>',
		$myrow['sales_type'],
		$myrow['discountcategory'],
		$myrow['itemlike'],
		$myrow['itmcategoryid'],
		$myrow['fromdate'],
		$myrow['todate'],
		number_format($myrow['discountrate']*100,2) ,
		$DeleteURL);

}

echo '</TABLE><HR>';

if(!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date('Y-m-d');
}

if(!isset($_POST['ToDate'])){
	$_POST['ToDate']=Date('Y-m-d');
}

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';


echo '<TABLE>';

$sql = 'SELECT typeabbrev,
		sales_type
	FROM salestypes';

$result = DB_query($sql, $db);

echo '<TR><TD>' . _('Customer Price List') . ' (' . _('Sales Type') . '):</TD><TD>';

echo "<SELECT NAME='SalesType'>";

while ($myrow = DB_fetch_array($result)){
	if ($myrow['typeabbrev']==$_POST['SalesType']){
		echo "<OPTION SELECTED VALUE='" . $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
	} else {
		echo "<OPTION VALUE='" . $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
	}
}

echo '</SELECT></TD></TR>';

// bowikaxu - get the categories
$sql = "SELECT categoryid, categorydescription FROM stockcategory";
$res = DB_query($sql,$db);
echo '<TR><TD>' . _('Category') . ':</TD><TD>';

echo "<SELECT NAME='Category'>";

while ($myrow = DB_fetch_array($res)){
	if ($myrow['categoryid']==$_POST['Category']){
		echo "<OPTION SELECTED VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categorydescription'];
	} else {
		echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categorydescription'];
	}
}

echo '</SELECT></TD></TR>';
// codigo articulo
echo '<TR><TD>' . _('Item Code') . ':</TD><TD>';

echo "<INPUT TYPE='Text' NAME='ItmCode' MAXLENGTH=19 SIZE=10 VALUE='" . $_POST['ItmCode'] . "'></TD></TR>";
// fecha inicio
echo '<TR><TD>' . _('From').' '._('Date') . ':</TD><TD>';

echo "<INPUT TYPE='Text' NAME='FromDate' MAXLENGTH=19 SIZE=10 VALUE='" . $_POST['FromDate'] . "'></TD></TR>";
// fecha termino
echo '<TR><TD>' . _('To').' '._('Date') . ':</TD><TD>';

echo "<INPUT TYPE='Text' NAME='ToDate' MAXLENGTH=19 SIZE=10 VALUE='" . $_POST['ToDate'] . "'></TD></TR>";
// codigo de descuento
echo '<TR><TD>' . _('Discount Category Code') . ':</TD><TD>';

echo "<INPUT TYPE='Text' NAME='DiscountCategory' MAXLENGTH=2 SIZE=2 VALUE='" . $_POST['DiscCat'] . "'></TD></TR>";

echo "<input type='Hidden' name='QuantityBreak' SIZE=10 MAXLENGTH=10 VALUE='-1'></TD></TR>";

echo '<TR><TD>' . _('Discount Rate') . " (%):</TD><TD><input type='Text' name='DiscountRate' SIZE=4 MAXLENGTH=4></TD></TR>";
echo '</TABLE>';

echo "<CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER>";

echo '</FORM>';

include('includes/footer.inc');
?>
