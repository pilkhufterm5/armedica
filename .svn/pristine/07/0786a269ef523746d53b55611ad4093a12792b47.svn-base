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
        <?php
        echo "<A ACCESSKEY=\"0\" HREF=\"" . $rootpath . '/Logout.php?' . SID . "\" onclick=\"return confirm('" . _('Are you sure you wish to logout?') . "');\">"  . _('Logout') . '</A>';
        ?>
    </div>
    <br />
    <h1>Acceso exclusivo al SAT</h1>
    <br/>
    <div id="divRfcDelEmisorSeleccionado" style="display: none">
        <table id="tableRfcDelEmisorSeleccionado">
            <tbody>
                <tr class="headland">
                    <td colspan="2">
                        Emisor
                    </td>
                </tr>
                <tr>
                    <td>
                        Nombre:
                    </td>
                    <td id="tdNombreEmisor">

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
    <div id="divRfcDelEmisor">
        Seleccion un Emisor:
        <div id="divSelectRfcDelEmisor">
        </div>
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
                                    Folio De:<input type="text" id="folioDe" size="4"/>
                                    Folio A:<input type="text" id="folioA" size="4"/>
                                    Fecha De:<input type="text" id="fechaDe" size="8"/>
                                    Fecha A:<input type="text" id="fechaA" size="8"/>
                                    RFC del receptor:<input type="text" id="rfcDelReceptor" size="10"/>
                                    Periodo:<span id="divSelectReporteMensual"></span>
                                    <br/>
                                    <input type="button" value="Limpiar" onclick="limpiarFiltro()" />
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <input type="button" value="Buscar" onclick="buscarCfds()"/>
        <br/>
        <br/>
        <a href="javascript:location.reload(true)">Seleccionar Emisor</a>
    </div>
    <br />
    <br />
    <div id="divTableCfd">
    </div>
</div>
<script type="text/javascript" src="rh_j_globalFacturacionElectronica.js"></script>
<script type="text/javascript" src="rh_j_cfd_1.js"></script>
<script type="text/javascript">
    loadSelectRfcDelEmisor()
    //loadTableCfd()
</script>
<link href="rh_j_globalFacturacionElectronica.css" rel="stylesheet" type="text/css">
<?php
include('includes/footer.inc');
?>
