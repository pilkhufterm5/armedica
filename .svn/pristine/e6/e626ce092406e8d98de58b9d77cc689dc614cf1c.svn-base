<?
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */
$PageSecurity = 14;
include('includes/session.inc');
include('includes/GetPrice.inc');
/*se recibe el tipo que es con el que se va a trabar este archivo*/
$type = $_POST['type'];

function generate_html_table($resultados) {
    $tabla = "";

    $tabla .= "<table cellpadding='0' cellspacing='0' border='0' width=60px class='display' id='example'>";
    $tabla .= "<thead>";
    $tabla .= "<tr>";
    $tabla .= "<th>Articulo</th>";
    $tabla .= "</tr>";
    $tabla .= "</thead>";
    /**/
    $tabla .= "<tbody>";
    while ($myrow1 = DB_fetch_array($resultados)) {
        $tabla .= "<tr>";
        $tabla .= "<td><a href='#' onclick='add_item(\"".$myrow1['stockid']."\")'>".$myrow1['stockid']."</a><br />".$myrow1['description']."</td>";
        $tabla .= "</tr>";
    }
    $tabla .= "</tbody>";
    /**/
    $tabla .= "<tfoot>";
    $tabla .= "<tr>";
    $tabla .= "<th>Articulo</th>";
    $tabla .= "</tr>";
    $tabla .= "</tfoot>";
    $tabla .= "</table>";
    /**/
    return $tabla;
}

function fetch_alt_ip() {
    $alt_ip = $_SERVER['REMOTE_ADDR'];
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
    }else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        // make sure we dont pick up an internal IP defined by RFC1918
        foreach ($matches[0] AS $ip) {
            if (!preg_match("#^(10|172\.16|192\.168)\.#", $ip)) {
                $alt_ip = $ip;
                break;
            }
        }
    }else if (isset($_SERVER['HTTP_FROM'])) {
        $alt_ip = $_SERVER['HTTP_FROM'];
    }
    return $alt_ip;
}

function add_array_array($array) {
    /*aki ya esta mi array base*/
    $_SESSION['ventas'][] = $array;
    print_r($_SESSION['ventas']);
}

function get_lastItem(){
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    $individuales = $padre[$elementos-1];
    $stockid = $individuales['item'];
    $precio = $individuales['precio'];
    $qty=$qty/10000;
    if($precio>0.0){
        echo 'modifica_cantidad("'.($elementos).'","'.$stockid.'");document.getElementById("ancla").scrollIntoView(true);';
    }else{
        echo 'modifica_cantidad("'.($elementos).'","'.$stockid.'");cambia_precio("'.($elementos).'","'.$stockid.'");document.getElementById("ancla").scrollIntoView(true);';
    }
}

function generate_table_items() {
    /*depende del array base de los elementos con los cuales se esta trabajando*/
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    $table = "";
    if($elementos == 0) {
        $table .= "<p>Sin Registros</p>";
    }else {
        $table .= "<table cellpadding='0' cellspacing='0' id='tiempo' border='0' id='ta_ite' class='ta_ite sofT'>";
        $table .= "<thead>";
        $table .= "<tr>";
        $table .= "<th class=\"helpHed\">#</th>";
        $table .= "<th class=\"helpHed\">Articulo</th>";
        $table .= "<th class=\"helpHed\">Cantidad</th>";
        $table .= "<th class=\"helpHed\">Unitario</th>";
        $table .= "<th class=\"helpHed\">Descuento</th>";
        $table .= "<th class=\"helpHed\">IVA</th>";
        $table .= "<th class=\"helpHed\">Total</th>";
        $table .= "</tr>";
        $table .= "</thead>";
        $table .= "<tbody>";
        $contador = 0;
        for($x=0;$x<$elementos;$x++) {
            /*for para obtener los elementos del array con el que se va a trabajar*/
            /**/
            $individuales = $padre[$x];
            /***/
            $stockid = $individuales['item'];
            $description = $individuales['descripcion'];
            $cantidad = $individuales['cantidad'];
            $descuento = $individuales['descuento'];
            $iva = $individuales['iva'];
            $precio = $individuales['precio'];
            $pretotal = $individuales['pretotal'];
            $total = $individuales['total'];
            /**/
            $contador = $contador + 1;
            $table .= "<tr onClick=\"get_extras('".$contador."', '".$stockid."'); $('table tr').removeClass('highlight'); $(this).addClass('highlight'); \" style=\"cursor:hand\">";
            $table .= "<td class=\"helpHed\">".$contador."</td>";
            $table .= "<td class=\"helpHed\">".$stockid."<br />".$description."</td>";
            $table .= "<td class=\"helpHed\">".number_format($cantidad, 4)."</td>";
            $table .= "<td class=\"helpHed\">".number_format($precio, 4)."</td>";
            $table .= "<td class=\"helpHed\">".number_format($descuento*100, 4)."</td>";
            $table .= "<td class=\"helpHed\">".number_format($iva*100, 4)."</td>";
            $table .= "<td class=\"helpHed\">$ ".number_format($total, 4)."</td>";
            $table .= "</tr>";
        }
        $table .= "</tbody>";
        $table .= "</table><div id='ancla'></div>";
    }
    return $table;
}

function get_total_compra() {
    /*depende del array base de los elementos con el cual se esta trabajanado*/
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    if($elementos == 0) {
        $total = 0;
    }else {
        $total = 0;
        for($x=0;$x<$elementos;$x++) {
            $individuales = $padre[$x];
            $total = $total + $individuales['total'];
        }
    }
    return number_format($total,2);
}

function get_total_pagado(){
    $padre = $_SESSION['pagos'];
    $elementos = count($padre);
    if($elementos == 0){
        $total = 0;
    }else{
        $total = 0;
        for($x=0;$x<$elementos;$x++){
            $individuales = $padre[$x];
            $total = $total + $individuales['monto'];
        }
    }
    return number_format($total,4);
}

