<?php

/* webERP Revision: 14 $ */

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-08-12 12:21:47 -0500 (mar, 12 ago 2008) $
 * $Rev: 388 $
 */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Supplier Transactions Inquiry');
include('includes/header.inc');
?>
<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
<script type="text/javascript">
$(function(){$('csv').show();})
</script>
<?php 

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";
echo "<OPTION VALUE='todos'>"._('Show All');

$sql = 'SELECT typeid, typename FROM systypes WHERE typeid in (20,22,21)';
$resultTypes = DB_query($sql,$db);

while ($myrow=DB_fetch_array($resultTypes)){
	if (isset($_POST['TransType'])){
		if ($myrow['typeid'] == $_POST['TransType']){
		     echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		}
	} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
	}
}
echo '</SELECT></TD>';

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('From') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('To') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '>';
echo _('Folio');
		echo ':<input type="text" name="FolioFactura" value="'.htmlentities($_REQUEST['FolioFactura']).'" style="width: 8em;"/>';
echo '</TD>';

echo "</TR>
<TR>
<TD>"._('Ordenado')."</TD>
<TD><SELECT NAME='orderby'>
<OPTION SELECT VALUE='date'>"._('Date')."
<OPTION VALUE='name'>"._('Supplier Name')."
<OPTION VALUE='id'>"._('Trans No.')."
</SELECT>
</TD>";

/*
	 * rleal
	 * Jun 23 2011
	 * Se agrega a que almac�n se relaciona
	 */
		echo  '<td colspan=4>'._('From Stock Location') . "(Solo Facturas): <SELECT name='StockLocation'> ";
		
		$sql = 'SELECT loccode, locationname FROM rh_locations';
		
		$resultStkLocs = DB_query($sql,$db);
		echo "<OPTION SELECTED Value='all'>Todos";
		while ($myrow=DB_fetch_array($resultStkLocs)){  
			if (isset($_POST['StockLocation'])){
				if ($myrow['loccode'] == $_POST['StockLocation']){
				     echo "<OPTION SELECTED Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
				} else {
				     echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
				}
			} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
				 echo "<OPTION SELECTED Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
			} else {
				 echo "<OPTION Value='" . $myrow['loccode'] . "'>" . $myrow['locationname'];
			}
		}

		echo '</SELECT> &nbsp&nbsp';
		echo '<td></TR>';
		
		/*fin de modificaci�n*/

echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults' VALUE='" . _('Show Transactions') . "'>";
?>
<csv style="display:none" target="Reporte Proveedor (<?=date('Y-m-d')?>)" title=".TablaProveedor"><button>Excel</button></csv>
<?php 
echo '<HR>';
echo '</FORM></CENTER>';

