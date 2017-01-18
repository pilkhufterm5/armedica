<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
 bowikaxu - realhost
Reporte de Comisiones por Vendedor

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=8;

include('includes/session.inc');

$title = _('Salesman Inquiry Dates');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy-M-d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Salesman Inquiry Dates')."</B></CENTER><BR>";

/*
 * rleal
* Mar 1 2011
* Se corrige formato de fechas
*/
if (strlen($_GET['FromDate']) > 3) {
	echo "<hr>";
	$rh_FromDate=ConvertSQLDate($_GET['FromDate'])." 00:00:00";
	$rh_ToDate=ConvertSQLDate($_GET['ToDate'])." 23:59:59";
} else {
	$rh_FromDate=ConvertSQLDate($_POST['FromDate'])." 00:00:00";
	$rh_ToDate=ConvertSQLDate($_POST['ToDate'])." 23:59:59";

}
/*
 * Fin Modificacion
*/

// bowikaxu - ver la lista de detalle de algun vendedor

if(isset($_GET['sman'])){

	if($_GET['type']==10){
		$type = 10;
		$SQL = "SELECT debtorsmaster.name,
                SUM(debtortrans.ovamount+debtortrans.ovfreight) AS subtotal,
                SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS balance,
			debtortrans.debtorno,
			custbranch.branchcode
			FROM debtortrans, custbranch, debtorsmaster
			WHERE custbranch.debtorno = debtortrans.debtorno
                        AND debtorsmaster.debtorno = debtortrans.debtorno
			AND custbranch.branchcode = debtortrans.branchcode
			AND custbranch.salesman = '".$_GET['sman']."'
			AND debtortrans.trandate >= '".$rh_FromDate."'
			AND debtortrans.trandate <= '".$rh_ToDate."'
			AND debtortrans.type = '".$type."'
			AND debtortrans.rh_status != 'C'
			GROUP BY debtortrans.debtorno";

		echo "<CENTER>"._('Type').': '._('Invoice')."</CENTER>";
	}else {
		$type = 12;
		$SQL = "select
                         			SUM(custallocns.amt) as T1,
                         			SUM(custallocns.amt) AS T2,
                         			SUM(custallocns.amt) AS saldo,
                         			SUM(custallocns.amt) AS subtotal,
                         			salesman.commissionrate1 AS C1,
                         			salesman.commissionrate2 AS C2,
                         			custbranch.salesman,
                         			salesman.breakpoint,
                         			salesman.salesmancode,
                         			salesman.salesmanname
                          		from 
                          			custallocns left join debtortrans deb1 on 
                        			custallocns.transid_allocfrom = deb1.id 
                        			left join debtortrans on 
                        			custallocns.transid_allocto =debtortrans.id 
                        			left join custbranch on debtortrans.debtorno = custbranch.debtorno
                        			left join salesman on salesman.salesmancode=custbranch.salesman
                        		where custallocns.datealloc between '{$rh_FromDate}' and '{$rh_ToDate}'  				
                          				AND deb1.type = '{$type}'
                          				AND debtortrans.rh_status != 'C'
                          				AND custbranch.salesman = '".$_GET['sman']."'
                          			GROUP BY debtortrans.debtorno";
		echo "<CENTER>"._('Type').': '._('Payments')."</CENTER>";
	}
	echo "<CENTER> Desde: ".$_GET['FromDate']." - A: ".$_GET['ToDate']."</CENTER><BR>";
	echo "<CENTER>"._('Salesman').': '.$_GET['sman']."</CENTER>";



	// agrupar por clientes debtorno, donde al hacer click a un debtorno mande a customer transaction inquiries
	// COLUMNAS: debtorno, saldo
	$DetailResult = DB_query($SQL,$db);
	echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
	$TableHeader = "<TR><TD CLASS='tableheader'>" ._('Debtor'). "</TD>".
                    "<TD CLASS='tableheader'>" . _('Name') . "</TD>".
                    "<TD CLASS='tableheader'>" . _('Subtotal') . "</TD>".
			"<TD CLASS='tableheader'>" . _('Total Balance') . "</TD>".
			"<TD CLASS='tableheader'>" . _('Comision 1') . "</TD>".
			"<TD CLASS='tableheader'>" . _('Comision 2') . "</TD></TR>";

	echo $TableHeader;

	$sql = "SELECT commissionrate1 as C1, commissionrate2 as C2 FROM salesman WHERE salesmancode ='".$_GET['sman']."'";
	$res2 = DB_query($sql,$db);
	$com = DB_fetch_array($res2);

	$fdate = explode("/",$_GET['FromDate']);
	$format_date = $fdate[0]."/".$fdate['1']."/".$fdate[2];
	$TotalBalance = 0;
	$TotC1 = 0;
	$TotC2 = 0;

	$j = 1;
	$k = 0; //row colour counter

	while($res = DB_fetch_array($DetailResult)){

		if ($k == 1){
			echo "<TR BGCOLOR='#CCCCCC'>";
			$k = 0;
		} else {
			echo "<TR BGCOLOR='#EEEEEE'>";
			$k = 1;
		}

		if($_GET['type']==12){
			$DisplaySaldo = number_format(-1*$res['balance'],2);

			$DisplayC1 = number_format(-1*$res['balance']*$com['C1'],2);
			$DisplayC2 = number_format(-1*$res['balance']*$com['C2'],2);
			$TotC1 += (-1*$res['balance']*$com['C1']);
			$TotC2 += (-1*$res['balance']*$com['C2']);
			$TotSubtotal += (-1*$res['subtotal']);

			$TotalBalance += (-1*$res['balance']);
		}else {
			$DisplaySaldo = number_format($res['balance'],2);
			$DisplaySubtotal = number_format($res['subtotal'],2);

			$DisplayC1 = number_format($res['balance']*$com['C1'],2);
			$DisplayC2 = number_format($res['balance']*$com['C2'],2);

			$TotSubtotal += ($res['subtotal']);
			$TotalBalance += ($res['balance']);
			$TotC1 += ($res['balance']*$com['C1']);
			$TotC2 += ($res['balance']*$com['C2']);
		}

		printf("<TD ALIGN=LEFT>%s</TD>
                            <TD ALIGN=LEFT>%s</TD>
                            <TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT><A HREF='".$rootpath."/CustomerInquiry.php?".SID."&CustomerID=%s&FromDate=%s&type=%s'>%s</A></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
		$res['debtorno'],
		$res['name'],
		$DisplaySubtotal,
		$res['debtorno'],
		$format_date,
		$_GET['type'],
		$DisplaySaldo,
		$DisplayC1,
		$DisplayC2);

	}

	echo "</TR><TR><TD></TD><TD>"._('Total Balance')."</TD><TD ALIGN=RIGHT>".number_format($TotSubtotal,2)."</TD><TD ALIGN=RIGHT>".number_format($TotalBalance,2)."</TD>
		<TD ALIGN=RIGHT>".number_format($TotC1,2)."</TD><TD ALIGN=RIGHT>".number_format($TotC2,2)."</TD></TR>";

	echo "</TABLE>";

	echo "<BR><BR>\n";
	echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
	echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
	echo "</FORM>";

	include('includes/footer.inc');
	exit;

}

