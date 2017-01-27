<?php

//$PageSecurity = 4;

/**************************************************************************
* Ruben Flores Barrios 19/Dic/2016
* Archivo creado para aceptar una req y hacer la orden de compra
***************************************************************************/

include('includes/DefinePOClass.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking
 * and authorisation level check
 */
include_once('includes/session.inc');
$title = _('Articulos en la requisicion');

include_once('includes/header.inc');

// consulta para verificar que el usuario existe como solicitante
$usuario=$_SESSION['UserID'];
$sqls_usuario = 'SELECT * FROM wrk_solicitantecc where solicitantecc_edo = 1 and solicitantecc="'.$usuario.'"';
$resultusuario = DB_query($sqls_usuario,$db);
    if(DB_num_rows($resultusuario)==0)
    {
        prnMsg(_('No se encontro el usuario'),'error');
        $insert = false;    
    }
$rowusuario = DB_fetch_array($resultusuario);

if(DB_num_rows($resultusuario)==0)
{
    // no existe en la tabla de solicitante, mostramos pantalla no autorizada
    prnMsg(_('No existe como solicitante'),'error');
    include ('includes/footer.inc');
    exit ();
}
// obtenemos el id de la requisicion
$reqid = $_GET['reqid'];
if($reqid==''){ $reqid=$_GET['reqno']; }
$identifier = $reqid;

if(empty($reqid))
{
        // no existe en la tabla de solicitante, mostramos pantalla no autorizada
        prnMsg(_('La requisicion no existe, favor de intentarlo de nuevo '),'error');
        include ('includes/footer.inc');
        exit ();
}




    $sqls_requisicion = 'SELECT * FROM wrk_requisicion where status="Nueva" and reqid='.$reqid;
    $resultrequisicion = DB_query($sqls_requisicion,$db);
    if(DB_num_rows($resultrequisicion)==0)
    {
        // no existe en la tabla de solicitante, mostramos pantalla no autorizada
        prnMsg(_('La requisicion no existe como nueva, favor de intentarlo de nuevo '),'error');
        include ('includes/footer.inc');
        exit ();
    }

    $rowdatosreq = DB_fetch_array($resultrequisicion);
/* SELECT 
          rh_requisicion.reqno,
          rh_requisicion.initiator,
          rh_requisicion.reqdate,
          rh_requisicion.status,
          rh_requisicion.intostocklocation,
          rh_requisicion.reqdate,
          wrk_requisiciondetalle.itemcode,
          wrk_requisiciondetalle.itemdescription,
          wrk_requisiciondetalle.quantityord,
          wrk_requisiciondetalle.uom,
          wrk_requisiciondetalle.unitprice 
FROM rh_requisicion 
LEFT JOIN wrk_requisiciondetalle ON rh_requisicion.reqno = wrk_requisiciondetalle.reqno 
WHERE rh_requisicion.reqno=6;

*/

//***********************Carga de archivo***************************************
if(isset($_FILES['im'])){
$flag=false;
if(@mkdir('importfiles',0777)){
    $flag=true;
} else if (chdir('importfiles')){
    $flag=true;
}else{
   prnMsg(_('No se tienen permisos de escritura, asegurece de tener permisos suficientes'), 'error');
}
if($flag){
    $p=@chdir('importfiles');
    $path=realpath($p);
    $path = str_replace('\\','/',$path);
}
if(($flag)){
$destino = '/importfiles' ;
$tamano = $_FILES['im']['size'];
$tipo = $_FILES["im"]["type"];
if(isset($_FILES['im'])&&$_FILES['im']['error']<>4&&($tamano < 3000000)//&&(($tipo=="text/csv")||($tipo=="text/text")||($tipo=="text/plain")||($tipo=="application/vnd.ms-excel"))
){
    if (!move_uploaded_file($_FILES['im']['tmp_name'],$path.'/'.$_FILES['im']['name'])){
     prnMsg(_('No se pudo cargar el archivo'), 'error');
    }else{
        $filename = $path.'/'.$_FILES['im']['name'];
        if(@$fh_in = fopen($filename,"r")){
            $separador=",";
            $ListadoIDAgrupador=array();
            while(($line = fgetcsv($fh_in,0,$separador))!==false){
                if($line!= null){
                    if(count($line)==1){
                        if(strpos($line[0],'|'))$separador='|';
                        else
                        if(strpos($line[0],';'))$separador=';';
                        $line=explode($separador,$line[0]);
                        
                    }
                    $size = sizeof($line);
                    //for($i=0;$i<$size;$i++){
                    $ItemCode=$line[0];
                    $Quantity=$line[1];
                    //**********************************************************
                    if ($_SESSION['PO_AllowSameItemMultipleTimes'] ==false){
                        if (count($_SESSION['REQ'.$identifier]->LineItems)!=0){
                            foreach ($_SESSION['REQ'.$identifier]->LineItems AS $OrderItem) {
                                if (($OrderItem->StockID == $ItemCode) and ($OrderItem->Deleted==false)) {
                                    $AlreadyOnThisOrder = 1;
                                    prnMsg( _('The item') . ' ' . $ItemCode . ' ' . _('is already on this order') . '. ' . _('The system will not allow the same item on the order more than once') . '. ' . _('However you can change the quantity ordered of the existing line if necessary'),'error');
                                }
                            }
                        }
                    }
                    if ($AlreadyOnThisOrder!=1 and $Quantity>0){
                        
                            if($ItemCode!=''){
                            $sql="SELECT stockmaster.description,
                                        stockmaster.stockid,
                                        stockmaster.units,
                                        stockmaster.decimalplaces,
                                        stockmaster.kgs,
                                        stockmaster.netweight,
                                        stockcategory.stockact,
                                        chartmaster.accountname
                                    FROM stockcategory,
                                        chartmaster,
                                        stockmaster
                                    WHERE chartmaster.accountcode = stockcategory.stockact
                                        AND stockcategory.categoryid = stockmaster.categoryid
                                        AND stockmaster.stockid = '". $ItemCode . "'";
                            }
                                 
                            $ErrMsg = _('The supplier pricing details for') . ' ' . $ItemCode . ' ' . _('could not be retrieved because');
                            $DbgMsg = _('The SQL used to retrieve the pricing details but failed was');
                            $result1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);
                            if ($myrow = DB_fetch_array($result1)){
                                
                                $_SESSION['REQ'.$identifier]->LineItems[$ItemCode] = array($ItemCode,$Quantity);
                            }
                        
                        }
                    }
                }
    }
    fclose($fh_in);
   

    }
 }
}else{
     prnMsg(_('>No se puede escribir en el directorio indicado'), 'error');
    include ('includes/footer.inc');
    exit;
}
}
//******************************************************************************

    

