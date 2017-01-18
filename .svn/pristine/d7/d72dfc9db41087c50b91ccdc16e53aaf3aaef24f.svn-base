<?php

/* $Revision: 266 $ */
/*
 * rleal
 * Mar 21 2011
 * Se crear para imprimir alguna transferencia de almacén
 */
if (isset($_POST['BatchNo'])){
    header( "Location: ./PDFStockLocTransfer.php?TransferNo=".$_POST['BatchNo'] ) ;
    //header("Location: http://www.example.com/");
    exit;
}
$PageSecurity = 2;
include ('includes/session.inc');
// bowikaxu realhost March 2008 - print amount in letters
include ('includes/class.pdf.php');

if (isset($_GET['BatchNo'])){
	$_POST['BatchNo'] = $_GET['BatchNo'];
        echo $_POST['BatchNo'];
}

if (!isset($_POST['BatchNo'])){

     $title = _('Imprimir Transferencia de Almac&eacute;n');
     include ('includes/header.inc');
     echo "<FORM METHOD='post' action=" . $_SERVER['PHP_SELF'] . '>';
     echo '<P>' . _('Ingrese el n&uacute;mero de transferencia') . ': <INPUT TYPE=text NAME=BatchNo MAXLENGTH=6 SIZE=6>';
     echo "<CENTER><INPUT TYPE=SUBMIT NAME='EnterBatchNo' VALUE='" . _('Create PDF') . "'></CENTER>";
}

?>