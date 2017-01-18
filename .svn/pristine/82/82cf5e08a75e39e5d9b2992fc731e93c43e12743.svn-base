<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */
/*
| delimitador

Realhost
BOWIKAXU
Oct-2006
ACTUALIZAR USUARIOS

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

$PageSecurity = 3;

include('includes/session.inc');
//include('rh_saldo.php');

$title = _('ACTUALIZAR USUARIOS');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "Archivo a Importar: <INPUT TYPE='text' NAME='filename'></INPUT>";
	
// OPCIONES PARA INVENTARIOS

	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "usuario | password<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	//$filename = "prueba.csv";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	DB_query("BEGIN",$db);
  if(@$fh_in = fopen("{$filename}","r"))
  {
	// CryptPass($_POST['Password'])
	while(!feof($fh_in))
    {

	$line = fgetcsv($fh_in,1024,'|');
                                                  
      if($line[0] == ""){
        // no contiene nada esta linea
      }else {
       
      	//$tax = ($line[2]*.15);
      	//create_order($products,$line[2],$tax,($line[2]+$tax),$line[0],$line[1]);
      	$password = CryptPass($line[1]);
      	$sql = "UPDATE www_users SET password = '".$password."' WHERE userid = '".$line[0]."'";
      	$res = DB_query($sql,$db,'ERROR: imposible hacer el update de '.$line[0],'el sql es: '.$sql,true);
      	
      	echo "<CENTER><B>".$line[0].' - '.$line[1]."</B></CENTER><HR>";
		echo "<CENTER><B>SATISFACTORIO !!!</B></CENTER><HR>";
      }
      
    }
    fclose($fh_in);
	DB_query("COMMIT",$db);

  }
	                                                  
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>