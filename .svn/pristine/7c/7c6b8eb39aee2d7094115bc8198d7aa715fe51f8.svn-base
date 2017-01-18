<?php
$PageSecurity = 2;

include('includes/session.inc');

$title = _('Importar Precios del Inventario de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if(!isset($_POST['opc1'])){     // no se envio ningun archivo
    echo '<CENTER><H1>Relacion Cliente-Vendedor</H1><br /><br /><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";
    echo '<input type="hidden" name="opc1" value="0" />';
    echo '<tr><td colspan="2" align="center"><input type="submit" name="send" value="Cargar" /></td></tr></table></form>';
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
                $size = sizeof($line);
                $Query2 = "update custbranch set salesman='".$line[1]."' where branchcode='".$line[0]."';";

                $ErrMsg = _('No se pudo importar el archivo debido a ');

                $sqlSelect = "select branchcode from custbranch where branchcode='".stripslashes($line[0])."';";
                $rp = DB_query($sqlSelect,$db);
                if(DB_num_rows($rp)>0){
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