function get_total_pagado_html(){
    $padre = $_SESSION['pagos'];
    $elementos = count($padre);
    if($elementos == 0){
        $total = "<p>Sin Registros</p>";
    }else{
        $total = "";
        $total .= "<table cellpadding='0' width='100%' cellspacing='0' id='tiempo' border='0' id='ta_ite' class='ta_ite sofT'>";
            $total .= "<thead>";
                $total .= "<tr>";
                    $total .= "<th class=\"helpHed\" width=\"25%\">No</th>";
                    $total .= "<th class=\"helpHed\" width=\"25%\">Tipo</th>";
                    $total .= "<th class=\"helpHed\" width=\"25%\">Cantidad</th>";
                    $total .= "<th class=\"helpHed\" width=\"25%\">Quitar</th>";
                $total .= "</tr>";
            $total .= "</thead>";
            $total .= "<tbody>";
            for($x=0;$x<$elementos;$x++){
                $individuales = $padre[$x];
                $total .= "<tr>";
                    $total .= "<td class=\"helpHed\" width=\"25%\">".($x+1)."</td>";
                    $total .= "<td class=\"helpHed\" width=\"25%\">".$individuales['tipo']."</td>";
                    $total .= "<td class=\"helpHed\" width=\"25%\">".number_format($individuales['monto'],2)."</td>";
                    $total .= "<td class=\"helpHed\" width=\"25%\"><a href=\"#\" onclick=\"javascript: remove('".$x."'); return false;\">Quitar</a></td>";
                $total .= "</tr>";
            }
            $total .= "</tbody>";
        $total .= "</table>";
    }
    return $total;
}

function total_inventarios($item) {
    global $db;
    /*funcion para consultar los inventarios*/
    /*traer los datos de configuracion para trabajar con ella*/
    $principal = $_SESSION['rh_pos_principal'];
    $terminal = $principal['terminal'];
    /*sql para traer los datos de la consulta de la termianr para obtener el lococode*/
    $sql_00 = "Select Sucursal FROM rh_pos_terminales WHERE id = '".$terminal."'";
    $result_00 =  DB_query($sql_00,$db);
    if(DB_num_rows($result_00)==0) {
        /*cuando no encuentra sucursal que marque error*/
        return "0";
        die();
    }else {
        /*cuando si hay resultados*/
        $procesa_00 = DB_fetch_row($result_00);
        $localidad = $procesa_00[0];

        /*sql Find the quantity in stock at location*/
        $qohsql = "SELECT quantity FROM locstock WHERE stockid='" .$item . "' AND loccode = '" . $localidad . "'";
        $qohresult =  DB_query($qohsql,$db);
        $qohrow = DB_fetch_row($qohresult);
        $qoh = $qohrow[0];

        /*sql Find the quantity on outstanding sales orders*/
        $sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                FROM salesorderdetails, salesorders WHERE salesorders.orderno = salesorderdetails.orderno AND salesorders.fromstkloc='" . $localidad . "' AND
                salesorderdetails.completed=0 AND salesorders.rh_status = 1 AND salesorders.quotation=0 AND salesorderdetails.stkcode='" . $item . "'";
        $DemandResult = DB_query($sql,$db);
        $DemandRow = DB_fetch_row($DemandResult);
        if ($DemandRow[0] != null) {
            $DemandQty =  $DemandRow[0];
        }else {
            $DemandQty = 0;
        }

        /*sql Find the quantity on purchase orders*/
        $sql = "SELECT SUM(purchorderdetails.quantityord-purchorderdetails.quantityrecd) AS dem
            	FROM purchorderdetails WHERE purchorderdetails.completed=0 AND purchorderdetails.itemcode='" . $item . "'";
        $PurchResult = db_query($sql,$db);
        $PurchRow = db_fetch_row($PurchResult);
        if ($PurchRow[0]!=null) {
            $PurchQty =  $PurchRow[0];
        }else {
            $PurchQty = 0;
        }
        /*$sql = "SELECT SUM(woitems.qtyreqd - woitems.qtyrecd) AS dedm FROM woitems
		WHERE stockid='" . $item ."'";
        $WoResult = db_query($sql,$db);
        $WoRow = db_fetch_row($WoResult);
        if ($WoRow[0]!=null) {
            $WoQty =  $WoRow[0];
        }else {
            $WoQty = 0;
        }   */
        $WoQty = 0;
        $OnOrder = $PurchQty + $WoQty;
        //$Available = $qoh - $DemandQty + $OnOrder;
        $Available = $qoh - $DemandQty + $OnOrder;

        return $Available;
        die();
    }
}

function get_decimal_places($item) {
    global $db;
    $sql = "SELECT decimalplaces FROM stockmaster where stockid = '".$item."'";
    $results = DB_query($sql,$db);
    $obtuvo = DB_fetch_row($results);
    if ($obtuvo[0]!=null) {
        /*cuando si obtuvo resultados validos*/
        return $obtuvo[0];
        die();
    }else {
        /*cuando no huvo resultados validos*/
        return "0";
        die();
    }
}

/*funcion para revisar lo que es el round*/
function floordec($zahl,$decimals=2) {
    return floor($zahl*pow(10,$decimals))/pow(10,$decimals);
}

function actualiza_pagos_p($llave){
    $padre = $_SESSION['pagos'];
    $elementos = count($padre);
    if($elementos != 0){
        /*entra cuando hay elementos*/
        for($x=0;$x<$elementos;$x++){
            if($x == $llave){
                /*cuanno no se tiene que agregar*/
            }else{
                $individuales = $padre[$x];
                /* -- */
                $array_tmp['tipo'] = $individuales['tipo'];
                $array_tmp['monto'] = $individuales['monto'];
                /*datos tarjeta*/
                $array_tmp['digitos'] = $individuales['digitos'];
                $array_tmp['aprovacion'] = $individuales['aprovacion'];
                /*datos cheque*/
                $array_tmp['banco'] = $individuales['banco'];
                $array_tmp['nombre'] = $individuales['nombre'];
                $array_tmp['numero'] = $individuales['numero'];
                /*general*/
                $array_array_tmp[] = $array_tmp;
            }
        }
        unset($_SESSION['pagos']);
        $_SESSION['pagos'] = 0;
        unset($_SESSION['pagos']);
        $_SESSION['pagos']  = $array_array_tmp;
    }
}

