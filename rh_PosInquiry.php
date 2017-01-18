<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/* 
bowikaxu - realhost
Reporte de Punto de Venta

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;

include('includes/session.inc');

$title = _('Reporte Punto de Venta');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Reporte Punto de Venta')."</B></CENTER><BR>";

if(isset($_POST['VerRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Algunos Campos Son Invalidos</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER><B>Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</B></CENTER><BR>";
		echo "<CENTER><B>Vendedor(a): ".$_POST['usuario']." - Terminal: ".$_POST['terminales']."</B></CENTER>";
		
		if($_POST['usuario']=='Todos'){
			$usuario = "LIKE '%'";
		}else {
			$usuario = "= '".$_POST['usuario']."'";
		}
		if($_POST['terminales']=='Todas'){
			$terminal = "LIKE '%'";
		}else {
			$terminal = "= '".$_POST['terminales']."'";
		}
		
		$SQL = "SELECT rh_possales.*, 
				salesorders.orddate, 
				salesorders.debtorno
				FROM rh_possales,
					salesorders
				WHERE
					salesorders.orddate >= '".$_POST['FromDate']."'
					AND salesorders.orddate <= '".$_POST['ToDate']."'
					AND rh_possales.user ".$usuario."
					AND rh_possales.ip ".$terminal." 
					AND rh_possales.trans = salesorders.orderno
					ORDER BY rh_possales.trans,rh_possales.user,rh_possales.ip";

		$result = DB_query($SQL,$db,"Imposible obtener ventas del POS");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('# Remision') . 
			"</TD><TD CLASS='tableheader'>" . _('Vendedor(a)') . 
			"</TD><TD CLASS='tableheader'>" . _('Cliente') . 
			"</TD><TD CLASS='tableheader'>" . _('Terminal') .
			"</TD><TD CLASS='tableheader'>" . _('Fecha') .
			"</TD><TD CLASS='tableheader'>" . _('Efectivo') . 
			"</TD><TD CLASS='tableheader'>" . _('Tarjeta de Credito') .
			"</TD><TD CLASS='tableheader'>" . _('Cheque') .
			"</TD><TD CLASS='tableheader'>" . _('Bono') .
			"</TD><TD CLASS='tableheader'>" . _('Total') .
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		$TotalFinal = 0;
		$TotalEfectivo = 0;
		$TotalTarjeta = 0;
		$TotalCheque = 0;
		$TotalBono = 0;
		while($res = DB_fetch_array($result)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD ALIGN=RIGHT><A HREF='OrderDetails.php?&OrderNumber=%s'>%s</A></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['trans'],
					$res['trans'],
					$res['user'],
					$res['debtorno'],
					$res['ip'],
					$res['orddate'],
					number_format($res['cash'],2),
					number_format($res['credcard'],2),
					number_format($res['cheque'],2),
					number_format($res['bono'],2),
					number_format($res['total'],2));
					$TotalFinal += $res['total'];
					$TotalEfectivo += $res['cash'];
					$TotalTarjeta += $res['credcard'];
					$TotalCheque += $res['cheque'];
					$TotalBono += $res['bono'];
			
		}
		
		if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
		
		printf("<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD><br>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					'Totales',
					'',
					'',
					'',
					'',
					number_format($TotalEfectivo,2),
					number_format($TotalTarjeta,2),
					number_format($TotalCheque,2),
					number_format($TotalBono,2),
					number_format($TotalFinal,2));
							
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Regresar')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['VerRes']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";

$sql = "SELECT user FROM rh_possales GROUP BY user";
$res = DB_query($sql,$db,'Imposible determinar usuarios');
echo "<TD>"._('Cajeros(as)').": </TD><TD>";
echo "<SELECT NAME='usuario'>
		<OPTION VALUE='Todos' SELECTED>Todos</OPTION>";
while ($user = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$user['user']."'>".$user['user']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

$sql = "SELECT ip FROM rh_possales GROUP BY ip";
$res = DB_query($sql,$db,'Imposible determinar terminales');
echo "<TD>"._('Terminales').": </TD><TD>";
echo "<SELECT NAME='terminales'>
		<OPTION VALUE='Todas' SELECTED>Todas</OPTION>";
while ($term = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$term['ip']."'>".$term['ip']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

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