if (isset($_POST['ShowResults'])){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);
   $Folio='';
	if(isset($_REQUEST['FolioFactura'])){
		$Folio=' AND supptrans.suppreference like "%'.DB_escape_string(trim($_REQUEST['FolioFactura'])).'%"';
	// 	$DateAfterCriteria='0000-00-00';
	}
   if (($_POST['TransType']!=20)) {
   $sql = "SELECT supptrans.transno, 
   					supptrans.type,
   					trandate, 
					supptrans.supplierno, 
					suppreference, 
					transtext, 
					duedate, 
					rate, 
					ovamount,
					ovgst,
					ovamount+ovgst as totalamt, 
					suppliers.currcode,
					rh_taxref,
					alloc,
					suppname,
					rh_chequeno
			FROM supptrans INNER JOIN suppliers ON supptrans.supplierno=suppliers.supplierid
			LEFT JOIN banktrans ON banktrans.type=supptrans.type and banktrans.transno =supptrans.transno
	WHERE ";

	if($_POST['TransType']=='todos'){
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type IN (20,22,21) ";
	}else {
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type = " . $_POST['TransType'] . " ";
	}
   }
   elseif ($_POST['StockLocation']=='all') {
   	
   	$sql = "SELECT transno, 
   					type,
   					trandate, 
					supptrans.supplierno, 
					suppreference, 
					transtext, 
					duedate, 
					rate, 
					ovamount,
					ovgst,
					ovamount+ovgst as totalamt, 
					currcode,
					rh_taxref,
					alloc,
					suppname
			FROM supptrans INNER JOIN suppliers ON supptrans.supplierno=suppliers.supplierid
	WHERE ";

	if($_POST['TransType']=='todos'){
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type IN (20,22,21) ";
	}else {
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type = " . $_POST['TransType'] . " ";
	}
   	
   }else{
   	
   $sql = "SELECT transno, 
   					type,
   					trandate, 
					supptrans.supplierno, 
					suppreference, 
					transtext, 
					duedate, 
					rate, 
					ovamount,
					ovgst,
					ovamount+ovgst as totalamt, 
					currcode,
					rh_taxref,
					alloc,
					suppname
			FROM supptrans INNER JOIN suppliers ON supptrans.supplierno=suppliers.supplierid
			INNER JOIN rh_supptrans_locations ON rh_supptrans_locations.supptransid = supptrans.id 
	WHERE ";
    //rleal Ene 19, 2014 Se agrega supptrans.type
	if($_POST['TransType']=='todos'){
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type IN (20,22,21) ";
	}else {
		$sql = $sql . "trandate >='" . $SQL_FromDate . "' AND trandate <= '" . $SQL_ToDate . "' AND supptrans.type = " . $_POST['TransType'] . " ";
	}

	//rleal se agrega codigo para lo del almac�n
	$sql = $sql. " AND rh_supptrans_locations.loccode='" . $_POST['StockLocation'] ."'";
    }
    $sql .= $Folio;
	if($_POST['orderby']=='date'){
		$sql .= " ORDER BY trandate";
	}else if($_POST['orderby']=='name'){
		$sql .= " ORDER BY suppname";
	}
	else{
		$sql .= " ORDER BY id";
	}

	//echo $sql."<br>";
	
   $TransResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');

   echo '<TABLE class="TablaProveedor" CELLPADDING=2 BORDER=2>';

   $tableheader = "<TR><TD class='tableheader'>" . _('Number') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Supplier') . "</TD>
			<TD class='tableheader'>" . _('R.F.C.') . "</TD>
			<TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>
			<TD class='tableheader'>Fecha de Pago</TD>
			<TD class='tableheader'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . 'Subtotal' . "</TD>
			<TD class='tableheader'>" . 'IVA' . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD></TR>';
	echo $tableheader;
	$tableheader=str_replace('<TR',"<TR class='no_print'",$tableheader);
	$RowCounter = 1;
	$k = 0; //row colour counter

	while ($myrow=DB_fetch_array($TransResult)) {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k++;
		}

		$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td width='200'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>";
				
				$numero=$myrow['transno'];

		if ($myrow['type']==20){ /* invoices */

			printf("$format_base
				<td class='no_print'><a target='_blank' href='%s/GLTransInquiry.php?&TypeID=20&TransNo=%s'><IMG SRC='%s' TITLE='" . _('Click to preview the invoice GL') . "' border=0></a></td>
				<td class='no_print'><a target='_blank' href='%s/rh_SuppInvoice_Details.php?&Transno=%s'><IMG SRC='%s' TITLE='" . _('Click to preview the invoice') . "' border=0></a></td>
				</tr>",
				_('Supplier Invoice').' '.$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['suppname'] . ' ['.$myrow['supplierno'].']',
				$myrow['rh_taxref'],
				$myrow['suppreference'],
				$myrow['transtext'],
				ConvertSQLDate($myrow['duedate']),
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$myrow['currcode'],
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif',
				$rootpath,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/reports.gif');
		} elseif ($myrow['type']==21){ /* credit notes */
			printf("$format_base
				<td class='no_print'><a target='_blank' href='%s/rh_PrintCustTrans.php?%s&FromTransNo=%s&InvOrCredit=Credit'><IMG SRC='%s' TITLE='" . _('Click to preview the credit') . "'></a></td>
				</tr>",
				_('Debit Note').' '.$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['suppname'] . '['.$myrow['supplierno'].']',
				$myrow['rh_taxref'],
				$myrow['suppreference'],
				$myrow['transtext'],
				ConvertSQLDate($myrow['duedate']),
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$myrow['currcode'],
				$rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');
		} elseif($myrow['type']==22) {  /* pago a proveedores */
			printf("$format_base</tr>",
				_('Supplier Payment').' '.$myrow['transno'],
				ConvertSQLDate($myrow['trandate']),
				$myrow['suppname'] . '['.$myrow['supplierno'].']',
				$myrow['rh_taxref'],
				$myrow['suppreference'].' '.$myrow['rh_chequeno'],
				$myrow['transtext'],
				'-',
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$myrow['currcode']);
				//ConvertSQLDate($myrow['duedate'])
		}

		$RowCounter++;
		If ($RowCounter == 12){
			$RowCounter=1;
			echo $tableheader;
		}
	//end of page full new headings if
	}
	//end of while loop

 echo '</TABLE>';
}

