<?php

/* webERP Revision: 1.9 $ 

bowikaxu - realhost
Reporte de Punto de Venta

*/

/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:39:36 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 14 $
 */

include('includes/SQL_CommonFunctions.inc');

$PageSecurity=1;
include('config.php');
include('includes/session.inc');

$title = _('Reporte Archivos');
// cargar el script del calendario
echo '<script language="JavaScript" src="CalendarPopup.js"></script>'; //<!-- Date only with year scrolling -->
$js_datefmt = "yyyy-M-d";

include('includes/header.inc');
$_POST['ShowMenu']=0;

echo "<BR><CENTER><B>"._('Reporte Archivos')."</B></CENTER><BR>";


if(isset($_GET['FromDate']) && isset($_GET['ToDate'])){
	
	$_POST['FromDate'] = $_GET['FromDate'];
	$_POST['ToDate'] = $_GET['ToDate'];
	
	if(isset($_GET['user'])){
		$_POST['user'] = $_GET['user'];
	}else {
		$_POST['user'] == 'Todos';
	}
	if(isset($_GET['type'])){
		$_POST['type'] = $_GET['type'];
	}else {
		$_POST['type'] == 'Todos';
	}
	if(isset($_GET['transno'])){
		$_POST['transno'] = $_GET['transno'];
	}else {
		$_POST['transno'] == 'Todas';
	}
	
	$_POST['VerRes']=1;
}

