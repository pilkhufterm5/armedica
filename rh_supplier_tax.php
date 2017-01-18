<?php
/* $Revision: 214 $ */
$PageSecurity = 3;

include('includes/session.inc');

$title = _('Reporte Impuestos Proveedores');
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";
include('includes/header.inc');

if(!isset($_POST['FromDate'])){
	$_POST['FromDate'] = date('Y-m-d');
}

if(!isset($_POST['ToDate'])){
	$_POST['ToDate'] = date('Y-m-d');
}

	echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo "<CENTER><TABLE>";
	
	//echo "</TD></TR>";
	echo "<TR><TD>"._('Fecha').' '._('De').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE='".$_POST['FromDate']."'>
	 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy-M-d');
	                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
	                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
	echo "</TD></TR>";
	echo "<TR><TD>"._('Fecha').' '._('Hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE='".$_POST['ToDate']."'>
	<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy-M-d');
	                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
	                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
	</TD></TR>";
	echo "</TABLE>";
	echo "<INPUT TYPE=submit NAME='view' VALUE='"._('Ver Reporte')."'>";
	echo "</CENTER></FORM>";

	if(!isset($_POST['FromDate']) OR strlen($_POST['FromDate'])<8 
		OR !isset($_POST['ToDate']) OR strlen($_POST['ToDate'])<8){
		prnMsg('Verifique las fechas','warn');
		include('includes/footer.inc');
		exit;
	}

	$tableheader = "<TR>
					<TD class='tableheader'>"._('R.F.C.')."</TD>
					<TD class='tableheader'>"._('Supplier Name')."</TD>
					<TD class='tableheader'>"._('Reference')."</TD>
					<TD class='tableheader'>"._('Invoice').' '._('Total')."</TD>
					<TD class='tableheader'>"._('Invoice').' '._('Tax')."</TD>
					<TD class='tableheader'>"._('Allocated')."</TD>
					<TD class='tableheader'>"._('Payment').' '._('Tax')."</TD>
					</TR>";
	
	echo "<TABLE BORDER=0 COLSPAN=2 ALIGN=CENTER>";
	echo $tableheader;
	$sql = "select supptrans.suppreference, supptrans.supplierno, suppliers.rh_taxref,
			ABS(supptrans.alloc) as alloc, ABS(supptrans.ovamount) as ovamount, ABS(supptrans.ovgst) as ovgst, suppliers.suppname
			FROM supptrans 
			 INNER JOIN suppliers ON supptrans.supplierno = suppliers.supplierid
			 where type = 20 and trandate>='".$_POST['FromDate']."' and trandate <='".$_POST['ToDate']."'
			 AND ABS(alloc)>0
			 ORDER BY supptrans.supplierno, supptrans.trandate";
	$res = DB_query($sql,$db);
	$last_supplier = '';
	while($info = DB_fetch_array($res)){
	
		if($last_supplier!=$info['supplierno'] && $last_supplier!=''){
			// show supplier totals
			echo "<TR>
					<TD class='tableheader'>".''."</TD>
					<TD class='tableheader'>".''."</TD>
					<TD class='tableheader'>".''."</TD>
					<TD align='right' class='tableheader'><B>".number_format($inv_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($tax_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($alloc_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($taxpaid_total,2)."</B></TD>
				</TR>";
		
			$inv_total = 0;
			$tax_total = 0;
			$alloc_total =0;
			$taxpaid_total = 0;
		}
	
		$tax_percent = ($info['alloc'])/($info['ovamount']+$info['ovgst']);
		$tax_paid_total = $info['ovgst']*$tax_percent;
		
		echo "<TR>
				<TD>".$info['rh_taxref']."</TD>
				<TD>".$info['suppname']."</TD>
				<TD>".$info['suppreference']."</TD>
				<TD align='right'>".number_format($info['ovamount']+$info['ovgst'],2)."</TD>
				<TD align='right'>".number_format($info['ovgst'],2)."</TD>
				<TD align='right'>".number_format($info['alloc'],2)."</TD>
				<TD align='right'>".number_format($tax_paid_total,2)."</TD>
			</TR>";
		$inv_total += ($info['ovamount']+$info['ovgst']);
		$tax_total += ($info['ovgst']);
		$alloc_total += ($info['alloc']);
		$taxpaid_total += $tax_paid_total;
		$last_supplier=$info['supplierno'];
	
	}
	// show last totals
	echo "<TR>
					<TD class='tableheader'>".''."</TD>
					<TD class='tableheader'>".''."</TD>
					<TD class='tableheader'>".''."</TD>
					<TD align='right' class='tableheader'><B>".number_format($inv_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($tax_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($alloc_total,2)."</B></TD>
					<TD align='right' class='tableheader'><B>".number_format($taxpaid_total,2)."</B></TD>
				</TR>";
	echo "</TABLE>";

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