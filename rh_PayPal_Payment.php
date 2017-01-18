<?php
/*
 * PayPal Payment Script
 * Bowikaxy Realhost - July 2008
 * 
 * Accept payments from customers via paypal in mexican Pesos
 * 
 * $LastChangedDate: 2008-07-01 08:41:13 -0500 (mar, 01 jul 2008) $
 * $Rev: 304 $
 */

// LINK FOR EMAILS:
/*
 https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=bowikaxu%40gmail%2ecom&item_name=Pago&item_number=itemID&amount=666%2e00&no_shipping=0&no_note=1&currency_code=MXN&lc=MX&bn=PP%2dBuyNowBF&charset=UTF%2d8
*/

$PageSecurity = 2;
include('includes/session.inc');

$title=_('Payment').' PayPal';
// HEADER
if (!headers_sent()){
		header('Content-type: text/html; charset=' . _('UTF-8'));
	}
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
	
	echo '<html><head><title>' . $title . '</title>';
	echo '<link REL="shortcut icon" HREF="'. $rootpath.'/favicon.ico">';
	echo '<link REL="icon" HREF="' . $rootpath.'/favicon.ico">';
	echo '<meta http-equiv="Content-Type" content="text/html; charset=' . _('ISO-8859-1') . '">';
	echo '<link href="'.$rootpath. '/css/'. $_SESSION['Theme'] .'/default.css" REL="stylesheet" TYPE="text/css">';
	echo '</HEAD>';
	echo '<BODY>';

class Cipher {
    private $securekey, $iv;
    function __construct($textkey) {
        $this->securekey = hash('sha256',$textkey,TRUE);
        $this->iv = mcrypt_create_iv(32);
    }
    function encrypt($input) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
    }
    function decrypt($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }
}

$cipher = new Cipher('xyz123xyz123');

/*
TESTING
$_GET['itemid'] = $cipher->encrypt('type-transno','xyz');
$_GET['amount'] = $cipher->encrypt('358.5','xyz');
$_GET['description'] = $cipher->encrypt('Debtorno - Pago Factura 12345','xyz');
*/

if(!isset($_GET['itemid']) OR !isset($_GET['amount']) OR !isset($_GET['description'])){

	prnMsg('No se ha enviado la infomacion correcta.','error');
	include('includes/footer.inc');
	exit;
	
}

// Desencriptar valores de concepto y cantidad
$description = $cipher->decrypt($_GET['description'],'xyz');
$itemid = $cipher->decrypt($_GET['itemid'],'xyz');
$amount = $cipher->decrypt($_GET['amount'], 'xyz');

if(!is_numeric($amount)){ // verificar cantidad

	prnMsg('La Cantidad es incorrecta.','error');
	include('includes/footer.inc');
	exit;

}

if(strlen($itemid)<= 1 OR strlen($description) <= 1){ // verificar concepto

	prnMsg('El concepto y/o ID no es correcto.','error');
	include('includes/footer.inc');
	exit;

}

?>

<img src="<?php echo 'companies/' . $_SESSION['DatabaseName'] . '/logo.jpg'; ?> ">
&nbsp;&nbsp;&nbsp;
<img src="https://www.paypal.com/en_US/i/logo/paypal_logo.gif" border=0>
<center>
<br>
<FONT SIZE=+1>
Gracias por utilizar nuestro servicio de pago en linea.<br>
En cuanto su pago sea verificado se le enviara un mail de notificacion con la informacion del pago.<br><br>
<TABLE BORDER=1 BORDERCOLOR=#c7ccf6>

<TR>
	<TD COLSPAN=2><STRONG>Detalles del Pago:</STRONG></TD>
</TR>
<TR>
<TD>ID</TD>
<TD><?php echo $itemid; ?></TD>
</TR>
<TR>
<TD>Cantidad</TD>
<TD><?php echo $amount; ?></TD>
</TR>
<TR>
<TD>Concepto</TD>
<TD><?php echo $description; ?></TD>
</TR>

</TABLE>
<br>
Atentamente ... <?php echo $_SESSION['CompanyRecord']['coyname']; ?><br><br>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="bowikaxu@gmail.com">
<input type="hidden" name="item_name" value="Pago">
<input type="hidden" name="item_number" value="<?php echo $itemid; ?>">
<input type="hidden" name="amount" value="<?php echo $amount; ?>">
<input type="hidden" name="no_shipping" value="0">
<input type="hidden" name="no_note" value="1">
<input type="hidden" name="currency_code" value="MXN">
<input type="hidden" name="lc" value="MX">
<input type="hidden" name="bn" value="PP-BuyNowBF">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - La manera mas facil y segura de pagar en linea!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
</center>
</font>
<?php
echo '</BODY>';
echo '</HTML>';
//include('includes/footer.inc');
exit;


?>
