<?php
$PageSecurity =15;

include('includes/session.inc');

$title = _('Tokens de seguridad');

include('includes/header.inc');
if (isset($_REQUEST['tokenname'])&&count($_REQUEST['tokenname'])>0)
foreach($_REQUEST['tokenname'] as $id=>$value)
	if(trim($value)!=''){
		$value=DB_escape_string($value);
		$id=DB_escape_string($id);
		if($id=='0'){
			$id=count($_REQUEST['tokenname']);
			$sqlQuery="replace securitytokens(tokenid,tokenname)values('{$id}','{$value}')";
		}else
			$sqlQuery="update securitytokens set tokenname='{$value}' where tokenid='{$id}'";
		DB_query($sqlQuery,$db);
}
?>
<center><?=$title?>
<form method="post">
<table class="selection">
	<tr>
		<th>Nivel</th>
		<th>Nombre</th>
	</tr>
	<?php
	$sqlQuery = "select * from securitytokens";
	$result = DB_query($sqlQuery,$db,'','',false,false);
	while($row = DB_fetch_assoc($result)){
		echo'<tr>';
			echo'<td>';
			echo htmlentities($row['tokenid']);
			echo'</td>';
			echo'<td>';
			echo'<input type=text name="tokenname[';
			echo htmlentities($row['tokenid']);
			echo ']" value="';
			echo htmlentities($row['tokenname']);
			echo '">';
			echo'</td>';
		echo'</tr>';
	}
	echo'<tr>';
		echo'<td>';
			echo htmlentities('Nuevo');
		echo'</td>';
		echo'<td>';
			echo'<input type=text name="tokenname[';
			echo '0';
			echo ']" value="';
			echo '">';
		echo'</td>';
	echo'</tr>';
	?>
	<tr><td colspan=2><center><input name="Guardar" value="Guardar" type="submit"></center></td></tr>
</table>
</form>
</center>
<?php
include('includes/footer.inc');
