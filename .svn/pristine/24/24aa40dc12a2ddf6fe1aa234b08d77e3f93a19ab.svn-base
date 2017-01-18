//Sello
function validateAltaDeSello(){
// /*noCertificado*/    var elementInputTextNoCertificado = $('inputTextNoCertificado')
    var elementInputFileCertificado = $('inputFileCertificado')
    var elementInputFileLlavePrivada = $('inputFileLlavePrivada')
    var elementInputPasswordContrasenaDeLlavePrivada = $('inputPasswordContrasenaDeLlavePrivada')
// /*noCertificado*/   if(!(elementInputTextNoCertificado.value && containsOnlyNumbers(elementInputTextNoCertificado.value)))
// /*noCertificado*/       throw new InvalidInputInHtmlInputException('El Numero de Serie es obligatorio y solo puede contener numeros', elementInputTextNoCertificado)
    if(!(elementInputFileCertificado.value && endsWith(elementInputFileCertificado.value, '\\.cer')))
        throw new InvalidInputInHtmlInputException('El Certificado es obligatorio y debe terminar con .cer', elementInputFileCertificado)
    if(!(elementInputFileLlavePrivada.value && endsWith(elementInputFileLlavePrivada.value, '\\.key')))
        throw new InvalidInputInHtmlInputException('La Llave Privada es obligatoria y debe terminar con .key', elementInputFileLlavePrivada)
    if(!elementInputPasswordContrasenaDeLlavePrivada.value)
        throw new InvalidInputInHtmlInputException('La Contraseña de Llave Privada es obligatoria', elementInputPasswordContrasenaDeLlavePrivada)
}

function loadTableSello(){
    var r = ajax('rh_j_sello.php', {
        request: 'loadTableSello'
    })
    loadTableWs(r, 'divTableSello', 'tableSello');
}

function createSello(){
    try{
        validateAltaDeSello()
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
    document.formAltaDeSello.submit()
}