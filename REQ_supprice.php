<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 20/Dic/2016
* Archivo creado para la lista de precios-proveedores
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Lista de Precios-Proveedores');
include('includes/header.inc');

// VERIFICAMOS QUE PUEDE ENTRAR AL MODULO - POR DANIEL VILLARREAL EL 16 DE ENERO DEL 2017
$sqlacceso = 'SELECT or_compras FROM www_users where userid ="'.$_SESSION['UserID'].'"';
$resultacceso = DB_query($sqlacceso,$db);
$rowacceso = DB_fetch_array($resultacceso);
if($rowacceso['or_compras']!=1)
{
    // no tiene acceso al modulo
    prnMsg(_('No cuenta con el acceso al catalogo.'),'warning');
    include('includes/footer.inc');
    exit;
}
// TERMINA - POR DANIEL VILLARREAL EL 16 DE ENERO DEL 2017


//***********************Carga de archivo***************************************
if(isset($_FILES['im'])){

$flag=false;
if(@mkdir('importfiles',0777)){
    $flag=true;
} else if (chdir('importfiles')){
    $flag=true;
}else{
   prnMsg(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'), 'error');
}
if($flag){
    $p=@chdir('importfiles');
    $path=realpath($p);
    $path = str_replace('\\','/',$path);
}
if(($flag)){
$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
if(isset($_FILES['im'])&&$_FILES['im']['error']<>4&&($tamano < 3000000)//&&(($tipo=="text/csv")||($tipo=="text/text")||($tipo=="text/plain")||($tipo=="application/vnd.ms-excel"))
){

    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
        $filename = $path.'/'.$_FILES['im']['name'];
        if(@$fh_in = fopen($filename,"r")){
            $separador=",";
            $ListadoIDAgrupador=array();
            while(($line = fgetcsv($fh_in,0,$separador))!==false){
                if($line!= null){
                    if(count($line)==1){
                        if(strpos($line[0],'|'))$separador='|';
                        else
                        if(strpos($line[0],';'))$separador=';';
                        $line=explode($separador,$line[0]);
                        
                    }
                    $size = sizeof($line);
                    //for($i=0;$i<$size;$i++){
                    $ItemCode=$line[0];
                    $SupplierId=$line[1];
                    $Price=$line[2];
                    
                    //**********************************************************
                    // verificamos que no esten vacios los campos
                    if($ItemCode=='' OR $SupplierId=='' OR $Price == '')
                    {
                        // nos saltamos al siguiente registro
                        continue;
                    }

                    // verificamos que exista el producto
                    $sqlproducto = '
                        SELECT *
                        FROM stockmaster
                        WHERE stockid="'.$ItemCode.'"';
                    $resultproducto = DB_query($sqlproducto,$db);
                    $rowproducto = DB_fetch_array($resultproducto);
                    if($rowproducto['stockid']=='')
                    {
                        // no se encontro el producto nos brincamos al siguiente renglon
                        continue;
                    }
                    // verificamos que exista el proveedor
                    $sqls_proveedor = '
                        SELECT *
                        FROM suppliers
                        WHERE supplierid="'.$SupplierId.'"';
                    $resultproveedor = DB_query($sqls_proveedor,$db);
                    $rowproveedor = DB_fetch_array($resultproveedor);
                    if($rowproveedor['supplierid']=='')
                    {
                        // no se encontro al proveedor nos brincamos al siguiente renglon
                        continue;
                    }
                    // verificamos si ya existe en la tabla de precios para hacer un insert update
                    $verificarproducto = '
                        SELECT *
                        FROM wrk_precioproveedor
                        WHERE supplierid="'.$SupplierId.'" and stockid ="'.$ItemCode.'"';
                    $verprodquery = DB_query($verificarproducto,$db);
                    $rowverprod = DB_fetch_array($verprodquery);
                    if($rowverprod['id']!='')
                    {
                        // si existe, hacemos un update
                        $sqlinsert = "UPDATE wrk_precioproveedor 
                            SET 
                                price='".$Price."',
                                user = '" . $_SESSION['UserID'] . "'
                            WHERE id = " .$rowverprod['id'] ; 

                        $ErrMsg = _('Los productos') . '  ' . _('se duplicaron o no pudieron ingresarse debidamente');
                        $DbgMsg = _('Se intento actualizar los productos nuevos pero hubo un fallo inesperado');
                        //echo $sql;
                        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
                        prnMsg(_('Se actualizo correctamente'),'success');
                    }else{
                        // si existe registro actualizamos el price
                         $sqlinsert = "INSERT INTO wrk_precioproveedor (
                            supplierid,
                            stockid,
                            price,
                            user
                        ) 
                         VALUES (
                            '" . $SupplierId . "',
                            '" . $ItemCode . "',
                            '" . $Price . "',
                            '" . $_SESSION['UserID'] . "'
                         )";
                        //echo $sqlinsert;
                        //exit;
                        $ErrMsg = _('Los productos') . '  ' . _('no pudieron ser insertados');
                        $DbgMsg = _('Se intento insertar los productos pero hubo un fallo inesperado');
                        //echo $sql;
                        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
                        prnMsg(_('Se agrego correctamente'),'success');
                    }
                    // listo
                }
             }
    }
    fclose($fh_in);
   

    }
 }
}else{
     prnMsg(_('>No se puede escribir en el directorio indicado'), 'error');
    include ('includes/footer.inc');
    exit;
}
}
//******************************************************************************
// Hacer clic para agregar registro
if(isset($_POST['altaprecioproveedor']))
{
    $insert = true;
    // CAMPOS PARA INSERTAR
    $supplierid = $_POST['supplierid'];
    $stockid = $_POST['stockid'];
    $price = $_POST['price'];
    $trandatetime = date('Y-m-d H:i:s');
    $user = $_SESSION['UserID'];

                    if($price==''){

                        prnMsg(_('El campo de precio se encuentra vacio, favor de intentarlo de nuevo '),'error');
                        $insert = false;
                    } 

                    // verificamos que no esten vacios los campos
                    if($stockid=='' OR $supplierid=='' OR $price == '')
                    {
                        // nos saltamos al siguiente registro
                        continue;
                    }

                    // verificamos que exista el producto
                    $sqlproducto = '
                        SELECT *
                        FROM stockmaster
                        WHERE stockid="'.$stockid.'"';
                    $resultproducto = DB_query($sqlproducto,$db);
                    $rowproducto = DB_fetch_array($resultproducto);
                    if($rowproducto['stockid']=='')
                    {
                        // no se encontro el producto nos brincamos al siguiente renglon
                        continue;
                    }

                    // verificamos que exista el proveedor
                    $sqls_proveedor = '
                        SELECT *
                        FROM suppliers
                        WHERE supplierid="'.$supplierid.'"';
                    $resultproveedor = DB_query($sqls_proveedor,$db);
                    $rowproveedor = DB_fetch_array($resultproveedor);
                    if($rowproveedor['supplierid']=='')
                    {
                        // no se encontro al proveedor nos brincamos al siguiente renglon
                        continue;
                    }

                    // verificamos si ya existe en la tabla de precios para hacer un insert update
                    $verificarproducto = '
                        SELECT *
                        FROM wrk_precioproveedor
                        WHERE supplierid="'.$supplierid.'" and stockid ="'.$stockid.'"';
                    $verprodquery = DB_query($verificarproducto,$db);
                    $rowverprod = DB_fetch_array($verprodquery);
                    if($rowverprod['id']!='')
                    {
                        // si existe, hacemos un update
                        $sqlinsert = "UPDATE wrk_precioproveedor 
                            SET 
                                price='".$price."',
                                user = '" . $_SESSION['UserID'] . "'
                            WHERE id = " .$rowverprod['id'] ; 

                        $ErrMsg = _('Los productos') . '  ' . _('se duplicaron o no pudieron ingresarse debidamente');
                        $DbgMsg = _('Se intento actualizar los productos nuevos pero hubo un fallo inesperado');
                        //echo $sql;
                        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
                        prnMsg(_('Se actualizo correctamente'),'success');
                    }else{
                        // si existe registro actualizamos el price
                         $sqlinsert = "INSERT INTO wrk_precioproveedor (
                            supplierid,
                            stockid,
                            price,
                            user
                        ) 
                         VALUES (
                            '" . $supplierid . "',
                            '" . $stockid . "',
                            '" . $price . "',
                            '" . $_SESSION['UserID'] . "'
                         )";
                        //echo $sqlinsert;
                        //exit;
                        $ErrMsg = _('Los productos') . '  ' . _('no pudieron ser insertados');
                        $DbgMsg = _('Se intento insertar los productos pero hubo un fallo inesperado');
                        //echo $sql;
                        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
                        prnMsg(_('Se agrego correctamente'),'success');
                    }
                    // listo
}
// Al hacer clic en editar registro
if(isset($_POST['editarprecioproveedor']))
{
    $UPDATE = true;
    $supplierid = $_POST['supplier'];
    $stockid = $_POST['stockid'];
    $price = $_POST['price'];
    $id = $_POST['id'];
    $trandatetime = date('Y-m-d H:i:s');
    $user = $_SESSION['UserID'];

    if($supplierid==''){

        prnMsg(_('El campo de proveedor se encuentra vacio, favor de intentarlo de nuevo '),'error');
        $insert = false;
    }

    if($stockid==''){

        prnMsg(_('El campo de producto se encuentra vacio, favor de intentarlo de nuevo '),'error');
        $insert = false;
    }

    if($price==''){

        prnMsg(_('El campo de precio se encuentra vacio, favor de intentarlo de nuevo '),'error');
        $insert = false;
    } 

    if($id==''){

        prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
        $insert = false;
    } 
    if($UPDATE)
    {
        $sqlinsert = "UPDATE wrk_precioproveedor 
            SET 
                supplierid='$supplierid',
                stockid='$stockid',
                price='$price',
                trandatetime='$trandatetime',
                user='$user'
            WHERE id = " .$id ; 

        $ErrMsg = _('El proveedor y/o el producto') . '  ' . _('no pudo ser agregado');
        $DbgMsg = _('Se intento insertar el proveedor y/o el producto pero hubo un fallo inesperado');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        prnMsg(_('Se actualizo correctamente'),'success');
        unset($_GET['action']);
    }
}

