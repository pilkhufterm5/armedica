<?php
/* $Revision: 214 $ */
$PageSecurity = 3;

include('includes/session.inc');

$title = _('Machines');

echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');


if (isset($_GET['SelectedMachine'])){
	$SelectedMachine = strtoupper($_GET['SelectedMachine']);
} elseif (isset($_POST['SelectedMachine'])){
	$SelectedMachine = strtoupper($_POST['SelectedMachine']);
}

if (isset($Errors)) {
	unset($Errors);
}
$Errors = array();

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;
	$i=1;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	$_POST['MachineCode'] = strtoupper($_POST['MachineCode']);
	$sql = "SELECT count(code) from rh_maquinas WHERE code='".$_POST['MachineCode']."'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_row($result);
	// mod to handle 3 char area codes
	if (strlen($_POST['MachineCode']) > 10) {
		$InputError = 1;
		prnMsg(_('The machine code must be 10 characters or less long'),'error');
		$Errors[$i] = 'MachineCode';
		$i++;
	} elseif ($myrow[0]>0 and !isset($SelectedMachine)){
		$InputError = 1;
		prnMsg(_('The machine code entered already exists'),'error');		
		$Errors[$i] = 'MachineCode';
		$i++;
	} elseif (strlen($_POST['MachineName']) >79) {
		$InputError = 1;
		prnMsg(_('The machine name must be 80 characters or less long'),'error');
		$Errors[$i] = 'MachineName';
		$i++;
	} elseif ( trim($_POST['Date']) == '' ) {
		$InputError = 1;
		prnMsg(_('The machine date may not be empty'),'error');
		$Errors[$i] = 'Date';
		$i++;
	}
	
	if (isset($SelectedMachine) AND $InputError !=1) {

		/*SelectedArea could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		$sql = "UPDATE rh_maquinas SET
				name='" . $_POST['MachineName'] . "',
				description='" . $_POST['MachineDescription'] . "',
				buydate='" . $_POST['Date'] . "'
			WHERE code = '$SelectedMachine'";

		$msg = _('Machine code') . ' ' . $SelectedMachine  . ' ' . _('has been updated');

	} elseif ($InputError !=1) {

	/*Selectedarea is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new area form */

		$sql = "INSERT INTO rh_maquinas (code,
						name, description, buydate)
				VALUES (
					'" . $_POST['MachineCode'] . "',
					'" . $_POST['MachineName'] . "',
					'" . $_POST['MachineDescription'] . "',
					'".$_POST['Date']."'
					)";

		$SelectedMachine =$_POST['MachineCode'];
		$msg = _('New machine code') . ' ' . $_POST['MachineCode'] . ' ' . _('has been inserted');
	} else {
		$msg='';
	}

	//run the SQL from either of the above possibilites
	if ($InputError !=1) {
		$ErrMsg = _('The machine could not be added or updated because');
		$DbgMsg = _('The SQL that failed was');
		$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
		unset($SelectedMachine);
		unset($_POST['MachineCode']);
		unset($_POST['MachineName']);
		unset($_POST['MachineDescription']);
		unset($_POST['Date']);
		prnMsg($msg,'success');
	}
	
} elseif (isset($_GET['delete'])) {
//the link to delete a selected record was clicked instead of the submit button

	$CancelDelete = 0;

// PREVENT DELETES IF DEPENDENT RECORDS IN 'DebtorsMaster'
	/*
	$sql= "SELECT COUNT(*) FROM custbranch WHERE custbranch.area='$SelectedArea'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		$CancelDelete = 1;
		prnMsg( _('Cannot delete this area because customer branches have been created using this area'),'warn');
		echo '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('branches using this area code');

	} else {
		$sql= "SELECT COUNT(*) FROM salesanalysis WHERE salesanalysis.area ='$SelectedArea'";
		$result = DB_query($sql,$db);
		$myrow = DB_fetch_row($result);
		if ($myrow[0]>0) {
			$CancelDelete = 1;
			prnMsg( _('Cannot delete this area because sales analysis ecords exist that use this area'),'warn');
			echo '<br>' . _('There are') . ' ' . $myrow[0] . ' ' . _('sales analysis records referring this area code');
		}
	}
	*/
	if ($CancelDelete==0) {
		$sql="DELETE FROM rh_maquinas WHERE code='" . $SelectedMachine . "'";
		$result = DB_query($sql,$db);
		prnMsg(_('Machine Code') . ' ' . $SelectedMachine . ' ' . _('has been deleted') .' !','success');
	} //end if Delete area
	unset($SelectedMachine);
	unset($_GET['delete']);
} 

