<?php
    /**
     * 	REALHOST 17 DE ABRIL DEL 2010
     * 	POS DEL WEBERP
     * 	VERSION 1.0
     * 	RICARDO ABULARACH GARCIA
     * */

$PageSecurity = 2;
include('includes/session.inc');

if(isset($_GET['action'])){
    if($_GET['type'] == "insert"){
        $nombre = $_POST['nombre'];
        $sucursal = $_POST['sucursal'];
        $cliente= $_POST['cliente'];
        $sql_insert = "INSERT INTO rh_pos_terminales(Sucursal, Terminal, userid, Fecha,debtorno) VALUES('".$sucursal."', '".$nombre."', '".$_SESSION['UserID']."', '".date('Y-m-d')."','".$cliente."')";
        $insert = DB_query($sql_insert,$db);
        header('location: rh_pos_terminal.php');
    }
}

$title = _('Punto de Venta');
include('includes/header.inc');
echo '<A HREF="'. $rootpath . '/index.php?&Application=system&' . SID . '">'. _('Back to Menu'). '</A><BR>';
?>
<script type="text/javascript" src="rh_pos_archivos/jquery-1.4.2.min.js"></script>
<center>
<BR><BR><FONT SIZE=3><B>Terminales Punto de Venta</B></FONT><br /><br />
<a href="rh_pos_terminal.php?action=operacion&type=add">Nueva Terminal</a>

<?if($_GET['action'] == "operacion"){?>
    <!-- forma para agregar elementos -->
    <br /><br /><br />
    <form id="agregar" name="agregar" action="rh_pos_terminal.php?action=operacion&type=insert" method="POST">
        <table CELLPADDING=3 COLSPAN=4>
            <tr>
                <td><label for="nombre">Nombre</label></td>
                <td><input type="text" name="nombre" id="nombre" class="nombre" /></td>
            </tr>
            <tr>
                <td><label for="sucursal">Sucursal</label></td>
                <td>
                    <select name="sucursal" id="sucursal" class="sucursal">
                        <option value="-9">Selecciona</option>
                        <?
                            $sql = "Select loccode, locationname FROM locations ORDER BY loccode DESC";
                            $sucursales = DB_query($sql,$db);
                            if (DB_num_rows($sucursales) != 0){
                                 while ($myrow=DB_fetch_array($sucursales)){
                                    echo "<option value='".$myrow['loccode']."'>".$myrow['locationname']."</option>";
                                 }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="cliente">Cliente Mostrador</label></td>
                <td>
                    <select name="cliente" id="cliente" class="sucursal">
                        <option value="-9">Selecciona</option>
                        <?
                            $sql = "select debtorno,name from debtorsmaster where `name` like 'MOSTRADOR%'";
                            $sucursales = DB_query($sql,$db);
                            if (DB_num_rows($sucursales) != 0){
                                 while ($myrow=DB_fetch_array($sucursales)){
                                    echo "<option value='".$myrow['debtorno']."'>".$myrow['name']."</option>";
                                 }
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="">Enviar</label></td>
                <td><input type="submit" id="enviar" class="enviar" value="Agregar" /></td>
            </tr>
        </table>
    </form>
<?}else{?>
    <!-- lista de terminales -->
    <table CELLPADDING="5" COLSPAN="5" cellspacing="5">
        <tr>
            <td class='tableheader'>Id</td>
            <td class='tableheader'>Sucursal</td>
            <td class='tableheader'>Terminal</td>
            <td class='tableheader'>Fecha</td>
        </tr>
        <?
            $sql = 'SELECT
                        rh_pos_terminales.id
                        , rh_pos_terminales.Sucursal
                        , rh_pos_terminales.Terminal
                        , rh_pos_terminales.userid
                        , rh_pos_terminales.Fecha
                        , debtorsmaster.name
                        , locations.locationname
                    FROM
                        locations
                        INNER JOIN rh_pos_terminales
                            ON (locations.loccode = rh_pos_terminales.Sucursal)
                        INNER JOIN debtorsmaster
                            ON (debtorsmaster.debtorno = rh_pos_terminales.debtorno)';

            $sucursales = DB_query($sql,$db);
            if (DB_num_rows($sucursales) == 0){
                echo "<tr>";
                    echo "<td colspan=5 align='center'>Sin Resultados</td>";
                echo "</tr>";
            }else{
                while ($myrow=DB_fetch_array($sucursales)) {
                    echo "<tr>";
                        echo "<td align='center'>".$myrow['id']."</td>";
                        echo "<td align='center'>".$myrow['locationname']."</td>";
                        echo "<td align='center'>".$myrow['Terminal']."</td>";
                        echo "<td align='center'>".$myrow['Fecha']."</td>";
                        echo "<td align='center'>".$myrow['name']."</td>";
                    echo "</tr>";
                }
            }
        ?>
    </table>
<?}?>
</center>
<script type="text/javascript">
$(document).ready(function() {
    jQuery("#agregar").submit(function(){
       var cate = document.agregar.sucursal.options[document.agregar.sucursal.selectedIndex].value;
       var nombre = jQuery("#nombre").val();
       var error = 0;
       var mensaje = "No se pudo procesar tu peticion debido a:\n";

       if(cate == "-9"){
            error = 1;
            mensaje += " - El campo de sucursal no puede ser vacio\n";
       }

       if(nombre == "" || nombre.length == 0){
            error = 1;
            mensaje += " - El campo de nombre no puede ser vacio\n";
       }

       if(error == 0){
            /*cuando no pasa nada*/
            return true;
       }else{
            alert(mensaje);
            return false;
       }
    });
});
</script>
<?
include('includes/footer.inc');
?>