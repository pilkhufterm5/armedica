<?php

/* webERP Revision: 14 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-08-08 12:46:27 -0500 (Fri, 08 Aug 2008) $
 * $Rev: 385 $
 */
$PageSecurity =3;

include('includes/session.inc');

/*Agrega Datos Bancarios para las Cartas de Adeudo*/
if(!empty($_POST['DunningBank'])){

    if($_POST['DunningBank']['Agregar']){
        $ParseInputs = parse_str($_POST['DunningBank']['Inputs'], $Inputs);
        $SQLInsert = "INSERT INTO rh_dunning_banks (coycode,banco,nombre_cuenta,clabe,tipo,activo)
                    VALUES(1,'{$Inputs['b_banco']}','{$Inputs['b_nombre_cuenta']}','{$Inputs['b_clabe']}', '{$Inputs['b_tipo']}',1)";
        if(DB_query($SQLInsert,$db)){
            echo json_encode(array(
                'requestresult' => 'ok',
                'message' => "Banco Agregado Correctamente...",
            ));
        }else{
            echo json_encode(array(
                'requestresult' => 'fail',
                'message' => "No se pudo Agregar el Banco...",
            ));
        }
    }

    if($_POST['DunningBank']['Borrar']){
        $SQLDelete = "DELETE  FROM rh_dunning_banks WHERE id = {$_POST['DunningBank']['BankID']}";
        if(DB_query($SQLDelete,$db)){
            echo json_encode(array(
                'requestresult' => 'ok',
                'message' => "El Banco fue Eliminado Correctamente...",
            ));
        }else{
            echo json_encode(array(
                'requestresult' => 'fail',
                'message' => "No se pudo Eliminar el Banco...",
            ));
        }
    }

    return;
}

$title = _('Company Preferences');

include('includes/header.inc');
require_once('includes/rh_functions.php');

if (isset($Errors)) {
	unset($Errors);
}


//initialise no input errors assumed initially before we test
$InputError = 0;
$Errors = array();
$i=1;

