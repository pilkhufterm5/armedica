<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

set_time_limit(0);
error_reporting(E_ALL);

include_once('class/fpdf/fpdf.php');
include_once("class/PHPJasperXML.inc");
include_once("Numbers/Words.php");
include('setting.php');
//$db_=$db;

class PHPJasperXMLExt extends PHPJasperXML {
public function pageFooter() {
		if (isset ( $this->arraypageFooter )) {
			foreach ( $this->arraypageFooter as $out ) {
				switch ($out [hidden_type]) {
					case "field" :
						$this->display ( $out, $this->arrayPageSetting [pageHeight] - $this->arraypageFooter [0] [height] - $this->arrayPageSetting [bottomMargin], true );
						break;
					default :
						$this->display ( $out, $this->arrayPageSetting [pageHeight] - $this->arraypageFooter [0] [height] - $this->arrayPageSetting [bottomMargin], false );
						break;
				}
			}
		} else {
			//$this->lastPageFooter ();
		}
	}
	public function detail() {
		$field_pos_y = $this->arraydetail [0] [y_axis];
		$biggestY = 0;
		$checkpoint = $this->arraydetail [0] [y_axis];
		$tempY = $this->arraydetail [0] [y_axis];
		$LastPage = isset ( $this->arraylastPageFooter );
		$MAxYheight=0;
		foreach ( $this->arraysqltable as $row ) {

			if (isset ( $this->arraygroup ) && ($this->global_pointer > 0) && ($this->arraysqltable ["$this->global_pointer"] ["$this->group_pointer"] != $this->arraysqltable ["$this->global_pointer" - 1] ["$this->group_pointer"])) 			// check the group's groupExpression existed and same or not
			{
				$MAxYheight=0;
				$this->pageFooter ();
				$this->pdf->AddPage ();
				$this->background ();
				$this->pageHeader ();
				$checkpoint = $this->arraydetail [0] [y_axis];
				$biggestY = 0;
				$tempY = $this->arraydetail [0] [y_axis];
			}
			$MAxheight=0;
			foreach ( $this->arraydetail as $compare ) 			// this loop is to count possible biggest Y of the coming row
			{
				$MAxheight=max($MAxheight,$compare [height]);
				switch ($compare [hidden_type]) {
					case "field" :
						$txt = $this->analyse_expression ( $row ["$compare[txt]"] );
						if (isset ( $this->arraygroup ["$this->group_name"] [groupFooter] ) && (($checkpoint + ($compare [height] * $txt)) > ($this->arrayPageSetting [pageHeight] - $this->arraygroup ["$this->group_name"] [groupFooter] [0] [height] - $this->arrayPageSetting [bottomMargin]))) 						// check group footer existed or not
						{
							$MAxYheight=0;
							$this->pageFooter ();
							$this->pdf->AddPage ();
							$this->background ();
							$this->pageHeader ();
							$checkpoint = $this->arraydetail [0] [y_axis];
							$biggestY = 0;
							$tempY = $this->arraydetail [0] [y_axis];
						} elseif (isset ( $this->arraypageFooter ) && (($checkpoint + ($compare [height] * ($this->NbLines ( $compare [width], $txt )))) > ($this->arrayPageSetting [pageHeight] - $this->arraypageFooter [0] [height] - $this->arrayPageSetting [bottomMargin]))) 						// check pagefooter existed or not
						{
							$MAxYheight=0;
							$this->pageFooter ();
							$this->pdf->AddPage ();
							$this->background ();
							$this->pageHeader ();
							$checkpoint = $this->arraydetail [0] [y_axis];
							$biggestY = 0;
							$tempY = $this->arraydetail [0] [y_axis];
						} elseif (isset ( $this->arraylastPageFooter ) && (($checkpoint + ($compare [height] * ($this->NbLines ( $compare [width], $txt )))) > ($this->arrayPageSetting [pageHeight] - $this->arraylastPageFooter [0] [height] - $this->arrayPageSetting [bottomMargin]))) 						// check lastpagefooter existed or not
						{
						}

						if (($checkpoint + ($compare [height] * ($this->NbLines ( $compare [width], $txt )))) > $tempY) {
							$tempY = $checkpoint + ($compare [height] * ($this->NbLines ( $compare [width], $txt )));
						}
						break;
					case "relativebottomline" :
						break;
					case "report_count" :
						$this->report_count ++;
						break;
					default :
						$this->display ( $compare, $checkpoint );
						break;
				}
			}

			if ($checkpoint + $this->arraydetail [0] [height] > ($this->arrayPageSetting [pageHeight] - $this->arraypageFooter [0] [height] - $this->arrayPageSetting [bottomMargin])) 			// check the upcoming band is greater than footer position or not
			{
				$MAxYheight=0;
				$this->pageFooter ();
				$this->pdf->AddPage ();
				$this->background ();
				$this->pageHeader ();
				$checkpoint = $this->arraydetail [0] [y_axis];
				$biggestY = 0;
				$tempY = $this->arraydetail [0] [y_axis];
			}
			foreach ( $this->arraydetail as $out ) {
				switch ($out [hidden_type]) {
					case "field" :

						$this->prepare_print_array = array (
								"type" => "MultiCell",
								"width" => $out [width],
								"height" => $out [height],
								"txt" => $out [txt],
								"border" => $out [border],
								"align" => $out [align],
								"fill" => $out [fill],
								"hidden_type" => $out [hidden_type],
								"printWhenExpression" => $out [printWhenExpression],
								"soverflow" => $out [soverflow],
								"poverflow" => $out [poverflow],
								"link" => $out [link],
								"pattern" => $out [pattern]
						);
						$this->display ( $this->prepare_print_array, 0, true );

						if ($this->pdf->GetY () > $biggestY) {
							$biggestY = $this->pdf->GetY ();
						}
						break;
					case "relativebottomline" :
						// $this->relativebottomline($out,$tempY);
						$this->relativebottomline ( $out, $biggestY );
						break;
					default :

						$this->display ( $out, $checkpoint );

						// $checkpoint=$this->pdf->GetY();
						break;
				}
				$MAxYheight= max($MAxYheight, $this->pdf->GetY());
			}
			$checkpoint_old=$checkpoint;
			$this->pdf->SetY ( $biggestY );
			if ($biggestY > $checkpoint + $this->arraydetail [0] [height]) {
				$checkpoint = $biggestY;
			} elseif ($biggestY < $checkpoint + $this->arraydetail [0] [height]) {
				$checkpoint = $checkpoint + $this->arraydetail [0] [height];
			} else {
				$checkpoint = $biggestY;
			}

			// if(isset($this->arraygroup)){$this->global_pointer++;}
			$this->global_pointer ++;
		}
		$this->global_pointer --;
		if ($LastPage) {
			if (
				$this->arrayPageSetting [pageHeight]- $MAxYheight-$this->arraylastPageFooter [0] [height]<0
			) {
				$this->pdf->AddPage ();
				$this->background ();
				$this->pageHeader ();
				$checkpoint = $this->arraydetail [0] [y_axis];
				$biggestY = 0;
				$tempY = $this->arraydetail [0] [y_axis];
			}
			$this->lastPageFooter ();
		} elseif (! isset ( $this->arraylastPageFooter )) {
			$this->pageFooter ();
		}
	}
	public function transferDBtoArray($host,$user,$password,$db){
		$this->m=0;
		if(!$this->connect($host,$user,$password,$db))	//connect database
		{
		echo "Fail to connect database";
		exit(0);
		}
		if($this->debugsql==true){
		echo $this->sql;
		//die;
		}
		$i=0;
		$result = @mysql_query($this->sql); //query from db
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
		{
			$i++;

			EvaluarResultadoJasper($row);

			if($this->debugsql==true){
				echo $i;
				var_dump($row);echo '<br >';
			}
			foreach($this->arrayfield as $out)
			{
				$this->arraysqltable[$this->m]["$out"]=$row["$out"];
			}
			$this->m++;
		}
		$this->disconnect();	//close connection to db

		if(isset($this->arrayVariable))	//if self define variable existing, go to do the calculation
		{$this->variable_calculation($m);}
	}
}

