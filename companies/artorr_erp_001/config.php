<?php

$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_trn';
$dbpassword = 'vYPDVRBCErpVHKuU';

$passwordAdjustment = 'Ave0814aR';


$AddCC = "";

$ServidoresdeCorreos=array();

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes_tor@armedica.com.mx",
	    //'Password' => '$ToR%_%0070&',
	    //'Password' => '$ToR%_%0002&',// Se agrego la nueva contraseña 2016/01/05 Angeles Perez
	    'Password' => '$_@R_%ToR_0003%_@r&',// Se cambio nuevamente la contraseña Angeles Perez 2016-01-27
        );






//ACCOUNTS that can cancel invoices
$rh_AllowCancelInvoice = array('gerardo', 'alicia','marthas');

