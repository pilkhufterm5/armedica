//Javascript, funciones globales

//Inline message
/* START OF MESSAGE SCRIPT */        var MSGTIMER = 20;var MSGSPEED = 5;var MSGOFFSET = 3;var MSGHIDE = 3;    /* build out the divs, set attributes and call the fade function */function inlineMsg(target,string,autohide) {var msg;var msgcontent;if(!document.getElementById('msg')) {msg = document.createElement('div');msg.id = 'msg';msgcontent = document.createElement('div');msgcontent.id = 'msgcontent';document.body.appendChild(msg);msg.appendChild(msgcontent);msg.style.filter = 'alpha(opacity=0)';msg.style.opacity = 0;msg.alpha = 0;} else {msg = document.getElementById('msg');msgcontent = document.getElementById('msgcontent');}msgcontent.innerHTML = string;msg.style.display = 'block';var msgheight = msg.offsetHeight;var targetdiv = document.getElementById(target);targetdiv.focus();var targetheight = targetdiv.offsetHeight;var targetwidth = targetdiv.offsetWidth;var topposition = topPosition(targetdiv) - ((msgheight - targetheight) / 2);var leftposition = leftPosition(targetdiv) + targetwidth + MSGOFFSET;msg.style.top = topposition + 'px';msg.style.left = leftposition + 'px';clearInterval(msg.timer);msg.timer = setInterval("fadeMsg(1)", MSGTIMER);if(!autohide) {autohide = MSGHIDE;}window.setTimeout("hideMsg()", (autohide * 1000));}    /* hide the form alert */function hideMsg(msg) {var msg = document.getElementById('msg');if(!msg.timer) {msg.timer = setInterval("fadeMsg(0)", MSGTIMER);}}    /* face the message box */function fadeMsg(flag) {if(flag == null) {flag = 1;}var msg = document.getElementById('msg');var value;if(flag == 1) {value = msg.alpha + MSGSPEED;} else {value = msg.alpha - MSGSPEED;}msg.alpha = value;msg.style.opacity = (value / 100);msg.style.filter = 'alpha(opacity=' + value + ')';if(value >= 99) {clearInterval(msg.timer);msg.timer = null;} else if(value <= 1) {msg.style.display = "none";clearInterval(msg.timer);}}    /* calculate the position of the element in relation to the left of the browser */function leftPosition(target) {var left = 0;if(target.offsetParent) {while(1) {left += target.offsetLeft;if(!target.offsetParent) {break;}target = target.offsetParent;}} else if(target.x) {left += target.x;}return left;}    /* calculate the position of the element in relation to the top of the browser window */function topPosition(target) {var top = 0;if(target.offsetParent) {while(1) {top += target.offsetTop;if(!target.offsetParent) {break;}target = target.offsetParent;}} else if(target.y) {top += target.y;}return top;}    /* preload the arrow */if(document.images) {arrow = new Image(7,80);arrow.src = "images/msg_arrow.gif";}
//Termina Inline message

function ajax(url, json) {
    var ajax
    var parameters = ''
    if (window.XMLHttpRequest)
        ajax=new XMLHttpRequest()
    else
        ajax=new ActiveXObject('Microsoft.XMLHTTP')
    for(var value in json)
        parameters += value + '=' + encodeURIComponent(json[value]) + '&'
    //parameters = parameters.substring(0, parameters.length)
    ajax.open('POST', url, false)
    //por default ya es utf8 :) //ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=UTF-8");
    ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
    ajax.setRequestHeader("Content-Length", parameters.length);
    //popHiddenLayer()
    ajax.send(parameters)
    //popHiddenLayer()
    var response
    try{
        response = eval(ajax.responseText)
    }
    catch(e){
        response = ajax.responseText
    }
    return response
}

function $_(e){
    return document.getElementById(e)
}

function arrayContainsValue(array, value){
    var i = array.length;
    while (i--) {
        if (array[i] === value) {
            return true;
        }
    }
    return false;
}

function getArrayElementLocation(array, value){
    var i = array.length;
    while (i--) {
        if (array[i] === value) {
            return i;
        }
    }
    return -1;
}

