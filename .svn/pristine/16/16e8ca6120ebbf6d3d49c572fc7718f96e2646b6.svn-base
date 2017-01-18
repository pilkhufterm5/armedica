<?php
$PageSecurity = 1;

/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');
include('includes/tablas.php');

$title = _('Reimpresion de codigos');
include('includes/header.inc');

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";


if($_POST['filtro'] == 'Buscar'){

   $sql="SELECT  '#' checkbox,
            rh_etiquetas.barcode,
            rh_etiquetas.stockid,
            description,
            rh_etiquetas.serialno,
            purchorderdetails.quantityord,
            grns.qtyrecd,
            grns.qtyrecd-rh_etiquetas_impresion.impresiones impresiones,
            rh_etiquetas_impresion.impresiones diferencia,
            rh_etiquetas_impresion.id
        FROM rh_etiquetas_impresion
        LEFT JOIN rh_etiquetas ON rh_etiquetas.barcode=rh_etiquetas_impresion.barcode
        LEFT JOIN stockmaster sma ON rh_etiquetas.stockid=sma.stockid
        LEFT JOIN purchorderdetails ON purchorderdetails.podetailitem=rh_etiquetas_impresion.podetailitem
        LEFT JOIN grns ON grns.grnno=rh_etiquetas_impresion.grnno
        WHERE
        (rh_etiquetas_impresion.impresiones<=grns.qtyrecd or grns.grnno is null)
        AND rh_etiquetas.barcode = '" . DB_escape_string($_POST['BarCode']) . "'
        ";
}
$Documento=0;


if(isset($_REQUEST['print'])){
	$res=DB_query(
	'select max(documento) Documento from rh_etiquetas_documento_impresion '
	,$db);
	$fila=DB_fetch_assoc($res);
	$Documento=$fila['Documento']+1;
	// $sqlx="insert into rh_etiquetas_documento_impresion(id_etiq_impresion,documento,barcode,stockid,serialno,userid,quantityord,qtyrecd,impresiones,creado)value(";

	// foreach($_REQUEST['porimprimir'] as $id=>$barras){
	// 	$where=" and rh_etiquetas.barcode ='".$barras."'";
	// 	$cantidad=(int)$_REQUEST['impresion'][$id];
	// 	$res=DB_query(str_replace("'#'",'rh_etiquetas_impresion.id',$sql).$where." limit 1 ",$db);
	// 	while ($row=DB_fetch_assoc($res)){
	// 		DB_query(
	// 		$sqlx.
	// 			'"'.DB_escape_string($row['checkbox']).'",'.
	// 			'"'.DB_escape_string($Documento).'",'.
	// 			'"'.DB_escape_string($row['barcode']).'",'.
	// 			'"'.DB_escape_string($row['stockid']).'",'.
	// 			'"'.DB_escape_string($row['serialno']).'",'.
	// 			'"'.DB_escape_string($_SESSION['UserID']).'",'.
	// 			'"'.DB_escape_string($row['quantityord']).'",'.
	// 			'"'.DB_escape_string($row['qtyrecd']).'",'.
	// 			'"'.DB_escape_string($cantidad).'",'.
	// 			'NOW()'.
	// 		")",$db
	// 		);
	// 	}
	// }


	foreach($_REQUEST['porimprimir'] as $id=>$barras)

        if(isset($_REQUEST['impresion'][$id])){

			// $cantidad=(int)$_REQUEST['impresion'][$id];
			// DB_query(
			// 'update rh_etiquetas set impresiones=impresiones+"'.$cantidad.'" where barcode="'.DB_escape_string($barras).'"'
			// ,$db);

			// DB_query(
			// 'update rh_etiquetas_impresion set impresiones=impresiones-"'.$cantidad.'" where id="'.DB_escape_string($id).'"'
			// ,$db);
			// DB_query(
			// 'update rh_etiquetas_impresion set documento="'.DB_escape_string($Documento).'" where id="'.DB_escape_string($id).'"'
			// ,$db);
			// DB_query(
			// 'update rh_etiquetas_impresion set status=1, impresiones=0 where impresiones<=0 and id="'.DB_escape_string($id).'"'
			// ,$db);

	}
}





$desde=date('Y-m-d');
$hasta=date('Y-m-d');
if(isset($_REQUEST['BarCode']))
	$recepcion=DB_escape_string($_REQUEST['BarCode']);

if(isset($recepcion)&&$recepcion!='')
	//$where.=" and grns.grnbatch='".$recepcion."'";

