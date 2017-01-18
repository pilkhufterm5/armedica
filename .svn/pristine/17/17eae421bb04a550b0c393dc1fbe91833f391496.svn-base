<?php

/*
| delimitador

Realhost
BOWIKAXU
June-2007
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
include('includes/DefineStockAdjustment.php');
include('includes/DefineSerialItems.php');

$title = _('Actualizar Cantidades Inventario de CSV');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if(!isset($_POST["filename"]) || $_POST["filename"]==""){ 	// no se envio ningun archivo 
	
	echo '<CENTER><TABLE>';
	echo "<FORM METHOD=POST ACTION=".$_SERVER['PHP_SELF'].">";
	echo "<TR><TD>Archivo a Importar:</TD><TD> <INPUT TYPE='text' NAME='filename'></INPUT></TD></TR>";
	
	echo '<TR><TD>'. _('Adjustment to Stock At Location').':</TD><TD><SELECT name="StockLocation"> ';

$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql,$db);
while ($myrow=DB_fetch_array($resultStkLocs)){
	if (isset($_SESSION['Adjustment']->StockLocation)){
		if ($myrow['loccode'] == $_SESSION['Adjustment']->StockLocation){
		     echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		} else {
		     echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		}
	} elseif ($myrow['loccode']==$_SESSION['UserStockLocation']){
		 echo '<OPTION SELECTED Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
		 $_POST['StockLocation']=$myrow['loccode'];
	} else {
		 echo '<OPTION Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
	}
}

echo '</SELECT></TD></TR>';
	
// OPCIONES PARA INVENTARIOS


// TERMINA SELECCION DE OPCIONES

	
	echo "</CENTER></TABLE>";
	echo "<BR><BR><INPUT TYPE=Submit></INPUT>";
	echo "</FORM>";
	
	echo "<center><h2> Formato del archivo: <br>";
	echo "stockid | cantidad<br></center></h2>";
	
	include ('includes/footer.inc');
	exit();
	
}else{								// se envio un archivo
	//$filename = "prueba.csv";
	echo "<CENTER>".$_POST['StockLocation']."</CENTER>";
	$filename = $_POST['filename'];
	//$fh_out = fopen("clientes.sql","w+");
	//if (!isset($_SESSION['Adjustment'])){
    unset($_SESSION['Adjustment']);
	$_SESSION['Adjustment'] = new StockAdjustment;
	//}
	
  if(@$fh_in = fopen("{$filename}","r"))
  {
                                                  

//    fwrite($fh_out,$Query2);
	DB_query("BEGIN",$db,"Begin Failed !");    
	
	
	while(!feof($fh_in))
    {
		
	 $line = fgetcsv($fh_in,1024,'|');
                                                  
      if($line[0] == "")
      {
        // no contiene nada esta linea
      }else {
   	
       	$sql ="SELECT stockid,
       			description,
				units,
				mbflag,
				materialcost+labourcost+overheadcost as standardcost,
				controlled,
				serialised,
				decimalplaces
			FROM stockmaster
			WHERE stockid='" . $line[0] . "'";
	$ErrMsg = _('Unable to load StockMaster info for part'). ':' . $line[0];
	$result = DB_query($sql, $db, $ErrMsg);
	$myrow = DB_fetch_row($result);

	if (DB_num_rows($result)==0){
                prnMsg( _('Unable to locate Stock Code').' '.$line[0], 'error' );
				unset($_SESSION['Adjustment']);
				exit;
	} elseif (DB_num_rows($result)>0){

		if ($myrow[2]=='D' OR $myrow[2]=='A' OR $myrow[2]=='K'){
			prnMsg( _('The part entered is either or a dummy part or an assembly or kit-set part') . '. ' . _('These parts are not physical parts and no stock holding is maintained for them') . '. ' . _('Stock adjustments are therefore not possible'),'error');
			echo '<HR>';
			echo '<A HREF="'. $rootpath .'/StockAdjustments.php?' . SID .'">'. _('Enter another adjustment'). '</A>';
			unset ($_SESSION['Adjustment']);
			include ('includes/footer.inc');
			exit;
		}
		
		$_SESSION['Adjustment']->StockID = $line[0];
		$_SESSION['Adjustment']->Quantity = $line[1];
		$_SESSION['Adjustment']->StockLocation = $_POST['StockLocation'];
		$_SESSION['Adjustment']->ItemDescription = $myrow[0];
		$_SESSION['Adjustment']->PartUnit = $myrow[1];
		$_SESSION['Adjustment']->StandardCost = $myrow[3];
		$_SESSION['Adjustment']->Controlled = $myrow[4];
		$_SESSION['Adjustment']->Serialised = $myrow[5];
		$_SESSION['Adjustment']->DecimalPlaces = $myrow[6];
		$_SESSION['Adjustment']->SerialItems = array();
	}
	
		
	
	/*All inputs must be sensible so make the stock movement records and update the locations stocks */

		$AdjustmentNumber = GetNextTransNo(17,$db);
		$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
		$SQLAdjustmentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));

		$SQL = 'BEGIN';
		//$Result = DB_query($SQL,$db);

		// Need to get the current location quantity will need it later for the stock movement
		$SQL="SELECT locstock.quantity
			FROM locstock
			WHERE locstock.stockid='" . $line[0] . "'
			AND loccode= '" .$_POST['StockLocation'] . "'";
		$Result = DB_query($SQL, $db);
		if (DB_num_rows($Result)==1){
			$LocQtyRow = DB_fetch_row($Result);
			$QtyOnHandPrior = $LocQtyRow[0];
		} else {
			// There must actually be some error this should never happen
			$QtyOnHandPrior = 0;
		}
		
		// bowikaxu - poner primero cantidades en ceros
		$SQL = "INSERT INTO stockmoves (
				stockid,
				type,
				transno,
				loccode,
				trandate,
				prd,
				reference,
				qty,
				newqoh)
			VALUES (
				'" . $line[0] . "',
				17,
				" . $AdjustmentNumber . ",
				'" . $_POST['StockLocation'] . "',
				'" . $SQLAdjustmentDate . "',
				" . $PeriodNo . ",
				'" . DB_escape_string('Inicializacion en ceros') ."',
				" . (-1*$QtyOnHandPrior) . ",
				0
			)";


		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
		// fin cantidades en ceros
		
		$SQL = "INSERT INTO stockmoves (
				stockid,
				type,
				transno,
				loccode,
				trandate,
				prd,
				reference,
				qty,
				newqoh)
			VALUES (
				'" . $_SESSION['Adjustment']->StockID . "',
				17,
				" . GetNextTransNo(17,$db) . ",
				'" . $_SESSION['Adjustment']->StockLocation . "',
				'" . $SQLAdjustmentDate . "',
				" . $PeriodNo . ",
				'" . DB_escape_string('Inicializacion Cantidad Inicial') ."',
				" . $_SESSION['Adjustment']->Quantity . ",
				" . (0 + $_SESSION['Adjustment']->Quantity) . "
			)";


		$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
		$DbgMsg =  _('The following SQL to insert the stock movement record was used');
		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);


