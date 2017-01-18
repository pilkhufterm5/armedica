<?php
function cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd) {
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
//        if(!DB_query('begin',$db,'','',false,false)) {
//            throw new Exception('Error al efectuar el begin' , 1);
//        }

        //Uso:
        //Emisor
        //companies
        $sql = "select regoffice1 Emisor_DomicilioFiscal_calle, regoffice2 Emisor_DomicilioFiscal_noExterior, regoffice3 Emisor_DomicilioFiscal_noInterior, regoffice4 Emisor_DomicilioFiscal_colonia, regoffice5 Emisor_DomicilioFiscal_localidad, regoffice6 Emisor_DomicilioFiscal_referencia, regoffice7 Emisor_DomicilioFiscal_municipio,  regoffice8 Emisor_DomicilioFiscal_estado,  regoffice9 Emisor_DomicilioFiscal_pais, regoffice10 Emisor_DomicilioFiscal_codigoPostal from companies limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
        }
        $row = DB_fetch_array($result);
        $Emisor_DomicilioFiscal_calle = $row['Emisor_DomicilioFiscal_calle'];//1
        //$Emisor_DomicilioFiscal_calle = "     á, é, í, ó, ú,  á, é, í, ó, ú, á, é, í, ó, ú,      ᴹḼⁿỜⱣｉṟṲá, é, í, ó, ú, Ṩṭᴆဘံ္ Ju ||| <>?:|  }{   | !@#$^%&%$#@!&*($&#@  an & Jo||JKKHPFO{!$@! sé & “Niño”";
        $Emisor_DomicilioFiscal_noExterior = $row['Emisor_DomicilioFiscal_noExterior'];//2
        $Emisor_DomicilioFiscal_noInterior = $row['Emisor_DomicilioFiscal_noInterior'];//3
        $Emisor_DomicilioFiscal_colonia = $row['Emisor_DomicilioFiscal_colonia'];//4
        $Emisor_DomicilioFiscal_localidad = $row['Emisor_DomicilioFiscal_localidad'];//5
        $Emisor_DomicilioFiscal_referencia = $row['Emisor_DomicilioFiscal_referencia'];//6
        $Emisor_DomicilioFiscal_municipio = $row['Emisor_DomicilioFiscal_municipio'];//7
        $Emisor_DomicilioFiscal_estado = $row['Emisor_DomicilioFiscal_estado'];//8
        $Emisor_DomicilioFiscal_pais = $row['Emisor_DomicilioFiscal_pais'];//9
        $Emisor_DomicilioFiscal_codigoPostal = $row['Emisor_DomicilioFiscal_codigoPostal'];//10
        //locations
        $sql = "select deladd1 Emisor_ExpedidoEn_calle, deladd2 Emisor_ExpedidoEn_noExterior, deladd3 Emisor_ExpedidoEn_noInterior, deladd4 Emisor_ExpedidoEn_colonia, deladd5 Emisor_ExpedidoEn_localidad, deladd6 Emisor_ExpedidoEn_referencia, deladd7 Emisor_ExpedidoEn_municipio,  deladd8 Emisor_ExpedidoEn_estado,  deladd9 Emisor_ExpedidoEn_pais, deladd10 Emisor_ExpedidoEn_codigoPostal from locations where loccode = '" . $_SESSION['UserStockLocation'] . "' limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la ubicacion', 1);
        }
        $row = DB_fetch_array($result);
        $Emisor_ExpedidoEn_calle = $row['Emisor_ExpedidoEn_calle'];//1
        $Emisor_ExpedidoEn_noExterior = $row['Emisor_ExpedidoEn_noExterior'];//2
        $Emisor_ExpedidoEn_noInterior = $row['Emisor_ExpedidoEn_noInterior'];//3
        $Emisor_ExpedidoEn_colonia = $row['Emisor_ExpedidoEn_colonia'];//4
        $Emisor_ExpedidoEn_localidad = $row['Emisor_ExpedidoEn_localidad'];//5
        $Emisor_ExpedidoEn_referencia = $row['Emisor_ExpedidoEn_referencia'];//6
        $Emisor_ExpedidoEn_municipio = $row['Emisor_ExpedidoEn_municipio'];//7
        $Emisor_ExpedidoEn_estado = $row['Emisor_ExpedidoEn_estado'];//8
        $Emisor_ExpedidoEn_pais = $row['Emisor_ExpedidoEn_pais'];//9
        $Emisor_ExpedidoEn_codigoPostal = $row['Emisor_ExpedidoEn_codigoPostal'];//10

        //sql Comprobante e Impuestos
        //Jaime (tipo de cambio), agregado el campo currencyCode para obtener el codigo de currency
        $sql = "select rh_transaddress.currcode currencyCode, cast(debtortrans.ovdiscount as decimal(10,2)) Comprobante_descuento, cast((((debtortrans.ovamount-debtortrans.ovdiscount)+debtortrans.ovgst)/1) as decimal(10,2)) Comprobante_total, debtortrans.rh_createdate Comprobante_fecha, cast((debtortrans.ovamount/1) as decimal(10,2)) Comprobante_subtotal, cast((debtortrans.ovgst/1) as decimal(10,2)) Traslado_importe, rh_transaddress.taxref Receptor_rfc, rh_transaddress.name Receptor_nombre, rh_transaddress.address1 Receptor_calle, rh_transaddress.address2 Receptor_noExterior, rh_transaddress.address3 Receptor_noInterior, rh_transaddress.address4 Receptor_colonia, rh_transaddress.address5 Receptor_localidad, rh_transaddress.address6 Receptor_referencia, rh_transaddress.address7 Receptor_municipio,  rh_transaddress.address8 Receptor_estado,  rh_transaddress.address9 Receptor_pais, rh_transaddress.address10 Receptor_codigoPostal FROM debtortrans, rh_transaddress WHERE rh_transaddress.type = $type AND debtortrans.type=$type and rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=$transno limit 1";
        //Termina Jaime (tipo de cambio), agregado el campo currencyCode para obtener el codigo de currency
        //$result = mysql_query($sql);
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
        }
        //$row = mysql_fetch_array($result, MYSQLI_ASSOC);
        $row = DB_fetch_array($result);
        //Jaime (tipo de cambio), se crea la variable currencyCode
        $currencyCode = $row['currencyCode'];
        //Termina Jaime (tipo de cambio), se crea la variable currencyCode
        $Comprobante_descuento = $row['Comprobante_descuento'];
        $Comprobante_total = $row['Comprobante_total'];
        $Comprobante_fecha = str_replace(' ', 'T', $row['Comprobante_fecha']);
        $Comprobante_subtotal = $row['Comprobante_subtotal'];
        $Traslado_importe = $row['Traslado_importe'];
        //Receptor
        $Receptor_rfc = $row['Receptor_rfc'];
        $Receptor_nombre = $row['Receptor_nombre'];//'á, é, í, ó, ú ᴹḼⁿỜ "&^<>/\\\'';
        $Receptor_Domicilio_calle = $row['Receptor_calle'];//1
        $Receptor_Domicilio_noExterior = $row['Receptor_noExterior'];//2
        $Receptor_Domicilio_noInterior = $row['Receptor_noInterior'];//3
        $Receptor_Domicilio_colonia = $row['Receptor_colonia'];//4
        $Receptor_Domicilio_localidad = $row['Receptor_localidad'];//5
        $Receptor_Domicilio_referencia = $row['Receptor_referencia'];//6
        $Receptor_Domicilio_municipio = $row['Receptor_municipio'];//7
        $Receptor_Domicilio_estado = $row['Receptor_estado'];//8
        $Receptor_Domicilio_pais = $row['Receptor_pais'];//9
        $Receptor_Domicilio_codigoPostal = $row['Receptor_codigoPostal'];//10

        //Conceptos
        //sin redondeo a 2 decimales (4 decimales por default)
        //$sql = "SELECT stockmoves.price Concepto_valorUnitario, stockmaster.description Concepto_descripcion, -stockmoves.qty Concepto_cantidad, stockmaster.units Concepto_unidad, (-stockmoves.qty * stockmoves.price) as Concepto_importe FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //con redondeo a 2 decimales
        //$sql = "SELECT cast(price as decimal(10,2)) Concepto_valorUnitario, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, stockmaster.units Concepto_unidad, cast((-stockmoves.qty * stockmoves.price) as decimal(10,2)) as Concepto_importe FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //$sql = "select cast((unitprice*(1-discountpercent)) as decimal(10,2)) Concepto_valorUnitario, description Concepto_descripcion, quantity Concepto_cantidad, cast((unitprice*(1-discountpercent)*quantity) as decimal(10,2)) Concepto_importe, stkcode, orderlineno from salesorderdetails where orderno = " . $_SESSION['ProcessingOrder'];
        //Jaime (redondeo), precios redondeados a 2 decimales
        //$sql = "SELECT stockmoves.rh_orderline, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty Concepto_cantidad as decimal(10,2)), cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price as decimal(10,2)) * (-stockmoves.qty)) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent)) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=10 AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //@nextline en pesos
        //$sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent)) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1";
        //@nextline en moneda del cliente
        $sql = "SELECT stockmoves.rh_orderline, stockmaster.stockid Concepto_noIdentificacion, stockmaster.description Concepto_descripcion, cast(-stockmoves.qty as decimal(10,2)) Concepto_cantidad, cast(((1 - stockmoves.discountpercent) * cast(stockmoves.price*debtortrans.rate as decimal(10,2)) * cast(-stockmoves.qty as decimal(10,2))) as decimal(10,2)) Concepto_importe, cast((stockmoves.price * (1 - stockmoves.discountpercent) * debtortrans.rate) as decimal(10,2)) Concepto_valorUnitario, stockmaster.units Concepto_unidad,stockmoves.stkmoveno Concepto_moveno FROM stockmoves, stockmaster, debtortrans WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=$type AND stockmoves.transno=$transno AND stockmoves.show_on_inv_crds=1 and stockmoves.transno = debtortrans.transno and stockmoves.type = debtortrans.type";
        //Termina Jaime (redondeo), precios redondeados a 2 decimales
        //$result = mysql_query($sql);
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

        //Impuestos por concepto
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
        //\Impuestos por concepto

        //Comprobante
        //Jaime (tipo de cambio), agregado el campo exchangeRate
        $sql = "select p.terms Comprobante_condicionesDePago, cast((1/dt.rate) as decimal(18,10)) exchangeRate  from paymentterms p, debtorsmaster dm, debtortrans dt where p.termsindicator = dm.paymentterms and dm.debtorno = dt.debtorno and dt.type=$type and dt.transno = $transno limit 1";
        //Termina Jaime (tipo de cambio), agregado el campo exchangeRate
        $result = DB_query($sql,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante (condiciones de pago) para la Factura Electronica', 1);
        }
        $row = mysql_fetch_array($result);
        $Comprobante_condicionesDePago = $row['Comprobante_condicionesDePago'];
        //Jaime (tipo de cambio), obtenemos exchangeRate
        $exchangeRate = $row['exchangeRate'];
        //Termina Jaime (tipo de cambio), obtenemos exchangeRate
        $ws = getWs();
        //ignorar este comentario -> //Jaime (redondeo) @oneLine, se corto la linea que setea el comprobante en el Web service de manera que primero se calcule correctamente el subtotal y total y luego ya se settie mas adelante
        switch($type){
            case 11:
                $ws->setComprobante(array('fecha' => (v($Comprobante_fecha)?$Comprobante_fecha:null), 'formaDePago' => 'PAGO EN UNA SOLA EXHIBICION', 'condicionesDePago' => (v($Comprobante_condicionesDePago)?$Comprobante_condicionesDePago:null), 'subTotal' => (v($Comprobante_subtotal)?-1*$Comprobante_subtotal:null), 'descuento' => (v($Comprobante_descuento)?$Comprobante_descuento:null), 'total' => (v($Comprobante_total)?-1*$Comprobante_total:null), 'tipoDeComprobante' => $tipoDeComprobante));
                break;
            default:
                $ws->setComprobante(array('fecha' => (v($Comprobante_fecha)?$Comprobante_fecha:null), 'formaDePago' => 'PAGO EN UNA SOLA EXHIBICION', 'condicionesDePago' => (v($Comprobante_condicionesDePago)?$Comprobante_condicionesDePago:null), 'subTotal' => (v($Comprobante_subtotal)?$Comprobante_subtotal:null), 'descuento' => (v($Comprobante_descuento)?$Comprobante_descuento:null), 'total' => (v($Comprobante_total)?$Comprobante_total:null), 'tipoDeComprobante' => $tipoDeComprobante));
                break;
        }
        $ws->setEmisor_DomicilioFiscal(array('calle' => (v($Emisor_DomicilioFiscal_calle)?$Emisor_DomicilioFiscal_calle:null),'codigoPostal' => (v($Emisor_DomicilioFiscal_codigoPostal)?$Emisor_DomicilioFiscal_codigoPostal:null),'colonia' => (v($Emisor_DomicilioFiscal_colonia)?$Emisor_DomicilioFiscal_colonia:null),'estado' => (v($Emisor_DomicilioFiscal_estado)?$Emisor_DomicilioFiscal_estado:null),'localidad' => (v($Emisor_DomicilioFiscal_localidad)?$Emisor_DomicilioFiscal_localidad:null),'municipio' => (v($Emisor_DomicilioFiscal_municipio)?$Emisor_DomicilioFiscal_municipio:null),'noExterior' => (v($Emisor_DomicilioFiscal_noExterior)?$Emisor_DomicilioFiscal_noExterior:null),'noInterior' => (v($Emisor_DomicilioFiscal_noInterior)?$Emisor_DomicilioFiscal_noInterior:null),'pais' => (v($Emisor_DomicilioFiscal_pais)?$Emisor_DomicilioFiscal_pais:null), 'referencia' => (v($Emisor_DomicilioFiscal_referencia)?$Emisor_DomicilioFiscal_referencia:null)));
        $ws->setEmisor_ExpedidoEn(array('calle' => (v($Emisor_ExpedidoEn_calle)?$Emisor_ExpedidoEn_calle:null),'codigoPostal' => (v($Emisor_ExpedidoEn_codigoPostal)?$Emisor_ExpedidoEn_codigoPostal:null),'colonia' => (v($Emisor_ExpedidoEn_colonia)?$Emisor_ExpedidoEn_colonia:null),'estado' => (v($Emisor_ExpedidoEn_estado)?$Emisor_ExpedidoEn_estado:null),'localidad' => (v($Emisor_ExpedidoEn_localidad)?$Emisor_ExpedidoEn_localidad:null),'municipio' => (v($Emisor_ExpedidoEn_municipio)?$Emisor_ExpedidoEn_municipio:null),'noExterior' => (v($Emisor_ExpedidoEn_noExterior)?$Emisor_ExpedidoEn_noExterior:null),'noInterior' => (v($Emisor_ExpedidoEn_noInterior)?$Emisor_ExpedidoEn_noInterior:null),'pais' => (v($Emisor_ExpedidoEn_pais)?$Emisor_ExpedidoEn_pais:null), 'referencia' => (v($Emisor_ExpedidoEn_referencia)?$Emisor_ExpedidoEn_referencia:null)));
        $ws->setReceptor(array('rfc' => (v($Receptor_rfc)?$Receptor_rfc:null), 'nombre' => (v($Receptor_nombre)?str_replace("\\'",'&apos;',$Receptor_nombre):null)));
        $ws->setReceptor_Domicilio(array('calle' => (v($Receptor_Domicilio_calle)?$Receptor_Domicilio_calle:null),'codigoPostal' => (v($Receptor_Domicilio_codigoPostal)?$Receptor_Domicilio_codigoPostal:null),'colonia' => (v($Receptor_Domicilio_colonia)?$Receptor_Domicilio_colonia:null),'estado' => (v($Receptor_Domicilio_estado)?$Receptor_Domicilio_estado:null),'localidad' => (v($Receptor_Domicilio_localidad)?$Receptor_Domicilio_localidad:null),'municipio' => (v($Receptor_Domicilio_municipio)?$Receptor_Domicilio_municipio:null),'noExterior' => (v($Receptor_Domicilio_noExterior)?$Receptor_Domicilio_noExterior:null),'noInterior' => (v($Receptor_Domicilio_noInterior)?$Receptor_Domicilio_noInterior:null),'pais' => (v($Receptor_Domicilio_pais)?$Receptor_Domicilio_pais:null), 'referencia' => (v($Receptor_Domicilio_referencia)?$Receptor_Domicilio_referencia:null)));
        for($i = 0; $i < count($Conceptos); $i++){
            $Concepto = $Conceptos[$i];
            switch($type){
                case 11:
                    $ws->addConceptos_Concepto(array('cantidad' => (v($Concepto['cantidad'])?-1*$Concepto['cantidad']:null),'unidad' => (v($Concepto['unidad'])?$Concepto['unidad']:null),'descripcion' => (v($Concepto['descripcion'])?$Concepto['descripcion']:null),'valorUnitario' => (v($Concepto['valorUnitario'])?$Concepto['valorUnitario']:null), 'importe' => (v($Concepto['importe'])?-1*$Concepto['importe']:null)));
                    break;
                default:
                    $ws->addConceptos_Concepto(array('cantidad' => (v($Concepto['cantidad'])?$Concepto['cantidad']:null),'unidad' => (v($Concepto['unidad'])?$Concepto['unidad']:null),'descripcion' => (v($Concepto['descripcion'])?$Concepto['descripcion']:null),'valorUnitario' => (v($Concepto['valorUnitario'])?$Concepto['valorUnitario']:null), 'importe' => (v($Concepto['importe'])?$Concepto['importe']:null)));
                    break;
            }
            foreach($Concepto['InformacionAduanera'] as $ia){
                $ws->addConceptos_Concepto_InformacionAduanera(array('aduana' => $ia['aduana'], 'fecha' => $ia['fecha'], 'numero' => $ia['numero']));
            }
        }
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
                                $ws->addImpuestos_Traslados_Traslado(array('importe' => (v($Impuesto['importe'])?-1*$Impuesto['importe']:null), 'impuesto' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null), 'tasa' => (v($Impuesto['tasa'])?$Impuesto['tasa']:null)));
                                break;
                            default:
                                $ws->addImpuestos_Traslados_Traslado(array('importe' => (v($Impuesto['importe'])?$Impuesto['importe']:null), 'impuesto' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null), 'tasa' => (v($Impuesto['tasa'])?$Impuesto['tasa']:null)));
                                break;
                        }
                    }
                    else{
                        $tieneImpuestoLocales = true;
                        //aki va impuesto local
                        $ws->addComplemento_ImpuestosLocales_TrasladosLocales(array('impLocTrasladado' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null), 'importe' => (v($Impuesto['importe'])?$Impuesto['importe']:null), 'tasadeTraslado' => (v($Impuesto['tasa'])?$Impuesto['tasa']:null)));
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
                                $ws->addImpuestos_Retenciones_Retencion(array('importe' => (v($Impuesto['importe'])?-1*$Impuesto['importe']:null), 'impuesto' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null)));
                                break;
                            default:
                                $ws->addImpuestos_Retenciones_Retencion(array('importe' => (v($Impuesto['importe'])?$Impuesto['importe']:null), 'impuesto' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null)));
                                break;
                        }
                    }
                    else{
                        $tieneImpuestoLocales = true;
                        //aki va impuesto local
                        //$ws->addComplemento_ImpuestosLocales_RetencionesLocales(array('impLocRetenido' => (v($Impuesto['impuesto'])?$Impuesto['impuesto']:null), 'importe' => (v($Impuesto['importe'])?$Impuesto['importe']:null), 'tasaDeRetencion' => (v($Impuesto['tasa'])?$Impuesto['tasa']:null)));
                        $totalDeRetenciones += $Impuesto['importe'];
                    }
                }
            }
        }
        if(!$tieneImpuestoNoLocal){
            $ws->setImpuestos();
        }
        if($tieneImpuestoLocales){
            $totalDeRetenciones = number_format($totalDeRetenciones, 2);
            $totalDeTraslados = number_format($totalDeTraslados, 2);
            //$ws->setComplemento_ImpuestosLocales(array('totalDeRetenciones'=>$totalDeRetenciones, 'totalDeTraslados'=>$totalDeTraslados));
        }

        $ws->setSerieFolio(array('serieFolio' => $serie));
        $ws->setIdCsd(array('idCsd' => (v($idCsd)?$idCsd:null)));
        //Jaime Addenda
        if (v($idXsd)) {
            switch($idXsd){
                case 1:
                    //@todo cambiar de manera que estemos seguros que es la addenda soriana, que envie el nombre tambien?
                    $addendaSoriana_fechaRemision = $_POST['addendaSoriana_fechaRemision'];
                    if(!v($addendaSoriana_fechaRemision))
                        throw new Exception("La Fecha De Remision para la addenda soriana es obligatoria");
                    if(!preg_match("/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/", $addendaSoriana_fechaRemision))
                        throw new Exception("La Fecha De Remision no es valida");
                    $addendaSoriana_folioNotaEntrada = $_POST['addendaSoriana_folioNotaEntrada'];
                    if(!v($addendaSoriana_folioNotaEntrada))
                        throw new Exception("El Folio De Nota de entrada para la addenda soriana es obligatorio");
                    $addendaSoriana_numeroDeCajas = $_POST['addendaSoriana_numeroDeCajas'];
                    if(!v($addendaSoriana_numeroDeCajas))
                        throw new Exception("El Numero De Cajas para la addenda soriana es obligatorio");
                    $sql = "select branchcode from debtortrans where transno = $transno and type = $type";
                    $result = DB_query($sql,$db,'','',false,false);
                    if(DB_num_rows($result) != 1)
                        throw new Exception("No se pudo obtener informacion para la addenda soriana");
                    $row = DB_fetch_array($result);
                    $addendaSoriana_tienda = $row['branchcode'];
                    include('rh_j_addendas.php');
                    $siguienteFolio = $ws->getSiguienteFolio(array('serie' => $serie))->return;
                    $addendaSoriana_proveedor = 93211;
                    $xmlXsd = Addendas::soriana($idDebtortrans, $serie, $siguienteFolio, $Comprobante_subtotal, $Comprobante_total, $addendaSoriana_proveedor, $addendaSoriana_fechaRemision, $addendaSoriana_folioNotaEntrada, $addendaSoriana_numeroDeCajas, $addendaSoriana_tienda, $Conceptos);
                    //para ver el xml enviado como addenda descomentarizar la siguiente linea, deberia ocurrir rollback (como si nada paso)
                    //throw new Exception(t($xmlXsd));
                break;
                case 2:
                    //Carta Porte
                break;
                default:
                    throw new Exception("Esta addenda aun no se soporta");
                break;
            }
            $ws->setIdXsd(array('idXsd' => $idXsd));
            $ws->setXmlAddenda(array('xmlAddenda' => $xmlXsd));
        }
        //throw new Exception(t($idXsd . '-' . $xmlXsd));
        //Termina Jaime Addenda

        //Jaime (tipo de cambio), invocamos la operacion setCurrency
        if(v($currencyCode) && $currencyCode != 'MXN')
            $ws->setCurrency(array('name' => $currencyCode, 'exchangeRate' => $exchangeRate));
        //Termina Jaime (tipo de cambio), invocamos la operacion setCurrency
        $idWsCfd = $ws->end()->return;
        if(!DB_query('commit',$db,'','',false,false)) {
            throw new Exception('Error al efectuar el commit, notifique error inmediatamente y suspenga operaciones hasta que se le avise', 1);
        }
        try{
            $xml = $ws->xml()->return;
            $cadenaOriginal = $ws->cadenaOriginal()->return;
            $selloDigital = $ws->sello()->return;
            $totalEnLetra = $ws->getTotalEnLetra()->return;
            $satData = $ws->getSatData()->return;
            $addendaResponse = $ws->getAddendaResponse()->return;
            for($i = 0; $i < count($satData[0]->item); $i++){
                switch($satData[0]->item[$i]){
                    case 'noAprobacion':
                        $noAprobacion = $satData[1]->item[$i];
                    break;
                    case 'anoAprobacion':
                        $anoAprobacion = $satData[1]->item[$i];
                    break;
                    case 'serie':
                        $serie = $satData[1]->item[$i];
                    break;
                    case 'folio':
                        $folio = $satData[1]->item[$i];
                    break;
                    case 'noCertificado':
                        $noCertificado = $satData[1]->item[$i];
                    break;
                }
            }

            if(!file_exists("XMLFacturacionElectronica/facturasElectronicas/$noCertificado/"))
                if(!mkdir("XMLFacturacionElectronica/facturasElectronicas/$noCertificado"))
                    throw new Exception('No se pudo crear el directorio donde se guardara el CFD, notifique error inmediatamente y suspenga operaciones hasta que se le avise');

            //debe guardarse el type tambien
            $filename = "XMLFacturacionElectronica/facturasElectronicas/$noCertificado/$serie$folio-$transno.xml";
            File::createFile($xml, $filename);

            $sqlInsert = "insert into rh_cfd__cfd(id_systypes, id_ws_cfd, no_certificado, ano_aprobacion, no_aprobacion, total_en_letra, cadena_original, sello, fecha, serie, xml, id_debtortrans, fk_transno, folio, addenda_response, tipo_de_comprobante)
                          values($type, $idWsCfd, '$noCertificado', $anoAprobacion, $noAprobacion, '$totalEnLetra', '".mysql_real_escape_string($cadenaOriginal)."', '$selloDigital', '" . str_replace('T', ' ', $Comprobante_fecha) . "', '$serie', '".mysql_real_escape_string($xml)."', $idDebtortrans, $transno, " . (v($folio)?"'$folio'":"null") . ", " . (v($addendaResponse)?("'".mysql_real_escape_string($addendaResponse)."'"):"null") . ', ' . (v($tipoDeComprobante)?"'$tipoDeComprobante'":"null") . ")";
            $result = DB_query($sqlInsert,$db,'','',false,false);
            if(DB_error_no($db)){
                throw new Exception('No se pudieron insertar los datos del CFD en la base de datos local, notifique error inmediatamente y suspenga operaciones hasta que se le avise', 1);
            }
            return Array('idWsCfd' => $idWsCfd, 'noCertificado' => $noCertificado, 'serie' => $serie, 'folio' => $folio, 'transno' => $transno, 'cadenaOriginal' => $cadenaOriginal, 'selloDigital' => $selloDigital, 'totalEnLetra' => $totalEnLetra, 'noAprobacion' => $noAprobacion, 'anoAprobacion' => $anoAprobacion, 'addendaResponse' => $addendaResponse);
        }
        catch(Exception $e){
            $msg = $e->getMessage();
            echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $msg . ', el pedido se efectuo correctamente en el Web Service<p></div>';
            include('includes/footer.inc');
            exit;
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