$tabla=new tablas($sql.$where.$grupo,"stockmoves",$db);

$head=array(
        "checkbox"=>'',
		"barcode"=>'Codigo de barras',
		"serialno"=>'Lote',
		"stockid"=>'Stockid',
		//"description"=>'Articulo',
		"quantityord"=>'Unidades en OC',
		"qtyrecd"=>'Unidades recibidas en OC',
		"impresiones"=>'Etiquetas Impresas',
		"Cantidad"=>'Cantidad a Imprimir'
		);
$Mensaje=array();


if(isset($_REQUEST['print'])){
	foreach($_REQUEST['porimprimir'] as $id=>$barras)
		//if($_REQUEST['impresion'][$id]>0)
		$Mensaje[]=array('codigo'=>$barras,'cantidad'=>$_REQUEST['Cantidad'][$id],'descripcion'=>$_REQUEST['descripcion'][$id]);
		if(count($Mensaje)>0){
	?>
	<center>
    	<applet  code="appletr.AppletR.class" archive="barcode2/AppletR.jar?param=10" width="2" height="2">
    			<PARAM name="Message" value="<?=htmlentities(json_encode($Mensaje))?>">
    	</applet>
    </center>
	<?php }
	?>
    <center>
	   <!-- <a href="rh_imprimir_hoja_codigo.php?documento=<?=$Documento?>">Imprimir hoja de relacion numero <b><?=$Documento?></b></a> -->
	</center>
	<?php
}
?>
<script type="text/javascript">
function CrearApplet(){
	$('#print').parent().find('applet').remove();
	applet="<applet  code=\"appletr.AppletR.class\" archive=\"barcode2/AppletR.jar?param=10\" width=\"70\" height=\"25\">"+
		"<PARAM name=\"Message\" value='[{codigo:\"NO DATA\",cantidad:3},{codigo:\"NO DATA\",cantidad:3}]'>"+
	"</applet>";
	parametros=[];
	$('#CodigosBarras').find("input[name^=porimprimir]:checked").map(function(){
		info={codigo:"NO DATA",cantidad:3};
		info.codigo=$(this).val();
		info.cantidad=$(this).closest('tr').find('input[type=text]').val();
		if(info.cantidad>0)
			parametros.push(info);
	});
    console.info(parametros);
	$(applet).find('PARAM[name=Message]').attr('value',JSON.stringify(parametros)).parent().appendTo($('#print').parent());

}
// $(function(){
// 		$('#CodigosBarras').find("input").change(CrearApplet);
// 		$('#print').click(function(){
// 			CrearApplet();
// 			setTimeout(function(){
// 				$(this).closest('form').find('[name=completed]').click();
// 			},60000);
// 		});

// });

</script>

<center>
	<form method="post">
	<table >
		<tr>
			<td>Codigo de Barras</td>
			<td><input type="text" name="BarCode" value="<?=$_REQUEST['BarCode']?>"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Buscar" name="filtro" class=" btn btn-success"></td>
		</tr>
	</table>

    <table id="CodigosBarras" class="table table-bordered table-striped table-hover" >
        <thead>
        <tr>
        	<?php
        	foreach($head as $nombre){
        	?>
        	<th><?=htmlentities($nombre)?></th>
        	<?php }?>
        </thead>
        </tr>
		<?php
		$columnas=count($head);
		foreach($tabla as $imprimir){
            $imprimir['checkbox']='<input type="checkbox" class="porimprimir" name="porimprimir['.$imprimir['id'].']" checked="checked" value="'.$imprimir['barcode'].'">';
            $imprimir['checkbox'].='<input type="hidden" name="descripcion['.$imprimir['id'].']" value="'.htmlentities($imprimir['stockid'].' ['.$imprimir['serialno'].']').'">';
			$imprimir['Cantidad']=
				'<input type="text" name="Cantidad['.$imprimir['id'].']" >';
			echo '<tr>';
			foreach($head as $id=>$nombre){
				echo '<td style="text-align: center;">'.($imprimir[$id]).'</td>';
			}
			echo '</tr>';
			$columnas=max($columnas,count($imprimir));
		}
		?>
		<tr><td colspan="<?=$columnas?>">
		<center>
			<input type="submit" name="print" id="print" value="Imprimir" class="btn btn-danger"><br />
		</center>
		</td></tr>
		</table>
	</form>
</center>
<?php
include('includes/footer.inc');
