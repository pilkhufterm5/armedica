<?php


/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
// bowikaxu - reporte valorizacion

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Stock After Update');
include('includes/header.inc');

echo "<HR><FORM ACTION='" . $_SERVER['PHP_SELF'] . "?". SID . "' METHOD=POST>";


echo '<CENTER><TABLE><TR>';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql, $db);

echo '<TD>' . _('For Stock Location') . ":</TD>
	<TD><SELECT NAME='StockLocation'> ";

while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
		if ($myrow['loccode'] == $_POST['StockLocation']){
		     echo "<OPTION SELECTED VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		} else {
		     echo "<OPTION VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo "<OPTION SELECTED VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo "<OPTION VALUE='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
	}
}
echo '</SELECT></TD></TR>';

echo "<TR><TD COLSPAN=6 ALIGN=CENTER><INPUT TYPE=SUBMIT NAME='ShowStatus' VALUE='" . _('Show Stock Status') ."'></TD></TR></TABLE>";
echo '</FORM><HR>';

$TotalQuantity = 0;

if(isset($_POST['ShowStatus']))
{
	
	if($_POST['StockCategory']!='All'){
		$sql = "SELECT *
			FROM rh_locstocktmp
			WHERE loccode = '".$_POST['StockLocation']."'";
	}else {
		$sql = "SELECT *
			FROM rh_locstocktmp";
	}

	$ErrMsg = _('The stock items in the category selected cannot be retrieved because');
	$DbgMsg = _('The SQL that failed was');

	$StockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

	$SQLOnHandDate = FormatDateForSQL($_POST['OnHandDate']);

	echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';
	$tableheader = "<TR>
				<TD CLASS='tableheader'>" . _('Location') . "</TD>
				<TD CLASS='tableheader'>" . _('Item Code') . "</TD>
				<TD CLASS='tableheader'>" . _('Cantidad') . "</TD></TR>";
	echo $tableheader;
	$TotalValue = 0;
	while ($myrows=DB_fetch_array($StockResult)) {

		$j = 1;
		$k=0; //row colour counter
		if ($k==1){
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k=0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k=1;
		}
		printf("<TD>%s</TD>
					<TD><A TARGET='_blank' HREF='StockStatus.php?%s'>%s</TD>
						<TD ALIGN=RIGHT>%s</TD>",
						$myrows['loccode'],
						SID . '&StockID=' . strtoupper($myrows['stockid']),
						strtoupper($myrows['stockid']),
						$myrows['quantity']);

						echo "</tr>";
	}//end of while loop
	echo '</TABLE>';
}

include('includes/footer.inc');
?>
