<?php

/*
| delimitador

Realhost
BOWIKAXU
Oct-2006
Importar Clientes a la tabla debtorsmaster

*/

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Importar Clientes de Excel');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "Archivo a Importar: <INPUT TYPE=TEXT NAME=filename></INPUT>";
	
	
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM></CENTER>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "codigo | nombre | direccion1 | direccion3 | direccion4 | direccion5 | direccion6 | RFC<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	
	$filename = $_POST['filename'];
	
	echo $filename;
  if(@$fh_in = fopen($filename,"r"))
  {
                                                  
	//DB_query("BEGIN",$db,"Begin Failed !");    	
    
	while(!feof($fh_in))
    {
                                                  
	 $Query2 = "update debtorsmaster SET ";
/*
	 						debtorno, 0
							name,	1
							address1, 2
							address3, 3
							address4, 4
							address5, 5
							address6, 6
							taxref, 7
*/
	 
    $line = fgetcsv($fh_in,1024,'|',"'");
                                                  
      if($line[0] == "")
      {
        //fwrite($fh_out,"\n"); 	// no contiene nada esta linea
      }                                                  
      else {
                                                  
		$Query2 .= "name = '".$line[1]."', ";
		$Query2 .= "address1 = '".$line[2]."', ";
		$Query2 .= "address3 = '".$line[3]."', ";
		$Query2 .= "address4 = '".$line[4]."', ";
		$Query2 .= "address5 = '".$line[5]."', ";
		$Query2 .= "address6 = '".$line[6]."', ";
		$Query2 .= "taxref = '".$line[7]."' ";
		$Query2 .= "WHERE debtorno = '".$line[0]."';";

       }
  
	    echo $Query2."<br><br>";
	    
	    /* */
		$ErrMsg = _('Imposible importar cliente ');
		//$result = DB_query($Query2,$db,$ErrMsg,'Error: Imposible importar el QUERY: ',true);
		
      	//echo "<STRONG>SATISFACTORIO</STRONG><HR>";
      	//$Query2 = $Query2."),";  
      }
      //DB_query("COMMIT",$db);
    }
    
                                                     
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>