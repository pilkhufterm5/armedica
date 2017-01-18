<?php
/* $Revision: 141 $ */



if ($_GET['InvOrCredit']=='Invoice'){
	$TransactionType = _('Invoice');
	$TypeCode = 10;
} else {
	$TransactionType = _('Credit Note');
	$TypeCode =11;
}
$title=_('Email') . ' ' . $TransactionType . ' ' . _('Number') . ' ' . $_GET['FromTransNo'];

if (isset($_POST['DoIt']) AND strlen($_POST['EmailAddr'])>3){
    //Jaime (agregado) si la factura es un CFD se envia por Email el archivo .xml
    if (isSet($_POST['isCfd'])){
        $isCfd = true;

        include('config.php');

        if (isset($SessionSavePath)){
	        session_save_path($SessionSavePath);
        }
        session_start();
        $PageSecurity=1;

        if ((!in_array($PageSecurity, $_SESSION['AllowedPageSecurityTokens']) OR !isset($PageSecurity))) {
            echo "<center><strong>Error 0s00001: El usuario no tiene permisos</strong></center>";
		    exit;
	    }

        $server=$host;//"localhost";
        $db=$_SESSION['DatabaseName'];//$DefaultCompany;//////"mangueras_erp_001";
        $user=$dbuser;//"root";
        $pass=$dbpassword;//"";
        $version="0.6";

            include_once('PHPJasperXML/class/fpdf/fpdf.php');
            include_once("PHPJasperXML/class/PHPJasperXML.inc");

            $xml = null;

            if($_POST['InvOrCredit']=='Credit'){
                $xml =  simplexml_load_file("PHPJasperXML/rh_FE_notaDeCredito.jrxml");
            } else{
                $xml =  simplexml_load_file("PHPJasperXML/rh_FE.jrxml");
            }

            $transno=$_POST['TransNo'];
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->debugsql=false;
            $rootpath2=dirname($_SERVER['SCRIPT_FILENAME']);
            $rootpath2 = $rootpath2. "/companies/$db";
            $PHPJasperXML->arrayParameter=array("transno"=>$transno, "rootpath"=>$rootpath2);
            $PHPJasperXML->xml_dismantle($xml);
            $PHPJasperXML->transferDBtoArray($server,$user,$pass,$db);
            //$PHPJasperXML->arrayPageSetting['name'] = $fileBasePath . '/' . $cfdName;
            $PDF=$PHPJasperXML->outpage("S");

//Jaime, se cambio el envio de email por smtp, con autentificacion y ssl
require("PHPMailer_v5.1/class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP(); // send via SMTP
$mail->Host = 'ssl://smtp.gmail.com';
$mail->Port = 465;//587
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->Username = "facturacion@realhost.com.mx"; // SMTP username
$mail->Password = "RH548933"; // SMTP password
$mail->SetFrom('facturacion@realhost.com.mx', utf8_decode('Servicio de Facturación '.$_SESSION['CompanyRecord']['coyname']));
/**********************************************/
/**********************************************/
/**********************************************/
/**********************************************/
//$from_email = "jaime.hinojosa@realhost.com.mx"; //Reply to this email ID
//$from_name = "Roberto Jaime Hinojosa";
//$name="Roberto Jaime Hinojosa"; // Recipient's name
//$mail->From = $from_email;
//$mail->FromName = $from_name;
$mails=explode(";",$_POST['EmailAddr']);
foreach($mails as $value){
    if(strlen($value)>3){
        $mail->AddAddress($value);
    }
}
//$mail->AddReplyTo(gerardo.delangel@armedica.com.mx,'Reply to name');
//$mail->WordWrap = 50; // set word wrap
$mail->AddCC = "gerardo.delangel@armedica.com.mx";
$fileBasePath = $_POST['path'];
$cfdName = $_POST['cfdName'];
$mail->AddAttachment($fileBasePath . '/' . $cfdName . '.xml', $cfdName . '.xml'); // attachment
$mail->AddStringAttachment($PDF,  $cfdName . '.pdf'); // attachment

//$mail->IsHTML(true); // send as HTML
$mail->Subject = 'CFD: ' . $cfdName;
$mail->Body = "Anexado se encuentra el CFD en formato XML y PDF."; //HTML Body
//$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
$mail_success = $mail->Send();
if(!$mail_success)
    $mail_error = $mail->ErrorInfo;
$mail = null;
//\Jaime, se cambio el envio de email por smtp, con autentificacion y ssl

        $title = _('Emailing') . ' CFD ' . _('Number') . ' ' . $_POST['TransNo'];
        include('includes/header.inc');
        if($mail_success)
            echo "<P>CFD " . _('number') . ' ' . $_POST['TransNo'] . ' ' . _('has been emailed to') . ' ' . $_POST['EmailAddr'];
        else
            echo "<P>Mailer Error: " . $mail_error;
        include('includes/footer.inc');
        exit;
    }
    //Termina Jaime (agregado) si la factura es un CFD se envia por Email el archivo .xml
	if ($_SESSION['InvoicePortraitFormat']==0){
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/PrintCustTransPortrait.php?' . SID . '&FromTransNo=' . $_POST['TransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>";

		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/rh_PrintCustTrans.php?' . SID . '&FromTransNo=' . $_POST['FromTransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
	} else {
		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/PrintCustTransPortrait.php?' . SID . '&FromTransNo=' . $_POST['TransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>";

		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/PrintCustTransPortrait.php?' . SID . '&FromTransNo=' . $_POST['FromTransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
	}
	exit;
} elseif (isset($_POST['DoIt'])) {
	$_GET['InvOrCredit'] = $_POST['InvOrCredit'];
	$_GET['FromTransNo'] = $_POST['FromTransNo'];
	prnMsg(_('The email address entered is too short to be a valid email address') . '. ' . _('The transaction was not emailed'),'warn');
}
$PageSecurity = 2;

include ('includes/session.inc');
include ('includes/header.inc');




echo "<FORM METHOD=POST>";

echo "<INPUT TYPE=HIDDEN NAME='TransNo' VALUE=" . $_GET['FromTransNo'] . ">";
echo "<INPUT TYPE=HIDDEN NAME='InvOrCredit' VALUE='" . $_GET['InvOrCredit'] . "'>";
//Jaime (agregado) se agrega el atributo isCfd para saber si la factura es un CFD
//if (isSet($_GET['isCfd']) && $_GET['isCfd'])
    echo '<input type="hidden" name="isCfd" value="1">';
if (isSet($_GET['isCartaPorte']) && $_GET['isCartaPorte'])
    echo '<input type="hidden" name="isCartaPorte" value="1">';
//Termina Jaime (agregado) se agrega el atributo isCfd para saber si la factura es un CFD
$row = mysql_fetch_array(DB_query("select c.no_certificado, c.serie, c.folio,c.fk_transno from rh_cfd__cfd c where c.id_systypes=".($_GET['InvOrCredit']=='Credit'?'11':'10')." and c.fk_transno = " .$_GET['FromTransNo'], $db, _("Error retrieving invoice data")));
$cfdName = $row['serie'] . $row['folio'] . '-' . $row['fk_transno'];
$fileBasePath = /*$rootpath .*/  'XMLFacturacionElectronica/facturasElectronicas/' . $row['no_certificado'];

echo '<input type="hidden" name="path" value="'.$fileBasePath.'">';
echo '<input type="hidden" name="cfdName" value="'.$cfdName.'">';
echo '<CENTER><P><TABLE>';


$SQL = "SELECT email
		FROM custbranch INNER JOIN debtortrans
			ON custbranch.debtorno= debtortrans.debtorno
			AND custbranch.branchcode=debtortrans.branchcode
	WHERE debtortrans.type=$TypeCode
	AND debtortrans.transno=" .$_GET['FromTransNo'];

$ErrMsg = _('There was a problem retrieving the contact details for the customer');
$ContactResult=DB_query($SQL,$db,$ErrMsg);

if (DB_num_rows($ContactResult)>0){
	$EmailAddrRow = DB_fetch_row($ContactResult);
	$EmailAddress = $EmailAddrRow[0];
} else {
	$EmailAddress ='';
}

echo '<TR><TD>' . _('Email') . ' ' . $_GET['InvOrCredit'] . ' ' . _('number') . ' ' . $_GET['FromTransNo'] . ' ' . _('to') . ":</TD>
	<TD><TEXTAREA COLS=25 ROWS=12 NAME='EmailAddr'>".$EmailAddress."</TEXTAREA></TD>
	</TABLE>";

echo "<BR><INPUT TYPE=SUBMIT NAME='DoIt' VALUE='" . _('OK') . "'>";
echo '</CENTER></FORM>';
include ('includes/footer.inc');
?>
