<?php
/* $Revision: 40 $ */

// andres amaya - realhost - view prices, cant edit, cant insert, cant delete

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Item Prices');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['StockID'])){  //The page is called with a StockID
	$_GET['StockID'] = trim(strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(strtoupper($_GET['StockID']));
}

if (isset($_GET['NewSearch'])){
	unset($StockID);
	unset($_SESSION['SelectedStockItem']);
	unset($_POST['Select']);
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset']==0) {
		$_POST['PageOffset'] = 1;
	}
}

if( isset($_POST['StockCode']) ) {
	$_POST['StockCode'] = trim(strtoupper($_POST['StockCode']));
}

// Always show the search facilities

$SQL='SELECT categoryid,
		categorydescription
	FROM stockcategory
	ORDER BY categorydescription';

$result1 = DB_query($SQL,$db);
if (DB_num_rows($result1)==0){
	echo '<P><FONT SIZE=4 COLOR=RED>' . _('Problem Report') . ':</FONT><BR>' . _('There are no stock categories currently defined please use the link below to set them up');
	echo '<BR><A HREF="' . $rootpath . '/StockCategories.php?' . SID .'">' . _('Define Stock Categories') . '</A>';
	exit;
}


?>
<CENTER>
<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<B><?php echo $msg; ?></B>
<TABLE>
<TR>
<TD><?php echo _('In Stock Category'); ?>:
<SELECT NAME="StockCat">
<?php
if (!isset($_POST['StockCat'])){
	$_POST['StockCat']="";
}
if ($_POST['StockCat']=="All"){
	echo '<OPTION SELECTED VALUE="All">' . _('All');
} else {
	echo '<OPTION VALUE="All">' . _('All');
}
while ($myrow1 = DB_fetch_array($result1)) {
	if ($myrow1['categoryid']==$_POST['StockCat']){
		echo '<OPTION SELECTED VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
	} else {
		echo '<OPTION VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
	}
}
?>

</SELECT>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('description'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
?>
<INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
<?php
}
?>
</TD>
</TR>
<TR><TD></TD>
<TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><?php echo _('Text in the'); ?> <B><?php echo _('Stock Code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['StockCode'])) {
?>
<INPUT TYPE="Text" NAME="StockCode" value="<?php echo $_POST['StockCode']?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>"></CENTER>
<HR>


<?php

// end of showing search facilities

// query for list of record(s)

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){

	if (!isset($_POST['Go']) AND !isset($_POST['Next']) AND !isset($_POST['Previous'])){
		// if Search then set to first page
		$_POST['PageOffset'] = 1;
	}

	If ($_POST['Keywords'] AND $_POST['StockCode']) {
		$msg=_('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	If ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.mbflag
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND stockmaster.description " . LIKE . " '$SearchString'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.mbflag
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND description " .  LIKE . " '$SearchString'
				AND categoryid='" . $_POST['StockCat'] . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
	} elseif (isset($_POST['StockCode'])){
		// bowikaxu realhost august '07
		$_POST['StockCode'] = strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND (stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
						OR stockmaster.barcode " . LIKE . " '%".$_POST['StockCode'] ."%') 
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";

		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					sum(locstock.quantity) as qoh,
					stockmaster.units
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND (stockmaster.stockid " . LIKE . " '%" . $_POST['StockCode'] . "%'
						OR stockmaster.barcode ".LIKE . " '%".$_POST['StockCode'] ."%')
				AND categoryid='" . $_POST['StockCat'] . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}

	} elseif (!isset($_POST['StockCode']) AND !isset($_POST['Keywords'])) {
		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description, 
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND categoryid='" . $_POST['StockCat'] . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
	}

	$ErrMsg = _('No stock items were returned by the SQL because');
	$Dbgmsg = _('The SQL that returned an error was');
	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($result)==0){
		prnMsg(_('No stock items were returned by this search please re-enter alternative criteria to try again'),'info');
	} elseif (DB_num_rows($result)==1){ /*autoselect it to avoid user hitting another keystroke */
		$myrow = DB_fetch_row($result);
		$_POST['Select'] = $myrow[0];
	}
	unset($_POST['Search']);
}

// end query for list of records

// display list if there is more than one record