if (isset($_POST['Commit'])){ //Insert Req Detalle
        unset($_POST['Commit']);
        /*
        echo "<pre>";
        print_r($_POST);
        echo "<pre>";
        */
    foreach($_POST as $Items){
        $SQLInsertItems = "INSERT INTO wrk_requisiciondetalle (
                                        reqno, 
                                        itemcode, 
                                        itemdescription,
                                        quantityreq,
                                        uom,
                                        unitprice,
                                        glcode)
                                VALUES ('".$identifier."', 
                                        '".$Items[0]."', 
                                        '".$Items[1]."', 
                                        '".$Items[2]."',
                                        '".$Items[3]."',   
                                        '".$Items[4]."',
                                        '".$Items[5]."')";
        $result = DB_query($SQLInsertItems,$db);
    }
        //actualizamos el estatus de la requisicion a "en proceso"
        unset($_SESSION['REQ'.$identifier]);

        // actualizamos el estatus
        $sqlinsert = "UPDATE wrk_requisicion
                    SET 
                        status='Pending'
                    WHERE reqid = ".$identifier ; 

        $ErrMsg = _('El estatus') . '  ' . _('no pudo ser modificado');
        $DbgMsg = _('Se intento; insertar el nuevo estatus pero hubo un fallo inesperado');
        //echo $sql;
        $result = DB_query($sqlinsert, $db, $ErrMsg, $DbgMsg);

        prnMsg(_('Articulos agregados con exito a la Requsicion #'.$identifier),'success');
        include ('includes/footer.inc');
        exit;
    
}

