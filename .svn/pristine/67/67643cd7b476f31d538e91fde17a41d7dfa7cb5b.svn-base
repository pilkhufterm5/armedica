<?php

	$PageSecurity = 2;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');

//SAINTS
if($_GET['copia']=="si")	
	include('includes/CFDViewer_copia.php');
else
	include('includes/CFDViewer.php');

	$transno = $_GET['transno'];
	if($transno == ''){
		echo "ERROR";
		exit();
	}

	//Se obtiene la información del CFD, se limita a 1 el query para obtener primero las direcciones, nombre cliente, etc.
	$sql = "SELECT l.deladd7 municipio_domicilio_expedido_en, l.deladd8 estado_domicilio_expedido_en,l.deladd1,l.deladd2,l.deladd3,l.deladd4,l.deladd5,l.deladd6,l.deladd7,l.deladd8,l.deladd9,l.deladd10, cp.coyname, cp.telephone, cp.gstno, cp.regoffice1, cp.regoffice2, cp.regoffice3, cp.regoffice4, cp.regoffice5, cp.regoffice6, cp.regoffice7, cp.regoffice8, cp.regoffice9,cp.regoffice10, concat(custbranch.branchcode) sucursal, debtortrans.trandate, SUBSTRING(DATE_FORMAT(debtortrans.trandate, '%M'),1,3) as mm, DATE_FORMAT(debtortrans.trandate, '%d') as dd, DATE_FORMAT(debtortrans.trandate, '%Y') as yy, debtortrans.ovamount, debtortrans.id as ID, debtortrans.ovdiscount, debtortrans.ovfreight, debtortrans.ovgst, debtortrans.rate, debtortrans.invtext, debtortrans.consignment, debtorsmaster.name AS name_old, debtorsmaster.name2 AS name2_old, debtorsmaster.address1 AS address1_old, debtorsmaster.address2 AS address2_old, debtorsmaster.address3 AS address3_old, debtorsmaster.address4 AS address4_old, debtorsmaster.address5 AS address5_old, debtorsmaster.address6 AS address6_old, rh_transaddress.name, rh_transaddress.name2, rh_transaddress.address1, rh_transaddress.address2, rh_transaddress.address3, rh_transaddress.address4, rh_transaddress.address5, rh_transaddress.address6, rh_transaddress.taxref, debtorsmaster.currcode, debtorsmaster.invaddrbranch, debtorsmaster.taxref AS taxref_old, salesorders.deliverto, salesorders.customerref, salesorders.orderno, salesorders.orddate, shippers.shippername, custbranch.brname, custbranch.braddress1, custbranch.braddress2, custbranch.braddress3, custbranch.braddress4, custbranch.braddress5, custbranch.braddress6, salesman.salesmanname, debtortrans.debtorno ,stockmoves.stockid, stockmaster.description as description, -stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty)*debtortrans.rate AS fxnet, (stockmoves.price * 1)*debtortrans.rate AS fxprice, stockmoves.narrative, stockmaster.units, c.folio extinvoice ,rh_transaddress.address1 as direccion1, rh_transaddress.address2 as direccion2, rh_transaddress.address3 as direccion3, rh_transaddress.address4 as direccion4, rh_transaddress.address5 as direccion5, rh_transaddress.address6 as direccion6, rh_transaddress.address7 as direccion7, rh_transaddress.address8 as direccion8, rh_transaddress.address9 as direccion9, rh_transaddress.address10 as direccion10,  concat(stockmaster.description) as descripcion, debtortrans.ovamount+debtortrans.ovgst+ debtortrans.ovfreight- debtortrans.ovdiscount as gtotal, paymentterms.terms, c.no_certificado comprobante_no_certificado, c.ano_aprobacion as yy_aprovacion, c.no_aprobacion as numero_apr, c.total_en_letra extra_importe_con_letra, c.cadena_original comprobante_cadena_original, c.sello comprobante_sello, c.fecha fecha_y_hora_de_expedicion, debtorsmaster.rh_tel, c.serie, c.total_en_letra extra_importe_con_letra_emdc,custbranch.phoneno telefono,stockmoves.narrative line_comment FROM debtortrans, debtorsmaster, custbranch,
                salesorders join locations l on salesorders.fromstkloc = l.loccode,
                shippers, salesman, rh_transaddress , stockmoves, stockmaster, paymentterms, rh_cfd__cfd c, companies cp