if (isset($result) AND !isset($_POST['Select'])) {

	$ListCount = DB_num_rows($result);
	if ($ListCount > 0) {
		// If the user hit the search button and there is more than one item to show

		$ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

		if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}

		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}

		if ($_POST['PageOffset']>$ListPageMax){
			$_POST['PageOffset'] = $ListPageMax;
		}
		echo '<CENTER><BR>&nbsp;&nbsp;' . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
	?>
	
	<select name="PageOffset">
	
	<?php
	$ListPage=1;
	while($ListPage<=$ListPageMax) {
		if ($ListPage==$_POST['PageOffset']) {
	?>
	
				<option value=<?php echo($ListPage); ?> selected><?php echo($ListPage); ?></option>
	<?php
		} else {
	?>
				<option value=<?php echo($ListPage); ?>><?php echo($ListPage); ?></option>
	
	<?php
		}
		$ListPage=$ListPage+1;
	}
	?>
		</select>
		<INPUT TYPE=SUBMIT NAME="Go" VALUE="<?php echo _('Go'); ?>">
		<INPUT TYPE=SUBMIT NAME="Previous" VALUE="<?php echo _('Previous'); ?>">
		<INPUT TYPE=SUBMIT NAME="Next" VALUE="<?php echo _('Next'); ?>">
	<?php

	echo '<br><br>';

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>';
	$tableheader = '<TR>
					<TD class="tableheader">' . _('Code') . '</TD>
					<TD class="tableheader">' . _('Description') . '</TD>
					<TD class="tableheader">' . _('Total Qty On Hand') . '</TD>
					<TD class="tableheader">' . _('Units') . '</TD>
				</TR>';
	echo $tableheader;

	$j = 1;

	$k = 0; //row counter to determine background colour

	$RowIndex = 0;

	if (DB_num_rows($result)<>0){
		DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
	}

	while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k++;
		}

		if ($myrow['mbflag']=='D') {
			$qoh = 'N/A';
		} else {
			$qoh = number_format($myrow["qoh"],1);
		}

		printf("<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				</tr>", 
		$myrow['stockid'],
		$myrow['description'],
		$qoh,
		$myrow['units']);

		$j++;
		If ($j == 20 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
			$j=1;
			echo $tableheader;

		}
		$RowIndex = $RowIndex + 1;
		//end of page full new headings if
	}
	//end of while loop

	echo '</TABLE>';
	}
}
// end display list if there is more than one record

// bowikaxu start
//initialise no input errors assumed initially before we test
$InputError = 0;

if (isset($_GET['Item'])){
	$Item = trim(strtoupper($_GET['Item']));
}elseif (isset($_POST['Item'])){
	$Item = trim(strtoupper($_POST['Item']));
}

