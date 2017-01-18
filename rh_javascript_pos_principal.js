
/**
 * seccion principal de javascript del pos
 * Realhost 17 de Abril del 2010
 * Ricardo Abularach
 * */

$(document).ready(function() {
	//actualiza_timer();
	maxWindow();
        
        update_cliente(document.cliente.CustKeywords.options[document.cliente.CustKeywords.selectedIndex].value);
        
        //jQuery('#example').dataTable({
        //    "iDisplayLength": 150
        //});
        
        jQuery('.dataTables_filter').hide();
        jQuery('#example_filter').hide();
        jQuery('.dataTables_length').hide();
        jQuery('#example_length').hide();

	jQuery("#articulos").submit(function(){
		/*seccion de busqueda de los items*/
		var item = jQuery("#StockCode").val();
		var desc = jQuery("#Keywords").val();
		var cate = document.articulos.StockCat.options[document.articulos.StockCat.selectedIndex].value;
		var error = 0;
		var mensaje = "Tu solicitud no se puede procesar por:\n";

                if(cate == "-9" && (item.length == 0 && desc.length == 0)){
                    error = 1;
                    mensaje += " - Debes de Seleccionar alguna categoria\n";
                }
		
		//if((item == "" && desc == "") || (item.length == 0 && desc.length == 0) || (item == "%" || desc == "%")){
		//	error = 1;
		//	mensaje += " - Los Campos de Item y Descripcion se encuentran vacios\n";
		//}
			
		if(error == 0){
			/*se hace la busqueda via ajax*/
                        $("#resultados").html('<center><p>Buscando...</p><img src="rh_pos_archivos/wait.png"/></center>');
			jQuery.post("rh_pos_procesa.php",{type:"item", item:item, desc:desc, cate:cate}, function(ResBusqueda){
				var myObject = eval('(' + ResBusqueda + ')');
				var bandera_error = myObject.error;
				if(bandera_error == "si"){
					/*cuando se produce un error*/
                                        var error_tipo = myObject.tipoE
					alert(error_tipo);
                                        return false;
				}else{
				    /*cuando si se puede procesar la informacion*/
                                    var bandera_resultados = myObject.resultados;
                                    if(bandera_resultados == 0){
                                        alert('No se encontraron datos en tu busqueda, intenta de nuevo');
                                        $("#resultados").html('<p>No se encontraron datos en tu busqueda, intenta de nuevo</p>');
                                        return false;
                                    }else{
                                        var sql = myObject.sql
                                        if(bandera_resultados == 1){
                                            /*cuando unicamente hay 1 resultado en la busqueda que se agrege de forma automaticamente*/
                                            var stockid = myObject.item;/*este el que se agrega de forma automaticamente*/
                                            add_item(stockid);
                                        }else{
                                            /*ejecuta los resultados para traer los datos con los que se deben de  trabajar*/
                                            jQuery.post("rh_pos_procesa.php",{type:"sql", sql:sql}, function(ResBusqueda){
                                                jQuery("#resultados").html(ResBusqueda);
                                                //jQuery('#example').dataTable({
                                                //    "iDisplayLength": 150
                                                //});
                                                jQuery('.dataTables_filter').hide();
                                                jQuery('#example_filter').hide();
                                                jQuery('.dataTables_length').hide();
                                                jQuery('#example_length').hide();
                                            });
                                        }
                                        return false;
                                    }
				}
			});
			return false;
		}else{
			alert(mensaje);
			return false;
		}
		return false;
	});
});

function update_cliente(cliente){
    /*funcion que trae las sucursales*/
    jQuery.post("rh_pos_procesa.php",{type:"sucursal", cliente:cliente}, function(ResBusqueda){
        if(ResBusqueda == "no"){
            alert('No se encontro sucursal para el cliente seleccionado');
        }else{
            jQuery("#BranchCode").html(ResBusqueda);
            /*se trae los datos basicos*/
            var BranchCode = document.cliente.BranchCode.options[document.cliente.BranchCode.selectedIndex].value;
            update_sucursales(BranchCode);
        }
       return false;
    });
    return false;
}

