<?php
/*
 * iJPe
 * realhost
 * 04-01-10
 * 
 * Script creado para la actualizacion de precios de mangueras
 */

$PageSecurity = 8;
include('includes/session.inc');

$title = _('Actulizar Precio y Costo');
include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
//print_r($_POST);

if (isset($_POST['update']) && strlen($_POST['filename'])>0)
{
	echo "<h4>Los articulos que no existen son los que no se han marcado:</h4>";
	
	$filename = $_POST['filename'];
	if(@$fh_in = fopen("{$filename}","r"))
  	{
  		$lastProd='';
  		$Result = DB_query('BEGIN',$db);
  		while(!feof($fh_in))
    	{
			$line = fgetcsv($fh_in,1024,',');
			//print_r($line);
			
			if($line[0] == "")
   		    {
				// no contiene nada esta linea
			}
			else
			{
				//Se verifica primero que exista el articulo para que no se reproduzca algun error porque
				//el articulo no existe
				$sql = "select * from stockmaster where stockid = ".$line[0];
				$res = DB_query($sql, $db);
				
				if (DB_num_rows($res)>0)
				{										
					$sqlIoU = "Select * from prices where stockid=".$line[0];
					$resIoU = DB_query($sqlIoU, $db);
					
					if (DB_num_rows($resIoU) > 0)
					{
						$sqlU = "UPDATE prices set price=".$line[1]." WHERE stockid = ".$line[0];
						DB_query($sqlU, $db);	
					}
					else
					{
						$sqlI = "INSERT INTO prices VALUES (".$line[0].", 'L1', 'MXN', '', ".$line[1].", '')";
						DB_query($sqlI, $db);
					}
					
					//Actualizar Costo
					$StockID = str_replace("'","",$line[0]);
					$_POST['MaterialCost'] = str_replace("'","",$line[2]);
					
					if (strlen($StockID)>0)
					{					
						$sql = "SELECT  materialcost,
									labourcost,
									overheadcost,
									mbflag,
									sum(quantity) as totalqoh
								FROM stockmaster INNER JOIN locstock
									ON stockmaster.stockid=locstock.stockid
								WHERE stockmaster.stockid='".$StockID."'
								GROUP BY description,
									units,
									lastcost,
									actualcost,
									materialcost,
									labourcost,
									overheadcost,
									mbflag";
						$ErrMsg = _('The entered item code does not exist');
						$oldresult = DB_query($sql,$db,$ErrMsg);
						$oldrow = DB_fetch_array($oldresult);
						$_POST['QOH'] = $oldrow['totalqoh'];
						$_POST['OldMaterialCost'] = $oldrow['materialcost'];
						if ($oldrow['mbflag']=='M') {
							$_POST['OldLabourCost'] = $oldrow['labourcost'];
							$_POST['OldOverheadCost'] = $oldrow['overheadcost'];
						} else {
							$_POST['OldLabourCost'] = 0;
							$_POST['OldOverheadCost'] = 0;
							$_POST['LabourCost'] = 0;
							$_POST['OverheadCost'] = 0;
						}
						DB_free_result($oldresult);

						$OldCost =$_POST['OldMaterialCost'] + $_POST['OldLabourCost'] + $_POST['OldOverheadCost'];
						$NewCost =$_POST['MaterialCost'] + $_POST['LabourCost'] + $_POST['OverheadCost'];

						if ($OldCost != $NewCost){
						//echo "<h3>".$_POST['MaterialCost']."</h3>";
						ItemCostUpdateGL($db, $StockID, $NewCost, $OldCost, $_POST['QOH']);


							$SQL = "UPDATE stockmaster SET
										materialcost=" . $_POST['MaterialCost'] . ",
										labourcost=" . $_POST['LabourCost'] . ",
										overheadcost=" . $_POST['OverheadCost'] . ",
										lastcost=" . $OldCost . "
								WHERE stockid='" . $StockID . "'";

							$ErrMsg = _('The cost details for the stock item could not be updated because');
							$DbgMsg = _('The SQL that failed was');
							$Result = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);

							if($_SESSION['CostHistory']==1){
											
											// bowikaxu realhost - historial del costo
												$sqlcost = "INSERT INTO rh_costhistory (stockid,
														cost, lastcost, trandate, user_) VALUES (
														'".$StockID."',
														'".($_POST['MaterialCost']+$_POST['LabourCost']+$_POST['OverheadCost'])."',
														'".$OldCost."',
														'".Date('Y-m-d H:m:s')."',
														'".$_SESSION['UserID']."')";
											// bowikaxu realhost - insert the price history
													DB_query($sqlcost,$db,'error al insertar el historial e costos','',true);
								}
						
							
							UpdateCost($db, $StockID); //Update any affected BOMs

						}
						
						$sqlPD = "UPDATE purchdata set price = ".$line[2]." where stockid=".$line[0];
						DB_query($sqlPD, $db);
					
					}
					
					
					prnMsg($line[0],'sucess');
				}
				else
				{
					echo $line[0].",";
					echo $line[1]."<br>";
				}
			}
		}
		$Result = DB_query('COMMIT',$db);
	}
	else
	{
		prnMsg('No se ha podido abrir el archivo','error');	
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
					echo $line[2]."<br>";
				} 
			}   		
		}
		
	}
}


if ((!isset($_POST['update'])) && !(isset($_POST['verify'])))
{
	prnMsg('No se ha ingresado la ruta del archivo','info');
}



echo "<center";
echo "<br>";
echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
echo "<br>";
echo "Archivo a Importar: <INPUT TYPE='text' NAME='filename'></INPUT>";

echo "<br>";
echo "<br>";
echo "<input type='submit' name='update' value='Actualizar' ></input>";
echo "<input type='submit' name='verify' value='Verificar Archivo' ></input>";
echo "</form>";
echo "</center";

include('includes/footer.inc');
?>
