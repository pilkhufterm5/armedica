<?php
$PageSecurity = 2;

include('includes/session.inc');
include ('includes/tablas.php');

$almacenes=array();
$resalmacenes=new tablas("select loccode, locationname from locations",'locations',$db);
foreach ($resalmacenes as $almacen)
	$almacenes[$almacen['loccode']]=$almacen;

if(isset($_REQUEST['Finicio'])){
	$Finicio=str_replace('/','-',FormatDateForSQL(Format_Date($_REQUEST['Finicio'])));
}else $Finicio=date('Y-m-d',strtotime('first day of this month'));

if(isset($_REQUEST['Ffin'])){
	$Ffin=str_replace('/','-',FormatDateForSQL(Format_Date($_REQUEST['Ffin'])));
}else $Ffin=date('Y-m-d',strtotime('last day of this month'));

$almacen='';
$where=array();
if(isset($_REQUEST['almacen'])&&$_REQUEST['almacen'][0]!=''){
	$where[]=' loc.loccode in("'.implode('","',array_map(function($dato){return DB_escape_string($dato);},$_REQUEST['almacen'])).'")';
}

$title = _('Kardex');
include('includes/header.inc');
$Titulos=array(
		"Clave"=>_('Clave'),
		"Material"=>_('Material'),
		"Almacen"=>_('Almacen'),
		"Linea"=>_('Linea'),
		"Grupo"=>_('Grupo'),
		"Unidad"=>_('Unidad'),
		"InvInicial"=>_('Inv. <br>Inicial'),
		"ED"=>_('ED'),
		"CM"=>_('CM'),
		"TP1"=>_('+ TP'),
		"TP2"=>_('- TP'),
		"DA1"=>_('+ DA'),
		"DA2"=>_('- DA'),
		"DP"=>_('DP'),
		"VT"=>_('VT'),
		'maximo'=>_('M&aacute;ximo'),
		'minimo'=>_('Minimo'),
		"Final"=>_('Inv. <br>Final'),
		"ExReal"=>_('Exi. <br>Real'),
		//'InvInicialstkmoveno'=>'InvInicialstkmoveno',
		"Estatus"=>_('Estatus')
);
if(!isset($_REQUEST['Estatus'])||in_array('EX',$_REQUEST['Estatus'])){
	$where[]=' loc.quantity<>0 ';
}

if(count($where)>0)$almacen=' where '.implode(' and ',$where);
$sqlEx="select newqoh-qty qty from stockmoves where stkmoveno='%s'";
$sql=
	"select ".
		"sma.stockid Clave, ".
		"sma.description Material, ".
		"group_concat(lc.loccode SEPARATOR \"', '\") loccode, ".
		"lc.locationname Almacen, ".
		"'' Linea, ".
		"'' Grupo, ".
		"sma.units Unidad, ".
		"min(sm.stkmoveno) InvInicialstkmoveno, ".
		"sm.newqoh InvInicial, ".
		"sum(if(sm.qty>0 and sm.type in(17,18,25,26),sm.qty,0)) ED, ".
		"sum(if(sm.qty<0 and sm.type in(10,28,29,20005,20010),sm.qty,0)) CM, ".
		"sum(if(sm.qty>0 and sm.type in(16),sm.qty,0)) TP1, ".
		"sum(if(sm.qty<0 and sm.type in(16),sm.qty,0)) TP2, ".
		"sum(if(sm.qty>0 and sm.type in(11),sm.qty,0)) DA1, ".
		"sum(if(sm.qty<0 and sm.type in(11),sm.qty,0)) DA2, ".
		"sum(if(sm.qty<0 and sm.type in(18,25),sm.qty,0)) DP, ".
		"sum(if(sm.qty>0 and sm.type in(10),sm.qty,0)) VT, ".
		"'' Final, ".
		"loc.quantity ExReal, ".
		"mx.maximo, ".
		"mx.minimo, ".
		"'OK' Estatus ".
	"from ".
		"stockmaster sma ".
		"join locstock loc on loc.stockid=sma.stockid ".
		"join locations lc on loc.loccode=lc.loccode ".
		"left join  stockmoves sm on sma.stockid=sm.stockid and sm.loccode=loc.loccode and sm.show_on_inv_crds=1 and ".
		"sm.trandate between '{$Finicio}' and '{$Ffin}' ".
		"left join rh_locstock_max_min_agr mx on mx.id_agrupador=sma.id_agrupador and mx.loccode=loc.loccode ".
		$almacen;
	if(in_array('AGR',$_REQUEST['Estatus'])){
		$sql.="group by sma.id_agrupador, loc.loccode order by Clave";
	}else
		$sql.="group by sma.stockid, loc.loccode order by Material";

