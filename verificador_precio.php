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

$title = _('Verificador de precios');
include('includes/header.inc');
?>
<center>
<form name="frmVerifica" method="POST" action="verificador_precio.php">
<table>
    <tr>
        <td colspan="2" align="center"><h2>Verificador de precios</h2>
        </td>
    </tr>
    <tr>
        <td>Punto de Venta
        </td>
        <td><select name="PO">
    <?php
                            $sql = "select salestype,name from debtorsmaster where `name` like 'MOSTRADOR%'";
                            $sucursales = DB_query($sql,$db);
                            if (DB_num_rows($sucursales) != 0){
                                 while ($myrow=DB_fetch_array($sucursales)){
                                    echo "<option value='".$myrow['salestype']."'>".$myrow['name']."</option>";
                                 }
                            }
    ?>
            </select></td>
    </tr>
     <tr>
        <td>C&oacute;digo
        </td>
        <td><input type="input" name="codigo" value="" />
        </td>
    </tr>
     <tr>
        <td colspan="2" align="center"><input type="submit" name="submit" value="Consultar" />
        </td>
    </tr>
</table>
</form>
<br />
<br />
<?php if(isset($_POST['submit'])){
                            if($_POST['PO']=='P1'){
                               $PO='M1';
                            }else if($_POST['PO']=='C1'){
                               $PO='M2';
                            }else{
                               $PO='M1';
                            }
                              $sql = "select description, price from stockmaster,prices where stockmaster.stockid = prices.stockid and prices.typeabbrev='".$PO."' and  (stockmaster.stockid LIKE '%" . $_POST['codigo'] . "%' OR stockmaster.barcode = '".$_POST['codigo']."')";
                            $prices = DB_query($sql,$db);
                            if (DB_num_rows($prices) == 0){
                              include('includes/footer.inc');
                              exit;
                            }else{
                               $myrow=DB_fetch_array($prices);
                            }
  ?>
<table>
    <tr>
        <td colspan="2" align="center"><h2>Resultado</h2></td>
    </tr>
    <tr>
        <td>Articulo:</td>
        <td><b><?php echo $myrow['description']; ?></b></td>
    </tr>
    <tr>
        <td>Precio:</td>
        <td><b><?php echo $myrow['price']; ?></b></td>
    </tr>
</table>
<?php }?>
</center>
<?php
include('includes/footer.inc');
?>