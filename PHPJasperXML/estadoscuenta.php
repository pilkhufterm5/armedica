<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

set_time_limit(0);
error_reporting(E_ALL);

include_once ('class/fpdf/fpdf.php');
include_once ("class/PHPJasperXML.inc");
include_once ("Numbers/Words.php");
include ('setting.php');

//$db_=$db;

class PHPJasperXMLExt extends PHPJasperXML
{
    public function pageFooter() {
        if (isset($this->arraypageFooter)) {
            foreach ($this->arraypageFooter as $out) {
                switch ($out[hidden_type]) {
                    case "field":
                        $this->display($out, $this->arrayPageSetting[pageHeight] - $this->arraypageFooter[0][height] - $this->arrayPageSetting[bottomMargin], true);
                        break;
                    default:
                        $this->display($out, $this->arrayPageSetting[pageHeight] - $this->arraypageFooter[0][height] - $this->arrayPageSetting[bottomMargin], false);
                        break;
                }
            }
        } else {
            //$this->lastPageFooter ();
        }
    }

    public function detail() {
        $field_pos_y = $this->arraydetail[0][y_axis];
        $biggestY = 0;
        $checkpoint = $this->arraydetail[0][y_axis];
        $tempY = $this->arraydetail[0][y_axis];
        $LastPage = isset($this->arraylastPageFooter);
        $MAxYheight = 0;
        foreach ($this->arraysqltable as $row) {

            if (EvaluarSaltoHoja($row)||isset($this->arraygroup) && ($this->global_pointer > 0) && ($this->arraysqltable["$this->global_pointer"]["$this->group_pointer"] != $this->arraysqltable["$this->global_pointer" - 1]["$this->group_pointer"]))
             // check the group's groupExpression existed and same or not
            {
                $MAxYheight = 0;
                $this->pageFooter();
                $this->pdf->AddPage();
                $this->background();
                $this->pageHeader();
                $checkpoint = $this->arraydetail[0][y_axis];
                $biggestY = 0;
                $tempY = $this->arraydetail[0][y_axis];
            }
            $MAxheight = 0;
            foreach ($this->arraydetail as $compare)
             // this loop is to count possible biggest Y of the coming row
            {
                $MAxheight = max($MAxheight, $compare[height]);
                switch ($compare[hidden_type]) {
                    case "field":
                        $txt = $this->analyse_expression($row["$compare[txt]"]);
                        if (isset($this->arraygroup["$this->group_name"][groupFooter]) && (($checkpoint + ($compare[height] * $txt)) > ($this->arrayPageSetting[pageHeight] - $this->arraygroup["$this->group_name"][groupFooter][0][height] - $this->arrayPageSetting[bottomMargin])))
                         // check group footer existed or not
                        {
                            $MAxYheight = 0;
                            $this->pageFooter();
                            $this->pdf->AddPage();
                            $this->background();
                            $this->pageHeader();
                            $checkpoint = $this->arraydetail[0][y_axis];
                            $biggestY = 0;
                            $tempY = $this->arraydetail[0][y_axis];
                        } elseif (isset($this->arraypageFooter) && (($checkpoint + ($compare[height] * ($this->NbLines($compare[width], $txt)))) > ($this->arrayPageSetting[pageHeight] - $this->arraypageFooter[0][height] - $this->arrayPageSetting[bottomMargin])))
                         // check pagefooter existed or not
                        {
                            $MAxYheight = 0;
                            $this->pageFooter();
                            $this->pdf->AddPage();
                            $this->background();
                            $this->pageHeader();
                            $checkpoint = $this->arraydetail[0][y_axis];
                            $biggestY = 0;
                            $tempY = $this->arraydetail[0][y_axis];
                        } elseif (isset($this->arraylastPageFooter) && (($checkpoint + ($compare[height] * ($this->NbLines($compare[width], $txt)))) > ($this->arrayPageSetting[pageHeight] - $this->arraylastPageFooter[0][height] - $this->arrayPageSetting[bottomMargin])))
                         // check lastpagefooter existed or not
                        {
                        }

                        if (($checkpoint + ($compare[height] * ($this->NbLines($compare[width], $txt)))) > $tempY) {
                            $tempY = $checkpoint + ($compare[height] * ($this->NbLines($compare[width], $txt)));
                        }
                        break;
                    case "relativebottomline":
                        break;

                    case "report_count":
                        $this->report_count++;
                        break;
                    default:
                        $this->display($compare, $checkpoint);
                        break;
                }
            }

            if ($checkpoint + $this->arraydetail[0][height] > ($this->arrayPageSetting[pageHeight] - $this->arraypageFooter[0][height] - $this->arrayPageSetting[bottomMargin]))
             // check the upcoming band is greater than footer position or not
            {
                $MAxYheight = 0;
                $this->pageFooter();
                $this->pdf->AddPage();
                $this->background();
                $this->pageHeader();
                $checkpoint = $this->arraydetail[0][y_axis];
                $biggestY = 0;
                $tempY = $this->arraydetail[0][y_axis];
            }
            foreach ($this->arraydetail as $out) {
                switch ($out[hidden_type]) {
                    case "field":

                        $this->prepare_print_array = array(
                            "type" => "MultiCell",
                            "width" => $out[width],
                            "height" => $out[height],
                            "txt" => $out[txt],
                            "border" => $out[border],
                            "align" => $out[align],
                            "fill" => $out[fill],
                            "hidden_type" => $out[hidden_type],
                            "printWhenExpression" => $out[printWhenExpression],
                            "soverflow" => $out[soverflow],
                            "poverflow" => $out[poverflow],
                            "link" => $out[link],
                            "pattern" => $out[pattern]
                        );
                        $this->display($this->prepare_print_array, 0, true);

                        if ($this->pdf->GetY() > $biggestY) {
                            $biggestY = $this->pdf->GetY();
                        }
                        break;

                    case "relativebottomline":

                        // $this->relativebottomline($out,$tempY);
                        $this->relativebottomline($out, $biggestY);
                        break;

                    default:

                        $this->display($out, $checkpoint);

                        // $checkpoint=$this->pdf->GetY();
                        break;
                }
                $MAxYheight = max($MAxYheight, $this->pdf->GetY());
            }
            $checkpoint_old = $checkpoint;
            $this->pdf->SetY($biggestY);
            if ($biggestY > $checkpoint + $this->arraydetail[0][height]) {
                $checkpoint = $biggestY;
            } elseif ($biggestY < $checkpoint + $this->arraydetail[0][height]) {
                $checkpoint = $checkpoint + $this->arraydetail[0][height];
            } else {
                $checkpoint = $biggestY;
            }

            // if(isset($this->arraygroup)){$this->global_pointer++;}
            $this->global_pointer++;
        }
        $this->global_pointer--;
        if ($LastPage) {
            if ($this->arrayPageSetting[pageHeight] - $MAxYheight - $this->arraylastPageFooter[0][height] < 0) {
                $this->pdf->AddPage();
                $this->background();
                $this->pageHeader();
                $checkpoint = $this->arraydetail[0][y_axis];
                $biggestY = 0;
                $tempY = $this->arraydetail[0][y_axis];
            }
            $this->lastPageFooter();
        } elseif (!isset($this->arraylastPageFooter)) {
            $this->pageFooter();
        }
    }

