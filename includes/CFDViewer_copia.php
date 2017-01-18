<?php

include('CFDReader.php');
include('fpdf.php');
class CFDViewer extends FPDF{
	private $Reader=NULL;
	private $Pages;
	private $pdf;
	private $CFD;
	private $ivasol;
	function CFDViewer(){
		parent::FPDF('P','mm','Letter');
	}
	public function loadData($direccion,$comprobante,$conceptos,$general){
		$this->SetCreator("Realhost");
		$this->SetAuthor("Realhost");
		$this->Reader = new CFDReader($direccion,$comprobante,$conceptos,$general);
		$this->CFD = $this->Reader->getCFDInfo();
		$this->setPages($this->getTotalPages());
	}
	public function loadFile($file){
		$this->SetCreator("SunSetSoft.org");
		$this->SetAuthor("Susetsoft.org");
		$this->Reader = new CFDReader();
		$this->Reader->loadFile($file);
		$this->CFD = $this->Reader->getCFDInfo();
		$this->setPages($this->getTotalPages());
	}
	private function setPages($p){
		$this->Pages=$p;
	}
	private function getPages(){
		return $this->Pages;
	}

  	public function __call($method_name, $arguments) {
    	$accepted_methods = array("hello");
    	if(!in_array($method_name, $accepted_methods)) {
      		trigger_error("Method <strong>$method_name</strong> no existe", E_USER_ERROR);
    	}

    	if(count($arguments) == 0) {
      		$this->PrintCFD();
    	} elseif(count($arguments) == 2) {
      		$this->PrintCFD($arguments[0], $arguments[1]);
   		} else {
     		 return false;
    	}
  	}

