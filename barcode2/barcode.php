<?php
	require("barcode.inc.php");

	// bowikaxu realhost january 2008
	if (isset($_REQUEST['encode'])){
		$encode=$_REQUEST['encode'];
	}else {
		$encode = '';
	}
	$bar= new BARCODE();
	
	if($bar==false)
		die($bar->error());
	// OR $bar= new BARCODE("I2O5");

	// bowikaxu realhost january 2008
	if (isset($_REQUEST['bdata'])){
		$barnumber=$_REQUEST['bdata'];
	}
	//$barnumber="200780";
	//$barnumber="801221905";
	//$barnumber="A40146B";
	//$barnumber="Code 128";
	//$barnumber="TEST8052";
	//$barnumber="TEST93";
	
	$bar->setSymblogy($encode);
	
	// bowikaxu realhost january 2008
	if (isset($_REQUEST['height'])){
		$bar->setHeight($_REQUEST['height']);
	}
	
	//$bar->setFont("arial");
	
	// bowikaxu realhost january 2008
	if (isset($_REQUEST['scale'])){
		$bar->setScale($_REQUEST['scale']);
	}
	
	
	// bowikaxu realhost january 2008
	if (isset($_REQUEST['color']) AND isset($_REQUEST['bgcolor'])){
		$bar->setHexColor($_REQUEST['color'],$_REQUEST['bgcolor']);
	}
	
	/*$bar->setSymblogy("UPC-E");
	$bar->setHeight(50);
	$bar->setFont("arial");
	$bar->setScale(2);
	$bar->setHexColor("#000000","#FFFFFF");*/

	//OR
	//$bar->setColor(255,255,255)   RGB Color
	//$bar->setBGColor(0,0,0)   RGB Color

  	if(isset($_REQUEST['type']) AND isset($_REQUEST['file'])){
	$return = $bar->genBarCode($barnumber,$_REQUEST['type'],$_REQUEST['file']);
  	}else {
  		$return = false;
  	}
	if($return==false)
		$bar->error(true);
	
?>