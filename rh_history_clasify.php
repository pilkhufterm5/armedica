<?php
/* webERP Revision: 1.19 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-18 13:28:12 -0500 (Fri, 18 Apr 2008) $
 * $Rev: 206 $
 */
$PageSecurity = 1;

include('includes/session.inc');
$title = _('Historial de clasificacion');
echo "<html><head><title>".$title."</title></head><body>";
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['stock'])) {
      $sql = 'select stockmaster.stockid,stockmaster.description  from stockmaster where  stockmaster.stockid="'.$_GET['stock'].'"';
    $result = DB_query($sql, $db);
    $myrow = DB_fetch_array($result);
    echo '<h2><center>
        <br />'.$myrow[0].' - '.$myrow[1].' <br />';

    $sql = 'select year(DATE_SUB(fecha_a, INTERVAL 1 MONTH)) as anho,month(DATE_SUB(fecha_a, INTERVAL 1 MONTH)) as mes,clasify  from rh_clasificacion_history where stockid="'.$_GET['stock'].'" order by anho,mes';
    echo '<CENTER><table border=1>';
    echo '<tr>
            <th>' . _('A&ntilde;o') . '</th>
    		<th>' . _('Mes') . '</th>
    		<th>' . _('Clasificacion') . '</th>
			</tr>';

    $k=0;
    $result = DB_query($sql, $db);
    while ($myrow = DB_fetch_row($result)) {
		echo "<tr><td align='right'>".$myrow[0]."</td><td align='right'>".$myrow[1]."</td><td><center>".$myrow[2]."</center></td></tr>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
}
echo "</body></html>";
?>
