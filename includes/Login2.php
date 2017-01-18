<?php
global $rootpath;
/* webERP 3.05 Revision: 1.17 $ */
/* webERP 3.08 Revision: 1.20 $ */
// Display demo user name and password within login form if $allow_demo_mode is true

include ('LanguageSetup.php');

if ($allow_demo_mode == True AND !isset($demo_text)) {
	$demo_text = _('login as user') .': <i>' . _('demo') . '</i><BR>' ._('with password') . ': <i>' . _('weberp') . '</i>';
} elseif (!isset($demo_text)) {
	$demo_text = _('Please login here');
}
$WebERP=explode('/',substr($_SERVER['REQUEST_URI'],1));
//var_dump($WebERP);
//$WebERP[0]=trim($rootpath,' /');

?>

<html>
<head>
    <title>LOGIN</title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _('UTF-8'); ?>" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="/<?php echo $WebERP['0'] . "/css/" .$theme;?>/login.css" type="text/css" />
</head>

<body>

<?php
if (get_magic_quotes_gpc()){ 
	echo '<p style="background:white">';
	echo _('Your webserver is configured to enable Magic Quotes. This may cause problems if you use punctuation (such as quotes) when doing data entry. You should contact your webmaster to disable Magic Quotes');
	echo '</p>';
}
?>

<div id="container">
	</BR>
	<div id="login_box">
	<div id="logo_empresa"></div>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" name="loginform" method="post">
	<?php
		$Select='<label>'._('Company').':</label><br />';
		$Data='';
	if ($AllowCompanySelectionBox == true){
			$Select.= '<CENTER><select name="CompanyNameField" id="CompanyNameField" onchange=cambiologo(this.value)>';
		}
		$DirHandle = dir('companies/');
		$total=0;
		while (false != ($CompanyEntry = $DirHandle->read())){
			$show=false;
			//if($CompanyEntry{0}!='.'&&is_file('companies/' . $CompanyEntry.'/config.php'))
			 if($CompanyEntry{0}!='.'&&is_file('companies/' . $CompanyEntry.'/config.php')&&(strpos($CompanyEntry,$WebERP[0])!==false))//PRODUCCION
				// if (is_dir('companies/' . $CompanyEntry)AND (strpos($CompanyEntry,$WebERP[0])!==false) AND $CompanyEntry != '..' AND $CompanyEntry != 'CVS' AND $CompanyEntry!='.' AND $CompanyEntry!='.svn' AND $CompanyEntry!='.git')
				// if (is_dir('companies/' . $CompanyEntry) AND $CompanyEntry != '..' AND $CompanyEntry != 'CVS' AND $CompanyEntry!='.svn' AND $CompanyEntry!='.')
			$show=true;
			if($show){
				$Compannia=$CompanyEntry;
				if ($AllowCompanySelectionBox == true){
					$Data.= "<option  value='$CompanyEntry'>$CompanyEntry";
					$total++;
				}else{
					$total=1;
					break;
				}
			}
		}
		if ($AllowCompanySelectionBox == true)
			$Data.= '</select></CENTER>';
		if($total==1){
			$CompanyEntry=$Compannia;
			$Data='';
			$Select='';
			include_once('companies/' . $CompanyEntry.'/config.php');
                        $Data.= '<input type="hidden" name="CompanyNameField"  id="CompanyNameField" value="' . $CompanyEntry. '">';
			
		}
		echo $Select.$Data;
	?>
	<br />
	<label><?php echo _('User name'); ?>:</label><br />
	<input type="TEXT" name="UserNameEntryField"/><br />
	<label><?php echo _('Password'); ?>:</label><br />
	<input type="PASSWORD" name="Password"><br />
	<div id="demo_text"><?php echo $demo_text;?></div>
	<input class="button" type="submit" value="<?php echo _('Login'); ?>" name="SubmitUser" />
	</form>
	</div>
</div>
    <script language="JavaScript" type="text/javascript">
    //<![CDATA[
           // <!--
                  document.loginform.UserNameEntryField.focus();
            //-->
    //]]>
    </script>
<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<script language="JavaScript" type="text/javascript">
document.getElementById('logo_empresa').innerHTML = '<CENTER><img width=70% height=15% src=/<?php echo $WebERP['0']; ?>/companies/'+document.getElementById('CompanyNameField').value+'/logo.jpg></CENTER>';
function cambiologo(q){
	document.getElementById('logo_empresa').innerHTML = '<CENTER><img width=70% height=15% src=/<?php echo $WebERP['0']; ?>/companies/'+q+'/logo.jpg></CENTER>';
}	
</script>
<!--//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
</body>
</html>