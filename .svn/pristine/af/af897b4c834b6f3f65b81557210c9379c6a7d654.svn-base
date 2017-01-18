<?php


$PageSecurity = 4;

include('includes/DefinePOClass.php');
include('includes/SQL_CommonFunctions.inc');

/* Session started in header.inc for password checking
 * and authorisation level check
 */
include_once('includes/session.inc');
$title = _('Purchase Order Items');
include_once('includes/header.inc');
?>

<script type="text/javascript">
    $(document).on('ready',function() {
        $("#StockLocation").select2();
    });
</script>

<?php

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




if (isset($_POST['SearchReqs'])){
    
    //echo "<pre>";
        //print_r($_POST);
    //echo "<pre>";
    
    if(isset($_POST['reqno'])){
        $Where = "";
        $Where .= "WHERE rh_requisicion.reqno=" . $_POST['reqno'];
        if(isset($_POST['StockLocation'])){
            $Where .=" AND rh_requisicion.intostocklocation = '". $_POST['StockLocation'] . "'"; 
        }
        
    }else{
        $Where = "";
        $Where .= " WHERE rh_requisicion.intostocklocation = '". $_POST['StockLocation'] . "'";
    }
    $SQL = "SELECT 
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
    {$Where}";
    $SearchResult = DB_query($SQL,$db);
    while ($Reqs = DB_fetch_assoc($SearchResult)) {
        $ReqData[] = $Reqs;
    }
    
    echo "<pre>";
        print_r($ReqData);
    echo "</pre>";
    
    
} 





?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="container-fluid no-outter-border" style="max-width: 50%; margin-left: 25%;">
                
                <form enctype="multipart/form-data" method="post" action="<?php echo $_SERVER['PHP_SELF'] . "?" . SID . "reqno=".$identifier; ?>" name="form1">
                    <input type="hidden" value="<?=$_SESSION['FormID']?>" name="FormID">
                        <?php
                        {
                            echo '<table class=selection>
                                <tr>
                                    <td>';
                                        if (isset($SelectedStockItem)) {
                                            echo _('For the part') . ':<b>' . $SelectedStockItem . '</b> ' . _('and') . ' <input type=hidden name="SelectedStockItem" value="' . $SelectedStockItem . '">';
                                        }
                                        echo _('N° Requisicion') . ': <input type=text name="OrderNumber" maxlength=8 size=9> ' . _('En el Almacén') . ':
                                        <select name="StockLocation" id="StockLocation"> ';
                                        $sql = "SELECT loccode, locationname FROM locations";
                                        $resultStkLocs = DB_query($sql, $db);
                                        while ($myrow = DB_fetch_array($resultStkLocs)) {
                                            if (isset($_POST['StockLocation'])) {
                                                if ($myrow['loccode'] == $_POST['StockLocation']) {
                                                    echo '<option selected Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
                                                } else {
                                                    echo '<option Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
                                                }
                                            } elseif ($myrow['loccode'] == $_SESSION['UserStockLocation']) {
                                                echo '<option selected Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
                                            } else {
                                                echo '<option Value="' . $myrow['loccode'] . '">' . $myrow['locationname'];
                                            }
                                        }
                                        echo '</select>
                                        <input type=submit name="SearchReqs" value="' . _('Buscar Requisuiciones') . '">
                                    </td>
                                </tr>
                            </table>';
                        } ?>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                        

                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once('includes/footer.inc');
?>
