<?php

$PageSecurity = 4;

include('includes/DefinePOClass.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking
 * and authorisation level check
 */
include_once('includes/session.inc');
$title = _('Purchase Order Items');

$identifier=$_GET['reqno'];

if (!isset($identifier)){
    header('Location:' . $rootpath . '/rh_REQ_Header.php?' . SID);
    exit;
}

include_once('includes/header.inc');



/* SELECT 
          rh_requisicion.reqno,
          rh_requisicion.initiator,
          rh_requisicion.reqdate,
          rh_requisicion.status,
          rh_requisicion.intostocklocation,
          rh_requisicion.reqdate,
          rh_requisiciondetalle.itemcode,
          rh_requisiciondetalle.itemdescription,
          rh_requisiciondetalle.quantityord,
          rh_requisiciondetalle.uom,
          rh_requisiciondetalle.unitprice 
FROM rh_requisicion 
LEFT JOIN rh_requisiciondetalle ON rh_requisicion.reqno = rh_requisiciondetalle.reqno 
WHERE rh_requisicion.reqno=6;



*/
if (isset($_POST['Commit'])){ //Insert Req Detalle
        unset($_POST['Commit']);
        /*
        echo "<pre>";
        print_r($_POST);
        echo "<pre>";
        */
    foreach($_POST as $Items){
        $SQLInsertItems = "INSERT INTO rh_requisiciondetalle (
                                        reqno, 
                                        itemcode, 
                                        itemdescription,
                                        quantityord,
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
        unset($_SESSION['REQ'.$identifier]);
    }
}

if (isset($_POST['Search'])){  /*ie seach for stock items */

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

if ( isset($_POST['EnterLine'])){ //Add NonStock Item
    /*
        echo "<pre>";
            print_r($_POST);
        echo "<pre>";
    */
    if ($_POST['Qty']>0) {
        $_SESSION['REQ' . $identifier]->LineItems[] = array(
            'NonStock',
            $_POST['ItemDescription'],
            $_POST['Qty'],
            $_POST['Units'],
            $_POST['Price'],
            $_POST['GLCode'],
            'NonStock' => 'NonStock'
        );
    }
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
} /* end of if its a new item */
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
                            <th>Precio</th>
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
                            <td><input type="text" value="<?=$ReqItems[2]?>" name="<?=$ReqItems[0]?>[]" style="width: 100px;" />
                                <input type="hidden" value="<?=$ReqItems[5]?>" name="<?=$ReqItems[0]?>[]" />
                            </td>
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
                                <td></td>
                                <td><font size="3"><b>Ó </b></font><a "="" href="<?php echo $rootpath.'/Stocks.php?"' . SID; ?>" target="_blank">Create a New Stock Item</a></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td><input type="submit" value="Buscar" name="Search"><input type="submit" value="Order a non stock item" name="NonStockOrder"></td>
                                <td></td>
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