/*funcion que sirve para trabajar con los datos con los cuales se va a trabajar*/
function operacion_sobre_item($operacion, $cantidad, $llave, $item) {
    //echo operacion_sobre_item('suma', 1, $no, $item);
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    if($elementos == 0) {
        /*cuando no hay elementos en el array base con el que se esta trabajando*/
        return "error 1";
        exit();
    }else {
        /*cuando si hay elementos en los cuales se va a trabajar*/
        $contador = 0;
        for($x=0;$x<$elementos;$x++) {
            /**/
            $contador = $contador + 1;
            /**/
            $individuales = $padre[$x];
            /*se agrego el item para poder realizar la operacion correspondiente*/
            if($contador == $llave && $individuales['item'] == $item){
                /*cuando aplica la informacion que estamos trabajando*/
                $stockid_a = $individuales['item'];
                $description_a = $individuales['descripcion'];
                $cantidad_a = $individuales['cantidad'];
                $descuento_a = $individuales['descuento'];
                $iva_a = $individuales['iva'];
                $precio_a = $individuales['precio'];
                $pretotal_a = $individuales['pretotal'];
                $total_a = $individuales['total'];
                /**/
                switch($operacion) {
                    case "suma":
                        $cantidad_sin_alterar = $cantidad_a;
                        $cantidad_previa = $cantidad_a + $cantidad;
                        /*se tiene que evaluar la cantidad*/
                        $bandera = checa_inventarios($stockid_a, $cantidad_previa, $llave);
                        $decode = json_decode($bandera);
                        //print_r($decode);
                        /*aki recibe el array que es el que valida los datos con los cuales se va a trabajar*/
                        $error = $decode->error;
                        /*se hace la evaluacion de los datos con los cuales se van a trabajar la informacion con la cual se va a evaluar*/
                        if($error == "si") {
                            /*porque no cabe*/
                            $cantidad_a = $cantidad_sin_alterar;
                        }else {
                            $cantidad_a = $cantidad_previa;
                        }

                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        /**/
                        $operaciones_descuento = $precio_a-($precio_a*$descuento_a);
                        $operacion_iva = $operaciones_descuento+($operaciones_descuento*$iva_a);
                        $operacion_antes = $operacion_iva*$cantidad_a;
                        /*operaciones con los cuales se esta trabajando*/
                        $cantidad_a = $cantidad_a;
                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        $total_a = $operacion_antes;

                        /*se asignar las variables con las cuales se va a trabajar*/
                        $tmp['item'] = $stockid_a;
                        $tmp['descripcion'] = $description_a;
                        $tmp['cantidad'] = $cantidad_a;
                        $tmp['descuento'] = $descuento_a;
                        $tmp['iva'] = $iva_a;
                        $tmp['precio'] = $precio_a;
                        $tmp['pretotal'] = $pretotal_a;
                        $tmp['total'] = $total_a;
                        $array_temporal[] = $tmp;
                        break;
                    /*---------------*/
                    case "resta":
                        $cantidad_sin_alterar = $cantidad_a;
                        $cantidad_previa = $cantidad_a - 1;

                        if($cantidad_previa <= 0) {
                            $cantidad_a = 1;
                        }else {
                            $cantidad_a = $cantidad_previa;
                        }

                        /*se tiene que evaluar la cantidad*/
                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        /**/
                        $operaciones_descuento = $precio_a-($precio_a*$descuento_a);
                        $operacion_iva = $operaciones_descuento+($operaciones_descuento*$iva_a);
                        $operacion_antes = $operacion_iva*$cantidad_a;
                        /*operaciones con los cuales se esta trabajando*/
                        $cantidad_a = $cantidad_a;
                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        $total_a = $operacion_antes;

                        /*se asignar las variables con las cuales se va a trabajar*/
                        $tmp['item'] = $stockid_a;
                        $tmp['descripcion'] = $description_a;
                        $tmp['cantidad'] = $cantidad_a;
                        $tmp['descuento'] = $descuento_a;
                        $tmp['iva'] = $iva_a;
                        $tmp['precio'] = $precio_a;
                        $tmp['pretotal'] = $pretotal_a;
                        $tmp['total'] = $total_a;
                        $array_temporal[] = $tmp;
                        break;
                    /*---------------*/
                    case "descuento":
                    /*aplicacion del descueto*/

                    /**
                     $stockid_a = $individuales['item'];
                     $description_a = $individuales['descripcion'];
                     $cantidad_a = $individuales['cantidad'];
                     $descuento_a = $individuales['descuento'];
                     $iva_a = $individuales['iva'];
                     $precio_a = $individuales['precio'];
                     $pretotal_a = $individuales['pretotal'];
                     $total_a = $individuales['total'];
                     * */


                    //echo operacion_sobre_item('descuento', $result, $no, $item);
                    /*se tien eque valida en que formato de dio de alta*/
                        if($cantidad > 1) {
                            /*cuando es mayor que uno*/
                            $antes = ($cantidad/100);
                        }else {
                            /*cuando es normal*/
                            $antes = $cantidad;
                        }

                        $operaciones_descuento = $precio_a-($precio_a*$antes);
                        $operacion_iva = $operaciones_descuento+($operaciones_descuento*$iva_a);
                        $operacion_antes = $operacion_iva*$cantidad_a;

                        $stockid_a = $stockid_a;
                        $description_a = $description_a;
                        $cantidad_a = $cantidad_a;
                        $descuento_a = $antes;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        $total_a = $operacion_antes;

                        $tmp['item'] = $stockid_a;
                        $tmp['descripcion'] = $description_a;
                        $tmp['cantidad'] = $cantidad_a;
                        $tmp['descuento'] = $descuento_a;
                        $tmp['iva'] = $iva_a;
                        $tmp['precio'] = $precio_a;
                        $tmp['pretotal'] = $pretotal_a;
                        $tmp['total'] = $total_a;

                        $array_temporal[] = $tmp;
                        break;
                    /*---------------*/
                    case "cantidad":
                        $cantidad_sin_alterar = $cantidad_a;
                        $cantidad_procesada = total_decimal_places($stockid_a, $cantidad);
                        $cantidad_previa = $cantidad_procesada;
                        /*se tiene que evaluar la cantidad*/
                        $bandera = checa_inventarios($stockid_a, $cantidad_previa, $llave);
                        $decode = json_decode($bandera);
                        //print_r($decode);
                        /*aki recibe el array que es el que valida los datos con los cuales se va a trabajar*/
                        $error = $decode->error;
                        /*se hace la evaluacion de los datos con los cuales se van a trabajar la informacion con la cual se va a evaluar*/
                        if($error == "si") {
                            /*porque no cabe*/
                            $cantidad_a = $cantidad_sin_alterar;
                        }else {
                            /*ak ientraria cuando si hay suficientes items para dar salida a los articulos*/
                            $cantidad_a = $cantidad_previa;
                        }
                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        /**/
                        $operaciones_descuento = $precio_a-($precio_a*$descuento_a);
                        $operacion_iva = $operaciones_descuento+($operaciones_descuento*$iva_a);
                        $operacion_antes = $operacion_iva*$cantidad_a;
                        /*operaciones con los cuales se esta trabajando*/
                        $cantidad_a = $cantidad_a;
                        $descuento_a = $descuento_a;
                        $iva_a = $iva_a;
                        $precio_a = $precio_a;
                        $pretotal_a = $pretotal_a;
                        $total_a = $operacion_antes;

                        /*se asignar las variables con las cuales se va a trabajar*/
                        $tmp['item'] = $stockid_a;
                        $tmp['descripcion'] = $description_a;
                        $tmp['cantidad'] = $cantidad_a;
                        $tmp['descuento'] = $descuento_a;
                        $tmp['iva'] = $iva_a;
                        $tmp['precio'] = $precio_a;
                        $tmp['pretotal'] = $pretotal_a;
                        $tmp['total'] = $total_a;
                        $array_temporal[] = $tmp;
                        break;
                    /*---------------*/
                    case "eliminar":
                    //$array_temporal[$x] = $tmp;
                        break;
                    /*---------------*/
                     case "precio":
                    //echo operacion_sobre_item('precio', $result, $no, $item);
                    /*aplicacion del descueto*/

                    /**
                     $stockid_a = $individuales['item'];
                     $description_a = $individuales['descripcion'];
                     $cantidad_a = $individuales['cantidad'];
                     $descuento_a = $individuales['descuento'];
                     $iva_a = $individuales['iva'];
                     $precio_a = $individuales['precio'];
                     $pretotal_a = $individuales['pretotal'];
                     $total_a = $individuales['total'];
                     * */

                        $operaciones_descuento = $cantidad-($cantidad*$antes);
                        $auxIva=1+$iva_a;
                        //$operacion_iva = $operaciones_descuento+($operaciones_descuento*$iva_a);
                        $operacion_iva = $operaciones_descuento/$auxIva;
                        $operacion_antes = $operacion_iva*$cantidad_a;
                        if($operacion_antes<=0.01){
                            $operacion_antes = $cantidad;
                        }

                        $stockid_a = $stockid_a;
                        $description_a = $description_a;
                        $cantidad_a = $cantidad_a;
                        $descuento_a = $antes;
                        $iva_a = $iva_a;
                        $pretotal_a = $pretotal_a;
                        //$precio_a = $cantidad;
                        //$total_a = $operacion_antes;
                        $precio_a = $operacion_iva;
                        $total_a = $operacion_antes*$auxIva;

                        $tmp['item'] = $stockid_a;
                        $tmp['descripcion'] = $description_a;
                        $tmp['cantidad'] = $cantidad_a;
                        $tmp['descuento'] = $descuento_a;
                        $tmp['iva'] = $iva_a;
                        $tmp['precio'] = $precio_a;
                        $tmp['pretotal'] = $pretotal_a;
                        $tmp['total'] = $total_a;

                        $array_temporal[] = $tmp;
                        break;
                    /*---------------*/
                }
                /**/
            }else {
                /*se trabajo con los datos con los cuales se estan trabajando*/
                $tmp['item'] = $individuales['item'];
                $tmp['descripcion'] = $individuales['descripcion'];
                $tmp['cantidad'] = $individuales['cantidad'];
                $tmp['descuento'] = $individuales['descuento'];
                $tmp['iva'] = $individuales['iva'];
                $tmp['precio'] = $individuales['precio'];
                $tmp['pretotal'] = $individuales['pretotal'];
                $tmp['total'] = $individuales['total'];
                $array_temporal[] = $tmp;
            }
        }
        /**/
        unset($_SESSION['ventas']);
        $_SESSION['ventas'] = 0;
        unset($_SESSION['ventas']);
        $_SESSION['ventas'] = $array_temporal;
        /*devuelve el detella*/
        return "si";
        exit();
    }
}

