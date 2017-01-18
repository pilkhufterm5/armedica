<?php
set_time_limit(0);

$PageSecurity = 2;
include('includes/session.inc');
include('PHPJasperXML/class/fpdf/fpdf.php');

class ReporteInventario extends FPDF{
    function Header(){
        $this->SetFont('Arial','B',11);
        $this->SetXY(5,5);
        $this->Cell(30,10,$_SESSION['CompanyRecord']['coyname'],0,0,'L');
        $this->SetXY(5,10);
        $this->Cell(30,10,'Reporte de abono por periodo: '.implode(',', $_POST['CLASS']),0,0,'L');
        $this->SetFont('Arial','B',8);
        $this->SetXY(155,10);
        $this->Cell(30,10,'Impreso: '.Date($_SESSION['DefaultDateFormat']),0,0,'R');
       /* if($_POST['DetailedReport']=='No'){
            $this->SetFont('Arial','B',8);
            $this->SetXY(5,15);
            $this->Cell(30,10,'Cliente',0,0,'L');
            $this->SetXY(130,15);
            $this->Cell(30,10,'Cantidad',0,0,'L');
            $this->SetXY(180,15);
            $this->Cell(30,10,'Valor',0,0,'L');
            $this->line(5,22,200,22);
            $this->line(5,22.2,200,22.2);
            $this->Ln(8);
        }else{*/
            $this->SetFont('Arial','B',8);
            $this->SetXY(5,15);
            $this->Cell(30,10,'Cliente',0,0,'L');
            $this->SetXY(80,15);
            $this->Cell(30,10,'Transaccion',0,0,'L');
            $this->SetXY(100,15);
            $this->Cell(30,10,'No.',0,0,'L');
            $this->SetXY(120,15);
            $this->Cell(30,10,'Metodo Pago',0,0,'L');
            $this->SetXY(145,15);
            $this->Cell(30,10,'Aplicacion',0,0,'L');
            $this->SetXY(170,15);
            $this->Cell(30,10,'Saldo',0,0,'L');
            $this->SetXY(190,15);
            $this->Cell(30,10,'Abono',0,0,'L');
            $this->line(5,22,200,22);
            $this->line(5,22.2,200,22.2);
            $this->Ln(8);
       // }
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
}


	
	
If (isset($_POST['PrintPDF'])
	&& isset($_POST['prd'])){

    $SQL="select debtortrans.debtorno,debtorsmaster.name,debtortrans.id,debtortrans.trandate as fechaFac,(debtortrans.ovamount + debtortrans.ovgst+ debtortrans.ovfreight - debtortrans.ovdiscount ) as total,dt.type,custallocns.amt,dt.trandate, dt.reference, dt.transno, rh_cfd__cfd.serie,rh_cfd__cfd.folio, concat(nc_cfd.serie,nc_cfd.folio)as NCFolio
	from debtortrans
		join debtorsmaster on debtortrans.debtorno=debtorsmaster.debtorno and debtortrans.type=10 and debtortrans.prd=".$_POST['prd']." and debtorsmaster.debtorno>='".$_POST['cliente_ini']."' and debtorsmaster.debtorno<='".$_POST['cliente_fin']."'
		join rh_cfd__cfd on debtortrans.id = rh_cfd__cfd.id_debtortrans
		left join custallocns on debtortrans.id=custallocns.transid_allocto
		left join debtortrans dt on dt.id=custallocns.transid_allocfrom
		left join rh_cfd__cfd nc_cfd on dt.id = rh_cfd__cfd.id_debtortrans
		order by debtortrans.debtorno,rh_cfd__cfd.serie,rh_cfd__cfd.folio,dt.trandate;";
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
        $categoria=$row['id'];
        $newCategoria=$row['id'];
        $nameCat=$row['serie'].$row['folio'];
        //if($_POST['DetailedReport']=='Yes'){
            $pdf->SetX(10);
            $pdf->Cell(20,2,utf8_decode('Factura - '.$nameCat.' '.$row['fechaFac'] ),0,0,'L');
            $pdf->SetX(165);
            $pdf->Cell(20,2,utf8_decode(number_format($row['total'],2)),0,1,'R');
            $TotalFactura=$row['total'];
            $TotalSaldo+=$TotalFactura;
            $TotalSaldoMarca+=$TotalFactura;
        //}
        while(($categoria==$newCategoria)&&($marca==$newMarca)&&(!$EOF)){
           // if($_POST['DetailedReport']=='Yes'){
                $TotalFactura-= $row['amt'];
                $pdf->SetFont('Times','',6);
                $pdf->SetX(80);
                $pdf->Cell(10,3,utf8_decode(($row['type']=='12'?'Deposito': ($row['type']=='11'?'Nota Credito':''))),0,0,'L');
                $pdf->SetX(100);
                $pdf->Cell(10,3,utf8_decode($row['transno']),0,0,'L');
                $pdf->SetX(120);
                $pdf->Cell(10,3,utf8_decode(substr($row['reference'],0,20)),0,0,'L');
                $pdf->SetX(145);
                $pdf->Cell(15,3,utf8_decode($row['trandate']),0,0,'L');
                $pdf->SetX(165);
                $pdf->Cell(20,3,utf8_decode(number_format($TotalFactura,2)),0,0,'R');
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($row['amt'],2)),0,1,'R');
           // }
                $TotalCat+=$row['amt'];
                $TotalMarca+=$row['amt'];
                $GranTotal+=$row['amt'];
               // $TotalQtyCat+=$row['qtyonhand'];
               // $TotalQtyMarca+=$row['qtyonhand'];
               // $GranQtyTotal+=$row['qtyonhand'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
            $newCategoria=$row['id'];
            $newMarca=$row['debtorno'];
        }
        $pdf->SetFont('Times','B',6);
        if($_POST['DetailedReport']=='Yes'){
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Factura - '.$nameCat),0,0,'L');
            $pdf->SetX(165);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalFactura,2)),0,0,'R');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        }else{
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Factura - '.$nameCat),0,0,'L');
            $pdf->SetX(165);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalFactura,2)),0,0,'R');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        }
        $TotalCat=0;
        $TotalQtyCat=0;
  }

  /******* MODIFICACION PARA CHEQUES DEVUELTOS *******************************************/
  $SqlDevo="select debtortrans.debtorno,dt.transno, dt.trandate,custallocns.amt,debtortrans.reference as devuelto ,dt.reference
	from custallocns
		join debtortrans on debtortrans.id=custallocns.transid_allocto and debtortrans.type=12 and debtortrans.debtorno = '".$marca."'
		join debtortrans dt on dt.id=custallocns.transid_allocfrom and dt.type=12 and dt.prd=".$_POST['prd']." ;";
  $RS = DB_query($SqlDevo,$db,'','',false,true);
  $pdf->SetX(10);
  $pdf->Cell(20,2,utf8_decode('Depositos Devueltos'),0,1,'L');
  $TotalFactura=0;
  $count=0;
 while($rw=DB_fetch_array($RS)){
       $TotalFactura+= $rw['amt'];
       $count++;
       $pdf->SetFont('Times','',6);
       $pdf->SetX(80);
       $pdf->Cell(10,3,utf8_decode('devuelto'),0,0,'L');
       $pdf->SetX(100);
       $pdf->Cell(10,3,utf8_decode($rw['transno']),0,0,'L');
       $pdf->SetX(120);
       $pdf->Cell(10,3,utf8_decode(substr($rw['reference'],0,20)),0,0,'L');
       $pdf->SetX(145);
       $pdf->Cell(15,3,utf8_decode($rw['trandate']),0,0,'L');
       $pdf->SetX(180);
       $pdf->Cell(20,3,utf8_decode(number_format($rw['amt'],2)),0,1,'R');
 }
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(10);
  $pdf->Cell(20,5,utf8_decode('Total depositos devueltos'),0,0,'L');
  $pdf->SetX(165);
  $pdf->Cell(20,5,utf8_decode(number_format($count,0)),0,0,'R');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalFactura,2)),0,1,'R');
  //****************************************************************************************


  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode('Total - '.$nameMarca),0,0,'L');
  $pdf->SetX(165);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalSaldoMarca-$TotalMarca,2)),0,0,'R');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($TotalMarca,2)),0,1,'R');
  $TotalMarca=0;
  $TotalQtyMarca=0;
}
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(45);
  $pdf->Cell(20,5,utf8_decode('GRAN TOTAL'),0,0,'L');
  $pdf->SetX(165);
  $pdf->Cell(20,5,utf8_decode(number_format( $TotalSaldo-$GranTotal,2)),0,0,'R');
  $pdf->SetX(180);
  $pdf->Cell(20,5,utf8_decode(number_format($GranTotal,2)),0,1,'R');
  $GranTotal=0;
  $GranQtyTotal=0;
$pdf->Output();
}

?>