<?php
/* $Revision: 141 $ */

/**************************************************************************
* Jorge Garcia
* 21/Nov/2008 Archivo creado para mandar mail Dunning letter
***************************************************************************/
$PageSecurity = 2;

include ('includes/session.inc');

$title=_('Email') . ' ' . _('Dunning Letter');

if (isset($_POST['DoIt']) AND strlen($_POST['EmailAddr'])>3){
	if($_POST['mod'] == 1){
		$rh_img = "";
	}else{
		$rh_img = "&Img=1";
	}
	if ($_SESSION['InvoicePortraitFormat']==0){
		$_SESSION['MAILBODY'] = str_replace('\r\n','<br>',$_POST['Body']);
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/rh_DunningLetter.php?' . SID . '&DebtorNo=' . $_POST['DebtorNo'] .$rh_img.'&Email=' . $_POST['EmailAddr'] . "&Subject=".$_POST['Subject']."'>";

		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/rh_DunningLetter.php?' . SID . '&DebtorNo=' . $_POST['DebtorNo'] . $rh_img.'&Email=' . $_POST['EmailAddr'] . "&Subject=".$_POST['Subject']."'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
	} else {
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/rh_DunningLetter.php?' . SID . '&DebtorNo=' . $_POST['DebtorNo'] . $rh_img.'&Email=' . $_POST['EmailAddr'] . "&Subject=".$_POST['Subject']."'>";

		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/rh_DunningLetter.php?' . SID . '&DebtorNo=' . $_POST['DebtorNo'] . $rh_img.'&Email=' . $_POST['EmailAddr'] . "&Subject=".$_POST['Subject']."'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
	}
	exit;
} elseif (isset($_POST['DoIt'])) {
	$_GET['DebtorNo'] = $_POST['DebtorNo'];
	prnMsg(_('The email address entered is too short to be a valid email address') . '. ' . _('The transaction was not emailed'),'warn');
}

include ('includes/header.inc');

echo "<FORM name='forma' ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";

echo "<INPUT TYPE=HIDDEN NAME='DebtorNo' VALUE=" . $_GET['DebtorNo'] . ">";
echo "<INPUT TYPE=HIDDEN NAME='mod' VALUE=" . $_GET['mod'] . ">";

echo '<CENTER><P><TABLE>';

echo '<TR><TD>' . _('Email') . ' ' ._('Dunning Letter') ." ". _('to') . ":</TD>
	<TD><INPUT size='50' NAME='EmailAddr'></TD></TR>
	<TR><TD>"._('Subject').":</TD><TD><INPUT size='50' NAME='Subject'></TD></TR>
	<TR><TD>"._('Body').":</TD><TD><TEXTAREA rows='5' cols='40' NAME='Body'></TEXTAREA></TD></TR>	
	</TABLE>";

echo "<BR><INPUT TYPE=SUBMIT NAME='DoIt' VALUE='" . _('OK') . "'>";
echo '</CENTER></FORM>';

/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/

include ('includes/footer.inc');
?>