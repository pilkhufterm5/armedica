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
class StockmovesController extends Controller {

    public $sep = ",";

    public function actionIndex() {

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
                    return array_map(function($r){return trim($r,'"');},$GetSSV);
                }
            break;
        }
        return false;
    }

    public function actionBajaporconsumo() {
        global $db;
        $file = getcwd() . '/tmp/'.$_FILES['csv']["name"];
        if (isset($_FILES['csv']) && ($_FILES['csv']['error'] == 0) && $_FILES['csv']['size'] > 10) {
            move_uploaded_file($_FILES['csv']['tmp_name'], $file); {
                $id = 0;
                $Unidad = 1;
                $CodigoBarras = 0;
                $Comentarios = 0;
                $StockIdPos = 0;
                $Lote=-1;
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
                                else
                                if ($Lote==-1&&stripos(' ' . $val, 'serie')) $Lote = $ind;

                                //$StockIdPos=$ind;$Comentarios

                            }
                        } else {
                            $SearchKey = $fila[$StockIdPos];
                            $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE stockid =('" . $SearchKey . "')";
                            $_resulta2 = DB_query($SearchByBarCode, $db);
                            if (DB_num_rows($_resulta2) == 0&&trim($fila[$CodigoBarras])!='') {
                                $SearchKey = $fila[$CodigoBarras];
                                $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE barcode =('" . $SearchKey . "')";
                            }
                            if ($_resulta2 = DB_query($SearchByBarCode, $db)) if ($myrow2 = DB_fetch_assoc($_resulta2)){
								$registro= array(
	                                'qty' => $fila[$Unidad],
	                                'stockid' => $myrow2['stockid'],
	                                'description' => $myrow2['description'],
	                                'comentarios2' => $fila[$Comentarios2]);
								if($Lote!=-1)
									$registro['lote'] = $fila[$Lote];
								else
									$registro['lote'] ='';
								$_POST[$id] = $registro;
                            }
                        }
                        $id++;
                    }
                }
                if($id>0)
                	$_POST['AddItems'] = $_POST['Commit'] = 'Procesar';
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
                $Comentarios = $_POST['Comentarios'];
                $Almacen = $_POST['intostocklocation'];
                $Type = 20010;
                foreach ($Items as $_2Item) {


                    $item = array(
                        'StockID' => $_2Item['stockid'],
                        'Description' => $_2Item['description'],
                        'Almacen' => $Almacen,
                        'QTY' => $_2Item['qty'],
                        'Coments' => $Comentarios,
                        'Type' => $Type,
                        'Comentarios2' => $_2Item['comentarios2']

                    );
                    $item['lote'] = trim($_2Item['lote']);
                    $_SESSION['AdjustmentCart']['BPC'][$_2Item['stockid'].'-'.$item['lote']]=$item;
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
                                '" . $_POST['Comentarios'] . "'
                        )";
            $ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
            $DbgMsg = _('The following SQL to insert the stock movement record was used');
            $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
            $rh_stockmoves_id = DB_Last_Insert_ID($db, 'rh_stockmoves', 'id');

            foreach ($_SESSION['AdjustmentCart']['BPC'] as $_Item) {
            	$lotes=array();
                $Comentarios = $_Item['Coments'];
                $Almacen = $_Item['Almacen'];
                $StockID = $_Item['StockID'];
                $Comentarios2 = $_Item['Comentarios2'];
                $Ajuste = ($_Item['QTY']) * -1;
                if(isset($_Item['lote']))
                	$lotes[]=$_Item['lote'];
                $Type = 20010;

                $this->StockAdjustments($StockID, $Almacen, $Ajuste, $Comentarios, $Type, $rh_stockmoves_id, $Comentarios2,$Comentarios,$lotes); {
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
                        Yii::app()->user->setFlash("success", sprintf(_("Los Ajustes se Realizaron Correctamente; Numero de movimiento %s") , $rh_stockmoves_id));
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

        $rh_query = "SELECT description FROM stockmaster";
        $ListDesc = DB_query($rh_query, $db);
        while ($MyItem = DB_fetch_assoc($ListDesc)) {
            $MyItem = preg_replace("/[\n|\r|\n\r]/i", "", $MyItem);
            $ListDescriptions[] = addslashes($MyItem['description']);
        }
        $this->render('bajaporconsumo', array(
            'ItemsData' => $ItemsData,
            'CatList' => $CatList,
            'ListDescriptions' => $ListDescriptions
        ));
    }
	public function obtenerSerie($stockid,$almacen,$total){
		$series=array();
		$sql="select * from stockserialitems where loccode='".$almacen."' and stockid='".$stockid."' and quantity>0 and quantity<={$total} order by unix_timestamp(expirationdate) asc, quantity desc";
		$res1=DB_query($sql,$db);
		while($resultado=DB_fetch_assoc($res1)){
			if($total>0&&$series['quantity']<=$total){
				$series[$resultado['serialno']]=$series['quantity'];
				$total=$total-$series['quantity'];
			}else{
				if($total>0&&$series['quantity']>$total){
					$series[$resultado['serialno']]=$total;
					$total=0;
				}
			}

		}
	}
    private function LlamarWS($WS, $params, $pagina = 1) {
        $params['reporte'] = $pagina;
        $ObtenerSalidasDiariasArray = $WS->consumir('ObtenSalidasDiarias', $params);

        IF (!is_array($ObtenerSalidasDiariasArray) || !is_array($ObtenerSalidasDiariasArray['NewDataSet'])) return false;

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
     * @Todo
     * WS ISSTELEON
     * @author Fernando Sanchez
     */
    public function actionBajaporconsumoWS() {
        global $db;
        $Almacen = "030";
        $AlmacenIsssteleonWS = "030";
        $Type = 20010;
        $rh_stockmoves_id = 0;
		$TotalDelDia=0;
        //include 'ws/class.consumirSOAP.php';

        //$consumirs = ISSTELEONWS::_();
        $consumirs = new ISSTELEONWS("http://ar.isssteleon.gob.mx/recetas/Medix.asmx?WSDL", array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => false
        ));

        /*
        $consumirs = new consumirSOAP("http://ar.isssteleon.gob.mx/recetas/Medix.asmx?WSDL", array(
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => false
        ));*/

        //$_Fecha = '2014-07-14';
        $_Fecha = date("Y-m-d");
        $params = array(
            'fecha' => $_Fecha,
            'almacen' => $AlmacenIsssteleonWS,
            'reporte' => '1'
        );

        $ObtenerSalidasDiariasArray = $this->LlamarWS($consumirs, $params);

        /**
         *Modifica la salida del ws al formato que necesita la funcion
         */

        //$ArregloAcomodado = array();
        if ($ObtenerSalidasDiariasArray) {
            $Comentarios = 0;
            foreach ($ObtenerSalidasDiariasArray['NewDataSet']['Table'] as $key => $value) {
                $StockIDws = "001";
//              $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE stockid =('" . $value['CodigoBarras'] . "')";
                $SearchByBarCode = "SELECT stockid,description FROM stockmaster WHERE stockid in(" .  
				"select stockid from locstock where quantity<>0 and stockid in(select stockid from stockmaster where barcode='" . DB_escape_string($value['CodigoBarras']) . "') and loccode='{$Almacen}' order by quantity desc "
                . ")";
                //pre_var_dump($SearchByBarCode);
                if ($_resulta = DB_query($SearchByBarCode, $db)) {
                    if ($myrow = DB_fetch_assoc($_resulta)) {
                        $StockIDws = $myrow['stockid'];
                    }
                }
                $comentarios = implode(' ', $value["Comentarios"]);
                $_POST[] = array(
                    "MedicamentoId" => $value['MedicamentoId'],
                    "CodigoBarras" => $value["CodigoBarras"],
                    "qty" => $value["UnidadesSurtidas"],
                    "FechaInicial" => $value["FechaInicial"],
                    "comentarios2" => "WS ISSSTELEON Numero de receta: " . $value["RecetaId"] . ":: Con Fecha: [" . $value['FechaInicial'] . ']::.[' . $value["CodigoBarras"] . '] ' . $comentarios,
                    "stockid" => $StockIDws,
                    "reference" => $value["RecetaId"],

                    //"description"=> str_replace(array(':','-','/','\\','T',' '),'',$value['FechaSurtido']).'::'.$value["RecetaId"],
                    "RecetaId" => $value["RecetaId"],
                    "description" => $value["Medicamento"],
                );
            }

            file_put_contents("entradas.txt", var_export($_POST, 1)); {
                $_POST['AddItems'] = $_POST['Commit'] = 'Procesar';
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
                    $Comentarios = $_POST['Comentarios'];

                    //$Almacen = $_POST['intostocklocation'];
                    foreach ($Items as $_2Item) {

                        $item = array(
                            "MedicamentoId" => $_2Item['MedicamentoId'],
                            'StockID' => $_2Item['stockid'],
                            'Description' => $_2Item['description'],
                            'Almacen' => $Almacen,
                            'QTY' => $_2Item['qty'],
                            'Coments' => $Comentarios,
                            'Type' => $Type,
                            'Comentarios2' => $_2Item['comentarios2'],
                            "reference" => $_2Item["reference"],
                            "description" => $_2Item["description"],
                            "CodigoBarras" => $_2Item["CodigoBarras"]
                        );
                        $item['lote']=trim($_2Item["lote"]);
                        if($item['lote']==''){
                        	$item['lotes']=$this->obtenerSerie($_2Item['stockid'],$Almacen,$_2Item['qty']);
                        }else {
                        	$_Item['lotes']=array($item['lote']);
                        }
                        $_SESSION['AdjustmentCart']['BPC'][$_2Item['stockid'].'-'.$item['lote']]=$item;
                    }
                }
            }

            if (!empty($_SESSION['AdjustmentCart']) && $_POST['Commit'] == 'Procesar') {
                if (!isset($_SESSION['UserID'])) $_SESSION['UserID'] = 'SYSTEM';
                $SQL = "INSERT INTO rh_stockmoves (
                                    userid,
                                    loccode,
                                    type,
                                    trandate,
                                    description)
                            VALUES (
                                    '" . $_SESSION['UserID'] . "',
                                    '" . $Almacen . "',
                                    '" . $Type . "',
                                    '" . date('Y-m-d') . "',
                                    '" . $_POST['Comentarios'] . "'
                            )";
                $ErrMsg = _('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('The stock movement record cannot be inserted because');
                $DbgMsg = _('The following SQL to insert the stock movement record was used');

                //                  $Result = DB_query($SQL, $db, $ErrMsg, $DbgMsg, true);
                //                  $rh_stockmoves_id = DB_Last_Insert_ID($db, 'rh_stockmoves', 'id');
                $InputError = false;
                $TotalDelDia=count($_SESSION['AdjustmentCart']['BPC']);
                foreach ($_SESSION['AdjustmentCart']['BPC'] as $_Item) if (!$InputError) {
                    //$Comentarios = $_Item['Coments'];
                    $Comentarios = $_Item['reference'];
                    $Almacen = $Almacen;
                    $StockID = $_Item['StockID'];
                    $Comentarios2 = $_Item['Comentarios2'];
                    $description = $_Item["description"];
                    $Type = $_Item["Type"];
                    $lotes=$_Item['lotes'];
                    if(!is_array($lotes))
                    	$lotes=array();

                    $Ajuste = ($_Item['QTY']) * -1;

                    $SQL = "SELECT count(*)T FROM stockmoves
                                            WHERE type='" . DB_escape_string($Type) . "'
                                            AND loccode='" . DB_escape_string($Almacen) . "'
                                            AND reference='" . DB_escape_string($Comentarios) . "'
                                            AND narrative LIKE '%::.[" . $_Item['CodigoBarras'] . "]%'";
                    $CheckNegResult = DB_query($SQL, $db);
                    $CheckNegRow = DB_fetch_array($CheckNegResult);

                    IF ($CheckNegRow[0] == 0) {
                        $SQL = "SELECT quantity FROM locstock
                                            WHERE stockid='" . DB_escape_string($StockID) . "'
                                            AND loccode='" . DB_escape_string($Almacen) . "'";
                        $CheckNegResult = DB_query($SQL, $db);
                        $CheckNegRow = DB_fetch_array($CheckNegResult);
                        $StockNegativo =$_SESSION['ProhibitNegativeStock'];
                        $_SESSION['ProhibitNegativeStock']=0;
                        if ($_SESSION['ProhibitNegativeStock'] == 1 && $CheckNegRow['quantity'] + $Ajuste < 0) {
                            $InputError = true;
                            Yii::app()->user->setFlash("error", 'ITEM ' . $StockID . ' : Se establecieron parámetros del sistema para prohibir stocks negativos. El procesamiento de este Ajuste resultaría en stock negativo. Este ajuste NO será procesado.');
                        }
                        if (!$InputError) {
                            $_POST['rh_orderline'] = $_Item["MedicamentoId"];
                            $rh_stockmoves_id++;
                            $this->StockAdjustments($StockID, $Almacen, $Ajuste, $Comentarios, $Type, $rh_stockmoves_id, $Comentarios2, $description, $lotes);
                        }
                        $_SESSION['ProhibitNegativeStock']=$StockNegativo;
                        
                    }
                }
                if (!$InputError) 
                	Yii::app()->user->setFlash("success", sprintf(_("Los Ajustes se Realizaron Correctamente; Numero de movimientos %s") , $rh_stockmoves_id.'/'.$TotalDelDia));

                unset($_SESSION['AdjustmentCart']['BPC']);
            }
            if (isset($_POST['Search'])) {
                $ItemsData = $this->SearchItems($_POST['StockCat'], $_POST['Keywords'], $_POST['StockCode']);
            }
        }
        $CatList = Controller::GetStockCatList();
        if (empty($CatList)) {
            $CatList = array();
        }

        if (empty($ItemsData)) {
            $ItemsData = array();
        }

        $rh_query = "SELECT description FROM stockmaster";
        $ListDesc = DB_query($rh_query, $db);
        while ($MyItem = DB_fetch_assoc($ListDesc)) {
            $MyItem = preg_replace("/[\n|\r|\n\r]/i", "", $MyItem);
            $ListDescriptions[] = addslashes($MyItem['description']);
        }
        $this->render('bajaporconsumo', array(
            'ItemsData' => $ItemsData,
            'CatList' => $CatList,
            'ListDescriptions' => $ListDescriptions
        ));
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
                prnMsg(_('Stock description keywords have been used in preference to the Stock code extract entered') , 'info');
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

    public function GetSeries($stockid){
    	global $db;
    	$series=false;
    	 $SearchByDescription = "SELECT serialno FROM stockserialitems WHERE quantity<>0 and stockid = '" . $stockid . "' group by serialno";
    	 $result = DB_query($SearchByDescription, $db);
         if($myrow = DB_fetch_assoc($result)){
         	$series=array();
         	do
         	{
         		$series[]=$myrow;
         	}
         	while($myrow = DB_fetch_assoc($result));
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
                    $SearchByDescription = "SELECT * FROM stockmaster WHERE description = '" . $SearchKey . "'";
                    $result = DB_query($SearchByDescription, $db);
                    $myrow = DB_fetch_assoc($result);

                    if (!empty($myrow)) {
                        $item=array(
                            'desc' => $myrow['description'],
                            'stockid' => $myrow['stockid'],
                            'Fila' => $_REQUEST['Fila']
                        );
                        $serie=$this->GetSeries($myrow['stockid']);
                        if($serie){
                        	$item['series']=$serie;
                        }
                        $Listado[] = $item;
                    }

                    if (empty($myrow)) {
                        $SearchKey = explode(" ", str_replace(array(
                            "\t",
                            "\n",
                            " "
                        ) , " ", $SearchKey));
                        $SearchKey = implode("','", $SearchKey);
                        $SearchByBarCode = "SELECT * FROM stockmaster WHERE barcode in('" . $SearchKey . "')";
                        $_resulta2 = DB_query($SearchByBarCode, $db);

                        while ($myrow2 = DB_fetch_assoc($_resulta2)) {
                        	$item= array(
                                'desc' => $myrow2['description'],
                                'stockid' => $myrow2['stockid'],
                                'Fila' => $_REQUEST['Fila']
                            );
	                        $serie=$this->GetSeries($myrow['stockid']);
	                        if($serie){
	                        	$item['series']=$serie;
	                        }

                            $Listado[] =$item;
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

    public function StockAdjustments($StockID, $Almacen, $Ajuste, $Referencia, $Type, $rh_stockmoves_id, $narrative, $description = '',$lotes=array()) {

        global $db;
        ini_set('display_errors', 1);error_reporting (1);
        $ExternalCall = true;
        ob_start();
        ob_start();
        $SQL = "select * from stockmaster where stockid='" . $StockID . "'";
        $result = DB_query($SQL, $db, $ErrMsg);
		$fila=DB_fetch_assoc($result);

        $_GET['NewAdjustment'] = 1;
        $_GET['StockID'] = $StockID;
        $Narrative = $Referencia;
        $_POST['Quantity'] = $Ajuste;
        $_POST['Comentarios2'] = $narrative;
        $_POST['description'] = $fila['description'].'::'.$description;
        
        $_SESSION['UserStockLocation']=
			$_POST['StockLocation']=
            $_POST["StockLocation"] = $Almacen;

        if ($_POST['Quantity'] != 0) {
            include ($_SERVER['LocalERP_path'] . '/StockAdjustments.php');
            if (count($lotes)>0) {
            	foreach ($lotes as $NewSerialNo){
            		$_SESSION['Adjustment']->SerialItems[$NewSerialNo] = new SerialItem ($NewSerialNo, $_POST['Quantity'], '');
            	}
            }else $_REQUEST['SoloExistenciaLote']=$_SESSION['Adjustment']->SoloExistenciaLote=2;
			$_SESSION ['Adjustment']->Comentarios2=$narrative;
            unset($_GET['StockID'], $_GET['NewAdjustment']);
            $_POST["CheckCode"] = "Check Part";
            $_POST["Narrative"] = $Narrative;
            $_POST["movePedimento"] = 1;
            $_POST['password'] = $passwordAdjustment;
            $_POST['EnterAdjustment'] = 1;
            
            $_SESSION['Adjustment']->StockLocation=
            $_SESSION['UserStockLocation']=
			$_POST['StockLocation']=
            $_POST["StockLocation"] = $Almacen;
            $_POST['StockID'] = $StockID;

            $succ = include ($_SERVER['LocalERP_path'] . '/StockAdjustments.php'); {
                $Mensaje = ob_get_contents();
            }
            $_POST = array();
        }

        //ob_clean();
        ob_end_clean();
        ob_end_clean();
        return;
    }

    public function actionReportebajas() {
        global $db;

        if (!empty($_POST['StartDate'])) {
            $StartDate = $_POST['StartDate'];
        } else {
            $StartDate = date('Y-m-d', mktime(0, 0, 0, date("m") , date("d") - 30, date("Y")));
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
            AND stockmoves.trandate BETWEEN '" . $StartDate . "' AND '" . $EndDate . "' ";
        FB::INFO($SQL, '___________________SQL');
        $result = DB_query($SQL, $db, $ErrMsg);
        $ListMovesData = array();
        while ($MyMoves = DB_fetch_assoc($result)) {
            $ListMovesData[] = ($MyMoves);
        }
        $this->render('reportebajas', array(
            'ListMovesData' => $ListMovesData
        ));
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

        if (!empty($_POST['Search']['Category1']) && !empty($_POST['Search']['Category2'])) {

            if ($_POST['Search']['LockStock'] == "All") {
                $LockStock = " ";
            } else {
                $LockStock = " AND locstock.loccode = '{$_POST['Search']['LockStock']}' ";
            }

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
                stockserialitems.quantity *(stockmaster.materialcost + stockmaster.labourcost + stockmaster.overheadcost) AS itemtotal
            FROM stockmaster
            LEFT JOIN stockcategory on stockmaster.categoryid=stockcategory.categoryid
            LEFT JOIN locstock on stockmaster.stockid = locstock.stockid
            LEFT JOIN stockserialitems on stockmaster.stockid=stockserialitems.stockid and locstock.loccode=stockserialitems.loccode
            LEFT JOIN rh_stock_grupo on stockmaster.id_agrupador = rh_stock_grupo.clave
            WHERE stockmaster.categoryid=stockcategory.categoryid
            AND stockmaster.categoryid >= '" . $_POST['Search']['Category1'] . "'
            AND stockmaster.categoryid <= '" . $_POST['Search']['Category2'] . "'
            AND stockserialitems.quantity!=0
            {$LockStock}
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

        $this->render('reporteinventario', array(
            'ListMovesData' => $ListMovesData,
            'LockStock' => $LockStock
        ));
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
                $pdf->Cell(0, 0, utf8_decode('Próxima cita: ') , 0, 0, "L");
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
            $pdf->Cell(0, 0, utf8_decode("Descripción") , 0, 0, "L");
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
            $pdf->Cell(0, 0, utf8_decode(' Firma médico tratante') , 0, 0, "L");

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
}
?>