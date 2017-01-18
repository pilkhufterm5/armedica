<?php
/*
 * iJPe
 * realhost
 * 02-01-10
 * 
 * Script creado para la actualizacion de inventario de mangueras
 * 
 * NOTAS:
 * -Se debe actualizar el periodo en los querys
 * -Se debe de tener ordenado alfabeticamente los productos para la correcta actualizacion en caso de que se repita un producto
 */

$PageSecurity = 8;
include('includes/session.inc');

/*
 * iJPe
 *
 * ESTAS VARIABLES SE DEBEN DE ACTUALIZAR
 */

//$fecha = "2010/03/06";
//$periodo = 16;
//$reference = "Ajuste 6/Mar/10 aut. Sergio Vega";

$fecha = $_POST['fecha'];
$reference = $_POST['comentario'];

$title = _('Actulizar Inventario');
include('includes/header.inc');
//print_r($_POST);

//echo "<hr>";
//print_r($_SESSION);
//echo "<hr>";

if (!isset($_POST['fecha'])){
    $_POST['fecha'] = date('d-m-Y');
}


?>

<link rel="stylesheet" type="text/css" href="javascript/dhtmlx/codebase/dhtmlxvault.css" />

<script language="JavaScript" type="text/javascript" src="javascript/dhtmlx/codebase/dhtmlxvault.js"></script>
<script language="JavaScript" type="text/javascript" src="javascript/jquery.MultiFile.js"></script>



<style>
	body {font-size:12px}
	.{font-family:arial;font-size:12px}
	h1 {cursor:hand;font-size:16px;margin-left:10px;line-height:10px}
	xmp {color:green;font-size:12px;margin:0px;font-family:courier;background-color:#e6e6fa;padding:2px}
	.hdr{
		background-color:lightgrey;
		margin-bottom:10px;
		padding-left:10px;
	}
    </style>





<?php

echo "<body>";

if (isset($_POST['update']) && strlen($_POST['files'])>0)
{
	echo "<h4>Los articulos que no existen son:</h4>";
	
	$filename = array();
        $filename = explode('|', $_POST['files']);

        print_r($filename);
        
        while ($file = $filename)
        {
            if(@$fh_in = fopen("/csv_stock/{$file}","r"))
            {
                    $lastProd='';
                    $Result = DB_query('BEGIN',$db);
                    while(!feof($fh_in))
            {
                            $line = fgetcsv($fh_in,1024,',');
                            //print_r($line);

                            if(strlen($line[0])<=0)
                            {
                                    // no contiene nada esta linea
                            }
                            else
                            {
                                    if (strlen($line[1])<=0)
                                    {
                                            $line[1]=0;
                                    }

                                    //Se verifica primero que exista el articulo para que no se reproduzca algun error porque
                                    //el articulo no existe
                                    $sql = "select * from stockmaster where stockid = ".$line[0];
                                    $res = DB_query($sql, $db);

                                    if (DB_num_rows($res)>0)
                                    {
    //					$SQL="SELECT locstock.quantity
    //					FROM locstock
    //					WHERE locstock.stockid=" . $line[0] . "
    //					AND loccode= '" . $_POST['suc'] . "'";
    //					$Result = DB_query($SQL, $db);

                                            $SQL = "SELECT SUM(qty) FROM stockmoves where loccode = '".$_POST['suc']."' AND stockid=".$line[0];
                                            $Result = DB_query($SQL,$db);

                                            if (DB_num_rows($Result)==1){
                                                    $LocQtyRow = DB_fetch_row($Result);
                                                    $QtyOnHandPrior = $LocQtyRow[0];
                                            } else {
                                                    $QtyOnHandPrior = 0;
                                            }

                                            //if ($line[0]!=$lastProd)
                                            //{
    //						$rh_qty = str_replace("'","",$line[1]);
    //						$quantityToMove = $rh_qty - $QtyOnHandPrior;
    //
    //						$sqlSM = "INSERT INTO stockmoves (stkmoveno, stockid, type, transno, loccode, trandate, prd, reference, qty, newqoh) VALUES (
    //						".$line[2].",".$line[0].", 17, ".$line[3].", '".$_POST['suc']."', '2010/01/02', 14, 'Ajuste 2/Ene/10 aut. Sergio Vega', ".$quantityToMove.", ".$rh_qty.")";
    //						DB_query($sqlSM, $db);
    //
    //						$sqlLS = "UPDATE locstock SET quantity=quantity +".$quantityToMove." WHERE stockid=".$line[0]." AND loccode = '".$_POST['suc']."'";
    //						DB_query($sqlLS, $db);
    //
    //						$lastProd = $line[0];
                                            //}
                                            //else
                                            //{
                                                    $rh_qty = str_replace("'","",$line[1]);
                                                    $quantityToMove = $rh_qty - $QtyOnHandPrior;
                                                    //$newQuantity = $line[1] - $QtyOnHandPrior;

                                                    if ($quantityToMove != 0)
                                                    {
                                                        $sqlAT = "UPDATE systypes set typeno=typeno+1 WHERE typeid = 17";
                                                        DB_query($sqlAT, $db);

                                                        $sqlNT = "SELECT typeno FROM systypes WHERE typeid=17";
                                                        $resNT = DB_query($sqlNT, $db);
                                                        $rowNT = DB_fetch_array($resNT);

                                                        $sqlSM = "INSERT INTO stockmoves (stockid, type, transno, loccode, trandate, prd, reference, qty, newqoh) VALUES (
                                                        ".$line[0].", 17, ".$rowNT['typeno'].", '".$_POST['suc']."', '$fecha', $periodo, '$reference', ".$quantityToMove.", ".$line[1].")";
                                                        DB_query($sqlSM, $db);

                                                        $sqlLS = "UPDATE locstock SET quantity=quantity +".$quantityToMove." WHERE stockid=".$line[0]." AND loccode = '".$_POST['suc']."'";
                                                        DB_query($sqlLS, $db);

                                                        $lastProd = $line[0];

                                                    }
                                            ///

                                    }
                                    else
                                    {
                                            echo $line[0].",";
                                            echo $line[1]."<br>";
                                    }
                            }
                    }
                    $Result = DB_query('COMMIT',$db);
                    //$Result = DB_query('rollback',$db);
            }
            else
            {
                    prnMsg('No se ha podido abrir el archivo','error');
            }
    }
}

