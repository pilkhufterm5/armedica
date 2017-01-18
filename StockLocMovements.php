<?php

//var_dump($_POST);

$PageSecurity = 11;

include('includes/session.inc');

$title = _('All Stock Movements By Location');

	$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
	$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);
$SQLMAin= "SELECT stockmoves.stockid,
		systypes.typename,
		stockmoves.type,
		stockmoves.transno,
		stockmoves.trandate,
		stockmoves.debtorno,
		stockmoves.branchcode,
		stockmoves.qty,
		stockmoves.reference,
		stockmoves.price,
		stockmoves.discountpercent,
		stockmoves.newqoh,
		stockmaster.decimalplaces,
		stockmaster.id_agrupador,
		stockmaster.barcode ";
	$SQLMAinleft=$SQLFamiliaWhere='';
	foreach($_REQUEST['Categoria'] as $categoria=>$valor){
		$categoria=(int)$categoria;
		$SQLMAinleft.=" left join rh_familia_stock rh_familiaCatStock".$categoria." on rh_familiaCatStock".$categoria.".stockid=stockmoves.stockid and rh_familiaCatStock".$categoria.".categoria='$categoria' ";
		$SQLMAinleft.=" left join rh_familia rh_familiaCat".$categoria." on rh_familiaCatStock".$categoria.".clave=rh_familiaCat".$categoria.".clave and rh_familiaCat".$categoria.".categoria='$categoria' ";
		$SQLMAin.=", rh_familiaCat".$categoria.".nombre Categoria".$categoria." ";
		if(trim($valor)!=''){
			$SQLFamiliaWhere.=" and rh_familiaCat".$categoria.".clave='".DB_escape_string($valor)."' ";
			$SQLFamiliaWhere.=" and rh_familiaCatStock".$categoria.".clave='".DB_escape_string($valor)."' ";
		}
	}
	$SQLMAin.=" FROM stockmoves
	INNER JOIN systypes ON stockmoves.type=systypes.typeid
	INNER JOIN stockmaster ON stockmoves.stockid=stockmaster.stockid ";
	
	
	$SQLMAin.=$SQLMAinleft." WHERE  stockmoves.loccode='" . $_POST['StockLocation'] . "'".
	$SQLFamiliaWhere.
	"AND stockmoves.trandate >= '". $SQLAfterDate . "'
	AND stockmoves.trandate <= '" . $SQLBeforeDate . "'
	AND stockmoves.type = '" .$_POST['move_type']. "'
	AND hidemovt=0
	ORDER BY stkmoveno DESC";
// bowikaxu realhost EXCEL
if($_POST['ShowMoves']=='Excel'){
	require ("includes/class-excel-xml.inc.php");
	$ii=2;
	$xls = new Excel_XML;

	$sql =$SQLMAin;
		
	//echo $sql."<BR>";
	$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because');
	$MovtsResult = DB_query($sql, $db,$ErrMsg);
	
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	$i=0;
	$doc = array();
	$fila=array();
	$fila[]=_('Item Code');
	$fila[]=_('Id Agrupador');
	
	while($filas=DB_fetch_assoc($res)){
		$i++;
		$fila[]= _('Categoria') . ' '.$i;
	}
	$fila[]= _('C&oacute;digo de barras');
	$fila[]= _('Type');
	$fila[]=_('Trans No');
	$fila[]=_('Date');
	$fila[]=_('Quantity');
	$fila[]=_('Price');
	$doc[1] =$fila;
	//$doc = array(1=>array(_('Item Code'),_('Id Agrupador'),_('Familia'), _('C&oacute;digo de barras'), _('Type'),_('Trans No'),_('Date'),_('Quantity'),_('Price')));
	while ($myrow=DB_fetch_assoc($MovtsResult)) {
		$fila=array();
		$fila[]=strtoupper($myrow['stockid']);
		$fila[]=$myrow['id_agrupador'];
		$myrow['familia']='';
		foreach($myrow as $llave=>$valor){
			if(strpos(' '.$llave,'Categoria')){
				$fila[]=htmlentities($valor);
			}
		}
		
		$fila[]= "'".trim($myrow['barcode']);
		$fila[]= $myrow['typename'];
		$fila[]=$myrow['transno'];
		$fila[]=$myrow['trandate'];
		$fila[]=$myrow['qty'];
		$fila[]=$myrow['price'];
		$doc[$ii] = $fila;
		//$xls->addRow();
		$ii++;
	}
	//print_r($doc);
	/*
	$doc = array (
	1 => array ("Oliver", "Peter", "Paul"),
	array ("Marlene", "Lucy", "Lina")
	);
	//echo "<BR><BR>";
	//print_r($doc);
	*/
	// generate excel file
	$xls->addArray ( $doc );
	$xls->generateXML ("StockLocMovements");
	exit;

}

	$SelectSysTypes="select typeid type, typename from systypes where typeid in(select type from stockmoves group by type);";

	$SysTypesResult=DB_query($SelectSysTypes, $db);
	
include('includes/header.inc');

echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . SID . '" METHOD=POST>';
ECHO '<CENTER>';

echo '<table>';
echo '<tr>';
echo '<td>';
echo '  ' . _('From Stock Location') . '</td><td><SELECT name="StockLocation"> ';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_POST['StockLocation']) AND $_POST['StockLocation']!='All'){
		if ($myrow['loccode'] == $_POST['StockLocation']){
		     echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		} else {
		     echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
	}
}

	echo '</SELECT>';
	echo '</td>';
	echo '<td>';
	echo _('Tipo Movimiento');
	echo '</td>';
	echo '<td>';
	echo '<select name="move_type" id="move_type">';
		while($SysTypes=DB_fetch_array($SysTypesResult)) {
			echo '<option value="' .$SysTypes['type']. '"';
			if($SysTypes['type']==$_REQUEST['move_type']) echo ' selected=selected ';
			echo '>' .$SysTypes['typename']. '</option>';
		}
	echo '</select>';
