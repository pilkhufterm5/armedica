<?php

$PageSecurity = 1;

include ('includes/session.inc');
include ('includes/tablas.php');
include('includes/DefinePOClass.php');
$title = _('Cerrar Ordenes de Compra');

if(isset($_REQUEST['Orden'])){
	
	echo '<result>';
	$sql="update purchorderdetails set forzed=purchorderdetails.quantityord-purchorderdetails.qtyinvoiced, completed=1 ".
			", purchorderdetails.qtyinvoiced=purchorderdetails.quantityord ".
		"where orderno in('".implode("','",array_keys($_REQUEST['Orden']))."')".
		" AND purchorderdetails.completed=0 ".
		" AND purchorderdetails.qtyinvoiced!=purchorderdetails.quantityord";
	DB_query($sql,$db);
	$sql="update purchorders set ".
		"status='".DB_escape_String(PurchOrder::STATUS_CANCELLED)."'".
		",stat_comment=concat('".DB_escape_String(_(PurchOrder::STATUS_CANCELLED).' by '.$_SESSION['UserID']).
		"',stat_comment)".
		"where orderno in('".implode("','",array_keys($_REQUEST['Orden']))."')";
	DB_query($sql,$db);
	foreach ($_REQUEST['Orden'] as $id=>$val){
		echo '<success>'.htmlentities($id).'</success>';
	}
	echo '</result>';
	exit;
}
include ('includes/header.inc');

$NombreTabla="purchorderdetails";
if($_REQUEST['almacen']!=''){
	$almacen = " AND purchorders.intostocklocation = '".DB_escape_string($_REQUEST['almacen'])."' ";
}else
	$almacen = "";
$Encabezados=array(
		'CheckBox'=>('-'),
		'orderno'=>_('Orden'),
		'suppname'=>_('Proveedor'),
		'currcode'=>_('Moneda'),
		'requisitionno'=>_('Referencia'),
		'orddate'=>_('Fecha del Pedido'),
		'initiator'=>_('Hecha por'),
		'status'=>_('Estado'),
		'ordervalue'=>_('Total Ordenado'),
		'Acciones'=>(' '),
);
$Status=array();
$Status[] = DB_escape_String(_(PurchOrder::STATUS_REJECTED));
$Status[] = DB_escape_String( (PurchOrder::STATUS_REJECTED));
$Status[] = DB_escape_String(_(PurchOrder::STATUS_CANCELLED));
$Status[] = DB_escape_String( (PurchOrder::STATUS_CANCELLED));

$SQL="SELECT ".
		" purchorders.realorderno, ".
		" purchorders.orderno, ".
		" suppliers.suppname, ".
		" DATE_FORMAT(purchorders.orddate,'".str_replace(array('Y','m','d'),array('%Y','%m','%d'),$_SESSION["DefaultDateFormat"])."') orddate, ".
		" purchorders.status, ".
		" purchorders.initiator, ".
		" purchorders.requisitionno, ".
		" purchorders.allowprint, ".
		" suppliers.currcode, ".
		" FORMAT(SUM(purchorderdetails.unitprice*purchorderdetails.quantityord),2) AS ordervalue ".
		",status ".
	" FROM purchorders, ".
		" purchorderdetails, ".
		" suppliers ".
	" WHERE purchorders.orderno = purchorderdetails.orderno ".
		" AND purchorders.supplierno = suppliers.supplierid ".
		" AND purchorderdetails.completed=0 ".
		" AND purchorderdetails.qtyinvoiced!=purchorderdetails.quantityord ".
		" AND purchorders.status not in ('".implode("','",$Status)."') ".
		$almacen.
	" GROUP BY purchorders.orderno ASC, ".
		" purchorders.realorderno, ".
		" suppliers.suppname, ".
		" purchorders.orddate, ".
		" purchorders.status, ".
		" purchorders.initiator, ".
		" purchorders.requisitionno, ".
		" purchorders.allowprint, ".
		" suppliers.currcode";
$OrdenesCompra=new tablas($SQL,$NombreTabla,$db);
$SQL="SELECT loccode, locationname FROM locations";
$Almacenes=new tablas($SQL,'locations',$db);


?>
<script type="text/javascript">
<!--
function CancelarResponse(data){
				if($(data).find('success').length>0){
					$(data).find('success').map(function(){
						id=$.trim($(this).text());
						fila=$('input[name="Orden['+id+']"]').closest('tr');
						fila.attr('style',"background:red");
						fila.find('input,buton').remove();
					});
				}
			}
$(function(){
	$('input[name=Orden_all]').click(function(){
		$(this).closest('table').find('input:checkbox').not(this).prop('checked', this.checked);
	});
	$('.CancelarTodosRecursos').click(function(){
		data="";
		$(this).closest('table').find('input[type=checkbox]:checked').map(function(){
			data+=$(this).attr('name')+'=1&';
		});
		$.ajax({
			data:data,
			success:CancelarResponse
		});
	});
	$('.CancelarRecurso').click(function(){
		checkbox=$(this).closest('tr').find('input[type=checkbox]');
		data=checkbox.attr('name')+'=1';
		$.ajax({
			data:data,
			success:CancelarResponse
		});
	});
});
//-->
</script>

<?php 
echo '<center>';
echo '<table>';
echo '<thead>';
$th='';
foreach($Encabezados as $llave=>$valor){
		$th.="<th>";
		switch ($llave){
			case 'CheckBox':
				$valor='<input type="checkbox" name="Orden_all">';
				break;
				default:
					$valor=htmlentities($valor);
		}
		$th.=($valor);
		$th.="</th>";
}
echo $th;
echo '</thead>';

echo '<tbody>';
$i=20;
foreach($OrdenesCompra as $orden){
	$i--;
	if($i<0){
		echo $th;
		$i=19;
	}
	echo '<tr>';
	foreach($Encabezados as $llave=>$valor){
		echo "<td";
		switch ($llave){
			case 'Acciones':
				$orden[$llave]='<buton class="CancelarRecurso btn btn-danger">Cancelar</buton>';
				break;
			case 'CheckBox':
				$orden[$llave]='<input type="checkbox" name="Orden['.htmlentities($orden['orderno']).']">';
				break;
			case 'status':
				$orden[$llave]=htmlentities(_($orden[$llave]));
				break;
			case 'ordervalue':
				echo ' style="text-align: right"';
			default:
				$orden[$llave]=htmlentities($orden[$llave]);
			
		}
		echo ">";
		echo ($orden[$llave]);
		echo "</td>";
	}
	echo "</tr>\r\n";
}
echo '</tbody>';
{
		echo "<td colspan=".count($Encabezados).">";
		echo '<button class="CancelarTodosRecursos">Cancelar Todos</button>';
		echo "</td>";
}
echo '</table>';
echo '</center>';
include ('includes/footer.inc');

