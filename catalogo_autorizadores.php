<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 25/Nov/2016
* Archivo creado para el catalogo de Categorias
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;

include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Catalogo de Autorizadores');
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

/* campos de la tabla
wrk_autorizadorescc
- autorizadorcc_id
- autorizadorcc
- centrocosto_id
- usuario
- fechaultimomov
- autorizadorcc_edo
*/

// Al hacer clic en grabar un nuevo autorizador
if(isset($_POST['guardarautorizador']))
{
    /*print_r($_POST);
    exit;*/
    $insert = true;
    // CAMPOS PARA INSERTAR
    $autorizadorcc = $_POST['autorizadorcc'];
    $centrocosto_id = $_POST['centrocosto_id'];
    $usuario = $_SESSION['UserID'];
    $fechaultimomov = date('Y-m-d H:i:s');
    $autorizadorcc_edo = 1;
    
    if($autorizadorcc==''){

        prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
        $insert = false;
    } 
    if($centrocosto_id==''){

        prnMsg(_('El campo se encuentra vacio, favor de intentarlo de nuevo '),'error');
        $insert = false;
    } 


    if($insert)
    {
        $sqlinsert = "INSERT INTO wrk_autorizadorescc (
            autorizadorcc,
            centrocosto_id,
            usuario,
            fechaultimomov,
            autorizadorcc_edo
        )
         VALUES (
                '" . $autorizadorcc . "',
                '" . $centrocosto_id . "',
                '" . $usuario . "',
                '" . $fechaultimomov . "',
                '" . $autorizadorcc_edo . "'
        )";

        $ErrMsg = _('El autorizador') . '  ' . _('no pudo ser ingresado debido');
        $DbgMsg = _('Se intento insertar el nuevo autorizador pero hubo un fallo');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        $autorizadorcc_id = DB_Last_Insert_ID($db,'wrk_autorizadorescc','autorizadorcc_id');
        // INSERTAMOS EN SOLICITANTES
        $sqlinsert = "INSERT INTO wrk_solicitantecc (
            autorizadorcc_id,
            solicitantecc,
            centrocosto_id,
            usuario,
            fechaultimomov,
            solicitantecc_edo
        )
         VALUES (
                '" . $autorizadorcc_id . "',
                '" . $autorizadorcc . "',
                '" . $centrocosto_id . "',
                '" . $usuario . "',
                '" . $fechaultimomov . "',
                '" . $autorizadorcc_edo . "'
        )";
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        // TERMINA LA INSERCION
        prnMsg(_('Se agrego correctamente'),'success');
        unset($_GET['action']);
    }
}
// Al hacer clic en grabar autorizador
if(isset($_POST['actualizarautorizador']))
{
    $UPDATE = true;
    $autorizadorcc = $_POST['autorizadorcc'];
    $centrocosto_id = $_POST['centrocosto_id'];
    $usuario = $_SESSION['UserID'];
    $fechaultimomov = date('Y-m-d H:i:s');
    $autorizadorcc_edo = 1;

    if($autorizadorcc==''){

        prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
        $insert = false;
    } 

    if($centrocosto_id==''){

        prnMsg(_('El campo esta vacio, vuelva a intentarlo por favor'),'error');
        $insert = false;
    } 


$id = $_POST['id'];

    if($id==''){

        prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
        $insert = false;
    }   
    if($UPDATE)
    {
        $sqlinsert = "UPDATE wrk_autorizadorescc 
            SET 
                autorizadorcc='$autorizadorcc',
                centrocosto_id='$centrocosto_id',
                usuario='$usuario',
                fechaultimomov='$fechaultimomov',
                autorizadorcc_edo='$autorizadorcc_edo' 
            WHERE autorizadorcc_id = " .$id ; 

        $ErrMsg = _('El autorizador') . '  ' . _('no pudo ser ingresado debido');
        $DbgMsg = _('Se intento insertar el nuevo autorizador pero fallo');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        /*
            autorizadorcc_anterior
            centrocosto_anterior
        */
        $autorizadorcc_anterior = $_POST['autorizadorcc_anterior'];
        $centrocosto_anterior = $_POST['centrocosto_anterior'];
        // ACTUALIZAMOS
        $sqlinsert = "UPDATE wrk_solicitantecc 
            SET 
                autorizadorcc_id='$id',
                solicitantecc='$autorizadorcc',
                centrocosto_id='$centrocosto_id',
                usuario='$usuario',
                fechaultimomov='$fechaultimomov',
                solicitantecc_edo='$autorizadorcc_edo' 
            WHERE solicitantecc = '".$autorizadorcc_anterior."' and centrocosto_id = '".$centrocosto_anterior."' "; 
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);  
        // TERMINA ACTUALIZACION

        prnMsg(_('Se inserto correctamente'),'success');
        unset($_GET['action']);
    }
}

