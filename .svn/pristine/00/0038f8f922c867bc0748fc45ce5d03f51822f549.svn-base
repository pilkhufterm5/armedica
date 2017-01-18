<?php
//rleal Feb 17 2010 a petición de Ever ticket 2011021507000051
$PageSecurity = 2;

include('includes/session.inc');

$title = _('Importar Precios del Inventario de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_POST['flag'])){     // no se envio ningun archivo
    
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";

// OPCIONES PARA INVENTARIOS
    echo "<input type='hidden' name='flag' value='0'/>";
    echo "</CENTER></TABLE>";
    echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
    echo "</FORM>";     
    echo "<center><h2> Formato del archivo: <br>";
    //echo "stockid | Descripcion Corta | Descripcion Larga | Cantidad economica reordenar | Volumen (mts cubicos) | Peso (kgs) | Codigo de Barras<br></center></h2>";
    echo "Codigo | Marca<br></center></h2>";
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
    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
	//$filename = "prueba.csv";
	$filename = $path.'/'.$_FILES['im']['name'];
	//$fh_out = fopen("clientes.sql","w+");
    if(@$fh_in = fopen($filename,"r")){
        DB_query("BEGIN",$db,"Begin Failed !");
        while(($line = fgetcsv($fh_in,0,'|'))!==false){
            if($line!= null){
                            $Query2 = "INSERT INTO rh_marca (
                            codigo,
                            nombre)
                        VALUES ('".$line[0]."','".$line[1]."')";
                 echo $Query2;
                $ErrMsg = _('No se pudo importar el archivo debido a ');

                $sqlSelect = "select codigo from rh_marca where codigo='".$line[0]."'";
                echo $sqlSelect;
                $rp = DB_query($sqlSelect,$db);
                if(DB_num_rows($rp)==0){
                 $result = DB_query($Query2,$db);
                 echo "<B>SATISFACTORIO !!!</B>";
                }else{
                    $Query2="update rh_marca set nombre='".$line[1]."' where codigo='".$line[0]."'";
                    $result = DB_query($Query2,$db);
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