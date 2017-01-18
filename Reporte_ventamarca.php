<?php
set_time_limit(0);

$PageSecurity = 2;
include('includes/session.inc');
include('PHPJasperXML/class/fpdf/fpdf.php');

class ReporteInventario extends FPDF{
    private $MarcaSelect = null;
    private $FechaSelect = null;

    function Header(){
        $this->SetFont('Arial','B',11);
        $this->SetXY(5,5);
        $this->Cell(30,10,$_SESSION['CompanyRecord']['coyname'],0,0,'L');
        $this->SetXY(5,10);
        $this->Cell(30,10,'Reporte de ventas por marca: '.$this->MarcaSelect,0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->SetXY(155,10);
        $this->Cell(30,10,'Impreso: '.Date($_SESSION['DefaultDateFormat']),0,0,'R');
        $this->SetFont('Arial','B',8);
        $this->SetXY(155,5);
        $this->Cell(30,10,'Desde '.$this->FechaSelect,0,0,'R');
            $this->SetFont('Arial','B',8);
            $this->SetXY(5,15);
            $this->Cell(30,10,'Cliente',0,0,'L');
            $this->SetXY(20,15);
            $this->Cell(30,10,'Nombre',0,0,'L');
            $this->SetXY(80,15);
            $this->Cell(30,10,'Importe',0,0,'L');
            $this->SetXY(100,15);
            $this->Cell(30,10,'Descuento',0,0,'L');
            $this->SetXY(125,15);
            $this->Cell(30,10,'Desc. financ',0,0,'L');
            $this->SetXY(150,15);
            $this->Cell(30,10,'Impuesto',0,0,'L');
            $this->SetXY(170,15);
            $this->Cell(30,10,'Flete',0,0,'L');
            $this->SetXY(190,15);
            $this->Cell(30,10,'Total',0,0,'L');
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

    public function setMarca($marca){
        $this->MarcaSelect=$marca;
    }

    public function setRange($fecha){
        $this->FechaSelect=$fecha;
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
        $text.=' '.$_POST['fecha_fin'].' hasta';
    }
    if(isset($_POST['marca'])&&($_POST['marca']!='%')){
        $Marca="and stockmaster.rh_marca=".$_POST['marca'];
    }else{
        $Marca="";
    }


    $SQL="select debtorsmaster.debtorno,debtortrans.transno,debtorsmaster.name,debtorsmaster.taxref,sum(stockmoves.price*stockmoves.qty*-1) as importe,sum(if(isnull(D1.monto),(stockmoves.discountpercent*stockmoves.price),D1.monto)*stockmoves.qty*-1)as Desc1,sum(if(isnull(D2.monto),0.00,D2.monto)*stockmoves.qty*-1)as Desc2,sum(((1-stockmoves.discountpercent)*stockmoves.price*stockmoves.qty*-1)*stockmovestaxes.taxrate) as impuesto, sum(debtortrans.ovfreight) as flete, sum((stockmoves.price*stockmoves.qty*-1)-(if(isnull(D1.monto),(stockmoves.discountpercent*stockmoves.price),D1.monto)*stockmoves.qty*-1)-(if(isnull(D2.monto),0.00,D2.monto)*stockmoves.qty*-1)+((((1-stockmoves.discountpercent)*stockmoves.price)*stockmoves.qty*-1)*stockmovestaxes.taxrate)+debtortrans.ovfreight)as total
	from stockmoves
		join stockmovestaxes on stockmoves.stkmoveno = stockmovestaxes.stkmoveno and stockmoves.type=10 and date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin."
        join debtortrans on stockmoves.transno = debtortrans.transno and debtortrans.type=10 and date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin."
        left join rh_descuentos D1 on  D1.stkmoveno = stockmoves.stkmoveno and D1.transno = debtortrans.order_  and D1.tipo_descuento=1
		left join rh_descuentos D2 on  D2.stkmoveno = stockmoves.stkmoveno and D2.transno = debtortrans.order_  and D2.tipo_descuento=2
		join stockmaster on stockmoves.stockid=stockmaster.stockid ".$Marca."
		join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
    where debtortrans.type=10 and stockmoves.type=10 AND debtortrans.rh_status != 'C'
	group by debtorsmaster.debtorno order by total DESC;";

	$Result = DB_query($SQL,$db,'','',false,true);

	if (DB_error_no($db) !=0) {
	  $title = _('Error 0x0001') . ' - ' . _('Problem Report');
	  include('includes/header.inc');
	   prnMsg( _('No es posible obtener información') . ' '  . DB_error_msg($db),'error');
	   echo "<BR><A HREF='" .$rootpath .'/index.php?' . SID . "'>" . _('Back to the menu') . '</A>';
	   if ($debug==1){
	      echo "<BR>$SQL";
	   }
	   include('includes/footer.inc');
	   exit;
	}

        if(isset($_POST['marca'])&&($_POST['marca']!='%')){
                    $SQL="select nombre from rh_marca where id=".$_POST['marca'];
	                $ResultMarca = DB_query($SQL,$db);
                    $rw=DB_fetch_array($ResultMarca);
                    $MarcaSelect=$rw['nombre'];
        }else{
            $MarcaSelect="Todas";
        }
//   echo $MarcaSelect;
$pdf=new ReporteInventario();
$pdf->setMarca($MarcaSelect);
$pdf->setRange($text);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',6);

$EOF=false;
$Total1=0;
$Total2=0;
$Total3=0;
$Total4=0;
$Total5=0;
$Total6=0;
$fistLoop=true;
while((DB_num_rows($Result)>0)&&(!$EOF)){
  if(($fistLoop)&&(($row=DB_fetch_array($Result))==false)){
    $EOF=true;
  }
        while((!$EOF)){
                $pdf->SetFont('Times','',6);
                $pdf->SetX(5);
                $pdf->Cell(10,3,utf8_decode($row['debtorno']),0,0,'L');
                $pdf->SetX(20);
                $pdf->Cell(10,3,utf8_decode($row['name']),0,0,'L');
                $pdf->SetX(75);
                $pdf->Cell(20,3,utf8_decode(number_format($row['importe'],2)),0,0,'R');
                $pdf->SetX(95);
                $pdf->Cell(20,3,utf8_decode(number_format($row['Desc1'],2)),0,0,'R');
                $pdf->SetX(125);
                $pdf->Cell(20,3,utf8_decode(number_format($row['Desc2'],2)),0,0,'R');
                $pdf->SetX(145);
                $pdf->Cell(20,3,utf8_decode(number_format($row['impuesto'],2)),0,0,'R');
                $pdf->SetX(160);
                $pdf->Cell(20,3,utf8_decode(number_format($row['flete'],2)),0,0,'R');
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($row['total'],2)),0,1,'R');
                $pdf->SetX(20);
                $pdf->Cell(10,3,utf8_decode($row['taxref']),0,1,'L');
                $Total1+=$row['importe'];
                $Total2+=$row['Desc1'];
                $Total3+=$row['Desc2'];
                $Total4+=$row['impuesto'];
                $Total5+=$row['flete'];
                $Total6+=$row['total'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
        }
  }
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(45);
  $pdf->Cell(20,5,utf8_decode('GRAN TOTAL'),0,0,'L');
  $pdf->SetX(75);
  $pdf->Cell(20,5,utf8_decode(number_format($Total1,2)),0,0,'R');
  $pdf->SetX(95);
  $pdf->Cell(20,5,utf8_decode(number_format($Total2,2)),0,0,'R');
  $pdf->SetX(125);
  $pdf->Cell(20,5,utf8_decode(number_format($Total3,2)),0,0,'R');
  $pdf->SetX(145);
  $pdf->Cell(20,5,utf8_decode(number_format($Total4,2)),0,0,'R');
  $pdf->SetX(160);
  $pdf->Cell(20,5,utf8_decode(number_format($Total5,2)),0,0,'R');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($Total6,2)),0,1,'R');
$pdf->Output();
}


?>