	private function TopPrint(){
		$this->SetAutoPageBreak(false,10);

		$this->SetFont('Arial','B',5);
		$this->SetXY(190,268);
	    $this->Cell(20,3,"Pagina ".$this->PageNo()." de ".$this->getPages(),0,0,'R');
		$this->SetFont('Arial','B',9);
		$this->SetTextColor(0,0,0);
		/*$this->SetXY(70,4);
		$this->multicell(195,15,$this->CFD["emisor"]["nombre"],0,'L');
		$this->SetXY(70,7);
		$this->SetFont('Arial','B',7);
		$this->multicell(195,15,$this->CFD["emisor"]["rfc"],0,'L');		*/

		//$this->SetXY(15,31);
		//$Emisor=$this->CFD["emisor"]["nombre"]."\n".$this->CFD["emisor"]["rfc"]."\n".rtrim(ltrim($this->CFD["emisor_DF"]["calle"].' '.$this->CFD["emisor_DF"]["noExterior"].' '.$this->CFD["emisor_DF"]["noInterior"]."\n".$this->CFD["emisor_DF"]["colonia"].' '.$this->CFD["emisor_DF"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["emisor_DF"]["localidad"].' '.$this->CFD["emisor_DF"]["municipio"].', '.$this->CFD["emisor_DF"]["estado"].', '.$this->CFD["emisor_DF"]["pais"]));
		//$this->multicell(117,3.5,$Emisor,0,'L');

		//mhidalgo - Información de la factura, serie, folio, etc
		$this->Line(15,26,81,26);
		$this->Line(15,48,81,48);
		$this->Line(15,26,15,48);
		$this->Line(81,26,81,48);
		$this->SetXY(15,25);
		$this->Cell(65,10,'COMPROBANTE FISCAL DIGITAL',0,0,'C');
		$this->SetXY(15,29);
		$this->Cell(65,10,'FACTURA' . ' ' . $this->CFD["comprobante"]["serie"].$this->CFD["comprobante"]["folio"],0,0,'C');
		$this->SetFont('Arial','',8);
		$this->SetXY(15,33);
		$this->Cell(65,10,'No. CERTIFICADO' . ' ' . $this->CFD["comprobante"]["noCertificado"],0,0,'C');
		$this->SetXY(15,37);
		$this->Cell(65,10,utf8_decode('No. APROBACIÓN') . ' ' . $this->CFD["comprobante"]["noAprobacion"],0,0,'C');
		$this->SetXY(15,41);
		$this->Cell(65,10,utf8_decode('AÑO DE APROBACIÓN') . ' ' . $this->CFD["comprobante"]["anoAprobacion"],0,0,'C');

		//Información del cliente lado izquierdo
		$x = 18;
		$this->SetFont('Arial','B',6);
		$this->SetXY(15,28 + $x);
		$this->Cell(20,10,'No. CLIENTE:',0,0,'L');
		$this->SetXY(15,31 + $x);
		$this->Cell(20,10,'NOMBRE:',0,0,'L');
		$this->SetXY(15,34 + $x);
		$this->Cell(20,10,'RFC:',0,0,'L');
		$this->SetXY(15,37 + $x);
		$this->Cell(20,10,utf8_decode('DIRECCIÓN:'),0,0,'L');

		$x = 18;
		$this->SetFont('Arial','',6);
		$this->SetXY(32,28 + $x);
		$this->Cell(20,10,$this->CFD['receptor']['no_cliente'],0,0,'L');
		$this->SetXY(32,31 + $x);
		$this->Cell(20,10,utf8_decode($this->CFD['receptor']['nombre']),0,0,'L');
		$this->SetXY(32,34 + $x);
		$this->Cell(20,10,utf8_decode($this->CFD['receptor']['rfc']),0,0,'L');
		$this->SetXY(32,41 + $x);
		$Direccion = rtrim(ltrim($this->CFD["receptor_DF"]["calle"].' '.$this->CFD["receptor_DF"]["noExterior"].' '.$this->CFD["receptor_DF"]["noInterior"]."\n".$this->CFD["receptor_DF"]["colonia"].' '.$this->CFD["receptor_DF"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["receptor_DF"]["localidad"].' '.$this->CFD["receptor_DF"]["municipio"].', '.$this->CFD["receptor_DF"]["estado"].', '.$this->CFD["receptor_DF"]["pais"]));
		$this->multicell(93,3,(html_entity_decode(html_entity_decode(html_entity_decode($Direccion)))),0,'L');
		//$this->Cell(20,10,$Receptor,0,0,'L');
		//Fin información del cliente lado izquierdo


		//Información del cliente lado derecho
		$x = 15;
		$this->SetFont('Arial','B',6);
		$this->SetXY(100,28 + $x);
		$this->Cell(20,10,'FECHA:',0,0,'L');
		$x -= 3;
		$this->SetXY(100,38 + $x);
		$this->multicell(93,3,"EJECUTIVO\nDE VENTAS:",0,'L');
		$this->SetXY(100,41 + $x);
		$this->Cell(20,10,'SUCURSAL:',0,0,'L');
		$this->SetXY(100,44 + $x);
		$this->Cell(20,10,utf8_decode('TÉRMINOS:'),0,0,'L');
		$this->SetXY(100,47 + $x);
		$this->Cell(20,10,utf8_decode('TELÉFONO:'),0,0,'L');

		$x = 15;
		$this->SetFont('Arial','',6);
		$this->SetXY(120,28 + $x);
		$this->Cell(20,10,$this->CFD["comprobante"]["fecha"],0,0,'L');
		$x-= 3;
		$this->SetXY(120,37 + $x);
		$this->Cell(20,10,$this->CFD['general']['vendedor'],0,0,'L');
		$this->SetXY(120,41 + $x);
		$this->Cell(20,10,$this->CFD['general']['sucursal'],0,0,'L');
		$this->SetXY(120,44 + $x);
		$this->Cell(20,10,$this->CFD['comprobante']['condicionesDePago'],0,0,'L');
		$this->SetXY(120,47 + $x);
		$this->Cell(20,10,$this->CFD['general']['telefono'],0,0,'L');
		//$this->Cell(20,10,$Receptor,0,0,'L');
		//Fin información del cliente lado derecho

		$x = 0;
		$this->SetFont('Arial','B',6);
		$this->SetXY(100,28 + $x);
		$this->Cell(20,10,utf8_decode('LUGAR DE EXPEDICIÓN:'),0,0,'L');

		$this->SetFont('Arial','',6);
		$this->SetXY(100,34 + $x);
		$Lugar=rtrim(ltrim($this->CFD["emisor_EE"]["calle"].' '.$this->CFD["emisor_EE"]["noExterior"].' '.$this->CFD["emisor_EE"]["noInterior"]."\n".$this->CFD["emisor_EE"]["colonia"].' '.$this->CFD["emisor_EE"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["emisor_EE"]["localidad"].' '.$this->CFD["emisor_EE"]["municipio"].', '.$this->CFD["emisor_EE"]["estado"].', '.$this->CFD["emisor_EE"]["pais"]));
		$this->multicell(117,3.5,$Lugar,0,'L');







		//Información de la cotización, orden de trabajo y orden de compra
		$x = 0;
		/*$this->SetFont('Arial','B',6);
		$this->SetXY(15,50 + $x);
		$this->Cell(20,10,utf8_decode('No. COTIZACIÓN:'),0,0,'L');
		$this->SetXY(15,53 + $x);
		$this->Cell(20,10,utf8_decode('No. ORDEN DE TRABAJO:'),0,0,'L');
		$this->SetXY(15,56 + $x);
		$this->Cell(20,10,'ORDEN DE COMPRA:',0,0,'L');

		$x = 0;
		$this->SetFont('Arial','',6);
		$this->SetXY(43,50 + $x);
		$this->Cell(20,10,$this->CFD['general']['cotizacion'],0,0,'L');
		$this->SetXY(43,53 + $x);
		$this->Cell(20,10,$this->CFD['general']['orden_trabajo'],0,0,'L');
		$this->SetXY(43,56 + $x);
		$this->Cell(20,10,$this->CFD['general']['orden_compra'],0,0,'L');
		*/

		//Se imprime el comentario general de la factura si es que tiene contenido
		if(trim($this->CFD['general']['com_general']) != ''){
			$x = 0; //para controlar mensaje
			$this->SetXY(15,60 + $x);
			//$this->Cell(20,10,utf8_decode(html_entity_decode($this->CFD['general']['com_general'])),0,0,'L');
			$this->multicell(117,3.5,utf8_decode(html_entity_decode($this->CFD['general']['com_general'])),0,'L');
			$x = 5; //Para contemplar encabezado
		}else{
			$x = 0; //Para contemplar encabezado
		}

		//Encabezado de conceptos
		$this->SetFont('Arial','B',8);
		$this->SetXY(3,70 + $x);
		$this->Cell(20,2.5,'UNIDAD',0,0,'L');
		$this->SetXY(16,70 + $x);
		$this->Cell(20,2.5,'CANT.',0,0,'L');
		$this->SetXY(30,70 + $x);
		$this->Cell(20,2.5,'COD.',0,0,'L');
		$this->SetXY(50,70 + $x);
		$this->Cell(80,2.5,utf8_decode('DESCRIPCIÓN'),0,0,'C');
		$this->SetXY(148,70 + $x);
		$this->Cell(20,2.5,'PRECIO',0,0,'C');
		$this->SetXY(185,70 + $x);
		$this->Cell(20,2.5,'TOTAL',0,0,'C');
		$this->SetXY(185,70 + $x);

		//Cuadro descripción
		$this->Line(3,73 + $x,208,73 + $x);
		$this->Line(3,73 + $x,3,169);
		$this->Line(208,73 + $x,208,169);

		$this->SetFont('Arial','B',5.5);

		/*$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',6);
		$this->SetXY(4,42);
		$Emisor=$this->CFD["emisor"]["nombre"]."\n".$this->CFD["emisor"]["rfc"]."\n".rtrim(ltrim($this->CFD["emisor_DF"]["calle"].' '.$this->CFD["emisor_DF"]["noExterior"].' '.$this->CFD["emisor_DF"]["noInterior"].' '.$this->CFD["emisor_DF"]["colonia"].' '.$this->CFD["emisor_DF"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["emisor_DF"]["localidad"].' '.$this->CFD["emisor_DF"]["municipio"].' '.$this->CFD["emisor_DF"]["estado"].' '.$this->CFD["emisor_DF"]["pais"]))."\nExpedido en:"."\n".rtrim(ltrim($this->CFD["emisor_EE"]["calle"].' '.$this->CFD["emisor_EE"]["noExterior"].' '.$this->CFD["emisor_EE"]["noInterior"].' '.$this->CFD["emisor_EE"]["colonia"].' '.$this->CFD["emisor_EE"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["emisor_EE"]["localidad"].' '.$this->CFD["emisor_EE"]["municipio"].' '.$this->CFD["emisor_EE"]["estado"].' '.$this->CFD["emisor_EE"]["pais"]));
		$this->multicell(117,2.2,$Emisor,0,'L');*/

		/*$this->SetXY(120,42);
		$Receptor=$this->CFD["receptor"]["nombre"]."\n".$this->CFD["receptor"]["rfc"]."\n".rtrim(ltrim($this->CFD["receptor_DF"]["calle"].' '.$this->CFD["receptor_DF"]["noExterior"].' '.$this->CFD["receptor_DF"]["noInterior"].' '.$this->CFD["receptor_DF"]["colonia"].' '.$this->CFD["receptor_DF"]["codigoPostal"]))."\n".rtrim(ltrim($this->CFD["receptor_DF"]["localidad"].' '.$this->CFD["receptor_DF"]["municipio"].' '.$this->CFD["receptor_DF"]["estado"].' '.$this->CFD["receptor_DF"]["pais"]));

		if($this->haveFecha($this->CFD["comprobante"]["condicionesDePago"])){
			$Receptor=$Receptor."\n"."Fecha limite de pago: ".substr($this->CFD["comprobante"]["condicionesDePago"],0,strpos($this->CFD["comprobante"]["condicionesDePago"],':'));
   		}else{
			$Receptor=$Receptor."\n"."Fecha limite de pago: Pagado";
		}
		$Receptor=$Receptor."\nForma de pago: ".$this->CFD["comprobante"]["formaDePago"];
		$this->multicell(93,2.2,$Receptor,0,'L');*/
	}

	private function printRecibo(){
		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',7);
		$this->SetXY(8,77);
		$Desc=$this->CFD["concepto"]["descripcion"][1];
		$label_monto="Honorario";
		if(strlen($this->CFD["concepto"]["predial"][1])>0){
			$Desc=$Desc." No. de cuenta predial del inmueble ".$this->CFD["concepto"]["predial"][1];
			$label_monto="Renta";
		}
		$this->multicell(123,2.5,$Desc,0,'L');
		$row=0;
		$this->SetFont('Arial','B',10);
		$this->SetXY(135,77+($row*8));
		$this->Cell(30,2.5,$label_monto,0,0,'R');
		$this->SetXY(166,77+($row*8));
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,77+($row*8));
		$this->Cell(30,2.5,number_format($this->CFD["concepto"]["importe"][1],2,'.',','),0,0,'R');
		if(strlen($this->CFD["comprobante"]["descuento"])>0){
			$row++;
			$this->SetXY(135,77+($row*8));
			$this->Cell(30,2.5,'Descuento',0,0,'R');
			$this->SetXY(166,77+($row*8));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,77+($row*8));
			$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["descuento"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Trasladados*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["trasladado"]["cantidad"];$i++){
			$row++;
			 if($this->CFD["impuestos"]["trasladado"][$i]["impuesto"]=='IVA'){
   				 $this->ivasol =$this->CFD["impuestos"]["trasladado"][$i]["importe"];
			}
			$this->SetXY(135,77+($row*8));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["trasladado"][$i]["impuesto"]." ".$this->CFD["impuestos"]["trasladado"][$i]["tasa"]." %",0,0,'R');
			$this->SetXY(166,77+($row*8));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,77+($row*8));
			$this->Cell(30,2.5,number_format($this->CFD["impuestos"]["trasladado"][$i]["importe"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Locales Trasladados************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["traslados"]["cantidad"];$i++){
			$row++;
			$this->SetXY(135,77+($row*8));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["traslados"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["traslados"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,77+($row*8));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,77+($row*8));
			$this->Cell(30,2.5,number_format($this->CFD["impuestoslocales"]["traslados"]["importe"][$i],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Retenidos*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["retenido"]["cantidad"];$i++){
			$row++;
			$monto = $this->CFD["impuestos"]["retenido"][$i]["importe"];
  			$gtotal =$this->CFD["comprobante"]["subTotal"];
  		    $tasa =(100*$monto)/$gtotal;
			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=10.66)&&($tasa<=10.7)){
				$tasa=10.6667;
				$tasa = number_format($tasa, 4, ".", "");
			}else if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=7.33)&&($tasa<=7.4)){
				$tasa=7.3333;
				$tasa = number_format($tasa, 4, ".", "");
			}else{
				$tasa = number_format($tasa, 2, ".", "");
			}
			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($monto==$this->ivasol)){
				$tasa=100;
			}
			$this->SetXY(135,77+($row*8));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["retenido"][$i]["impuesto"]." Ret. ".$tasa." %",0,0,'R');
			$this->SetXY(166,77+($row*8));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,77+($row*8));
			$this->Cell(30,2.5,number_format($this->CFD["impuestos"]["retenido"][$i]["importe"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Locales Retenidos************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["retenidos"]["cantidad"];$i++){
			$row++;
			$this->SetXY(135,77+($row*8));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["retenidos"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["retenidos"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,77+($row*8));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,77+($row*8));
			$this->Cell(30,2.5,number_format($this->CFD["impuestoslocales"]["retenidos"]["importe"][$i],2,'.',','),0,0,'R');
		}
		$this->SetFont('Arial','B',11);
		$this->SetXY(135,140);
		$this->Cell(30,2.5,"TOTAL ".$this->CFD["addenda"]["moneda"],0,0,'R');
		$this->SetXY(166,140);
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,140);
		$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["total"],2,'.',','),0,0,'R');

		$this->SetFont('Arial','B',10);
		$this->SetXY(5,155);
		$this->multicell(207,4,strtoupper($this->CFD['general']['importe_letra']." ".$this->CFD["addenda"]["moneda"]),0,'L');

		$this->SetFont('Arial','B',5);

		$pie=$pie."\n\nESTE DOCUMENTO ES UNA ".utf8_decode('REPRESENTACIÓN')." IMPRESA DE UN CFD";
		$pie=$pie."\n\nSello\n".$this->CFD["comprobante"]["sello"];
		$pie=$pie."\n\nCadena Original\n".$this->CFD["comprobante"]["original"];
		$this->multicell(204,2.5,$pie,0,'J');
	}
	private function haveFecha($pago){
		if(strpos($pago,':')<=11){
			$fecha=substr($pago,0,strpos($pago,':'));
   			if (ereg("^[0-9]{0,1}[0-9]{1}[\/]{1}[0-9]{0,1}[0-9]{1}[\/]{1}[0-9]{2,4}$", $fecha)) {
      			return true;
   			} else {
      			return false;
   			}
		}else{
			return false;
		}
   }
	public function PrintCFD(){
		$this->AddPage();
		if(!(($this->CFD["conceptos"]["cantidad"]==1)&&($this->CFD["impuestos"]["retenido"]["cantidad"]==2))){
			/*if (file_exists($_SESSION['LogoFile'])) {
				$this->Image($_SESSION['LogoFile'],3,4,209,271);
			}else{
				$this->Image($_SESSION['LogoFile'],3,4,209,271);
			}*/
			//SAINTS
				$this->Image(dirname(__FILE__).'/copia.png',3,4,209,271);
			//----Logo
				if (file_exists($this->CFD['general']['logo'])){
					//$this->Image($this->CFD['general']['logo'],157,6,50);
				}
			$this->TopPrint();
			$this->printFactura();
		}else{
			/*if (file_exists($_SESSION['LogoFile'])) {
				$this->Image($_SESSION['LogoFile'],3,3,209,183);
			}else{
				$this->Image($_SESSION['LogoFile'],3,3,209,183);
			}*/
			//----Logo
				if (file_exists($this->CFD['general']['logo'])){
				   //	$this->Image($this->CFD['general']['logo'],15,4,50);
				}
			$this->TopPrint();
			$this->printRecibo();
		}
		return $this->Output("COPIA_CFD_".$this->CFD["comprobante"]["serie"].'-'.$this->CFD["comprobante"]["folio"].".pdf",'I');
	}
	public function PrintCFD2($name=null,$opc){
		$this->AddPage();
		if(!(($this->CFD["conceptos"]["cantidad"]==1)&&($this->CFD["impuestos"]["retenido"]["cantidad"]==2))){
			/*if (file_exists($_SESSION['LogoFile'])) {
				$this->Image($_SESSION['LogoFile'],3,4,209,271);
			}else{*/
			//SAINTS
				$this->Image(dirname(__FILE__).'/copia.png',3,4,209,271);
			//}
			//----Logo
				if (file_exists($this->CFD['general']['logo'])){
					//$this->Image($this->CFD['general']['logo'],157,6,50);
				}
			$this->TopPrint();
			$this->printFactura();
		}else{
			/*if (file_exists($_SESSION['LogoFile'])) {
				$this->Image($_SESSION['LogoFile'],3,3,209,183);
			}else{
				$this->Image($_SESSION['LogoFile'],3,3,209,183);
			}*/
			//----Logo
				if (file_exists($this->CFD['general']['logo'])){
					//$this->Image($this->CFD['general']['logo'],15,4,50);
				}
			$this->TopPrint();
			$this->printRecibo();
		}
        if(!is_null($name)){
		    $this->Output($name,$opc);
        }else{
            $this->Output("COPIA_CFD_".$this->CFD["comprobante"]["serie"].'-'.$this->CFD["comprobante"]["folio"].".pdf",$opc);
        }
	}

	private function printFactura(){
		//Encabezado de conceptos
		/*$x = 5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,70 + $x);
		$this->Cell(20,2.5,'UNIDAD',0,0,'L');
		$this->SetXY(25,70 + $x);
		$this->Cell(20,2.5,'CANT.',0,0,'L');
		$this->SetXY(45,70 + $x);
		$this->Cell(80,2.5,utf8_decode('DESCRIPCIÓN'),0,0,'C');
		$this->SetXY(138,70 + $x);
		$this->Cell(20,2.5,'PRECIO',0,0,'C');
		$this->SetXY(185,70 + $x);
		$this->Cell(20,2.5,'TOTAL',0,0,'C');
		$this->SetXY(185,70 + $x);
		$this->Line(10,75 + $x,203,75 + $x);*/

		$this->Line(3,169,208,169);

		$this->SetFont('Arial','B',5.5);
		$row=0;
		$totalrow=3;
		$rwheight=0;
		$unalinea = true;
		while(($row<$this->CFD["conceptos"]["cantidad"])and($totalrow<=30)){
			//-----------------Conceptos-----------------------------------

			$txt=$this->CFD["concepto"]["descripcion"][$row];
			$txt = str_replace('\n\r',"\n",strip_tags(html_entity_decode(html_entity_decode(html_entity_decode($txt)))));
			$rwheight= intval(strlen($txt)/ 100) + $rwheight_narr;
			if((strlen($txt) % 100)>0){
  				$rwheight= $rwheight+1;
  			}

  			if(trim($this->CFD["concepto"]["narrative"][$row]) == ''){
				$rwheight= $rwheight+1;
			}

  			$qty = substr_count($txt,"\n");
  			$rwheight += $qty;

  			//$totalrow += $rwheight;

  			$rwheight_narr = 0;
			$y_narr = 0;
			$qty_narr = 0;
			if(trim($this->CFD["concepto"]["narrative"][$row]) != ''){

				//Otro metodo
				$narr = $this->CFD["concepto"]["narrative"][$row];
				$narr = str_replace('\r\n',"\n",utf8_decode(strip_tags(html_entity_decode(html_entity_decode(html_entity_decode($narr))))));
				$rwheight_narr = 0;
				$qty_narr = substr_count($narr,"\n");
				if($qty_narr > 0){
					$lines = explode("\n",$narr);
					foreach($lines as $ind=>$line){
						$rwheight_narr += intval(strlen($line)/ 100);
						//Se le quita línea, porque si lleva salto de línea entonces se reduce esto.
						$rwheight_narr -= 1;
					}
					$rwheight_narr += $qty_narr;
					//$rwheight_narr -= 1;
				}else{
					$rwheight_narr = intval(strlen($narr)/ 100)+1;
					if($rwheight_narr <= 1){
						$unalinea = true;
					}else{
						$unalinea = false;
					}
				}

				if((strlen($narr) % 100)>0){
					$rwheight_narr= $rwheight_narr+1;
				}

				//$y_narr =  72+($totalrow)+1;

				//$totalrow2 += ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.6);
				$totalrow2 += $rwheight_narr+$qty_narr;
				//$totalrow2 += 1.5;
				//$y_narr =  72+($totalrow*3.5);
				//echo "<br>Totalrow2: ".$totalrow2."<br>";
			}else{
				//Si es la primera línea y no tiene comentario extra, le quitamos espacio.
				if($totalrow==3)
					$totalrow -= 1.5;
				$totalrow += 0.5;
				$totalrow2 = 0;
			}


			if($totalrow+$rwheight+$totalrow2<=30){//52
				$this->SetFont('Arial','B',5.5);
			   	$this->SetXY(8,72+($totalrow*3));
				$this->Cell(1,2.5,$this->CFD["concepto"]["unidad"][$row],0,0,'C');
			   	$this->SetXY(23,72+($totalrow*3));
				$this->Cell(1,2.5,$this->CFD["concepto"]["cantidad"][$row],0,0,'R');
 			   	$this->SetXY(27,72+($totalrow*3));
				$this->Cell(1,2.5,$this->CFD["concepto"]["codigo"][$row],0,0,'L');

				if(trim($this->CFD["concepto"]["narrative"][$row]) != ''){
					$this->SetXY(55,72+($totalrow*3)+2.5);
					$this->multicell(101,2.5,$narr,0,'L');
				}

			   	$this->SetXY(55,72+($totalrow*3));
				$this->multicell(101,2.5,$txt,0,'L');
			   	$this->SetXY(150,72+($totalrow*3));
				$this->Cell(1,2.5,"$",0,0,'L');
			   	$this->SetXY(171,72+($totalrow*3));
				$this->Cell(1,2.5,number_format($this->CFD["concepto"]["valorUnitario"][$row],2,'.',','),0,0,'R');
			   	$this->SetXY(183,72+($totalrow*3));
				$this->Cell(1,2.5,"$",0,0,'L');
			   	$this->SetXY(206,72+($totalrow*3));
				$this->Cell(1,2.5,number_format($this->CFD["concepto"]["importe"][$row],2,'.',','),0,0,'R');
  				$totalrow=$totalrow+$rwheight+$totalrow2;
  				//if(trim($this->CFD["concepto"]["narrative"][$row]) != '' && !$unalinea){
					//$totalrow -= ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.3);
				//}
			}else{
				//echo "<br>Total row: factura: ".$totalrow."<br>";
				//echo "<br>Serv fact: ".$this->CFD["concepto"]["descripcion"][$row]."<br>";
  				$row=$row-1;
  				$this->printButton();
  				$this->AddPage();
				//----Logo
                $this->Image(dirname(__FILE__).'/copia.png',3,4,209,271);
				if (file_exists($this->CFD['general']['logo'])){
					//$this->Image($this->CFD['general']['logo'],157,6,50);
				}
				$this->TopPrint();
  				$totalrow=3;
  				$totalrow2=0;
  				$this->Line(3,169,208,169);
			}
 			$row++;
		//-------------------------------------------------------------
		}

		//********************************************************************************************************************
		$row=0;
		$this->SetFont('Arial','B',9.5);
		$this->SetXY(133,220+($row*3));
		$this->Cell(30,2.5,"Sub-Total",0,0,'R');
		$this->SetXY(166,220+($row*3));
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,220+($row*3));
		$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["subTotal"],2,'.',','),0,0,'R');
		if(strlen($this->CFD["comprobante"]["descuento"])>0){
			$row++;
			$this->SetXY(133,214+($row*3));
			$this->Cell(30,2.5,'Descuento',0,0,'R');
			$this->SetXY(166,214+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,214+($row*3));
			$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["descuento"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Trasladados*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["trasladado"]["cantidad"];$i++){
			$row++;
			 if($this->CFD["impuestos"]["trasladado"][$i]["impuesto"]=='IVA'){
   				 $this->ivasol =$this->CFD["impuestos"]["trasladado"][$i]["importe"];
			}
			$this->SetXY(133,214+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["trasladado"][$i]["impuesto"]." ".$this->CFD["impuestos"]["trasladado"][$i]["tasa"]." %",0,0,'R');
			$this->SetXY(166,214+($row*3));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,214+($row*3));
			$this->Cell(30,2.5,number_format($this->CFD["impuestos"]["trasladado"][$i]["importe"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Locales Trasladados************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["traslados"]["cantidad"];$i++){
			$row++;
			$this->SetXY(133,214+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["traslados"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["traslados"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,214+($row*3));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,214+($row*3));
			$this->Cell(30,2.5,number_format($this->CFD["impuestoslocales"]["traslados"]["importe"][$i],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Retenidos*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["retenido"]["cantidad"];$i++){
			$row++;
			$monto = $this->CFD["impuestos"]["retenido"][$i]["importe"];
  			$gtotal =$this->CFD["comprobante"]["subTotal"];
  		    $tasa =(100*$monto)/$gtotal;
			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=10.66)&&($tasa<=10.7)){
				$tasa=10.6667;
				$tasa = number_format($tasa, 4, ".", "");
			}else if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=7.33)&&($tasa<=7.4)){
				$tasa=7.3333;
				$tasa = number_format($tasa, 4, ".", "");
			}else{
				$tasa = number_format($tasa, 2, ".", "");
			}
			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($monto==$this->ivasol)){
				$tasa=100;
			}
			$this->SetXY(133,214+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["retenido"][$i]["impuesto"]." Ret. ".$tasa." %",0,0,'R');
			$this->SetXY(166,214+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,214+($row*3));
			$this->Cell(30,2.5,number_format($this->CFD["impuestos"]["retenido"][$i]["importe"],2,'.',','),0,0,'R');
		}
		//***************************Impuestos Locales Retenidos************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["retenidos"]["cantidad"];$i++){
			$row++;
			$this->SetXY(133,214+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["retenidos"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["retenidos"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,214+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,214+($row*3));
			$this->Cell(30,2.5,number_format($this->CFD["impuestoslocales"]["retenidos"]["importe"][$i],2,'.',','),0,0,'R');
		}

		//Subtotal
		$this->SetFont('Arial','B',9.5);
		$this->SetXY(133,228);
		$this->Cell(30,2.5,"IVA ".$this->CFD["addenda"]["moneda"],0,0,'R');
		$this->SetXY(166,228);
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,228);
		$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["impuesto"],2,'.',','),0,0,'R');

		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',11);
		$this->SetXY(133,235);
		$this->Cell(30,2.5,"TOTAL ".$this->CFD["addenda"]["moneda"],0,0,'R');
		$this->SetXY(166,235);
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,235);
		$this->Cell(30,2.5,number_format($this->CFD["comprobante"]["total"],2,'.',','),0,0,'R');
		//********************************************************************************************************************

		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',4.5);
		$this->SetXY(3,210);
		$this->multicell(200,2,"Sello\n".$this->CFD["comprobante"]["sello"],0,'J');
		//245 Cantidad en letras
		$this->SetFont('Arial','B',7);
		$this->SetXY(3,235);
		$this->multicell(128,2,strtoupper($this->CFD['general']['importe_letra']." ".$this->CFD["addenda"]["moneda"]),0,'J');
		$this->SetFont('Arial','B',4.5);
		$this->SetXY(2,268);

		$pie=$pie."\n\nESTE DOCUMENTO ES UNA ".utf8_decode('REPRESENTACIÓN')." IMPRESA DE UN CFD";
		$this->multicell(128,2,$pie,0,'J');

		if($this->Pages>1){
			$this->AddPage();
			$this->SetTextColor(0,0,0);
			$this->SetXY(3,45);
			$this->SetFont('Arial','B',5);
			$this->multicell(150,2,"RFC Emisor: ".$this->CFD["emisor"]["rfc"]." Folio: ".$this->CFD["comprobante"]["serie"].$this->CFD["comprobante"]["folio"].utf8_decode(" No. Aprobación: ").$this->CFD["comprobante"]["noAprobacion"].utf8_decode(" Año Aprobación: ").$this->CFD["comprobante"]["anoAprobacion"]." No. Certificado: ".$this->CFD["comprobante"]["noCertificado"]." Fecha: ".$this->CFD["comprobante"]["fecha"]."\nEsta hoja muestra la cadena original que pertenece a la factura con los datos mostrados en la parte superior",0,'L');
			$this->SetFont('Arial','B',5);
			$this->SetXY(190,45);
	    	$this->Cell(20,1,"Pagina ".$this->PageNo()." de ".$this->getPages(),0,0,'R');

			$this->setFillColor($this->CFD["addenda"]["colorDocument"]["r"],$this->CFD["addenda"]["colorDocument"]["g"],$this->CFD["addenda"]["colorDocument"]["b"]);
			$this->Rect(3,50, 208, 10,'F');
			$this->SetTextColor(255,255,255);
			$this->SetFont('Arial','B',14);
			$this->SetXY(3,55);
			$this->multicell(206,1.5,"Cadena Original",0,'C');

			$this->SetTextColor(0,0,0);
			$this->SetFont('Arial','B',4);
			$this->SetXY(3,62);
			$this->multicell(200,1.5,$this->CFD["comprobante"]["original"],0,'J');
		}else{
			$this->SetTextColor(0,0,0);
			$this->SetFont('Arial','B',4);
			$this->SetXY(3,170);
			$this->multicell(200,1.5,"Cadena Original\n".$this->CFD["comprobante"]["original"],0,'J');
		}
	}
	private function printButton(){
		//********************************************************************************************************************
		/*$row=0;
		$this->SetFont('Arial','B',9.5);
		$this->SetXY(133,244+($row*3));
		//$this->Cell(30,2.5,"Sub-Total",0,0,'R');
		$this->SetXY(166,244+($row*3));
		//$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,244+($row*3));
		//$this->Cell(30,2.5,"---------------",0,0,'R');
		if(strlen($this->CFD["comprobante"]["descuento"])>0){
			$row++;
			$this->SetXY(133,244+($row*3));
			$this->Cell(30,2.5,'Descuento',0,0,'R');
			$this->SetXY(166,244+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,244+($row*3));
			$this->Cell(30,2.5,"---------------",0,0,'R');
		}
		//***************************Impuestos Trasladados*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["trasladado"]["cantidad"];$i++){
			$row++;
			 if($this->CFD["impuestos"]["trasladado"][$i]["impuesto"]=='IVA'){
   				 $this->ivasol =$this->CFD["impuestos"]["trasladado"][$i]["importe"];
			}
			$this->SetXY(133,244+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["trasladado"][$i]["impuesto"]." ".$this->CFD["impuestos"]["trasladado"][$i]["tasa"]." %",0,0,'R');
			$this->SetXY(166,244+($row*3));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,244+($row*3));
			$this->Cell(30,2.5,"---------------",0,0,'R');
		}
		//***************************Impuestos Locales Trasladados************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["traslados"]["cantidad"];$i++){
			$row++;
			$this->SetXY(133,244+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["traslados"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["traslados"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,244+($row*3));
			$this->Cell(10,2.5,"$ +",0,0,'L');
			$this->SetXY(180,244+($row*3));
			$this->Cell(30,2.5,"---------------",0,0,'R');
		}
		//***************************Impuestos Retenidos*******************************************************************
		for($i=1;$i<=$this->CFD["impuestos"]["retenido"]["cantidad"];$i++){
			$row++;
			$monto = $this->CFD["impuestos"]["retenido"][$i]["importe"];
  			$gtotal =$this->CFD["comprobante"]["subTotal"];
  		    $tasa =(100*$monto)/$gtotal;
			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=10.66)&&($tasa<=10.7)){
				$tasa=10.6667;
				$tasa = number_format($tasa, 4, ".", "");
			}else if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($tasa>=7.33)&&($tasa<=7.4)){
				$tasa=7.3333;
				$tasa = number_format($tasa, 4, ".", "");
			}else{
				$tasa = number_format($tasa, 2, ".", "");
			}

			if(($this->CFD["impuestos"]["retenido"][$i]["impuesto"]=='IVA')&&($monto==$this->ivasol)){
				$tasa=100;
			}
			$this->SetXY(133,244+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestos"]["retenido"][$i]["impuesto"]." Ret. ".$tasa." %",0,0,'R');
			$this->SetXY(166,244+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,244+($row*3));
			$this->Cell(30,2.5,"---------------",0,0,'R');
		}
		//***************************Impuestos Locales Retenidos************************************************************
		for($i=1;$i<=$this->CFD["impuestoslocales"]["retenidos"]["cantidad"];$i++){
			$row++;
			$this->SetXY(133,244+($row*3));
			$this->Cell(30,2.5,$this->CFD["impuestoslocales"]["retenidos"]["impuesto"][$i]." ".$this->CFD["impuestoslocales"]["retenidos"]["tasa"][$i]." %",0,0,'R');
			$this->SetXY(166,244+($row*3));
			$this->Cell(10,2.5,"$ -",0,0,'L');
			$this->SetXY(180,244+($row*3));
			$this->Cell(30,2.5,"---------------",0,0,'R');
		}
		$this->SetTextColor(255,255,255);
		$this->SetFont('Arial','B',11);
		$this->SetXY(133,235);
		$this->Cell(30,2.5,"TOTAL ".$this->CFD["addenda"]["moneda"],0,0,'R');
		$this->SetXY(166,235);
		$this->Cell(10,2.5,"$",0,0,'L');
		$this->SetXY(180,235);
		$this->Cell(30,2.5,"---------------",0,0,'R');
		//********************************************************************************************************************

		$this->SetTextColor(0,0,0);
		$this->SetFont('Arial','B',4.5);
		$this->SetXY(3,238);
		//$this->multicell(204,2,"Sello\n".$this->CFD["comprobante"]["sello"],0,'J');
		//245 Cantidad en letras
		$this->SetFont('Arial','B',7);
		$this->SetXY(3,249);
		//$this->multicell(128,2,strtoupper($this->CFD['general']['importe_letra']." ".$this->CFD["addenda"]["moneda"]),0,'J');
	   */	$this->SetFont('Arial','B',4.5);
		$this->SetXY(2,268);

		$pie=$pie."\n\nESTE DOCUMENTO ES UNA ".utf8_decode('IMPRESIÓN')." DE UN COMPROBANTE FISCAL DIGITAL";
		$this->multicell(128,2,$pie,0,'J');
	}
	private function getTotalPages2(){
		$row=0;
		$totalrow=2;
		$rwheight=0;
		$unalinea = true;
		$cont_live = 2;
		$aditional_page = 0;
		$cont_live_ant = 0;
		while($row<=$this->CFD["conceptos"]["cantidad"]){

			if($this->CFD["concepto"]["narrative"][$row] != '' ){
				$narr = $this->CFD["concepto"]["narrative"][$row];
				$narr = str_replace('\n\r',"\n",utf8_decode(strip_tags(html_entity_decode(html_entity_decode(html_entity_decode($narr))))));
				$rwheight_narr = 0;
				$qty_narr = substr_count($narr,"\n");
				if($qty_narr > 0){
					$lines = explode("\n",$narr);
					foreach($lines as $ind=>$line){
						$rwheight_narr += intval(strlen($line)/ 100);
						$rwheight_narr -= 1;
					}
					$rwheight_narr += $qty_narr;
				}else{
					$rwheight_narr = intval(strlen($narr)/ 100)+1;
				}

				if((strlen($narr) % 100)>0){
					$rwheight_narr= $rwheight_narr+1;
				}

				/*if($unalinea)
					$qty_narr = substr_count($narr,"\n");
				else
					$qty_narr;*/

				$ajuste = ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.6);
				//$totalrow += (($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.6);
				//$cont_live += ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.6);
				$totalrow += $ajuste;
				$cont_live += $ajuste;
			}else{
				$totalrow += 0.5;
				$cont_live += 0.5;
			}

			//$totalrow += ((($rwheight_narr+$qty_narr) = ($rwheight_narr+$qty_narr) - (($rwheight_narr+$qty_narr)/3)*0.6);
			//$cont_live += ((($rwheight_narr+$qty_narr) = ($rwheight_narr+$qty_narr) - (($rwheight_narr+$qty_narr)/3)*0.6);

			echo "<br><br>";
			echo "<br>row1: $row - ".$totalrow." - $cont_live<br>";
			echo "<br><br>";

			$txt=$this->CFD["concepto"]["descripcion"][$row];
			$rwheight= intval(strlen($txt) / 100);
			if((strlen($txt) % 100)>0){
  				$rwheight= $rwheight+1;
  			}

  			if(trim($this->CFD["concepto"]["narrative"][$row]) == ''){
				$rwheight= $rwheight+1;
			}

			$totalrow=$totalrow+$rwheight;
			$cont_live = $cont_live+$rwheight;


			echo "<br><br>";
			echo "<br>row: $row - ".$totalrow." - $cont_live<br>";
			echo "<br><br>";

			if($cont_live > 30){
				$sobrante = $cont_live - $cont_live_ant;
				$totalrow += $sobrante;
				echo "<br>hoja: ".$totalrow." - $cont_live - $cont_live_ant<br>";
				echo "<br>Serv pag $row: ".$this->CFD["concepto"]["descripcion"][$row]."<br>";
				$row--;
				$cont_live = 2;
				//break;
			}
			$row++;
			$cont_live_ant = $cont_live;
		}
		echo "<br>Totalrow: ".$totalrow."<br>";
		$p = intval($totalrow /30);
		if(($totalrow % 30)>0){
  			$p= $p+1;
  		}

		if($p>1){
  			$p=$p+1;
  		}
		return $p;
	}

	private function getTotalPages(){
		$row=0;
		$totalrow=2;
		$rwheight=0;
		$unalinea = true;
		$cont_live = 2;
		$aditional_page = 0;
		$cont_live_ant = 0;
		while($row<=$this->CFD["conceptos"]["cantidad"]){

			//-----------------Conceptos-----------------------------------
			$rwheight_narr = 0;
			$y_narr = 0;
			if(trim($this->CFD["concepto"]["narrative"][$row]) != ''){

				//Otro metodo
				$narr = $this->CFD["concepto"]["narrative"][$row];
				$narr = str_replace('\n\r',"\n",utf8_decode(strip_tags(html_entity_decode(html_entity_decode(html_entity_decode($narr))))));
				$rwheight_narr = 0;
				$qty_narr = substr_count($narr,"\n");
				if($qty_narr > 0){
					$lines = explode("\n",$narr);
					foreach($lines as $ind=>$line){
						$rwheight_narr += intval(strlen($line)/ 100);
						//Se le quita línea, porque si lleva salto de línea entonces se reduce esto.
						$rwheight_narr -= 1;
					}
					$rwheight_narr += $qty_narr;
				}else{
					$rwheight_narr = intval(strlen($narr)/ 100)+1;
					if($rwheight_narr == 0){
						$unalinea = true;
					}else{
						$unalinea = false;
					}
				}

				if((strlen($narr) % 100)>0){
					$rwheight_narr= $rwheight_narr+1;
				}

				$y_narr =  72+($totalrow*3);

				$ajuste = ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.6);
				$totalrow += $ajuste;
				$cont_live += $ajuste;
			}else{
				//Si es la primera línea y no tiene comentario extra, le quitamos espacio.
				if($totalrow==3){
					$totalrow -= 1.5;
				}
				$cont_live += 0.5;
				$totalrow += 0.5;
			}

			$txt=$this->CFD["concepto"]["descripcion"][$row];
			$txt = str_replace('\n\r',"\n",strip_tags(html_entity_decode(html_entity_decode(html_entity_decode($txt)))));
			$rwheight= intval(strlen($txt)/ 100) + $rwheight_narr;
			if((strlen($txt) % 100)>0){
  				$rwheight= $rwheight+1;
  			}

  			if(trim($this->CFD["concepto"]["narrative"][$row]) == ''){
				$rwheight= $rwheight+1;
			}

  			$qty = substr_count($txt,"\n");
  			$rwheight += $qty;

			if(($cont_live+$rwheight) > 30){
				//echo "<br><br>";
				//echo "<br>rowM: $row - ".$totalrow." - $cont_live<br>";
				//echo "<br><br>";
				$sobrante = ($cont_live - $cont_live_ant + $rwheight);
				$totalrow += $sobrante;
				$row--;
				$cont_live = 2;
				//break;
			}else{
				$cont_live+=$rwheight;
				$totalrow=$totalrow+$rwheight;
  				if(trim($this->CFD["concepto"]["narrative"][$row]) != '' && !$unalinea){
					//$totalrow -= ($rwheight_narr+$qty_narr -= (($rwheight_narr+$qty_narr)/3)*0.3);
					$totalrow -= $ajuste;
					$cont_live -= $ajuste;
				}
				//echo "<br><br>";
				//echo "<br>rowm: $row - ".$totalrow." - $cont_live<br>";
				//echo "<br><br>";
				}
				$row++;
				$cont_live_ant = $cont_live;
		}

		$p = intval($totalrow /30);
		if(($totalrow % 30)>0){
  			$p= $p+1;
  		}

		if($p>1){
  			$p=$p+1;
  		}
		return $p;
	}

}
?>