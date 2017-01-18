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
        $this->Cell(30,10,'Reporte de ventas por vendedor: '.implode(',', $_POST['CLASS']),0,0,'L');
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
            $this->Cell(30,10,'Vendedor',0,0,'L');
            $this->SetXY(20,15);
            $this->Cell(30,10,'Gamma',0,0,'L');
            $this->SetXY(50,15);
            $this->Cell(30,10,'Marca.',0,0,'L');
            $this->SetXY(80,15);
            $this->Cell(30,10,'Especie',0,0,'L');
            $this->SetXY(100,15);
            $this->Cell(30,10,'Categoria',0,0,'L');
            if(!isset($_POST['PrintPDFResume'])){
                $this->SetXY(120,15);
                $this->Cell(30,10,'Codigo',0,0,'L');
                $this->SetXY(135,15);
                $this->Cell(30,10,'Producto',0,0,'L');
                $this->SetXY(175,15);
                $this->Cell(30,10,'Cant.',0,0,'L');
            }
            $this->SetXY(190,15);
            $this->Cell(30,10,'Importe',0,0,'L');
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
    if(count($_POST['gamma'])>0){
        foreach ($_POST['gamma'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['gamma'][$key]=$value;
            }else{
                $_POST['gamma'][$key]="'".$value."'";
            }
        }
    }
    if(count($_POST['especie'])>0){
        foreach ($_POST['especie'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['especie'][$key]=$value;
            }else{
                $_POST['especie'][$key]="'".$value."'";
            }
        }
    }
    if(count($_POST['category'])>0){
        foreach ($_POST['category'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['category'][$key]=$value;
            }else{
                $_POST['category'][$key]="'".$value."'";
            }
        }
    }


    if(isset($_POST['marca'])&&count($_POST['marca'])>0 ){
        array_push($arrayCondicional,'rh_marca.id in('.implode(',',$_POST['marca']).')');
    }
    if(isset($_POST['gamma'])&&count($_POST['gamma'])>0 ){
        array_push($arrayCondicional,'rh_gamma.id in('.implode(',',$_POST['gamma']).')');
    }
    if(isset($_POST['especie'])&&count($_POST['especie'])>0 ){
        array_push($arrayCondicional,'rh_especie.id in('.implode(',',$_POST['especie']).')');
    }
    if(isset($_POST['category'])&&count($_POST['category'])>0 ){
        array_push($arrayCondicional,'stockcategory.categoryid in('.implode(',',$_POST['category']).')');
    }

    $finalCondicion="";
    if(count($arrayCondicional)>0){
      $finalCondicion=implode(' and ',$arrayCondicional).' and ';
    }

    $SQL="select  salesman.salesmanname,rh_gamma.descripcion as gamma,rh_especie.descripcion as especie,stockcategory.categorydescription as categoria,rh_marca.nombre as marca, stockmoves.transno, stockmoves.stockid,stockmaster.description,sum((stockmoves.qty*-1)) as cantidad, sum(((1-stockmoves.discountpercent)*stockmoves.price*stockmoves.qty*-1)) as importe
	        from stockmoves
		        join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmoves.type=10
		            left join rh_gamma_stock on rh_gamma_stock.stockid= stockmaster.stockid
			            left join rh_gamma on rh_gamma_stock.idGamma = rh_gamma.id
		            left join rh_especie_stock on rh_especie_stock.stockid = stockmaster.stockid
			            left join rh_especie on rh_especie_stock.idEspecie = rh_especie.id
		        join stockcategory on stockmaster.categoryid = stockcategory.categoryid
		        join rh_marca on stockmaster.rh_marca = rh_marca.id
		        join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type=debtortrans.type and stockmoves.type=10
		        join custbranch on debtortrans.branchcode = custbranch.branchcode and debtortrans.debtorno=custbranch.debtorno
		        join salesman on  custbranch.salesman = salesman.salesmancode
	        where  ".$finalCondicion." (date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin.") and stockmoves.type=10 and debtortrans.type=10 AND debtortrans.rh_status != 'C' group by salesman.salesmanname,rh_gamma.id,stockmoves.transno,stockmoves.stockid   order by salesman.salesmanname, gamma;";
            //
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
  $marca=$row['salesmanname'];
  $newMarca=$row['salesmanname'];
  $nameMarca =$row['salesmanname'];
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode($nameMarca),0,1,'L');
  $TotalSaldoMarca=0;
  while(($marca==$newMarca)&&(!$EOF)){
        $categoria=$row['gamma'];
        $newCategoria=$row['gamma'];
        $nameCat=$row['gamma'];
        //if($_POST['DetailedReport']=='Yes'){
            $pdf->SetX(10);
            $pdf->Cell(20,2,utf8_decode($row['gamma'] ),0,1,'L');
        //}
        while(($categoria==$newCategoria)&&($marca==$newMarca)&&(!$EOF)){
           // if($_POST['DetailedReport']=='Yes'){
                $pdf->SetFont('Times','',6);
                $pdf->SetX(50);
                $pdf->Cell(10,3,utf8_decode($row['marca']),0,0,'L');
                $pdf->SetX(80);
                $pdf->Cell(10,3,utf8_decode($row['especie']),0,0,'L');
                $pdf->SetX(100);
                $pdf->Cell(10,3,utf8_decode($row['categoria']),0,0,'L');
                $pdf->SetX(120);
                $pdf->Cell(10,3,utf8_decode($row['stockid']),0,0,'L');
                $pdf->SetX(135);
                $pdf->Cell(10,3,utf8_decode($row['description']),0,0,'L');
                $pdf->SetX(165);
                $pdf->Cell(20,3,utf8_decode(number_format($row['cantidad'],2)),0,0,'R');
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($row['importe'],2)),0,1,'R');
           // }
                $TotalCat+=$row['importe'];
                $TotalMarca+=$row['importe'];
                $GranTotal+=$row['importe'];
               // $TotalQtyCat+=$row['qtyonhand'];
               // $TotalQtyMarca+=$row['qtyonhand'];
               // $GranQtyTotal+=$row['qtyonhand'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
            $newCategoria=$row['gamma'];
            $newMarca=$row['salesmanname'];
        }
        $pdf->SetFont('Times','B',6);
        if($_POST['DetailedReport']=='Yes'){
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Categoria - '.$nameCat),0,0,'L');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        }else{
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Categoria - '.$nameCat),0,0,'L');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        }
        $TotalCat=0;
        $TotalQtyCat=0;
  }

  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode('Total - '.$nameMarca),0,0,'L');
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
//*****************************************************************************
//*****                        Reporte Resumen
//*****************************************************************************
If (isset($_POST['PrintPDFResume'])){
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
    if(count($_POST['gamma'])>0){
        foreach ($_POST['gamma'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['gamma'][$key]=$value;
            }else{
                $_POST['gamma'][$key]="'".$value."'";
            }
        }
    }
    if(count($_POST['especie'])>0){
        foreach ($_POST['especie'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['especie'][$key]=$value;
            }else{
                $_POST['especie'][$key]="'".$value."'";
            }
        }
    }
    if(count($_POST['category'])>0){
        foreach ($_POST['category'] as $key=>$value){
            if(is_numeric($value)){
                $_POST['category'][$key]=$value;
            }else{
                $_POST['category'][$key]="'".$value."'";
            }
        }
    }

    if(isset($_POST['marca'])&&count($_POST['marca'])>0 ){
        array_push($arrayCondicional,'rh_marca.id in('.implode(',',$_POST['marca']).')');
    }
    if(isset($_POST['gamma'])&&count($_POST['gamma'])>0 ){
        array_push($arrayCondicional,'rh_gamma.id in('.implode(',',$_POST['gamma']).')');
    }
    if(isset($_POST['especie'])&&count($_POST['especie'])>0 ){
        array_push($arrayCondicional,'rh_especie.id in('.implode(',',$_POST['especie']).')');
    }
    if(isset($_POST['category'])&&count($_POST['category'])>0 ){
        array_push($arrayCondicional,'stockcategory.categoryid in('.implode(',',$_POST['category']).')');
    }

    $finalCondicion="";
    if(count($arrayCondicional)>0){
      $finalCondicion=implode(' and ',$arrayCondicional).' and ';
    }

    $SQL="select  salesman.salesmanname,rh_gamma.descripcion as gamma,rh_especie.descripcion as especie,stockcategory.categorydescription as categoria,rh_marca.nombre as marca, stockmoves.transno, stockmoves.stockid,stockmaster.description,sum((stockmoves.qty*-1)) as cantidad, sum(((1-stockmoves.discountpercent)*stockmoves.price*stockmoves.qty*-1)) as importe
	        from stockmoves
		        join stockmaster on stockmoves.stockid=stockmaster.stockid and stockmoves.type=10
		            left join rh_gamma_stock on rh_gamma_stock.stockid= stockmaster.stockid
			            left join rh_gamma on rh_gamma_stock.idGamma = rh_gamma.id
		            left join rh_especie_stock on rh_especie_stock.stockid = stockmaster.stockid
			            left join rh_especie on rh_especie_stock.idEspecie = rh_especie.id
		        join stockcategory on stockmaster.categoryid = stockcategory.categoryid
		        join rh_marca on stockmaster.rh_marca = rh_marca.id
		        join debtortrans on stockmoves.transno = debtortrans.transno and stockmoves.type=debtortrans.type and stockmoves.type=10
		        join custbranch on debtortrans.branchcode = custbranch.branchcode and debtortrans.debtorno=custbranch.debtorno
		        join salesman on  custbranch.salesman = salesman.salesmancode
	        where  ".$finalCondicion." (date(stockmoves.trandate)>=".$fecha_ini." and date(stockmoves.trandate)<=".$fecha_fin.") and stockmoves.type=10 and debtortrans.type=10 AND debtortrans.rh_status != 'C' group by salesman.salesmanname, gamma order by salesman.salesmanname, gamma;";

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
  $marca=$row['salesmanname'];
  $newMarca=$row['salesmanname'];
  $nameMarca =$row['salesmanname'];
  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode($nameMarca),0,1,'L');
  $TotalSaldoMarca=0;
  while(($marca==$newMarca)&&(!$EOF)){
        while(($marca==$newMarca)&&(!$EOF)){
           // if($_POST['DetailedReport']=='Yes'){
                $pdf->SetFont('Times','',6);
                $pdf->SetX(10);
                $pdf->Cell(20,3,utf8_decode($row['gamma'] ),0,0,'L');
                $pdf->SetX(50);
                $pdf->Cell(10,3,utf8_decode($row['marca']),0,0,'L');
                $pdf->SetX(80);
                $pdf->Cell(10,3,utf8_decode($row['especie']),0,0,'L');
                $pdf->SetX(100);
                $pdf->Cell(10,3,utf8_decode($row['categoria']),0,0,'L');
                /*$pdf->SetX(120);
                $pdf->Cell(10,3,utf8_decode($row['stockid']),0,0,'L');
                $pdf->SetX(135);
                $pdf->Cell(10,3,utf8_decode($row['description']),0,0,'L');
                $pdf->SetX(165);
                $pdf->Cell(20,3,utf8_decode(number_format($row['cantidad'],2)),0,0,'R');*/
                $pdf->SetX(180);
                $pdf->Cell(20,3,utf8_decode(number_format($row['importe'],2)),0,1,'R');
           // }
                $TotalCat+=$row['importe'];
                $TotalMarca+=$row['importe'];
                $GranTotal+=$row['importe'];
            if(($row=DB_fetch_array($Result))==false){
                $EOF=true;
            }
            $newMarca=$row['salesmanname'];
        }
       /* $pdf->SetFont('Times','B',6);
        if($_POST['DetailedReport']=='Yes'){
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Categoria - '.$nameCat),0,0,'L');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        }else{
            $pdf->SetX(10);
            $pdf->Cell(20,5,utf8_decode('Total por Categoria - '.$nameCat),0,0,'L');
            $pdf->SetX(180);
            $pdf->Cell(20,5,utf8_decode(number_format($TotalCat,2)),0,1,'R');
        } */
        $TotalCat=0;
        $TotalQtyCat=0;
  }

  $pdf->SetFont('Times','B',6);
  $pdf->SetX(5);
  $pdf->Cell(20,5,utf8_decode('Total - '.$nameMarca),0,0,'L');
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