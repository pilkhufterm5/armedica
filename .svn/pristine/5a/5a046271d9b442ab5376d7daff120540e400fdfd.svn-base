<?php
/**
 * 	REALHOST 17 DE ABRIL DEL 2010
 * 	POS DEL WEBERP
 * 	VERSION 1.0
 * 	RICARDO ABULARACH GARCIA
 * */

/*encargado de imprimir el ticekt de venta*/
$PageSecurity = 14;
include('includes/session.inc');
//header("Content-type:application/pdf");
//header("Content-Disposition:attachment;filename='downloaded.pdf'");

/*se obtienen los datos con los cuales se va a trabajar*/
$empresa = $_SESSION['CompanyRecord'];

include('rh_pos_archivos/barcode/barcode.php');
require_once('rh_pos_archivos/html2pdf/html2pdf.class.php');
include("rh_pos_archivos/rh_numbers.php");

/*se reciben lo que son los datos para poder trabajar con ellos*/
if(!isset($_GET['datos'])){
    /*si no existe*/
    echo "<p>NOTA: no se puede procesar tu peticion, intenta de nuevo</p>";
    echo "<p>Codigo de Referencia: ".$_GET['datos']."</p>";
    die();
}

$numalet= new CNumeroaletra;
$numalet->setMoneda("Pesos");
$numalet->setPrefijo("");
$numalet->setSufijo("");

function codeWrap($code, $cutoff = null, $delimiter = "&raquo;\n") {
    $lines = explode("\n", $code);
    $count = count($lines);
    for ($i = 0; $i < $count; ++$i) {
        preg_match('/^\s*/', $lines[$i], $matches);
        $lines[$i] = wordwrap($lines[$i], $cutoff, ($delimiter . $matches[0]));
    }
    return implode("\n", $lines);
}

function get_time(){
    // Obtenemos y traducimos el nombre del día
    $dia=date("l");
    if ($dia=="Monday") $dia="Lunes";
    if ($dia=="Tuesday") $dia="Martes";
    if ($dia=="Wednesday") $dia="Miercoles";
    if ($dia=="Thursday") $dia="Jueves";
    if ($dia=="Friday") $dia="Viernes";
    if ($dia=="Saturday") $dia="Sabado";
    if ($dia=="Sunday") $dia="Domingo";

    // Obtenemos el número del día
    $dia2=date("d");

    // Obtenemos y traducimos el nombre del mes
    $mes=date("F");
    if ($mes=="January") $mes="01";
    if ($mes=="February") $mes="02";
    if ($mes=="March") $mes="03";
    if ($mes=="April") $mes="04";
    if ($mes=="May") $mes="05";
    if ($mes=="June") $mes="06";
    if ($mes=="July") $mes="07";
    if ($mes=="August") $mes="08";
    if ($mes=="September") $mes="09";
    if ($mes=="October") $mes="10";
    if ($mes=="November") $mes="11";
    if ($mes=="December") $mes="12";

    // Obtenemos el año
    $ano=date("Y");

    return "$dia2/$mes/$ano";
}

