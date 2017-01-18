var ID_WS_CFD = 0
var FOLIO = 1
var SERIE = 2
var FECHA_EMISION = 3
var NOMBRE_DEL_RECEPTOR = 4
var RFC_DEL_RECEPTOR = 5
var SUBTOTAL = 6
var TOTAL = 7
var FECHA_CANCELACION = 8

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
    $('diaDe').value = '';
    $('diaA').value = '';
    if($('selectRfcDelReceptor'))
        $('selectRfcDelReceptor').selectedIndex = 0;
}

function displayAllRowsFromTable(divTable){
    var rows = $(divTable).rows
    for(var i = 0; i < rows.length; i++){
        rows[i].style.display = '';
    }
}

function loadSelectRfcDelReceptor(){
    var rows = $('tableCfd').rows
    var rfcs = new Array();
    var select = '<select id="selectRfcDelReceptor"><option></option>'
    for(var i = 0; i < rows.length; i++){
        var rfc = rows[i].cells[RFC_DEL_RECEPTOR].innerHTML
        if(!arrayContainsValue(rfcs, rfc)){
            select += '<option value="' + rfc + '">' + rfc + '</option>'
            rfcs.push(rfc)
        }
    }
    select += '</select>'
    $('divSelectRfcDelReceptor').innerHTML = select
}

function buscarCfds(){
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
    }
    else{
        displayAllRowsFromTable('tableCfd')
        return
    }
    displayAllRowsFromTable('tableCfd')
    var folioDe, folioA, diaDe, diaA, rfcDelReceptor
    folioDe = $('folioDe').value
    folioA = $('folioA').value
    diaDe = $('diaDe').value
    diaA = $('diaA').value
    rfcDelReceptor = $('selectRfcDelReceptor').value
    var table = $('tableCfd')
    var rows = table.rows
    for(var i = 0; i < rows.length; i++){
        var filterRow = false
        if(folioDe){
            var folio = parseInt(rows[i].cells[FOLIO].innerHTML)
            folioDe = parseInt(folioDe)
            if(!folioA){
                if(folio != folioDe)
                    filterRow = true
            }
            else
                if(folio < folioDe || folio > parseInt(folioA))
                    filterRow = true
        }
        if(!filterRow && diaDe){
            var dia = parseInt(rows[i].cells[FECHA_EMISION].innerHTML.substring(8,10))
            diaDe = parseInt(diaDe)
            if(!diaA){
                if(dia != diaDe)
                    filterRow = true
            }
            else
                if(dia < diaDe || dia > parseInt(diaA))
                    filterRow = true
        }
        if(!filterRow && rfcDelReceptor){
            var rfc = rows[i].cells[RFC_DEL_RECEPTOR].innerHTML
            if(rfcDelReceptor.indexOf(rfc) == -1)
                filterRow = true
        }
        if(filterRow)
            rows[i].style.display='none';
    }
}

function validateBuscarCfds(){
    var elementSelectReporteMensual = $('selectReporteMensual')
    if(!elementSelectReporteMensual.value)
        throw new InvalidInputInHtmlInputException('Favor de seleccionar un Periodo primero', elementSelectReporteMensual)

    var elementInputTextFolioDe = $('folioDe')
    var elementInputTextFolioA = $('folioA')
    if(elementInputTextFolioDe.value && !isPositiveInteger(elementInputTextFolioDe.value))
        throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextFolioDe)
    if(elementInputTextFolioA.value && !isPositiveInteger(elementInputTextFolioA.value))
        throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextFolioA)
    if(elementInputTextFolioDe.value && elementInputTextFolioA.value){
        if(elementInputTextFolioDe.value && !isPositiveInteger(elementInputTextFolioDe.value))
            throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextDiaDe)
        if(elementInputTextFolioA.value && !isPositiveInteger(elementInputTextFolioA.value) && parseInt(elementInputTextFolioA.value) > 31)
            throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo', elementInputTextFolioA)
    }
    else
        if(elementInputTextFolioA.value)
            throw new InvalidInputInHtmlInputException('Debe especificar tanto el "Folio de" como el "Folio a"', elementInputTextFolioDe)

    var elementInputTextDiaDe = $('diaDe')
    var elementInputTextDiaA = $('diaA')
    if(elementInputTextDiaDe.value && elementInputTextDiaA.value){
        if(elementInputTextDiaDe.value && !isPositiveInteger(elementInputTextDiaDe.value) && parseInt(elementInputTextDiaDe.value) > 31)
            throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo menor a 31', elementInputTextDiaDe)
        if(elementInputTextDiaA.value && !isPositiveInteger(elementInputTextDiaA.value) && parseInt(elementInputTextDiaA.value) > 31)
            throw new InvalidInputInHtmlInputException('Favor de poner un numero entero positivo menor a 31', elementInputTextDiaA)
    }
    else
        if(elementInputTextDiaA.value)
            throw new InvalidInputInHtmlInputException('Debe especificar tanto el "Dia de" como el "Dia a"', elementInputTextDiaDe)
}

