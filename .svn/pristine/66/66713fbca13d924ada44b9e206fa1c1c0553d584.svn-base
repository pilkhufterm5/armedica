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

$title=_('Email') . ' ' . _('Supplier') . ' ' . _('Payment') . ' ' . _('Number') . ' ' . $TransNo;

if (isset($_POST['DoIt']) AND strlen($_POST['EmailAddr'])>3){
	
	 include ('includes/htmlMimeMail.php');

		$mail = new PHPMailer();
		
		$sql = "select supptrans.id, supptrans.transno, ABS((supptrans.ovamount+supptrans.ovgst)) AS total, suppreference, suppliers.suppname,
				suppliers.bankact, suppliers.bankref, suppliers.currcode
				from supptrans 
				INNER JOIN suppliers ON supptrans.supplierno = suppliers.supplierid
				where  type = 22 and transno = ".$TransNo."";
		
		$res2 = DB_query($sql,$db);
		if(DB_num_rows($res2)<=0){
			prnMsg("Lo Sentimos no se pudieron obtener detalles del pago.",'warn');
			include('includes/footer.inc');
			exit;
		}
		
		$pago = DB_fetch_array($res2);
		
		$body = $info['suppname'];
		$SuppName = $info['suppname'];
		$body .= "<br>
						Estimado Proveedor:<br><br>
						Nos permitimos informarle que se proceso un pago a su cuenta ".$pago['bankact'].
						" [".$pago['bankref']."]							por la cantidad de *** $".number_format($pago['total'],2)." *** ".
						$pago['currcode']." por el concepto del pago de la(s) siguiente(s)
						factura(s):<br><br>";
		
		$sql = "select supptrans.transno, (supptrans.ovamount+supptrans.ovgst) AS total, suppreference, suppliers.suppname,
				suppliers.bankact, suppliers.bankref, suppliers.currcode, suppallocs.amt
				from supptrans 
				INNER JOIN suppliers ON supptrans.supplierno = suppliers.supplierid
				INNER JOIN suppallocs ON supptrans.id = suppallocs.transid_allocto
				where suppallocs.transid_allocfrom = ".$pago['id']."";
		
		$res = DB_query($sql,$db);
		if(DB_num_rows($res)<=0){
			prnMsg("Lo Sentimos este pago no tiene facturas asignadas.",'warn');
			include('includes/footer.inc');
			exit;
		}

		while($info = DB_fetch_array($res)){

			$body .= $info['suppreference']." $".number_format($info['amt'],2)."<br>";
			
		}
		
		$body .= "<br><br>Atentamente<br>
				Departamento de Tesoreria<br>
				".$_SESSION['CompanyRecord']['coyname']."<br><br><br>
				Mensaje generado automaticamente por Realhost webERP
				<br>
				Este mensaje fue enviado sin acentos para su mejor lectura";
		
$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server

$mail->Username   = "username@gmail.com";  // GMAIL username
$mail->Password   = "password";            // GMAIL password

$mail->From       = $_SESSION['CompanyRecord']['coyname'];
$mail->FromName   = $_SESSION['CompanyRecord']['email'];

$mail->Subject    = 'AVISO DE PAGO';

$mail->MsgHTML($body);

$mails = explode(',',$_POST["EmailAddr"]);
foreach($mails AS $addmail){
	
	$mail->AddAddress($addmail, $SuppName);

}

//$mail->AddAttachment('companies/' . $_SESSION['DatabaseName'] . '/logo.jpg');             // attachment

//$mail->AddAttachment("images/phpmailer.gif");             // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
 	$title = _('Emailing') . ' ' . _('Number') . ' ' . $_POST['TransNo'];
	include('includes/header.inc');
	
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

$SQL = "SELECT email
		FROM suppliercontacts INNER JOIN suppliers
			ON suppliercontacts.supplierid= suppliers.supplierid
		LIMIT 1";

$ErrMsg = _('There was a problem retrieving the contact details for the supplier');
$ContactResult=DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($ContactResult)>0){
	$EmailAddrRow = DB_fetch_row($ContactResult);
	$EmailAddress = $EmailAddrRow[0];
} else {
	$EmailAddress ='';
}

echo '<TR><TD>' . _('Email') . ' ' . _('Supplier') . ' ' . _('Payment') . ' ' . _('number') . ' ' . $_GET['TransNo'] . ' ' . _('to') . ":</TD>
	<TD><TEXTAREA COLS=25 ROWS=12 NAME='EmailAddr'>".$EmailAddress."</TEXTAREA></TD>
	</TABLE>";

// bowikaxu - vista previo del mensaje
echo "<br><hr>";

echo "Le notificamos que se ha realizado y confirmado el pago de las facturas: <br>";
$sql = "select supptrans.transno, suppreference from supptrans where id IN 
		(select transid_allocto from suppallocs where transid_allocfrom IN 
		(SELECT id FROM supptrans WHERE type = 22 and transno = ".$TransNo."))";

$res = DB_query($sql,$db);
while($info = DB_fetch_array($res)){

	echo $info['suppreference'].", ";

}

echo "<hr><br>"; 
// fin vista previa mensaje

echo "<BR><INPUT TYPE=SUBMIT NAME='DoIt' VALUE='" . _('OK') . "'>";
echo '</CENTER></FORM>';
include ('includes/footer.inc');
?>