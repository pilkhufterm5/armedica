<?php
/* webERP Revision: 1.19 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-18 13:28:12 -0500 (Fri, 18 Apr 2008) $
 * $Rev: 206 $
 */
$PageSecurity = 1;

include('includes/session.inc');
$title = _('Historial de precios');
echo "<html><head><title>".$title."</title></head><body>";
include('includes/SQL_CommonFunctions.inc');
?>
  <script language="javascript">
function link_popup(enlace) {
      features='width=800, height=600,status=0, menubar=0,toolbar=0, scrollbars=1';
      window.open(enlace.getAttribute('href'), 'ventana2', features);

}
</script>
<?php
if (isset($_GET['stock'])&& isset($_GET['debtorno'])) {
    $sql = 'select '.
    ' stockmoves.trandate,stockmoves.price,debtorsmaster.name,stockmaster.stockid,stockmaster.description,MAX(stockmoves.price) as max,'.
    ' MIN(stockmoves.price) as min,AVG(stockmoves.price) as promedio '.
    ' from stockmoves '.
    ' join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type '.
    ' join debtorsmaster on debtorsmaster.debtorno =debtortrans.debtorno '.
    ' join stockmaster on stockmaster.stockid = stockmoves.stockid '.
    ' where stockmoves.type in(10,20000) and stockmoves.hidemovt=0 and stockmoves.stockid="'.$_GET['stock'].'" and debtortrans.debtorno="'.$_GET['debtorno'].'" order by stockmoves.trandate DESC;';
    $result = DB_query($sql, $db);
    $myrow = DB_fetch_array($result);
    echo '<h2><center>
        <br />'.$myrow[3].' - '.$myrow[4].' <br />
        '.$myrow[2].'<br />
        <br />
    </center></h2>';
    echo '<CENTER><table border=1>';
    echo '<tr><td>Maximo</td><td>'.number_format($myrow['max'],2).'</td></tr>';
    echo '<tr><td>Minimo</td><td>'.number_format($myrow['min'],2).'</td></tr>';
    echo '<tr><td>Promedio</td><td>'.number_format($myrow['promedio'],2).'</td></tr>';
    echo '</table></CENTER><BR>';
    echo '<CENTER><table border=1>';
    echo '<tr>
            <th>' . _('Factura') . '</th>
    		<th>' . _('Fecha') . '</th>
    		<th>' . _('Precio') . '</th>
			</tr>';

    $k=0;
    $sql = 'select '.
    ' stockmoves.trandate,(stockmoves.price)as price,debtorsmaster.name,stockmaster.stockid,stockmaster.description,rh_cfd__cfd.serie,rh_cfd__cfd.folio,debtortrans.transno,debtortrans.id '.
    ' from stockmoves '.
    ' join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type '.
    ' join rh_cfd__cfd on debtortrans.id=rh_cfd__cfd.id_debtortrans '.
    ' join debtorsmaster on debtorsmaster.debtorno =debtortrans.debtorno '.
    ' join stockmaster on stockmaster.stockid = stockmoves.stockid '.
    ' where stockmoves.type in(10,20000) and stockmoves.hidemovt=0 and stockmoves.stockid="'.$_GET['stock'].'" and debtortrans.debtorno="'.$_GET['debtorno'].'" '.
    ' order by stockmoves.trandate DESC;';
    $result = DB_query($sql, $db);
    while ($myrow = DB_fetch_row($result)) {
		echo "<tr><td><A target='_blank' onclick='link_popup(this); return false;' href='". $rootpath ."/rh_PrintCustTrans.php?idDebtortrans=".$myrow[8]."&FromTransNo=".$myrow[7]."&InvOrCredit=Invoice&isCfd=1' >".$myrow[5].$myrow[6]."</a></td><td>".$myrow[0]."</td><td><strong>".number_format($myrow[1],2)."</strong></td></tr>";
    } //END WHILE LIST LOOP
    echo '</table></CENTER><BR>';
}
echo "</body></html>";
?>