function update_sucursales(sucursal){
    var clientee = document.cliente.CustKeywords.options[document.cliente.CustKeywords.selectedIndex].value;
    jQuery.post("rh_pos_procesa.php",{type:"UPsucursal", sucursal:sucursal, clientee:clientee}, function(ResBusqueda){
        //alert(ResBusqueda);
        //alert('Se ha actualizo el cliente y sucursal correctamente');
        return false;
    });
    return false;
}

function showKeyCode(e){
	alert("Inside function showKeyCode(e)");
	var keycode =(window.event) ? event.keyCode : e.keyCode;
	if(keycode == 116){
		event.keyCode = 0;
		event.returnValue = false;
		return false;
	}
}

window.history.forward(1);
document.onkeydown = my_onkeydown_handler;
function my_onkeydown_handler(){
	switch (event.keyCode){
		case 116 : // 'F5'
			event.returnValue = false;
			event.keyCode = 0;
			window.status = "We have disabled F5";
		break;
	}
} 

function checkKeyCode(evt){
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if(event.keyCode==116){
		evt.keyCode=0;
		return false
	}
}
document.onkeydown=checkKeyCode;

function catchFKeys() { 
f5key=116;
f1key=112;   
  if (window.event && window.event.keyCode == f1key) {
	alert("Help section")
    window.event.keyCode = 0;
  }
   if (window.event && window.event.keyCode == 0) {
    window.event.cancelBubble = true;
    window.event.returnValue = false;
    return false;
  }
}
document.onkeydown=catchFKeys;

function actualiza_timer(){
	momentoActual = new Date() 
	hora = momentoActual.getHours() 
	minuto = momentoActual.getMinutes() 
	segundo = momentoActual.getSeconds()
	horaImprimible = hora + ":" + minuto + ":" + segundo 
	$("#tiempo").html(horaImprimible);
	setTimeout("actualiza_timer()",1000);
}

function maxWindow() {
    window.moveTo(0, 0);
    if (document.all) {
		top.window.resizeTo(screen.availWidth, screen.availHeight);
    }else if (document.layers || document.getElementById) {
		if (top.window.outerHeight < screen.availHeight || top.window.outerWidth < screen.availWidth) {
			top.window.outerHeight = screen.availHeight;
            top.window.outerWidth = screen.availWidth;
        }
    }
}

function max(){
	window.moveTo(0,0);
	window.resizeTo(screen.width,screen.height);
} 

function confirma_salir(){
	if(!confirm("\u00BFSeguro?\n\u00A1Se perderan todos los cambios no guardados\u0021")){
		/*no hace nada*/
		return false;
	}else{
		var ventana = window.self;
		ventana.opener = window.self;
		ventana.close();
		return true;
	}
}

function get_extras(no, item){
        /*funcion para traer lo que son los extras*/
	/*aki se llama a las opciones extras para trabajar con ellas*/
        var extras = "";
        extras += "<table border=\"0\" width=\"100%\" style=\"border:0px; margin:0px;\">";
            extras += "<tr>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opMas\" class=\"botonn\" onclick=\"agrega_uno('"+no+"', '"+item+"');\">+</span></td>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opMen\" class=\"botonn\" onclick=\"quita_uno('"+no+"', '"+item+"');\">-</span></td>";
            extras += "</tr>";
            extras += "<tr>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opDes\" class=\"botonn\" onclick=\"agrega_descuento('"+no+"', '"+item+"');\">Descuento</span></td>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opCan\" class=\"botonn\" onclick=\"modifica_cantidad('"+no+"', '"+item+"');\">Cantidad</span></td>";
            extras += "</tr>";
            extras += "<tr>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opEli\" class=\"botonn\" onclick=\"elimina_uno('"+no+"', '"+item+"');\">Eliminar</span></td>";
                extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opPre\" class=\"botonn\" onclick=\"cambia_precio('"+no+"', '"+item+"');\">Precio</span></td>";
            extras += "</tr>";
            extras += "<tr>";
                extras += "<td colspan='2' valign=\"MIDDLE\" align=\"center\"><span id=\"opEli\" class=\"botonn\" onclick=\"add_serie('"+no+"', '"+item+"');\">Serie/Lote</span></td>";
                //extras += "<td valign=\"MIDDLE\" align=\"center\"><span id=\"opPre\" class=\"botonn\" onclick=\"cambia_precio('"+no+"', '"+item+"');\">Precio</span></td>";
            extras += "</tr>";
        extras += "</table>";
        $("#ventaE").html(extras);
	return false;
}

