<?php

/* $Revision: 138 $ */
/* $Revision: 138 $ */

$PageSecurity =15;

include('includes/session.inc');

$title = _('System Configuration');

include('includes/header.inc');
if (isset($_POST['submit'])) {

	//initialise no input errors assumed initially before we test
	$InputError = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible
	/*
	Note: the X_ in the POST variables, the reason for this is to overcome globals=on replacing
	the actial system/overidden variables.
	*/
	if (strlen($_POST['X_PastDueDays1']) > 3 || !is_numeric($_POST['X_PastDueDays1']) ) {
		$InputError = 1;
		prnMsg(_('First overdue deadline days must be a number'),'error');
	} elseif (strlen($_POST['X_PastDueDays2'])  > 3 || !is_numeric($_POST['X_PastDueDays2']) ) {
		$InputError = 1;
		prnMsg(_('Second overdue deadline days must be a number'),'error');
	} elseif (strlen($_POST['X_DefaultCreditLimit']) > 12 || !is_numeric($_POST['X_DefaultCreditLimit']) ) {
		$InputError = 1;
		prnMsg(_('Default Credit Limit must be a number'),'error');
	} elseif (strstr($_POST['X_RomalpaClause'], "'") || strlen($_POST['X_RomalpaClause']) > 5000) {
		$InputError = 1;
		prnMsg(_('The Romalpa Clause may not contain single qoutes and may not be longer than 5000 chars'),'error');
	} elseif (strlen($_POST['X_QuickEntries']) > 2 || !is_numeric($_POST['X_QuickEntries']) ||
	$_POST['X_QuickEntries'] < 1 || $_POST['X_QuickEntries'] > 99 ) {
		$InputError = 1;
		prnMsg(_('No less than 1 and more than 99 Quick entries allowed'),'error');
	} elseif (strlen($_POST['X_FreightChargeAppliesIfLessThan']) > 12 || !is_numeric($_POST['X_FreightChargeAppliesIfLessThan']) ) {
		$InputError = 1;
		prnMsg(_('Freight Charge Applies If Less Than must be a number'),'error');
	} elseif (strlen($_POST['X_NumberOfPeriodsOfStockUsage']) > 2 || !is_numeric($_POST['X_NumberOfPeriodsOfStockUsage']) ||
	$_POST['X_NumberOfPeriodsOfStockUsage'] < 1 || $_POST['X_NumberOfPeriodsOfStockUsage'] > 12 ) {
		$InputError = 1;
		prnMsg(_('Finantial period per year must be a number between 1 and 12'),'error');
	} elseif (strlen($_POST['X_TaxAuthorityReferenceName']) >25) {
		$InputError = 1;
		prnMsg(_('The Tax Authority Reference Name must be 25 characters or less long'),'error');
	} elseif (strlen($_POST['X_OverChargeProportion']) > 3 || !is_numeric($_POST['X_OverChargeProportion']) ||
	$_POST['X_OverChargeProportion'] < 0 || $_POST['X_OverChargeProportion'] > 100 ) {
		$InputError = 1;
		prnMsg(_('Over Charge Proportion must be a percentage'),'error');
	} elseif (strlen($_POST['X_OverReceiveProportion']) > 3 || !is_numeric($_POST['X_OverReceiveProportion']) ||
	$_POST['X_OverReceiveProportion'] < 0 || $_POST['X_OverReceiveProportion'] > 100 ) {
		$InputError = 1;
		prnMsg(_('Over Receive Proportion must be a percentage'),'error');
	} elseif (strlen($_POST['X_PageLength']) > 3 || !is_numeric($_POST['X_PageLength']) ||
	$_POST['X_PageLength'] < 1 ) {
		$InputError = 1;
		prnMsg(_('Lines per page must be greater than 1'),'error');
	} elseif (strlen($_POST['X_MonthsAuditTrail']) > 2 || !is_numeric($_POST['X_MonthsAuditTrail']) ||
		$_POST['X_MonthsAuditTrail'] < 0 ) {
		$InputError = 1;
		prnMsg(_('The number of months of audit trail to keep must be zero or a positive number less than 100 months'),'error');
	}elseif (strlen($_POST['X_DefaultTaxCategory']) > 1 || !is_numeric($_POST['X_DefaultTaxCategory']) ||
	$_POST['X_DefaultTaxCategory'] < 1 ) {
		$InputError = 1;
		prnMsg(_('DefaultTaxCategory must be between 1 and 9'),'error');
	} elseif (strlen($_POST['X_DefaultDisplayRecordsMax']) > 3 || !is_numeric($_POST['X_DefaultDisplayRecordsMax']) ||
	$_POST['X_DefaultDisplayRecordsMax'] < 1 ) {
		$InputError = 1;
		prnMsg(_('Default maximum number of records to display must be between 1 and 500'),'error');
	}elseif (strlen($_POST['X_MaxImageSize']) > 3 || !is_numeric($_POST['X_MaxImageSize']) ||
	$_POST['X_MaxImageSize'] < 1 ) {
		$InputError = 1;
		prnMsg(_('The maximum size of item image files musst be between 50 and 500 (NB this figure refers to KB)'),'error');
	}

	if ($InputError !=1){

		$sql = array();

		if ($_SESSION['DefaultDateFormat'] != $_POST['X_DefaultDateFormat'] ) {
			$sql[] = "UPDATE config SET confvalue = '".DB_escape_string($_POST['X_DefaultDateFormat'])."' WHERE confname = 'DefaultDateFormat'";
		}
		if ($_SESSION['DefaultTheme'] != $_POST['X_DefaultTheme'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DefaultTheme']."' WHERE confname = 'DefaultTheme'";
		}
		if ($_SESSION['PastDueDays1'] != $_POST['X_PastDueDays1'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_PastDueDays1']."' WHERE confname = 'PastDueDays1'";
		}
		if ($_SESSION['PastDueDays2'] != $_POST['X_PastDueDays2'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_PastDueDays2']."' WHERE confname = 'PastDueDays2'";
		}
		if ($_SESSION['DefaultCreditLimit'] != $_POST['X_DefaultCreditLimit'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DefaultCreditLimit']."' WHERE confname = 'DefaultCreditLimit'";
		}
		if ($_SESSION['Show_Settled_LastMonth'] != $_POST['X_Show_Settled_LastMonth'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_Show_Settled_LastMonth']."' WHERE confname = 'Show_Settled_LastMonth'";
		}
		if ($_SESSION['RomalpaClause'] != $_POST['X_RomalpaClause'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_RomalpaClause']) . "' WHERE confname = 'RomalpaClause'";
		}
		if ($_SESSION['QuickEntries'] != $_POST['X_QuickEntries'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_QuickEntries']."' WHERE confname = 'QuickEntries'";
		}
		if ($_SESSION['DispatchCutOffTime'] != $_POST['X_DispatchCutOffTime'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DispatchCutOffTime']."' WHERE confname = 'DispatchCutOffTime'";
		}
		if ($_SESSION['AllowSalesOfZeroCostItems'] != $_POST['X_AllowSalesOfZeroCostItems'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_AllowSalesOfZeroCostItems']."' WHERE confname = 'AllowSalesOfZeroCostItems'";
		}
		if ($_SESSION['CreditingControlledItems_MustExist'] != $_POST['X_CreditingControlledItems_MustExist'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_CreditingControlledItems_MustExist']."' WHERE confname = 'CreditingControlledItems_MustExist'";
		}
		if ($_SESSION['DefaultPriceList'] != $_POST['X_DefaultPriceList'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DefaultPriceList']."' WHERE confname = 'DefaultPriceList'";
		}
		if ($_SESSION['Default_Shipper'] != $_POST['X_Default_Shipper'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_Default_Shipper']."' WHERE confname = 'Default_Shipper'";
		}
		if ($_SESSION['DoFreightCalc'] != $_POST['X_DoFreightCalc'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DoFreightCalc']."' WHERE confname = 'DoFreightCalc'";
		}
		if ($_SESSION['FreightChargeAppliesIfLessThan'] != $_POST['X_FreightChargeAppliesIfLessThan'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_FreightChargeAppliesIfLessThan']."' WHERE confname = 'FreightChargeAppliesIfLessThan'";
		}
		if ($_SESSION['DefaultTaxCategory'] != $_POST['X_DefaultTaxCategory'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DefaultTaxCategory']."' WHERE confname = 'DefaultTaxCategory'";
		}
		if ($_SESSION['TaxAuthorityReferenceName'] != $_POST['X_TaxAuthorityReferenceName'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . DB_escape_string($_POST['X_TaxAuthorityReferenceName']) . "' WHERE confname = 'TaxAuthorityReferenceName'";
		}
		if ($_SESSION['CountryOfOperation'] != $_POST['X_CountryOfOperation'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_CountryOfOperation']) ."' WHERE confname = 'CountryOfOperation'";
		}
		if ($_SESSION['NumberOfPeriodsOfStockUsage'] != $_POST['X_NumberOfPeriodsOfStockUsage'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_NumberOfPeriodsOfStockUsage']."' WHERE confname = 'NumberOfPeriodsOfStockUsage'";
		}
		if ($_SESSION['Check_Qty_Charged_vs_Del_Qty'] != $_POST['X_Check_Qty_Charged_vs_Del_Qty'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_Check_Qty_Charged_vs_Del_Qty']."' WHERE confname = 'Check_Qty_Charged_vs_Del_Qty'";
		}
		if ($_SESSION['Check_Price_Charged_vs_Order_Price'] != $_POST['X_Check_Price_Charged_vs_Order_Price'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_Check_Price_Charged_vs_Order_Price']."' WHERE confname = 'Check_Price_Charged_vs_Order_Price'";
		}
		if ($_SESSION['OverChargeProportion'] != $_POST['X_OverChargeProportion'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_OverChargeProportion']."' WHERE confname = 'OverChargeProportion'";
		}
		if ($_SESSION['OverReceiveProportion'] != $_POST['X_OverReceiveProportion'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_OverReceiveProportion']."' WHERE confname = 'OverReceiveProportion'";
		}
		if ($_SESSION['PO_AllowSameItemMultipleTimes'] != $_POST['X_PO_AllowSameItemMultipleTimes'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_PO_AllowSameItemMultipleTimes']."' WHERE confname = 'PO_AllowSameItemMultipleTimes'";
		}
		if ($_SESSION['SO_AllowSameItemMultipleTimes'] != $_POST['X_SO_AllowSameItemMultipleTimes'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_SO_AllowSameItemMultipleTimes']."' WHERE confname = 'SO_AllowSameItemMultipleTimes'";
		}
		if ($_SESSION['YearEnd'] != $_POST['X_YearEnd'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_YearEnd']."' WHERE confname = 'YearEnd'";
		}
		if ($_SESSION['PageLength'] != $_POST['X_PageLength'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_PageLength']."' WHERE confname = 'PageLength'";
		}
		if ($_SESSION['DefaultDisplayRecordsMax'] != $_POST['X_DefaultDisplayRecordsMax'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_DefaultDisplayRecordsMax']."' WHERE confname = 'DefaultDisplayRecordsMax'";
		}
		if ($_SESSION['MaxImageSize'] != $_POST['X_MaxImageSize'] ) {
			$sql[] = "UPDATE config SET confvalue = '".$_POST['X_MaxImageSize']."' WHERE confname = 'MaxImageSize'";
		}
		if ($_SESSION['part_pics_dir'] != $_POST['X_part_pics_dir'] ) {
			$sql[] = "UPDATE config SET confvalue = 'companies/" . $_SESSION['DatabaseName'] . '/' . DB_escape_string($_POST['X_part_pics_dir'])."' WHERE confname = 'part_pics_dir'";
		}
		if ($_SESSION['reports_dir'] != $_POST['X_reports_dir'] ) {
			$sql[] = "UPDATE config SET confvalue = 'companies/" . $_SESSION['DatabaseName'] . '/' . DB_escape_string($_POST['X_reports_dir'])."' WHERE confname = 'reports_dir'";
		}
		if ($_SESSION['AutoDebtorNo'] != $_POST['X_AutoDebtorNo'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_AutoDebtorNo'])."' WHERE confname = 'AutoDebtorNo'";
		}
		if ($_SESSION['HTTPS_Only'] != $_POST['X_HTTPS_Only'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_HTTPS_Only'])."' WHERE confname = 'HTTPS_Only'";
		}
		if ($_SESSION['DB_Maintenance'] != $_POST['X_DB_Maintenance'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_DB_Maintenance'])."' WHERE confname = 'DB_Maintenance'";
		}
		if ($_SESSION['DefaultBlindPackNote'] != $_POST['X_DefaultBlindPackNote'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_DefaultBlindPackNote'])."' WHERE confname = 'DefaultBlindPackNote'";
		}
		if ($_SESSION['PackNoteFormat'] != $_POST['X_PackNoteFormat'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_PackNoteFormat'])."' WHERE confname = 'PackNoteFormat'";
		}
		if ($_SESSION['CheckCreditLimits'] != $_POST['X_CheckCreditLimits'] ) {
			$sql[] = "UPDATE config SET confvalue = '". ($_POST['X_CheckCreditLimits'])."' WHERE confname = 'CheckCreditLimits'";
		}
		if ($_SESSION['WikiApp'] != $_POST['X_WikiApp'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_WikiApp'])."' WHERE confname = 'WikiApp'";
		}
		if ($_SESSION['WikiPath'] != $_POST['X_WikiPath'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_WikiPath'])."' WHERE confname = 'WikiPath'";
		}
		if ($_SESSION['ProhibitJournalsToControlAccounts'] != $_POST['X_ProhibitJournalsToControlAccounts'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_ProhibitJournalsToControlAccounts'])."' WHERE confname = 'ProhibitJournalsToControlAccounts'";
		}
		if ($_SESSION['InvoicePortraitFormat'] != $_POST['X_InvoicePortraitFormat'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_InvoicePortraitFormat'])."' WHERE confname = 'InvoicePortraitFormat'";
		}
		if ($_SESSION['AllowOrderLineItemNarrative'] != $_POST['X_AllowOrderLineItemNarrative'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_AllowOrderLineItemNarrative'])."' WHERE confname = 'AllowOrderLineItemNarrative'";
		}
		if ($_SESSION['vtiger_integration'] != $_POST['X_vtiger_integration'] ) {
			$sql[] = "UPDATE config SET confvalue = '". DB_escape_string($_POST['X_vtiger_integration'])."' WHERE confname = 'vtiger_integration'";
		}
		if ($_SESSION['ProhibitPostingsBefore'] != $_POST['X_ProhibitPostingsBefore'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['X_ProhibitPostingsBefore']."' WHERE confname = 'ProhibitPostingsBefore'";
		}
		if ($_SESSION['WeightedAverageCosting'] != $_POST['X_WeightedAverageCosting'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['X_WeightedAverageCosting']."' WHERE confname = 'WeightedAverageCosting'";
		}
		if (isset($_POST['x_CashBase'])&&$_SESSION['CashBase'] != $_POST['x_CashBase'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_CashBase']."' WHERE confname = 'CashBase'";
		}
		if ($_SESSION['PayInCounter'] != $_POST['x_PayInCounter'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_PayInCounter']."' WHERE confname = 'PayInCounter'";
		}
		if ($_SESSION['PriceHistory'] != $_POST['x_PriceHistory'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_PriceHistory']."' WHERE confname = 'PriceHistory'";
		}
		if ($_SESSION['CostHistory'] != $_POST['x_CostHistory'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_CostHistory']."' WHERE confname = 'CostHistory'";
		}
		if ($_SESSION['GroupItems'] != $_POST['x_GroupItems'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_GroupItems']."' WHERE confname = 'GroupItems'";
		}
		if ($_SESSION['AutoIssue'] != $_POST['X_AutoIssue']){
			$sql[] = 'UPDATE config SET confvalue=' . $_POST['X_AutoIssue'] . " WHERE confname='AutoIssue'";
		}
		if ($_SESSION['ProhibitNegativeStock'] != $_POST['X_ProhibitNegativeStock']){
			$sql[] = 'UPDATE config SET confvalue=' . $_POST['X_ProhibitNegativeStock'] . " WHERE confname='ProhibitNegativeStock'";
		}
		if ($_SESSION['MonthsAuditTrail'] != $_POST['X_MonthsAuditTrail']){
			$sql[] = 'UPDATE config SET confvalue=' . $_POST['X_MonthsAuditTrail'] . " WHERE confname='MonthsAuditTrail'";
		}
		if ($_SESSION['UpdateCurrencyRatesDaily'] != $_POST['X_UpdateCurrencyRatesDaily']){
			if ($_POST['X_UpdateCurrencyRatesDaily']=='Auto'){
				$sql[] = "UPDATE config SET confvalue='" . Date('Y-m-d',mktime(0,0,0,Date('m'),Date('d')-1,Date('Y'))) . "' WHERE confname='UpdateCurrencyRatesDaily'";
			} else {
				$sql[] = "UPDATE config SET confvalue='0' WHERE confname='UpdateCurrencyRatesDaily'";
			}
		}

        if($_SESSION['SalesmanInquiry'] != $_POST['x_SalesmanInquiry']){
          $result = DB_query("select * from config WHERE confname = 'SalesmanInquiry'",$db);
          if(DB_num_rows($result)>0){
            //echo "UPDATE config SET confvalue = '" . $_POST['x_SalesmanInquiry']."' WHERE confname = 'SalesmanInquiry'";
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_SalesmanInquiry']."' WHERE confname = 'SalesmanInquiry'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_SalesmanInquiry']."','SalesmanInquiry');";
          }
        }
        
		if( isset($_POST['x_Dias'])&& $_SESSION['DiasCancel'] != $_POST['x_Dias']){
          $result = DB_query("select * from config WHERE confname = 'DiasCancel'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Dias']."' WHERE confname = 'DiasCancel'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_Dias']."','DiasCancel');";
          }
        }

        if($_SESSION['Gamma'] != $_POST['x_Gamma']){
          $result = DB_query("select * from config WHERE confname = 'Gamma'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Gamma']."' WHERE confname = 'Gamma'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_Gamma']."','Gamma');";
          }
        }
        if($_SESSION['Especie'] != $_POST['x_Especie']){
          $result = DB_query("select * from config WHERE confname = 'Especie'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Especie']."' WHERE confname = 'Especie'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_Especie']."','Especie');";
          }
        }
		//echo "<br>".$_SESSION['GammaName']."<br>";
        //echo "<br>".$_POST['x_GammaName']."<br>";
        if($_SESSION['GammaName'] != $_POST['x_GammaName']){
            //echo "<br>ÑOOOOOOOOOOOOOOOOOO<br>";
          $result = DB_query("select * from config WHERE confname = 'GammaName'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_GammaName']."' WHERE confname = 'GammaName'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_GammaName']."','GammaName');";
          }
        }
        if($_SESSION['EspecieName'] != $_POST['x_EspecieName']){
          $result = DB_query("select * from config WHERE confname = 'EspecieName'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_EspecieName']."' WHERE confname = 'EspecieName'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_EspecieName']."','EspecieName');";
          }
        }
        if($_SESSION['TCambio'] != $_POST['x_TCambio']){
          $result = DB_query("select * from config WHERE confname = 'TCambio'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_TCambio']."' WHERE confname = 'TCambio'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_TCambio']."','TCambio');";
          }
        }

        if($_SESSION['TCambio'] != $_POST['x_TCambio']){
          $result = DB_query("select * from config WHERE confname = 'TCambio'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_TCambio']."' WHERE confname = 'TCambio'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_TCambio']."','TCambio');";
          }
        }

        if($_SESSION['SuppAuto'] != $_POST['x_SuppAuto']){
          $result = DB_query("select * from config WHERE confname = 'SuppAuto'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_SuppAuto']."' WHERE confname = 'SuppAuto'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_SuppAuto']."','SuppAuto');";
          }
        }

        if($_SESSION['Rutas'] != $_POST['x_Rutas']){
          $result = DB_query("select * from config WHERE confname = 'Rutas'",$db);
          if(DB_num_rows($result)>0){
            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Rutas']."' WHERE confname = 'Rutas'";
          }else{
            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_Rutas']."','Rutas');";
          }
        }
        //******************************Bandera de Version de CFDI************************************************************
        if($_SESSION['UserID']=='realhost'){
        	
        	if(isset($_POST['x_Permisos'])&&$_SESSION['Permisos'] != $_POST['x_Permisos']){
        		$result = DB_query("select * from config WHERE confname = 'Permisos'",$db);
        		if(DB_num_rows($result)>0){
        			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Permisos']."' WHERE confname = 'Permisos'";
        		}else{
        			$sql[] = "insert into config (confvalue, confname) values('".$_POST['x_Permisos']."','Permisos');";
        		}
        	}
        	
        	if(isset($_POST['x_StorageBins'])&&$_SESSION['StorageBins'] != $_POST['x_StorageBins']){
        		$result = DB_query("select * from config WHERE confname = 'StorageBins'",$db);
        		if(DB_num_rows($result)>0){
        			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_Permisos']."' WHERE confname = 'StorageBins'";
        		}else{
        			$sql[] = "insert into config (confvalue, confname) values('".$_POST['x_StorageBins']."','StorageBins');";
        		}
        	}
        	
        	
        	if(isset($_POST['x_AllowShowPriceList'])&&$_SESSION['AllowShowPriceList'] != $_POST['x_AllowShowPriceList']){
        		$result = DB_query("select * from config WHERE confname = 'CFDIProduccion'",$db);
        		if(DB_num_rows($result)>0){
        			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_AllowShowPriceList']."' WHERE confname = 'AllowShowPriceList'";
        		}else{
        			$sql[] = "insert into config (confvalue, confname) values('".$_POST['x_AllowShowPriceList']."','AllowShowPriceList');";
        		}
        	}
	        if(isset($_POST['x_CFDIVersion'])&&$_SESSION['CFDIVersion'] != $_POST['x_CFDIVersion']){
	          $result = DB_query("select * from config WHERE confname = 'CFDIVersion'",$db);
	          if(DB_num_rows($result)>0){
	            //echo "UPDATE config SET confvalue = '" . $_POST['x_SalesmanInquiry']."' WHERE confname = 'SalesmanInquiry'";
	            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_CFDIVersion']."' WHERE confname = 'CFDIVersion'";
	          }else{
	            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_CFDIVersion']."','CFDIVersion');";
	          }
	        }
	
	        if(isset($_POST['x_CFDIProduccion'])&&$_SESSION['CFDIProduccion'] != $_POST['x_CFDIProduccion']){
	          $result = DB_query("select * from config WHERE confname = 'CFDIProduccion'",$db);
	          if(DB_num_rows($result)>0){
	            //echo "UPDATE config SET confvalue = '" . $_POST['x_SalesmanInquiry']."' WHERE confname = 'SalesmanInquiry'";
	            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_CFDIProduccion']."' WHERE confname = 'CFDIProduccion'";
	          }else{
	            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_CFDIProduccion']."','CFDIProduccion');";
	          }
	        }

	        if(isset($_POST['x_ExtraModule'])&&$_SESSION['ExtraModule'] != $_POST['x_ExtraModule']){
	          $result = DB_query("select * from config WHERE confname = 'ExtraModule'",$db);
	          if(DB_num_rows($result)>0){
	            //echo "UPDATE config SET confvalue = '" . $_POST['x_SalesmanInquiry']."' WHERE confname = 'SalesmanInquiry'";
	            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_ExtraModule']."' WHERE confname = 'ExtraModule'";
	          }else{
	            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_ExtraModule']."','ExtraModule');";
	          }
	        }

	        if(isset($_POST['x_OServicesMod'])&&$_SESSION['OServicesMod'] != $_POST['x_OServicesMod']){
	          $result = DB_query("select * from config WHERE confname = 'OServicesMod'",$db);
	          if(DB_num_rows($result)>0){
	            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_OServicesMod']."' WHERE confname = 'OServicesMod'";
	          }else{
	            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_OServicesMod']."','OServicesMod');";
	          }
	        }

	        if(isset($_POST['x_DocType'])&&$_SESSION['DocType'] != $_POST['x_DocType']){
	          $result = DB_query("select * from config WHERE confname = 'DocType'",$db);
	          if(DB_num_rows($result)>0){
	            $sql[] = "UPDATE config SET confvalue = '" . $_POST['x_DocType']."' WHERE confname = 'DocType'";
	          }else{
	            $sql[] = "insert into config (confvalue, confname) values('".$_POST['x_DocType']."','DocType');";
	          }
	        }


        }
        //*********************************************************************************************************************

		if ($_SESSION['AllowAllocate'] != $_POST['x_AllowAllocate'] ) {
			$sql[] = "UPDATE config SET confvalue = '" . $_POST['x_AllowAllocate']."' WHERE confname = 'AllowAllocate'";
		}
                //Jaime, agregado campos para la conf del correo
                $sql[] = "UPDATE config SET confvalue = '" . mysql_real_escape_string($_POST['rh_mail__host']) . "' WHERE confname = 'rh_mail__host'";
                if($_POST['rh_mail__password']!='')
                    $sql[] = "UPDATE config SET confvalue = '" . mysql_real_escape_string($_POST['rh_mail__password']) . "' WHERE confname = 'rh_mail__password'";
                $sql[] = "UPDATE config SET confvalue = '" . mysql_real_escape_string($_POST['rh_mail__port']) . "' WHERE confname = 'rh_mail__port'";
                $sql[] = "UPDATE config SET confvalue = '" . mysql_real_escape_string($_POST['rh_mail__username']) . "' WHERE confname = 'rh_mail__username'";
                //\Jaime, agregado campos para la conf del correo
		$ErrMsg =  _('The system configuration could not be updated because');
		if (sizeof($sql) > 1 ) {
			$result = DB_query('BEGIN',$db,$ErrMsg);
			foreach ($sql as $line) {
				$result = DB_query($line,$db,$ErrMsg);
			}
			$result = DB_query('COMMIT',$db,$ErrMsg);
		} elseif(sizeof($sql)==1) {
			$result = DB_query($sql,$db,$ErrMsg);
		}

		prnMsg( _('System configuration updated'),'success');

		$ForceConfigReload = True; // Required to force a load even if stored in the session vars
		include('includes/GetConfig.php');
		$ForceConfigReload = False;
	} else {
		prnMsg( _('Validation failed') . ', ' . _('no updates or deletes took place'),'warn');
	}



} /* end of if submit */



echo '<FORM METHOD="post" action=' . $_SERVER['PHP_SELF'] . '>';
echo '<CENTER><TABLE BORDER=1>';

$TableHeader = '<TR><TH>' . _('System Variable Name') . '</TH>
	<TH>' . _('Value') . '</TH>
	<TH>' . _('Notes') . '</TH>';

echo '<TR><TH COLSPAN=3><CENTER>' . _('General Settings') . '</CENTER></TH></TR>';
echo $TableHeader;

// DefaultDateFormat
echo '<TR><TD>' . _('DefaultDateFormat') . ' (' . _('for input and to appear on reports') . '):</TD>
	<TD><SELECT Name="X_DefaultDateFormat">
	<OPTION '.(($_SESSION['DefaultDateFormat']=='d/m/Y')?'SELECTED ':'').'Value="d/m/Y">d/m/Y
	<OPTION '.(($_SESSION['DefaultDateFormat']=='m/d/Y')?'SELECTED ':'').'Value="m/d/Y">m/d/Y
	<OPTION '.(($_SESSION['DefaultDateFormat']=='Y/m/d')?'SELECTED ':'').'Value="Y/m/d">Y/m/d
	</SELECT></TD>
	<TD>' . _('The default date format for entry of dates and display use d/m/Y for England/Australia/NZ or m/d/Y for US and Canada') . '</TD></TR>';

// DefaultTheme
echo '<TR><TD>' . _('New Users Default Theme') . ':</TD>
	 <TD><SELECT Name="X_DefaultTheme">';
$ThemeDirectory = dir('css/');
while (false != ($ThemeName = $ThemeDirectory->read())){
	if (is_dir("css/$ThemeName") AND $ThemeName != '.' AND $ThemeName != '..' AND $ThemeName != 'CVS'){
		if ($_SESSION['DefaultTheme'] == $ThemeName)
		echo "<OPTION SELECTED VALUE='$ThemeName'>$ThemeName";
		else
		echo "<OPTION VALUE='$ThemeName'>$ThemeName";
	}
}
echo '</SELECT></TD>
	<TD>' . _('The default theme is used for new users who have not yet defined the display colour scheme theme of their choice') . '</TD></TR>';

echo '<TR><TH COLSPAN=3><CENTER>' . _('Accounts Receivable/Payable Settings') . '</CENTER></TH></TR>';

// PastDueDays1
echo '<TR><TD>' . _('First Overdue Deadline in (days)') . ':</TD>
	<TD><input type="Text" Name="X_PastDueDays1" value="' . $_SESSION['PastDueDays1'] . '" SIZE=3 MAXLENGTH=3></TD>
	<TD>' . _('Customer and supplier balances are displayed as overdue by this many days. This parameter is used on customer and supplier enquiry screens and aged listings') . '</TD></TR>';

// PastDueDays2
echo '<TR><TD>' . _('Second Overdue Deadline in (days)') . ':</TD>
	<TD><input type="Text" Name="X_PastDueDays2" value="' . $_SESSION['PastDueDays2'] . '" SIZE=3 MAXLENGTH=3></TD>
	<TD>' . _('As above but the next level of overdue') . '</TD></TR>';


// DefaultCreditLimit
echo '<TR><TD>' . _('Default Credit Limit') . ':</TD>
	<TD><input type="Text" Name="X_DefaultCreditLimit" value="' . $_SESSION['DefaultCreditLimit'] . '" SIZE=6 MAXLENGTH=12></TD>
	<TD>' . _('The default used in new customer set up') . '</TD></TR>';

// Check Credit Limits
echo '<TR><TD>' . _('Check Credit Limits') . ':</TD>
	<TD><SELECT Name="X_CheckCreditLimits">
	<OPTION '.($_SESSION['CheckCreditLimits']==0?'SELECTED ':'').'VALUE="0">'._('Do not check').'
	<OPTION '.($_SESSION['CheckCreditLimits']==1?'SELECTED ':'').'VALUE="1">'._('Warn on breach').'
	<OPTION '.($_SESSION['CheckCreditLimits']==2?'SELECTED ':'').'VALUE="2">'._('Prohibit Sales').'
	</SELECT></TD>
	<TD>' . _('Credit limits can be checked at order entry to warn only or to stop the order from being entered where it would take a customer account balance over their limit') . '</TD></TR>';

// Show_Settled_LastMonth
echo '<TR><TD>' . _('Show Settled Last Month') . ':</TD>
	<TD><SELECT Name="X_Show_Settled_LastMonth">
	<OPTION '.($_SESSION['Show_Settled_LastMonth']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['Show_Settled_LastMonth']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('This setting refers to the format of customer statements. If the invoices and credit notes that have been paid and settled during the course of the current month should be shown then select Yes. Selecting No will only show currently outstanding invoices, credits and payments that have not been allocated') . '</TD></TR>';

//RomalpaClause
echo '<TR><TD>' . _('Romalpa Clause') . ':</TD>
	<TD><textarea Name="X_RomalpaClause" rows=3 cols=40>' . htmlentities($_SESSION['RomalpaClause']) . '</textarea></TD>
	<TD>' . _('This text appears on invoices and credit notes in small print. Normally a reservation of title clause that gives the company rights to collect goods which have not been paid for - to give some protection for bad debts.') . '</TD></TR>';

// QuickEntries
echo '<TR><TD>' . _('Quick Entries') . ':</TD>
	<TD><input type="Text" Name="X_QuickEntries" value="' . $_SESSION['QuickEntries'] . '" SIZE=3 MAXLENGTH=2></TD>
	<TD>' . _('This parameter defines the layout of the sales order entry screen. The number of fields available for quick entries. Any number from 1 to 99 can be entered.') . '</TD></TR>';

//'AllowOrderLineItemNarrative'
echo '<TR><TD>' . _('Order Entry allows Line Item Narrative') . ':</TD>
	<TD><SELECT Name="X_AllowOrderLineItemNarrative">
	<OPTION '.($_SESSION['AllowOrderLineItemNarrative']=='1'?'SELECTED ':'').'VALUE="1">'._('Allow Narrative Entry').'
	<OPTION '.($_SESSION['AllowOrderLineItemNarrative']=='0'?'SELECTED ':'').'VALUE="0">'._('No Narrative Line').'
	</SELECT></TD>
	<TD>' . _('Select whether or not to allow entry of narrative on order line items. This narrative will appear on invoices and packing slips. Useful mainly for service businesses.') . '</TD>
	</TR>';
//UpdateCurrencyRatesDaily
echo '<TR><TD>' . _('Auto Update Exchange Rates Daily') . ':</TD>
	<TD><SELECT Name="X_UpdateCurrencyRatesDaily">
	<OPTION '.($_SESSION['UpdateCurrencyRatesDaily']!='0'?'SELECTED ':'').'VALUE="Auto">'._('Automatic').'
	<OPTION '.($_SESSION['UpdateCurrencyRatesDaily']=='0'?'SELECTED ':'').'VALUE="0">'._('Manual').'
	</SELECT></TD>
	<TD>' . _('Automatic updates to exchange rates will retrieve the latest daily rates from the European Central Bank once per day - when the first user logs in for the day. Manual will never update the rates automatically - exchange rates will need to be maintained manually') . '</TD>
	</TR>';

//Default Packing Note Format
echo '<TR><TD>' . _('Format of Packing Slips') . ':</TD>
	<TD><SELECT Name="X_PackNoteFormat">
	<OPTION '.($_SESSION['PackNoteFormat']=='1'?'SELECTED ':'').'VALUE="1">'._('Laser Printed').'
	<OPTION '.($_SESSION['PackNoteFormat']=='2'?'SELECTED ':'').'VALUE="2">'._('Special Stationery').'
	</SELECT></TD>
	<TD>' . _('Choose the format that packing notes should be printed by default') . '</TD>
	</TR>';

//Default Invoice Format
echo '<TR><TD>' . _('Invoice Orientation') . ':</TD>
	<TD><SELECT Name="X_InvoicePortraitFormat">
	<OPTION '.($_SESSION['InvoicePortraitFormat']=='0'?'SELECTED ':'').'VALUE="0">'._('Landscape').'
	<OPTION '.($_SESSION['InvoicePortraitFormat']=='1'?'SELECTED ':'').'VALUE="1">'._('Portrait').'
	</SELECT></TD>
	<TD>' . _('Select the invoice layout') . '</TD>
	</TR>';

//Blind packing note
echo '<TR><TD>' . _('Show company details on packing slips') . ':</TD>
	<TD><SELECT Name="X_DefaultBlindPackNote">
	<OPTION '.($_SESSION['DefaultBlindPackNote']=="1"?'SELECTED ':'').'VALUE="1">'._('Show Company Details').'
	<OPTION '.($_SESSION['DefaultBlindPackNote']=="2"?'SELECTED ':'').'VALUE="2">'._('Hide Company Details').'
	</SELECT></TD>
	<TD>' . _('Customer branches can be set by default not to print packing slips with the company logo and address. This is useful for companies that ship to customers customers and to show the source of the shipment would be inappropriate. There is an option on the setup of customer branches to ship blind, this setting is the default applied to all new customer branches') . '</TD>
	</TR>';


// DispatchCutOffTime
echo '<TR><TD>' . _('Dispatch Cut-Off Time') . ':</TD>
	<TD><SELECT Name="X_DispatchCutOffTime">';
for ($i=0; $i < 24; $i++ )
echo '<OPTION '.($_SESSION['DispatchCutOffTime'] == $i?'SELECTED ':'').'VALUE="'.$i.'">'.$i;
echo '</SELECT></TD>
	<TD>' . _('Orders entered after this time will default to be dispatched the following day, this can be over-ridden at the time of sales order entry') . '</TD></TR>';

// AllowSalesOfZeroCostItems
echo '<TR><TD>' . _('Allow Sales Of Zero Cost Items') . ':</TD>
	<TD><SELECT Name="X_AllowSalesOfZeroCostItems">
	<OPTION '.($_SESSION['AllowSalesOfZeroCostItems']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['AllowSalesOfZeroCostItems']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('If an item selected at order entry does not have a cost set up then if this parameter is set to No then the order line will not be able to be entered') . '</TD></TR>';

// CreditingControlledItems_MustExist
echo '<TR><TD>' . _('Controlled Items Must Exist For Crediting') . ':</TD>
	<TD><SELECT Name="X_CreditingControlledItems_MustExist">
	<OPTION '.($_SESSION['CreditingControlledItems_MustExist']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['CreditingControlledItems_MustExist']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('This parameter relates to the behaviour of the controlled items code. If a serial numbered item has not previously existed then a credit note for it will not be allowed if this is set to Yes') . '</TD></TR>';

// DefaultPriceList
$sql = 'SELECT typeabbrev, sales_type FROM salestypes ORDER BY sales_type';
$ErrMsg = _('Could not load price lists');
$result = DB_query($sql,$db,$ErrMsg);
echo '<TR><TD>' . _('Default Price List') . ':</TD>';
echo '<TD><SELECT Name="X_DefaultPriceList">';
if( DB_num_rows($result) == 0 ) {
	echo '<OPTION SELECTED VALUE="">'._('Unavailable');
} else {
	while( $row = DB_fetch_array($result) ) {
		echo '<OPTION '.($_SESSION['DefaultPriceList'] == $row['typeabbrev']?'SELECTED ':'').'VALUE="'.$row['typeabbrev'].'">'.$row['sales_type'];
	}
}
echo '</SELECT></TD>
	<TD>' . _('This price list is used as a last resort where there is no price set up for an item in the price list that the customer is set up for') . '</TD></TR>';

// Default_Shipper
$sql = 'SELECT shipper_id, shippername FROM shippers ORDER BY shippername';
$ErrMsg = _('Could not load shippers');
$result = DB_query($sql,$db,$ErrMsg);
echo '<TR><TD>' . _('Default Shipper') . ':</TD>';
echo '<TD><SELECT Name="X_Default_Shipper">';
if( DB_num_rows($result) == 0 ) {
	echo '<OPTION SELECTED VALUE="">'._('Unavailable');
} else {
	while( $row = DB_fetch_array($result) ) {
		echo '<OPTION '.($_SESSION['Default_Shipper'] == $row['shipper_id']?'SELECTED ':'').'VALUE="'.$row['shipper_id'].'">'.$row['shippername'];
	}
}
echo '</SELECT></TD>
	<TD>' . _('This shipper is used where the best shipper for a customer branch has not been defined previously') . '</TD></TR>';

// DoFreightCalc
echo '<TR><TD>' . _('Do Freight Calculation') . ':</TD>
	<TD><SELECT Name="X_DoFreightCalc">
	<OPTION '.($_SESSION['DoFreightCalc']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['DoFreightCalc']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('If this is set to Yes then the system will attempt to calculate the freight cost of a dispatch based on the weight and cubic and the data defined for each shipper and their rates for shipping to various locations. The results of this calculation will only be meaningful if the data is entered for the item weight and volume in the stock item setup for all items and the freight costs for each shipper properly maintained.') . '</TD></TR>';

//FreightChargeAppliesIfLessThan
echo '<TR><TD>' . _('Apply freight charges if an order is less than') . ':</TD>
	<TD><input type="Text" Name="X_FreightChargeAppliesIfLessThan" SIZE=6 MAXLENGTH=12 value="' . $_SESSION['FreightChargeAppliesIfLessThan'] . '"></TD>
	<TD>' . _('This parameter is only effective if Do Freight Calculation is set to Yes. If it is set to 0 then freight is always charged. The total order value is compared to this value in deciding whether or not to charge freight') .'</TD></TR>';


// AutoDebtorNo
echo '<TR><TD>' . _('Create Debtor Codes Automatically') . ':</TD>
	<TD><SELECT Name="X_AutoDebtorNo">';

if ($_SESSION['AutoDebtorNo']==0) {
	echo '<OPTION SELECTED Value=0>' . _('Manual Entry');
	echo '<OPTION Value=1>' . _('Automatic');
} else {
	echo '<OPTION SELECTED Value=1>' . _('Automatic');
	echo '<OPTION Value=0>' . _('Manual Entry');
}
echo '</SELECT></TD>
	<TD>' . _('Set to Automatic - customer codes are automatically created - as a sequential number') .'</TD></TR>';

//==HJ== drop down list for tax category
$sql = 'SELECT taxcatid, taxcatname FROM taxcategories ORDER BY taxcatname';
$ErrMsg = _('Could not load tax categories table');
$result = DB_query($sql,$db,$ErrMsg);
echo '<TR><TD>' . _('Default Tax Category') . ':</TD>';
echo '<TD><SELECT Name="X_DefaultTaxCategory">';
if( DB_num_rows($result) == 0 ) {
	echo '<OPTION SELECTED VALUE="">'._('Unavailable');
} else {
	while( $row = DB_fetch_array($result) ) {
		echo '<OPTION '.($_SESSION['DefaultTaxCategory'] == $row['taxcatid']?'SELECTED ':'').'VALUE="'.$row['taxcatid'].'">'.$row['taxcatname'];
	}
}
echo '</SELECT></TD>
	<TD>' . _('This is the tax category used for entry of supplier invoices and the category at which freight attracts tax') .'</TD></TR>';


//TaxAuthorityReferenceName
echo '<TR><TD>' . _('TaxAuthorityReferenceName') . ':</TD>
	<TD><input type="Text" Name="X_TaxAuthorityReferenceName" SIZE=16 MAXLENGTH=25 value="' . $_SESSION['TaxAuthorityReferenceName'] . '"></TD>
	<TD>' . _('This parameter is what is displayed on tax invoices and credits for the tax authority of the company eg. in Australian this would by A.B.N.: - in NZ it would be GST No: in the UK it would be VAT Regn. No') .'</TD></TR>';

// CountryOfOperation
$sql = 'SELECT currabrev, country FROM currencies ORDER BY country';
$ErrMsg = 'Could not load the countries from the currency table';
$result = DB_query($sql,$db,$ErrMsg);
echo '<TR><TD>' . _('Country Of Operation') . ':</TD>';
echo '<TD><SELECT Name="X_CountryOfOperation">';
if( DB_num_rows($result) == 0 ) {
	echo '<OPTION SELECTED VALUE="">'._('Unavailable');
} else {
	while( $row = DB_fetch_array($result) ) {
		echo '<OPTION '.($_SESSION['CountryOfOperation'] == $row['currabrev']?'SELECTED ':'').'VALUE="'.$row['currabrev'].'">'.$row['country'];
	}
}
echo '</SELECT></TD>
	<TD>' . _('This parameter is only effective if Do Freight Calculation is set to Yes. Country names come from the currencies table.') .'</TD></TR>';

// NumberOfPeriodsOfStockUsage
echo '<TR><TD>' . _('Number Of Periods Of StockUsage') . ':</TD>
	<TD><SELECT Name="X_NumberOfPeriodsOfStockUsage">';
for ($i=1; $i <= 12; $i++ )
echo '<OPTION '.($_SESSION['NumberOfPeriodsOfStockUsage'] == $i?'SELECTED ':'').'VALUE="'.$i.'">'.$i;
echo '</SELECT></TD><TD>' . _('In stock usage inquiries this determines how many periods of stock usage to show. An average is calculated over this many periods') .'</TD></TR>';

// Check_Qty_Charged_vs_Del_Qty
echo '<TR><TD>' . _('Check Quantity Charged vs Deliver Qty') . ':</TD>
	<TD><SELECT Name="X_Check_Qty_Charged_vs_Del_Qty">
	<OPTION '.($_SESSION['Check_Qty_Charged_vs_Del_Qty']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['Check_Qty_Charged_vs_Del_Qty']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('In entry of AP invoices this determines whether or not to check the quantites received into stock tie up with the quantities invoiced') .'</TD></TR>';

// Check_Price_Charged_vs_Order_Price
echo '<TR><TD>' . _('Check Price Charged vs Order Price') . ':</TD>
	<TD><SELECT Name="X_Check_Price_Charged_vs_Order_Price">
	<OPTION '.($_SESSION['Check_Price_Charged_vs_Order_Price']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['Check_Price_Charged_vs_Order_Price']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('In entry of AP invoices this parameter determines whether or not to check invoice prices tie up to ordered prices') .'</TD></TR>';

// OverChargeProportion
echo '<TR><TD>' . _('Allowed Over Charge Proportion') . ':</TD>
	<TD><input type="Text" Name="X_OverChargeProportion" SIZE=4 MAXLENGTH=3 value="' . $_SESSION['OverChargeProportion'] . '"></TD>
	<TD>' . _('If check price charges vs Order price is set to yes then this proportion determines the percentage by which invoices can be overcharged with respect to price') .'</TD></TR>';

// OverReceiveProportion
echo '<TR><TD>' . _('Allowed Over Receive Proportion') . ':</TD>
	<TD><input type="Text" Name="X_OverReceiveProportion" SIZE=4 MAXLENGTH=3 value="' . $_SESSION['OverReceiveProportion'] . '"></TD>
	<TD>' . _('If check quantity charged vs delivery quantity is set to yes then this proportion determines the percentage by which invoices can be overcharged with respect to delivery') .'</TD></TR>';

// PO_AllowSameItemMultipleTimes
echo '<TR><TD>' . _('Purchase Order Allows Same Item Multiple Times') . ':</TD>
	<TD><SELECT Name="X_PO_AllowSameItemMultipleTimes">
	<OPTION '.($_SESSION['PO_AllowSameItemMultipleTimes']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['PO_AllowSameItemMultipleTimes']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>&nbsp;<TD></TD></TR>';

// SO_AllowSameItemMultipleTimes
echo '<TR><TD>' . _('Sales Order Allows Same Item Multiple Times') . ':</TD>
	<TD><SELECT Name="X_SO_AllowSameItemMultipleTimes">
	<OPTION '.($_SESSION['SO_AllowSameItemMultipleTimes']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['SO_AllowSameItemMultipleTimes']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD><TD>&nbsp;</TD></TR>';

echo '<TR><TH COLSPAN=3><CENTER>' . _('General Settings') . '</CENTER></TH></TR>';
echo $TableHeader;

// YearEnd
$MonthNames = array( 1=>_('January'),
			2=>_('February'),
			3=>_('March'),
			4=>_('April'),
			5=>_('May'),
			6=>_('June'),
			7=>_('July'),
			8=>_('August'),
			9=>_('September'),
			10=>_('October'),
			11=>_('November'),
			12=>_('December') );
echo '<TR><TD>' . _('Financial Year Ends On') . ':</TD>
	<TD><SELECT Name="X_YearEnd">';
for ($i=1; $i <= sizeof($MonthNames); $i++ )
echo '<OPTION '.($_SESSION['YearEnd'] == $i ? 'SELECTED ' : '').'VALUE="'.$i.'">'.$MonthNames[$i];
echo '</SELECT></TD>
	<TD>' . _('Defining the month in which the financial year ends enables the system to provide useful defaults for general ledger reports') .'</TD></TR>';

//PageLength
echo '<TR><TD>' . _('Report Page Length') . ':</TD>
	<TD><input type="Text" Name="X_PageLength" SIZE=4 MAXLENGTH=6 value="' . $_SESSION['PageLength'] . '"></TD><TD>&nbsp;</TD>
</TR>';

//DefaultDisplayRecordsMax
echo '<TR><TD>' . _('Default Maximum Number of Records to Show') . ':</TD>
	<TD><input type="Text" Name="X_DefaultDisplayRecordsMax" SIZE=4 MAXLENGTH=3 value="' . $_SESSION['DefaultDisplayRecordsMax'] . '"></TD>
	<TD>' . _('When pages have code to limit the number of returned records - such as select customer, select supplier and select item, then this will be the default number of records to show for a user who has not changed this for themselves in user settings.') . '</TD>
	</TR>';

//MaxImageSize
echo '<TR><TD>' . _('Maximum Size in KB of uploaded images') . ':</TD>
	<TD><input type="Text" Name="X_MaxImageSize" SIZE=4 MAXLENGTH=3 value="' . $_SESSION['MaxImageSize'] . '"></TD>
	<TD>' . _('Picture files of items can be uploaded to the server. The system will check that files uploaded are less than this size (in KB) before they will be allowed to be uploaded. Large pictures will make the system slow and will be difficult to view in the stock maintenance screen.') .'</TD>
</TR>';

//$part_pics_dir
echo '<TR><TD>' . _('The directory where images are stored') . ':</TD>
	<TD><SELECT NAME="X_part_pics_dir">';

$CompanyDirectory = 'companies/' . $_SESSION['DatabaseName'] . '/';
$DirHandle = dir($CompanyDirectory);

while ($DirEntry = $DirHandle->read() ){

	if (is_dir($CompanyDirectory . $DirEntry)
	AND $DirEntry != '..'
	AND $DirEntry!='.'
	AND $DirEntry != 'CVS'
	AND $DirEntry != 'reports'
	AND $DirEntry != 'locale'
	AND $DirEntry != 'fonts'   ){

		if ($_SESSION['part_pics_dir'] == $CompanyDirectory . $DirEntry){
			echo "<OPTION SELECTED VALUE='$DirEntry'>$DirEntry";
		} else {
			echo "<OPTION VALUE='$DirEntry'>$DirEntry";
		}
	}
}
echo '</SELECT></TD>
	<TD>' . _('The directory under which all image files should be stored. Image files take the format of ItemCode.jpg - they must all be .jpg files and the part code will be the name of the image file. This is named automatically on upload. The system will check to ensure that the image is a .jpg file') . '</TD>
	</TR>';


//$reports_dir
echo '<TR><TD>' . _('The directory where reports are stored') . ':</TD>
	<TD><SELECT NAME="X_reports_dir">';

$DirHandle = dir($CompanyDirectory);

while (false != ($DirEntry = $DirHandle->read())){

	if (is_dir($CompanyDirectory . $DirEntry)
	AND $DirEntry != '..'
	AND $DirEntry != 'includes'
	AND $DirEntry!='.'
	AND $DirEntry != 'doc'
	AND $DirEntry != 'css'
	AND $DirEntry != 'CVS'
	AND $DirEntry != 'sql'
	AND $DirEntry != 'part_pics'
	AND $DirEntry != 'locale'
	AND $DirEntry != 'fonts'      ){

		if ($_SESSION['reports_dir'] == $CompanyDirectory . $DirEntry){
			echo "<OPTION SELECTED VALUE='$DirEntry'>$DirEntry";
		} else {
			echo "<OPTION VALUE='$DirEntry'>$DirEntry";
		}
	}
}

echo '</SELECT></TD>
	<TD>' . _('The directory under which all report pdf files should be created in. A separate directory is recommended') . '</TD>
	</TR>';


// HTTPS_Only
echo '<TR><TD>' . _('Only allow secure socket connections') . ':</TD>
	<TD><SELECT Name="X_HTTPS_Only">
	<OPTION '.($_SESSION['HTTPS_Only']?'SELECTED ':'').'VALUE="1">'._('Yes').'
	<OPTION '.(!$_SESSION['HTTPS_Only']?'SELECTED ':'').'VALUE="0">'._('No').'
	</SELECT></TD>
	<TD>' . _('Force connections to be only over secure sockets - ie encrypted data only') . '</TD>
	</TR>';

/*Perform Database maintenance DB_Maintenance*/
echo '<TR><TD>' . _('Perform Database Maintenance At Logon') . ':</TD>
	<TD><SELECT Name="X_DB_Maintenance">';
if ($_SESSION['DB_Maintenance']=='1'){
	echo '<OPTION SELECTED VALUE="1">'._('Daily');
} else {
	echo '<OPTION VALUE="1">'._('Daily');
}
if ($_SESSION['DB_Maintenance']=='7'){
	echo '<OPTION SELECTED VALUE="7">'._('Weekly');
} else {
	echo '<OPTION VALUE="7">'._('Weekly');
}
if ($_SESSION['DB_Maintenance']=='30'){
	echo '<OPTION SELECTED VALUE="30">'._('Monthly');
} else {
	echo '<OPTION VALUE="30">'._('Monthly');
}
if ($_SESSION['DB_Maintenance']=='0'){
	echo '<OPTION SELECTED VALUE="0">'._('Never');
} else {
	echo '<OPTION VALUE="0">'._('Never');
}

echo '</SELECT></TD>
	<TD>' . _('Uses the function DB_Maintenance defined in ConnectDB_XXXX.inc to perform database maintenance tasks, to run at regular intervals - checked at each and every user login') . '</TD>
	</TR>';

$WikiApplications = array( _('Disabled'),
_('WackoWiki'),
_('MediaWiki') );

echo '<TR><TD>' . _('Wiki application') . ':</TD>
	<TD><SELECT Name="X_WikiApp">';
for ($i=0; $i < sizeof($WikiApplications); $i++ ) {
	echo '<OPTION '.($_SESSION['WikiApp'] == $WikiApplications[$i] ? 'SELECTED ' : '').'VALUE="'.$WikiApplications[$i].'">'.$WikiApplications[$i];
}
echo '</SELECT></TD>
	<TD>' . _('This feature makes webERP show links to a free form company knowlege base using a wiki. This allows sharing of important company information - about customers, suppliers and products and the set up of work flow menus and/or company procedures documentation') .'</TD></TR>';

echo '<TR><TD>' . _('Wiki Path') . ':</TD>
	<TD><input type="Text" Name="X_WikiPath" SIZE=40 MAXLENGTH=40 value="' . $_SESSION['WikiPath'] . '"></TD>
	<TD>' . _('The path to the wiki installation to form the basis of wiki URLs - this should be the directory on the web-server where the wiki is installed. The wiki must be installed on the same web-server as webERP') .'</TD></TR>';

/*

Not implemented ... yet - any offers?

echo '<TR><TD>' . _('vtiger Integration:') . ':</TD>
<TD><SELECT Name="X_vtiger_integration">';
echo '<OPTION ' . ($_SESSION['vtiger_integration'] == '0' ? 'SELECTED ' : '') . 'VALUE="0">' . _('No Integration');
echo '<OPTION '.($_SESSION['vtiger_integration'] == '1' ? 'SELECTED ' : '').'VALUE="1">' . _('Integration Enabled');

echo '</SELECT></TD>
<TD>' . _('This feature makes webERP create entries in vtiger tables in the same database as webERP to allow an instance of vtiger to be integrated with webERP data') .'</TD></TR>';
*/


echo '<TR><TD>' . _('Prohibit GL Journals to Control Accounts') . ':</TD>
	<TD><SELECT Name="X_ProhibitJournalsToControlAccounts">';
if ($_SESSION['ProhibitJournalsToControlAccounts']=='1'){
	echo  '<OPTION SELECTED value="1">' . _('Prohibited');
	echo  '<OPTION value="0">' . _('Allowed');
} else {
	echo  '<OPTION value="1">' . _('Prohibited');
	echo  '<OPTION SELECTED value="0">' . _('Allowed');
}
echo '</SELECT></TD><TD>' . _('Setting this to prohibited prevents accidentally entering a journal to the automatically posted and reconciled control accounts for creditors (AP) and debtors (AR)') . '</TD></TR>';


echo '<TR><TD>' . _('Prohibit GL Journals to Periods Prior To') . ':</TD>
	<TD><SELECT Name="X_ProhibitPostingsBefore">';

$sql = 'SELECT lastdate_in_period FROM periods ORDER BY periodno DESC';
$ErrMsg = _('Could not load periods table');
$result = DB_query($sql,$db,$ErrMsg);
while ($PeriodRow = DB_fetch_row($result)){
	if ($_SESSION['ProhibitPostingsBefore']==$PeriodRow[0]){
		echo  '<OPTION SELECTED value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[0]);
	} else {
		echo  '<OPTION value="' . $PeriodRow[0] . '">' . ConvertSQLDate($PeriodRow[0]);
	}
}
echo '</SELECT></TD><TD>' . _('This allows all periods before the selected date to be locked from postings. All postings for transactions dated prior to this date will be posted in the period following this date.') . '</TD></TR>';

echo '<TR><TD>' . _('Inventory Costing Method') . ':</TD>
	<TD><SELECT Name="X_WeightedAverageCosting">';

if ($_SESSION['WeightedAverageCosting']==1){
	echo  '<OPTION SELECTED value="1">' . _('Weighted Average Costing');
	echo  '<OPTION value="0">' . _('Standard Costing');
} else {
	echo  '<OPTION SELECTED value="0">' . _('Standard Costing');
	echo  '<OPTION value="1">' . _('Weighted Average Costing');
}

echo '</SELECT></TD><TD>' . _('webERP allows inventory to be costed based on the weighted average of items in stock or full standard costing with price variances reported. The selection here determines the method used and the general ledger postings resulting from purchase invoices and shipment closing') . '</TD></TR>';

echo '<TR><TD>' . _('Auto Issue Components') . ':</TD>
		<TD>
		<SELECT name="X_AutoIssue">';
if ($_SESSION['AutoIssue']==0) {
	echo '<OPTION SELECTED VALUE=0>' . _('No');
	echo '<OPTION VALUE=1>' . _('Yes');
} else {
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
}
echo '</SELECT></TD><TD>' . _('When items are manufactured it is possible for the components of the item to be automatically decremented from stock in accordance with the Bill of Material setting') . '</TD></TR>' ;

echo '<TR><TD>' . _('Prohibit Negative Stock') . ':</TD>
		<TD>
		<SELECT name="X_ProhibitNegativeStock">';
if ($_SESSION['ProhibitNegativeStock']==0) {
	echo '<OPTION SELECTED VALUE=0>' . _('No');
	echo '<OPTION VALUE=1>' . _('Yes');
} else {
	echo '<OPTION SELECTED VALUE=1>' . _('Yes');
	echo '<OPTION VALUE=0>' . _('No');
}
echo '</SELECT></TD><TD>' . _('Setting this parameter to Yes prevents invoicing and the issue of stock if this would result in negative stock. The stock problem must be corrected before the invoice or issue is allowed to be processed.') . '</TD></TR>' ;

//Months of Audit Trail to Keep
echo '<TR><TD>' . _('Months of Audit Trail to Retain') . ':</TD>
	<TD><input type="Text" Name="X_MonthsAuditTrail" SIZE=3 MAXLENGTH=2 value="' . $_SESSION['MonthsAuditTrail'] . '"></TD><TD>' . _('If this parameter is set to 0 (zero) then no audit trail is retained. An audit trail is a log of which users performed which additions updates and deletes of database records. The full SQL is retained') . '</TD>
</TR>';

// bowikaxu realhost - modifications
echo '<TR><TD COLSPAN=3 class="tableheader"><CENTER>' . _('Realhost Modifications') . '</CENTER></TD></TR>';

// bowikaxu realhost - may 2007 - cash base
// bowikaxu realhost Feb 2008 - only super users can modify
$auth=0;
foreach($rh_SMAuth as $uid){
	if ($uid == $_SESSION['UserID']){
		$auth=1;
		break;
	}else {

	}
}/*
if($auth){

}else {
	if($_SESSION['CashBase']==1){
		echo "<TD>"._('Cash Base')."</TD>";
	}else{
		echo "<TD>"._('No Cash Base')."</TD>";
	}
} */

echo '<TR><TD>' . _('Dias para cancelar') . ':</TD>';
    echo	'<TD><input  Name="x_Dias" value="'.$_SESSION['DiasCancel'].'" style="width:100%" /></TD>';
    echo '<TD>' . _('Indica el numero de dias en el que se puede cancelar un documento despues del dia ultimo del mes') . '</TD></TR>';
    
if($_SESSION['UserID']=='realhost'){
	echo '<TR><TD>' . _('Cash Base') . ':</TD>';
	echo	'<TD><SELECT Name="x_CashBase">';

	if ($_SESSION['CashBase']==1){
		echo  '<OPTION SELECTED value="1">' . _('Cash Base');
		echo  '<OPTION value="0">' . _('No Cash Base');
	} else {
		echo  '<OPTION SELECTED value="0">' . _('No Cash Base');
		echo  '<OPTION value="1">' . _('Cash Base');
	}
	echo '</SELECT></TD>';
    echo '<TD>' . _('Use Cash Base or Not') . '</TD></TR>';
    

    echo '<TR><TD>' . _('Sub-Familia Adcional') . ':</TD>';
    	echo	'<TD><SELECT Name="x_Gamma">';
	    if ($_SESSION['Gamma']==1){
		        echo  '<OPTION SELECTED value="1">' . _('Si');
		    echo  '<OPTION value="0">' . _('No');
	    } else {
		    echo  '<OPTION SELECTED value="0">' . _('No');
		    echo  '<OPTION value="1">' . _('Si');
	    }
	    echo '</SELECT></TD>';
    echo '<TD>' . _('Usar una Sub-Familia Adicional a los productos') . '</TD></TR>';

    echo '<TR><TD>' . _('Nombre de Sub-Familia Adcional') . ':</TD>';
    	echo	'<TD><input  Name="x_GammaName" value="'.(isset($_SESSION['GammaName'])?$_SESSION['GammaName']:'Gamma').'" style="width:100%" /></TD>';
    echo '<TD>' . _('Nombre a Usar para Sub-Familia Adicional a los productos') . '</TD></TR>';

    echo '<TR><TD>' . _('Sub-Familia Adcional') . ':</TD>';
    	echo	'<TD><SELECT Name="x_Especie">';
	    if ($_SESSION['Especie']==1){
		        echo  '<OPTION SELECTED value="1">' . _('Si');
		    echo  '<OPTION value="0">' . _('No');
	    } else {
		    echo  '<OPTION SELECTED value="0">' . _('No');
		    echo  '<OPTION value="1">' . _('Si');
	    }
	    echo '</SELECT></TD>';
    echo '<TD>' . _('Usar una Sub-Familia Adicional a los productos') . '</TD></TR>';

    echo '<TR><TD>' . _('Nombre de Sub-Familia Adcional') . ':</TD>';
    	echo	'<TD><input  Name="x_EspecieName" value="Especie" style="width:100%" /></TD>';
    echo '<TD>' . _('Nombre a Usar para Sub-Familia Adicional a los productos') . '</TD></TR>';

    echo '<TR><TD>' . _('Rutas') . ':</TD>';
    	echo	'<TD><SELECT Name="x_Rutas">';
	    if ($_SESSION['Rutas']==1){
		        echo  '<OPTION SELECTED value="1">' . _('Si');
		    echo  '<OPTION value="0">' . _('No');
	    } else {
		    echo  '<OPTION SELECTED value="0">' . _('No');
		    echo  '<OPTION value="1">' . _('Si');
	    }
	    echo '</SELECT></TD>';
    echo '<TD>' . _('Activa Apartado de Rutas') . '</TD></TR>';
    
    
    echo '<TR><TD>' . _('Permisos por usuario') . ':</TD>';
    echo	'<TD><SELECT Name="x_Permisos">';
    if ($_SESSION['Permisos']==1){
    	echo  '<OPTION SELECTED value="1">' . _('Si');
    	echo  '<OPTION value="0">' . _('No');
    } else {
    	echo  '<OPTION SELECTED value="0">' . _('No');
    	echo  '<OPTION value="1">' . _('Si');
    }
    echo '</SELECT></TD>';
    echo '<TD>' . _('Activa Apartado de Permisos por script por usuario') . '</TD></TR>';
}


if($_SESSION['UserID']=='realhost'){
echo '<TR><TD>' . _('CFDI Produccion') . ':</TD>
	<TD><SELECT Name="x_CFDIProduccion">';
if ($_SESSION['CFDIProduccion']==1){
	echo  '<OPTION SELECTED value="1">' . _('Si');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION value="1">' . _('Si');
	echo  '<OPTION SELECTED value="0">' . _('No');
}

echo '</SELECT></TD><TD>' . _('Indica si se esta usando Test o produccion en el timbrado.') . '</TD></TR>';

echo '<TR><TD>' . _('CFD/CFDI') . ':</TD>
	<TD><SELECT Name="x_CFDIVersion">';
if ($_SESSION['CFDIVersion']==32){
	echo  '<OPTION SELECTED value="32">' . _('CFDI Versi&oacute;n 3.2');
	echo  '<OPTION value="22">' . _('CFD Versi&oacute;n 2.2');
} else {
	echo  '<OPTION value="32">' . _('CFDI Versi&oacute;n 3.2');
	echo  '<OPTION SELECTED value="22">' . _('CFD Versi&oacute;n 2.2');
}

echo '</SELECT></TD><TD>' . _('Indica la versi&oacute;n de CFD/CFDI a usar.') . '</TD></TR>';

echo '<TR><TD>' . _('Tipo de Documento') . ':</TD>
	<TD><SELECT Name="x_DocType">';
if ($_SESSION['DocType']==1){
	echo  '<OPTION SELECTED value="1">' . _('Facturas con Retenciones');
	echo  '<OPTION value="0">' . _('Factura sin Retenciones');
} else {
	echo  '<OPTION value="1">' .  _('Facturas con Retenciones');
	echo  '<OPTION SELECTED value="0">' . _('Factura sin Retenciones');
}
echo '</SELECT></TD><TD>' . _('Indica como mostrar la fcatura con o sin retenciones, para diversas finalidades fiscales.') . '</TD></TR>';


echo '<TR><TD>' . _('Modulo Extra') . ':</TD>
	<TD><SELECT Name="x_ExtraModule">';
if ($_SESSION['ExtraModule']==1){
	echo  '<OPTION SELECTED value="1">' . _('Activar Modulo Extras');
	echo  '<OPTION value="0">' . _('Desactivar Modulo Extras');
} else {
	echo  '<OPTION value="1">' . _('Activar Modulo Extras');
	echo  '<OPTION SELECTED value="0">' . _('Desactivar Modulo Extras');
}
echo '</SELECT></TD><TD>' . _('Indica si se puede o no usar el modulo Extra') . '</TD></TR>';


echo '<TR><TD>' . _('Modulo Ordenes de Servicio') . ':</TD>
	<TD><SELECT Name="x_OServicesMod">';
if ($_SESSION['OServicesMod']==1){
	echo  '<OPTION SELECTED value="1">' . _('Activar Modulo');
	echo  '<OPTION value="0">' . _('Desactivar Modulo');
} else {
	echo  '<OPTION value="1">' . _('Activar Modulo');
	echo  '<OPTION SELECTED value="0">' . _('Desactivar Modulo');
}
echo '</SELECT></TD><TD>' . _('Indica si se puede o no usar el modulo Ordenes de Servicio') . '</TD></TR>';


echo '<TR><TD>' . _('Listado de precios') . ':</TD>
<TD><SELECT Name="x_AllowShowPriceList">';

if ($_SESSION['AllowShowPriceList']==1){
	echo  '<OPTION SELECTED value="1">' . _('Yes');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Yes');
}
echo '</SELECT></TD><TD>' . _('Indica si se puede o no cambiar los precios en base al listado de precios en el pedido') . '</TD></TR>';




echo '<TR><TD>' . _('Storage Bins') . ':</TD>';
echo	'<TD><SELECT Name="x_StorageBins">';
if ($_SESSION['StorageBins']==1){
	echo  '<OPTION SELECTED value="1">' . _('Si');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Si');
}
echo '</SELECT></TD>';
echo '<TD>' . _('Activa Apartado de Storage bins, locaciones virutales.') . '</TD></TR>';


}

// bowikaxu realhost - may 2007 - Show Pay In Counter or not
echo '<TR><TD>' . _('Pay In Counter') . ':</TD>
	<TD><SELECT Name="x_PayInCounter">';

if ($_SESSION['PayInCounter']==1){
	echo  '<OPTION SELECTED value="1">' . _('Yes');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Yes');
}

echo '</SELECT></TD><TD>' . _('Show the Pay text field in the Point of Sale or Not') . '</TD></TR>';

 echo '<TR><TD>' . _('Automatizar Tipo de Cambio') . ':</TD>';
    	echo	'<TD><SELECT Name="x_TCambio">';
	    if ($_SESSION['TCambio']==1){
		        echo  '<OPTION SELECTED value="1">' . _('Si');
		    echo  '<OPTION value="0">' . _('No');
	    } else {
		    echo  '<OPTION SELECTED value="0">' . _('No');
		    echo  '<OPTION value="1">' . _('Si');
	    }
	    echo '</SELECT></TD>';
    echo '<TD>' . _('Si.- Toma el Tipo de Cambio del DOF. No.- Se introduce manualmente.') . '</TD></TR>';

 echo '<TR><TD>' . _('Automatizar C&oacute;digo de Proveedor') . ':</TD>';
    	echo	'<TD><SELECT Name="x_SuppAuto">';
	    if ($_SESSION['SuppAuto']==1){
		        echo  '<OPTION SELECTED value="1">' . _('Si');
		    echo  '<OPTION value="0">' . _('No');
	    } else {
		    echo  '<OPTION SELECTED value="0">' . _('No');
		    echo  '<OPTION value="1">' . _('Si');
	    }
	    echo '</SELECT></TD>';
    echo '<TD>' . _('Si.- Coloca automatico el SupplierNo. No.- Se introduce manualmente.') . '</TD></TR>';

echo '<TR><TD>' . _('Price History') . ':</TD>
	<TD><SELECT Name="x_PriceHistory">';

if ($_SESSION['PriceHistory']==1){
	echo  '<OPTION SELECTED value="1">' . _('Yes');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Yes');
}

echo '</SELECT></TD><TD>' . _('Save the items price history, every change or new price will be saved') . '</TD></TR>';

echo '<TR><TD>' . _('Reporte Vendedores') . ':</TD>
	<TD><SELECT Name="x_SalesmanInquiry">';
if ($_SESSION['SalesmanInquiry']==1){
	echo  '<OPTION SELECTED value="1">' . _('Sucursal');
	echo  '<OPTION value="0">' . _('Orden de Venta');
} else {
	echo  '<OPTION value="1">' . _('Sucursal');
	echo  '<OPTION SELECTED value="0">' . _('Orden de Venta');
}

echo '</SELECT></TD><TD>' . _('Indica como sera el reporte de vendedores') . '</TD></TR>';

// bowikaxu realhost Aprli 2008 - items cost history
echo '<TR><TD>' . _('Cost History') . ':</TD>
	<TD><SELECT Name="x_CostHistory">';

if ($_SESSION['CostHistory']==1){
	echo  '<OPTION SELECTED value="1">' . _('Yes');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Yes');
}

echo '</SELECT></TD><TD>' . _('Save the items cost history, every cost change will be saved') . '</TD></TR>';

// bowikaxu realhost 2007 - consolidar articulos de remision a factura
echo '<TR><TD>' . _('Group Items') . ':</TD>';
echo	'<TD><SELECT Name="x_GroupItems">';

if ($_SESSION['GroupItems']==1){
	echo  '<OPTION SELECTED value="1">' . _('Group Items');
	echo  '<OPTION value="0">' . _('No').' '._('Group Items');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No').' '._('Group Items');
	echo  '<OPTION value="1">' . _('Group Items');
}

echo '</SELECT></TD><TD>' . _('Group Items') . '</TD></TR>';

// bowikaxu realhost - Feb 2008 - Solo Asignar si esta conciliado (todo o su parte parcial)
echo '<TR><TD>' . _('Allow').' '._('Allocate') . ':</TD>
	<TD><SELECT Name="x_AllowAllocate" DISABLED>';

if ($_SESSION['AllowAllocate']==1){
	echo  '<OPTION SELECTED value="1">' . _('Yes');
	echo  '<OPTION value="0">' . _('No');
} else {
	echo  '<OPTION SELECTED value="0">' . _('No');
	echo  '<OPTION value="1">' . _('Yes');
}
echo '</SELECT></TD><TD>' . _('Permitir Asignacion solamente si esta conciliada la transaccion') . '</TD></TR>';
//\Jaime, forma con los datos de configuracion de envio de correos
$rh_mail__result = DB_fetch_row(DB_query("select confvalue from config where confname = 'rh_mail__host'",$db));
$rh_mail__host = $rh_mail__result[0];
$rh_mail__result = DB_fetch_row(DB_query("select confvalue from config where confname = 'rh_mail__port'",$db));
$rh_mail__port = $rh_mail__result[0];;
$rh_mail__result = DB_fetch_row(DB_query("select confvalue from config where confname = 'rh_mail__username'",$db));
$rh_mail__username = $rh_mail__result[0];;
$rh_mail__result = DB_fetch_row(DB_query("select confvalue from config where confname = 'rh_mail__password'",$db));
$rh_mail__password = $rh_mail__result[0];;
?>
<tr>
    <td>Mail Host:</td><td><input type="text" name="rh_mail__host" value="<?php echo str_replace('"', "&quot;", $rh_mail__host) ?>"/></td><td>Direccion del Servidor de Correos que enviara los mensajes. Ejem: ssl://smtp.gmail.com (gmail).</td>
</tr>
<tr>
    <td>Mail Port:</td><td><input type="text" name="rh_mail__port" value="<?php echo str_replace('"', "&quot;", $rh_mail__port) ?>"/></td><td>Puerto del Servidor de Correos que enviara los mensajes. Ejem: 465 (gmail).</td>
</tr>
<tr>
    <td>Mail Username:</td><td><input type="text" name="rh_mail__username" value="<?php echo str_replace('"', "&quot;", $rh_mail__username) ?>"/></td><td>Direccion del correo con que se enviaran los mensajes. Ejem: mi_correo@gmail.com (gmail).</td>
</tr>
<tr>
    <td>Mail Password:</td><td><input type="password" name="rh_mail__password" /></td><td>Password del correo con que se enviaran los mensajes.</td>
</tr>
<?php

echo '</SELECT></TD><TD>' . _('Save the items cost history, every cost change will be saved') . '</TD></TR>';

if(isset($_REQUEST['InvoiceService']))
	$_SESSION['InvoiceService']=UpdateConfig('InvoiceService',$_REQUEST['InvoiceService']);
else
	$_SESSION['InvoiceService']=GetConfig('InvoiceService');
echo '<TR><TD>' . _('Servicio de Facturación') . ':</TD>
	<TD><SELECT Name="InvoiceService">
	<OPTION '.($_SESSION['InvoiceService']!='EDICOM'?'SELECTED ':'').'VALUE="EDICOM">'._('EDICOM').'
	<OPTION '.($_SESSION['InvoiceService']=='INVOICE_ONE'?'SELECTED ':'').'VALUE="INVOICE_ONE">'._('INVOICE ONE').'
	</SELECT></TD>
</TR>';

//\Jaime, forma con los datos de configuracion de envio de correos
echo '</TABLE><input type="Submit" Name="submit" value="' . _('Update') . '"></CENTER></FORM>';

include('includes/footer.inc');
?>