if (isset($_POST['Search'])){  /*ie search for stock items */

    if ($_POST['Keywords'] AND $_POST['StockCode']) {
        prnMsg( _('Stock description keywords have been used in preference to the Stock code extract entered'), 'info' );
    }
    if ($_POST['Keywords']) {
        //insert wildcard characters in spaces
        $SearchString = '%' . str_replace(' ', '%', $_POST['Keywords']) . '%';

        if ($_POST['StockCat']=='All'){
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                AND stockmaster.description LIKE '" . $SearchString ."'
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        } else {
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                AND stockmaster.description LIKE '". $SearchString ."'
                AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        }

    } elseif ($_POST['StockCode']){

        $_POST['StockCode'] = '%' . $_POST['StockCode'] . '%';

        if ($_POST['StockCat']=='All'){
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                AND stockmaster.stockid LIKE '" . $_POST['StockCode'] . "'
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        } else {
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                AND stockmaster.stockid LIKE '" . $_POST['StockCode'] . "'
                AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        }

    } else {
        if ($_POST['StockCat']=='All'){
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        } else {
            $sql = "SELECT stockmaster.stockid,
                    stockmaster.description,
                    stockmaster.units
                FROM stockmaster INNER JOIN stockcategory
                ON stockmaster.categoryid=stockcategory.categoryid
                WHERE stockmaster.mbflag!='D'
                AND stockmaster.mbflag!='A'
                AND stockmaster.mbflag!='K'
                and stockmaster.discontinued!=1
                AND stockmaster.categoryid='" . $_POST['StockCat'] . "'
                ORDER BY stockmaster.stockid
                LIMIT ".$_SESSION['DefaultDisplayRecordsMax'];
        }
    }

    $ErrMsg = _('There is a problem selecting the part records to display because');
    $DbgMsg = _('The SQL statement that failed was');
    $SearchResult = DB_query($sql,$db,$ErrMsg,$DbgMsg);

    if (DB_num_rows($SearchResult)==0 && $debug==1){
        prnMsg( _('There are no products to display matching the criteria provided'),'warn');
    }
    if (DB_num_rows($SearchResult)==1){

        $myrow=DB_fetch_array($SearchResult);
        $_GET['NewItem'] = $myrow['stockid'];
        DB_data_seek($SearchResult,0);
    }

} //end of if search REQ



if($_GET['Option']=="Delete"){ //Delete Item
    /*echo "<pre>";
            print_r($_SESSION['REQ'.$identifier]->LineItems);
    echo "<pre>";*/
    $StockID = $_GET['stockid'];
    unset($_SESSION['REQ'.$identifier]->LineItems[$StockID]);
}


if (isset($_POST['NewItem'])){ // Add Items
        
    /*
    echo "<pre>";
        print_r($_POST);
    echo "<pre>";
    */
    
    $_SESSION['REQ'.$identifier]->OrderNo = $identifier;
    foreach ($_POST as $key => $value) {
        if (substr($key, 0, 3)=='qty') {
            $ItemCode=substr($key, 3, strlen($key)-3);
            $Quantity=$value;
            $AlreadyOnThisOrder =0;
        }
        if ($Quantity > 0 ){
            $_SESSION['REQ'.$identifier]->LineItems[$ItemCode] = array($ItemCode,$Quantity);
        }
    }
} 

/* end of if its a new item */
?>

