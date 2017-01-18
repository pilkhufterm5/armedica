<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**sel
 * Description of FacturaElectronica
 *
 * @author roberto
 */
require_once('Numbers/Words.php');

class FacturaElectronica {
    //put your code here
    public static function calculateCadenaOriginal($xslFilePath, $xmlFilePath){
        $command = "xsltproc $xslFilePath $xmlFilePath";
        //Hacer replace de:
        //En el caso del & se deberá usar la secuencia &amp;
        //En el caso del “ se deberá usar la secuencia &quot;
        //En el caso del < se deberá usar la secuencia &lt;
        //En el caso del > se deberá usar la secuencia &gt;
        //En el caso del „ se deberá usar la secuencia &apos;
        //$result = htmlentities(utf8_encode(Console::execOutput($command)));

        /*Pag 48 Anexo 20 2006(posiblemente en verdad el 2009)
        5. Los espacios en blanco que se presenten dentro de la cadena original serán tratados de la
        siguiente manera:
        a.    Se deberán remplazar todos los tabuladores, retornos de carro y saltos de línea por espacios
             en blanco.
        b.    Acto seguido se elimina cualquier carácter en blanco al principio y al final de cada separador
             | (“pipe” sencillo).
        c.    Finalmente, toda secuencia de caracteres en blanco intermedias se sustituyen por un único
             carácter en blanco.
        6. Los datos opcionales no expresados, no aparecerán en la cadena original y no tendrán delimitador
        alguno.
        7. El final de la cadena original será expresado mediante una cadena de caracteres || (doble “pipe”).
        8. Toda la cadena de original se expresará en el formato de codificación UTF-8.
        */
        $result = utf8_encode(Console::execOutput($command));
        $result = str_replace('&', '&amp;', $result);
        $result = str_replace('"', '&quot;', $result);
        $result = str_replace('<', '&lt;', $result);
        $result = str_replace('>', '&gt;', $result);
        $result = str_replace("'", '&#36;', $result);
        //$result = htmlentities($result);
        return $result;
    }

    public static function calculateSelloDigital($cerFilePath, $tmpPemFilePath, $xslFilePath, $xmlFilePath, $keyFilePath, $passwordFilePath){
        /*
        La clave privada solo debe mantenerse en
        memoria durante la llamada a la función de encripción; inmediatamente después de su uso debe ser
        eliminada de su registro de memoria mediante la sobre escritura de secuencias binarias alternadas de
        "unos" y "ceros".
        */

        //Obtenemos el string del archivo, quitamos los saltos de linea
        //Si $password contiene el signo $ puede haber problemas en linux, hay que encerrarlo entre '
        $password = preg_replace('/\n/', '', ("'" . file_get_contents($passwordFilePath) . "'"));
        //Introducimos el nombre del key provisto por hacienda y su respectivo password, al igual que el nombre del archivo de salida PEM
        $cmd = "openssl pkcs8 -inform DER -in $keyFilePath -passin pass:$password -out $tmpPemFilePath";
        //se crea el archivo tmpSatFiles/tmp.key.pem
        //Modificado para que no muestre la contrasena de llave privada en caso de error
        try{
            Console::execOutput($cmd);
        }
        catch(Exception $exception){
            throw new Exception('La Contrasena de Llave Privada no corresponde a la Llave Privada');
        }
        //Termina Modificado para que no muestre la contrasena de llave privada en caso de error
        //Ver la cadena en MD5, aquí obtenemos el mismo resultado que el SAT
        //$cadenaOriginalEnMD5 = md5($cadenaOriginal);
        //Aquí lo que hacemos es escribir un txt (md5.txt) con la digestión MD5 para usarlo en el sellado
        //File::createFile($cadenaOriginal, 'tmpSatFiles/Md5.txt');
        //Aquí sellamos con el MD5 con el key(.pem) para obtener el sello
        //$cmd = "echo '$cadenaOriginal' | openssl dgst -md5 -sign tmpSatFiles/aaa010101aaa_CSD_01.key.pem | openssl enc -base64  -A";
        $cmd = "xsltproc $xslFilePath $xmlFilePath | openssl dgst -md5 -sign $tmpPemFilePath | openssl enc -base64  -A";
        $selloDigital = Console::execOutput($cmd);
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        return $selloDigital;
        //return Console::execOutput('openssl dgst -sign tmpSatFiles/aaa010101aaa_CSD_01.key.pem tmpSatFiles/Md5.txt | openssl enc -base64 -A');
    }

    //$transno, $serie, $noCertificado, $xmlFilePath, $xslFilePath
    public static function guardarDatosParaReporteMensual($idFacturaElectronica, $xmlFilePath, $xslFilePath){
        global $db;
        //|rfcReceptor|serie|folio|noAprobacion|fecha|total|montoIVA|estadoComprobante|
        $comprobante = simplexml_load_file($xmlFilePath);
        $rfcDelCliente = $comprobante->Receptor['rfc'];
        $serie = $comprobante['serie'];
        $folioDelComprobanteFiscal = $comprobante['folio'];
        $numeroDeAprobacion = $comprobante['noAprobacion'];
        $fechaYHoraDeExpedicion = str_replace('T', ' ', $comprobante['fecha']);
        $montoDeLaOperacion = $comprobante['total'];
        $montoDelImpuesto = $comprobante->Impuestos->Traslados->Traslado['importe'];
        $estadoDelComprobante = 1;

        //--- otros campos de referencia con el weberp
        //en el siguiente query $idFacturaElectronica debe ser igual a rht.transno
        $sql = "select rhir.id idRhInvoiceReference, rht.type, rht.transno from rh_transaddress rht, rh_invoicesreference rhir where rht.transno = rhir.intinvoice and rhir.intinvoice = $idFacturaElectronica limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se pudo obtener la informacion de ciertas tablas al intentar guardar informacion para el reporte mensual', 1);
        }
        $row = DB_fetch_array($result);
        $idRhInvoiceReference = $row['idRhInvoiceReference'];
        $type = $row['type'];
        $transno = $row['transno'];
        //--- Termina otros campos de referencia con el weberp

        //--- otros campos para la entidad cfd
        $Comprobante_noCertificado = $comprobante['noCertificado'];
        $Comprobante_anoAprobacion = $comprobante['anoAprobacion'];
        $Comprobante_cadenaOriginal = Console::execOutput("xsltproc $xslFilePath $xmlFilePath");
        $Comprobante_sello = $comprobante['sello'];

        //aki me kede, guardar la cantidad con letra de la moneda del cliente y guardar tanto las cantidades en pesos como en la moneda del usuario
        $extra_importeConLetra = FacturaElectronica::calculateCantidadEnLetra($montoDeLaOperacion, "es", "Pesos", "MN");
        //--- Termina otros campos para la entidad cfd

