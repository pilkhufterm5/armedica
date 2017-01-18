<?php
/* $Revision: 1.5 $ */
/*Input Serial Items - used for inputing serial numbers or batch/roll/bundle references
for controlled items - used in:
- ConfirmDispatchControlledInvoice.php
- GoodsReceivedControlled.php
- StockAdjustments.php
- StockTransfers.php
- CreditItemsControlled.php

*/
//echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
//$js_datefmt = "yyyy/M/d";

//we start with a batch or serial no header and need to display something for verification...
global $tableheader;

if (isset($_GET['LineNo'])){
	$LineNo = $_GET['LineNo'];
} elseif (isset($_POST['LineNo'])){
	$LineNo = $_POST['LineNo'];
}

/*Display the batches already entered with quantities if not serialised */

echo '<TABLE><TR><TD valign=top><TABLE>';
echo $tableheader;

$TotalQuantity = 0; /*Variable to accumulate total quantity received */
$RowCounter =0;

foreach ($LineItem->SerialItems as $Bundle){
    /*
    echo "<pre>";
    print_r($Bundle);
    echo "<pre>";
    */

	if ($RowCounter == 10){
		echo $tableheader;
		$RowCounter =0;
	} else {
		$RowCounter++;
	}

	if ($k==1){
		echo '<tr bgcolor="#CCCCCC">';
		$k=0;
	} else {
		echo '<tr bgcolor="#EEEEEE">';
		$k=1;
	}

	echo '<TD style="max-width: 60px;">' ;
	if(isset($Bundle->stockid)&&isset($ArticulosSeleccionados[$Bundle->stockid])){
		echo $ArticulosSeleccionados[$Bundle->stockid].' [<b style="color:red;">';
	}
	
	 echo $Bundle->BundleRef ;
	 if(isset($Bundle->stockid)&&isset($ArticulosSeleccionados[$Bundle->stockid]))
	 	echo '</b>]';
	 echo  '</TD>';

	if ($LineItem->Serialised==0){
		echo '<TD ALIGN=RIGHT>' . number_format($Bundle->BundleQty, $LineItem->DecimalPlaces) . '</TD>';
	}
    echo '<TD ALIGN=RIGHT>' . $Bundle->BundleExpD . '</TD>';
    echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . $term . '&Delete=' . $Bundle->BundleRef . '&StockID=' . $LineItem->StockID . '&LineNo=' . $LineNo .'">'. _('Delete'). '</A></TD></TR>';

	$TotalQuantity += $Bundle->BundleQty;
}


/*Display the totals and rule off before allowing new entries */
if ($LineItem->Serialised==1){
	echo '<TR><TD ALIGN=RIGHT><B>'. _('Total Quantity'). ': ' . number_format($TotalQuantity,$LineItem->DecimalPlaces) . '</B></TD></TR>';
} else {
	echo '<TR><TD ALIGN=RIGHT><B>'. _('Total Quantity'). ':</B></TD><TD ALIGN=RIGHT><B>' . number_format($TotalQuantity,$LineItem->DecimalPlaces) . '</B></TD></TR>';
}

/*Close off old table */
echo '</TABLE></TD><TD valign=top>';

/*Start a new table for the Serial/Batch ref input  in one column (as a sub table
then the multi select box for selection of existing bundle/serial nos for dispatch if applicable*/
//echo '<TABLE><TR><TD valign=TOP>';

/*in the first column add a table for the input of newies */
echo '<TABLE>';
echo $tableheader;


echo '<FORM ACTION="' . $_SERVER['PHP_SELF'] . '?' . $term . '" METHOD="POST">
      <input type=hidden name=LineNo value="' . $LineNo . '">
      <input type=hidden name=StockID value="' . $StockID . '">
      <input type=hidden name=EntryType value="KEYED">';
