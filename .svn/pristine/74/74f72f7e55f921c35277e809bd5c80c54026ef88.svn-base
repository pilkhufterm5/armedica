<?php
//Php
$PageSecurity = 12;
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
        <span>
            <a href="a.pdf">Manual</a>
        </span>
        |
        <span>
        <?php
        echo "<A ACCESSKEY=\"0\" HREF=\"" . $rootpath . '/Logout.php?' . SID . "\" onclick=\"return confirm('" . _('Are you sure you wish to logout?') . "');\">"  . _('Logout') . '</A>';
        ?>
        </span>
    </div>
    <br />
    <h1>Acceso exclusivo al SAT</h1>
    <br/>
    <div id="divRfcDelEmisor">
        <table>
            <tr>
                <td style="text-align: left">
                    Criterio de busqueda:
                    <br/>
                    <a href="">Manual de busqueda</a>
                </td>
                <td style="text-align: left">
                    RFC: <input type="radio" id="radioRfc" name="radioGroupRfcDelEmisor" />
                    <br/>
                    Razon Social: <input type="radio" id="radioNombre" name="radioGroupRfcDelEmisor" />
                </td>
            </tr>
            <tr>
                <td style="text-align: left">
                    <input type="text" id="inputTextRfcDelEmisor" onchange="createAutocomplete()" />
                <td>
            </tr>
            <tr>
                <td id="tdAutocomplete" style="text-align: left">
                </td>
            </tr>
        </table>
    </div>
    <div id="divUser">
        <input type="button" value="Mostrar todos los Emisores" onclick="loadPaginationTableUser();satLoadTableUser(0, 9)" />
        <br/><br/>
        <div id="divPaginationTableUser"></div>
        <div id="divTableUser"></div>
    </div>
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
                        <?php echo _('Periodo') ?>
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
                                    <tr><td align="left">Folio De:</td><td><input type="text" id="folioDe" size="4"/>
                                    <td align="left">Folio A:</td><td><input type="text" id="folioA" size="4"/>
                                    <td align="left">Dia De:</td><td><input type="text" id="diaDe" size="4"/></td>
                                    <td align="left">Dia A:</td><td><input type="text" id="diaA" size="4"/></td>
                                    <td align="left">RFC del receptor:</td><td><div id="divSelectRfcDelReceptor"></div></td>
                                </table>
                                <input type="button" value="Limpiar" onclick="limpiarFiltro()" />
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
    <div id="divTableCfd"></div>
    <br/>
    <br/>
    <div id="divInformacionEstadistica" style="display: none">
        <h3>Informacion estadistica:</h3>
        <div id="divTableSatInformacionEstadistica">
        </div>
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_cfd_sat.js"></script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
include('includes/footer.inc');
?>
