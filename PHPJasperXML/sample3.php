<?php
global $xmlCancelado, $xmlCopia,$copia,$isCfdCancelado;
$xmlCancelado=<<<Cancelado
<image>
<reportElement x="4" y="6" width="548" height="674"/>
<imageExpression class="java.lang.String"><![CDATA[\$P{rootpath}+"/cancelada.jpg"]]></imageExpression>
</image>
Cancelado;
$xmlCopia=<<<Copia
<image>
<reportElement x="6" y="26" width="544" height="488"/>
<imageExpression class="java.lang.String"><![CDATA[\$P{rootpath}+"/copia.png"]]></imageExpression>
</image>
Copia;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
set_time_limit(0);

error_reporting(E_ALL);

include_once('class/fpdf/fpdf.php');
include_once("class/PHPJasperXML.inc");
include('setting.php');
$type=10;
$cer='';
$fecha='';
$xml_='';

$tipoCfd;
$isCfdCancelado = isSet($_GET['isCfdCancelado'])?true:false;

$isCartaPorte = (isSet($_GET['isCartaPorte']) && $_GET['isCartaPorte'])?true:false;
$isTransportista = (isSet($_GET['isTransportista']) && $_GET['isTransportista'])?true:false;
$isNotaDeCredito = (isSet($_GET['isNotaDeCredito']) && $_GET['isNotaDeCredito'])?true:false;
$isNotaDeCargo = (isSet($_GET['isNotaDeCargo']) && $_GET['isNotaDeCargo'])?true:false;
if(!isset($LBIvaRet)){
    $LBIvaRet='RET. IVA';
}
if(!isset($LBIsrRet)){
    $LBIsrRet='';
}
if(!isset($ISRRET)){
    $ISRRET='';
}
$copia=$_GET['copia'];

if($isCartaPorte){
    $tipoCfd = 'cartaPorte';
}

