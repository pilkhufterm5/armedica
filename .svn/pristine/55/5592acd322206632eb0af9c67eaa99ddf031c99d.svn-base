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

if(!isset($_POST["SalesType"])){ 	// no se envio ningun archivo

    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";
	
		$result=DB_query('SELECT typeabbrev, sales_type FROM salestypes ',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no sales types/price lists currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		echo '<TR><TD>' . _('Sales Type/Price List') . ":</TD>
			<TD><SELECT name='SalesType'>";

		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['typeabbrev'] . "'>" . $myrow['sales_type'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}
	
	//$DateString = Date($_SESSION['DefaultDateFormat']);
	//echo '<TR><TD>' . _('Customer Since') . ' (' . $_SESSION['DefaultDateFormat'] . "):</TD><TD><input type='Text' name='ClientSince' value=$DateString SIZE=12 MAXLENGTH=10></TD></TR>";
	
	echo '<TR><TD>' . _('Credit Status') . ":</TD><TD><SELECT name='HoldReason'>";

	$result=DB_query('SELECT reasoncode, reasondescription FROM holdreasons',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no credit statuses currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		while ($myrow = DB_fetch_array($result)) {
			echo "<OPTION VALUE='". $myrow['reasoncode'] . "'>" . $myrow['reasondescription'];
		} //end while loop
		DB_data_seek($result,0);
		echo '</SELECT></TD></TR>';
	}
		
	$result=DB_query('SELECT currency, currabrev FROM currencies',$db);
	if (DB_num_rows($result)==0){
		$DataError =1;
		echo '<TR><TD COLSPAN=2>' . prnMsg(_('There are no currencies currently defined - go to the setup tab of the main menu and set at least one up first'),'error') . '</TD></TR>';
	} else {
		if (!isset($_POST['CurrCode'])){
			$CurrResult = DB_query('SELECT currencydefault FROM companies WHERE coycode=1',$db);
			$myrow = DB_fetch_row($CurrResult);
			$_POST['CurrCode'] = $myrow[0];
		}
		echo '<TR><TD>' . _('Customer Currency') . ":</TD><TD><SELECT name='CurrCode'>";
		while ($myrow = DB_fetch_array($result)) {
			if ($_POST['CurrCode']==$myrow['currabrev']){
				echo '<OPTION SELECTED VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			} else {
				echo '<OPTION VALUE='. $myrow['currabrev'] . '>' . $myrow['currency'];
			}
		} //end while loop
		DB_data_seek($result,0);

		echo '</SELECT></TD></TR>';
	}

	if($auth){ // authorized
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
	}else { // not authorized
		echo "<TD>";
		// bowikaxu - get the first salesman
		if(isset($_POST['Salesman'])){
			$sql = "SELECT salesmanname, salesmancode FROM salesman WHERE salesmancode = '".$_POST['Salesman']."'";
			$res12 = DB_query($sql,$db);
			if(DB_num_rows($res12)>0){
				echo $_POST['Salesman'];
				echo "<INPUT TYPE=hidden name='Salesman' value='".$_POST['Salesman']."'>";
			}else {
				echo "<FONT COLOR=RED>"._('No Valid Sales Mans')."</FONT>";
				exit;
			}
		}else {
			$sql = "SELECT salesmanname, salesmancode FROM salesman ORDER BY salesmancode LIMIT 1";
			$res12 = DB_query($sql,$db);
			$sman = DB_fetch_array($res12);
			if(DB_num_rows($res12)<=0){
				echo "<FONT COLOR=RED>"._('No Valid Sales Mans')."</FONT>";
				exit;
			}else {
				echo $sman['salesmanname'];
				echo "<INPUT TYPE=hidden name='Salesman' value='".$sman['salesmancode']."'>";
			}
			//end while loop
        echo "</TD></TR>";

	$sql = 'SELECT areacode, areadescription FROM areas';
	$result = DB_query($sql,$db);
	echo '<TR><TD>'._('Sales Area').':</TD>';
	echo '<TD><SELECT tabindex=14 name="Area">';
	while ($myrow = DB_fetch_array($result)) {
		if (isset($_POST['Area']) and $myrow['areacode']==$_POST['Area']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['areacode'] . '>' . $myrow['areadescription'];

	} //end while loop
	echo '</SELECT></TD></TR>';

	$sql = 'SELECT loccode, locationname FROM rh_locations';
	$result = DB_query($sql,$db);
	echo '<TR><TD>'._('Draw Stock From').':</TD>';
	echo '<TD><SELECT tabindex=15 name="DefaultLocation">';

	while ($myrow = DB_fetch_array($result)) {
		if (isset($_POST['DefaultLocation']) and $myrow['loccode']==$_POST['DefaultLocation']) {
			echo '<OPTION SELECTED VALUE=';
		} else {
			echo '<OPTION VALUE=';
		}
		echo $myrow['loccode'] . '>' . $myrow['locationname'];

	} //end while loop
	echo '</SELECT></TD></TR>';

    echo '<TR><TD>'._('Tax Group').':</TD>';
    echo '<TD><SELECT tabindex=19 name="TaxGroup">';
$sql = 'SELECT taxgroupid, taxgroupdescription FROM taxgroups';
	$result = DB_query($sql,$db);

	while ($myrow = DB_fetch_array($result)) {
		if (isset($_POST['TaxGroup']) and $myrow['taxgroupid']==$_POST['TaxGroup']) {
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
	echo '<TR><TD>' . _('Default Packlist') . ":</TD><TD><SELECT tabindex=22 NAME='DeliverBlind'>";
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

		}
	}

	echo '<TR><TD>' . _('Invoice Addressing') . ":</TD><TD><SELECT NAME='AddrInvBranch'>";
		echo '<OPTION SELECTED VALUE=0>' . _('Address to HO');
		echo '<OPTION VALUE=1>' . _('Address to Branch');
	echo '</SELECT></TD></TR>';
	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "codigo | nombre | direccion1 | direccion3 | direccion4 | direccion5 | direccion6 | cliente desde | descuento | desc./ventas | limite credito | RFC | terminos pago<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();

}else{
  var_dump($_FILES);								// se envio un archivo
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
if(($tamano < 3000000)&&(($tipo=="text/csv")||($tipo=="text/plain")||($tipo=="application/vnd.ms-excel"))){
    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
        	echo $_POST['SalesType']."<br>";
	        echo $_POST['HoldReason']."<br>";
	        echo $_POST['CurrCode']."<br>";
            echo $_POST['AddrInvBranch']."<br>";

            $sqlVerifyLoc = "SELECT rh_master_loccode FROM rh_locations_virtual WHERE loccode = '".$_POST['DefaultLocation']."'";
            $resVerifyLoc = DB_query($sqlVerifyLoc, $db);
            if (DB_num_rows($resVerifyLoc) > 0){
                $rowVerifyLoc = DB_fetch_array($resVerifyLoc);
                $locSM = $rowVerifyLoc['rh_master_loccode'];
            }else{
                $locSM = $_POST['DefaultLocation'];
            }

        $filename = $path.'/'.$_FILES['im']['name'];
        if(@$fh_in = fopen($filename,"r")){
           $Extra = "'','".$_POST['CurrCode']."',now(),'".$_POST['HoldReason']."','CA',0,'',0,10000,'".$_POST['SalesType']."','".$_POST['AddrInvBranch']."',0);";
            DB_query("BEGIN",$db,"Begin Failed !");
            while(($line = fgetcsv($fh_in,0,'|'))!==false){
             $Query3 = "INSERT INTO custbranch (branchcode,
						debtorno,
						brname,
						braddress1,
						braddress2,
						braddress3,
						braddress4,
						braddress5,
						braddress6,
 						braddress7,
 						braddress8,
 						braddress9,
 						braddress10,
 						specialinstructions,
						estdeliverydays,
						fwddate,
						salesman,
						phoneno,
						faxno,
						contactname,
						area,
						email,
						taxgroupid,
						defaultlocation,
                        rh_defaultlocation,
						brpostaddr1,
						brpostaddr2,
						brpostaddr3,
						brpostaddr4,
						disabletrans,
						defaultshipvia,
						custbranchcode,
                       	deliverblind)
				VALUES ('" . $line[0] . "',
					'" . $line[0] . "',
					'" . $line[1] . "',
					'" . $line[3] . "',
					'" . $line[4] . "',
					'" . $line[5] . "',
					'" . $line[6] . "',
					'" . $line[7] . "',
					'" . $line[8] . "',
					'" . $line[9] . "',
					'" . $line[10] . "',
					'" . $line[11] . "',
					'" . $line[12] . "',
					'',
					0,
					now(),
					'" . $_POST['Salesman'] . "',
					'',
					'',
					'',
					'" . $_POST['Area'] . "',
					'',
					" . $_POST['TaxGroup'] . ",
					'" . $locSM . "',
                    '" . $_POST['DefaultLocation'] . "',
					'',
					'',
					'',
					'',
					" . $_POST['DisableTrans'] . ",
					" . $_POST['DefaultShipVia'] . ",
					'',
					" . $_POST['DeliverBlind'] . "
					)";

                //'3246', '3246', '11', '33', '44', '55', '66', '77', '88', '99', '1010', '1111', '1212', 'ESPECIAL INTRUCCION', 0, 0, '2RC', 'TELEFONO', 'FAX', 'PPPP', 'ALL', 'MAIL', 4, 'ALLLE', 'ALLLE', 'DIR1', 'DIR2', 'DIR3', 'DIR4', 0, 1, '', 1 )

                $Query2 = "INSERT INTO debtorsmaster (
							debtorno,
							name,
							name2,
							address1,
							address2,
							address3,
							address4,
							address5,
							address6,
							address7,
							address8,
							address9,
							address10,
                            taxref,
							rh_Tel,
							currcode,
							clientsince,
							holdreason,
							paymentterms,
							discount,
							discountcode,
							pymtdiscount,
							creditlimit,
							salestype,
							invaddrbranch,

							customerpoline)
				VALUES (";
                if($line!= null){
                    $size = sizeof($line);
                    for($i=0;$i<$size;$i++){
                        $Query2 = $Query2."'".stripslashes($line[$i])."',";
                    }
                    $Query2 = $Query2.$Extra;

                    $ErrMsg = _('No se pudo importar el archivo debido a ');

                    $sqlSelect = "select debtorno from debtorsmaster where debtorno='".stripslashes($line[0])."';";
                    $rp = DB_query($sqlSelect,$db);
                    if(DB_num_rows($rp)==0){
                        $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                        echo "<B>SATISFACTORIO, Cliente agregado - ".$line[0].'-'.$line[1]." !!!</B>";
                        $result = DB_query($Query3,$db,$ErrMsg,'Error SQL',True);
                        echo "<B>SATISFACTORIO, Sucursal del cliente agregado - ".$line[0].'-'.$line[1]." !!!</B>";
                    }/*else{
                        //$Query2="update prices set price='".$line[1]."' where stockid='".stripslashes($line[0])."' and typeabbrev='".$_POST['TypeAbbrev']."' and currabrev='".$_POST['CurrAbrev']."' ;";
                        //$result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);
                        echo "<B>SATISFACTORIO !!!</B>";
                    } */
                    //echo $Query2."<BR>";

                }
            }
        }
    fclose($fh_in);
    DB_query("COMMIT",$db);
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