function agrega_uno(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    jQuery.post("rh_pos_procesa.php",{type:"unomasuno", no:no, item:item}, function(ResBusqueda){
        /*se envianlos datos con los cuales se va a trabajar*/
        if(ResBusqueda == "si"){
            /*se pudo efectuar la transaccion*/
            get_tabla();
            /**/
            get_total();
            /**/
            limpiar();
        }else{
            /*cuando no se puede efectuar la transaccion*/
            alert('No se pudo procesar tu peticion, intenta de nuevo');
            return false;
        }
        return false;
    });
    return false;
}

function quita_uno(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    jQuery.post("rh_pos_procesa.php",{type:"restaunoresta", no:no, item:item}, function(ResBusqueda){
        /*se envianlos datos con los cuales se va a trabajar*/
        if(ResBusqueda == "si"){
            /*se pudo efectuar la transaccion*/
            get_tabla();
            /**/
            get_total();
            /**/
            limpiar();
        }else{
            /*cuando no se puede efectuar la transaccion*/
            alert('No se pudo procesar tu peticion, intenta de nuevo');
            return false;
        }
        return false;
    });
    return false;
}

function agrega_descuento(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    /*validar la nueva cantidad para la cual se va a estar trabajando*/
    $.msgbox("<p>Para poder aplicar un descuento al articulo "+item+" es necesario permisos de administrador</p><br /><center><p style='font-size:9px; color:red'>NOTA:usuario@contrase&ntilde;a</p></center>", {
        type    : "prompt",
        inputs  : [
          {type: "password", label: "Acceso:", required: true}
        ],
        buttons : [
          {type: "submit", value: "OK"},
          {type: "cancel", value: "Exit"}
        ]
    },function(password){
       if(password){
            /*se hacen las validaciones correspondientes*/
            var patron = /(^[0-9a-zA-Z]*)@([0-9a-zA-Z]*)$/;
            if(!patron.test(password)){
                $.msgbox("Error en el formato, comprueba la cadena de validacion<br />No se aplico el descuento sobre el articulo "+item+" (error 1)", {type: "error"});
                return false;
            }
            /*se envian los datos via ajax*/
            jQuery.post("rh_pos_procesa.php",{type:"descuento", password:password}, function(ResBusqueda){
                if(ResBusqueda == "si"){
                    /*en caso que sea que si se tieen que pedir cual es la cantidad con la cual se va a trabajar*/
                    $.msgbox("<p>Porcentaje de Descuento para el articulo "+item+"</p><p style='font-size:9px; color:red'>El Descuento es en formato Porcentaje</p>", {
                        type: "prompt"
                    },function(result){
                        if(result){
                            /*aki se tiene que validar que verdaderamente cumpla con el formato del porcentaje*/
                            var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                            if(!ca_dena.test(result)){
                                $.msgbox("El formato de descuento no es correcto<br />No se aplico el descuento sobre el articulo "+item+" (error 2)", {type: "error"});
                                return false;
                            }

                            /*aki es donde efectua el cambio de los documentos*/
                            jQuery.post("rh_pos_procesa.php",{type:"Adescuento", result:result, no:no, item:item}, function(ResBusqued_a){
                               if(ResBusqued_a == "si"){
                                    /*que si*/
                                    get_tabla();
                                    /**/
                                    get_total();
                                    /**/
                                    limpiar();
                                   return true;
                               }else{
                                    /*cuando no*/
                                   $.msgbox("No se aplico descuento sobre el articulo "+item+" (error 3)", {type: "error"});
                                   return false;
                               }
                               return false;
                            });
                            return false;
                        }else{
                            $.msgbox("Para aplicar el porcentaje de descuento es necesario el porcentaje de descuento<br />No se aplico descuento sobre el articulo "+item+" (error 4)", {type: "error"});
                            return false;
                        }
                   });
                }else{
                    $.msgbox("Error en la Validacion de credenciales<br />No se aplico descuento sobre el articulo "+item+" (error 5)", {type: "error"});
                    return false;
                }
            });
       }else{
            $.msgbox("Para Aplicar el descuento al articulo es necesario permisos de administrador<br />No se aplico el descuento sobre el articulo "+item+" (error 6)", {type: "error"});
            return false;
       }
    });
    return false;
}

