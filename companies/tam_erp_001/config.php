<?php
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_tam';
$dbpassword = 'PNVpqdxXRnKdRCta';

$passwordAdjustment = 'Ave0814aR';

//ACCOUNTS that can cancel invoices
$rh_AllowCancelInvoice = array('realhost', 'alicia','margaritas');



$AddCC = "atencionclientes_tampico@armedica.com.mx";

$ServidoresdeCorreos=array();

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
//            'Username' => "ATENCIONCLIENTES_TAMPICO@ARMEDICA.COM.MX",
            'Username' => "atencionclientes_tampico@armedica.com.mx",
            //'Password' => '$TaM%_%0006&',
            'Password' => '$_@R_%TaM_0001%_@r&',// Se cambio contraseña Angeles Perez 2016-01-27
        );
