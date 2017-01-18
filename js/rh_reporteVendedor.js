//Jaime
function FechaDeInicioMayorAFechaDeFinException(mensaje){
    this.mensaje = mensaje
}
function FormatoInvalidoDeFechaException(mensaje, idTextbox){
    this.mensaje = mensaje
    this.idTextbox = idTextbox
}

function validar(){
    var textboxFechaDeInicio = document.getElementById("Fdesde")
    var textboxFechaDeFin = document.getElementById("Fhasta")
    var fechaInicio = textboxFechaDeInicio.value
    var fechaFin = textboxFechaDeFin.value
    try{
        validarFechasValidas(fechaInicio, fechaFin)
        document.menu.submit()
    }
    catch(excepcion){
        if(excepcion instanceof FechaDeInicioMayorAFechaDeFinException)
            document.getElementById("Fdesde").focus()
        if(excepcion instanceof FormatoInvalidoDeFechaException)
            document.getElementById(excepcion.idTextbox).focus()
        alert(excepcion.mensaje)
        return false
    }
}

function validarFechasValidas(fechaDeInicio, fechaDeFin){
    validarFormatoDeFecha(fechaDeInicio, document.getElementById("Fdesde"))
    validarFormatoDeFecha(fechaDeFin, document.getElementById("Fhasta"))
    var valoresDeFechaInicio = fechaDeInicio.split("/")
    var valoresDeFechaFin = fechaDeFin.split("/")
    var diaDeFechaInicio = parseInt(valoresDeFechaInicio[0])
    var mesDeFechaInicio = parseInt(valoresDeFechaInicio[1]) - 1
    var anioDeFechaInicio = parseInt(valoresDeFechaInicio[2])
    var diaDeFechaFin = parseInt(valoresDeFechaFin[0])
    var mesDeFechaFin = parseInt(valoresDeFechaFin[1]) -1
    var anioDeFechaFin = parseInt(valoresDeFechaFin[2])
    var fechaInicioJS = new Date(anioDeFechaInicio, mesDeFechaInicio, diaDeFechaInicio)
    var fechaFinJS = new Date(anioDeFechaFin, mesDeFechaFin, diaDeFechaFin)
    if(fechaInicioJS.getTime() > fechaFinJS.getTime())
        throw new FechaDeInicioMayorAFechaDeFinException("La fecha de inicio (" + fechaDeInicio + ") debe ser menor o igual a la fecha de fin (" + fechaDeFin + ")")
}

//formato de fecha: 12/01/2010, elementoTextbox es opcional (posicional el cursor en el elemento)
function validarFormatoDeFecha(fecha, elementoTextbox){
    if(!/(0[1-9]|[1-2][0-9]|3[0-1])\/(0[1-9]|1[0-2])\/\d{4}$/.test(fecha))
        throw new FormatoInvalidoDeFechaException("El formato de la fecha (" + fecha + ") es incorrecto\nEjemplo de formato valido: 12/01/2009", elementoTextbox.id)
}

function submitFormaConSalesman(salesman){
    document.getElementById("salesmanJ").value = salesman
    document.menu.submit()
}