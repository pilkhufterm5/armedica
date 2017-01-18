<?php
	$PageSecurity = 2;
	include('includes/session.inc');
	
	$title=_('Item').' / '._('Prices');
	include('includes/header.inc');
//strlen($_POST['FromCriteria'])<1 || strlen($_POST['ToCriteria'])<1
	if (1) {

		/*if $FromCriteria is not set then show a form to allow input	*/

		echo '<FORM ACTION=' . $_SERVER['PHP_SELF'] . " METHOD='POST'><CENTER><TABLE>";

		echo '<TR><TD>' . _('From Inventory Category Code') . ':</FONT></TD><TD><SELECT name=FromCriteria>';

		$sql='SELECT categoryid, categorydescription FROM stockcategory ORDER BY categoryid';
		$CatResult= DB_query($sql,$db);
		While ($myrow = DB_fetch_array($CatResult)){
			if($_POST['FromCriteria']==$myrow['categoryid']){
				echo "<OPTION SELECTED VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
			}else {
				echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
			}
		}
		echo '</SELECT></TD></TR>';

		echo '<TR><TD>' . _('To Inventory Category Code') . ':</TD><TD><SELECT name=ToCriteria>';

		/*Set the index for the categories result set back to 0 */
		DB_data_seek($CatResult,0);

		While ($myrow = DB_fetch_array($CatResult)){
			if($_POST['ToCriteria']==$myrow['categoryid']){
				echo "<OPTION SELECTED VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
			}else {
				echo "<OPTION VALUE='" . $myrow['categoryid'] . "'>" . $myrow['categoryid'] . ' - ' . $myrow['categorydescription'];
			}
		}
		echo '</SELECT></TD></TR>';
		
		$sql="select * from currencies";
		$CatResult= DB_query($sql,$db);
		echo '<TR><TD>' . _('Currency') . ':</FONT></TD><TD><SELECT name=currency>';
		While ($myrow = DB_fetch_assoc($CatResult)){
			echo "<OPTION ";
			if(!isset($_POST['currency'])||$_POST['currency']==$myrow['currabrev']){
				echo " selected=selected ";
				$_POST['currency']=$myrow['currabrev'];
			}
			echo " VALUE='" . $myrow['currabrev'] . "'>" . $myrow['currabrev'] . ' - ' . $myrow['currency'];
		}
		echo '</SELECT></TD></TR>';		
		echo "</TABLE><INPUT TYPE=Submit Name='View' Value='" . _('View') . "'>";
		if(isset($_POST['View'])){
		?>
		<script type="text/javascript" src="javascript/descargar/csvExporter.js"></script>
		<script type="text/javascript" src="javascript/descargar/pdfExporter.js"></script>
		<script type="text/javascript">
        $(function(){
        	$('csv').show();	
		})
        </script>
		<csv style="display:none" target="Perecios (<?=date('Y-m-d')?>)" title=".TablaItemPrecios"><button>Excel</button></csv>
		<pdf style="display:none" target="Perecios (<?=date('Y-m-d')?>)" title=".TablaItemPrecios"><button>Pdf</button></pdf>
		<?php
		}
		echo "</CENTER>";
	}
	
if(isset($_POST['View'])){

	$tableheader = "<TR>
						<TD class='tableheader' align='left'>"._('Category')."</TD>
						<TD class='tableheader' align='left'>"._('Item')."</TD>
						<TD class='tableheader' align='left'>"._('ID Agrupador')."</TD>
						<TD class='tableheader' align='left'>"._('Código de barras')."</TD>
						<TD class='tableheader' align='left'>"._('Description')."</TD>
						<TD class='tableheader' align='right'>"._('Cost')."</TD>";
	
	$sql = "SELECT * FROM salestypes ORDER BY typeabbrev";
	$st_res = DB_query($sql,$db);
	$sales = array();
	while($st_info = DB_fetch_array($st_res)){
		$tableheader .= "<TD class='tableheader' align='right'>".$st_info['sales_type']."</TD>";
		$sales[] = $st_info['typeabbrev'];
	}
	
	//$tableheader .= "<TD class='tableheader' align='right'>"._('Special Prices')."</TD>";
	$tableheader .= "</TR>";
	
	$k=0;
	$sql = "SELECT stockmaster.stockid, 
			stockmaster.description,
			stockmaster.id_agrupador,
			stockmaster.barcode,
			stockmaster.categoryid,
			(stockmaster.materialcost+stockmaster.labourcost+stockmaster.overheadcost) AS cost,
			stockcategory.categorydescription
			FROM stockmaster
			LEFT JOIN prices ON stockmaster.stockid = prices.stockid
			LEFT JOIN stockcategory ON stockcategory.categoryid = stockmaster.categoryid
			WHERE stockmaster.categoryid >= '".$_POST['FromCriteria']."'
			AND stockmaster.categoryid <= '".$_POST['ToCriteria']."'
			GROUP BY stockmaster.stockid
			ORDER BY stockmaster.categoryid, stockmaster.stockid";
	
	//echo $sql."<br><br>";
	$pr_res = DB_query($sql,$db);
	
	$StockID = '';
	$CategoryID = '';
	$TypeAbbrev = '';
	$cli = 0;
	//print_r($sales);
	echo '<br>';
	echo "<TABLE ALIGN='center' BORDER=0 class='TablaItemPrecios'>";
	echo $tableheader;
	
	while($pr_info = DB_fetch_array($pr_res)){
		
		//$prices_res = DB_query($sql,$db);
		
		if($CategoryID != $pr_info['categoryid']){
			echo "<TR>
					<TD><B>".$pr_info['categorydescription']."</B></TD>
					</TR>";
			$CategoryID = $pr_info['categoryid'];
		}
		
		echo "<TD ALIGN=left></TD>
					<TD align=left>".$pr_info['stockid']."</TD>
					<TD align=left>".$pr_info['id_agrupador']."</TD>
					<TD align=left>".$pr_info['barcode']."</TD>
					<TD align=left>".$pr_info['description']."</TD>
					<TD align=right>".number_format($pr_info['cost'],2)."</TD>";
		
		foreach($sales AS $key => $value){
			$sql = "SELECT prices.price FROM prices
					WHERE debtorno = ''
					AND branchcode = ''
					AND stockid = '".$pr_info['stockid']."'
					AND typeabbrev = '".$value."'".
					" AND currabrev='".$_POST['currency']."'";
			$sale_res = DB_query($sql,$db);
			if(DB_num_rows($sale_res)<=0){
				echo "<TD ALIGN=RIGHT>0.00</TD>";
			}else {
				echo "<TD ALIGN=right>";
				$d='';
				while($sale_info = DB_fetch_array($sale_res)){
					echo number_format($sale_info['price'],2).$d;
					$d='<br /> ';
				}
				echo "</TD>";
			}
		}
		DB_free_result($sale_res);
		echo "
				</TR>";
		//DB_free_result($prices_res);
	}// end products while
	
} // end if
	echo "</TABLE>";
	echo "</FORM>";
	include('includes/footer.inc');
?>