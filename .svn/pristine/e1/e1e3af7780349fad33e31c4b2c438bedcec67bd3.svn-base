function jsonPHP(url, json) {
    var ajax
    var parameters = ""
    if (window.XMLHttpRequest)
        ajax=new XMLHttpRequest()
    else
        ajax=new ActiveXObject("Microsoft.XMLHTTP")
    for(var value in json)
        parameters += value + "=" + escape(json[value]) + "&"
    parameters = parameters.substring(0, parameters.length)
    ajax.open("POST", url, false)
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    ajax.send(parameters)
    try{
        return eval(ajax.responseText)
    }
    catch(e){
        return ajax.responseText
    }
}

function printFacturaElectronica(idFacturaElectronica){
    var Comprobante = jsonPHP('XMLFacturacionElectronica/index.php', {idFacturaElectronica:idFacturaElectronica, requestFacturacionElectronica:'printFacturaElectronica'})
    var cadenaOriginal = Comprobante[1].@attributes.cadenaOriginal
    Comprobante = Comprobante[0]
    if(!Comprobante.Conceptos.Concepto[0].@attributes.cantidad)
        throw ("No se pudo crear la Factura Electronica")
    var table = '<table width="100%" border="1"><tr><td colspan="2" align="right"><table border="1"><tr><th >Serie</th><td >' + (Comprobante.@attributes.serie||'') + '</td></tr><tr><th >Folio</th><td >' + Comprobante.@attributes.folio + '</td></tr><tr><th >Fecha</th><td >' + Comprobante.@attributes.fecha + '</td></tr><tr><th >Aprobacion</th><td >' + Comprobante.@attributes.noAprobacion + '</td></tr></table></td></tr><tr><td width="50%"><table width="100%" border="1"><tr><th colspan="2" >Emisor</th></tr><tr><th>RFC</th><td>' + Comprobante.Emisor.@attributes.rfc + '</td></tr><tr><th>Nombre</th><td>' + Comprobante.Emisor.@attributes.nombre + '</td></tr><tr><th colspan="2" >Domicilio</th></tr><tr><td colspan="2">' + Comprobante.Emisor.DomicilioFiscal.@attributes.calle + ' # ' + (Comprobante.Emisor.DomicilioFiscal.@attributes.noInterior||'') + ' - ' + (Comprobante.Emisor.DomicilioFiscal.@attributes.noExterior||'') + '</td></tr><tr><td colspan="2">' + (Comprobante.Emisor.DomicilioFiscal.@attributes.colonia||'') + '</td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2">' + Comprobante.Emisor.DomicilioFiscal.@attributes.municipio + ' ' + Comprobante.Emisor.DomicilioFiscal.@attributes.codigoPostal + '</td></tr><tr><td colspan="2">' + Comprobante.Emisor.DomicilioFiscal.@attributes.estado + '</td></tr><tr><td colspan="2">' + (Comprobante.Emisor.DomicilioFiscal.@attributes.localidad||'') + '</td></tr></table></td><td><table width="100%" border="1"><tr><th colspan="2" >Receptor</th></tr><tr><th>RFC</th><td>' + Comprobante.Receptor.@attributes.rfc + '</td></tr><tr><th>Nombre</th><td>' + (Comprobante.Receptor.@attributes.nombre||'') + '</td></tr><tr><th colspan="2" >Domicilio</th></tr><tr><td colspan="2">' + (Comprobante.Receptor.Domicilio.@attributes.calle||'') + ' # ' + (Comprobante.Receptor.Domicilio.@attributes.noExterior||'') + '</td></tr><tr><td colspan="2">' + (Comprobante.Receptor.Domicilio.@attributes.colonia||'') + '</td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2">' + (Comprobante.Receptor.Domicilio.@attributes.municipio||'') + ' ' + (Comprobante.Receptor.Domicilio.@attributes.codigoPostal||'') + '</td></tr><tr><td colspan="2">' + (Comprobante.Receptor.Domicilio.@attributes.estado||'') + '</td></tr><tr><td colspan="2">' + (Comprobante.Receptor.Domicilio.@attributes.localidad||'') + '</td></tr></table></td></tr>'
    table += '<tr><table width="100%" border="1"><tr><th>Cantidad</th><th>Descripcion</th><th>Precio</th><th>Importe</th></tr>'
    var conceptos = ''
    for(var i = 0; i < Comprobante.Conceptos.Concepto.length; i++)
        conceptos += '<tr><td align="center">' + Comprobante.Conceptos.Concepto[i].@attributes.cantidad + '</td><td>' + '' + Comprobante.Conceptos.Concepto[i].@attributes.descripcion + '</td><td>' + Comprobante.Conceptos.Concepto[i].@attributes.valorUnitario + '</td><td align="right">' + Comprobante.Conceptos.Concepto[i].@attributes.importe + '</td></tr>'
    table += conceptos
    table += '<tr><td colspan="3"></td><td></td><td></td></tr></table></tr></table><hr><table width="100%" border="1"><tr><th>Numero de serie del Certificado</th></tr><tr><td>' + Comprobante.@attributes.noCertificado + '</td></tr><tr><th>Cadena Original</th></tr><tr><td>' + cadenaOriginal + '</td></tr><tr><th>Sello Digital</th></tr><tr><td><small><small>' + Comprobante.@attributes.sello + '</small></small></td></tr>'
    table += '<tr><td align="center">Este documento es una impresión de un comprobarte fiscal digital</td></tr>'
    table += '</table>';
    var t = window.open('', 'Imprimiendo', '');
    t.document.write('<html><body>' + table + '</body></html>');
    t.print();
    t.close();
}