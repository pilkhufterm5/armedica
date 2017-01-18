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

$title = _('Importar CustBranch de Excel');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "Archivo a Importar: <INPUT TYPE=TEXT NAME=filename></INPUT>";
	
	//SQL to poulate account selection boxes
	$sql = "SELECT salesmanname, salesmancode FROM salesman";

	$result = DB_query($sql,$db);

	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no sales people defined as yet') . ' - ' . _('customer branches must be allocated to a sales person') . '. ' . _('Please use the link below to define at least one sales person'),'error');
		echo "<BR><A HREF='$rootpath/SalesPeople.php?" . SID . "'>"._('Define Sales People').'</A>';
		include('includes/footer.inc');
		exit;
	}
	
	echo '<TR><TD>'._('Forward Date After (day in month)').':</TD>';
	echo '<TD><input type="Text" name="FwdDate" SIZE=4 MAXLENGTH=2 value='. $_POST['FwdDate'].'></TD></TR>';

	echo '<TR><TD>'._('Salesperson').':</TD>';
	echo '<TD><SELECT name="Salesman">';

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['salesmancode']==$_POST['Salesman']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['salesmancode'] . '>' . $myrow['salesmanname'];

	} //end while loop

	echo '</SELECT></TD></TR>';

	DB_data_seek($result,0);
	
	$sql = 'SELECT areacode, areadescription FROM areas';
	$result = DB_query($sql,$db);
	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no areas defined as yet') . ' - ' . _('customer branches must be allocated to an area') . '. ' . _('Please use the link below to define at least one sales area'),'error');
		echo "<BR><A HREF='$rootpath/Areas.php?" . SID . "'>"._('Define Sales Areas').'</A>';
		include('includes/footer.inc');
		exit;
	}

	echo '<TR><TD>'._('Sales Area').':</TD>';
	echo '<TD><SELECT name="Area">';
	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['areacode']==$_POST['Area']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['areacode'] . '>' . $myrow['areadescription'];

	} //end while loop


	echo '</SELECT></TD></TR>';
	DB_data_seek($result,0);

	$sql = 'SELECT loccode, locationname FROM locations';
	$result = DB_query($sql,$db);

	if (DB_num_rows($result)==0){
		echo '</TABLE>';
		prnMsg(_('There are no stock locations defined as yet') . ' - ' . _('customer branches must refer to a default location where stock is normally drawn from') . '. ' . _('Please use the link below to define at least one stock location'),'error');
		echo "<BR><A HREF='$rootpath/Locations.php?" . SID . "'>"._('Define Stock Locations').'</A>';
		include('includes/footer.inc');
		exit;
	}
	
	echo '<TR><TD>'._('Draw Stock From').':</TD>';
	echo '<TD><SELECT name="DefaultLocation">';

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['loccode']==$_POST['DefaultLocation']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['loccode'] . '>' . $myrow['locationname'];

	} //end while loop

	echo '</SELECT></TD></TR>';
	
		echo '<TR><TD>'._('Tax Group').':</TD>';
	echo '<TD><SELECT name="TaxGroup">';

	DB_data_seek($result,0);

	$sql = 'SELECT taxgroupid, taxgroupdescription FROM taxgroups';
	$result = DB_query($sql,$db);

	while ($myrow = DB_fetch_array($result)) {
		if ($myrow['taxgroupid']==$_POST['TaxGroup']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['taxgroupid'] . '>' . $myrow['taxgroupdescription'];

	} //end while loop

	echo '</SELECT></TD></TR>';
	echo '<TR><TD>'._('Disable transactions on this branch').":</TD><TD><SELECT NAME='DisableTrans'>";
	if ($_POST['DisableTrans']==0){
		echo '<OPTION SELECTED VALUE=0>' . _('Enabled');
		echo '<OPTION VALUE=1>' . _('Disabled');
	} else {
		echo '<OPTION SELECTED VALUE=1>' . _('Disabled');
		echo '<OPTION VALUE=0>' . _('Enabled');
	}

	echo '	</SELECT></TD></TR>';

	echo '<TR><TD>'._('Default freight company').":</TD><TD><SELECT name='DefaultShipVia'>";
	$SQL = 'SELECT shipper_id, shippername FROM shippers';
	$ShipperResults = DB_query($SQL,$db);
	while ($myrow=DB_fetch_array($ShipperResults)){
		if ($myrow['shipper_id']==$_POST['DefaultShipVia']){
			echo '<OPTION SELECTED VALUE=' . $myrow['shipper_id'] . '>' . $myrow['shippername'];
		}else {
			echo '<OPTION VALUE=' . $myrow['shipper_id'] . '>' . $myrow['shippername'];
		}
	}

	echo '</SELECT></TD></TR>';
	
		/* This field is a default value that will be used to set the value
	on the sales order which will control whether or not to display the
	company logo and address on the packlist */
	echo '<TR><TD>' . _('Default Packlist') . ":</TD><TD><SELECT NAME='DeliverBlind'>";
        for ($p = 1; $p <= 2; $p++) {
            echo '<OPTION VALUE=' . $p;
            if ($p == $_POST['DeliverBlind']) {
                echo ' SELECTED>';
            } else {
                echo '>';
            }
            switch ($p) {
                case 1:
                    echo _('Show company details and logo'); break;
                case 2:
                    echo _('Hide company details and logo'); break;
            }
        }
    echo '</SELECT></TD></TR>';
	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "codigo cliente | codigo sucursal | nombre sucursal| nombre contacto | direccion1 | direccion2 | direccion3 | direccion4 | direccion5 | direccion6 | dias de entrega | telefono | fax |email
fax | e-mail | nombre del contacto<br>";    	
    //fclose($fh_out);
    //fclose($fh_out);
	
} else{								// se envio un archivo
	//$filename = "prueba.csv";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	
  if(@$fh_in = fopen("{$filename}","r"))
  {
                                                  
    //$fh_out = fopen("{$filename}.sql","a");
    
	echo $_POST['EstDeliveryDays']."<br>";       		
	echo $_POST['FwdDate']."<br>";
	echo $_POST['Salesman']."<br>";
	echo $_POST['Area']."<br>";	
	echo $_POST['DefaultLocation']."<br>";
    echo $_POST['TaxGroup']."<br>";
    echo $_POST['DisableTrans']."<br>";
    echo $_POST['DefaultShipVia']."<br>";
    echo $_POST['DeliverBlind']."<br>";
    
    $Extra = "'".$_POST['EstDeliveryDays']."','".$_POST['FwdDate']."','".$_POST['Salesman']."','".$_POST['Area']."','".$_POST['TaxGroup']."','".
    $_POST['DefaultLocation']."','".$_POST['DisableTrans']."','".$_POST['DefaultShipVia']."','".$_POST['DeliverBlind']."');";

//    fwrite($fh_out,$Query2);
	//DB_query("BEGIN",$db,"Begin Failed !",true);    	
    
	while(!feof($fh_in))
    {
                                                  
	 $Query2 = "INSERT INTO custbranch (
	 					debtorno,
	 					branchcode,
						brname,
						braddress1,
						braddress3,
						braddress4,
						braddress5,
						braddress6,
						
						fwddate,
						salesman,
						area,
						taxgroupid,
						defaultlocation,
						disabletrans,
						defaultshipvia,
                        deliverblind)
				VALUES (";
	 
    $line = fgetcsv($fh_in,1024,'|');
                                                  
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
	    
	    /* GENERAR EL INSERT */
		$ErrMsg = _('No se pudo importar el archivo debido a ');
		$result = DB_query($Query2,$db,$ErrMsg,true);
		
      	//echo "<STRONG>SATISFACTORIO</STRONG><HR>";
      	//$Query2 = $Query2."),";  
      }
      
    }
    fclose($fh_in);
    DB_query("COMMIT",$db,"COMMIT Failed !");    	
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