// Al hacer clic en eliminar
if(isset($_GET['action']) and $_GET['action']=='eliminar')
{
    $delete = true;
    $id = $_GET['id'];
    $usuario = $_SESSION['UserID']; 
    $fechaultimomov = date('Y-m-d H:i:s');

    if($id==''){

        prnMsg(_('Seleccione un registro, vuelva a intentarlo '),'error');
        $delete = false;
    } 

    if($delete)
    {
        $usuario = $_SESSION['UserID'];
        $fechaultimomov = date('Y-m-d H:i:s');
        $sqlinsert = "UPDATE wrk_autorizadorescc SET autorizadorcc_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE autorizadorcc_id = " . $id;

        $ErrMsg = _('El autorizador') . ' ' . $id . ' ' . _('no se pudo eliminar');
        $DbgMsg = _('Se intento eliminar el autorizador pero hubo un fallo');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        // ELIMINAMOS - SOLICITANTE
        /*
            autorizadorcc
            centrocosto
        */
        $autorizadorcc = $_GET['autorizadorcc'];
        $centrocosto = $_GET['centrocosto'];
        $sqlinsert = "UPDATE wrk_solicitantecc SET solicitantecc_edo = 0,usuario='$usuario',fechaultimomov='$fechaultimomov' WHERE solicitantecc = '$autorizadorcc' and centrocosto_id='$centrocosto' ";
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);
        // ELIMINAMOS -  SOLICITANTE
        prnMsg(_('Se elimino correctamente'),'success');
    }
}





