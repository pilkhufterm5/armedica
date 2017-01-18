<?php
if(!function_exists("ObEndLinks")){
	function EliminaPagina(&$Bufer,$pagina,$offset=0){
		if($pagina=='index.php') return true;
		$Posicion=strpos($Bufer,$pagina,$offset);
		if($Posicion!==false){
			if(
					strpos($pagina,'index.php')!==false||
					strpos($pagina,'ManualContents.php')!==false||
					strpos($pagina,'SelectProduct.php')!==false||
					strpos($pagina,'SelectCustomer.php')!==false||
					strpos($pagina,'Logout.php')!==false||
					strpos($pagina,'SelectSupplier.php')!==false||
					strpos($pagina,'crm')!==false
			){
				$TokenInicio='<td';
				$TokenFin='td>';
			}else{
				$TokenInicio='<tr';
				$TokenFin='tr>';
			}
			$offset=$Posicion+strlen($pagina);
			$from=strripos(substr($Bufer,0,$Posicion),$TokenInicio);
			$to=stripos($Bufer,$TokenFin,$Posicion)+3;
			$Elimina=true;
			$P=substr($Bufer,$from,$to-$from);//Elemento que nos interesa quitar
			$P=explode('/',$P);
			foreach($P as $item){
				$Posicion=strpos($item,$pagina);
				if($Posicion!==false&&$Posicion>0){
					$Elimina=false;
					EliminaPagina($Bufer,$pagina,$offset);
				}
			}
			if($Elimina)
				$Bufer=substr($Bufer,0,$from).
				//substr($Bufer,$from,$to-$from).//Elemento que nos interesa quitar
			substr($Bufer,$to);
		}
	}
	function ObEndLinks(){
		$Bufer=ob_get_contents();
		ob_end_clean();
		if(isset($_SESSION['BlockingPages']))
			foreach($_SESSION['BlockingPages'] as $pagina)
			if(trim($pagina)!=''){
			$offset=0;
			EliminaPagina($Bufer,$pagina);
		}
		echo $Bufer;
	}
}
/* $Revision: 1.29 $ */
/* $Revision: 1.31 $ */
	// Titles and screen header
	// Needs the file config.php loaded where the variables are defined for
	//  $rootpath
	//  $title - should be defined in the page this file is included with

	if (!headers_sent()){
		header('Content-type: text/html; charset=utf-8');
	}
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
	$title = $_SESSION['title'];
	echo '<html><head><title>' . $title . '</title>';
	echo '<link REL="shortcut icon" HREF="'. $rootpath.'/favicon.ico">';
	echo '<link REL="icon" HREF="' . $rootpath.'/favicon.ico">';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
	echo '<link href="'.$rootpath. '/css/'. $_SESSION['Theme'] .'/default.css" REL="stylesheet" TYPE="text/css">';
	echo '<script type="text/javascript" src = "'.$rootpath.'/javascript/MiscFunctions.js"></script>';
	//echo '<script src="' . $rootpath .'/includes/num_date.js" language="JavaScript"></script>';
	
	
	// bowikaxu realhost Feb 2008 - no backspace button or F5
	if($rh_disableKeys == 1){
?>

<script type="text/javascript">
 if (typeof window.event == 'undefined'){
   document.onkeypress = function(e){
 	var test_var=e.target.nodeName.toUpperCase();
 	if (e.target.type) var test_type=e.target.type.toUpperCase();
 	if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA'){
 	  return e.keyCode;
 	}else if (e.keyCode == 8 || e.keyCode == 116){
 	  e.preventDefault();
 	}
   }
 }else{
   document.onkeydown = function(){
 	var test_var=event.srcElement.tagName.toUpperCase();
 	if (event.srcElement.type) var test_type=event.srcElement.type.toUpperCase();
 	if ((test_var == 'INPUT' && test_type == 'TEXT') || test_var == 'TEXTAREA'){
 	  return event.keyCode;
 	}else if (event.keyCode == 8 || e.keyCode == 116){
 	  event.returnValue=false;
 	}
   }
 }
 </script>

<?php
	}
	//echo '</head>';

