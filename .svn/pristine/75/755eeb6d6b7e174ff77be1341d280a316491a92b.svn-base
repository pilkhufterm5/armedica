//Cfd
function validateDownloadReporteMensual(){
    var elementSelectReporteMensual = $('selectReporteMensual')
    if(!(elementSelectReporteMensual.value))
        throw new InvalidInputInHtmlInputException('El Ano y Mes son obligatorios', elementSelectReporteMensual)
}

function hideFiltro(){
    if($('divFiltroContent').style.display=='none'){
        $('aFiltro').innerHTML = '- Filtro'
        $('divFiltroContent').style.display='block'
    }
    else{
        $('aFiltro').innerHTML = '+ Filtro'
        $('divFiltroContent').style.display='none'
    }
}

function limpiarFiltro(){
    $('folioDe').value = '';
    $('folioA').value = '';
    $('fechaDe').value = '';
    $('fechaA').value = '';
    $('rfcDelReceptor').value = '';
    $('selectReporteMensual').options[0].selected = true;
}

function buscarCfds(){
    var folioDe = '', folioA = '', fechaDe = '', fechaA = '', rfcDelReceptor = '', yearAndMonth = '', rfcDelEmisor = $('selectRfcDelEmisor').value
    if($('aFiltro').innerHTML!='+ Filtro'){
        try{
            validateBuscarCfds()
        }
        catch(exception){
            if(exception instanceof InvalidInputInHtmlInputException){
                try{
                    exception.element.focus()
                    exception.element.select()
                }
                catch(e){}
                inlineMsg(exception.element.id, exception.message);
            }
            else
                alert(exception)
            return
        }
        folioDe = $('folioDe').value
        folioA = $('folioA').value
        fechaDe = $('fechaDe').value
        fechaA = $('fechaA').value
        rfcDelReceptor = $('rfcDelReceptor').value
        yearAndMonth = $('selectReporteMensual').value
    }
    var consulta =  {
        folioDe:folioDe,
        folioA:folioA,
        fechaDe:fechaDe,
        fechaA:fechaA,
        rfcDelReceptor:rfcDelReceptor,
        rfcDelEmisor:rfcDelEmisor,
        yearAndMonth:yearAndMonth}
    loadTableCfd(consulta)
}

function guardarRfcEmisorYMostrarFiltro(){
    var rfcEmisor = $('selectRfcDelEmisor').value
    $('divConsulta').style.display='block'
    $('divRfcDelEmisor').style.display='none'
    var nameAndRfcFromEmisor = $('selectRfcDelEmisor').options[$('selectRfcDelEmisor').selectedIndex].text
    $('tdNombreEmisor').innerHTML = nameAndRfcFromEmisor.substring(nameAndRfcFromEmisor.indexOf('-')+1, nameAndRfcFromEmisor.length)
    $('tdRFCEmisor').innerHTML = rfcEmisor
    $('divRfcDelEmisorSeleccionado').style.display='block'
    loadSelectReporteMensual(rfcEmisor)
}

function validateBuscarCfds(){
    //var folioDe = '', folioA = '', fechaDe = '', fechaA = '', rfcDelReceptor = ''
    var elementInputTextFolioDe = $('folioDe')
    if(elementInputTextFolioDe.value && !isPositiveInteger(elementInputTextFolioDe.value))
        throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextFolioDe)
    var elementInputTextFolioA = $('folioA')
    if(elementInputTextFolioA.value && !isPositiveInteger(elementInputTextFolioA.value))
        throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextFolioA)
    var elementInputTextFechaDe = $('fechaDe')
    var elementInputTextFechaA = $('fechaA')
    if(elementInputTextFechaDe.value && elementInputTextFechaA.value){
        if(elementInputTextFechaDe.value && !isValidDate(new Date(elementInputTextFechaDe.value)))
            throw new InvalidInputInHtmlInputException('Favor de poner una fecha valida', elementInputTextFechaDe)
        if(elementInputTextFechaA.value && !isValidDate(new Date(elementInputTextFechaA.value)))
            throw new InvalidInputInHtmlInputException('Favor de poner una fecha valida', elementInputTextFechaA)
    }
    var elementInputTextRfcDelReceptor = $('rfcDelReceptor')
    if(elementInputTextRfcDelReceptor.value && !isRfc(elementInputTextRfcDelReceptor.value))
        throw new InvalidInputInHtmlInputException('Favor de poner un RFC valido', elementInputTextRfcDelReceptor)
}

