

<?php
//Php




$PageSecurity = 2;
include('includes/session.inc');
$title = _('Digital Invoice');
include('includes/header.inc');
?>
<!--Para el lockscreen -->
<div id="hiddenLayer" class="POP_LAYER_NONE" style="height: 800px;"></div>

<div id="divCfd" align="center">
    <div id="divWeberpPrnMsg">
    </div>
    <div align="right">
        <?php
        echo "<A ACCESSKEY=\"0\" HREF=\"" . $rootpath . '/Logout.php?' . SID . "\" onclick=\"return confirm('" . _('Are you sure you wish to logout?') . "');\">"  . _('Logout') . '</A>';
        ?>
    </div>
    <br />


    <h1>Acceso exclusivo al SAT</h1>
    <br/>


    <div id="divRfcDelEmisor">

<fieldset>
<legend>Búsqueda de emisor</legend>
RFC <input type="radio" id="radioRfc" value="radioGroupRfcDelEmisor"/> <br/>
Razón social<input type="radio" id="radioNombre" name="radioGroupRfcDelEmisor"/>  <br/>
<input type="text" id="inputTextRfcDelEmisor" onchange="createAutocomplete()" /> <br />
<input type="submit" value="Aceptar"/>
<input type="reset" value="Limpiar"/><br/>
</fieldset>

<a href="">Manual de busqueda</a>

</div>

<div class="orbea"> <a href="http://mozillaeurope.org/es/"><img src="manual_icon.jpg"></a></div>
    
<div id="divRfcDelEmisorSeleccionado" style="display: none">
        <table id="tableRfcDelEmisorSeleccionado">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        Emisor
                    </td>
                </tr>
                <!--<tr style="display: none">-->
                <tr>
                    <td>
                        ID:
                    </td>
                    <td id="tdIdEmisor">
                    </td>
                </tr>
                <tr>
                    <td>
                        RFC:
                    </td>
                    <td id="tdRFCEmisor">
                    </td>
                </tr>
        </table>
    </div>
    <br/>
    <div id="divTableReporteMensual" style="display: none">
        <table id="tableReporteMensual">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Reporte Mensual') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="requiredField" for="divSelectReporteMensual">
                            <?php echo _('Mes y Año') ?>
                        </label>
                    </td>
                    <td>
                        <div id="divSelectReporteMensual">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="center" colspan="2">
                        <input type="button" value="Descargar" onclick="downloadReporteMensual()"/>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br/>
    <div id="divConsulta" style="display: none">
        <table id="tableReporteMensual">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        <?php echo _('Consulta') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div id="divFiltro">
                            <a onclick="hideFiltro()" id="aFiltro">- Filtro</a>
                            <div id="divFiltroContent">
                                <table>
                                    <!--folio, periodos, fecha,  RFC del receptor-->
                                    <tr><td align="left">Folio De:</td><td><input type="text" id="folioDe" size="4"/></tr>
                                    <tr><td align="left">Folio A:</td><td><input type="text" id="folioA" size="4"/></tr>
                                    <tr><td align="left">Dia De:</td><td><input type="text" id="diaDe" size="4"/></td></tr>
                                    <tr><td align="left">Dia A:</td><td><input type="text" id="diaA" size="4"/></td></tr>
                                    <tr><td align="left">RFC del receptor:</td><td><div id="divSelectRfcDelReceptor"></div></td></tr>
                                    <tr><td colspan="2" <input type="button" value="Limpiar" onclick="limpiarFiltro()"
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="button" value="Buscar" onclick="buscarCfds()"/>
    </div>
    <br />
    <br />
    <div id="divTableCfd">
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_cfd_sat.js"></script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
include('includes/footer.inc');
?>
