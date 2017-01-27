<?php

define("COMMANDO", "openssl");

class Console {
    public static function execOutput($command, $errorMessage='') {
        $output;
        $error;
        @exec($command, $output, $error);
        if($error){
            $consoleOutput = '';
            for($i = 0; $i < count($output); $i++){
                $consoleOutput .= " (" . ($i+1) . "):" .  $output[$i];
            }
            throw new Exception("El comando '$command' se ejecuto con errores. Codigo de error del comando: " . ($errorMessage?($errorMessage . ' (php)'):($error . ' (linux)')) . ($consoleOutput?(' Salida de consola: ' . $consoleOutput):''));
        }
        return @$output[0];
    }
}

class Converter {
    public static function hex2Ascii($str){
        $p = '';
        for ($i=0; $i < strlen($str); $i=$i+2)
            $p .= chr(hexdec(substr($str, $i, 2)));
        return $p;
    }
    public static function phpToMysqlTimestamp($timestampInSeconds){
        $mysqlTimestamp= date("Y-m-d H:i:s", $timestampInSeconds);
        return $mysqlTimestamp;
    }
}

class Openssl {
    private static function monthToNumber($month){
        switch($month){
            case "Jan":
                return '01';
            break;
            case "Feb":
                return '02';
            break;
            case "Mar":
                return '03';
            break;
            case "Apr":
                return '04';
            break;
            case "May":
                return '05';
            break;
            case "Jun":
                return '06';
            break;
            case "Jul":
                return '07';
            break;
            case "Aug":
                return '08';
            break;
            case "Sep":
                return '09';
            break;
            case "Oct":
                return '10';
            break;
            case "Nov":
                return '11';
            break;
            case "Dec":
                return '12';
            break;
        }
    }
    public static function dateToPHPDate($date){
        $date = preg_split ("/\s+/", $date);
        $day = $date[1];
        $month = Openssl::monthToNumber($date[0]);
        $year = $date[3];
        $time = explode(':', $date[2]);
        $hour = $time[0];
        $minute = $time[1];
        $second = $time[2];
        $date = "$year-$month-$day : $hour:$minute:$second";
        $date = str_replace(': ', '', $date);
        //$date = strtotime($date);
        return $date;
    }

}

$PageSecurity = 2;
include('includes/session.inc');
ini_set('display_errors', 1);
$title = _('Serie Certificados');
include('includes/header.inc');
$path=realpath(dirname(__FILE__)."/XMLFacturacionElectronica/csdandkey");
$ruta=dirname(__FILE__).'/companies/'.$_SESSION['DatabaseName'].'/csdandkey/';
   	if(is_dir($ruta)){
   		$ruta.='certificados/';
   		if(!is_dir($ruta)||mkdir($ruta))
	   		if(is_dir($ruta))
	   			$path=$ruta;
   }
   
