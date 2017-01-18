<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
bowikaxu - realhost
Reporte de Punto de Venta

*/

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;

include('includes/session.inc');

$title = _('Reporte Autorizacion de Precios');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy/M/d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Reporte Autorizacion de Precios')."</B></CENTER><BR>";

if(isset($_POST['VerRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Algunos Campos Son Invalidos</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER><B>Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</B></CENTER><BR>";
		echo "<CENTER><B>Usuario(s): ".$_POST['usuario']."</B></CENTER>";
		
		if($_POST['usuario']=='Todos'){
			$usuario = "LIKE '%'";
		}else {
			$usuario = "= '".$_POST['usuario']."'";
		}
	
		$SQL = "SELECT rh_priceauth.*,
				www_users.realname
				FROM rh_priceauth,
					www_users
				WHERE
					rh_priceauth.date_ >= '".$_POST['FromDate']."'
					AND rh_priceauth.date_ <= '".$_POST['ToDate']."'
					AND rh_priceauth.user_ ".$usuario."
					AND www_users.userid = rh_priceauth.user_
					ORDER BY rh_priceauth.date_";

		$result = DB_query($SQL,$db,"Imposible obtener registros");
		
		/*show a table of the transactions returned by the SQL */

		echo '<CENTER><TABLE CELLPADDING=2 COLSPAN=7>';
		$TableHeader = "<TR><TD CLASS='tableheader'>" . _('Username') . 
			"</TD><TD CLASS='tableheader'>" . _('Date') . 
			"</TD><TD CLASS='tableheader'>" . _('Order') . 
			"</TD><TD CLASS='tableheader'>" . _('Narrative') .
			"</TD></TR>";

		echo $TableHeader;

		$j = 1;
		$k = 0; //row colour counter
		while($res = DB_fetch_array($result)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			printf("<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>",
					$res['realname'],
					$res['date_'],
					$res['order_'],
					$res['comments']);
			
		}
							
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Regresar')."'>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['VerRes']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";

$sql = "SELECT user_ FROM rh_priceauth GROUP BY user_";
$res = DB_query($sql,$db,'Imposible determinar usuarios');
echo "<TD>"._('User').": </TD><TD>";
echo "<SELECT NAME='usuario'>
		<OPTION VALUE='Todos' SELECTED>Todos</OPTION>";
while ($user = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$user['user_']."'>".$user['user_']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

echo "<TR><TD>"._('Fecha').' '._('desde').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy/M/d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>
</TD></TR>";
echo "</TABLE>";
echo "<INPUT TYPE=submit NAME='VerRes' VALUE='"._('Ver Resultados')."'>";
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
