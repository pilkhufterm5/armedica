<?php
/*
 * $_GET['Invoice'] = Factura.
 * $_GET['UUID'] = se usa para el nombre del Barcode generado.
 * $_GET['Amount'] = Total de la Factura.
 * */

include_once 'PNArMedica.php';
$issuer = "00000V";
// Este $issuer es de Prueba '00000H'

if (!empty($_GET['Invoice']) && $_GET['UUID']) {

    //$_GET['Amount'] = 100.50;

    $AMT = number_format($_GET['Amount'],2,'',''); //EL Monto debe Tener 2 Decimales son el Punto Ex. 100.50 = 10050

    $InvoiceRef = str_pad($_GET['Invoice'], 10, "0", STR_PAD_LEFT);    //Relleno con ceros ala Izq para completar 10 caracteres;
    $_InvoiceAmount = str_pad($AMT, 6, "0", STR_PAD_LEFT);    //Relleno con ceros ala Izq -> este campo es un decimal(8,2)
    $InvoiceAmount = str_pad($_InvoiceAmount, 8, "0",STR_PAD_LEFT);    // Los 2 ultimos numeros son decimales

    $referencia = $InvoiceRef . $InvoiceAmount;
    //echo $referencia;
    //$referencia = "000000000100100000";
    $generator = new RUGenerator();
    $paynetReference = $generator->createRU($issuer, $referencia);

    include_once ('Image/Barcode.php');
    Image_Barcode::draw($paynetReference, 'code128', 'png', $_GET['UUID'],$bSendToBrowser = false, $height = 60, $barwidth = 2, $file = '',$CodigoBarras);
}

