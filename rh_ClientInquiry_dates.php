<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-04-14 10:08:39 -0500 (Mon, 14 Apr 2008) $
 * $Rev: 171 $
 */
/*
bowikaxu - realhost
View Customer Balance within two dates and within supplier name

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('Customer Inquiry Dates');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Customer Inquiry Dates')."</B></CENTER><BR>";

//  2007/02/08 bowikaxu mostrar facturado de determinado cliente
if(isset($_GET['Factur'])){
	
	$Total = 0;
	$Alloc = 0;
	$Disc = 0;
	echo "<CENTER><STRONG> ".$_GET['Factur']."</STRONG><BR>Desde: ".$_GET['From']." - A: ".$_GET['To']."</CENTER><BR>";
	$SQL = "SELECT debtortrans.transno, debtortrans.trandate, debtortrans.debtorno, debtortrans.branchcode, (debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS Total, debtortrans.alloc, debtortrans.ovdiscount, systypes.typename, debtortrans.branchcode,
	debtortrans.type
	FROM debtortrans,systypes
			WHERE debtorno='".$_GET['Factur']."' AND debtortrans.trandate<='".$_GET['To']."' 
			AND debtortrans.trandate>='".$_GET['From']."'
			AND debtortrans.type = systypes.typeid
			ORDER BY debtortrans.branchcode";
	
	$res = DB_query($SQL,$db,'Imposible obtener los datos del cliente');
	
	echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Cliente/Sucursal') . 
			"</TD><TD CLASS='tableheader'>" . _('Concepto') . 
			"</TD><TD CLASS='tableheader'>" . _('Transaccion') . 
			"</TD><TD CLASS='tableheader'>" . _('Total') .
			"</TD><TD CLASS='tableheader'>" . _('Allocated') .
			"</TD><TD CLASS='tableheader'>" . _('Descuento') .
			"</TD><TD CLASS='tableheader'>" . _('Balance') .			
			"</TD><TD CLASS='tableheader'>" . _('Fecha') . 
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
	
	while($info = DB_fetch_array($res)){
		
		if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			if($info['type']==20000){
				$sql = "SELECT Facturado FROM rh_invoiceshipment WHERE Shipment = ".$info['transno']."";
				$RemRes = DB_query($sql,$db);
				$Rem = DB_fetch_array($RemRes);
				if($Rem['Facturado']==0){
					$Tot += $info['Total'];
					$Alloc += $info['alloc'];
					$Disc += $info['ovdiscount'];
					printf("<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD></TR>",
					$info['debtorno'].'/'.$info['branchcode'],
					$info['typename'],
					$info['transno'],					
					number_format($info['Total'],2),
					number_format($info['alloc'],2),
					number_format($info['ovdiscount'],2),
					number_format(($info['Total']-$info['alloc']+$info['ovdiscount']),2),
					$info['trandate']
					);
				}
			}else {
					$Tot += $info['Total'];
					$Alloc += $info['alloc'];
					$Disc += $info['ovdiscount'];
					printf("<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD></TR>",
					$info['debtorno'].'/'.$info['branchcode'],
					$info['typename'],
					$info['transno'],					
					number_format($info['Total'],2),
					number_format($info['alloc'],2),
					number_format($info['ovdiscount'],2),
					number_format(($info['Total']-$info['alloc']+$info['ovdiscount']),2),
					$info['trandate']
					);
				}
			
			
		}
		echo "<TR><TD></TD><TD></TD><TD></TD>
		<TD ALIGN=RIGHT>".number_format($Tot,2)."</TD>
		<TD  ALIGN=RIGHT>".number_format($Alloc,2)."</TD>
		<TD  ALIGN=RIGHT>".number_format($Disc,2)."</TD>
		<TD  ALIGN=RIGHT>".number_format(($Tot-$Alloc-$Disc),2)."</TD>
		</TR>";		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
		echo "</FORM>";
		include('includes/footer.inc');
		exit;
	
}
// fin mostrar facturado

// 2007/02/08 bowikaxu mostrar balance de saldo
if(isset($_POST['ShowSupp'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' OR $_POST['FromSupp']=='' OR $_POST['ToSupp']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Some fields are invalid</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER> Desde: ".$_POST['FromDate']." - A: ".$_POST['ToDate']."</CENTER><BR>";
		
		$SQL = "SELECT debtorno FROM debtortrans WHERE trandate<='".$_POST['ToDate']."' AND trandate>='".$_POST['FromDate']."' 
				AND debtorno>='".$_POST['FromSupp']."' AND debtorno<='".$_POST['ToSupp']."'
				GROUP BY debtorno
				ORDER BY debtorno";
		
		$result = DB_query($SQL,$db,"Imposible obtener cliente");
		
		/*show a table of the transactions returned by the SQL */
