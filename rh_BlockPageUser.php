<?php
    /**
     * 	REALHOST 17 DE ABRIL DEL 2010
     * 	POS DEL WEBERP
     * 	VERSION 1.0
     * 	RICARDO ABULARACH GARCIA
     * */

$PageSecurity = 2;
include('includes/session.inc');
$title = _('Bloquear Funciones por Usuario');
include('includes/header.inc');
echo '<A HREF="'. $rootpath . '/index.php?&Application=system&' . SID . '">'. _('Back to Menu'). '</A><BR>';

$path=dirname(__FILE__);
$directorio=dir($path);
$FilesWeb=array();
while ($archivo = $directorio->read()){
    if($archivo!="." OR $archivo!=".."){
        if (strtolower(substr($archivo, -3) == "php")){
            array_push($FilesWeb,$archivo);
        }
    }
}
$directorio->close();
natsort($FilesWeb);
//echo  count($FilesWeb);

$sql ="select secroleid,secrolename from securityroles";
$rs=DB_query($sql,$db);
$CmbRole = array();
while($rw = DB_fetch_array($rs)){
    $CmbRole[$rw['secroleid']] =  $rw['secrolename'];
}
?>
<script>
    function saveChange(pageid){
        var title = document.getElementById("title"+pageid).value;
        var pagesec = document.getElementById("cmb"+pageid).value;
        var desc = document.getElementById("desc"+pageid).value;
        var chk = document.getElementById("chk"+pageid).checked;

        document.getElementById('pageid').value=pageid;
        document.getElementById('PageSecurity').value=pagesec;
        document.getElementById('title').value=title;
        document.getElementById('description').value=desc;
        if(chk){
        document.getElementById('active').value=1;
        }else{
        document.getElementById('active').value=0;
        }
        document.frmSave.submit();
    }
</script>
<form name="frmSave" method="post">
    <input type="hidden" id="pageid" name="pageid" value="" />
    <input type="hidden" id="PageSecurity" name="PageSecurity" value="" />
    <input type="hidden" id="active" name="active" value="" />
    <input type="hidden" id="title" name="title" value="" />
    <input type="hidden" id="description" name="description" value="" />
</form>
<?php

if(isset($_POST['pageid'])){
  $sql="update scripts set  pagedescription='".$_POST['description']."',
                            title='".$_POST['title']."',
                            pagesecurity='".$_POST['PageSecurity']."',
                            active='".$_POST['active']."' where pageid='".$_POST['pageid']."'";
  DB_query($sql,$db);
}

echo "<form name='' method='POST'> ";
  echo "<center><table>
    <tr>
        <td class='tableheader' ></td>
        <td class='tableheader' >Titulo</td>
        <td class='tableheader' >Seguridad</td>
        <td class='tableheader' >Archivo</td>
        <td class='tableheader' ></td>
    </tr> ";
foreach($FilesWeb as $page){
  $script = file_get_contents (dirname(__FILE__).'/'.$page);
  $pos1 = strpos($script, '$PageSecurity');
  $pos2 = strpos($script, ';',$pos1+strlen('$PageSecurity'));
  $pagesec =false;
  $havetitle=false;
  if ($pos1 !== false && $pos2!==false) {
        eval(substr($script,$pos1,$pos2-$pos1+1));
        $pagesec=true;
  }
  $pos1 = strpos($script, '$title');
  $pos2 = strpos($script, ';',$pos1+strlen('$title'));
  if ($pos1 !== false && $pos2!==false) {
        eval(substr($script,$pos1,$pos2-$pos1+1));
        $havetitle=true;
  }
  if($havetitle && $pagesec){
    $sql="select active,pageid,pagedescription,title from scripts where filename='".$page."'";
    $rs = DB_query($sql,$db);
    $registrado = false;
    if($rw = DB_fetch_array($rs)){
        if($rw['active']==1){
            $Check=" checked='cheked'";
        }else{
            $Check="";
        }
        $registrado=true;
    }else{
       $Check="";
       $registrado = false;
    }
    if($registrado){
        echo '<tr><td><input type="checkbox" id="chk'.$rw['pageid'].'" name="page[]" '.$Check.' value="'.$page.'" /></td><td><input type="text" style="width:350px;" value="'.($havetitle==true?utf8_encode($rw['title']):'').'" id="title'.$rw['pageid'].'" /></td>';
        echo '<td><select style="width:150px;" id="cmb'.$rw['pageid'].'">';
        foreach($CmbRole as $key=>$value){
            echo '<option value="'.$key.'" '.($PageSecurity==$key?'selected="selected"':'').'>'.$value.'</option>';
        }
        //.($pagesec==true?$PageSecurity:'').
        echo '</select> </td><td><a href="'.$page.'" target="_blank">'.$page.'</a></td>
        <td rowspan="2">
            <input type="button" style="width:100%; height:100%;" onclick="saveChange('.$rw['pageid'].');" value="Guardar" />
        </td></tr>';
        echo "<tr><td colspan='4'><input type='text' id='desc".$rw['pageid']."' style='width:100%;' value='".$rw['pagedescription']."' /></td></tr>";
    }else{
        echo '<tr><td><input type="checkbox" id="'.$rw['pageid'].'" name="page[]" '.$Check.' value="'.$page.'" /></td><td>'.($havetitle==true?utf8_encode($title):'').'</td><td>'.($pagesec==true?$PageSecurity:'').'</td><td><a href="'.$page.'" target="_blank">'.$page.'</a></td></tr>';
    }
  }

  if(isset($_POST['charge'])){
    $sql="select count(filename) as files from scripts where filename='".$page."'";
    $rs = DB_query($sql,$db);
    if($rw = DB_fetch_array($rs)){
        if($rw['files']==0){
            $sql="insert into scripts (filename,pagedescription,title,pagesecurity,active)
                values('".$page."','".utf8_encode($title)."','".utf8_encode($title)."','".$PageSecurity."','1')";
            DB_query($sql,$db);
        }
    }
  }

}
echo "</table></center>";
echo "</form> ";
?>
<center>
 <form name="carga" method="POST">
    <input type="submit" name="charge" value="Cargar Scripts" />
 </form>
</center>
<?
include('includes/footer.inc');
?>