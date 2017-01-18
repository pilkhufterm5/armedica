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
        $this->Cell(30,10,'Reporte de ventas por cantidad: '.$this->text,0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->SetXY(155,10);
        $this->Cell(30,10,'Impreso: '.Date($_SESSION['DefaultDateFormat']),0,0,'R');
            $this->SetFont('Arial','B',8);
            $this->SetXY(5,15);
            $this->Cell(20,10,'Codigo',0,0,'L');
            $this->SetXY(30,15);
            $this->Cell(30,10,'Descripcion',0,0,'L');
            $this->SetXY(100,15);
            $this->Cell(30,10,'Cantidad',0,0,'L');
            $this->SetXY(115,15);
            $this->Cell(30,10,'Venta',0,0,'L');
            $this->SetXY(127,15);
            $this->Cell(30,10,'Costo',0,0,'L');
            $this->SetXY(142,15);
            $this->Cell(30,10,'Utilidad',0,0,'L');
            $this->SetXY(162,15);
            $this->Cell(30,10,'Promedio',0,0,'L');
            $this->SetXY(182,15);
            $this->Cell(30,10,'Existencia',0,0,'L');
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

    if(isset($_POST['marca'])&&count($_POST['marca'])>0 && $_POST['marca']!='%' ){
        //array_push($arrayCondicional,'stockmaster.rh_marca in('.implode(',',$_POST['marca']).')');
        array_push($arrayCondicional,'stockmaster.rh_marca ='.$_POST['marca']);
    }

    if(isset($_POST['location'])&&count($_POST['location'])>0 ){
      		if($_POST['location']=='Todos'){
                array_push($arrayCondicional,"stockmoves.loccode LIKE '%'");
		    }else {
                array_push($arrayCondicional,"stockmoves.loccode = '".$_POST['location']."'");
		    }
    }


    $finalCondicion="";
    if(count($arrayCondicional)>0){
      $finalCondicion=implode(' and ',$arrayCondicional).' and ';
    }


		$SQL ="SELECT
                (SUM(-1*stockmoves.qty)/(datediff('".$_POST['fecha_fin']."','".$_POST['fecha_ini']."')+1)) as promedio,
				stockmoves.stockid,
				stockmoves.prd,
				stockmoves.loccode,
				stockmoves.price,
				SUM(stockmoves.price*stockmoves.qty*-1) as totalprice,
				SUM(-1*stockmoves.qty) as qty,
				stockmoves.standardcost,
				SUM(stockmoves.qty*stockmoves.standardcost*-1) as totalcost,
				stockmaster.description,
				rh_locations.locationname
				FROM stockmoves
				INNER JOIN stockmaster ON stockmoves.stockid = stockmaster.stockid
				INNER JOIN rh_locations ON rh_locations.loccode = stockmoves.loccode
				INNER JOIN debtortrans ON debtortrans.transno = stockmoves.transno AND debtortrans.type = stockmoves.type
				WHERE
                ".$finalCondicion."
				stockmoves.type IN (10, 11, 20000)
				AND date(stockmoves.trandate) >= '".$_POST['fecha_ini']."'
				AND date(stockmoves.trandate) <= '".$_POST['fecha_fin']."'
				AND debtortrans.rh_status NOT IN ('C', 'F')
				GROUP BY stockmoves.stockid
				ORDER BY stockmoves.stockid, stockmaster.description";

   /* $SQL="select  debtortrans.debtorno,debtorsmaster.name,rh_marca.nombre as marca,rh_cfd__cfd.serie,rh_cfd__cfd.folio, stockmoves.transno, sum((stockmoves.price*stockmoves.qty*-1)) as importe
	from stockmoves
		join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmoves.type=10
		join rh_marca on stockmaster.rh_marca = rh_marca.id
		join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type=debtortrans.type
		left join rh_cfd__cfd on debtortrans.id = rh_cfd__cfd.id_debtortrans
		join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
	        where  ".$finalCondicion." (date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin.") group by transno order by debtorsmaster.debtorno, importe";
    */ //echo $SQL;
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
$nameMarca='';
$nameCat='';
$fistLoop=true;
$TotalQty=0;
$TotalPrice=0;
$TotalCost=0;
$TotalUtilidad=0;
$TotalProm=0;
$TotalExis=0;
while((DB_num_rows($Result)>0)&&(!$EOF)){
  if(($fistLoop)&&(($row=DB_fetch_array($Result))==false)){
    $EOF=true;
  }
  $fistLoop =false;
  $marca=$row['stockid'];
  $newMarca=$row['stockid'];
 /* $nameMarca =$row['name'];
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode($marca.' - '.$nameMarca),0,1,'L');
  $TotalSaldoMarca=0;*/

  while(($marca==$newMarca)&&(!$EOF)){
        while(($marca==$newMarca)&&(!$EOF)){
          			$sql_qty = "SELECT quantity FROM locstock WHERE loccode = '".$row['loccode']."'
						AND stockid = '".$row['stockid']."'";
			    $qty_res = DB_query($sql_qty,$db);
			    $qty_info = DB_fetch_array($qty_res);

                $pdf->SetFont('Times','',6);
                $pdf->SetX(5);
                $pdf->Cell(10,3,utf8_decode($row['stockid']),0,0,'L');
                $pdf->SetX(30);
                $pdf->Cell(10,3,utf8_decode($row['description']),0,0,'L');
                $pdf->SetX(90);
                $pdf->Cell(20,3,utf8_decode(number_format($row['qty'],0)),0,0,'R');
                $pdf->SetX(105);
                $pdf->Cell(20,3,utf8_decode(number_format($row['totalprice'],2)),0,0,'R');
                $pdf->SetX(120);
                $pdf->Cell(20,3,utf8_decode(number_format($row['totalcost'],2)),0,0,'R');
                $pdf->SetX(145);
                $pdf->Cell(20,3,utf8_decode(number_format($row['totalprice']-$row['totalcost'],2)),0,0,'R');
                $pdf->SetX(165);
                $pdf->Cell(20,3,utf8_decode(number_format($row['promedio'],2)),0,0,'R');
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($qty_info['quantity'],2)),0,1,'R');

                $TotalQty+=$row['qty'];
                $TotalPrice+=$row['totalprice'];
                $TotalCost+=$row['totalcost'];
                $TotalUtilidad+=$row['totalprice']-$row['totalcost'];
                $TotalProm+=$row['promedio'];
                $TotalExis+=$qty_info['quantity'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
            $newMarca=$row['stockid'];
        }
  }
  /*
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode('Total - '.$marca.' - '.$nameMarca),0,0,'L');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalMarca,2)),0,1,'R');
  $TotalMarca=0;
  $TotalQtyMarca=0;*/
}
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(45);
  $pdf->Cell(20,5,utf8_decode('GRAN TOTAL'),0,0,'L');
  $pdf->SetX(90);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalQty,2)),0,0,'R');
  $pdf->SetX(105);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalPrice,2)),0,0,'R');
  $pdf->SetX(120);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalCost,2)),0,0,'R');
  $pdf->SetX(145);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalUtilidad,2)),0,0,'R');
  $pdf->SetX(165);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalProm,2)),0,0,'R');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalExis,2)),0,1,'R');
  $GranTotal=0;
  $GranQtyTotal=0;
$pdf->Output();
}
?>