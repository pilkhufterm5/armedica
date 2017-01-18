<?php
$PageSecurity = 1;

include('includes/session.inc');
$title=_('Saldos / Existencias');
include ('includes/tablas.php');
include('includes/header.inc');
$Existencias=false;
if(isset($_POST['DebajoMinimo'])){
	$SQL = "select sum(locstock.quantity)quantity, locstock.stockid, stockmaster.lowestlevel Minimo from ";
	$SQL.= " locstock ";
	$SQL.= " left join stockmaster on stockmaster.stockid=locstock.stockid ";
	if(isset($_REQUEST['almacen'])&&$_REQUEST['almacen']!='')
		$SQL.= " where locstock.loccode='".DB_escape_string($_REQUEST['almacen'])."' and locstock.quantity<>0 ";
	
	$SQL.= " group by stockid ";
// 	if (isset($_POST['Minimo'])){
// 		$SQL.= " having Minimo>=quantity ";
// 	}
	$Existencias = DB_query($SQL,$db,$ErrMsg,$DbgMsg);
	
}
$tabla='locations';
$sql="select * from ".$tabla;

$Almacenes=new tablas($sql,$tabla,$db);
?>
<center>
<form action="" method="post">
<input type="checkbox" name="Minimo" <?=(isset($_POST['Minimo'])?" checked=checked ":"")?>><?=_('Igual y por debajo del Minimo');?><br>
<select name="almacen">
<option value="">Todos</option>
<?php
foreach($Almacenes as $almacen){
echo '<option ';
if($_REQUEST['almacen']==$almacen['loccode'])
	echo ' selected=selected ';
echo ' value="'.htmlentities($almacen['loccode']).'">'.htmlentities($almacen['locationname']).'</option>';
} 
?>
</select>
<br>
<input type="submit" value="Buscar" name="DebajoMinimo">
<?php if($Existencias){?>
<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
<script type="text/javascript">
$(function(){
	$('csv').show();	
})
</script>
<csv style="display:none" target="Existencias (<?=date('Y-m-d')?>)" title=".TablaExistencias"><button>Excel</button></csv>
<pdf style="display:none" target="Existencias (<?=date('Y-m-d')?>)" title=".TablaExistencias"><button>Pdf</button></pdf>
<?php }?>
</form>
<table class="TablaExistencias">
	<tr>
		<th><?=_('Stockid')?></th>
		<th><?=_('Minimo')?></th>
		<th><?=_('Existencia')?></th>
	</tr><?php
	if($Existencias)
	while($Fila=DB_fetch_assoc($Existencias)){ 
		$StockID=$Fila['stockid'];
		$sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
		FROM salesorderdetails,
		salesorders
		WHERE salesorders.orderno = salesorderdetails.orderno AND
		salesorderdetails.completed=0 AND
		salesorders.quotation=0 AND
		salesorderdetails.stkcode='" . $StockID . "'";
		$DemandResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);
		if (DB_num_rows($DemandResult)==1){
			$DemandRow = DB_fetch_row($DemandResult);
			$DemandQty =  $DemandRow[0];
		} else {
			$DemandQty =0;
		}
		if (isset($_POST['Minimo'])&&(($Fila['quantity']-$DemandQty)>$Fila['Minimo'])) continue;
	?>
	<tr>
		<td><?=$StockID;?></td>
		<td><?=$Fila['Minimo']?></td>
		<td><?php 
		echo $Fila['quantity']-$DemandQty;
		?></td>
	</tr><?php }?>
</table>
</center>
<?php 
include('includes/footer.inc');