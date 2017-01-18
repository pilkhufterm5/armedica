<?php

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* 

bowikaxu - realhost
Reporte de Proveedores

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('Supplier Inquiry Dates');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Supplier Inquiry Dates')."</B></CENTER><BR>";

if(isset($_POST['ShowSupp'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' OR $_POST['FromSupp']=='' OR $_POST['ToSupp']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Some fields hare invalid</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		$SQL = "SELECT supplierno FROM supptrans WHERE trandate<='".FormatDateForSQL($_POST['ToDate'])."' AND trandate>='".FormatDateForSQL($_POST['FromDate'])."' 
				AND supplierno>='".$_POST['FromSupp']."' AND supplierno<='".$_POST['ToSupp']."'
				GROUP BY supplierno";
		
		$result = DB_query($SQL,$db,"Imposible obtener proveedor");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Supplier') . 
			"</TD><TD CLASS='tableheader'>" . _('Facturado') . 
			"</TD><TD CLASS='tableheader'>" . _('Pagado') .
			"</TD><TD CLASS='tableheader'>" . _('Saldo') . 
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		while($res = DB_fetch_array($result)){
			
			$SQL = "SELECT SUM(ovamount+ovgst) AS facturado FROM supptrans WHERE supplierno='".$res['supplierno']."'
				AND trandate<='".FormatDateForSQL($_POST['ToDate'])."' AND trandate>='".FormatDateForSQL($_POST['FromDate'])."' AND type = 20";
			$factres = DB_query($SQL,$db,"Imposible obtener Facturado");
			$Fact = DB_fetch_array($factres);
			
			$SQL = "SELECT SUM(ovamount+ovgst) AS pagado FROM supptrans WHERE supplierno= '".$res['supplierno']."'
				AND trandate<='".FormatDateForSQL($_POST['ToDate'])."' AND trandate>='".FormatDateForSQL($_POST['FromDate'])."' AND type != 20";
			$pagadores = DB_query($SQL,$db,"Imposible obtener pagado");
			$Pagado = DB_fetch_array($pagadores);
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['supplierno'],
					number_format($Fact['facturado'],2),
					number_format($Pagado['pagado'],2),
					number_format((($Fact['facturado'])-($Pagado['pagado']*-1)),2));
			
		}
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Back')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['ShowSupp']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";
echo "<TD>"._('Supplier').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromSupp' VALUE='111'></TD></TR>";
echo "<TR><TD>"._('Supplier').' '._('To').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToSupp' VALUE='zzzz'></TD></TR>";
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
echo "<INPUT TYPE=submit NAME='ShowSupp' VALUE='"._('Ver Resultados')."'>";
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
