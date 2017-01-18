<?php

/* $Revision: 1.9 $ 

bowikaxu - realhost
Reporte del Historial de Precios

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;

include('includes/session.inc');

$title = _('Cost History');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

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

echo "<BR><CENTER><B>"._('Cost History')."</B></CENTER><BR>";

if(isset($_GET['StockID']) && $_GET['StockID']!=''){
	
	$item = $_GET['StockID'];
	
}

If (!isset($_POST['Search']) AND (isset($_POST['Select']) OR isset($_SESSION['SelectedStockItem']))) {
	
	// COMIENZA VER RESULTADOS
if(isset($_POST['VerRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' ){
//OR $_POST['FromDebtor']=='' OR $_POST['ToDebtor']==''
		echo "<CENTER><B><FONT COLOR=red>ERROR: Algunos Campos Son Invalidos</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER><B>Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</B></CENTER><BR>";
		//echo "<CENTER><B>Desde: ".$_POST['FromDebtor']." - Hasta: ".$_POST['ToDebtor']."</B></CENTER><BR>";
		echo "<CENTER><B>Articulo(s): ".$_POST['stockid']."</B></CENTER>";
		
		$SQL = "SELECT rh_costhistory.*
				FROM rh_costhistory
				WHERE 
					rh_costhistory.stockid = '".$_POST['stockid']."'
					AND rh_costhistory.trandate >= '".$_POST['FromDate']."'
					AND rh_costhistory.trandate <= '".$_POST['ToDate']."'
					ORDER BY trandate, stockid";
		
		$result = DB_query($SQL,$db,"Imposible obtener historial de precios");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Stock Code') . 
			"</TD><TD CLASS='tableheader'>" . _('Old Cost') .
			"</TD><TD CLASS='tableheader'>" . _('Cost') . 
			"</TD><TD CLASS='tableheader'>" . _('Date') .
			"</TD><TD CLASS='tableheader'>" . _('User') .
			"</TD></TR>";

		echo $TableHeader;
		
		$j = 1;
		$k = 0; //row colour counter
		while($res = DB_fetch_array($result)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['stockid'],
					number_format($res['lastcost'],2),
					number_format($res['cost'],2),
					$res['trandate'],
					$res['user_']);
			
		}
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Regresar')."'>";
		echo "</FORM>";
		
	}
	
}
	
// COMIENZA MOSTRAR EL MENU
if(!isset($_POST['VerRes']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda
$item = $_POST['Select'];

if(!isset($_POST['FromDate'])){
	$_POST['FromDate'] = date('Y-m-d');
}
if(!isset($_POST['ToDate'])){
	$_POST['ToDate'] = date('Y-m-d');
}
echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";

$sql = "SELECT stockid FROM rh_costhistory GROUP BY stockid ORDER BY stockid";
$res = DB_query($sql,$db,'Imposible determinar articulos');
echo "<TD>"._('Items').": </TD><TD>".$item."</TD></TR>";

//echo "<TR><TD>"._('Cliente').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDebtor' VALUE='1'></TD></TR>";
//echo "<TR><TD>"._('Cliente').' '._('To').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDebtor' VALUE='zzzz'></TD></TR>";

echo "<TR><TD>"._('Fecha').' '._('desde').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE='".$_POST['FromDate']."'>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE='".$_POST["ToDate"]."'>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=HIDDEN NAME='stockid' VALUE='".$_POST['Select']."'>";
echo "<INPUT TYPE=HIDDEN NAME='Select' VALUE='".$_POST['Select']."'>";
echo "<INPUT TYPE=submit NAME='VerRes' VALUE='"._('Ver Resultados')."'>";

echo "</CENTER></FORM>";
// fin meu principal busqueda
}
}

?>

<script language="JavaScript">
<!-- // create calendar object(s) just after form tag closed
				 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
				 // note: you can have as many calendar objects as you need for your application
var cal = new CalendarPopup();
document.forms[0].StockCode.select();
            document.forms[0].StockCode.focus();
				//-->
</script>

<?php

include('includes/footer.inc');

?>