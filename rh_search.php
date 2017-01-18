<?php

/**
 * REALHOST 2008
 * $LastChangedDate$
 * $Rev$
 */

$PageSecurity = 2;
include('includes/session.inc');

$title = _('Search Now');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

// bowikaxu - no se envio busqueda o la busqueda es muy corta
 if(!isset($_POST['txtsearch']) OR strlen($_POST['txtsearch'])<=3){
 	
	prnMsg('No se envio ninguna busqueda o la busqueda es demasiado corta, minimo 4 letras.','warn');
	include('includes/footer.inc');
	exit;
	
 }else { // si hay busqueda
 	
	$search_term = DB_escape_string(str_replace("-",'_',$_POST['txtsearch']));
	$found = 0;
	
	$afil=unserialize(GetConfig('Afiliaciones'));
	$p=$afil['Prefijo'];
	
	// Search in CustBranch
	$sql = "SELECT custbranch.branchcode,
					custbranch.debtorno,
					custbranch.brname,
					custbranch.braddress1,
					custbranch.braddress2,
					custbranch.braddress3,
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					rh_titular.folio
			FROM rh_titular 
			left join custbranch on 
				custbranch.debtorno=rh_titular.debtorno and custbranch.branchcode like '{$p}%'  
			WHERE rh_titular.folio LIKE '".$search_term."'";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Customer Code')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Branch Code')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Folio')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Branch Name')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 1')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 2')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 3')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 4')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 5')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 6')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sucursales cliente
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Customer').' '._('Branch')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
		
			$link = "<a href='".$rootpath.'/CustomerInquiry.php?' . SID . "CustomerID=".$info['debtorno']."'"."><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>";
		
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>
					<a target="_blank" class="badge badge-success" href="modulos/index.php?r=afiliaciones/afiliacion&amp;Folio='.$info['folio'].'">'.$info['folio'].'</a>
					</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					$link,
					$info['debtorno'],
					$info['branchcode'],
					$info['brname'],
					$info['braddress1'],
					$info['braddress2'],
					$info['braddress3'],
					$info['braddress4'],
					$info['braddress5'],
					$info['braddress6']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN CUSTBRANCH
	
	// Search in CustBranch
	$sql = "SELECT custbranch.branchcode,
					custbranch.debtorno,
					custbranch.brname,
					custbranch.braddress1,
					custbranch.braddress2,
					custbranch.braddress3,
					custbranch.braddress4,
					custbranch.braddress5,
					custbranch.braddress6,
					rh_titular.folio
			FROM custbranch
			left join rh_titular on 
				custbranch.debtorno=rh_titular.debtorno
			WHERE custbranch.branchcode LIKE '%".$search_term."%'
			OR custbranch.debtorno LIKE '%".$search_term."%'
			OR custbranch.brname LIKE '%".$search_term."%'
			OR custbranch.braddress1 LIKE '%".$search_term."%'
			OR custbranch.braddress2 LIKE '%".$search_term."%'
			OR custbranch.braddress3 LIKE '%".$search_term."%'
			OR custbranch.braddress4 LIKE '%".$search_term."%'
			OR custbranch.braddress5 LIKE '%".$search_term."%'
			OR custbranch.braddress6 LIKE '%".$search_term."%'
			GROUP BY custbranch.branchcode
			ORDER BY custbranch.brname, custbranch.debtorno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Customer Code')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Branch Code')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Folio')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Branch Name')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 1')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 2')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 3')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 4')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 5')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 6')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sucursales cliente
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Customer').' '._('Branch')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
		
			$link = "<a href='".$rootpath.'/CustomerInquiry.php?' . SID . "CustomerID=".$info['debtorno']."'"."><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>";
		
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>
						<a target="_blank" class="badge badge-success" href="modulos/index.php?r=afiliaciones/afiliacion&amp;Folio='.$info['folio'].'">'.$info['folio'].'</a>
					</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					$link,
					$info['debtorno'],
					$info['branchcode'],
					$info['brname'],
					$info['braddress1'],
					$info['braddress2'],
					$info['braddress3'],
					$info['braddress4'],
					$info['braddress5'],
					$info['braddress6']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN CUSTBRANCH
	
	// Search in SUPPLIERS
	$sql = "SELECT suppliers.supplierid,
					suppliers.suppname,
					suppliers.suppliersince,
					suppliers.address1,
					suppliers.address2,
					suppliers.address3,
					suppliers.address4,
					suppliers.address5,
					suppliers.address6
			FROM suppliers
			WHERE supplierid LIKE '%".$search_term."%'
			OR suppliers.suppname LIKE '%".$search_term."%'
			OR suppliers.suppliersince LIKE '%".$search_term."%'
			OR suppliers.address1 LIKE '%".$search_term."%'
			OR suppliers.address2 LIKE '%".$search_term."%'
			OR suppliers.address3 LIKE '%".$search_term."%'
			OR suppliers.address4 LIKE '%".$search_term."%'
			OR suppliers.address5 LIKE '%".$search_term."%'
			OR suppliers.address6 LIKE '%".$search_term."%'
			GROUP BY suppliers.supplierid
			ORDER BY suppliers.suppname, suppliers.supplierid";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier Code')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier Since')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 1')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 2')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 3')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 4')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 5')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Address 6')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sucursales cliente
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Supplier')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
		
			$link = "<a href='".$rootpath.'/SupplierInquiry.php?' . SID . "SupplierID=".$info['supplierid']."'"."><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>";
		
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					$link,
					$info['supplierid'],
					$info['suppname'],
					$info['suppliersince'],
					$info['address1'],
					$info['address2'],
					$info['address3'],
					$info['address4'],
					$info['address5'],
					$info['address6']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN SUPPLIERS
	
	// Search in Sales Orders
	$sql = "SELECT salesorders.orderno,
					salesorders.debtorno,
					salesorders.branchcode,
					salesorders.comments,
					salesorders.orddate,
					salesorders.fromstkloc,
					salesorders.deladd1,
					salesorders.deladd2,
					salesorders.deladd3,
					salesorders.deladd4,
					salesorders.deladd5,
					salesorders.deladd6,
					salesorderdetails.narrative
			FROM salesorders
				INNER JOIN salesorderdetails ON salesorderdetails.orderno = salesorders.orderno
			WHERE salesorders.orderno LIKE '%".$search_term."%'
			OR debtorno LIKE '%".$search_term."%'
			OR branchcode LIKE '%".$search_term."%'
			OR comments LIKE '%".$search_term."%'
			OR fromstkloc LIKE '%".$search_term."%'
			OR deladd1 LIKE '%".$search_term."%'
			OR deladd2 LIKE '%".$search_term."%'
			OR deladd3 LIKE '%".$search_term."%'
			OR deladd4 LIKE '%".$search_term."%'
			OR deladd5 LIKE '%".$search_term."%'
			OR deladd6 LIKE '%".$search_term."%'
			OR salesorderdetails.narrative LIKE '%".$search_term."%'
			AND rh_status = 0
			GROUP BY salesorders.orderno
			ORDER BY salesorders.orderno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Order')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Customer/Branch')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Date')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Location')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Comments')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Narrative')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center' COLSPAN=6>"._('Address')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Sales Orders')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					"<a href='OrderDetails.php?&OrderNumber=".$info['orderno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>",
					$info['orderno'],
					$info['debtorno'].'/'.$info['branchcode'],
					ConvertSQLDate($info['orddate']),
					$info['fromstkloc'],
					$info['comments'],
					$info['narrative'],
					$info['deladd1'],
					$info['deladd2'],
					$info['deladd3'],
					$info['deladd4'],
					$info['deladd5'],
					$info['deladd6']);
			
		}
		
		echo "</TABLE>";
		echo "<HR>";
	}
	unset($info);
	// FIN SALES ORDERS
	
	// Search in DebtorTrans
	$sql = "SELECT debtortrans.transno,
					debtortrans.type,
					debtortrans.debtorno,
					debtortrans.branchcode,
					debtortrans.trandate,
					(debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight-debtortrans.ovdiscount) as total,
					debtortrans.alloc,
					debtortrans.invtext,
					debtortrans.order_,
					systypes.typename,
					debtorsmaster.name
					,rh_cfd__cfd.serie
					,rh_cfd__cfd.folio
					,debtortrans.rh_status
			FROM debtortrans
				INNER JOIN systypes ON systypes.typeid = debtortrans.type
				INNER JOIN debtorsmaster ON debtorsmaster.debtorno = debtortrans.debtorno
				LEFT JOIN rh_cfd__cfd ON rh_cfd__cfd.id_debtortrans = debtortrans.id
			WHERE transno LIKE '%".$search_term."%'
			OR debtortrans.type LIKE '%".$search_term."%'
			OR debtorsmaster.name LIKE '%".$search_term."%'
			OR debtortrans.debtorno LIKE '%".$search_term."%'
			OR debtortrans.branchcode LIKE '%".$search_term."%'
			OR debtortrans.trandate LIKE '%".$search_term."%'
			OR debtortrans.alloc LIKE '%".$search_term."%'
			OR debtortrans.invtext LIKE '%".$search_term."%'
			OR debtortrans.order_ LIKE '%".$search_term."%'
			OR rh_cfd__cfd.folio LIKE '%".$search_term."%'
			OR concat(rh_cfd__cfd.serie,' ',rh_cfd__cfd.folio) LIKE '%".$search_term."%'
			GROUP BY debtortrans.id
			ORDER BY debtortrans.type, debtortrans.transno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Tipo').' '._('Trans.')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Customer/Branch')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Date')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Total')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Allocated')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Text')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Order')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Trans.').' '._('Customer')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
		
		if($info['type']==10){
			/*$sql = "SELECT rh_invoicesreference.extinvoice, locations.rh_serie FROM rh_invoicesreference, locations
		    		WHERE rh_invoicesreference.intinvoice = ".$info['transno']."
		    		AND locations.loccode = rh_invoicesreference.loccode";
					$res2 = DB_query($sql,$db);
					$ExtInvoice = DB_fetch_array($res2);
			$invoice = $info['typename'].' '.$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].' ('.$info['transno'].')';/**/
			$invoice = $info['typename'].' '.$info['serie'].'-'.$info['folio'].' ('.$info['transno'].')';
			$link = "<a href='".$rootpath.'/rh_PrintCustTrans.php?' . SID . 'FromTransNo='.$info['transno']."&InvOrCredit=Invoice'"."><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>";
			$link .= "<a href='".$rootpath.'/rh_invoicenew.php?isTransportista=0&transno='.$info['transno']."&InvOrCredit=Invoice".
					($info['rh_status']!='N'?"&isCfdCancelado=true":"").
			"'"."><img src=\"{$rootpath}/css/{$theme}/images/pdf.gif\" title=\"" . _('Click to preview') . "\"></a>";
			if($info['rh_status']!='N')
				$invoice ='Cancelada '.$invoice ;
		}else {
			$invoice = $info['typename'].' #'.$info['transno'];
			$link = "";
		}
		
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					$link,
					$invoice,
					$info['name'].' / '.$info['branchcode'],
					ConvertSQLDate($info['trandate']),
					number_format($info['total'],2),
					number_format($info['alloc'],2),
					$info['invtext'],
					$info['order_']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN DEBTORTRANS
	
	// Search in Purchase Orders
	$sql = "SELECT orderno,
					supplierno,
					comments,
					orddate,
					intostocklocation,
					deladd1,
					deladd2,
					deladd3,
					deladd4,
					deladd5,
					deladd6,
					contact
			FROM purchorders
			WHERE orderno LIKE '%".$search_term."%'
			OR supplierno LIKE '%".$search_term."%'
			OR comments LIKE '%".$search_term."%'
			OR orddate LIKE '%".$search_term."%'
			OR intostocklocation LIKE '%".$search_term."%'
			OR deladd1 LIKE '%".$search_term."%'
			OR deladd2 LIKE '%".$search_term."%'
			OR deladd3 LIKE '%".$search_term."%'
			OR deladd4 LIKE '%".$search_term."%'
			OR deladd5 LIKE '%".$search_term."%'
			OR deladd6 LIKE '%".$search_term."%'
			GROUP BY orderno
			ORDER BY orderno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Order')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Date')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Location')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center' COLSPAN=6>"._('Address')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Comments')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Purchase Orders')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					"<a href='PO_OrderDetails.php?OrderNo=".$info['orderno']."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>",
					$info['orderno'],
					$info['supplierno'],
					ConvertSQLDate($info['orddate']),
					$info['intostocklocation'],
					$info['deladd1'],
					$info['deladd2'],
					$info['deladd3'],
					$info['deladd4'],
					$info['deladd5'],
					$info['deladd6'],
					$info['comments']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN PURCHASE ORDERS
	
	// Search in SuppTrans
	$sql = "SELECT supptrans.transno,
					supptrans.type,
					supptrans.supplierno,
					supptrans.suppreference,
					supptrans.trandate,
					(supptrans.ovamount+supptrans.ovgst) as total,
					supptrans.alloc,
					supptrans.transtext,
					systypes.typename,
					suppliers.suppname
			FROM supptrans
				INNER JOIN systypes ON systypes.typeid = supptrans.type
				INNER JOIN suppliers ON supplierid = supptrans.supplierno
			WHERE transno LIKE '%".$search_term."%'
			OR supptrans.type LIKE '%".$search_term."%'
			OR suppliers.suppname LIKE '%".$search_term."%'
			OR supptrans.supplierno LIKE '%".$search_term."%'
			OR supptrans.suppreference LIKE '%".$search_term."%'
			OR supptrans.trandate LIKE '%".$search_term."%'
			OR supptrans.alloc LIKE '%".$search_term."%'
			OR supptrans.transtext LIKE '%".$search_term."%'
			GROUP BY supptrans.id
			ORDER BY supptrans.type, supptrans.transno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD></TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Tipo').' '._('Trans.')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Supplier').' '._('Reference')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Date')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Total')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Allocated')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Text')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Trans.').' '._('Supplier')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
		
		if($info['type']==20){
			$link = "rh_SuppInvoice_Details.php?&Transno=".$info['transno']."";
		}else {
			$link = "";
		}
		
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					"<a href='".$link."'><IMG BORDER=0 SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>",
					$info['typename'].' #'.$info['transno'],
					$info['suppname'],
					$info['suppreference'],
					ConvertSQLDate($info['trandate']),
					number_format($info['total'],2),
					number_format($info['alloc'],2),
					$info['transtext']);
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN SUPPTRANS
	
	// Search in Stockmaster
	$sql = "SELECT stockmaster.stockid,
					stockmaster.categoryid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.actualcost,
					(stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost) AS sumcost,
					stockmaster.barcode
			FROM stockmaster
			WHERE stockid LIKE '%".$search_term."%'
			OR categoryid LIKE '%".$search_term."%'
			OR description LIKE '%".$search_term."%'
			OR longdescription LIKE '%".$search_term."%'
			OR (stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost) LIKE '%".$search_term."%'
			OR barcode LIKE '%".$search_term."%'
			GROUP BY stockid
			ORDER BY stockid";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Item')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Category')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Description')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Long Description')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Cost')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Barcode')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Item')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD>%s</TD>
					</TR>',
					$info['stockid'],
					$info['categoryid'],
					$info['description'],
					$info['longdescription'],
					number_format($info['sumcost'],2),
					$info['barcode']);
			
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN STOCKMASTER
	
	// Search in BankTrans
	$sql = "SELECT banktrans.type,
					banktrans.transno,
					banktrans.bankact,
					banktrans.amount,
					banktrans.amountcleared,
					banktrans.transdate,
					banktrans.currcode,
					banktrans.ref,
					banktrans.rh_chequeno,
					chartmaster.accountname,
					systypes.typename
			FROM banktrans
				INNER JOIN chartmaster ON chartmaster.accountcode = banktrans.bankact
				INNER JOIN systypes ON systypes.typeid = banktrans.type
			WHERE transno LIKE '%".$search_term."%'
			OR  bankact LIKE '%".$search_term."%'
			OR accountname LIKE '%".$search_term."%'
			OR transdate LIKE '%".$search_term."%'
			OR currcode LIKE '%".$search_term."%'
			OR rh_chequeno LIKE '%".$search_term."%'
			OR ref LIKE '%".$search_term."%'
			GROUP BY banktransid
			ORDER BY type,transno";
	$res = DB_query($sql,$db);
	$tableheader = "<TR>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Tipo').' '._('Trans.')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Account')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Date')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Amount')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Amount Cleared')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Currency')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Cheque')."</TD>
						<TD CLASS='quick_menu_tab' ALIGN='center'>"._('Reference')."</TD>
					</TR>";
	if(DB_num_rows($res)>0){ // si hay resultados en sales orders
		$found++;
		echo "<TABLE ALIGN='center' WIDTH=80%>";
		echo "<TR><TD COLSPAN=5><B>"._('Bank')." (".DB_num_rows($res).")</B></TD></TR>";
		echo $tableheader;
		
		$k=0;
		while($info = DB_fetch_array($res)){
			if ($k == 1){
			echo '<TR BGCOLOR="#CCCCCC">';
			$k = 0;
		} else {
			echo '<TR BGCOLOR="#EEEEEE">';
			$k++;
		}
			printf('<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD ALIGN="right">%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					<TD>%s</TD>
					</TR>',
					$info['typename'].' #'.$info['transno'],
					$info['accountname'].' [ '.$info['bankact'].' ]',
					ConvertSQLDate($info['transdate']),
					number_format($info['amount'],2),
					number_format($info['amountcleared'],2),
					$info['currcode'],
					$info['rh_chequeno'],
					$info['ref']);
			//"<a href=''><IMG SRC='".$rootpath.'/css/'.$theme.'/images/preview.gif'."' TITLE='" . _('Click to preview') . "'></a>"
		}
		
		echo "</TABLE>";
		echo "<HR>";	
	}
	unset($info);
	// FIN BANKTRANS
	if($found==0){
		prnMsg('No se han encontrado resultados con esta busqueda, favor de intentar de nuevo.','info');
	}
	
 }
 
 include('includes/footer.inc');
 ?>