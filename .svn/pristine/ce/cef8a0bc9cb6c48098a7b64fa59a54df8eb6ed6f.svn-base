<?php
/* $Revision: 214 $ */
$PageSecurity = 3;

include('includes/session.inc');

$title = _('Especialistas');

include('includes/header.inc');

if (isset($_GET['SelectedSpecialist'])){
	$SelectedSpecialist = strtoupper($_GET['SelectedSpecialist']);
} elseif (isset($_POST['SelectedSpecialist'])){
	$SelectedSpecialist = strtoupper($_POST['SelectedSpecialist']);
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
	$_POST['Code'] = strtoupper($_POST['Code']);
	$sql = "SELECT count(code) from rh_especialistas WHERE code='".$_POST['Code']."'";
	$result = DB_query($sql, $db);
	$myrow = DB_fetch_row($result);
	// mod to handle 3 char area codes
	if (strlen($_POST['Code']) > 10) {
		$InputError = 1;
		prnMsg(_('The espeialist code must be 10 characters or less long'),'error');
		$Errors[$i] = 'Code';
		$i++;
	} elseif ($myrow[0]>0 and !isset($SelectedSpecialist)){
		$InputError = 1;
		prnMsg(_('The specialist code entered already exists'),'error');		
		$Errors[$i] = 'Code';
		$i++;
	} elseif (strlen($_POST['Name']) >79) {
		$InputError = 1;
		prnMsg(_('The specialist name must be 60 characters or less long'),'error');
		$Errors[$i] = 'Name';
		$i++;
	} elseif ( trim($_POST['address1']) == '' ) {
		$InputError = 1;
		prnMsg(_('The especialist address1 may not be empty'),'error');
		$Errors[$i] = 'address1';
		$i++;
	} elseif ( trim($_POST['address2']) == '' ) {
		$InputError = 1;
		prnMsg(_('The especialist address2 may not be empty'),'error');
		$Errors[$i] = 'address2';
		$i++;
	} elseif ( trim($_POST['city']) == '' ) {
		$InputError = 1;
		prnMsg(_('The especialist city may not be empty'),'error');
		$Errors[$i] = 'city';
		$i++;
	} elseif ( trim($_POST['tel']) == '' ) {
		$InputError = 1;
		prnMsg(_('The especialist tel may not be empty'),'error');
		$Errors[$i] = 'tel';
		$i++;
	}
	
	if (isset($SelectedSpecialist) AND $InputError !=1) {

		/*SelectedArea could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/

		$sql = "UPDATE rh_especialistas SET
				name='" . $_POST['Name'] . "',
				address1='" . $_POST['address1'] . "',
				address2='" . $_POST['address2'] . "',
				city='" . $_POST['city'] . "',
				tel='" . $_POST['tel'] . "',
				tel2='" . $_POST['tel2'] . "',
				tel3='" . $_POST['tel3'] . "',
				email='" . $_POST['email'] . "'
			WHERE code = '$SelectedSpecialist'";

		$msg = _('Specialist code') . ' ' . $SelectedSpecialist  . ' ' . _('has been updated');

	} elseif ($InputError !=1) {

	/*Selectedarea is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new area form */

		$sql = "INSERT INTO rh_especialistas (code,
						name, address1, address2, city, tel, tel2, tel3, email)
				VALUES (
					'" . $_POST['Code'] . "',
					'" . $_POST['Name'] . "',
					'" . $_POST['address1'] . "',
					'" . $_POST['address2'] . "',
					'" . $_POST['city'] . "',
					'" . $_POST['tel'] . "',
					'" . $_POST['tel2'] . "',
					'" . $_POST['tel3'] . "',
					'".$_POST['email']."'
					)";

		$SelectedSpecialist =$_POST['Code'];
		$msg = _('New specialist code') . ' ' . $_POST['Code'] . ' ' . _('has been inserted');
	} else {
		$msg='';
	}

	//run the SQL from either of the above possibilites
	if ($InputError !=1) {
		$ErrMsg = _('The specialist could not be added or updated because');
		$DbgMsg = _('The SQL that failed was');
		$result = DB_query($sql, $db, $ErrMsg, $DbgMsg);
		unset($SelectedSpecialist);
		unset($_POST['Code']);
		unset($_POST['Name']);
		unset($_POST['address1']);
		unset($_POST['address2']);
		unset($_POST['city']);
		unset($_POST['tel']);
		unset($_POST['tel2']);
		unset($_POST['tel3']);
		unset($_POST['email']);
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
		$sql="DELETE FROM rh_especialistas WHERE code='" . $SelectedSpecialist . "'";
		$result = DB_query($sql,$db);
		prnMsg(_('Specialist Code') . ' ' . $SelectedSpecialist . ' ' . _('has been deleted') .' !','success');
	} //end if Delete area
	unset($SelectedSpecialist);
	unset($_GET['delete']);
} 