function get_ivas($taxcatid){
    global $db;
    //$sql = "SELECT taxgrouptaxes.calculationorder, taxauthorities.description, taxgrouptaxes.taxauthid, taxauthorities.taxglcode, taxgrouptaxes.taxontax, taxauthrates.taxrate FROM taxauthrates INNER JOIN taxgrouptaxes ON taxauthrates.taxauthority=taxgrouptaxes.taxauthid INNER JOIN taxauthorities ON taxauthrates.taxauthority=taxauthorities.taxid WHERE taxgrouptaxes.taxgroupid=4 AND taxauthrates.dispatchtaxprovince=2 AND taxauthrates.taxcatid = ".$taxcatid." ORDER BY taxgrouptaxes.calculationorder";
    $sql='SELECT
    taxauthrates.taxrate,
    (taxauthrates.taxrate * 100) as iva
FROM
    custbranch
    INNER JOIN taxgroups
        ON (custbranch.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxgrouptaxes
        ON (taxgrouptaxes.taxgroupid = taxgroups.taxgroupid)
    INNER JOIN taxauthrates
        ON (taxgrouptaxes.taxauthid = taxauthrates.taxauthority) where taxauthrates.taxcatid=6 and custbranch.branchcode="'.$_SESSION['rh_pos_principal']['sucursalC'].'" and taxauthrates.taxrate>0;';
    $ivass = DB_query($sql,$db);
    if(DB_num_rows($ivass) == 0) {
        $ivadd = 0;
        $ivaTasa=0;
    }else {
        $myrowd=DB_fetch_array($ivass);
        $ivadd[0] = $myrowd['taxrate'];
        $ivadd[1]=$myrowd['iva'];
    }
    return $ivadd;
}

/*si se llegaron lso datos correctamente seguimos trabajando con los datos con lo que debemos de trabajar*/
$dividir = @explode("***", $_GET['datos']);
$OrderNo = $dividir[0];
$InvoiceNo = $dividir[1];

/*obtener informacion base*/
$sql_suc_ = "Select terminal, usuario, cliente, sucursalC, IP, Fecha FROM rh_pos_guardar_venta WHERE pedido = '".$OrderNo."' AND remision = '".$InvoiceNo."'";
$clie_ = DB_query($sql_suc_,$db);
$myrow=DB_fetch_array($clie_);

/*terminar obtener suc*/
$sql_terminal = "Select Sucursal FROM rh_pos_terminales WHERE id = '".$myrow['terminal']."'";
$termi_ = DB_query($sql_terminal,$db);
$myrowt_ = DB_fetch_array($termi_);

/*sucursal venta*/
$sql_suc_venta = "Select locationname, deladd1, deladd2, deladd3, deladd4, deladd5, deladd6, tel FROM locations where loccode = '".$myrowt_['Sucursal']."'";
$suc_ = DB_query($sql_suc_venta,$db);
$myrows_ = DB_fetch_array($suc_);



/*parametros para la generacion del codigo de barras del ticket que se va a imprimir*/
//$myFile = "rh_pos_archivos/barcode/codigos/".$InvoiceNo."."."GIF";
//Barcode39($InvoiceNo, 200, 50, 100, "GIF", $InvoiceNo, $myFile);
//<img src="$myFile" alt="$myFile" title="$myFile" /> --> aki es donde se genera la imagen con la cual se va a trabajar
/*html necesario para la generacion del ticket con el que se va a trabajar*/
$barcode='';
for ($i=1; $i<=12-strlen($InvoiceNo);$i++){
    $barcode.='0';
}
$barcode.=$InvoiceNo;

?>

<?php
echo '<applet code="Tickets.class" archive="POS.jar" width="32" height="32" alt="Instala la maquina virtual de java">  ';
    $aux = strtoupper($_SERVER["HTTP_USER_AGENT"]);
    if(strstr($aux,'LINUX')!==false){?>
    <param name="SO" value="Linux">
<?}else{?>
    <param name="SO" value="Windows">
<?}?>
    <param name="RazonSocial" value="<?php echo $empresa['0'];?>">
    <param name="rfc" value="<?php echo $empresa['1'];?>">
    <param name="direccion" value="<?php echo $empresa['regoffice1'].' '.$empresa['regoffice4'].' '.$empresa['regoffice5'].' '.$empresa['telephone'];?>">
    <param name="sucursal" value="<?php echo $myrows_['locationname'];?>">
    <param name="direccion2" value="<?php echo $myrows_['deladd1'].' '.$myrows_['deladd4'].' '.$myrows_['deladd5'];?>">
    <?php
        $sql_cliente = "Select * FROM debtorsmaster,debtortrans where debtorsmaster.debtorno = debtortrans.debtorno and debtortrans.type=20000 and transno=".$InvoiceNo;
        $cliente = DB_query($sql_cliente,$db);
        $cliente_rows = DB_fetch_array($cliente);
    ?>
    <param name="ncliente" value="<?php echo $cliente_rows['debtorno']; ?>">
    <param name="razon_cliente" value="<?php echo $cliente_rows['name']; ?>">
    <param name="rfc_cliente" value="<?php echo $cliente_rows['taxref']; ?>">
    <?php if(strlen($cliente_rows[12])>0){ ?>
        <param name="direccion_cliente" value="<?php echo $cliente_rows['address1'].' '.$cliente_rows['address2'].' '.$cliente_rows['address3'].' '.$cliente_rows['address4'].' '.$cliente_rows['address5'].' C.P. '.$cliente_rows['address6'].' '.$cliente_rows['address7'].' '.$cliente_rows['address8'].' '.$cliente_rows['address9'].' '.$cliente_rows['address10']; ?>">
    <?php }?>
    <param name="ticket" value="<?=$barcode?>">
    <!---<param name="terminal" value="1566332">-->
    <param name="cajero" value=" <?=$myrow['usuario']?>">
    <param name="fecha" value="<?php echo get_time();?>, <?php echo date('H:i:s')?>">
                    <?
                        //$sq_items = "Select rm.stockid, rm.transno, rm.price, rm.reference, rm.qty, rm.discountpercent, st.description, st.taxcatid FROM rh_remdetails rm, stockmaster st WHERE rm.transno = '".$InvoiceNo."' AND rm.stockid = st.stockid";
                        $sq_items = "Select rm.stockid, rm.transno, rm.price, rm.reference, (rm.qty*-1) as qty, rm.discountpercent,  st.description, st.taxcatid FROM stockmoves rm, stockmaster st WHERE rm.transno = '".$InvoiceNo."' AND rm.type = 20000 AND rm.show_on_inv_crds=1 AND rm.stockid = st.stockid";
                       //$sq_items="SELECT stockmoves.stockid, stockmaster.description, -stockmoves.qty as quantity, stockmoves.discountpercent, ((1 - stockmoves.discountpercent) * stockmoves.price * 1* -stockmoves.qty) AS fxnet, (stockmoves.price * 1) AS fxprice, stockmoves.narrative, stockmaster.units FROM stockmoves, stockmaster WHERE stockmoves.stockid = stockmaster.stockid AND stockmoves.type=20000 AND stockmoves.transno='".$InvoiceNo."' AND stockmoves.show_on_inv_crds=1 ORDER BY stockmoves.rh_orderline";
                       //echo $sq_items;

                        $itemsResult = DB_query($sq_items,$db);

                        $contador_iva = 0;
                        $contador_descuento = 0;
                        $contador_total = 0;
                        $content='';
                        $i=true;
                        while ($myrow_i_ = DB_fetch_array($itemsResult)){
                            $iva_elemento = get_ivas($myrow_i_['taxcatid']);
                            $totalpre = (($myrow_i_['qty']*$myrow_i_['price']));
                            $total = $totalpre-($totalpre*$myrow_i_['discountpercent']);
                             $Impuesto='';
                            if(($myrow_i_['taxcatid']==8)||($myrow_i_['taxcatid']==11)){
                                $iva = 0;
                                if($myrow_i_['taxcatid']==8){
                                    $Impuesto='E';
                                }else if($myrow_i_['taxcatid']==11){
                                    $Impuesto='I';
                                }
                            }else if($myrow_i_['taxcatid']==6) {
                                $iva = $total*$iva_elemento[0];
                                $descuento = ($totalpre*$myrow_i_['discountpercent']);
                            }
                            $contador_iva = $contador_iva + $iva;
                            $contador_descuento = $contador_descuento + $descuento;
                            $contador_total = $contador_total + $total;
                            if($i){
                                $content.=$myrow_i_['qty'].'¬'.substr($myrow_i_['description'],0,33).' '.$Impuesto.' D'.number_format((100*$myrow_i_['discountpercent']), 2).'%¬'.number_format($myrow_i_['price'], 2).'¬'.number_format($total, 2);
                                $i=false;
                            }else{
                                $content.=']'.$myrow_i_['qty'].'¬'.substr($myrow_i_['description'],0,33).' '.$Impuesto.' D'.number_format((100*$myrow_i_['discountpercent']), 2).'%¬'.number_format($myrow_i_['price'], 2).'¬'.number_format($total, 2);
                            }
                        }
                        $content=$content;
            $sql_pagos = "Select tipo, monto FROM rh_pos_pagos where pedido = '".$OrderNo."' AND remision = '".$InvoiceNo."'";
            $PagosResult = DB_query($sql_pagos,$db);
            $pagado = 0;
            $montoFlag=true;
            $monto='';
            while ($myrow_p_ = DB_fetch_array($PagosResult)){
                if($montoFlag){
                  $monto.=$myrow_p_['tipo'].'='.$myrow_p_['monto'];
                } else{
                  $monto.=']'.$myrow_p_['tipo'].'='.$myrow_p_['monto'];
                }
                $pagado+=$myrow_p_['monto'];
                $montoFlag=false;
            }
        $numalet->setNumero($contador_total+$contador_iva);
        ?>
    <param name="articulos" value="<?php echo $content;?>">
    <param name="subtotal" value="<?php echo number_format($contador_total, 2);?>">
    <param name="iva16" value="<?php echo $iva_elemento[1]."T".number_format($contador_iva, 2);?>">
    <!--<param name="ieps" value="0=000.00">-->
    <param name="total" value="<?=number_format(($contador_total+$contador_iva), 2)?>">
    <param name="cantidadLetra" value="<?php echo codeWrap($numalet->letra().' M.N.',70,'<BR />');?>">
    <param name="formaPago" value="<?php echo $monto; ?>">
    <param name="cambio" value="<?=number_format((-1)*((($contador_total+$contador_iva)-($pagado))), 2)?>">
    <param name="barcode" value="<?php echo $barcode;?>">
  </applet>
<?
/*$content = ob_get_clean();
$pdf = new HTML2PDF('P','A4');//legal
$pdf->WriteHTML($content, "");
$pdf->Output("","");
die();
exit();
header("Content-type:application/pdf");
header("Content-Disposition:attachment;filename='downloaded.pdf'");
$pdf = new HTML2PDF('P','A4');//legal}
$pdf->WriteHTML($content, "");
$pdf->Output($_SESSION['reports_dir'] . '/PurchOrder.pdf', 'F');

/*ya empieza con la generacion del pdf*/
/*include_once('rh_pos_archivos/PHPJasperXML/fpdf/fpdf.php');
include_once("rh_pos_archivos/PHPJasperXML/PHPJasperXML.inc");

$xml =  simplexml_load_file("rh_pos_archivos/jrxml/remision.jrxml");

$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->debugsql = false;
$PHPJasperXML->arrayParameter = array("transno"=>$InvoiceNo);
$PHPJasperXML->xml_dismantle($xml);

$PHPJasperXML->transferDBtoArray($host,$dbuser,$dbpassword,$_SESSION['DatabaseName']);
$PHPJasperXML->outpage("I");
die();          */

?>