    public function transferDBtoArray($host, $user, $password, $db) {
        $this->m = 0;
        if (!$this->connect($host, $user, $password, $db))
         //connect database
        {
            echo "Fail to connect database";
            exit(0);
        }
        if ($this->debugsql == true) {
            echo $this->sql;
            //die;
        }
        $i = 0;
        $result = @mysql_query($this->sql);
         //query from db
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            $i++;

            EvaluarResultadoJasper($row);

            if ($this->debugsql == true) {
                echo '<br >';
                echo $i;
                echo "<pre>";
                var_dump($row);
                echo "</pre>";
                echo '<br >';
            }
            foreach ($this->arrayfield as $out) {
                $this->arraysqltable[$this->m]["$out"] = $row["$out"];
            }
            $this->m++;
        }
        $this->disconnect();
         //close connection to db

        if (isset($this->arrayVariable))
         //if self define variable existing, go to do the calculation
        {
            $this->variable_calculation($m);
        }
    }

    public function processArray($Tables=array()){
    	$this->m = 0;
        $i = 0;
        foreach ($Tables as $row) {
            $i++;
            EvaluarResultadoJasper($row);
            if ($this->debugsql == true) {
                echo $i;
                echo '<br >';
                echo "<pre>";
                var_dump($row);
                echo "</pre>";
                echo '<br >';
            }
            foreach ($this->arrayfield as $out) {
                $this->arraysqltable[$this->m]["$out"] = $row["$out"];
            }
            $this->m++;
        }
        if (isset($this->arrayVariable)){
            $this->variable_calculation($m);
        }
    }
}

