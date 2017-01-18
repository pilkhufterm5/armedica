<?php


$PageSecurity = 2;

include ('includes/session.inc');

$sql ="select gstno from companies limit 1;";
$rs=DB_query($sql,$db);
if($rw = DB_fetch_array($rs)){
  $RFC=$rw['gstno'];
}else{
  $RFC="NOVALIDO";
}

require_once('CFD22Manager.php');

if(isset($_POST['month'])&&isset($_POST['year'])){
    $CFDManager = CFD22Manager::getInstance();
    try{
        $onError=false;
        $Reporte =   $CFDManager->reporteMensual($_POST['month'],$_POST['year']);
        $LF = 0x0A;
	    $CR = 0x0D;
	    $nl = sprintf("%c%c",$CR,$LF);
        $Reporte=str_replace('\n',$nl,$Reporte); 
        if(strlen($Reporte)<10){
          throw new Exception('No Existe Reporte para el mes'.$_POST['month'].' del '.$_POST['year']);
        }
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"1".$RFC.$_POST['month'].$_POST['year'].".txt\"");
        //header("Content-length: ".sizeof($Reporte));
        header("Cache-control: private");
        echo $Reporte;
        die;
    }catch(Exception $e){
      $onError=true;
    }
}
$title = _ ( 'Reporte CFD Mensual' );
include ('includes/header.inc');
if($onError){
    echo '<div class="error"><p><b>' . _('ERROR') . '</b> : ' . $e->getMessage() . '<p></div>';
}
?>

<form name="Form" method="POST" enctype="multipart/form-data"
	style="width: 100%;" action="">

<center>
<table cellpadding="0" cellspacing="2" borde="0" width="35%">
	<tr>
		<td colspan="2" align="center"><b>Filtro de seleccion</b></td>
	</tr>
	<tr>
		<td>Mes:</td>
		<td><select name="month" style="width: 100%">
            <option value="01" >Enero</option>
            <option value="02" >Febrero</option>
            <option value="03" >Marzo</option>
            <option value="04" >Abril</option>
            <option value="05" >Mayo</option>
            <option value="06" >Junio</option>
            <option value="07" >Julio</option>
            <option value="08" >Agosto</option>
            <option value="09" >Septiembre</option>
            <option value="10" >Octubre</option>
            <option value="11" >Noviembre</option>
            <option value="12" >Diciembre</option>
        </select>
        </td>
	</tr>
	<tr>
		<td>A&ntilde;o:</td>
		<td><select name="year" style="width: 100%">
            <option value="2010">2010</option>
            <option value="2011">2011</option>
            <option value="2012">2012</option>
            <option value="2013">2013</option>
            <option value="2014">2014</option>
            <option value="2015">2015</option>
            <option value="2016">2016</option>
        </select></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
			<input type="submit" name="download" value="Descargar Reporte" style="width: 100%; display:inline;float:left" />
			</td>
    </tr>
</table>
</center>
</form>
<br />
<br />
<br />
<br />
<?php
include ('includes/footer.inc');
?>