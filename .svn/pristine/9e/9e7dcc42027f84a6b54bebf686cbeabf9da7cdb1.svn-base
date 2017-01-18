<?php

$PageSecurity = 4;
include('includes/DefinePOClass.php');
include('includes/session.inc');

include('includes/SQL_CommonFunctions.inc');

/*
echo "<pre>";
print_r($_SESSION['rh_permitionlocation']);
echo "</pre>"; 
*/


if (isset($_POST['UpdateReq']) && isset($_GET['reqno'])){
        
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
        $SQLUpdate = "UPDATE rh_requisicion SET
                                    initiator = '".$_POST['initiator']."',
                                    reqdate = '".$_POST['reqdate']."',
                                    status = '".$_POST['status']."',
                                    stat_comment = '".$_POST['stat_comment']."',
                                    intostocklocation = '".$_POST['intostocklocation']."',
                                    deladd1 = '".$_POST['deladd1']."',
                                    deladd2 = '".$_POST['deladd2']."',
                                    deladd3 = '".$_POST['deladd3']."',
                                    deladd4 = '".$_POST['deladd4']."',
                                    deladd5 = '".$_POST['deladd5']."',
                                    deladd6 = '".$_POST['deladd6']."',
                                    contact = '".$_POST['contact']."',
                                    tel = '".$_POST['tel']."',
                                    comments = '".$_POST['comments']."'
                                WHERE
                                    reqno = '".$_GET['reqno']."'";
            
            $ErrMsg = _('The requesition order header record could not be inserted into the database because');
            $DbgMsg = _('The SQL statement used to insert the requesition order header record and failed was');
            $result = DB_query($SQLUpdate,$db,$ErrMsg,$DbgMsg,true);
            
    if (isset($_GET['reqno'])){
    header('Location:' . $rootpath . '/rh_REQ_Items.php?reqno=' . SID . $_GET['reqno']);
    exit;
    }
}


if (isset($_POST['SaveReq'])){
    
    $TransNo = GetNextTransNo(20004, $db);
    
    echo "<pre>";
    echo $TransNo;
    print_r($_POST);
    echo "</pre>";
    
    //FormatDateForSQL();
    
    $SQLInsert = "INSERT INTO rh_requisicion (
                                    reqno, 
                                    initiator, 
                                    reqdate,
                                    status,
                                    stat_comment, 
                                    intostocklocation, 
                                    deladd1, 
                                    deladd2, 
                                    deladd3, 
                                    deladd4, 
                                    deladd5, 
                                    deladd6, 
                                    contact, 
                                    tel,
                                    comments)
                            VALUES ('".$TransNo."', 
                                    '".$_POST['initiator']."', 
                                    '".$_POST['reqdate']."', 
                                    '".$_POST['status']."',
                                    '".$_POST['stat_comment']."',   
                                    '".$_POST['intostocklocation']."',  
                                    '".$_POST['deladd1']."',  
                                    '".$_POST['deladd2']."',  
                                    '".$_POST['deladd3']."',  
                                    '".$_POST['deladd4']."',  
                                    '".$_POST['deladd5']."',  
                                    '".$_POST['deladd6']."',  
                                    '".$_POST['contact']."',  
                                    '".$_POST['tel']."',  
                                    '".$_POST['comments']."')";
            
            $ErrMsg = _('The requesition order header record could not be inserted into the database because');
            $DbgMsg = _('The SQL statement used to insert the requesition order header record and failed was');
            $result = DB_query($SQLInsert,$db,$ErrMsg,$DbgMsg,true);
            
    if (isset($TransNo)){
    header('Location:' . $rootpath . '/rh_REQ_Items.php?reqno=' . SID . $TransNo);
    exit;
    }
}

include('includes/header.inc');




if (isset($_GET['reqno']) && !isset($_POST['intostocklocation'])){
    $sql = "SELECT *
    FROM rh_requisicion
    WHERE reqno='" . $_GET['reqno'] . "'";
    $ReqNo = DB_query($sql,$db);
    $ReqNo = DB_fetch_assoc($ReqNo);
    
    $_POST['UserID'] = $ReqNo['initiator'];
    $_POST['intostocklocation'] = $ReqNo['intostocklocation'];
    $LocnAddrResult['deladd1'] = $ReqNo['deladd1'];
    $LocnAddrResult['tel'] = $ReqNo['tel'];
    $LocnAddrResult['deladd2'] = $ReqNo['deladd2'];
    $LocnAddrResult['contact'] = $ReqNo['contact'];
    $LocnAddrResult['deladd3'] = $ReqNo['deladd3'];
    $LocnAddrResult['deladd4'] = $ReqNo['deladd4'];
    $LocnAddrResult['deladd5'] = $ReqNo['deladd5'];
    $LocnAddrResult['deladd6'] = $ReqNo['deladd6'];
    $_POST['stat_comment'] = $ReqNo['stat_comment'];
    $_POST['comments'] = $ReqNo['comments'];
    $_POST['reqdate'] = $ReqNo['reqdate'];
}else{
    $_POST['UserID'] = $_SESSION['UserID'];
    $_POST['reqdate'] = date('Y-m-d');
}

