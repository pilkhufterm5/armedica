<?php
/* $Revision: 399 $ */



$PageSecurity = 2;

include('includes/session.inc');

$title = _('Search Inventory Items');

include('includes/header.inc');
?>
<style>
<!--
.StorageBins{
vertical-align: top;
overflow: auto;
max-height: 223px;
display: block;
}
-->
</style>
<?php 
include('includes/Wiki.php');

class ClasifyItem{
    private $db;
    function ClasifyItem($db){
        $this->db=$db;
    }

}
/* 2013-05-08 Realhost: Rafael Rojas
 * Se agrega permisos por fragmento de codigo
*/
$_Permisos=getPermisosPagina($_SESSION["UserID"],'SelectProduct.php');
if(!is_array($_Permisos)||count($_Permisos)==0||!isset($_Permisos['Costo']))
{
	_('Costo')._('Utilidad Bruta');
	$_Permisos=array(
			'Costo'=>array(
					'Nombre'=>'Costo',
					'Visible'=>1,
			),
			'TablaUtilidad'=>array(
					'Nombre'=>'Utilidad Bruta',
					'Visible'=>1,
			)
	);
	setPermisosPagina($_Permisos,$_SESSION["UserID"],'SelectProduct.php');
}
if(!isset($_Permisos['TablaStorageBins'])){
	$_Permisos['TablaStorageBins']=array(
					'Nombre'=>'Locaciones',
					'Visible'=>1,
					'Posicion'=>2.5,
					'Html'=>''
			);
	setPermisosPagina($_Permisos,$_SESSION["UserID"],'SelectProduct.php');
}
/*
 *
*/

//$Clasify = new ClasifyItem($db);

$msg='';

if (isset($_GET['StockID'])){  //The page is called with a StockID
	$_GET['StockID'] = trim(strtoupper($_GET['StockID']));
	$_POST['Select'] = trim(strtoupper($_GET['StockID']));
}

if (isset($_GET['NewSearch'])){
	unset($StockID);
	unset($_SESSION['SelectedStockItem']);
	unset($_POST['Select']);
}

if (!isset($_POST['PageOffset'])) {
	$_POST['PageOffset'] = 1;
} else {
	if ($_POST['PageOffset']==0) {
		$_POST['PageOffset'] = 1;
	}
}

if( isset($_POST['StockCode']) ) {
    $_POST['StockCode'] = trim(strtoupper($_POST['StockCode']));
}

// Always show the search facilities

$SQL='SELECT categoryid,
		categorydescription
	FROM stockcategory
	ORDER BY categorydescription';

$result1 = DB_query($SQL,$db);
if (DB_num_rows($result1)==0){
	echo '<P><FONT SIZE=4 COLOR=RED>' . _('Problem Report') . ':</FONT><BR>' . _('There are no stock categories currently defined please use the link below to set them up');
	echo '<BR><A HREF="' . $rootpath . '/StockCategories.php?' . SID .'">' . _('Define Stock Categories') . '</A>';
	exit;
}

?>
<CENTER>
<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<B><?php echo $msg; ?></B>
<TABLE>
<TR>
<TD><?php echo _('In Stock Category'); ?>:
<SELECT NAME="StockCat">
<?php
	if (!isset($_POST['StockCat'])){
		$_POST['StockCat']="";
	}
	if ($_POST['StockCat']=="All"){
		echo '<OPTION SELECTED VALUE="All">' . _('All');
	} else {
		echo '<OPTION VALUE="All">' . _('All');
	}
	while ($myrow1 = DB_fetch_array($result1)) {
		if ($myrow1['categoryid']==$_POST['StockCat']){
			echo '<OPTION SELECTED VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
		} else {
			echo '<OPTION VALUE="' . $myrow1['categoryid'] . '">' . $myrow1['categorydescription'];
		}
	}
?>

</SELECT>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('description'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['Keywords'])) {
?>
<INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
<?php
}
?>
</TD>
</TR>
<TR><TD></TD>
<TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><?php echo _('Text in the'); ?> <B><?php echo _('Stock Code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['StockCode'])) {
?>
<INPUT TYPE="Text" NAME="StockCode" value="<?php echo $_POST['StockCode']?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
<!--*******************************************************************************************************************************
Jorge Garcia
05/01/2009 Busqueda por marca del articulo
********************************************************************************************************************************-->
<TR><TD></TD>
<TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><?php echo _('Text in the'); ?> <B><?php echo _('Marca'); ?></B>:</TD>
<TD>
<INPUT TYPE="Text" NAME="rh_marca" SIZE=20 MAXLENGTH=25>
</TD>
</TR>
<!--*******************************************************************************************************************************
Fin Jorge Garcia
********************************************************************************************************************************-->
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>"></CENTER>
<HR>


<?php

// end of showing search facilities

// query for list of record(s)

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){

	if (!isset($_POST['Go']) AND !isset($_POST['Next']) AND !isset($_POST['Previous'])){
		// if Search then set to first page
   		 $_POST['PageOffset'] = 1;
	}
	
	If ($_POST['Keywords'] AND $_POST['StockCode']) {
		$msg=_('Stock description keywords have been used in preference to the Stock code extract entered');
	}
	If ($_POST['Keywords']) {
		//insert wildcard characters in spaces
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
		$i=0;
		$SearchString = '%';
		while (strpos($_POST['Keywords'], ' ', $i)) {
			$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
			$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
			$i=strpos($_POST['Keywords'],' ',$i) +1;
		}
		$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND stockmaster.description " . LIKE . " '$SearchString'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.mbflag,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND description " .  LIKE . " '$SearchString'
				AND categoryid='" . DB_escape_string($_POST['StockCat']) . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
	}else{
	if (($_POST['StockCode'])){
		// bowikaxu realhost august '07 - search in barcode field also
		$_POST['StockCode'] = strtoupper($_POST['StockCode']);
		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND (stockmaster.stockid " . LIKE . " '%" . DB_escape_string($_POST['StockCode']) . "%'
				OR stockmaster.barcode " . LIKE . " '%".DB_escape_string($_POST['StockCode']) ."%')
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";

		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					sum(locstock.quantity) as qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND (stockmaster.stockid " . LIKE . " '%" . DB_escape_string($_POST['StockCode']) . "%'
				OR stockmaster.barcode " . LIKE . " '%".DB_escape_string($_POST['StockCode']) ."%') 
				AND categoryid='" . DB_escape_string($_POST['StockCat']) . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
/****************************************************************************************************************************
* Jorge Garcia
* 29/Ene/2009 Busqueda por marca
****************************************************************************************************************************/
	}else{
	if ($_POST['rh_marca']) {
		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock,
					rh_marca
				WHERE stockmaster.stockid=locstock.stockid
				AND rh_marca.nombre LIKE '%".DB_escape_string($_POST['rh_marca'])."%'
				AND stockmaster.rh_marca = rh_marca.id
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock,
					rh_marca
				WHERE stockmaster.stockid=locstock.stockid
				AND categoryid='" . DB_escape_string($_POST['StockCat']) . "'
				AND rh_marca.nombre LIKE '%".DB_escape_string($_POST['rh_marca'])."%'
				AND stockmaster.rh_marca = rh_marca.id
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/
	}else{
	if (!($_POST['StockCode']) AND !($_POST['Keywords']) AND !$_POST['rh_marca']) {
		if ($_POST['StockCat'] == 'All'){
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		} else {
			$SQL = "SELECT stockmaster.stockid,
					stockmaster.description,
					stockmaster.longdescription,
					stockmaster.mbflag,
					SUM(locstock.quantity) AS qoh,
					stockmaster.units,
					stockmaster.materialcost,
					stockmaster.labourcost,
					stockmaster.overheadcost
				FROM stockmaster,
					locstock
				WHERE stockmaster.stockid=locstock.stockid
				AND categoryid='" . DB_escape_string($_POST['StockCat']) . "'
				GROUP BY stockmaster.stockid,
					stockmaster.description,
					stockmaster.units,
					stockmaster.mbflag
				ORDER BY stockmaster.stockid";
		}
	}}}}
	$ErrMsg = _('No stock items were returned by the SQL because');
	$Dbgmsg = _('The SQL that returned an error was');
	$result = DB_query($SQL,$db,$ErrMsg,$DbgMsg);
    //echo $SQL;
	if (DB_num_rows($result)==0){
		prnMsg(_('No stock items were returned by this search please re-enter alternative criteria to try again'),'info');
	} elseif (DB_num_rows($result)==1){ /*autoselect it to avoid user hitting another keystroke */
		$myrow = DB_fetch_row($result);
		$_POST['Select'] = $myrow[0];
	}
	unset($_POST['Search']);
}

