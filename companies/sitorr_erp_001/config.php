<?php
$Data = "";
$Data .= <<<NOMBRE
<label style='text-align:center; 
width: 100%;
display: block;
padding-left: 0;
font-size: xx-large;
'>SERVICIOS INTEGRALES TORREON</label><br>

<label style='text-align:center; padding-left: 0;
width: 100%;
display: block;
'>SERVICIOS MEDICOS <br />DE EMERGENCIAS</label><style>#logo_empresa {display:none;}</style>
NOMBRE;
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'usr_sitorr';
$dbpassword = 'Sitorr01';

$passwordAdjustment = 'Ave0814aR';


$AddCC = "atencionclientes_sitorr@armedica.com.mx";

$ServidoresdeCorreos=array();

        $ServidoresdeCorreos[] = array(
            'Host' => 'smtp.armedica.com.mx',
            'SMTPSecure' => "tls",
            'Port' => 587,
            'SMTPAuth' => true,
//            'Username' => "ATENCIONCLIENTES_CHH@ARMEDICA.COM.MX",
            'Username' => "atencionclientes_sitorr@armedica.com.mx",
            //'Password' => "Sitorr01",
	    'Password' => 'Sitorr01',
        );






//ACCOUNTS that can cancel invoices
$rh_AllowCancelInvoice = array('realhost', 'alicia', 'zayras');

