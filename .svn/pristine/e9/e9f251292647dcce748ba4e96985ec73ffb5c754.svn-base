<?php

$PageSecurity = 2;

include ('includes/session.inc');
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
	chdir(__FILE__);

	include_once('CFDI32.php');
	include('rh_cfdiFunctions32.php');

include('XMLFacturacionElectronica/utils/File.php');
require_once('Numbers/Words.php');
	chdir(__FILE__);

    function printDatosSatx($datosSat){
        $idWsCfd = $datosSat['idWsCfd'];
        $noCertificado = $datosSat['noCertificado'];
        $serie = $datosSat['serie'];
        $folio = $datosSat['folio'];
        $cadenaOriginal = $datosSat['cadenaOriginal'];
        $cadenaOriginal = str_replace(" ", "&nbsp;", $cadenaOriginal);
        //chars per line
        $cpl = 180;
        //cadena original length
        $col = strlen($cadenaOriginal);
        if($col>$cpl){
            $cadenaOriginalFormatted = '';
            for($i = 0; $i < $col; $i+=$cpl)
                $cadenaOriginalFormatted.=substr($cadenaOriginal, $i, $cpl) . "\n";
            $cadenaOriginal = $cadenaOriginalFormatted;
        }
        $selloDigital = $datosSat['selloDigital'];
        $totalEnLetra = $datosSat['totalEnLetra'];
        $noAprobacion = $datosSat['noAprobacion'];
        $anoAprobacion = $datosSat['anoAprobacion'];
        $addendaResponse = $datosSat['addendaResponse'];
        echo '<table><tr><td bgcolor="#bbbbbb"><b>ID CFD (WS):</b></td></tr><tr><td>' . $idWsCfd . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>No Aprobacion:</b></td></tr><tr><td>' . $noAprobacion . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>A̱o Aprobacion:</b></td></tr><tr><td>' . $anoAprobacion . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Serie:</b></td></tr><tr><td>' . $serie . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Folio:</b></td></tr><tr><td>' . $folio . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>No Certificado: </b></td></tr><tr><td>' . $noCertificado . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Cadena Original:</b></td></tr><tr><td>' . $cadenaOriginal . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Sello Digital:</b></td></tr><tr><td>' . $selloDigital . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Cantidad con Letra:</b></td></tr><tr><td>' . $totalEnLetra . '</td></tr>';
        echo '<tr><td bgcolor="#bbbbbb"><b>Respuesta de la addenda:</b></td></tr><tr><td>' . t($addendaResponse) . '</td></tr></table>';
    }
if(isset($_GET['id'])){

   {
    	$sql=" select count(*)t from rh_cfd__cfd where rh_cfd__cfd.serie is not null  and id_debtortrans=".$_GET['id'];
    	$rs = DB_query($sql,$db);
    	$rw = DB_fetch_assoc($rs);
    	if($rw['t']>0){
	        prnMsg( _('Imposible Recouperar XML, registro de recuperacion no encontrado o ya fue recuperado'), 'error' );
    	}else{


    		$idDebtortrans=$_GET['id'];
    		$sql=" select * from debtortrans where id=".$_GET['id'];
    		$rs = DB_query($sql,$db);
    		$rw = DB_fetch_assoc($rs);

    		$transno = $rw['transno'];
    		$type = $rw['type'];

    		$sql="update rh_transaddress, debtorsmaster set rh_transaddress.taxref=debtorsmaster.taxref where rh_transaddress.debtorno=debtorsmaster.debtorno and rh_transaddress.transno='".$transno."' and rh_transaddress.type='".$type."'";
    		$rss = DB_query($sql,$db);

    		$Campos=array('name','name2', 'address1','address2','address3','address4','address5','address6','address7','address8','address9','address10','rh_tel');
    		foreach($Campos as $valor){
    			$sql="update rh_transaddress, debtorsmaster set ".
      				"rh_transaddress.{$valor}=debtorsmaster.{$valor} ".
    				" where rh_transaddress.debtorno=debtorsmaster.debtorno ".
    				" and rh_transaddress.transno='".$transno."' and rh_transaddress.type='".$type."'".
    				" and rh_transaddress.{$valor}<>debtorsmaster.{$valor} ";
    			$rss = DB_query($sql,$db);
    		}


    		$sql="select * from stockmoves where type='".$type."' and transno='".$transno."' limit 1";
    		$rss = DB_query($sql,$db);
    		$rsm = DB_fetch_assoc($rss);
    		$Location=$rsm['loccode'];
    		$sql= 'select id_ws_csd, serie from rh_cfd__locations__systypes__ws_csd where id_locations = "'.$rsm['loccode'].'"  and id_systypes = '.$type;
    		$rscsd = DB_query($sql,$db);
    		$idCsdYSerie = DB_fetch_array($rscsd);

    		//$idCsdYSerie = explode('-', $_POST['selectIdFolio']);
    		$idCsd = $idCsdYSerie[0];
    		$serie = $idCsdYSerie[1];

    			$serie ='';// getSerieByBranch($Location,$type,$db);
    		$idXsd = '';
    		$xmlXsd = '';

    	 	$sql = "select not isnull(v.id_salesorders) is_transportista from debtortrans d join rh_vps__transportista v on d.order_ = v.id_salesorders and d.id = $idDebtortrans";
    		$result = DB_query($sql, $db);
    		$is_transportista = DB_num_rows($result);
    		//var_dump($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd,$_SESSION['metodoPago'],$_SESSION['cuentaPago']);
    		//$datosSat2 = cfdi($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd);
            $datosSat2 = cfd($db, $idDebtortrans, $transno, $type, $serie, $idCsd, $idXsd, $xmlXsd, $MetodoPago = 'NO Identificado', $ctaPago = 'NO Identificado');
    		printDatosSat($datosSat2);
    	}

    }
}else{
    echo "HERE2";
    prnMsg( _('Parametro faltante.'), 'error' );
}

include ('includes/footer.inc');
