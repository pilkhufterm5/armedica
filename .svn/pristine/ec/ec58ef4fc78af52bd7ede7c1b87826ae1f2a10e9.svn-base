<?php

/*
| delimitador

Realhost
BOWIKAXU
March 2007
Importar Contactos Proveedores

*/

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Importar Proveedores');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "Archivo a Importar: <INPUT TYPE=TEXT NAME=filename></INPUT>";
	
	echo "</SELECT></TD></TR></TABLE><p><CENTER><INPUT TYPE='Submit' NAME='submit' VALUE='" . _('Importar Contactos Proveedores') . "'>";
	echo '</FORM>';
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "codigo_prov | tel | contacto<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	//$filename = "prueba.csv";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	echo $filename;
  if(@$fh_in = fopen($filename,"r"))
  {
                                                  
    //$fh_out = fopen("{$filename}.sql","a");
    
    $Extra = "'".$_POST['PaymentTerms']."','".$_POST['CurrCode'].
    "','".$_POST['TaxGroup']."',".$_POST['Remittance'].");";

//    fwrite($fh_out,$Query2);
	DB_query("BEGIN",$db,"Begin Failed !");    	
    
	while(!feof($fh_in))
    {
                                                  
	 $Query2 = "INSERT INTO bom (workcentreadded, loccode, effectiveafter, effectiveto, parent, component, quantity) VALUES
	 ('MTY','MTY','2007-04-11','2037-04-12'";
	 
    $line = fgetcsv($fh_in,1024,'|',"'");
                                                  
      if($line[0] == "")
      {
        //fwrite($fh_out,"\n"); 	// no contiene nada esta linea
      }                                                  
      else {
                                                  
        //fwrite($fh_out,implode($line,"\t")."\n");
       $size = sizeof($line);
      
  //     fwrite($fh_out,"(");
       
       for($i=0;$i<$size;$i++){
       	
       	$Query2 = $Query2.",'".$line[$i]."'";
    //   	fwrite($fh_out,"'".$line[$i]."'");
       	
       	//if($i<$size-1){
      // 		fwrite($fh_out,",");
       	//}

       }
		
       $Query2 .= ");";
//      	$Query2 = substr($Query2,0,strlen($Query2)-1).";";
	    echo $Query2."<br>";
	    
	    /* */
		$ErrMsg = _('Imposible importar contactos proveedores ');
		//$result = DB_query($Query2,$db,$ErrMsg,'Error: Imposible importar el QUERY: ',true);
		
      	//echo "<STRONG>SATISFACTORIO</STRONG><HR>";
      	//$Query2 = $Query2."),";  
      }
      
    }
    fclose($fh_in);
    DB_query("COMMIT",$db);
    fclose($fh_out);
    fclose($fh_out);
  }
	                                                  
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>