// end query for list of records

// display list if there is more than one record

if (isset($result) AND !isset($_POST['Select'])) {

	$ListCount = DB_num_rows($result);
	if ($ListCount > 0) {
	// If the user hit the search button and there is more than one item to show

		$ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

		if (isset($_POST['Next'])) {
			if ($_POST['PageOffset'] < $ListPageMax) {
				$_POST['PageOffset'] = $_POST['PageOffset'] + 1;
			}
		}

		if (isset($_POST['Previous'])) {
			if ($_POST['PageOffset'] > 1) {
				$_POST['PageOffset'] = $_POST['PageOffset'] - 1;
			}
		}

		if ($_POST['PageOffset']>$ListPageMax){
			$_POST['PageOffset'] = $ListPageMax;
		}
		if ($ListPageMax >1) {
			echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';

			echo "<SELECT NAME='PageOffset' id='PageOffset' onchange=document.getElementById('PageOffset2').value=this.value>";

			$ListPage=1;
			while($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
				} else {
					echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
				}
				$ListPage++;
			}
			echo '</SELECT>
				<INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
				<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
				<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
			echo '<P>';
		}

		echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>';
		$tableheader = '<TR>
					<TD class="tableheader">' . _('Code') . '</TD>
					<TD class="tableheader">' . _('Description') . '</TD>
					<TD class="tableheader">' . _('Description') . ' (' . _('long') . ')</TD>
					<TD class="tableheader">' . _('Total Qty On Hand') . '</TD>
					<TD class="tableheader">' . _('Units') . '</TD>
					<TD class="tableheader">' . _('Cost') . '</TD>
					<TD class="tableheader">' . _('Prices') . '</TD>
				</TR>';
		echo $tableheader;

		$j = 1;

		$k = 0; //row counter to determine background colour

	$RowIndex = 0;

	if (DB_num_rows($result)<>0){
		DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
	}

		while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

			if ($k==1){
				echo '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				echo '<tr bgcolor="#EEEEEE">';
				$k++;
			}

			if ($myrow['mbflag']=='D') {
				$qoh = 'N/A';
			} else {
				$qoh = number_format($myrow["qoh"],1);
			}
			
			//rleal Aug 16, 2011
			//Se agregan los precios al resultado bœsqueda
			$rh_PriceResult = DB_query("SELECT typeabbrev, ((prices.price*rh_sales_factors.factor)/currencies.rate)as price2,(prices.price/currencies.rate) as price,prices.debtorno,prices.price as precioNeto,prices.currabrev as curr FROM prices join stockmaster on stockmaster.stockid=prices.stockid ".
                                "join rh_sales_factors on stockmaster.rh_sales_factor=rh_sales_factors.id  ".
								"left join currencies on currencies.currabrev=prices.currabrev "
								."WHERE prices.stockid='".$myrow['stockid']."'",
								$db);
		$rh_text='<table border=0><tr>';
	if (DB_num_rows($rh_PriceResult)==0){
		$rh_text.='<td>'. _('No Price Set');
		$Price =0;
	} else {
		$Cost=$myrow['materialcost']+$myrow['overheadcost']+$myrow['labourcost'];
		$PriceRow = DB_fetch_row($rh_PriceResult);
		$Price = $PriceRow[1];
        $Price2 = $PriceRow[2];
        $PriceNeto = $PriceRow[4];
        $Curr = $PriceRow[5];
		$rh_text.= '<TD align=right class="tableheader">'.$PriceRow[0].(trim($PriceRow[3])!=""?" ({$PriceRow[3]})":"").'</TD><TD align=right>'.$Curr.'</TD>';
			if($_Permisos['TablaUtilidad']['Visible']==1){
	            //<TD align=right>'.number_format($Price,2).'</TD>
				$rh_text.= '<TD align=right class="tableheader">' . _('Gross Profit') . '</TD><TD align=right>';
				if ($Price2 >0&&$Cost>0) {
						$GP = number_format(($Price - $Cost)*100/$Cost,2);
				} else {
					$GP=_('N/A');
				}
				$rh_text.= $GP.'%'. '</TD>';
			}
			echo '</TR>';
			//$rh_text.= '</TD></TR>';
		while ($PriceRow = DB_fetch_row($rh_PriceResult)) {
			$Price = $PriceRow[1];
            $Price2 = $PriceRow[2];
            $PriceNeto = $PriceRow[4];
            $Curr = $PriceRow[5];
			$rh_text.= '<TR><TD align=right class="tableheader">'.$PriceRow[0].'</TD><TD align=right>'.number_format($PriceNeto,2).'</TD><TD align=right>'.$Curr.'</TD>';
			if($_Permisos['TablaUtilidad']['Visible']==1){
	            //<TD align=right>'.number_format($Price,2).'</TD>
				$rh_text.= '<TD align=right class="tableheader">' . _('Gross Profit') . '</TD><TD align=right>';
				if ($Price2 >0&&$Cost>0) {
					$GP = number_format(($Price - $Cost)*100/$Cost,2);
				} else {
					$GP=_('N/A');
				}
				$rh_text.= $GP.'%'. '</TD>';
			}
			echo '</TR>';
		}
		
	}
	$rh_text.= '</TD></TR></table>';
			printf("<td><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=LEFT>%s</td>
				</tr>",
				$myrow['stockid'],
				$myrow['description'],
				$myrow['longdescription'],
				$qoh,
				$myrow['units'],
				number_format(($myrow['materialcost']+$myrow['overheadcost']+$myrow['labourcost']),2),
				$rh_text);

			$j++;
			If ($j == 20 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
				$j=1;
				echo $tableheader;

			}
	$RowIndex = $RowIndex + 1;
	//end of page full new headings if
		}
	//end of while loop

		echo '</TABLE>';
		
		if ($ListPageMax >1) {
			echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';

			echo "<SELECT NAME='PageOffset' id='PageOffset2' onchange=document.getElementById('PageOffset').value=this.value>";

			$ListPage=1;
			while($ListPage <= $ListPageMax) {
				if ($ListPage == $_POST['PageOffset']) {
					echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
				} else {
					echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
				}
				$ListPage++;
			}
			echo '</SELECT>
				<INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
				<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
				<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
			echo '<P>';
		}
	}
}
// end display list if there is more than one record

