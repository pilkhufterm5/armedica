<?php
global $cfdi_locacion, $cfdi_systypes;
function getSerieByBranch($branch, $type, $db) {
    global $cfdi_locacion, $cfdi_systypes;
    $sql = "select serie from rh_cfd__locations__systypes__ws_csd where id_locations='" . $branch . "' and id_systypes=" . $type;
    $result = DB_query($sql, $db);
    if (DB_error_no($db)) {
        throw new Exception('No se puede obtener el folio siguiente de la serie.0' . DB_error_msg($db) , 1);
    }
    $cfdi_systypes = $type;
    $cfdi_locacion = $branch;
    $row = DB_fetch_array($result);

    return $row['serie'];
}

function getFolio($serie, $db) {
    global $cfdi_locacion, $cfdi_systypes, $BlockFolio;

    /**
     * Se agrega bloqueo de tabla para evitar duplicar folio.
     * @author Rafael Rojas@realhost
     * @since 2013-08-28
     *
     */
    $sql = "LOCK TABLES rh_cfd__locations__systypes__ws_csd WRITE;";

    //DB_query($sql,$db,'','');
    $sql = "select fsiguiente from rh_cfd__locations__systypes__ws_csd where serie='" . $serie . "'";
    if (isset($cfdi_locacion) && !is_null($cfdi_locacion) && $cfdi_locacion != "" && isset($cfdi_systypes) && !is_null($cfdi_systypes) && $cfdi_systypes != "") {
        $sql.= " and id_locations='" . $cfdi_locacion . "'";
        $sql.= " and id_systypes='" . $cfdi_systypes . "'";
    }
    $sql.= ' for update ';
    $result = DB_query($sql, $db, '', '', false, false);
    if (DB_error_no($db)) {

        // bowikaxu realhost dic 07 - lock tables so we dont duplicate transno
        //DB_query("UNLOCK TABLES",$db,'','',true);
        throw new Exception('No se puede obtener el folio siguiente de la serie.', 1);
    }
    $row = DB_fetch_array($result);
    $sql = "update rh_cfd__locations__systypes__ws_csd set fsiguiente=fsiguiente+1 where serie='" . $serie . "'";
    $result = DB_query($sql, $db, '', '', false, false);
    if (DB_error_no($db)) {

        // bowikaxu realhost dic 07 - lock tables so we dont duplicate transno
        //DB_query("UNLOCK TABLES",$db,'','',true);
        throw new Exception('No se puede obtener el folio siguiente de la serie.', 1);
    }

    // bowikaxu realhost dic 07 - lock tables so we dont duplicate transno
    //DB_query("UNLOCK TABLES",$db,'','',true);
    return $row['fsiguiente'];
}

function getNoCertificado($serie, $db) {
    $sql = "select id_ws_csd from rh_cfd__locations__systypes__ws_csd where serie='" . $serie . "'";
    $result = DB_query($sql, $db, '', '', false, false);
    if (DB_error_no($db)) {
        throw new Exception('No se puede obtener certificado.', 1);
    }
    $row = DB_fetch_array($result);
    return $row['id_ws_csd'];
}

function getCSD($csd, $db) {
    $sql = "select certificado,`key` from rh_csd where noserie='" . $csd . "'";

    //echo $sql;
    $result = DB_query($sql, $db, '', '', false, false);
    if (DB_error_no($db)) {
        throw new Exception('No se puede obtener certificado.', 1);
    }
    $row = DB_fetch_array($result);
    return $row;
}

function cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd = 0, $xmlXsd = 0, $metodoPago = 'No Identificado', $ctaPago = 'No Identificado', $Masiva = false) {
    return cfdi($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $metodoPago, $ctaPago, $Masiva);
}