function modifica_cantidad(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    /*validar la nueva cantidad para la cual se va a estar trabajando*/
    var name=prompt("Ingresa la Nueva Cantidad","1");
    if (name!=null && name!=""){
        /*validar los datos con los que se estan trabajando*/
        //var patron = /^[1-9]?\d+(\.?\d)*$/;
        var patron = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
        if(!patron.test(name)){
            alert('La cadena que ingresaste: '+name+' no es valida');
            return false;
        }else{
            if(name <= 0){
                alert('La cadena que ingresaste: '+name+' no puede ser menor o igual que 0');
                return false;
            }
        }
    }else{
        /*marca error porque se encuentra vacio la informacion*/
        alert('Para poder cambiar la cantidad es necesario ingresarla previamente');
        return false;
    }

    jQuery.post("rh_pos_procesa.php",{type:"manualcantidad", no:no, item:item, name:name}, function(ResBusqueda){
        /*se envianlos datos con los cuales se va a trabajar*/
        if(ResBusqueda == "si"){
            /*se pudo efectuar la transaccion*/
            get_tabla();
            /**/
            get_total();
            /**/
            limpiar();
        }else{
            /*cuando no se puede efectuar la transaccion*/
            alert('No se pudo procesar tu peticion, intenta de nuevo');
            return false;
        }
        return false;
    });
    return false;
}

function elimina_uno(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    jQuery.post("rh_pos_procesa.php",{type:"deleteuno", no:no, item:item}, function(ResBusqueda){
        /*se envianlos datos con los cuales se va a trabajar*/
        if(ResBusqueda == "si"){
            /*se pudo efectuar la transaccion*/
            get_tabla();
            /**/
            get_total();
            /**/
            limpiar();
        }else{
            /*cuando no se puede efectuar la transaccion*/
            alert('No se pudo procesar tu peticion, intenta de nuevo');
            return false;
        }
        return false;
    });
    return false;
}

function cambia_precio(no, item){
    /*seccion para agregar de 1 en 1 items al item*/
    /*validar la nueva cantidad para la cual se va a estar trabajando*/
    $.msgbox("<p>Para poder cambiar el precio al articulo "+item+" es necesario permisos de administrador</p><br /><center><p style='font-size:9px; color:red'>NOTA:usuario@contrase&ntilde;a</p></center>", {
        type    : "prompt",
        inputs  : [
          {type: "password", label: "Acceso:", required: true}
        ],
        buttons : [
          {type: "submit", value: "OK"},
          {type: "cancel", value: "Exit"}
        ]
    },function(password){
       if(password){
            /*se hacen las validaciones correspondientes*/
            var patron = /(^[0-9a-zA-Z]*)@([0-9a-zA-Z]*)$/;
            if(!patron.test(password)){
                $.msgbox("Error en el formato, comprueba la cadena de validacion<br />No se aplico el precio sobre el articulo "+item+" (error 1)", {type: "error"});
                return false;
            }
            /*se envian los datos via ajax*/
            jQuery.post("rh_pos_procesa.php",{type:"descuento", password:password}, function(ResBusqueda){
                if(ResBusqueda == "si"){
                    /*en caso que sea que si se tieen que pedir cual es la cantidad con la cual se va a trabajar*/
                    $.msgbox("<p>Precio para el articulo "+item+"</p>", {
                        type: "prompt"
                    },function(result){
                        if(result){
                            /*aki se tiene que validar que verdaderamente cumpla con el formato del porcentaje*/
                            var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                            if(!ca_dena.test(result)){
                                $.msgbox("El formato del Precio no es correcto<br />No se aplico el precio sobre el articulo "+item+" (error 2)", {type: "error"});
                                return false;
                            }

                            /*aki es donde efectua el cambio de los documentos*/
                            jQuery.post("rh_pos_procesa.php",{type:"cPrecio", result:result, no:no, item:item}, function(ResBusqued_a){
                               if(ResBusqued_a == "si"){
                                    /*que si*/
                                    get_tabla();
                                    /**/
                                    get_total();
                                    /**/
                                    limpiar();
                                   return true;
                               }else{
                                    /*cuando no*/
                                   $.msgbox("No se aplico el precio sobre el articulo "+item+" (error 3)", {type: "error"});
                                   return false;
                               }
                               return false;
                            });
                            return false;
                        }else{
                            $.msgbox("Para aplicar el precio es necesario el precio del articulo<br />No se aplico el precio sobre el articulo "+item+" (error 4)", {type: "error"});
                            return false;
                        }
                   });
                }else{
                    $.msgbox("Error en la Validacion de credenciales<br />No se aplico el precio sobre el articulo "+item+" (error 5)", {type: "error"});
                    return false;
                }
            });
       }else{
            $.msgbox("Para Aplicar el precio al articulo es necesario permisos de administrador<br />No se aplico el precio sobre el articulo "+item+" (error 6)", {type: "error"});
            return false;
       }
    });
    return false;
}