if (!isset($SelectedMachine)) {

	$sql = 'SELECT * FROM rh_maquinas';
	$result = DB_query($sql,$db);

	echo '<CENTER><table border=1>';
	echo "<tr>
		<th>" . _('Machine Code') . "</th>
		<th>" . _('Machine Name') . "</th>
		<th>" . _('Machine Description') . "</th>
		<th>" . _('Machine Date') . '</th>';

	$k=0; //row colour counter

	while ($myrow = DB_fetch_row($result)) {
		if ($k==1){
			echo '<tr class="EvenTableRows">';
			$k=0;
		} else {
			echo '<tr class="OddTableRows">';
			$k++;
		}

		echo '<TD>' . $myrow[0] . '</TD>';
		echo '<TD>' . $myrow[1] . '</TD>';
		echo '<TD>' . $myrow[2] . '</TD>';
		echo '<TD>' . $myrow[3] . '</TD>';
		echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedMachine=' . $myrow[0] . '">' . _('Edit') . '</A></TD>';
		echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedMachine=' . $myrow[0] . '&delete=yes">' . _('Delete') . '</A></TD>';

	}
	//END WHILE LIST LOOP
	echo '</TABLE></CENTER>';
}

//end of ifs and buts!

if (isset($SelectedMachine)) {
	echo "<CENTER><A HREF='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>" . _('Review Machines Defined') . '</A></CENTER>';
}


if (!isset($_GET['delete'])) {

	echo "<FORM NAME='maquinas' METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

	if (isset($SelectedMachine)) {
		//editing an existing area

		$sql = "SELECT code,
				name,
				description,
				buydate
			FROM rh_maquinas
			WHERE code='$SelectedMachine'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['MachineCode'] = $myrow['code'];
		$_POST['MachineName']  = $myrow['name'];
		$_POST['MachineDescription']  = $myrow['description'];
		$_POST['Date']  = $myrow['buydate'];

		echo '<INPUT TYPE=HIDDEN NAME=SelectedMachine VALUE=' . $SelectedMachine . '>';
		echo '<INPUT TYPE=HIDDEN NAME=MachineCode VALUE=' .$_POST['MachineCode'] . '>';
		echo '<CENTER><TABLE><TR><TD>' . _('Machine Code') . ':</TD><TD>' . $_POST['MachineCode'] . '</TD></TR>';

	} else {
		if (!isset($_POST['MachineCode'])) {
			$_POST['MachineCode'] = '';
		}
		if (!isset($_POST['MachineName'])) {
			$_POST['MachineName'] = '';
		}
		if (!isset($_POST['MachineDescription'])) {
			$_POST['MachineDescription'] = '';
		}
		echo '<CENTER><TABLE>
			<TR>
				<TD>' . _('Machine Code') . ':</TD>
				<TD><input tabindex="1" ' . (in_array('MachineCode',$Errors) ?  'class="inputerror"' : '' ) .'   type="Text" name="MachineCode" value="' . $_POST['MachineCode'] . '" SIZE=8 MAXLENGTH=10></TD>
			</TR>';
	}

	echo '<TR><TD>' . _('Machine Name') . ':</TD>
		<TD><input tabindex="2" ' . (in_array('MachineName',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="MachineName" value="' . $_POST['MachineName'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		
		<TR><TD>' . _('Machine Description') . ':</TD>
		<TD><TEXTAREA NAME="MachineDescription" COLS=20 ROWS=5>'.$_POST['MachineDescription'].'</TEXTAREA></TD>
		</TR>
		
		<TR><TD>' . _('Date') . ':</TD>
		<TD><input tabindex="2" ' . (in_array('Date',$Errors) ?  'class="inputerror"' : '' ) ."  type='Text' name='Date' value='" . $_POST['Date'] ."' SIZE=15 MAXLENGTH=20>
		<a href=\"#\" onclick=\"maquinas.Date.value='';cal.select(document.forms['maquinas'].Date,'date_anchor','yyyy-M-d');
                      return false;\" name=\"date_anchor\" id=\"date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>	
		</TD>
		</TR>
		
	</TABLE>";

	echo "<CENTER><input tabindex='3' type='Submit' name='submit' value=" . _('Enter Information') .">
		</FORM>";

 } //end if record deleted no point displaying form to add record

 ?>

<script language="JavaScript">
<!-- // create calendar object(s) just after form tag closed
				 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
				 // note: you can have as many calendar objects as you need for your application
var cal = new CalendarPopup();
				//-->
</script>

<?php
 
include('includes/footer.inc');
?>