//alberto Realhost 14/Agosto/2007
//scripts necesarios para el calendario
/*
echo "<script src='".$rootpath."/javascript/general.js'></script>";
echo "<script src='".$rootpath."/javascript/overlib_mini.js'></script>";
echo "<script src='".$rootpath."/javascript/calendar.js'></script>";
echo "<script src='".$rootpath."/javascript/lang/calendar-es.js'></script>";
echo "<script src='".$rootpath."/javascript/calendar-setup.js'></script>";
echo "<link rel='stylesheet' href='".$rootpath. '/css/'. $_SESSION['Theme'] ."/calendar.css' type='text/css'>";
*/
//end alberto

	echo '</HEAD>';
	echo '<BODY>';
	include 'rh_chat.php';
	echo '<TABLE CLASS="callout_main" CELLPADDING="0" CELLSPACING="0">';
	echo '<TR>';
	echo '<TD COLSPAN="2" ROWSPAN="2">';

	echo '<TABLE CLASS="main_page" ALIGN="right" CELLPADDING="0" CELLSPACING="0">';
	echo '<TR>';
	echo '<TD>';
	echo '<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0" >';
	echo '<TR>';
	echo '<TD>';

	if ($title AND substr($title,0,4) != 'Help') {
		echo '<TABLE CELLPADDING="0" CELLSPACING="0" BORDER="0" id="quick_menu" CLASS="quick_menu">';
		echo '<TR>';
		echo '<TD ALIGN="left" WIDTH="70%" CLASS="quick_menu_left">';
		echo  $_SESSION['CompanyRecord']['coyname'] . ': <A HREF="' .  $rootpath . '/UserSettings.php?' . SID . '">' . $_SESSION['UsersRealName'] . '</A>';
		echo '<BR>' . $title . '</TD>';
		
		//añadiendo calendario
/*
		echo "<td width='30%'><center>";
?>
<script type="text/javascript">

function direccion_fecha(fecha){
//aaaa-mm-dd
	rh_anno = fecha.substr(0,4);
	rh_mes = fecha.substr(5,2);
	rh_dia = fecha.substr(8,2);
	document.getElementById('calendario').action = "rh_calendar.php";
	document.getElementById('type').value = "jour";
	document.getElementById('jour').value = rh_dia;
	document.getElementById('mois').value = rh_mes;
	document.getElementById('annee').value = rh_anno;
	document.getElementById('calendario').submit();
}

</script>
<?php
//		echo ucfirst(strftime('%A ')). date($_SESSION['DefaultDateFormat'])."<br />";

//parametros para la pagina del calendario
		echo "<form id='calendario' method='get'>";
		echo "<input type='hidden' id='type' name='type' />";
		echo "<input type='hidden' id='jour' name='jour' />";
		echo "<input type='hidden' id='mois' name='mois' />";
		echo "<input type='hidden' id='annee' name='annee' />";

//se escoge una fecha
		echo "<input type=\"button\" id=\"trigger_c\" value='Fecha'' /></button><script type=\"text/javascript\">Calendar.setup({ inputField:\"type\", button:\"trigger_c\" });</script>";

//se llama a la funcion
		echo "<input type='button' value='Calendario' onclick='direccion_fecha(document.getElementById(\"type\").value)'>";
		echo "</center></td>";
		echo "</form>";
*/
//end alberto
		
		echo '<TD CLASS="quick_menu_tabs">';
		echo '<TABLE CELLPADDING="0" CELLSPACING="0" CLASS="quick_menu_tabs"><TR>';
		
		// bowikaxu realhost March 2008 - Link to Transaction File Upload
		echo "<TD><a href='rh_upload.php'>"._('File Upload')."</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>";
		
		echo '<TD CLASS="quick_menu_tab" ALIGN="center"><A ACCESSKEY="1" HREF="' .  $rootpath . '/index.php?' . SID . '"><U>1</U> ' . _('Main Menu') . '</A></TD>';

		if (count($_SESSION['AllowedPageSecurityTokens'])>1){

			echo '<TD CLASS="quick_menu_tab" ALIGN="center"><A ACCESSKEY="2" HREF="' .  $rootpath . '/SelectCustomer.php?' . SID . '"><U>2</U> ' . _('Select Customer') . '</A></TD>';

			echo '<TD CLASS="quick_menu_tab" ALIGN="center"><A ACCESSKEY="3" HREF="' .  $rootpath . '/SelectProduct.php?' . SID . '"><U>3</U> ' . _('Select Item') . '</A></TD>';

			echo '<TD CLASS="quick_menu_tab" ALIGN="center"><A ACCESSKEY="4" HREF="' .  $rootpath . '/SelectSupplier.php?' . SID . '"><U>4</U> ' . _('Select Supplier') . '</A></TD>';

			// bowikaxu realhost March 2008 - mostrar opcion de busqueda
			echo '<TD CLASS="quick_menu_tab" ALIGN="center">
				
				<FORM METHOD="POST" ACTION="rh_search.php" NAME="rh_search">
				<INPUT TYPE="TEXT" NAME="txtsearch" VALUE="'.$_POST['txtsearch'].'" SIZE=18 maxlength=15>
				<INPUT TYPE="SUBMIT" NAME="allsearch" VALUE="'._('Search Now').'">
				</FORM>';
				//<A TARGET="_blank" ACCESSKEY="8" HREF="' .  $rootpath . '/doc/Manual/ManualContents.php?' . SID . '"><U>8</U> ' . _('Manual') . '</A>
                                echo '&nbsp;&nbsp;&nbsp;<A TARGET="_blank" ACCESSKEY="9" HREF="mailto:gerardo.delangel@armedica.com.mx"><U>9</U> ' . _('Contact') . '</A>';
				
				echo "&nbsp;&nbsp;&nbsp;<A ACCESSKEY=\"0\" HREF=\"" . $rootpath . '/Logout.php?' . SID . "\" onclick=\"return confirm('" . _('Are you sure you wish to logout?') . "');\"><U>0</U> "  . _('Logout') . '</A>';
				echo '</TD>';

		}else { // bowikaxu - entrada como cliente, solo mostrar boton de salir
			
			echo "<TD CLASS=\"quick_menu_tab\" ALIGN=\"center\"><A ACCESSKEY=\"0\" HREF=\"" . $rootpath . '/Logout.php?' . SID . "\" onclick=\"return confirm('" . _('Are you sure you wish to logout?') . "');\"><U>0</U> "  . _('Logout') . '</A></TD>'; 
			
		}

		echo '</TR></TABLE>';
		echo '</TD></TR></TABLE>';

	}

echo '</TD>';
echo '</TR>';
echo '</TABLE>';

?>
