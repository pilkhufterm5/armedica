<?php

function libxml_display_error($error){
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";
    return $return;
}

function libxml_display_errors() {
    $Errors="";
    $errors = libxml_get_errors();
    foreach ($errors as $error) {
       $Errors .= libxml_display_error($error);
    }
    libxml_clear_errors();
    return $Errors;
}

function cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd=0, $xmlXsd=0,$metodoPago='No Identificado',$ctaPago='No Identificado') {
    $OnError=false;
    try {
        $tipoDeComprobante;
        switch($type){
            case 10:
                $tipoDeComprobante = 'ingreso';
            break;
            case 11:
                $tipoDeComprobante = 'egreso';
            break;
            case 20002:
                $cartaPorteTipoDeComprobante = $_POST['cartaPorteTipoDeComprobante'];
                if(!$cartaPorteTipoDeComprobante)
                    throw new Exception('No se escogio si la carta porte sera ingreso o traslado');
                $tipoDeComprobante = $cartaPorteTipoDeComprobante;
            break;
            default:
                throw new Exception('No se ha definido el tipo de comprobante (ingreso|egreso|traslado) para el tipo de transaccion ' . $type);
            break;
        }

        $sql = "select coyname Emisor_Nombre,gstno Emisor_RFC,regoffice1 Emisor_DomicilioFiscal_calle, regoffice2 Emisor_DomicilioFiscal_noExterior, regoffice3 Emisor_DomicilioFiscal_noInterior, regoffice4 Emisor_DomicilioFiscal_colonia, regoffice5 Emisor_DomicilioFiscal_localidad, regoffice6 Emisor_DomicilioFiscal_referencia, regoffice7 Emisor_DomicilioFiscal_municipio,  regoffice8 Emisor_DomicilioFiscal_estado,  regoffice9 Emisor_DomicilioFiscal_pais, regoffice10 Emisor_DomicilioFiscal_codigoPostal,regimen from companies limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
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
        $sql = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la ubicacion', 1);
        }
        $row = DB_fetch_array($result);
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
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
        }
        $row = DB_fetch_array($result);
        $currencyCode = $row['currencyCode'];
        $Comprobante_descuento = $row['Comprobante_descuento'];
        $Comprobante_total = $row['Comprobante_total'];
        $Comprobante_fecha = str_replace(' ', 'T', $row['Comprobante_fecha']);
        $Comprobante_subtotal = $row['Comprobante_subtotal'];
        $Traslado_importe = $row['Traslado_importe'];
        $Receptor_rfc = $row['Receptor_rfc'];
        $Receptor_nombre = $row['Receptor_nombre'];
        $Receptor_Domicilio_calle = $row['Receptor_calle'];
        $Receptor_Domicilio_noExterior = $row['Receptor_noExterior'];
        $Receptor_Domicilio_noInterior = $row['Receptor_noInterior'];
        $Receptor_Domicilio_colonia = $row['Receptor_colonia'];
        $Receptor_Domicilio_localidad = $row['Receptor_localidad'];
        $Receptor_Domicilio_referencia = $row['Receptor_referencia'];
        $Receptor_Domicilio_municipio = $row['Receptor_municipio'];
        $Receptor_Domicilio_estado = $row['Receptor_estado'];
        $Receptor_Domicilio_pais = $row['Receptor_pais'];
        $Receptor_Domicilio_codigoPostal = $row['Receptor_codigoPostal'];

        $sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad,stockmoves.stkmoveno Concepto_moveno FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";
         $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica', 1);
        }
        $Conceptos = Array();
        while($row = DB_fetch_array($result)) {
            $Concepto_noIdentificacion = $row['Concepto_noIdentificacion'];
            $Concepto_unidad = $row['Concepto_unidad'];
            $Concepto_cantidad = $row['Concepto_cantidad'];
            $Concepto_descripcion = $row['Concepto_descripcion'];
            $Concepto_valorUnitario = $row['Concepto_valorUnitario'];
            $Concepto_importe = $row['Concepto_importe'];
            $Concepto_moveno = $row['Concepto_moveno'];
            $sql='select rh_pedimento.nopedimento,rh_pedimento.fecha, rh_pedimento.aduana from stockpedimentomoves join rh_pedimento on stockpedimentomoves.pedimentoid=rh_pedimento.pedimentoid and stockpedimentomoves.stockmoveno='.$Concepto_moveno;
            $rs = DB_query($sql,$db,'','',false,false);
            if(DB_error_no($db)) {
                throw new Exception('No se pudo obtener los conceptos de la BD de la Factura Electronica', 1);
            }
            $orderlineno = $row['rh_orderline'];
            $pedimento = 0;
            $InformacionAduanera = Array();
            while($arrayAduana = DB_fetch_array($rs)){
                $Concepto_InformacionAduanera_numero = $arrayAduana['nopedimento'];
                $Concepto_InformacionAduanera_fecha = $arrayAduana['fecha'];
                $Concepto_InformacionAduanera_aduana = $arrayAduana['aduana'];
                $pedimento++;
                array_push($InformacionAduanera, Array('numero' => $Concepto_InformacionAduanera_numero, 'fecha' => $Concepto_InformacionAduanera_fecha, 'aduana' => $Concepto_InformacionAduanera_aduana));
            }
            array_push($Conceptos, Array('noIdentificacion' => $Concepto_noIdentificacion, 'unidad' => $Concepto_unidad, 'cantidad' => $Concepto_cantidad, 'descripcion' => $Concepto_descripcion, 'valorUnitario' => $Concepto_valorUnitario, 'importe' => $Concepto_importe, 'InformacionAduanera' => $InformacionAduanera));
        }

        $Impuestos = array();
        $Concepto_Impuestos = "select cast(((1 - s.discountpercent) * s.price * d.rate * -s.qty * abs(st.taxrate)) as decimal(10,2)) importe, t.description impuesto, st.taxrate*100 tasa from debtortrans d join stockmoves s on d.transno = s.transno and d.type = s.type join stockmovestaxes st on s.stkmoveno = st.stkmoveno join taxauthorities t on st.taxauthid = t.taxid where s.transno = $transno and s.type = $type";
        $result = DB_query($Concepto_Impuestos,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener los impuestos del concepto', 1);
        }
        while($Concepto_Impuestos = DB_fetch_array($result)) {
            $Impuesto = array('impuesto' => $Concepto_Impuestos['impuesto'], 'tasa'=>$Concepto_Impuestos['tasa'], 'importe'=>$Concepto_Impuestos['importe']);
            array_push($Impuestos, $Impuesto);
        }


        $sql = "select p.terms Comprobante_condicionesDePago, cast((1/dt.rate) as decimal(18,10)) exchangeRate, dm.currcode,curr.currency  from paymentterms p, debtorsmaster dm, debtortrans dt, currencies curr where p.termsindicator = dm.paymentterms and dm.debtorno = dt.debtorno and dt.type=$type and dt.transno = $transno and dm.currcode=curr.currabrev limit 1";

        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante (condiciones de pago) para la Factura Electronica', 1);
        }
        $row = mysql_fetch_array($result);
        $Comprobante_condicionesDePago = $row['Comprobante_condicionesDePago'];
        $Comprobante_TasaCambio = $row['exchangeRate'];
        $Comprobante_Moneda = $row['currcode'];
        $Comprobante_NombreMoneda = $row['currency'];


        try{
            $cfd22=new CFD22();
            $cfd22->comprobante->setAtribute('fecha','@TIME@');
            //$cfd22->comprobante->setAtribute('serie',$serie);
            $cfd22->comprobante->setAtribute('folio','@FOLIO@');
            $cfd22->comprobante->setAtribute('TipoCambio',number_format($Comprobante_TasaCambio,6,'.',''));
            $cfd22->comprobante->setAtribute('Moneda',$Comprobante_Moneda);
            if($type==10){
                $cfd22->comprobante->setAtribute('total',number_format($Comprobante_total,4,'.',''));
                $cfd22->comprobante->setAtribute('subTotal',number_format($Comprobante_subtotal,4,'.',''));
            }else{
                $cfd22->comprobante->setAtribute('total',number_format((-1*$Comprobante_total),4,'.',''));
                $cfd22->comprobante->setAtribute('subTotal',number_format((-1*$Comprobante_subtotal),4,'.',''));
            }
            $cfd22->comprobante->setAtribute('tipoDeComprobante',$tipoDeComprobante);
            $cfd22->comprobante->setAtribute('formaDePago','PAGO EN UNA SOLA EXHIBICION');
            $cfd22->comprobante->setAtribute('condicionesDePago',$idDebtortrans);
            $cfd22->comprobante->setAtribute('metodoDePago',$metodoPago);
            $cfd22->comprobante->setAtribute('NumCtaPago',$ctaPago);
            $cfd22->comprobante->setAtribute('LugarExpedicion',$Emisor_ExpedidoEn_municipio.', '.$Emisor_ExpedidoEn_estado);
            $cfd22->comprobante->setAtribute('noAprobacion','@NOAUTH@');
            $cfd22->comprobante->setAtribute('anoAprobacion','@ANOAUTH@');
            $cfd22->comprobante->setAtribute('noCertificado','@CERT@');
            $cfd22->comprobante->setAtribute('certificado','');
            $cfd22->comprobante->setAtribute('sello','');

            $cfd22->emisor->setAtribute('rfc',$Emisor_RFC);
            $cfd22->emisor->setAtribute('nombre',$Emisor_Nombre);
            $cfd22->emisor->domicilioFiscal->setAtribute('calle',$Emisor_DomicilioFiscal_calle);
            $cfd22->emisor->domicilioFiscal->setAtribute('noExterior',$Emisor_DomicilioFiscal_noExterior);
            $cfd22->emisor->domicilioFiscal->setAtribute('noInterior',$Emisor_DomicilioFiscal_noInterior);
            $cfd22->emisor->domicilioFiscal->setAtribute('colonia',$Emisor_DomicilioFiscal_colonia);
            $cfd22->emisor->domicilioFiscal->setAtribute('municipio',$Emisor_DomicilioFiscal_municipio);
            $cfd22->emisor->domicilioFiscal->setAtribute('estado',$Emisor_DomicilioFiscal_estado);
            $cfd22->emisor->domicilioFiscal->setAtribute('pais',$Emisor_DomicilioFiscal_pais);
            $cfd22->emisor->domicilioFiscal->setAtribute('codigoPostal',$Emisor_DomicilioFiscal_codigoPostal);
            $cfd22->emisor->expedidoEn->setAtribute('calle',$Emisor_ExpedidoEn_calle);
            $cfd22->emisor->expedidoEn->setAtribute('noExterior',$Emisor_ExpedidoEn_noExterior);
            $cfd22->emisor->expedidoEn->setAtribute('noInterior',$Emisor_ExpedidoEn_noInterior);
            $cfd22->emisor->expedidoEn->setAtribute('colonia',$Emisor_ExpedidoEn_colonia);
            $cfd22->emisor->expedidoEn->setAtribute('municipio',$Emisor_ExpedidoEn_municipio);
            $cfd22->emisor->expedidoEn->setAtribute('estado',$Emisor_ExpedidoEn_estado);
            $cfd22->emisor->expedidoEn->setAtribute('pais',$Emisor_ExpedidoEn_pais);
            $cfd22->emisor->expedidoEn->setAtribute('codigoPostal',$Emisor_ExpedidoEn_codigoPostal);
            $cfd22->emisor->regimenFiscal->setAtribute('Regimen',$RegimenFiscal);

            $cfd22->receptor->setAtribute('rfc',$Receptor_rfc);
            $cfd22->receptor->setAtribute('nombre',$Receptor_nombre);
            $cfd22->receptor->domicilio->setAtribute('calle',$Receptor_Domicilio_calle);
            $cfd22->receptor->domicilio->setAtribute('noExterior',$Receptor_Domicilio_noExterior);
            $cfd22->receptor->domicilio->setAtribute('noInterior',$Receptor_Domicilio_noInterior);
            $cfd22->receptor->domicilio->setAtribute('colonia',$Receptor_Domicilio_colonia);
            $cfd22->receptor->domicilio->setAtribute('municipio',$Receptor_Domicilio_municipio);
            $cfd22->receptor->domicilio->setAtribute('estado',$Receptor_Domicilio_estado);
            $cfd22->receptor->domicilio->setAtribute('codigoPostal',$Receptor_Domicilio_codigoPostal);
            $cfd22->receptor->domicilio->setAtribute('pais',$Receptor_Domicilio_pais);

            for($i = 0; $i < count($Conceptos); $i++){
            $Concepto = $Conceptos[$i];
            switch($type){
                case 11:
                    $Concep=$cfd22->conceptos->addConcepto();
                    $Concep->setAtribute('unidad',$Concepto['unidad']);
                    $Concep->setAtribute('cantidad',-1*$Concepto['cantidad']);
                    $Concep->setAtribute('descripcion',$Concepto['descripcion']);
                    $Concep->setAtribute('valorUnitario',number_format($Concepto['valorUnitario'],4,'.',''));
                    $Concep->setAtribute('importe',number_format(-1*$Concepto['importe'],4,'.',''));
                    break;
                default:
                    $Concep=$cfd22->conceptos->addConcepto();
                    $Concep->setAtribute('unidad',$Concepto['unidad']);
                    $Concep->setAtribute('cantidad',$Concepto['cantidad']);
                    $Concep->setAtribute('descripcion',$Concepto['descripcion']);
                    $Concep->setAtribute('valorUnitario',number_format($Concepto['valorUnitario'],4,'.',''));
                    $Concep->setAtribute('importe',number_format($Concepto['importe'],4,'.',''));
                    break;
            }
                foreach($Concepto['InformacionAduanera'] as $pedimento){
                    $InfoAduana = $Concep->addPedimento();
                    $InfoAduana->setAtribute('numero',$pedimento['numero']);
                    $InfoAduana->setAtribute('fecha',$pedimento['fecha']);
                    $InfoAduana->setAtribute('aduana',$pedimento['aduana']);
                }
            }

            $totalDeRetenciones = 0;
            $totalDeTraslados = 0;
            $totalDeRetencionesFED = 0;
            $totalDeTrasladosFED = 0;
            $tieneImpuestoNoLocal = false;
            $tieneImpuestoLocales = false;
            $IMPUESTOST = array();
            $IMPUESTOSR = array();
            foreach($Impuestos as $Impuesto){
                if($Impuesto['importe']!=''){
                    if($Impuesto['tasa']>=0){
                        if($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "IEPS"){
                            $tieneImpuestoNoLocal = true;
                            switch($type){
                                case 11:
                                    //$traslado=$cfd22->impuestos->addTraslado();
                                    //$traslado->setAtribute('tasa',$Impuesto['tasa']);
                                    //$traslado->setAtribute('importe',(($Impuesto['importe']>0)?-1*$Impuesto['importe']:0));
                                    $totalDeTrasladosFED+=(($Impuesto['importe']>0)?-1*$Impuesto['importe']:0);
                                    if(isset($IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']])){
                                         $IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']] += (-1*$Impuesto['importe']);
                                    }else{
                                         $IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']] = (-1*$Impuesto['importe']);
                                    }
                                    //$traslado->setAtribute('impuesto',$Impuesto['impuesto']);
                                    break;
                                default:
                                    if(isset($IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']])){
                                         $IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']] += ($Impuesto['importe']);
                                    }else{
                                         $IMPUESTOST[$Impuesto['impuesto'].'-'.$Impuesto['tasa']] = ($Impuesto['importe']);
                                    }
                                    //$traslado=$cfd22->impuestos->addTraslado();
                                    //$traslado->setAtribute('tasa',$Impuesto['tasa']);
                                    //$traslado->setAtribute('importe',$Impuesto['importe']);
                                    $totalDeTrasladosFED+=$Impuesto['importe'];
                                    //$traslado->setAtribute('impuesto',$Impuesto['impuesto']);
                                    break;
                            }
                        }else{
                            $tieneImpuestoLocales = true;
                            $TrasladoLoc=$cfd22->impuestosLocales->addTraslado();
                            $TrasladoLoc->setAtribute('ImpLocTrasladado',$Impuesto['impuesto']);
                            $TrasladoLoc->setAtribute('TasadeTraslado',$Impuesto['tasa']);
                            $TrasladoLoc->setAtribute('Importe',number_format($Impuesto['importe'],4,'.',''));
                            $totalDeTraslados += $Impuesto['importe'];
                        }
                    }else{
                        $Impuesto['tasa'] *= -1;
                        if($Impuesto['impuesto'] == "IVA" || $Impuesto['impuesto'] == "ISR"){
                            $tieneImpuestoNoLocal = true;
                            switch($type){
                                case 11:
                                    //$retencion=$cfd22->impuestos->addRetencion();
                                    //$retencion->setAtribute('importe',-1*$Impuesto['importe']);
                                    $totalDeRetencionesFED+=(-1*$Impuesto['importe']);
                                    //$retencion->setAtribute('impuesto',$Impuesto['impuesto']);
                                    if(isset($IMPUESTOSR[$Impuesto['impuesto']])){
                                         $IMPUESTOSR[$Impuesto['impuesto']] += (-1*$Impuesto['importe']);
                                    }else{
                                         $IMPUESTOSR[$Impuesto['impuesto']] = (-1*$Impuesto['importe']);
                                    }
                                    break;
                                default:
                                   // $retencion=$cfd22->impuestos->addRetencion();
                                    //$retencion->setAtribute('importe',-1*$Impuesto['importe']);
                                    //$retencion->setAtribute('impuesto',$Impuesto['impuesto']);
                                    if(isset($IMPUESTOSR[$Impuesto['impuesto']])){
                                         $IMPUESTOSR[$Impuesto['impuesto']] += (-1*$Impuesto['importe']);
                                    }else{
                                         $IMPUESTOSR[$Impuesto['impuesto']] = (-1*$Impuesto['importe']);
                                    }
                                     $totalDeRetencionesFED+=-1*$Impuesto['importe'];
                                    break;
                            }
                        }else{
                            $tieneImpuestoLocales = true;
                            $RetencionLoc=$cfd22->impuestosLocales->addRetencion();
                            $RetencionLoc->setAtribute('ImpLocRetenido',$Impuesto['impuesto']);
                            $RetencionLoc->setAtribute('TasadeRetencion',$Impuesto['tasa']);
                            $RetencionLoc->setAtribute('Importe',number_format($Impuesto['importe'],4,'.',''));
                            $totalDeRetenciones += $Impuesto['importe'];
                        }
                    }
                }
            }

            foreach ($IMPUESTOST as $k=>$v){
                 $Aux = explode('-',$k);
                 $traslado=$cfd22->impuestos->addTraslado();
                 $traslado->setAtribute('tasa',$Aux[1]);
                 $traslado->setAtribute('importe',number_format($v,4,'.',''));
                 $traslado->setAtribute('impuesto',$Aux[0]);
            }

            foreach ($IMPUESTOSR as $k=>$v){
                 $retencion=$cfd22->impuestos->addRetencion();
                 $retencion->setAtribute('importe',$v);
                 $retencion->setAtribute('impuesto',number_format($k,4,'.',''));
            }

            $cfd22->impuestos->setAtribute('totalImpuestosTrasladados',number_format($totalDeTrasladosFED, 4,'.',''));
            $cfd22->impuestos->setAtribute('totalImpuestosRetenidos',number_format($totalDeRetencionesFED, 4,'.',''));
            if($tieneImpuestoLocales){
                $cfd22->complementos=true;
                $totalDeRetenciones = number_format($totalDeRetenciones, 4,'.','');
                $totalDeTraslados = number_format($totalDeTraslados, 4,'.','');
                $cfd22->impuestosLocales->setAtribute('version','1.0');
                $cfd22->impuestosLocales->setAtribute('TotaldeRetenciones',$totalDeRetenciones);
                $cfd22->impuestosLocales->setAtribute('TotaldeTraslados',$totalDeTraslados);
            }

            $Letras = new Numbers_Words();
		    $tot = explode(".",number_format($Comprobante_total,2,'.',''));
		    $Letra = Numbers_Words::toWords($tot[0],"es");
            if($Comprobante_Moneda=='MXN'){
                $Comprobante_Moneda='M.N.';
            }
		    if($tot[1]==0){
		        $ConLetra = $Letra.' '.$Comprobante_NombreMoneda." 00/100 ".$Comprobante_Moneda;
		    }else if(strlen($tot[1])>=2){
		        $ConLetra = $Letra.' '.$Comprobante_NombreMoneda.' '.$tot[1]."/100 ".$Comprobante_Moneda;
		    }else {
		        $ConLetra = $Letra.' '.$Comprobante_NombreMoneda.' '.$tot[1]."0/100 ".$Comprobante_Moneda;
		    }
            $ConLetra= strtoupper($ConLetra);

            $CFDManager = CFD22Manager::getInstance();
            try{

                $CFDFinal = base64_decode($cfd22->getXML());
                $CFDFinal = str_replace('@FOLIO@','1',$CFDFinal);
                $CFDFinal = str_replace('@TIME@','2012-06-30T00:00:00',$CFDFinal);
                $CFDFinal = str_replace('@NOAUTH@','0',$CFDFinal);
                $CFDFinal = str_replace('@ANOAUTH@','0',$CFDFinal);
                $CFDFinal = str_replace('@CERT@','30000000000000000000',$CFDFinal);

                libxml_use_internal_errors(true);
                $xml = new DOMDocument();
                $xml->loadXML($CFDFinal);
                if (!$xml->schemaValidate('cfdv22.xsd')) {
                    $OnError=true;
                     throw new Exception(libxml_display_errors());
                }

                $sql="insert into rh_recoverxml values('".$idDebtortrans."','".$cfd22->getXML()."','".$serie."','".$idCsd."')";
                DB_query($sql,$db);
                if(!DB_query('commit',$db,'','',false,false)) {
                    $OnError=true;
                    throw new Exception('Error al efectuar el commit, Transaccion no efectuada.', 1);
                }
                try{
                    $MyXML= $CFDManager->SignXML($serie,$idCsd,$cfd22->getXML());
                }catch(Exception $e){
                    throw new Exception($e->getMessage());
                }
                $XMLSerie=$CFDManager->getXMLSerie();
                $XMLFolio=$CFDManager->getXMLFolio();
                $OString = $CFDManager->getCadena($XMLSerie,$XMLFolio);
                $NoAuth = $CFDManager->getXMLnoAuth();
                $AnoAuth = $CFDManager->getXMLanoAuth();
                $Sello = $CFDManager->getXMLSello();
                $Fecha = $CFDManager->getXMLFecha();
                $idWsCfd = 0;


                if(strpos($MyXML,'xsi:schemaLocation')===false){
                    $MyXML = str_replace('xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"', 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/2 http://www.sat.gob.mx/sitio_internet/cfd/2/cfdv22.xsd"',$MyXML);
                }

                if(!file_exists("XMLFacturacionElectronica/facturasElectronicas/$idCsd/"))
                    if(!mkdir("XMLFacturacionElectronica/facturasElectronicas/$idCsd"))
                        throw new Exception('No se pudo crear el directorio donde se guardara el CFD');

                $filename = "XMLFacturacionElectronica/facturasElectronicas/$idCsd/$XMLSerie$XMLFolio-$transno.xml";
                File::createFile($MyXML, $filename);

                $sqlInsert = "insert into rh_cfd__cfd(metodopago,cuentapago,id_systypes, id_ws_cfd, no_certificado, ano_aprobacion, no_aprobacion, total_en_letra, cadena_original, sello, fecha, serie, xml, id_debtortrans, fk_transno, folio, addenda_response, tipo_de_comprobante)
                          values('".$metodoPago."','".$ctaPago."',$type, $idWsCfd, '$idCsd', $AnoAuth, $NoAuth, '$ConLetra', '".mysql_real_escape_string($OString)."', '$Sello', '$Fecha', '$XMLSerie', '".mysql_real_escape_string($MyXML)."', $idDebtortrans, $transno, " . (v($XMLFolio)?"'$XMLFolio'":"null") . ", " . (v($addendaResponse)?("'".mysql_real_escape_string($addendaResponse)."'"):"null") . ', ' . (v($tipoDeComprobante)?"'$tipoDeComprobante'":"null") . ")";

                $result = DB_query($sqlInsert,$db);
                if(DB_error_no($db)){
                    throw new Exception('No se pudieron insertar los datos del CFD en la base de datos local.');
                }
                if(!DB_query('commit',$db,'','',false,false)) {
                    throw new Exception('Error al efectuar el commit, Transaccion no efectuada.', 1);
                }
                return Array('idWsCfd' => $idWsCfd, 'noCertificado' => $idCsd, 'serie' => $XMLSerie, 'folio' => $XMLFolio, 'transno' => $transno, 'cadenaOriginal' => $OString, 'selloDigital' => $Sello, 'totalEnLetra' => $ConLetra, 'noAprobacion' => $NoAuth, 'anoAprobacion' => $AnoAuth, 'addendaResponse' => $addendaResponse);
            }catch(Exception $e){
                throw new Exception($e->getMessage());
            }

        }catch(Exception $e){
           throw new Exception($e->getMessage());
        }
    }
    catch(Exception $exception) {
        $msg = $exception->getMessage();
        if($exception->getCode()==1) {
            $error = mysql_error();
            $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
        }
        if(!DB_query('rollback',$db,'','',false,false)) {
            $msg .= 'Error al efectuar el rollback';
        }
        echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $msg . '<p></div>';
        if(!$OnError){
            echo "<br /><br /><center><b>La Factura no pudo ser registrada en su sistema satifactoriamente,<a href='rh_recoverxml.php?id=".$idDebtortrans."'> de clic aqu&iacute; para recuperar</a></b></center>";
        }
        include('includes/footer.inc');
        exit;
    }
}

function printDatosSat($datosSat){
        $idWsCfd = $datosSat['idWsCfd'];
        $noCertificado = $datosSat['noCertificado'];
        $serie = $datosSat['serie'];
        $folio = $datosSat['folio'];
        $cadenaOriginal = $datosSat['cadenaOriginal'];
        $cadenaOriginal = str_replace(" ", "&nbsp;", $cadenaOriginal);
        //chars per line
        $cpl = 180;
        //cadena original length
        $col = strlen($cadenaOriginal);
        if($col>$cpl){
            $cadenaOriginalFormatted = '';
            for($i = 0; $i < $col; $i+=$cpl)
                $cadenaOriginalFormatted.=substr($cadenaOriginal, $i, $cpl) . "\n";
            $cadenaOriginal = $cadenaOriginalFormatted;
        }
        $selloDigital = $datosSat['selloDigital'];
        $totalEnLetra = $datosSat['totalEnLetra'];
        $noAprobacion = $datosSat['noAprobacion'];
        $anoAprobacion = $datosSat['anoAprobacion'];
        $addendaResponse = $datosSat['addendaResponse'];

        echo '<table><tr><td bgcolor="#bbbbbb"><b>ID CFD (WS):</b></td></tr><tr><td>' . $idWsCfd . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>No Aprobacion:</b></td></tr><tr><td>' . $noAprobacion . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Año Aprobacion:</b></td></tr><tr><td>' . $anoAprobacion . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Serie:</b></td></tr><tr><td>' . $serie . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Folio:</b></td></tr><tr><td>' . $folio . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>No Certificado: </b></td></tr><tr><td>' . $noCertificado . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Cadena Original:</b></td></tr><tr><td>' . $cadenaOriginal . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Sello Digital:</b></td></tr><tr><td>' . $selloDigital . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Cantidad con Letra:</b></td></tr><tr><td>' . $totalEnLetra . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Respuesta de la addenda:</b></td></tr><tr><td>' . t($addendaResponse) . '</td></tr></table>';
}

function cancelCfd($db, $idDebtortrans){
    //Realhost, Jaime 15 Abril 2010 17:14, se marca como cancelada el CFD en la tabla rh_factura_electronica_reporte_mensual_sat para que se incluya en el reporte mensual
    $sqlQuery = "select id_ws_cfd from rh_cfd__cfd where id_debtortrans = $idDebtortrans limit 1";
    $result = DB_query($sqlQuery,$db,'','',false,false);
    if(DB_error_no($db)) {
        throw new Exception("No se pudo obtener la informacion necesaria para cancelar el CFD");
    }
    $row = DB_fetch_row($result);
    if((!is_null($row[0]))&&(strlen($row[0])>0)){
        getWs()->cancelCfd(Array('idCfd' => $row[0]));
    }
    //Termina Realhost, Jaime 15 Abril 2010 17:14, se marca como cancelada el CFD en la tabla rh_factura_electronica_reporte_mensual_sat para que se incluya en el reporte mensual
}

function v($text){
    if(isSet($text) && trim($text)!='')
        return true;
    else
        return false;
}

function t($t){
    return str_replace('<', '&lt;', str_replace('<', '&lt;', str_replace('>', '&gt;', $t)));
}
?>
