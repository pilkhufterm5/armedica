function validateAltaDeFolio(){
    var elementInputTextValorInicial = $('inputTextValorInicial')
    var elementInputTextValorFinal = $('inputTextValorFinal')
    var elementInputTextSerie = $('inputTextSerie')
    var elementInputTextAnoAprobacion = $('inputTextAnoAprobacion')
    var elementInputTextNoAprobacion = $('inputTextNoAprobacion')
    if(!(elementInputTextValorInicial.value && isPositiveInteger(elementInputTextValorInicial.value)))
        throw new InvalidInputInHtmlInputException('El Valor Inicial es obligatorio y debe ser un numero entero positivo', elementInputTextValorInicial)
    if(!(elementInputTextValorFinal.value && isPositiveInteger(elementInputTextValorFinal.value)))
        throw new InvalidInputInHtmlInputException('El Valor Final es obligatorio y debe ser un numero entero positivo', elementInputTextValorFinal)
    if(elementInputTextSerie.value)
        if(!containsOnlyCapitalLetters(elementInputTextSerie.value))
            throw new InvalidInputInHtmlInputException('La Serie solo puede contener letras mayusculas', elementInputTextSerie)
    if(!(elementInputTextAnoAprobacion.value && isYear(elementInputTextAnoAprobacion.value)))
        throw new InvalidInputInHtmlInputException('El A;o de Aprobacion es obligatorio y debe ser un a;o con numeros', elementInputTextAnoAprobacion)
    if(!(elementInputTextNoAprobacion.value && isPositiveInteger(elementInputTextNoAprobacion.value)))
        throw new InvalidInputInHtmlInputException('El No de Aprobacion es obligatorio y debe ser un numero entero positivo', elementInputTextNoAprobacion)
}

function loadTableFolio(){
    var r = ajax('rh_j_folio.php', {
        request: 'loadTableFolio'
    })
    loadTableWs(r, 'divTableFolio', 'tableFolio');
}

function createFolio(){
    try{
        validateAltaDeFolio()
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
        return;
    }
    var valorInicial = $('inputTextValorInicial').value
    var valorFinal = $('inputTextValorFinal').value
    var serie = $('inputTextSerie').value
    var anoAprobacion = $('inputTextAnoAprobacion').value
    var noAprobacion = $('inputTextNoAprobacion').value

    var r = ajax('rh_j_folio.php', {request: 'createFolio', valorInicial:valorInicial, valorFinal:valorFinal, serie:serie, anoAprobacion:anoAprobacion, noAprobacion:noAprobacion})[0]
    $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
    if(r.cssClass=='success'){
        loadTableFolio()
    }
}