/*Get the ID of the StockMove... */
		$StkMoveNo = DB_Last_Insert_ID($db,'stockmoves','stkmoveno');

/*Insert the StockSerialMovements and update the StockSerialItems  for controlled items*/

		if ($_SESSION['Adjustment']->Controlled ==1){
			foreach($_SESSION['Adjustment']->SerialItems as $Item){
			/*We need to add or update the StockSerialItem record and
			The StockSerialMoves as well */

				/*First need to check if the serial items already exists or not */
				$SQL = "SELECT COUNT(*)
					FROM stockserialitems
					WHERE
					stockid='" . $_SESSION['Adjustment']->StockID . "'
					AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'
					AND serialno='" . $Item->BundleRef . "'";
				$ErrMsg = _('Unable to determine if the serial item exists');
				$Result = DB_query($SQL,$db,$ErrMsg);
				$SerialItemExistsRow = DB_fetch_row($Result);

				if ($SerialItemExistsRow[0]==1){

					$SQL = "UPDATE stockserialitems SET
						quantity= quantity + " . $Item->BundleQty . "
						WHERE
						stockid='" . $_SESSION['Adjustment']->StockID . "'
						AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'
						AND serialno='" . $Item->BundleRef . "'";

					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				} else {
					/*Need to insert a new serial item record */
					$SQL = "INSERT INTO stockserialitems (stockid,
									loccode,
									serialno,
									quantity)
						VALUES ('" . $_SESSION['Adjustment']->StockID . "',
						'" . $_SESSION['Adjustment']->StockLocation . "',
						'" . $Item->BundleRef . "',
						" . $Item->BundleQty . ")";

					$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock item record could not be updated because');
					$DbgMsg =  _('The following SQL to update the serial stock item record was used');
					$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
				}


				/* now insert the serial stock movement */

				$SQL = "INSERT INTO stockserialmoves (stockmoveno, 
									stockid, 
									serialno, 
									moveqty) 
						VALUES (" . $StkMoveNo . ", 
							'" . $_SESSION['Adjustment']->StockID . "', 
							'" . $Item->BundleRef . "', 
							" . $Item->BundleQty . ")";
				$ErrMsg =  _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The serial stock movement record could not be inserted because');
				$DbgMsg =  _('The following SQL to insert the serial stock movement records was used');
				$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

			}/* foreach controlled item in the serialitems array */
		} /*end if the adjustment item is a controlled item */



		$SQL = "UPDATE locstock SET quantity = " . $_SESSION['Adjustment']->Quantity . " 
				WHERE stockid='" . $_SESSION['Adjustment']->StockID . "' 
				AND loccode='" . $_SESSION['Adjustment']->StockLocation . "'";

		$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' ._('The location stock record could not be updated because');
		$DbgMsg = _('The following SQL to update the stock record was used');

		$Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);

		if ($_SESSION['CompanyRecord']['gllink_stock']==1 AND $_SESSION['Adjustment']->StandardCost > 0){

			$StockGLCodes = GetStockGLCode($_SESSION['Adjustment']->StockID,$db);
			// bowikaxu insert cantidades en ceros
			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'" . $SQLAdjustmentDate . "', 
						" . $PeriodNo . ", 
						" .  $StockGLCodes['adjglact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * -(-1*$QtyOnHandPrior) . ", 
						'" . $_SESSION['Adjustment']->StockID . " x " . (-1*$QtyOnHandPrior) . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string('Inicializacion a Ceros') . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);
			// fin cantidades en ceros
			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'" . $SQLAdjustmentDate . "', 
						" . $PeriodNo . ", 
						" .  $StockGLCodes['adjglact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * -($_SESSION['Adjustment']->Quantity) . ", 
						'" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string('Inicializacion en Ceros') . "')";

			$ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg, true);

			
			// bowikaxu insert cantidades en ceros
			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'" . $SQLAdjustmentDate . "', 
						" . $PeriodNo . ", 
						" .  $StockGLCodes['stockact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * (-1*$QtyOnHandPrior) . ", 
						'" . $_SESSION['Adjustment']->StockID . " x " . (-1*$QtyOnHandPrior) . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string('Inicializacion en Ceros') . "')";

			$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
			// fin insert cantidades en ceros
			$SQL = "INSERT INTO gltrans (type, 
							typeno, 
							trandate, 
							periodno, 
							account, 
							amount, 
							narrative) 
					VALUES (17,
						" .$AdjustmentNumber . ", 
						'" . $SQLAdjustmentDate . "', 
						" . $PeriodNo . ", 
						" .  $StockGLCodes['stockact'] . ", 
						" . $_SESSION['Adjustment']->StandardCost * $_SESSION['Adjustment']->Quantity . ", 
						'" . $_SESSION['Adjustment']->StockID . " x " . $_SESSION['Adjustment']->Quantity . " @ " . $_SESSION['Adjustment']->StandardCost . " " . DB_escape_string($_SESSION['Adjustment']->Narrative) . "')";

			$Errmsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The general ledger transaction entries could not be added because');
			$DbgMsg = _('The following SQL to insert the GL entries was used');
			$Result = DB_query($SQL,$db, $ErrMsg, $DbgMsg,true);
		}

		//$Result = DB_query('COMMIT',$db);
		//prnMsg( _('A stock adjustment for'). ' ' . $_SESSION['Adjustment']->StockID . ' -  ' . $_SESSION['Adjustment']->ItemDescription . ' '._('has been created from location').' ' . $_SESSION['Adjustment']->StockLocation .' '. _('for a quantity of') . ' ' . (-1*$QtyOnHandPrior),'success');
		prnMsg( _('A stock adjustment for'). ' ' . $_SESSION['Adjustment']->StockID . ' -  ' . $_SESSION['Adjustment']->ItemDescription . ' '._('has been created from location').' ' . $_SESSION['Adjustment']->StockLocation .' '. _('for a quantity of') . ' ' . $_SESSION['Adjustment']->Quantity,'success');
		
		unset ($_SESSION['Adjustment']);
		
       
		echo "<HR>";
      }
      
    }
    fclose($fh_in);
    //echo "COMMIT"."<BR>";
	DB_query("COMMIT",$db);

  }
	                                                  
  else {
    echo "<CENTER><H2>Archivo Inexistente</CENTER></H2>";
    include ('includes/footer.inc');
    exit;
  }
  
                                                  
  }
include ('includes/footer.inc');
?>