/****************************************************************************************************************************
* Jorge Garcia
* 26/Ene/2009 Notas de credito
****************************************************************************************************************************/
		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Cliente') . 
			"</TD><TD CLASS='tableheader'>" . _('Facturado/Remisionado') . 
			"</TD><TD CLASS='tableheader'>" . _('Notas de Credito') .
			"</TD><TD CLASS='tableheader'>" . _('Pagado') .
			"</TD><TD CLASS='tableheader'>" . _('Saldo') . 
			"</TD></TR>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		
		$fdate = explode("/",$_POST['FromDate']);
		$format_date = $fdate[2]."/".$fdate['1']."/".$fdate[0];
		
		while($res = DB_fetch_array($result)){
			
			// facturado
			$SQL = "SELECT SUM(ovamount+ovgst+ovfreight) AS facturado FROM debtortrans WHERE debtorno='".$res['debtorno']."'				
				AND trandate<='".$_POST['ToDate']."' AND trandate>='".$_POST['FromDate']."'
				AND type = 10";
			$factres = DB_query($SQL,$db,"Imposible obtener Facturado");
			$Fact = DB_fetch_array($factres);
			// remisionado
			$SQL = "SELECT SUM(ovamount+ovgst+ovfreight) AS remisionado FROM debtortrans WHERE debtorno='".$res['debtorno']."'				
				AND trandate<='".$_POST['ToDate']."' AND trandate>='".$_POST['FromDate']."'
				AND type = 20000
				AND transno IN (SELECT Shipment FROM rh_invoiceshipment WHERE Facturado = 0)";
			$remres = DB_query($SQL,$db,"Imposible obtener Facturado");
			$Rem = DB_fetch_array($remres);
			
			$SQL = "SELECT SUM(ovamount+ovgst+ovfreight)*-1 AS pagado FROM debtortrans WHERE debtorno= '".$res['debtorno']."'
				AND trandate<='".$_POST['ToDate']."' AND trandate>='".$_POST['FromDate']."'
				AND type = 12";
			$pagadores = DB_query($SQL,$db,"Imposible obtener pagado");
			$Pagado = DB_fetch_array($pagadores);
/****************************************************************************************************************************
* Jorge Garcia
* 26/Ene/2009 Notas de credito
****************************************************************************************************************************/
			$SQL = "SELECT SUM(ovamount+ovgst+ovfreight)*-1 AS notas FROM debtortrans WHERE debtorno= '".$res['debtorno']."' AND trandate<='".$_POST['ToDate']."' AND trandate>='".$_POST['FromDate']."' AND type = 11";
			$notasres = DB_query($SQL,$db,"Imposible obtener pagado");
			$Nota = DB_fetch_array($notasres);
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
/****************************************************************************************************************************
* Jorge Garcia
* 26/Ene/2009 Notas de credito
****************************************************************************************************************************/
			printf("<TD><A HREF='CustomerInquiry.php?CustomerID=%s&FromDate=%s'>%s</A></TD>
					<TD ALIGN=RIGHT><A HREF='rh_ClientInquiry_dates.php?Factur=%s&From=%s&To=%s'>%s</A></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['debtorno'],
					$format_date,
					$res['debtorno'],
					$res['debtorno'],
					$_POST['FromDate'],
					$_POST['ToDate'],
					number_format($Fact['facturado']+$Rem['remisionado'],2),
					number_format($Nota['notas'],2),
					number_format(-1*$Pagado['pagado'],2),
					number_format((($Fact['facturado']+$Rem['remisionado'])-($Nota['notas'])-($Pagado['pagado'])),2));
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
			
		}
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['ShowSupp']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";
echo "<TD>"._('Cliente').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromSupp' VALUE='1'></TD></TR>";
echo "<TR><TD>"._('Cliente').' '._('To').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToSupp' VALUE='zzzz'></TD></TR>";
echo "<TR><TD>"._('Date').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Date').' '._('To').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='ShowSupp' VALUE='"._('Ver Reporte')."'>";
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
