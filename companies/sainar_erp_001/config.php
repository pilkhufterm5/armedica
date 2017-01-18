<?php
//$Data='<label>Sucursal 1</label>';
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_sainar';
$dbpassword = 'cEN2rRnGTewPbpEh';

$dbpassword = 'cEN2rRnGTewPbpEh';



$rh_AllowCancelInvoice = array('alicia','carlam','gerardo');

$ServidoresdeCorreos=array();
        //$AddCC='briscia.sada@armedica.com.mx';
	$AddCC = "claudia.barrientos@armedica.com.mx";
	/*
        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes_qro@armedica.com.mx",
            'Password' => '$QrO%_%0014&',
        );*/

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes@armedica.com.mx",
            //'Password' => '$MtY%_%0003&',
            'Password' => '$_@R_%ReF_0003%_@r&',// Se actualizo contraseña Angeles Perez 2016-01-27
        );