if($isTransportista){
    $tipoCfd = 'transportista';
}
if($isNotaDeCredito){
    $tipoCfd = 'notaDeCredito';
    $type=11;
}
if($isNotaDeCargo){
    $tipoCfd = 'notaDeCargo';
}
if(!function_exists('rh_GetjasperXML')){
	function rh_GetjasperXML($File){
		$Empresa=explode('_',$_SESSION['DatabaseName']);
		array_pop($Empresa);// 001
		array_pop($Empresa);// cfdi
		$Empresa='_'.implode('_',$Empresa);
		if(is_file(dirname(__FILE__).'/'.$File)){
        	$fil=str_replace('.jrxml',$Empresa.'.jrxml',$File);
            if(is_file(dirname(__FILE__).'/'.$fil)){
            	$File=$fil;
			}else {
				$fil=str_replace('.jrxml','_CFDI'.'.jrxml',$File);
                if(is_file(dirname(__FILE__).'/'.$fil))
                	$File=$fil;
         	}
		}
		if(strpos($File,'CFDI.')){
			$fil=str_replace('.jrxml','_garmedica.jrxml',$File);
			if(is_file(dirname(__FILE__).'/'.$fil))
                        $File=$fil;
                
		}

		$Archivo=file_get_contents($File);
		return rh_simplexml_load_string($Archivo);
	}
	function rh_simplexml_load_string($Archivo){
		global $xmlCancelado, $xmlCopia,$copia,$isCfdCancelado;

		$dato='';
		if($isCfdCancelado){
			$dato=$xmlCancelado;
		}else
			if($copia=='si'){
				$dato=$xmlCopia;
			}
		$inicio=strpos($Archivo,'>',strpos($Archivo,'<band',strpos($Archivo,'<pageHeader')))+1;
		$Archivo=substr($Archivo,0,$inicio).$dato.substr($Archivo,$inicio);
		return $Archivo;
	}
	function rh_simplexml_load_file($File){
		return simplexml_load_string(rh_GetjasperXML($File));
	}
}
IF(!isset($_GET['afil']))$_GET['afil']=true;
{

    if(isset($_GET['ERPConection'])){
        $db_ = $_GET['ERPConection'];
        //FB::INFO($ERPConection,'___________________________DBCON');
    }
    if(!function_exists('getAttr')){
    function getAttr($xml,$attr,$start=0){
            $p1=strpos($xml,'"',strpos($xml,$attr,$start))+1;
            $p2=strpos($xml,'"',$p1);
            $cer=substr($xml,$p1,$p2-$p1);
            return $cer;
        }
    }

    $transno=(int)$_GET['transno'];
    $SQL="select * from config where confname='CFDIVersion'";
    $con=DB_query($SQL,$db_,$ErrMsg,$DbgMsg);
    $ver=DB_fetch_assoc($con);



    $CFDIVersion=$ver['confvalue'];
    $SQL="select uuid, xml,serie,folio,addenda_response from rh_cfd__cfd where fk_transno=$transno and id_systypes=$type";
    $con=DB_query($SQL,$db_,$ErrMsg,$DbgMsg);
    $ver=DB_fetch_assoc($con);

    // Bandera agregada por Daniel Villarreal el 23 de enero del 2016
  	$solo1concepto = $ver['addenda_response'];


    {//Crea Codigo PayNet
    $SQLT="SELECT debtorno,
        debtortrans.ovamount+debtortrans.ovgst+ debtortrans.ovfreight- debtortrans.ovdiscount as gtotal, rh_status
        FROM debtortrans
        WHERE transno=$transno and type=10";
    /*Si es Nota de Credito*/
    if($_GET['isNotaDeCredito'] == true){
        $SQLT="SELECT debtorno,
            debtortrans.ovamount+debtortrans.ovgst+ debtortrans.ovfreight- debtortrans.ovdiscount as gtotal, rh_status
            FROM debtortrans
            WHERE transno=$transno and type=11";
    }
    $_Dtrans=DB_query($SQLT,$db_,$ErrMsg,$DbgMsg);
    $GTotal=DB_fetch_assoc($_Dtrans);

    $isCfdCancelado=$GTotal['rh_status']=='C';


    if($ver['uuid']==''){
        $ver['uuid']=$ver['folio'];
    }
    $_GET['Invoice'] = $ver['serie'] . $ver['folio'];
    $UUID =
    $_GET['UUID'] = $ver['uuid'];
    $_GET['Amount'] = $GTotal['gtotal'];

	$CodigoBarras = "/tmp/" . $UUID;

    $_2DebtorNo = $GTotal['debtorno'];

    //echo "class/BCGen.php";
    //exit;
    include (dirname(__FILE__)."/class/BCGen.php");

//echo "ok2"; exit;
    }

    if($ver['uuid']==''){
        $CFDIVersion=22;
    }else{
        $xml_=$xml=$ver['xml'];
        $p1=strpos($xml,'"',strpos($xml,'noCertificado',0))+1;
        $p2=strpos($xml,'"',$p1);
        $cer=substr($xml,$p1,$p2-$p1);

        $p1=strpos($xml,'"',strpos($xml,'fecha',0))+1;
        $p2=strpos($xml,'"',$p1);
        $fecha=  str_replace("T"," ",substr($xml,$p1,$p2-$p1));
    }
}

