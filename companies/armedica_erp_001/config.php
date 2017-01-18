<?php
//$Data='<label>Sucursal 1</label>';
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
$Data='<center>'.ucfirst($WebERP[0]).'</center>';