// Al hacer clic en eliminar registro
if(isset($_GET['action']) and $_GET['action']=='eliminar')
{
    $delete = true;
    $id = $_GET['id'];

    if($id==''){

        prnMsg(_('No se pudo eliminar el registro, vuelva a intentarlo '),'error');
        $delete = false;
    } 

    if($delete)
    {
        $user = $_SESSION['UserID'];
        $trandatetime = date('Y-m-d H:i:s');
        $sqlinsert = "DELETE FROM wrk_precioproveedor
        				WHERE id = ".$_GET['id']
        				;

        $ErrMsg = _('El registro') . ' ' . $id . ' ' . _('no se pudo eliminar');
        $DbgMsg = _('Se intento eliminar el registro pero hubo un fallo inesperado');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        prnMsg(_('Se elimino correctamente'),'success');
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


echo "<script>
     $( document ).ready(function() { 
            $('.select2').select2();
    });
</script>
<style>
.select2-container {
    margin: 0;
    position: relative;
    display: inline-block;
    zoom: 1;
    vertical-align: middle;
    min-width: 400px;
    margin-top: 15px;
}
</style>

";
if(!$_GET['action']=='agregarregistro')
{


echo "<CENTER><H1>Lista de Precios-Proveedores</H1></CENTER>";

	echo "
	<link rel='stylesheet' type='text/css' href='js/DataTables/datatables.min.css'>
    <script type='text/javascript' src='js/DataTables/datatables.min.js'></script>
    <script>
        $( document ).ready(function() {
          $('#registros').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'csv',
                    text: 'Exportar a Excel'
                },{
                    extend:'pageLength',
                    text:'Paginacion'
                }],
            aLengthMenu: [[10,15,25,50, -1], [10,15,25,50, 'All']],
            });
        });
    </script>";


	echo "
	<CENTER>
	<span id='datagrid'>
	<form method='GET'>
        <select id='filtroproveedor' name='filtroproveedor' onchange='this.form.submit()' class='select2'>
            <option value=''>TODOS LOS PROVEEDORES</option>";
            
                $sql = 'select * from suppliers 
                inner join wrk_precioproveedor on suppliers.supplierid = wrk_precioproveedor.supplierid
                group by suppliers.supplierid 
                order by suppname';
                $result_centrocosto = DB_query($sql,$db);
                while ($rowcentro = DB_fetch_array($result_centrocosto)) {
                    // hacemos los options
                    if($_GET['filtroproveedor']==$rowcentro['supplierid'])
                    {
                        echo '<option value="'.$rowcentro['supplierid'].'" selected>'.$rowcentro['suppname'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowcentro['supplierid'].'" >'.$rowcentro['suppname'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <select id='filtroproducto' name='filtroproducto' onchange='this.form.submit()' class='select2'>
            <option value=''>TODOS LOS PRODUCTOS</option>";
            
                $sql = 'select * from stockmaster 
                    inner join wrk_precioproveedor on stockmaster.stockid = wrk_precioproveedor.stockid
                    group by stockmaster.stockid 
                    order by description';
                $result_centrocosto = DB_query($sql,$db);
                while ($rowcentro = DB_fetch_array($result_centrocosto)) {
                    // hacemos los options
                    if($_GET['filtroproducto']==$rowcentro['stockid'])
                    {
                        echo '<option value="'.$rowcentro['stockid'].'" selected>'.$rowcentro['stockid'].' '.$rowcentro['description'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowcentro['stockid'].'" >'.$rowcentro['stockid'].' '.$rowcentro['description'].'</option>';
                    }
                }
                // termina
                echo "
                        
        </select>
	</form>
	<TABLE width='' id='registros' class='table table-bordered table-striped table-hover table-condensed'>
	<thead>
		<TR>
            <TD CLASS='tableheader' width='8%'># Proveedor</TD>
			<TD CLASS='tableheader' width='8%'>Proveedor</TD>
			<TD CLASS='tableheader' width='8%'># Producto</TD>
			<TD CLASS='tableheader' width='8%'>Descripcion</TD>
			<TD CLASS='tableheader' width='8%'>Precios</TD>
			<TD CLASS='tableheader' width='8%'>Dia de Transaccion</TD>
            <TD CLASS='tableheader' width='8%'>Acciones</TD>
            <TD CLASS='tableheader' width='8%'></TD>
		</TR>
	</thead>
	<tbody>";
	$sql = 'SELECT
                wpp.supplierid,
                wpp.stockid,
                wpp.price,
                wpp.trandatetime,
                sup.suppname,
                stm.description,
                wpp.id,
                wpp.supplierid
                from wrk_precioproveedor wpp
                INNER JOIN suppliers sup ON sup.supplierid=wpp.supplierid
                INNER JOIN stockmaster stm ON stm.stockid=wpp.stockid
                WHERE 1=1 
            ';
    /*filtroproveedor
    filtroproducto*/
	if(isset($_GET['filtroproveedor']) and $_GET['filtroproveedor']!='')
	{
		$sql .= ' AND wpp.supplierid = "'.$_GET['filtroproveedor'].'" ';
	}
    if(isset($_GET['filtroproducto']) and $_GET['filtroproducto']!='')
    {
        $sql .= ' AND wpp.stockid = "'.$_GET['filtroproducto'].'" ';
    }
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		
		echo "
		<TR>
            <TD align=center>".$myrow['supplierid']."</TD>
			<TD align=center>".$myrow['suppname']."</TD>
			<TD align=center>".$myrow['stockid']."</TD>
			<TD align=center>".$myrow['description']."</TD>
			<TD align=center> $ ".number_format($myrow['price'],2)."</TD>
			<TD align=center>".$myrow['trandatetime']."</TD>
            <TD align=right><a href='REQ_supprice.php?id=".$myrow['id']."&action=editar' >"._('Edit')."</a>
            </TD>";
            ?>
            <TD align=right><a href='REQ_supprice.php?id=<?php echo $myrow['id'] ?>&action=eliminar' onclick='return confirm("¿Esta Seguro de Eliminarlo?")' >Eliminar</a></TD>
            <?php echo "
		</TR>
		";
	}

	echo "</tbody></TABLE>
	</span>
	</CENTER>";


		// SE AGREGA UN NUEVO REGISTRO
	echo "
	<CENTER>
    	<hr>
    	<h3>Subir Archivo</h3>
    	<FORM NAME='altaarchivo' method='POST' enctype='multipart/form-data'>
        	<TABLE BORDER=0  width='45%'>
            	<TR>
                	<TD>Archivo a Importar:</td>
                    <TD> <input type='file' name='im' /></TD>
                </tr>
                <tr>
                    <td>stockid|supplierid|price</td>
                	<TD>
                	<input type=submit name='loadFile' value='Cargar archivo'>
                	</TD>
            	</TR>
        	</TABLE>
        </FORM>
	</CENTER>

	<br>
	";
}elseif($_GET['action']=='agregarregistro')
    {
        // SE AGREGA UN NUEVO REGISTRO
    
    echo "
    <CENTER>
        <hr>
        <h3>Alta de Registros</h3>
            <FORM NAME='altaprecioproveedor' method='POST'>
                <TABLE BORDER=1  width='45%'>
                    <TR>
                        <TD>
                            "._('Proveedor').":
                        </TD>
                        <TD>
                            <select name='supplierid' id='supplierid' class='select2'>
                                <option value=''>-- Seleccione --</option>
                                    ";
                                // hacemos una consulta para obtener todo de la tabla de suppliers
                                $sqls_proveedortabla = 'SELECT * FROM suppliers
                                        where factorcompanyid = 1';
                                $result_proveedortabla = DB_query($sqls_proveedortabla,$db);
                                while ($rowproveedortabla = DB_fetch_array($result_proveedortabla)) {
                                    // hacemos los options
                                    echo '<option value="'.$rowproveedortabla['supplierid'].'">'.$rowproveedortabla['supplierid'].' '.$rowproveedortabla['suppname'].'</option>';
                                }
                                // termina
                                echo "
                            </select>
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            "._('Producto').":
                        </TD>
                        <TD>
                        	<select name='stockid' id='stockid' class='select2'>
                                <option value=''>-- Seleccione --</option>
                                    ";
                                // hacemos una consulta para obtener todo de la tabla de stockmaster
                                $sqls_productotabla = 'SELECT *  FROM stockmaster';
                                $result_productotabla = DB_query($sqls_productotabla,$db);

                                while ($rowproductotabla = DB_fetch_array($result_productotabla)) {
                                    // hacemos los options
                                    echo '<option value="'.$rowproductotabla['stockid'].'">'.$rowproductotabla['stockid'].' '.$rowproductotabla['description'].'</option>';
                                }
                                // termina
                                echo "
                            </select>
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            "._('Precio').":
                        </TD>
                        <TD>
                            <INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='price'>
                        </TD>
                    </TR>
                </TABLE>
            <INPUT TYPE='submit' VALUE="._('Agregar')." NAME='altaprecioproveedor'>
            </FORM>
    </CENTER>";

    }
    else if ($_GET['action']=='editar')
    {

    // Opcion para editar un registro
    $id = $_GET['id'];

    if($id=='')
    {
        prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
        $delete = false;
    }

    $sqleditar = 'SELECT * FROM wrk_precioproveedor WHERE id = '.$id;
    $result = DB_query($sqleditar,$db);
    $datos = DB_fetch_array($result);
    echo "
    <CENTER>
    <hr>
        <h3>Editar Registro</h3>
        <FORM NAME='editarprecioproveedor' method='POST'>
            <TABLE BORDER=1  width='45%'>
                <TR>
                    <TD>
                        "._('Proveedor').":
                    </TD>
                    <TD>

                        <select name='supplier' id='supplierid' class='select2'>
                            <option value=''>-- Seleccione --</option>
                            ";	//CONSULTA PARA OBTENER EL REGISTRO A EDITAR
                            	$sqls_relacion = 'SELECT * FROM wrk_precioproveedor
                                       where id = '.$_GET["id"];
								$resultadowrk = DB_query($sqls_relacion,$db);
								$datoseditar = DB_fetch_array($resultadowrk);

                                // hacemos una consulta para obtener todo de la tabla de suppliers
                                $sqls_proveedortabla = 'SELECT * FROM suppliers
                                        where factorcompanyid = 1 ORDER BY supplierid';
                                $result_proveedortabla = DB_query($sqls_proveedortabla,$db);
                                while ($rowproveedortabla = DB_fetch_array($result_proveedortabla)) {
                                
                                // hacemos los options
                                    if($datoseditar['supplierid']==$rowproveedortabla['supplierid'])
                                    {
                                        echo '<option value="'.$rowproveedortabla['supplierid'].'" selected>'.$rowproveedortabla['supplierid'].' '.$rowproveedortabla['suppname'].'</option>';
                                    }else
                                    {
                                       echo '<option value="'.$rowproveedortabla['supplierid'].'" >'.$rowproveedortabla['supplierid'].' '.$rowproveedortabla['suppname'].'</option>';
                                    }
                                }
                            // termina
                            echo "
                        </select>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        "._('Producto').":
                    </TD>
                    <TD>
                        <select name='stockid' id='stockid' class='select2'>
                            <option value=''>-- Seleccione --</option>
                            ";
                                // hacemos una consulta para obtener todo de la tabla de stockmaster
                                $sqls_productotabla = 'SELECT * FROM stockmaster';
                                $result_productotabla = DB_query($sqls_productotabla,$db);
                                while ($rowproductotabla = DB_fetch_array($result_productotabla)) {
                                // hacemos los options
                                    if($datoseditar['stockid']==$rowproductotabla['stockid'])
                                    {
                                        echo '<option value="'.$rowproductotabla['stockid'].'" selected>'.$rowproductotabla['stockid'].' '.$rowproductotabla['description'].'</option>';
                                    }else
                                    {
                                       echo '<option value="'.$rowproductotabla['stockid'].'" >'.$rowproductotabla['stockid'].' '.$rowproductotabla['description'].'</option>';
                                    }
                                }
                            // termina
                            echo "
                        </select>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        "._('Precio').":
                    </TD>
                    <TD>
                        <INPUT TYPE='text' style='width:75%;' MAXLENGTH=600 NAME='price' value='".$datoseditar['price']."'>
                    </TD>
                </TR>
            </TABLE>
            <INPUT TYPE='hidden' VALUE='".$datoseditar['id']."' NAME='id'>
            <INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='editarprecioproveedor'>
        </FORM>
    </CENTER>
        ";
    }

    echo "
        <CENTER>
            <a href='REQ_supprice.php'>Lista de registros</a>|<a href='REQ_supprice.php?action=agregarregistro'>Agregar Nuevo Registro manual</a>
        </CENTER>";

include('includes/footer.inc');
?>