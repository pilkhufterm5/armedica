<?php
//$Data='<label>Sucursal 1</label>';
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_mga';

$dbpassword = 'mH9QqutFmA44rUjv';
$rh_AllowCancelInvoice = array('alicia', 'alfredop');

$ServidoresdeCorreos=array();
        
        $AddCC='anselmo.salas@armedica.com.mx';

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes_ags@armedica.com.mx",
            //'Password' => '$AgS%_%0006&',
            'Password' => '$_@R_%AgS_0001%_@r&', // Se cambio contraseña Angeles Perez 2016-01-27
        );

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes@armedica.com.mx",
            'Password' => '$MtY%_%0003&',
        );