function total_decimal_places($item, $cantidad) {
    /*funcion que permite evaluar lo que son los decimal places*/
    $decimales = get_decimal_places($item);
    /*se compara las cantidades con las cuales se esta trabajanado*/
    $cantidad_procesada = floordec($cantidad, $decimales);
    /*regresa la informacion*/
    return $cantidad_procesada;
}

function checa_inventarios($item, $cantidad, $indice = -1) {
    /*funcion para validar el inventario*/
    $inventario = total_inventarios($item, $cantidad);
    /*revisa si de verdad se puede agregar esa cantidad a mi venta*/
    $total_pedida = total_decimal_places($item, $cantidad);
    /*se trae cuantos elementos hay para poder ver si en la cantidad que hay dentro de mi venta no hay mas elementos
     * para ver si las cantidades que hay dentro de mi venta no supera el total de las cantidad
    */
    $tota_imte_ventas = get_total_cantidad_item_array($item, $indice);
    /*se trae la variable de configuracion*/
    global $db;
    /*se revisa el tipo de articulo que es*/
    $sql_item = "Select mbflag FROM stockmaster WHERE stockid = '".$item."'";
    $results_item = DB_query($sql_item,$db);
    $obtuvo_item = DB_fetch_row($results_item);

    if($obtuvo_item[0] == 'A' || $obtuvo_item[0] == 'E' || $obtuvo_item[0] == 'K' || $obtuvo_item[0] == 'D'){
        $inventario = ($inventario + $cantidad + 10)*2;
    }else{
        $inventario = $inventario;
    }

    $sql_config = "Select confvalue FROM config WHERE confname = 'ProhibitNegativeStock'";
    $results = DB_query($sql_config,$db);
    $obtuvo = DB_fetch_row($results);
    if ($obtuvo[0]!=null) {
        $bandera_config = $obtuvo[0];
    }else {
        $bandera_config = 1;
    }
    /**/
    if($bandera_config == 1) {
        /*que no permite stock negativos*/
        $total_suma_suma = ($tota_imte_ventas+$total_pedida);
        $total_suma_suma = ($tota_imte_ventas);
        if($inventario >= $total_suma_suma) {
            /*si hay cantidad para poder satisfacer el articulo*/
            $regresa = array('error' => 'no', 'cantidad' => '0');
        }else {
            /*no hay cantidad para poder satisfacer el articulo*/
            $regresa = array('error' => 'si', 'cantidad' => $inventario);
        }
    }else {
        /*permite stock negativo*/
        $regresa = array('error' => 'no', 'cantidad' => '0');
    }
    return json_encode($regresa);
}

function get_total_cantidad_item_array($item, $indice = -1) {
    $padre = $_SESSION['ventas'];
    $elementos = count($padre);
    if($elementos == 0) {
        /*cuando no hay elementos en el array base con el que se esta trabajando*/
        return "0";
    }else {
        $contador = 0;
        $el_conta = 0;
        for($x=0;$x<$elementos;$x++) {
            $contador = $contador + 1;
            $individuales = $padre[$x];
            if($indice != $contador) {
                if($individuales['item'] == $item) {
                    $el_conta = $el_conta+$individuales['cantidad'];
                }
            }
        }
        return $el_conta;
    }
}