function loadTableWs(r, idDiv, idTable){
    //Valida si ocurrio un error
    if(!(r instanceof Array)){
        document.getElementById(idDiv).innerHTML = r
        return
    }
    //\Valida si ocurrio un error
    document.getElementById(idDiv).innerHTML = ''
    if(r.length == 2){
        document.getElementById(idDiv).innerHTML = 'Aun no hay resultados'
        return;
    }
    var table = document.createElement('table')
    table.border = 1
    table.id = idTable;
    var a
    var hasOneColumn = !(r[1] instanceof Array)
    var th
    var thead = document.createElement('thead')
    if(hasOneColumn){
        th = document.createElement('th')
        th.appendChild(document.createTextNode(r[1]))
        thead.appendChild(th)
    }
    else
        for(a in r[1]){
            th = document.createElement('th')
            th.appendChild(document.createTextNode(r[1][a]))
            thead.appendChild(th)
        }
    table.appendChild(thead)
    var tbody = document.createElement('tbody')
    var i
    var tr
    var td
    if(hasOneColumn){
        for(i = 2; i < r.length; i++){
            tr = document.createElement('tr')
            td = document.createElement('td')
            td.appendChild(document.createTextNode(r[i]))
            tr.appendChild(td)
            tr.className = (i%2==0?'OddTableRows':'EvenTableRows')
            tbody.appendChild(tr);
        }
    }
    else
        for(i = 2; i < r.length; i++){
            tr = document.createElement('tr')
            for(a in r[i]){
                td = document.createElement('td')
                td.appendChild(document.createTextNode(r[i][a]))
                tr.appendChild(td)
                tr.className = (i%2==0?'OddTableRows':'EvenTableRows')
            }
            tbody.appendChild(tr);
        }
    table.appendChild(tbody)
    document.getElementById(idDiv).appendChild(table)
}

//function loadTableWs(r, idDiv, idTable){
//    //@todo cambiar la operacion para que utilize dom
//    if(r.length == 2){
//        document.getElementById(idDiv).innerHTML = 'Aun no hay Sellos'
//        return;
//    }
//    var table = '<table id="' + idTable + '" cellpadding="2" border="2" width="100%"><thead><tr>'
//    var a
//    var hasOneColumn = !(r[1] instanceof Array)
//    if(hasOneColumn)
//        table += '<th>' + r[1] + '</th>'
//    else
//        for(a in r[1])
//            table += '<th>' + r[1][a] + '</th>'
//    table += '</tr></thead>'
//    var i
//    //@todo si el valor d
//    if(hasOneColumn)
//        for(i = 2; i < r.length; i++)
//            table += i%2==0?'<tr class="OddTableRows">':'<tr class="EvenTableRows"><td>' + r[i] + '</td></tr>'
//    else
//        for(i = 2; i < r.length; i++){
//            table += i%2==0?'<tr class="OddTableRows">':'<tr class="EvenTableRows">'
//            for(a in r[i])
//                table += '<td>' + r[i][a] + '</td>'
//            table += '</tr>'
//        }
//    table += '</table>'
//    document.getElementById(idDiv).innerHTML = table
//}

function loadTableWs2(r, idDiv, idTable){
    if(r.length == 2){
        document.getElementById(idDiv).innerHTML = 'Aun no hay Sellos'
        return;
    }
    var table = '<table id="' + idTable + '" cellpadding="2" border="2" width="100%"><thead><tr>'
    for(var i = 0; i < r[1].length; i++)
        table += '<th>' + r[1][i] + '</th>'
    table += '</tr></thead>'
    for(i = 2; i < r.length; i++){
        table += i%2==0?'<tr class="OddTableRows">':'<tr class="EvenTableRows">'
        for(var j = 0; j < r[i].length; i++)
            table += '<td>' + r[i][j] + '</td>'
        table += '</tr>'
    }
    table += '</table>'
    document.getElementById(idDiv).innerHTML = table
}

function InvalidInputInHtmlInputException(message, element){
    this.message = message
    this.element = element
}

function isPositiveInteger(positiveInteger){
    if(/(^[1-9]$)|(^[1-9]\d+$)/.test(positiveInteger))
        return true
    else
        return false
}

