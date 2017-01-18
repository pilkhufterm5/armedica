<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-07-16 09:21:05 -0500 (Wed, 16 Jul 2008) $
 * $Rev: 332 $
 */
/*
bowikaxu - realhost
Reporte de Remisiones Facturadas y no Facturadas

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('Reporte de Remisiones');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Reporte de Remisiones')."</B></CENTER><BR>";

// 2007/02/08 bowikaxu mostrar balance de saldo
if(isset($_POST['ShowRem'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' OR $_POST['FromDebtor']=='' OR $_POST['ToDebtor']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Some fields are invalid</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER>";
		if ($_POST['type']==20001)
        {
                //echo "<OPTION SELECTED Value='20001'>Notas de Cargo";
                //echo "<OPTION Value='20001'>Remisiones";
                echo "<h3>Notas de Cargo</h3>";                
        } else {
            //echo "<OPTION Value='20001'>Notas de Cargo";
                //echo "<OPTION SELECTED Value='20001'>Remisiones";
                echo "<h3>Remisiones</h3>";                
        }
        $type=$_POST['type'];
        echo "</CENTER>";
		
		echo "<CENTER> Desde: ".$_POST['FromDate']." - A: ".$_POST['ToDate']."</CENTER><BR>";
		echo "<CENTER> Desde: ".$_POST['FromDebtor']." - A: ".$_POST['ToDebtor']."</CENTER><BR>";
		
		$SQL = "SELECT debtortrans.*,date(debtortrans.trandate)as fecha, rh_invoiceshipment.type AS RTipo, rh_invoiceshipment.Facturado, rh_invoiceshipment.Invoice FROM debtortrans, rh_invoiceshipment
				WHERE debtortrans.trandate<='".FormatDateForSQL($_POST['ToDate'])." 23:59:59'
				AND debtortrans.trandate>='".FormatDateForSQL($_POST['FromDate'])." 00:00:00'
				AND debtortrans.debtorno >='".$_POST['FromDebtor']."' 
				AND debtortrans.debtorno <='".$_POST['ToDebtor']."'
				AND debtortrans.type=".$type."
				AND debtortrans.transno = rh_invoiceshipment.Shipment
				ORDER BY debtortrans.transno ASC";
		//echo $SQL;
		$result = DB_query($SQL,$db,"Imposible obtener remisiones");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Remision') . 
			"</TD><TD CLASS='tableheader'>" . _('Cliente') .
			"</TD><TD CLASS='tableheader'>" . _('Fecha Rem') .
            "</TD><TD CLASS='tableheader'>" . _('Fecha Creacion') .
            "</TD><TD CLASS='tableheader'>" . _('Sub-Total') .
            "</TD><TD CLASS='tableheader'>" . _('IVA') .
			"</TD><TD CLASS='tableheader'>" . _('Total') .
			"</TD><TD CLASS='tableheader'>" . _('Facturado') .
			"</TD><TD CLASS='tableheader'>" . _('# Factura') .
			"</TD><TD CLASS='tableheader'>" . _('Tipo') . 
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		
		$fdate = explode("/",$_POST['FromDate']);
		$format_date = $fdate[2]."/".$fdate[1]."/".$fdate[0];
		
		//rleal Jul 19 2011 se agrega estas variables para la sumatoria
		$rh_ovamount=0;
		$rh_ovgst=0;
		while($res = DB_fetch_array($result)){
			$rh_ovamount+=$res['ovamount'];
			$rh_ovgst+=$res['ovgst'];
			
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			if($res['Facturado']==1){
				$Factur = "Si";
			}else {
				$Factur = "No"; 
			}
			
			if($res['RTipo']==0){
				$RTipo = "Normal";
			}else if($res['RTipo']==1){
				$RTipo = "Muestra";
			}else if($res['RTipo']==2){
				$RTipo = "Punto de Venta";
			}
			//rh_PDFRemGde.php?&FromTransNo=150&InvOrCredit=Invoice
			//?FromTransNo=' . $inv . '&InvOrCredit=Invoice&PrintPDF=Yes" target=_blank>;
			printf("<TD><A HREF='rh_PDFRemGde.php?%s&FromTransNo=%s&InvOrCredit=Invoice'>%s</A></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
                    <TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT><A HREF='rh_PrintCustTrans.php?%sFromTransNo=%s&InvOrCredit=Invoice'>%s</A></TD>
					<TD ALIGN=RIGHT>%s</TD>",
					SID,
					$res['transno'],
					$res['transno'],
					$res['debtorno'],
					$res['fecha'],
                    $res['rh_createdate'],
                    number_format(($res['ovamount']),2),
                    number_format(($res['ovgst']),2),
					number_format(($res['ovamount']+$res['ovfreight']+$res['ovgst']),2),
					$Factur,
					SID,
					$res['Invoice'],
					$res['Invoice'],
					$RTipo);
			
		}
		
		//rleal Jul 19 2011 se agregan las sumatorias
		$TableFooter = "<TR><TD CLASS='tableheader'>"  . 
			"</TD><TD CLASS='tableheader'>" . 
			"</TD><TD CLASS='tableheader'>" . 
            "</TD><TD CLASS='tableheader'>TOTALES"  .
            "</TD><TD CLASS='tableheader'>" . number_format($rh_ovamount,2) .
            "</TD><TD CLASS='tableheader'>" . number_format($rh_ovgst,2) .
			"</TD><TD CLASS='tableheader'>"  .number_format($rh_ovamount+$rh_ovgst,2).
			"</TD><TD CLASS='tableheader'>"  .
			"</TD><TD CLASS='tableheader'>"  .
			"</TD><TD CLASS='tableheader'>" . 
			"</TD></TR>";
		
		echo $TableFooter;
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['ShowRem']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE>";

echo "<tr><SELECT name='type'> ";
        
		echo "<OPTION SELECTED Value='20000'>Remisiones";
        //echo "<OPTION Value='20001'>Notas de Cargo";                
        
echo "<tr>";

echo "<TR><TD>"._('Cliente').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDebtor' VALUE='1'></TD></TR>";
echo "<TR><TD>"._('Cliente').' '._('To').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDebtor' VALUE='zzzz'></TD></TR>";

//echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('De').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('Hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='ShowRem' VALUE='"._('Ver Reporte')."'>";
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
