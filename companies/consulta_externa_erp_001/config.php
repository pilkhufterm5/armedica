<?php
$Data = "";
$Data .= <<<NOMBRE
<label style='text-align:center; 
width: 100%;
display: block;
padding-left: 0;
font-size: xx-large;
'>CONSULTA</label>
<label style='text-align:center; 
width: 100%;
display: block;
padding-left: 0;
font-size: xx-large;
'>EXTERNA</label>
<br>

<label style='text-align:center; padding-left: 0;
width: 100%;
display: block;
'>SERVICIOS MEDICOS <br />DE EMERGENCIAS</label><style>#logo_empresa {display:none;}</style>
NOMBRE;
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_externo';
$dbpassword = 'JtvLpz3BrzqwAdR6';

$passwordAdjustment = 'Ave0814aR';
