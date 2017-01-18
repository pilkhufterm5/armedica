<?php
//$Data='<label>Sucursal 1</label>';
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_qro';

$dbpassword = 'LsRvrUaUx4t3jz57';
$rh_AllowCancelInvoice = array('alicia', 'juliom');

$ServidoresdeCorreos=array();
        $AddCC='briscia.sada@armedica.com.mx';
        
        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes_qro@armedica.com.mx",
            //'Password' => '$QrO%_%0014&',
            'Password' => '$_@R_%QrO_0001%_@r&',// Se cambio contraseña Angeles Perez 2016-01-27
        );

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
            'Username' => "atencionclientes@armedica.com.mx",
            'Password' => '$MtY%_%0003&',
        );