function add_item(item){
    jQuery.post("rh_pos_procesa.php",{type:"addItem", item:item}, function(ResBusqueda){
       if(ResBusqueda == "no"){
           alert('No se pudo procesar tu peticion\nIntenta de Nuevo');
           limpiar();
           return false;
       }else if(ResBusqueda == "stock"){
           /*otro error extra que puede ser el de cantidad de stock en el inventario*/
           alert('No Existe Stock en Almacen para vender el Articulo: '+item+'');
           //limpiar();
           return false;
       }else{
           /*para este punto ya se agrego el item al rray de seccion donde estan los demas items*/
           /*se llama a la funcion correspondiente para traer la tabla*/
           get_tabla();
           /*se llama a la funcion correspondiente para traer el total de las compras*/
           get_total();
           /*se llama a la funcion que limpia los datos*/
           limpiar();
           return false;
       }
       return false;
    });
    return false;
}

function limpiar(){
    document.articulos.StockCat.selectedIndex = 0;
    jQuery("#StockCode").val('');
    jQuery("#Keywords").val('');
    $("#resultados").html('<p>Sin Datos</p>');
    /*se remueve la clase de todo*/
    $("#ventaE").html('<p>Para acceder a este menu es necesario seleccionar un articulo previamente</p>');
}

function get_tabla(){
    jQuery.post("rh_pos_procesa.php",{type:"getTable"}, function(ResBusqueda){
       $("#productos").html(ResBusqueda);
    });
}

function get_total(){
    jQuery.post("rh_pos_procesa.php",{type:"getTotal"}, function(ResBusqueda){
       $("#pagarTotal").html(ResBusqueda);
    });
}

function load_compra(){
    $.msgbox("<p>Ingresar el numero de venta:</p>", {
        type: "prompt"
    },function(result){
        if(result){
            /*aki es donde se carga la venta*/
            jQuery.post("rh_pos_procesa.php",{type:"recuperarVenta", result:result}, function(ResBusqueda){
                if(ResBusqueda == "si"){
                    get_tabla();
                    /*se llama a la funcion correspondiente para traer el total de las compras*/
                    get_total();
                    /*se llama a la funcion que limpia los datos*/
                    limpiar();
                    return false;
                }else{
                    $.msgbox("La venta que buscas ha sido remplazada<br />Por Favor Intenta de Nuevo");
                    return false;
                }
            });
            return false;
        }else{
            $.msgbox("Para Recuperar la Venta es necesario ingresar el numero de Venta");
            return false;
        }
    });
    return false;
}

function save_compra(){
        /*se pone la comporbacion para poder guardar la venta*/
        $.msgbox("&iquest;Esta usted seguro, se guardara la venta?", {
          type: "confirm",
          buttons : [
            {type: "submit", value: "Yes"},
            {type: "submit", value: "No"}
          ]
        },function(result){
            if(result == "Yes"){
                /*cuando inicia*/
                jQuery.post("rh_pos_procesa.php",{type:"guardarVenta"}, function(ResBusqueda){
                    if(ResBusqueda == "no"){
                        $.msgbox("No se pudo guardar la venta");
                        return false;
                    }else{
                        $.msgbox("El numero de venta fue <b>"+ResBusqueda+"</b>; para poder recuperar la venta es necesario dicho numero");
                        get_tabla();
                        /*se llama a la funcion correspondiente para traer el total de las compras*/
                        get_total();
                        /*se llama a la funcion que limpia los datos*/
                        limpiar();
                        return false;
                    }
                });
            }
            return false;
        });
	return false;
}

