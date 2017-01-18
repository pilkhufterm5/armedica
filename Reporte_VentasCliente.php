<?php
set_time_limit(0);

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
        $this->Cell(30,10,'Reporte de ventas por cliente: '.$this->text,0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->SetXY(155,10);
        $this->Cell(30,10,'Impreso: '.Date($_SESSION['DefaultDateFormat']),0,0,'R');
            $this->SetFont('Arial','B',8);
            $this->SetXY(5,15);
            $this->Cell(30,10,'Vendedor',0,0,'L');
            $this->SetXY(20,15);
            $this->Cell(30,10,'Transaccion',0,0,'L');
            $this->SetXY(50,15);
            $this->Cell(30,10,'Marca',0,0,'L');
            $this->SetXY(80,15);
            $this->Cell(30,10,'Codigo',0,0,'L');
            $this->SetXY(100,15);
            $this->Cell(30,10,'Descripcion',0,0,'L');
            $this->SetXY(140,15);
            $this->Cell(30,10,'Unidad',0,0,'L');
            $this->SetXY(160,15);
            $this->Cell(30,10,'Cantidad',0,0,'L');
            $this->SetXY(190,15);
            $this->Cell(30,10,'Importe',0,0,'L');
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




If (isset($_POST['PrintPDF'])){
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
    if(count($_POST['marca'])>0){
        foreach ($_POST['marca'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['marca'][$key]=$value;
            }else{
                $_POST['marca'][$key]="'".$value."'";
            }
        }
    }

    if(count($_POST['sucursal'])>0){
        foreach ($_POST['sucursal'] as $key=>$value){
               $_POST['sucursal'][$key]="'".$value."'";
        }
    }

    if(isset($_POST['marca'])&&count($_POST['marca'])>0 ){
        array_push($arrayCondicional,'rh_marca.id in('.implode(',',$_POST['marca']).')');
    }

    if(isset($_POST['sucursal'])&&count($_POST['sucursal'])>0 ){
        array_push($arrayCondicional,'debtorsmaster.debtorno in('.implode(',',$_POST['sucursal']).')');
    }


    $finalCondicion="";
    if(count($arrayCondicional)>0){
      $finalCondicion=implode(' and ',$arrayCondicional).' and ';
    }

    $SQL="select rh_cfd__cfd.serie,rh_cfd__cfd.folio, debtortrans.debtorno,debtorsmaster.name,rh_marca.nombre as marca, stockmoves.transno, stockmoves.stockid,stockmaster.description,stockmaster.units,(stockmoves.qty*-1) as cantidad, ((1-stockmoves.discountpercent)*stockmoves.price*stockmoves.qty*-1) as importe
	        from stockmoves
		        join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmoves.type=10
		        join rh_marca on stockmaster.rh_marca = rh_marca.id
		        join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type=debtortrans.type
                left join rh_cfd__cfd on debtortrans.id = rh_cfd__cfd.id_debtortrans
		        join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
	        where  ".$finalCondicion." (date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin.") and stockmoves.type=10 and debtortrans.type=10 AND debtortrans.rh_status != 'C' order by debtorsmaster.debtorno, rh_marca,stockmoves.transno;";
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
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode($marca.' - '.$nameMarca),0,1,'L');
  $TotalSaldoMarca=0;
  while(($marca==$newMarca)&&(!$EOF)){
        while(($marca==$newMarca)&&(!$EOF)){
                $pdf->SetFont('Times','',6);
                $pdf->SetX(20);
                $pdf->Cell(10,3,utf8_decode($row['serie'].$row['folio'].' ('.$row['transno'].')'),0,0,'L');
                $pdf->SetX(50);
                $pdf->Cell(10,3,utf8_decode($row['marca']),0,0,'L');
                $pdf->SetX(80);
                $pdf->Cell(10,3,utf8_decode($row['stockid']),0,0,'L');
                $pdf->SetX(95);
                $pdf->Cell(10,3,utf8_decode($row['description']),0,0,'L');
                $pdf->SetX(145);
                $pdf->Cell(10,3,utf8_decode($row['units']),0,0,'L');
                $pdf->SetX(160);
                $pdf->Cell(20,3,utf8_decode(number_format($row['cantidad'],2)),0,0,'R');
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($row['importe'],2)),0,1,'R');
                $TotalCat+=$row['importe'];
                $TotalMarca+=$row['importe'];
                $GranTotal+=$row['importe'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
            $newMarca=$row['debtorno'];
        }
  }

  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode('Total - '.$marca.' - '.$nameMarca),0,0,'L');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalMarca,2)),0,1,'R');
  $TotalMarca=0;
  $TotalQtyMarca=0;
}
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(45);
  $pdf->Cell(20,5,utf8_decode('GRAN TOTAL'),0,0,'L');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($GranTotal,2)),0,1,'R');
  $GranTotal=0;
  $GranQtyTotal=0;
$pdf->Output();
}
?>