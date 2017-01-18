<?
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */
$PageSecurity = 14;
include('includes/session.inc');

//print_r($_SESSION);

unset($_SESSION['rh_pos_principal']);
$_SESSION['rh_pos_principal']  = 0;
unset($_SESSION['rh_pos_principal']);

$title = _('Punto de Venta');
include('includes/header.inc');
echo '<A HREF="'. $rootpath . '/index.php?&Application=orders">'. _('Back to Sales Orders'). '</A><BR>';

/*se hace sql para conseguir el cliente*/
$sql = "select name,TaxRef from debtorsmaster where name like 'MOSTRADOR%' AND (TaxRef = 'XAXX010101000' or TaxRef = 'XEXX010101000')";
$cliente = DB_query($sql,$db);
if (DB_num_rows($cliente) == 0){
    $bandera = 0;
}else{
    $bandera = 1;
}
?>
<center>
<BR><BR><FONT SIZE=3><B>Seccion Principal del Punto de Venta</B></FONT>
<br /><br /><br />
<form name="pos" id="pos" class="pos" action="#" method="POS">
    <label for="">Terminal</label>
    <select name="sucursal" id="sucursal">
        <option value="-9">Selecciona</option>
        <?
            $sql = "Select pos.id, pos.Sucursal, pos.Terminal, pos.userid, pos.Fecha, loc.locationname from rh_pos_terminales pos LEFT JOIN locations loc ON pos.Sucursal = loc.loccode order by Fecha DESC";
            $sucursales = DB_query($sql,$db);
            if (DB_num_rows($sucursales) != 0){
                while ($myrow=DB_fetch_array($sucursales)) {
                    echo "<option value='".$myrow['id']."'>".$myrow['Terminal']."</option>";
                }
            }
        ?>
    </select><br /><br />
    <input type="submit" name="pos" id="pos" value="Iniciar POS" style="width:150px; height:50px" />
    <input type="hidden" name="bandera" id="bandera" class="bandera" value="<?=$bandera;?>" />
</form>
</center>
<script type="text/javascript" src="rh_pos_archivos/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
        $(document).ready(function() {
            jQuery("#pos").submit(function(){
               var cate = document.pos.sucursal.options[document.pos.sucursal.selectedIndex].value;
               var bandera = jQuery("#bandera").val();

               if(bandera == 0){
                    alert('El Cliente Mostrador no se encuentra reigstrado en la base de datos\nSin este registro no se puede iniciar el Punto de Venta');
                    return false;
               }

               if(cate == -9){
                   alert('Debes de Seleccionar una Terminar para iniciar el Punto de Venta');
                   return false;
               }else{
                   jQuery.post("rh_pos_procesa.php",{type:"inicial", cate:cate, user:"<?=$_SESSION['UserID'];?>"}, function(ResBusqueda){
                        //alert(ResBusqueda);
                       inicia();
                   });
                   return false;
               }
            });
        });
        function inicia(){
           window.open("rh_pos_principal.php", "mywindow", "fullscreen=yes, scrollbars=yes");
        }
</script>
<?
include('includes/footer.inc');
?>