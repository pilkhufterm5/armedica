<?php
/* $Revision: 1 $ */

/**************************************************************************
* Ruben Flores Barrios 13/Dic/2016
* Archivo creado para mostrar las requisiciones pendientes
***************************************************************************/
//Seguridad de la pagina
//$PageSecurity = 1;
?>

<style>
<!--
    table.selection tbody tr:nth-child(2n+3){
        
        background-color: #CCCCCC;
    }
    table.selection tbody tr{
        background-color: #EEEEEE;
    }
    @media print{
        .no_print{
            display:none;
        }
    }
-->
</style>

<?php
include('includes/session.inc');
//Titulo de nuestro explorador
$title = _('Requisiciones Pendientes');


echo '<script language="JavaScript" src="CalendarPopup.js"></script>';
?>
<script language="JavaScript">
var cal = new CalendarPopup();
</script>
<?php


include('includes/header.inc');

echo "<CENTER><H1>Busqueda de Requisiciones</H1></CENTER>";

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
            $('.select2').select2();
        });
    </script>";

    // VERIFICAMOS SI EL USUARIO ES ADMINISTRADOR DE COMPRAS PARA DARLE ACCESO TOTAL 
    $sqlacceso = 'SELECT or_compras FROM www_users where userid ="'.$_SESSION['UserID'].'"';
    $resultacceso = DB_query($sqlacceso,$db);
    $rowacceso = DB_fetch_array($resultacceso);
    if($rowacceso['or_compras']==1)
    {
        // PUEDE VER LO DE TODOS
         $sql_autorizador = 'select * from wrk_autorizadorescc 
                    inner join wrk_requisicion on wrk_requisicion.autorizadorcc_id = wrk_autorizadorescc.autorizadorcc_id
                    where autorizadorcc_edo = 1
                    group by wrk_autorizadorescc.autorizadorcc_id 
                    order by autorizadorcc';
        $sql_solicitante = 'select * from wrk_solicitantecc
                    inner join wrk_requisicion on wrk_requisicion.solicitantecc_id = wrk_solicitantecc.solicitantecc_id 
                    where solicitantecc_edo = 1
                    group by wrk_solicitantecc.solicitantecc_id 
                    order by solicitantecc';
         $sql_centrocosto = 'select * from wrk_centrocosto 
                    inner join wrk_requisicion on wrk_requisicion.centrocosto_id = wrk_centrocosto.centrocosto_id  
                    where centrocosto_edo = 1
                    group by wrk_centrocosto.centrocosto_id 
                    order by centrocosto';
         $sql_subcentrocosto = 'SELECT
                                            *,
                                            wrk_centrocosto.centrocosto
                                        FROM
                                            wrk_subcentrocosto
                                        INNER JOIN wrk_requisicion ON wrk_requisicion.subcentrocosto_id = wrk_subcentrocosto.subcentrocosto_id
                                        INNER JOIN wrk_centrocosto ON wrk_centrocosto.centrocosto_id = wrk_subcentrocosto.centrocosto_id
                                        WHERE
                                            subcentrocosto_edo = 1
                                        GROUP BY
                                            wrk_subcentrocosto.subcentrocosto_id
                                        ORDER BY
                                            subcentrocosto';
        $todos = '<option value="">-- TODOS --</option>';
    }else{
        // SOLO PUEDE VER LO SUYO
        $sql_autorizador = 'select * from wrk_autorizadorescc 
                    inner join wrk_requisicion on wrk_requisicion.autorizadorcc_id = wrk_autorizadorescc.autorizadorcc_id
                    where autorizadorcc_edo = 1 AND autorizadorcc = "'.$_SESSION['UserID'].'"
                    group by wrk_autorizadorescc.autorizadorcc_id 
                    order by autorizadorcc';
         //echo $sql_autorizador;
        $sql_solicitante = 'select * from wrk_solicitantecc
                    inner join wrk_requisicion on wrk_requisicion.solicitantecc_id = wrk_solicitantecc.solicitantecc_id 
                    where solicitantecc_edo = 1 AND solicitantecc = "'.$_SESSION['UserID'].'"
                    group by wrk_solicitantecc.solicitantecc_id 
                    order by solicitantecc';
         $sql_centrocosto = 'select * from wrk_centrocosto 
                    inner join wrk_requisicion on wrk_requisicion.centrocosto_id = wrk_centrocosto.centrocosto_id 
                    inner join wrk_autorizadorescc on wrk_requisicion.autorizadorcc_id = wrk_autorizadorescc.autorizadorcc_id 
                    where centrocosto_edo = 1 AND wrk_autorizadorescc.autorizadorcc = "'.$_SESSION['UserID'].'"
                    group by wrk_centrocosto.centrocosto_id 
                    order by centrocosto';
         $sql_subcentrocosto = 'SELECT
                                            *,
                                            wrk_centrocosto.centrocosto
                                        FROM
                                            wrk_subcentrocosto
                                        INNER JOIN wrk_requisicion ON wrk_requisicion.subcentrocosto_id = wrk_subcentrocosto.subcentrocosto_id
                                        INNER JOIN wrk_centrocosto ON wrk_centrocosto.centrocosto_id = wrk_subcentrocosto.centrocosto_id
                                        inner join wrk_autorizadorescc on wrk_centrocosto.centrocosto_id = wrk_autorizadorescc.autorizadorcc_id 
                                        WHERE
                                            subcentrocosto_edo = 1  AND wrk_autorizadorescc.autorizadorcc = "'.$_SESSION['UserID'].'"
                                        GROUP BY
                                            wrk_subcentrocosto.subcentrocosto_id
                                        ORDER BY
                                            subcentrocosto';
        $todos = '<option value="99999">-- TODOS --</option>';
    }
    // termina

    echo "
    <CENTER>
    <span id='datagrid'>
    <form method='GET' name='filtrarrequisicion' id='filtrarrequisicion'>";
    if($rowacceso['or_compras']==1)
    {
        echo "
        <select id='filtroautorizador' name='filtroautorizador' onchange='this.form.submit()' class='select2'>
            '. $todos.'";

                $result_autorizador = DB_query($sql_autorizador,$db);


                while ($rowautorizador = DB_fetch_array($result_autorizador)) {
                    // hacemos los options
                    if($_GET['filtroautorizador']==$rowautorizador['autorizadorcc_id'])
                    {
                        echo '<option value="'.$rowautorizador['autorizadorcc_id'].'" selected>'.$rowautorizador['autorizadorcc'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowautorizador['autorizadorcc_id'].'" >'.$rowautorizador['autorizadorcc'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <select id='filtrosolicitante' name='filtrosolicitante' onchange='this.form.submit()' class='select2'>
             '. $todos.'";
                
                $result_solicitante = DB_query($sql_solicitante,$db);
                while ($rowsolicitante = DB_fetch_array($result_solicitante)) {
                    // hacemos los options
                    if($_GET['filtrosolicitante']==$rowsolicitante['solicitantecc_id'])
                    {
                        echo '<option value="'.$rowsolicitante['solicitantecc_id'].'" selected>'.$rowsolicitante['solicitantecc'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowsolicitante['solicitantecc_id'].'" >'.$rowsolicitante['solicitantecc'].'</option>';
                    }
                }
                // termina
                echo "      
        </select>
        <select id='filtrocategoria' name='filtrocategoria' onchange='this.form.submit()' class='select2'>
            <option value=''>TODAS LAS CATEGORIAS</option>";
                $sql_categoria = 'select * from wrk_categoriascc
                    inner join wrk_requisicion on wrk_requisicion.categoriacc_id = wrk_categoriascc.categoriacc_id  
                    where categoriacc_edo = 1
                    group by wrk_categoriascc.categoriacc_id 
                    order by categoriacc';
                $result_categoria = DB_query($sql_categoria,$db);
                while ($rowcategoria = DB_fetch_array($result_categoria)) {
                    // hacemos los options
                    if($_GET['filtrocategoria']==$rowcategoria['categoriacc_id'])
                    {
                        echo '<option value="'.$rowcategoria['categoriacc_id'].'" selected>'.$rowcategoria['categoriacc'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowcategoria['categoriacc_id'].'" >'.$rowcategoria['categoriacc'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <select id='filtrocentrocosto' name='filtrocentrocosto' onchange='this.form.submit()' class='select2'>
             '. $todos.'";
                $result_centrocosto = DB_query($sql_centrocosto,$db);
                while ($rowcentrocosto = DB_fetch_array($result_centrocosto)) {
                    // hacemos los options
                    if($_GET['filtrocentrocosto']==$rowcentrocosto['centrocosto_id'])
                    {
                        echo '<option value="'.$rowcentrocosto['centrocosto_id'].'" selected>'.$rowcentrocosto['centrocosto'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowcentrocosto['centrocosto_id'].'" >'.$rowcentrocosto['centrocosto'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <br>
        <select id='filtrosubcentrocosto' name='filtrosubcentrocosto' onchange='this.form.submit()' class='select2'>
             '. $todos.'";
               
                $result_subcentrocosto = DB_query($sql_subcentrocosto,$db);
                while ($rowsubcentrocosto = DB_fetch_array($result_subcentrocosto)) {
                    // hacemos los options
                    if($_GET['filtrosubcentrocosto']==$rowsubcentrocosto['subcentrocosto_id'])
                    {
                        echo '<option value="'.$rowsubcentrocosto['subcentrocosto_id'].'" selected>'.$rowsubcentrocosto['centrocosto'].' - '.$rowsubcentrocosto['subcentrocosto'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowsubcentrocosto['subcentrocosto_id'].'" >'.$rowsubcentrocosto['centrocosto'].' - '.$rowsubcentrocosto['subcentrocosto'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <select id='filtroestatus' name='filtroestatus' onchange='this.form.submit()' class='select2'>
            <option value=''>TODOS LOS ESTATUS</option>";
                $sql_estatus = 'select * from wrk_requisicion 
                group by wrk_requisicion.status 
                order by status';
                $result_estatus = DB_query($sql_estatus,$db);
                while ($rowestatus = DB_fetch_array($result_estatus)) {
                    // hacemos los options
                    if($_GET['filtroestatus']==$rowestatus['status'])
                    {
                        echo '<option value="'.$rowestatus['status'].'" selected>'.$rowestatus['status'].'</option>';
                    }else
                    {
                        echo '<option value="'.$rowestatus['status'].'" >'.$rowestatus['status'].'</option>';
                    }
                }
                // termina
                echo "   
        </select>
        <br>";
    }else{
        // solicitante y autorizador solo sus listas

    }
    if(isset($_GET['fecha_hasta']) and $_GET['fecha_hasta']!='' && isset($_GET['fecha_desde']) and $_GET['fecha_desde']!='')
    {
        $fecha_desde = $_GET['fecha_desde'];
        $fecha_hasta = $_GET['fecha_hasta'];
    }else{
        $fecha_desde = date('d/m/Y');
        $fecha_hasta = date('d/m/Y');
    }
    echo "
        <INPUT type=text name='fecha_desde' MAXLENGTH =10 SIZE=8 value=" . $fecha_desde . " id='fecha_desde'>
        <a href=\"#\" onclick=\"filtrarrequisicion.fecha_desde.value='';cal.select(
        document.forms['filtrarrequisicion'].fecha_desde,'from_date_anchor','d/M/yyyy');
        return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
        <img src='img/cal.gif' width='16' height='16' border='0' alt='Click para escoger la 1ra fecha'></a>

        <INPUT type=text name='fecha_hasta' MAXLENGTH =10 SIZE=8 value=" .$fecha_hasta . " value='fecha_hasta'>
        <a href=\"#\" onclick=\"filtrarrequisicion.fecha_hasta.value='';cal.select(
        document.forms['filtrarrequisicion'].fecha_hasta,'from_date_anchor2','d/M/yyyy');
        return false;\" name=\"from_date_anchor\" id=\"from_date_anchor2\">
        <img src='img/cal.gif' width='16' height='16' border='0' alt='Click para escoger la 2da fecha'></a><br>
        <input type='submit' value='Filtrar resultados' name='search'>
    </form>";


    if(isset($_GET['fecha_desde']))
    {
        echo "
        <TABLE width='' id='registros' class='table table-bordered table-striped table-hover table-condensed'>
        <thead>
            <TR>
                <TD CLASS='tableheader' width='8%'># Req</TD>
                <TD CLASS='tableheader' width='8%'>Fecha Creada</TD>
                <TD CLASS='tableheader' width='8%'>Fecha Req</TD>
                <TD CLASS='tableheader' width='8%'>Autorizador</TD>
                <TD CLASS='tableheader' width='8%'>Solicitante</TD>
                <TD CLASS='tableheader' width='8%'>Categoria</TD>
                <TD CLASS='tableheader' width='8%'>Centro de Costo</TD>
                <TD CLASS='tableheader' width='8%'>Sub Centro de Costo</TD>
                <TD CLASS='tableheader' width='8%'>Estatus</TD>
                <TD CLASS='tableheader' width='8%'>Opciones</TD>
            </TR>
        </thead>
        <tbody>";

        $usuario=$_SESSION['UserID'];
        $sql_usuario = "SELECT wrkr.*,
                        wrkr.reqid,
                        wrkr.trandatetime,
                        wrkr.reqdate,
                        wrka.autorizadorcc as autorizadorbase,
                        wrks.solicitantecc as solicitantebase,
                        wrkc.categoriacc as categoriabase,
                        wrkcc.centrocosto as centrocostobase,
                        wrkscc.subcentrocosto as subcentrocostobase,
                        wrkr.status
                FROM wrk_requisicion wrkr
                INNER JOIN wrk_autorizadorescc wrka ON wrka.autorizadorcc_id=wrkr.autorizadorcc_id
                INNER JOIN wrk_solicitantecc wrks ON wrks.solicitantecc_id=wrkr.solicitantecc_id
                INNER JOIN wrk_categoriascc wrkc ON wrkc.categoriacc_id=wrkr.categoriacc_id
                INNER JOIN wrk_centrocosto wrkcc ON wrkcc.centrocosto_id=wrkr.centrocosto_id
                INNER JOIN wrk_subcentrocosto wrkscc ON wrkscc.subcentrocosto_id=wrkr.subcentrocosto_id
                WHERE 1=1";

        /*
        if(isset($_POST['search']) and $_POST['search']!=NULL)
        {
            $sql_usuario .= ' AND CONCAT_WS (" ", autorizadorbase, solicitantebase, categoriabase, centrocostobase) LIKE "%'.$_POST['search'].'%" ';
        }
        */

        if($rowacceso['or_compras']==1)
        {

            if(isset($_GET['filtroautorizador']) and $_GET['filtroautorizador']!='')
            {
                $sql_usuario .= ' AND wrkr.autorizadorcc_id = "'.$_GET['filtroautorizador'].'" ';
            }
            if(isset($_GET['filtrosolicitante']) and $_GET['filtrosolicitante']!='' and $_GET['filtrosolicitante']!='99999')
            {
                $sql_usuario .= ' AND wrkr.solicitantecc_id = "'.$_GET['filtrosolicitante'].'" ';
            }
            if(isset($_GET['filtrocategoria']) and $_GET['filtrocategoria']!='')
            {
                $sql_usuario .= ' AND wrkr.categoriacc_id = "'.$_GET['filtrocategoria'].'" ';
            }
            if(isset($_GET['filtrocentrocosto']) and $_GET['filtrocentrocosto']!='' and $_GET['filtrocentrocosto']!='99999')
            {
                $sql_usuario .= ' AND wrkr.centrocosto_id = "'.$_GET['filtrocentrocosto'].'" ';
            }
            if(isset($_GET['filtrosubcentrocosto']) and $_GET['filtrosubcentrocosto']!='' and $_GET['filtrosubcentrocosto']!='99999')
            {
                $sql_usuario .= ' AND wrkr.subcentrocosto_id = "'.$_GET['filtrosubcentrocosto'].'" ';
            }
            
            if(isset($_GET['filtroestatus']) and $_GET['filtroestatus']!='')
            {
                $sql_usuario .= ' AND wrkr.status = "'.$_GET['filtroestatus'].'" ';
            }
        }else{
            $sql_usuario .= ' AND CONCAT_WS(" ",wrka.autorizadorcc,wrks.solicitantecc) LIKE  "%'.$_SESSION['UserID'].'%" ';
            
        }
        
        if(isset($_GET['fecha_hasta']) and $_GET['fecha_hasta']!='' && isset($_GET['fecha_desde']) and $_GET['fecha_desde']!='')
        {
            $fecha_desde = $_GET['fecha_desde'];
            $fecha_desde = str_replace('/', '-', $fecha_desde);
            $fecha_desde = date('Y-m-d', strtotime($fecha_desde));
            $fecha_hasta = $_GET['fecha_hasta'];
            $fecha_hasta = str_replace('/', '-', $fecha_hasta);
            $fecha_hasta = date('Y-m-d', strtotime($fecha_hasta));
            //fecha_desde y fecha_hasta
            $sql_usuario .= ' AND wrkr.trandatetime BETWEEN "'.$fecha_desde.' 00:00:00" AND "'.$fecha_hasta.' 23:59:59" ';
        }else{
            $fecha_desde = date('d/m/Y');
            $fecha_hasta = date('d/m/Y');
            $sql_usuario .= ' AND wrkr.trandatetime BETWEEN "'.date("Y-m-d").' 00:00:00" AND "'.date("Y-m-d").' 23:59:59" ';
        }
        
       /* echo $sql_usuario;*/
        $resultusuario = DB_query($sql_usuario,$db);
        $k=0;
        while ($myrow = DB_fetch_array($resultusuario)) {


            echo "<TR>
            <TD>".$myrow['reqid']."</TD>
            <TD>".$myrow['trandatetime']."</TD>
            <TD>".$myrow['reqdate']."</TD>
            <TD>".$myrow['autorizadorbase']."</TD>
            <TD>".$myrow['solicitantebase']."</TD>
            <TD>".$myrow['categoriabase']."</TD>
            <TD>".$myrow['centrocostobase']."</TD>
            <TD>".$myrow['subcentrocostobase']."</TD>
            <TD>".$myrow['status']."</TD>
            <td class=no_print><a href='REQ_Details.php?reqid=".$myrow['reqid']."'>Ver Requisicion</a></td>
            </TR>";
        
        }

        echo "
        </tbody>
        </TABLE>";
    }
    echo "
    </span>
    </CENTER>";


include('includes/footer.inc');
?>
