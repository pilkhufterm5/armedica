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

$title = _('Saldo Inicial en Proveedores');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
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
	echo "supplierno | saldo | texto factura<br></center></h2>";
	
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
	DB_query("BEGIN",$db);
	$i=1;
	while(!feof($fh_in))
    {
                                                  
	 
	$line = fgetcsv($fh_in,1024,'|');
                                                  
      if($line[0] == "")
      {
        // no contiene nada esta linea
      }else {
       
      	/*
      	
      	BEGIN
		SELECT typeno FROM systypes WHERE typeid = 20
		UPDATE systypes SET typeno = 1105 WHERE typeid = 20
		SELECT periodno FROM periods WHERE lastdate_in_period < '2007/09/30' AND lastdate_in_period >= '2007/08/30'
		
		-INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount, jobref) VALUES (20, 1105, '2007/08/30', 5, 506011001, 'PROV199 observaciones', 1000, '')
		
		-INSERT INTO rh_suppinvdetails ( transno, trandate, period, account, narrative, amount) VALUES ( '1105', '2007/08/30', 5, 506011001, 'PROV199 observaciones', 1000)
		
		-INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (20, 1105, '2007/08/30', 5, 110106003, 'PROV199 - Inv 1 IVA 15.00% MN150 @ exch rate 1', 150)
		
		-INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (20, 1105, '2007/08/30', 5, 210001001, 'PROV199 - Inv 1 MN1,150.00 @ una tasa de 1', -1150)
		
		INSERT INTO supptrans (transno, type, supplierno, suppreference, trandate, duedate, ovamount, ovgst, rate, transtext, rh_invdate) VALUES (1105, 20 , 'PROV199', '1', '2007/08/30', '2007/09/01', 1000, 150, 1, '', '2007/08/30')
		
		INSERT INTO supptranstaxes (supptransid, taxauthid, taxamount) VALUES (1911, 14, 150)
		COMMIT
      	
      	*/
      	$tax = ($line[1]*0);
      	$sql = "SELECT typeno FROM systypes WHERE typeid = 20";
      	$res = DB_query($sql,$db,'','',true);
      	$typeno = DB_fetch_array($res);
      	
      	$sql = "UPDATE systypes SET typeno = ".($typeno['typeno']+1)." WHERE typeid = 20";
      	$res = DB_query($sql,$db,'','',true);
      	
      	$sql = "SELECT periodno FROM periods WHERE lastdate_in_period = '".$_POST['Fecha']."'";
      	$res = DB_query($sql,$db,''.'',true);
      	$per = DB_fetch_array($res);
      	
      	/*$sql = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount, jobref) VALUES (20, '".($typeno['typeno']+1)."', '".$_POST['Fecha']."', ".$per['periodno'].", 506011001, '".$line[0]." SALDO INICIAL', '".$line[1]."', '')";
		$res = DB_query($sql,$db,'','',true);*/

		$sql = "INSERT INTO rh_suppinvdetails ( transno, trandate, period, account, narrative, amount) VALUES ('".($typeno['typeno']+1)."', '".$_POST['Fecha']."', ".$per['periodno'].", 506011001, '".$line[0]." SALDO INICIAL', '".$line[1]."')";
		$res = DB_query($sql,$db,'','',true);
		
		/*$sql = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (20, '".($typeno['typeno']+1)."', '".$_POST['Fecha']."', ".$per['periodno'].", 110106003, '".$line[0]." - Inv ".$i." IVA 15.00% MN".$tax." @ exch rate 1', '".$tax."')";
      	$res = DB_query($sql,$db,'','',true);
      	
      	$sql = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES (20, '".($typeno['typeno']+1)."', '".$_POST['Fecha']."', ".$per['periodno'].", 210001001, '".$line[0]." - Inv ".$i." MN".number_format(($line[1]+tax),2)." @ una tasa de 1', ".(-1)*($line[1]+$tax).")";
		$res = DB_query($sql,$db,'','',true);*/
		
		$sql = "INSERT INTO supptrans (transno, type, supplierno, suppreference, trandate, duedate, ovamount, ovgst, rate, transtext, rh_invdate) VALUES ('".($typeno['typeno']+1)."', 20 , '".$line[0]."', '".$i."', '".$_POST['Fecha']."', '".$_POST['Fecha']."', ".$line[1].", ".$tax.", 1, '".$line[2]."', '".$_POST['Fecha']."')";
		$res = DB_query($sql,$db,'','',true);
		
		$SuppTransID = DB_Last_Insert_ID($db,'supptrans','id');
		
		/*$sql = "INSERT INTO supptranstaxes (supptransid, taxauthid, taxamount) VALUES (".$SuppTransID.", 14, ".$tax.")";
		$res = DB_query($sql,$db,'','',true);*/
		
      	echo "FECHA: ".$_POST['Fecha']." PROVEEDORE: ".$line[0]." SALDO: ".$line[1]." COMENTARIOS: ".$line[2];
		echo "<CENTER><B>SATISFACTORIO !!!</B></CENTER><HR>";
		
      }
     $i++;
    }
    DB_query("COMMIT",$db);
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