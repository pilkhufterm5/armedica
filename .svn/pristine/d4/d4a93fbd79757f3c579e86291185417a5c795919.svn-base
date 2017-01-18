<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
bowikaxu - realhost
View Customer Balance within two dates and within supplier name

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=2;

include('includes/session.inc');

$title = _('GL Trans Inquiry');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('GL Trans Inquiry')."</B></CENTER><BR>";

if(isset($_POST['ShowGL'])){
	
	echo "<CENTER> Tipo: ".$_POST['tipo']." Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</CENTER><BR>";
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Algun Campo es Invalido</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		$SQL = "SELECT * FROM gltrans WHERE type='".$_POST['tipo']."' AND 
				trandate >='".$_POST['FromDate']."' AND
				trandate <='".$_POST['ToDate']."' ORDER BY trandate";
		
		$result = DB_query($SQL,$db,"Imposible obtener reporte");

		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Fecha') . 
			"</TD><TD CLASS='tableheader'>" . _('Account') . 
			"</TD><TD CLASS='tableheader'>" . _('Periodo') .
			"</TD><TD CLASS='tableheader'>" . _('Descripcion') . 
			"</TD><TD CLASS='tableheader'>" . _('Cantidad') .
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		while($reporte = DB_fetch_array($result)){
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$reporte['trandate'],
					$reporte['account'],
					$reporte['periodno'],
					$reporte['narrative'],
					number_format($reporte['amount'],2));
			
		}
		
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Otra Busqueda')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['ShowGL']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";

$sql = "SELECT systypes.typename, systypes.typeid FROM systypes";
$result = DB_query($sql,$db,'ERROR: Imposible obtener los tipos');
echo "<TR><TD>"._('Tipo').": </TD><TD>";
// tipos de gltrans	
echo "<SELECT NAME='tipo'>";
while ($tipo = DB_fetch_array($result)){
	
	echo "<OPTION VALUE='".$tipo['typeid']."'>".$tipo['typeid']." - "._($tipo['typename'])."</OPTION>";
	
}
echo "</SELECT>";
// fin tipos de gltans
echo "</TD></TR>";

echo "<TR><TD>"._('Fecha').' '._('de').": "."</TD><TD><INPUT TYPE=TEXT SIZE=12 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=12 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='ShowGL' VALUE='"._('Ver Reporte')."'>";
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
