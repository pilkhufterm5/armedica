//Cells tableCfd
var ID_WS_CFD = 0
var FOLIO = 3
var SERIE = 2
var FECHA_EMISION = 5
//var NOMBRE_DEL_RECEPTOR = 1
var RFC_DEL_RECEPTOR = 1
//var SUBTOTAL = 6
var TOTAL = 6
var FECHA_CANCELACION = 8

//Cells tableSatInformacionEstadistica
var MES_2 = 0

//Cells divTableUser
var ID_WS_USER_3 = 0
var RFC_3 = 1

//others
var lastAnchorSelected

function createAutocomplete(){
    var value = $('inputTextRfcDelEmisor').value
    if(!value)
        return
    value += '%'

    var radioSelected = ($('radioRfc').checked?'satGetRfcsLike':($('radioNombre').checked?'satGetNombresLike':''))
    try{
        if(!radioSelected)
            throw new InvalidInputInHtmlInputException('Favor de marcar una opcion', $('radioRfc'))
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
    
    $('tdAutocomplete').innerHTML = '';
    var s = document.createElement('select')
    s.size = 10
    s.id = 'selectAutocomplete'
    var r = ajax('rh_j_cfd_sat.php', {request: radioSelected, rfc: value, nombre: value})
    var o
    if(r.length == 2){
        o = document.createElement('option')
        o.text = 'No hay resultados...'
        s.add(o,null)
    }
    for(var i = 2; i < r.length; i++){
        o = document.createElement('option')
        o.value = r[i][0]
        o.text = r[i][1]
        o.addEventListener("click",
        function(){
            $('tdIdEmisor').innerHTML = this.value
            $('tdAutocomplete').innerHTML = ''
            $('divConsulta').style.display='block'
            $('divRfcDelEmisor').style.display='none'
            $('tdRFCEmisor').innerHTML = this.text
            $('divRfcDelEmisorSeleccionado').style.display='block'
            $('divTableReporteMensual').style.display='block'
            $('divInformacionEstadistica').style.display='block'
            $('divUser').style.display='none'
            loadSelectSatReporteMensual(this.value)
            satInformacionEstadistica(this.value)
        }
        ,false)
        s.add(o,null)
    $('tdAutocomplete').appendChild(s)
    }
}

function modifyTableSatInformacionEstadistica(){
    var rows = $('tableSatInformacionEstadistica').rows
    for(var i = 0; i < rows.length; i++){
        var c = rows[i].cells
        var mesYAnio = c[this.MES_2].innerHTML.split('-')
        var mes = mesYAnio[0]
        var anio = mesYAnio[1]
        c[this.MES_2].innerHTML = intToMonth(mes) + " " + anio
    }
}

function satInformacionEstadistica(idUser){
    var r = ajax('rh_j_cfd_sat.php', {request: 'satInformacionEstadistica',idUser: idUser})
    loadTableWs(r, 'divTableSatInformacionEstadistica', 'tableSatInformacionEstadistica')
    modifyTableSatInformacionEstadistica()
//    var r2 = new Array(new Array("Mes", "Numero de CFDs","Total"), new Array("Mes", "Numero de CFDs","Total"))
//    for(var i = 1; i < 12; i++){
//        var mes = ''
//        var total = 0
//        var numeroDeCfds = 0
//        var monthFound = false
//        for(var j = 0; j < r.length; j++){
//            if(r[j][0] == i){
//                mes = intToMonth(r[j][0])
//                numeroDeCfds = r[j][1]
//                total = r[j][2]
//                monthFound = true
//                break
//            }
//        }
//        if(!monthFound)
//            mes = intToMonth(i)
//        r2.push(new Array(mes, numeroDeCfds, total))
//    }
//    loadTableWs(r2, 'divTableSatInformacionEstadistica', 'tableSatInformacionEstadistica')
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
            var dia = parseInt(rows[i].cells[FECHA_EMISION].innerHTML.substring(0,2))
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

function calcularSumas(){
    //formatCurrency
    var rows = $('tableCfd').rows
    var sumaTotal = 0
    var sumaSubtotal = 0
    for(var i = 0; i < rows.length; i++){
        if(rows[i].style.display==''){
            sumaTotal += parseFloat(i.cells[this.TOTAL].innerHTML)
            sumaSubtotal += parseFloat(i.cells[this.SUBTOTAL].innerHTML)
            //i.cells[this.TOTAL].innerHTML = formatCurrency(i.cells[this.TOTAL].innerHTML)
            //i.cells[this.SUBTOTAL].innerHTML = formatCurrency(i.cells[this.SUBTOTAL].innerHTML)
        }
    }
    alert(sumaTotal)
}

function modifyTableUser(){
    var r = $('tableUser').rows
    for(var i = 0; i < r.length; i++){
        var c = r[i].cells
        var id = c[this.ID_WS_USER_3].innerHTML

        var a = document.createElement('a')
        a.innerHTML = " " + id
        a.style.cursor = "pointer"
        a.addEventListener("click",
            function(){
                $('tdIdEmisor').innerHTML = this.innerHTML
                $('tdAutocomplete').innerHTML = ''
                $('divConsulta').style.display='block'
                $('divRfcDelEmisor').style.display='none'
                $('tdRFCEmisor').innerHTML = this.parentNode.parentNode.cells[RFC_3].innerHTML
                $('divRfcDelEmisorSeleccionado').style.display='block'
                $('divTableReporteMensual').style.display='block'
                $('divInformacionEstadistica').style.display='block'
                $('divUser').style.display='none'
                loadSelectSatReporteMensual(this.innerHTML)
                satInformacionEstadistica(this.innerHTML)
            }
        ,false)
        c[this.ID_WS_USER_3].innerHTML = ''
        c[this.ID_WS_USER_3].appendChild(a)
    }
}

function satLoadTableUser(limit){
    var r = ajax('rh_j_cfd_sat.php', {request: 'satLoadTableUser',limit: limit})
    loadTableWs(r, 'divTableUser', 'tableUser')
    modifyTableUser()
}

function loadPaginationTableUser(){
    $('divPaginationTableUser').innerHTML = ''
    var numberOfUsers = parseInt(ajax('rh_j_cfd_sat.php', {request: 'getNumberOfUsers'})[0].numberOfUsers)
    var pageSize = parseInt(ajax('rh_j_cfd_sat.php', {request: 'getPageSize'})[0].pageSize)
    var pages = Math.ceil(numberOfUsers/pageSize)
    if(pages > 1){
        var t = document.createElement('table')
        t.id = 'paginationTableUser'
        for(var i = 1; i <= pages; i++){
            var a = document.createElement('a')
            a.removeAttribute("style");
            a.innerHTML = " " + i
            a.style.cursor = "pointer"
            a.p1 = ((pageSize*i)-pageSize)
            a.addEventListener("click",
                function(){
                    if(lastAnchorSelected)
                        lastAnchorSelected.style.color = ""
                    satLoadTableUser(this.p1)
                    this.style.color = "00FFFF";
                    lastAnchorSelected = this
                }
            ,false)
            $('divPaginationTableUser').appendChild(a)
        }
    }
}

function satLoadTableCfd(){
    var yearAndMonth = $('selectReporteMensual').value
    var idUser = $('tdIdEmisor').innerHTML
    if(!yearAndMonth)
        return
    var r = ajax('rh_j_cfd_sat.php', {
        request: 'satLoadTableCfd',
        idUser: idUser,
        yearAndMonth: yearAndMonth
    })
    loadTableWs(r, 'divTableCfd', 'tableCfd');
    loadSelectRfcDelReceptor()
    addColumnsToTableCfd()
}

function addColumnsToTableCfd(){
    var table = $('tableCfd')
    var th = document.createElement('th')
    th.appendChild(document.createTextNode('XML'))
    table.tHead.appendChild(th)
    var rows = table.rows
    for(var i = 0; i < rows.length; i++){
        var row = rows[i]
        var cell = row.insertCell(row.cells.length)
        var img = document.createElement('img')
        img.src = 'images/xml.gif'
        img.title = 'Descargar XML'
        img.addEventListener("click",function(){satDownloadXml(this.parentNode.parentNode.cells[ID_WS_CFD].innerHTML)},false)
        cell.appendChild(img);
    }
}

function satDownloadXml(idWsCfd){
    ajax('rh_j_cfd_sat.php', {request: 'satDownloadXml',idWsCfd: idWsCfd})
    var url = 'rh_j_downloadFacturaElectronicaXML.php?downloadPath=XMLFacturacionElectronica/tmp/sat.xml'
    window.open(url)
}

function loadSelectSatReporteMensual(idUser){
    var select = document.createElement('select')
    select.id = 'selectReporteMensual'
    select.addEventListener("change",function(){satLoadTableCfd()},false)
    select.add(document.createElement('option'), null)
    var r = ajax('rh_j_cfd_sat.php', {
        request: 'satLoadSelectReporteMensual',
        idUser: idUser
    })
    for(var i = 2; i < r.length; i++){
        var o = document.createElement('option')
        var text = r[i][0] + '-' + (r[i][1])
        o.value = text
        o.text = intToMonth(r[i][1]) + ' ' + r[i][0]
        select.add(o, null)
    }
    $('divSelectReporteMensual').appendChild(select)
}