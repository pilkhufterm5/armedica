<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
// andres amaya diaz

$PageSecurity = 2;

include('includes/session.inc');

$title = _('Cliente Remisiones');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

// bowikaxu - se escogio un clinete y sucursal
if(isset($_POST['SelectNEW']) && $_POST['SelectNEW']!=''){
	
		$branch = substr($_POST['SelectNEW'],strpos($_POST['SelectNEW'],' - ')+3);
		$cust = substr($_POST['SelectNEW'],0,strpos($_POST['SelectNEW'],' - '));
		$_SESSION['NewCustRem'] = $cust;
		$_SESSION['NewBranchRem'] = $branch;
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . "/rh_ViewRemisiones.php?" . SID . "'>";
		//echo "1: ".$cust."<br>2: ".$branch."<br>";
}

// bowikaxu - search the new customer
if(isset($_POST['Search2']) && $_POST['Search2']!=''){
	$_POST['PageOffset2'] = 1;
	If ($_POST['Keywords2'] AND $_POST['CustCode2']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords2'] = strtoupper($_POST['Keywords2']);
	}
	If ($_POST['Keywords2']=="" AND $_POST['CustCode2']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.branchcode,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords2'])>0) {

			$_POST['Keywords2'] = strtoupper($_POST['Keywords2']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords2'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords2']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords2'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords2']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords2'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.branchcode,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

		} elseif (strlen($_POST['CustCode2'])>0){

			$_POST['CustCode2'] = strtoupper($_POST['CustCode2']);

			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.branchcode,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode2'] . "%'";
		}
	} //one of keywords or custcode was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$resultnew = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($resultnew)==1){
		$myrow=DB_fetch_array($resultnew);
		
		$_SESSION['NewCustRem'] = $myrow['debtorno'];
		$_SESSION['NewBranchRem'] = $myrow['branchcode'];
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . "/rh_ViewRemisiones.php?" . SID . "'>";
		
		unset($resultnew);
	} elseif (DB_num_rows($resultnew)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}$msg="";
if (!isset($_SESSION['NewCust'])){ //initialise if not already done
	$_SESSION['NewCust']="";
}

if (!isset($_POST['PageOffset2'])) {
  $_POST['PageOffset2'] = 1;
} else {
  if ($_POST['PageOffset2']==0) {
    $_POST['PageOffset2'] = 1;
  }
	
}

if (isset($_POST['Search2']) OR isset($_POST['Go2']) OR isset($_POST['Next2']) OR isset($_POST['Previous2'])){
	if (isset($_POST['Search2'])){
		$_POST['PageOffset2'] = 1;
	}
	If ($_POST['Keywords2'] AND $_POST['CustCode2']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords2'] = strtoupper($_POST['Keywords2']);
	}
	If ($_POST['Keywords2']=="" AND $_POST['CustCode2']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.branchcode,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords2'])>0) {

			$_POST['Keywords2'] = strtoupper($_POST['Keywords2']);

			//insert wildcard characters in spaces

			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords2'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords2']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords2'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords2']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords2'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.branchcode,
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
// bowikaxu - end searching the new customer

?>
<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo "<H2><B>NUEVO CLIENTE</B></H2>"; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('name'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
?>
<INPUT TYPE="Text" NAME="Keywords2" value="<?php echo $_POST['Keywords2']?>" SIZE=20 MAXLENGTH=25>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="Keywords2" SIZE=20 MAXLENGTH=25>
<?php
}
?>
</TD>
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text extract in the customer'); ?> <B><?php echo _('code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['CustCode2'])) {
?>
<INPUT TYPE="Text" NAME="CustCode2" value="<?php echo $_POST['CustCode2'] ?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="CustCode2" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search2" VALUE="<?php echo _('Show All'); ?>">
<INPUT TYPE=SUBMIT NAME="Search2" VALUE="<?php echo _('Search Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>
<?php
If (isset($resultnew)) {
  $ListCount=DB_num_rows($resultnew);
  $ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

  if (isset($_POST['Next'])) {
    if ($_POST['PageOffset2'] < $ListPageMax) {
	    $_POST['PageOffset2'] = $_POST['PageOffset'] + 1;
    }
	}

  if (isset($_POST['Previous2'])) {
    if ($_POST['PageOffset2'] > 1) {
	    $_POST['PageOffset2'] = $_POST['PageOffset2'] - 1;
    }
  }

  echo "&nbsp;&nbsp;" . $_POST['PageOffset2'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
?>

  <select name="PageOffset2">

<?php
  $ListPage=1;
  while($ListPage<=$ListPageMax) {
	  if ($ListPage==$_POST['PageOffset2']) {
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
  <INPUT TYPE=SUBMIT NAME="Go2" VALUE="<?php echo _('Go'); ?>">
  <INPUT TYPE=SUBMIT NAME="Previous2" VALUE="<?php echo _('Previous'); ?>">
  <INPUT TYPE=SUBMIT NAME="Next2" VALUE="<?php echo _('Next'); ?>">

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

  if (DB_num_rows($resultnew)<>0){
  	DB_data_seek($resultnew, ($_POST['PageOffset2']-1)*$_SESSION['DisplayRecordsMax']);
  }

	while (($myrow=DB_fetch_array($resultnew)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='SelectNEW' VALUE='%s - %s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>",
			$myrow["debtorno"],
			$myrow['branchcode'],
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
$NoRem = count($remisiones);

while($ii < $NoRem){
	
//print_r($_SESSION[$remisiones[$ii]]);
//echo "<br>";
//echo "<INPUT TYPE=hidden NAME='remisiones['".$ii."'] VALUE='".$remisiones[$ii]."'>";
$ii++;
}

echo "<INPUT TYPE=hidden NAME='Update' VALUE='"._('Yes')."'>";
//$remisiones = $_POST['remisiones'];
echo '</FORM></CENTER>';

echo "<br>";

?>