function EvaluarSaltoHoja(&$row){
	global $db_, $DebtorNo, $SALDOTOtal;
    $db = $db_;
    if(!isset($DebtorNo)){
        $DebtorNo = $row['debtorno'];
        $SALDOTOtal = 0;
    }
    if($DebtorNo != $row['debtorno']){
        $DebtorNo = $row['debtorno'];
        $row['SALDO_TOTAL'] = $SALDOTOtal;
        $SALDOTOtal = 0;
        return true;
    }
    $SALDOTOtal = $SALDOTOtal + $row['alloc'];
    $row['SALDO_TOTAL'] = $SALDOTOtal;

	return false;
}

function EvaluarResultadoJasper(&$row) {
    global $db_, $DebtorNo1, $SALDOTOtal;
    $db = $db_;

    if(!isset($DebtorNo1)){
        $DebtorNo1 = $row['debtorno'];
        $SALDOTOtal = 0;
    }

    if($DebtorNo1 != $row['debtorno']){
        $DebtorNo1 = $row['debtorno'];
        $row['SALDO_TOTAL'] = $SALDOTOtal;
        $SALDOTOtal = 0;
        return true;
    }

    $SALDOTOtal = $SALDOTOtal + $row['alloc'];
    $row['SALDO_TOTAL'] = $SALDOTOtal;
    $row['SALDO_TOTAL'] = "$" . number_format($row['SALDO_TOTAL'],2);

    $row['ImporteFactura'] = "$" . number_format($row['ImporteFactura'],2);
    $row['ImporteDeposito'] = "$" . number_format($row['ImporteDeposito'],2);
    $row['ImporteNota'] = "$" . number_format($row['ImporteNota'],2);
    $row['alloc'] = "$" . number_format($row['alloc'],2);


    switch ($row['rh_status']) {
        case 'N':
            $row['rh_status'] = 'PENDIENTE';
            if($row['settled'] == 1){
                $row['rh_status'] = 'PAGADO';
            }
            break;
        case 'C':
            $row['rh_status'] = 'CANCELADO';
            break;
        default:
            # code...
            break;
    }

    if (!empty($row['debtorno'])) {

        /**********************************Se comentarizo para poder imprimir el estado de cuenta Angeles Perez 2016-06-02*********************************************************************************************************************/
       /* include_once("modulos/protected/models/SQLServerWS.php");
        include_once("modulos/protected/models/xmlToArrayParser.php");

        $CECOMWS = new SQLServerWS();

        $GetDay = explode('-', date('Y-m-d'));
        $LastDay = date("d", (mktime(0, 0, 0, $GetDay['1'] + 1, 1, $GetDay['0']) - 1));
        $FirstDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], 01, $GetDay['0']));
        $LastDate = date('Y-m-d', mktime(0, 0, 0, $GetDay['1'], $LastDay, $GetDay['0']));

        $Folio = $GetAfilData['AfilFolio'];

        $XmlCountDateRange = ("<row>
                                <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$row['FolioTitular']}' AND FECHA BETWEEN '{$FirstDate}' AND '{$LastDate}' \">
                                    <folio weberp='folio' prime='1' >{$row['FolioTitular']}</folio>
                                </CCM_Despachos>
                            </row>");

        $XmlCountAll = ("<row>
                            <CCM_Despachos weberp='rh_titular' where=\"FOLIO = '{$row['FolioTitular']}'\">
                                <folio weberp='folio' prime='1' >{$row['FolioTitular']}</folio>
                            </CCM_Despachos>
                        </row>");
        $url = "http://104.130.129.147/wsceom2/WebService.asmx?wsdl";
        $CountAction = "http://tempuri.org/selectcont";

        $CountByDateRange = $CECOMWS->GetCountWS($XmlCountDateRange, $url, array('SOAPAction: ' . $CountAction))->saveXML();
        $CountAll = $CECOMWS->GetCountWS($XmlCountAll, $url, array('SOAPAction: ' . $CountAction))->saveXML();

        $ObjByDateRange = new xmlToArrayParser($CountByDateRange);
        if ($ObjByDateRange->parse_error) {

        } else {
            $GetThisMonth = $ObjByDateRange->array;
            $GetThisMonth = $GetThisMonth['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
        }
        $row['SERVICIO_ULTIMO_MES'] = $GetThisMonth;

        $ObjAll = new xmlToArrayParser($CountAll);
        if ($ObjAll->parse_error) {

        } else {
            $GetAll = $ObjAll->array;
            $GetAll = $GetAll['soap:Envelope']['soap:Body']['selectcontResponse']['selectcontResult'];
        }
        $row['SERVICIO_TOTAL_ACUM'] = $GetAll;*/
        /*************************************************Termina******************************************************************************************************/


        // $Letras = new Numbers_Words();

        // $tot = explode(".", number_format($Result['TOTAL'] * 24, 2, '.', ''));
        // $Letra = Numbers_Words::toWords($tot[0], "es");
        // if ($Comprobante_Moneda == 'MXN') {
        //     $Comprobante_Moneda = 'M.N.';
        // }
        // if ($tot[1] == 0) {
        //     $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . " 00/100 " . $Comprobante_Moneda;
        // } else if (strlen($tot[1]) >= 2) {
        //     $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "/100 " . $Comprobante_Moneda;
        // } else {
        //     $ConLetra = $Letra . ' ' . $Comprobante_NombreMoneda . ' ' . $tot[1] . "0/100 " . $Comprobante_Moneda;
        // }
        // $ConLetra = strtoupper($ConLetra);
        // $row['IMPORTE_LETRA'] = $ConLetra;
    }
    if(trim($row['NoNota'])!=''){
    	$sql="select serie,folio from rh_cfd__cfd where id_systypes=11 and fk_transno in(".trim($row['NoNota'],', ').") ";
    	if($res=DB_query($sql,$db)){
    		$row['NoNota']='';
    		$espacio='';
    		while($rows=DB_fetch_assoc($res)){
    			$row['NoNota'].=trim($rows['serie'].$rows['folio']).$espacio;
    			$espacio=', ';
    		}
    	}
    }
}

