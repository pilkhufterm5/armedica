<?php

//rleal Feb 17 2010 a petición de Ever ticket 2011021507000051
$PageSecurity = 2;

include('includes/session.inc');

$title = _('Importar Costos del Inventario de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_POST['submit'])){     // no se envio ningun archivo
    echo "<br /><br /><center><strong>Importar contenido</strong></center>";
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";

// OPCIONES PARA INVENTARIOS

   /* $SQL = "SELECT currabrev, currency FROM currencies";
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
    */
    //DB_free_result($result);
    echo "</CENTER></TABLE>";
    echo "<BR><BR><INPUT TYPE=Submit name='submit'></INPUT>";
    echo "</FORM>";
    echo "<center><h2> Formato del archivo: <br>";
    //echo "stockid | Descripcion Corta | Descripcion Larga | Cantidad economica reordenar | Volumen (mts cubicos) | Peso (kgs) | Codigo de Barras<br></center></h2>";
    echo "stockid | Costo<br></center></h2>";
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
if(($tamano < 3000000)&&(($tipo=="text/csv")||($tipo=="text/text")||($tipo=="application/vnd.ms-excel"))){
    echo 'Moneda seleccionada: '.$_POST['CurrAbrev']."<br />";
    echo 'Lista de precios seleccionada: '.$_POST['TypeAbbrev']."<br /><hr />";

    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
	//$filename = "prueba.csv";
	$filename = $path.'/'.$_FILES['im']['name'];
	//$fh_out = fopen("clientes.sql","w+");
    if(@$fh_in = fopen($filename,"r")){
        $Extra = "'".$_POST['CurrAbrev']."','".$_POST['TypeAbbrev']."');";
        DB_query("BEGIN",$db,"Begin Failed !");
        while(($line = fgetcsv($fh_in,0,'|'))!==false){
            $Query2 = "update stockmaster set materialcost=%DATA1% where stockid='%DATA2%'";
            ;
            if($line!= null){

                $ErrMsg = _('No se pudo importar el archivo debido a ');
                $Query2= str_replace('%DATA1%',$line[1],$Query2);
                $Query2= str_replace('%DATA2%',$line[0],$Query2);

                $sqlSelect = "select stockid from stockmaster where stockid='".stripslashes($line[0])."';";

                $rp = DB_query($sqlSelect,$db);
                echo  $sqlSelect;
                if($rw=DB_fetch_array($rp)){
                    $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                    echo "<B>SATISFACTORIO !!!</B><br />";
                }else{
                  echo "<B>El producto no existe!!!</B><br />";
                }
                //echo $Query2."<BR>";

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