function isYear(year){
    if(/^\d{4}$/.test(year))
        return true
    else
        return false
}

function containsOnlyCapitalLetters(string){
    if(/^([A-Z]|Ñ)+$/.test(string))
        return true
    else
        return false
}

function containsOnlyNumbers(string){
    if(/^([0-9])+$/.test(string))
        return true
    else
        return false
}

function isEmailAddress(emailAddress){
    if(/^[a-zA-Z0-9](\w|\.)*@([a-zA-Z0-9]+(\.)[a-zA-Z0-9]+)+$/.test(emailAddress))
        return true
    else
        return false
}

function endsWith(string, finalString){
    var re = new RegExp(finalString + '$');
    if(re.test(string))
        return true
    else
        return false
}

function loadSelectInDiv(table, valueColumn, optionColumn, div, id, page, request){
    var select = '<select ' + (id?'id="'+id+'"':'') + '><option></option>'
    var r = ajax(page, {request: request, table:table, valueColumn:valueColumn, optionColumn:optionColumn})
    for(var i = 0; i < r.length; i++)
            select += '<option value="' + r[i].value + '">' + r[i].my_option + '</option>'
    select += '</select>'
    document.getElementById(div).innerHTML = select
}

function getSelect(table, valueColumn, optionColumn, page, request){
    var select = '<option></option>'
    var r = ajax(page, {request: request, table:table, valueColumn:valueColumn, optionColumn:optionColumn})
    for(var i = 0; i < r.length; i++)
            select += '<option value="' + r[i].value + '">' + r[i].my_option + '</option>'
    return select
}

function loadSelectFromJson(selectId, r){
    var s = document.createElement('select')
    s.add(document.createElement('option'),null)
    if(r.length > 0){
        for(var i = 0; i < r.length; i++){
            var o = document.createElement('option')
            var onValue = true
            for(var e in r[i]){
                if(onValue){
                    o.value = r[i][e]
                    onValue = false
                }
                else
                    o.text = r[i][e]
            }
            s.add(o,null)
        }
    }
    s.id = selectId
    return s
}

function popHiddenLayer() {
    var element = document.getElementById('hiddenLayer');
    if(element.className=='POP_LAYER')
        element.className = 'POP_LAYER_NONE'
    else
        element.className = 'POP_LAYER'
}

function intToMonth(integer){
    var m = new Array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre')
    return m[--integer];
}

function trim(s){
    return s.replace(/^\s+|\s+$/g,"");
}

function formatCurrency(num) {
    num = num.toString().replace(/\$|\,/g,'');
    if(isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num*100+0.50000000001);
    cents = num%100;
    num = Math.floor(num/100).toString();
    if(cents<10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
        num = num.substring(0,num.length-(4*i+3))+','+
        num.substring(num.length-(4*i+3));
    return (((sign)?'':'-') + '$' + num + '.' + cents);
}

function isValidDate(d) {
  if ( Object.prototype.toString.call(d) !== "[object Date]" )
    return false;
  return !isNaN(d.getTime());
}

function isRfc(rfc){
    var re = /^[a-zA-Z]{3,4}\d{6}[a-zA-Z0-9]{3}$/;
    return re.test(rfc)
}

function getInputTextElements(){
    var a = new Array();
    var inputArray = document.getElementsByTagName("input");
    for(var index = 0; index < inputArray.length; index++)
        if(inputArray[index].type == 'text')
            a.push(inputArray[index])
    return a;
}

function toUpper(e){
    var targ;
    if(!e)
        var e = window.event;
    if (e.target)
        targ = e.target;
    else
        if (e.srcElement) targ = e.srcElement;
    if (targ.nodeType == 3) // defeat Safari bug
            targ = targ.parentNode;
    targ.value = targ.value.toUpperCase()
}

function setInputTextToUpper(){
    var inputArray = getInputTextElements();
    //inputArray.push(document.getElementsByTagName("textarea")) //no jala :S
    for(var index = 0; index < inputArray.length; index++)
        inputArray[index].onkeyup = toUpper;
}

function v(x){
    if(x)
        return true
    else
        if(x===0)
            return true
        else
            return false
}
