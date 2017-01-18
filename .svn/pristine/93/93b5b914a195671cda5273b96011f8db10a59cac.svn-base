<?php

/*$Id: PageSecurity.php 4500 2011-02-27 09:18:42Z daintree $ */
$PageSecurity=1;
include('includes/session.inc');
$title = _('Page Security Levels');

$NoScriptshow=array(
		'phpinfo.php',
		'config.php',
		'PrintSalesOrder_generic.php',
		'\') and filename not like(\'%Z_Upgrade%.php%\') and filename not like(\'%config%.php%'
);

/************
 * Buscamos todos los archivos que no esten dados de alta en la tabla script
*
* */
$FilesWeb=glob("*.php");
natsort($FilesWeb);
$SQL="select * from scripts order by filename desc";
$Resultado=DB_query($SQL,$db);
while ($myrow=DB_fetch_assoc($Resultado)) {
	$id=array_search($myrow['script'],$FilesWeb);
	if($id!==false){
		unset($FilesWeb[$id]);
	}
}
/*
 * Por cada archivo que no este dado de alta, buscamos su nivel de permiso y texto de encabezado
* */
if(count($FilesWeb)>0){
	$_PageSecurity="PageSecurity";
	$_Titulo="title";
	$Restringidos=$Permitidos=array();
	foreach($FilesWeb as $page){
		$$_Titulo=$pageName=$page;
		$to=0;
		$$_PageSecurity=-1;
		if(isset($UsuarioRegistro["PageSecurityArray"][$page]))
			$$_PageSecurity=$UsuarioRegistro["PageSecurityArray"][$page];
		$handle = @fopen($page, "r");
		if ($handle) {
			while (($buffer = fgets($handle)) !== false) {
				if(strpos($buffer,'$title')!==false){
					$from=strpos($buffer,'$title');
					$to=strpos($buffer,';')+1;
					$buffer=//substr($buffer,0,$from).
					substr($buffer,$from,$to-$from)//Elemento que nos interesa quitar
					//.substr($buffer,$to)
					;if(trim($buffer)!=''){
						eval($buffer);
						$pageName=$$_Titulo;
					}
					break;
				}
				if((strpos($buffer,'$PageSecurity')!==false&&strpos($buffer,'=')!==false)){
					$from=strpos($buffer,'$PageSecurity');
					$to=strpos($buffer,';')+1;
					$buffer=//substr($buffer,0,$from).
					substr($buffer,$from,$to-$from)//Elemento que nos interesa quitar
					//.substr($buffer,$to)
					;eval($buffer);
				}
			}
			fclose($handle);
		}
		if($$_PageSecurity>=0){
			$SQL="insert into scripts set filename='{$page}', pagesecurity='{$$_PageSecurity}', title='{$$_Titulo}', pagedescription='{$$_Titulo}'";
		}else
			/*
			 * Si no tiene page security lo ponemos a 1 por default,
		* */
			 $SQL="insert into scripts set filename='{$page}', pagesecurity='1', title='{$$_Titulo}', pagedescription='{$$_Titulo}'";
		$Resultado=DB_query($SQL,$db);
	}
}
include('includes/header.inc');

echo '<p class="page_title_text">' . $title.'</p><br />';

if (isset($_POST['Update'])) {
	foreach ($_POST as $ScriptName => $PageSecurityValue) {
		if ($ScriptName!='Update' and $ScriptName!='FormID') {
			$ScriptName = mb_substr($ScriptName, 0, mb_strlen($ScriptName)-4).'.php';
			$sql="UPDATE scripts SET pagesecurity='". $PageSecurityValue . "' WHERE filename='" . $ScriptName . "'";
			$UpdateResult=DB_query($sql, $db,_('Could not update the page security value for the script because'));
		}
	}
}

$NoScriptshow="'".implode("', '",$NoScriptshow)."'";
$sql="SELECT filename,
			pagesecurity,
			pagedescription from scripts where filename not in ({$NoScriptshow})";

$result=DB_query($sql, $db);

echo '<center><br /><form method="post" id="PageSecurity" action="' . htmlspecialchars($_SERVER['PHP_SELF'],ENT_QUOTES,'UTF-8') . '">';
echo '<div>';
echo '<input type="hidden" name="FormID" value="' . $_SESSION['FormID'] . '" />';

echo '<table class="selection">';

$TokenSql="SELECT tokenid, tokenname
			FROM securitytokens 
			ORDER BY tokenname";
$TokenResult=DB_query($TokenSql, $db);

while ($myrow=DB_fetch_array($result)) {
	echo '<tr><td>'.$myrow['filename'].'</td>';
	echo '<td><select name="'.$myrow['filename'].'">';
	while ($mytokenrow=DB_fetch_array($TokenResult)) {
		if ($mytokenrow['tokenid']==$myrow['pagesecurity']) {
			echo '<option selected="selected" value="'.$mytokenrow['tokenid'].'">'.$mytokenrow['tokenname'].'</option>';
		} else {
			echo '<option value="'.$mytokenrow['tokenid'].'">'.$mytokenrow['tokenname'].'</option>';
		}
	}
	echo '</select></td></tr>';
	DB_data_seek($TokenResult, 0);
}

echo '</table><br />';

echo '<div class="centre">
			<input type="submit" name="Update" value="'._('Update Security Levels').'" />
	</div>
	<br />
    </div>
	</form></center>';

include('includes/footer.inc');
/*
CREATE TABLE `scripts` (
  `script` varchar(78) NOT NULL default '',
  `pagesecurity` int(11) NOT NULL default '1',
  `description` text NOT NULL,
  PRIMARY KEY  (`script`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 */