WHERE debtortrans.order_ = salesorders.orderno AND debtortrans.type=10 AND rh_transaddress.type = 10 AND rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=".$transno." AND debtortrans.shipvia=shippers.shipper_id AND debtortrans.debtorno=debtorsmaster.debtorno AND debtortrans.debtorno=custbranch.debtorno AND debtortrans.branchcode=custbranch.branchcode AND salesorders.salesman=salesman.salesmancode AND stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno= rh_transaddress.transno AND stockmoves.show_on_inv_crds=1 AND debtortrans.transno=rh_transaddress.transno AND debtorsmaster.paymentterms=paymentterms.termsindicator AND c.fk_transno = debtortrans.transno AND c.id_systypes = 10 limit 1";
	
	$rs_sql = DB_query($sql,$db);
	$myrow = DB_fetch_array($rs_sql);
	
	$dirs = array();
	$general_data = array();
	$comprobante = array();
	$conceptos = array();
	
	//Datos del emisor - direccion fiscal
	$dirs['emisor_DF']['calle'] = utf8_decode($myrow['regoffice1']);
	$dirs['emisor_DF']['noExterior'] = utf8_decode($myrow['regoffice2']);
	$dirs['emisor_DF']['noInterior'] = utf8_decode($myrow['regoffice3']);
	$dirs['emisor_DF']['colonia'] = utf8_decode($myrow['regoffice4']);
	$dirs['emisor_DF']['codigoPostal'] = utf8_decode($myrow['regoffice10']);
	$dirs['emisor_DF']['localidad'] = utf8_decode($myrow['regoffice5']);
	$dirs['emisor_DF']['municipio'] = utf8_decode($myrow['regoffice7']);
	$dirs['emisor_DF']['estado'] = utf8_decode($myrow['regoffice8']);
	$dirs['emisor_DF']['pais'] = utf8_decode($myrow['regoffice9']);
	
	//Datos del emisor - direccion emitido
	$dirs['emisor_EE']['calle'] = utf8_decode($myrow['deladd1']);
	$dirs['emisor_EE']['noExterior'] = utf8_decode($myrow['deladd2']);
	$dirs['emisor_EE']['noInterior'] = utf8_decode($myrow['deladd3']);
	$dirs['emisor_EE']['colonia'] = utf8_decode($myrow['deladd4']);
	$dirs['emisor_EE']['codigoPostal'] = utf8_decode($myrow['deladd10']);
	$dirs['emisor_EE']['localidad'] = utf8_decode($myrow['deladd5']);
	$dirs['emisor_EE']['municipio'] = utf8_decode($myrow['deladd7']);
	$dirs['emisor_EE']['estado'] = utf8_decode($myrow['deladd8']);
	$dirs['emisor_EE']['pais'] = utf8_decode($myrow['deladd9']);
	
	//Datos del receptor
	$dirs['receptor_DF']['calle'] = utf8_decode($myrow['direccion1']);
	$dirs['receptor_DF']['noExterior'] = utf8_decode($myrow['direccion2']);
	$dirs['receptor_DF']['noInterior'] = utf8_decode($myrow['direccion3']);
	$dirs['receptor_DF']['colonia'] = utf8_decode($myrow['direccion4']);
	$dirs['receptor_DF']['codigoPostal'] = utf8_decode($myrow['direccion10']);
	$dirs['receptor_DF']['localidad'] = utf8_decode($myrow['direccion5']);
	$dirs['receptor_DF']['municipio'] = utf8_decode($myrow['direccion7']);
	$dirs['receptor_DF']['estado'] = utf8_decode($myrow['direccion8']);
	$dirs['receptor_DF']['pais'] = utf8_decode($myrow['direccion9']);
	
	$comprobante['noCertificado'] = $myrow['comprobante_no_certificado'];
	$comprobante['formaDePago'] = '';
	$comprobante['noAprobacion'] = $myrow['numero_apr'];
	$comprobante['metodoDePago'] = '';
	$comprobante['condicionesDePago'] = $myrow['terms'];
	$comprobante['sello'] = $myrow['comprobante_sello'];
	$comprobante['anoAprobacion'] = $myrow['yy_aprovacion'];
	$comprobante['fecha'] = $myrow['fecha_y_hora_de_expedicion'];
	$comprobante['subTotal'] = $myrow['ovamount'];
	$comprobante['impuesto'] = $myrow['ovgst'];
	$comprobante['total'] = $myrow['gtotal'];
	$comprobante['serie'] = $myrow['serie'];
	$comprobante['folio'] = $myrow['extinvoice'];
	$comprobante['tipoDeComprobante'] = '';
	$comprobante['version'] = '';
	$comprobante['motivoDescuento'] = '';
	$comprobante['descuento'] = '';
	$comprobante['original'] = utf8_decode($myrow['comprobante_cadena_original']);
	
	$general_data['receptor']['nombre'] = $myrow['name'];
	$general_data['receptor']['rfc'] = $myrow['taxref'];
	$general_data['receptor']['no_cliente'] = $myrow['debtorno'];
	
	$general_data['emisor']['nombre'] = $myrow['coyname'];
	$general_data['emisor']['rfc'] = $myrow['gstno'];
	
	$general_data['vendedor'] = utf8_decode($myrow['salesmanname']);
	$general_data['sucursal'] = $myrow['sucursal'];
	$general_data['telefono'] = $myrow['telefono'];
	$general_data['cotizacion'] = $myrow['no_cotizacion'];
	$general_data['orden_trabajo'] = $myrow['orden_trabajo'];
	$general_data['orden_compra'] = $myrow['orden_compra'];
	$general_data['com_general'] = $myrow['invtext'];
	$general_data['url'] = 'www.gamatek.com.mx';
	$general_data['importe_letra'] = $myrow['extra_importe_con_letra_emdc'];
	
	$general_data['logo'] = 'companies/' . $_SESSION['DatabaseName'] . '/logo.jpg';
	

	$sql = "SELECT l.deladd7 municipio_domicilio_expedido_en, l.deladd8 estado_domicilio_expedido_en,l.deladd1,l.deladd2,l.deladd3,l.deladd4,l.deladd5,l.deladd6,l.deladd7,l.deladd8,l.deladd9,l.deladd10, cp.coyname, cp.telephone, cp.gstno, cp.regoffice1, cp.regoffice2, cp.regoffice3, cp.regoffice4, cp.regoffice5, cp.regoffice6, cp.regoffice7, cp.regoffice8, cp.regoffice9,cp.regoffice10, concat(custbranch.branchcode) sucursal, debtortrans.trandate, SUBSTRING(DATE_FORMAT(debtortrans.trandate, '%M'),1,3) as mm, DATE_FORMAT(debtortrans.trandate, '%d') as dd, DATE_FORMAT(debtortrans.trandate, '%Y') as yy, debtortrans.ovamount, debtortrans.id as ID, debtortrans.ovdiscount, debtortrans.ovfreight, debtortrans.ovgst, debtortrans.rate, debtortrans.invtext, debtortrans.consignment, debtorsmaster.name AS name_old, debtorsmaster.name2 AS name2_old, debtorsmaster.address1 AS address1_old, debtorsmaster.address2 AS address2_old, debtorsmaster.address3 AS address3_old, debtorsmaster.address4 AS address4_old, debtorsmaster.address5 AS address5_old, debtorsmaster.address6 AS address6_old, rh_transaddress.name, rh_transaddress.name2, rh_transaddress.address1, rh_transaddress.address2, rh_transaddress.address3, rh_transaddress.address4, rh_transaddress.address5, rh_transaddress.address6, rh_transaddress.taxref, debtorsmaster.currcode, debtorsmaster.invaddrbranch, debtorsmaster.taxref AS taxref_old, salesorders.deliverto, salesorders.deladd1, salesorders.deladd2, salesorders.deladd3, salesorders.deladd4, salesorders.customerref, salesorders.orderno, salesorders.orddate, shippers.shippername, custbranch.brname, custbranch.braddress1, custbranch.braddress2, custbranch.braddress3, custbranch.braddress4, custbranch.braddress5, custbranch.braddress6, salesman.salesmanname, debtortrans.debtorno ,stockmoves.stockid, stockmaster.description as description, -stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty)*debtortrans.rate AS fxnet, (stockmoves.price * 1)*debtortrans.rate AS fxprice, stockmoves.narrative, stockmaster.units, c.folio extinvoice ,rh_transaddress.address1 as direccion1, rh_transaddress.address2 as direccion2, rh_transaddress.address3 as direccion3, rh_transaddress.address4 as direccion4, rh_transaddress.address5 as direccion5, rh_transaddress.address6 as direccion6, rh_transaddress.address7 as direccion7, rh_transaddress.address8 as direccion8, rh_transaddress.address9 as direccion9, rh_transaddress.address10 as direccion10,  concat(stockmaster.description) as descripcion, debtortrans.ovamount+debtortrans.ovgst+ debtortrans.ovfreight- debtortrans.ovdiscount as gtotal, paymentterms.terms, c.no_certificado comprobante_no_certificado, c.ano_aprobacion as yy_aprovacion, c.no_aprobacion as numero_apr, c.total_en_letra extra_importe_con_letra, c.cadena_original comprobante_cadena_original, c.sello comprobante_sello, c.fecha fecha_y_hora_de_expedicion, debtorsmaster.rh_tel, c.serie, c.total_en_letra extra_importe_con_letra_emdc,custbranch.phoneno telefono,stockmoves.narrative line_comment ,GROUP_CONCAT(rh_pedimento.nopedimento) pedimento, GROUP_CONCAT(rh_pedimento.fecha) fecha_ped,GROUP_CONCAT(rh_pedimento.aduana) aduana FROM debtortrans, debtorsmaster, custbranch,
                salesorders join locations l on salesorders.fromstkloc = l.loccode,
                shippers, salesman, rh_transaddress , stockmoves left join stockpedimentomoves join rh_pedimento on rh_pedimento.pedimentoid = stockpedimentomoves.pedimentoid  on stockmoves.stkmoveno = stockpedimentomoves.stockmoveno , stockmaster, paymentterms, rh_cfd__cfd c, companies cp
