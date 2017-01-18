<?php
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */

$PageSecurity = 14;
include('includes/session.inc');
$array_pago = array('1' => 'Efectivo', '2' => 'Tarjeta', '3' => 'Cheque', '4' => 'Vales');

/**/
unset($_SESSION['pagos']);
$_SESSION['pagos'] = 0;
unset($_SESSION['pagos']);

function get_total_compra($tipo = 0){
    /*depende del array base de los elementos con el cual se esta trabajanado*/
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    if($elementos == 0){
        $total = 0;
    }else{
        $total = 0;
        for($x=0;$x<$elementos;$x++){
            $individuales = $padre[$x];
            $total = $total + $individuales['total'];
        }
    }
    if($tipo == 0){
        return number_format($total,2);
    }else{
        return str_replace(",", "", number_format($total,2));
    }
}
?>

<html>
    <head>
        <title>Forma de Pago</title>
        <LINK href="rh_pos_archivos/jquery.msgbox.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="rh_pos_archivos/jquery-1.4.2.min.js"></script>
        <script type="text/javascript" src="rh_pos_archivos/jquery.msgbox.min.js"></script>
        <style>
        html {
            background:#d7e2f9;
        }

        table.helpT{
            text-align: center;
            font-family: Verdana;
            font-weight: normal;
            font-size: 11px;
            color: #404040;
            width: 99% !important;
            background-color: #fafafa;
            border: 1px #6699CC solid;
            border-collapse: collapse;
            margin:3px !important;
            padding:3px !important;
            border-spacing: 0px;
        }
        td.helpHed{
                border-bottom: 2px solid #6699CC;
                border-left: 1px solid #6699CC;
                background-color: #BEC8D1;
                text-align: left;
                text-indent: 5px;
                margin:3px !important;
                padding:3px !important;
                font-family: Verdana;
                font-weight: bold;
                font-size: 11px;
                color: #404040;
        }

        td.helpBod{
            border-bottom: 1px solid #9CF;
            border-top: 0px;
            border-left: 1px solid #9CF;
            border-right: 0px;
            text-align: left;
            text-indent: 0px !important;
            font-family: Verdana, sans-serif, Arial;
            font-weight: normal;
            font-size: 11px;
            margin:3px !important;
            padding:3px !important;
            color: #404040;
            background-color: #fafafa;
        }

        table.sofT{
            text-align: left;
            font-family: Verdana;
            font-weight: normal;
            font-size: 11px;
            color: #404040;
            margin:3px !important;
            padding:3px !important;
            width: 99% !important;
            background-color: #fafafa;
            border: 1px #6699CC solid;
            border-collapse: collapse;
            border-spacing: 0px;
        }
        </style>
        <script>
            /*funcion que sirve para actualizar los pagos que traemos disponibles*/
            function actualiza(){
                jQuery.post("rh_pos_procesa.php",{type:"actuPagOs"}, function(ResBusqueda){
                    var myObject = eval('(' + ResBusqueda + ')');
                    jQuery("#pago").html(myObject.pago);
                    jQuery("#cambio").html(myObject.cambio);
                    jQuery("#restante").html(myObject.restante);
                    jQuery("#totpa").html(myObject.totalP);
                    return false;
                });
                return false;
            }

            /*funcion que confirma el pago final de la venta*/
            function confirma_final(){
                jQuery.post("rh_pos_procesa.php",{type:"pagoFiNaL"}, function(ResBusqueda){
                    var myObject = eval('(' + ResBusqueda + ')');
                    var total = myObject.total;
                    var pagado = myObject.pagado;
                    if(parseFloat(pagado) >= parseFloat(total)){
                        jQuery.post("rh_pos_procesa.php",{type:"procesaCompra"}, function(ResBusquedad){
                            //alert(ResBusquedad);
                            //return false;
                            //alert(ResBusquedad);
                            //28/12/2010 Alert de error;
                            if(ResBusquedad == "no"){
                                alert('No se pudo procesar tu solicitud\nPor favor intenta de nuevo');
                                return false;
                            }else{
                               //ResBusquedad es el numero de requiscion
                               window.open("rh_pos_printicket.php?datos="+ResBusquedad, "ticket", 'width=100,height=100');
                               /*se acceden a los metodos padres para limpiar*/
                               window.parent.nueva_inicializa();
                               /*se cierra esta ventana*/
                               //window.top.tb_remove();
                               /*finaliza el metodo con el cual se esta trabajando*/
                               window.top.tb_remove();
                               return false;
                            }
                            return false;
                        });
                        return false;
                    }else{
                        alert('No se puede cerrar la compra porque el total pagado es de $'+pagado+' de un total de $'+total);
                        return false;
                    }
                });
            }

            function remove(llave){
                jQuery.post("rh_pos_procesa.php",{type:"quiTarPaGo", llave:llave}, function(ResBusqueda){
                    if(ResBusqueda == "si"){
                        jQuery("#matodos_pago").html('');
                        document.pagoF.tipoP.selectedIndex = 0;
                        actualiza();
                        return false;
                    }
                    return false;
                });
                return false;
            }

            function procesar(valor){
                var html = "";
                if(valor == '-9'){
                    /*cuando no hay seleccion*/
                }else{
                    switch(valor){
                        case "1":
                            html += "<center>";
                            html += "<h4 style='margin:0px !important; padding:0px !important; border:0px !important;'>Efectivo</h4>";
                            html += "<label for='monto'>Monto</label>";
                            html += "<input type='text' style='align-text:center !important; align:center !important;' name='monto' value='0' id='monto' class='pagos_D' />";
                            html += "</center>";
                            break;
                        case "2":
                            html += "<center>";
                                html += "<h4 style='margin:0px !important; padding:0px !important; border:0px !important;'>Tarjeta</h4>";

                                html += "<label>Tarjeta</label>"
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='digitos' value='0' id='digitos' class='pagos_D' /><br />";

                                html += "<label>Aprobacion</label>"
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='aprovacion' value='0' id='aprovacion' class='pagos_D' /><br />";

                                html += "<label for='monto'>Monto</label>";
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='monto' value='0' id='monto' class='pagos_D' /><br />";

                            html += "</center>";
                            break;
                        case "3":
                            html += "<center>";
                                html += "<h4 style='margin:0px !important; padding:0px !important; border:0px !important;'>Cheque</h4>";

                                html += "<label>Banco</label>"
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='banco' value='0' id='banco' class='pagos_D' /><br />";

                                html += "<label>Nombre</label>"
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='nombre' value='0' id='nombre' class='pagos_D' /><br />";

                                html += "<label>Numero Cheque</label>"
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='numero' value='0' id='numero' class='pagos_D' /><br />";

                                html += "<label for='monto'>Monto</label>";
                                html += "<input type='text' style='align-text:center !important; align:center !important;' name='monto' value='0' id='monto' class='pagos_D' /><br />";

                            html += "</center>";
                            break;
                        case "4":
                            html += "<center>";
                            html += "<h4 style='margin:0px !important; padding:0px !important; border:0px !important;'>Vales</h4>";
                            html += "<label for='monto'>Monto</label>";
                            html += "<input type='text' style='align-text:center !important; align:center !important;' name='monto' value='0' id='monto' class='pagos_D' />";
                            html += "</center>";
                            break;
                    }
                }
                jQuery("#matodos_pago").html(html);
                return false;
            }

            var total_pagar = <?=get_total_compra(1);?>;

            $(document).ready(function(){
                $("#pagoF").submit(function(){
                    var cate = document.pagoF.tipoP.options[document.pagoF.tipoP.selectedIndex].value;
                    if(cate == '-9'){
                        actualiza();
                        if(jQuery("#restante").val()<=0){
                            confirma_final();
                        }else{
                            alert('Para poder agregar un pago es necesario ingresarlo');
                        }
                        return false;
                    }else{

                        if(total_pagar <= 0){
                            alert('Para poder agregar un pago es necesario tener que pagar');
                            return false;
                        }

                        switch(cate){
                            case "1":


                        jQuery.post("rh_pos_procesa.php",{type:"pagoFiNaL"}, function(ResBusqueda){
                            var myObject = eval('(' + ResBusqueda + ')');
                            var total = myObject.total;
                            var pagado = myObject.pagado;
                            if(parseFloat(pagado) >= parseFloat(total)){
                                confirma_final();
                                return false;
                            }else{
                                var monto = jQuery("#monto").val();
                                if(monto == 0 || monto.length == 0){
                                    alert('El Campo de Monto es Requerido');
                                    return false;
                                }

                                var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                                if(!ca_dena.test(monto)){
                                    alert('El Monto debe de ser formato numerico');
                                    return false;
                                }

                                jQuery.post("rh_pos_procesa.php",{type:"pagar", tipos:"efectivo", monto:monto}, function(ResBusqueda){

                                    /**/
                                    jQuery("#matodos_pago").html('');
                                    document.pagoF.tipoP.selectedIndex = 1;
                                    /**/

                                    /*actualida datos*/
                                    actualiza();

                                    jQuery("#monto").val('0');
                                    procesar('1');
                                    document.getElementById('monto').focus();
                                    return false;
                                });
                            }
                     });

                                return false
                                break;
                            case "2":
                                var digitos = jQuery("#digitos").val();
                                var aprovacion = jQuery("#aprovacion").val();
                                var monto = jQuery("#monto").val();

                                if(digitos == 0 || digitos.length == 0){
                                    alert('El Campo de Tarjeta es Requerido');
                                    return false;
                                }

                                if(aprovacion == 0 || aprovacion.length == 0){
                                    alert('El Campo de Aprovacion es Requerido');
                                    return false;
                                }

                                if(monto == 0 || monto.length == 0){
                                    alert('El Campo de Monto es Requerido');
                                    return false;
                                }

                                var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                                if(!ca_dena.test(monto)){
                                    alert('El Monto debe de ser formato numerico');
                                    return false;
                                }

                                jQuery.post("rh_pos_procesa.php",{type:"pagar", tipos:"tarjeta", monto:monto, digitos:digitos, aprovacion:aprovacion}, function(ResBusqueda){
                                    jQuery("#digitos").val('');
                                    jQuery("#aprovacion").val('');
                                    jQuery("#monto").val('0');

                                    /**/
                                    jQuery("#matodos_pago").html('');
                                    document.pagoF.tipoP.selectedIndex = 1;
                                    /**/

                                    /*actualida datos*/
                                    actualiza();
                                    procesar('1');
                                    document.getElementById('monto').focus();
                                    return false;
                                });

                                return false;
                                break;
                            case "3":
                                var banco = jQuery("#banco").val();
                                var nombre = jQuery("#nombre").val();
                                var numero = jQuery("#numero").val();
                                var monto = jQuery("#monto").val();

                                if(banco == 0 || banco.length == 0){
                                    alert('El Campo de Banco es Requerido');
                                    return false;
                                }

                                if(nombre == 0 || nombre.length == 0){
                                    alert('El Campo de Nombre es Requerido');
                                    return false;
                                }

                                if(numero == 0 || numero.length == 0){
                                    alert('El Campo de Numero es Requerido');
                                    return false;
                                }

                                if(monto == 0 || monto.length == 0){
                                    alert('El Campo de Monto es Requerido');
                                    return false;
                                }

                                var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                                if(!ca_dena.test(monto)){
                                    alert('El Monto debe de ser formato numerico');
                                    return false;
                                }

                                jQuery.post("rh_pos_procesa.php",{type:"pagar", tipos:"cheque", monto:monto, banco:banco, nombre:nombre, numero:numero}, function(ResBusqueda){
                                    jQuery("#banco").val('');
                                    jQuery("#nombre").val('');
                                    jQuery("#numero").val('');
                                    jQuery("#monto").val('0');

                                    /**/
                                    jQuery("#matodos_pago").html('');
                                    document.pagoF.tipoP.selectedIndex = 1;
                                    /**/

                                    /*actualida datos*/
                                    actualiza();
                                                                        procesar('1');
                                    document.getElementById('commandos').focus();
                                    return false;
                                });
                                return false;
                                break;
                            case "4":
                                var monto = jQuery("#monto").val();
                                if(monto == 0 || monto.length == 0){
                                    alert('El Campo de Monto es Requerido');
                                    return false;
                                }

                                var ca_dena = /(^\d*\.?\d*[1-9]+\d*$)|(^[1-9]+\d*\.\d*$)/;
                                if(!ca_dena.test(monto)){
                                    alert('El Monto debe de ser formato numerico');
                                    return false;
                                }

                                jQuery.post("rh_pos_procesa.php",{type:"pagar", tipos:"vales", monto:monto}, function(ResBusqueda){

                                    /**/
                                    jQuery("#matodos_pago").html('');
                                    document.pagoF.tipoP.selectedIndex = 1;
                                    /**/

                                    /*actualida datos*/
                                    actualiza();

                                    jQuery("#monto").val('0');
                                    procesar('1');
                                    document.getElementById('monto').focus();
                                    return false;
                                });
                                return false
                                break;
                        }
                    }
                    procesar('1');
                    document.getElementById('monto').focus();
                    return false;
                });
            });
        </script>
        <style>
            .input {
                border: 1px solid #006;
                background: #ffc;
            }
            .input:hover {
                border: 1px solid #f00;
                background: #ff6;
            }
            label {
                display: block;
                width: 150px;
                float: left;
                margin: 2px 4px 6px 4px;
                text-align: right;
            }
            br { clear: left; }
        </style>
    </head>
    <body onload="procesar('1');document.getElementById('monto').focus();" id="BodyPagos" onclick=" document.getElementById('monto').focus();   ">
        <table width="100%">
            <tr>
                <td align="center" valign="top"><h3>Forma de Pago</h3><hr /></td>
            </tr>
            <tr>
                <td>
                    <div id="pago" class="pago" style="display:block; clear:width; border:1px solid black; height:200px; overflow: auto !important;">
                        <p>Sin Pagos</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td align="right" width="75%">Total Pagado</td>
                            <td  align="right" width="25%">$ <span id="totpa">0</span></td>
                        </tr>
                    </table>
                </td>
            </tr>           
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td align="right" width="75%">Total</td>
                            <td  align="right" width="25%">$ <span><?=get_total_compra();?></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                     <table width="100%">
                        <tr>
                            <td align="right" width="75%">Restante</td>
                            <td  align="right" width="25%">$ <span id="restante"><?=get_total_compra();?></span></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td align="right" width="75%">Cambio</td>
                            <td  align="right" width="25%">$ <span id="cambio">0</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <form id="pagoF" class="pagoF" name="pagoF" method="POST" action="#">
                        <table width="100%">
                            <tr>
                                <td align="right">Tipo</td>
                                <td align="right" width="25%">
                                    <select name="tipoP" id="tipoP" class="tipoP" onchange="javascript: procesar(this.value);">
                                        <option value="-9">Selecciona</option>
                                        <?
                                            foreach($array_pago as $a => $b){
                                              if($a=='1'){
                                                echo "<option value='".$a."' selected='selected' >".$b."</option>";
                                              }else{
                                                echo "<option value='".$a."'>".$b."</option>";
                                              }
                                            }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div id="matodos_pago" style="display:block; clear:width; height: 110px;">
                                        
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" width="99%" colspan="2">
                                    <table width="100%">
                                        <tr>
                                            <td width="33%" align="center"><input type="button"  value="Agregar Pago"  onclick="sendPago();" />
                                            <input type="submit" name="Procesar" value="Agregar Pago" id="EnvioF" style="display:none; visibility:hidden;"  /></td>

                                            <td width="33%" align="center"><button name="cerrarT" id="cerrarT" class="cerrarT" value="Close" onclick="javascript: window.top.tb_remove(); return false;">Cerrar</button></td>
                                            
                                            <td width="33%" align="center"><button name="pagarT" id="pagarT" class="pagarT" value="Pagar" onclick="javascript: confirma_final(); return false;">Realizar Pago</button></td>


                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <input type="text" id="commandos" style="display:none; visibility:hidden;"/>
                    </form>
                </td>
            </tr>
        </table>
    </body>
    <script>


    var empresa2  = document.getElementById("BodyPagos");
    empresa2.onkeypress = function(e){
        var keynum;
        if(window.event){
            keynum = e.keyCode
        }else if(e.which){
            keynum = e.which
        }
        if(keynum==13){
            document.getElementById('EnvioF').click();
             return false;
        }
    }
    function sendPago(){
       document.getElementById('EnvioF').click();
    }

    function setFocus(){
      //alert('HOLA');
      document.getElementById('monto').focus();
    }
	setTimeout("setFocus()",1000);


    </script>
</html>