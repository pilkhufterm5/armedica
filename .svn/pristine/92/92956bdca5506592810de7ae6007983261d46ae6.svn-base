<?
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */
$PageSecurity = 14;
include('includes/session.inc');
/**/
unset($_SESSION['ventas']);
$_SESSION['ventas'] = 0;
unset($_SESSION['ventas']);
/**/
$ruta_imagen = "companies/".$_SESSION['DatabaseName']."/logo.jpg";
$datos_companie = $_SESSION['CompanyRecord'];
/*se validan los datos principales del array*/

if(!isset($_SESSION['rh_pos_principal'])){
    /*error cuando no exista*/
    echo "<p>No se encontraron los datos basicos de configuracion, intenta de nuevos Error Code:501</p>";
    exit();
}else{
    /*si existe, validan que sean datos validos*/
    $generales = $_SESSION['rh_pos_principal'];
    $cliente = $generales['cliente'];
    $sucursal = $generales['sucursalC'];
    /*valida que contenga datos coherentes*/
    if($cliente == "" || $sucursal == ""){
        /*cuando estos datos se reciben de manera mal*/
        echo "<p>No se encontraron los datos basicos de configuracion, intenta de nuevos Error Code:502</p>";
        exit();
    }
}

/*se consulta la informacion basica sobre la terminar, el cajero y la sucursal*/
$terminal = $generales['terminal'];
$ip = $generales['IP'];
$cliente = $cliente;
$sucursal = $sucursal;
$usuario = $generales['usuario'];

/*1er sql*/
$sql = "Select pos.id, pos.Sucursal, pos.Terminal, pos.userid, pos.Fecha, loc.locationname from rh_pos_terminales pos LEFT JOIN locations loc ON pos.Sucursal = loc.loccode WHERE pos.id = '".$terminal."'";
$base_terminal = DB_query($sql,$db);
if(DB_num_rows($base_terminal) == 0){
    echo "<p>No se encontraron los datos basicos de configuracion, intenta de nuevos Error Code:503</p>";
    exit();
}else{
    $myrow=DB_fetch_array($base_terminal);
    $nombre_terminal = $myrow['Terminal'].'; IP:'.$ip;
    $sucursal_terminal = $myrow['Sucursal'];
}

/*2do sql*/
$sql = "Select realname FROM www_users where userid = '".$usuario."'";
$base_usuario = DB_query($sql,$db);
if(DB_num_rows($base_usuario) == 0){
    echo "<p>No se encontraron los datos basicos de configuracion, intenta de nuevos Error Code:504</p>";
    exit();
}else{
    $myrow=DB_fetch_array($base_usuario);
    $nombre_usuario = $myrow['realname'];
}

/*3cer sql*/
$sql = "Select locationname FROM locations where loccode = '".$sucursal_terminal."'";
$base_sucursal = DB_query($sql,$db);
if(DB_num_rows($base_sucursal) == 0){
    echo "<p>No se encontraron los datos basicos de configuracion, intenta de nuevos Error Code:505</p>";
    exit();
}else{
    $myrow=DB_fetch_array($base_sucursal);
    $nombre_sucursal = $myrow['locationname'];
}

