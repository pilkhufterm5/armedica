<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:39:36 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 14 $
 */
// sept 2008 - get variables
$PageSecurity=15;

include('includes/session.inc');

$title=_('File Upload');

include('includes/header.inc');
// GET VARIABLES
// type - transaction type
// typeno - transaction number
// comments

echo "<CENTER><B><BIG>"._('File Upload')."</BIG></B></CENTER>";

echo "<TABLE ALIGN=CENTER>";
echo "<FORM ENCTYPE='multipart/form-data' ACTION='rh_uploadresult.php' METHOD=POST>
	  <TR><TD class='tableheader'>"._('Type').":</TD><TD><SELECT NAME='type'>";
$sql = "SELECT typeid, typename FROM systypes WHERE typeno > 0";
$typeres = DB_query($sql,$db);

while($type = DB_fetch_array($typeres)){
	if($type['typeid']==$_GET['type']){
		echo "<OPTION SELECTED VALUE=".$type['typeid'].">".$type['typename'];
	}else {
		echo "<OPTION VALUE=".$type['typeid'].">".$type['typename'];
	}
}
		
echo "	</SELECT></TD></TR>";

echo "<TR><TD class='tableheader'>"._('Trans').' '._('Number').":</TD><TD><INPUT TYPE=TEXT NAME='trans' SIZE=10 VALUE=".$_GET['typeno']."></TD>";

echo "<TR><TD class='tableheader'>"._('Comments').":</TD><TD><TEXTAREA NAME='comments' COLS=25 ROWS=7>".$_GET['comments']."</TEXTAREA></TD>";

echo "	<TR><TD class='tableheader'><INPUT TYPE='hidden' name='MAX_FILE_SIZE' value='1000000'>" .
		_('Send this file') . ": </TD><TD><INPUT NAME='userfile' TYPE='file'></TD></TR>
		<TR><TD COLSPAN=2 ALIGN=CENTER><INPUT TYPE='submit' VALUE='" . _('Send File') . "'></TD></TR>
		</FORM>";
echo "</TABLE>";
include('includes/footer.inc');
?>