        //todos los datos del comprobante
        //ya insertados anterioremente: comprobante_no_certificado, comprobante_ano_aprobacion, comprobante_sello, comprobante_cadena_original
        //$comprobante_no_certificado = $comprobante['noCertificado'];
        //$comprobante_ano_aprobacion = $comprobante['anoAprobacion'];
        //$comprobante_cadena_original = $Comprobante_cadenaOriginal;
        //$comprobante_sello = $comprobante['sello'];
        $comprobante_version = $comprobante['version'];
        $comprobante_folio = $folioDelComprobanteFiscal;
        $comprobante_fecha = $fechaYHoraDeExpedicion;
        $comprobante_no_aprobacion = $numeroDeAprobacion;
        $comprobante_forma_de_pago = $comprobante['formaDePago'];
        $comprobante_sub_total = $comprobante['subTotal'];
        $comprobante_total = $comprobante['total'];
        $comprobante_tipo_de_comprobante = $comprobante['tipoDeComprobante'];
        $comprobante_serie = $serie;
        $comprobante_certificado = $comprobante['certificado'];
        $comprobante_condiciones_de_pago = $comprobante['condicionesDePago'];
        $comprobante_emisor_rfc = $comprobante['rfc'];
        $comprobante_emisor_nombre = $comprobante->Emisor['nombre'];
        $comprobante_emisor_domicilio_fiscal_calle = $comprobante->Emisor->DomicilioFiscal['calle'];
        $comprobante_emisor_domicilio_fiscal_no_exterior = $comprobante->Emisor->DomicilioFiscal['noExterior'];
        $comprobante_emisor_domicilio_fiscal_no_interior = $comprobante->Emisor->DomicilioFiscal['noInterior'];
        $comprobante_emisor_domicilio_fiscal_colonia = $comprobante->Emisor->DomicilioFiscal['colonia'];
        $comprobante_emisor_domicilio_fiscal_localidad = $comprobante->Emisor->DomicilioFiscal['localidad'];
        $comprobante_emisor_domicilio_fiscal_referencia = $comprobante->Emisor->DomicilioFiscal['referencia'];
        $comprobante_emisor_domicilio_fiscal_municipio = $comprobante->Emisor->DomicilioFiscal['municipio'];
        $comprobante_emisor_domicilio_fiscal_estado = $comprobante->Emisor->DomicilioFiscal['estado'];
        $comprobante_emisor_domicilio_fiscal_pais = $comprobante->Emisor->DomicilioFiscal['pais'];
        $comprobante_emisor_domicilio_fiscal_codigo_postal = $comprobante->Emisor->DomicilioFiscal['codigoPostal'];
        $comprobante_emisor_expedido_en_calle = $comprobante->Emisor->ExpedidoEn['calle'];
        $comprobante_emisor_expedido_en_no_exterior = $comprobante->Emisor->ExpedidoEn['noExterior'];
        $comprobante_emisor_expedido_en_no_interior = $comprobante->Emisor->ExpedidoEn['noInterior'];
        $comprobante_emisor_expedido_en_colonia = $comprobante->Emisor->ExpedidoEn['colonia'];
        $comprobante_emisor_expedido_en_localidad = $comprobante->Emisor->ExpedidoEn['localidad'];
        $comprobante_emisor_expedido_en_referencia = $comprobante->Emisor->ExpedidoEn['referencia'];
        $comprobante_emisor_expedido_en_municipio = $comprobante->Emisor->ExpedidoEn['municipio'];
        $comprobante_emisor_expedido_en_estado = $comprobante->Emisor->ExpedidoEn['estado'];
        $comprobante_emisor_expedido_en_pais = $comprobante->Emisor->ExpedidoEn['pais'];
        $comprobante_emisor_expedido_en_codigo_postal = $comprobante->Emisor->ExpedidoEn['codigoPostal'];
        $comprobante_receptor_rfc = $comprobante->Receptor['rfc'];
        $comprobante_receptor_nombre = $comprobante->Receptor['nombre'];
        $comprobante_receptor_domicilio_calle = $comprobante->Receptor->Domicilio['calle'];
        $comprobante_receptor_domicilio_no_exterior = $comprobante->Receptor->Domicilio['noExterior'];
        $comprobante_receptor_domicilio_no_interior = $comprobante->Receptor->Domicilio['noInterior'];
        $comprobante_receptor_domicilio_colonia = $comprobante->Receptor->Domicilio['colonia'];
        $comprobante_receptor_domicilio_localidad = $comprobante->Receptor->Domicilio['localidad'];
        $comprobante_receptor_domicilio_referencia = $comprobante->Receptor->Domicilio['referencia'];
        $comprobante_receptor_domicilio_municipio = $comprobante->Receptor->Domicilio['municipio'];
        $comprobante_receptor_domicilio_estado = $comprobante->Receptor->Domicilio['estado'];
        $comprobante_receptor_domicilio_pais = $comprobante->Receptor->Domicilio['pais'];
        $comprobante_receptor_domicilio_codigo_postal = $comprobante->Receptor->Domicilio['codigoPostal'];
        $comprobante_impuestos_traslados_traslado_impuesto = $comprobante->Impuestos->Traslados->Traslado['impuesto'];
        $comprobante_impuestos_traslados_traslado_tasa = $comprobante->Impuestos->Traslados->Traslado['tasa'];
        $comprobante_impuestos_traslados_traslado_importe = $comprobante->Impuestos->Traslados->Traslado['importe'];
        //termina todos los datos del comprobante

        //emdc
            //obtenemos la moneda del cliente
            $sql = "select cast((t.ovgst + t.ovamount + t.ovfreight - t.ovdiscount) as decimal(10,2)) extra_comprobante_total_emdc, t.rate, m.currcode from debtortrans t, debtorsmaster m where m.debtorno = t.debtorno and t.transno =  $idFacturaElectronica and t.type = 10 limit 1";
            $result = DB_query($sql,$db,'','',false,false);
            if(mysql_errno($db) || mysql_num_rows($result)!=1){
                throw new Exception('No se pudo obtener el tipo de moneda del cliente', 1);
            }
            $row = DB_fetch_array($result);
            $rate = $row['rate'];
            $currcode = $row['currcode'];
            $extra_comprobante_total_emdc = $row['extra_comprobante_total_emdc'];
            $sql = "select currency from currencies where currabrev = '$currcode' limit 1";
            $result = DB_query($sql,$db,'','',false,false);
            if(mysql_errno($db) || mysql_num_rows($result)!=1){
                throw new Exception('No se pudo obtener el nombre de la moneda del cliente', 1);
            }
            $row = DB_fetch_array($result);
            $currency = $row['currency'];
            //$extra_comprobante_total_emdc = round($comprobante_total*$rate, 2);
            $extra_comprobante_sub_total_emdc = round($comprobante_sub_total*$rate, 2);
            $extra_comprobante_impuestos_traslados_traslado_importe_emdc = round($comprobante_impuestos_traslados_traslado_importe*$rate, 2);
            $extra_importeConLetraEmdc = FacturaElectronica::calculateCantidadEnLetra($extra_comprobante_total_emdc, "es", $currency, $currcode);
        //termina emdc

