<?php
$PageSecurity = 2;
include('includes/session.inc');

$title = _('Serie Certificados');
include('includes/header.inc');

?>

<div id="divAltaDeFolio" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <div id="divLigas">
        <a href="rh_CFDI_csd.php">Administraci&oacute;n de Sellos</a>
        <br />
        <a href="rh_CFDI_serie.php">Administraci&oacute;n de Series</a>
        <br />
    </div>
    <br />
     <form name="formAltaDeSello" method="POST" enctype="multipart/form-data" action="">
    <div id="divTableAltaDeFolio">
        <table id="tableAltaDeFolio" border="1">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Alta de Folio') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextSerie">
                            <?php echo _('Serie') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextSerie" name="inputTextSerie" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="inputTextValorInicial">
                            <?php echo _('Valor Inicial') ?>
                        </label>
                    </td>
                    <td>
                        <input type="text" id="inputTextValorInicial" name="inputTextValorInicial" />
                    </td>
                </tr>
               <tr>
                    <td class="center" colspan="2">
                        <input type="submit" value="Crear" onclick="createFolio()"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br />
    <br />
</form>
</div>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php

echo "<center>";
echo "<table class='headland'>";
echo "    <tr><td>Ubicacion-- </td>&nbsp;<td> Serie-- </td>&nbsp;<td> FolioIni-- </td>&nbsp;<td> Folio_Siguiente-- </td>&nbsp;<td>CSD</td></tr>";

$sql= "select id_locations,serie,folio,fsiguiente,id_ws_csd from rh_cfd__locations__systypes__ws_csd";
$result=DB_query($sql,$db);
while($rs=DB_fetch_array($result)){
    echo "    <tr><td>".$rs['id_locations']."</td>&nbsp;<td>".$rs['serie']."</td>&nbsp;<td>".$rs['folio']."</td>&nbsp;<td>".$rs['fsiguiente']."</td>&nbsp;<td>".$rs['id_ws_csd']."</td></tr>";
}
echo "</table>";
echo "</center>";
if(!isset($_POST['inputTextValorInicial'])&&!isset($_POST['inputTextSerie'])){

}else{
       $sql = "insert into rh_cfdi_folio (serie,folioInicial,fecha) values ('".$_POST['inputTextSerie']."','".$_POST['inputTextValorInicial']."',now());";
        $result = DB_query($sql,$db);
        if(DB_error_no($db)) {
          echo '<div class="error"><p><b>' . _('ERROR') . '</b> : No se pudo agregar la serie a la BD<p></div>';
          include ('includes/footer.inc');
          exit();
        }
}
include ('includes/footer.inc');
?>
