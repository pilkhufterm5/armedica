<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
bowikaxu - realhost
Reporte del Historial de Precios

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;

include('includes/session.inc');

$title = _('Price History');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Price History')."</B></CENTER><BR>";

if(isset($_GET['StockID']) && $_GET['StockID']!=''){
	
	$item = $_GET['StockID'];
	
}

// COMIENZA VER RESULTADOS
if(isset($_POST['VerRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' OR $_POST['FromDebtor']=='' OR $_POST['ToDebtor']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Algunos Campos Son Invalidos</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER><B>Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</B></CENTER><BR>";
		echo "<CENTER><B>Desde: ".$_POST['FromDebtor']." - Hasta: ".$_POST['ToDebtor']."</B></CENTER><BR>";
		echo "<CENTER><B>Articulo(s): ".$_POST['stockid']."</B></CENTER>";
		
		if($_POST['stockid']=='Todos'){
			$_POST['stockid'] = "LIKE '%'";
		}else {
			$_POST['stockid'] = "= '".$_POST['stockid']."'";
		}

                /*
                 * rleal
                 * Mar 14 2011
                 * Se formatea la fecha
                 */

                $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
                $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

		$SQL = "SELECT rh_pricehistory.*
				FROM rh_pricehistory
				WHERE 
					rh_pricehistory.stockid ".$_POST['stockid']."
					AND rh_pricehistory.debtorno >= '".$_POST['FromDebtor']."'
					AND rh_pricehistory.debtorno <= '".$_POST['ToDebtor']."'
					AND rh_pricehistory.trandate >= '".$SQL_FromDate."'
					AND rh_pricehistory.trandate <= '".$SQL_ToDate."'
					ORDER BY trandate, stockid, debtorno";
		
		$result = DB_query($SQL,$db,"Imposible obtener historial de precios");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Stock Code') . 
			"</TD><TD CLASS='tableheader'>" . _('Customer Code') . 
			"</TD><TD CLASS='tableheader'>" . _('Customer branch') . 
			"</TD><TD CLASS='tableheader'>" . _('Price List') .
			"</TD><TD CLASS='tableheader'>" . _('Old Price') .
			"</TD><TD CLASS='tableheader'>" . _('Price') . 
			"</TD><TD CLASS='tableheader'>" . _('Home Currency') . 
			"</TD><TD CLASS='tableheader'>" . _('Date') .
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
			
			printf("<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['stockid'],
					$res['debtorno'],
					$res['branchcode'],
					$res['typeabbrev'],
					number_format($res['lastprice'],2),
					number_format($res['price'],2),
					$res['currabrev'],
					$res['trandate']);
			
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

$sql = "SELECT stockid FROM rh_pricehistory GROUP BY stockid ORDER BY stockid";
$res = DB_query($sql,$db,'Imposible determinar articulos');
echo "<TD>"._('Items').": </TD><TD>";
echo "<SELECT NAME='stockid'>";

echo "		<OPTION VALUE='Todos'>Todos</OPTION>";
while ($items = DB_fetch_array($res)){
	
	if($items['stockid']==$item){
		echo "<OPTION VALUE='".$items['stockid']."' SELECTED>".$items['stockid']."</OPTION>";
	}else {
		echo "<OPTION VALUE='".$items['stockid']."'>".$items['stockid']."</OPTION>";
	}
}
echo "</SELECT></TD></TR>";

echo "<TR><TD>"._('Cliente').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDebtor' VALUE='1'></TD></TR>";
echo "<TR><TD>"._('Cliente').' '._('To').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDebtor' VALUE='zzzz'></TD></TR>";

echo "<TR><TD>"._('Fecha').' '._('desde').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='VerRes' VALUE='"._('Ver Resultados')."'>";
echo "</CENTER></FORM>";
// fin meu principal busqueda
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

include('includes/footer.inc');

?>