if (isset($_POST['SelectLocation']) && $_POST['intostocklocation']!=''){
    
    $SQLLoc = "SELECT deladd1,
        deladd2,
        deladd3,
        deladd4,
        deladd5,
        deladd6,
        tel,
        contact
    FROM locations
    WHERE loccode='" . $_POST['intostocklocation'] . "'";
    $_LocnAddrResult = DB_query($SQLLoc,$db);
    $LocnAddrResult = DB_fetch_assoc($_LocnAddrResult);
    
    /*
    echo "<pre>";
    print_r($LocnAddrResult);
    echo "</pre>"; 
    */
    
}

?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <div class="container-fluid no-outter-border" style="max-width: 50%; margin-left: 25%;">

                <div style="height: 20px;"></div>
                <form  name="WareHouse" method="POST">
                    <table class="table tableprops">
                        <thead>
                            <tr>
                                <th colspan="2">Detalles de Iniciación de la Requisición</th>
                                <th colspan="2">Estado de la Requisioción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Fecha Req.</td>
                                <td><?php echo $_POST['reqdate']; ?><input name="reqdate" type="hidden" value="<?php echo $_POST['reqdate']; ?>" /></td>
                                <td>Estado:</td>
                                <td>
                                    <select name="status">
                                        <option>Nuevo</option>
                                        <option>Pendiente</option>
                                        <option>Autorizado</option>
                                        <option>Cancelado</option>
                                        <option>Impreso</option>
                                        <option>Completo</option>
                                    </select>
                                </td>
                            </tr>
                            
                            <tr>
                                <td>Requerida por:</td>
                                <td><?php echo $_POST['UserID']; ?> <input type="hidden" name="initiator" value="<?php echo $_POST['UserID']; ?>" /></td>
                                <td>Comentarios:</td>
                                <td><textarea name="stat_comment"><?php echo $_POST['stat_comment']; ?></textarea></td>
                            </tr>
                            
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            
                        </tbody>
                    </table>
                
                    <div style="height: 20px;"></div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th colspan="4">Warehose Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Almacen</td>
                                <td>
                                    <select name="intostocklocation" onChange="ReloadForm(WareHouse.SelectLocation)" >
                                        <?php foreach ($_SESSION['rh_permitionlocation'] as $value => $name){ 
                                            if(isset($_POST['intostocklocation']) && $_POST['intostocklocation'] == $value){
                                               echo '<option selected=selected value="'.$value.'">'.$name.'</option>';
                                            }
                                        ?>
                                            <option value="<?=$value?>"><?=$name?></option>
                                        <?php } ?>
                                    </select>
                                    <input type="submit" name="SelectLocation" value="Seleccionar" />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            
                            <tr>
                                <td>Address 1:</td>
                                <td><input type="text" name="deladd1" value="<?=$LocnAddrResult['deladd1']?>" /></td>
                                <td>Telefono:</td>
                                <td><input type="text" name="tel" value="<?=$LocnAddrResult['tel']?>" /></td>
                            </tr>
                            <tr>
                                <td>Addres 2:</td>
                                <td><input type="text" name="deladd2" value="<?=$LocnAddrResult['deladd2']?>" /></td>
                                <td>Contacto:</td>
                                <td><input type="text" name="contact" value="<?=$LocnAddrResult['contact']?>" /></td>
                            </tr>
                            <tr>
                                <td>Address 3:</td>
                                <td><input type="text" name="deladd3" value="<?=$LocnAddrResult['deladd3']?>" /></td>
                            </tr>
                            <tr>
                                <td>Addres 4:</td>
                                <td><input type="text" name="deladd4" value="<?=$LocnAddrResult['deladd4']?>" /></td>
                            </tr>
                            <tr>
                                <td>Address 5:</td>
                                <td><input type="text" name="deladd5" value="<?=$LocnAddrResult['deladd5']?>" /></td>
                            </tr>
                            <tr>
                                <td>Addres 6:</td>
                                <td><input type="text" name="deladd6" value="<?=$LocnAddrResult['deladd6']?>" /></td>
                            </tr>
                        </tbody>
                    </table>
                    <label>Comentarios:</label>
                    <textarea name="comments"><?php echo $_POST['comments']; ?></textarea>
                    <?php if(!isset($_GET['reqno'])){ ?>
                        <input type="submit" name="SaveReq" value="Guardar Requisición" />
                    <?php }else{ ?>
                        <input type="submit" name="UpdateReq" value="Actualizar Requisición" />
                    <?php } ?>
                </form>
                
                
                <?php if(isset($_GET['reqno'])){ ?>
                <a href="<?php echo $rootpath.'/rh_REQ_Items.php?reqno=' . SID . $_GET['reqno']; ?>" ><input type="button" id="AddItems" name="AddItems" value="Agregar Articulos"  /></a>
                <?php } ?>
                
                
                <div style="height: 20px;"></div>
                <?php if(isset($_GET['reqno'])){ ?>
                <div id="Accordion" class="accordion">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseOne">
                                <?php echo _('Items'); ?>
                            </a>
                        </div>
                        <div id="collapseOne" class="accordion-body collapse in">
                            <div class="accordion-inner">
                                <p>
                                    <?php include("rh_REQ_Items.php?reqno=" . SID . $_GET['reqno']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div><!--Accordion end-->
                <div id="Savebtn" class="control-group row-fluid">
                    <div class="span3">
                        <input type="submit" id="CreateLead" name="CreateLead" value="Guardar" style="margin-bottom: 0px;" />
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>




<?php
include_once('includes/footer.inc');
?>