if(!isset($_POST['inputPasswordContrasenaDeLlavePrivada'])){     // no se envio ningun archivo
}else{
//**************************Importar Archivos CSV******************************
$flag=false;


if(is_dir($path)||mkdir($path,0777)){
    $flag=true;
} else{
   prnMsg(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'), 'error');
}

if(($flag)){
$destino = '/XMLFacturacionElectronica/csdandkey' ;
$tamano = $_FILES['inputFileCertificado']['size'];
$tipo = $_FILES["inputFileCertificado"]["type"];

if(($tamano < 3000000)
// 		&&(
// 		($tipo=="application/octet-stream")
// 		||($tipo=='application/x-x509-ca-cert')
// 		)
		){
    if (!move_uploaded_file($_FILES['inputFileCertificado']['tmp_name'],$path.'/'.$_FILES['inputFileCertificado']['name'])){
     prnMsg(_('No se pudo cargar el archivo de certificado'), 'error');
    }else{
      $CSDFile=$path.'/'.$_FILES['inputFileCertificado']['name'];
      Console::execOutput(COMMANDO." x509 -inform der -in \"".$CSDFile."\" -out \"".$CSDFile.'.pem"');
      $cmd = COMMANDO." x509 -inform pem -in \"".$CSDFile.".pem\" -noout -serial";
      $noCertificado = Console::execOutput($cmd);
      $noCertificado = substr($noCertificado, strpos($noCertificado, '=')+1);
      $noCertificado = Converter::hex2Ascii($noCertificado);

      $fechaDeInicio = Console::execOutput(COMMANDO." x509 -inform pem -in \"".$CSDFile.".pem\" -noout -startdate");
      $fechaDeInicio = substr($fechaDeInicio, strpos($fechaDeInicio, '=')+1);
      $fechaDeInicio = Openssl::dateToPHPDate($fechaDeInicio);

      $fechaDeFin = Console::execOutput(COMMANDO." x509 -inform pem -in \"".$CSDFile.".pem\" -noout -enddate");
      $fechaDeFin = substr($fechaDeFin, strpos($fechaDeFin, '=')+1);
      $fechaDeFin = Openssl::dateToPHPDate($fechaDeFin);

    }
}

$tamano = $_FILES['inputFileLlavePrivada']['size'];
$tipo = $_FILES["inputFileLlavePrivada"]["type"];
if(($tamano < 3000000)
// 		&&(
// 		($tipo=="application/octet-stream")
// 		||($tipo=="application/x-iwork-keynote-sffkey")
// 		)
		){
    if (!move_uploaded_file($_FILES['inputFileLlavePrivada']['tmp_name'],$path.'/'.$_FILES['inputFileLlavePrivada']['name'])){
     prnMsg(_('No se pudo cargar el archivo de certificado'), 'error');
    }else{
      $KEYFile=$path.'/'.$_FILES['inputFileLlavePrivada']['name'];
      $cmd = COMMANDO." pkcs8 -inform DER -in \"".$KEYFile."\" -passin pass:".$_POST['inputPasswordContrasenaDeLlavePrivada']." -out \"".
      		$KEYFile.".pem\"";
      try{
          Console::execOutput($cmd);
          if((filesize($KEYFile.".pem")===false)||(filesize($KEYFile.".pem")==0)){
            throw new Exception('La Contrasena de Llave Privada no corresponde a la Llave Privada');
          }
      }catch(Exception $exception){
          echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' .$exception->getMessage() . '<p></div>';
          include ('includes/footer.inc');
          exit();
      }
    }
}
//************************************pfx***********************************************************************/
if(trim($_POST['inputPasswordContrasenaPfx'])!=''&&$noCertificado&&$path&&$noCertificado!=''&&$path!=''){
	$tamano = $_FILES['inputFilepfx']['size'];
	$tipo = $_FILES["inputFilepfx"]["type"];
	if(($tamano < 3000000)&&(($tipo=="application/octet-stream")||($tipo=="application/octet-stream")||($tipo=="application/octet-stream"))){
	    if (!move_uploaded_file($_FILES['inputFilepfx']['tmp_name'],$path.'/'.$noCertificado.'.pfx')){
	     prnMsg(_('No se pudo cargar el archivo de certificado'), 'error');
	    }
	}else{
	//openssl pkcs12 -export -out archivopfx.pfx -inkey llave.pem -in certificado.pem -passout pass:clavedesalida
	      $cmd = COMMANDO." pkcs12 -export -out \"".$path.'/'.$noCertificado.".pfx\" -inkey \"".$KEYFile.".pem\"  -in \"".$CSDFile.".pem\" -passout pass:\"".$_POST['inputPasswordContrasenaPfx']."\" ";
	     // echo $cmd;
	      try{
	          Console::execOutput($cmd);
	          if((filesize($path.'/'.$noCertificado.".pfx")===false)||($path.'/'.filesize($noCertificado.".pfx")==0)){
	          	echo $cmd;
	            throw new Exception('No fue posible crear el pfx');
	          }
	      }catch(Exception $exception){
	          echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' .$exception->getMessage() . '<p></div>';
	          include ('includes/footer.inc');
	          exit();
	      }
	}
}
//**************************************************************************************************************/
		$Directorio=dirname($CSDFile);
		rename($CSDFile,$Directorio.'/'.$noCertificado.'.cer');
		rename($CSDFile.'.pem',$Directorio.'/'.$noCertificado.'.cer.pem');
		rename($KEYFile,$Directorio.'/'.$noCertificado.'.key');
		rename($KEYFile.'.pem',$Directorio.'/'.$noCertificado.'.key.pem');
		if(!isset($_POST['inputPasswordContrasenaPfx'])||$_POST['inputPasswordContrasenaPfx']=='')
			$_POST['inputPasswordContrasenaPfx']=$_SESSION['CompanyRecord']['gstno'];
        $sql = "insert into rh_csd (certificado,`key`,fechaExp,fechaCad,noserie,pass,pfx,pfxpass) values ('".$noCertificado.'.cer'."','".$noCertificado.".key.pem','".$fechaDeInicio."','".$fechaDeFin."','".$noCertificado."',SHA1('".$_POST['inputPasswordContrasenaDeLlavePrivada']."'),'".$noCertificado.".pfx','".$_POST['inputPasswordContrasenaPfx']."');";
        $result = DB_query($sql,$db);
        if(DB_error_no($db)) {
          echo '<div class="error"><p><b>' . _('ERROR') . '</b> : No se pudo agregar el certificado a la BD<p></div>';
        }
}
}
?>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<div id="divAltaDeSello" align="center">
    <?php if(isSet($_GET['msgAltaDeSello'])) prnMsg($_GET['msgAltaDeSello'], $_GET['msgType']); ?>
    <div id="divLigas">
        <a href="rh_CFDI_folio.php">Administraci&oacute;n de Folios</a>
        <br />
        <a href="rh_CFDI_serie.php">Administraci&oacute;n de Series</a>
        <br />
    </div>
    <br />
    <div id="divFormAltaDeSello">
        <form name="formAltaDeSello" method="POST" enctype="multipart/form-data" action="">
            <input type="hidden" name="request" value="altaDeSello" />
            <div id="divTableAltaDeSello">
                <table id="tableAltaDeSello">
                    <tbody>
                        <tr class="headland">
                            <td colspan="2">
                                <?php echo _('Alta de Sello') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileCertificado">
                                    <?php echo _('Certificado') . ' (.cer)' ?>
                                </label>
                            </td>
                            <td>
                                <input type="file" id="inputFileCertificado" name="inputFileCertificado" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileLlavePrivada">
                                    <?php echo _('Llave Privada') . ' (.key)' ?>
                                </label>
                            </td>
                            <td>
                                <input type="file" id="inputFileLlavePrivada" name="inputFileLlavePrivada" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputPasswordContrasenaDeLlavePrivada">
                                    <?php echo _('Contrase&ntilde;a de Llave Privada') ?>
                                </label>
                            </td>
                            <td>
                                <input type="password" id="inputPasswordContrasenaDeLlavePrivada" name="inputPasswordContrasenaDeLlavePrivada" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputFileLlavePrivada">
                                    <?php echo _('Sello PFX') . ' (.pfx)' ?>
                                </label>
                            </td>
                            <td>
                                <input type="file" id="inputFilepfx" name="inputFilepfx" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label class="requiredField" for="inputPasswordContrasenaDeLlavePrivada">
                                    <?php echo _('Contrase&ntilde;a de PFX') ?>
                                </label>
                            </td>
                            <td>
                                <input type="password" id="inputPasswordContrasenaPfx" name="inputPasswordContrasenaPfx" />
                            </td>
                        </tr>
                        <tr>
                            <td class="center" colspan="2">
                                <input type="submit" value="Crear"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <br />
    <br />
    <div id="divTableSello">
    </div>
</div>

<?php
echo "<center>";
echo "<table class='headland'>";
echo "    <tr><td>no Certificado</td><td>Fecha Expedicion</td><td>Fecha Caducidad</td></tr>";

$sql= "select * from rh_csd";
$result=DB_query($sql,$db);
while($rs=DB_fetch_assoc($result)){
    echo "    <tr><td>".$rs['noserie']."</td><td>".$rs['fechaExp']."</td><td>".$rs['fechaCad'];
    if(!is_file($path."/".$rs['pfx'])){
		echo ' No se ha generado el PFX';
		echo '</td></tr><tr><td colspan=3>';
		if($rs['pfxpass']=='')
			$rfc=$_SESSION['CompanyRecord']['gstno'];
		else 
			$rfc=$rs['pfxpass'];
		$cmd = COMMANDO." pkcs12 -export -out \"".$path.'/'.$rs['pfx']."\" -inkey \"".$path.'/'.$rs['key']."\"  -in \"".$path.'/'.$rs['certificado'].".pem\" -passout pass:\"".$rfc."\" ";
		try{
			Console::execOutput($cmd);
		}catch(Exception $exception){
			echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' .$exception->getMessage() . '<p></div>';
			echo htmlentities($cmd); 
		}
    }
    echo "</td></tr>";
}
echo "</table>";
echo "</center>";

include ('includes/footer.inc');
