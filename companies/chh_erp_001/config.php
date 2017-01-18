<?php
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_chi';
$dbpassword = 'c5PEGAFH8VESEPq5';

$passwordAdjustment = 'Ave0814aR';


$AddCC = "atencionclientes_chh@armedica.com.mx";

$ServidoresdeCorreos=array();

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
//            'Username' => "ATENCIONCLIENTES_CHH@ARMEDICA.COM.MX",
            'Username' => "atencionclientes_chh@armedica.com.mx",
            //'Password' => "Chi0017",
	    //'Password' => '$ChI%_%0019&',
	    'Password' => '$_@R_%ChI_0005%_@r&',// Se cambio contraseña Angeles Perez 2016-01-27
        );






//ACCOUNTS that can cancel invoices
$rh_AllowCancelInvoice = array('alicia', 'johanap');