function cfdi($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $metodoPago = 'No Identificado', $ctaPago = 'No Identificado', $Masiva = false) {

    try {

        $sql = "select * from debtortrans deb left join stockmoves on stockmoves.type=deb.type and deb.transno=stockmoves.transno where deb.id='{$idDebtortrans}' limit 1";
        $result = DB_query($sql, $db, '', '', false, false);
        if (DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
        }
        $row = DB_fetch_assoc($result);
        $Locacion = $row['loccode'];

        $tipoDeComprobante;
        switch ($type) {
            case 10:
                $tipoDeComprobante = 'ingreso';
                break;

            case 11:
                $tipoDeComprobante = 'egreso';
                break;

            case 20002:
                $cartaPorteTipoDeComprobante = $_POST['cartaPorteTipoDeComprobante'];
                if (!$cartaPorteTipoDeComprobante) throw new Exception('No se escogio si la carta porte sera ingreso o traslado');
                $tipoDeComprobante = $cartaPorteTipoDeComprobante;
                break;

            default:
                throw new Exception('No se ha definido el tipo de comprobante (ingreso|egreso|traslado) para el tipo de transaccion ' . $type);
                break;
            }

            $sql = "select coyname Emisor_Nombre,gstno Emisor_RFC,regoffice1 Emisor_DomicilioFiscal_calle, regoffice2 Emisor_DomicilioFiscal_noExterior, regoffice3 Emisor_DomicilioFiscal_noInterior, regoffice4 Emisor_DomicilioFiscal_colonia, regoffice5 Emisor_DomicilioFiscal_localidad, regoffice6 Emisor_DomicilioFiscal_referencia, regoffice7 Emisor_DomicilioFiscal_municipio,  regoffice8 Emisor_DomicilioFiscal_estado,  regoffice9 Emisor_DomicilioFiscal_pais, regoffice10 Emisor_DomicilioFiscal_codigoPostal, regimen from companies limit 1";

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
            }
            $row = DB_fetch_array($result);
            $Emisor_Nombre = $row['Emisor_Nombre'];
            $Emisor_RFC = $row['Emisor_RFC'];

            $Emisor_DomicilioFiscal_calle = $row['Emisor_DomicilioFiscal_calle'];
            $Emisor_DomicilioFiscal_noExterior = $row['Emisor_DomicilioFiscal_noExterior'];
            $Emisor_DomicilioFiscal_noInterior = $row['Emisor_DomicilioFiscal_noInterior'];
            $Emisor_DomicilioFiscal_colonia = $row['Emisor_DomicilioFiscal_colonia'];
            $Emisor_DomicilioFiscal_localidad = $row['Emisor_DomicilioFiscal_localidad'];
            $Emisor_DomicilioFiscal_referencia = $row['Emisor_DomicilioFiscal_referencia'];
            $Emisor_DomicilioFiscal_municipio = $row['Emisor_DomicilioFiscal_municipio'];
            $Emisor_DomicilioFiscal_estado = $row['Emisor_DomicilioFiscal_estado'];
            $Emisor_DomicilioFiscal_pais = $row['Emisor_DomicilioFiscal_pais'];
            $Emisor_DomicilioFiscal_codigoPostal = $row['Emisor_DomicilioFiscal_codigoPostal'];
            $RegimenFiscal = $row['regimen'];

            $sqlExpedicion = "select " . " loc.deladd1 Emisor_ExpedidoEn_calle,
            " . " loc.deladd2 Emisor_ExpedidoEn_noExterior,
            " . " loc.deladd3 Emisor_ExpedidoEn_noInterior,
            " . " loc.deladd4 Emisor_ExpedidoEn_colonia,
            " . " loc.deladd5 Emisor_ExpedidoEn_localidad,
            " . " loc.deladd6 Emisor_ExpedidoEn_referencia,
            " . " loc.deladd7 Emisor_ExpedidoEn_municipio,
            " . " loc.deladd8 Emisor_ExpedidoEn_estado,
            " . " loc.deladd9 Emisor_ExpedidoEn_pais,
            " . " loc.deladd10 Emisor_ExpedidoEn_codigoPostal
            " . " from rh_cfd__locations__systypes__ws_csd csd
            left join locations loc on loc.loccode= csd.id_locations
            where csd.serie='{$serie}' and csd.id_ws_csd='{$idCsd}'";
            $result = DB_query($sqlExpedicion, $db, '', '', false, false);
            if (DB_num_rows($result) > 1) {
                 //Si tiene varias locaciones
                $sqlExpedicionx = $sqlExpedicion . " and loccode ='{$Locacion}'";
                $result = DB_query($sqlExpedicionx, $db, '', '', false, false);
                if (DB_num_rows($result) == 1) $sqlExpedicion = $sqlExpedicionx;
            }
            switch ($type) {
                case 10:
                    $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                    if (DB_num_rows($result) == 0) {
                        $sqlExpedicion = "select " . " loc.deladd1 Emisor_ExpedidoEn_calle, " . " loc.deladd2 Emisor_ExpedidoEn_noExterior, " . " loc.deladd3 Emisor_ExpedidoEn_noInterior, " . " loc.deladd4 Emisor_ExpedidoEn_colonia, " . " loc.deladd5 Emisor_ExpedidoEn_localidad, " . " loc.deladd6 Emisor_ExpedidoEn_referencia, " . " loc.deladd7 Emisor_ExpedidoEn_municipio,  " . " loc.deladd8 Emisor_ExpedidoEn_estado,  " . " loc.deladd9 Emisor_ExpedidoEn_pais, " . " loc.deladd10 Emisor_ExpedidoEn_codigoPostal " . " from debtortrans deb " . " left join salesorders sal on deb.order_=sal.orderno  " . " left join locations loc on loc.loccode = sal.fromstkloc where " . " deb.transno='{$transno}' and deb.type='{$type}'";
                        $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                        if (DB_num_rows($result) == 0) {
                            $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                        }
                    }
                    break;

                case 11:
                    $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                    if (DB_num_rows($result) == 0) {
                        $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                    }
                    break;

                case 20002:
                    $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                    break;

                default:
                    throw new Exception('No se ha definido el tipo de comprobante (ingreso|egreso|traslado) para el tipo de transaccion ' . $type);
                    break;
            }
            $sql = $sqlExpedicion;

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('Aun no esta configurada la informacion de la ubicacion', 1);
            }
            $row = DB_fetch_array($result);

            /*Datos Emisor*/
            $Emisor_ExpedidoEn_calle = $row['Emisor_ExpedidoEn_calle'];
            $Emisor_ExpedidoEn_noExterior = $row['Emisor_ExpedidoEn_noExterior'];
            $Emisor_ExpedidoEn_noInterior = $row['Emisor_ExpedidoEn_noInterior'];
            $Emisor_ExpedidoEn_colonia = $row['Emisor_ExpedidoEn_colonia'];
            $Emisor_ExpedidoEn_localidad = $row['Emisor_ExpedidoEn_localidad'];
            $Emisor_ExpedidoEn_referencia = $row['Emisor_ExpedidoEn_referencia'];
            $Emisor_ExpedidoEn_municipio = $row['Emisor_ExpedidoEn_municipio'];
            $Emisor_ExpedidoEn_estado = $row['Emisor_ExpedidoEn_estado'];
            $Emisor_ExpedidoEn_pais = $row['Emisor_ExpedidoEn_pais'];
            $Emisor_ExpedidoEn_codigoPostal = $row['Emisor_ExpedidoEn_codigoPostal'];

            $sql = "select rh_transaddress.currcode currencyCode, cast(debtortrans.ovdiscount as decimal(10,2)) Comprobante_descuento, cast((((debtortrans.ovamount-debtortrans.ovdiscount)+debtortrans.ovgst)/1) as decimal(10,2)) Comprobante_total, debtortrans.rh_createdate Comprobante_fecha, cast((debtortrans.ovamount/1) as decimal(10,2)) Comprobante_subtotal, cast((debtortrans.ovgst/1) as decimal(10,2)) Traslado_importe, rh_transaddress.taxref Receptor_rfc, rh_transaddress.name Receptor_nombre, rh_transaddress.address1 Receptor_calle, rh_transaddress.address2 Receptor_noExterior, rh_transaddress.address3 Receptor_noInterior, rh_transaddress.address4 Receptor_colonia, rh_transaddress.address5 Receptor_localidad, rh_transaddress.address6 Receptor_referencia, rh_transaddress.address7 Receptor_municipio,  rh_transaddress.address8 Receptor_estado,  rh_transaddress.address9 Receptor_pais, rh_transaddress.address10 Receptor_codigoPostal FROM debtortrans, rh_transaddress WHERE rh_transaddress.type = $type AND debtortrans.type=$type and rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=$transno limit 1";
            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
            }
            $row = DB_fetch_array($result);


            /*Datos Receptot*/
            $currencyCode = $row['currencyCode'];
            $Comprobante_descuento = $row['Comprobante_descuento'];
            $Comprobante_total = $row['Comprobante_total'];
            $Comprobante_fecha = str_replace(' ', 'T', $row['Comprobante_fecha']);
            $Comprobante_subtotal = $row['Comprobante_subtotal'];
            $Traslado_importe = $row['Traslado_importe'];

            $Receptor_rfc = $row['Receptor_rfc'];
            $Receptor_nombre = $row['Receptor_nombre'];
             //'á, é, í, ó, ú ᴹḼⁿỜ "&^<>/\\\'';
            $Receptor_Domicilio_calle = $row['Receptor_calle'];
             //1
            $Receptor_Domicilio_noExterior = $row['Receptor_noExterior'];
             //2
            $Receptor_Domicilio_noInterior = $row['Receptor_noInterior'];
             //3
            $Receptor_Domicilio_colonia = $row['Receptor_colonia'];
             //4
            $Receptor_Domicilio_localidad = $row['Receptor_localidad'];
             //5
            $Receptor_Domicilio_referencia = $row['Receptor_referencia'];
             //6
            $Receptor_Domicilio_municipio = $row['Receptor_municipio'];
             //7
            $Receptor_Domicilio_estado = $row['Receptor_estado'];
             //8
            $Receptor_Domicilio_pais = $row['Receptor_pais'];
             //9
            $Receptor_Domicilio_codigoPostal = $row['Receptor_codigoPostal'];
             //10

            $sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, if(stockmoves.description='',stockmaster.description,stockmoves.description) Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";

            //$sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad,stockmoves.stkmoveno Concepto_moveno FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica ' . $sql, 1);
            }
            $Conceptos = Array();
            $DescuentoGlobalFactura=0;
            $motivoDescuentoGlobalFactura='';
            while ($row = DB_fetch_array($result)) {
                $Concepto_noIdentificacion = $row['Concepto_noIdentificacion'];
                $Concepto_unidad = $row['Concepto_unidad'];
                $Concepto_cantidad = $row['Concepto_cantidad'];
                $Concepto_descripcion = $row['Concepto_descripcion'];
                $Concepto_valorUnitario = $row['Concepto_valorUnitario'];
                $Concepto_importe = $row['Concepto_importe'];
                $Concepto_moveno = $row['Concepto_moveno'];

                //Monto negativo se manda a descuento
//                 if($type==10&&$Concepto_importe*$Concepto_cantidad<0){
//                 	$DescuentoGlobalFactura+=$Concepto_importe*$Concepto_cantidad*-1;
//                 	$motivoDescuentoGlobalFactura.=' '.$Concepto_descripcion;
//                 	continue;
//                 }

                $sql = 'select rh_pedimento.nopedimento,rh_pedimento.fecha, rh_pedimento.aduana from stockpedimentomoves join rh_pedimento on stockpedimentomoves.pedimentoid=rh_pedimento.pedimentoid and stockpedimentomoves.stockmoveno=' . $Concepto_moveno;
                $rs = DB_query($sql, $db, '', '', false, false);

                /*if(DB_error_no($db)) {
                throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica '.$sql, 1);
                }/**/
                $orderlineno = $row['rh_orderline'];
                $pedimento = 0;
                $InformacionAduanera = Array();
                while ($arrayAduana = DB_fetch_array($rs)) {
                    $Concepto_InformacionAduanera_numero = $arrayAduana['nopedimento'];
                    $Concepto_InformacionAduanera_fecha = $arrayAduana['fecha'];
                    $Concepto_InformacionAduanera_aduana = $arrayAduana['aduana'];
                    $pedimento++;
                    array_push($InformacionAduanera, Array(
                        'numero' => $Concepto_InformacionAduanera_numero,
                        'fecha' => $Concepto_InformacionAduanera_fecha,
                        'aduana' => $Concepto_InformacionAduanera_aduana
                    ));
                }
                 /**/
                array_push($Conceptos, Array(
                    'noIdentificacion' => $Concepto_noIdentificacion,
                    'unidad' => $Concepto_unidad,
                    'cantidad' => $Concepto_cantidad,
                    'descripcion' => $Concepto_descripcion,
                    'valorUnitario' => $Concepto_valorUnitario,
                    'importe' => $Concepto_importe,
                    'InformacionAduanera' => $InformacionAduanera
                ));
            }

            $Impuestos = array();
            $Concepto_Impuestos = "select cast(((1 - s.discountpercent) * s.price * d.rate * -s.qty * abs(st.taxrate)) as decimal(10,2)) importe, t.description impuesto, st.taxrate*100 tasa from debtortrans d join stockmoves s on d.transno = s.transno and d.type = s.type join stockmovestaxes st on s.stkmoveno = st.stkmoveno join taxauthorities t on st.taxauthid = t.taxid where s.transno = $transno and s.type = $type";
            $result = DB_query($Concepto_Impuestos, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener los impuestos del concepto', 1);
            }
            while ($Concepto_Impuestos = DB_fetch_array($result)) {
                $Impuesto = array(
                    'impuesto' => $Concepto_Impuestos['impuesto'],
                    'tasa' => $Concepto_Impuestos['tasa'],
                    'importe' => $Concepto_Impuestos['importe']
                );
                array_push($Impuestos, $Impuesto);
            }

            $sql = "select p.terms Comprobante_condicionesDePago, cast((1/dt.rate) as decimal(18,10)) exchangeRate, dm.currcode,curr.currency  from paymentterms p, debtorsmaster dm, debtortrans dt, currencies curr where p.termsindicator = dm.paymentterms and dm.debtorno = dt.debtorno and dt.type=$type and dt.transno = $transno and dm.currcode=curr.currabrev limit 1";

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener informacion sobre el Comprobante (condiciones de pago) para la Factura Electronica', 1);
            }
            $row = mysql_fetch_array($result);
            $Comprobante_condicionesDePago = $row['Comprobante_condicionesDePago'];
            $Comprobante_TasaCambio = $row['exchangeRate'];
            $Comprobante_Moneda = $row['currcode'];
            $Comprobante_NombreMoneda = $row['currency'];

            try {
                $cfdi = new CFDI();
                $cfdi->comprobante->setAtribute('fecha', $Comprobante_fecha);
                $cfdi->comprobante->setAtribute('serie', $serie);
                $FFOLIO = getFolio($serie, $db);
                $cfdi->comprobante->setAtribute('folio', $FFOLIO);
                $cfdi->comprobante->setAtribute('TipoCambio', $Comprobante_TasaCambio);
                $cfdi->comprobante->setAtribute('Moneda', $Comprobante_Moneda);

                //    $Comprobante_descuento
                if ($type == 11) {
                    $cfdi->comprobante->setAtribute('total', -1 * $Comprobante_total);
                    $cfdi->comprobante->setAtribute('subTotal', -1 * $Comprobante_subtotal);
                } else {
                    $cfdi->comprobante->setAtribute('total', $Comprobante_total);
                    $cfdi->comprobante->setAtribute('subTotal', $Comprobante_subtotal);
                }
                if($DescuentoGlobalFactura>0){
                	$cfdi->comprobante->setAtribute('descuento', $DescuentoGlobalFactura);
                	$cfdi->comprobante->setAtribute('motivoDescuento', trim($motivoDescuentoGlobalFactura));
                }



                $cfdi->comprobante->setAtribute('tipoDeComprobante', $tipoDeComprobante);
                $cfdi->comprobante->setAtribute('formaDePago', 'PAGO EN UNA SOLA EXHIBICION');
                $cfdi->comprobante->setAtribute('metodoDePago', $metodoPago);
                $cfdi->comprobante->setAtribute('NumCtaPago', $ctaPago);
                $cfdi->comprobante->setAtribute('LugarExpedicion', $Emisor_ExpedidoEn_municipio . ', ' . $Emisor_ExpedidoEn_estado);

                //echo getNoCertificado($serie,$db).' sdfsdff'.$serie;
                $noCertificado = getNoCertificado($serie, $db);
                $cfdi->comprobante->setAtribute('noCertificado', $noCertificado);

                $cfdi->emisor->setAtribute('rfc', $Emisor_RFC);
                $cfdi->emisor->setAtribute('nombre', $Emisor_Nombre);

                //$Emisor_ExpedidoEn_colonia
                //$Emisor_DomicilioFiscal_noExterior
                //$Emisor_DomicilioFiscal_noInterior
                //$Emisor_DomicilioFiscal_referencia
                //$Emisor_DomicilioFiscal_localidad
                $cfdi->emisor->domicilioFiscal->setAtribute('calle', $Emisor_DomicilioFiscal_calle);
                $cfdi->emisor->domicilioFiscal->setAtribute('noExterior', $Emisor_DomicilioFiscal_noExterior);
                $cfdi->emisor->domicilioFiscal->setAtribute('noInterior', $Emisor_DomicilioFiscal_noInterior);
                $cfdi->emisor->domicilioFiscal->setAtribute('colonia', $Emisor_DomicilioFiscal_colonia);
                $cfdi->emisor->domicilioFiscal->setAtribute('municipio', $Emisor_DomicilioFiscal_municipio);
                $cfdi->emisor->domicilioFiscal->setAtribute('estado', $Emisor_DomicilioFiscal_estado);
                $cfdi->emisor->domicilioFiscal->setAtribute('pais', $Emisor_DomicilioFiscal_pais);
                $cfdi->emisor->domicilioFiscal->setAtribute('codigoPostal', $Emisor_DomicilioFiscal_codigoPostal);
                $cfdi->emisor->expedidoEn->setAtribute('calle', $Emisor_ExpedidoEn_calle);
                $cfdi->emisor->expedidoEn->setAtribute('noExterior', $Emisor_ExpedidoEn_noExterior);
                $cfdi->emisor->expedidoEn->setAtribute('noInterior', $Emisor_ExpedidoEn_noInterior);
                $cfdi->emisor->expedidoEn->setAtribute('colonia', $Emisor_ExpedidoEn_colonia);
                $cfdi->emisor->expedidoEn->setAtribute('municipio', $Emisor_ExpedidoEn_municipio);
                $cfdi->emisor->expedidoEn->setAtribute('estado', $Emisor_ExpedidoEn_estado);
                $cfdi->emisor->expedidoEn->setAtribute('pais', $Emisor_ExpedidoEn_pais);
                $cfdi->emisor->expedidoEn->setAtribute('codigoPostal', $Emisor_ExpedidoEn_codigoPostal);
                $cfdi->emisor->regimenFiscal->setAtribute('Regimen', $RegimenFiscal);

                $cfdi->receptor->setAtribute('rfc', $Receptor_rfc);
                $cfdi->receptor->setAtribute('nombre', $Receptor_nombre);
                $cfdi->receptor->domicilio->setAtribute('calle', $Receptor_Domicilio_calle);
                $cfdi->receptor->domicilio->setAtribute('noExterior', $Receptor_Domicilio_noExterior);
                $cfdi->receptor->domicilio->setAtribute('noInterior', $Receptor_Domicilio_noInterior);
                $cfdi->receptor->domicilio->setAtribute('colonia', $Receptor_Domicilio_colonia);
                $cfdi->receptor->domicilio->setAtribute('municipio', $Receptor_Domicilio_municipio);
                $cfdi->receptor->domicilio->setAtribute('estado', $Receptor_Domicilio_estado);
                $cfdi->receptor->domicilio->setAtribute('codigoPostal', $Receptor_Domicilio_codigoPostal);
                $cfdi->receptor->domicilio->setAtribute('pais', $Receptor_Domicilio_pais);

                for ($i = 0; $i < count($Conceptos); $i++) {
                    $Concepto = $Conceptos[$i];
                    switch ($type) {
                        case 11:
                            $Concep = $cfdi->conceptos->addConcepto();
                            $Concep->setAtribute('unidad', $Concepto['unidad']);
                            $Concep->setAtribute('cantidad', -1 * $Concepto['cantidad']);
                            $Concep->setAtribute('descripcion', $Concepto['descripcion']);
                            $Concep->setAtribute('valorUnitario', $Concepto['valorUnitario']);
                            $Concep->setAtribute('importe', -1 * $Concepto['importe']);
                            break;

                        default:
                            $Concep = $cfdi->conceptos->addConcepto();
                            $Concep->setAtribute('unidad', $Concepto['unidad']);
                            $Concep->setAtribute('cantidad', $Concepto['cantidad']);
                            $Concep->setAtribute('descripcion', $Concepto['descripcion']);
                            $Concep->setAtribute('valorUnitario', $Concepto['valorUnitario']);
                            $Concep->setAtribute('importe', $Concepto['importe']);
                            break;
                    }
                    if (isset($Concepto['InformacionAduanera']) && count($Concepto['InformacionAduanera']) > 0) {
                        foreach ($Concepto['InformacionAduanera'] as $pedimento) {
                            $InformacionAduanera = new InformacionAduanera();
                            foreach ($pedimento as $nodo => $valor) $InformacionAduanera->setAtribute($nodo, $valor);
                            $Concep->setPedimentos($InformacionAduanera);
                        }
                    }
                }

                //*****************************************************************************************************************

                $totalDeRetenciones = 0;
                $totalDeTraslados = 0;
                $totalDeRetencionesFED = 0;
                $totalDeTrasladosFED = 0;
                $tieneImpuestoNoLocal = false;
                $tieneImpuestoLocales = false;
                $IMPUESTOST = array();
                $IMPUESTOSR = array();
                foreach ($Impuestos as $Impuesto) {
                    if ($Impuesto['importe'] != '') {
                        if ($Impuesto['tasa'] >= 0) {

                            //traslado
                            if ($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "IEPS") {
                                $tieneImpuestoNoLocal = true;
                                switch ($type) {
                                    case 11:
                                        $totalDeTrasladosFED+= (($Impuesto['importe'] > 0) ? -1 * $Impuesto['importe'] : 0);
                                        if (isset($IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']])) {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']] = (-1 * $Impuesto['importe']);
                                        }
                                        break;

                                    default:
                                        if (isset($IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']])) {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']]+= ($Impuesto['importe']);
                                        } else {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']] = ($Impuesto['importe']);
                                        }
                                        $totalDeTrasladosFED+= $Impuesto['importe'];
                                        break;
                                }
                            } else {
                                $tieneImpuestoLocales = true;
                                $TrasladoLoc = $cfdi->impuestosLocales->addTraslado();
                                $TrasladoLoc->setAtribute('ImpLocTrasladado', $Impuesto['impuesto']);
                                $TrasladoLoc->setAtribute('TasadeTraslado', $Impuesto['tasa']);
                                $TrasladoLoc->setAtribute('Importe', $Impuesto['importe']);
                                $totalDeTraslados+= $Impuesto['importe'];
                            }
                        } else {

                            //retencion
                            $Impuesto['tasa']*= - 1;
                            if ($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "ISR") {
                                $tieneImpuestoNoLocal = true;
                                switch ($type) {
                                    case 11:
                                        $totalDeRetencionesFED+= (-1 * $Impuesto['importe']);
                                        if (isset($IMPUESTOSR[$Impuesto['impuesto']])) {
                                            $IMPUESTOSR[$Impuesto['impuesto']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOSR[$Impuesto['impuesto']] = (-1 * $Impuesto['importe']);
                                        }
                                        break;

                                    default:
                                        if (isset($IMPUESTOSR[$Impuesto['impuesto']])) {
                                            $IMPUESTOSR[$Impuesto['impuesto']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOSR[$Impuesto['impuesto']] = (-1 * $Impuesto['importe']);
                                        }
                                        $totalDeRetencionesFED+= - 1 * $Impuesto['importe'];
                                        break;
                                }
                            } else {
                                $tieneImpuestoLocales = true;
                                $RetencionLoc = $cfdi->impuestosLocales->addRetencion();
                                $RetencionLoc->setAtribute('ImpLocRetenido', $Impuesto['impuesto']);
                                $RetencionLoc->setAtribute('TasadeRetencion', $Impuesto['tasa']);
                                $RetencionLoc->setAtribute('Importe', number_format($Impuesto['importe'], 2, '.', ''));
                                $totalDeRetenciones+= ($Impuesto['importe']);
                            }
                        }
                    }
                }

                foreach ($IMPUESTOST as $k => $v) {
                    $Aux = explode('-', $k);
                    $traslado = $cfdi->impuestos->addTraslado();
                    $traslado->setAtribute('tasa', $Aux[1]);
                    $traslado->setAtribute('importe', number_format($v, 4, '.', ''));
                    $traslado->setAtribute('impuesto', $Aux[0]);
                }

                foreach ($IMPUESTOSR as $k => $v) {
                    $retencion = $cfdi->impuestos->addRetencion();
                    $retencion->setAtribute('importe', number_format(-1 * $v, 4, '.', ''));
                    $retencion->setAtribute('impuesto', $k);
                }

                $cfdi->impuestos->setAtribute('totalImpuestosTrasladados', number_format($totalDeTrasladosFED, 4, '.', ''));
                $cfdi->impuestos->setAtribute('totalImpuestosRetenidos', number_format(-1 * $totalDeRetencionesFED, 4, '.', ''));

                if ($tieneImpuestoLocales) {
                    $cfdi->complementos = true;
                    $totalDeRetenciones = number_format($totalDeRetenciones, 2, '.', '');
                    $totalDeTraslados = number_format($totalDeTraslados, 2, '.', '');
                    $cfdi->impuestosLocales->setAtribute('version', '1.0');
                    $cfdi->impuestosLocales->setAtribute('TotaldeRetenciones', $totalDeRetenciones);
                    $cfdi->impuestosLocales->setAtribute('TotaldeTraslados', $totalDeTraslados);
                }

                //*****************************************************************************************************************
                /*
                $totalDeRetenciones = 0;
                $totalDeTraslados = 0;
                $tieneImpuestoNoLocal = false;
                $tieneImpuestoLocales = false;
                foreach($Impuestos as $Impuesto){
                if($Impuesto['importe']!=''){
                if($Impuesto['tasa']>=0){
                    //traslado
                    if($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "IEPS"){
                        $tieneImpuestoNoLocal = true;
                        switch($type){
                            case 11:
                                $traslado=$cfdi->impuestos->addTraslado();
                                $traslado->setAtribute('tasa',$Impuesto['tasa']);
                                $traslado->setAtribute('importe',(($Impuesto['importe']>0)?-1*$Impuesto['importe']:0));
                                $traslado->setAtribute('impuesto',$Impuesto['impuesto']);
                                break;
                            default:
                                $traslado=$cfdi->impuestos->addTraslado();
                                $traslado->setAtribute('tasa',$Impuesto['tasa']);
                                $traslado->setAtribute('importe',$Impuesto['importe']);
                                $traslado->setAtribute('impuesto',$Impuesto['impuesto']);
                                break;
                        }
                    }else{
                        $tieneImpuestoLocales = true;
                        //aki va impuesto local
                        $TrasladoLoc=$cfdi->impuestosLocales->addTraslado();
                        $TrasladoLoc->setAtribute('ImpLocTrasladado',$Impuesto['impuesto']);
                        $TrasladoLoc->setAtribute('TasadeTraslado',$Impuesto['tasa']);
                        $TrasladoLoc->setAtribute('Importe',$Impuesto['importe']);
                        $totalDeTraslados += $Impuesto['importe'];
                    }
                }
                else{
                    //retencion
                    $Impuesto['tasa'] *= -1;
                    if($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "ISR"){
                        $tieneImpuestoNoLocal = true;
                        switch($type){
                            case 11:
                                $retencion=$cfdi->impuestos->addRetencion();
                                $retencion->setAtribute('importe',-1*$Impuesto['importe']);
                                $retencion->setAtribute('impuesto',$Impuesto['impuesto']);
                                break;
                            default:
                                $retencion=$cfdi->impuestos->addRetencion();
                                $retencion->setAtribute('importe',-1*$Impuesto['importe']);
                                $retencion->setAtribute('impuesto',$Impuesto['impuesto']);
                                break;
                        }
                    }else{
                        $tieneImpuestoLocales = true;
                        $RetencionLoc=$cfdi->impuestosLocales->addRetencion();
                        $RetencionLoc->setAtribute('ImpLocRetenido',$Impuesto['impuesto']);
                        $RetencionLoc->setAtribute('TasadeRetencion',$Impuesto['tasa']);
                        $RetencionLoc->setAtribute('Importe',$Impuesto['importe']);
                        $totalDeRetenciones += $Impuesto['importe'];
                    }
                }
                }
                }
                if($tieneImpuestoLocales){
                $cfdi->complementos=true;
                $totalDeRetenciones = number_format($totalDeRetenciones, 2,'.','');
                $totalDeTraslados = number_format($totalDeTraslados, 2,'.','');
                $cfdi->impuestosLocales->setAtribute('version','1.0');
                $cfdi->impuestosLocales->setAtribute('TotaldeRetenciones',$totalDeRetenciones);
                $cfdi->impuestosLocales->setAtribute('TotaldeTraslados',$totalDeTraslados);
                } */

                //echo $noCertificado;
                $CSDFiles = getCSD($noCertificado, $db);

                //var_dump($CSDFiles);
                $cfdi->setCSD(realpath('XMLFacturacionElectronica/csdandkey/' . $CSDFiles[0]));
                $cfdi->setKEY(realpath("XMLFacturacionElectronica/csdandkey/" . $CSDFiles[1]));


                // echo realpath('XMLFacturacionElectronica/csdandkey/'.$CSDFiles[0]);
                //exit;
                $xml = $cfdi->getXML();
                $var['UUID'] = $cfdi->getUUID();
                $var['timbre'] = $cfdi->getTimbre();
                $var['timetimbre'] = $cfdi->getTimbreTime();
                $var['original'] = trim($cfdi->getCOriginalTFD());
                $var['sello'] = $cfdi->getSello();
                $var['noCertificado'] = $cfdi->getnoCertificadoTimbre();
                $cfdi->getQRCode($Emisor_RFC, $Receptor_rfc, $Comprobante_total);
                chdir(dirname(__FILE__));
                $Letras = new Numbers_Words();

                $tot = explode(".", number_format($Comprobante_total, 2, '.', ''));
                $Letra = Numbers_Words::toWords($tot[0], "es");
                if ($Comprobante_Moneda == 'MXN') {
                    $Comprobante_Moneda = 'M.N.';
                }
                if ($tot[1] == 0) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . " 00/100 " . $Comprobante_Moneda;
                } else if (strlen($tot[1]) >= 2) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "/100 " . $Comprobante_Moneda;
                } else {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "0/100 " . $Comprobante_Moneda;
                }
                $ConLetra = strtoupper($ConLetra);

                /**
                 * Se agrega chdir, en algunas partes al generar el xml hace un cambio de directorio y fallaba con los cascos
                 * @author Rafael Rojas@realhost
                 *
                 */
                chdir(dirname(__FILE__));
                //printf('TIMBRADA1') ;
                $sqlInsert = "insert into rh_cfd__cfd(metodoPago,cuentapago,id_systypes, no_certificado, total_en_letra, cadena_original, sello,timbre,uuid, fecha, serie, xml, id_debtortrans, fk_transno, folio, tipo_de_comprobante)
                          values('" . $metodoPago . "',
                            '" . $ctaPago . "',$type,
                            '" . $var['noCertificado'] . "',
                            '" . $ConLetra . "',
                            '" . DB_escape_string($var['original']) . "',
                            '" . $var['sello'] . "',
                            '" . $var['timbre'] . "',
                            '" . $var['UUID'] . "',
                            '" . str_replace('T', ' ', $var['timetimbre']) . "',
                            '$serie',
                            '" . DB_escape_string($xml) . "',
                            $idDebtortrans,
                            $transno,
                            " . $FFOLIO . ",
                            " . (($tipoDeComprobante) ? "'" . DB_escape_string($tipoDeComprobante) . "'" : "null") . ")";
                $result = DB_query($sqlInsert, $db, '', '', false, false);
                if (DB_error_no($db)) {
                    throw new Exception('No se pudieron insertar los datos del CFDI en la base de datos local, notifique error inmediatamente y suspenda operaciones hasta que se le avise ' . DB_error_msg($db) , 1);
                }
                //printf('TIMBRADA') ;

                if (!DB_query('commit', $db, '', '', false, false)) {
                    throw new Exception('Error al efectuar el commit, notifique error inmediatamente y suspenga operaciones hasta que se le avise', 1);
                }
                return $var;
            }
            catch(Exception $e) {
                if (!DB_query('rollback', $db, '', '', false, false)) {
                    $msg.= 'Error al efectuar el rollback';
                }

                //Para Facturacion Masiva, se necesita que continue aun en caso de Error,
                if ($Masiva) {
                    $ErrorMSG = "ID DebtorTrans: {$idDebtortrans} -> ERROR: " . $e->getMessage();
                    CrateErrorLog($ErrorMSG, $idDebtortrans);
                } else {
                    echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e->getMessage() . '<p></div>';
                    include ('includes/footer.inc');
                    exit;
                }
            }
        }
        catch(Exception $e) {
            if (!DB_query('rollback', $db, '', '', false, false)) {
                $msg.= 'Error al efectuar el rollback';
            }

            //Para Facturacion Masiva, se necesita que continue aun en caso de Error,
            if ($Masiva) {
                $ErrorMSG = "ID DebtorTrans: {$idDebtortrans} -> ERROR: " . $e->getMessage();
                CrateErrorLog($ErrorMSG, $idDebtortrans);
            } else {
                echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e->getMessage() . '<p></div>';
                include ('includes/footer.inc');
                exit;
            }
        }
    }

    /**
     * @Todo
     * Crea Log para los Errores en Facturacion Masiva
     * Crea Archivo en /tmp   con el nombre 'emisionmasiva_2014-06-26'
     * @author erasto@realhost.com.mx
     */
    function CrateErrorLog($msg = null, $idDebtortrans = null) {
        global $db;
        /* Actualizo status en la Bitacora*/
        if (!empty($idDebtortrans)) {
            //$SQLUpdate = "UPDATE rh_facturacion SET status = 'Error'  WHERE debtortrans_id = '{$idDebtortrans}'";
            //$_2Update = DB_query($SQLUpdate, $db);

            $SQLInsert = "INSERT INTO rh_logfacturacion (id_debtortrans, error)VALUES('{$idDebtortrans}', '". DB_escape_string($msg) . "') ";
            $_2Insert = DB_query($SQLInsert, $db);
        }

        if (!file_exists("/tmp/emisionmasiva_" . date('Y-m-d') . ".log")) {
            $Titulo = "Emision Masiva Fecha: " . date('Y-m-d H:i:s') . "\n \n";
            $fp = fopen("/tmp/emisionmasiva_" . date('Y-m-d') . ".log", "x");
            fwrite($fp, $Titulo);
            fclose($fp);
        }

        $filename = "/tmp/emisionmasiva_" . date('Y-m-d') . ".log";
        $ErrorText = $msg . "\n \n";

        // Verificamos si se puede escribir en el archivo
        if (is_writable($filename)) {
            if (!$handle = fopen($filename, 'a')) {
                echo "El Archivo no Existe";
                //exit;
            }

            // Logea el Error
            if (fwrite($handle, $ErrorText) === FALSE) {
                //echo "No se Pudo Escribir en el Archivo.";
                //exit;
            }

            //echo "Success";
            fclose($handle);
        } else {
            echo "No se pudo escribir en el Archivo";
        }
    }

    function printDatosSat(&$datosSat) {
        $UUID = $datosSat['UUID'];
        $Timbre = $datosSat['timbre'];
        $TimeTimbre = $datosSat['timetimbre'];
        $COTimbre = str_replace(" ", "&nbsp;", $datosSat['original']);
        $noCertificado = $datosSat['noCertificado'];
        $sello = $datosSat['sello'];

        //chars per line
        $cpl = 180;

        //cadena original length
        $col = strlen($COTimbre);
        if ($col > $cpl) {
            $cadenaOriginalFormatted = '';
            for ($i = 0; $i < $col; $i+= $cpl) $cadenaOriginalFormatted.= substr($COTimbre, $i, $cpl) . "\n";
            $COTimbre = $cadenaOriginalFormatted;
        }
        $dato = explode('-', $UUID);
        $datosSat['transno'] = array_pop($dato);
        $datosSat['serie'] = implode('-', $dato);
        $datosSat['folio'] = '';

        echo '<table style="width:900px;"><tr><td bgcolor="#bbbbbb"><b>Hora de timbrado:</b></td></tr><tr><td>' . $TimeTimbre . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Folio Fiscal:</b></td></tr><tr><td>' . $UUID . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Timbre Fiscal:</b></td></tr><tr><td>' . $Timbre . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Certificado Fiscal:</b></td></tr><tr><td>' . $noCertificado . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Sello:</b></td></tr><tr><td>' . $sello . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Cadena Original: </b></td></tr><tr><td>' . $COTimbre . '</td></tr></table>';
    }
    function v($text) {
        if (isSet($text) && trim($text) != '') return true;
        else return false;
    }

    function t($t) {
        return str_replace('<', '&lt;', str_replace('<', '&lt;', str_replace('>', '&gt;', $t)));
    }


