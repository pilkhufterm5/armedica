<?php

    $PageSecurity = 11;
    
    include('includes/DefineCartClass.php');
    include('includes/GetPrice.inc');
    include('includes/SQL_CommonFunctions.inc');
    include('includes/session.inc');
    $title = _('Transferencia por Lote');
    echo '<script type="text/javascript" src="javascripts/jquery.js"></script>';
    
    ?>
	
	<script type="text/javascript">
		function habilita_combo(){
			if(document.getElementById('Prestamo').checked){
				document.getElementById('Motivo_salida').disabled = true
			}else{
				document.getElementById('Motivo_salida').disabled = false
			}
		}
		
		$(document).ready(function() {			
			$("#hidden_location").val($("#location").val());
			
			$("#location").change(function(){
				var loc = this.value;
				$("#hidden_location").val(loc);
				$("#location_act").val(loc);
				$("#location_search").val(loc);
				$("#location_search2").val(loc);
			});
			
			$("#Motivo_salida").change(function(){
				var valor = this.value;
				$("#Motivo_salida_act").val(valor);
				$("#Motivo_salida_search").val(valor);
				$("#Motivo_salida_search2").val(valor);
			});
			
			$("#Area").change(function(){
				var valor = this.value;
				$("#Area_act").val(valor);
				$("#Area_search").val(valor);
				$("#Area_search2").val(valor);
			});
			
			$("#Empleado").change(function(){
				var valor = this.value;
				$("#Empleado_act").val(valor);
				$("#Empleado_search").val(valor);
				$("#Empleado_search2").val(valor);
			});
			
			$("#Comment").change(function(){
				var valor = this.value;
				$("#Comment_act").val(valor);
				$("#Comment_search").val(valor);
				$("#Comment_search2").val(valor);
			});
		});
		
	</script>
	
    <?php
    
    include('includes/header.inc');
    
    /*
     * 
     * 
     * funcion para evaluar si el numero esta correctamente
     */
    function revisar_cantidad_numerica($cantidad){
        if(is_numeric($cantidad)){
            $cantidad = $cantidad;
        }else{
            $cantidad = 1;
        }

        if($cantidad < 0){
            $cantidad = 1;
        }else{
            $cantidad = $cantidad;
        }
        return $cantidad;
    }
	
	
	//$result  =  move_uploaded_file($_FILES['ItemPicture']['tmp_name'], $filename);
	//Upload del archivo
	if(($_FILES['Archivo']['size']>0)){
		
		$path=realpath('tmp/');
		$path = str_replace('\\','/',$path);
		
		if (!move_uploaded_file($_FILES['Archivo']['tmp_name'],$path.'/'.$_FILES['Archivo']['name'])){
			prnMsg(_('No se pudo cargar el archivo'), 'error');
		}else{
			$filename = $path.'/'.$_FILES['Archivo']['name'];
			if(@$fh_in = fopen($filename,"r")){
				$_SESSION['ArtToTiCket']=array();
				while(($line = fgetcsv($fh_in,0,','))!==false){
					if($line!= null){
						
						/*$stockid = $line[0];
						$barcode = $line[1];
						$desc    = $line[2];
						$lote    = $line[3];
						$NewItemQty = $line[4];*/
						$ID = $line[0];
						$NewItemQty = $line[1];
						$barcode = $line[2];
						$lote    = $line[3];
						//$desc    = $line[2];
						if($NewItemQty > 0){
							/*se hace el sql para verficar que exista el articulo de vdd exista*/
							$sql = "SELECT stockmaster.description,
												stockmaster.stockid,
												stockmaster.units,
												stockmaster.volume,
												stockmaster.kgs,
												(materialcost+labourcost+overheadcost) AS standardcost,
												locstock.quantity,
												UPPER(stockmaster.mbflag) as mbflag,
												stockmaster.discountcategory,
												stockmaster.decimalplaces,
												stockmaster.discontinued,
												stockmaster.barcode
									FROM stockmaster INNER JOIN locstock
									ON stockmaster.stockid=locstock.stockid
									WHERE stockmaster.barcode = '". $barcode . "'";
								/*cuando se agrega al arreglo de arreglos*/
							$result1 = DB_query($sql,$db,'No se puede procesar el sql de los articulos');
							if (DB_num_rows($result1)==0){
								/*se da la advertencia si no se encuentran los elementos*/
								prnMsg(_('The item code') . ' ' . $barcode  . ' '  . _('could not be found in the database'),'warn',_('Item Does Not Exist'));
							}elseif ($myItemRow = DB_fetch_array($result1)){
								
								$sql_rev = 'select * from stockmaster 
								inner join stockserialitems on stockmaster.stockid=stockserialitems.stockid 
								where stockmaster.barcode="'.$barcode.'" and loccode="'.$_POST['FromStockLocation'].'" and serialno="'.$lote.'"';
								$rs_rev  = DB_query($sql_rev,$db);
								if(!DB_num_rows($rs_rev)){
									prnMsg(_('El art&iacute;culo con c&oacute;digo de barras '.$barcode.' no existe en el Lote y Almac&eacute;n seleccionado como origen.'), 'error');
								}else{
									/*valida la canitdad*/
									if($NewItemQty == 0){
										$NewItemQty = 1;
									}else{
										$NewItemQty = $NewItemQty;
									}

									if(is_numeric($NewItemQty)){
										$NewItemQty = $NewItemQty;
									}else{
										$NewItemQty = 1;
									}

									if($NewItemQty < 0){
										$NewItemQty = 1;
									}else{
										$NewItemQty = $NewItemQty;
									}
									
									/*si existe se agrega al array*/
									$arraiTemporal['NewItem'] = $myItemRow['stockid'];
									$arraiTemporal['NewItemQty'] = $NewItemQty;
									$arraiTemporal['description'] = $myItemRow['description'];
									$arraiTemporal['units'] = $myItemRow['units'];
									$arraiTemporal['barcode'] = $myItemRow['barcode'];
									$arraiTemporal['lote'] = $lote;

									/*se asigna el articulo a la variable de session con la cual estyo trabajando*/
									$_SESSION['ArtToTiCket'][] = $arraiTemporal;
								}
							}
						}
					}
				}
			}
		}
	}
	
	
	/*arreglo de articulos cuando se envian y se trabajan con ellos*/
    if (isset($_POST['order_items'])){
            foreach ($_POST as $key => $value) {
                    if (strstr($key,"itm")) {
                            $NewItem_array[substr($key,3)] = trim($value);
                    }
            }
    }
	
	If (isset($NewItem_array) && isset($_POST['order_items'])){
            foreach($NewItem_array as $NewItem => $NewItemQty){
                if($NewItemQty > 0){
                    /*se hace el sql para verficar que exista el articulo de vdd exista*/
                    $sql = "SELECT stockmaster.description,
                                        stockmaster.stockid,
                                        stockmaster.units,
                                        stockmaster.volume,
                                        stockmaster.kgs,
                                        (materialcost+labourcost+overheadcost) AS standardcost,
                                        locstock.quantity,
                                        UPPER(stockmaster.mbflag) as mbflag,
                                        stockmaster.discountcategory,
                                        stockmaster.decimalplaces,
                                        stockmaster.discontinued,
                                        stockmaster.barcode
                            FROM stockmaster INNER JOIN locstock
                            ON stockmaster.stockid=locstock.stockid
                            WHERE stockmaster.stockid = '". $NewItem . "'";
                        /*cuando se agrega al arreglo de arreglos*/
                    $result1 = DB_query($sql,$db,'No se puede procesar el sql de los articulos');
                    if (DB_num_rows($result1)==0){
                        /*se da la advertencia si no se encuentran los elementos*/
                        prnMsg(_('The item code') . ' ' . $NewItem  . ' '  . _('could not be found in the database'),'warn',_('Item Does Not Exist'));
                    }elseif ($myItemRow = DB_fetch_array($result1)){
                        /*valida la canitdad*/
                        if($NewItemQty == 0){
                            $NewItemQty = 1;
                        }else{
                            $NewItemQty = $NewItemQty;
                        }

                        if(is_numeric($NewItemQty)){
                            $NewItemQty = $NewItemQty;
                        }else{
                            $NewItemQty = 1;
                        }

                        if($NewItemQty < 0){
                            $NewItemQty = 1;
                        }else{
                            $NewItemQty = $NewItemQty;
                        }
                        
                        /*si existe se agrega al array*/
                        $arraiTemporal['NewItem'] = $NewItem;
                        $arraiTemporal['NewItemQty'] = $NewItemQty;
                        $arraiTemporal['description'] = $myItemRow['description'];
                        $arraiTemporal['units'] = $myItemRow['units'];
                        $arraiTemporal['barcode'] = $myItemRow['barcode'];
                        $arraiTemporal['lote'] = $_POST['Lote'.$NewItem];

                        /*se asigna el articulo a la variable de session con la cual estyo trabajando*/
                        $_SESSION['ArtToTiCket'][] = $arraiTemporal;
                    }
                }
            }
        }

    /*operaciones sobre el array*/
    If ((isset($_SESSION['ArtToTiCket']) && count($_SESSION['ArtToTiCket'])>0)){
        If(isset($_GET['Delete'])){
            if(count($_SESSION['ArtToTiCket'])>0){
                /*se quita*/
                $mi_contador = 1;
                $item_borrar = $_GET['Delete'];
                $mi_otro_contador = 0;
		foreach ($_SESSION['ArtToTiCket'] as $OrderLine) {
                    if($item_borrar != $mi_contador){
                        $arraiTemporal['NewItem'] = $OrderLine['NewItem'];
                        $arraiTemporal['NewItemQty'] = $OrderLine['NewItemQty'];
                        $arraiTemporal['description'] = $OrderLine['description'];
                        $arraiTemporal['units'] = $OrderLine['units'];
                        $arraiTemporal['barcode'] = $OrderLine['barcode'];
                        $arraiTemporal['lote'] = $OrderLine['lote'];
                        /*se asigna el articulo a la variable de session con la cual estyo trabajando*/
                        $mi_tempora_array[$mi_otro_contador] = $arraiTemporal;
                        $mi_otro_contador = $mi_otro_contador + 1;
                    }
                    $mi_contador = $mi_contador +1;
                }
                unset($_SESSION['ArtToTiCket']);
                $_SESSION['ArtToTiCket'] = $mi_tempora_array;
            }
        }

        /*se procesa la inforamcion del array para actualizarlo*/

        /*aki entraria cuando existe via post*/
        /*poder hacer operacion sobre el array*/

        $mi_contador = 1;
        $mi_otro_contador = 0;
        foreach($_SESSION['ArtToTiCket'] as $OrderLine){
            if (isset($_POST['Quantity_'.$mi_contador])){
                if($_POST['Quantity_'.$mi_contador] == 0){
                    $mi_cantidad = 1;
                }else{
                    $mi_cantidad = $_POST['Quantity_'.$mi_contador];
                }

                if(is_numeric($mi_cantidad)){
                    $mi_cantidad = $mi_cantidad;
                }else{
                    $mi_cantidad = 1;
                }

                if($mi_cantidad < 0){
                    $mi_cantidad = 1;
                }else{
                    $mi_cantidad = $mi_cantidad;
                }

                /*la ultima validacion*/
                $mi_cantidad = $mi_cantidad * 1;
                $arraiTemporal['NewItem'] = $OrderLine['NewItem'];
                $arraiTemporal['NewItemQty'] = $mi_cantidad;
                $arraiTemporal['description'] = $OrderLine['description'];
                $arraiTemporal['units'] = $OrderLine['units'];
                $arraiTemporal['comment'] = $_POST['Comment_'.$mi_contador];
                $arraiTemporal['barcode'] = $OrderLine['barcode'];
                $arraiTemporal['lote'] = $_POST['Lote_'.$mi_contador];
                
                /*se asigna el articulo a la variable de session con la cual estyo trabajando*/
                $mi_tempora_array[$mi_otro_contador] = $arraiTemporal;
                $mi_otro_contador = $mi_otro_contador + 1;
            }
            $mi_contador = $mi_contador + 1;
        }
        /*por lo cual aqui debe de iniciar*/
        if($mi_otro_contador > 0){
            /*porque de vdd entro*/
            unset($_SESSION['ArtToTiCket']);
            $_SESSION['ArtToTiCket'] = $mi_tempora_array;
        }

    }else{
        /*si no existe pues lo limpiamos*/
        unset($_SESSION['ArtToTiCket']);
    }
    
    /*seccion donde se procesan los datos*/
	if(isset($_GET['action'])){
		if($_GET['action'] == "Procesar" && ($_FILES['Archivo']['size']<=0) && count($_SESSION['ArtToTiCket'])>0){
				
			$errors = array();
			$error = array();
			$errors_info = array();
			
			
			
			//Validamos los datos - que existan los barcode, stockid, serialno
			foreach($_SESSION['ArtToTiCket'] as $id=>$item){
				$cancel = true;
				
				//Validamos existencias
				$sql_rev = 'select * from stockserialitems where stockid="'.$item['NewItem'].'" and loccode="'.$_POST['FromStockLocation'].'" and serialno="'.$item['lote'].'"';
				$rs_rev  = DB_query($sql_rev,$db);
				if(!DB_num_rows($rs_rev)){
					prnMsg(_('El art&iacute;culo '.$item['NewItem'].' no existe en el Lote y Almac&eacute;n seleccionado como origen.'), 'error');
					$errors[] = 1;
					
				}else{
					if($_SESSION['ProhibitNegativeStock']==1){
						//Comprobamos si hay suficiente stock para mandar.
						$rw_rev = DB_fetch_assoc($rs_rev);
						$cantidad_disponible = $rw_rev['quantity'];
						if($item['NewItemQty']>$cantidad_disponible){
							$item_qty = 0;
							$errors[] = 1;
							prnMsg(_('El art&iacute;culo '.$item['NewItem'].' no tiene la suficiente cantidad en el almacen y lote de origen para hacer la transferencia.'), 'error');
						}
					}
				}
			}
			
			
			if(count($errors) == 0 && count($errors_info)==0){
				//$AdjustmentNumber = GetNextTransNo(20002,$db);
				//$PeriodNo = GetPeriod (Date($_SESSION['DefaultDateFormat']), $db);
				$CurrentDate = FormatDateForSQL(Date($_SESSION['DefaultDateFormat']));
				
				$Result = DB_Txn_Begin($db);
				
				if(!isset($Trf_ID)){
					$Trf_ID = GetNextTransNo(16,$db);
				}
				
				//Se hace el insert a la tabla header
				//@ToDo: Considerar poner datetime
				$sql_ticket  = 'insert into rh_transfer_lote_preview(reference,fecha_envio,userid_envio,location_from,location_to,comentario) values';
				$sql_ticket .= '('.$Trf_ID.', "'.$CurrentDate.'", "'.$_SESSION['UserID'].'", "'.$_POST['FromStockLocation'].'", "'.$_POST['ToStockLocation'].'", "'.DB_escape_string($_POST['Comment']).'")';
				$Result = DB_query($sql_ticket,$db);
				
				$ticket = DB_Last_Insert_ID($db,'rh_transfer_lote_preview','id');
				
				
				foreach($_SESSION['ArtToTiCket'] as $item){
					
					$sql = "INSERT INTO loctransfers (reference,
								stockid,
								shipqty,
								shipdate,
								shiploc,
								recloc,
								rh_usrsend)
						VALUES ('" . $Trf_ID . "',
							'" . $item['NewItem'] . "',
							'" . $item['NewItemQty'] . "',
							'" . Date('Y-m-d') . "',
							'" . $_POST['FromStockLocation']  ."',
							'" . $_POST['ToStockLocation'] . "',
							'".$_SESSION['UserID']."')";
					$ErrMsg = _('CRITICAL ERROR') . '! ' . _('Unable to enter Location Transfer record for'). ' '.$_POST['StockID' . $i];
					$resultLocShip = DB_query($sql,$db, $ErrMsg);
					
					$transfer_id = DB_Last_Insert_ID($db,'loctransfers','id');
					
					//$sqlInsert = "VALUES($AdjustmentNumber, '".$item['NewItem']."', '".$item['description']."', ".$item['NewItemQty'].", '".$item['units']."', '".$item['UserID']."', ".$item['NewItemQty'].", '".date('Y-m-d h:i:s')."', 0)";
					$sqlInsert = 'insert into rh_transfer_lote_preview_details(loctransfer_id,transfer_lote_id, stockid, barcode, serialno, qty_envio) values';
					$sqlInsert .= '('.$transfer_id.','.$ticket.', "'.$item['NewItem'].'", "'.$item['barcode'].'", "'.$item['lote'].'", "'.$item['NewItemQty'].'")';
					$result1 = DB_query($sqlInsert,$db);
					
				}
				
				$Result = DB_Txn_Commit($db);
				unset($_SESSION['ArtToTiCket']);
				unset($_SESSION['FromLocation']);
				unset($_SESSION['ToLocation']);
				unset($_POST);
				prnMsg( sprintf(_('La solicitud de env&iacute;o %s se ha creado exitosamente.'),$Trf_ID), 'success');
				
				$link  = 'Imprimit env&iacute;o: <A target="_blank" HREF="' . $rootpath . '/rh_PDF_StocktransferLote.php?' . SID . '&TransferID=' . $ticket . '">' . 'Imprimir PDF' . '</A>';
				$link .= '<br>';
				$link .= 'Imprimit env&iacute;o: <A target="_blank" HREF="' . $rootpath . '/rh_CSV_StocktransferLote.php?' . SID . '&TransferID=' . $ticket . '">' . 'Imprimir CSV' . '</A>';
				prnMsg( _($link), 'success');
				
			}
		}
	}
    
    if($_GET['action']=='Procesar' && isset($_POST['FromStockLocation'])){
		$_SESSION['FromLocation'] = $_POST['FromStockLocation'];
		$_SESSION['ToLocation']   = $_POST['ToStockLocation'];
	}
	
    /*falta el poder definir si el arreglo es nuevo o no es nuevo*/

    /*empieza la pagina y el codigo de la misma*/

    echo "<center>";
    
    echo '<h2>Transferencia entre almacenes por lote</h2>';
    
    
    //Encabezado
    echo '<br /><form enctype="multipart/form-data" action="'.$_SERVER['PHP_SELF'].'?action=Procesar" method="POST">';
    echo '<table>';
		
		//Almacen
		//Motivo de salida
		echo '<TR><TD>'. _('Seleccione su archivo'). ':</TD><TD><input type="file" name="Archivo" size=21></TD></TR>';
	
		echo '<TR><TD colspan="2"><small></smal>El orden de las columnas es: ID, Cantidad, C&oacute;digo de Barras, y Lote.</small></TD></TR>';
		
		
		//Almacen from
		echo '<tr>';
		echo '<td>Almacen origen</td>';
		echo '<td>';
		echo '<SELECT name="FromStockLocation">';
		
		foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
			if ($_POST['FromStockLocation']==$key || $_SESSION['FromLocation']==$key){
				echo "<OPTION SELECTED Value='$key'>$value";
			} else {
				echo "<OPTION Value='$key'>$value";
			}
		}
		echo '</SELECT>';
		echo '</td>';
		echo '</tr>'."\n";
		
		
		//Almacen To
		echo '<tr>';
		echo '<td>Almacen Destino</td>';
		echo '<td>';
		echo '<SELECT name="ToStockLocation">';

		foreach($_SESSION['rh_permitionlocation'] as $key=>$value){
			if ($_POST['ToStockLocation']==$key || $_SESSION['ToLocation']==$key){
				echo "<OPTION SELECTED Value='$key'>$value";
			} else {
				echo "<OPTION Value='$key'>$value";
			}
		}
		echo '</SELECT>';
		echo '</td>';
		echo '</tr>';
		
		
		echo '<tr><td>'._('Comments').'</td>';
		echo '<td><textarea name="Comment" id="Comment" cols=40 rows=5>'.htmlentities($_POST['Comment']).'</textarea></td>';
		echo '</tr>';
		
	echo '</table>';
	
	/*if (count($_SESSION['ArtToTiCket'])>0  && isset($_SESSION['ArtToTiCket'])){
		echo '<INPUT TYPE=SUBMIT NAME="Procesa_Transfer" VALUE="' . _('Procesar') . '"><HR>';
	}*/
	echo '<INPUT TYPE=SUBMIT NAME="Procesa_Transfer" VALUE="' . _('Procesar') . '"><HR>';
	echo '</form>';
    

    echo "<b><p>Articulos en la transferencia</p></b>";
	
        /*tabla donde se presenta el contenido de los articulos*/
	if (count($_SESSION['ArtToTiCket'])>0  && isset($_SESSION['ArtToTiCket'])){
		echo '<br /><form action="'.$_SERVER['PHP_SELF'].'?action=update" method="POST">
			<TABLE CELLPADDING=2 COLSPAN=7 BORDER=1>
			<TR BGCOLOR=#800000>';
		echo '<TD class="tableheader">' . _('Stockid') . '</TD>
			<TD class="tableheader">' . _('Barcode') . '</TD>
			<TD class="tableheader">' . _('Descripcion') . '</TD>
			<TD class="tableheader">' . _('Lote') . '</TD>
			<TD class="tableheader">' . _('Cantidad') . '</TD>
            <TD class="tableheader">' . _('Borrar') . '</TD>';

		$k =0;  //row colour counter
                $mi_contador = 1;
		foreach ($_SESSION['ArtToTiCket'] as $OrderLine) {
            if ($k==1){
				$RowStarter = '<tr bgcolor="#CCCCCC">';
				$k=0;
			} else {
				$RowStarter = '<tr bgcolor="#EEEEEE">';
				$k=1;
			}
			echo $RowStarter;
			echo '<input type="hidden" name="POLine_' . $mi_contador . '" value="">';
			echo '<TD><A target="_blank" HREF="' . $rootpath . '/StockStatus.php?' . SID . '&StockID=' . $OrderLine['NewItem'] . '">' . $OrderLine['NewItem'] . '</A></TD>
				<TD>' .  $OrderLine['barcode'] . '</TD>
				<TD>' .  $OrderLine['description'] . '</TD>';
			echo '<TD><INPUT TABINDEX=2 TYPE=TEXT NAME="Lote_' . $mi_contador . '" SIZE=6 MAXLENGTH=30 VALUE=' . $OrderLine['lote'] . '>';
			echo '<TD><INPUT TABINDEX=2 TYPE=TEXT NAME="Quantity_' . $mi_contador . '" SIZE=6 MAXLENGTH=6 VALUE=' . $OrderLine['NewItemQty'] . '>';
			echo '</TD>';
            
			$RemTxt = _('Borrar');
			echo '<TD><A HREF="' . $_SERVER['PHP_SELF'] . '?' . SID . '&action=edit&id=' . $_GET['id'] . '&Delete=' . $mi_contador . '" onclick="return confirm(\'' . _('Are You Sure?') . '\');">' . $RemTxt . '</A></TD></TR>';
                        $mi_contador = $mi_contador + 1;
		}

		echo "<input type='hidden' name='ticket' value='".$_GET['id']."' />";
                
		echo '</TABLE><br />';
		
		
		echo '<br>';
		
		echo '<input type="hidden" name="location" id="location_act" value="'.$_POST['location'].'">';
        echo '<input type="hidden" name="Motivo_salida" id="Motivo_salida_act" value="'.$_POST['Motivo_salida'].'">';
        echo '<input type="hidden" name="Area" id="Area_act" value="'.$_POST['Area'].'">';
        echo '<input type="hidden" name="Empleado" id="Empleado_act" value="'.$_POST['Empleado'].'">';
        echo '<input type="hidden" name="Comment" id="Comment_act" value="'.$_POST['Comment'].'">';
        echo '<input type="hidden" name="ToStockLocation" value="'.$_POST['ToStockLocation'].'">';
        echo '<input type="hidden" name="FromStockLocation" value="'.$_POST['FromStockLocation'].'">';
        
		
		//echo '<INPUT TYPE=SUBMIT NAME="DeliveryDetails" VALUE="' . _('Procesar') . '"><HR>';
		echo '<INPUT TYPE=SUBMIT NAME="DeliveryDetails" VALUE="' . _('Actualizar') . '"><HR>';
        echo "</form>";
	}

    /*a partir de aki no se tendira que mostrar cuando ya cambio de fase*/
    if(1==1){

    echo "<br /><hr /><br />";
    echo "<b><p>Agregar Articulos desde el Inventario</p></b>";

    /*aki se inicia el codigo para la busqueda los articulos y lo que conlleva*/
    echo "<form name='articulos' action='".$_SERVER['PHP_SELF']."?action=search' method='POST'>";
        $SQL="SELECT categoryid, categorydescription FROM stockcategory ORDER BY categorydescription";
        $result1 = DB_query($SQL,$db);
		
		echo '<input type="hidden" name="location" id="location_search2" value="'.$_POST['location'].'">';
        echo '<input type="hidden" name="Motivo_salida" id="Motivo_salida_search2" value="'.$_POST['Motivo_salida'].'">';
        echo '<input type="hidden" name="Area" id="Area_search2" value="'.$_POST['Area'].'">';
        echo '<input type="hidden" name="Empleado" id="Empleado_search2" value="'.$_POST['Empleado'].'">';
        echo '<input type="hidden" name="Comment" id="Comment_search2" value="'.$_POST['Comment'].'">';

        echo '<input type="hidden" name="PartSearch" value="' .  _('Yes Please') . '">';
        echo '<input type="hidden" name="hidden_location" id="hidden_location" value="">';
        echo '<BR><CENTER><b>' . _('Search for Order Items') . '</b><TABLE><TR><TD><FONT SIZE=2>' . _('Select a Stock Category') . ':</FONT><SELECT TABINDEX=1 NAME="StockCat">';

        if (!isset($_POST['StockCat'])){
            echo "<OPTION SELECTED VALUE='All'>" . _('All'); $_POST['StockCat'] ='All';
         }else{
            echo "<OPTION VALUE='All'>" . _('All');
        }

        while ($myrow1 = DB_fetch_array($result1)) {
            if ($_POST['StockCat']==$myrow1['categoryid']){
                echo '<OPTION SELECTED VALUE=' . $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
            }else{
                echo '<OPTION VALUE='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
            }
        }
        echo "</SELECT>";
    ?>
    <TD><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Description'); ?>:</FONT></TD>
    <TD><INPUT TABINDEX=2 TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25 VALUE="<?php if (isset($_POST['Keywords'])) echo $_POST['Keywords']; ?>"></TD></TR>
    <TR><TD></TD>
    <TD><FONT SIZE 3><B><?php echo _('OR'); ?> </B></FONT><FONT SIZE=2><?php echo _('Enter partial'); ?> <?php echo _('Stock Code'); ?>:</FONT></TD>
    <TD><INPUT TABINDEX=3 TYPE="Text" NAME="StockCode" SIZE=15 MAXLENGTH=18 VALUE="<?php if (isset($_POST['StockCode'])) echo $_POST['StockCode']; ?>"></TD>
    </TR>
    </TABLE>
    <CENTER><INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Buscar'); ?>">
    </FORM><br/>
    <?php

    /*busqueda de articulos se copio identico de la seccion de los pedidos*/
    If (isset($_POST['Search']) or isset($_POST['Next']) or isset($_POST['Prev'])){
		If (isset($_POST['Keywords']) AND isset($_POST['StockCode'])) {
			$msg='<BR>' . _('Stock description keywords have been used in preference to the Stock code extract entered') . '.';
		}

		If (isset($_POST['Keywords']) AND strlen($_POST['Keywords'])>0) {
			//insert wildcard characters in spaces
			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

			$i=0;
			$SearchString = '%';
			while (strpos($_POST['Keywords'], ' ', $i)) {
				$wrdlen=strpos($_POST['Keywords'],' ',$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . '%';
				$i=strpos($_POST['Keywords'],' ',$i) +1;
			}
			$SearchString = $SearchString. substr($_POST['Keywords'],$i).'%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster,
						stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='M' )
					AND stockmaster.description " . LIKE . " '$SearchString'
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='D' )
					AND stockmaster.description " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					ORDER BY stockmaster.stockid";
			}

		} elseif (strlen($_POST['StockCode'])>0){

			$_POST['StockCode'] = strtoupper($_POST['StockCode']);
			$SearchString = '%' . $_POST['StockCode'] . '%';

			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='D' )
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					AND stockmaster.controlled=1 
					AND stockmaster.serialised=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='D' )
					AND stockmaster.stockid " . LIKE . " '" . $SearchString . "'
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					AND stockmaster.controlled=1 
					AND stockmaster.serialised=0
					ORDER BY stockmaster.stockid";
			}

		} else {
			if ($_POST['StockCat']=='All'){
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE  stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='D' )
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					AND stockmaster.controlled=1 
					AND stockmaster.serialised=0
					ORDER BY stockmaster.stockid";
			} else {
				$SQL = "SELECT stockmaster.stockid,
						stockmaster.description,
						stockmaster.units
					FROM stockmaster, stockcategory
					WHERE stockmaster.categoryid=stockcategory.categoryid
					AND (stockcategory.stocktype='F' OR stockcategory.stocktype='D' OR stockcategory.stocktype='D' )
					AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
					AND stockmaster.discontinued = 0
					AND stockmaster.mbflag='B'
					AND stockmaster.controlled=1 
					AND stockmaster.serialised=0
					ORDER BY stockmaster.stockid";
			  }
		}

		if (isset($_POST['Next'])) {
			$Offset = $_POST['nextlist'];
		}
		if (isset($_POST['Prev'])) {
			$Offset = $_POST['previous'];
		}
		if (!isset($Offset) or $Offset<0) {
			$Offset=0;
		}
		$SQL = $SQL . ' LIMIT ' . $_SESSION['DisplayRecordsMax'].' OFFSET '.number_format($_SESSION['DisplayRecordsMax']*$Offset);
		$ErrMsg = _('There is a problem selecting the part records to display because');
		$DbgMsg = _('The SQL used to get the part selection was');
		$SearchResult = DB_query($SQL,$db,$ErrMsg, $DbgMsg);

		if (DB_num_rows($SearchResult)==0 ){
			prnMsg (_('There are no products available meeting the criteria specified'),'info');

			if ($debug==1){
				prnMsg(_('The SQL statement used was') . ':<BR>' . $SQL,'info');
			}
		}
		if (DB_num_rows($SearchResult)==1){
			$myrow=DB_fetch_array($SearchResult);
			$NewItem = $myrow['stockid'];
			DB_data_seek($SearchResult,0);
		}
		if (DB_num_rows($SearchResult)<$_SESSION['DisplayRecordsMax']){
			$Offset=0;
		}   
    }

    /*seccion donde se inician los resultados de busqueda, se modifico de pedidos*/
    if (isset($SearchResult)) {
        echo '<CENTER><form name="orderform" method="POST" action="rhStocktransferLote.php?action=edit&id='.$_GET['id'].'"><TABLE CELLPADDING=2 COLSPAN=7 >';
            
            //Se agregan los objetos hidden para que no se borra la información superior
            
            echo '<input type="hidden" name="location" id="location_search" value="'.$_POST['location'].'">';
            echo '<input type="hidden" name="Motivo_salida" id="Motivo_salida_search" value="'.$_POST['Motivo_salida'].'">';
            echo '<input type="hidden" name="Area" id="Area_search" value="'.$_POST['Area'].'">';
            echo '<input type="hidden" name="Empleado" id="Empleado_search" value="'.$_POST['Empleado'].'">';
            echo '<input type="hidden" name="Comment" id="Comment_search" value="'.$_POST['Comment'].'">';
            
            $TableHeader = '<TR><TD class="tableheader">' . _('Code') . '</TD>
               			<TD class="tableheader">' . _('Description') . '</TD>
              			<TD class="tableheader">' . _('Units') . '</TD>
               			<TD class="tableheader">' . _('Quantity') . '</TD>
               			<TD class="tableheader">' . _('Lote') . '</TD>
               			<TD class="tableheader">' . _('Existencia') . '</TD></TR>';
               			
            echo $TableHeader;
            $j = 1;
            $k=0; //row colour counter

            while ($myrow=DB_fetch_array($SearchResult)) {
				
				$sql_loc = "select sum(quantity) from locstock where stockid='".$myrow['stockid']."'";
				$rs_loc = DB_query($sql_loc,$db);
				$rw_loc = DB_fetch_row($rs_loc);
				$existencia = $rw_loc[0];
				
				$sql_lote = 'select serialno from stockserialitems where stockid="'.$myrow['stockid'].'" group by serialno order by serialno asc ';
				$rs_lote  = DB_query($sql_lote,$db);
				$cmb_lote = '<select name="Lote'.$myrow['stockid'].'">';
				while($rw_lote = DB_fetch_assoc($rs_lote)){
					$cmb_lote .= '<option value="'.$rw_lote['serialno'].'">'.$rw_lote['serialno'].'</option>'; 
				}
				$cmb_lote .= '</select>';
				
                printf('<TD><FONT SIZE=1>%s</FONT></TD>
                        <TD><FONT SIZE=1>%s</FONT></TD>
				<TD><FONT SIZE=1>%s</FONT></TD>
				<TD><FONT SIZE=1><input tabindex='.number_format($j+7).' type="textbox" size=6 name="itm'.$myrow['stockid'].'" value=0>
				</FONT></TD>
				<TD><FONT SIZE=1>%s</FONT></TD>
				<TD><FONT SIZE=1>%d</FONT></TD></TR>',
				$myrow['stockid'],
				$myrow['description'],
				$myrow['units'],
				$cmb_lote,
				$existencia);
				$j++;
            }
            echo '<tr><td align=center><input type="hidden" name="previous" value='.number_format($Offset-1).'><input tabindex='.number_format($j+7).' type="submit" name="Prev" value="Prev"></td>';
            echo '<td align=center colspan=2><input type="hidden" name="order_items" value=1><input tabindex='.number_format($j+8).' type="submit" value="Order"></td>';
            echo '<td align=center><input type="hidden" name="nextlist" value='.number_format($Offset+1).'><input tabindex='.number_format($j+9).' type="submit" name="Next" value="Next"></td></tr>';
            echo '</TABLE></form>';
    }

    }
    //include('includes/footer.inc');
?>
