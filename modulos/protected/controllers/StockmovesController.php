<?php
include_once ($_SERVER['LocalERP_path'] . '/includes/DefinePedimentoItems.php');
include_once ($_SERVER['LocalERP_path'] . '/includes/DefineStockAdjustment.php');
include_once ($_SERVER['LocalERP_path'] . '/includes/DefineSerialItems.php');

/**
 * StockmovesController
 *
 * @package ARMedica
 * @author  erasto@realhost.com.mx
 */

class StockmovesController extends Controller
{

    public $sep = ",";

    public function actionIndex() {


//    $_Fecha = date("Y-m-d", strtotime("2016/12/20"));

//    $params = array('fecha' => $_Fecha, 'almacen' => '046', 'reporte' => 1,'movimientoid'=>'3623066');

//    $this->actionBajaPorConsumoWS();  
   // $this->actionBajaPorConsumo();
 //   $consumirs = new ISSTELEONWS("https://tema.isssteleon.gob.mx/cisec/medix.asmx?WSDL", array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => false));
  // $ObtenerSalidasDiariasArray = $consumirs->consumir('ObtenSalidasDiarias', $params);
  // pr($ObtenerSalidasDiariasArray);

        $this->render('index');
    }

    public function actionTest() {

  


      $this->render('test');
    }

    /*
     * Funcion que devuelve una fila de un archivo dado, por default es un archivo csv,
     * @TODO implementar otros formatos como el xls, xlsx
    */
    public function NextLine($file) {
        static $Archivo, $formato;

        /*
         * Detectar formato del archivo
        */

        if (!isset($formato)) {
            $dom = @DOMDocument::load($file);
            if ($dom) $formato = 'xml';
            else $formato = 'csv';
        }

        /*
         *
        */
        switch ($formato) {
            case 'xml':
                if (!isset($Archivo)) {
                    $Archivo = $dom->getElementsByTagName('Row');
                    reset($Archivo);
                } else next($Archivo);
                if ($row = current($Archivo)) {
                    $cells = $row->getElementsByTagName('Cell');
                    $Fila = array();
                    foreach ($cells as $cell) {
                        $Fila[] = $cell->nodeValue;
                    }
                    return $Fila;
                }

                break;

            case 'csv':
                if (!isset($Archivo)) {
                    if (!(@$Archivo = fopen($file, "r"))) $Archivo = false;
                }
                if ($Archivo) if (!feof($Archivo)) {
                    $GetSSV = fgetcsv($Archivo, $this->sep);
                    if (!is_array($GetSSV) || count($GetSSV) == 1) {
                        if (count($GetSSV) == 1) $GetSSV = array_pop($GetSSV);
                        if (stripos($GetSSV, ";")) $this->sep = ';';
                        else if (stripos($GetSSV, "|")) $this->sep = '|';
                        else if (stripos($GetSSV, ",")) $this->sep = ',';
                        $GetSSV = explode($this->sep, $GetSSV);
                    }
                    return array_map(function ($r) {
                        return trim($r, '"');
                    }, $GetSSV);
                }
                break;
            }
            return false;
        }

        public function actionBajaporconsumo() {
            global $db;
            $file = getcwd() . '/tmp/' . $_FILES['csv']["name"];
            if (isset($_FILES['csv']) && ($_FILES['csv']['error'] == 0) && $_FILES['csv']['size'] > 10) {
                move_uploaded_file($_FILES['csv']['tmp_name'], $file); {
                    $id = 0;
                    $Unidad = 1;
                    $CodigoBarras = 0;
                    $Comentarios = 0;
                    $StockIdPos = 0;
                    $Lote = - 1;
                    while ($fila = $this->NextLine($file)) {
                        if (trim(implode('', $fila)) != '') {
                            if ($id == 0) {
                                foreach ($fila as $ind => $val) {
                                    if (stripos(' ' . $val, 'stockid')) {
                                        $StockIdPos = $ind;
                                    } else if (stripos(' ' . $val, 'Unidades')) $Unidad = $ind;
                                    if (stripos(' ' . $val, 'Barras')) $CodigoBarras = $ind;

                                    //$StockIdPos=$ind;
                                    if (stripos(' ' . $val, 'Comentarios')) $Comentarios2 = $ind;
                                    if (stripos(' ' . $val, 'lote')) $Lote = $ind;
                                    else if ($Lote == - 1 && stripos(' ' . $val, 'serie')) $Lote = $ind;

                                    //$StockIdPos=$ind;$Comentarios


                                }
                            } else {
                                $SearchKey = $fila[$StockIdPos];
                                $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE stockid =('" . $SearchKey . "')";
                                $_resulta2 = DB_query($SearchByBarCode, $db);
                                if (DB_num_rows($_resulta2) == 0 && trim($fila[$CodigoBarras]) != '') {
                                    $SearchKey = $fila[$CodigoBarras];
                                    $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE barcode =('" . $SearchKey . "')";
                                }
                                if ($_resulta2 = DB_query($SearchByBarCode, $db)) if ($myrow2 = DB_fetch_assoc($_resulta2)) {
                                    $registro = array('qty' => $fila[$Unidad], 'stockid' => $myrow2['stockid'], 'description' => $myrow2['description'], 'comentarios2' => $fila[$Comentarios2]);
                                    if ($Lote != - 1) $registro['lote'] = $fila[$Lote];
                                    else $registro['lote'] = '';
                                    $_POST[$id] = $registro;
                                }
                            }
                            $id++;
                        }
                    }
                    if ($id > 0) $_POST['AddItems'] = $_POST['Commit'] = 'Procesar';
                }
            }

            if (isset($_POST['AddItems'])) {
                foreach ($_POST as $_Items) {
                    if (!empty($_Items['qty'])) {
                        if ($_Items['qty'] != 0) {
                            $Items[] = $_Items;
                        }
                    }
                }

                if (!empty($Items) && !empty($_POST['AddItems'])) {

                    //$Comentarios = $_POST['Comentarios'];


                    $Almacen = $_POST['intostocklocation'];
                    $Type = 20010;
                    foreach ($Items as $_2Item) {
                        
                        $query1 = "SELECT description FROM stockmaster where stockid='".$_2Item['stockid']."'";
                        $res = DB_query($query1, $db);
                        $reg = DB_fetch_assoc($res);
                        $Comentarios = $reg['description'];
                        $item = array('StockID' => $_2Item['stockid'], 'Description' =>$Comentarios, 'Almacen' => $Almacen, 'QTY' => $_2Item['qty'], 'Coments' => '', 'Type' => $Type, 'Comentarios2' => $_2Item['comentarios2']);
                        $item['lote'] = trim($_2Item['lote']);
                        $_SESSION['AdjustmentCart']['BPC'][$_2Item['stockid'] . '-' . $item['lote']] = $item;
                    }
                }
            }
            if (!empty($_SESSION['AdjustmentCart']) && $_POST['Commit'] == 'Procesar') {
                $SQL = "INSERT INTO rh_stockmoves (
                                userid,
                                loccode,
                                type,
                                trandate,
                                description)
                        VALUES (
                                '" . $_SESSION['UserID'] . "',
                                '" . $_POST['intostocklocation'] . "',
                                '20010',
                                '" . date('Y-m-d') . "',
                                '" . $_POST['Coments'] . "'
                        )";
                $ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
                $DbgMsg = _('The following SQL to insert the stock movement record was used');
                $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
                $rh_stockmoves_id = DB_Last_Insert_ID($db, 'rh_stockmoves', 'id');
                
                foreach ($_SESSION['AdjustmentCart']['BPC'] as $_Item) {
                    $lotes = array();
                    $Comentarios = $_Item['Coments'];
                    $Almacen = $_Item['Almacen'];
                    $StockID = $_Item['StockID'];
                    $Comentarios2 = $_Item['Comentarios2'];
                    $Ajuste = ($_Item['QTY']) * -1;
                    if (isset($_Item['lote'])) $lotes[$_Item['lote']] = $Ajuste;
                    $Type = 20010;

                    $this->StockAdjustments($StockID, $Almacen, $Ajuste, $Comentarios, $Type, $rh_stockmoves_id, $Comentarios2, $Comentarios, $lotes); {
                        $SQL = "SELECT quantity FROM locstock
                                        WHERE stockid='" . DB_escape_string($StockID) . "'
                                        AND loccode='" . DB_escape_string($Almacen) . "'";
                        FB::INFO($SQL, '_______________________Query String');
                        $CheckNegResult = DB_query($SQL, $db);
                        $CheckNegRow = DB_fetch_array($CheckNegResult);
                        FB::INFO($CheckNegRow, '_______________________Result');

                        if ($_SESSION['ProhibitNegativeStock'] == 1 && $CheckNegRow['quantity'] + $Ajuste < 0) {
                            $InputError = true;
                            Yii::app()->user->setFlash("error", 'ITEM ' . $StockID . ' : Se establecieron parámetros del sistema para prohibir stocks negativos. El procesamiento de este Ajuste resultaría en stock negativo. Este ajuste NO será procesado.');
                        } else {
                            Yii::app()->user->setFlash("success", sprintf(_("Los Ajustes se Realizaron Correctamente; Numero de movimiento %s"), $rh_stockmoves_id));
                        }
                    }
                }
                unset($_SESSION['AdjustmentCart']['BPC']);
            }

