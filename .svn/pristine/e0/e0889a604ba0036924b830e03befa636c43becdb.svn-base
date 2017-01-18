<?php
set_time_limit(0);
//error_reporting(0);
ini_set('memory_limit','768M');
require_once ('autoload.php');
ini_set("session.cookie_lifetime",10800);

Config::noCACHE();
$width=(isset($_GET['w'])?$_GET['w']:'200');
$height=(isset($_GET['h'])?$_GET['h']:'200');
$quality=(isset($_GET['q'])?$_GET['q']:'80');
if((strlen($_GET['p'])>0) && (file_exists('/var/www/vhosts/softwareservicio.com/subdomains/bordados/httpdocs/bordados/companies/'.Config::GENERAL_DB.'/reportwriter/'.$_GET['p'].'.jpg'))){
    $img = new Thumbnail('/var/www/vhosts/softwareservicio.com/subdomains/bordados/httpdocs/bordados/companies/'.Config::GENERAL_DB.'/reportwriter/'.$_GET['p'].'.jpg');
    $img->size_width($width);
    $img->size_height($height);
    $img->jpeg_quality($quality);
    $img->show();
}else{
    $img = new Thumbnail('imgs/filenotfound.gif');
    $img->size_width($width);
    $img->size_height($height);
    $img->jpeg_quality($quality);
    $img->show();
}
?>