// fin vista detalle algun vendedor

// 2007/02/08 bowikaxu mostrar comisiones de los determinados vendedores
if(isset($_POST['ShowSupp'])){

	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']=='' OR $_POST['SalesPeople']=='' OR $_POST['type']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Some fields are invalid</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;

	}else {

		// bowikaxu obtener el tipo de transacciones (facturas o pagos)
		if($_POST['type']=='Inv'){
			$type = 10;
			echo "<CENTER>"._('Type').': '._('Invoice')."</CENTER>";
		}else {
			$type = 12;
			echo "<CENTER>"._('Type').': '._('Payments')."</CENTER>";
		}
		echo "<CENTER> Desde: ".$_POST['FromDate']." - A: ".$_POST['ToDate']."</CENTER><BR>";



		// bowikaxu - todos los vendedores
		if (in_array('All', $_POST['SalesPeople'])){

			if ($type==10) {
				$SQL = "SELECT SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) as T1,
				SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS T2,
				SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS saldo,
                                SUM(debtortrans.ovamount+debtortrans.ovfreight) AS subtotal,
				salesman.commissionrate1 AS C1,
				salesman.commissionrate2 AS C2,
				custbranch.salesman,
				salesman.breakpoint,
				salesman.salesmancode,
				salesman.salesmanname
			FROM debtortrans, salesman, custbranch
			WHERE debtortrans.debtorno = custbranch.debtorno
			AND custbranch.branchcode = debtortrans.branchcode
			AND custbranch.salesman = salesman.salesmancode
			AND debtortrans.trandate >= '".$rh_FromDate."'
			AND debtortrans.trandate <= '".$rh_ToDate."'
			AND debtortrans.type = ".$type."
			AND debtortrans.rh_status != 'C'
			GROUP BY salesman.salesmancode
			ORDER BY salesman.salesmancode";
			} else {
				$SQL = "select
 			SUM(custallocns.amt) as T1,
 			SUM(custallocns.amt) AS T2,
 			SUM(custallocns.amt) AS saldo,
 			SUM(custallocns.amt) AS subtotal,
 			salesman.commissionrate1 AS C1,
 			salesman.commissionrate2 AS C2,
 			custbranch.salesman,
 			salesman.breakpoint,
 			salesman.salesmancode,
 			salesman.salesmanname
  		from 
  			custallocns left join debtortrans deb1 on 
			custallocns.transid_allocfrom = deb1.id 
			left join debtortrans on 
			custallocns.transid_allocto =debtortrans.id 
			left join custbranch on debtortrans.debtorno = custbranch.debtorno
			left join salesman on salesman.salesmancode=custbranch.salesman
		where custallocns.datealloc between '{$rh_FromDate}' and '{$rh_ToDate}'  				
  				AND deb1.type = '{$type}'
  				AND debtortrans.rh_status != 'C'
  			GROUP BY salesman.salesmancode
  			ORDER BY salesman.salesmancode";
			}


		}else { // bowikaxu - selecciono solo algunos vendedores
			//AND debtortrans.trandate >= ".$_POST['FromDate']."
			//AND debtortrans.trandate <= ".$_POST['ToDate']."

			if ($type==10) {
				$SQL = "SELECT SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS T1,
				SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS T2,
				SUM(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight) AS saldo,
                                SUM(debtortrans.ovamount+debtortrans.ovfreight) AS subtotal,
				salesman.commissionrate1 AS C1,
				salesman.commissionrate2 AS C2,
				custbranch.salesman,
				salesman.breakpoint,
				salesman.salesmancode,
				salesman.salesmanname
			FROM debtortrans, salesman, custbranch
			WHERE debtortrans.debtorno = custbranch.debtorno
			AND custbranch.branchcode = debtortrans.branchcode
			AND salesman.salesmancode = custbranch.salesman
			AND debtortrans.trandate >= '".$rh_FromDate."'
			AND debtortrans.trandate <= '".$rh_ToDate."'
			AND debtortrans.type = ".$type."
			AND debtortrans.rh_status != 'C'";
				$__OR=array();
				foreach ($_POST['SalesPeople'] as $Salesperson)
					$__OR[]="custbranch.salesman='" . $Salesperson ."'";
				$__OR=implode(" OR ",$__OR);
				if(trim($__OR)=="")$__OR='false';
				$SQL .=" AND (".$__OR.") ";
				echo $SQL .= "GROUP BY salesman.salesmancode ORDER BY salesman.salesmancode";
			} else {
				$SQL = "select
				 			SUM(custallocns.amt) as T1,
				 			SUM(custallocns.amt) AS T2,
				 			SUM(custallocns.amt) AS saldo,
				 			SUM(custallocns.amt) AS subtotal,
				 			salesman.commissionrate1 AS C1,
				 			salesman.commissionrate2 AS C2,
				 			custbranch.salesman,
				 			salesman.breakpoint,
				 			salesman.salesmancode,
				 			salesman.salesmanname
				  		from 
				  			custallocns left join debtortrans deb1 on 
							custallocns.transid_allocfrom = deb1.id 
							left join debtortrans on 
							custallocns.transid_allocto =debtortrans.id 
							left join custbranch on debtortrans.debtorno = custbranch.debtorno
							left join salesman on salesman.salesmancode=custbranch.salesman
						where custallocns.datealloc between '{$rh_FromDate}' and '{$rh_ToDate}'  				
				  				AND deb1.type = '{$type}'
				  				AND debtortrans.rh_status != 'C'";

				 
				$__OR=array();
				foreach ($_POST['SalesPeople'] as $Salesperson)
				$__OR[]="custbranch.salesman='" . $Salesperson ."'";
				$__OR=implode(" OR ",$__OR);
				if(trim($__OR)=="")$__OR='false';
				$SQL .=" AND (".$__OR.") ";
				$SQL .= "GROUP BY salesman.salesmancode ORDER BY salesman.salesmancode";
			}

		}
		$CommissionResult = DB_query($SQL,$db,"Imposible obtener comisiones");

		/*show a table of the transactions returned by the SQL */
		
		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" ._('Salesman').' '. _('Number')."</TD>";
		$TableHeader .="<TD CLASS='tableheader'>" . _('Salesman') ."</TD>";
		if($type==10)
			$TableHeader .="<TD CLASS='tableheader'>" . _('Subtotal') ."</TD>";
		$TableHeader .="<TD CLASS='tableheader'>" . _('Total Balance') ."</TD>";
		$TableHeader .="<TD CLASS='tableheader'>" . _('Commission Rate 1') ."</TD>";
		$TableHeader .="<TD CLASS='tableheader'>" . _('Commission Rate 2') ."</TD>";
		$TableHeader .="<TD CLASS='tableheader'>" . _('Breakpoint') ."</TD>";
		$TableHeader .="</TR>";

		echo $TableHeader;
		$TotalBalance = 0;
		$TotalCom1 = 0;
		$TotalCom2 = 0;
		$j = 1;
		//$k = 0; //row colour counter
		$k ="#CCC";
		while($res = DB_fetch_array($CommissionResult)){
			if ($k !="#CCC")
				$k ="#CCC";
			 else 
				$k ="#EEE";
			echo "<TR BGCOLOR='{$k}'>";

			if($type==12){
				$DisplayC1 = number_format(-1*$res['T1']*$res['C1'],2);
				$DisplayC2 = number_format(-1*$res['T2']*$res['C2'],2);
				$DisplaySaldo = number_format(-1*$res['saldo'],2);
				$TotalBalance += (-1*$res['saldo']);
				$Totalsubtotal+= (-1*$res['subtotal']);
				$TotalCom1 += (-1*$res['T1']*$res['C1']);
				$TotalCom2 += (-1*$res['T2']*$res['C2']);
			}else {
				$DisplayC1 = number_format($res['T1']*$res['C1'],2);
				$DisplayC2 = number_format($res['T2']*$res['C2'],2);
				$DisplaySaldo = number_format($res['saldo'],2);
				$TotalBalance += ($res['saldo']);
				$Totalsubtotal+=($res['subtotal']);
				$TotalCom1 += ($res['T1']*$res['C1']);
				$TotalCom2 += ($res['T2']*$res['C2']);
			}

			printf("<TD ALIGN=LEFT>%s</TD>",$res['salesmancode']);
			printf("<TD ALIGN=LEFT>%s</TD>",$res['salesmanname']);
			if($type==10)
				printf("<TD ALIGN=RIGHT>%s</TD>",number_format($res['subtotal'],2));
			printf("<TD ALIGN=RIGHT><A HREF='rh_SalesMan_Inquiry.php?".SID."FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&type=%s&sman=%s'>%s</A></TD>",$type,$res['salesmancode'],$DisplaySaldo);
			printf("<TD ALIGN=RIGHT>%s</TD>",$DisplayC1);
			printf("<TD ALIGN=RIGHT>%s</TD>",$DisplayC2);
			printf("<TD ALIGN=RIGHT>%s</TD>",$res['breakpoint']);
			

		}

		echo "</TR><TR><TD COLSPAN=2>"._('Total Balance')."</TD>";
		if($type==10)
                echo "<TD ALIGN=RIGHT>".number_format($Totalsubtotal,2)."</TD>";
		echo "<TD ALIGN=RIGHT>".number_format($TotalBalance,2)."</TD>
		<TD ALIGN=RIGHT>".number_format($TotalCom1,2)."</TD>
		<TD ALIGN=RIGHT>".number_format($TotalCom2,2)."</TD>
		</TR>";

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

	echo "<TR><TD>"._('Type').': '."</TD><TD>"."<SELECT name='type'>";
	echo "<OPTION SELECTED VALUE='Inv'>"._('Invoice')."</OPTION>";
	echo "<OPTION VALUE='Pay'>"._('Payments')."</OPTION>";
	echo "</SELECTED></TD></TR>";

	echo '<TR><TD>' . _('For Sales folk'). ':</TD><TD><SELECT name=SalesPeople[] multiple>';

	echo '<OPTION SELECTED VALUE="All">'. _('All sales folk');

	$sql = 'SELECT salesmancode, salesmanname FROM salesman';
	$SalesFolkResult = DB_query($sql,$db);

	While ($myrow = DB_fetch_array($SalesFolkResult)){
		echo '<OPTION VALUE="' . $myrow['salesmancode'] . '">' . $myrow['salesmanname'];
	}
	echo '</SELECT></TD></TR>';

	echo "<TR><TD>"._('Date').' '._('From').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy-M-d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
	echo "</TD></TR>";
	echo "<TR><TD>"._('Date').' '._('To').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy-M-d');
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
