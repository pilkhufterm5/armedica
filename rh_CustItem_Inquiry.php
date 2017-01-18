<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Customer Item Inquiry');
include('includes/header.inc');

if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper($_POST['CustCode']);

			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode'] . "%'";
		}
	} //one of keywords or custcode was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){
		$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'];
		unset($result);
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

} //end of if search
		}
	}
}

// TERMINA SELECCIONAR CLIENTE PARA VER REMISIONES

if(isset($_POST['Select']) || isset($_POST['ShowResults'])){

	if(isset($_POST['Select'])){
		$_SESSION['CustomerID'] = $_POST['Select'];	
	}
	
	
echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

  $sql = "SELECT name FROM debtorsmaster WHERE debtorno = '".$_SESSION['CustomerID']."'";
  $res = DB_query($sql,$db);
  $CustName = DB_fetch_array($res);
echo "<CENTER><H2>"._('Customer').": ".$_SESSION['CustomerID'].' - '.$CustName['name']."</H2><BR>";

echo '<TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";
$sql = 'SELECT typeid, typename FROM systypes WHERE typeid = 10 OR typeid=20000';
$resultTypes = DB_query($sql,$db);

//echo "<OPTION Value='All'> All";
while ($myrow=DB_fetch_array($resultTypes)){
	if (isset($_POST['TransType'])){
		if ($myrow['typeid'] == $_POST['TransType']){
		     echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		}
	} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
	}
}
echo '</SELECT></TD>';

echo "<TD>"._('Item').":<INPUT TYPE=text NAME='item'></TD>";

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('From') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('To') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '></TD>';
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults' VALUE='" . _('Show Transactions') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

}

