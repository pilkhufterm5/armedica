<?php
/* webERP Revision: 14 $ */
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-09-19 10:50:29 -0500 (Fri, 19 Sep 2008) $
 * $Rev: 402 $
 */
/**************************************************************************
* Jorge Garcia
* 11/Nov/2008 Archivo creado para el reverseo de pagos
***************************************************************************/
$PageSecurity = 3;

include('includes/session.inc');

$title = _('Reverse');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

if (isset($_GET['TypeID'])) {
	$TypeID = ($_GET['TypeID']);
}elseif (isset($_POST['TypeID'])){
	$TypeID = ($_POST['TypeID']);
}

if(isset($_GET['TransID'])) {
	$TransID = $_GET['TransID'];
}else if(isset($_POST['TransID'])){
	$TransID = ($_POST['TransID']);
}

if(isset($_GET['TransNo'])) {
	$TransNo = ($_GET['TransNo']);
}elseif(isset($_POST['TransNo'])){
	$TransNo = ($_POST['TransNo']);
}

if($TypeID == 12){	
	if(isset($_GET['CustomerID'])) {
		$CustomerID = strtoupper($_GET['CustomerID']);
	}elseif(isset($_POST['CustomerID'])){
		$CustomerID = strtoupper($_POST['CustomerID']);
	}
	echo "<BR><A HREF='" . $rootpath . "/CustomerInquiry.php?CustomerID=".$CustomerID."'>" . _('Back') . "</A>";
}

if($TypeID == 22){	
	if(isset($_GET['SupplierID'])) {
		$SupplierID = strtoupper($_GET['SupplierID']);
	}elseif(isset($_POST['SupplierID'])){
		$SupplierID = strtoupper($_POST['SupplierID']);
	}
	echo "<BR><A HREF='" . $rootpath . "/SupplierInquiry.php?SupplierID=".$SupplierID."'>" . _('Back') . "</A>";
}

DB_query("BEGIN",$db);

if(!isset($_POST['darreverso'])){
	echo "<FORM METHOD='post' action=".$_SERVER['PHP_SELF'].">";
	echo "<H1><STRONG>
	"._('Desea dar reverso a la transaccion ').$TransNo." "._('?')."
	</H1></STRONG>
	<input type=SUBMIT name='darreverso' value='Yes'>
	<input type=SUBMIT name='darreverso' value='No'>
	<input type='hidden' name='TypeID' value='".$TypeID."'>
	<input type='hidden' name='TransNo' value='".$TransNo."'>
	<input type='hidden' name='TransID' value='".$TransID."'>";
	
	if($TypeID == 12){
		echo "<input type='hidden' name='CustomerID' value='".$CustomerID."'></FORM>";
	}
	if($TypeID == 22){
		echo "<input type='hidden' name='SupplierID' value='".$SupplierID."'></FORM>";
	}
}

