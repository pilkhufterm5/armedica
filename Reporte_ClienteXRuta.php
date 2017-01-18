<?php
set_time_limit(0);
ob_start();
$PageSecurity = 2;
include('includes/session.inc');
include('PHPJasperXML/class/fpdf/fpdf.php');
class ReporteInventario extends FPDF{
	private $text;
	function Header(){
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,5);
		$this->Cell(30,10,$_SESSION['CompanyRecord']['coyname'],0,0,'L');
		$this->SetXY(5,10);
		$this->Cell(30,10,'Reporte de cliente por ruta: '.$this->text,0,0,'L');
		$this->SetFont('Arial','B',8);
		$this->SetXY(155,10);
		$this->Cell(30,10,'Impreso: '.Date($_SESSION['DefaultDateFormat']),0,0,'R');
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,15);
		$this->Cell(30,10,'Ruta',0,0,'L');
		$this->SetXY(15,15);
		$this->Cell(30,10,'Vendedor',0,0,'L');
		
		$this->SetXY(35,15);
		$this->Cell(30,10,'Municipio',0,0,'L');
		$this->SetXY(65,15);
		$this->Cell(30,10,'Cod',0,0,'L');
		$this->SetXY(72,15);
		$this->Cell(30,10,'Cliente',0,0,'L');
		$this->SetXY(120,15);
		$this->Cell(30,10,'Direccion',0,0,'L');
		$this->line(5,22,200,22);
		$this->line(5,22.2,200,22.2);
		$this->Ln(8);
	}


	function Footer() {
		$this->line(5,17,200,17);
		$this->line(5,17.2,200,17.2);
		$this->SetY(-15);
		$this->line(5,277,200,277);
		$this->line(5,277.2,200,277.2);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,10,'Pagina '.$this->PageNo().' de {nb}',0,0,'C');
	}

	public function setText($txt=""){
		$this->text = $txt;
	}
}


$text="";
if(strlen($_POST['fecha_ini'])<10){
	$fecha_ini='date(now())';
	$text.=Date($_SESSION['DefaultDateFormat']).' hasta ';
}else {
	$fecha_ini= "'".$_POST['fecha_ini']."'";
	$text.=' '.$_POST['fecha_ini'].' hasta';
}

if(strlen($_POST['fecha_fin'])<10){
	$fecha_fin='date(now())';
	$text.=' '.Date($_SESSION['DefaultDateFormat']);
}else {
	$fecha_fin= "'".$_POST['fecha_fin']."'";
	$text.=' '.$_POST['fecha_fin'].' ';
}

$arrayCondicional = array();
if(count($_POST['muni'])>0){
	foreach ($_POST['muni'] as $key=>$value){
		if(is_numeric($value)){
			$_POST['muni'][$key]=$value;
		}else{
			$_POST['muni'][$key]="'".$value."'";
		}
	}
}

if(count($_POST['ruta'])>0){
	foreach ($_POST['ruta'] as $key=>$value){
		$_POST['ruta'][$key]="'".$value."'";
	}
}

if(isset($_POST['muni'])&&count($_POST['muni'])>0 ){
	array_push($arrayCondicional,'custbranch.braddress7 in('.implode(',',$_POST['muni']).')');
}

if(isset($_POST['ruta'])&&count($_POST['ruta'])>0 ){
	array_push($arrayCondicional,'rh_rutas.id in('.implode(',',$_POST['ruta']).')');
}


$finalCondicion="";
if(count($arrayCondicional)>0){
	$finalCondicion= "".implode(' and ',$arrayCondicional).' ';
}
$SQL="select rh_rutas.descripcion,custbranch.braddress7,debtorsmaster.debtorno,debtorsmaster.name,debtorsmaster.taxref,
            concat(custbranch.braddress1,' ',custbranch.braddress2,' ',custbranch.braddress3,' ',custbranch.braddress4,' ',custbranch.braddress8,' ',custbranch.braddress8,' ',custbranch.braddress9,' ',custbranch.braddress10 ) as dir
            ,custbranch.salesman, salesman.salesmanname
    from
	debtorsmaster
        join custbranch on debtorsmaster.debtorno = custbranch.debtorno
		join rh_rutas_debtors on custbranch.debtorno = rh_rutas_debtors.debtorno and custbranch.branchcode = rh_rutas_debtors.branchcode
		join rh_rutas on rh_rutas_debtors.idrutas = rh_rutas.id
		left join salesman on custbranch.salesman =salesman.salesmancode
	where
        ".$finalCondicion."
        order by  rh_rutas.descripcion ;";
// echo $SQL;
$Result = DB_query($SQL,$db,'','',false,true);

