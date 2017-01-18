<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 5;

include('includes/DefineSuppTransClass.php');
/* Session started in header.inc for password checking and authorisation level check */
include('includes/session.inc');

$title = _('Ver Nota de Credito del Proveedor');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');

echo "<A HREF='" . $rootpath . '/SelectSupplier.php?' . SID . "'>" . _('Back to Suppliers') . '</A><BR>';

if (isset($_GET['Transno'])){

/*Now retrieve supplier information - name, currency, default ex rate, terms, tax rate etc */

	// bowikaxu March 2007 - comenzar a desplegar informacion
	
	$SQL = "SELECT supptrans.transno,
			supptrans.suppreference,
			supptrans.trandate,
			supptrans.duedate,
			supptrans.rate,
			supptrans.rh_invdate,
			supptrans.transtext,
			supptrans.ovamount,
			supptrans.ovgst,
			supptrans.alloc,
			suppliers.suppname,
			suppliers.currcode,
			paymentterms.terms,
			taxgroups.taxgroupdescription
			FROM supptrans, suppliers, paymentterms, taxgroups WHERE
			supptrans.transno = ".$_GET['Transno']."
			AND supptrans.type = 21
			AND suppliers.paymentterms=paymentterms.termsindicator
			AND suppliers.taxgroupid=taxgroups.taxgroupid
			AND suppliers.supplierid = supptrans.supplierno";
	
	$TransRes = DB_query($SQL,$db);
	
	$Trans = DB_fetch_array($TransRes);
	
	$sql = "SELECT * FROM gltrans WHERE type = 21 AND typeno =".$_GET['Transno']."";

	$CharRes = DB_query($sql,$db);
	
	echo "<CENTER><TABLE>";
	echo "<TR><TD colspan=4 align=center><B>"._('NOTA DEUDORA ').$Trans['transno']."</B></TD></TR>";
	echo "<TR><TD class='tableheader'>"._('Proveedor').": </TD><TD>".$SupplierID.' - '.$Trans['suppname']."</TD>";
	echo "<TD class='tableheader'>"._('Moneda').": </TD><TD>".$Trans['currcode']."</TD></TR>";
	
	echo "<TR><TD class='tableheader'>"._('Terminos').": </TD><TD>".$Trans['terms']."</TD>";
	echo "<TD class='tableheader'>"._('Impuesto').": </TD><TD>".$Trans['taxgroupdescription']."</TD></TR>";
	
	echo "<TR><TD class='tableheader'>"._('Referencia').": </TD><TD>".$Trans['suppreference']."</TD>";
	echo "<TD class='tableheader'>"._('Fecha Transaccion').": </TD><TD>".$Trans['trandate']."</TD></TR>";
	
	echo "<TR><TD class='tableheader'>"._('Rate').": </TD><TD>".$Trans['rate']."</TD>";
	echo "<TD class='tableheader'>"._('Fecha Deuda').": </TD><TD>".$Trans['duedate']."</TD></TR>";
	$Total = $Trans['ovamount']+$Trans['ovgst'];
	echo "<TR><TD colspan=4 align=center><B>"._('CANTIDADES')."</B></TD></TR>";
	echo "<TR><TD class='tableheader'>"._('Total').": </TD><TD align=right>".number_format($Trans['ovamount'],2)."</TD></TR>";
	echo "<TR><TD class='tableheader'>"._('Impuestos').": </TD><TD align=right>".number_format($Trans['ovgst'],2)."</TD></TR>";
	echo "<TR><TD class='tableheader'>"._('Provisto').": </TD><TD align=right>".number_format($Trans['alloc'],2)."</TD></TR>";
	echo "<TR><TD class='tableheader'>"._('Balance').": </TD><TD align=right>".number_format(($Trans['ovamount']+$Trans['ovgst']-$Trans['alloc']),2)."</TD></TR>";
	
	echo "<TR><TD colspan=4 align=center><B>"._('TEXTO')."</B></TD></TR>";
	echo "<TR><TD class='tableheader' colspan=4>".$Trans['transtext']."</TD></TR>";
	
	echo "<TR><TD colspan=4 align=center><B>"._('GL Trans')."</B></TD></TR>";
	while($Charges = DB_fetch_array($CharRes)){
		
		echo "<TR><TD class='tableheader'>"._('Descripcion').": </TD><TD align=right>".$Charges['narrative']."</TD>";
		echo "<TD align=right>".number_format($Charges['amount'],2)."</TD></TR>";
		//$Total = $Total - $Pay['amt'];
		//echo "<TR><TD class='tableheader'>"._('Saldo Final').": </TD><TD align=right>".number_format($Total,2)."</TD></TR>";
	}

	echo "<CENTER></TABLE>";
	
} elseif (!isset($_GET['Transno'])){

	prnMsg( _('To enter a supplier invoice the supplier must first be selected from the supplier selection screen'),'warn');
	echo "<BR><A HREF='$rootpath/SelectSupplier.php?" . SID ."'>" . _('Select A Supplier to Enter an Invoice For') . '</A>';
	include('includes/footer.inc');
	exit;

	/*It all stops here if there ain't no supplier selected */
}



include('includes/footer.inc');
?>
