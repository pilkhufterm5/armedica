<?php

/*
| delimitador

Realhost
BOWIKAXU
Oct-2006
Importar Inventario

$sql = "INSERT INTO prices (stockid,
                        typeabbrev,
                        currabrev,
                        debtorno,
                        price)
                VALUES ('$Item',
                    '" . $_POST['TypeAbbrev'] . "',
                    '" . $_POST['CurrAbrev'] . "',
                    '',
                    " . $_POST['Price'] . ")";
            
$SQL = "INSERT INTO stockserialitems (stockid,
                                    loccode,
                                    serialno,
                                    quantity)
                        VALUES ('" . $_SESSION['Adjustment']->StockID . "',
                        '" . $_SESSION['Adjustment']->StockLocation . "',
                        '" . $Item->BundleRef . "',
                        " . $Item->BundleQty . ")";
                    
*/
//rleal Feb 17 2010 a petición de Ever ticket 2011021507000051
$PageSecurity = 2;

include('includes/session.inc');

$title = _('Importar Precios del Inventario de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_POST['CurrAbrev'])){     // no se envio ningun archivo
    
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";

// OPCIONES PARA INVENTARIOS

    $SQL = "SELECT currabrev, currency FROM currencies";
    $result = DB_query($SQL,$db);

    echo '<CENTER><TABLE><TR><TD>' . _('Currency') . ':</TD><TD><SELECT name="CurrAbrev">';
    while ($myrow = DB_fetch_array($result)) {
        if ($myrow['currabrev']==$_POST['CurrAbrev']) {
            echo '<OPTION SELECTED VALUE="'.$myrow['currabrev'] . '">' . $myrow['currency'];
        } else {
            echo '<OPTION VALUE="'.$myrow['currabrev'] . '">' . $myrow['currency'];
        }
    } //end while loop

    DB_free_result($result);

    echo '</SELECT>    </TD></TR><TR><TD>' . _('Sales Type Price List') . ':</TD><TD><SELECT name="TypeAbbrev">';

    $SQL = "SELECT typeabbrev, sales_type FROM salestypes";
    $result = DB_query($SQL,$db);

    while ($myrow = DB_fetch_array($result)) {
        if ($myrow['typeabbrev']==$_POST['TypeAbbrev']) {
            echo '<OPTION SELECTED VALUE="';
        } else {
            echo '<OPTION VALUE="';
        }
        echo $myrow['typeabbrev'] . '">' . $myrow['sales_type'];

    } //end while loop

    DB_free_result($result);
    echo "</CENTER></TABLE>";
    echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
    echo "</FORM>";     
    echo "<center><h2> Formato del archivo: <br>";
    //echo "stockid | Descripcion Corta | Descripcion Larga | Cantidad economica reordenar | Volumen (mts cubicos) | Peso (kgs) | Codigo de Barras<br></center></h2>";
    echo "stockid | Precio<br></center></h2>";
    include ('includes/footer.inc');
    exit();
    
}else{
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
$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
if(($tamano < 3000000)//&&(($tipo=="text/csv")||($tipo=="text/text")||($tipo=="application/vnd.ms-excel"))
){
    echo 'Moneda seleccionada: '.$_POST['CurrAbrev']."<br />";
    echo 'Lista de precios seleccionada: '.$_POST['TypeAbbrev']."<br /><hr />";

    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
	//$filename = "prueba.csv";
	$filename = $path.'/'.$_FILES['im']['name'];
	//$fh_out = fopen("clientes.sql","w+");
    if(@$fh_in = fopen($filename,"r")){
    	$sep='|';
        $Extra = "'".$_POST['CurrAbrev']."','".$_POST['TypeAbbrev']."');";
        DB_query("BEGIN",$db,"Begin Failed !");
        while(($line = fgetcsv($fh_in,0,$sep))!==false){
        	
        	if (!is_array($line) || count($line) == 1) {
            	if (count($line) == 1) $line = array_pop($line);
                if (stripos($line, ";")) $sep = ';';
                else if (stripos($line, "|")) $sep = '|';
                else if (stripos($line, ",")) $sep = ',';
                $line = array_map(function($r){return trim($r,'"');},explode($sep, $line));
			}
        	
        	$sql="select count(*)t from stockmaster where stockid='".DB_escape_string($line[0])."'";
        	$fila=DB_fetch_assoc(DB_query($sql,$db));
        	if($fila['t']==0)$line=null;
            $Query2 = "INSERT INTO prices (
                            stockid,
                            price,
                            currabrev,
                            typeabbrev)
                        VALUES (";
            ;
            if($line!= null){
                $size = sizeof($line);
                for($i=0;$i<$size;$i++){
                    $Query2 = $Query2."'".DB_escape_string($line[$i])."',";
                }
                $Query2 = $Query2.$Extra;

                $ErrMsg = _('No se pudo importar el archivo debido a ');

                $sqlSelect = "select stockid from prices where stockid='".DB_escape_string($line[0])."' and typeabbrev='".DB_escape_string($_POST['TypeAbbrev'])."' and currabrev='".DB_escape_string($_POST['CurrAbrev'])."' ;";
                $rp = DB_query($sqlSelect,$db);
                if(DB_num_rows($rp)==0){
                 $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                 echo "<B>SATISFACTORIO !!!</B>";
                }else{
                    $Query2="update prices set price='".DB_escape_string($line[1])."' where stockid='".DB_escape_string($line[0])."' and typeabbrev='".DB_escape_string($_POST['TypeAbbrev'])."' and currabrev='".DB_escape_string($_POST['CurrAbrev'])."' ;";
                    $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                    echo "<B>SATISFACTORIO !!!</B>";
                }
                echo $Query2."<BR>";

             }
    }
    fclose($fh_in);
    DB_query("COMMIT",$db);
}else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
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