if ( isset($_GET['EditControlled']) ) {
	$EditControlled = isset($_GET['EditControlled'])?$_GET['EditControlled']:false;
} elseif ( isset($_POST['EditControlled']) ){
	$EditControlled = isset($_POST['EditControlled'])?$_POST['EditControlled']:false;
}
$StartAddingAt = 0;
if ($EditControlled){
	foreach ($LineItem->SerialItems as $Bundle){

		echo '<TR><TD valign=top><input type=text name="SerialNo'. $StartAddingAt .'"
			value="'.$Bundle->BundleRef.'" size=21  maxlength=20></td>';

		/*if the item is controlled not serialised - batch quantity required so just enter bundle refs
		into the form for entry of quantites manually */

		if ($LineItem->Serialised==1){
			echo '<input type=hidden name="Qty' . $StartAddingAt .'" Value=1></TR>';
		} else {
		   	echo '<TD><input type=text name="Qty' . $StartAddingAt .'" size=11
		   		value="'. number_format($Bundle->BundleQty, $LineItem->DecimalPlaces). '" maxlength=10>';
		}

		/**************************************************************************
            Lotes
		*************************************************************************/
        echo '<TD><input type=hidden name="expirationdate' . $StartAddingAt .'" size=11 Value='.$Bundle->BundleExpD.'></TD>';
		if (!isset($_SESSION['Transfer'])){
			echo "<TD valign=top><input type=text name='expirationdate". $StartAddingAt ."' size=12  value=".$Bundle->BundleExpD." maxlength=10 readonly=true>
			<a href=\"#\" onclick=\"menu.expirationdate". $StartAddingAt .".value='';cal.select(document.forms['menu'].expirationdate". $StartAddingAt .",'from_date_anchor','d/M/yyyy');
			return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
			<img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a></TD><TR>";
		}
		$StartAddingAt++;
	}
}

for ($i=0;$i < 10;$i++){

	echo '<TR><TD valign=top>';
	if(isset($Articulos )){
		if (count($Articulos)>1) {
			echo '<select name="StockID'. ($StartAddingAt+$i) .'">';
			foreach($Articulos as $elem=>$item){
					echo '<option';
					echo ' value="'.htmlentities($elem).'"';
					if($elem==$LineItem->stockid)echo ' selected=selected ';
					echo '>';
					echo htmlentities($item);
					echo '</option>';
			}
			echo '</select>';
		}else{
			echo '<input type=hidden value="';
			foreach($Articulos as $elem=>$item) echo $elem; 
			echo '" name="StockID'. ($StartAddingAt+$i) .'" >';
		}
	}	
	echo '<input type=text name="SerialNo'. ($StartAddingAt+$i) .'" size=21  maxlength=20></td>';
	/*if the item is controlled not serialised - batch quantity required so just enter bundle refs
	into the form for entry of quantites manually */

	if ($LineItem->Serialised==1){
		echo '<input type=hidden name="Qty' . ($StartAddingAt+$i) .'" Value=1></TR>';
	} else {
		echo '<TD><input type=text name="Qty' . ($StartAddingAt+$i) .'" style="width:100px" ></td>';
	}
    echo '<TD valign=top><input type=text class="Date DatePicker2" name="expirationdate'. ($StartAddingAt+$i) .'" style="width:120px" ></td>';

    echo '</tr>';
}

echo '</table>';
echo '<br><center><INPUT TYPE=SUBMIT NAME="AddBatches" VALUE="'. _('Enter'). '"></center><BR>';
echo '</FORM></TD><TD valign=top>';
if ($ShowExisting){
	include('includes/InputSerialItemsExisting.php');
}
echo '</TD></TR></TABLE>'; /*end of nested table */
?>

<script type="text/javascript">
    $(document).on('ready',function() {
        $(".DatePicker2").datepicker({
            dateFormat : 'yy-mm-dd',
            changeMonth: true,
            changeYear: true,
            yearRange: '-100:+10'
        });
    });
</script>