if(isset($_POST['darreverso']) AND $_POST['darreverso'] == "Yes"){
	if($TypeID == 12){
		$sql = "SELECT settled, transno, type, debtorno, branchcode, trandate, prd, reference, tpe, rate, ovamount, ovdiscount, invtext, rh_createdate, alloc FROM debtortrans WHERE id=".$_POST['TransID'];
		$result = DB_query($sql,$db);
		$row=DB_fetch_array($result);
		if($row['settled'] == 0 AND $row['alloc'] == 0){
			$old_transno = $row['transno'];
			$old_type = $row['type'];
			$old_debtorno = $row['debtorno'];
			$old_branchcode = $row['branchcode'];
			$old_trandate = $row['trandate'];
			$old_prd = $row['prd'];
			$old_reference = $row['reference'];
			$old_tpe = $row['tpe'];
			$old_rate = $row['rate'];
			$old_ovamount = $row['ovamount'];
			$old_ovdiscount = $row['ovdiscount'];
			$old_invtext = $row['invtext'];
			$old_rh_createdate = $row['rh_createdate'];
			/*
			$checkperiod = rh_checkperiods($old_trandate,$db);
			//echo $checkperiod;
			//exit;
			if($checkperiod == 'BLOQUEO'){
				prnMsg( _('The period is closed. The transaction can not be processed'),'error');
				include('includes/footer.inc');
				exit;
			}
			*/
			$sqltype = "SELECT (typeno+1) AS typeno FROM systypes WHERE typeid = ".$TypeID."";
			$resulttype = DB_query($sqltype,$db);
			$rowtype = DB_fetch_array($resulttype);
		
			$sqltypeplus = "UPDATE systypes SET typeno = typeno+1 WHERE typeid = ".$TypeID."";
			$resulttypeplus = DB_query($sqltypeplus,$db);
		
			$sqltrans = "INSERT INTO debtortrans (transno, type, debtorno, branchcode, trandate, prd, reference, tpe, rate, ovamount, ovdiscount, invtext, rh_createdate) VALUES ('".$rowtype['typeno']."','".$old_type."','".$old_debtorno."','".$old_branchcode."','".$old_trandate."','".$old_prd."','Reverso de:".$old_reference." [".$old_transno."] por: ".$_SESSION['UserID']."','".$old_tpe."','".$old_rate."','".($old_ovamount)*(-1)."','".($old_ovdiscount)*(-1)."','".$old_invtext."', NOW())";
			$resulttrans = DB_query($sqltrans,$db);
		
			$sqltypeplus = "UPDATE debtorsmaster SET lastpaiddate = '".date('Y-m-d')."', lastpaid=".($old_ovamount)*(-1)." WHERE debtorsmaster.debtorno='".$old_debtorno."'";
			$resulttypeplus = DB_query($sqltypeplus,$db);
			$narra1 = "";
			$narra2 = "";
			$sqlgltrans = "SELECT type, typeno, trandate, periodno, account, narrative, amount FROM gltrans WHERE type = ".$TypeID." AND typeno = ".$TransNo."";
			$resultgltrans = DB_query($sqlgltrans,$db);
			while($rowgltrans=DB_fetch_array($resultgltrans)){
				//rleal Jun 16 2011 se revisan los signos
				if ($rowgltrans['amount']>0)
					$old_ovamount=$old_ovamount*-1;
				//mhidalgo - 20110120 - El amount que se inserta, es el que se obtiene de debtortrans
				$sqlgl = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES ('".$rowgltrans['type']."','".$rowtype['typeno']."','".$rowgltrans['trandate']."','".$rowgltrans['periodno']."','".$rowgltrans['account']."','Reverso de: ".$rowgltrans['narrative']." [".$rowgltrans['typeno']."] por: ".$_SESSION['UserID']."','".(($old_ovamount)*(-1))."')";
				if ($rowgltrans['amount']>0)
					$old_ovamount=$old_ovamount*-1;
				$resultgl = DB_query($sqlgl,$db);
				$narra1 = $rowgltrans['narrative'];
				$narra2 = $rowgltrans['typeno'];
			}
		
			$sqlbanktrans = "SELECT type, transno, bankact, ref, exrate, transdate, banktranstype, amount, currcode FROM banktrans WHERE type = ".$TypeID." AND transno = ".$TransNo."";
			$resultbanktrans = DB_query($sqlbanktrans,$db);
			$rowbanktrans = DB_fetch_array($resultbanktrans);
			//mhidalgo - 20110120 - El amount que se inserta, es el que se obtiene de debtortrans
			$sqlbank = "INSERT INTO banktrans (type, transno, bankact, ref, exrate, transdate, banktranstype, amount, currcode) VALUES ('".$rowbanktrans['type']."','".$rowtype['typeno']."','".$rowbanktrans['bankact']."','Reverso de: ".$narra1." [".$narra2."] por: ".$_SESSION['UserID']."', '".$rowbanktrans['exrate']."', '".$rowbanktrans['transdate']."', '".$rowbanktrans['banktranstype']."', '".(($old_ovamount)*(-1))."','".$rowbanktrans['currcode']."')";
			$resultbank = DB_query($sqlbank,$db);
			
			//No es necesario hacer esta consulta, ya que con la modificación ya se debe tener el parametro
			//$sqldebtor1 = "SELECT id FROM debtortrans WHERE transno = ".$TransNo." AND type = ".$TypeID."";
			//$resultdebtor1 = DB_query($sqldebtor1,$db);
			//$rowdebtor1 = DB_fetch_array($resultdebtor1);
			$rowdebtor1 = $_POST['TransID'];
		
			$sqldebtor2 = "SELECT id FROM debtortrans WHERE transno = ".$rowtype['typeno']." AND type = ".$TypeID."";
			$resultdebtor2 = DB_query($sqldebtor2,$db);
			$rowdebtor2 = DB_fetch_array($resultdebtor2);
		
			$sqlcust = "INSERT INTO custallocns (amt, datealloc, transid_allocfrom, transid_allocto) VALUES ('".$old_ovamount."','".date('Y-m-d')."','".$rowdebtor1."','".$rowdebtor2['id']."')";
			$resultcust = DB_query($sqlcust,$db);
				
			$sqlrever = "INSERT INTO rh_reverseo (amt, daterevers, transid_reversfrom, transid_reversto, type) VALUES ('".$old_ovamount."','".date('Y-m-d')."','".$rowdebtor1."','".$rowdebtor2['id']."','".$TypeID."')";
			$resultcust = DB_query($sqlrever,$db);
			
			$sqldeb1 = "UPDATE debtortrans SET alloc = '".($old_ovamount+$old_ovdiscount)."', settled = '1' WHERE id=".$_POST['TransID'];
			$resultcust = DB_query($sqldeb1,$db);
				
			$sqldeb2 = "UPDATE debtortrans SET alloc = '".($old_ovamount+$old_ovdiscount)*(-1)."', settled = '1' WHERE transno = ".$rowtype['typeno']." AND type = ".$TypeID."";
			$resultcust = DB_query($sqldeb2,$db);
		
			prnMsg(_('El reverso de la transaccion se realizo'),'success');
		}else{
			prnMsg(_('Esta transaccion ya ha sido asignada'),'error');
		}
	}
	
	if($TypeID == 22){
		$sql = "SELECT settled, transno, type, supplierno, suppreference, trandate, rate, ovamount, ovgst, transtext, alloc FROM supptrans WHERE type = ".$TypeID." AND transno = ".$TransNo." AND supplierno = '".$_POST['SupplierID']."'";
		$result = DB_query($sql,$db);
		$row=DB_fetch_array($result);
		if($row['settled'] == 0 AND $row['alloc'] == 0){
			$old_transno = $row['transno'];
			$old_type = $row['type'];
			$old_supplierno = $row['supplierno'];
			$old_suppreference = $row['suppreference'];
			$old_trandate = $row['trandate'];
			$old_rate = $row['rate'];
			$old_ovamount = $row['ovamount'];
			$old_ovgst = $row['ovgst'];
			$old_transtext = $row['transtext'];
			
		/*	$checkperiod = rh_checkperiods($old_trandate,$db);
			//echo $checkperiod;
			//exit;
			if($checkperiod == 'BLOQUEO'){
				prnMsg( _('The period is closed. The transaction can not be processed'),'error');
				include('includes/footer.inc');
				exit;
			}
		*/
			$sqltype = "SELECT (typeno+1) AS typeno FROM systypes WHERE typeid = ".$TypeID."";
			$resulttype = DB_query($sqltype,$db);
			$rowtype = DB_fetch_array($resulttype);
		
			$sqltypeplus = "UPDATE systypes SET typeno = typeno+1 WHERE typeid = ".$TypeID."";
			$resulttypeplus = DB_query($sqltypeplus,$db);
		
			$sqltrans = "INSERT INTO supptrans (transno, type, supplierno, suppreference, trandate, rate, ovamount, ovgst, transtext) VALUES ('".$rowtype['typeno']."', '".$old_type."', '".$old_supplierno."', '".$old_suppreference."', '".$old_trandate."', '".$old_rate."', '".(($old_ovamount)*(-1))."', '".$old_ovgst."', 'Reverso de:".$old_transtext." [".$old_transno."] por: ".$_SESSION['UserID']."')";
			$resulttrans = DB_query($sqltrans,$db);
		
			$sqlsup = "UPDATE suppliers SET lastpaiddate = '".date('Y-m-d')."', lastpaid=".(($old_ovamount)*(-1))." WHERE supplierid = '".$old_supplierno."'";
			$resultsup = DB_query($sqlsup,$db);
			$narra1 = "";
			$narra2 = "";
			$sqlgltrans = "SELECT type, typeno, trandate, periodno, account, narrative, amount FROM gltrans WHERE type = ".$TypeID." AND typeno = ".$TransNo."";
			$resultgltrans = DB_query($sqlgltrans,$db);
			while($rowgltrans=DB_fetch_array($resultgltrans)){
				$sqlgl = "INSERT INTO gltrans (type, typeno, trandate, periodno, account, narrative, amount) VALUES ('".$rowgltrans['type']."','".$rowtype['typeno']."','".$rowgltrans['trandate']."','".$rowgltrans['periodno']."','".$rowgltrans['account']."','Reverso de: ".$rowgltrans['narrative']." [".$rowgltrans['typeno']."] por: ".$_SESSION['UserID']."','".(($rowgltrans['amount'])*(-1))."')";
				$resultgl = DB_query($sqlgl,$db);
				$narra1 = $rowgltrans['narrative'];
				$narra2 = $rowgltrans['typeno'];
			}
		
			$sqlbanktrans = "SELECT type, transno, bankact, ref, exrate, transdate, banktranstype, amount, currcode FROM banktrans WHERE type = ".$TypeID." AND transno = ".$TransNo."";
			$resultbanktrans = DB_query($sqlbanktrans,$db);
			$rowbanktrans = DB_fetch_array($resultbanktrans);
			$sqlbank = "INSERT INTO banktrans (type, transno, bankact, ref, exrate, transdate, banktranstype, amount, currcode) VALUES ('".$rowbanktrans['type']."','".$rowtype['typeno']."','".$rowbanktrans['bankact']."','Reverso de: ".$narra1." [".$narra2."] por: ".$_SESSION['UserID']."', '".$rowbanktrans['exrate']."', '".$rowbanktrans['transdate']."', '".$rowbanktrans['banktranstype']."', '".(($rowbanktrans['amount'])*(-1))."','".$rowbanktrans['currcode']."')";
			$resultbank = DB_query($sqlbank,$db);
			
			$sqldebtor1 = "SELECT id FROM supptrans WHERE transno = ".$TransNo." AND type = ".$TypeID."";
			$resultdebtor1 = DB_query($sqldebtor1,$db);
			$rowdebtor1 = DB_fetch_array($resultdebtor1);
		
			$sqldebtor2 = "SELECT id FROM supptrans WHERE transno = ".$rowtype['typeno']." AND type = ".$TypeID."";
			$resultdebtor2 = DB_query($sqldebtor2,$db);
			$rowdebtor2 = DB_fetch_array($resultdebtor2);
		
			$sqlcust = "INSERT INTO suppallocs (amt, datealloc, transid_allocfrom, transid_allocto) VALUES ('".$rowbanktrans['amount']."','".date('Y-m-d')."','".$rowdebtor1['id']."','".$rowdebtor2['id']."')";
			$resultcust = DB_query($sqlcust,$db);
				
			$sqlrever = "INSERT INTO rh_reverseo (amt, daterevers, transid_reversfrom, transid_reversto, type) VALUES ('".$rowbanktrans['amount']."','".date('Y-m-d')."','".$rowdebtor1['id']."','".$rowdebtor2['id']."','".$TypeID."')";
			$resultcust = DB_query($sqlrever,$db);
			
			$sqldeb1 = "UPDATE supptrans SET alloc = '".$old_ovamount."', settled = '1' WHERE transno = ".$TransNo." AND type = ".$TypeID."";
			$resultcust = DB_query($sqldeb1,$db);
				
			$sqldeb2 = "UPDATE supptrans SET alloc = '".($old_ovamount)*(-1)."', settled = '1' WHERE transno = ".$rowtype['typeno']." AND type = ".$TypeID."";
			$resultcust = DB_query($sqldeb2,$db);
		
			prnMsg(_('El reverso de la transaccion se realizo'),'success');
		}else{
			prnMsg(_('Esta transaccion ya ha sido asignada'),'error');
		}
	}
}else{
	if($_POST['darreverso'] == "No"){
		if($TypeID == 12){
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/CustomerInquiry.php?' . SID . '&CustomerID='.$CustomerID.'">';
			prnMsg(_('You should automatically be forwarded') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
			'<a href="' . $rootpath . '/CustomerInquiry.php?' . SID . '&CustomerID='.$CustomerID.'">' . _('click here') . '</a> ' . _('to continue') . 'info');
			exit;
		}
		if($TypeID == 22){
			echo '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=' . $rootpath . '/SupplierInquiry.php?' . SID . '&SupplierID='.$SupplierID.'">';
			prnMsg(_('You should automatically be forwarded') . '. ' . _('If this does not happen') . ' (' . _('if the browser does not support META Refresh') . ') ' .
			'<a href="' . $rootpath . '/SupplierInquiry.php?' . SID . '&SupplierID='.$SupplierID.'">' . _('click here') . '</a> ' . _('to continue') . 'info');
			exit;
		}
	}
}
DB_query("COMMIT",$db);
include('includes/footer.inc');
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
?>
