<?php

/*
| delimitador

Realhost
BOWIKAXU
Oct-2006
Importar Clientes a la tabla debtorsmaster

*/
global $_SQLX,$Obs;
$_SQLX=array();
class Observer{
	public function __destruct(){
		global $_SQLX;
		if(count($_SQLX)>0){
			//ob_clean();
			foreach($_SQLX as $_SQLX1)
			if(count($_SQLX1)>0){
					
				echo "<pre>";
				foreach($_SQLX1 as $sql){
					$sql=str_replace("\t",'  ',$sql);
					$sql=str_replace("\r",' ',$sql);
					$sql=str_replace("\n",' ',$sql);
					do{
						$sql=str_replace("  ",' ',$sql);
					}while(strpos($sql,"  "));
					$sql=str_replace("( ",'(',$sql);
					$sql=str_replace(" )",')',$sql);
					$sql=str_replace(") ",')',$sql);
					echo $sql.";\n";
				}
				echo "</pre>";
			}
		}
	}
}
$Obs=new Observer();
$PageSecurity = 3;

include('includes/session.inc');

$title = _('Importar Clientes de Excel');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST['CategoryID'])){ 	// no se envio ningun archivo
	$Errors=array();
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";
    echo '<tr><td>' . _('Category') . ':</td><td><select name="CategoryID" onChange="ReloadForm(this.form)">';
    $sql = 'SELECT categoryid, categorydescription FROM stockcategory';
    $ErrMsg = _('The stock categories could not be retrieved because');
    $DbgMsg = _('The SQL used to retrieve stock categories and failed was');
    $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
    while ($myrow=DB_fetch_array($result)){
	    if (!isset($_POST['CategoryID']) or $myrow['categoryid']==$_POST['CategoryID']){
	    	echo '<OPTION SELECTED VALUE="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'];
	    } else {
		    echo '<OPTION VALUE="'. $myrow['categoryid'] . '">' . $myrow['categorydescription'];
	    }
    }
    echo '</select><a target="_blank" href="'. $rootpath . '/StockCategories.php?' . SID . '">' . _('Add or Modify Stock Categories') . '</a></td></tr>';

    echo '<tr><td>' . _('Marca') . ':</td><td><select name="rh_marca">';
    $sql = 'SELECT * FROM rh_marca ORDER BY id ASC';
    $ErrMsg = _('The stock categories could not be retrieved because');
    $DbgMsg = _('The SQL used to retrieve stock categories and failed was');
    $result = DB_query($sql,$db,$ErrMsg,$DbgMsg);
    while ($myrow=DB_fetch_array($result)){
	    if ($myrow['id'] == $_POST['rh_marca']){
		    echo '<OPTION SELECTED VALUE="'. $myrow['id'] . '">' . $myrow['nombre'];
	    } else {
		    echo '<OPTION VALUE="'. $myrow['id'] . '">' . $myrow['nombre'];
	    }
    }
    echo '</select></td></tr>';

    $sql = 'SELECT unitname FROM unitsofmeasure ORDER by unitname';
    $UOMResult = DB_query($sql,$db);
	echo '<SELECT name="Units">';
    while( $UOMrow = DB_fetch_array($UOMResult) ) {
         if (!isset($_POST['Units']) or $_POST['Units']==$UOMrow['unitname']){
	        echo "<OPTION SELECTED Value='" . $UOMrow['unitname'] . "'>" . $UOMrow['unitname'];
        } else {
	        echo "<OPTION Value='" . $UOMrow['unitname'] . "'>" . $UOMrow['unitname'];
        }
    }

    echo '</SELECT></TD></TR>';

    echo '<TR><TD>' . _('Make, Buy, Kit, Assembly or Service Part') . ':</TD><TD><SELECT name="MBFlag">';
    if ($_POST['MBFlag']=='A'){
	    echo '<OPTION SELECTED VALUE="A">' . _('Assembly');
    } else {
	    echo '<OPTION VALUE="A">' . _('Assembly');
    }
    if ($_POST['MBFlag']=='K'){
	    echo '<OPTION SELECTED VALUE="K">' . _('Kit');
    } else {
	    echo '<OPTION VALUE="K">' . _('Kit');
    }
    if ($_POST['MBFlag']=='M'){
	    echo '<OPTION SELECTED VALUE="M">' . _('Manufactured');
    } else {
	    echo '<OPTION VALUE="M">' . _('Manufactured');
    }
    if (!isset($_POST['MBFlag']) or $_POST['MBFlag']=='B' OR $_POST['MBFlag']==''){
	    echo '<OPTION SELECTED VALUE="B">' . _('Purchased');
    } else {
	    echo '<OPTION VALUE="B">' . _('Purchased');
    }
    if ($_POST['MBFlag']=='D'){
	    echo '<OPTION SELECTED VALUE="D">' . _('Service');
    } else {
	    echo '<OPTION VALUE="D">' . _('Service');
    }
    echo '</SELECT></TD></TR>';

    echo '<TR><TD>' . _('Current or Obsolete') . ':</TD><TD><SELECT name="Discontinued">';
    if ($_POST['Discontinued']==0){
	    echo '<OPTION SELECTED VALUE=0>' . _('Current');
    } else {
	    echo '<OPTION VALUE=0>' . _('Current');
    }
    if ($_POST['Discontinued']==1){
	    echo '<OPTION SELECTED VALUE=1>' . _('Obsolete');
    } else {
	    echo '<OPTION VALUE=1>' . _('Obsolete');
    }
    echo '</SELECT></TD></TR>';

    echo '<TR><TD>' . _('Batch, Serial or Lot Control') . ':</TD><TD><SELECT name="Controlled">';

    if ($_POST['Controlled']==0){
	    echo '<OPTION SELECTED VALUE=0>' . _('No Control');
    } else {
        echo '<OPTION VALUE=0>' . _('No Control');
    }
    if ($_POST['Controlled']==1){
	    echo '<OPTION SELECTED VALUE=1>' . _('Controlled');
    } else {
	    echo '<OPTION VALUE=1>' . _('Controlled');
    }
    echo '</SELECT></TD></TR>';

    echo '<TR><TD>' . _('Serialised') . ':</TD><TD><SELECT ' . (in_array('Serialised',$Errors) ?  'class="selecterror"' : '' ) .'  name="Serialised">';

    if ($_POST['Serialised']==0){
        echo '<OPTION SELECTED VALUE=0>' . _('No');
    } else {
        echo '<OPTION VALUE=0>' . _('No');
    }
    if ($_POST['Serialised']==1){
        echo '<OPTION SELECTED VALUE=1>' . _('Yes');
    } else {
        echo '<OPTION VALUE=1>' . _('Yes');
    }
    echo '</SELECT><i>' . _('Note') . ', ' . _('this has no effect if the item is not Controlled') . '</i></TD></TR>';

    echo '<TR><TD>' . _('Perishable') . ':</TD><TD><SELECT name="Perishable">';

    if ($_POST['Perishable']==0){
        echo '<OPTION SELECTED VALUE=0>' . _('No');
    } else {
        echo '<OPTION VALUE=0>' . _('No');
    }
    if (!isset($_POST['Perishable']) or $_POST['Perishable']==1){
        echo '<OPTION SELECTED VALUE=1>' . _('Yes');
    } else {
        echo '<OPTION VALUE=1>' . _('Yes');
    }
    echo '</SELECT></TD></TR>';

    echo '<TR><TD>' . _('Tax Category') . ':</TD><TD><SELECT NAME="TaxCat">';
    $sql = 'SELECT taxcatid, taxcatname FROM taxcategories ORDER BY taxcatname';
    $result = DB_query($sql, $db);

    if (!isset($_POST['TaxCat'])){
	    $_POST['TaxCat'] = $_SESSION['DefaultTaxCategory'];
    }

    while ($myrow = DB_fetch_array($result)) {
	    if ($_POST['TaxCat'] == $myrow['taxcatid']){
		    echo '<OPTION SELECTED VALUE=' . $myrow['taxcatid'] . '>' . $myrow['taxcatname'];
	    } else {
		    echo '<OPTION VALUE=' . $myrow['taxcatid'] . '>' . $myrow['taxcatname'];
	    }
    } //end while loop

    echo '</SELECT></TD></TR>';
    echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";

	echo "<center><h2> Formato del archivo: <br>";
	echo "CODIGO ARTICULO | CATEGORIA | DESCRIPCION CORTA | DESCRIPCION LARGA | CANTIDAD ECONOMICA A REORDENAR | VOLUMEN(MTS CUBICOS) | PESO (KGS) | CODIGO DE BARRAS | TASA IVA</center></h2>";

	include ('includes/footer.inc');
	exit();

}else{
  $Raiz=dirname(realpath(__FILE__))."/";
//**************************Importar Archivos CSV******************************
$flag=false;
if(!is_dir($Raiz.'importfiles')&&mkdir($Raiz.'importfiles',0777)){
    $flag=true;
} else if (chdir($Raiz)){
    $flag=true;
}else{
   prnMsg(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'), 'error');
}
if($flag){
    $path=realpath($Raiz.'importfiles');
}

if(($flag)){
$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
if(($tamano < 3000000)&&(($tipo=="text/csv")||($tipo=="text/plain")||($tipo=="application/vnd.ms-excel"))){
    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
        if (!isset($_POST['EOQ']) or $_POST['EOQ']==''){
            $_POST['EOQ']=0;
        }

        if ($_POST['LowestLevel']=='' or !isset($_POST['LowestLevel'])){
            $_POST['LowestLevel']=0;
        }

        if ($_POST['rh_lowestprod']=='' or !isset($_POST['rh_lowestprod'])){
            $_POST['rh_lowestprod']=1;
        }

        if (!isset($_POST['Volume']) or $_POST['Volume']==''){
            $_POST['Volume']=0;
        }
        if (!isset($_POST['KGS']) or $_POST['KGS']==''){
            $_POST['KGS']=0;
        }
        if (!isset($_POST['Controlled']) or $_POST['Controlled']==''){
            $_POST['Controlled']=0;
        }
        if (!isset($_POST['Serialised']) or $_POST['Serialised']=='' || $_POST['Controlled']==0){
            $_POST['Serialised']=0;
        }
        if (!isset($_POST['DecimalPlaces']) or $_POST['DecimalPlaces']==''){
	        $_POST['DecimalPlaces']=0;
        }
        if (!isset($_POST['Discontinued']) or $_POST['Discontinued']==''){
            $_POST['Discontinued']=0;
        }

        $filename = $path.'/'.$_FILES['im']['name'];
             DB_query("BEGIN",$db,"Begin Failed !");
        if(@$fh_in = fopen($filename,"r")){
           $Extra = "'','".$_POST['CurrCode']."',now(),'".$_POST['HoldReason']."','CA',0,'',0,10000,'".$_POST['SalesType']."','".$_POST['AddrInvBranch']."',0);";
            while(($line = fgetcsv($fh_in,0,'|'))!==false){
              $line[0]=str_replace(array("/"," ","-"),"",$line[0]);
              foreach($line as $id=>$dat){
              	$line[$id]=DB_escape_string(trim($line[$id]," \r\n\t\0'"));
              }
             $microSql="select id from rh_marca where codigo='".$line[1]."'";
             $rss=false;
             if(($res=DB_query($microSql,$db)))
             	$rss = DB_fetch_array($res);
             if($rss===false)
             	$rss[0]=1;
			if($line[1]=='')
				$line[1]='SN';
              $Query3 = "INSERT INTO stockmaster (
							stockid,
							description,
							longdescription,
							categoryid,
							units,
							mbflag,
							eoq,
							discontinued,
							controlled,
							serialised,
							perishable,
							volume,
							kgs,
							barcode,
							discountcategory,
							taxcatid,
							lowestlevel,
							rh_lowestprod,
							decimalplaces, rh_marca)
						VALUES ('".$line[0]."',
							'" . $line[2] . "',
							'" . $line[3] . "',
							'" . $line[1] . "',
							'" . $_POST['Units'] . "',
							'" . $_POST['MBFlag'] . "',
							" . $_POST['EOQ'] . ",
							" . $_POST['Discontinued'] . ",
							" . $_POST['Controlled'] . ",
							" . $_POST['Serialised']. ",
							" . $_POST['Perishable']. ",
							" . $_POST['Volume'] . ",
							" . $_POST['KGS'] . ",
							'" . $line[0] . "',
							'',
							" . $_POST['TaxCat'] . ",
							" . $_POST['LowestLevel'] . ",
							" . $_POST['rh_lowestprod'] . ",
							" . $_POST['DecimalPlaces']. ",
							'" . $rss[0]. "')";
				$Query4 = "UPDATE stockmaster SET
							description=
							'" . $line[2] . "',
							longdescription=
							'" . $line[3] . "',
							categoryid=
							'" . $line[1] . "',
							units=
							'" . $_POST['Units'] . "',
							mbflag=
							'" . $_POST['MBFlag'] . "',
							eoq=
							" . $_POST['EOQ'] . ",
							discontinued=
							" . $_POST['Discontinued'] . ",
							controlled=
							" . $_POST['Controlled'] . ",
							serialised=
							" . $_POST['Serialised']. ",
							perishable=
							" . $_POST['Perishable']. ",
							volume=
							" . $_POST['Volume'] . ",
							kgs=
							" . $_POST['KGS'] . ",
							barcode=
							'" . $line[0] . "',
							discountcategory=
							'',
							taxcatid=
							" . $_POST['TaxCat'] . ",
							lowestlevel=
							" . $_POST['LowestLevel'] . ",
							rh_lowestprod=
							" . $_POST['rh_lowestprod'] . ",
							decimalplaces=
							" . $_POST['DecimalPlaces']. ",
						rh_marca=
							'" . $rss[0]. "'
						WHERE
						stockid='".$line[0]."'
							";
	
                if($line!= null){
                    $sqlSelect = "select stockid from stockmaster where stockid='".stripslashes($line[0])."';";
                    $rp = DB_query($sqlSelect,$db);
                    if(DB_num_rows($rp)==0){
                        //echo $Query3;
                        $result = DB_query($Query3,$db,$ErrMsg,'Error SQL',True);
                    	$_SQLX[0][]=$Query3;
                       // echo "<B>SATISFACTORIO, Art&iacute;culo agregado - ".$line[0].'-'.$line[1]." !!!</B><br />";

                        $sql = "INSERT INTO locstock (loccode,stockid)	SELECT locations.loccode,'" . stripslashes($line[0]) . "'	FROM locations";
                        $result = DB_query($sql,$db,$ErrMsg,'Error SQL',True);
                    	$_SQLX[1][]=$sql;
                    }else{
						$sqlSelect = "select stockid from locstock where stockid='".stripslashes($line[0])."';";
						$rp = DB_query($sqlSelect,$db);
	                    if(DB_num_rows($rp)==0){
							$sql = "INSERT INTO locstock (loccode,stockid)	SELECT locations.loccode,'" . stripslashes($line[0]) . "'	FROM locations";
	                        $result = DB_query($sql,$db,$ErrMsg,'Error SQL',True);
							$_SQLX[3][]=$sql;
						}
						$_SQLX[2][]=$Query4;
						//DB_query($Query4,$db,$ErrMsg,'Error SQL',True);
						echo "<B>Error, El Art&iacute;culo ya existe en stock - ".$line[0].'-'.$line[2]." !!!</B><br />";
                    }
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