echo "<CENTER><H1>Cat&aacute;logo de Autorizadores</H1></CENTER>";

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
    <form method='POST'>
    </form>
    <TABLE width='' id='registros' class='table table-bordered table-striped table-hover table-condensed'>
    <thead>
    <TR>
        <TD CLASS='tableheader' width='8%' >ID</TD>
        <TD CLASS='tableheader' >Autorizadores</TD>
        <TD CLASS='tableheader' width='20%'>Prefijo Contable</TD>
        <TD CLASS='tableheader' width='8%'>Acciones</TD>
        <TD CLASS='tableheader' width='8%'></TD>
    </TR>
    </thead>
    <tbody>";


    $sql = 'SELECT acc.*,cc.centrocosto centrodecosto_nombre,cc.subfijo centrodecosto_subfijo,us.realname FROM wrk_autorizadorescc acc
            INNER JOIN wrk_centrocosto  cc on cc.centrocosto_id=acc.centrocosto_id
            INNER JOIN www_users  us on us.userid=acc.autorizadorcc
            where autorizadorcc_edo = 1';

    if(isset($_POST['search']) and $_POST['search']!=NULL)
    {
        $sql .= ' AND CONCAT_WS (" ", us.realname,cc.subfijo,cc.centrocosto) LIKE "%'.$_POST['search'].'%" ';
    }
    $result = DB_query($sql,$db);
    $k=0;
    while ($myrow = DB_fetch_array($result)) {
        
        echo "<TR>
        <TD align=right>".$myrow['autorizadorcc_id']."</TD>
        <TD>".$myrow['realname']."</TD>
        <TD>".$myrow['centrodecosto_subfijo']." ".$myrow['centrodecosto_nombre']."</TD>
        <TD align=right><a href='catalogo_autorizadores.php?id=".$myrow['autorizadorcc_id']."&action=editar' >"._('Edit')."</a></TD>";
        ?>
        <TD align=right><a href='catalogo_autorizadores.php?id=<?php echo $myrow['autorizadorcc_id'] ?>&action=eliminar&autorizadorcc=<?php echo $myrow['autorizadorcc'] ?>&centrocosto=<?php echo $myrow['centrocosto_id'] ?>' onclick='return confirm("¿Estas Seguro de Eliminarlo?")' >Eliminar</a></TD>
        <?php echo "
        </TR>";
    }

    

    echo "</tbody></TABLE>
    </span>
    </CENTER>";

    if($_GET['action']!='editar')
    {
        // SE AGREGA UN NUEVO AUTORIZADOR
    
    echo "
    <CENTER>

    <hr>
        <h3>Alta de Autorizadores</h3>
            <FORM NAME='altaautorizador' method='POST'>
                <TABLE BORDER=1  width='45%'>
                    <TR>
                        <TD>
                            "._('Autorizadores').":
                        </TD>
                        <TD>
                            <select name='autorizadorcc' class='select2'>
                                <option value=''>-- Seleccione --</option>
                        ";
                                // hacemos una consulta para obtener los campos userid,realname de la tabla de www_users
                                $sql_usuarios = 'SELECT userid,realname from www_users where blocked = 0 order by realname';
                                $result_usuarios = DB_query($sql_usuarios,$db);
                                while ($rowcentro = DB_fetch_array($result_usuarios)) {
                                    // hacemos los options
                                    echo '<option value="'.$rowcentro['userid'].'">'.$rowcentro['realname'].'</option>';
                                }
                                // termina
                                echo "
                            </select>
                        </TD>
                    </TR>
                    <TR>
                        <TD>
                            "._('Centro de Costo').":
                        </TD>
                        <TD>
                            <select name='centrocosto_id' class='select2'>
                                <option value=''>-- Seleccione --</option>
                        ";
                                // hacemos una consulta para obtener todo de la tabla de centro de costo
                                $sql_centrocosto = 'SELECT * from wrk_centrocosto where centrocosto_id not in (
                                    select centrocosto_id from wrk_autorizadorescc where autorizadorcc_edo = 1) 
                                    and centrocosto_edo = 1';
                                $result_centrocosto = DB_query($sql_centrocosto,$db);
                                while ($rowcentro = DB_fetch_array($result_centrocosto)) {
                                    // hacemos los options
                                    echo '<option value="'.$rowcentro['centrocosto_id'].'">'.$rowcentro['centrocosto'].'</option>';
                                }
                                // termina
                                echo "
                            </select>
                        </TD>
                    </TR>
                </TABLE>

            <INPUT TYPE='submit' VALUE="._('Accept')." NAME='guardarautorizador'>
            <INPUT TYPE='reset' VALUE="._('Cancel').">
        </FORM>
    </CENTER>";
    }
    else if ($_GET['action']=='editar')
    {
    // Opcion para editar un autorizador
    $id = $_GET['id'];

    if($id=='')
    {
        prnMsg(_('Seleccione un registro y vuelva a intentarlo '),'error');
        $delete = false;
    }

    $sqleditar = 'SELECT * FROM wrk_autorizadorescc WHERE autorizadorcc_id = '.$id;
    $result = DB_query($sqleditar,$db);
    $datos = DB_fetch_array($result);
    echo "
    <hr>
    <CENTER>
        <h3>Editar Autorizador</h3>
        <FORM NAME='editarautorizador' method='POST'>
            <TABLE BORDER=1  width='45%'>
                <TR>
                    <TD>
                            "._('Autorizadores').":
                        </TD>
                        <TD>
                            <select name='autorizadorcc' class='select2'>
                                <option value=''>-- Seleccione --</option>
                            ";
                                // hacemos una consulta para obtener los campos userid,realname de la tabla de www_users
                                $sql_usuarios = 'SELECT userid,realname from www_users where blocked = 0 order by realname';
                                $result_usuarios = DB_query($sql_usuarios,$db);
                                while ($rowus = DB_fetch_array($result_usuarios)) {
                                // hacemos los options
                                if($datos['autorizadorcc']==$rowus['userid'])
                                {   
                                echo '<option value="'.$rowus['userid'].'" selected>'.$rowus['realname'].'</option>';
                                }else{
                                echo '<option value="'.$rowus['userid'].'" >'.$rowus['realname'].'</option>';
                                }
                            }
                            // termina
                            echo "
                        </select>
                    </TD>
                </TR>
                <TR>
                    <TD>
                        "._('Centro de Costo').":</TD>
                    <TD>
                        <select name='centrocosto_id' class='select2'>
                            <option value=''>-- Seleccione --</option>
                    ";
                // hacemos una consulta para obtener todo  de la tabla de centro de costo
                $sql_centrocosto = 'SELECT * from wrk_centrocosto where centrocosto_id not in (     select centrocosto_id from wrk_autorizadorescc where autorizadorcc_edo = 1 and centrocosto_id!='.$datos['centrocosto_id'].') and centrocosto_edo = 1';
                $result_centrocosto = DB_query($sql_centrocosto,$db);
                while ($rowcentro = DB_fetch_array($result_centrocosto)) {
                    // hacemos los options
                    if($datos['centrocosto_id']==$rowcentro['centrocosto_id'])
                    {
                        echo '<option value="'.$rowcentro['centrocosto_id'].'" selected>'.$rowcentro['subfijo'].' '.$rowcentro['centrocosto'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowcentro['centrocosto_id'].'" >'.$rowcentro['subfijo'].' '.$rowcentro['centrocosto'].'</option>';
                    }
                }
                // termina
                echo "
                        </select>
                    </TD>
                </TR>
            </TABLE>
            <INPUT TYPE='hidden' VALUE='".$datos['autorizadorcc_id']."' NAME='id'>
            <INPUT TYPE='hidden' VALUE='".$datos['autorizadorcc']."' NAME='autorizadorcc_anterior'>
            <INPUT TYPE='hidden' VALUE='".$datos['centrocosto_id']."' NAME='centrocosto_anterior'>
            <INPUT TYPE='submit' VALUE="._('Guardar Cambios')." NAME='actualizarautorizador'>
        </FORM>
        <br>
        
    </CENTER>
        ";

    
    }
    echo "<center><a href='catalogo_autorizadores.php'>Agregar Nuevo Autorizador</a></center>";

include('includes/footer.inc');
?>
