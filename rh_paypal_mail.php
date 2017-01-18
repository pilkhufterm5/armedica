<?php
/* $Revision: 141 $ */
$PageSecurity = 2;
include ('includes/session.inc');
include('includes/SQL_CommonFunctions.inc');
include_once('phpmailer/class.phpmailer.php');

if (isset($_POST['TypeID'])){
	$TypeID = $_POST['TypeID'];
} else {
	// bowikaxu - error no se envio tipo
}
if (isset($_POST['TransNo'])){
	$TransNo = $_POST['TransNo'];
} else {
	// bowikaxu - error no se envio transaccion
}

if (isset($_GET['TypeID'])){
	$TypeID = $_GET['TypeID'];
} else {
	// bowikaxu - error no se envio tipo
}
if (isset($_GET['TransNo'])){
	$TransNo = $_GET['TransNo'];
} else {
	// bowikaxu - error no se envio transaccion
}

$title=_('Email') . ' ' . _('Customer') . ' ' . _('Payment') . ' ' . _('Link') . ' ' . $TransNo;

if (isset($_POST['DoIt']) AND strlen($_POST['EmailAddr'])>3){
	
	 include ('includes/htmlMimeMail.php');

		$mail = new PHPMailer();
		
		$SQL = "SELECT email, (ovamount+ovgst) AS total, custbranch.debtorno,
				debtorsmaster.name, debtorsmaster.taxref
		FROM custbranch, debtortrans, debtorsmaster
		WHERE 
		debtortrans.branchcode = custbranch.branchcode
		AND debtortrans.debtorno = custbranch.debtorno
		AND debtorsmaster.debtorno = debtortrans.debtorno
		AND debtortrans.type = ".$TypeID."
		AND debtortrans.transno = ".$TransNo."";
		
		$res2 = DB_query($SQL,$db);
		if(DB_num_rows($res2)<=0){
			prnMsg("Lo Sentimos no se pudieron obtener detalles del pago.",'warn');
			include('includes/footer.inc');
			exit;
		}
		
		$info = DB_fetch_array($res2);

		$body = "<br>
						Estimado Cliente:<br><br>
						".$info['name'].' ['.$info['taxref'].']'."<br><br>
						Nos permitimos informarle que puede realizar su pago de *** $".number_format($info['total'],2)." *** <br>
						Atravez de nuestro sistema de pago en linea, usando el siguiente link:<br><br>
						https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=bowikaxu%40gmail%2ecom&item_name="._('Invoice').'-'.$TransNo."&item_number=".$info['debtorno'].'-'.$TypeID.'-'.$TransNo."&amount=".$info['total']."&no_shipping=0&no_note=1&currency_code=MXN&lc=MX&bn=PP%2dBuyNowBF&charset=UTF%2d8";
		
		if(strlen($_POST['description'])>=2)
			$body .= "<br><br>".$_POST['description']."";
		
		$body .= "<br><br>Atentamente<br>
				Departamento de Tesoreria<br>
				".$_SESSION['CompanyRecord']['coyname']."<br><br><br>
				Mensaje generado automaticamente por Realhost webERP.
				<br>
				Este mensaje fue enviado sin acentos para su mejor lectura.";

$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

$mail->Username   = "username@gmail.com";  // GMAIL username
$mail->Password   = "password";            // GMAIL password

$mail->From       = $_SESSION['CompanyRecord']['coyname'];
$mail->FromName   = $_SESSION['CompanyRecord']['email'];

$mail->Subject    = 'PAGO EN LINEA';

$mail->MsgHTML($body);

$mails = explode(',',$_POST["EmailAddr"]);
foreach($mails AS $addmail){
	
	$mail->AddAddress($addmail, $info['name']);

}

//$mail->AddAttachment('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg');             // attachment

//$mail->AddAttachment("images/phpmailer.gif");             // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
 	$title = _('Emailing') . ' ' . _('Number') . ' ' . $_POST['TransNo'];
	include('includes/header.inc');
	
	/*
	$sql = "INSERT INTO rh_suppnotifications (
			type,
			transno,
			date,
			emails) VALUES (
			22,
			".$TransNo.",
			NOW(),
			'".$_POST['EmailAddr']."')";
	$res = DB_query($sql,$db,'Imposible guardar datos de la notificacion');
	*/
	echo "<P>". _('Transaction') . _('number') . ' ' . $_POST['TransNo'] . ' ' . _('has been emailed to') . ' ' . $_POST['EmailAddr'];
	include('includes/footer.inc');
	exit;
}	
	
} elseif (isset($_POST['DoIt'])) {
	$_GET['TypeID'] = $_POST['TypeID'];
	$_GET['TransNo'] = $_POST['TransNo'];
	prnMsg(_('The email address entered is too short to be a valid email address') . '. ' . _('The transaction was not emailed'),'warn');
}

include ('includes/header.inc');

echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";
echo "<INPUT TYPE=HIDDEN NAME='TypeID' VALUE=" . $TypeID . ">";
echo "<INPUT TYPE=HIDDEN NAME='TransNo' VALUE=" . $TransNo . '>';
echo '<CENTER><P><TABLE>';

$SQL = "SELECT email, custbranch.debtorno, debtortrans.invtext
		FROM custbranch, debtortrans
		WHERE 
		debtortrans.branchcode = custbranch.branchcode
		AND debtortrans.debtorno = custbranch.debtorno
		AND debtortrans.type = ".$TypeID."
		AND debtortrans.transno = ".$TransNo."";

$ErrMsg = _('There was a problem retrieving the contact details for the customer');
$EmailResult=DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($EmailResult)>0){
	$EmailAddrRow = DB_fetch_row($EmailResult);
	$EmailAddress = $EmailAddrRow[0];
} else {
	$EmailAddress ='';
}

echo '<TR><TD>' . _('Email') . ' ' . _('Customer') . ' ' . _('Transaction') . ' ' . _('Number') . ' ' . $_GET['TransNo'] . ' ' . _('to') . ":</TD>
	<TD><TEXTAREA COLS=25 ROWS=12 NAME='EmailAddr'>".$EmailAddress."</TEXTAREA></TD>
	<TR>
	<TD>"._('Narrative')."</TD>
	<TD><TEXTAREA COLS=25 ROWS=12 NAME='description'>".$EmailAddrRow[2]."</TEXTAREA></TD>
	</TR>
	</TABLE>";

echo "<hr><br>"; 
// fin vista previa mensaje

echo "<BR><INPUT TYPE=SUBMIT NAME='DoIt' VALUE='" . _('OK') . "'>";
echo '</CENTER></FORM>';
include ('includes/footer.inc');
?>