// displays item options if there is one and only one selected
?>
<script language="javascript">
function link_popup(enlace) {
      features='width=400, height=400,status=0, menubar=0,toolbar=0, scrollbars=0';
      var a = window.open(enlace.getAttribute('href'), 'ventanaClas', features);

}
</script>
<?php

If (!isset($_POST['Search']) AND (isset($_POST['Select']) OR isset($_SESSION['SelectedStockItem']))) {

	if (isset($_POST['Select'])){
		$_SESSION['SelectedStockItem']= $_POST['Select'];
		$StockID = $_POST['Select'];
		unset($_POST['Select']);
	} else {
		$StockID = $_SESSION['SelectedStockItem'];
	}

	$result = DB_query("SELECT stockmaster.description,
							stockmaster.mbflag,
							stockmaster.units,
							stockmaster.decimalplaces,
							stockmaster.controlled,
							stockmaster.serialised,
							stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost AS cost,
							stockmaster.discontinued,
							stockmaster.eoq,
							stockmaster.volume,
							stockmaster.lowestlevel,
							stockmaster.kgs,
                            stockcategory.categorydescription,
                            rh_sales_factors.factor,
                            rh_sales_factors.name
   							FROM stockmaster join rh_sales_factors on stockmaster.rh_sales_factor=rh_sales_factors.id
                                join stockcategory on stockcategory.categoryid=stockmaster.categoryid
                                 WHERE stockmaster.stockid='" . DB_escape_string($StockID) . "'",$db);
	$myrow = DB_fetch_array($result);
	$Its_A_Kitset_Assembly_Or_Dummy=False;
	$Its_A_Dummy=False;
	$Its_A_Kitset=False;

	$col=4;
	if($_Permisos['TablaStorageBins']['Visible']==1){
		$col++;
		$_Permisos['TablaStorageBins']['Html']='';
		
		$sql="Select * from rh_storagebins_stock sbs join rh_storagebins sb on sbs.storageid=sb.id where sbs.stockid ='".DB_escape_string($StockID)."'";
		if($resultado= DB_query($sql,$db))
			if($fila=DB_fetch_assoc($resultado)){
				$_Permisos['TablaStorageBins']['Html'].='<table>';
				$_Permisos['TablaStorageBins']['Html'].='<tr>';
						$_Permisos['TablaStorageBins']['Html'].='<th>';
							$_Permisos['TablaStorageBins']['Html'].=_('Descripci&oacute;n');
						$_Permisos['TablaStorageBins']['Html'].='</th>';
						$_Permisos['TablaStorageBins']['Html'].='<th>';
							$_Permisos['TablaStorageBins']['Html'].=_('Pasillo');
						$_Permisos['TablaStorageBins']['Html'].='</th>';
						$_Permisos['TablaStorageBins']['Html'].='<th>';
							$_Permisos['TablaStorageBins']['Html'].=_('Anaquel');
						$_Permisos['TablaStorageBins']['Html'].='</th>';
						$_Permisos['TablaStorageBins']['Html'].='<th>';
							$_Permisos['TablaStorageBins']['Html'].=_('Fila');
						$_Permisos['TablaStorageBins']['Html'].='</th>';
						$_Permisos['TablaStorageBins']['Html'].='<th>';
							$_Permisos['TablaStorageBins']['Html'].=_('Almacen');
						$_Permisos['TablaStorageBins']['Html'].='</th>';
					$_Permisos['TablaStorageBins']['Html'].='</tr>';
				do{
					
					$_Permisos['TablaStorageBins']['Html'].='<tr>';
						$_Permisos['TablaStorageBins']['Html'].='<td>';
							$_Permisos['TablaStorageBins']['Html'].=htmlentities($fila['description']);
						$_Permisos['TablaStorageBins']['Html'].='</td>';
						$_Permisos['TablaStorageBins']['Html'].='<td>';
							$_Permisos['TablaStorageBins']['Html'].=htmlentities($fila['way']);
						$_Permisos['TablaStorageBins']['Html'].='</td>';
						$_Permisos['TablaStorageBins']['Html'].='<td>';
							$_Permisos['TablaStorageBins']['Html'].=htmlentities($fila['level']);
						$_Permisos['TablaStorageBins']['Html'].='</td>';
						$_Permisos['TablaStorageBins']['Html'].='<td>';
							$_Permisos['TablaStorageBins']['Html'].=htmlentities($fila['position']);
						$_Permisos['TablaStorageBins']['Html'].='</td>';
						$_Permisos['TablaStorageBins']['Html'].='<td>';
							$_Permisos['TablaStorageBins']['Html'].=htmlentities($fila['location']);
						$_Permisos['TablaStorageBins']['Html'].='</td>';
					$_Permisos['TablaStorageBins']['Html'].='</tr>';
				}while($fila=DB_fetch_assoc($resultado));
				$_Permisos['TablaStorageBins']['Html'].='</table>';
			}
	}
	
	
	// bowikaxu realhost Feb 2008 - center product info table
	echo "<CENTER>";
	
	echo '<TABLE BORDER=1><TR><TD colspan='.$col.' class="tableheader"><font size=4>' . $StockID . ' - ' . $myrow['description'] . ' </font></TD></TR>';

	echo '<TR>';
	if($_Permisos['TablaStorageBins']['Visible']==1&&$_Permisos['TablaStorageBins']['Posicion']==1){
		echo '<td class="StorageBins" rowspan=5 >'.$_Permisos['TablaStorageBins']['Html'].'</td>';
	}
	echo '<TD WIDTH="60%">
			<TABLE>'; //nested table

	echo '<TR><TD align=right class="tableheader">' . _('Item type:') . '</TD><TD COLSPAN=2>';

	switch ($myrow['mbflag']) {
		case 'A':
			echo _('Assembly Item');
			$Its_A_Kitset_Assembly_Or_Dummy=True;
			break;
		case 'E': // bowikaxu realhost 15 july 2008 - ensamblado costo por componente
			echo _('Assembly Item').' '._('Costo por Componente');
			$Its_A_Kitset_Assembly_Or_Dummy=True;
			break;
		case 'K':
			echo _('Kitset Item');
			$Its_A_Kitset_Assembly_Or_Dummy=True;
			$Its_A_Kitset=True;
			break;
		case 'D':
			echo _('Service Item');
			$Its_A_Kitset_Assembly_Or_Dummy=True;
			$Its_A_Dummy=True;
			break;
		case 'B':
			echo _('Purchased Item');
			break;
		default:
			echo _('Manufactured Item');
			break;
	}
	echo '</TD><TD align=right class="tableheader">' . _('Control Level:') .'</TD><TD>';
	if ($myrow['serialised']==1){
		echo _('serialised');
	} elseif ($myrow['controlled']==1){
		echo _('Batchs/Lots');
	} else {
		echo _('N/A');
	}
	echo '</TD><TD align=right class="tableheader">' . _('Units') . ':</TD><TD>' . $myrow['units'] . '</TD></TR>';
    echo '</TD><TD align=right class="tableheader">' . _('Category') . ':</TD><TD>' .$myrow['categorydescription']. '</TD></TR>';
    //echo '</TD><TD align=right class="tableheader">' . _('Factor') . ':</TD><TD>' .$myrow['name'].' '.$myrow['factor'] . '</TD></TR>';
    //echo '</TD><TD align=right class="tableheader">' . _('Clasificacion') . ':</TD><TD><A target="_blank" onclick="link_popup(this); return false;" href="'.$rootpath . '/rh_history_clasify.php?' . SID . '&stock=' . $StockID.'"><strong>'.$myrow['clasify']. '</strong></a></TD></TR>';

	echo '<TR><TD align=right class="tableheader">' . _('Volume') . ':</TD><TD align=right COLSPAN=2>' . number_format($myrow['volume'],3) . '</TD>
			<td></td><TD align=right class="tableheader">' . _('Weight') . ':</TD><TD align=right>' . number_format($myrow['kgs'],3) . '</TD>
			<TD align=right class="tableheader">' . _('EOQ') . ':</TD><TD align=right>' . number_format($myrow['eoq'],$myrow['decimalplaces']) . '</TD></TR>';

	echo '<TR><TD class="tableheader">' . _('Sell Price') . ':</TD><TH>';

	// bowikaxu realhost - 15 july 2008 - ensamblado costo por componente
	if ($myrow['mbflag']=='K' OR $myrow['mbflag']=='A' OR $myrow['mbflag']=='E' OR $myrow['mbflag']=='M'){
		$CostResult = DB_query("SELECT SUM(bom.quantity*
						(stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost)) AS cost
					FROM bom INNER JOIN
						stockmaster
					ON bom.component=stockmaster.stockid
					WHERE bom.parent='" . $StockID . "'
					AND bom.effectiveto >= '" . Date("Y-m-d") . "'
					AND bom.effectiveafter <= '" . Date("Y-m-d") . "'",
					$db);
		$CostRow = DB_fetch_row($CostResult);
		$Cost = $CostRow[0];
	} else {
		$Cost = $myrow['cost'];
	}

	/*$PriceResult = DB_query("SELECT typeabbrev, (prices.price*rh_sales_factors.factor)as price2,prices.price,prices.debtorno  FROM prices join stockmaster on stockmaster.stockid=prices.stockid
                                                        join rh_sales_factors on stockmaster.rh_sales_factor=rh_sales_factors.id  "
//								WHERE currabrev ='" . $_SESSION['CompanyRecord']['currencydefault'] . "'
//								AND typeabbrev = '" . $_SESSION['DefaultPriceList'] . "'
//								AND debtorno=''
//								AND branchcode=''
								."WHERE prices.stockid='".$StockID."'",
								$db);  */
    $PriceResult = DB_query("SELECT typeabbrev, ((prices.price*rh_sales_factors.factor)/currencies.rate)as price2,(prices.price/currencies.rate) as price,prices.debtorno,prices.price as precioNeto,prices.currabrev as curr FROM prices join stockmaster on stockmaster.stockid=prices.stockid ".
                                "join rh_sales_factors on stockmaster.rh_sales_factor=rh_sales_factors.id  ".
								"left join currencies on currencies.currabrev=prices.currabrev "
								."WHERE prices.stockid='".$StockID."'",
								$db);
	
	if (DB_num_rows($PriceResult)==0){
		echo _('No Price Set');
		$Price =0;
	} else {
		$PriceRow = DB_fetch_row($PriceResult);
		$Price = $PriceRow[1];
        $Price2 = $PriceRow[2];
        $PriceNeto = $PriceRow[4];
        $Curr = $PriceRow[5];
		echo $PriceRow[0].(trim($PriceRow[3])!=""?" ({$PriceRow[3]})":"").'</TH><TD align=right>'.number_format($PriceNeto,2).'</TD><TD align=right>'.$Curr.'</TD>';
			if($_Permisos['TablaUtilidad']['Visible']==1){
				echo 
	            //<TD align=right>'.number_format($Price,2).'</TD>
				'<TD align=right class="tableheader">' . _('Gross Profit') . '</TD><TD align=right>';
				if ($Price2 >0) {
					$GP = number_format(($Price - $Cost)*100/$Cost,2);
				} else {
					$GP=_('N/A');
				}
				echo $GP.'%'. '</TD>';
			}
			echo '</TR>';
			echo '</TD></TR>';
		while ($PriceRow = DB_fetch_row($PriceResult)) {
			$Price = $PriceRow[1];
            $Price2 = $PriceRow[2];
            $PriceNeto = $PriceRow[4];
            $Curr = $PriceRow[5];
			echo '<TR><TD></TD><TH>'.$PriceRow[0].(trim($PriceRow[3])!=""?" ({$PriceRow[3]})":"").'</TH><TD align=right>'.number_format($PriceNeto,2).'</TD><TD align=right>'.$Curr.'</TD>';
			if($_Permisos['TablaUtilidad']['Visible']==1){
				echo 
	            //<TD align=right>'.number_format($Price,2).'</TD>
				'<TD align=right class="tableheader">' . _('Gross Profit') . '</TD><TD align=right>';
				if ($Price2 >0) {
					$GP = number_format(($Price - $Cost)*100/$Cost,2);
				} else {
					$GP=_('N/A');
				}
				echo $GP.'%'. '</TD>';
			}
			echo '</TR>';
			echo '</TD></TR>';
		}
	}
	if($_Permisos['Costo']['Visible']==1)
	echo '<TD align=right class="tableheader">' . _('Cost') . '</TD><TD align=right colspan=2>' . number_format($Cost,3) . '</TD>';
	echo '</TABLE>'; //end of first nested table
	
   // Item Category Property mod: display the item properties
       echo '<table>';
       $CatValResult = DB_query("SELECT categoryid FROM
                                                                               stockmaster
                                                                               WHERE stockid='" . $StockID . "'", $db);
               $CatValRow = DB_fetch_row($CatValResult);
               $CatValue = $CatValRow[0];

       $sql = "SELECT stkcatpropid,
                                       label,
                                       controltype,
                                       defaultvalue
                       FROM stockcatproperties
                       WHERE categoryid ='" . $CatValue . "'
                       AND reqatsalesorder =0
                       ORDER BY stkcatpropid";

       $PropertiesResult = DB_query($sql,$db);
       $PropertyCounter = 0;
       $PropertyWidth = array();

       while ($PropertyRow=DB_fetch_array($PropertiesResult)){

               $PropValResult = DB_query("SELECT value FROM
                                                                               stockitemproperties
                                                                               WHERE stockid='" . $StockID . "'
                                                                               AND stkcatpropid =" . $PropertyRow['stkcatpropid'],
                                                                       $db);
               $PropValRow = DB_fetch_row($PropValResult);
               $PropertyValue = $PropValRow[0];

               echo '<tr><td class="tableheader" align="right">' . $PropertyRow['label']
. ':</td>';
               switch ($PropertyRow['controltype']) {
                       case 0; //textbox
                               echo '<td align=right width=60>' . $PropertyValue;
                               break;
                       case 1; //select box
                               $OptionValues = explode(',',$PropertyRow['defaultvalue']);
                               echo '<select name="PropValue' . $PropertyCounter . '">';
                               foreach ($OptionValues as $PropertyOptionValue){
                                       if ($PropertyOptionValue == $PropertyValue){
                                               echo '<option selected value="' . $PropertyOptionValue . '">' .
$PropertyOptionValue . '</option>';
                                       } else {
                                               echo '<option value="' . $PropertyOptionValue . '">' .
$PropertyOptionValue . '</option>';
                                       }
                               }
                               echo '</select>';
                               break;
                       case 2; //checkbox
                               echo '<input type="checkbox" name="PropValue' . $PropertyCounter . '"';
                               if ($PropertyValue==1){
                                       echo '"checked"';
                               }
                               echo '>';
                               break;
               } //end switch
               echo '</td></tr>';
               $PropertyCounter++;
       } //end loop round properties for the item category
       echo '</table>'; //end of Item Category Property mod
	if($_Permisos['TablaStorageBins']['Visible']==1&&$_Permisos['TablaStorageBins']['Posicion']==2){
		echo '<td class="StorageBins" rowspan=5 >'.$_Permisos['TablaStorageBins']['Html'].'</td>';
	}
	echo '<TD WIDTH="15%">
			<TABLE>'; //nested table to show QOH/orders


	$QOH=0;
	switch ($myrow['mbflag']) {
		case 'A':
		case 'E': // bowikaxu realhost 15 july 2008 - ensamblado costo por componente
 		case 'D':
		case 'K':
			$QOH=_('N/A');
			$QOO =_('N/A');
			break;
		case 'M':
		// bowikaxu realhost - april 2008 - get stock quantity if flag = costo por componente
		case 'C':
		case 'B':
			$QOHResult = DB_query("SELECT sum(quantity)
                            			FROM locstock
                        				WHERE stockid = '" . $StockID . "'",
                        				$db);
            $QOHRow = DB_fetch_row($QOHResult);
            $QOH = number_format($QOHRow[0],$myrow['decimalplaces']);

            $QOOResult = DB_query("SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd)
                   					FROM purchorderdetails
                   					WHERE purchorderdetails.itemcode='" . $StockID . "'",
                   					$db);
			if (DB_num_rows($QOOResult)==0){
				$QOO=0;
			} else {
				$QOORow = DB_fetch_row($QOOResult);
				$QOO = $QOORow[0];
			}
			//Also the on work order quantities
			$sql = "SELECT SUM(woitems.qtyreqd-woitems.qtyrecd) AS qtywo
				FROM woitems INNER JOIN workorders
				ON woitems.wo=workorders.wo
				WHERE workorders.closed=0
				AND woitems.stockid='" . $StockID . "'";
			$ErrMsg = _('The quantity on work orders for this product cannot be retrieved because');
			$QOOResult = DB_query($sql,$db,$ErrMsg);

			if (DB_num_rows($QOOResult)==1){
				$QOORow = DB_fetch_row($QOOResult);
				$QOO +=  $QOORow[0];
			}
			$QOO = number_format($QOO,$myrow['decimalplaces']);
			break;
	}
	$Demand =0;
	$DemResult = DB_query("SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                 					FROM salesorderdetails INNER JOIN salesorders
                 					ON salesorders.orderno = salesorderdetails.orderno
                 					WHERE salesorderdetails.completed=0
		 							AND salesorders.quotation=0
                 					AND salesorderdetails.stkcode='" . $StockID . "'",
                 			$db);

    $DemRow = DB_fetch_row($DemResult);
    $Demand = $DemRow[0];
	$DemAsComponentResult =	DB_query("SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                 						FROM salesorderdetails,
	                  						salesorders,
    						                bom,
                      						stockmaster
                 						WHERE salesorderdetails.stkcode=bom.parent AND
                       						salesorders.orderno = salesorderdetails.orderno AND
                                            salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0 AND
                       						bom.component='" . $StockID . "' AND stockmaster.stockid=bom.parent AND
                       						(stockmaster.mbflag='A' OR stockmaster.mbflag='E')
		       								AND salesorders.quotation=0",
		       							$db);
	$DemAsComponentRow = DB_fetch_row($DemAsComponentResult);
	$Demand += $DemAsComponentRow[0];
	//Also the demand for the item as a component of works orders

	$sql = "SELECT SUM(qtypu*(woitems.qtyreqd - woitems.qtyrecd)) AS woqtydemo
				FROM woitems INNER JOIN worequirements
				ON woitems.stockid=worequirements.parentstockid
				INNER JOIN workorders
				ON woitems.wo=workorders.wo
				AND woitems.wo=worequirements.wo
				WHERE  worequirements.stockid='" . $StockID . "'
				AND workorders.closed=0";

	$ErrMsg = _('The workorder component demand for this product cannot be retrieved because');
	$DemandResult = DB_query($sql,$db,$ErrMsg);

	if (DB_num_rows($DemandResult)==1){
		$DemandRow = DB_fetch_row($DemandResult);
		$Demand += $DemandRow[0];
	}

	echo '<TR><TD align=right width="15%" class="tableheader">' . _('Quantity On Hand') . ':</TD><TD width="17%" align=right>' . $QOH . '</TD></TR>';
	echo '<TR><TD align=right width="15%" class="tableheader">' . _('Quantity Demand') . ':</TD><TD width="17%" align=right>' . number_format($Demand,$myrow['decimalplaces']) . '</TD></TR>';
	echo '<TR><TD align=right width="15%" class="tableheader">' . _('Quantity On Order') . ':</TD><TD width="17%" align=right>' . $QOO . '</TD></TR>';
	if($_Permisos['TablaStorageBins']['Visible']==1&&$_Permisos['TablaStorageBins']['Posicion']==2.5){
		echo '<tr><td class="StorageBins" rowspan=5 >'.$_Permisos['TablaStorageBins']['Html'].'</td></tr>';
	}
	echo '</TABLE>';//end of nested table
	if($_Permisos['TablaStorageBins']['Visible']==1&&$_Permisos['TablaStorageBins']['Posicion']==3){
		echo '<td class="StorageBins" rowspan=5 >'.$_Permisos['TablaStorageBins']['Html'].'</td>';
	}
    echo '</TD>'; //end cell of master table
    if ($myrow['mbflag']=='B'){
		echo '<TD WIDTH="40%" VALIGN="TOP"><TABLE>
			<TR><TD width="50%" class="tableheader">' . _('Supplier') . '</TD>
				<TD width="20%" class="tableheader">' . _('Cost') . '</TD>
				<TD width="10%" class="tableheader">' . _('Currency') . '</TD>
				<TD width="20%" class="tableheader">' . _('Lead Time') . '</TD></TR>';

		$SuppResult = DB_query("SELECT  suppliers.suppname,
								purchdata.price,
								suppliers.currcode,
								purchdata.leadtime,
								purchdata.conversionfactor,
								purchdata.preferred
						FROM purchdata INNER JOIN suppliers
						ON purchdata.supplierno=suppliers.supplierid
						WHERE purchdata.stockid = '" . $StockID . "'",
						$db);
		while ($SuppRow = DB_fetch_array($SuppResult)){
			echo '<TR><TD>' . $SuppRow['suppname'] . '</TD>
						<TD align=right>' . number_format($SuppRow['price']/$SuppRow['conversionfactor'],2) . '</TD>
						<TD>' . $SuppRow['currcode'] . '</TD>
						<TD>' . $SuppRow['leadtime'] . '</TD></TR>';

		}
		echo '</TR></TABLE></TD>';
	}
	if($_Permisos['TablaStorageBins']['Visible']==1&&$_Permisos['TablaStorageBins']['Posicion']==4){
		echo '<td class="StorageBins" rowspan=5 >'.$_Permisos['TablaStorageBins']['Html'].'</td>';
	}
	echo '</TR></TABLE><HR>'; // end first item details table

/*
 * rleal
 * Se agrega info a esta pagina
 */
$result = DB_query("SELECT description,
                           units,
                           mbflag,
                           decimalplaces,
                           serialised,
                           controlled
                    FROM
                           stockmaster
                    WHERE
                           stockid='$StockID'",
                           $db,
                           _('Could not retrieve the requested item'),
                           _('The SQL used to retrieve the items was'));

$myrow = DB_fetch_row($result);

$DecimalPlaces = $myrow[3];
$Serialised = $myrow[4];
$Controlled = $myrow[5];

$sql = "SELECT locstock.loccode,
               locations.locationname,
               locstock.quantity,
               locstock.reorderlevel,
	       locations.managed
               FROM locstock,
                    locations
               WHERE locstock.loccode=locations.loccode AND
                     locstock.stockid = '" . $StockID . "'
               ORDER BY locstock.loccode";

$ErrMsg = _('The stock held at each location cannot be retrieved because');
$DbgMsg = _('The SQL that was used to update the stock item and failed was');
$LocStockResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);
/*
echo '<TABLE CELLPADDING=2 BORDER=0>';

if ($Its_A_KitSet_Assembly_Or_Dummy == True){
	$tableheader = '<TR>
			<TH>' . _('Location') . '</TH>
			<TH>' . _('Demand') . '</TH>
			</TR>';
} else {
	$tableheader = '<TR>
			<TH>' . _('Location') . '</TH>
			<TH>' . _('Quantity On Hand') . '</TH>
			<TH>' . _('Re-Order Level') . '</FONT></TH>
			<TH>' . _('Demand') . '</TH>
			<TH>' . _('Available') . '</TH>
			<TH>' . _('On Order') . '</TH>
			</TR>';
}
echo $tableheader;
$j = 1;
$k=0; //row colour counter

while ($myrow=DB_fetch_array($LocStockResult)) {

	if ($k==1){
		echo '<tr class="EvenTableRows">';
		$k=0;
	} else {
		echo '<tr class="OddTableRows">';
		$k=1;
	}

	$sql = "SELECT SUM(salesorderdetails.quantity-salesorderdetails.qtyinvoiced) AS dem
                 FROM salesorderdetails,
                      salesorders
                 WHERE salesorders.orderno = salesorderdetails.orderno AND
                 salesorders.fromstkloc='" . $myrow['loccode'] . "' AND
                 salesorderdetails.completed=0 AND
		 salesorders.quotation=0 AND
                 salesorderdetails.stkcode='" . $StockID . "'";

	$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
	$DemandResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($DemandResult)==1){
	  $DemandRow = DB_fetch_row($DemandResult);
	  $DemandQty =  $DemandRow[0];
	} else {
	  $DemandQty =0;
	}

	//Also need to add in the demand as a component of an assembly items if this items has any assembly parents.
	$sql = "SELECT SUM((salesorderdetails.quantity-salesorderdetails.qtyinvoiced)*bom.quantity) AS dem
                 FROM salesorderdetails,
                      salesorders,
                      bom,
                      stockmaster
                 WHERE salesorderdetails.stkcode=bom.parent AND
                       salesorders.orderno = salesorderdetails.orderno AND
                       salesorders.fromstkloc='" . $myrow['loccode'] . "' AND
                       salesorderdetails.quantity-salesorderdetails.qtyinvoiced > 0 AND
                       bom.component='" . $StockID . "' AND stockmaster.stockid=bom.parent AND
                       stockmaster.mbflag='A'
		       AND salesorders.quotation=0";

	$ErrMsg = _('The demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
	$DemandResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($DemandResult)==1){
		$DemandRow = DB_fetch_row($DemandResult);
		$DemandQty += $DemandRow[0];
	}

	//Also the demand for the item as a component of works orders

	$sql = "SELECT SUM(qtypu*(woitems.qtyreqd - woitems.qtyrecd)) AS woqtydemo
				FROM woitems INNER JOIN worequirements
				ON woitems.stockid=worequirements.parentstockid
				INNER JOIN workorders
				ON woitems.wo=workorders.wo
				AND woitems.wo=worequirements.wo
				WHERE workorders.loccode='" . $myrow['loccode'] . "'
				AND worequirements.stockid='" . $StockID . "'
				AND workorders.closed=0";

	$ErrMsg = _('The workorder component demand for this product from') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
	$DemandResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

	if (DB_num_rows($DemandResult)==1){
		$DemandRow = DB_fetch_row($DemandResult);
		$DemandQty += $DemandRow[0];
	}

	if ($Its_A_KitSet_Assembly_Or_Dummy == False){

		$sql = "SELECT SUM(purchorderdetails.quantityord - purchorderdetails.quantityrecd) AS qoo
                   	FROM purchorderdetails
                   	INNER JOIN purchorders ON purchorderdetails.orderno=purchorders.orderno
                   	WHERE purchorders.intostocklocation='" . $myrow['loccode'] . "' AND
                   	purchorderdetails.itemcode='" . $StockID . "'";
		$ErrMsg = _('The quantity on order for this product to be received into') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$QOOResult = DB_query($sql,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($QOOResult)==1){
			$QOORow = DB_fetch_row($QOOResult);
			$QOO =  $QOORow[0];
		} else {
			$QOO = 0;
		}

		//Also the on work order quantities
		$sql = "SELECT SUM(woitems.qtyreqd-woitems.qtyrecd) AS qtywo
				FROM woitems INNER JOIN workorders
				ON woitems.wo=workorders.wo
				WHERE workorders.closed=0
				AND workorders.loccode='" . $myrow['loccode'] . "'
				AND woitems.stockid='" . $StockID . "'";
		$ErrMsg = _('The quantity on work orders for this product to be received into') . ' ' . $myrow['loccode'] . ' ' . _('cannot be retrieved because');
		$QOOResult = DB_query($sql,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($QOOResult)==1){
			$QOORow = DB_fetch_row($QOOResult);
			$QOO +=  $QOORow[0];
		}

		echo '<TD>' . $myrow['locationname'] . '</TD>';

		printf("<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>
			<td ALIGN=RIGHT>%s</td>",
			number_format($myrow['quantity'], $DecimalPlaces),
			number_format($myrow['reorderlevel'], $DecimalPlaces),
			number_format($DemandQty, $DecimalPlaces),
			number_format($myrow['quantity'] - $DemandQty, $DecimalPlaces),
			number_format($QOO, $DecimalPlaces)
			);

		if ($Serialised ==1){ /*The line is a serialised item*/
/*
			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockSerialItems.php?' . SID . '&Serialised=Yes&Location=' . $myrow['loccode'] . '&StockID=' .$StockID . '">' . _('Serial Numbers') . '</A></TD></TR>';
		} elseif ($Controlled==1){
			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockSerialItems.php?' . SID . '&Location=' . $myrow['loccode'] . '&StockID=' .$StockID . '">' . _('Batches') . '</A></TD></TR>';
		}

	} else {
	/* It must be a dummy, assembly or kitset part */
/*
		printf("<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['locationname'],
			number_format($DemandQty, $DecimalPlaces)
			);
	}

//end of page full new headings if
}
//end of while loop
//echo '</TABLE><HR>';

/*
 * fin modificacion
 */


	echo "</CENTER>";

	echo '<TABLE WIDTH="100%" BORDER=1><TR>
		<TD WIDTH=33% class="tableheader">' . _('Item Inquiries') . '</TD>
		<TD WIDTH=33% class="tableheader">' . _('Item Transactions') . '</TD>
		<TD WIDTH=33% class="tableheader">' . _('Item Maintenance') . '</TD>
	</TR>';
	echo '<TR><TD valign="top">';

	/*Stock Inquiry Options */

        echo '<A HREF="' . $rootpath . '/StockMovements.php?' . SID . '&StockID=' . $StockID . '">' . _('Show Stock Movements') . '</A><BR>';

	if ($Its_A_Kitset_Assembly_Or_Dummy==False){
        echo '<A HREF="' . $rootpath . '/StockStatus.php?' . SID . '&StockID=' . $StockID . '">' . _('Show Stock Status') . '</A><BR>';
        echo '<A HREF="' . $rootpath . '/StockUsage.php?' . SID . '&StockID=' . $StockID . '">' . _('Show Stock Usage') . '</A><BR>';
	}
        echo '<A HREF="' . $rootpath . '/SelectSalesOrder.php?' . SID . '&SelectedStockItem=' . $StockID . '">' . _('Search Outstanding Sales Orders') . '</A><BR>';
        echo '<A HREF="' . $rootpath . '/SelectCompletedOrder.php?' .SID . '&SelectedStockItem=' . $StockID . '">' . _('Search Completed Sales Orders') . '</A><BR>';
	if ($Its_A_Kitset_Assembly_Or_Dummy==False){
		echo '<A HREF="' . $rootpath . '/PO_SelectOSPurchOrder.php?' . SID . '&SelectedStockItem=' . $StockID . '">' . _('Search Outstanding Purchase Orders') . '</A><BR>';
		echo '<A HREF="' . $rootpath . '/PO_SelectPurchOrder.php?' . SID . '&SelectedStockItem=' . $StockID . '">' . _('Search All Purchase Orders') . '</A><BR>';
		echo '<A HREF="' . $rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $StockID . '.jpg?' . SID . '">' . _('Show Part Picture (if available)') . '</A><BR>';
	}

	if ($Its_A_Dummy==False){
		echo '<A HREF="' . $rootpath . '/BOMInquiry.php?' . SID . '&StockID=' . $StockID . '">' . _('View Costed Bill Of Material') . '</A><BR>';
		echo '<A HREF="' . $rootpath . '/WhereUsedInquiry.php?' . SID . '&StockID=' . $StockID . '">' . _('Where This Item Is Used') . '</A><BR>';
	}

	// bowikaxu realhost january 2008 - view this item price history
	echo '<A HREF="' . $rootpath . '/rh_PriceHistory_Inquiry.php?' . SID . '&StockID=' . $StockID . '">' . _('Price History') . '</A><BR>';
	
	wikiLink('Product', $StockID);

	echo '</TD><TD valign="top">';

	/* Stock Transactions */
	if ($Its_A_Kitset_Assembly_Or_Dummy==False){
		echo '<A HREF="' . $rootpath . '/StockAdjustments.php?' . SID . '&StockID=' . $StockID . '">' . _('Quantity Adjustments') . '</A><BR>';
        echo '<A HREF="' . $rootpath . '/StockTransfers.php?' . SID . '&StockID=' . $StockID . '">' . _('Location Transfers') . '</A><BR>';
	}

	echo '</TD><TD valign="top">';

	/*Stock Maintenance Options */

  echo '<A HREF="' . $rootpath . '/Stocks.php?">' . _('Add Inventory Items') . '</A><BR>';
  echo '<A HREF="' . $rootpath . '/Stocks.php?' . SID . '&StockID=' . $StockID . '">' . _('Modify Item Details') . '</A><BR>';
  

  // bowikaxu realhost - 25 une 2008 - cost update
  if($_SESSION['rh_updatecost']==1){
  	// bowikaxu realhost january 2008 - View Cost
  	echo '<A HREF="' . $rootpath . '/rh_StockCost.php?' . SID . '&StockID=' . $StockID . '">' . _('Ver Costo') . '</A><BR>';
  }
	if ($Its_A_Kitset_Assembly_Or_Dummy==False){
		echo '<A HREF="' . $rootpath . '/StockReorderLevel.php?' . SID . '&StockID=' . $StockID . '">' . _('Maintain Reorder Levels') . '</A><BR>';
        	echo '<A HREF="' . $rootpath . '/StockCostUpdate.php?' . SID . '&StockID=' . $StockID . '">' . _('Maintain Standard Cost') . '</A><BR>';
        	echo '<A HREF="' . $rootpath . '/PurchData.php?' . SID . '&StockID=' . $StockID . '">' . _('Maintain Purchasing Data') . '</A><BR>';
	}
	if (! $Its_A_Kitset){
		echo '<A HREF="' . $rootpath . '/Prices.php?' . SID . '&Item=' . $StockID . '">' . _('Maintain Pricing') . '</A><BR>';
        	if (isset($_SESSION['CustomerID']) AND $_SESSION['CustomerID']!="" AND Strlen($_SESSION['CustomerID'])>0){
			echo '<A HREF="' . $rootpath . '/Prices_Customer.php?' . SID . '&Item=' . $StockID . '">' . _('Special Prices for customer') . ' - ' . $_SESSION['CustomerID'] . '</A><BR>';
        	}
	}
/***********************************************************************************************************************************/
	echo '<A HREF="' . $rootpath . '/rh_miniumprice.php?' . SID . '&StockID=' . $StockID . '">' . _('Maintenance of Minium Price') . '</A><BR>';
/***********************************************************************************************************************************/
	if(isset($_SESSION['StorageBins'])&&$_SESSION['StorageBins']==1){
		echo '<A HREF="' . $rootpath . '/rh_storagebins_stock.php?' . SID . '&StockID=' . $StockID . '">' . _('Storage bins') . '</A><BR>';
	}
	echo '</TD></TR></TABLE>';
	
	

} else {
  // options (links) to pages. This requires stock id also to be passed.
	echo '<CENTER><TABLE WIDTH=90% COLSPAN=2 BORDER=2 CELLPADDING=4>';
	echo '<TR>
		<TD WIDTH=33% class="tableheader">' . _('Item Inquiries') . '</TD>
		<TD WIDTH=33% class="tableheader">' . _('Item Transactions') . '</TD>
		<TD WIDTH=33% class="tableheader">' . _('Item Maintenance') . '</TD>
	</TR>';
	echo '<TR><TD>';

	/*Stock Inquiry Options */

	echo '</TD><TD>';

	/* Stock Transactions */

	echo '</TD><TD>';

	/*Stock Maintenance Options */

  echo '<A HREF="' . $rootpath . '/Stocks.php?">' . _('Add Inventory Items') . '</A><BR>';

echo '</TD></TR></TABLE>';

}// end displaying item options if there is one and only one record

?>
</CENTER>
</FORM>
<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].StockCode.select();
            document.forms[0].StockCode.focus();
            //-->
    //]]>
</script>

<?php
include('includes/footer.inc');
?>