function nueva_inicializa(){
    jQuery.post("rh_pos_procesa.php",{type:"clearVenta"}, function(ResBusqueda){
        if(ResBusqueda == "si"){
            /*para este punto ya se agrego el item al rray de seccion donde estan los demas items*/
           /*se llama a la funcion correspondiente para traer la tabla*/
           get_tabla();
           /*se llama a la funcion correspondiente para traer el total de las compras*/
           get_total();
           /*se llama a la funcion que limpia los datos*/
           limpiar();
           return false;
       }
    });
    return false;
}

function new_compra(){
	/*se pone la comprobacion para crear la nueva venta*/
        $.msgbox("&iquest;Esta usted seguro, se perderan los cambios no guardados?", {
          type: "confirm",
          buttons : [
            {type: "submit", value: "Yes"},
            {type: "submit", value: "No"}
          ]
        },function(result){
            if(result == "Yes"){
                /*cuando inicia*/
                jQuery.post("rh_pos_procesa.php",{type:"clearVenta"}, function(ResBusqueda){
                    if(ResBusqueda == "si"){
                        /*para este punto ya se agrego el item al rray de seccion donde estan los demas items*/
                       /*se llama a la funcion correspondiente para traer la tabla*/
                       get_tabla();
                       /*se llama a la funcion correspondiente para traer el total de las compras*/
                       get_total();
                       /*se llama a la funcion que limpia los datos*/
                       limpiar();
                       return false;
                   }
                });
            }
            return false;
        });
	return false;
}

function caja_registradora(){
    $.msgbox("<p>Ingresar el código del artículo:</p>", {
        type: "prompt"
    },function(result){
        if(result){
            /*aki es donde se carga la venta*/
            jQuery.post("rh_pos_procesa.php",{type:"verificador", result:result}, function(ResBusqueda){
                if(ResBusqueda != "no"){
                    var tmpArray = ResBusqueda.split('?');
                    $.msgbox("Articulo:"+tmpArray[0]+" <br /> Precio: "+tmpArray[1]);
                    return false;
                }else{
                    $.msgbox("Articulo no encontrado<br />Por Favor Intenta de Nuevo");
                    return false;
                }
            });
            return false;
        }else{
            $.msgbox("Para verificar el precio de un artículo, debe ingresar un código");
            return false;
        }
    });
    return false;
}

function pagar_total(){
    /*se tiene que emular el click para que funcione correctamente*/
    //$('#pagar').triggerHandler();
    /*se agrega quien es el vendedor y se obtiene por correo*/
    var vendedor = document.vendedor.Vendedor.options[document.vendedor.Vendedor.selectedIndex].value;
    jQuery.post("rh_pos_procesa.php",{type:"revPago", vendedor:vendedor}, function(ResBusqueda){
        if(ResBusqueda == "si"){
            /*si hay pago que procesar*/
            $('#pagarR').click();
            return false;
        }else{
            /*no hay pago que procesar*/
            $.msgbox("Para efectuar un pago es necesario tener una cantidad valida que pagar", {type: "error"});
            return false;
        }
    });
    return false;
}

function add_serie(){
    /*se tiene que emular el click para que funcione correctamente*/
    //$('#pagar').triggerHandler();
    /*se agrega quien es el vendedor y se obtiene por correo*/
    var vendedor = document.vendedor.Vendedor.options[document.vendedor.Vendedor.selectedIndex].value;
    jQuery.post("rh_pos_procesa.php",{type:"revPago", vendedor:vendedor}, function(ResBusqueda){
        if(ResBusqueda == "si"){
            /*si hay pago que procesar*/
            $('#seriesLotes').click();
            return false;
        }else{
            /*no hay pago que procesar*/
            $.msgbox("Para efectuar un pago es necesario tener una cantidad valida que pagar", {type: "error"});
            return false;
        }
    });
    return false;
}