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
Jan - 2008
Importar ajustes de inventario

*/

$PageSecurity = 3;

include('includes/session.inc');

$title = _('Importar Ajuste Inventarios de Excel');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "Archivo a Importar: <INPUT TYPE=TEXT NAME=filename></INPUT>";
	
	//$DateString = Date($_SESSION['DefaultDateFormat']);
	//echo '<TR><TD>' . _('Customer Since') . ' (' . $_SESSION['DefaultDateFormat'] . "):</TD><TD><input type='Text' name='ClientSince' value=$DateString SIZE=12 MAXLENGTH=10></TD></TR>";
	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "bodega | stockid | cantidad<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	//$filename = "prueba.csv";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	echo $filename;
  if(@$fh_in = fopen($filename,"r"))
  {                                            
    
    $Extra = "0);";

	//SELECT locstock.quantity FROM locstock WHERE locstock.stockid='AEA03' AND loccode= 'MTY'
	DB_query("BEGIN",$db,"Begin Failed !");    	
    
	while(!feof($fh_in))
    {
                                                  
	 $Query2 = "INSERT INTO rh_locstocktmp (
							loccode,
							stockid,
							quantity,
							reorderlevel)
				VALUES (";
	 
    $line = fgetcsv($fh_in,1024,',',"'");
                                               
      if($line[0] == "")
      {
        //fwrite($fh_out,"\n"); 	// no contiene nada esta linea
      }                                                  
      else {
                                                  
        //fwrite($fh_out,implode($line,"\t")."\n");
       $size = sizeof($line);
      
  //     fwrite($fh_out,"(");
       
       for($i=0;$i<$size;$i++){
       	
       	$Query2 = $Query2."'".$line[$i]."',";
    //   	fwrite($fh_out,"'".$line[$i]."'");
       	
       	//if($i<$size-1){
      // 		fwrite($fh_out,",");
       	//}

       }
       //$Query2 = substr($Query2,0,strlen($Query2)-1);
      	$Query2 = $Query2.$Extra;
      	//fwrite($fh_out,$Extra);

//      	$Query2 = substr($Query2,0,strlen($Query2)-1).";";
	    echo $Query2."<br><br>";
	    
	    /* */
		$ErrMsg = _('Imposible importar articulo');
		$result = DB_query($Query2,$db,$ErrMsg,'Error: Imposible importar el QUERY: ',true);
		
      	echo "<STRONG>SATISFACTORIO</STRONG><HR>";
      	$Query2 = $Query2."),";  
      }
      
    }
    fclose($fh_in);
    DB_query("COMMIT",$db);
    //fclose($fh_out);
    //fclose($fh_out);
  }
	                                                  
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>