echo '</td>';
echo '</tr>';
echo '<tr>';

if (!isset($_POST['BeforeDate']) OR !Is_Date($_POST['BeforeDate'])){
   $_POST['BeforeDate'] = Date($_SESSION['DefaultDateFormat']);
}
if (!isset($_POST['AfterDate']) OR !Is_Date($_POST['AfterDate'])){
   $_POST['AfterDate'] = Date($_SESSION['DefaultDateFormat'], Mktime(0,0,0,Date('m')-1,Date('d'),Date('y')));
}
echo '<td>';
echo ' ' . _('But after') . ': </td><td><INPUT TYPE=TEXT class="date" NAME="AfterDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['AfterDate'] . '">';
echo '</td>';
echo '<td>';
echo ' ' . _('Show Movements before') . '</td><td>: <INPUT TYPE=TEXT class="date" NAME="BeforeDate" SIZE=12 MAXLENGTH=12 Value="' . $_POST['BeforeDate'] . '">';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan=4>';

{
	echo '<table><tr>';	
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	while($fila=DB_fetch_assoc($res)){
		echo '<td>Categoria '.htmlentities($fila['categoria']).' </td>';
	}
	echo '</tr><tr>';
	
	$Familias="";
	$SQL="Select * from rh_familia  group by categoria asc";
	$res=DB_query($SQL,$db);
	$i=0;
	while($fila=DB_fetch_assoc($res)){
		$i++;
		$Familias.='<TD class="tableheader">' . _('Categoria') . ' '.$i.'</TD>';
		
		$SQL="Select * from rh_familia where categoria='".DB_escape_string($fila['categoria'])."'";
		$res2=DB_query($SQL,$db);
		echo '<td><select name="Categoria['.htmlentities($fila['categoria']).']">';
		echo '<option value="">Todos</option>';
		while($fila2=DB_fetch_assoc($res2)){
			echo '<option';
			echo ' value="';
			echo htmlentities($fila2['clave']);
			echo '"';
			if($_REQUEST['Categoria'][$fila['categoria']]==$fila2['clave'])
				echo ' selected=selected ';
			echo '>';
			echo htmlentities('( '.$fila2['clave'].' ) '.$fila2['nombre']);
			echo '</option>';
		}
		echo '</select></td>';	
	} 
	echo '</tr></table>';
	echo '</td>';
	echo '</tr>';	
}
echo '</table>';

echo '<BR>';
echo ' <INPUT TYPE=SUBMIT NAME="ShowMoves" VALUE="' . _('Show Stock Movements') . '">';

// bowikaxu realhost january 2008 - export report to excel
echo ' <INPUT TYPE=SUBMIT NAME="ShowMoves" VALUE="' . _('Excel') . '">';
ECHO '</CENTER>';
echo '<HR>';

$SQLBeforeDate = FormatDateForSQL($_POST['BeforeDate']);
$SQLAfterDate = FormatDateForSQL($_POST['AfterDate']);

$sql =$SQLMAin;

$ErrMsg = _('The stock movements for the selected criteria could not be retrieved because');
$MovtsResult = DB_query($sql, $db,$ErrMsg);

echo '<TABLE CELLPADDING=5 CELLSPACING=4 BORDER=0>';
$tableheader = '<TR>
		<TD class="tableheader">' . _('Item Code') . '</TD>
		<TD class="tableheader">' . _('Id Agrupador') . '</TD>
		' . $Familias . '
		<TD class="tableheader">' . _('C&oacute;digo de barras') . '</TD>
		<TD class="tableheader">' . _('Type') . '</TD>
		<TD class="tableheader">' . _('Trans No') . '</TD>
		<TD class="tableheader">' . _('Date') . '</TD>
		<TD class="tableheader">' . _('Customer') . '</TD>
		<TD class="tableheader">' . _('Quantity') . '</TD>
		<TD class="tableheader">' . _('Reference') . '</TD>
		<TD class="tableheader">' . _('Price') . '</TD>
		<TD class="tableheader">' . _('Discount') . '</TD>
		</TR>';
echo $tableheader;

$j = 1;
$k=0; //row colour counter

while ($myrow=DB_fetch_array($MovtsResult)) {

	if ($k==1){
		echo '<tr bgcolor="#CCCCCC">';
		$k=0;
	} else {
		echo '<tr bgcolor="#EEEEEE">';
		$k=1;
	}

	$DisplayTranDate = ConvertSQLDate($myrow['trandate']);

		$myrow['familia']='';
		foreach($myrow as $llave=>$valor){
			if(strpos(' '.$llave,'Categoria')){
				$myrow['familia'].='<td>'.htmlentities($valor).'</td>';
			}
		}
		printf("<td><a target='_blank' href='StockStatus.php?" . SID . "&StockID=%s'>%s</td>
			<td>%s</td>
			%s
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			strtoupper($myrow['stockid']),
			strtoupper($myrow['stockid']),
			strtoupper($myrow['id_agrupador']),
			($myrow['familia']),
			strtoupper($myrow['barcode']),
			$myrow['typename'],
			$myrow['transno'],
			$DisplayTranDate,
			$myrow['debtorno'],
			number_format($myrow['qty'],
			$myrow['decimalplaces']),
			$myrow['reference'],
			number_format($myrow['price'],2),
			number_format($myrow['discountpercent']*100,2));

	$j++;
	If ($j == 16){
		$j=1;
		echo $tableheader;
	}
//end of page full new headings if
}
//end of while loop

echo '</TABLE><HR>';
echo '</form>';

include('includes/footer.inc');

?>