//FB::INFO($_SERVER['HTTP_USER_AGENT'],'___TET');

function is_chrome(){
   return(!!stripos( " ".$_SERVER['HTTP_USER_AGENT'],"chrome"));
}

$Chrome = false;
if(is_chrome()){
    //echo 'El navegador es Google Chrome.';
    $Chrome = true;
}


if (!empty($_POST['FOLIO'])) {
    //$FOLIO_TITULAR = $_POST['FOLIO'];
    $FOLIO_TITULAR = " AND titular.folio = '{$_POST['FOLIO']}' ";
}

if(!empty($_POST['FINICIO'])){
    $WhereDate = " AND  date(dt1.trandate)>= '" . $_POST['FINICIO'] . "' ";
    $WhereDate .= " AND  date(dt1.trandate)<= '" . $_POST['FFIN'] . "' ";
}

$Periodo = $_POST['FINICIO'] . " al " . $_POST['FFIN'];

if (!empty($_POST['STATUS'])) {
    $STATUS = " AND titular.movimientos_afiliacion = '{$_POST['STATUS']}' ";
}



/*DATOS DE COBRANZA*/
$DatosCobranza = Yii::app()->db->createCommand()
    ->select('coyname,
            gstno AS rfc,
            companynumber,
            regoffice1,
            regoffice2,
            regoffice3,
            regoffice4,
            regoffice5,
            regoffice6,
            telephone,
            fax,
            email,
            dtranferencia_nombre_responsable,
            dtranferencia_correo_responsable,
            dtranferencia_telefono_responsable')
    ->from('companies')
    ->where('coycode=1')
    ->queryRow();

    $TEXTO_COBRANZA = "Aclaraciones: {$DatosCobranza['dtranferencia_telefono_responsable']} (Depto. Cobranza) o {$DatosCobranza['dtranferencia_correo_responsable']}";


$xml =  simplexml_load_file("estadoscuenta.jrxml");
$rootpath = $filePath = realpath(dirname(__FILE__) . '/../') . '/';
$filePath = substr($filePath, 0, strrpos($filePath, "/")) . "/companies/$db";

$LogoPath = $rootpath . "PHPJasperXML";

if($_SESSION['DatabaseName'] == 'sainar_erp_001'){
    $LogoPath2 = $rootpath . "PHPJasperXML/";
}else{
    $LogoPath2 = $rootpath . "PHPJasperXML/OTRO_";
}

$PHPJasperXML = new PHPJasperXMLExt();
$PHPJasperXML->debugsql = false;

$PHPJasperXML->arrayParameter = array(
    "rootpath" => $rootpath,
    'filePath' => $filePath,
    "LogoPath" => $LogoPath,
    "LogoPath2" => $LogoPath2,
    "EmpresaID" => $Empresa,
    "STATUS" => $STATUS,
    "FOLIO_TITULAR" => $FOLIO_TITULAR,
    "TRAN_DATE" => $WhereDate,
    "PERIODO" => $Periodo,
    "TEXTO_COBRANZA" => $TEXTO_COBRANZA,
);

$PHPJasperXML->xml_dismantle($xml);
$PHPJasperXML->transferDBtoArray($server, $user, $pass, $db);
if(isset($bufer)){
    $bufer = $PHPJasperXML->outpage("S");
}else{
    if($Chrome){
        $PHPJasperXML->outpage("D");
    }else
    {
        $PHPJasperXML->outpage("I");
    }
}
$db = $db_;