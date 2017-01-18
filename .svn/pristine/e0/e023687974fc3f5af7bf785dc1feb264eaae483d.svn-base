<?php

/* $Revision: 1 $ 

bowikaxu - realhost
bowikaxu@gmail.com
Reporte de Remisiones Facturadas y no Facturadas

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('Reporte de Ventas');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Reporte de Ventas')."</B></CENTER><BR>";

// 2007/02/08 bowikaxu mostrar balance de saldo
if(isset($_POST['ShowRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['Days']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Alguno de los campos es invalido</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER> Desde: ".$_POST['FromDate']." - Dias: ".$_POST['Days']."</CENTER><BR>";
		
		/*
		 * debtorno IN (";
		
		$row = 1;
		$handle = fopen("debtors.csv", "r");
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
		    $num = count($data);
		    //echo "<p> $num fields in line $row: <br /></p>\n";
		    $row++;
		    for ($c=0; $c < $num; $c++) {
		        //echo $data[$c] . "<br />\n";
		        $SQL .= "'".$data[$c]."',";
		    }
		}
		fclose($handle);
		//echo $SQL."<hr>";
		$SQL = substr($SQL,0,strlen($SQL)-1);
		//echo $SQL."<hr>";
		$SQL .= ") AND 
		 */
		
		$SQL = "select debtorsmaster.debtorno, debtorsmaster.name,
		DATE_SUB('".$_POST['FromDate']."', INTERVAL ".$_POST['Days']." DAY) as fecha 
		FROM debtorsmaster 
		WHERE 
		
		debtorno NOT IN 
		(select salesorders.debtorno FROM salesorders WHERE 
				salesorders.orddate >= DATE_SUB('".$_POST['FromDate']."', INTERVAL ".$_POST['Days']." DAY) 
		group by salesorders.debtorno)";
		
		//echo $SQL."<hr>";
		$result = DB_query($SQL,$db,"Imposible obtener resultados");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Client Code') . 
			"</TD><TD CLASS='tableheader'>" . _('Client') . 
		"</TD><TD CLASS='tableheader'>" . _('Date') .' '. _('Ultimo').' '._('Order').
			"</TD><TD CLASS='tableheader'>" . _('Salesman') .
			"</TD><TD CLASS='tableheader'>" . _('Phone') .
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		
		while($res = DB_fetch_array($result)){
			
			$sql = "SELECT MAX(salesorders.orddate) AS fecha, custbranch.salesman, custbranch.phoneno, salesman.salesmanname
			FROM salesorders 
			INNER JOIN custbranch ON custbranch.branchcode = salesorders.branchcode
			INNER JOIN salesman ON salesman.salesmancode = custbranch.salesman
			WHERE salesorders.debtorno = '".$res['debtorno']."'
			GROUP BY salesorders.debtorno";
			$res2 = DB_query($sql,$db);
			$last_debtor_date = DB_fetch_array($res2);
		
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>",
					$res['debtorno'],
					$res['name'],
					$last_debtor_date['fecha'],
					$last_debtor_date['salesmanname'],
					$last_debtor_date['phoneno']);
			
		}
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['ShowRem']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE>";

echo "<TR><TD>"._('Days').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='Days' VALUE='20'></TD></TR>";

//echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('De').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE='".date('Y/m/d')."'>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/MM/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";

echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='ShowRes' VALUE='"._('Ver Reporte')."'>";
echo "</CENTER></FORM>";
// fin meu principal busqueda
}

?>

<script language="JavaScript">
<!-- // create calendar object(s) just after form tag closed
				 // specify form element as the only parameter (document.forms['formname'].elements['inputname']);
				 // note: you can have as many calendar objects as you need for your application
var cal = new CalendarPopup();
				//-->
</script>

<?php

include('includes/footer.inc');

?>