function cfdMultiEmpresas($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd = 0, $xmlXsd = 0, $metodoPago = 'No Identificado', $ctaPago = 'No Identificado', $Masiva = false) {
    return cfdiMultiEmpresas($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $metodoPago, $ctaPago, $Masiva);
}

function cfdiMultiEmpresas($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $metodoPago = 'No Identificado', $ctaPago = 'No Identificado', $Masiva = false) {

    try {

        $sql = "select * from debtortrans deb left join stockmoves on stockmoves.type=deb.type and deb.transno=stockmoves.transno where deb.id='{$idDebtortrans}' limit 1";
        $result = DB_query($sql, $db, '', '', false, false);
        if (DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
        }
        $row = DB_fetch_assoc($result);
        $Locacion = $row['loccode'];

        $tipoDeComprobante;
        switch ($type) {
            case 10:
                $tipoDeComprobante = 'ingreso';
                break;

            case 11:
                $tipoDeComprobante = 'egreso';
                break;

            case 20002:
                $cartaPorteTipoDeComprobante = $_POST['cartaPorteTipoDeComprobante'];
                if (!$cartaPorteTipoDeComprobante) throw new Exception('No se escogio si la carta porte sera ingreso o traslado');
                $tipoDeComprobante = $cartaPorteTipoDeComprobante;
                break;

            default:
                throw new Exception('No se ha definido el tipo de comprobante (ingreso|egreso|traslado) para el tipo de transaccion ' . $type);
                break;
            }

            $sql = "select coyname Emisor_Nombre,gstno Emisor_RFC,regoffice1 Emisor_DomicilioFiscal_calle, regoffice2 Emisor_DomicilioFiscal_noExterior, regoffice3 Emisor_DomicilioFiscal_noInterior, regoffice4 Emisor_DomicilioFiscal_colonia, regoffice5 Emisor_DomicilioFiscal_localidad, regoffice6 Emisor_DomicilioFiscal_referencia, regoffice7 Emisor_DomicilioFiscal_municipio,  regoffice8 Emisor_DomicilioFiscal_estado,  regoffice9 Emisor_DomicilioFiscal_pais, regoffice10 Emisor_DomicilioFiscal_codigoPostal, regimen from companies limit 1";
            
            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
            }
            $row = DB_fetch_array($result);
            $Emisor_Nombre = $row['Emisor_Nombre'];
            $Emisor_RFC = $row['Emisor_RFC'];

            $Emisor_DomicilioFiscal_calle = $row['Emisor_DomicilioFiscal_calle'];
            $Emisor_DomicilioFiscal_noExterior = $row['Emisor_DomicilioFiscal_noExterior'];
            $Emisor_DomicilioFiscal_noInterior = $row['Emisor_DomicilioFiscal_noInterior'];
            $Emisor_DomicilioFiscal_colonia = $row['Emisor_DomicilioFiscal_colonia'];
            $Emisor_DomicilioFiscal_localidad = $row['Emisor_DomicilioFiscal_localidad'];
            $Emisor_DomicilioFiscal_referencia = $row['Emisor_DomicilioFiscal_referencia'];
            $Emisor_DomicilioFiscal_municipio = $row['Emisor_DomicilioFiscal_municipio'];
            $Emisor_DomicilioFiscal_estado = $row['Emisor_DomicilioFiscal_estado'];
            $Emisor_DomicilioFiscal_pais = $row['Emisor_DomicilioFiscal_pais'];
            $Emisor_DomicilioFiscal_codigoPostal = $row['Emisor_DomicilioFiscal_codigoPostal'];
            $RegimenFiscal = $row['regimen'];

            $sqlExpedicion = "select " . " loc.deladd1 Emisor_ExpedidoEn_calle,
            " . " loc.deladd2 Emisor_ExpedidoEn_noExterior,
            " . " loc.deladd3 Emisor_ExpedidoEn_noInterior,
            " . " loc.deladd4 Emisor_ExpedidoEn_colonia,
            " . " loc.deladd5 Emisor_ExpedidoEn_localidad,
            " . " loc.deladd6 Emisor_ExpedidoEn_referencia,
            " . " loc.deladd7 Emisor_ExpedidoEn_municipio,
            " . " loc.deladd8 Emisor_ExpedidoEn_estado,
            " . " loc.deladd9 Emisor_ExpedidoEn_pais,
            " . " loc.deladd10 Emisor_ExpedidoEn_codigoPostal
            " . " from rh_cfd__locations__systypes__ws_csd csd
            left join locations loc on loc.loccode= csd.id_locations
            where csd.serie='{$serie}' and csd.id_ws_csd='{$idCsd}'";
            $result = DB_query($sqlExpedicion, $db, '', '', false, false);
            if (DB_num_rows($result) > 1) {
                 //Si tiene varias locaciones
                $sqlExpedicionx = $sqlExpedicion . " and loccode ='{$Locacion}'";
                $result = DB_query($sqlExpedicionx, $db, '', '', false, false);
                if (DB_num_rows($result) == 1) $sqlExpedicion = $sqlExpedicionx;
            }
            switch ($type) {
                case 10:
                    $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                    if (DB_num_rows($result) == 0) {
                        $sqlExpedicion = "select " . " loc.deladd1 Emisor_ExpedidoEn_calle, " . " loc.deladd2 Emisor_ExpedidoEn_noExterior, " . " loc.deladd3 Emisor_ExpedidoEn_noInterior, " . " loc.deladd4 Emisor_ExpedidoEn_colonia, " . " loc.deladd5 Emisor_ExpedidoEn_localidad, " . " loc.deladd6 Emisor_ExpedidoEn_referencia, " . " loc.deladd7 Emisor_ExpedidoEn_municipio,  " . " loc.deladd8 Emisor_ExpedidoEn_estado,  " . " loc.deladd9 Emisor_ExpedidoEn_pais, " . " loc.deladd10 Emisor_ExpedidoEn_codigoPostal " . " from debtortrans deb " . " left join salesorders sal on deb.order_=sal.orderno  " . " left join locations loc on loc.loccode = sal.fromstkloc where " . " deb.transno='{$transno}' and deb.type='{$type}'";
                        $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                        if (DB_num_rows($result) == 0) {
                            $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                        }
                    }
                    break;

                case 11:
                    $result = DB_query($sqlExpedicion, $db, '', '', false, false);
                    if (DB_num_rows($result) == 0) {
                        $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                    }
                    break;

                case 20002:
                    $sqlExpedicion = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
                    break;

                default:
                    throw new Exception('No se ha definido el tipo de comprobante (ingreso|egreso|traslado) para el tipo de transaccion ' . $type);
                    break;
            }
            $sql = $sqlExpedicion;

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('Aun no esta configurada la informacion de la ubicacion', 1);
            }
            $row = DB_fetch_array($result);

            /*Datos Emisor*/
            $Emisor_ExpedidoEn_calle = $row['Emisor_ExpedidoEn_calle'];
            $Emisor_ExpedidoEn_noExterior = $row['Emisor_ExpedidoEn_noExterior'];
            $Emisor_ExpedidoEn_noInterior = $row['Emisor_ExpedidoEn_noInterior'];
            $Emisor_ExpedidoEn_colonia = $row['Emisor_ExpedidoEn_colonia'];
            $Emisor_ExpedidoEn_localidad = $row['Emisor_ExpedidoEn_localidad'];
            $Emisor_ExpedidoEn_referencia = $row['Emisor_ExpedidoEn_referencia'];
            $Emisor_ExpedidoEn_municipio = $row['Emisor_ExpedidoEn_municipio'];
            $Emisor_ExpedidoEn_estado = $row['Emisor_ExpedidoEn_estado'];
            $Emisor_ExpedidoEn_pais = $row['Emisor_ExpedidoEn_pais'];
            $Emisor_ExpedidoEn_codigoPostal = $row['Emisor_ExpedidoEn_codigoPostal'];

            $sql = "select rh_transaddress.currcode currencyCode, cast(debtortrans.ovdiscount as decimal(10,2)) Comprobante_descuento, cast((((debtortrans.ovamount-debtortrans.ovdiscount)+debtortrans.ovgst)/1) as decimal(10,2)) Comprobante_total, debtortrans.rh_createdate Comprobante_fecha, cast((debtortrans.ovamount/1) as decimal(10,2)) Comprobante_subtotal, cast((debtortrans.ovgst/1) as decimal(10,2)) Traslado_importe, rh_transaddress.taxref Receptor_rfc, rh_transaddress.name Receptor_nombre, rh_transaddress.address1 Receptor_calle, rh_transaddress.address2 Receptor_noExterior, rh_transaddress.address3 Receptor_noInterior, rh_transaddress.address4 Receptor_colonia, rh_transaddress.address5 Receptor_localidad, rh_transaddress.address6 Receptor_referencia, rh_transaddress.address7 Receptor_municipio,  rh_transaddress.address8 Receptor_estado,  rh_transaddress.address9 Receptor_pais, rh_transaddress.address10 Receptor_codigoPostal FROM debtortrans, rh_transaddress WHERE rh_transaddress.type = $type AND debtortrans.type=$type and rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=$transno limit 1";
            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
            }
            $row = DB_fetch_array($result);


            /*Datos Receptot*/
            $currencyCode = $row['currencyCode'];
            $Comprobante_descuento = $row['Comprobante_descuento'];
            $Comprobante_total = $row['Comprobante_total'];
            $Comprobante_fecha = str_replace(' ', 'T', $row['Comprobante_fecha']);
            $Comprobante_subtotal = $row['Comprobante_subtotal'];
            $Traslado_importe = $row['Traslado_importe'];

            $Receptor_rfc = $row['Receptor_rfc'];
            $Receptor_nombre = $row['Receptor_nombre'];
             //'á, é, í, ó, ú ᴹḼⁿỜ "&^<>/\\\'';
            $Receptor_Domicilio_calle = $row['Receptor_calle'];
             //1
            $Receptor_Domicilio_noExterior = $row['Receptor_noExterior'];
             //2
            $Receptor_Domicilio_noInterior = $row['Receptor_noInterior'];
             //3
            $Receptor_Domicilio_colonia = $row['Receptor_colonia'];
             //4
            $Receptor_Domicilio_localidad = $row['Receptor_localidad'];
             //5
            $Receptor_Domicilio_referencia = $row['Receptor_referencia'];
             //6
            $Receptor_Domicilio_municipio = $row['Receptor_municipio'];
             //7
            $Receptor_Domicilio_estado = $row['Receptor_estado'];
             //8
            $Receptor_Domicilio_pais = $row['Receptor_pais'];
             //9
            $Receptor_Domicilio_codigoPostal = $row['Receptor_codigoPostal'];
             //10

            /*==========================================================*/
            // COMIENZA OBTENEMOS LOS CONCEPTOS 
            $sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, if(stockmoves.description='',stockmaster.description,stockmoves.description) Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";

            //$sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad,stockmoves.stkmoveno Concepto_moveno FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";

                       $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica ' . $sql, 1);
            }
            $Conceptos = Array();
            $DescuentoGlobalFactura=0;
            $motivoDescuentoGlobalFactura='';
            $Concepto_valorUnitarioN = 0;
            while ($row = DB_fetch_array($result)) {
                $Concepto_noIdentificacion = $row['Concepto_noIdentificacion'];
                $Concepto_unidad = $row['Concepto_unidad'];
                $Concepto_cantidad = $row['Concepto_cantidad'];
                $Concepto_descripcion = $row['Concepto_descripcion'];
                $Concepto_valorUnitario = $row['Concepto_valorUnitario'];
                $Concepto_importe = $row['Concepto_importe'];
                $Concepto_moveno = $row['Concepto_moveno'];


                $sql = 'select rh_pedimento.nopedimento,rh_pedimento.fecha, rh_pedimento.aduana from stockpedimentomoves join rh_pedimento on stockpedimentomoves.pedimentoid=rh_pedimento.pedimentoid and stockpedimentomoves.stockmoveno=' . $Concepto_moveno;
                $rs = DB_query($sql, $db, '', '', false, false);

                /*if(DB_error_no($db)) {
                throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica '.$sql, 1);
                }/**/
                $orderlineno = $row['rh_orderline'];
                $pedimento = 0;
                $InformacionAduanera = Array();
                while ($arrayAduana = DB_fetch_array($rs)) {
                    $Concepto_InformacionAduanera_numero = $arrayAduana['nopedimento'];
                    $Concepto_InformacionAduanera_fecha = $arrayAduana['fecha'];
                    $Concepto_InformacionAduanera_aduana = $arrayAduana['aduana'];
                    $pedimento++;
                    array_push($InformacionAduanera, Array(
                        'numero' => $Concepto_InformacionAduanera_numero,
                        'fecha' => $Concepto_InformacionAduanera_fecha,
                        'aduana' => $Concepto_InformacionAduanera_aduana
                    ));
                }
                 /**/
                array_push($Conceptos, Array(
                    'noIdentificacion' => $Concepto_noIdentificacion,
                    'unidad' => $Concepto_unidad,
                    'cantidad' => $Concepto_cantidad,
                    'descripcion' => $Concepto_descripcion,
                    'valorUnitario' => $Concepto_valorUnitario,
                    'importe' => $Concepto_importe,
                    'InformacionAduanera' => $InformacionAduanera
                ));

                $Concepto_valorUnitarioN = $Concepto_valorUnitarioN  + $Concepto_valorUnitario;
            } // end  while ($row = DB_fetch_array($result))
            //
            // CONCEPTOS
            //

            $arr = explode('SERVICIO MEDICO', $Concepto_descripcion );
            $Concepto_descripcion = 'SERVICIO MEDICO '.  $arr[1];
           

            $Impuestos = array();
            $Concepto_Impuestos = "select cast(((1 - s.discountpercent) * s.price * d.rate * -s.qty * abs(st.taxrate)) as decimal(10,2)) importe, t.description impuesto, st.taxrate*100 tasa from debtortrans d join stockmoves s on d.transno = s.transno and d.type = s.type join stockmovestaxes st on s.stkmoveno = st.stkmoveno join taxauthorities t on st.taxauthid = t.taxid where s.transno = $transno and s.type = $type";
            $result = DB_query($Concepto_Impuestos, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener los impuestos del concepto', 1);
            }
            while ($Concepto_Impuestos = DB_fetch_array($result)) {
                $Impuesto = array(
                    'impuesto' => $Concepto_Impuestos['impuesto'],
                    'tasa' => $Concepto_Impuestos['tasa'],
                    'importe' => $Concepto_Impuestos['importe']
                );
                array_push($Impuestos, $Impuesto);
            }
            // TERMINA  - DANIEL VILLARREAL 
            /*==========================================================*/
            
            $sql = "select p.terms Comprobante_condicionesDePago, cast((1/dt.rate) as decimal(18,10)) exchangeRate, dm.currcode,curr.currency  from paymentterms p, debtorsmaster dm, debtortrans dt, currencies curr where p.termsindicator = dm.paymentterms and dm.debtorno = dt.debtorno and dt.type=$type and dt.transno = $transno and dm.currcode=curr.currabrev limit 1";

            $result = DB_query($sql, $db, '', '', false, false);
            if (DB_error_no($db)) {
                throw new Exception('No se pudo obtener informacion sobre el Comprobante (condiciones de pago) para la Factura Electronica', 1);
            }
            $row = mysql_fetch_array($result);
            $Comprobante_condicionesDePago = $row['Comprobante_condicionesDePago'];
            $Comprobante_TasaCambio = $row['exchangeRate'];
            $Comprobante_Moneda = $row['currcode'];
            $Comprobante_NombreMoneda = $row['currency'];

            try {
                $cfdi = new CFDI();
                $cfdi->comprobante->setAtribute('fecha', $Comprobante_fecha);
                $cfdi->comprobante->setAtribute('serie', $serie);
                $FFOLIO = getFolio($serie, $db);
                $cfdi->comprobante->setAtribute('folio', $FFOLIO);
                $cfdi->comprobante->setAtribute('TipoCambio', $Comprobante_TasaCambio);
                $cfdi->comprobante->setAtribute('Moneda', $Comprobante_Moneda);

                //    $Comprobante_descuento
                if ($type == 11) {
                    $cfdi->comprobante->setAtribute('total', -1 * $Comprobante_total);
                    $cfdi->comprobante->setAtribute('subTotal', -1 * $Comprobante_subtotal);
                } else {
                    $cfdi->comprobante->setAtribute('total', $Comprobante_total);
                    $cfdi->comprobante->setAtribute('subTotal', $Comprobante_subtotal);
                }
                if($DescuentoGlobalFactura>0){
                    $cfdi->comprobante->setAtribute('descuento', $DescuentoGlobalFactura);
                    $cfdi->comprobante->setAtribute('motivoDescuento', trim($motivoDescuentoGlobalFactura));
                }



                $cfdi->comprobante->setAtribute('tipoDeComprobante', $tipoDeComprobante);
                $cfdi->comprobante->setAtribute('formaDePago', 'PAGO EN UNA SOLA EXHIBICION');
                $cfdi->comprobante->setAtribute('metodoDePago', $metodoPago);
                $cfdi->comprobante->setAtribute('NumCtaPago', $ctaPago);
                $cfdi->comprobante->setAtribute('LugarExpedicion', $Emisor_ExpedidoEn_municipio . ', ' . $Emisor_ExpedidoEn_estado);

                //echo getNoCertificado($serie,$db).' sdfsdff'.$serie;
                $noCertificado = getNoCertificado($serie, $db);
                $cfdi->comprobante->setAtribute('noCertificado', $noCertificado);

                $cfdi->emisor->setAtribute('rfc', $Emisor_RFC);
                $cfdi->emisor->setAtribute('nombre', $Emisor_Nombre);

                //$Emisor_ExpedidoEn_colonia
                //$Emisor_DomicilioFiscal_noExterior
                //$Emisor_DomicilioFiscal_noInterior
                //$Emisor_DomicilioFiscal_referencia
                //$Emisor_DomicilioFiscal_localidad
                $cfdi->emisor->domicilioFiscal->setAtribute('calle', $Emisor_DomicilioFiscal_calle);
                $cfdi->emisor->domicilioFiscal->setAtribute('noExterior', $Emisor_DomicilioFiscal_noExterior);
                $cfdi->emisor->domicilioFiscal->setAtribute('noInterior', $Emisor_DomicilioFiscal_noInterior);
                $cfdi->emisor->domicilioFiscal->setAtribute('colonia', $Emisor_DomicilioFiscal_colonia);
                $cfdi->emisor->domicilioFiscal->setAtribute('municipio', $Emisor_DomicilioFiscal_municipio);
                $cfdi->emisor->domicilioFiscal->setAtribute('estado', $Emisor_DomicilioFiscal_estado);
                $cfdi->emisor->domicilioFiscal->setAtribute('pais', $Emisor_DomicilioFiscal_pais);
                $cfdi->emisor->domicilioFiscal->setAtribute('codigoPostal', $Emisor_DomicilioFiscal_codigoPostal);
                $cfdi->emisor->expedidoEn->setAtribute('calle', $Emisor_ExpedidoEn_calle);
                $cfdi->emisor->expedidoEn->setAtribute('noExterior', $Emisor_ExpedidoEn_noExterior);
                $cfdi->emisor->expedidoEn->setAtribute('noInterior', $Emisor_ExpedidoEn_noInterior);
                $cfdi->emisor->expedidoEn->setAtribute('colonia', $Emisor_ExpedidoEn_colonia);
                $cfdi->emisor->expedidoEn->setAtribute('municipio', $Emisor_ExpedidoEn_municipio);
                $cfdi->emisor->expedidoEn->setAtribute('estado', $Emisor_ExpedidoEn_estado);
                $cfdi->emisor->expedidoEn->setAtribute('pais', $Emisor_ExpedidoEn_pais);
                $cfdi->emisor->expedidoEn->setAtribute('codigoPostal', $Emisor_ExpedidoEn_codigoPostal);
                $cfdi->emisor->regimenFiscal->setAtribute('Regimen', $RegimenFiscal);

                $cfdi->receptor->setAtribute('rfc', $Receptor_rfc);
                $cfdi->receptor->setAtribute('nombre', $Receptor_nombre);
                $cfdi->receptor->domicilio->setAtribute('calle', $Receptor_Domicilio_calle);
                $cfdi->receptor->domicilio->setAtribute('noExterior', $Receptor_Domicilio_noExterior);
                $cfdi->receptor->domicilio->setAtribute('noInterior', $Receptor_Domicilio_noInterior);
                $cfdi->receptor->domicilio->setAtribute('colonia', $Receptor_Domicilio_colonia);
                $cfdi->receptor->domicilio->setAtribute('municipio', $Receptor_Domicilio_municipio);
                $cfdi->receptor->domicilio->setAtribute('estado', $Receptor_Domicilio_estado);
                $cfdi->receptor->domicilio->setAtribute('codigoPostal', $Receptor_Domicilio_codigoPostal);
                $cfdi->receptor->domicilio->setAtribute('pais', $Receptor_Domicilio_pais);

                // Agregamos el concepto a la factura
                // DANIEL VILLARREAL EL 8 DE ENERO DEL 2016
                // NO SE TOMA EN REFERENCIA LO ANTERIOR.
                //NO SE AGREGA OTRO CONCEPTO, SOLO 1
                $Concep = $cfdi->conceptos->addConcepto();
                $Concep->setAtribute('unidad', 1);
                $Concep->setAtribute('cantidad', 1);
                $Concep->setAtribute('descripcion', $Concepto_descripcion);
                $Concep->setAtribute('valorUnitario',$Concepto_valorUnitarioN);
                $Concep->setAtribute('importe', $Concepto_valorUnitarioN);

                //*****************************************************************************************************************

                $totalDeRetenciones = 0;
                $totalDeTraslados = 0;
                $totalDeRetencionesFED = 0;
                $totalDeTrasladosFED = 0;
                $tieneImpuestoNoLocal = false;
                $tieneImpuestoLocales = false;
                $IMPUESTOST = array();
                $IMPUESTOSR = array();
                foreach ($Impuestos as $Impuesto) {
                    if ($Impuesto['importe'] != '') {
                        if ($Impuesto['tasa'] >= 0) {

                            //traslado
                            if ($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "IEPS") {
                                $tieneImpuestoNoLocal = true;
                                switch ($type) {
                                    case 11:
                                        $totalDeTrasladosFED+= (($Impuesto['importe'] > 0) ? -1 * $Impuesto['importe'] : 0);
                                        if (isset($IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']])) {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']] = (-1 * $Impuesto['importe']);
                                        }
                                        break;

                                    default:
                                        if (isset($IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']])) {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']]+= ($Impuesto['importe']);
                                        } else {
                                            $IMPUESTOST[$Impuesto['impuesto'] . '-' . $Impuesto['tasa']] = ($Impuesto['importe']);
                                        }
                                        $totalDeTrasladosFED+= $Impuesto['importe'];
                                        break;
                                }
                            } else {
                                $tieneImpuestoLocales = true;
                                $TrasladoLoc = $cfdi->impuestosLocales->addTraslado();
                                $TrasladoLoc->setAtribute('ImpLocTrasladado', $Impuesto['impuesto']);
                                $TrasladoLoc->setAtribute('TasadeTraslado', $Impuesto['tasa']);
                                $TrasladoLoc->setAtribute('Importe', $Impuesto['importe']);
                                $totalDeTraslados+= $Impuesto['importe'];
                            }
                        } else {

                            //retencion
                            $Impuesto['tasa']*= - 1;
                            if ($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "ISR") {
                                $tieneImpuestoNoLocal = true;
                                switch ($type) {
                                    case 11:
                                        $totalDeRetencionesFED+= (-1 * $Impuesto['importe']);
                                        if (isset($IMPUESTOSR[$Impuesto['impuesto']])) {
                                            $IMPUESTOSR[$Impuesto['impuesto']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOSR[$Impuesto['impuesto']] = (-1 * $Impuesto['importe']);
                                        }
                                        break;

                                    default:
                                        if (isset($IMPUESTOSR[$Impuesto['impuesto']])) {
                                            $IMPUESTOSR[$Impuesto['impuesto']]+= (-1 * $Impuesto['importe']);
                                        } else {
                                            $IMPUESTOSR[$Impuesto['impuesto']] = (-1 * $Impuesto['importe']);
                                        }
                                        $totalDeRetencionesFED+= - 1 * $Impuesto['importe'];
                                        break;
                                }
                            } else {
                                $tieneImpuestoLocales = true;
                                $RetencionLoc = $cfdi->impuestosLocales->addRetencion();
                                $RetencionLoc->setAtribute('ImpLocRetenido', $Impuesto['impuesto']);
                                $RetencionLoc->setAtribute('TasadeRetencion', $Impuesto['tasa']);
                                $RetencionLoc->setAtribute('Importe', number_format($Impuesto['importe'], 2, '.', ''));
                                $totalDeRetenciones+= ($Impuesto['importe']);
                            }
                        }
                    }
                }

                foreach ($IMPUESTOST as $k => $v) {
                    $Aux = explode('-', $k);
                    $traslado = $cfdi->impuestos->addTraslado();
                    $traslado->setAtribute('tasa', $Aux[1]);
                    $traslado->setAtribute('importe', number_format($v, 4, '.', ''));
                    $traslado->setAtribute('impuesto', $Aux[0]);
                }

                foreach ($IMPUESTOSR as $k => $v) {
                    $retencion = $cfdi->impuestos->addRetencion();
                    $retencion->setAtribute('importe', number_format(-1 * $v, 4, '.', ''));
                    $retencion->setAtribute('impuesto', $k);
                }

                $cfdi->impuestos->setAtribute('totalImpuestosTrasladados', number_format($totalDeTrasladosFED, 4, '.', ''));
                $cfdi->impuestos->setAtribute('totalImpuestosRetenidos', number_format(-1 * $totalDeRetencionesFED, 4, '.', ''));

                if ($tieneImpuestoLocales) {
                    $cfdi->complementos = true;
                    $totalDeRetenciones = number_format($totalDeRetenciones, 2, '.', '');
                    $totalDeTraslados = number_format($totalDeTraslados, 2, '.', '');
                    $cfdi->impuestosLocales->setAtribute('version', '1.0');
                    $cfdi->impuestosLocales->setAtribute('TotaldeRetenciones', $totalDeRetenciones);
                    $cfdi->impuestosLocales->setAtribute('TotaldeTraslados', $totalDeTraslados);
                }

                

                //echo $noCertificado;
                $CSDFiles = getCSD($noCertificado, $db);

                //var_dump($CSDFiles);
                $cfdi->setCSD(realpath('XMLFacturacionElectronica/csdandkey/' . $CSDFiles[0]));
                $cfdi->setKEY(realpath("XMLFacturacionElectronica/csdandkey/" . $CSDFiles[1]));


                // echo realpath('XMLFacturacionElectronica/csdandkey/'.$CSDFiles[0]);
                //exit;
                $xml = $cfdi->getXML();
                $var['UUID'] = $cfdi->getUUID();
                $var['timbre'] = $cfdi->getTimbre();
                $var['timetimbre'] = $cfdi->getTimbreTime();
                $var['original'] = trim($cfdi->getCOriginalTFD());
                $var['sello'] = $cfdi->getSello();
                $var['noCertificado'] = $cfdi->getnoCertificadoTimbre();
                $cfdi->getQRCode($Emisor_RFC, $Receptor_rfc, $Comprobante_total);
                chdir(dirname(__FILE__));
                $Letras = new Numbers_Words();

                $tot = explode(".", number_format($Comprobante_total, 2, '.', ''));
                $Letra = Numbers_Words::toWords($tot[0], "es");
                if ($Comprobante_Moneda == 'MXN') {
                    $Comprobante_Moneda = 'M.N.';
                }
                if ($tot[1] == 0) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . " 00/100 " . $Comprobante_Moneda;
                } else if (strlen($tot[1]) >= 2) {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "/100 " . $Comprobante_Moneda;
                } else {
                    $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "0/100 " . $Comprobante_Moneda;
                }
                $ConLetra = strtoupper($ConLetra);

                /**
                 * Se agrega chdir, en algunas partes al generar el xml hace un cambio de directorio y fallaba con los cascos
                 * @author Rafael Rojas@realhost
                 *
                 */
                chdir(dirname(__FILE__));
                //printf('TIMBRADA1') ;
                $sqlInsert = "insert into rh_cfd__cfd(metodoPago,cuentapago,id_systypes, no_certificado, total_en_letra, cadena_original, sello,timbre,uuid, fecha, serie, xml, id_debtortrans, fk_transno, folio, tipo_de_comprobante,addenda_response)
                          values('" . $metodoPago . "',
                            '" . $ctaPago . "',$type,
                            '" . $var['noCertificado'] . "',
                            '" . $ConLetra . "',
                            '" . DB_escape_string($var['original']) . "',
                            '" . $var['sello'] . "',
                            '" . $var['timbre'] . "',
                            '" . $var['UUID'] . "',
                            '" . str_replace('T', ' ', $var['timetimbre']) . "',
                            '$serie',
                            '" . DB_escape_string($xml) . "',
                            $idDebtortrans,
                            $transno,
                            " . $FFOLIO . ",
                            " . (($tipoDeComprobante) ? "'" . DB_escape_string($tipoDeComprobante) . "'" : "null") . ",'1')";
                $result = DB_query($sqlInsert, $db, '', '', false, false);
                if (DB_error_no($db)) {
                    throw new Exception('No se pudieron insertar los datos del CFDI en la base de datos local, notifique error inmediatamente y suspenda operaciones hasta que se le avise ' . DB_error_msg($db) , 1);
                }
                //printf('TIMBRADA') ;

                if (!DB_query('commit', $db, '', '', false, false)) {
                    throw new Exception('Error al efectuar el commit, notifique error inmediatamente y suspenga operaciones hasta que se le avise', 1);
                }
                return $var;
            }
            catch(Exception $e) {
                if (!DB_query('rollback', $db, '', '', false, false)) {
                    $msg.= 'Error al efectuar el rollback';
                }

                //Para Facturacion Masiva, se necesita que continue aun en caso de Error,
                if ($Masiva) {
                    $ErrorMSG = "ID DebtorTrans: {$idDebtortrans} -> ERROR: " . $e->getMessage();
                    CrateErrorLog($ErrorMSG, $idDebtortrans);
                } else {
                    echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e->getMessage() . '<p></div>';
                    include ('includes/footer.inc');
                    exit;
                }
            }
        }
        catch(Exception $e) {
            if (!DB_query('rollback', $db, '', '', false, false)) {
                $msg.= 'Error al efectuar el rollback';
            }

            //Para Facturacion Masiva, se necesita que continue aun en caso de Error,
            if ($Masiva) {
                $ErrorMSG = "ID DebtorTrans: {$idDebtortrans} -> ERROR: " . $e->getMessage();
                CrateErrorLog($ErrorMSG, $idDebtortrans);
            } else {
                echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e->getMessage() . '<p></div>';
                include ('includes/footer.inc');
                exit;
            }
        }
    }