if (!isset($SelectedSpecialist)) {

	$sql = 'SELECT * FROM rh_especialistas';
	$result = DB_query($sql,$db);

	echo '<CENTER><table border=1>';
	echo "<tr>
		<th>" . _('Specialist Code') . "</th>
		<th>" . _('Specialist Name') . "</th>
		<th>" . _('Specialist Address1') . "</th>
		<th>" . _('Specialist Address2') . "</th>
		<th>" . _('Specialist City') . "</th>
		<th>" . _('Specialist tel') . "</th>
		<th>" . _('Specialist tel2') . "</th>
		<th>" . _('Specialist tel3') . "</th>
		<th>" . _('Specialist Email') . '</th>';

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
		echo '<TD>' . $myrow[4] . '</TD>';
		echo '<TD>' . $myrow[5] . '</TD>';
		echo '<TD>' . $myrow[6] . '</TD>';
		echo '<TD>' . $myrow[7] . '</TD>';
		echo '<TD>' . $myrow[8] . '</TD>';
		echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedSpecialist=' . $myrow[0] . '">' . _('Edit') . '</A></TD>';
		echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&SelectedSpecialist=' . $myrow[0] . '&delete=yes">' . _('Delete') . '</A></TD>';

	}
	//END WHILE LIST LOOP
	echo '</TABLE></CENTER>';
}

//end of ifs and buts!

if (isset($SelectedSpecialist)) {
	echo "<CENTER><A HREF='" . $_SERVER['PHP_SELF'] . '?' . SID . "'>" . _('Review Specialists Defined') . '</A></CENTER>';
}


if (!isset($_GET['delete'])) {

	echo "<FORM NAME='especialistas' METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

	if (isset($SelectedSpecialist)) {
		//editing an existing area

		$sql = "SELECT code,
				name,
				address1,
				address2,
				city,
				tel,
				tel2,
				tel3,
				email
			FROM rh_especialistas
			WHERE code='$SelectedSpecialist'";

		$result = DB_query($sql, $db);
		$myrow = DB_fetch_array($result);

		$_POST['Code'] = $myrow['code'];
		$_POST['Name']  = $myrow['name'];
		$_POST['address1']  = $myrow['address1'];
		$_POST['address2']  = $myrow['address2'];
		$_POST['city']  = $myrow['city'];
		$_POST['tel']  = $myrow['tel'];
		$_POST['tel2']  = $myrow['tel2'];
		$_POST['tel3']  = $myrow['tel3'];
		$_POST['email']  = $myrow['email'];

		echo '<INPUT TYPE=HIDDEN NAME=SelectedSpecialist VALUE=' . $SelectedSpecialist . '>';
		echo '<INPUT TYPE=HIDDEN NAME=Code VALUE=' .$_POST['Code'] . '>';
		echo '<CENTER><TABLE><TR><TD>' . _('Specialist Code') . ':</TD><TD>' . $_POST['Code'] . '</TD></TR>';

	} else {
		if (!isset($_POST['Code'])) {
			$_POST['Code'] = '';
		}
		if (!isset($_POST['Name'])) {
			$_POST['Name'] = '';
		}
		if (!isset($_POST['address1'])) {
			$_POST['address1'] = '';
		}
		if (!isset($_POST['address2'])) {
			$_POST['address2'] = '';
		}
		if (!isset($_POST['city'])) {
			$_POST['city'] = '';
		}
		if (!isset($_POST['tel'])) {
			$_POST['tel'] = '';
		}
		if (!isset($_POST['tel2'])) {
			$_POST['tel2'] = '';
		}
		if (!isset($_POST['tel3'])) {
			$_POST['tel3'] = '';
		}
		if (!isset($_POST['email'])) {
			$_POST['email'] = '';
		}
		echo '<CENTER><TABLE>
			<TR>
				<TD>' . _('Specialist Code') . ':</TD>
				<TD><input tabindex="1" ' . (in_array('Code',$Errors) ?  'class="inputerror"' : '' ) .'   type="Text" name="Code" value="' . $_POST['Code'] . '" SIZE=8 MAXLENGTH=10></TD>
			</TR>';
	}

	echo '<TR><TD>' . _('Specialist Name') . ':</TD>
		<TD><input tabindex="2" ' . (in_array('Name',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="Name" value="' . $_POST['Name'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		
		<TR><TD>' . _('Address1') . ':</TD>
		<TD><input tabindex="3" ' . (in_array('address1',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="address1" value="' . $_POST['address1'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		
		<TR><TD>' . _('Address2') . ':</TD>
		<TD><input tabindex="4" ' . (in_array('address2',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="address2" value="' . $_POST['address2'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		<TR><TD>' . _('City') . ':</TD>
		<TD><input tabindex="5" ' . (in_array('city',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="city" value="' . $_POST['city'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		<TR><TD>' . _('Tel') . ':</TD>
		<TD><input tabindex="6" ' . (in_array('tel',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="tel" value="' . $_POST['tel'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		<TR><TD>' . _('Tel2') . ':</TD>
		<TD><input tabindex="7" ' . (in_array('tel2',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="tel2" value="' . $_POST['tel2'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		<TR><TD>' . _('Tel3') . ':</TD>
		<TD><input tabindex="8" ' . (in_array('tel3',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="tel3" value="' . $_POST['tel3'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		<TR><TD>' . _('Email') . ':</TD>
		<TD><input tabindex="9" ' . (in_array('email',$Errors) ?  'class="inputerror"' : '' ) .'  type="Text" name="email" value="' . $_POST['email'] .'" SIZE=30 MAXLENGTH=79></TD>
		</TR>
		
	</TABLE>';

	echo "<CENTER><input tabindex='3' type='Submit' name='submit' value=" . _('Enter Information') .">
		</FORM>";

 } //end if record deleted no point displaying form to add record
 
include('includes/footer.inc');
?>
