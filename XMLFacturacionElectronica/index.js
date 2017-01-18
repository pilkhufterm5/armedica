/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
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
    /*Extras
    ajax.setRequestHeader("Content-length", parameters.length)
    ajax.setRequestHeader("Connection", "close")
    Terminan extras*/
    ajax.send(parameters)
    try{
        //alert(ajax.responseText)
        return eval(ajax.responseText)
    }
    catch(e){
        return ajax.responseText
    }
}

function writeInvoiceTable(){
    //facturaElectronica
    document.getElementById('divButtonOkWriteInvoiceTable').style.display = 'none'
    var facturaElectronica = jsonPHP('index.php', {request: 'writeInvoiceTable'})[0]
    //document.getElementById('divTableFacturaElectronica').innerHTML = facturaElectronica.cadenaOriginal
    var comprobante = facturaElectronica.comprobante
    var table = '<table width="100%" border="1"><tr><td colspan="2" align="right"><table border="1"><tr><th >Serie</th><td >' + comprobante.serie + '</td></tr><tr><th >Folio</th><td >' + comprobante.folio + '</td></tr><tr><th >Fecha</th><td >' + comprobante.fecha + '</td></tr><tr><th >Aprobacion</th><td >' + comprobante.noAprobacion + '</td></tr></table></td></tr><tr><td width="50%"><table width="100%" border="1"><tr><th colspan="2" >Emisor</th></tr><tr><th>RFC</th><td>' + comprobante.emisor.rfc + '</td></tr><tr><th>Nombre</th><td>' + comprobante.emisor.nombre + '</td></tr><tr><th colspan="2" >Domicilio</th></tr><tr><td colspan="2">' + comprobante.emisor.domicilioFiscal.calle + ' # ' + comprobante.emisor.domicilioFiscal.noInterior + ' - ' + comprobante.emisor.domicilioFiscal.noExterior + '</td></tr><tr><td colspan="2">' + comprobante.emisor.domicilioFiscal.colonia + '</td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2">' + comprobante.emisor.domicilioFiscal.municipio + ' ' + comprobante.emisor.domicilioFiscal.codigoPostal + '</td></tr><tr><td colspan="2">' + comprobante.emisor.domicilioFiscal.estado + '</td></tr><tr><td colspan="2">' + comprobante.emisor.domicilioFiscal.localidad + '</td></tr></table></td><td><table width="100%" border="1"><tr><th colspan="2" >Receptor</th></tr><tr><th>RFC</th><td>' + comprobante.receptor.rfc + '</td></tr><tr><th>Nombre</th><td>' + comprobante.receptor.nombre + '</td></tr><tr><th colspan="2" >Domicilio</th></tr><tr><td colspan="2">' + comprobante.receptor.domicilio.calle + ' # ' + comprobante.receptor.domicilio.noExterior + '</td></tr><tr><td colspan="2">' + comprobante.receptor.domicilio.colonia + '</td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2"></td></tr><tr><td colspan="2">' + comprobante.receptor.domicilio.municipio + ' ' + comprobante.receptor.domicilio.codigoPostal + '</td></tr><tr><td colspan="2">' + comprobante.receptor.domicilio.estado + '</td></tr><tr><td colspan="2">' + comprobante.receptor.domicilio.localidad + '</td></tr></table></td></tr>'
    table += '<tr><table width="100%" border="1"><tr><th>Cantidad</th><th>Descripcion</th><th>Precio</th><th>Importe</th></tr>'
    var conceptos = ''
    for(var i = 0; i < comprobante.conceptos.length; i++)
        conceptos += '<tr><td align="center">' + comprobante.conceptos[i].cantidad + '</td><td>' + comprobante.conceptos[i].descripcion + '</td><td align="right">' + comprobante.conceptos[i].valorUnitario + '</td><td align="right">' + comprobante.conceptos[i].importe + '</td></tr>'
    table += conceptos
    table += '<tr><td colspan="3"></td><td></td><td></td></tr></table></tr></table><hr><table width="100%" border="1"><tr><th>Numero de serie del Certificado</th></tr><tr><td>' + comprobante.noCertificado + '</td></tr><tr><th>Cadena Original</th></tr><tr><td>' + facturaElectronica.cadenaOriginal + '</td></tr><tr><th>Sello Digital</th></tr><tr><td><small><small>' + comprobante.sello + '</small></small></td></tr></table>'
    document.getElementById('divTableFacturaElectronica').innerHTML = table
    //var conceptos = facturaElectronica.cadenaOriginal;

}