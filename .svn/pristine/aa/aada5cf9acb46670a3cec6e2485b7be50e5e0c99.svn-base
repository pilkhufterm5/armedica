<?php
$PageSecurity = 2;

include('includes/session.inc');

$title = _('Importar Inventario inicial de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
if(isset($_POST['submit']))
{
?>
<table style="border: 1px solid blue;width: 100%;">
  <tbody><tr>
    <td>
    <div class='progreso' style="display: block;background-color: red;height:30px;width: 20%;text-align: center;vertical-align: middle;overflow: hidden;
    ">
    </div>
    </td>
  </tr>
</tbody></table>

<?php 
//**************************Importar Archivos CSV******************************
ini_set('display_errors', 1);error_reporting (1);
$flag=false;

$path=$SessionSavePath;
$flag=is_dir($path)? 1 : 0;

if(($flag)){


$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
#var_dump($_FILES);
if(($tamano < 3000000)
/*&&(($tipo=="text/csv")||($tipo=="text/text")||($tipo=="application/vnd.ms-excel")||($tipo=="application/octet-stream"))*/

){

//     echo 'Moneda seleccionada: '.$_POST['CurrAbrev']."<br />";
//     echo 'Lista de precios seleccionada: '.$_POST['TypeAbbrev']."<br /><hr />";

    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
	$filename = $path.'/'.$_FILES['im']['name'];
    $sql="select max(stkmoveno) as stkmove from stockmoves;";
    $rs = DB_query($sql,$db);
    if($rw = DB_fetch_array($rs)){
     $firstMove =$rw['stkmove'];
    }
    if(@$fh_in = fopen($filename,"r")){
    	set_time_limit(0);
        //DB_query("BEGIN",$db,"Begin Failed !");
        DB_Txn_Begin($db);
        $separador='|';
        $total=0;
        while(($line = fgetcsv($fh_in,0,$separador))!==false){
        	if(count($line)==1){
				$line=$line[0];
				if(strpos($line,'|')){
					$separador='|';
				}else
				if(strpos($line,';')){
					$separador=';';
				}else if(strpos($line,',')){
					$separador=',';
				}
				$line=array_map(function($d){return trim($d,"\"' \r\n\t\0\x0b");},explode($separador,$line));
			}
			if(trim($line[0])==''||trim($line[0])=='stockid')continue;
        	$total++;
        	if($line[2]!='')
        		UpdateSerieLocacion($line[0],$line[2],$_POST['locations'],false);
        }
        $i=0;
        $articulos_insertados=array();
        if(@$fh_in = fopen($filename,"r"))
        while(($line = fgetcsv($fh_in,0,$separador))!==false){
			if(count($line)==1){
				$line=$line[0];
				if(strpos($line,'|')){
					$separador='|';
				}else
				if(strpos($line,';')){
					$separador=';';
				}else if(strpos($line,',')){
					$separador=',';
				}
				$line=array_map(function($d){return trim($d,"\"' \r\n\t\0\x0b");},explode($separador,$line));
			}
			if(trim($line[0])==''||trim($line[0])=='stockid')continue;
			$i++;
			?>
			<script type="text/javascript">
<!--
$('.progreso').css('width','<?php echo ((int)100*$i/$total)?>%');
$('.progreso').html('<?php echo ((int)100*$i/$total)?> %');
<?php
if(((int)100*$i/$total)==100){
?>
setTimeout(function(){$('.progreso').closest('table').fadeOut();},5000);
<?php 
} 
?>
//-->
</script>
			
			<?php 
			
	        $StockIniNo = GetNextTransNo(17, $db);
	        //$PeriodNo = $_POST['periodNo'];
                $PeriodNo = GetPeriod(date($_SESSION['DefaultDateFormat']), $db);
            if($line[2]=='')
            	$sqlQuery="select (quantity)as QTY,stockid from locstock where loccode ='".$_POST['locations']."' and stockid='".$line[0]."'";
            else
            	$sqlQuery="select (quantity)as QTY,stockid from stockserialitems where loccode ='".$_POST['locations']."' and stockid='".$line[0]."' and serialno='".$line[2]."'";
            
            $rp = DB_query($sqlQuery,$db);
            if($rw=DB_fetch_array($rp)){
              if($_POST['mvto']==0){
                $onHand=$rw['QTY']-$line[1];
                $line[1]=$line[1]*-1;
              }elseif($_POST['mvto']==1){
                $onHand=$rw['QTY']+$line[1];
              }else if($_POST['mvto']==2){
              	$onHand=$line[1];
                $line[1]=$line[1]-$rw['QTY'];
              }
            }
            if($onHand=='')$onHand=0;
            $Query2 = "insert into stockmoves( stockid, type, transno, loccode, trandate, prd, reference, qty, newqoh) value('%DATA1%',17,".$StockIniNo.",'".$_POST['locations']."',now(),$PeriodNo,'Ajuste',%DATA2%,$onHand)";
            $Query3 = "insert into stockserialitems(loccode,stockid,serialno, quantity,expirationdate) value('".$_POST['locations']."','%DATA1%','%DATA3%', '%DATA2%','%DATA4%')";
            $Query4 = "insert into stockserialmoves(stockmoveno,stockid,serialno, moveqty) value('STKMOVENO','%DATA1%','%DATA3%', '%DATA2%')";
            $Query5 = "update stockserialitems set  quantity = quantity+(%DATA2%) where serialno='%DATA3%' and stockid='%DATA1%' and loccode='".$_POST['locations']."'; ";
            echo "stockid ".stripslashes($line[0])." serialno ".stripslashes($line[2]);
            $articulos_insertados[]=array($line[0],$line[2]);
            if($line!= null){
                $ErrMsg = _('No se pudo importar el archivo debido a ');
                $Query2= str_replace('%DATA1%',$line[0],$Query2);
                $Query2= str_replace('%DATA2%',$line[1],$Query2);

                $Query3= str_replace('%DATA1%',$line[0],$Query3);
                $Query3= str_replace('%DATA2%',$line[1],$Query3);
                $Query3= str_replace('%DATA3%',$line[2],$Query3);
                $Query3= str_replace('%DATA4%',FormatDateForSQL($line[3]),$Query3);

                $Query4= str_replace('%DATA1%',$line[0],$Query4);
                $Query4= str_replace('%DATA2%',$line[1],$Query4);
                $Query4= str_replace('%DATA3%',$line[2],$Query4);
                $Query4= str_replace('%DATA4%',FormatDateForSQL($line[3]),$Query4);

                $Query5= str_replace('%DATA1%',$line[0],$Query5);
                $Query5= str_replace('%DATA2%',$line[1],$Query5);
                $Query5= str_replace('%DATA3%',$line[2],$Query5);

                $sqlSelect = "select stockid from stockmaster where stockid='".stripslashes($line[0])."';";

                $rp = DB_query($sqlSelect,$db);
                if($rw=DB_fetch_array($rp)){
// 			echo "xx<br>".$Query2."<br>xx";
                    $result = DB_query($Query2,$db,$ErrMsg,'Error SQL',True);

                        $StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');
                        $Query4= str_replace('STKMOVENO',$StkMoveNo,$Query4);
// 			echo "<br><br>".$Query4."<br><br>";
                        $sqlSelect2 = "select count(stockid) as contador from stockserialitems where stockid='".stripslashes($line[0])."' and serialno='".stripslashes($line[2])."' and loccode='".$_POST['locations']."';";
//                         echo   $sqlSelect2."<br />";
                        $rpp = DB_query($sqlSelect2,$db);
                        if($rww=DB_fetch_array($rpp)){
                            $Lotes= $rww['contador'];
                        }
                        if($Lotes>0){
                            $result = DB_query($Query5,$db,$ErrMsg,'Error SQL',True);
                            $result = DB_query($Query4,$db,$ErrMsg,'Error SQL',True);
                            echo " <B>SATISFACTORIO !!!</B><br />";
                        }else{
                            echo " El articulo ".$line[0]." con el lote: ".$line[2]." no existe.<br />";
			    //rleal may 11, 2013, no estaba este codigo aqui
			    $result = DB_query($Query3,$db,$ErrMsg,'Error SQL',True);
			    $result = DB_query($Query4,$db,$ErrMsg,'Error SQL',True);
                            echo "<B>SATISFACTORIO !!!</B><br />";
                        }
                        
                }else{
                  echo " <B>El producto no existe!!!</B><br />";
                }
             }else{
               echo " Linea Nullo <br />";
             }
    }
    fclose($fh_in);

    DB_Txn_Commit($db);
    
    foreach ($articulos_insertados as $line)
    	if($line[1]!='')
    		UpdateSerieLocacion($line[0],$line[1],$_POST['locations'],false);
    //DB_query("COMMIT",$db);
    $sql="select max(stkmoveno) as stkmove from stockmoves;";
    $rs = DB_query($sql,$db);
    if($rw = DB_fetch_array($rs)){
        $secondMove =$rw['stkmove'];
    }
    
	//DB_query("call update_allstockmoves(".$firstMove.",".$secondMove.")",$db);
    DB_query("call update_allstockmovesnewqoh(".$firstMove.",".$secondMove.")",$db);
    DB_query("call update_alllocstock('".$_POST['locations']."')",$db);
    //DB_query("COMMIT",$db);
    

}else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
 }
 }else{
     prnMsg(_('>No se puede escribir en el directorio indicado2'), 'error');
    include ('includes/footer.inc');
    exit;
 }
}else{
     prnMsg(_('>No se puede escribir en el directorio indicado'), 'error');
    include ('includes/footer.inc');
    exit;
}
}else
{     // no se envio ningun archivo
    echo "<br /><br /><center><strong>Importar contenido</strong></center>";
    echo '<CENTER><TABLE>';
	echo "<FORM METHOD='POST' ACTION='".$_SERVER['PHP_SELF']."' enctype='multipart/form-data'>";
	echo "<TR><TD>Archivo a Importar:</td><TD> <input type='file' name='im' /></TD></TR>";
    $permitionStr="'".implode("','",array_keys($_SESSION["rh_permitionlocation"]))."'";
    $SQL = "SELECT locationname,loccode
			FROM rh_locations
			WHERE loccode in({$permitionStr})
			ORDER BY locationname";
    $result = DB_query($SQL,$db);

    echo '<CENTER><TR><TD>' . _('Sucursal') . ':</TD><TD><SELECT name="locations">';
    while ($myrow = DB_fetch_array($result)) {
        if ($myrow['loccode']==$_POST['locations']) {
            echo '<OPTION SELECTED VALUE="'.$myrow['loccode'] . '">' . $myrow['locationname'];
        } else {
            echo '<OPTION VALUE="'.$myrow['loccode'] . '">' . $myrow['locationname'];
        }
    } //end while loop

    DB_free_result($result);

    echo '</SELECT>    </TD></TR>';

    /*echo '<TR><TD>' . _('Lotes') . ':</TD><TD><SELECT name="lotes">';
    echo '<option value="0"> No </option>';
    echo '<option value="1"> Si </option>';

    echo '</SELECT>    </TD></TR>';
*/
    echo '<TR><TD>' . _('Tipo de Movimiento') . ':</TD><TD><SELECT name="mvto">';
    echo '<option value="2"> Ajustar </option>';
    echo '<option value="1"> Entrada </option>';
	echo '<option value="0"> Salida </option>';
    echo '</SELECT>    </TD></TR>';

    /*echo '<CENTER><TR><TD>' . _('Periodo') . ':</TD><TD><SELECT name=" periodNo">';
    $SQL = "SELECT periodno,lastdate_in_period FROM periods";
    $result = DB_query($SQL,$db);
    while ($myrow = DB_fetch_array($result)) {
        if ($myrow['periodno']==$_POST['periodNo']) {
            echo '<OPTION SELECTED VALUE="'.$myrow['periodno'] . '">' . $myrow['lastdate_in_period'];
        } else {
            echo '<OPTION VALUE="'.$myrow['periodno'] . '">' . $myrow['lastdate_in_period'];
        }
    } //end while loop
*/
    DB_free_result($result);
    echo '</SELECT>    </TD></TR>';
    echo "</CENTER></TABLE>";
    echo "<BR><BR><INPUT TYPE=Submit name='submit'></INPUT>";
    echo "</FORM>";
    echo "<center><h2> Formato del archivo: <br>";
    //echo "stockid | Descripcion Corta | Descripcion Larga | Cantidad economica reordenar | Volumen (mts cubicos) | Peso (kgs) | Codigo de Barras<br></center></h2>";
    echo "stockid | Cantidad | Lote | caducidad<br></center></h2><br />
    <ul><li>Sin Espacios en blanco</li><li>Delimitador de Espacio |</li><li>Cantidad siempre positiva, indicar si es Salida o entrada</li><li>Formato *.csv</li></ul>";
    
}


include ('includes/footer.inc');