        //CREATE TABLE `rh_factura_electronica_reporte_mensual_sat` (  `rfc_del_cliente` varchar(13) DEFAULT NULL,  `serie` varchar(256) DEFAULT NULL,  `folio_del_comprobante_fiscal` varchar(20) DEFAULT NULL,  `numero_de_aprobacion` int(11) DEFAULT NULL,  `fecha_y_hora_de_expedicion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  `monto_de_la_operacion` double DEFAULT NULL,  `monto_del_impuesto` double DEFAULT NULL,  `estado_del_comprobante` tinyint(4) DEFAULT NULL,  `fk_rh_transaddress_type` smallint(6) DEFAULT NULL,  `fk_rh_transaddress_transno` int(11) DEFAULT NULL,  `fk_rh_invoicesreference_id` int(11) DEFAULT NULL,  KEY `fk_rh_invoicesreference_id` (`fk_rh_invoicesreference_id`),  KEY `fk_rh_transaddress_type` (`fk_rh_transaddress_type`),  KEY `fk_rh_transaddress_transno` (`fk_rh_transaddress_transno`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_3` FOREIGN KEY (`fk_rh_transaddress_transno`) REFERENCES `rh_transaddress` (`transno`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_1` FOREIGN KEY (`fk_rh_invoicesreference_id`) REFERENCES `rh_invoicesreference` (`id`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_2` FOREIGN KEY (`fk_rh_transaddress_type`) REFERENCES `rh_transaddress` (`type`)) ENGINE=InnoDB DEFAULT CHARSET=latin1
        $data = "$idRhInvoiceReference, $type, $transno, '" . $rfcDelCliente . "', '" . $serie . "', '" . $folioDelComprobanteFiscal . "', $numeroDeAprobacion, '" . $fechaYHoraDeExpedicion . "', $montoDeLaOperacion, $montoDelImpuesto, $estadoDelComprobante, '" . $Comprobante_noCertificado . "', $Comprobante_anoAprobacion, '" . $Comprobante_sello . "', '" . $Comprobante_cadenaOriginal . "', '" . $extra_importeConLetra . "'";
        $sql = "insert into rh_factura_electronica_reporte_mensual_sat(fk_rh_invoicesreference_id, fk_rh_transaddress_type, fk_rh_transaddress_transno, rfc_del_cliente, serie, folio_del_comprobante_fiscal, numero_de_aprobacion, fecha_y_hora_de_expedicion, monto_de_la_operacion, monto_del_impuesto, estado_del_comprobante, comprobante_no_certificado, comprobante_ano_aprobacion, comprobante_sello, comprobante_cadena_original, extra_importe_con_letra) values($data)";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception("No se pudo insertar los datos de la factura electronica para el reporte mensual (posiblemente se intento duplicar un folio)", 1);
        }
        $id = mysql_insert_id($db);
        $sql = "update rh_factura_electronica_reporte_mensual_sat set
            comprobante_version = '$comprobante_version',
            comprobante_folio = '$comprobante_folio',
            comprobante_fecha = '$comprobante_fecha',
            comprobante_no_aprobacion = '$comprobante_no_aprobacion',
            comprobante_forma_de_pago = '$comprobante_forma_de_pago',
            comprobante_sub_total = '$comprobante_sub_total',
            comprobante_total = '$comprobante_total',
            comprobante_tipo_de_comprobante = '$comprobante_tipo_de_comprobante',
            comprobante_serie = '$comprobante_serie',
            comprobante_certificado = '$comprobante_certificado',
            comprobante_condiciones_de_pago = '$comprobante_condiciones_de_pago',
            comprobante_emisor_rfc = '$comprobante_emisor_rfc',
            comprobante_emisor_nombre = '$comprobante_emisor_nombre',
            comprobante_emisor_domicilio_fiscal_calle = '$comprobante_emisor_domicilio_fiscal_calle',
            comprobante_emisor_domicilio_fiscal_no_exterior = '$comprobante_emisor_domicilio_fiscal_no_exterior',
            comprobante_emisor_domicilio_fiscal_no_interior = '$comprobante_emisor_domicilio_fiscal_no_interior',
            comprobante_emisor_domicilio_fiscal_colonia = '$comprobante_emisor_domicilio_fiscal_colonia',
            comprobante_emisor_domicilio_fiscal_localidad = '$comprobante_emisor_domicilio_fiscal_localidad',
            comprobante_emisor_domicilio_fiscal_referencia = '$comprobante_emisor_domicilio_fiscal_referencia',
            comprobante_emisor_domicilio_fiscal_municipio = '$comprobante_emisor_domicilio_fiscal_municipio',
            comprobante_emisor_domicilio_fiscal_estado = '$comprobante_emisor_domicilio_fiscal_estado',
            comprobante_emisor_domicilio_fiscal_pais = '$comprobante_emisor_domicilio_fiscal_pais',
            comprobante_emisor_domicilio_fiscal_codigo_postal = '$comprobante_emisor_domicilio_fiscal_codigo_postal',
            comprobante_emisor_expedido_en_calle = '$comprobante_emisor_expedido_en_calle',
            comprobante_emisor_expedido_en_no_exterior = '$comprobante_emisor_expedido_en_no_exterior',
            comprobante_emisor_expedido_en_no_interior = '$comprobante_emisor_expedido_en_no_interior',
            comprobante_emisor_expedido_en_colonia = '$comprobante_emisor_expedido_en_colonia',
            comprobante_emisor_expedido_en_localidad = '$comprobante_emisor_expedido_en_localidad',
            comprobante_emisor_expedido_en_referencia = '$comprobante_emisor_expedido_en_referencia',
            comprobante_emisor_expedido_en_municipio = '$comprobante_emisor_expedido_en_municipio',
            comprobante_emisor_expedido_en_estado = '$comprobante_emisor_expedido_en_estado',
            comprobante_emisor_expedido_en_pais = '$comprobante_emisor_expedido_en_pais',
            comprobante_emisor_expedido_en_codigo_postal = '$comprobante_emisor_expedido_en_codigo_postal',
            comprobante_receptor_rfc = '$comprobante_receptor_rfc',
            comprobante_receptor_nombre = '$comprobante_receptor_nombre',
            comprobante_receptor_domicilio_calle = '$comprobante_receptor_domicilio_calle',
            comprobante_receptor_domicilio_no_exterior = '$comprobante_receptor_domicilio_no_exterior',
            comprobante_receptor_domicilio_no_interior = '$comprobante_receptor_domicilio_no_interior',
            comprobante_receptor_domicilio_colonia = '$comprobante_receptor_domicilio_colonia',
            comprobante_receptor_domicilio_localidad = '$comprobante_receptor_domicilio_localidad',
            comprobante_receptor_domicilio_referencia = '$comprobante_receptor_domicilio_referencia',
            comprobante_receptor_domicilio_municipio = '$comprobante_receptor_domicilio_municipio',
            comprobante_receptor_domicilio_estado = '$comprobante_receptor_domicilio_estado',
            comprobante_receptor_domicilio_pais = '$comprobante_receptor_domicilio_pais',
            comprobante_receptor_domicilio_codigo_postal = '$comprobante_receptor_domicilio_codigo_postal',
            comprobante_impuestos_traslados_traslado_impuesto = '$comprobante_impuestos_traslados_traslado_impuesto',
            comprobante_impuestos_traslados_traslado_tasa = '$comprobante_impuestos_traslados_traslado_tasa',
            comprobante_impuestos_traslados_traslado_importe = '$comprobante_impuestos_traslados_traslado_importe',
            extra_comprobante_total_emdc = $extra_comprobante_total_emdc,
            extra_importe_con_letra_emdc = '$extra_importeConLetraEmdc',
            extra_comprobante_sub_total_emdc = $extra_comprobante_sub_total_emdc,
            extra_comprobante_impuestos_traslados_traslado_importe_emdc = $extra_comprobante_impuestos_traslados_traslado_importe_emdc,
            extra_tipo_de_moneda = '$currcode',
            extra_tipo_de_cambio = '$rate'
        where id = $id";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('No se pudo insertar los datos completos de la Factura Electronica (sin conceptos)', 1);
        }

        $sql = "insert into rh_factura_electronica_comprobante_conceptos_concepto (id_rh_factura_electronica_reporte_mensual_sat, cantidad, unidad, descripcion, valor_unitario, importe, extra_importe_emdc, extra_valor_unitario_emdc) values";
        //$comprobante_conceptos = array();
        foreach($comprobante->Conceptos as $concepto){
            $concepto = $concepto->Concepto;
            $sql .= '(' . $id . ',' . (string)$concepto['cantidad'] . ',\'' . (string)$concepto['unidad'] . '\',\'' . (string)$concepto['descripcion'] . '\',' . (string)$concepto['valorUnitario'] . ',' . (string)$concepto['importe'] . ',' . round((double)$concepto['importe']*$rate, 2) . ',' . round((double)$concepto['valorUnitario']*$rate, 2) . '),';
            //array_push($comprobante_conceptos, $concepto);
        }
        $sql = substr($sql, 0, strlen($sql)-1);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)<1){
            throw new Exception('No se pudo insertar los datos completos de la Factura Electronica (los conceptos)', 1);
        }
        //aki me kede, probar
    }

    public static function calculateCantidadEnLetra($cantidad, $idioma, $nombreDeMoneda, $codigoDeMoneda){
        //$montoDeLaOperacionEmdc = explode(".",$montoDeLaOperacion*$rate);
        $tot = explode(".",$cantidad);
        $extra_importeConLetra = Numbers_Words::toWords($tot[0],$idioma);


        $cents = (isSet($tot[1])?$tot[1]:'00');
        if($cents!='00'){
            $cents = '.' . $cents;
            $cents = (double)($cents);
            $cents = $cents * 100;
            $cents = round($cents, 0);
        }

        $extra_importeConLetra .= " $nombreDeMoneda " . $cents . "/100 ". $codigoDeMoneda;
        $extra_importeConLetra = strtoupper($extra_importeConLetra);
        return $extra_importeConLetra;
    }

    public static function guardarDatosParaReporteMensualBackup($idFacturaElectronica, $serie, $noCertificado) {
        global $db;
        global $xslFilePath;
        //|rfcReceptor|serie|folio|noAprobacion|fecha|total|montoIVA|estadoComprobante|
        $comprobante = simplexml_load_file("XMLFacturacionElectronica/facturasElectronicas/$noCertificado/$serie$idFacturaElectronica.xml");
        $rfcDelCliente = $comprobante->Receptor['rfc'];
        $serie = $comprobante['serie'];
        $folioDelComprobanteFiscal = $comprobante['folio'];
        $numeroDeAprobacion = $comprobante['noAprobacion'];
        $fechaYHoraDeExpedicion = str_replace('T', ' ', $comprobante['fecha']);
        $montoDeLaOperacion = $comprobante['total'];
        $montoDelImpuesto = $comprobante->Impuestos->Traslados->Traslado['importe'];
        $estadoDelComprobante = 1;

        //--- otros campos de referencia con el weberp
        //en el siguiente query $idFacturaElectronica debe ser igual a rht.transno
        $sql = "select rhir.id idRhInvoiceReference, rht.type, rht.transno from rh_transaddress rht, rh_invoicesreference rhir where rht.transno = rhir.intinvoice and rhir.intinvoice = $idFacturaElectronica limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('No se puedo obtener informacion importante para el reporte mensual de la Factura Electronica', 1);
        }
        $row = mysql_fetch_array($result);
        $idRhInvoiceReference = $row['idRhInvoiceReference'];
        $type = $row['type'];
        $transno = $row['transno'];
        //--- Termina otros campos de referencia con el weberp

        //--- otros campos para la entidad cfd
        $Comprobante_noCertificado = $comprobante['noCertificado'];
        $Comprobante_anoAprobacion = $comprobante['anoAprobacion'];
        $Comprobante_cadenaOriginal = Console::execOutput("xsltproc $xslFilePath XMLFacturacionElectronica/facturasElectronicas/$noCertificado/$serie$idFacturaElectronica.xml");
        $Comprobante_sello = $comprobante['sello'];

        $tot = explode(".",$montoDeLaOperacion);
        $extra_importeConLetra = Numbers_Words::toWords($tot[0],"es");
        $extra_importeConLetra .= ' Pesos ' . (isSet($tot[1])?$tot[1]:'00') . "/100 ". 'MN';
        $extra_importeConLetra = strtoupper($extra_importeConLetra);
        //--- Termina otros campos para la entidad cfd

        //todos los datos del comprobante
        //ya insertados anterioremente: comprobante_no_certificado, comprobante_ano_aprobacion, comprobante_sello, comprobante_cadena_original
        //$comprobante_no_certificado = $comprobante['noCertificado'];
        //$comprobante_ano_aprobacion = $comprobante['anoAprobacion'];
        //$comprobante_cadena_original = $Comprobante_cadenaOriginal;
        //$comprobante_sello = $comprobante['sello'];
        $comprobante_version = $comprobante['version'];
        $comprobante_folio = $folioDelComprobanteFiscal;
        $comprobante_fecha = $fechaYHoraDeExpedicion;
        $comprobante_no_aprobacion = $numeroDeAprobacion;
        $comprobante_forma_de_pago = $comprobante['formaDePago'];
        $comprobante_sub_total = $comprobante['subTotal'];
        $comprobante_total = $comprobante['total'];
        $comprobante_tipo_de_comprobante = $comprobante['tipoDeComprobante'];
        $comprobante_serie = $serie;
        $comprobante_certificado = $comprobante['certificado'];
        $comprobante_condiciones_de_pago = $comprobante['condicionesDePago'];
        $comprobante_emisor_rfc = $comprobante['rfc'];
        $comprobante_emisor_nombre = $comprobante->Emisor['nombre'];
        $comprobante_emisor_domicilio_fiscal_calle = $comprobante->Emisor->DomicilioFiscal['calle'];
        $comprobante_emisor_domicilio_fiscal_no_exterior = $comprobante->Emisor->DomicilioFiscal['noExterior'];
        $comprobante_emisor_domicilio_fiscal_no_interior = $comprobante->Emisor->DomicilioFiscal['noInterior'];
        $comprobante_emisor_domicilio_fiscal_colonia = $comprobante->Emisor->DomicilioFiscal['colonia'];
        $comprobante_emisor_domicilio_fiscal_localidad = $comprobante->Emisor->DomicilioFiscal['localidad'];
        $comprobante_emisor_domicilio_fiscal_referencia = $comprobante->Emisor->DomicilioFiscal['referencia'];
        $comprobante_emisor_domicilio_fiscal_municipio = $comprobante->Emisor->DomicilioFiscal['municipio'];
        $comprobante_emisor_domicilio_fiscal_estado = $comprobante->Emisor->DomicilioFiscal['estado'];
        $comprobante_emisor_domicilio_fiscal_pais = $comprobante->Emisor->DomicilioFiscal['pais'];
        $comprobante_emisor_domicilio_fiscal_codigo_postal = $comprobante->Emisor->DomicilioFiscal['codigoPostal'];
        $comprobante_emisor_expedido_en_calle = $comprobante->Emisor->ExpedidoEn['calle'];
        $comprobante_emisor_expedido_en_no_exterior = $comprobante->Emisor->ExpedidoEn['noExterior'];
        $comprobante_emisor_expedido_en_no_interior = $comprobante->Emisor->ExpedidoEn['noInterior'];
        $comprobante_emisor_expedido_en_colonia = $comprobante->Emisor->ExpedidoEn['colonia'];
        $comprobante_emisor_expedido_en_localidad = $comprobante->Emisor->ExpedidoEn['localidad'];
        $comprobante_emisor_expedido_en_referencia = $comprobante->Emisor->ExpedidoEn['referencia'];
        $comprobante_emisor_expedido_en_municipio = $comprobante->Emisor->ExpedidoEn['municipio'];
        $comprobante_emisor_expedido_en_estado = $comprobante->Emisor->ExpedidoEn['estado'];
        $comprobante_emisor_expedido_en_pais = $comprobante->Emisor->ExpedidoEn['pais'];
        $comprobante_emisor_expedido_en_codigo_postal = $comprobante->Emisor->ExpedidoEn['codigoPostal'];
        $comprobante_receptor_rfc = $comprobante->Receptor['rfc'];
        $comprobante_receptor_nombre = $comprobante->Receptor['nombre'];
        $comprobante_receptor_domicilio_calle = $comprobante->Receptor->Domicilio['calle'];
        $comprobante_receptor_domicilio_no_exterior = $comprobante->Receptor->Domicilio['noExterior'];
        $comprobante_receptor_domicilio_no_interior = $comprobante->Receptor->Domicilio['noInterior'];
        $comprobante_receptor_domicilio_colonia = $comprobante->Receptor->Domicilio['colonia'];
        $comprobante_receptor_domicilio_localidad = $comprobante->Receptor->Domicilio['localidad'];
        $comprobante_receptor_domicilio_referencia = $comprobante->Receptor->Domicilio['referencia'];
        $comprobante_receptor_domicilio_municipio = $comprobante->Receptor->Domicilio['municipio'];
        $comprobante_receptor_domicilio_estado = $comprobante->Receptor->Domicilio['estado'];
        $comprobante_receptor_domicilio_pais = $comprobante->Receptor->Domicilio['pais'];
        $comprobante_receptor_domicilio_codigo_postal = $comprobante->Receptor->Domicilio['codigoPostal'];
        $comprobante_impuestos_traslados_traslado_impuesto = $comprobante->Impuestos->Traslados->Traslado['impuesto'];
        $comprobante_impuestos_traslados_traslado_tasa = $comprobante->Impuestos->Traslados->Traslado['tasa'];
        $comprobante_impuestos_traslados_traslado_importe = $comprobante->Impuestos->Traslados->Traslado['importe'];
        //termina todos los datos del comprobante

        //CREATE TABLE `rh_factura_electronica_reporte_mensual_sat` (  `rfc_del_cliente` varchar(13) DEFAULT NULL,  `serie` varchar(256) DEFAULT NULL,  `folio_del_comprobante_fiscal` varchar(20) DEFAULT NULL,  `numero_de_aprobacion` int(11) DEFAULT NULL,  `fecha_y_hora_de_expedicion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,  `monto_de_la_operacion` double DEFAULT NULL,  `monto_del_impuesto` double DEFAULT NULL,  `estado_del_comprobante` tinyint(4) DEFAULT NULL,  `fk_rh_transaddress_type` smallint(6) DEFAULT NULL,  `fk_rh_transaddress_transno` int(11) DEFAULT NULL,  `fk_rh_invoicesreference_id` int(11) DEFAULT NULL,  KEY `fk_rh_invoicesreference_id` (`fk_rh_invoicesreference_id`),  KEY `fk_rh_transaddress_type` (`fk_rh_transaddress_type`),  KEY `fk_rh_transaddress_transno` (`fk_rh_transaddress_transno`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_3` FOREIGN KEY (`fk_rh_transaddress_transno`) REFERENCES `rh_transaddress` (`transno`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_1` FOREIGN KEY (`fk_rh_invoicesreference_id`) REFERENCES `rh_invoicesreference` (`id`),  CONSTRAINT `rh_factura_electronica_reporte_mensual_sat_ibfk_2` FOREIGN KEY (`fk_rh_transaddress_type`) REFERENCES `rh_transaddress` (`type`)) ENGINE=InnoDB DEFAULT CHARSET=latin1
        $data = "$idRhInvoiceReference, $type, $transno, '" . $rfcDelCliente . "', '" . $serie . "', '" . $folioDelComprobanteFiscal . "', $numeroDeAprobacion, '" . $fechaYHoraDeExpedicion . "', $montoDeLaOperacion, $montoDelImpuesto, $estadoDelComprobante, '" . $Comprobante_noCertificado . "', $Comprobante_anoAprobacion, '" . $Comprobante_sello . "', '" . $Comprobante_cadenaOriginal . "', '" . $extra_importeConLetra . "'";
        $sql = "insert into rh_factura_electronica_reporte_mensual_sat(fk_rh_invoicesreference_id, fk_rh_transaddress_type, fk_rh_transaddress_transno, rfc_del_cliente, serie, folio_del_comprobante_fiscal, numero_de_aprobacion, fecha_y_hora_de_expedicion, monto_de_la_operacion, monto_del_impuesto, estado_del_comprobante, comprobante_no_certificado, comprobante_ano_aprobacion, comprobante_sello, comprobante_cadena_original, extra_importe_con_letra) values($data)";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('No se pudo insertar los datos de la factura electronica para el reporte mensual', 1);
        }
        $id = mysql_insert_id($db);
        $sql = "update rh_factura_electronica_reporte_mensual_sat set
            comprobante_version = '$comprobante_version',
            comprobante_folio = '$comprobante_folio',
            comprobante_fecha = '$comprobante_fecha',
            comprobante_no_aprobacion = '$comprobante_no_aprobacion',
            comprobante_forma_de_pago = '$comprobante_forma_de_pago',
            comprobante_sub_total = '$comprobante_sub_total',
            comprobante_total = '$comprobante_total',
            comprobante_tipo_de_comprobante = '$comprobante_tipo_de_comprobante',
            comprobante_serie = '$comprobante_serie',
            comprobante_certificado = '$comprobante_certificado',
            comprobante_condiciones_de_pago = '$comprobante_condiciones_de_pago',
            comprobante_emisor_rfc = '$comprobante_emisor_rfc',
            comprobante_emisor_nombre = '$comprobante_emisor_nombre',
            comprobante_emisor_domicilio_fiscal_calle = '$comprobante_emisor_domicilio_fiscal_calle',
            comprobante_emisor_domicilio_fiscal_no_exterior = '$comprobante_emisor_domicilio_fiscal_no_exterior',
            comprobante_emisor_domicilio_fiscal_no_interior = '$comprobante_emisor_domicilio_fiscal_no_interior',
            comprobante_emisor_domicilio_fiscal_colonia = '$comprobante_emisor_domicilio_fiscal_colonia',
            comprobante_emisor_domicilio_fiscal_localidad = '$comprobante_emisor_domicilio_fiscal_localidad',
            comprobante_emisor_domicilio_fiscal_referencia = '$comprobante_emisor_domicilio_fiscal_referencia',
            comprobante_emisor_domicilio_fiscal_municipio = '$comprobante_emisor_domicilio_fiscal_municipio',
            comprobante_emisor_domicilio_fiscal_estado = '$comprobante_emisor_domicilio_fiscal_estado',
            comprobante_emisor_domicilio_fiscal_pais = '$comprobante_emisor_domicilio_fiscal_pais',
            comprobante_emisor_domicilio_fiscal_codigo_postal = '$comprobante_emisor_domicilio_fiscal_codigo_postal',
            comprobante_emisor_expedido_en_calle = '$comprobante_emisor_expedido_en_calle',
            comprobante_emisor_expedido_en_no_exterior = '$comprobante_emisor_expedido_en_no_exterior',
            comprobante_emisor_expedido_en_no_interior = '$comprobante_emisor_expedido_en_no_interior',
            comprobante_emisor_expedido_en_colonia = '$comprobante_emisor_expedido_en_colonia',
            comprobante_emisor_expedido_en_localidad = '$comprobante_emisor_expedido_en_localidad',
            comprobante_emisor_expedido_en_referencia = '$comprobante_emisor_expedido_en_referencia',
            comprobante_emisor_expedido_en_municipio = '$comprobante_emisor_expedido_en_municipio',
            comprobante_emisor_expedido_en_estado = '$comprobante_emisor_expedido_en_estado',
            comprobante_emisor_expedido_en_pais = '$comprobante_emisor_expedido_en_pais',
            comprobante_emisor_expedido_en_codigo_postal = '$comprobante_emisor_expedido_en_codigo_postal',
            comprobante_receptor_rfc = '$comprobante_receptor_rfc',
            comprobante_receptor_nombre = '$comprobante_receptor_nombre',
            comprobante_receptor_domicilio_calle = '$comprobante_receptor_domicilio_calle',
            comprobante_receptor_domicilio_no_exterior = '$comprobante_receptor_domicilio_no_exterior',
            comprobante_receptor_domicilio_no_interior = '$comprobante_receptor_domicilio_no_interior',
            comprobante_receptor_domicilio_colonia = '$comprobante_receptor_domicilio_colonia',
            comprobante_receptor_domicilio_localidad = '$comprobante_receptor_domicilio_localidad',
            comprobante_receptor_domicilio_referencia = '$comprobante_receptor_domicilio_referencia',
            comprobante_receptor_domicilio_municipio = '$comprobante_receptor_domicilio_municipio',
            comprobante_receptor_domicilio_estado = '$comprobante_receptor_domicilio_estado',
            comprobante_receptor_domicilio_pais = '$comprobante_receptor_domicilio_pais',
            comprobante_receptor_domicilio_codigo_postal = '$comprobante_receptor_domicilio_codigo_postal',
            comprobante_impuestos_traslados_traslado_impuesto = '$comprobante_impuestos_traslados_traslado_impuesto',
            comprobante_impuestos_traslados_traslado_tasa = '$comprobante_impuestos_traslados_traslado_tasa',
            comprobante_impuestos_traslados_traslado_importe = '$comprobante_impuestos_traslados_traslado_importe'
        where id = $id";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('No se pudo insertar los datos completos de la Factura Electronica (sin conceptos)', 1);
        }

        $sql = "insert into rh_factura_electronica_comprobante_conceptos_concepto (id_rh_factura_electronica_reporte_mensual_sat, cantidad, unidad, descripcion, valor_unitario, importe) values";
        //$comprobante_conceptos = array();
        foreach($comprobante->Conceptos as $concepto) {
            $concepto = $concepto->Concepto;
            $sql .= '(' . $id . ',' . (string)$concepto['cantidad'] . ',\'' . (string)$concepto['unidad'] . '\',\'' . (string)$concepto['descripcion'] . '\',' . (string)$concepto['valorUnitario'] . ',' . (string)$concepto['importe'] . '),';
        //array_push($comprobante_conceptos, $concepto);
        }
        $sql = substr($sql, 0, strlen($sql)-1);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)<1){
            throw new Exception('No se pudo insertar los datos completos de la Factura Electronica (los conceptos)', 1);
        }
    //aki me kede, probar
    }

    public static function writeSelloDigitalInXml($xmlFilePath, $selloDigital){
        $xml = simplexml_load_file($xmlFilePath);
        $xml['sello'] = $selloDigital;
        //Solo necesario para indentar el XML (podemos quitarlo si no queremos indentar)
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        $xml = $dom->saveXML();
        $xml = str_replace('|', '-', $xml);
        $xml = new SimpleXMLElement($xml);
        //Termina Solo necesario para identar el XML (podemos quitarlo si no queremos identar)
        //regresa 1 si se pudo grabar
        $xml->asXML($xmlFilePath);
    }

    public static function validateCrearFactura($tmpPemFilePath, $xmlFilePath, $xsdFilePath, $cerFilePath){
        FacturaElectronica::validateXmlSchema($xmlFilePath, $xsdFilePath);
        FacturaElectronica::validateEstadoDeCertificadoConElSat($tmpPemFilePath, $cerFilePath);
        //FacturaElectronica::validateFoliosDisponibles($rfc);
        FacturaElectronica::validateFechasDeCertificado($tmpPemFilePath, $cerFilePath);
    }

    public static function validateEstadoDeCertificadoConElSat($tmpPemFilePath, $cerFilePath){
        global $db;
        $noCertificado = FacturaElectronica::getNumeroDeCertificado($tmpPemFilePath, $cerFilePath);
        //por prueba
        /*prueba*/ //$noCertificado = '00001000000001973811';
        //Termina por prueba
        //mysql_connect("localhost", "root", "");
        //mysql_select_db("prueba_sat");
        $sql = "select edo_certificado from csd where no_serie = '$noCertificado' limit 1";
        //$result = mysql_query($query);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception("El certificado ($noCertificado) no esta registrado en el SAT", 1);
        }
        //$estadoCertificado = mysql_fetch_array($result, MYSQLI_ASSOC);
        $estadoCertificado = mysql_fetch_array($result);
        $estadoCertificado = $estadoCertificado['edo_certificado'];
        switch($estadoCertificado){
            case 'C':
                throw new Exception("El SAT ha marcado el certificado ($noCertificado) como caduco");
            break;
            case 'R':
                throw new Exception("El SAT ha marcado el certificado ($noCertificado) como revocado");
            break;
            case 'A':
                //Aprovado
            break;
            default:
                throw new Exception("A ocurrido un error grave en la aplicacion:\\nNo se encontro el estado del certificado ($noCertificado) con el SAT");
            break;
        }
    }

    public static function getNumeroDeCertificado($tmpPemFilePath, $cerFilePath){
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $cmd = "openssl x509 -inform pem -in $tmpPemFilePath -noout -serial";
        $noCertificado = Console::execOutput($cmd);
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        $noCertificado = substr($noCertificado, strpos($noCertificado, '=')+1);
        $noCertificado = Converter::hex2Ascii($noCertificado);
        return $noCertificado;
    }

    public static function validateIntegridadDeCertificado($cerFilePath, $tmpPemFilePath){
        /*codigo que obtendra el .CER del emisor
        ...
        */
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
    }

    public static function createTemporaryPem($cerFilePath, $tmpPemFilePath){
        /*codigo que obtendra el .CER del emisor
        ...
        */
        Console::execOutput("openssl x509 -inform der -in $cerFilePath -out $tmpPemFilePath");
        //'No se pudo procesar el Certificado (.CER)'
    }

    public static function deleteTemporaryPem($tmpPemFilePath){
        Console::execOutput("rm $tmpPemFilePath");
        //No se pudo borrar el .PEM
    }

    public static function validateNoCertificadoEscritoPorElContribuyenteContraElNoCertificadoDelCertificadoSubidoPorElContribuyente($cerFilePath, $tmpPemFilePath, $noCertificadoEscritoPorElContribuyente){
        $noCertificado = FacturaElectronica::getNumeroDeCertificado($tmpPemFilePath, $cerFilePath);
        if($noCertificadoEscritoPorElContribuyente != $noCertificado)
            throw new Exception('El Certificado (.cer) no coincide con el Numero de Certificado especificado');
    }

    public static function validateFechasDeCertificado($tmpPemFilePath, $cerFilePath){
    //roberto@roberto-laptop:~/Desktop/noCertificadoPruebaSAT$ openssl x509 -in pem.pem -out -serial -startdate
    //notBefore=Aug 21 15:22:08 2008 GMT
    //roberto@roberto-laptop:~/Desktop/noCertificadoPruebaSAT$ openssl x509 -in pem.pem -out -serial -enddate

    /*
     * Obtenemos las fechas del certificado con el comando: openssl x509 -inform pem -in pem.pem -out -startdate -enddate
     */
        //Con estos comando podemos obtener las fecha de creacion y caducidad desde el .CER
        $fechaDeInicio = FacturaElectronica::getFechaDeInicioDeCertificado($tmpPemFilePath, $cerFilePath);
        $fechaDeFin = FacturaElectronica::getFechaDeFinDeCertificado($tmpPemFilePath, $cerFilePath);
        $today = strtotime(date("Y-m-d H:i:s", time()));
        if(!($fechaDeInicio < $today &&  $fechaDeFin > $today))
            throw new Exception('El Certificado no esta vigente');
    }

    public static function getFechaDeInicioDeCertificado($tmpPemFilePath, $cerFilePath){
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $fechaDeInicio = Console::execOutput("openssl x509 -inform pem -in $tmpPemFilePath -noout -startdate");
        $fechaDeInicio = substr($fechaDeInicio, strpos($fechaDeInicio, '=')+1);
        $fechaDeInicio = Openssl::dateToPHPDate($fechaDeInicio);
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        return $fechaDeInicio;
    }

    public static function getFechaDeFinDeCertificado($tmpPemFilePath, $cerFilePath){
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $fechaDeFin = Console::execOutput("openssl x509 -inform pem -in $tmpPemFilePath -noout -enddate");
        $fechaDeFin = substr($fechaDeFin, strpos($fechaDeFin, '=')+1);
        $fechaDeFin = Openssl::dateToPHPDate($fechaDeFin);
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        return $fechaDeFin;
    }
    
    public static function validateFoliosDisponibles(){
        //Codigo que verifica si aun hay folios, en base a que? rfc emisor?, que rango de folios escoger si tiene varios?, que certificado?
        //Todos aquellos datos de la "Alta de folios" deben de haberse dado de alta en ese momento, para poder hacer esta validacion contra esos datos en la base de datos
    }

    public static function validateXmlSchema($xmlFilePath, $xsdFilePath){
//        El siguiente codigo no funciona dado a un bug, leer el primer comentario del url: http://php.net/manual/en/domdocument.schemavalidate.php, se opto por usar xmllint desde consola (el cual tampoco acepta multipes xsd)
//        //IMPORTANTE: este validador no acepta multiples xsd, habra que cambiarlo para incluir vario ya que para ser PACFD se requiere otro elemento <complemento>: http://www.sat.gob.mx/sitio_Internet/e_sat/comprobantes_fiscales/15_9255.html (vease el cfdv2.xml de XMLFacturacionElectronicaVersion2)
//        $xdoc = new DomDocument;
//        //Load the xml document in the DOMDocument object
//        $xdoc->Load($xmlFilePath);
//        //Validate the XML file against the schema
//        if(!$xdoc->schemaValidate($xsdFilePath));
//            throw new Exception('El XML no cumple con el Esquema XML proporcionado por el SAT');
//        Termina El siguiente codigo no funciona dado a un bug, leer el primer comentario del url: http://php.net/manual/en/domdocument.schemavalidate.php, se opto por usar xmllint desde consola (el cual tampoco acepta multipes xsd)
          //El XML no cumple con el Esquema XML proporcionado por el SAT
          Console::execOutput("xmllint --schema $xsdFilePath $xmlFilePath --noout 2>&1");
    }

    //funciones para el alta de folios, usarla en el modulo de alta de folios
    public static function validateAltaDeFolios($tmpPemFilePath, $cerFilePath, $noCertificado, $rfc, $noAprobacion, $anoAprobacion, $folioInicial, $folioFinal, $serie){
        /*
         * Esta funcion se ejecuta despues de dar de alta folios, la primera validacion que se hace es:
         * 0-El certificado debe de ser integro (no haber sido modificado, ser un certificado valido)
         * 1-La "serie de certificado" del .CER uplodeado debe ser igual a "$cerFilePath" (investigar como obtener la serie de un .CER)
         * 2-Debe buscarse un registro en la tabla rh_folios_cfd y rh_csd que contenga todos los parametros, excepto $cerFilePath
         */
        FacturaElectronica::validateIntegridadDeCertificado($cerFilePath, $tmpPemFilePath);
        FacturaElectronica::validateNoCertificadoEscritoPorElContribuyenteContraElNoCertificadoDelCertificadoSubidoPorElContribuyente($cerFilePath, $pemFilePath, $noCertificado);
        FacturaElectronica::validateFechasDeCertificado($tmpPemFilePath, $cerFilePath);
        FacturaElectronica::validateInformacionDelContribuyenteContraLaDelSat($noCertificado, $rfc, $noAprobacion, $anoAprobacion, $folioInicial, $folioFinal, $serie);
//        //1
//        $serie = Console::execOutput("openssl x509 -inform pem -in pem.pem -noout -startdate");
//        $n = "";
//        for i = 2 to length($noCertificado) step 2
//           $n .= substr($noCertificado,$i,1)
//        end for
//        serial = int($n)
//        //Termina 1
        //1
        //


        //2
        //Termina 2

    }

    //funciones para el alta de folios, usarla en el modulo de alta de folios
    public static function validateAltaDeCertificados($tmpPemFilePath, $cerFilePath){
        /*
         * Esta funcion se ejecuta despues de dar de alta folios, la primera validacion que se hace es:
         * 0-El certificado debe de ser integro (no haber sido modificado, ser un certificado valido)
         * 1-La "serie de certificado" del .CER uplodeado debe ser igual a "$cerFilePath" (investigar como obtener la serie de un .CER)
         * 2-Debe buscarse un registro en la tabla rh_folios_cfd y rh_csd que contenga todos los parametros, excepto $cerFilePath
         */
        try{
            FacturaElectronica::validateIntegridadDeCertificado($cerFilePath, $tmpPemFilePath);
        }
        catch(Exception $exception){
            throw new Exception('El certificado no paso la prueba de integridad, asegurese de haber subido un Certificado valido');
        }
        // /*noCertificado*/ FacturaElectronica::validateNoCertificadoEscritoPorElContribuyenteContraElNoCertificadoDelCertificadoSubidoPorElContribuyente($cerFilePath, $tmpPemFilePath, $noCertificado);
        FacturaElectronica::validateFechasDeCertificado($tmpPemFilePath, $cerFilePath);
        //regresa el RFC asociado con el Certificado del SAT
        return FacturaElectronica::validateCertificadoConElSat($cerFilePath, 'pem.pem');
//        //1
//        $serie = Console::execOutput("openssl x509 -inform pem -in pem.pem -noout -startdate");
//        $n = "";
//        for i = 2 to length($noCertificado) step 2
//           $n .= substr($noCertificado,$i,1)
//        end for
//        serial = int($n)
//        //Termina 1
        //1
        //


        //2
        //Termina 2

    }

    //funciones para el alta de folios, usarla en el modulo de alta de folios
    public static function validateAltaDeCertificadosBackupConNoCertificado($tmpPemFilePath, $cerFilePath, $noCertificado){
        /*
         * Esta funcion se ejecuta despues de dar de alta folios, la primera validacion que se hace es:
         * 0-El certificado debe de ser integro (no haber sido modificado, ser un certificado valido)
         * 1-La "serie de certificado" del .CER uplodeado debe ser igual a "$cerFilePath" (investigar como obtener la serie de un .CER)
         * 2-Debe buscarse un registro en la tabla rh_folios_cfd y rh_csd que contenga todos los parametros, excepto $cerFilePath
         */
        try{
            FacturaElectronica::validateIntegridadDeCertificado($cerFilePath, $tmpPemFilePath);
        }
        catch(Exception $exception){
            throw new Exception('El certificado no paso la prueba de integridad, asegurese de haber subido un Certificado valido');
        }
        FacturaElectronica::validateNoCertificadoEscritoPorElContribuyenteContraElNoCertificadoDelCertificadoSubidoPorElContribuyente($cerFilePath, $tmpPemFilePath, $noCertificado);
        FacturaElectronica::validateFechasDeCertificado($tmpPemFilePath, $cerFilePath);
        //regresa el RFC asociado con el Certificado del SAT
        return FacturaElectronica::validateCertificadoConElSat($cerFilePath, 'pem.pem');
//        //1
//        $serie = Console::execOutput("openssl x509 -inform pem -in pem.pem -noout -startdate");
//        $n = "";
//        for i = 2 to length($noCertificado) step 2
//           $n .= substr($noCertificado,$i,1)
//        end for
//        serial = int($n)
//        //Termina 1
        //1
        //


        //2
        //Termina 2

    }

    public static function validateLlavePrivadaYContrasenaDeLlavePrivada($tmpPemFilePath, $keyFilePath, $contrasenaLlavePrivada){
        try{
//            Por si el password contiene caracter $ y no nos de error al ejecutar el comando en Linux (agarra $A como variable de ambiente)
//            $pos = strpos($haystack,$needle);
//
//            if($pos === false) {
//            // string needle NOT found in haystack
//            }
//            else {
//            // string needle found in haystack
//            }
            Console::execOutput("openssl pkcs8 -inform DER -in $keyFilePath -passin pass:'$contrasenaLlavePrivada' -out $tmpPemFilePath");
        }
        catch(Exception $exception){
            throw new Exception('La Contrasena de Llave Privada es incorrecta o la Llave Privada no paso la prueba de integridad');
        }
    }

    public static function validateLlavePrivadaYContrasenaDeLlavePrivadaBackup($keyFilePath, $contrasenaLlavePrivada) {
        try {
            Console::execOutput("openssl pkcs8 -inform DER -in $keyFilePath -passin pass:$contrasenaLlavePrivada > /dev/null");
        }
        catch(Exception $exception ){
            throw new Exception('La Contrasena de Llave Privada es incorrecta o la Llave Privada no paso la prueba de integridad');
        }
    }

    public static function saveInDbDatosDelCertificado($tmpPemFilePath, $cerFilePath, $contrasenaDeLlavePrivada, $rfc){
        global $db;
        $certificado_noCertificado = FacturaElectronica::getNumeroDeCertificado($tmpPemFilePath, $cerFilePath);
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $ssl = openssl_x509_parse(file_get_contents($tmpPemFilePath));
        $certificado_rfc = explode('/', $ssl['subject']['x500UniqueIdentifier']);
        $certificado_rfc = trim($certificado_rfc[0]);
        if($certificado_rfc!=$rfc)
            throw new Exception('El RFC del SAT y el RFC del Certificado no coincidieron');
        $certificado_fechaDeInicio = Converter::phpToMysqlTimestamp($ssl['validFrom_time_t']);
        $certificado_fechaDeFin = Converter::phpToMysqlTimestamp($ssl['validTo_time_t']);
        $emisor_nombre = mysql_real_escape_string($ssl['issuer']['CN']);
        $emisor_organizacion = mysql_real_escape_string($ssl['issuer']['O']);
        if($emisor_organizacion!='Servicio de Administración Tributaria')
            throw new Exception('El Certificado no fue emitido por el SAT');
        $emisor_departamento = mysql_real_escape_string($ssl['issuer']['OU']);
        $emisor_pais = mysql_real_escape_string($ssl['issuer']['C']);
        $emisor_estado = mysql_real_escape_string($ssl['issuer']['ST']);
        $asunto_cn = mysql_real_escape_string($ssl['subject']['CN']);
        $asunto_organizacion = mysql_real_escape_string($ssl['subject']['O']);
        $asunto_departamento = mysql_real_escape_string($ssl['subject']['OU']);
        //$asunto_pais
        //$asunto_estado
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        $sql = "insert into rh_factura_electronica_certificado
                  (certificado_no_certificado, certificado_contrasena_de_llave_privada, certificado_fecha_de_inicio,certificado_fecha_de_fin,emisor_nombre,emisor_organizacion,emisor_departamento,emisor_pais,emisor_estado,asunto_organizacion,asunto_departamento, certificado_rfc)
                  values ('$certificado_noCertificado', '$contrasenaDeLlavePrivada', '$certificado_fechaDeInicio', '$certificado_fechaDeFin', '$emisor_nombre', '$emisor_organizacion', '$emisor_departamento', '$emisor_pais', '$emisor_estado', '$asunto_organizacion', '$asunto_departamento', '$rfc')";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('No se pudo guardar el Certificado en la BD', 1);
        }
        return $certificado_noCertificado;
        //return $certificado_noCertificado;
    }

    public static function saveInDbDatosDelCertificadoBackup($tmpPemFilePath, $cerFilePath, $contrasenaDeLlavePrivada){
        global $db;
        //openssl x509 -inform pem -in pem.pem -serial -subject -issuer -email -modulus -ocspid -noout
        //aki me kede, la sig instruccion falla, checar porke
        $certificado_noCertificado = FacturaElectronica::getNumeroDeCertificado($tmpPemFilePath, $cerFilePath);
        $certificado_fechaDeInicio = Converter::phpToMysqlTimestamp(FacturaElectronica::getFechaDeInicioDeCertificado($tmpPemFilePath, $cerFilePath));
        $certificado_fechaDeFin = Converter::phpToMysqlTimestamp(FacturaElectronica::getFechaDeFinDeCertificado($tmpPemFilePath, $cerFilePath));
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $emisor = explode('/', Console::execOutput("openssl x509 -inform pem -in $tmpPemFilePath -noout -issuer"));
        $emisor_nombre = mysql_real_escape_string(substr($emisor[4], 3));
        $emisor_organizacion = mysql_real_escape_string(substr($emisor[6], 2));
        $emisor_departamento = mysql_real_escape_string(substr($emisor[5], 3));
        $emisor_pais = mysql_real_escape_string(substr($emisor[3], 2));
        $emisor_estado = mysql_real_escape_string(substr($emisor[2], 3));
        $asunto = explode('/', Console::execOutput("openssl x509 -inform pem -in $tmpPemFilePath -noout -subject"));
        $asunto_cn = mysql_real_escape_string(substr($asunto[7], 3));
        $asunto_organizacion = mysql_real_escape_string(substr($asunto[8], 5));
        $asunto_departamento = mysql_real_escape_string(substr($asunto[6], 3));
        //$asunto_pais
        //$asunto_estado
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        $sql = "insert into rh_factura_electronica_certificado
                  (certificado_no_certificado, certificado_contrasena_de_llave_privada, certificado_fecha_de_inicio,certificado_fecha_de_fin,emisor_nombre,emisor_organizacion,emisor_departamento,emisor_pais,emisor_estado,asunto_organizacion,asunto_departamento)
                  values ('$certificado_noCertificado', '$contrasenaDeLlavePrivada', '$certificado_fechaDeInicio', '$certificado_fechaDeFin', '$emisor_nombre', '$emisor_organizacion', '$emisor_departamento', '$emisor_pais', '$emisor_estado', '$asunto_organizacion', '$asunto_departamento')";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_affected_rows($db)!=1){
            throw new Exception('No se pudo guardar el Certificado en la Base de Datos', 1);
        }
        //return $certificado_noCertificado;
    }


    public static function getCertificadoEnBase64($cerFilePath, $tmpPemFilePath){
        FacturaElectronica::createTemporaryPem($cerFilePath, $tmpPemFilePath);
        $certificadoEnBase64='';
        $output;
        $error;
        $command = "cat $tmpPemFilePath";
        exec($command, $output, $error);
        if($error){
            throw new Exception("El comando '$command' se ejecuto con errores.\\nCodigo de error del comando:\\n" . ($errorMessage?($errorMessage . ' (php)'):($error . ' (linux)')) . ($output[0]?('\nConsole output:\\n' . $output[0]):''));
        }
        else{
            for($i = 1; $i < (count($output) - 1); $i++){
                $certificadoEnBase64.= $output[$i];
            }
        }
        FacturaElectronica::deleteTemporaryPem($tmpPemFilePath);
        return $certificadoEnBase64;
    }

    public static function validateCertificadoConElSat($cerFilePath, $tmpPemFilePath){
        global $db;
        $noCertificado = FacturaElectronica::getNumeroDeCertificado($tmpPemFilePath, $cerFilePath);
        //Verificamos que el Certificado ya esta registrado con el SAT
        //Nota: podriamos obtener las fechas de inicio y fin y verificar que sean iguales a las que obtenemos del .cer con openssl como una validacion adicional
        $sql = "select rfc, edo_certificado from csd where no_serie = '$noCertificado' limit 1";
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('Este Certificado aun no esta registrado en el SAT, recuerde que debe esperar minimo 24 horas antes de registrarlos en la aplicacion', 1);
        }
        $rfc;
        $row = mysql_fetch_array($result);
        switch($row['edo_certificado']){
            case 'C':
                throw new Exception('El SAT ha marcado este certificado como caduco');
            break;
            case 'R':
                throw new Exception('El SAT ha marcado este certificado como revocado');
            break;
            case 'A':
                //Aprovado
                $rfc = $row['rfc'];
            break;
            default:
                throw new Exception('No se encontro el estado del certificado con el SAT');
            break;
        }
        
        return $rfc;
        //Termina Verificamos que el Certificado ya esta registrado con el SAT
    }

    //el rfc debe de obtenerse de la cuenta del contribuyente que crea la factura electronica, dato que debe de estar internamente guardado en la aplicacion del ERP (weberp)
    public static function validateInformacionDelContribuyenteContraLaDelSat($noCertificado, $rfc, $noAprobacion, $anoAprobacion, $folioInicial, $folioFinal, $serie){
        global $db;
        //mysql_connect("localhost", "root", "");
        //mysql_select_db("prueba_sat");
        $sql = "select c.rfc from csd c, folios_cfd f where f.rfc = c.rfc and c.no_serie = '$noCertificado' and f.no_aprobacion = $noAprobacion and f.ano_aprobacion = $anoAprobacion and f.folio_inicial = $folioInicial and f.folio_final = $folioFinal and f.rfc = $rfc and f.serie = '$serie' limit 1";
        //$result = mysql_query($query);
        $result = DB_query($sql,$db,'','',false,false);
        if(mysql_errno($db) || mysql_num_rows($result)!=1){
            throw new Exception('Este Certificado aun no esta registrado en el SAT, recuerde que debe esperar minimo 24 horas antes de registrarlos en la aplicacion', 1);
        }
        //if(mysql_num_rows($result) <= 0)
        //$estadoCertificado = mysql_fetch_array($result, MYSQLI_ASSOC);
        $estadoCertificado = mysql_fetch_array($result);
        $estadoCertificado = $estadoCertificado['edo_certificado'];
        switch($estadoCertificado){
            case 'C':
                throw new Exception('El SAT ha marcado este certificado como caduco');
            break;
            case 'R':
                throw new Exception('El SAT ha marcado este certificado como revocado');
            break;
            case 'A':
                //Aprovado
            break;
            default:
                throw new Exception('No se encontro el estado del certificado con el SAT');
            break;
        }
    }

}
?>