function EvaluarResultadoJasper(&$row){
	global $db_;
	$db=$db_;

    if(!empty($row['FolioTitular'])){
        $GetCosto = "SELECT ((costo_total)/2) AS TOTAL FROM rh_titular WHERE folio = '{$row['FolioTitular']}' ";
        $_Result = DB_query($GetCosto, $db);
        $Result = DB_fetch_assoc($_Result);
        $Comprobante_Moneda = 'MXN';
        $Comprobante_NombreMoneda = "PESOS";

        $QuinAnio = explode("-", $row['sm_vigencia']);

        $Quincena = $QuinAnio[0]/2;
        $Quincena2 = explode(".", $Quincena);

        if(isset($Quincena2[1])){
            $NumeroQuincena = 1;
            $NumeroMes = $Quincena2[0] + 1;
        }else{
            $NumeroQuincena = 2;
            $NumeroMes = $Quincena2[0];
        }
        $date = date_create($QuinAnio[1] . '-01-01');
        $NumeroAnio = date_format($date, 'y');

        $row['NUMERO_ANIO'] = $NumeroAnio;
        $row['NUMERO_QUINCENA'] = $NumeroQuincena;
        $row['NUMERO_MES'] = $NumeroMes;

        $Meses = array(
            1 => "ENERO",
            2 => "FEBRERO",
            3 => "MARZO",
            4 => "ABRIL",
            5 => "MAYO",
            6 => "JUNIO",
            7 => "JULIO",
            8 => "AGOSTO",
            9 => "SEPTIEMBRE",
            10 => "OCTUBRE",
            11 => "NOVIEMBRE",
            11 => "DICIEMBRE"
            );
        $row['NUMERO_MES'] = $Meses[$row['NUMERO_MES']];

        $row['PAGO_QUINCENA'] = number_format($Result['TOTAL'],2);
        $row['PAGO_ANUAL'] = number_format(($Result['TOTAL']) * 24,2);

        $GetQtySocios = "SELECT COUNT(*) as QTY
            FROM custbranch
            WHERE folio = '" . $row['FolioTitular'] . "'
            AND movimientos_socios = 'Activo'
            AND rh_status_captura = 'Activo' ";
        $res_qty = DB_query($GetQtySocios, $db);
        $_GetQTY = DB_fetch_assoc($res_qty);
        $row['SOCIOS_ACTIVOS'] = $_GetQTY['QTY'];

        $Letras = new Numbers_Words();

        $tot = explode(".", number_format($Result['TOTAL']*24, 2, '.', ''));
        $Letra = Numbers_Words::toWords($tot[0], "es");
        if ($Comprobante_Moneda == 'MXN') {
            $Comprobante_Moneda = 'M.N.';
        }
        if ($tot[1] == 0) {
            $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . " 00/100 " . $Comprobante_Moneda;
        } else if (strlen($tot[1]) >= 2) {
            $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "/100 " . $Comprobante_Moneda;
        } else {
            $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "0/100 " . $Comprobante_Moneda;
        }
        $ConLetra = strtoupper($ConLetra);
        $row['IMPORTE_LETRA'] = $ConLetra;
        //FB::INFO($row,'________________ROW2');

    }

}