/****************************************************************************************************************************
* Jorge Garcia
* 23/Ene/2009 Sumatorias
****************************************************************************************************************************/
$SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
$SQL_ToDate = FormatDateForSQL($_POST['ToDate']);
 
//rleal jun 24 se agrega lo del almac�n
if (($_POST['TransType']!=20)) 
	$sql = "SELECT systypes.typename, SUM(supptrans.ovamount+supptrans.ovgst) AS amnt, SUM(supptrans.alloc) AS alloc FROM supptrans INNER JOIN systypes ON systypes.typeid = supptrans.type WHERE supptrans.trandate >='" . $SQL_FromDate . "' AND supptrans.trandate <= '" . $SQL_ToDate . "'"; 
elseif ($_POST['StockLocation']=='all') 
	 $sql = "SELECT systypes.typename, SUM(supptrans.ovamount+supptrans.ovgst) AS amnt, SUM(supptrans.alloc) AS alloc FROM supptrans INNER JOIN systypes ON systypes.typeid = supptrans.type WHERE supptrans.trandate >='" . $SQL_FromDate . "' AND supptrans.trandate <= '" . $SQL_ToDate . "'"; 
 else
 	$sql = "SELECT systypes.typename, SUM(supptrans.ovamount+supptrans.ovgst) AS amnt, SUM(supptrans.alloc) AS alloc FROM supptrans INNER JOIN systypes ON systypes.typeid = supptrans.type INNER JOIN rh_supptrans_locations ON rh_supptrans_locations.supptransid = supptrans.id WHERE rh_supptrans_locations.loccode='". $_POST['StockLocation'] . "' AND supptrans.trandate >='" . $SQL_FromDate . "' AND supptrans.trandate <= '" . $SQL_ToDate . "'"; 
 
 if ($_POST['TransType']!='todos')  {
	$sql .= " AND supptrans.type = '" . $_POST['TransType']."'";
}
$sql .= " GROUP BY supptrans.type";

$res = DB_query($sql,$db);
// tableheader
$tableheader = "<TR>
<TD class='tableheader'>" . _('Type') . "</TD>
<TD class='tableheader'>" . _('Amount') . "</TD>
<TD class='tableheader'>" . _('Allocated') . "</TD>
<TD></TD>
</TR>";
			
echo "<TABLE ALIGN=CENTER>";
echo $tableheader;
$k=0;
while($myrow = DB_fetch_array($res)){
	
	if ($k==1){
		echo "<tr bgcolor='#CCCCCC'>";
		$k=0;
	} else {
		echo "<tr bgcolor='#EEEEEE'>";
		$k=1;
	}
	
	echo "<TD>".$myrow['typename']."</TD>
	<TD>".number_format($myrow['amnt'],2)."</TD>
	<TD>".number_format($myrow['alloc'],2)."</TD>
	</TR>";
}
echo "</TABLE>";
/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/

include('includes/footer.inc');

?>