function downloadReporteMensual(){
    try{
        validateDownloadReporteMensual()
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
    var yearAndMonth = $('selectReporteMensual').value
    var r = ajax('rh_j_cfd.php', {request: 'downloadReporteMensual', yearAndMonth: yearAndMonth})[0]	
    $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>'
    if(r.cssClass=='success')
        window.open (r.downloadPath)
}

function loadTableCfd(){
    var yearAndMonth = $('selectReporteMensual').value
    if(!yearAndMonth)
        return
    var r = ajax('rh_j_cfd.php', {
        request: 'loadTableCfd',
        yearAndMonth: yearAndMonth
    })
    loadTableWs(r, 'divTableCfd', 'tableCfd');
    loadSelectRfcDelReceptor()
    addColumnsToTableCfd()
}

function addColumnsToTableCfd(){
    var table = $('tableCfd')
    var th = document.createElement('th')
    th.appendChild(document.createTextNode('Email'))
    table.tHead.appendChild(th)
    th = document.createElement('th')
    th.appendChild(document.createTextNode('XML'))
    table.tHead.appendChild(th)
    th = document.createElement('th')
    th.appendChild(document.createTextNode('PDF'))
    table.tHead.appendChild(th)
    var rows = table.rows
    for(var i = 0; i < rows.length; i++){
        var row = rows[i]
        var cell = row.insertCell(row.cells.length)
        
        var img = document.createElement('img')
        img.src = 'css/silverwolf/images/email.gif'
        img.title = 'Email CFD'
        img.addEventListener("click",function(){emailCFD(this.parentNode.parentNode.cells[ID_WS_CFD].innerHTML)},false)
        cell.appendChild(img);

        cell = row.insertCell(row.cells.length)
        img = document.createElement('img')
        img.src = 'images/xml.gif'
        img.title = 'Descargar XML'
        img.addEventListener("click",function(){downloadXml(this.parentNode.parentNode.cells)},false)
        cell.appendChild(img);

        cell = row.insertCell(row.cells.length)
        img = document.createElement('img')
        img.src = 'images/pdf.gif'
        img.title = 'Descargar PDF'
        img.addEventListener("click",function(){downloadPdf(this.parentNode.parentNode.cells[ID_WS_CFD].innerHTML)},false)
        cell.appendChild(img);

//        cell.innerHTML = '<a href="/cfd/REALHOST_ERP308/EmailCustTrans.php?FromTransNo=1341&InvOrCredit=Invoice&isCfd=1" target="_blank"><img title="Click para  enviar la Factura por email" src="/cfd/REALHOST_ERP308/css/silverwolf/images/email.gif"></a>'
//        cell = row.insertCell(row.cells.length)
//        cell.innerHTML = '<a href="/cfd/REALHOST_ERP308/EmailCustTrans.php?FromTransNo=1341&InvOrCredit=Invoice&isCfd=1" target="_blank"><img title="Click para  enviar la Factura por email" src="/cfd/REALHOST_ERP308/css/silverwolf/images/email.gif"></a>'
    }
}

function emailCFD(idWsCfd){
    var r = ajax('rh_j_cfd.php', {request: 'emailCFD',idWsCfd: idWsCfd})[0]
    var url = 'EmailCustTrans.php?FromTransNo=' + r['fk_transno'] + '&InvOrCredit=Invoice&isCfd=1'
    window.open(url)
}

function downloadXml(cells){
    var idWsCfd = cells[ID_WS_CFD].innerHTML
    var folio = cells[FOLIO].innerHTML
    var serie = cells[SERIE].innerHTML
    var r = ajax('rh_j_cfd.php', {request: 'downloadXml',idWsCfd: idWsCfd})[0]
    var url = 'rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/facturasElectronicas/' + r['no_certificado'] + '/' + serie + folio + '-' + r['fk_transno'] + '.xml'
    window.open(url)
}

function downloadPdf(idWsCfd){
    var r = ajax('rh_j_cfd.php', {request: 'downloadPdf',idWsCfd: idWsCfd})[0]
    var url = 'rh_printFE.php?transno=' + r['fk_transno']
    window.open(url)
}

function loadSelectReporteMensual(){
    var select = '<select id="selectReporteMensual" onchange="loadTableCfd()"><option></option>'
    var r = ajax('rh_j_cfd.php', {
        request: 'loadSelectReporteMensual'
    })
    for(var i = 2; i < r.length; i++){
        var text = r[i][0] + '-' + (r[i][1])
        select += '<option value="' + text + '">' + intToMonth(r[i][1]) + ' ' + r[i][0] + '</option>'
    }
    select += '</select>'
    document.getElementById('divSelectReporteMensual').innerHTML = select
}