function loadSelectRfcDelEmisor(){
    var select = '<select id="selectRfcDelEmisor" onchange="guardarRfcEmisorYMostrarFiltro()"><option></option>'
    var r = ajax('rh_j_cfd_1.php', {request: 'loadSelectRfcDelEmisor'})
    for(var i = 0; i < r.length; i++){
        var text = r[i].rfcEmisor
        select += '<option value="' + text.substring(0, text.indexOf('-')-1) + '">' + text + '</option>'
    }
    select += '</select>'
    document.getElementById('divSelectRfcDelEmisor').innerHTML = select
}

function loadTableCfd(consulta){
    var columns = new Array('Folio', 'Serie', 'Expedida', 'Nombre del Cliente', 'RFC', 'SubTotal','Total', 'IVA', 'Estado', 'Cancelado ', 'XML')//,'Cancelar')
    var r
    r = ajax('rh_j_cfd_1.php', {
        request: 'loadTableCfd',
        consulta:true,
        folioDe:consulta.folioDe,
        folioA:consulta.folioA,
        fechaDe:consulta.fechaDe,
        fechaA:consulta.fechaA,
        rfcDelReceptor:consulta.rfcDelReceptor,
        rfcDelEmisor:consulta.rfcDelEmisor,
        yearAndMonth:consulta.yearAndMonth
    })
    if(r.length==0){
        document.getElementById("divTableCfd").innerHTML = 'No hay CFDs'
        return
    }
    var table = '<table id="tableFolio" cellpadding="2" border="2" width="100%"><thead><tr>'
    for(var i = 0; i < columns.length; i++)
        table += '<th>' + columns[i] + '</th>'
    table += '</tr></thead>'
    var sumaSubtotal = 0
    var sumaTotal = 0
    for(i = 0; i < r.length; i++){
        table += i%2==0?'<tr class="OddTableRows">':'<tr class="EvenTableRows">'
        var c = r[i]
        table += '<td>' + c['comprobante_folio'] + '</td><td>' + c['comprobante_serie'] + '</td><td>' + c['comprobante_fecha'] + '</td><td>' + c['comprobante_receptor_nombre'] + '</td><td>' + c['comprobante_receptor_rfc'] + '</td><td>' + formatCurrency(c['comprobante_sub_total']) + '</td><td>' + formatCurrency(c['comprobante_total']) + '</td><td>' + formatCurrency(parseFloat(c['comprobante_total'])-parseFloat(c['comprobante_sub_total'])) + '</td><td>' + (c['extra_fecha_y_hora_de_cancelacion']=='0000-00-00 00:00:00'?'Vigente':'Cancelado') + '</td><td>' + (c['extra_fecha_y_hora_de_cancelacion']=='0000-00-00 00:00:00'?'N/A':c['extra_fecha_y_hora_de_cancelacion']) + '</td><td>' + ('<a href="rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/facturasElectronicas/' + c['xml'] + '.xml">Descargar</a>') + '</td></tr>'
        sumaSubtotal += parseFloat(c['comprobante_sub_total'])
        sumaTotal += parseFloat(c['comprobante_total'])
    }
    table += '<td></td><td></td><td></td><td></td><td></td><td><b>' + formatCurrency(sumaSubtotal) + '</b></td><td><b>' + formatCurrency(sumaTotal) + '</b></td><td><b>' + formatCurrency(sumaTotal - sumaSubtotal) + '</b></td><td></td><td></td>'
    table += '</table>'
    document.getElementById("divTableCfd").innerHTML = table
}

function loadSelectReporteMensual(rfcEmisor){
    var select = '<select id="selectReporteMensual""><option></option>'
    var r = ajax('rh_j_cfd_1.php', {request: 'loadSelectReporteMensual', rfcEmisor:rfcEmisor})
    for(var i = 0; i < r.length; i++){
        var text = r[i].year + '-' + (parseInt(r[i].month)<10?('0' + r[i].month):r[i].month)
        select += '<option value="' + text + '">' + text + '</option>'
    }
    select += '</select>'
    document.getElementById('divSelectReporteMensual').innerHTML = select
    var options = $('selectReporteMensual').options
    for(i = 1; i < options.length; i++){
        options[i].text = intToMonth(parseInt(options[i].text.substring(5, 7))) + ' ' + options[i].text.substring(0, 4)
    }
}