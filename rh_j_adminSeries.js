var ID_LOCATIONS = 0
var ID_SYSTYPES = 1
var LOCATION = 2
var SYSTYPE = 3
var SERIES_Y_CERTIFICADOS = 4
var ADD_SERIE = 5

function loadTableAdminSeries(){
    $('divTableAdminSeries').innerHTML = ''
    var columns = new Array('idLocations', 'idSystypes', 'Localizacion', 'Tipo CFD', 'Series y Certificados', 'Agregar Serie')
    var r = ajax('rh_j_adminSeries.php', {request: 'loadTableAdminSeries'})
    var table = document.createElement('table')
    table.id = 'tableAdminSeries'
    table.border = 1
    var thead = document.createElement('thead')
    for(var i = 0; i < columns.length; i++){
        var th = document.createElement('th')
        th.appendChild(document.createTextNode(columns[i]))
        if(columns[i] == 'idLocations' || columns[i] == 'idSystypes')
            th.style.display = 'none'
        thead.appendChild(th)
    }
    table.appendChild(thead)
    var tbody = document.createElement('tbody')
    for(i = 0; i < r.length; i++){
        var c = r[i]
        var tr = document.createElement('tr')
        tr.className = i%2==0?'OddTableRows':'EvenTableRows'
        var tdIdLocations = document.createElement('td')
        tdIdLocations.appendChild(document.createTextNode(c['id_locations']))
        tdIdLocations.style.display = 'none'
        var tdIdSystypes = document.createElement('td')
        tdIdSystypes.appendChild(document.createTextNode(c['id_systypes']))
        tdIdSystypes.style.display = 'none'
        var tdLocation = document.createElement('td')
        tdLocation.appendChild(document.createTextNode(c['location']))
        var tdSystype = document.createElement('td')
        tdSystype.appendChild(document.createTextNode(c['systype']))
        var tdSeriesYCertificados = document.createElement('td')
        var tdAddSerie = document.createElement('td')
        tr.appendChild(tdIdLocations)
        tr.appendChild(tdIdSystypes)
        tr.appendChild(tdLocation)
        tr.appendChild(tdSystype)
        tr.appendChild(tdSeriesYCertificados)
        tr.appendChild(tdAddSerie)
        tbody.appendChild(tr)
    }
    table.appendChild(tbody)
    $('divTableAdminSeries').appendChild(table)
    loadSeriesYCertificadosInTableAdminSeries()
    loadSeriesInTableAdminSeries()
}

//function loadTableAdminSeries(){
//    $('divTableAdminSeries').innerHTML = ''
//    var columns = new Array('idLocations', 'idSystypes', 'Localizacion', 'Tipo CFD', 'Series y Certificados', 'Agregar Serie', 'Quitar Serie')
//    var r = ajax('rh_j_adminSeries.php', {request: 'loadTableAdminSeries'})
//    var table = '<table id="tableAdminSeries" cellpadding="2" border="2"><thead><tr>'
//    for(var i = 0; i < columns.length; i++)
//            table += '<th ' + (i == 0 || i == 1?'style="display:none"':'') + '>' + columns[i] + '</th>'
//    table += '</tr></thead>'
//    for(i = 0; i < r.length; i++){
//        var c = r[i]
//        table += i%2==0?'<tr class="OddTableRows">':'<tr class="EvenTableRows">'
//        table += '<td style="display:none" id="idLocations">' + c['id_locations'] + '</td><td style="display:none" id="idSystypes">' + c['id_systypes'] + '</td><td id="location">' + c['location'] + '</td><td id="systype">' + c['systype'] + '</td><td id=""seriesYCertificados></td><td id="addSerie"></td><td id="removeSerie"></td></tr>'
//    }
//    $('divTableAdminSeries').innerHTML = table
//    loadSeriesYCertificadosInTableAdminSeries()
//    loadSeriesInTableAdminSeries()
//}