if($_POST['EMPRESA'] == 25){
    //SECCION 50
    if($_POST['JUBILADOS'] == 1){
        //JUBILADOS
        $FOLIO_FORMA = GetNextTransNo(20023,$db_);
        $xml =  simplexml_load_file("formasmaestros_jubilados50.jrxml");
    }else{
        //ACTIVOS
        $FOLIO_FORMA = GetNextTransNo(20022,$db_);
        $xml =  simplexml_load_file("formasmaestros50.jrxml");
    }

    $Empresa = 25;
}

if($_POST['EMPRESA'] == 38){
    //SECCION 21
    $FOLIO_FORMA = GetNextTransNo(20024,$db_);
    FB::INFO($FOLIO_FORMA,'FOLIO___21');
    $xml =  simplexml_load_file("formasmaestros21.jrxml");
    $Empresa = 38;
}

if (!empty($_POST['QUINCENA']) && !empty($_POST['ANIO'])) {
    $VIGENCIA = " AND cobranza.sm_vigencia = '{$_POST['QUINCENA']}-{$_POST['ANIO']}' ";
}

if(!empty($_POST['STATUS'])){
    $STATUS = " AND titular.movimientos_afiliacion = '{$_POST['STATUS']}' ";
}

if(!empty($_POST['FOLIOS'])){
    $FOLIOS = " AND titular.folio IN ({$_POST['FOLIOS']}) ";
}


function is_chrome(){
   return(eregi("chrome", $_SERVER['HTTP_USER_AGENT']));
}

$Chrome = false;
if(is_chrome()){
    //echo 'El navegador es Google Chrome.';
    $Chrome = true;
}


//$xml =  simplexml_load_file("formasmaestros.jrxml");
$rootpath=$filePath=realpath(dirname(__FILE__).'/../').'/';
$filePath = substr($filePath, 0, strrpos( $filePath, "/")) . "/companies/$db";
$LogoPath = $rootpath."PHPJasperXML";

$PHPJasperXML = new PHPJasperXMLExt();
$PHPJasperXML->debugsql=false;
$PHPJasperXML->arrayParameter=array(
        "rootpath"=>$rootpath,
        'filePath'=>$filePath,
        "LogoPath" => $LogoPath,
        "EmpresaID" => $Empresa,
        "STATUS" => $STATUS,
        "VIGENCIA" => $VIGENCIA,
        "FOLIOS" => $FOLIOS,
        "FOLIO_FORMA" => $FOLIO_FORMA
        );
$PHPJasperXML->xml_dismantle($xml);

// FB::INFO($PHPJasperXML,'__________JASPER');
// exit;
$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
//Jaime (agregado) si es CFD guardalo en un archivo temporal para luego enviarlo por email
if (isSet($isCfd)){
    $filePath = $fileBasePath . '/' . $cfdName;
    $PHPJasperXML->arrayPageSetting['name'] = $filePath;
    $PHPJasperXML->outpage("F", $filePath);
}else{
    if($Chrome){
        $PHPJasperXML->outpage("D");
    }else{
        $PHPJasperXML->outpage("I");
    }
}

$db=$db_;