WHERE debtortrans.order_ = salesorders.orderno AND debtortrans.type=10 AND rh_transaddress.type = 10 AND rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=".$transno." AND debtortrans.shipvia=shippers.shipper_id AND debtortrans.debtorno=debtorsmaster.debtorno AND debtortrans.debtorno=custbranch.debtorno AND debtortrans.branchcode=custbranch.branchcode AND custbranch.salesman=salesman.salesmancode AND stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno= rh_transaddress.transno AND stockmoves.show_on_inv_crds=1 AND debtortrans.transno=rh_transaddress.transno AND debtorsmaster.paymentterms=paymentterms.termsindicator AND c.fk_transno = debtortrans.transno AND c.id_systypes = 10 group by stockmoves.rh_orderline ";

  //echo $sql;
/*$sql = "SELECT l.deladd7 municipio_domicilio_expedido_en, l.deladd8 estado_domicilio_expedido_en,l.deladd1,l.deladd2,l.deladd3,l.deladd4,l.deladd5,l.deladd6,l.deladd7,l.deladd8,l.deladd9,l.deladd10, cp.coyname, cp.telephone, cp.gstno, cp.regoffice1, cp.regoffice2, cp.regoffice3, cp.regoffice4, cp.regoffice5, cp.regoffice6, cp.regoffice7, cp.regoffice8, cp.regoffice9,cp.regoffice10, concat(custbranch.branchcode) sucursal, debtortrans.trandate, SUBSTRING(DATE_FORMAT(debtortrans.trandate, '%M'),1,3) as mm, DATE_FORMAT(debtortrans.trandate, '%d') as dd, DATE_FORMAT(debtortrans.trandate, '%Y') as yy, debtortrans.ovamount, debtortrans.id as ID, debtortrans.ovdiscount, debtortrans.ovfreight, debtortrans.ovgst, debtortrans.rate, debtortrans.invtext, debtortrans.consignment, debtorsmaster.name AS name_old, debtorsmaster.name2 AS name2_old, debtorsmaster.address1 AS address1_old, debtorsmaster.address2 AS address2_old, debtorsmaster.address3 AS address3_old, debtorsmaster.address4 AS address4_old, debtorsmaster.address5 AS address5_old, debtorsmaster.address6 AS address6_old, rh_transaddress.name, rh_transaddress.name2, rh_transaddress.address1, rh_transaddress.address2, rh_transaddress.address3, rh_transaddress.address4, rh_transaddress.address5, rh_transaddress.address6, rh_transaddress.taxref, debtorsmaster.currcode, debtorsmaster.invaddrbranch, debtorsmaster.taxref AS taxref_old, salesorders.deliverto, salesorders.deladd1, salesorders.deladd2, salesorders.deladd3, salesorders.deladd4, salesorders.customerref, salesorders.orderno, salesorders.orddate, shippers.shippername, custbranch.brname, custbranch.braddress1, custbranch.braddress2, custbranch.braddress3, custbranch.braddress4, custbranch.braddress5, custbranch.braddress6, salesman.salesmanname, debtortrans.debtorno ,stockmoves.stockid, stockmaster.description as description, -stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty)*debtortrans.rate AS fxnet, (stockmoves.price * 1)*debtortrans.rate AS fxprice, stockmoves.narrative, stockmaster.units, c.folio extinvoice ,rh_transaddress.address1 as direccion1, rh_transaddress.address2 as direccion2, rh_transaddress.address3 as direccion3, rh_transaddress.address4 as direccion4, rh_transaddress.address5 as direccion5, rh_transaddress.address6 as direccion6, rh_transaddress.address7 as direccion7, rh_transaddress.address8 as direccion8, rh_transaddress.address9 as direccion9, rh_transaddress.address10 as direccion10,  concat(stockmaster.description) as descripcion, debtortrans.ovamount+debtortrans.ovgst+ debtortrans.ovfreight- debtortrans.ovdiscount as gtotal, paymentterms.terms, c.no_certificado comprobante_no_certificado, c.ano_aprobacion as yy_aprovacion, c.no_aprobacion as numero_apr, c.total_en_letra extra_importe_con_letra, c.cadena_original comprobante_cadena_original, c.sello comprobante_sello, c.fecha fecha_y_hora_de_expedicion, debtorsmaster.rh_tel, c.serie, c.total_en_letra extra_importe_con_letra_emdc,custbranch.phoneno telefono,stockmoves.narrative line_comment  FROM debtortrans, debtorsmaster, custbranch,
                salesorders join locations l on salesorders.fromstkloc = l.loccode,
                shippers, salesman, rh_transaddress , stockmoves , stockmaster, paymentterms, rh_cfd__cfd c, companies cp
WHERE debtortrans.order_ = salesorders.orderno AND debtortrans.type=10 AND rh_transaddress.type = 10 AND rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=".$transno." AND debtortrans.shipvia=shippers.shipper_id AND debtortrans.debtorno=debtorsmaster.debtorno AND debtortrans.debtorno=custbranch.debtorno AND debtortrans.branchcode=custbranch.branchcode AND custbranch.salesman=salesman.salesmancode AND stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno= rh_transaddress.transno AND stockmoves.show_on_inv_crds=1 AND debtortrans.transno=rh_transaddress.transno AND debtorsmaster.paymentterms=paymentterms.termsindicator AND c.fk_transno = debtortrans.transno AND c.id_systypes = 10 ";*/

	$rsp = DB_query($sql,$db);
	while($myrow = DB_fetch_array($rsp)){

		$concepto['unidad'][] = $myrow['units'];
        $concepto['codigo'][] = $myrow['stockid'];
		$concepto['cantidad'][] = $myrow['quantity'];
		$concepto['descripcion'][] = utf8_decode($myrow['descripcion']);
        $concepto['pedimento'][] = utf8_decode($myrow['pedimento']);
        $concepto['aduana'][] = utf8_decode($myrow['aduana']);
        $concepto['fecha'][] = utf8_decode($myrow['fecha_ped']);
		$concepto['valorUnitario'][] = $myrow['fxprice'];
		$concepto['importe'][] = $myrow['fxnet'];
		$concepto['narrative'][] = html_entity_decode(html_entity_decode($myrow['line_comment']));
	}
	$view = new CFDViewer();
	$view->loadData($dirs,$comprobante,$concepto,$general_data);
	$view->PrintCFD2(null,'D');

?>