<?php
/* $Revision: 141 $ */
global $correos_facturas;
$PageSecurity = 2;
include ('includes/session.inc');
if ($_REQUEST['InvOrCredit']=='Invoice'){
	$TransactionType = _('Invoice');
	$TypeCode = 10;
} else {
	$TransactionType = _('Credit Note');
	$TypeCode =11;
}
$title=_('Email') . ' ' . $TransactionType . ' ' . _('Number') . ' ' . $_GET['FromTransNo'];

if (isset($_POST['DoIt']) AND strlen($_POST['EmailAddr'])>3){
    //Jaime (agregado) si la factura es un CFD se envia por Email el archivo .xml
    //if (isSet($_POST['isCfd']))
    {$isCfd = true;
        $_GET['transno']=$transno=(int)$_POST['TransNo'];
        $row = DB_fetch_assoc(DB_query("select c.* from rh_cfd__cfd c where c.id_systypes=$TypeCode and c.fk_transno = " . $_POST['TransNo'], $db, _("Error retrieving invoice data")));
        $cfdName = $row['uuid'];
        $folio=$row['serie'].''.$row['folio'];
   		$xmlFile=$row['xml'];
   	 	{
				if(!stripos($xmlFile, "folio") || !stripos($xmlFile, "serie"))
	            {
	                if(!stripos($xmlFile, "folio")&&$row['folio']!=''){
		                $patron = '/((F|f)echa\s?=\s?"([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})")/i';
		                $sustitucion = '${1} folio = "'.$row['folio'].'" ';
		                $xmlFile = preg_replace($patron, $sustitucion, $xmlFile);
	                }
	                
	                if(!stripos($xmlFile, "serie")&&$row['serie']!=''){
	                	$patron = '/((F|f)echa\s?=\s?"([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})")/i';
	                	$sustitucion = '${1} serie = "'.$row['serie'].'"  ';
	                	$xmlFile = preg_replace($patron, $sustitucion, $xmlFile);
	                }
                }
                $xmlFile=str_replace('folio="'.$row['id_debtortrans'].'"','folio="'.$row['folio'].'"',$xmlFile);
				
			}	
        $Fila = DB_fetch_assoc(DB_query("select * from debtortrans where type=".
        		($_POST['InvOrCredit']=='Credit'?11:10)
        		." and transno = " . $_POST['TransNo'], $db, _("Error retrieving invoice data")));
        $dbz=$db;
        
        if($Fila['rh_status']=='C')
        	$_GET['isCfdCancelado']=1;
        if($_POST['InvOrCredit']=='Credit')
        	$_GET['isNotaDeCredito']=1;
        if(isSet($_POST['isCartaPorte']))
        	$_GET['isCartaPorte']=1;
        $fileBasePath = dirname(__FILE__).'/XMLFacturacionElectronica/xmlbycfdi';
        $rootpathX=$rootpath;
        //if(!is_file($fileBasePath . '/' . $cfdName . '.pdf'))
        	include dirname(__FILE__).'/PHPJasperXML/sample1.php';
        {
        	chdir(dirname(__FILE__));
        	$fileBasePath = dirname(__FILE__).'/XMLFacturacionElectronica/xmlbycfdi';
			
			$dbname=$_SESSION['DatabaseName'];//$DefaultCompany;//////"mangueras_erp_001";	
			$rootpath2=dirname($_SERVER['SCRIPT_FILENAME']);
		        $rootpath2 = $rootpath2. "/companies/{$dbname}";
	        $Adjuntos=array();
	        $cfdNameMsg=$cfdName;
	        
        	if($_SESSION['DatabaseName']=='tractocli_cfdi_001'){
        		$cfdNameMsg=$folio.'('.$cfdName .')';
			}
			
			{
	        	$Adjuntos[]=array('archivo'=>$xmlFile,
        			'nombre'=> $cfdNameMsg . '.xml'); // attachment
        		$Adjuntos[]=array('ruta'=>$fileBasePath . '/' . $cfdName . '.pdf',
        			'nombre'=> $cfdNameMsg .'.pdf'); // attachment
			}
		    $BCC='';$repplyTo='';
		    
        		$CopiaVenta=GetConfig('CorreosRespuestaVenta');
				if($CopiaVenta){
					$repplyTo=array();
					$CopiaVenta=explode(";",str_replace(array(',',';',' '),';',$CopiaVenta));
					foreach($CopiaVenta as $value){
						if(strlen(trim($value))>3){
							$repplyTo[]=($value);
						}
					}
				}
				
				$CopiaVenta=GetConfig('CorreosCopiaVenta');
				if($CopiaVenta){
					$BCC=array();
					$CopiaVenta=explode(";",str_replace(array(',',';',' '),';',$CopiaVenta));
					foreach($CopiaVenta as $value){
						if(strlen(trim($value))>3){
							$BCC[]=($value);
						}
					}
				}
				$CopiaVenta=GetConfig('CorreosAdjuntosVenta');
				if($CopiaVenta){
					$CopiaVenta=unserialize($CopiaVenta);
					if($CopiaVenta['tiempo']<date('Ymd')){
						if(is_file($fileBasePath.'/../'.$CopiaVenta['archivo'])){
							$Adjuntos[]=array('ruta'=>$fileBasePath.'/../'.$CopiaVenta['archivo'],
        						'nombre'=> $CopiaVenta['nombreArchivo']); // attachment
						}elseif(trim($CopiaVenta['archivoContenido'])!=''&&trim($CopiaVenta['nombreArchivo'])!=''){
							$Adjuntos[]=array('archivo'=>$CopiaVenta['archivoContenido'],
        						'nombre'=> $CopiaVenta['nombreArchivo']); // attachment
						}
					}
				}

				
				
				$mails=explode(";",$_POST['EmailAddr']);
			    /*if($correos_facturas==true){*/

			    	/*
					function EnviarMail($from='Servicio de Alertas',$To='test@realhost.com.mx',$Subject='Prueba',$Mensaje='Mensaje prueba',$adjuntos=array(),$BCC='',$repplyTo='')
					Verificamos que el correo en copias correo no este valido
					*/
					if($_SESSION['CompanyRecord']['or_copia_enviofactura']!='')
					{						
						//
						if (strpos( $_SESSION['CompanyRecord']['or_copia_enviofactura'], ';') !== false) {
						   $correos = explode(";",$_SESSION['CompanyRecord']['or_copia_enviofactura']);
						   foreach ($correos as $correrow) {
						   	$mails[] = $correrow;	
						   }
						}else{
							$mails[] = $_SESSION['CompanyRecord']['or_copia_enviofactura'];	
						}
		            //
		            
					}
					/* TERMINA - POR DANIEL VILLARREAL EL 18 DE MAYO DEL 2016 */
					
					
			    	$id_factura = $_POST['TransNo'];
				   	foreach($mails as $value){
				   		$sql_correos = "INSERT INTO rh_facturas_enviadas SET  id_factura='{$id_factura}' ,
						    fecha=now() ,
						    usuario='',
						    nombre_usuario='{$_SESSION['UserID']}' ,
						    correos='{$value}'";
						DB_query($sql_correos,$db);
			
					}//end foreach
				  /*}	*/
				  /* SE MODIFICO EL ASUNTO DEL CORREO */
					$SQL = "SELECT folio,brname
							FROM custbranch INNER JOIN debtortrans
								ON custbranch.debtorno= debtortrans.debtorno
								AND custbranch.branchcode=debtortrans.branchcode
						WHERE debtortrans.type=10
						AND debtortrans.transno=" .$_POST['TransNo'];

					$ErrMsg = _('There was a problem retrieving the contact details for the customer');
					$ContactResult=DB_query($SQL,$db,$ErrMsg);
					$ContactResult= DB_fetch_array($ContactResult);
					
					$EmailSubjet = 'Folio:'.$ContactResult['folio'].' | Titular:'.$ContactResult['brname'].' | No. Factura: '.$row['serie'].$row['folio']; 
					/* TERMINA POR DANIEL VILLARREAL EL 10 DE JUNIO DEL 2016 */
				
			$mail_error=
			    EnviarMail(
			    	html_entity_decode('Servicio de Facturaci&oacute;n',ENT_COMPAT | ENT_HTML401 ,'ISO8859-15'),
			    	$mails,
			    		$EmailSubjet,
				    "Anexo se encuentra el CFDI en formato XML y PDF.",
				    $Adjuntos,
				    $BCC,
		    		$repplyTo
				);
			$mail_success=$mail_error=='';
			
        }
        $rootpath=$rootpathX;
        $title = _('Emailing') . ' CFDI ' . _('Number') . ' ' . $_POST['TransNo'];
        include(dirname(__FILE__).'/includes/header.inc');
        if($mail_success)
            echo "<P>CFDI " . _('number') . ' ' . $_POST['TransNo'] . ' ' . _('has been emailed to') . ' ' . $_POST['EmailAddr'];
        else
            echo "<P>Mailer Error: " . $mail_error;
        include(dirname(__FILE__).'/includes/footer.inc');
        exit;
    }
    //Termina Jaime (agregado) si la factura es un CFD se envia por Email el archivo .xml
// 	if ($_SESSION['InvoicePortraitFormat']==0){
// 		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/rh_PrintCustTrans.php?' . SID . '&FromTransNo=' . $_POST['TransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>";

// 		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/rh_PrintCustTrans.php?' . SID . '&FromTransNo=' . $_POST['FromTransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
// 	} else {
// 		echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/PrintCustTransPortrait.php?' . SID . '&FromTransNo=' . $_POST['TransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>";

// 		prnMsg(_('The transaction should have been emailed off') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ')' . "<a href='" . $rootpath . '/PrintCustTransPortrait.php?' . SID . '&FromTransNo=' . $_POST['FromTransNo'] . '&PrintPDF=Yes&InvOrCredit=' . $_POST['InvOrCredit'] .'&Email=' . $_POST['EmailAddr'] . "'>" . _('click here') . '</a> ' . _('to email the customer transaction'),'success');
// 	}
	exit;
} elseif (isset($_POST['DoIt'])) {
	$_GET['InvOrCredit'] = $_POST['InvOrCredit'];
	$_GET['FromTransNo'] = $_POST['FromTransNo'];
	prnMsg(_('The email address entered is too short to be a valid email address') . '. ' . _('The transaction was not emailed'),'warn');
}

include ('includes/header.inc');




echo "<FORM ACTION='" . $_SERVER['PHP_SELF'] . '?' . SID . "' METHOD=POST>";

echo "<INPUT TYPE=HIDDEN NAME='TransNo' VALUE=" . $_GET['FromTransNo'] . ">";
echo "<INPUT TYPE=HIDDEN NAME='InvOrCredit' VALUE=" . $_GET['InvOrCredit'] . '>';
//Jaime (agregado) se agrega el atributo isCfd para saber si la factura es un CFD
if (isSet($_GET['isCfd']) && $_GET['isCfd'])
    echo '<input type="hidden" name="isCfd" value="1">';
if (isSet($_GET['isCartaPorte']) && $_GET['isCartaPorte'])
    echo '<input type="hidden" name="isCartaPorte" value="1">';
//Termina Jaime (agregado) se agrega el atributo isCfd para saber si la factura es un CFD

echo '<CENTER><P><TABLE>';
$SQL="select * from debtortrans where rh_status='C' and type=10 and transno='".$_GET['FromTransNo']."' limit 1";
if(DB_num_rows(DB_query($SQL,$db,'','',0,0))>0){
	echo '<input type="hidden" name="isCfdCancelado" value="true">';
}

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