{
    ob_clean();
    $LBIsrRet=$ISRRET='';
switch($tipoCfd){
    case 'cartaPorte':
            $xml =  rh_simplexml_load_file("rh_cartaPorte_clausulaImagen.jrxml");
        break;
    case 'transportista':
            $xml =  rh_simplexml_load_file("rh_FE_transportista.jrxml");
        break;
    case 'notaDeCredito':
            $xml =  rh_simplexml_load_file("rh_FE_notaDeCredito_CFDI.jrxml");
        break;
    case 'notaDeCargo':
            $xml =  rh_simplexml_load_file("rh_NCargo_CFDI.jrxml");
        break;
    default:
            //**** Este codigo es para Recibos de Arrendamiento :D ***//
//             if ($_SESSION['DocType']==1){
//             	if(($_SESSION['DatabaseName'] == 'samplast_cfdi_001')){
//                 $_GET['transno']=(int)$_GET['transno'];
//                 $LBIsrRet=$ISRRET='';
//                 $sql="select if(taxamount<=0,'Retencion','Traslado') as impuesto,description,taxamount*debtortrans.rate as taxamount from debtortranstaxes
//                 join taxauthorities on taxauthorities.taxid = debtortranstaxes.taxauthid
//                 join debtortrans on debtortrans.id = debtortranstaxes.debtortransid where debtortrans.transno={$_GET['transno']} and debtortrans.type=10;";
//                 $Conn=mysql_connect($server,$user,$pass);
//                 mysql_select_db($db);
//                 $queryRes=mysql_query($sql,$Conn);
//                 if(($_SESSION['DatabaseName'] == 'samplast_cfdi_001')){{

//                             $Archivo=rh_GetjasperXML("rh_FE_R_samplast.jrxml");
//                             while($Row=mysql_fetch_array($queryRes)){
//                                 if($Row['impuesto']=='Traslado' && $Row['description']=='IVA'){
//                                     $IVATRAS=number_format($Row['taxamount'],4,'.','');
//                                 }else
//                                 if($Row['impuesto']=='Retencion' && $Row['description']=='ISR'){
//                                         $ISRRET=number_format($Row['taxamount'],4,'.','');
//                                         $LBIsrRet=$Row['description'];
//                                 }else
//                                     if($Row['impuesto']=='Retencion'){
//                                         $IVARET=number_format($Row['taxamount'],4,'.','');
//                                         if($Row['description']!='IVA')
//                                             $LBIvaRet=$Row['description'];
//                                 }
//                             }
//                             $Archivo=str_replace('<![CDATA[$F{IVATRAS}]]>','<![CDATA["'.$IVATRAS.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$F{IVARET}]]>','<![CDATA["'.$IVARET.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$F{ISRRET}]]>','<![CDATA["'.$ISRRET.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA["RET. IVA"]]>','<![CDATA["'.$LBIvaRet.'"]]>',$Archivo);

//                             $Archivo=str_replace('<![CDATA["ISR"]]>','<![CDATA["'.$LBIsrRet.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$P{ISR}]]>','<![CDATA["'.$ISRRET.'"]]>',$Archivo);

//                             $xml =  simplexml_load_string($Archivo);
//                                 }
//                                 }
//                 }else{
//                            $Archivo=rh_GetjasperXML("rh_FE_R.jrxml");

//                             while($Row=mysql_fetch_array($queryRes)){
//                                 if($Row['impuesto']=='Traslado' && $Row['description']=='IVA'){
//                                     $IVATRAS=number_format($Row['taxamount'],4,'.','');
//                                 }else
//                                 if($Row['impuesto']=='Retencion' && $Row['description']=='ISR'){
//                                     $ISRRET=number_format($Row['taxamount'],4,'.','');
//                                     $LBIsrRet=$Row['description'];
//                                 }else
//                                     if($Row['impuesto']=='Retencion'){
//                                         $IVARET=number_format($Row['taxamount'],4,'.','');
//                                         if($Row['description']!='IVA')
//                                             $LBIvaRet=$Row['description'];
//                                     }
//                             }
//                             mysql_select_db($db);
//                             $queryRes=mysql_query($sqlCSD,$Conn);
//                             if($Row=mysql_fetch_array($queryRes)){
//                                 $CSD = $Row['id_ws_csd'];
//                             }
//                             $Archivo=str_replace('<![CDATA[$F{CSD}]]>','<![CDATA["'.$CSD.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$F{IVATRAS}]]>','<![CDATA["'.$IVATRAS.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$F{IVARET}]]>','<![CDATA["'.$IVARET.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$F{ISRRET}]]>','<![CDATA["'.$ISRRET.'"]]>',$Archivo);

//                             $Archivo=str_replace('<![CDATA["ISR"]]>','<![CDATA["'.$LBIsrRet.'"]]>',$Archivo);
//                             $Archivo=str_replace('<![CDATA[$P{ISR}]]>','<![CDATA["'.$ISRRET.'"]]>',$Archivo);
//                             $xml =  simplexml_load_string($Archivo);
//                 }
//             }else
            {
                    //Tamplates para Razon Social ARMedica_erp_001
                    $InvoiceTemplateName = 'rh_FE_CFDI_garmedica_anteriordomi.jrxml';

                    // En caso de que la variable $solo1concepto sea 1, quiere decir que solo debe mostrar 1 concepto, cargamos otro jasper.
                    // Daniel Villarreal, 23 de enero del 2016
                    if($solo1concepto == 1){ $InvoiceTemplateName = 'rh_FE_CFDI_1Concepto.jrxml'; }
                    $xml =  rh_simplexml_load_file($InvoiceTemplateName);
            }
        break;
}

$transno=$_GET['transno'];
$rootpath=$filePath=realpath(dirname(__FILE__).'/../').'/';

$filePath = substr($filePath, 0, strrpos( $filePath, "/")) . "/companies/$db";
//$rootpath=dirname($_SERVER['SCRIPT_FILENAME']);

$QRpath = substr($rootpath, 0, strrpos( $rootpath, "/")) . "/QRCode/cache";

$BCpath = "/tmp";
// $CodigoBarras = "/tmp/" . $UUID;

//if(!is_file($QRpath.'/'.$UUID.'.jpg'))
{
    include_once('CFDI32.php');
    if($xml_!=''){
        $Comprobante_total=getAttr($xml_,'total');
        $Emisor_RFC=getAttr($xml_,'rfc', strpos($xml_,'cfdi:Emisor',0));
        $Receptor_rfc=getAttr($xml_,'rfc', strpos($xml_,'cfdi:Receptor',0));
    }else{
        $sqlE = "select coyname Emisor_Nombre,gstno Emisor_RFC from companies limit 1";
        $resultE = DB_query($sqlE,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('Aun no esta configurada la informacion de la empresa', 1);
        }
        $row = DB_fetch_array($resultE);
        $Emisor_RFC = $row['Emisor_RFC'];



        $sqlR = "select rh_transaddress.currcode currencyCode, cast(debtortrans.ovdiscount as decimal(10,2)) Comprobante_descuento, cast((((debtortrans.ovamount-debtortrans.ovdiscount)+debtortrans.ovgst)/1) as decimal(10,2)) Comprobante_total, debtortrans.rh_createdate Comprobante_fecha, cast((debtortrans.ovamount/1) as decimal(10,2)) Comprobante_subtotal, cast((debtortrans.ovgst/1) as decimal(10,2)) Traslado_importe, rh_transaddress.taxref Receptor_rfc, rh_transaddress.name Receptor_nombre, rh_transaddress.address1 Receptor_calle, rh_transaddress.address2 Receptor_noExterior, rh_transaddress.address3 Receptor_noInterior, rh_transaddress.address4 Receptor_colonia, rh_transaddress.address5 Receptor_localidad, rh_transaddress.address6 Receptor_referencia, rh_transaddress.address7 Receptor_municipio,  rh_transaddress.address8 Receptor_estado,  rh_transaddress.address9 Receptor_pais, rh_transaddress.address10 Receptor_codigoPostal FROM debtortrans, rh_transaddress WHERE rh_transaddress.type = $type AND debtortrans.type=$type and rh_transaddress.transno = debtortrans.transno AND debtortrans.transno=$transno limit 1";
        $resultR = DB_query($sqlR,$db,'','',false,false);
        if(DB_error_no($db)) {
            throw new Exception('No se pudo obtener informacion sobre el Comprobante y los Impuestos para la Factura Electronica', 1);
        }
        $row2 = DB_fetch_array($resultR);
        $Receptor_rfc = $row2['Receptor_rfc'];
        $Comprobante_total = $row2['Comprobante_total'];
    }
    $re=$Emisor_RFC;
    $rr=$Receptor_rfc;
    $QRTotal=$Comprobante_total;
    //function getQRCode($re='',$rr='',$Total='0.00',$UUID='')
    {
        $format = 'J';
        $size = '10';
        if($Total<0){
            $Total=$Total*-1;
        }
        $QRdata = "?re=".$re."&rr=".$rr."&tt=".number_format($QRTotal,6,'.','')."&id=".$UUID;

        if(is_file($QRpath.'/'.$UUID.'.jpg')) unlink($QRpath.'/'.$UUID.'.jpg');
        qr_code($QRdata, "M", $format, $size,7,$UUID);
    }
    //getQRCode($Emisor_RFC,$Receptor_rfc,$Comprobante_total);
}

    /*Datos para Pie de Pagina de la Factura de Afiliacion*/
    if($_GET['afil'] == true){
        $GetFolio = "select titular.folio,
                           cobranza.cobrador,
                           cobranza.dias_cobro,
                           cobranza.dias_cobro_dia,
                           cobranza.cobro_datefrom,
                           cobranza.cobro_dateto,
                           cobranza.dias_revision,
                           cobranza.dias_revision_dia,
                           cobranza.revision_datefrom,
                           cobranza.revision_dateto,
                           cobranza.rh_tel,
                           cobranza.address1,
                           cobranza.address2,
                           cobranza.address3,
                           cobranza.address4,
                           cobranza.address5,
                           cobranza.address6,
                           cobranza.address7,
                           cobranza.address8,
                           cobranza.address9,
                           cobranza.address10,
                           cobranza.cuadrante1,
                           cobranza.cuadrante2,
                           cobranza.cuadrante3,
                           empresas.empresa
                    from rh_titular as titular
                        left join rh_cobranza cobranza on titular.folio = cobranza.folio
                        left join rh_empresas empresas on cobranza.empresa = empresas.id
                        where titular.debtorno = '" .$_2DebtorNo . "'
       ";
        $RGetFolio = DB_query($GetFolio,$db_);
        $DF = DB_fetch_assoc($RGetFolio);
        /*
        Empresa Comercial:
        FP-24 C-36 DC-5(-)
        DR-0(-) TEL: 8347.0169. FOLIO INT: 92920

        cobro_datefrom
        cobro_dateto

        revision_datefrom
        revision_dateto

        Empresa Comercial:SCHOELLER BLECKMANN DE MEXICO SA DE C.V
        FP-20 C-58 DC-Viernes(02:30-05:00)
        DR-Lunes(09:00-04:30) TEL: 1344.3343. FOLIO INT: 90453

        */

        $DiaSemana = array(
            0 => 'DOMINGO',
            1 => 'LUNES',
            2 => 'MARTES',
            3 => 'MIERCOLES',
            4 => 'JUEVES',
            5 => 'VIERNES',
            6 => 'SABADO'
        );

        if($DF['dias_cobro'] == 'Por Dia'){
            $DF['dias_cobro_dia'] = $DiaSemana[$DF['dias_cobro_dia']];
        }

        if($DF['dias_revision'] == 'Por Dia'){
            $DF['dias_revision_dia'] = $DiaSemana[$DF['dias_revision_dia']];
        }
        $DF['cuadrante2'] = strtoupper($DF['cuadrante2']);
        $CUADRANTES = "{$DF['cuadrante1']}-{$DF['cuadrante2']}-{$DF['cuadrante3']}";
        $DireccionCobro = "{$DF['address1']} {$DF['address2']} {$DF['address4']} {$DF['address7']} {$DF['address8']} {$DF['address10']} ";
        $ECDatos = " FP- C-{$DF['cobrador']} DC-{$DF['dias_cobro_dia']}({$DF['cobro_datefrom']}-{$DF['cobro_dateto']}) \n DR-{$DF['dias_revision_dia']}({$DF['revision_datefrom']}-{$DF['revision_dateto']}) TEL: {$DF['rh_tel']} ";
    }else{
        $DF['folio'] = $_2DebtorNo;
    }

$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->debugsql=false;
$PHPJasperXML->arrayParameter=array(
        "transno"=>$transno,
        "rootpath"=>$rootpath,
        'qrpath'=>$QRpath,
        'IVARET'=>$IVARET,
        'LBIvaRet'=>$LBIvaRet,
        'ISRRET'=>$ISRRET,
        'CSDFuente'=>$cer,
        'CFDIFecha'=>$fecha,
        '$bcpath'=>$BCpath,
        'filePath'=>$filePath,
        'Folio' => $DF['folio'],
        'ECDatos' => $ECDatos,
        'AfilEmpresa' => $DF['empresa'],
        'CodigoBarras' => $CodigoBarras,
        'DireccionCobro' => $DireccionCobro,
        'CUADRANTES' => $CUADRANTES
        );
$PHPJasperXML->xml_dismantle($xml);


$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
//Jaime (agregado) si es CFD guardalo en un archivo temporal para luego enviarlo por email
if (isSet($isCfd)){
    $filePath = $fileBasePath . '/' . $cfdName;
    $PHPJasperXML->arrayPageSetting['name'] = $filePath;
    $PHPJasperXML->outpage("F", $filePath);
}else
//Termina Jaime (agregado) si es CFD guardalo en un archivo temporal para luego enviarlo por email
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file
}
$db=$db_;
