<?php
	$rutaImagenOriginal="headFactura.jpg";
	$img_original = imagecreatefromjpeg($rutaImagenOriginal);
	$max_ancho = 600;
	$max_alto = 150;
	list($ancho,$alto)=getimagesize($rutaImagenOriginal);
	$x_ratio = $max_ancho / $ancho;
	$y_ratio = $max_alto / $alto;
	if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho
		$ancho_final = $ancho;
		$alto_final = $alto;
	}elseif (($x_ratio * $alto) < $max_alto){
		$alto_final = ceil($x_ratio * $alto);
		$ancho_final = $max_ancho;
	}else{
		$ancho_final = ceil($y_ratio * $ancho);
		$alto_final = $max_alto;
	}
	
	$tmp=imagecreatetruecolor($ancho_final,$alto_final);
	imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);
	imagedestroy($img_original);

	Header("Content-type: image/jpeg");
	imagejpeg($tmp);