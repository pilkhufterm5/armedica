<?php

/*
 * iJPe
 * realhost
 * 2010-05-25
 *
 * Se realizo este archivo para implementar la funcionalidad de cambio de fecha a las facturas
 */

$PageSecurity = 10;

include('includes/session.inc');

$title = _('Cambiar fecha a factura');
include('includes/header.inc');

If (isset($_POST['verInv']) || isset($_POST['changeDate'])){
 
    if (isset($_POST['numInv']) && is_numeric($_POST['numInv'])){
        $sqlVer = "SELECT trandate, DAY(trandate) as dia, MONTH(trandate) as mes, YEAR(trandate) as anio FROM debtortrans WHERE type = 10 and transno = ".$_POST['numInv']."";
        $resVer = DB_query($sqlVer, $db);
        $rowVer = DB_fetch_array($resVer);

        $_POST['nowDate'] = $rowVer['dia']."-".$rowVer['mes']."-".$rowVer['anio']; 

    }else{
        prnMsg("Favor de ingresar correctamente el numero de factura","warn");
        $error = 1;
    }
}

If (isset($_POST['changeDate']) && $error != 1){

    $sqlPerA = "SELECT periodno FROM periods WHERE lastdate_in_period > '".FormatDateForSQL($_POST['nowDate'])."' limit 1";
    $resPerA = DB_query($sqlPerA, $db);
    $rowPerA = DB_fetch_array($resPerA);

    $sqlPerN = "SELECT periodno FROM periods WHERE lastdate_in_period > '".FormatDateForSQL($_POST['newDate'])."' limit 1";
    $resPerN = DB_query($sqlPerN, $db);
    $rowPerN = DB_fetch_array($resPerN);

    if ($rowPerA['periodno'] == $rowPerN['periodno']){

        $sqlUpdDeb = "UPDATE debtortrans SET trandate = '".FormatDateForSQL($_POST['newDate'])."' WHERE transno = ".$_POST['numInv']." AND type = 10";
        DB_query($sqlUpdDeb, $db);

        $sqlUpdGL = "UPDATE gltrans SET trandate = '".FormatDateForSQL($_POST['newDate'])."' WHERE typeno = ".$_POST['numInv']." AND type = 10";
        DB_query($sqlUpdGL, $db);

        $sqlUpdSM = "UPDATE stockmoves SET trandate = '".FormatDateForSQL($_POST['newDate'])."' WHERE transno = ".$_POST['numInv']." AND type = 10";
        DB_query($sqlUpdSM, $db);

        prnMsg("Se ha realizado correctamente el cambio de fecha al ".$_POST['newDate']." para la factura con numero interno ".$_POST['numInv']."","success");
        unset($_POST);

    }else{
        prnMsg("Solo se permite realizar la modificacion de fecha de factura para el mismo mes (periodo)","info");
    }
}


echo "<center>";
echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . "?" . SID . "' METHOD=POST NAME='form'>";


echo "<table>";
echo "<tr><td>";
echo _('Numero de Factura (Interno)')."</td>";
echo "<td><input type='textbox' name='numInv' value='".$_POST['numInv']."'></td>";
echo "<td><input type='submit' name='verInv' value='"._('Verificar Fecha de Factura')."'>";
echo "</td></tr>";

echo "<tr><td>";
echo _('Fecha Actual')."</td>";
echo "<td><input type='textbox' name='nowDate' value='".$_POST['nowDate']."' readonly>";
echo "</td></tr>";

echo "<tr><td>";
echo _('Fecha a Asignar')."</td>";
echo "<td><input type='textbox' name='newDate' value='".$_POST['newDate']."'>";
echo "</td></tr>";

echo "<tr><td colspan=2 align='center'>";
echo "<input type='submit' name='changeDate' value='"._('Cambiar Fecha')."'>";
echo "</td></tr>";

echo "</FORM>";
echo "</center>";

include("includes/footer.inc");

?>
