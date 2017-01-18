<?php
/* $Revision: 14 $ */

$PageSecurity = 11;
include('includes/session.inc');
$title = _('Supplier Discounts');
include('includes/header.inc');

if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	if (!is_numeric($_POST['DiscountRate'])){
		prnMsg( _('The discount rate must be entered as a positive number'),'warn');
		$InputError =1;
	}

	if ($_POST['DiscountRate']<=0 OR $_POST['DiscountRate']>=70){
		prnMsg( _('The discount rate applicable for this record is either less than 0% or greater than 70%') . '. ' . _('Numbers between 1 and 69 are expected'),'warn');
		$InputError =1;
	}

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	if ($InputError !=1) {

		$sql = "INSERT INTO rh_suppdiscounts (supplierid, 
							discount, 
							transtext, 
							automatic) 
					VALUES('" . $_POST['Supplier'] . "', 
						" . $_POST['DiscountRate'] . ", 
						'" . $_POST['Narrative'] . "', 
						" . $_POST['automatic'] . ')';

		$result = DB_query($sql,$db);
		prnMsg( _('The discount matrix record has been added'),'success');
		unset($_POST['Supplier']);
		unset($_POST['Discount']);
		unset($_POST['Narrative']);
		unset($_POST['automatic']);
	}
} elseif ($_GET['Delete']=='yes') {
/*the link to delete a selected record was clicked instead of the submit button */

	$sql="DELETE FROM rh_suppdiscounts
		WHERE id='" .$_GET['ID'] . "'";

	$result = DB_query($sql,$db);
	prnMsg( _('The discount matrix record has been deleted'),'success');
}

$sql = 'SELECT rh_suppdiscounts.*, suppliers.suppname FROM rh_suppdiscounts, suppliers
		WHERE rh_suppdiscounts.supplierid = suppliers.supplierid';
$result = DB_query($sql,$db);

echo '<CENTER><table>';
echo "<tr><td class='tableheader'>" . _('ID') . "</td>
	<td class='tableheader'>" . _('Supplier') . "</td>
	<td class='tableheader'>" . _('Discount') .' %' ."</td>
	<td class='tableheader'>" . _('Narrative') . "</td>
	<td class='tableheader'>" . _('Automatic') . "</td>
	</TR>";

$k=0; //row colour counter

while ($myrow = DB_fetch_array($result)) {
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}
	$DeleteURL = $_SERVER['PHP_SELF'] . '?' . SID . '&Delete=yes&ID=' . $myrow['id'];

	if($myrow['automatic']==true){
		$automatic = _('Yes');
	}else {
		$automatic = _('No');
	}

	printf("<td>%s</td>
		<td>%s</td>
		<td align=right>%s</td>
		<td>%s</td>
		<td>%s</td>
		<td><a href='%s'>" . _('Delete') . '</td>
		</tr>',
		$myrow['id'],
		$myrow['suppname'],
		number_format($myrow['discount'],2),
		$myrow['transtext'],
		$automatic,
		$DeleteURL);

}

echo '</TABLE><HR>';

echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

//----------------------- new supplier discount -------------- //
echo '<TABLE>';

$sql = 'SELECT supplierid, suppname FROM suppliers';

$result = DB_query($sql, $db);

echo '<TR><TD>' . _('Supplier') . ': </TD><TD>';

echo "<SELECT NAME='Supplier'>";

while ($myrow = DB_fetch_array($result)){
	if ($myrow['supplierid']==$_POST['Supplier']){
		echo "<OPTION SELECTED VALUE='" . $myrow['supplierid'] . "'>" . $myrow['supplierid'].' '.$myrow['suppname'];
	} else {
		echo "<OPTION VALUE='" . $myrow['supplierid'] . "'>" . $myrow['supplierid'].' '.$myrow['suppname'];
	}
}

echo '</SELECT>';

echo '<TR><TD>' . _('Discount Rate') . " (%):</TD><TD><input type='Text' name='DiscountRate' SIZE=4 MAXLENGTH=4></TD></TR>";
echo '<TR><TD>' . _('Narrative') . ":</TD><TD><TEXTAREA NAME='Narrative' COLS=20 ROWS=12>".$_POST['Narrative']."</TEXTAREA></TD></TR>";
echo "<TR><TD>"._('Automatic').": </TD><TD>
		<SELECT NAME='automatic'>
			<OPTION SELECTED VALUE=true>"._('Yes')."
			<OPTION VALUE=false>"._('No')."
		</SELECT>
	</TD></TR>";

echo '</TABLE>';

echo "<CENTER><input type='Submit' name='submit' value='" . _('Enter Information') . "'></CENTER>";

echo '</FORM>';

include('includes/footer.inc');
?>