if (isset($_POST['submit'])) {
        //Jaime, se sube la imagen de la empresa
       if ($_FILES["imagenFactura"]["error"] < 1)
            move_uploaded_file($_FILES['imagenFactura']['tmp_name'],"companies/".$_SESSION['DatabaseName']."/headFactura.jpg");

        if ($_FILES["imagenEmpresa"]["error"] < 1)
            move_uploaded_file($_FILES['imagenEmpresa']['tmp_name'],"companies/".$_SESSION['DatabaseName']."/logo.jpg");
        //\Jaime, se sube la imagen de la empresa
        //Jaime, se sube la imagen de la clausula
        if ($_FILES["imagenClausula"]["error"] < 1)
            move_uploaded_file($_FILES['imagenClausula']['tmp_name'],"companies/".$_SESSION['DatabaseName']."/clausula.jpg");
        //\Jaime, se sube la imagen de la clausula

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */


	//first off validate inputs sensible

	if (strlen($_POST['CoyName']) > 150 OR strlen($_POST['CoyName'])==0) {
		$InputError = 1;
		prnMsg(_('The company name must be entered and be fifty characters or less long'), 'error');
		$Errors[$i] = 'CoyName';
		$i++;
	}
	if (strlen($_POST['RegOffice1']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 1 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'RegOffice1';
		$i++;
	}
	if (strlen($_POST['RegOffice2']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 2 of the address must be forty characters or less long'),'error');
		$Errors[$i] = 'RegOffice2';
		$i++;
	}
	if (strlen($_POST['RegOffice3']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 3 of the address must be 80 characters or less long'),'error');
		$Errors[$i] = 'RegOffice3';
		$i++;
	}
	if (strlen($_POST['RegOffice4']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 4 of the address must be 80 characters or less long'),'error');
		$Errors[$i] = 'RegOffice4';
		$i++;
	}
	if (strlen($_POST['RegOffice5']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 5 of the address must be 80 characters or less long'),'error');
		$Errors[$i] = 'RegOffice5';
		$i++;
	}
	if (strlen($_POST['RegOffice6']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 6 of the address must be 80 characters or less long'),'error');
		$Errors[$i] = 'RegOffice6';
		$i++;
	}
	if (strlen($_POST['Telephone']) >25) {
		$InputError = 1;
		prnMsg(_('The Line 7 of the address must be 80 characters or less long'),'error');
	} elseif (strlen($_POST['RegOffice8']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 8 of the address must be 80 characters or less long'),'error');
	} elseif (strlen($_POST['RegOffice9']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 9 of the address must be 80 characters or less long'),'error');
	} elseif (strlen($_POST['RegOffice10']) >80) {
		$InputError = 1;
		prnMsg(_('The Line 10 of the address must be 80 characters or less long'),'error');
	} elseif (strlen($_POST['Telephone']) >25) {
		$InputError = 1;
		prnMsg(_('The telephone number must be 25 characters or less long'),'error');
		$Errors[$i] = 'Telephone';
		$i++;
	}
	if (strlen($_POST['Fax']) >25) {
		$InputError = 1;
		prnMsg(_('The fax number must be 25 characters or less long'),'error');
		$Errors[$i] = 'Fax';
		$i++;
	}
	if (strlen($_POST['Email']) >256) {
		$InputError = 1;
		prnMsg(_('The email address must be 255 characters or less long'),'error');
		$Errors[$i] = 'Email';
		$i++;
	}
	if (strlen($_POST['Email'])>0 and !IsEmailAddress($_POST['Email'])) {
		$InputError = 1;
		prnMsg(_('The email address is not correctly formed'),'error');
		$Errors[$i] = 'Email';
		$i++;
	}

	if ($InputError !=1){
		// bowikaxu - Se agrego el insert de la cuenta rh_InvoiceShipmentact
		//rleal Mar 4, 2010 Se agregaron las address7-10 para FE
		$sql = "UPDATE companies SET
				coyname='" . DB_escape_string($_POST['CoyName']) . "',
				companynumber = '" . DB_escape_string($_POST['CompanyNumber']) . "',
				gstno='" . DB_escape_string($_POST['GSTNo']) . "',
				regoffice1='" . DB_escape_string($_POST['RegOffice1']) . "',
				regoffice2='" . DB_escape_string($_POST['RegOffice2']) . "',
				regoffice3='" . DB_escape_string($_POST['RegOffice3']) . "',
				regoffice4='" . DB_escape_string($_POST['RegOffice4']) . "',
				regoffice5='" . DB_escape_string($_POST['RegOffice5']) . "',
				regoffice6='" . DB_escape_string($_POST['RegOffice6']) . "',
				regoffice7='" . DB_escape_string($_POST['RegOffice7']) . "',
				regoffice8='" . DB_escape_string($_POST['RegOffice8']) . "',
				regoffice9='" . DB_escape_string($_POST['RegOffice9']) . "',
				regoffice10='" . DB_escape_string($_POST['RegOffice10']) . "',
				telephone='" . DB_escape_string($_POST['Telephone']) . "',
				fax='" . DB_escape_string($_POST['Fax']) . "',
				email='" . DB_escape_string($_POST['Email']) . "',
				currencydefault='" . DB_escape_string($_POST['CurrencyDefault']) . "',
				debtorsact=" . DB_escape_string($_POST['DebtorsAct']) . ",
				pytdiscountact=" . DB_escape_string($_POST['PytDiscountAct']) . ",
				creditorsact=" . DB_escape_string($_POST['CreditorsAct']) . ",
				payrollact=" . DB_escape_string($_POST['PayrollAct']) . ",
				grnact=" . DB_escape_string($_POST['GRNAct']) . ",
				exchangediffact=" . DB_escape_string($_POST['ExchangeDiffAct']) . ",
				purchasesexchangediffact=" . DB_escape_string($_POST['PurchasesExchangeDiffAct']) . ",
				retainedearnings=" . DB_escape_string($_POST['RetainedEarnings']) . ",
				gllink_debtors=" . $_POST['GLLink_Debtors'] . ",
				gllink_creditors=" . $_POST['GLLink_Creditors'] . ",
				gllink_stock=" . $_POST['GLLink_Stock'] .",
				rh_InvoiceShipmentact = ".$_POST['rh_InvoiceShipmentact'].",
				rh_advdebtorsact = ".$_POST['AntDebtorsAct'].",
				site= '".$_POST['Site']."',
				freightact=" . DB_escape_string($_POST['FreightAct']) . ",
				rh_umbral=".$_POST['rh_umbral']."  ,
                regimen='".$_POST['regimen']."',
                dtranferencia_nombre_responsable='".$_POST['dtranferencia_nombre_responsable']."',
                dtranferencia_correo_responsable='".$_POST['dtranferencia_correo_responsable']."',
                dtranferencia_telefono_responsable='".$_POST['dtranferencia_telefono_responsable']."',
                or_copia_enviofactura='".$_POST['or_copia_enviofactura']."'
			WHERE coycode=1";
//rh_advcreditorsact = ".DB_escape_string($_POST['AntCreditorsAct']).",
			$ErrMsg =  _('The company preferences could not be updated because');
			$result = DB_query($sql,$db,$ErrMsg);
			prnMsg( _('Company preferences updated'),'success');

			/* Alter the exchange rates in the currencies table */

			/* Get default currency rate */
			$sql='SELECT rate from currencies WHERE currabrev="'.$_POST['CurrencyDefault'].'"';
			$result = DB_query($sql,$db);
			$myrow = DB_fetch_row($result);
			$NewCurrencyRate=$myrow[0];

			/* Set new rates */
			$sql='UPDATE currencies SET rate=rate/'.$NewCurrencyRate;
			$ErrMsg =  _('Could not update the currency rates');
			$result = DB_query($sql,$db,$ErrMsg);

			/* End of update currencies */

			$ForceConfigReload = True; // Required to force a load even if stored in the session vars
			include('includes/GetConfig.php');
			$ForceConfigReload = False;

	} else {
		prnMsg( _('Validation failed') . ', ' . _('no updates or deletes took place'),'warn');
	}

} /* end of if submit */



echo '<FORM enctype="multipart/form-data" METHOD="post" action=' . $_SERVER['PHP_SELF'] . '>';
echo '<CENTER><TABLE>';
if ($InputError != 1) {
// bowikaxu - Se agrego obtener la cuenta rh_InvoiceShipmentact
$sql = "SELECT coyname,
		gstno,
		companynumber,
		regoffice1,
		regoffice2,
		regoffice3,
		regoffice4,
		regoffice5,
		regoffice6,
		regoffice7,
		regoffice8,
		regoffice9,
		regoffice10,
		telephone,
		fax,
		email,
		currencydefault,
		debtorsact,
		pytdiscountact,
		creditorsact,
		payrollact,
		grnact,
		exchangediffact,
		purchasesexchangediffact,
		retainedearnings,
		gllink_debtors,
		gllink_creditors,
		gllink_stock,
		rh_InvoiceShipmentact,
		rh_advdebtorsact,
		site,
		freightact,
		rh_umbral,
        regimen,
        dtranferencia_nombre_responsable,
        dtranferencia_correo_responsable,
        dtranferencia_telefono_responsable,
        or_copia_enviofactura
	FROM companies
	WHERE coycode=1";
//rh_advcreditorsact,


$ErrMsg =  _('The company preferences could not be retrieved because');
$result = DB_query($sql, $db,$ErrMsg);


$myrow = DB_fetch_array($result);

$_POST['CoyName'] = $myrow['coyname'];
$_POST['GSTNo'] = $myrow['gstno'];
$_POST['CompanyNumber']  = $myrow['companynumber'];
$_POST['RegOffice1']  = $myrow['regoffice1'];
$_POST['RegOffice2']  = $myrow['regoffice2'];
$_POST['RegOffice3']  = $myrow['regoffice3'];
$_POST['RegOffice4']  = $myrow['regoffice4'];
$_POST['RegOffice5']  = $myrow['regoffice5'];
$_POST['RegOffice6']  = $myrow['regoffice6'];
$_POST['RegOffice7']  = $myrow['regoffice7'];
$_POST['RegOffice8']  = $myrow['regoffice8'];
$_POST['RegOffice9']  = $myrow['regoffice9'];
$_POST['RegOffice10']  = $myrow['regoffice10'];
$_POST['Telephone']  = $myrow['telephone'];
$_POST['Fax']  = $myrow['fax'];
$_POST['Email']  = $myrow['email'];
$_POST['CurrencyDefault']  = $myrow['currencydefault'];
$_POST['DebtorsAct']  = $myrow['debtorsact'];
$_POST['PytDiscountAct']  = $myrow['pytdiscountact'];
$_POST['CreditorsAct']  = $myrow['creditorsact'];
$_POST['PayrollAct']  = $myrow['payrollact'];
$_POST['GRNAct'] = $myrow['grnact'];
$_POST['ExchangeDiffAct']  = $myrow['exchangediffact'];
$_POST['PurchasesExchangeDiffAct']  = $myrow['purchasesexchangediffact'];
$_POST['RetainedEarnings'] = $myrow['retainedearnings'];
$_POST['GLLink_Debtors'] = $myrow['gllink_debtors'];
$_POST['GLLink_Creditors'] = $myrow['gllink_creditors'];
$_POST['GLLink_Stock'] = $myrow['gllink_stock'];
$_POST['FreightAct'] = $myrow['freightact'];
$_POST['rh_InvoiceShipmentact'] = $myrow['rh_InvoiceShipmentact'];
// bowikaxu realhost Feb 2008 -
$_POST['AntDebtorsAct'] = $myrow['rh_advdebtorsact'];
//$_POST['AntCreditorsAct'] = $myrow['rh_advcreditorsact'];
$_POST['rh_umbral'] = $myrow['rh_umbral'];
$_POST['regimen'] = $myrow['regimen'];
$_POST['Site'] = $myrow['site'];
$_POST['dtranferencia_nombre_responsable'] = $myrow['dtranferencia_nombre_responsable'];
$_POST['dtranferencia_correo_responsable'] = $myrow['dtranferencia_correo_responsable'];
$_POST['dtranferencia_telefono_responsable'] = $myrow['dtranferencia_telefono_responsable'];
$_POST['or_copia_enviofactura'] = $myrow['or_copia_enviofactura'];

}

echo '<TR><TD>' . _('Name') . ' (' . _('to appear on reports') . '):</TD>
	<TD><input '.(in_array('CoyName',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="1" type="Text" Name="CoyName" value="' .
stripslashes($_POST['CoyName']) . '" SIZE=50 MAXLENGTH=150></TD>
</TR>';

echo '<TR><TD>' . _('CURP') . ':</TD>
	<TD><input '.(in_array('CoyNumber',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="2" type="Text" Name="CompanyNumber" value="' . $_POST['CompanyNumber'] . '" SIZE=22 MAXLENGTH=20></TD>
	</TR>';

echo '<TR><TD>' . _('Tax Authority Reference') . ':</TD>
	<TD><input '.(in_array('TaxRef',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="3" type="Text" Name="GSTNo" value="' . $_POST['GSTNo'] . '" SIZE=22 MAXLENGTH=20></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 1') . ':</TD>
	<TD><input '.(in_array('RegOffice1',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="4" type="Text" Name="RegOffice1" SIZE=42 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice1']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 2') . ':</TD>
	<TD><input '.(in_array('RegOffice2',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="5" type="Text" Name="RegOffice2" SIZE=42 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice2']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 3') . ':</TD>
	<TD><input '.(in_array('RegOffice3',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="6" type="Text" Name="RegOffice3" SIZE=42 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice3']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 4') . ':</TD>
	<TD><input '.(in_array('RegOffice4',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="7" type="Text" Name="RegOffice4" SIZE=42 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice4']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 5') . ':</TD>
	<TD><input '.(in_array('RegOffice5',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="8" type="Text" Name="RegOffice5" SIZE=42 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice5']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 6') . ':</TD>
	<TD><input '.(in_array('RegOffice6',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="9" type="Text" Name="RegOffice6" SIZE=17 MAXLENGTH=150
value="' . stripslashes($_POST['RegOffice6']) . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 7') . ':</TD>
	<TD><input type="Text" Name="RegOffice7"  tabindex="10" SIZE=42 MAXLENGTH=150 value="' . $_POST['RegOffice7'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 8') . ':</TD>
	<TD><input type="Text" Name="RegOffice8" tabindex="11" SIZE=40 MAXLENGTH=150 value="' . $_POST['RegOffice8'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 9') . ':</TD>
	<TD><input type="Text" Name="RegOffice9" tabindex="12" SIZE=40 MAXLENGTH=150 value="' . $_POST['RegOffice9'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Address Line 10') . ':</TD>
	<TD><input type="Text" Name="RegOffice10" tabindex="13" SIZE=15 MAXLENGTH=150 value="' . $_POST['RegOffice10'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Site web') . ':</TD>
	<TD><input  tabindex="14" type="Text" Name="Site"
value="' . $_POST['Site'] . '"></TD>
</TR>';


echo '<TR><TD>' . _('Telephone Number') . ':</TD>
	<TD><input '.(in_array('Telephone',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="14" type="Text" Name="Telephone" SIZE=26 MAXLENGTH=25
value="' . $_POST['Telephone'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Facsimile Number') . ':</TD>
	<TD><input '.(in_array('Fax',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="15" type="Text" Name="Fax" SIZE=26 MAXLENGTH=25 value="' .
$_POST['Fax'] . '"></TD>
</TR>';

echo '<TR><TD>' . _('Email Address') . ':</TD>
	<TD><input '.(in_array('Email',$Errors) ?  'class="inputerror"' : '' ) .' tabindex="16" type="Text" Name="Email" SIZE=50 MAXLENGTH=255 value="' .
$_POST['Email'] . '"></TD>
</TR>';


$result=DB_query("SELECT currabrev, currency FROM currencies",$db);

echo '<TR><TD>' . _('Home Currency') . ':</TD><TD><SELECT tabindex="17" Name=CurrencyDefault>';

while ($myrow = DB_fetch_array($result)) {
	if ($_POST['CurrencyDefault']==$myrow['currabrev']){
		echo "<OPTION SELECTED VALUE='". $myrow['currabrev'] . "'>" . $myrow['currency'];
	} else {
		echo "<OPTION VALUE='". $myrow['currabrev'] . "'>" . $myrow['currency'];
	}
} //end while loop

DB_free_result($result);

echo '</SELECT></TD></TR>';

$result=DB_query("SELECT accountcode,
			accountname
		FROM chartmaster,
			accountgroups
		WHERE chartmaster.group_=accountgroups.groupname
		AND accountgroups.pandl=0
		ORDER BY chartmaster.accountcode",$db);

/*
	rleal
	Nov 27 2010
	Se quitan las ligas a petición de roberto castillo
*/
echo '<TR><TD>' . _('Debtors Control GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="18" Name=DebtorsAct>';

while ($myrow = DB_fetch_row($result)) {
		if ($_POST['DebtorsAct']==$myrow[0]){
			echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
		} else {
			echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
		}
} //end while loop
DB_data_seek($result,0);
echo '</SELECT></TD>';
echo '</TR>';

// bowikaxu realhost - Feb 2008 - cuenta anticipo clientes
$result=DB_query("SELECT accountcode,
			accountname
		FROM chartmaster,
			accountgroups
		WHERE chartmaster.group_=accountgroups.groupname
		AND accountgroups.pandl=0
		ORDER BY chartmaster.accountcode",$db);

echo '<TR><TD>' . _('Advance Debtors Control GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="19" Name=AntDebtorsAct>';

while ($myrow = DB_fetch_row($result)) {
		if ($_POST['AntDebtorsAct']==$myrow[0]){
			echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
		} else {
			echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
		}
} //end while loop
DB_data_seek($result,0);
echo '</SELECT></TD>';
echo '</TR>';
// en anticipo clientes

echo '<TR><TD>' . _('Creditors Control GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="20"  Name=CreditorsAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['CreditorsAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
//echo '</TR>';


/*
// bowikaxu realhost Feb 2008 - anticipo proveedores
echo '<TR><TD>' . _('Advance Creditors Control GL Account') . ':</TD><TD><SELECT Name=AntCreditorsAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['AntCreditorsAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD></TR>';
// end anticipo proveedores
*/
// bowikaxu - LISTADO DE CUENTAS PARA VENTAS AL PUBLICO

echo '<TR><TD>' . _('Remisiones') . ':</TD>';
echo '<TD>';
echo '<SELECT Name=rh_InvoiceShipmentact>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['rh_InvoiceShipmentact']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

// bowikaxu - TERMINA LISTADO DE CUENTAS PARA VENTAS AL PUBLICO


echo '<TR><TD>' . _('Payroll Net Pay Clearing GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT Name=PayrollAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['PayrollAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Goods Received Clearing GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="19" Name=GRNAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['GRNAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);
echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Retained Earning Clearing GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="20" Name=RetainedEarnings>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['RetainedEarnings']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_free_result($result);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Freight Re-charged GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="21" Name=FreightAct>';

$result=DB_query('SELECT accountcode,
			accountname
		FROM chartmaster,
			accountgroups
		WHERE chartmaster.group_=accountgroups.groupname
		AND accountgroups.pandl=1
		ORDER BY chartmaster.accountcode',$db);

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['FreightAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Sales Exchange Variances GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="22" Name=ExchangeDiffAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['ExchangeDiffAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Purchases Exchange Variances GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="23" Name=PurchasesExchangeDiffAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['PurchasesExchangeDiffAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Payment Discount GL Account') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="24" Name=PytDiscountAct>';

while ($myrow = DB_fetch_row($result)) {
	if ($_POST['PytDiscountAct']==$myrow[0]){
		echo "<OPTION SELECTED VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	} else {
		echo "<OPTION  VALUE='". $myrow[0] . "'>" . $myrow[1] . ' ('.$myrow[0].')';
	}
} //end while loop

DB_data_seek($result,0);

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Create GL entries for accounts receivable transactions') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="25" Name=GLLink_Debtors>';

if ($_POST['GLLink_Debtors']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('No');
	echo '<OPTION VALUE=1>' . _('Yes');
} else {
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
}

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Create GL entries for accounts payable transactions') . ':</TD>';
echo '<TD>';
echo '<SELECT tabindex="26" Name=GLLink_Creditors>';

if ($_POST['GLLink_Creditors']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('No');
	echo '<OPTION VALUE=1>' . _('Yes');
} else {
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
}

echo '</SELECT></TD>';
echo '</TR>';

echo '<TR><TD>' . _('Create GL entries for stock transactions') . ' (' . _('at standard cost') . '):</TD>';
echo '<TD>';
echo '<SELECT tabindex="27" Name=GLLink_Stock>';

if ($_POST['GLLink_Stock']==0){
	echo '<OPTION SELECTED VALUE=0>' . _('No');
	echo '<OPTION VALUE=1>' . _('Yes');
} else {
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
}
/****************************************************************************************************************************
* Jorge Garcia
* 08/Dic/2008
****************************************************************************************************************************/

echo '<TR><TD>' . _('Asignacion Umbral') . ':</TD>';

echo '<TD>';

echo '<input tabindex="28" type="Text" Name="rh_umbral" SIZE=4 MAXLENGTH=3 value="'.($_POST['rh_umbral']).'"></TD>
	</TR>';

echo '<input tabindex="28" type="Text" Name="rh_umbral" SIZE=4 MAXLENGTH=3 value=1></TD> </TR>';

/****************************************************************************************************************************
* Jorge Garcia Fin Modificacion
****************************************************************************************************************************/

echo '
</SELECT></TD></TR>';

echo '<TR><TD>' . _('Regimen Fiscal') . ':</TD>';
echo '<TD>';
echo '<input tabindex="28" type="Text" Name="regimen"  MAXLENGTH=100 value="'.($_POST['regimen']).'"></TD>
	</TR>';
//Jaime, se sube la imagen de la empresa

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";

?>
<tr>
    <td>
        <?php echo _('Imagen Empresa') . ':' ?>
    </td>
    <td>
        <input type="file" name="imagenEmpresa" value="" />
    </td>
</tr>
<tr>
    <td>
        <?php echo _('Encabezado de Facturas') . ':' ?>
    </td>
    <td>
        <input type="file" name="imagenFactura" value="" />
    </td>
</tr>
<?php

    $_2GetBankData = "SELECT * FROM rh_dunning_banks WHERE coycode = 1 ";
    $GetBankData = DB_query($_2GetBankData, $db);

?>
<tr>
    <td>
        <?php echo _('Imagen Clausula') . ':' ?>
    </td>
    <td>
        <input type="file" name="imagenClausula" value="" />
    </td>
</tr>

<tr>
    <td><strong>Datos Para Carta de Cobranza</strong></td>
    <td></td>
</tr>

<tr>
    <td>Nombre Responsable</td>
    <td>
        <input type="text" name="dtranferencia_nombre_responsable" value="<?=$_POST['dtranferencia_nombre_responsable']?>" />
    </td>
</tr>

<tr>
    <td>Correo Responsable</td>
    <td>
        <input type="text" name="dtranferencia_correo_responsable" value="<?=$_POST['dtranferencia_correo_responsable']?>" />
    </td>
</tr>

<tr>
    <td>Telefono Responsable</td>
    <td>
        <input type="text" name="dtranferencia_telefono_responsable" value="<?=$_POST['dtranferencia_telefono_responsable']?>" />
    </td>
</tr>
<tr>
    <td>Cuenta correo p/ copias facturas</td>
    <td>
        <input type="text" name="or_copia_enviofactura" value="<?=$_POST['or_copia_enviofactura']?>" />
    </td>
</tr>

<tr>
    <td>Cuentas Bancarias</td>
    <td></td>
</tr>

<tr>
    <td colspan="2">
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>Nombre Cuenta</th>
                    <th>Banco</th>
                    <th>Tipo</th>
                    <th>Cuenta/Clabe</th>
                    <th>Status</th>
                    <th style="text-align:center;">
                        <a title="Agregar Banco" onclick="AddBankData()" href="javascript:;">
                            <i class="icon-plus-sign" ></i>
                        </a>
                    </th>
                </tr>
            </thead>
            <body id="BankTBody">
            <?php while ($Bank = DB_fetch_assoc($GetBankData)) {
                if($Bank['activo'] ==1){
                    $Bank['activo'] = "Activo";
                }else{
                    $Bank['activo'] = "Inactivo";
                }
                ?>
                <tr>
                    <td><?=$Bank['nombre_cuenta']?></td>
                    <td><?=$Bank['banco']?></td>
                    <td><?=$Bank['tipo']?></td>
                    <td><?=$Bank['clabe']?></td>
                    <td><?=$Bank['activo']?></td>
                    <td style="text-align:center;">
                        <a title="Eliminar Banco" onclick="DelBankData(<?=$Bank['id']?>)" href="javascript:;">
                            <i class="icon-minus-sign" ></i>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </body>
        </table>

    </td>

</tr>

<!-- START MODAL-->
<div id="ModalAddBank" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Agregar Banco</h3>
    </div>
    <div class="modal-body">
        <p>

            <div class="control-group row-fluid">
                <div class="span4">
                    <label class="control-label">Nombre Cuenta</label>
                </div>
                <div class="span8">
                    <div class="controls">
                        <input type="text" name="b_nombre_cuenta" id="b_nombre_cuenta" class="BankField">
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span4">
                    <label class="control-label">Nombre Banco</label>
                </div>
                <div class="span8">
                    <div class="controls">
                        <input type="text" name="b_banco" id="b_banco" class="BankField">
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span4">
                    <label class="control-label">Cuenta/Clabe</label>
                </div>
                <div class="span8">
                    <div class="controls">
                        <input type="text" name="b_clabe" id="b_clabe" class="BankField">
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span4">
                    <label class="control-label">Tipo</label>
                </div>
                <div class="span8">
                    <div class="controls">
                        <select id="b_tipo" name="b_tipo" class="BankField">
                            <option>Deposito</option>
                            <option>Transferencia</option>
                        </select>
                    </div>
                </div>
            </div>

        </p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        <button class="btn btn-primary" id="AddBank" class="BankField" >Save changes</button>
    </div>
</div>
<!-- END MODAL-->
<script type="text/javascript">
    $(document).on('ready', function() {

        $('#AddBank').click(function() {

            //return false;
            var jqxhr = $.ajax({
                url: "CompanyPreferences.php",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    DunningBank:{
                        Inputs: $('.BankField').serialize(),
                        Agregar: 'Agregar'
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#ModalAddBank').modal('toggle');
                        window.setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
            return false;
        });

    });

    function AddBankData(){
        $('#ModalAddBank').modal('show');
    }

    function DelBankData(id){
        var jqxhr = $.ajax({
            url: "CompanyPreferences.php",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                DunningBank:{
                    BankID: id,
                    Borrar: 'Borrar'
                },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                    window.setTimeout(function() {
                        location.reload();
                    }, 1000);
                }else{
                    displayNotify('error', Response.message);
                }
            },
            error : ajaxError
        });
    }

</script>


<?php
//\Jaime, se sube la imagen de la clausula
echo '</TABLE><CENTER>
<br><br><br>
<input tabindex="29" type="Submit" Name="submit" class="btn btn-success" value="' . _('Actualizar Datos') . '">';

include('includes/footer.inc');
?>