if (isset($_POST['verify']) && strlen($_POST['filename'])>0)
{
	echo "<h4>Los articulos que no existen son:</h4>";
	
	$filename = $_POST['filename'];
	if(@$fh_in = fopen("{$filename}","r"))
  	{
  		$lastProd='';
  		while(!feof($fh_in))
    	{
    		$line = fgetcsv($fh_in,1024,',');
    		
    		if($line[0] == "")
   		    {
				// no contiene nada esta linea
			}
			else
			{    		
				$sql = "select * from stockmaster where stockid = ".$line[0];
				$res = DB_query($sql, $db);
				
				if (DB_num_rows($res)<=0)
				{
					echo $line[0].",";
					echo $line[1].",";
					echo $line[2].",";
					echo $line[3]."<br>";
				} 
			}   		
		}
		
	}
}


//if ((!isset($_POST['update'])) && !(isset($_POST['verify'])))
//{
//	prnMsg('No se ha ingresado la ruta del archivo','info');
//}
echo "<br>";
echo "<center";

?>

<div id="vaultDiv"></div>
<script type="text/javascript">
   atributos = new Array();

   vault=new dhtmlXVaultObject();
   vault.setImagePath("javascript/dhtmlx/codebase/imgs/");
   vault.setServerHandlers("javascript/dhtmlx/handlers/php/UploadHandler.php", "javascript/dhtmlx/handlers/php/GetInfoHandler.php", "javascript/dhtmlx/handlers/php/GetIdHandler.php");
   vault.setFilesLimit(3);
   vault.onAddFile = function(fileName) {
      var ext = this.getFileExtension(fileName);
      if (ext != "csv") {
         alert("Favor de elegir un archivo .csv");
         return false;
      }
      else return true;
   };

      vault.onUploadComplete = function(files) {      
      //var s="";
      for (var i=0; i<files.length; i++)  {
         var file = files[i];
         atributos.push(file.name);
         //s += ("id:" + file.id + ",name:" + file.name + ",uploaded:" + file.uploaded + ",error:" + file.error)+"\n";
      }
      archivosActualizar(atributos);
      //alert(s);
    };

//   vault.onUploadComplete = function(files) {
//      atributos = new array();
//      var s="";
//      for (var i=0; i<files.length; i++)  {
//         var file = files[i];
//         atributos.push(file.name);
//         s += ("id:" + file.id + ",name:" + file.name + ",uploaded:" + file.uploaded + ",error:" + file.error)+"\n";
//      }
//      archivosActualizar(atributos);
//      alert(s);
//    };
   vault.create("vaultDiv");

   function archivosActualizar(atributosFiles){
       var toUpload = generateList(atributosFiles);
       document.info.files.value = toUpload;       
       //alert(toUpload);
   }

    function generateList(a) {
      var i, s = '';
      for (i = 0; i < a.length; ++i) {
        if (typeof a[i] == 'string') {
          s += a[i];
        }
        else { // assume number type
          s += a[i];
        }
        if (i < a.length - 1) {
          s += '|';
        }
      }
      s += '';
      return s;
    }


</script>

<?php

echo "<br>";
echo "<FORM NAME='info' METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";

echo "Codigo Sucursal: <select name='suc'>";
	$sql = "SELECT loccode, locationname FROM locations";
	$res = DB_query($sql, $db);
	
	while ($row=DB_fetch_array($res))
	{
		echo "<option value=".$row['loccode'].">".$row['loccode']."  (".$row['locationname'].")</option>";
	}
	echo "</select>";
	
echo "<br>";
echo "<br>";
echo "<table>";
//echo "Archivo a Importar: <INPUT TYPE='text' NAME='filename'></INPUT>";
echo "<tr>";
//echo "<td>Archivo a Importar: </td><td><INPUT TYPE='text' NAME='filename'></INPUT></td>";
//echo "</tr><tr>";
echo "<td>Fecha: </td><td><INPUT TYPE='text' NAME='fecha' value = '".$_POST['fecha']."'></INPUT></td>";
echo "</tr><tr>";
echo "<td>Comentario: </td><td><INPUT TYPE='text' NAME='comentario'></INPUT></td>";
echo "</tr><tr>";
echo '<input type="file" class="multi" maxlength="2"/>';
echo "</tr>";
echo "</tr><tr>";
echo "<input type='text' name='files' value='' ></input>";
echo "</tr>";
echo "<tr>";
echo "<td><input type='submit' name='update' value='Actualizar' ></input></td>";
echo "<td><input type='submit' name='verify' value='Verificar Archivo' ></input></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center";
echo "</body>";

include('includes/footer.inc');
?>
