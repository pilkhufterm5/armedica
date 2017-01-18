<?php

/*
| delimitador

Realhost
BOWIKAXU
March 2007
Importar Proveedores

*/

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Importar Proveedores');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["PaymentTerms"]) || $_POST["PaymentTerms"]==""){ 	// no se envio ningun archivo
	
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";
	
	$result=DB_query('SELECT terms, termsindicator FROM paymentterms', $db);

	echo '<TR><TD>' . _('Payment Terms') . ":</TD><TD><SELECT NAME='PaymentTerms'>";

	while ($myrow = DB_fetch_array($result)) {
		echo "<OPTION VALUE='". $myrow['termsindicator'] . "'>" . $myrow['terms'];
	} //end while loop
	DB_data_seek($result, 0);
	echo '</SELECT></TD></TR>';

	$result=DB_query('SELECT currency, currabrev FROM currencies', $db);
	if (!isset($_POST['CurrCode'])){
		$CurrResult = DB_query('SELECT currencydefault FROM companies WHERE coycode=1', $db);
		$myrow = DB_fetch_row($CurrResult);
		$_POST['CurrCode'] = $myrow[0];
	}

	echo '<TR><TD>' . _("Supplier Currency") . ":</TD><TD><SELECT NAME='CurrCode'>";
	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['CurrCode'] == $myrow['currabrev']){
			echo '<OPTION SELECTED VALUE=' . $myrow['currabrev'] . '>' . $myrow['currency'];
		} else {
			echo '<OPTION VALUE=' . $myrow['currabrev'] . '>' . $myrow['currency'];
		}
	} //end while loop
	DB_data_seek($result, 0);

	echo '</SELECT></TD></TR><TR><TD>' . _('Remittance Advice') . ":</TD><TD><SELECT NAME='Remittance'>";
	echo '<OPTION VALUE=0>' . _('Not Required');
	echo '<OPTION VALUE=1>' . _('Required');

	echo '</SELECT></TD></TR>';

	echo '<TR><TD>' . _('Tax Group') . ":</TD><TD><SELECT NAME='TaxGroup'>";

	DB_data_seek($result, 0);

	$sql = 'SELECT taxgroupid, taxgroupdescription FROM taxgroups';
	$result = DB_query($sql, $db);

	while ($myrow = DB_fetch_array($result)) {
		if ($_POST['TaxGroup'] == $myrow['taxgroupid']){
			echo '<OPTION SELECTED VALUE=' . $myrow['taxgroupid'] . '>' . $myrow['taxgroupdescription'];
		} else {
			echo '<OPTION VALUE=' . $myrow['taxgroupid'] . '>' . $myrow['taxgroupdescription'];
		}
	} //end while loop

	echo "</SELECT></TD></TR></TABLE><p><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Importar Proveedores') . "'>";
	echo '</FORM>';
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "codigo | nombre | direccion1 | direccion3 | direccion4 | direccion5 | direccion6 | RFC | conatcto<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{
  var_dump($_FILES);								// se envio un archivo
//**************************Importar Archivos CSV******************************
$flag=false;
if(mkdir('importfiles',0777)){
    $flag=true;
} else if (chdir('importfiles')){
    $flag=true;
}else{
   prnMsg(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'), 'error');
}
if($flag){
    $p=chdir('importfiles');
    $path=realpath($p);
    $path = str_replace('\\','/',$path);
}

if(($flag)){
  echo "AKI";
$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
if(($tamano < 3000000)&&(($tipo=="text/csv")||($tipo=="text/plain")||($tipo=="application/vnd.ms-excel"))){
    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
        $filename = $path.'/'.$_FILES['im']['name'];
        if(@$fh_in = fopen($filename,"r")){
            $Extra = "'".$_POST['PaymentTerms']."','".$_POST['CurrCode']."','".$_POST['TaxGroup']."',".$_POST['Remittance'].");";
            DB_query("BEGIN",$db,"Begin Failed !");
            while(($line = fgetcsv($fh_in,0,'|'))!==false){
	            $Query2 = "INSERT INTO suppliers (
							supplierid,
							suppname,
							address1,
							address2,
							address3,
							address4,
							address5,
							address6,
                            address7,
                            address8,
                            address9,
                            address10,
							rh_taxref,
							paymentterms,
							currcode,
							taxgroupid,
							remittance)
				        VALUES (";
                    if($line!= null){
                    $size = sizeof($line);
                    for($i=0;$i<$size;$i++){
                        $Query2 = $Query2."'".stripslashes($line[$i])."',";
                    }
                    $Query2 = $Query2.$Extra;

                    $ErrMsg = _('No se pudo importar el archivo debido a ');
                        $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                        echo "<B>SATISFACTORIO, Cliente agregado - ".$line[0].'-'.$line[1]." !!!</B>";
                }
            }
        }
        fclose($fh_in);
        DB_query("COMMIT",$db);
}
}
}else{
     prnMsg(_('>No se puede escribir en el directorio indicado'), 'error');
    include ('includes/footer.inc');
    exit;
}
}
include ('includes/footer.inc');
?>