<?php
/* webERP Revision: 14 $ */

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:39:36 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 14 $
 */

$PageSecurity = 11;

include ('includes/session.inc');
$title = _('Produce Stock Cost CSV');
include ('includes/header.inc');

function stripcomma($str) { //because we're using comma as a delimiter
	return str_replace(",", "", $str);
}

echo '<P>' . _('Making a comma seperated values file of the current stock cost');

$ErrMsg = _('The SQL to get the stock cost failed with the message');

$sql = 'SELECT stockmaster.stockid, description, units, SUM(locstock.quantity), (materialcost+labourcost+overheadcost) AS cost 
FROM stockmaster
INNER JOIN locstock ON stockmaster.stockid = locstock.stockid
GROUP BY stockid, description';
$result = DB_query($sql, $db, $ErrMsg);

$fp = fopen($_SESSION['reports_dir'] . '/StockCost.csv', "w");

While ($myrow = DB_fetch_row($result)){
	$line = stripcomma($myrow[0]) . ', ' . stripcomma($myrow[1]). ', ' . stripcomma($myrow[2]). ', ' . stripcomma($myrow[3]). ', ' . stripcomma($myrow[4]);
	fputs($fp, $line . "\n");
}

fclose($fp);

echo "<P><A HREF='" . $rootpath . '/' . $_SESSION['reports_dir'] . "/StockCost.csv'>" . _('click here') . '</A> ' . _('to view the file') . '<BR>';

include('includes/footer.inc');

?>