function loadSeriesYCertificadosInTableAdminSeries(){
    var certificados = getCertificados()
    var r = ajax('rh_j_adminSeries.php', {request: 'loadSeriesYCertificadosInTableAdminSeries'})
    var rows = $('tableAdminSeries').rows

    var s = document.createElement('select')
    s.add(document.createElement('option'),null)
    if(r.length > 0){
        for(var k = 0; k < certificados.length; k++){
            var o = document.createElement('option')
            o.value = certificados[k].id
            o.text = getOu(certificados[k].subject) + '-' + certificados[k].noCertificado
            s.add(o,null)
        }
    }
    
    for(var i = 0; i < rows.length; i++){
        var c = rows[i].cells
        var t = document.createElement('table')
        for(var j = 0; j < r.length; j++){
            if(c[0].innerHTML == r[j].id_locations && c[1].innerHTML == r[j].id_systypes){
                var row = document.createElement('tr')
                var cellSerie = document.createElement('td')
                var cellCertificado = document.createElement('td')
                var cellQuitar = document.createElement('td')
                var linkQuitar = document.createElement('a')
                linkQuitar.innerHTML ="Quitar";
                linkQuitar.addEventListener("click",function(){deleteSerie(this)},false)
                cellQuitar.appendChild(linkQuitar)
                cellSerie.appendChild(document.createTextNode(r[j].serie))
                var sClone = s.cloneNode(true)
                sClone.addEventListener("change",function(){updateCertificado(this)},false)
                for(var op = 0; op < sClone.options.length; op++){
                    if(sClone.options[op].value == r[j].id_ws_csd){
                        sClone.options[op].selected = true
                        break
                    }
                }
                cellCertificado.appendChild(sClone)
                row.appendChild(cellSerie)
                row.appendChild(cellCertificado)
                row.appendChild(cellQuitar)
                t.appendChild(row)
            }
        }
        c[4].appendChild(t)
    }
}

function loadSeriesInTableAdminSeries(){
    var series = getSeries()
    series.sort()
    var s = document.createElement('select')
    s.add(document.createElement('option'),null)
    for(var i = 0; i < series.length; i++){
        var o = document.createElement('option')
        o.value = series[i]
        o.text = series[i]
        s.add(o,null)
    }
    var rows = $('tableAdminSeries').rows
    for(i = 0; i < rows.length; i++){
        var sClone = s.cloneNode(true)
        sClone.addEventListener("change",function(){addSerie(this)},false)
        var rows2 = rows[i].cells[SERIES_Y_CERTIFICADOS].firstChild.rows
        for(var j = 0; j < rows2.length; j++){
            var ownedSerie = rows2[j].cells[ID_LOCATIONS].innerHTML
            if(arrayContainsValue(series, ownedSerie)){
                for(var k = 0; k < sClone.options.length; k++){
                    if(sClone.options[k].value == ownedSerie){
                        sClone.remove(sClone.options[k].index)
                    }
                }
            }
        }
        rows[i].cells[ADD_SERIE].appendChild(sClone)
    }
}

function getOu(s){
    /OU=([^,]+)/.exec(s)
    return RegExp.$1
}

function getSeries(){
    var series = new Array()
    var r = ajax('rh_j_adminSeries.php', {request: 'getSeries'})
    for(var i = 2; i < r.length; i++){
        if(!arrayContainsValue(series, r[i][3]))
            series.push(r[i][3])
    }
    return series
}

function getCertificados(){
    //certificados
    var certificados = new Array()
    var r = ajax('rh_j_adminSeries.php', {request: 'getCertificados'})
    for(var i = 2; i < r.length; i++){
        var id = r[i][0]
        var noCertificado = r[i][1]
        var estado = r[i][2]
        var fechaInicial = r[i][3]
        var fechaFinal = r[i][4]
        var issuer = r[i][5]
        var subject = r[i][6]
        certificados.push({id:id, noCertificado:noCertificado, estado:estado, fechaInicial:fechaInicial, fechaFinal:fechaFinal, issuer:issuer, subject:subject})
    }
    return certificados
}

function addSerie(select){
    var serie = select.value
    var idLocation = select.parentNode.parentNode.cells[ID_LOCATIONS].innerHTML
    var idSystype = select.parentNode.parentNode.cells[ID_SYSTYPES].innerHTML
    var r = ajax('rh_j_adminSeries.php', {request: 'addSerie', serie:serie, idLocation:idLocation, idSystype:idSystype})[0]
    loadTableAdminSeries()
    $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
}

function updateCertificado(select){
    var idWsCsd = select.value
    if(idWsCsd == '')
        idWsCsd = null
    var serie = select.parentNode.parentNode.cells[ID_LOCATIONS].innerHTML
    var idLocation = select.parentNode.parentNode.parentNode.parentNode.parentNode.cells[ID_LOCATIONS].innerHTML
    var idSystype = select.parentNode.parentNode.parentNode.parentNode.parentNode.cells[ID_SYSTYPES].innerHTML
    var r = ajax('rh_j_adminSeries.php', {request: 'updateCertificado', idWsCsd:idWsCsd, serie:serie, idLocation:idLocation, idSystype:idSystype})[0]
    $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
}

function deleteSerie(select){
    var serie = select.parentNode.parentNode.cells[ID_LOCATIONS].innerHTML
    var idLocation = select.parentNode.parentNode.parentNode.parentNode.parentNode.cells[ID_LOCATIONS].innerHTML
    var idSystype = select.parentNode.parentNode.parentNode.parentNode.parentNode.cells[ID_SYSTYPES].innerHTML
    var r = ajax('rh_j_adminSeries.php', {request: 'deleteSerie', serie:serie, idLocation:idLocation, idSystype:idSystype})[0]
    loadTableAdminSeries()
    $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
}