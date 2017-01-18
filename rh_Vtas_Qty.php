<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
bowikaxu - realhost
Reporte de Ventas por Cantidad

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;

include('includes/session.inc');

$title =_('Ventas').' '._('Por').' '._('Quantity');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
include_once 'php-ofc-library/open_flash_chart_object.php';
?>
 <script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css"
	href="jscalendar/src/css/steel/steel.css" />
<?
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Ventas').' '._('Por').' '._('Quantity')."</B></CENTER><BR>";
// fin headers

// COMIENZA VER RESULTADOS
if(isset($_POST['VerRes'])){

// verify variables
	if($_POST['fecha_ini']=='' OR $_POST['fecha_fin']=='' OR $_POST['location']==''){

		prnMsg('Algunos Campos son invalidos.','error');
		$_POST['ShowMenu']=1;
		
	}else { // todo correcto

        if($_POST['marca']=='%'){
            $Marca="";
        }else{
            $Marca="and stockmaster.rh_marca=".$_POST['marca'];
        }
		/*$sql = "SELECT lastdate_in_period, MONTH(lastdate_in_period) as month,
		DAY(lastdate_in_period) as day,
		YEAR(lastdate_in_period) as year
		FROM periods WHERE periodno = ".$_POST['FromDate']."";
		$f_res = DB_query($sql,$db);
		$from = DB_fetch_array($f_res);
		
		$sql = "SELECT lastdate_in_period, MONTH(lastdate_in_period) as month, 
		DAY(lastdate_in_period) as day,
		YEAR(lastdate_in_period) as year
		FROM periods WHERE periodno = ".$_POST['ToDate']."";
		$t_res = DB_query($sql,$db);
		$to = DB_fetch_array($t_res);   */
	
		echo "<CENTER><B>Desde: "._($_POST['fecha_ini'])." - Hasta: "._($_POST['fecha_fin'])."</B></CENTER><BR>";
		echo "<CENTER><B>"._('Location').": ".$_POST['location']."</B></CENTER>";
		
		if($_POST['location']=='Todos'){
            $AUXLOC  = "LIKE '%'";
		}else {
            $AUXLOC  = "= '".$_POST['location']."'";
		}
		
		$SQL ="SELECT
                (SUM(-1*stockmoves.qty)/(datediff('".$_POST['fecha_fin']."','".$_POST['fecha_ini']."')+1)) as promedio,
				stockmoves.stockid,
				stockmoves.prd,
				stockmoves.loccode,
				stockmoves.price,
				SUM(stockmoves.price*stockmoves.qty*-1*(1-stockmoves.discountpercent)) as totalprice,
				SUM(-1*stockmoves.qty) as qty,
				stockmoves.standardcost,
				SUM(stockmoves.qty*stockmoves.standardcost*-1) as totalcost,
				stockmaster.description,
				rh_locations.locationname
				FROM stockmoves
				INNER JOIN stockmaster ON stockmoves.stockid = stockmaster.stockid ".$Marca."
				INNER JOIN rh_locations ON rh_locations.loccode = stockmoves.loccode
				INNER JOIN debtortrans ON debtortrans.transno = stockmoves.transno AND debtortrans.type = abs(stockmoves.type)
				WHERE
				stockmoves.type IN (10,11,20000,-10)
				AND stockmoves.loccode ".$AUXLOC."
				AND date(stockmoves.trandate) >= '".$_POST['fecha_ini']."'
				AND date(stockmoves.trandate) <= '".$_POST['fecha_fin']."'
				AND debtortrans.type IN (10,11,20000)
				GROUP BY stockmoves.stockid
				ORDER BY stockmoves.stockid, stockmaster.description";

		$result = DB_query($SQL,$db,"Imposible obtener datos de venta.");

        //echo $SQL;
		/*show a table of the transactions returned by the SQL */

		echo "<BR><center>";
	   //	open_flash_chart_object( 300, 300, $online . "rh_charts_vtasqty.php?".SID."&location=".$_POST['location']."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."",false);
	//	open_flash_chart_object( 300, 300, $online . "rh_charts_vtasprice.php?".SID."&location=".$_POST['location']."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."",false);
		echo "</center><BR>";

        ?>
            <center>
            <form action="Reporte_VtaQTY.php" method="POST" target="_blank" >
                <input type="hidden" value="<? echo $_POST['fecha_ini']; ?>" name="fecha_ini" />
                <input type="hidden" value="<? echo $_POST['fecha_fin']; ?>" name="fecha_fin" />
                <input type="hidden" value="<? echo $_POST['location']; ?>" name="location" />
                <input type="hidden" value="<? echo $_POST['marca']; ?>" name="marca" />
                <input type="submit" value="Imprimir PDF" name="PrintPDF" />
            </form>
            </center>
        <?
		
		echo '<CENTER><TABLE CELLPADDING=4>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Code') . 
			"</TD><TD CLASS='tableheader'>" . _('Description') . 
			"</TD><TD CLASS='tableheader'>" . _('Total').' '._('Quantity') . 
			"</TD><TD CLASS='tableheader'>" . _('Venta') .
			"</TD><TD CLASS='tableheader'>" . _('Cost') .
			"</TD><TD CLASS='tableheader'>" . _('Profit') . 
			"</TD><TD CLASS='tableheader'>" . _('Promedio') . 
			"</TD><TD CLASS='tableheader'>" . _('Total Qty On Hand') .
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
			//rleal abr 10 2012
			//Se cambia el codigo para que funcione
			$sql_qty = "SELECT sum(quantity) as quantity FROM locstock WHERE loccode ".$AUXLOC."
						AND stockid = '".$res['stockid']."'";
			$qty_res = DB_query($sql_qty,$db);
			$qty_info = DB_fetch_array($qty_res);
			
			/*$after = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,$from['month'],1,$from['year']));
			$before = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,$to['month'],$to['day'],$to['year'])); */

			printf("<TD ALIGN=LEFT><A HREF='StockMovements.php?".SID."&StockID=".$res['stockid']./*"&BeforeDate=".$before."&AfterDate=".$after.*/"'>%s</A></TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['stockid'],
					$res['description'],
					number_format($res['qty'],2),
					number_format($res['totalprice'],2),
					number_format($res['totalcost'],2),
					number_format($res['totalprice']-$res['totalcost'],2),
					number_format($res['promedio'],2),
					number_format($qty_info['quantity'],2));
			
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
	
	echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo "<CENTER><TABLE><TR>";
	
	$sql = "SELECT loccode, locationname FROM rh_locations ORDER BY locationname";
	$res = DB_query($sql,$db,'Imposible determinar Ubicacion.');
	echo "<TD>"._('Location').": </TD><TD>";
	echo "<SELECT NAME='location'>";
	
	echo "		<OPTION VALUE='Todos'>Todos</OPTION>";
	while ($items = DB_fetch_array($res)){
		
		if($items['loccode']==$item){
			echo "<OPTION VALUE='".$items['loccode']."' SELECTED>".$items['locationname']."</OPTION>";
		}else {
			echo "<OPTION VALUE='".$items['loccode']."'>".$items['locationname']."</OPTION>";
		}
	}
	echo "</SELECT></TD></TR>";
	
	$sql = "SELECT * FROM periods ORDER by periodno";
	$p_res = DB_query($sql,$db);
	/*
	echo "<TR>
			<TD>"._('Fecha').' '._('desde')."</TD>
			<TD><SELECT NAME='FromDate'>";
	while($p_info = DB_fetch_array($p_res)){
		if($_POST['FromDate']==$p_info['periodno']){
			echo "<OPTION SELECTED VALUE='".$p_info['periodno']."'>"._(MonthAndYearFromSQLDate($p_info['lastdate_in_period']));
		}else {
			echo "<OPTION VALUE='".$p_info['periodno']."'>"._(MonthAndYearFromSQLDate($p_info['lastdate_in_period']));
		}
	}

	echo "</SELECT></TD></TR>";

	$sql = "SELECT * FROM periods ORDER by periodno";
	$p_res = DB_query($sql,$db);

	echo "<TR>
			<TD>"._('Fecha').' '._('hasta')."</TD>
			<TD><SELECT NAME='ToDate'>";
	while($p_info = DB_fetch_array($p_res)){
		if($_POST['FromDate']==$p_info['periodno']){
			echo "<OPTION SELECTED VALUE='".$p_info['periodno']."'>"._(MonthAndYearFromSQLDate($p_info['lastdate_in_period']));
		}else {
			echo "<OPTION VALUE='".$p_info['periodno']."'>"._(MonthAndYearFromSQLDate($p_info['lastdate_in_period']));
		}
	}

	echo "</SELECT></TD></TR>";   */
 echo '<tr>
		<td >Fecha inicial:</td>
		<td ><input type="text" name="fecha_ini" id="fecha_ini"
			style="width: 50%" value="'.$_POST ['fecha_ini'].'" /></td>
	    </tr> ';
    	echo '<tr>
		<td>Fecha Final:</td>
		<td><input type="text" name="fecha_fin" id="fecha_fin"
			style="width: 50%" value="'.$_POST ['fecha_fin'].'" /></td>
	    </tr>';

	$sql = "SELECT * FROM rh_marca ORDER by id";
	$p_res = DB_query($sql,$db);

	echo "<TR>
			<TD>"._('Marca')."</TD>
			<TD><SELECT NAME='marca'>";
            echo "<OPTION SELECTED VALUE='%'> ---------------------------- Todas -------------------------------";
	while($p_info = DB_fetch_array($p_res)){
		if($_POST['marca']==$p_info['id']){
			echo "<OPTION SELECTED VALUE='".$p_info['id']."'>".$p_info['nombre'];
		}else {
			echo "<OPTION VALUE='".$p_info['id']."'>".$p_info['nombre'];
		}
	}

	echo "</SELECT></TD></TR>";
	
	echo "</TABLE>";
	echo "<INPUT TYPE=submit NAME='VerRes' VALUE='"._('Ver Resultados')."'>";
	echo "</CENTER></FORM>";
// fin meu principal busqueda
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<br />";
    echo "<br />";
}

?>

<script language="JavaScript">
<!-- // create calendar object(s) just after form tag closed
				 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
				 // note: you can have as many calendar objects as you need for your application
var cal = new CalendarPopup();
				//-->
</script>

<?php
echo "<script type=\"text/javascript\">//<![CDATA[
      var cal2 = Calendar.setup({
          onSelect: function(cal2) { cal2.hide() },
          showTime: false
      });

      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });
      cal.setLanguage('es');
      cal.manageFields(\"fecha_ini\", \"fecha_ini\", \"%Y-%m-%d\");

      cal2.setLanguage('es');
      cal2.manageFields(\"fecha_fin\", \"fecha_fin\", \"%Y-%m-%d\");
    //]]>
</script>";
include('includes/footer.inc');

?>