            if (isset($_POST['Search'])) {
                $ItemsData = $this->SearchItems($_POST['StockCat'], $_POST['Keywords'], $_POST['StockCode']);
            }
            
            $CatList = Controller::GetStockCatList();
            if (empty($CatList)) {
                $CatList = array();
            }

            if (empty($ItemsData)) {
                $ItemsData = array();
            }

            //$rh_query = "SELECT description FROM stockmaster";
            /*$ListDesc = DB_query($rh_query, $db);
            while ($MyItem = DB_fetch_assoc($ListDesc)) {
                $MyItem = preg_replace("/[\n|\r|\n\r]/i", "", $MyItem);
                $ListDescriptions[] = addslashes($MyItem['description']);
            }*/

            $this->render('bajaporconsumo', array('ItemsData' => $ItemsData, 'CatList' => $CatList, 'ListDescriptions' => $ListDescriptions));
        }


        public function obtenerSerie($stockid, $almacen, $total) {
        	global $db;
            $series = array();
            $sql = "select * from stockserialitems where loccode='" . $almacen . "' and stockid='" . $stockid . "' and quantity>0 and quantity>={$total} order by unix_timestamp(expirationdate) asc, quantity desc";
            $res1 = DB_query($sql, $db);
            while ($resultado = DB_fetch_assoc($res1)) {
                if ($total > 0 && $resultado['quantity'] <= $total) {
                    $series[$resultado['serialno']] = -$resultado['quantity'];
                    $total = $total - $resultado['quantity'];
                } else {
                    if ($total > 0 && $resultado['quantity'] > $total) {
                        $series[$resultado['serialno']] = -$total;
                        $total = 0;
                    }
                }
            }
            return $series;
        }


        private function LlamarWS($WS, $params) {
            global $ServidoresdeCorreos;
            try {
            $ObtenerSalidasDiariasArray = $WS->consumir('ObtenSalidasDiarias', $params);
            }catch (Exception $e){
            	echo $e->getMessage();
            	return false;
            }
            IF (!is_array($ObtenerSalidasDiariasArray) || !is_array($ObtenerSalidasDiariasArray['NewDataSet'])){
            if(
                ((date("i")<10&&date("H")<13&&date("H")>8&&date('w')>0&&date('w')==1)
                    ||
                (date("H")==8&&date("i")<10&&date('w')==6)
                )&&
                    count($ServidoresdeCorreos)>0
                ){
                $ServidoresdeCorreos=array();
                $ServidoresdeCorreos[]=array(
                    'Host' => 'ssl://smtp.gmail.com',
                    'Port' => 465,
                    'SMTPAuth' => true,
                    'Username' => "envio@realhost.com.mx",
                    'Password' => "47V94669",
                    );
                    $ServidoresdeCorreos[]=array(
                    'Host' => 'ssl://smtp.gmail.com',
                    'Port' => 465,
                    'SMTPAuth' => true,
                    'Username' => "envio2@realhost.com.mx",
                    'Password' => "47V94669",
                    );
                    $ServidoresdeCorreos[]=array(
                    'Host' => 'ssl://smtp.gmail.com',
                    'Port' => 465,
                    'SMTPAuth' => true,
                    'Username' => "envio3@realhost.com.mx",
                    'Password' => "47V94669",
                    );
                    $ServidoresdeCorreos[]=array(
                    'Host' => 'ssl://smtp.gmail.com',
                    'Port' => 465,
                    'SMTPAuth' => true,
                    'Username' => "envio4@realhost.com.mx",
                    'Password' => "47V94669",
                    );
                EnviarMail('Servicio de Alertas',
                    array(
                //'angeles.perez@bacitconsultoria.com',
                //'rleal@realhost.com.mx',
                //'luis.borrego@bacitconsultoria.com',
                //'william.mejia@bacitconsultoria.com',
                'alicia.villarreal@armedica.com.mx',
		'gerardo.delangel@armedica.com.mx',
                //'rcortez@armedica.com.mx',
                'monica.vallejo@isssteleon.gob.mx'
                //'Cesar.montfort@ISSSTELEON.gob.mx',
                //'erasto@realhost.com.mx',
                //'rafael.rojas@realhost.com.mx'
                ),'WS ISSSTELEON INACTIVO','MENSAJE GENERADO AUTOMATICAMENTE<BR>'.
                '<BR> SE INTENTO CONSULTAR EL SERVIDOR DE ISSSTELEON Y FALLO, FAVOR DE REVISAR POSIBLE PROBLEMA DE SINCRONIZACION');
                unset($ServidoresdeCorreos);
                    }
                 return false;
            }

            //          $ObtenerSalidasDiariasArray2=$this->LlamarWS($WS,$params,$pagina+1);
            //          if($ObtenerSalidasDiariasArray2){
            //              foreach ($ObtenerSalidasDiariasArray2['NewDataSet']['Table'] as $key => $value){
            //                  if(isset($value["RecetaId"]))
            //                      $ObtenerSalidasDiariasArray['NewDataSet']['Table'][$key]=$value;
            //              }
            //          }
            return $ObtenerSalidasDiariasArray;
        }
		/**
		 * 
		 */
        public function actionBajaPorConsumoWS(){
        	
            global $db, $MensajesGlobales;
            $Almacen = "030";
            $AlmacenComplementarioSerie = "031";
            $AlmacenIsssteleonWS = "030";
            $AlmacenesWS=array(
            		array(
            				
            				'Almacen'=>'030',
            				'AlmacenComplementarioSerie'=>'031',
            				'AlmacenIsssteleonWS'=>'030',
            		),
            		array(
            				
            				'Almacen'=>'046',
            				'AlmacenComplementarioSerie'=>'031',
            				'AlmacenIsssteleonWS'=>'046',
            		),
            	);
            $reportes=array(20010=>1, 20009=>3); //array(Systypes=> "tipo reporte web service");
            $rh_stockmoves_id = 0;
            $TotalDelDia = 0;
            $Items=array();
            ini_set('memory_limit','256M');

			if(isset($_REQUEST['fecha']))
				$_Fecha = date("Y-m-d", strtotime($_REQUEST['fecha']));
			else{
            	if (((int)date("Hi")) < 145) {
               		$_Fecha = date("Y-m-d", strtotime('-1 day'));            	
            	} else $_Fecha = date("Y-m-d");
			}
			
            //$consumirs = new ISSTELEONWS("http://ar.isssteleon.gob.mx/recetas/Medix.asmx?WSDL", array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => false));
		$consumirs = new ISSTELEONWS("https://tema.isssteleon.gob.mx/cisec/medix.asmx?WSDL", array('cache_wsdl' => WSDL_CACHE_NONE, 'trace' => false));	

		$reporte=1;
			$_GET=$_POST;
			foreach($AlmacenesWS as $almacenws) {
				$Almacen=$almacenws['Almacen'];
				$AlmacenComplementarioSerie=$almacenws['AlmacenComplementarioSerie'];
				$AlmacenIsssteleonWS=$almacenws['AlmacenIsssteleonWS'];
			foreach($reportes as $Type=>$reporte)
			{
				$movimientoid=0;
				
				if (!(
                      ((int)date("Hi")) <=  430	//Update general antes de las 4:30 horas, considerando inventarios 
					||((int)date("Hi")) == 1000 //Update general a las 10:00 horas
                    ||((int)date("Hi")) == 1100 //Update general a las 11:00 horas
					||((int)date("Hi")) == 1200 //Update general a las 12:00 horas
                    ||((int)date("Hi")) == 1300 //Update general a las 13:00 horas
                    ||((int)date("Hi")) == 1400 //Update general a las 14:00 horas
					||((int)date("Hi")) == 1500 //Update general a las 15:00 horas
                    ||((int)date("Hi")) == 1600 //Update general a las 16:00 horas
					||((int)date("Hi")) == 1700 //Update general a las 17:00 horas
                    ||((int)date("Hi")) == 1800 //Update general a las 18:00 horas
                    ||((int)date("Hi")) == 1900 //Update general a las 19:00 horas
                    ||((int)date("Hi")) == 2000 //Update general a las 20:00 horas
					||((int)date("Hi")) >= 2100 //Update despues de las 9:00 pm
	            )) {
					$sql="select * from rh_isssteleonWS where date(FechaInicial)<=date('".DB_escape_string($_Fecha)."') and reporte={$reporte} order by MovimientoId desc limit 1";
					$res=DB_query($sql,$db);
					$movimientoid=1;
					if(!isset($_REQUEST['fecha'])&&$fila=DB_fetch_assoc($res))
						$movimientoid=$fila['MovimientoId'];
				}
	            $params = array('fecha' => $_Fecha, 'almacen' => $AlmacenIsssteleonWS, 'reporte' => $reporte,'movimientoid'=>$movimientoid);
	            
				
	            $ObtenerSalidasDiariasArray = $this->LlamarWS($consumirs, $params);
	            
	            if ($ObtenerSalidasDiariasArray) {
	                $Comentarios = 0;
	                if(!isset($ObtenerSalidasDiariasArray['NewDataSet'])||!isset($ObtenerSalidasDiariasArray['NewDataSet']['Table']))
	                	break;
	                foreach ($ObtenerSalidasDiariasArray['NewDataSet']['Table'] as $key => $value) {
	                    $StockIDws = "001";
	                   $sql="Select count(*)t from rh_isssteleonWS where "
	                    	." MovimientoId='".DB_escape_string($value['MovimientoId'])."'".
	                    	" and reporte='".DB_escape_string($reporte)."'";
	                    $res=DB_query($sql,$db);
	                    $fila=DB_fetch_assoc($res);
	                    if($fila['t']==0){
	                    	$sql="insert into rh_isssteleonWS(MedicamentoId,Medicamento,CodigoBarras,UnidadesSurtidas,FechaInicial,Comentarios,RecetaId, reporte, MovimientoId,FechaCreacion, loccode)values(";
	                    	$sql.='"'.DB_escape_string($value['MedicamentoId']).'",';
	                    	$sql.='"'.DB_escape_string($value['Medicamento']).'",';
	                    	$sql.='"'.DB_escape_string($value['CodigoBarras']).'",';
	                    	
	                    	// El reporte 3 envia un campo con FechaCancelacion en 
	                    	// ves de una FechaInicial
	                    	if($reporte!=3){
	                    		$sql.='"'.DB_escape_string($value['UnidadesSurtidas']).'",';
	                    	    $sql.='"'.DB_escape_string(date("Y-m-d H:i:s",strtotime(str_replace('T',' ',$value['FechaInicial'])))).'",';
	                    	}else{
	                    		$sql.='"'.DB_escape_string($value['UnidadesCanceladas']).'",';
	                    	    $sql.='"'.DB_escape_string(date("Y-m-d H:i:s",strtotime(str_replace('T',' ',$value['FechaCancelacion'])))).'",';
	                    	}
	                    	$sql.='"'.DB_escape_string(implode(' ',$value['Comentarios'])).'",';
	                    	$sql.='"'.DB_escape_string($value['RecetaId']).'"';
	                    	$sql.=",'".DB_escape_string($reporte)."'";
	                    	$sql.=",'".DB_escape_string($value['MovimientoId'])."'";
	                    	$sql.=",now()";
	                    	$sql.=",'".DB_escape_string($Almacen)."'";
	                    	$sql.=')';
	                    	DB_query($sql,$db);
	                    }
	                }
	            }
	            
	            $sql="update rh_isssteleonWS, stockmaster set rh_isssteleonWS.stockid=stockmaster.stockid where rh_isssteleonWS.CodigoBarras =stockmaster.barcode and stockmaster.discontinued=0 and rh_isssteleonWS.stkmoveno is null;";
	            $res=DB_query($sql,$db);
	                $sql="select stockmaster.stockid, ".
	 	                " rh_isssteleonWS.CodigoBarras, ".
	 	                " rh_isssteleonWS.stockid wsStockid, ".
	 	                " group_concat(distinct MovimientoId) MovimientoId, ".
	 	                " sum(UnidadesSurtidas) UnidadesSurtidas, ".
	 	                " group_concat(distinct RecetaId) RecetaId, ".
	 	                " rh_isssteleonWS.Medicamento, ".
	 	                " rh_isssteleonWS.FechaInicial, ".
	 	                " rh_isssteleonWS.Comentarios ".
	 	                ", rh_isssteleonWS.MedicamentoId".
	 	                ", rh_isssteleonWS.loccode ".
	 	                " from rh_isssteleonWS ".
	 	                " left join stockmaster on stockmaster.stockid= rh_isssteleonWS.stockid ".
	                 " where rh_isssteleonWS.loccode ='".DB_escape_string($Almacen)."'and ".
	                "stkmoveno is null and reporte='".DB_escape_string($reporte)."' group by stockmaster.stockid, CodigoBarras, RecetaId, MedicamentoId ";
	                $res=DB_query($sql,$db);
	                while($fila=DB_fetch_assoc($res)){//Buscamos los registros pendientes
	                        
                        $StockIDws = $fila['stockid'];
	                    $comentarios = $fila["Comentarios"];
	                    if($StockIDws=='') continue;
	                    $FechaInicial = date("Y-m-d", strtotime($fila['FechaInicial']));
	                    // Esto cambia el tipo de comentario dependiendo del reporte
	                    // Si el reporte es el 3, los movimientos agregan una narrativa 
	                    // que incluye que es una cancelación.
	                    $_Items = array(
	                    			"MedicamentoId" => $fila['MedicamentoId'], 
	                    			"CodigoBarras" => $fila["CodigoBarras"], 
	                    			"qty" => -$fila["UnidadesSurtidas"], 
	                    			"FechaInicial" => $fila["FechaInicial"], 
	                    			"comentarios2" => "WS ISSSTELEON Numero de receta: " . 
	                    								$fila["RecetaId"] . ":: Con Fecha: [" . $fila['FechaInicial'] . ']::.[' . 
	                    								$fila["CodigoBarras"] . '] ' . $comentarios, 
	                    			"stockid" => $fila['stockid'], 
	                    			"reference" => $fila["RecetaId"],
	                    			"RecetaId" => $fila["RecetaId"], 
	                    			"description" => $fila["Medicamento"], 
	                    			"loccode" => $fila["loccode"]);
	                    
	                    $_Items["MovimientoId"]=$fila['MovimientoId'];
	                    if($reporte==3){
	                    	$_Items["comentarios2"]="CANCELACION ".$_Items["comentarios2"];
	                    	$_Items["qty"]=abs($_Items["qty"]);
	                    }else
	                    	$_Items["qty"]=-abs($_Items["qty"]);
	                    $Comentarios = $_Items['reference'];
	                    
	                    
						$sql="update rh_isssteleonWS set stkmoveno=0 where MovimientoId in(".$_Items["MovimientoId"].") and stkmoveno is null and reporte='".DB_escape_string($reporte)."'";
	                    DB_query($sql,$db);
	                    $Ajuste = ($_Items['qty']);
	                    if($reporte==3)
                        {
								$SQL="select * from stockserialmoves where stockmoveno in(select stkmoveno from rh_isssteleonWS where MedicamentoId='".
										DB_escape_string($fila['MedicamentoId'])."' and RecetaId='".
										DB_escape_string($fila["RecetaId"])."')";
								$ress=DB_query($SQL,$db);
								$total=abs($Ajuste);
								while($Series=DB_fetch_assoc($ress)){
									if($total>=abs($Series['moveqty'])){
										$_Items['lotes']=array($Series['serialno']=>abs($Series['moveqty']));
										$total=$total-abs($Series['moveqty']);
									}else if($total>0){
										$_Items['lotes']=array($Series['serialno']=>$total);
										$total=0;
									}
								}
						}
	                    
	                	if (((float)$_Items['qty']) != 0){//Ajuste de cantidates
	                    	if($reporte==1)
                            {//Llenamos el lote
                                $_Items['lotes'] = $this->obtenerSerie($_Items['stockid'], $Almacen, abs($_Items['qty']));
                                if(!is_array($_Items['lotes'])||count($_Items['lotes'])==0)
                                	$_Items['lotes'] = $this->obtenerSerie($_Items['stockid'], $AlmacenComplementarioSerie, abs($_Items['qty']));
                            }else if($reporte==3){
                            	
                            }
                            {//Buscamos si se prohiben stocks negativos y hacemos el ajuste
                            		$Comentarios = $_Items['reference'];
                            		if(trim($_Items['loccode'])!='')
			                        	$Almacen2 = $_Items['loccode'];
                            		$Almacen2 =$Almacen;
			                        $StockID = $_Items['stockid'];
			                        $Comentarios2 = $_Items['comentarios2'];
			                        $description = $_Items["description"];
			                        $lotes = $_Items['lotes'];
			                        if (!is_array($lotes)||count($lotes)==0) $lotes = array();
			                        $Ajuste = ($_Items['qty']);
				                        
                            	$SQL = "SELECT quantity FROM locstock ".
				                                            " WHERE stockid='" . DB_escape_string($StockID) . "'".
				                                            " AND loccode='" . DB_escape_string($Almacen2) . "'";
	                            $CheckNegResult = DB_query($SQL, $db);
	                            $CheckNegRow = DB_fetch_array($CheckNegResult);
	                            $StockNegativo = $_SESSION['ProhibitNegativeStock'];
	                            $_SESSION['ProhibitNegativeStock'] = 0;
	                            if ($_SESSION['ProhibitNegativeStock'] == 1 && $CheckNegRow['quantity'] + $Ajuste < 0) {
	                                $InputError = true;
	                                Yii::app()->user->setFlash("error", 'ITEM ' . $StockID . ' : Se establecieron parámetros del sistema para prohibir stocks negativos. El procesamiento de este Ajuste resultaría en stock negativo. Este ajuste NO será procesado.');
	                            }
	                            if (!$InputError) {
	                            	
	                                $_POST['rh_orderline'] = $_Items["MedicamentoId"];
	                                $rh_stockmoves_id++;
	                                $stkmoveno=$this->StockAdjustments($StockID, $Almacen2, $Ajuste, $Comentarios, $Type, $rh_stockmoves_id, $Comentarios2, $description, $lotes);
	                                $sql="update rh_isssteleonWS set stkmoveno='".DB_escape_string($stkmoveno)."' where MovimientoId in(".$_Items["MovimientoId"].") and stkmoveno =0 and reporte='".DB_escape_string($reporte)."'";
	                                DB_query($sql,$db);
	                            }
	                            $_SESSION['ProhibitNegativeStock'] = $StockNegativo;
                            }
	                    }       
	                }//Buscamos los registros pendientes
	                $sql="update rh_isssteleonWS set stkmoveno=null where stkmoveno=0 and reporte='".DB_escape_string($reporte)."'";
                    DB_query($sql,$db);
			}
			echo '<div>Completed</div>';
			}
			return '';
        }

        /**
         * Actualiza y/o Elimina Items del Carrito.
         *
         * @return true
         * @author erasto@realhost.com.mx
         */
        public function actionUpdatecartlines() {
            if (!empty($_POST['UpdateCartLines']['stockid'])) {
                switch ($_POST['UpdateCartLines']['action']) {
                    case 'Update':
                        $_SESSION['AdjustmentCart']['BPC'][$_POST['UpdateCartLines']['stockid']]['QTY'] = $_POST['UpdateCartLines']['qty'];
                        if (!empty($_POST['UpdateCartLines']['coments'])) {
                            $_SESSION['AdjustmentCart']['BPC'][$_POST['UpdateCartLines']['stockid']]['Coments'] = $_POST['UpdateCartLines']['coments'];
                        }
                        Yii::app()->user->setFlash("success", "Actualizado Correctamente.");
                        break;

                    case 'Delete':
                        unset($_SESSION['AdjustmentCart']['BPC'][$_POST['UpdateCartLines']['stockid']]);
                        Yii::app()->user->setFlash("success", "Articulo Eliminado Correctamente.");
                        break;

                    default:
                        break;
                }
            }
        }

        public function SearchItems($StockCat, $Keywords, $StockCode) {
            global $db;
            if (isset($_POST['Search'])) {

                /*ie seach for stock items */
                if ($Keywords AND $StockCode) {
                    prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered'), 'info');
                }
                if ($Keywords) {

                    //insert wildcard characters in spaces
                    $SearchString = '%' . str_replace(' ', '%', $Keywords) . '%';
                    if ($StockCat == 'All') {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    AND stockmaster.description LIKE '" . $SearchString . "'
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    } else {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    AND stockmaster.description LIKE '" . $SearchString . "'
                    AND stockmaster.categoryid='" . $StockCat . "'
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    }
                } elseif ($StockCode) {
                    $_POST['StockCode'] = '%' . $StockCode . '%';
                    if ($StockCat == 'All') {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    AND stockmaster.stockid LIKE '" . $StockCode . "'
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    } else {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    AND stockmaster.stockid LIKE '" . $StockCode . "'
                    AND stockmaster.categoryid='" . $StockCat . "'
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    }
                } else {
                    if ($StockCat == 'All') {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    } else {
                        $sql = "SELECT stockmaster.stockid,
                        stockmaster.description,
                        stockmaster.units,
                        stockmaster.controlled,
                        stockmaster.serialised
                    FROM stockmaster INNER JOIN stockcategory
                    ON stockmaster.categoryid=stockcategory.categoryid
                    WHERE stockmaster.mbflag!='D'
                    AND stockmaster.mbflag!='A'
                    AND stockmaster.mbflag!='K'
                    and stockmaster.discontinued!=1
                    AND stockmaster.categoryid='" . $StockCat . "'
                    ORDER BY stockmaster.stockid
                    LIMIT " . $_SESSION['DefaultDisplayRecordsMax'];
                    }
                }
                $ErrMsg = _('There is a problem selecting the part records to display because');
                $DbgMsg = _('The SQL statement that failed was');
                $SearchResult = DB_query($sql, $db, $ErrMsg, $DbgMsg);

                while ($_ItemData = DB_fetch_assoc($SearchResult)) {
                    if ($_ItemData['serialised'] == 1) {
                        Yii::app()->user->setFlash("error", 'ITEM ' . $_ItemData['stockid'] . ' : Es serializado, funcion no activa para este usuario.');
                    } else $ItemData[] = $_ItemData;
                }
            }
            return $ItemData;
        }

        public function GetSeries($stockid) {
            global $db;
            $series = false;
            $SearchByDescription = "SELECT serialno FROM stockserialitems WHERE quantity<>0 and stockid = '" . $stockid . "' group by serialno";
            $result = DB_query($SearchByDescription, $db);
            if ($myrow = DB_fetch_assoc($result)) {
                $series = array();
                do {
                    $series[] = $myrow;
                } while ($myrow = DB_fetch_assoc($result));
            }
            return $series;
        }

        /**
         * Devuelve Descripcion de Articulo
         *
         * @return string
         * @author erasto@realhost.com.mx
         */
        public function actionGetitem() {
            global $db;
            $Listado = array();
            if (!empty($_POST['searchkey'])) {
                switch ($_POST['action']) {
                    case 'GetStockID':
                        $SearchKey = trim($_POST['searchkey']);
                        $SearchByDescription = "SELECT * FROM stockmaster WHERE description like '%description%" . $SearchKey . "%'";
                        $result = DB_query($SearchByDescription, $db);
                        $myrow = DB_fetch_assoc($result);

                        if (!empty($myrow)) {
                            $item = array('desc' => $myrow['description'], 'stockid' => $myrow['stockid'], 'Fila' => $_REQUEST['Fila']);
                            $serie = $this->GetSeries($myrow['stockid']);
                            if ($serie) {
                                $item['series'] = $serie;
                            }
                            $Listado[] = $item;
                        }

                        if (empty($myrow)) {
                            $SearchKey = explode(" ", str_replace(array("\t", "\n", " "), " ", $SearchKey));
                            $SearchKey = implode("','", $SearchKey);
                            $SearchByBarCode = "SELECT * FROM stockmaster WHERE barcode in('" . $SearchKey . "')";
                            $_resulta2 = DB_query($SearchByBarCode, $db);

                            while ($myrow2 = DB_fetch_assoc($_resulta2)) {
                                $item = array('desc' => $myrow2['description'], 'stockid' => $myrow2['stockid'], 'Fila' => $_REQUEST['Fila']);
                                $serie = $this->GetSeries($myrow['stockid']);
                                if ($serie) {
                                    $item['series'] = $serie;
                                }

                                $Listado[] = $item;
                            }
                        }
                        break;

                    default:
                        break;
                }
            }
            echo CJSON::encode($Listado);
            return;
        }

        public function StockAdjustments($StockID, $Almacen, $Ajuste, $Referencia, $Type, $rh_stockmoves_id, $narrative, $description = '', $lotes = array()) {

            global $db;
            $StkMoveNo=0;
            ini_set('display_errors', 1);
            error_reporting(1);
            $ExternalCall = true;
            ob_start();
            ob_start();
            $SQL = "select * from stockmaster where stockid='" . $StockID . "'";
            $result = DB_query($SQL, $db, $ErrMsg);
            $fila = DB_fetch_assoc($result);

            $_GET['NewAdjustment'] = 1;
            $_GET['StockID'] = $StockID;
            $Narrative = $Referencia;
            $_POST['Quantity'] = $Ajuste;
            $_POST['Comentarios2'] = $narrative;
            $_POST['description'] = $fila['description'] . '::' . $description;

            $_SESSION['UserStockLocation'] = $_POST['StockLocation'] = $_POST["StockLocation"] = $Almacen;

            if ($_POST['Quantity'] != 0) {
                include ($_SERVER['LocalERP_path'] . '/StockAdjustments.php');
                if (count($lotes) > 0) {
                    foreach ($lotes as $NewSerialNo=>$cantidad) {
                        $_SESSION['Adjustment']->SerialItems[$NewSerialNo] = new SerialItem($NewSerialNo, $cantidad, '');
                    }
                } else $_REQUEST['SoloExistenciaLote'] = $_SESSION['Adjustment']->SoloExistenciaLote = 2;
                $_SESSION['Adjustment']->Comentarios2 = $narrative;
                unset($_GET['StockID'], $_GET['NewAdjustment']);
                $_POST["CheckCode"] = "Check Part";
                $_POST["Narrative"] = $Narrative;
                $_POST["movePedimento"] = 1;
                $_POST['password'] = $passwordAdjustment;
                $_POST['EnterAdjustment'] = 1;

                $_SESSION['Adjustment']->StockLocation = $_SESSION['UserStockLocation'] = $_POST['StockLocation'] = $_POST["StockLocation"] = $Almacen;
                $_POST['StockID'] = $StockID;

                $succ = include ($_SERVER['LocalERP_path'] . '/StockAdjustments.php'); {
                    $Mensaje = ob_get_contents();
                }
                $_POST = array();
            }

            //ob_clean();
            ob_end_clean();
            ob_end_clean();
            return $StkMoveNo;
        }

        public function actionReportebajas() {
            global $db;

            if (!empty($_POST['StartDate'])) {
                $StartDate = $_POST['StartDate'];
            } else {
                $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
            }
            if (!empty($_POST['EndDate'])) {
                $EndDate = $_POST['EndDate'];
            } else {
                $EndDate = date('Y-m-d');
            }

            $SQL = "SELECT * FROM rh_stockmoves
            LEFT JOIN stockmoves on stockmoves.rh_stockmoves_id = rh_stockmoves.id
            LEFT JOIN systypes on stockmoves.type = systypes.typeid
            LEFT JOIN stockmaster on stockmaster.stockid = stockmoves.stockid
            WHERE stockmoves.type = 20010
            AND (stockmoves.trandate BETWEEN '" . $StartDate . "' AND '" . $EndDate . "')
            AND stockmoves.loccode = '" . $_POST['intostocklocation'] . "'";
            FB::INFO($SQL, '___________________SQL');
            $result = DB_query($SQL, $db, $ErrMsg);
            $ListMovesData = array();
            while ($MyMoves = DB_fetch_assoc($result)) {
                $ListMovesData[] = ($MyMoves);
            }
            $this->render('reportebajas', array('ListMovesData' => $ListMovesData));
        }

        /**
         * @Todo
         * Reporte de Inventario por Categorias y Almacen
         * Fechas de Caducidad
         * @author erasto@realhost.com.mx
         */
        public function actionReporteinventario() {
            FB::INFO($_POST, '_________________POST');
            global $db;
            set_time_limit(0);
            ini_set('memory_limit', '128M');
            
            
			$Familias=array();
			$SQL="Select * from rh_familia  group by categoria asc";
			$res=DB_query($SQL,$db);
			$i=0;
			if(!isset($_REQUEST['Categoria']))$_REQUEST['Categoria']=array();
			while($fila=DB_fetch_assoc($res)){
				$i++;
				$Familias[$i]=array('Nombre'=>_('Categoria').' '.$i);
				
				if(!isset($_REQUEST['Categoria'][$i]))$_REQUEST['Categoria'][$i]='';
				$SQL="Select * from rh_familia where categoria='".DB_escape_string($fila['categoria'])."'";
				$res2=DB_query($SQL,$db);
				$Familias[$i]['SelectName']='Categoria['.htmlentities($fila['categoria']).']';
				$Familias[$i]['SelectOption'][]=array(
						'value'=>'',
						'selected'=>false,
						'html'=>htmlentities('Todos')
				);
				while($fila2=DB_fetch_assoc($res2)){
					$Familias[$i]['SelectOption'][]=array(
							'value'=>htmlentities($fila2['clave']),
							'selected'=>$_REQUEST['Categoria'][$fila['categoria']]==$fila2['clave'],
							'html'=>htmlentities('( '.$fila2['clave'].' ) '.$fila2['nombre'])
					);
				}	
			}
			
            if (!empty($_POST['Search']['Category1']) && !empty($_POST['Search']['Category2'])) {

                if ($_POST['Search']['LockStock'] == "All") {
                    $LockStock = " ";
                } else {
                    $LockStock = " AND locstock.loccode = '{$_POST['Search']['LockStock']}' ";
                }
                if(!isset($_POST['Search']['InventarioCero']))
                	$LockStock .=" AND stockserialitems.quantity!=0 ";

                /*
                $SQL = "select stockmaster.stockid,
                     stockmaster.barcode,
                     stockmaster.description,
                     stockmaster.longdescription,
                     locstock.quantity
                     from locstock
                LEFT JOIN stockmaster on stockmaster.stockid = locstock.stockid
                WHERE locstock.loccode = '" . $LockStock . "'";*/

                $SQL = "SELECT
                stockmaster.barcode,
                (rh_stock_grupo.clave) as id_agrupador,
                (rh_stock_grupo.nombre) as id_agrupador_description,
                stockmaster.categoryid,
                stockcategory.categorydescription,
                stockmaster.stockid,
                stockmaster.description,
                stockserialitems.serialno,
                date(stockserialitems.expirationdate) as expirationdate,
                stockserialitems.quantity AS qtyonhand,
                stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost AS unitcost,
                stockserialitems.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal ";
            $SQLMAinleft=$SQLFamiliaWhere='';
			$and='';
			foreach($_REQUEST['Categoria'] as $categoria=>$valor){
				$categoria=(int)$categoria;
				$SQLMAinleft.=" left join rh_familia_stock rh_familiaCatStock".$categoria." on rh_familiaCatStock".$categoria.".stockid=stockmaster.stockid and rh_familiaCatStock".$categoria.".categoria='$categoria' ";
				$SQLMAinleft.=" left join rh_familia rh_familiaCat".$categoria." on rh_familiaCatStock".$categoria.".clave=rh_familiaCat".$categoria.".clave and rh_familiaCat".$categoria.".categoria='$categoria' ";
				$SQL.=", (rh_familiaCat".$categoria.".nombre) Categoria".$categoria." ";
				if(trim($valor)!=''){
					$SQLFamiliaWhere.=" and rh_familiaCat".$categoria.".clave='".DB_escape_string($valor)."' ";
					$SQLFamiliaWhere.=" and rh_familiaCatStock".$categoria.".clave='".DB_escape_string($valor)."' ";
				}
			}
            $SQL .=" FROM stockmaster
            LEFT JOIN stockcategory on stockmaster.categoryid=stockcategory.categoryid
            LEFT JOIN locstock on stockmaster.stockid = locstock.stockid
            LEFT JOIN stockserialitems on stockmaster.stockid=stockserialitems.stockid and locstock.loccode=stockserialitems.loccode
            LEFT JOIN rh_stock_grupo on stockmaster.id_agrupador = rh_stock_grupo.clave
            $SQLMAinleft
            WHERE stockmaster.categoryid=stockcategory.categoryid
            AND stockmaster.categoryid >= '" . $_POST['Search']['Category1'] . "'
            AND stockmaster.categoryid <= '" . $_POST['Search']['Category2'] . "'
            {$LockStock}
            $SQLFamiliaWhere
            AND stockmaster.discontinued = 0
            ORDER BY stockmaster.categoryid,
            expirationdate asc, stockmaster.stockid";

                //FB::INFO($SQL, '___________________SQL');
                $result = DB_query($SQL, $db);

                $ListMovesData = array();
                while ($MyMoves = DB_fetch_assoc($result)) {
                    array_push($ListMovesData, $MyMoves);

                    //$ListMovesData[] =  $MyMoves;

                }

                // echo CJSON::encode(array(
                //     'requestresult' => 'ok',
                //     'Data' => $ListMovesData
                // ));
                // return;

            }
            

            //FB::INFO($ListMovesData,'______________LIST MOVES');

            $this->render('reporteinventario', array('ListMovesData' => $ListMovesData, 'LockStock' => $LockStock, 'Categorias'=>$Familias));
        }

        public function actionPdfreport() {
            global $db;

            $_GET['TransNo'] = 1;

            chdir(dirname(__FILE__));
            include_once ($_SERVER['LocalERP_path'] . '/PHPJasperXML/class/fpdf/fpdf.php');
            include ($_SERVER['LocalERP_path'] . '/barcode2/barcode.inc.php');

            $dirfile = $_SERVER['LocalERP_path'] . "/tmp";
            $file = "Rec_" . $_GET['TransNo'] . ".jpeg";
            if (!is_file($dirfile . "/" . $file)) {
                $bar = new BARCODE();
                $bar->setSymblogy("CODE39");
                $bar->setHeight(30);
                $bar->setScale(2);
                $bar->setHexColor("#00000", "#FFFFFF");
                $return = $bar->genBarCode($_GET['TransNo'], "jpeg", $dirfile . "/Rec_" . $_GET['TransNo']);
            }

            include ($_SERVER['LocalERP_path'] . '/includes/FPDF.php');
            $pdf = new FPDF;
            $pdf->AliasNbPages();

            $pdf->AddPage();
            $pdf->SetFont('Arial', '', 7);
            $y = 5;

            //$q = "SELECT critico FROM  rh_salesorders WHERE  rh_salesorders.orderno = '" . $_GET['TransNo'] . "' ";
            //$result = DB_query($q, $db);

            /*
            if (DB_num_rows($result)==0) {
            $title = _('Select Order To Print');
            include ('includes/header.inc');
            echo '<DIV ALIGN=CENTER><BR><BR><BR>';
            prnMsg(_('Orden no encontrada'), 'error');
            echo '<BR><BR><BR><table class="table_index"><TR><TD CLASS="menu_group_item">
            <LI><A HREF="' . $rootpath . '/rh_tools/index.php?r=receta/index">' . _('Crear Receta') . '</A></LI>
            </TD></TR></TABLE></DIV><BR><BR><BR>';
            include ('includes/footer.inc');
            exit ;
            }
            $row = DB_fetch_array($result);
            */

            //$p = $row["critico"]!="" && $row["critico"]!=0 ? $row["critico"] : 1;
            $p = 1;

            $p = $p * 2;
            $par = 0;
            $n = 1;
            for ($i = 0; $i < $p; $i++) {

                $par++;
                if ($par > 2) {
                    $par = 1;
                    $pdf->AddPage();
                    $y = 0;
                    $pdf->SetY($y);
                    $n++;
                }

                if ($par == 2) {
                    $y = $pdf->PageBreakTrigger / 2;
                    $pdf->SetY($y);
                    $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/copia.png", 50, 120, 100, 'L');
                } else {
                    $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/logo_ma.png", 50, 30, 90, 'L');
                }

                /*
                $q = "SELECT
                rh_salesorders.*,
                debtorsmaster.name
                FROM
                rh_salesorders
                LEFT JOIN debtorsmaster ON debtorsmaster.debtorno = rh_salesorders.debtorno
                WHERE rh_salesorders.orderno = '" . $_GET['TransNo'] . "'";
                $result = DB_query($q, $db);

                $row = DB_fetch_array($result);
                */

                //$Comentarios = utf8_decode($row["comments"]);
                //$dir = $row["dirfactura"];
                //$numero_receta = $row["orderno"];

                $pdf->SetXY(12, $y);
                $pdf->Image($_SERVER['LocalERP_path'] . "/companies/" . $_SESSION['DatabaseName'] . "/logo.jpg", 5, $y, 30, 'L');

                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetX(170);

                //$pdf->Cell(30,10,'No:  '.$row["orderno"],0,0,'C');

                $y+= 12;

                //$pdf->SetXY(80,$y);
                //$pdf->Cell(0,0,'Nombre:',0,0,"L");

                $pdf->SetXY(80, $y);

                //$pdf->Cell(0, 0, $row["customerref"], 0, 0, "L");
                $y+= 6;
                $pdf->SetFont('Arial', '', 6);
                $pdf->SetXY(80, $y);

                //$pdf->Cell(0, 0, utf8_decode($row["universidad"]), 0, 0, "L");
                $pdf->SetXY(140, $y);

                //$pdf->Cell(0, 0, $row["especialidad"] . " " . $row["ced_esp"], 0, 0, "L");
                $y+= 6;

                $pdf->SetFont('Arial', '', 7);
                $pdf->SetXY(80, $y);

                //$pdf->Cell(0, 0, $row["domicilio"], 0, 0, "L");
                $y+= 6;

                $pdf->SetXY(30, $y);
                $pdf->SetFont('Arial', '', 9);

                //$pdf->Cell(0, 0, utf8_decode('Numero nómina:  ' . $row["numero_nomina"]), 0, 0, "L");
                $pdf->SetXY(80, $y);
                $pdf->SetFont('Arial', '', 9);

                //$pdf->Cell(0, 0, 'Ced. Prof:  ' . $row["ced_prof"], 0, 0, "L");
                $pdf->SetXY(138, $y);

                //$pdf->Cell(0, 0, 'Reg. SAA:  ' . $row["registro_ssa"], 0, 0, "L");
                $y+= 7;

                $pdf->SetXY(5, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 0, 'Titular:', 0, 0, "L");
                $pdf->SetXY(30, $y);
                $pdf->SetFont('Arial', '', 9);

                //$pdf->Cell(0, 0, $row["name"], 0, 0, "L");
                $pdf->SetXY(95, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 0, 'Fecha:', 0, 0, "L");
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY(110, $y);
                $pdf->Cell(0, 0, $row["deliverydate"], 0, 0, "L");
                $pdf->SetXY(138, $y);
                $pdf->SetFont('Arial', 'B', 9);
                if ($row["pro_consulta"] == 1) {
                    $pdf->Cell(0, 0, utf8_decode('Próxima cita: '), 0, 0, "L");
                    $pdf->SetFont('Arial', '', 9);
                    $pdf->SetXY(163, $y);

                    //$pdf->Cell(0, 0, $row["proxima_consulta"], 0, 0, "L");


                }
                $pdf->SetXY(163, $y);

                //$pdf->Cell(0,0,$row["deliverydate"],0,0,"L");
                $y+= 5;
                $pdf->SetXY(5, $y);
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 0, 'Paciente:', 0, 0, "L");
                $pdf->SetXY(30, $y);
                $pdf->SetFont('Arial', '', 9);

                //$pdf->Cell(0, 0, $row["buyername"] . '    Edad: ' . $row["edad_paciente"], 0, 0, "L");
                $pdf->SetXY(138, $y);
                $pdf->SetFont('Arial', '', 9);

                //$pdf->Cell(0, 0, utf8_decode('Paciente crónico:') . ' ' . $row["critico"], 0, 0, "L");
                $y+= 6;

                $pdf->SetFont('Arial', 'B', 8);
                $pdf->SetXY(5, $y);
                $pdf->Cell(0, 0, 'Codigo', 0, 0, "L");
                $pdf->SetXY(18, $y);
                $pdf->Cell(0, 0, utf8_decode("Descripción"), 0, 0, "L");
                $pdf->SetXY(75, $y);

                //$pdf->Cell(0,0,'Sustancia Activa',0,0,"L");
                $pdf->SetXY(88, $y);
                $pdf->Cell(0, 0, 'Cantidad', 0, 0, "L");
                $pdf->SetXY(115, $y);
                $pdf->Cell(0, 0, 'Observaciones', 0, 0, "L");
                $y+= 3;
                $pdf->Line(3, $y, 205, $y);

                $pdf->Ln(1);

                /*
                $q = "SELECT
                rh_salesorderdetails.stkcode,
                stockmaster.description,
                stockmaster.units,
                stockmaster.rh_descripcion_receta,
                rh_sustanciaactiva.nombre as sustanciaactiva,
                rh_salesorderdetails.quantity,
                rh_salesorderdetails.narrative
                FROM
                rh_salesorderdetails left join
                stockmaster on rh_salesorderdetails.stkcode =stockmaster.stockid LEFT JOIN
                rh_sustanciaactiva ON rh_sustanciaactiva.id=stockmaster.rh_sustanciaactiva
                WHERE
                rh_salesorderdetails.orderno = '" . $_GET['TransNo'] . "'";
                $result = DB_query($q, $db);
                */

                $pdf->SetFont('Arial', '', 7);
                $y+= 2;
                $pdf->SetY($y);

                while ($row = DB_fetch_array($result)) {

                    $pdf->SetX(5);

                    //$pdf->Cell(15, 3, $row["stkcode"], 0, 0, "L");
                    //$y1 = $pdf->rh_multicell(trim($row["rh_descripcion_receta"]), 45, 3, 57, 3, 18);
                    $pdf->SetX(90);

                    //$pdf->Cell(15, 3, $row["quantity"] . '  ' . $row["units"], 0, 0, "L");
                    //$y2 = $pdf->rh_multicell(utf8_decode($row["narrative"]), 45, 3, 57, 3, 115);
                    $y = $y1 > $y2 ? $y1 : $y2;
                    $pdf->SetY($y);
                }

                $pdf->SetDrawColor(0, 0, 0);
                $y = $pdf->GetY() + 8;

                $pdf->SetXY(10, $y + 16);
                $pdf->Cell(0, 0, '___________________________', 0, 0, "L");
                $y+= 6;
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY(12, $y + 16);
                $pdf->Cell(0, 0, utf8_decode(' Firma médico tratante'), 0, 0, "L");

                $pdf->SetXY(70, $y - 10);
                $pdf->Cell(130, 20, ' ', 1, 1, "L");
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetXY(72, $y - 5);
                $pdf->Cell(0, 0, 'Dx:', 0, 0, "L");

                $pdf->SetFont('Arial', '', 8);
                $pdf->SetDrawColor(255, 255, 255);
                $pdf->SetY($pdf->GetY() - 1.5);
                $pdf->SetX(80);
                $pdf->MultiCell(80, 3, $Comentarios, 0, 1, "L");

                $pdf->SetDrawColor(115, 115, 115);
                $pdf->SetXY(72, $y + 15);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 0, $dir, 0, 0, "L");
                $y+= 18;

                $pdf->Image($dirfile . "/" . $file, 167, $y, 38, 'L');
                $pdf->SetXY(10, $y + 10);
                $pdf->Cell(30, 10, 'Vigencia de 72 horas ', 0, 0, 'C');
                $pdf->SetXY(169, $y + 10);
                $pdf->Cell(30, 10, 'No:  ' . $numero_receta . "-$n", 0, 0, 'C');
                $y+= 25;
                $pdf->Line(3, $y, 205, $y);
                $pdf->Line(3, $y + 0.5, 205, $y + 0.5);
                $pdf->SetY($y+= 22);
            }

            ob_clean();
            header('Content-type: application/pdf');

            //header('Content-Length: ' . $len);
            header('Content-Disposition: inline; filename=StockLocStatus.pdf');
            header('Expires: 0');
            header('Cache-Control: no-cache, no-store, must-revalidate, post-check=0, pre-check=0');

            // HTTP 1.1.
            header('Pragma: no-cache');

            // HTTP 1.0.
            header('Pragma: public');
            $pdf->output();
            shell_exec("rm -f  $dirfile/$file");

            exit;

            $this->render('pdfreport');
        }

        /**
        * @todo
        * Lista Stockmaster
        */
        public function actionProducts(){

            $_2GetProductData = Yii::app()->db->createCommand()->select(' stockmaster.*, taxcat.taxcatname, stkcat.categorydescription ')
            ->from('stockmaster')
            ->leftJoin('taxcategories taxcat', 'stockmaster.taxcatid = taxcat.taxcatid')
            ->leftJoin('stockcategory stkcat', 'stockmaster.categoryid = stkcat.categoryid')
            //->where('debtorsmaster.debtorno = "' . $DebtorNo . '"')
            //->limit('10')
            ->queryAll();
            //FB::INFO($_2GetProductData,'______________________DATA');
            $this->render("products", array("ProductData" => $_2GetProductData));
        }




    }

