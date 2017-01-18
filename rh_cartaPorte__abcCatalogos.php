<?php
//Php
header('Content-type: text/html; charset=utf-8');
$PageSecurity = 2;
include('includes/session.inc');
if(isSet($_POST['request'])){
    switch($_POST['request']){
        case 'altaCiudad':
            try{
                if(!DB_query('begin',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el begin' , 1);
                }
                $idEstado = $_POST['idEstado'];
                $ciudad = $_POST['ciudad'];
                //validaciones y noSqlInjection
                if(!is_numeric($idEstado))
                    throw new Exception('El id del estado no es entero');
                $ciudad = mysql_real_escape_string($ciudad);
                //\validaciones y noSqlInjection
                $sqlInsert = "insert into rh_carta_porte__catalogo(pid, nombre, tipo) values($idEstado, '$ciudad', 2)";
                $result = DB_query($sqlInsert,$db,'','',false,false);
                if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                    throw new Exception('No se pudo insertar la ciudad', 1);
                }
                if(!DB_query('commit',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el commit' , 1);
                }
            }
            catch(Exception $exception){
                $msg = $exception->getMessage();
                if($exception->getCode()==1){
                    $error = mysql_error();
                    $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
                }
                if(!DB_query('rollback',$db,'','',false,false)){
                    $msg .= ' (Error al efectuar el rollback)';
                }
                echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
                return;
            }
            echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se dio de alta la ciudad con exito"}]';
            return;
            break;
        case 'altaCatalogo':
            try{
                $valor = $_POST['valor'];
                $tipo = $_POST['tipo'];
                //validaciones y noSqlInjection
                if(!is_numeric($tipo))
                    throw new Exception('El tipo del valor del catalogo no es entero');
                $valor = mysql_real_escape_string($valor);
                //\validaciones y noSqlInjection
                if(!DB_query('begin',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el begin' , 1);
                }
                $sqlInsert = "insert into rh_carta_porte__catalogo(nombre, tipo) values('$valor', $tipo)";
                $result = DB_query($sqlInsert,$db,'','',false,false);
                if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                    throw new Exception('No se pudo insertar la ciudad', 1);
                }
                if(!DB_query('commit',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el commit' , 1);
                }
            }
            catch(Exception $exception){
                $msg = $exception->getMessage();
                if($exception->getCode()==1){
                    $error = mysql_error();
                    $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
                }
                if(!DB_query('rollback',$db,'','',false,false)){
                    $msg .= ' (Error al efectuar el rollback)';
                }
                echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
                return;
            }
            echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se dio de alta el valor del catalogo con exito"}]';
            return;
            break;
        case 'bajaCatalogo':
            try{
                $id = $_POST['id'];
                //validaciones y noSqlInjection
                if(!is_numeric($id))
                    throw new Exception('El id del valor del catalogo no es entero');
                //\validaciones y noSqlInjection
                if(!DB_query('begin',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el begin' , 1);
                }
                $sqlInsert = "delete from rh_carta_porte__catalogo where id = $id";
                $result = DB_query($sqlInsert,$db,'','',false,false);
                if(mysql_errno($db) || mysql_affected_rows($db)!=1){
                    throw new Exception('No se pudo dar de baja el valor', 1);
                }
                if(!DB_query('commit',$db,'','',false,false)){
                    throw new Exception('Error al efectuar el commit' , 1);
                }
            }
            catch(Exception $exception){
                $msg = $exception->getMessage();
                if($exception->getCode()==1){
                    $error = mysql_error();
                    $msg .= ' (SQL' . ($error?': ' . $error:'') . ')';
                }
                if(!DB_query('rollback',$db,'','',false,false)){
                    $msg .= ' (Error al efectuar el rollback)';
                }
                echo '[{cssClass:"error", prefix:"' . _('ERROR') . '", msg:"' . $msg . '"}]';
                return;
            }
            echo '[{cssClass:"success", prefix:"' . _('SUCCESS') . '", msg:"Se dio de baja el valor con exito"}]';
            return;
            break;
        case 'getCatalogo':
            $tipo = $_POST['tipo'];
            //validaciones y noSqlInjection
            if (!is_numeric($tipo))
                throw new Exception('El tipo del valor del catalogo no es entero');
            //\validaciones y noSqlInjection
            $query = "select id, nombre from rh_carta_porte__catalogo where tipo = $tipo";
            break;
        case 'getEstados':
            $query = 'select id, nombre from rh_carta_porte__catalogo where tipo = 1';
            break;
        case 'getCiudades':
            $idEstado = $_POST['idEstado'];
            //validaciones y noSqlInjection
            if (!is_numeric($idEstado))
                throw new Exception('El id del estado no es entero');
            //\validaciones y noSqlInjection
            $query = "select id, nombre from rh_carta_porte__catalogo where tipo = 2 and pid = $idEstado";
            break;
    }
    $resultado = DB_query($query, $db);
    $arreglo = array();
    while($objeto = mysql_fetch_object($resultado))
        $arreglo[] = $objeto;
    print json_encode($arreglo);
    return;
}
$title = _('ABC Catalogos');
include('includes/header.inc');
?>
<div id="divTodo" align="center">
    <div id="divWeberpPrnMsg"></div>
    <?php if(isSet($_GET['msg'])) prnMsg($_GET['msg'], $_GET['msgType']); ?>
    <br />
    <div id="divTableAbcCiudad">
        <table id="tableAbcCiudad" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Ciudad') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divSelectEstado" title="seleccione para ver ciudades">
                            <?php echo _('Estado')?>
                        </label>
                    </td>
                    <td>
                        <div id="divSelectEstado"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableCiudad">
                            <?php echo _('Ciudades:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableCiudad">Seleccione una ciudad...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcOperador">
        <table id="tableAbcOperador" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Operador') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableOperador">
                            <?php echo _('Operadores:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableOperador"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcCarro">
        <table id="tableAbcCarro" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Carro') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableCarro">
                            <?php echo _('Carros:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableCarro"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcRemolque">
        <table id="tableAbcRemolque" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Remolque') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableRemolque">
                            <?php echo _('Remolques:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableRemolque"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcCliente">
        <table id="tableAbcCliente" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Cliente') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableCliente">
                            <?php echo _('Clientes:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableCliente"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcDestino">
        <table id="tableAbcDestino" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Destino') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableDestino">
                            <?php echo _('Destinos:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableDestino"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <br/>
    <br/>
    <div id="divTableAbcRetenedor">
        <table id="divTableAbcRetenedor" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Retenedor') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divTableRetenedor">
                            <?php echo _('Retenedores:')?>
                        </label>
                    </td>
                    <td>
                        <div id="divTableRetenedor"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript">
    main()

    function altaCiudad(ciudad){
        var idEstado = $('selectEstado').value
        var r = ajax('rh_cartaPorte__abcCatalogos.php', {request:'altaCiudad', idEstado: idEstado, ciudad: ciudad})[0]
        if(r.cssClass == 'success')
            return true
        else{
            $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
            return false
        }
    }

    function bajaCiudad(idCiudad){
        var r = ajax('rh_cartaPorte__abcCatalogos.php', {request:'bajaCiudad', idCiudad: idCiudad})[0]
        if(r.cssClass == 'success')
            return true
        else{
            $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
            return false
        }
    }

    function getCiudades(){
        var idEstado = $('selectEstado').value
        return ajax('rh_cartaPorte__abcCatalogos.php', {request:'getCiudades', idEstado:idEstado})
    }

    function loadSelectCiudad(){
        var idEstado = $('selectEstado').value
        if(!v(idEstado)){
            $('divTableCiudad').innerHTML = 'Seleccione una ciudad...'
            return
        }
        abcTable(getCiudades, 'divTableCiudad', 'inputTextCiudad', function(){return altaCiudad($('inputTextCiudad').value)}, bajaCatalogo);
    }
    
    function loadSelectEstado(){
        var r = ajax('rh_cartaPorte__abcCatalogos.php', {request:'getEstados'})
        var s = loadSelectFromJson('selectEstado', r)
        s.onchange = function(){
            loadSelectCiudad()
        }
        $('divSelectEstado').appendChild(s)
    }

    function abcTable(funcionGetData, div, idInputTextAlta, funcionAlta, funcionBaja){
        var r = funcionGetData()
        var table = document.createElement('table')
        //table.id = idTable;
        var tr = document.createElement('tr')
        var td1 = document.createElement('td');
        td1.style.display = 'none'
        var td2 = document.createElement('td');
        var inputTextAlta = document.createElement('input')
        inputTextAlta.id = idInputTextAlta
        inputTextAlta.type = 'text'
        inputTextAlta.onkeydown = function(e){
            if (e.keyCode == 13){
                if(funcionAlta())
                    abcTable(funcionGetData, div, idInputTextAlta, funcionAlta, funcionBaja)
                $(idInputTextAlta).focus();
            }
        }
        td2.appendChild(inputTextAlta)
        var td3 = document.createElement('td');
        var buttonAlta = document.createElement('input')
        buttonAlta.type = 'button'
        buttonAlta.setAttribute('value', 'Alta')
        buttonAlta.onclick = function(){
            if(funcionAlta())
                abcTable(funcionGetData, div, idInputTextAlta, funcionAlta, funcionBaja)
            $(idInputTextAlta).focus();
        }
        td3.appendChild(buttonAlta)
        tr.appendChild(td1)
        tr.appendChild(td2)
        tr.appendChild(td3)
        table.appendChild(tr)

        for(var i = r.length-1; i >= 0; i--){
            var tr = document.createElement('tr')
            var td1 = document.createElement('td');
            td1.appendChild(document.createTextNode(r[i].id))
            td1.style.display = 'none'
            var td2 = document.createElement('td');
            td2.appendChild(document.createTextNode(r[i].nombre))
            var td3 = document.createElement('td');
            var buttonBaja = document.createElement('input')
            buttonBaja.type = 'button'
            buttonBaja.setAttribute('value', 'Baja')
            buttonBaja.appendChild(document.createTextNode('Baja'))
            buttonBaja.onclick = function(){
                if(funcionBaja(this.parentNode.parentNode.cells[0].innerHTML))
                    abcTable(funcionGetData, div, idInputTextAlta, funcionAlta, funcionBaja)
            }
            td3.appendChild(buttonBaja)
            tr.appendChild(td1)
            tr.appendChild(td2)
            tr.appendChild(td3)
            table.appendChild(tr)
        }
        $(div).innerHTML = ''
        $(div).appendChild(table)
        //recarga el nuevo input text para que contenga solo mayusculas
        setInputTextToUpper()
    }

    function getCatalogo(tipo){
        return ajax('rh_cartaPorte__abcCatalogos.php', {request:'getCatalogo', tipo: tipo})
    }

    function altaCatalogo(idInputText, tipo){
        var valor = $(idInputText).value
        var r = ajax('rh_cartaPorte__abcCatalogos.php', {request:'altaCatalogo', valor: valor, tipo: tipo})[0]
        if(r.cssClass == 'success')
            return true
        else{
            $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
            return false
        }
    }

    function bajaCatalogo(id){
        var r = ajax('rh_cartaPorte__abcCatalogos.php', {request:'bajaCatalogo', id: id})[0]
        if(r.cssClass == 'success')
            return true
        else{
            $('divWeberpPrnMsg').innerHTML += '<div class="' + r.cssClass + '"><p><b>' + r.prefix + '</b> : ' + r.msg + '<p></div>';
            return false
        }
    }

    function loadAbcTableOperador(){
        abcTable(function(){return getCatalogo(3)}, 'divTableOperador', 'inputTextOperador', function(){return altaCatalogo('inputTextOperador', 3)}, bajaCatalogo);
    }

    function loadAbcTableCarro(){
        abcTable(function(){return getCatalogo(4)}, 'divTableCarro', 'inputTextCarro', function(){return altaCatalogo('inputTextCarro', 4)}, bajaCatalogo);
    }

    function loadAbcTableRemolque(){
        abcTable(function(){return getCatalogo(5)}, 'divTableRemolque', 'inputTextRemolque', function(){return altaCatalogo('inputTextRemolque', 5)}, bajaCatalogo);
    }

    function loadAbcTableCliente(){
        abcTable(function(){return getCatalogo(6)}, 'divTableCliente', 'inputTextCliente', function(){return altaCatalogo('inputTextCliente', 6)}, bajaCatalogo);
    }

    function loadAbcTableDestino(){
        abcTable(function(){return getCatalogo(7)}, 'divTableDestino', 'inputTextDestino', function(){return altaCatalogo('inputTextDestino', 7)}, bajaCatalogo);
    }

    function loadAbcTableRetenedor(){
        abcTable(function(){return getCatalogo(8)}, 'divTableRetenedor', 'inputTextRetenedor', function(){return altaCatalogo('inputTextRetenedor', 8)}, bajaCatalogo);
    }

    function main(){
        loadSelectEstado()
        loadAbcTableOperador()
        loadAbcTableCarro()
        loadAbcTableRemolque()
        loadAbcTableCliente()
        loadAbcTableDestino()
        loadAbcTableRetenedor()
    }
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<script type="text/javascript">
    setInputTextToUpper()
</script>
<?php
include('includes/footer.inc');
?>