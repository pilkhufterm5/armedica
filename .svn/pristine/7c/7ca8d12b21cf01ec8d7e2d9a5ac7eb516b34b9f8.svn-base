<?php
/* $Revision: 14 $ */

$PageSecurity=15;

include('includes/session.inc');

$title=_('File Upload Result');
include('config.php');
include('includes/header.inc');

$files_path = 'companies/'.$_SESSION['DatabaseName'].'/rh_files';

if(!isset($_POST['type']) OR !isset($_POST['trans']) OR $_POST['trans']<0){
	prnMsg("Favor de corregir los datos",'error');
	echo "<A HREF='rh_upload.php'>Regresar</A>";
	include('includes/footer.inc');
	exit;
}

if ($_FILES["userfile"]["error"] > 0)
  {
  	echo "Error: " . $_FILES["userfile"]["error"] . "<br />";
	echo "<CENTER>Lo Sentimos ha ocurrido un error al intentar subir el archivo. Verifique lo siguiente:
	<br><li>El Tama&nacute;o maximo es de 1MB
	<br><li>Extension sin restriccion excepto (.php, .htm, .html)
	</CENTER>";
  }
else
  {
	  //echo "File: " . $_FILES["userfile"]["name"] . "<br />";
	  echo "Type: " . $_FILES["userfile"]["type"] . "<br />";
	  //echo "Size: " . ($_FILES["userfile"]["size"] / 1024) . " Kb<br />";
	  //echo "Stored in: " . $_FILES["userfile"]["tmp_name"]."<BR>";
	  $type = $_POST['type'];
	  $transno = $_POST['trans'];
	  $size = number_format(($_FILES["userfile"]["size"] / 1024),0);
	  $filename = explode(".",$_FILES["userfile"]["name"],2);

// verificar que no tenga la extension php, htm o html
	if($filename[1] == 'php' OR $filename[1]=='html' OR $filename[1]=='htm'){
		
		//echo "Error: " . $_FILES["userfile"]["error"] . "<br />";
		echo "<CENTER>Lo Sentimos ha ocurrido un error al intentar subir el archivo. Verifique lo siguiente:
		<br><li>El Tama&nacute;o maximo es de 1MB
		<br><li>Extension sin restriccion excepto (.php, .htm, .html)
		</CENTER>";
		includes('includes/footer.inc');
		exit;
		
	}

	$sql = "INSERT INTO rh_files (type, transno, trandate, user_, size, filename, comments) VALUES (
			".$type.",
			".$transno.",
			NOW(),
			'".$_SESSION['UserID']."',
			'".$size."',
			'".$_FILES["userfile"]["name"]."',
			'".DB_escape_string($_POST['comments'])."')";
			
	DB_query($sql,$db,"ERROR: Imposible insertar datos del archivo");
	$id = DB_Last_Insert_ID($db,'rh_files','id');
	
	move_uploaded_file($HTTP_POST_FILES['userfile']['tmp_name'], $files_path.'/'.$id.'.'.$filename[1]);
	prnMsg( _('The file') . ' ' . $_FILES['userfile']['name'] . ' ' . _('ha sido exitosamente guardado.'),'info');
 }

include('includes/footer.inc');

?>
