<?php
$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
require_once('CFD22Manager.php');
include('includes/SQL_CommonFunctions.inc');
include('includes/FreightCalculation.inc');
include('includes/GetSalesTransGLCodes.inc');


$CFDManager = CFD22Manager::getInstance();
$dom = new DOMDocument('1.0', 'utf-8');
$dom->loadXML($CFDManager->getSeries());
$rows = $dom->getElementsByTagName('row');
$Series="";
foreach($rows as $row){
   $Series.= "<option value='".$row->getAttribute('serie')."'>".$row->getAttribute('serie')."</option>";
}

if(isset($_POST['make'])&&isset($_POST['folioO'])&&isset($_POST['folioD'])){
        //*************************************************************************
        //**********                MOVIMIENTO CONTRARIO               ************
        //*************************************************************************
        $SQL = "select * from debtortrans WHERE id in (select id_debtortrans from rh_cfd__cfd where serie='".$_POST['serieO']."' and folio=".$_POST['folioO']." )";
        $Rs = DB_query($SQL,$db);
        $TableFields=array();
        $FieldsName=array();
        $Query=array();
        $Query2=array();
        $flag=false;
        while($rw = DB_fetch_assoc($Rs)){
          $flag=true;
          foreach($rw as $k=>$v){
            if($k!='id'){
                $TableFields[$k] = $k;
                if($k=='transno'){
                    $FieldsName[$k] = "'#TRNO#'";
                }else if($k=='rh_status'){
                    $FieldsName[$k] = "'C'";
                }else{
                    $FieldsName[$k] = "'".$v."'";
                }
            }else{
               $StkMoveno=$v;
            }

          }
          $Querys[]="insert into debtortrans (".implode(",",$TableFields).") values(".implode(",",$FieldsName).");";


                $SQL2 = "select * from rh_cfd__cfd where id_debtortrans=".$StkMoveno;
                $Rs2 = DB_query($SQL2,$db);
                $TableFields2=array();
                $FieldsName2=array();
                while($rw2 = DB_fetch_assoc($Rs2)){
                    foreach($rw2 as $k=>$v){
                        if($k!='fk_transno' && $k!='id_debtortrans' && $k!='serie' && $k!='folio' && $k!='id'){
                            $TableFields2[$k] = $k;
                            $FieldsName2[$k] = "'".DB_escape_string($v)."'";
                        }else if($k=='serie'){
                            $TableFields2[$k] = $k;
                            $FieldsName2[$k] = "'#SERIE#'";
                        }else if($k=='folio'){
                            $TableFields2[$k] = $k;
                            $FieldsName2[$k] = "'#FOLIO#'";
                        }else if($k=='fk_transno'){
                            $TableFields2[$k] = $k;
                            $FieldsName2[$k] = "'#TRNO#'";
                        }else if($k=='id_debtortrans'){
                            $TableFields2[$k] = $k;
                            $FieldsName2[$k] = "'#ID#'";
                        }
                    }
                    $Query2[]="insert into rh_cfd__cfd (".implode(",",$TableFields2).") values(".implode(",",$FieldsName2).");";
                }
          }

          if(!$flag){
            prnMsg(_('No se encontro documento con ese serie y folio.'),'warn');
          }else{
            foreach ($Querys as $k=>$SQL){
                $InvoiceNo = GetNextTransNo(10, $db);
                $SQL = str_replace("'#TRNO#'","'".$InvoiceNo."'",$SQL);
                echo $SQL;
                DB_query($SQL,$db);


                $ID_DEBTOR =  DB_Last_Insert_ID($db,'debtortrans','id');
                if(strlen($Query2[$k])>0){
                    $SQL2 = str_replace("'#ID#'","'".$ID_DEBTOR."'",$Query2[$k]);
                    $SQL2 = str_replace("'#TRNO#'","'".$InvoiceNo."'",$SQL2);
                    $SQL2 = str_replace("'#FOLIO#'","'".$_POST['folioD']."'",$SQL2);
                    $SQL2 = str_replace("'#SERIE#'","'".$_POST['serieD']."'",$SQL2);
                    echo $SQL2;
                   DB_query($SQL2,$db);
                   $sql="update debtortrans set alloc = ovgst+ovamount where  id=".$ID_DEBTOR;
                   DB_query($sql,$db); 
                   $CFDManager->cancelCFD($_POST['serieD'],$_POST['folioD']);
                }
            }
          }
}
?>

<form name="Form" method="POST" enctype="multipart/form-data"
	style="width: 100%;" action="">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Recuperar Documento V2.2</b></td>
	</tr>
	<tr>
		<td>Serie Origen:</td>
		<td><select name="serieO" style="width:100%;">
            <?php
               echo  $Series;
            ?>
        </select>
        </td>
	</tr>
	<tr>
		<td>Folio Origen:</td>
		<td><input type="text" name="folioO" style="width:100%;" value="0" />
        </td>
	</tr>
	<tr>
		<td>Serie Destino:</td>
		<td><select name="serieD" style="width:100%;">
            <?php
               echo  $Series;
            ?>
        </select>
        </td>
	</tr>
	<tr>
		<td>Folio Destino:</td>
		<td><input type="text" name="folioD" style="width:100%;" value="0" />
        </td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" name="make" value="Copiar Folio" style="width: 100%; display:inline;float:left" />
			</td>
    </tr>
</table>
</center>
</form>
<br />
<br />
<br />
<br />
<?php

include ('includes/footer.inc');
?>