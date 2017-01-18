<?php
$PageSecurity = 2;
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size','200M');

unset($_COOKIES['debug'],$_COOKIE['debug']);
include('includes/session.inc');
global $FontSize,$Escala;
if (!isset($PaperSize)){
	$PaperSize = $_SESSION['DefaultPageSize'];
}

$Data=simplexml_load_string(str_replace(array('<th>','</th>','<th '),array('<td>','</td>','<td '),$_REQUEST['tabla']));
$Data=json_decode(json_encode($Data),true);

$maxW=(int)($Data['@attributes']['xwidth']);
$max=(int)($Data['@attributes']['xheight']);
if(isset($Data['@attributes']['font'])&&is_file('./fonts/'.$Data['@attributes']['font'].'.afm')){
	$Font='./fonts/'.$Data['@attributes']['font'].'.afm';
}
$Escala=1;

unset($_REQUEST['tabla']);
if($maxW<627){
	if($PaperSize=='A4')
		$PaperSize.='_Landscape';
	if($PaperSize=='letter')
		$PaperSize.='_landscape';
// 	$Page_Width=595;
// 	$Page_Height=842;
// 	$Top_Margin=30;
// 	$Bottom_Margin=30;
// 	$Left_Margin=40;
// 	$Right_Margin=30;
}
$FontSize=9;
include('includes/PDFStarter.php');

$Escala=($Page_Width-$Left_Margin)/$maxW;

$pdf->selectFont($Font);
$YPos=$Page_Height-$Top_Margin;
if(isset($Data['tbody'])){
	$tr=$Data['tbody']['tr'];
	$th=$Data['thead']['tr'];
}
else 
	if(isset($Data['tr'])){
		$tr=$Data['tr'];
		$th=$tr[0];
	}
$Encabezados=true;
foreach($tr as $fila){
	if($YPos<=$Bottom_Margin){//tr
		$pdf->newPage();
		$YPos=$Page_Height-$Top_Margin;
		$Encabezados=true;
	}
	
	if($Encabezados){
		$Encabezados=false;
		foreach($th as $filaH){
			$XPos=$Left_Margin;
			
// 			$td=array_pop($filaH);
			
			if(isset($filaH['@attributes'])&&isset($filaH['@attributes']['style']))
				$estilo=$filaH['@attributes']['style'];
			else $estilo="";
			foreach($filaH as $elemento=>$celdasX)
			if($elemento!='@attributes')
				foreach($celdasX as $celda){
					MostrarCelda($celda,$YPos,$XPos,$pdf,$estilo);
			}
			$YPos-=$FontSize;//((float)$filaH['@attributes']['xheight'])*$Escala;
		}
	}
	if(isset($fila['@attributes'])&&isset($fila['@attributes']['style']))
		$estilo=$fila['@attributes']['style'];
	else $estilo="";
	$XPos=$Left_Margin;
	if(isset($fila['td']))
		foreach($fila['td'] as $celda)
			MostrarCelda($celda,$YPos,$XPos,$pdf,$estilo);
	$YPos-=$FontSize;//((float)$fila['@attributes']['xheight'])*$Escala;
}
function MostrarCelda($celda,&$YPos,&$XPos,&$pdf,$estilo=''){
	global $FontSize,$Escala;
	if(!isset($celda['@attributes']['hiden'])){
		if(isset($celda['@attributes'])&&isset($celda['@attributes']['style']))
			$estilo=$celda['@attributes']['style'];
		$estilo1=explode(';',$estilo);
		$estilo=array();
		foreach($estilo1 as $ll=>$val){
			list($llave,$valor)= explode(':',$val);
			$llave=trim($llave);
			$estilo[$llave]=$valor;
		}
		$width=((int)$celda['@attributes']['xwidth'])*$Escala;
		$LeftOvers =end($celda);
		$YPos_=$YPos;
		if(isset($celda['@attributes']['alingn']))
			$alingn=$celda['@attributes']['alingn'];
		else
		if(isset($estilo['align']))
			$alingn=$estilo['align'];
		if(isset($estilo['border']))
			$borde=$estilo['border'];
		else 
			$estilo['border']='';
		do{
			$LeftOvers = trim($pdf->addTextWrap($XPos,$YPos_,$width,$FontSize,$LeftOvers,$alingn,$borde));
			$YPos_-=$FontSize;
		}while(trim($LeftOvers)!='');
		$XPos+=$width;
	}
}
// foreach($Data as $data=>$valor)//tabla
// 	if($data!='@attributes'){
// 		foreach($valor as $llave=>$fila){//tbody
// 			if($fila!='@attributes'&&(!isset($valor['@attributes']['hiden']))){
// 				if($YPos<=$Bottom_Margin){//tr
// 					$pdf->newPage();
// 					$YPos=$Page_Height-$Top_Margin;
// 				}
// 				foreach($fila as $camp=>$celdas){//td
// 					$XPos=$Left_Margin;
// 					if($camp!='@attributes'){
// 						foreach($celdas as $cell=>$celda)
// 						if(!isset($celda['@attributes']['hiden'])){
// 							$alingn='left';
// 							if(strpos(' '.$celda['@attributes']['class'],'tableheader')>0||$camp=='th')
// 								$alingn='center';
// 							$width=((int)$celda['@attributes']['xwidth'])*$Escala;	
// 							$LeftOvers =end($celda);
// 							$YPos_=$YPos;
// 							while(trim($LeftOvers)!=''){
// 								$LeftOvers = trim($pdf->addTextWrap($XPos,$YPos_,$width,$FontSize,$LeftOvers,$alingn));
// 								$YPos_-=$FontSize;
// 							}
// 							$XPos+=$width;
// 						}
// 					}
// 				}
// 				$YPos-=((float)$fila['@attributes']['xheight'])*$Escala;
// 			}
			
// 		}
// 	}
	
$_REQUEST['data']= base64_encode($pdf->output());
echo json_encode($_REQUEST);
