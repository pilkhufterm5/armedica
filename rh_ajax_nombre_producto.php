<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
$PageSecurity = 1;
include('includes/DefineCartClass.php');
include('includes/session.inc');
include('includes/GetPrice.inc');
include('includes/SQL_CommonFunctions.inc');

if(isset($_POST["clave"]) && trim($_POST["clave"]) != ""){
	$rh_query = "SELECT description FROM stockmaster WHERE stockid = '" . $_POST["clave"] . "'";
	$resultado = DB_query($rh_query,$db,$ErrMsg);
	if(DB_num_rows($resultado) == 1){
		$myrow = DB_fetch_array($resultado);
		echo $myrow['description'];
	}else{
		echo "Ambiguo";
	}
}else{
	echo "";
}
?>