if (isset($_POST['ShowResults']) && $_POST['TransType'] != ''){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);
   
 
   echo "<CENTER>"._('From').": ".$_POST['FromDate'].' '._('To').": ".$_POST['ToDate']."</CENTER><BR>";
   
   $sql = "SELECT 
			stockmoves.stockid,
			stockmoves.trandate,
			stockmoves.branchcode,
			stockmoves.price,
			(-1 * stockmoves.qty) AS qty,
			stockmaster.description,
			debtortrans.rh_status,
			debtortrans.transno,
			debtortrans.type,
			custbranch.brname,
			rh_locations.locationname,
            if(isnull(rh_gamma.descripcion),'Gamma sin seleccionar',rh_gamma.descripcion) as Gamma,
            if(isnull(rh_especie.descripcion),'Especie sin seleccionar',rh_especie.descripcion) as especie
		FROM 
		stockmoves, stockmaster
            left join rh_gamma_stock on stockmaster.stockid =  rh_gamma_stock.stockid
                left join rh_gamma on rh_gamma_stock.idGamma = rh_gamma.codigo
            left join rh_especie_stock on stockmaster.stockid = rh_especie_stock.stockid
                left join rh_especie on rh_especie_stock.idEspecie = rh_especie.codigo
            , custbranch, debtortrans, rh_locations
		WHERE 
		stockmaster.stockid = stockmoves.stockid
		AND debtortrans.rh_status != 'C'
		AND rh_locations.loccode = stockmoves.loccode
		AND debtortrans.transno = stockmoves.transno
		AND debtortrans.type = stockmoves.type
		AND custbranch.debtorno = '".$_SESSION['CustomerID']."'
		AND stockmoves.debtorno = custbranch.debtorno
		AND stockmoves.branchcode = custbranch.branchcode
		AND stockmoves.show_on_inv_crds = 1
		AND stockmoves.stockid LIKE '%".$_POST['item']."%'
		AND
   ";

   $sql = $sql . "stockmoves.trandate >='" . $SQL_FromDate . "' AND stockmoves.trandate <= '" . $SQL_ToDate . "'";
	if  ($_POST['TransType']!='All')  {
		$sql .= " AND stockmoves.type = " . $_POST['TransType'];
	}
	$sql .=  " AND stockmoves.debtorno = '".$_SESSION['CustomerID']."'
	 ORDER BY trandate, branchcode, stockid";

   $TransResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');

   echo '<TABLE CELLPADDING=2 BORDER=2 ALIGN=CENTER>';

   $tableheader = "<TR>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
			<TD class='tableheader'>" . _('Location') . "</TD>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Trans') . "</TD>
			<TD class='tableheader'>" . _('Item Code') . "</TD>
			<TD class='tableheader'>" . _('Item') . "</TD>
            <TD class='tableheader'>" . _('Gamma') . "</TD>
            <TD class='tableheader'>" . _('Especie') . "</TD>
			<TD class='tableheader'>" . _('Quantity') . "</TD>
			<TD class='tableheader'>" . _('Price') . "</TD>
			<TD class='tableheader'>" . _('Total') . '</TD></TR>';
	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter
	while ($myrow=DB_fetch_array($TransResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		if($myrow['type']==10){
			$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s(%s)</td>
				<td>%s</td>
				<td>%s</td>
                <td>%s</td>
                <td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td><a target='_blank' href='%s/rh_PrintCustTrans.php?%&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s' TITLE='" . _('Click to preview the invoice') . "'></a></td>";
			// bowikaxu april 2007 - get external invoice number
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie, rh_locations.locationname FROM rh_invoicesreference, rh_locations
			 WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']." AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);
			
			printf($format_base,
			$myrow['trandate'],
			$myrow['brname'],
			$ExtInvoice['locationname'],
			_('Invoice'),
			$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'],
			$myrow['transno'],
			$myrow['stockid'],
			$myrow['description'],
            $myrow['Gamma'],
            $myrow['especie'],
			$myrow['qty'],
			$myrow['price'],
			number_format($myrow['qty']*$myrow['price'],2),
			$rootpath,
			SID,
			$myrow['transno'],
			$rootpath.'/css/'.$theme.'/images/preview.gif');
			
		}else {
			
			$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td><a target='_blank' href='%s/rh_PDFRemGde.php?%s&FromTransNo=%s&InvOrCredit=Invoice'><IMG SRC='%s' TITLE='" . _('Click to preview the credit') . "'></a></td>";
			
			printf($format_base,
			$myrow['trandate'],
			$myrow['brname'],
			$myrow['locationname'],
			_('Remision'),
			$myrow['transno'],
			$myrow['stockid'],
			$myrow['description'],
			$myrow['qty'],
			$myrow['price'],
			number_format($myrow['qty']*$myrow['price'],2),
			$rootpath,
			SID,
			$myrow['transno'],
			$rootpath.'/css/'.$theme.'/images/preview.gif');
		}
		$RowCounter++;
		If ($RowCounter == 12){
			$RowCounter=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop

 echo '</TABLE>';
}

?>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo $msg; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('name'); ?></B>:</TD>
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
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text extract in the customer'); ?> <B><?php echo _('code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['CustCode'])) {
?>
<INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="CustCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Show All'); ?>">
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>


<?php

If (isset($result)) {
  $ListCount=DB_num_rows($result);
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

  echo "&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
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

  echo '<BR><BR>';

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = '<TR>
				<TD Class="tableheader">' . _('Code') . '</TD>
				<TD Class="tableheader">' . _('Customer Name') . '</TD>
				<TD Class="tableheader">' . _('Branch') . '</TD>
				<TD Class="tableheader">' . _('Contact') . '</TD>
				<TD Class="tableheader">' . _('Phone') . '</TD>
				<TD Class="tableheader">' . _('Fax') . '</TD>
			</TR>';

	echo $TableHeader;
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
			$k=1;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>",
			$myrow["debtorno"],
			$myrow["name"],
			$myrow["brname"],
			$myrow["contactname"],
			$myrow["phoneno"],
			$myrow["faxno"]);

		$j++;
		If ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
			$j=1;
			echo $TableHeader;
		}

    $RowIndex = $RowIndex + 1;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show
echo '</FORM></CENTER>';

include('includes/footer.inc');

?>

<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    //]]>
</script>