if (DB_error_no($db) !=0) {
	$title = _('Inventory Valuation') . ' - ' . _('Problem Report');
	include('includes/header.inc');
	prnMsg( _('The inventory valuation could not be retrieved by the SQL because') . ' '  . DB_error_msg($db),'error');
	echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	if ($debug==1){
		echo "<BR>$SQL";
	}
	include('includes/footer.inc');
	exit;
}
If (isset($_POST['PrintXls'])){
	include_once ("includes/class-excel-xml.inc.php");
	$xls = new Excel_XML;
	ob_end_clean();
	$Documento=array();
	$xls->SendHeaders('ClienteXRuta');
	echo $xls->GetHeader(). $xls->SendBody();
	//$Documento[]=array('Ruta','Vendedor','Municipio','Cod','Cliente','Direccion');
	$xls->addRow(array('Ruta','Vendedor','Municipio','Cod','Cliente','Direccion'));
	echo $xls->getLines();
	if($Result&&(DB_num_rows($Result)>0))
	while($row=DB_fetch_array($Result)){
		$xls->rowClear();
		$xls->addRow(
// 		$Documento[]=
		array(utf8_decode($row['descripcion']),utf8_decode($row['salesmanname']),utf8_decode($row['braddress7']),utf8_decode($row['debtorno']),utf8_decode($row['name']),utf8_decode($row['dir']))
		);
		echo $xls->getLines()
		;
	}
// 	$xls->addArray ( $Documento );
	echo $xls->GetFooter();
// 	$xls->generateXML ("ClienteXRuta");

}else
If (isset($_POST['PrintPDF'])){

	$pdf=new ReporteInventario();
	$pdf->setText($text);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Times','',6);


	$EOF=false;
	$TotalCat=0;
	$TotalMarca=0;
	$GranTotal=0;
	$TotalQtyCat=0;
	$TotalQtyMarca=0;
	$GranQtyTotal=0;
	$nameMarca='';
	$nameCat='';
	$fistLoop=true;
	$TotalFactura=0;
	$TotalSaldo=0;
	$TotalSaldoMarca=0;
	while((DB_num_rows($Result)>0)&&(!$EOF)){
		if(($fistLoop)&&(($row=DB_fetch_array($Result))==false)){
			$EOF=true;
		}
		$fistLoop =false;
		$marca=$row['debtorno'];
		$newMarca=$row['debtorno'];
		$nameMarca =$row['name'];
		//$pdf->SetFont('Times','B',6);
		//$pdf->SetX(5);
		//$pdf->Cell(20,5,utf8_decode($marca.' - '.$nameMarca),0,1,'L');
		$TotalSaldoMarca=0;
		while(($marca==$newMarca)&&(!$EOF)){
			while(($marca==$newMarca)&&(!$EOF)){


				$pdf->SetFont('Times','',5);
								
				$pdf->SetX(5);
				$pdf->Cell(10,3,utf8_decode($row['descripcion']),0,0,'L');
				
				$pdf->SetX(15);
				$pdf->Cell(10,3,utf8_decode($row['salesmanname']),0,0,'L');				
				
				$pdf->SetX(35);
				$pdf->Cell(10,3,utf8_decode($row['braddress7']),0,0,'L');
				$pdf->SetX(65);
				$pdf->Cell(10,3,utf8_decode($row['debtorno']),0,0,'L');
				$pdf->SetX(72);
				$pdf->Cell(10,3,utf8_decode($row['name']),0,1,'L');
				$pdf->SetX(120);
				$pdf->Cell(10,3,utf8_decode($row['dir']),0,1,'L');
				/* $TotalCat+=$row['importe'];
				 $TotalMarca+=$row['importe'];
				$GranTotal+=$row['importe'];*/
				if(($row=DB_fetch_array($Result))==false){
					$EOF=true;
				}
				$newMarca=$row['debtorno'];
			}
		}

		/* $pdf->SetFont('Times','B',6);
		 $pdf->SetX(5);
		$pdf->Cell(20,5,utf8_decode('Total - '.$marca.' - '.$nameMarca),0,0,'L');
		$pdf->SetX(180);
		$pdf->Cell(20,5,utf8_decode(number_format($TotalMarca,2)),0,1,'R');
		$TotalMarca=0;
		$TotalQtyMarca=0; */
	}
	/*$pdf->SetFont('Times','B',6);
	 $pdf->SetX(45);
	$pdf->Cell(20,5,utf8_decode('GRAN TOTAL'),0,0,'L');
	$pdf->SetX(180);
	$pdf->Cell(20,5,utf8_decode(number_format($GranTotal,2)),0,1,'R');
	$GranTotal=0;
	$GranQtyTotal=0;*/
	ob_end_clean();
	$pdf->Output();
}
?>