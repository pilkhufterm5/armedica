<?php
//$Data='<label>sme_erp_001</label>';
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
$Data='<center>'.ucfirst($WebERP[0]).'</center>';

$dbuser = 'erp_arsain';
$dbpassword = 'pFRcsAbrSwr7TKyn';
$prefijoAfiliados='';
