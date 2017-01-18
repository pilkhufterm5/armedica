<?php

$PageSecurity = 2;
if(!isset($tipos))
	$tipos=array(16,20010);
include_once('includes/session.inc');
include_once('includes/tablas.php');
if(!function_exists('UpdateStockSerialItems')){
include_once('includes/tablas.php');
function UpdateStockSerialItems($db){
	$sql="drop table if exists tmp_stockserialitems";
	DB_query($sql,$db);
	$sql="create table tmp_stockserialitems 
	select group_concat(stkmoveno),qty,sum(moveqty) cantidad, serialno, stockserialmoves.stockid,loccode from stockserialmoves join stockmoves on stockmoves.stkmoveno =stockserialmoves.stockmoveno group by stockserialmoves.stockid, stockmoves.loccode, stockserialmoves.serialno";
	DB_query($sql,$db);
	$sql="update stockserialitems, tmp_stockserialitems set stockserialitems.quantity=tmp_stockserialitems.cantidad where stockserialitems.stockid=tmp_stockserialitems.stockid and stockserialitems.loccode=tmp_stockserialitems.loccode and stockserialitems.serialno= tmp_stockserialitems.serialno";
	DB_query($sql,$db);
	$sql="drop table if exists tmp_stockserialitems";
	DB_query($sql,$db);
}
}
$PorCorregir=false;
DB_Txn_Begin($db);
$sql="select count(*)t from stockmoves where stockid in(select stockid from stockmaster where controlled=1) and stkmoveno not in(select stockmoveno from stockserialmoves) order by qty asc";
$res=DB_query($sql,$db);
$fila=DB_fetch_assoc($res);
$PorCorregir=$PorCorregir||$fila['t']>0;
$sql="select b.stockid, a.loccode, b.serialno, sum(moveqty) as qty_serial, sum(qty) as qty, quantity as qty_itemserial from stockmoves a left join stockserialmoves b on b.stockmoveno=a.stkmoveno left join stockserialitems c on c.stockid=a.stockid where c.loccode=a.loccode and c.serialno=b.serialno  group by b.stockid, a.loccode, b.serialno having qty_serial!=qty_itemserial order by a.loccode, b.serialno;";
$tabla=new tablas($sql,'stockmoves',$db);
if(count($tabla)>0)
{
	if($PorCorregir){
		echo 'Total a corregir:';
		echo $fila['t'];
		echo '<br>';
	}
	if($fila=$tabla->first()){
	$PorCorregir=true;
	echo '<table>';
	
	echo '<tr>';
	echo '<td>';
	echo implode('</td><td>',$tabla->getHeaders());
	echo '</td>';
	echo '</tr>';
	//	foreach($tabla as $fila)
	do{
		echo '<tr>';
		echo '<td>';
		echo implode('</td><td>',$fila);
		echo '</td>';
		echo '</tr>';
	}while($fila=$tabla->next());
	echo '</table>';
	}
}
if($PorCorregir){
?>
<form method="POST">
<input name="Corregir" value="Corregir" type="Submit">
</form>
<?php 
}
if(isset($_REQUEST['Corregir'])){	
UpdateStockSerialItems($db);
foreach($tipos as $type){
	$SerieLocacion=array();
	$sql="select * from stockmoves where stockid in('00193_01') and stkmoveno not in(select stockmoveno from stockserialmoves) order by stkmoveno asc";
	$sql="select * from stockmoves where stockid in(select stockid from stockmaster where controlled=1) and stkmoveno not in(select stockmoveno from stockserialmoves) and type=$type order by stkmoveno asc";
	$res=DB_query($sql,$db);
	while($fila=DB_fetch_assoc($res))
	{
		$total=$fila['qty'];
		$series=array();
		if($total<0){
			$sql="select * from stockserialitems where loccode='".$fila['loccode']."' and stockid='".$fila['stockid']."' and quantity>0 order by unix_timestamp(expirationdate) asc, quantity desc";
			$res1=DB_query($sql,$db);
			while((-$total)>0&&$serie=DB_fetch_assoc($res1)){
				if($serie['quantity']<=(-$total)){
					$serie['quantity']=-$serie['quantity'];
					$series[$serie['serialno']]=$serie;
					if($serie['quantity']==($total))
						$total=0;
					else	
						$total-=$serie['quantity'];
				}else{
					$serie['quantity']=$total;
					$series[$serie['serialno']]=$serie;
					$total=0;
				}
			}
			
			$SerieLocacion[$fila['type']][$fila['transno']][$fila['stockid']]=$series;
			
		}else{
			foreach($SerieLocacion[$fila['type']][$fila['transno']][$fila['stockid']] as $serialno=>$valor){
				
				$sql="select count(*)t from stockserialitems where loccode='".
				$fila['loccode']."' and stockid='".$fila['stockid'].
				"' and serialno='".$serialno."'";
				
				$seiress=DB_query($sql,$db);
				$ser=DB_fetch_assoc($seiress);
				if($ser['t']==0){
					
					$sql="insert into stockserialitems values('".
							$fila['stockid']."','".
							$fila['loccode']."','".
							$serialno."','".
							$valor['expirationdate']."','".
							"0',''".
							")";
					DB_query($sql,$db);
				}
				$valor['quantity']=-$valor['quantity'];
				$series[$serialno]=$valor;
			}
		}
		foreach($series as $serialno=>$value){
			$sql="update stockserialitems set quantity=quantity+".
					$value['quantity']
			." where loccode='".$fila['loccode']."' and stockid='".$fila['stockid']."' and serialno='".$serialno."'";
			DB_query($sql,$db);
			$SQL="insert into stockserialmoves values(0,'".$fila['stkmoveno']."','".$fila['stockid']."','".$serialno."',".$value['quantity'].")";
			DB_query($SQL,$db);
		}
		
	}
}

$sql="select count(*)t from stockmoves where stockid in(select stockid from stockmaster where controlled=1) and stkmoveno not in(select stockmoveno from stockserialmoves) order by qty asc";
$res=DB_query($sql,$db);
$fila=DB_fetch_assoc($res);
echo 'Total pendientes a corregir:';
echo $fila['t'];
echo '<br>';
UpdateStockSerialItems($db);
}
DB_Txn_Commit($db);
//DB_Txn_Rollback($db);