<?php 

    echo "


    <CENTER>
    <span id='datagrid'>
    <form method='POST'>
    </form>
    <TABLE width='' id='' class='table table-bordered table-striped table-hover table-condensed'>
    <thead>
        <TR>
            <TD CLASS='tableheader' width='8%'>
                # Requisicion<br>
                ".$rowdatosreq['reqid']."
            </TD>
            <TD CLASS='tableheader' width='7%'>    
                Desarrollado Por:<br>
                ".$rowdatosreq['initiator']."
            </TD>
            <TD CLASS='tableheader' width='8%'>
                Fecha Req<br>
                ".$rowdatosreq['reqdate']."
            </TD>
            <TD CLASS='tableheader' width='7%'>
                Estatus<br>
                ".$rowdatosreq['status']."
            </TD>
        </tr>
        <tr>
            <TD CLASS='tableheader' width='8%'>
                # Almacen<br>
                ".$rowdatosreq['intostocklocation']."
            </TD>
            <TD CLASS='tableheader' width='7%'>
                Calle<br>
                ".$rowdatosreq['deladd1']."
            </TD>
            <TD CLASS='tableheader' width='8%'>
                Numero<br>
                ".$rowdatosreq['deladd2']."
            </TD>
            <TD CLASS='tableheader' width='7%'>
                Colonia<br>
                ".$rowdatosreq['deladd4']."
            </TD>
        </tr>
        <tr>
            <TD CLASS='tableheader' width='8%'>
                Municipio<br>
                ".$rowdatosreq['deladd7']."
            </TD>
            <TD CLASS='tableheader' width='7%'>    
                Estado<br>
                ".$rowdatosreq['deladd8']."
            </TD>
            <TD CLASS='tableheader' width='8%'>
                Pais<br>
                ".$rowdatosreq['deladd9']."
            </TD>
            <TD CLASS='tableheader' width='7%'>    
                Codigo Postal<br>
                ".$rowdatosreq['deladd10']."
            </TD>
        </tr>
        <tr>
            <TD CLASS='tableheader' width='8%' colspan='4'>
                Comentarios<br>
                ".$rowdatosreq['comments']."
            </TD>
        </tr>
    </thead>
    </TABLE>
    </span>
    </CENTER>

    ";

 ?>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            
        <?php
        if (count($_SESSION['REQ'.$identifier]->LineItems)>0){
        
            if (isset($_SESSION['REQ'.$identifier]->OrderNo)) {
                echo  '<label>' . _('Requisición N°') .' '. $_SESSION['REQ'.$identifier]->OrderNo .'</label>';
            }
            ?>
            <label>Detalle de la Requisición</label>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "?" . SID . "reqno=".$identifier; ?>" name="form1">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    <?php
                    foreach ($_SESSION['REQ'.$identifier]->LineItems as $StockID => $ReqItems) {
                        
                        if($ReqItems['NonStock'] == 'NonStock'){
                            $ReqItems[0] = 'NonStock';
                            $ItemDesc['description'] = $ReqItems[1];
                            $ReqItems[1] = $ReqItems[2];
                            $ItemDesc['units'] = $ReqItems[3];
                            $ReqItems[2] = $ReqItems[4];
                        }else{
                        $SQLDesc = "select description,units from stockmaster where stockid ='" . $ReqItems[0] . "'";
                        $ItemDesc = DB_query($SQLDesc,$db);
                        $ItemDesc = DB_fetch_assoc($ItemDesc);
                    }
                        ?>
                        <tr>
                            <td><?=$ReqItems[0]?><input name="<?=$ReqItems[0]?>[]" type="hidden" value="<?=$ReqItems[0]?>"></td>
                            <td><?=$ItemDesc['description']?><input name="<?=$ReqItems[0]?>[]" type="hidden" value="<?=$ItemDesc['description']?>"></td>
                            <td><input type="text" value="<?=$ReqItems[1]?>" name="<?=$ReqItems[0]?>[]" style="width: 100px;" /></td>
                            <td><?=$ItemDesc['units']?><input name="<?=$ReqItems[0]?>[]" type="hidden" value="<?=$ItemDesc['units']?>"></td>
                            <td><a href="<?php echo $_SERVER['PHP_SELF'] . "?" . SID . "reqno=".$identifier . "&Option=Delete&stockid=" . $StockID; ?>"><input name="<?=$ReqItems[0]?>[]" type="button" value="Eliminar" ></a></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <input type="submit" name="Commit" value="Procesar Requisición"></div>
            </form>
            <?php } ?>
            
            <div class="container-fluid no-outter-border" style="max-width: 50%; margin-left: 25%;">
                <?php
                if (isset($_POST['NonStockOrder'])) {
                    echo "<form method='POST' nome='form0'";
                    echo '<br><table class=table><tr><td>'._('Descripción').'</td>';
                    echo '<td><input type=text name=ItemDescription size=40></td></tr>';
                    echo '<tr><td>'._('Cuenta').'</td>';
                    echo '<td><select name="GLCode">';
                    $sql="SELECT
                            accountcode,
                            accountname
                          FROM chartmaster
                          ORDER BY accountcode ASC";
                    $result=DB_query($sql, $db);
                    while ($myrow=DB_fetch_array($result)) {
                        echo '<option value="'.$myrow['accountcode'].'">'.$myrow['accountcode'].' - '.$myrow['accountname'].'</option>';
                    }
                    echo '</td></tr>';
                    echo '<tr><td>'._('Cantidad').'</td>';
                    echo '<td><input type=text class=number name=Qty size=10></td></tr>';
                    
                    echo '<tr><td>' . _('Unidad de Medida') . ':</td>
                    <td><SELECT ' . (in_array('Description',$Errors) ?  'class="selecterror"' : '' ) .'  name="Units">';
                    $sql = 'SELECT unitname FROM unitsofmeasure ORDER by unitname';
                    $UOMResult = DB_query($sql,$db);
                    while( $UOMrow = DB_fetch_array($UOMResult) ) {
                         if (!isset($_POST['Units']) or $_POST['Units']==$UOMrow['unitname']){
                            echo "<OPTION SELECTED Value='" . $UOMrow['unitname'] . "'>" . $UOMrow['unitname'];
                         } else {
                            echo "<OPTION Value='" . $UOMrow['unitname'] . "'>" . $UOMrow['unitname'];
                         }
                    }
                    echo '</select></td></tr>';
                    
                    echo '<tr><td>'._('Precio').'</td>';
                    echo '<td><input type=text class=number name=Price size=10></td></tr>';
                    echo '</table>';
                    echo '<div class=centre><input type=submit name="EnterLine" value="Enter Item"></div>'; 
                    echo "</form>";
                }
                ?>
                
                <CENTER><H1>Productos de las Requisiciones</H1></CENTER>
                <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?" . SID . "reqno=".$identifier; ?>" name="form1">
                    <input type="hidden" value="<?=$_SESSION['FormID']?>" name="FormID">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th colspan="3"><font size="3" color="blue">Buscar Productos de Existencias</font></th>
                            </tr>
                            <tr>
                                <?php
                                $sql="SELECT categoryid,
                                        categorydescription
                                    FROM stockcategory
                                    WHERE stocktype<>'L'
                                    AND stocktype<>'D'
                                    ORDER BY categorydescription";
                                $ErrMsg = _('The supplier category details could not be retrieved because');
                                $DbgMsg = _('The SQL used to retrieve the category details but failed was');
                                $result1 = DB_query($sql,$db,$ErrMsg,$DbgMsg);
                                
                                ?>
                                <td><select name="StockCat">
                                    <option value="All" selected="">Todos</option>
                                    <?php
                                        while ($myrow1 = DB_fetch_array($result1)) {
                                            if (isset($_POST['StockCat']) and $_POST['StockCat']==$myrow1['categoryid']){
                                                echo "<option selected value=". $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
                                            } else {
                                                echo "<option value=". $myrow1['categoryid'] . '>' . $myrow1['categorydescription'];
                                            }
                                        }
                                    ?>
                                    </select>
                                </td>
                                <td>Ingrese extracto del texto de la descripción:</td>
                                <td><input type="text" value="" maxlength="25" size="20" name="Keywords"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><font size="3"><b>Ó </b></font>Ingrese extracto del código de Existencias:</td>
                                <td><input type="text" value="" maxlength="18" size="15" name="StockCode"></td>
                            </tr>
                           
                            <tr>
                                <TD>Archivo a Importar:</td>
                                <TD> <input type='file' name='im' /></TD>
                                <td>stockid|Cantidad</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><input type="submit" value="Buscar" name="Search"></td>
                                <td><input type=submit name='loadFile' value='Cargar archivo'></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                
                <div style="height: 20px;"></div>
                <?php if (isset($SearchResult)) { ?>
                <!-- <form method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?" . SID . "reqno=".$identifier; ?>" name="form2"> -->
                <table class="_table">
                    <thead>
                        <tr>
                            <th>Codigo</th>
                            <th>Descripción</th>
                            <th>Unidades</th>
                            <th colspan="2"><a href='#end'>ir al final</a></th>
                        </tr>
                    </thead>
                    <tbody>
                
                    <?php
                    $j = 1;
                    $k=0; //row colour counter
                    while ($myrow=DB_fetch_array($SearchResult)) {
                        if ($k==1){
                            echo '<tr class="EvenTableRows">';
                            $k=0;
                        } else {
                            echo '<tr class="OddTableRows">';
                            $k=1;
                        }
                        
                        $filename = $myrow['stockid'] . '.jpg';
                        if (file_exists( $_SESSION['part_pics_dir'] . '/' . $filename) ) {
                            $ImageSource = '<img src="'.$rootpath . '/' . $_SESSION['part_pics_dir'] . '/' . $myrow['stockid'] .
                                '.jpg" width="50" height="50">';
                        } else {
                            $ImageSource = '<i>'._('No Image').'</i>';
                        }
                            $uomsql="SELECT conversionfactor,
                                        suppliersuom,
                                        unitsofmeasure.unitname
                                    FROM purchdata
                                    LEFT JOIN unitsofmeasure
                                    ON purchdata.suppliersuom=unitsofmeasure.unitid
                                    WHERE supplierno='".$_SESSION['PO'.$identifier]->SupplierID."'
                                    AND stockid='".$myrow['stockid']."'";
                
                            $uomresult=DB_query($uomsql, $db);
                            if (DB_num_rows($uomresult)>0) {
                                $uomrow=DB_fetch_array($uomresult);
                                if (strlen($uomrow['suppliersuom'])>0) {
                                    $uom=$uomrow['unitname'];
                                } else {
                                    $uom=$myrow['units'];
                                }
                            } else {
                                $uom=$myrow['units'];
                            }
                            echo 
                            "<td>".$myrow['stockid']."</td>
                            <td>".$myrow['description']."</td>
                            <td>".$uom."</td>
                            <td>".$ImageSource."</td>
                            <td><input class='number' type='text' size=6 value=0 name='qty".$myrow['stockid']."'></td>
                            <input type='hidden' size=6 value=".$uom." name=uom>
                            </tr>";
                        
                        $PartsDisplayed++;
                        if ($PartsDisplayed == $Maximum_Number_Of_Parts_To_Show){
                            break;
                        }
                #end of page full new headings if
                    }
                #end of while loop
                    echo '</tbody>';
                    echo '</table>';
                    if ($PartsDisplayed == $Maximum_Number_Of_Parts_To_Show){
                
                    /*$Maximum_Number_Of_Parts_To_Show defined in config.php */
                
                        prnMsg( _('Only the first') . ' ' . $Maximum_Number_Of_Parts_To_Show . ' ' . _('can be displayed') . '. ' .
                            _('Please restrict your search to only the parts required'),'info');
                    }
                    echo '<a name="end"></a>
                          <br><div class="centre"><input type="submit" name="NewItem" value="Order some"></div>';
                }#end if SearchResults to show
                
                ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once('includes/footer.inc');
?>