If (!isset($_POST['Search']) AND (isset($_POST['Select']) OR isset($_SESSION['SelectedStockItem']))) {
	$Item = $_POST['Select'];
	unset($_POST['Select']);
	
	// bowikaxu realhost Feb 2008 - print item description and units
$sql = "SELECT stockid, description, units FROM stockmaster WHERE stockid = '".$Item."'";
$itres = DB_query($sql,$db);
$ItemDetails = DB_fetch_array($itres);
	
echo "<a href='" . $rootpath . '/SelectProduct.php?' . SID . "'>" . _('Back to Items') . '</a><BR>';

echo '<BR><FONT COLOR=BLUE SIZE=3><B>' . $Item . ' - ' . $ItemDetails['description'] . ' / ' ._('Units').' '.$ItemDetails['units'] . '</B></FONT> ';
//echo "<CENTER><TABLE>";
//echo '<FORM METHOD="post" action=' . $_SERVER['PHP_SELF'] . '?' . SID . '>';
//echo "<TR><TD>"._('Pricing for part') . ':</TD><TD><INPUT TYPE=text NAME="Item" MAXSIZE=22 VALUE="' . $Item . '" maxlength=20></TD></TR>';
//echo "<TR><TD>"._('Description').":</TD><TD><INPUT TYPE=text NAME=Desc MAXSIZE=22 VALUE='".$Desc."' maxlength=20></TD></TR>";
//echo '<TR><TD></TD><TD><INPUT TYPE=SUBMIT NAME=NewPart Value="' . _('Review Prices') . '"></TD></TR>';
//echo '</TABLE></CENTER><HR>';

	//Normal Prices
	if(strlen($Item)>0){
		$sql = "SELECT currencies.currency,
	        	salestypes.sales_type,
			prices.price,
			prices.stockid,
			prices.typeabbrev,
			prices.currabrev
		FROM prices,
			salestypes,
			currencies
		WHERE prices.currabrev=currencies.currabrev
		AND prices.typeabbrev = salestypes.typeabbrev
		AND prices.debtorno = ''
		AND prices.stockid='$Item'
		ORDER BY prices.stockid,
			prices.currabrev,
			prices.typeabbrev";

	}else if(strlen($Desc)>0){
		$sql = "SELECT currencies.currency,
	        	salestypes.sales_type,
	        	stockmaster.description,
			prices.price,
			prices.stockid,
			prices.typeabbrev,
			prices.currabrev
		FROM prices,
			salestypes,
			currencies,
			stockmaster
		WHERE prices.currabrev=currencies.currabrev
		AND prices.typeabbrev = salestypes.typeabbrev
		AND prices.debtorno = ''
		AND prices.stockid = stockmaster.stockid
		AND stockmaster.description LIKE '%".$Desc."%'
		ORDER BY prices.stockid,
			prices.currabrev,
			prices.typeabbrev";
	}
	$result = DB_query($sql,$db);

	echo "<CENTER><STRONG>"._('Normal Prices')."</STRONG><BR><BR>";
	echo '<table>';
	echo '<tr><td class="tableheader">' . _('Item Code') .
	'</td><td class="tableheader">' . _('Currency') .
	'</td><td class="tableheader">' . _('Sales Type') .
	'</td><td class="tableheader">' . _('Price') .
	'</td></tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		// realhost andres amaya - april 2007 - show 4 decimals instead of 2
		printf("<td>%s</td>
					<td>%s</td>	
			        <td>%s</td>
				<td ALIGN=RIGHT>%0.4f</td>",
		$myrow['stockid'],
		$myrow['currency'],
		$myrow['sales_type'],
		$myrow['price']
		);

	}
	//END WHILE LIST LOOP
	echo '</table></CENTER><p>';

	if (DB_num_rows($result) == 0) {
		prnMsg(_('There are no prices set up for this part'),'warn');
	}

	// SPECIAL PRICES FOR THIS CUSTOMER
	if(strlen($Item)>0){
		$sql = "SELECT currencies.currency,
	        	salestypes.sales_type,
	        	prices.debtorno,
	        	prices.branchcode,
			prices.price,
			prices.stockid,
			prices.typeabbrev,
			prices.currabrev
		FROM prices,
			salestypes,
			currencies
		WHERE prices.currabrev=currencies.currabrev
		AND prices.typeabbrev = salestypes.typeabbrev
		AND debtorno != ''
		AND prices.stockid='$Item'
		ORDER BY prices.stockid,
			prices.branchcode,
			prices.typeabbrev";
	}else if(strlen($Desc)>0) {
		$sql = "SELECT currencies.currency,
	        	salestypes.sales_type,
	        	stockmaster.description,
	        	prices.debtorno,
	        	prices.branchcode,
			prices.price,
			prices.stockid,
			prices.typeabbrev,
			prices.currabrev
		FROM prices,
			salestypes,
			currencies,
			stockmaster
		WHERE prices.currabrev=currencies.currabrev
		AND prices.typeabbrev = salestypes.typeabbrev
		AND debtorno != ''
		AND prices.stockid=stockmaster.stockid
		AND stockmaster.description LIKE '%".$Desc."%'
		ORDER BY prices.stockid,
			prices.branchcode,
			prices.typeabbrev";
	}

	$result = DB_query($sql,$db);

	echo "<CENTER><STRONG>"._('Special Prices')."</STRONG><BR><BR>";
	echo '<table>';
	echo '<tr><td class="tableheader">' . _('Item Code') .
	'</td><td class="tableheader">' . _('Currency') .
	'</td><td class="tableheader">' . _('Sales Type') .
	'</td><td class="tableheader">' . _('Customer Code') .
	'</td><td class="tableheader">' . _('Branch Code') .
	'</td><td class="tableheader">' . _('Price') .
	'</td></tr>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		// realhost andres amaya - april 2007 - show 4 decimals instead of 2
		printf("<td>%s</td>
			        <td>%s</td>
			        <td>%s</td>
			        <td>%s</td>
			        <td>%s</td>
				<td ALIGN=RIGHT>%0.4f</td>",
		$myrow['stockid'],
		$myrow['currency'],
		$myrow['sales_type'],
		$myrow['debtorno'],
		$myrow['branchcode'],
		$myrow['price']
		);

	}
	//END WHILE LIST LOOP
	echo '</table></CENTER><p>';

	if (DB_num_rows($result) == 0) {
		prnMsg(_('There are no sepcial prices set up for this part'),'warn');
	}

	echo '</FORM>';
}



include('includes/footer.inc');
?>