$res=new tablas($sql,'systypes',$db);
?>
<center>
<form method="post">
<table>
	<tr>
		<td><?=_('Desde')?></td>
		<td><input name="Finicio" type="date" value="<?=$Finicio?>"></td>
		<td><?=_('Hasta')?></td>
		<td><input name="Ffin" type="date" value="<?=$Ffin?>"></td>
	</tr>
	<tr>
		<td><?=_('Almacen')?></td>
		<td>
			<select name="almacen[]">
			<option value="">Todos</option><?php 
           		foreach ($almacenes as $alm){
					echo '<option ';
					if(isset($_REQUEST['almacen'])&&in_array($alm['loccode'],$_REQUEST['almacen']))
						echo ' selected=selected ';
					echo 'value="';
					echo htmlentities($alm['loccode']);
					echo '">';
					echo htmlentities($alm['locationname']);
					echo '</option>';
                }
               ?>
              </select>
             </td>
		<td><?=_('Solo para')?></td>
		<td>
		<select name="Estatus[]" multiple=multiple>
			<option <?php if(!isset($_REQUEST['Estatus'])||in_array('EX',$_REQUEST['Estatus']))echo ' selected=selected '; ?> value="EX">Con Existencia</option>
			<option <?php if(!isset($_REQUEST['Estatus'])||in_array('OK',$_REQUEST['Estatus']))echo ' selected=selected '; ?> value="OK">OK</option>
			<option <?php if(!isset($_REQUEST['Estatus'])||in_array('PR',$_REQUEST['Estatus']))echo ' selected=selected '; ?> value="PR">PR</option>
			<option <?php if(!isset($_REQUEST['Estatus'])||in_array('FI',$_REQUEST['Estatus']))echo ' selected=selected '; ?> value="FI">FI</option>
			<option <?php if(isset($_REQUEST['Estatus'])&&in_array('AGR',$_REQUEST['Estatus']))echo ' selected=selected '; ?> value="AGR">Id Agrupador</option>
		</select>
		<script type="text/javascript">
		$(function(){
			$('select[name^=Estatus]').select2();
		})
		</script>
		</td>
	</tr>
	<tr>
		<td colspan=4><center>
			<input type="submit">
			<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
			<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
            <script type="text/javascript">
            	$(function(){
                	$('csv, pdfs').show();
                            		
                 })
			</script>
            <csv style="display:none" target="Kardex(<?=date('Y-m-d')?>)" title=".Kardex"><button>Excel</button></csv>
            <pdf style="display:none" target="Kardex(<?=date('Y-m-d')?>)" title=".Kardex"><button>Pdf</button></pdf>
		</center></td>
	</tr>
</table>
</form>
<?php 
echo '<table class="Kardex">';
echo '<tr>';
	foreach($Titulos as $Titulo){		
		echo '<th>';
			echo ($Titulo);
		echo '</th>';
	}
echo '</tr>';

foreach($res as $fila){
	$fila['Estatus']=($fila['minimo']>=$fila['ExReal']&&$fila['minimo']>0?'PR':(
		$fila['ExReal']==0?'FI':
		$fila['Estatus']
		)
	);
	if(isset($_REQUEST['Estatus'])&&!in_array($fila['Estatus'],$_REQUEST['Estatus'])) continue;
	if($fila['InvInicialstkmoveno']!=''){
		$sqlx=sprintf($sqlEx,$fila['InvInicialstkmoveno']);
		
		$res=DB_query($sqlx,$db);
		if($resultado=DB_fetch_assoc($res)){
			$fila['InvInicial']=$resultado['qty'];
		}
	}
	if($fila['InvInicial']=='')$fila['InvInicial']=0;
	
	$fila['Final']=
		$fila['InvInicial']+
		$fila['ED']+
		$fila['CM']+
		$fila['TP1']+
		$fila['TP2']+
		$fila['DA1']+
		$fila['DA1']+
		$fila['DP']+
		$fila['VT'];

// 		$fila['ED']=abs($fila['ED']);
// 		$fila['CM']=abs($fila['CM']);
// 		$fila['TP1']=abs($fila['TP1']);
// 		$fila['TP2']=abs($fila['TP2']);
// 		$fila['DA1']=abs($fila['DA1']);
// 		$fila['DA1']=abs($fila['DA1']);
// 		$fila['DP']=abs($fila['VT']);
// 		$fila['VT']=abs($fila['VT']);
	
	echo '<tr';
	if($fila['Estatus']=='FI')
		echo ' style="background-color: yellow;"';
	else if($fila['Estatus']=='PR')
		echo ' style="background-color: red;color: white;"';
	echo '>';
	foreach($Titulos as $llave=>$Titulo){		
		echo '<td>';
			echo htmlentities($fila[$llave]);
		echo '</td>';
	}
echo '</tr>';
	
}
echo '</table>';
echo '</center>';

include('includes/footer.inc');