if(isset($_POST['VerRes'])){
	
	// verify variables
	if($_POST['FromDate']=='' OR $_POST['ToDate']==''){

		echo "<CENTER><B><FONT COLOR=red>ERROR: Algunos Campos Son Invalidos</FONT></B></CENTER>";
		$_POST['ShowMenu']=1;
		
	}else {	
		
		echo "<CENTER><B>Desde: ".$_POST['FromDate']." - Hasta: ".$_POST['ToDate']."</B></CENTER><BR>";
		echo "<CENTER><B>Usuario: ".$_POST['usuario']." / Tipo: ".$_POST['type']." / Trans.: ".$_POST['transno']."</B></CENTER>";
		
		if($_POST['usuario']=='Todos'){
			$usuario = "LIKE '%%'";
		}else {
			$usuario = "= '".$_POST['usuario']."'";
		}
		
		if($_POST['transno']=='Todas'){
			$transno = "LIKE '%%'";
		}else {
			$transno = "= '".$_POST['transno']."'";
		}
		
		if($_POST['type']=='Todos'){
			$type = "LIKE '%%'";
		}else {
			$type = "= '".$_POST['type']."'";
		}
		
		$SQL = "SELECT rh_files.*, systypes.typename
				FROM rh_files,
					systypes
				WHERE
					rh_files.trandate >= '".$_POST['FromDate']."'
					AND rh_files.trandate <= '".$_POST['ToDate']."'
					AND rh_files.user_ ".$usuario."
					AND rh_files.type ".$type."
					AND rh_files.transno ".$transno."
					AND rh_files.type = systypes.typeid
					ORDER BY rh_files.type, rh_files.transno, rh_files.trandate";

		$result = DB_query($SQL,$db,"Imposible obtener Archivos");
		
		/*show a table of the transactions returned by the SQL */

		echo "<BR><BR><TABLE ALIGN=CENTER>
		<TR><TD COLSPAN=7 ALIGN=CENTER><B><BIG>"._('Related Files')."<BIG></B></TD></TR>
		<TR>
		<TD class='tableheader'>ID</TD>
		<TD class='tableheader'>"._('Trans.').' '._('Type').' / '._('Number')."</TD>
		<TD class='tableheader'>Nombre</TD>
		<TD class='tableheader'>Size</TD>
		<TD class='tableheader'>Fecha</TD>
		<TD class='tableheader'>Usuario</TD>
		<TD class='tableheader'>Comentarios</TD>
		</TR>";

		$j = 1;
		$k = 0; //row colour counter
		$TotalFinal = 0;
		$TotalEfectivo = 0;
		$TotalTarjeta = 0;
		$TotalCheque = 0;
		$TotalBono = 0;
		while($finfo = DB_fetch_array($result)){
			
			if ($k == 1){
				echo "<TR BGCOLOR='#CCCCCC'>";
				$k = 0;
			} else {
				echo "<TR BGCOLOR='#EEEEEE'>";
				$k = 1;
			}
			
			$filename = explode(".",$finfo['filename'],2);
			echo "
				<TD>".$finfo['id']."</TD>
				<TD>".$finfo['typename'].' '.$finfo['transno']."</TD>
				<TD><A href='companies/".$_SESSION['DatabaseName']."/rh_files/".$finfo['id'].'.'.$filename[1]."'>".$finfo['filename']."</A></TD>
				<TD>".$finfo['size']."</TD>
				<TD>".$finfo['trandate']."</TD>
				<TD>".$finfo['user_']."</TD>
				<TD>".$finfo['comments']."</TD>";
			
		}
							
		echo "</TABLE>";
		
		echo "<BR><BR>\n";
		echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
		echo "<CENTER><INPUT TYPE=submit NAME='ShowMenu' VALUE='"._('Regresar')."'></CENTER>";
		echo "</FORM>";

	}
	
}
if(!isset($_POST['VerRes']) OR $_POST['ShowMenu']==1) {
// inicia menu principal de busqueda

if(!isset($_POST['FromDate'])){
	$_POST['FromDate'] = date('yyyy-M-dd');
	$_POST['ToDate'] = date('yyyy-M-dd');
}

echo "<FORM NAME='menu' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
echo "<CENTER><TABLE><TR>";

$sql = "SELECT rh_files.type, systypes.typename FROM rh_files, systypes WHERE rh_files.type = systypes.typeid
		GROUP BY rh_files.type";
$typeres = DB_query($sql,$db,'Imposible determinar type');
echo "<TD>"._('Trans.').' '._('Type').": </TD><TD>";
echo "<SELECT NAME='type'>
		<OPTION VALUE='Todos' SELECTED>Todos</OPTION>";
while ($type = DB_fetch_array($typeres)){
	
	echo "<OPTION VALUE='".$type['type']."'>".$type['typename']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

$sql = "SELECT rh_files.transno FROM rh_files GROUP BY transno";
$res = DB_query($sql,$db,'Imposible determinar transno');
echo "<TD>"._('Trans.').": </TD><TD>";
echo "<SELECT NAME='transno'>
		<OPTION VALUE='Todas' SELECTED>Todas</OPTION>";
while ($term = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$term['transno']."'>".$term['transno']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

$sql = "SELECT rh_files.user_, www_users.realname FROM rh_files, www_users WHERE rh_files.user_ = www_users.userid GROUP BY user_";
$res = DB_query($sql,$db,'Imposible determinar usuarios');
echo "<TD>"._('User').": </TD><TD>";
echo "<SELECT NAME='usuario'>
		<OPTION VALUE='Todos' SELECTED>Todos</OPTION>";
while ($user = DB_fetch_array($res)){
	
	echo "<OPTION VALUE='".$user['user_']."'>".$user['realname']."</OPTION>";
	
}
echo "</SELECT></TD></TR>";

echo "<TR><TD>"._('Fecha').' '._('desde').": "."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='FromDate' VALUE=''>
 <a href=\"#\" onclick=\"menu.FromDate.value='';cal.select(document.forms['menu'].FromDate,'from_date_anchor','yyyy-M-d');
                      return false;\" name=\"from_date_anchor\" id=\"from_date_anchor\">
                      <img src='img/cal.gif' width='16' height='16' border='0' alt='Click Para Escoger Fecha'></a>";
echo "</TD></TR>";
echo "<TR><TD>"._('Fecha').' '._('hasta').': '."</TD><TD><INPUT TYPE=TEXT SIZE=10 Name='ToDate' VALUE=''>
<a href=\"#\" onclick=\"menu.ToDate.value='';cal.select(document.forms['menu'].ToDate,'from_date_anchor','yyyy-M-d');
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
