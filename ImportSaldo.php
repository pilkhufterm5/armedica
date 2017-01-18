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

$PageSecurity = 3;

include('includes/session.inc');
include('rh_saldos.php');

$title = _('Saldo Inicial en Clientes');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo'<TABLE>';
	echo "<TR><TD>Archivo a Importar:</TD><TD> <INPUT TYPE='text' NAME='filename'></INPUT></TD></TR>";
	
	echo '<TR><TD>'._('Select the balance date').":</TD><TD><SELECT Name='Fecha'>";

	$sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	$Periods = DB_query($sql,$db);

	while ($myrow=DB_fetch_array($Periods,$db)){
		if( $_POST['Fecha']== $myrow['lastdate_in_period']){
			echo '<OPTION SELECTED VALUE=' . $myrow['lastdate_in_period'] . '>' . ConvertSQLDate($myrow['lastdate_in_period']);
		} else {
			echo '<OPTION VALUE=' . $myrow['lastdate_in_period'] . '>' . ConvertSQLDate($myrow['lastdate_in_period']);
		}
	}

	echo '</SELECT></TD></TR>';
	
// OPCIONES PARA INVENTARIOS

	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "debtorno | branchcode | saldo<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	//$filename = "prueba.csv";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	
  if(@$fh_in = fopen("{$filename}","r"))
  {
                                                  
//    fwrite($fh_out,$Query2);
	// la variable products debe de ser $products[i][ProductId] y $products[i][Quantity] = 1 siempre
	$products[0]['ProductId'] = 'SALDO_INICIAL';
	$products[0]['Quantity'] = 1;
	while(!feof($fh_in))
    {
                                                  
	 
	$line = fgetcsv($fh_in,1024,'|');
      foreach($line as $id=>$val){
			$line[$id]=trim($line[$id]," \t\r\0\n\x0'");
	  }
      if($line[0] == "")
      {
        // no contiene nada esta linea
      }else {
       
      	$tax = ($line[2]*.15);
      	echo "FECHA: ".$_POST['Fecha']." PRODUCTO: ".$products[0]['ProductId']." SUB: ".$line[2]." CLIENTE: ".$line[0]." BRANCH: ".$line[1]."<BR>";
      	
      	$sql = "SELECT debtorno FROM custbranch WHERE debtorno ='".$line[0]."' AND branchcode = '".$line[1]."'";
      	$res = DB_query($sql,$db);
      	
      	if(DB_num_rows($res)>0){
      		echo "ORDER: ".create_order($products,$line[2],$tax,($line[2]+$tax),$line[0],$line[1],$_POST['Fecha'],$line[3]);
      		echo "<CENTER><B>SATISFACTORIO !!!</B></CENTER><HR>";      		
      	}else {
      		echo "EROR: CLIENTE O SUCURSAL INCORRECTOS<br>";
      		echo "<CENTER><B><FONT COLOR=RED>ERRORES ENCONTRADOS !!!</FONT></B></CENTER><HR>";
      	}      	
		
      }
      
    }
    fclose($fh_in);
  }
	                                                  
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>