switch($type) {
    case "item":
    /*seccion de la busqueda de item*/
        $item = $_POST['item'];
        $desc = $_POST['desc'];
        $cate = $_POST['cate'];
        $qty=substr($item,5);
        if(strlen($item)==12){
            $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND (stockmaster.barcode = '".substr($item,0,5)."') AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            $SearchResult = DB_query($SQL,$db);
            if (DB_num_rows($SearchResult)==0) {
                    $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND (stockmaster.stockid LIKE '%" . $item . "%' OR stockmaster.barcode = '".$item."') AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
                    $SearchResult2 = DB_query($SQL,$db);
                if (DB_num_rows($SearchResult2)==0) {
                    $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => '0', 'sql' => $SQL );
                }else{
                    $myrow=DB_fetch_array($SearchResult2);
                    $stockid = $myrow['stockid'];
                    $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => '1', 'sql' => $SQL, 'item' => $stockid);

                }
            }elseif(DB_num_rows($SearchResult)==1) {
             /*1 resultado*/
                $myrow=DB_fetch_array($SearchResult);
                $stockid = $myrow['stockid'];
                $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => '1', 'sql' => $SQL, 'item' => $stockid, 'qty'=>$qty ,'Scale'=>'YES');
            }
            echo json_encode($regresar);
            exit();
        }else{
        if($cate == "-9") {
            /*cuando es todas las caterogiras*/
            if(strlen($item) > 0) {
                $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND (stockmaster.stockid LIKE '%" . $item . "%' OR stockmaster.barcode = '".$item."') AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            }elseif(strlen($desc) > 0) {
                $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND stockmaster.description LIKE '%" . $desc . "%' AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            }
        }else{
            /*cuando trae una categoria en especial*/
            if(strlen($item) > 0) {
                $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND stockmaster.stockid LIKE '%" . $item . "%' AND stockmaster.categoryid='" . $cate . "' AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            }elseif(strlen($desc) > 0) {
                $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE  stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND stockmaster.description LIKE '%" . $desc . "%' AND stockmaster.categoryid='" . $cate . "' AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            }else{
                $SQL = "SELECT stockmaster.stockid, stockmaster.description, stockmaster.units FROM stockmaster, stockcategory WHERE  stockmaster.categoryid=stockcategory.categoryid AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D') AND stockmaster.categoryid='" . $cate . "' AND stockmaster.discontinued = 0 ORDER BY stockmaster.stockid";
            }
        }
        $SearchResult = DB_query($SQL,$db);
        if (DB_num_rows($SearchResult)==0) {
            /*sin resultados*/
            $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => '0', 'sql' => '');
        }elseif(DB_num_rows($SearchResult)==1) {
            /*1 resultado*/
            $myrow=DB_fetch_array($SearchResult);
            $stockid = $myrow['stockid'];
            $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => '1', 'sql' => $SQL, 'item' => $stockid);
        }else {
            /*muchos resultados*/
            $regresar = array('error' => 'no', 'tipoE' => '0', 'resultados' => DB_num_rows($SearchResult), 'sql' => $SQL);
        }
        echo json_encode($regresar);
        exit();
        }
        break;
    /*entra otra funcion*/
    case "sql":
        $sql = $_POST['sql'];
        $sql = stripslashes($sql);
        $SearchResult = DB_query($sql,$db);
        $tabla = generate_html_table($SearchResult);
        echo $tabla;
        exit();
        break;
    /*entra otra funcion*/
    case "inicial":
        $terminal = $_POST['cate'];
        $user = $_POST['user'];
        /*se obtienen los datos de los clientes */
        $sql = 'SELECT
                      custbranch.branchcode
                    , debtorsmaster.debtorno
                FROM
                    locations
                INNER JOIN rh_pos_terminales
                    ON (locations.loccode = rh_pos_terminales.Sucursal)
                INNER JOIN debtorsmaster
                    ON (debtorsmaster.debtorno = rh_pos_terminales.debtorno)
                INNER JOIN custbranch
                    ON (custbranch.debtorno = debtorsmaster.debtorno) where rh_pos_terminales.id="'.$terminal.'"';
        $cliente = DB_query($sql,$db);
        if(DB_num_rows($cliente) == 0) {
            /*no hay resultados*/
            $cliente = "";
            $sucursal = "";
        }else {
            /*si hay resultados*/
            $myrow=DB_fetch_array($cliente);
            $cliente = $myrow['debtorno'];
            $sucursal = $myrow['branchcode'];
        }
        $_SESSION['rh_pos_principal'] = array('terminal' => $terminal, 'usuario' => $user, 'cliente' => $cliente, 'sucursalC' => $sucursal, 'IP' => fetch_alt_ip());
        break;
    /*entra otra funcion*/
    case "sucursal":
        $cliente = $_POST['cliente'];
        $sql = "SELECT custbranch.branchcode, debtorsmaster.taxref FROM custbranch INNER JOIN debtorsmaster ON (custbranch.debtorno = debtorsmaster.debtorno) WHERE debtorsmaster.debtorno = '".$cliente."'";
        //$sql = "Select id, razon FROM rh_datos_facturacion_cliente WHERE debtorno = '".$cliente."'";
        $cliente = DB_query($sql,$db);
        if(DB_num_rows($cliente) == 0) {
            echo "no";
        }else {
            $sucursales = "";
            $contador = 0;
            while ($myrow=DB_fetch_array($cliente)) {
                $contador = $contador + 1;
                if($contador == 1) {
                    $sucursales .= "<option value='".$myrow['branchcode']."' selected='selected'>".$myrow['taxref']."</option>";
                }else {
                    $sucursales .= "<option value='".$myrow['branchcode']."'>".$myrow['taxref']."</option>";
                }
            }
        }
        echo $sucursales;
        exit();
        break;
    /*entra otra funcion*/
    case "UPsucursal":
        $sucursal = $_POST['sucursal'];
        $clientee = $_POST['clientee'];

        $principal = $_SESSION['rh_pos_principal'];
        $terminal = $principal['terminal'];
        $ip = $principal['IP'];
        $usuario = $principal['usuario'];
        /*se hace update del array*/
        unset($_SESSION['rh_pos_principal']);
        $_SESSION['rh_pos_principal'] = 0;
        unset($_SESSION['rh_pos_principal']);
        $_SESSION['rh_pos_principal'] = array('terminal' => $terminal, 'usuario' => $usuario, 'cliente' => $clientee, 'sucursalC' => $sucursal, 'IP' => $ip);
        //print_r($_SESSION['rh_pos_principal']);
        exit();
        break;
    /*entra otra funcion*/
    case "addItem":
        $item = $_POST['item'];
        $qtyScale = $_POST['qty'];
        if($qtyScale!='NO'){
            $qtyScale=$qtyScale/10000;
        }
        /*revisa que verdaderamente que se pueda pedir el articulos*/
        $bandera = checa_inventarios($item, 1);
        $decode = json_decode($bandera);
        //print_r($decode);
        /*aki recibe el array que es el que valida los datos con los cuales se va a trabajar*/
        $error = $decode->error;
        /*se hace la evaluacion de los datos con los cuales se van a trabajar la informacion con la cual se va a evaluar*/
        if($error == "si") {
            /*porque no cabe*/
            echo "stock";
            exit();
        }
        /*se consulta la informacion con la cual se esta trabajando el elemento*/
        $sql = "Select stockid, categoryid, description, mbflag, units, taxcatid FROM stockmaster WHERE stockid = '".$item."'";
        $ite = DB_query($sql,$db);

        if(DB_num_rows($ite) == 0) {
            /*cuando no hay resultados*/
            echo "no";
        }else {
            $myrow=DB_fetch_array($ite);
            /*se preparan los datos con los cuales vamos a estar trabajando*/
            $stockid = $myrow['stockid'];
            $description = $myrow['description'];
            if($qtyScale!='NO'){
                $cantidad = $qtyScale;
            }else{
                $cantidad = 1;
            }
            $descuento = 0;
            $taxcatid = $myrow['taxcatid'];
            $unidad = $myrow['units'];

            //$sql = "SELECT taxgrouptaxes.calculationorder, taxauthorities.description, taxgrouptaxes.taxauthid, taxauthorities.taxglcode, taxgrouptaxes.taxontax, taxauthrates.taxrate FROM taxauthrates INNER JOIN taxgrouptaxes ON taxauthrates.taxauthority=taxgrouptaxes.taxauthid INNER JOIN taxauthorities ON taxauthrates.taxauthority=taxauthorities.taxid WHERE taxgrouptaxes.taxgroupid=4 AND taxauthrates.dispatchtaxprovince=2 AND taxauthrates.taxcatid = ".$taxcatid." ORDER BY taxgrouptaxes.calculationorder";
            $sql='SELECT
    taxauthrates.taxrate,
    (taxauthrates.taxrate * 100) as iva
FROM
    custbranch
    INNER JOIN taxgroups
        ON (custbranch.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxgrouptaxes
        ON (taxgrouptaxes.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxauthrates
        ON (taxgrouptaxes.taxauthid = taxauthrates.taxauthority) where taxauthrates.taxcatid=6 and custbranch.branchcode="'.$_SESSION['rh_pos_principal']['sucursalC'].'" and taxauthrates.taxrate>0;';
            $ivass = DB_query($sql,$db);
            if(DB_num_rows($ivass) == 0) {
                $ivadd = 0;
            }else {
                $myrowd=DB_fetch_array($ivass);
                $ivadd = $myrowd['taxrate'];
            }
            if($taxcatid==6){
                $iva = $ivadd;
            }else{
                $iva = 0;
            }
            $principal = $_SESSION['rh_pos_principal'];
            $preprecio = GetPrice ($stockid, $principal['cliente'], $principal['sucursalC'], $db);
            $pre_total = (($preprecio*$iva)*$qtyScale);
            $total = ($pre_total+$preprecio);
            $total = (int) ($total*100);
            $total = $total/100;

            $prepara['item'] = $stockid;
            $prepara['descripcion'] = $description;
            if($qtyScale!='NO'){
                $prepara['cantidad'] = $qtyScale;
            } else{
                $prepara['cantidad'] = 1;
            }
            $prepara['descuento'] = 0;
            $prepara['iva'] = $iva;
            $prepara['precio'] = $preprecio;
            $prepara['pretotal'] = $pre_total*$qtyScale;
            $prepara['total'] = $total*$qtyScale;
            /*se sube a la funcion general*/
            add_array_array($prepara);
        }
        exit();
        break;
    /*entra otro metodo*/
    case "getLastItem":
        $qtyScale=$_POST['qty'];
        echo get_lastItem($qtyScale);
        exit();
        break;
    case "getTable":
        echo generate_table_items();
        exit();
        break;
    /*entra otro metodo*/
    case "getTotal":
        echo get_total_compra();
        exit();
        break;
    /*entra otro metodo*/
    case "unomasuno":
        $no = $_POST['no'];
        $item = $_POST['item'];
        /*se consultan los datos*/
        echo operacion_sobre_item('suma', 1, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "restaunoresta":
        $no = $_POST['no'];
        $item = $_POST['item'];
        /*se consultan los datos*/
        echo operacion_sobre_item('resta', 1, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "manualcantidad":
        $no = $_POST['no'];
        $item = $_POST['item'];
        $name = $_POST['name'];
        /*se consultan los datos*/
        echo operacion_sobre_item('cantidad', $name, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "deleteuno":
        $no = $_POST['no'];
        $item = $_POST['item'];
        echo operacion_sobre_item('eliminar', 1, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "descuento":
        $password = $_POST['password'];
        $dividir_password = @explode("@", $password);

        $sql = "SELECT userid FROM www_users WHERE userid = '".$dividir_password[0]."' AND password = '".CryptPass($dividir_password[1])."' AND (fullaccess = '8' OR userid='rosyb' OR userid='SUEMI' OR userid='ANDREA')";
        $acc = DB_query($sql,$db);
        if(DB_num_rows($acc) == 0) {
            /*no tiene acceso*/
            echo "no";
        }else {
            /*tiene acceso*/
            echo "si";
        }
        die();
        break;
    /*entra otro metodo*/
    case "Adescuento":
        $result = $_POST['result'];
        $no = $_POST['no'];
        $item = $_POST['item'];

        /*se procesa la administracion de el descuento*/
        echo operacion_sobre_item('descuento', $result, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "cPrecio":
        $result = $_POST['result'];
        $no = $_POST['no'];
        $item = $_POST['item'];

        /*se procesa la administracion del nuevo precio*/
        echo operacion_sobre_item('precio', $result, $no, $item);
        exit();
        break;
    /*entra otro metodo*/
    case "clearVenta":
        unset($_SESSION['ventas']);
        $_SESSION['ventas'] = 0;
        unset($_SESSION['ventas']);
        echo "si";
        break;
    /*entra en otro metodo*/
    case "guardarVenta":
    /*se obtiene los datos basicos*/
        $padre = $_SESSION['ventas'];
        $elementos = count($padre);
        if($elementos == 0) {
            /*marca error*/
            echo "no";
            exit();
        }else {
            $contador = 0;
            $principal = $_SESSION['rh_pos_principal'];
            $terminal = $principal['terminal'];
            $ip = $principal['IP'];
            $usuario = $principal['usuario'];
            /**/
            $sql_00 = "INSERT INTO rh_pos_guardarVenta(Fecha, Usuario, Terminal, IP) VALUES('".date('Y-m-d H:i:s')."', '".$usuario."', '".$terminal."', '".$ip."')";
            //echo $sql_00;
            $guardar_00 = DB_query($sql_00,$db);
            $venta_guardada = DB_Last_Insert_ID($db,'rh_pos_guardarVenta','id');//$_SESSION['LastInsertId'];
            /**/
            for($x=0;$x<$elementos;$x++) {
                $contador = $contador + 1;
                $individuales = $padre[$x];

                $stockid_a = $individuales['item'];
                $description_a = $individuales['descripcion'];
                $cantidad_a = $individuales['cantidad'];
                $descuento_a = $individuales['descuento'];
                $iva_a = $individuales['iva'];
                $precio_a = $individuales['precio'];
                $pretotal_a = $individuales['pretotal'];
                $total_a = $individuales['total'];

                $sql_01 = "INSERT INTO rh_pos_guardarVenta_Items(Venta, item, descripcion, cantidad, descuento, iva, precio, pretotal, total) VALUES('".$venta_guardada."', '".$stockid_a."', '".$description_a."', '".$cantidad_a."', '".$descuento_a."', '".$iva_a."', '".$precio_a."', '".$pretotal_a."', '".$total_a."')";
                $guardar_01 = DB_query($sql_01,$db);
            }

            unset($_SESSION['ventas']);
            $_SESSION['ventas'] = 0;
            unset($_SESSION['ventas']);

            echo $venta_guardada;
            exit();
        }
        break;
    /*otro metodo*/
    case "cajaR":
    /*http://www.kksou.com/php-gtk2/articles/kick-open-the-cash-drawer-of-a-point-of-sale-system.php*/
        $handle = fopen("PRN", "w"); // note 1
        fwrite($handle, 'text to printer'); // note 2
        fclose($handle); // note 3
        echo "si";
        exit();
        break;
    /*otro metodo*/
    case "recuperarVenta":
        $result = $_POST['result'];
        /*sql de los articulos*/
        $sql = "Select item, descripcion, cantidad, descuento, iva, precio, pretotal, total FROM rh_pos_guardarVenta_Items WHERE Venta = '".$result."'";
        $results = DB_query($sql,$db);
        if(DB_num_rows($results) == 0) {
            echo "no";
            exit();
        }else {
            unset($_SESSION['ventas']);
            $_SESSION['ventas'] = 0;
            unset($_SESSION['ventas']);
            /*valores*/
            $contador = 0;
            while($myrow=DB_fetch_array($results)) {
                $tmp['item'] = $myrow['item'];
                $tmp['descripcion'] = $myrow['descripcion'];
                $tmp['cantidad'] = $myrow['cantidad'];
                $tmp['descuento'] = $myrow['descuento'];
                $tmp['iva'] = $myrow['iva'];
                $tmp['precio'] = $myrow['precio'];
                $tmp['pretotal'] = $myrow['pretotal'];
                $tmp['total'] = $myrow['total'];
                /*datos con los cuales se van a trabajar*/
                $array_temporal[$contador] = $tmp;
                $contador = $contador + 1;
            }
            /**/
            unset($_SESSION['ventas']);
            $_SESSION['ventas'] = 0;
            unset($_SESSION['ventas']);
            $_SESSION['ventas'] = $array_temporal;
            /**/
            //se boran los registros de la base de datos

            $sql__ = "delete from rh_pos_guardarVenta WHERE id = '".$result."'";
            //$results = DB_query($sql__,$db);

            $sql__ = "delete from rh_pos_guardarVenta_Items WHERE Id = '".$result."'";
            //$results = DB_query($sql__,$db);

            /**/
            echo "si";
            exit();
        }
        break;
    /*otro metodo*/
    /*otro metodo*/
    case "verificador":
        $result = $_POST['result'];
        $sql = "Select stockid, categoryid, description, mbflag, units, taxcatid FROM stockmaster WHERE (stockmaster.stockid LIKE '%" . $result . "%' OR stockmaster.barcode = '".$result."')";
        $ite = DB_query($sql,$db);
        if(DB_num_rows($ite) == 0) {
            echo "no";
            exit;
        }else {
            $myrow=DB_fetch_array($ite);
            /*se preparan los datos con los cuales vamos a estar trabajando*/
            $stockid = $myrow['stockid'];
            $description = $myrow['description'];
            $cantidad = 1;
            $descuento = 0;
            $taxcatid = $myrow['taxcatid'];
            $unidad = $myrow['units'];

            //$sql = "SELECT taxgrouptaxes.calculationorder, taxauthorities.description, taxgrouptaxes.taxauthid, taxauthorities.taxglcode, taxgrouptaxes.taxontax, taxauthrates.taxrate FROM taxauthrates INNER JOIN taxgrouptaxes ON taxauthrates.taxauthority=taxgrouptaxes.taxauthid INNER JOIN taxauthorities ON taxauthrates.taxauthority=taxauthorities.taxid WHERE taxgrouptaxes.taxgroupid=4 AND taxauthrates.dispatchtaxprovince=2 AND taxauthrates.taxcatid = ".$taxcatid." ORDER BY taxgrouptaxes.calculationorder";
            $sql='SELECT
    taxauthrates.taxrate,
    (taxauthrates.taxrate * 100) as iva
FROM
    custbranch
    INNER JOIN taxgroups
        ON (custbranch.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxgrouptaxes
        ON (taxgrouptaxes.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxauthrates
        ON (taxgrouptaxes.taxauthid = taxauthrates.taxauthority) where taxauthrates.taxcatid=6 and custbranch.branchcode="'.$_SESSION['rh_pos_principal']['sucursalC'].'" and taxauthrates.taxrate>0;';
            $ivass = DB_query($sql,$db);
            if(DB_num_rows($ivass) == 0) {
                $ivadd = 0;
            }else {
                $myrowd=DB_fetch_array($ivass);
                $ivadd = $myrowd['taxrate'];
            }
            if($taxcatid==6){
                $iva = $ivadd;
            }else{
                $iva = 0;
            }
            $principal = $_SESSION['rh_pos_principal'];
            $preprecio = GetPrice ($stockid, $principal['cliente'], $principal['sucursalC'], $db);
            $pre_total = (($preprecio*$iva));
            $total = ($pre_total+$preprecio);
            $total = (int) ($total*100);
            $total = $total/100;
            echo utf8_encode($description).'?'.number_format($total,2);
        }
        break;
    /*otro metodo*/
    case "pagar":
        $tipos = $_POST['tipos'];
        switch($tipos) {
            case "efectivo":
                /*datos basicos*/
                $array_tmp['tipo'] = $tipos;
                $array_tmp['monto'] = $_POST['monto'];
                /*datos tarjeta*/
                $array_tmp['digitos'] = '';
                $array_tmp['aprovacion'] = '';
                /*datos cheque*/
                $array_tmp['banco'] = '';
                $array_tmp['nombre'] = '';
                $array_tmp['numero'] = '';
                /*general*/
                $_SESSION['pagos'][] = $array_tmp;
                die();
                break;
            case "tarjeta":
                /*datos basicos*/
                $array_tmp['tipo'] = $tipos;
                $array_tmp['monto'] = $_POST['monto'];
                /*datos tarjeta*/
                $array_tmp['digitos'] = $_POST['digitos'];
                $array_tmp['aprovacion'] = $_POST['aprovacion'];
                /*datos cheque*/
                $array_tmp['banco'] = '';
                $array_tmp['nombre'] = '';
                $array_tmp['numero'] = '';
                /*general*/
                $_SESSION['pagos'][] = $array_tmp;
                die();
                break;
            case "cheque":
                /*datos basicos*/
                $array_tmp['tipo'] = $tipos;
                $array_tmp['monto'] = $_POST['monto'];
                /*datos tarjeta*/
                $array_tmp['digitos'] = '';
                $array_tmp['aprovacion'] = '';
                /*datos cheque*/
                $array_tmp['banco'] = $_POST['banco'];
                $array_tmp['nombre'] = $_POST['nombre'];
                $array_tmp['numero'] = $_POST['numero'];
                /*general*/
                $_SESSION['pagos'][] = $array_tmp;
                die();
                break;
            case "vales":
                /*datos basicos*/
                $array_tmp['tipo'] = $tipos;
                $array_tmp['monto'] = $_POST['monto'];
                /*datos tarjeta*/
                $array_tmp['digitos'] = '';
                $array_tmp['aprovacion'] = '';
                /*datos cheque*/
                $array_tmp['banco'] = '';
                $array_tmp['nombre'] = '';
                $array_tmp['numero'] = '';
                /*general*/
                $_SESSION['pagos'][] = $array_tmp;
                die();
                break;
        }
        exit();
        break;
    /**/
    case "topagado":
        $padre = $_SESSION['pagos'];
        $movimiento = count($padre);
        if($movimiento == 0) {
            /*cuando no hay movimientos*/
            echo 0;
        }else {
            /*cuando si hay movimientos*/
            $totalP = 0;
            for($r=0;$r<$movimiento;$r++) {
                $nodos = $padre[$r];
                $totalP = $totalP + $nodos['monto'];
            }
            echo $totalP;
        }
        exit();
        break;
    /*entra otro metodo*/
    case "revPago":
    /*seccion para revisar si hay pago*/
        $devuelve =  str_replace(",", "", get_total_compra());
        /*asiga la funcion del vendedor para trabajar con el*/
        $principal = $_SESSION['rh_pos_principal'];
        $terminal = $principal['terminal'];
        $usuario = $principal['usuario'];
        $cliente = $principal['cliente'];
        $sucursalC = $principal['sucursalC'];
        $IP = $principal['IP'];

        unset($_SESSION['rh_pos_principal']);
        $_SESSION['rh_pos_principal'] = 0;
        unset($_SESSION['rh_pos_principal']);

        $_SESSION['rh_pos_principal'] = array('terminal' => $terminal, 'usuario' => $usuario, 'cliente' => $cliente, 'sucursalC' => $sucursalC, 'IP' => $IP, 'ved_de_dor' => $_POST['vendedor']);
        
        if($devuelve > 0){
            /*hay que pagar*/
            echo "si";
            exit();
        }else{
            /*no hay que pagar*/
            echo "no";
            exit();
        }
        exit();
   break;
   /*entra otro metodo*/
   case "actuPagOs":
        $generales = $_SESSION['pagos'];
        $total = count ($generales);
        if($generales == 0){
            /*que no hay datos en el array que tenemos*/
            $regresa = array('pago' => '<p>Sin Pagos</p>', 'cambio' => '0', 'restante' => '0', 'totalP' => '0');
        }else{
            $total_compra = get_total_compra();
            $total_pagado = get_total_pagado();

            $rev_total_compra = str_replace(",", "", $total_compra);
            $rev_total_pagado = str_replace(",", "", $total_pagado);
            $html = get_total_pagado_html();

            $restante = ($rev_total_compra-$rev_total_pagado);
            if($restante > 0){
                /*cuando es mayor*/
                $regresa = array('pago' => $html, 'cambio' => '0', 'restante' => number_format($restante, 2), 'totalP' => number_format($total_pagado, 2));
            }elseif($restante == 0){
                /*cuando es igual*/
                $regresa = array('pago' => $html, 'cambio' => '0', 'restante' => '0', 'totalP' => number_format($total_pagado, 2));
            }else{
                /*cuando es menor*/
                $regresa = array('pago' => $html, 'cambio' => number_format(($restante*-1), 2), 'restante' => '0', 'totalP' => number_format($total_pagado, 2));
            }
        }
        echo json_encode($regresa);
        exit();
   break;
   case "quiTarPaGo":
        $llave = $_POST['llave'];
        actualiza_pagos_p($llave);
        echo "si";
        exit();
   break;
   case "pagoFiNaL":
        $total_compra = get_total_compra();
        $total_pagado = get_total_pagado();
        $regresa = array('total' => str_replace(",", "", $total_compra), 'pagado' => str_replace(",", "", $total_pagado));
        echo json_encode($regresa);
        exit();
   break;
   case "procesaCompra":
       /*seccion donde se realizar la compra*/
       include('includes/SQL_CommonFunctions.inc');
       include('includes/FreightCalculation.inc');
       include('includes/GetSalesTransGLCodes.inc');
       include ('rh_pos_genera.php');
       echo genera_venta_weberp();
       exit();
   break;
}

?>