?>
<html>
	<head>
		<title>RealHost webERP</title>
	</head>
		<!-- SECCION DE LOS CSS -->
		<LINK href="rh_pos_archivos/rh_css_pos_principal.css" rel="stylesheet" type="text/css">
		<LINK href="rh_pos_archivos/thickbox.css" rel="stylesheet" type="text/css">
		<LINK href="rh_pos_archivos/demos.css" rel="stylesheet" type="text/css">
                <LINK href="rh_pos_archivos/jquery.msgbox.css" rel="stylesheet" type="text/css">
		<!-- SECCION DEL JAVASCRIPT -->
		<script type="text/javascript" src="rh_pos_archivos/jquery-1.4.2.min.js"></script>
		<script type="text/javascript" src="rh_pos_archivos/jquery.dataTables.js"></script>
		<script type="text/javascript" src="rh_pos_archivos/thickbox-compressed.js"></script>
                <script type="text/javascript" src="rh_pos_archivos/jquery.msgbox.min.js"></script>
		<script type="text/javascript" src="rh_pos_archivos/rh_javascript_pos_principal.js"></script>
		<!--[if lt IE 7]>
			<style media="screen" type="text/css">
				.col1 {
					width:100%;
				}
			</style>
		<![endif]-->	
	<body>
	<!--	SECCION INICIAL DE LA PAGINA	 -->
        <a href="rh_pos_pago.php?keepThis=true&TB_iframe=true&height=600&width=500" title="Forma de Pago" class="thickbox" id="pagarR" style="visibility:hidden !important; display:none !important;"></a>
        <a href="rh_pos_serieLote.php?keepThis=true&TB_iframe=true&height=600&width=500" title="Serie - Lote" class="thickbox" id="seriesLotes" style="visibility:hidden !important; display:none !important;"></a>
		<div id="header">
			<!-- SECCION DE HEADER -->
			<table border="0" height="80px" width="99%" style="width:99% !important; height:80px !important; border:0px !important; margin:5px !important; padding:5px !important;">
				<tr>
					<td valign="top" width="48%" style="width:40% !important;">
						<table border="0" height="100%" width="100%" style="width:100% !important; height:100% !important; border:0px !important; margin:0px !important; padding:0px !important;">
							<tr>
								<td valign="MIDDLE" width="170px" style="width:170px !important;">
									<img src="rh_pos_archivos/realhost.png" alt='logo' title='logo'>
								</td>
								<td valign="MIDDLE">
									<p>&nbsp;&nbsp;RealHost webERP POS 2008 - <?=date("Y")?></p>
									<p>&nbsp;&nbsp;Version 0.1 (beta)</p>
									<p>&nbsp;&nbsp;<a href="http://www.realhost.com.mx" target="_blank">RealHost</a></p>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top" width="48%" style="width:40% !important; border:0px">
						<table border="0" height="100%" width="100%" style="width:100% !important; height:100% !important; border:0px !important; margin:0px !important; padding:0px !important;">
							<tr>
								<td valign="MIDDLE" width="110px" style="width:110px !important;">
									<img src="<?=$ruta_imagen?>" alt='logo' title='logo' width="110px" height="70px">
								</td>
								<td valign="MIDDLE">
									<p>&nbsp;Empresa: <?=$datos_companie[0];?></p>
									<p>&nbsp;RFC: <?=$datos_companie['gstno'];?></p>
									<p>&nbsp;Fecha: <?=date("Y-m-d")?>, son las <span id='tiempo'><?=date("H:i:s")?></span></p>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<!--	FIN DE LA SECCION DEL HEADER -->
		</div>
		<div class="colmask holygrail">
			<div class="colmid">
				<div class="colleft">
					<div class="col1wrap">
						<div class="col1">
								<div style="width:97.7% !important; border:1px solid black; margin:5px !important; padding:5px !important;">
									<table>
										<tr>
											<td width="90px" align="right">Terminal:</td>
											<td><?=$nombre_terminal?></td>
										</tr>
										<tr>
											<td width="90px" align="right">Cajero:</td>
											<td><?=$nombre_usuario?></td>
										</tr>
										<tr>
											<td width="90px" align="right">Sucursal:</td>
											<td><?=$nombre_sucursal;?></td>
										</tr>																					
									</table>
								</div>
								<div id="productos" class="productos" style="width:97.7% !important; height:55% !important; border:1px solid black; margin:5px !important; padding:5px !important; display:block; overflow: auto;">
									<p>Sin Articulos</p>
								</div>
								<div id="total" class="total" style="width:97.7% !important; height:16% !important; border:1px solid black; margin:5px !important; padding:5px !important; display:block">
									<table height="100%" width="100%" border="0" style="margin:0px !important; padding:0px !important; border:0px !important;">
										<tr>
											<td valign="MIDDLE" width="265px" align="right" style="font-size:35px;">&nbsp;Total a Pagar&nbsp;</td>
											<td valign="MIDDLE" align="left" style="font-size:35px">$ <span id="pagarTotal" class="pagarTotal">0.00</span></td>
										</tr>
									</table>
								</div>
						</div>
					</div>
					<div class="col2">
						<div style="display:block; clear:both; margin-top:5px !important; border:0px; height:200px !important; ">
							<form name="articulos" id="articulos" class="articulos" method="POST" action="#">
								<fieldset style="border:1px solid black;">
									<legend>Articulos</legend>
									<label for="StockCode" class="item">Articulo:</label>
									<input type="text" name="StockCode" id="StockCode" class="input" tabindex="1" /> 
									
                                                                        <label for="Keywords" class="item">Descripci&oacute;n:</label>
									<input type="text" name="Keywords" id="Keywords" class="input" tabindex="2" />
									
                                                                        <label for="StockCat" class="item">Categor&iacute;as</label>
									<select name="StockCat" id="StockCat" class="select" tabindex="3">
                                                                                <option value="-9">Todas</option>
                                                                                <?
                                                                                    $SQL="SELECT categoryid, categorydescription FROM stockcategory WHERE stocktype='F' OR stocktype='D' ORDER BY categorydescription";
                                                                                    $result1 = DB_query($SQL,$db);
                                                                                    while ($myrow1 = DB_fetch_array($result1)){
                                                                                        echo '<option value='. $myrow1['categoryid'] . '>' . $myrow1['categorydescription']."</option>";
                                                                                    }
                                                                                ?>
									</select>
									
									<label for="Search" class="item">Consultar</label>
									<input type="submit" name="Search" id="Search" class="submit" tabindex="4" value="Buscar" />
								</fieldset>
							</form>
						</div>
						<br />
						<div style="width:264px;">
							<fieldset style="border:1px solid black;">
								<legend>Resultados Busqueda</legend>
								<div id="resultados" class="resultados" style="height:191px !important; width:100% !important; overflow: auto;">
									<p>Sin Resultados</p>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="col3">
						<div id="opcionesE" class="opcionesE" style="display:block; clear:both;">
							<fieldset style="border:1px solid black;">
								<legend>Opciones Extra</legend>
								<div id="ventaE" class="ventaE" style="display:block; clear:both; height:204px;">
                                                                    <p>Para acceder a este menu es necesario seleccionar un articulo previamente</p>
								</div>
							</fieldset>
						</div>
						<div id="clienteE" class="clienteE" style="display:block; clear:both;">
							<fieldset style="border:1px solid black;">
								<legend>Cliente</legend>
								<center>
									<form name="cliente" id="cliente" method="POST" action="#">
										<label for="CustKeywords">Cliente</label><br />
										<select name="CustKeywords" id="CustKeywords" class="CustKeywords" onchange="update_cliente(this.value);" r>
                                                                                        <?
                                                                                            /*sql que trae todas las sucursales*/
                                                                                            $sql_ = "Select debtorno, name FROM debtorsmaster order by name ASC";
                                                                                            $result1 = DB_query($sql_,$db);
                                                                                            while ($myrow1 = DB_fetch_array($result1)){
                                                                                                if($cliente == $myrow1['debtorno']){
                                                                                                    echo '<option value='. $myrow1['debtorno'] . ' selected>' . $myrow1['name']."</option>";
                                                                                                }else{
                                                                                                    //echo '<option value='. $myrow1['debtorno'] . '>' . $myrow1['name']."</option>";
                                                                                                }
                                                                                            }
                                                                                        ?>
										</select>
										<label for="BranchCode">Sucursal</label><br />
										<select name="BranchCode" id="BranchCode" class="BranchCode" onchange="update_sucursales(this.value);">
											<option value="-9" selected>Mostrador</option>
										</select>
									</form>
								</center>
								<!-- <p style="font-size:10px; margin:0px !important; color:red">*NOTA: El Cliente inicial es el cliente Mostrador</p> -->
							</fieldset>						
						</div>

                                                <div id="Svend" class="Svend" style="display:block; clear:both;">
                                                    <fieldset style="border:1px solid black; height:35px !important; margin:5px;">
                                                        <legend>Vendedor</legend>
                                                        <center>
                                                            <form id="vendedor" name="vendedor" class="vendedor">
                                                                <select name="Vendedor" id="Vendedor" class="Vendedor">
                                                                        <option value="-9" selected>Selecciona</option>
                                                                        <?
                                                                            $sql_Ve = "Select userid, realname FROM www_users ORDER BY realname";
                                                                            $result_Ve = DB_query($sql_Ve,$db);
                                                                            while ($myrow1_Ve = DB_fetch_array($result_Ve)){
                                                                                echo "<option value='".$myrow1_Ve['userid']."'>".$myrow1_Ve['realname']."</option>";
                                                                            }
                                                                        ?>
                                                                </select>
                                                           </form>
                                                        </center>
                                                    </fieldset>
                                                </div>

						<div id="pagarE" class="pagarE" style="display:block; clear:both;">
							<fieldset style="border:1px solid black; height:50px !important; margin:5px;">
								<legend>Cerrar Transaccion</legend>
								<center>
									<form name="pagar" id="pagar" method="POST" action="#">
										<label for="pagar"></label>
										<input type="submit" name="pagarT" id="pagarT" class="pagarT" value="Pagar" onclick="javascript: pagar_total(); return false;" />
									</form>
								</center>
							</fieldset>						
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="footer">
			<!-- SECCION DEL FOOTER DE LA PAGINA -->
			<table border="0" height="100px" width="100%" style="width:100% !important; height:100px !important;">
				<tr valign='MIDDLE' height="100%" align="center">
					<td width="100%" height="100%" style="width:100% !important; height:100% !important; display:block; clear:both;">
						<table border="0" height="100%" width="100%" style="width:100% !important; height:100% !important;">
							<tr>
								<td width="20%" align="center" style="width:20% !important;"><input type='button' name='nueva' id='nueva' class='opciones' value='Nueva Venta' onclick="javascript: return new_compra();" /></td>
                                                                <td width="20%" align="center" style="width:20% !important;"><input type='button' name='limpiar' id='limpiar' class='opciones' value='Guardar Venta' onclick="javascript: return save_compra();" /></td>
                                                                <td width="20%" align="center" style="width:20% !important;"><input type='button' name='cargar' id='cargar' class='opciones' value='Recuperar Venta' onclick="javascript: return load_compra();" /></td>
                                                                <td width="20%" align="center" style="width:20% !important;"><input type='button' name='registradora' id='registradora' class='opciones' value='Verificador de Precios' onclick="javascript: return caja_registradora();" /></td>
								<td width="20%" align="center" style="width:20% !important;"><input type='button' name='salir' id='salir' class='opciones' value='Salir' onclick="javascript: return confirma_salir();" /></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
	<!--	FIN DE PAGINA	 -->
	</body>
</html>
