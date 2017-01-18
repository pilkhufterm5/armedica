<?php
/**
 * REALHOST 2008
 * $LastChangedDate: 2008-02-06 12:48:53 -0600 (Wed, 06 Feb 2008) $
 * $Rev: 15 $
 */

$PageSecurity = 2;
include('includes/session.inc');
/*
if(isset($_POST['Excell'])){
	
	header("Location: rh_customer_policy.php");
	
}
*/
include('includes/class.pdf.php');
include('includes/SQL_CommonFunctions.inc');
if (!isset($_POST['Show'])){
$title = _('Cobranza al Mes');
include('includes/header.inc');

echo "<FORM METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';

/*Dates in SQL format for the last day of last month*/
$DefaultPeriodDate = Date ('Y-m-d', Mktime(0,0,0,Date('m'),0,Date('Y')));

/*Show a form to allow input of criteria for TB to show */
echo '<CENTER><TABLE>
		<TR>
		<TD COLSPAN=2 ALIGN=CENTER><B>'._('Cobranza al Mes').'</TD>
		</TR>';	
echo '<TR>
         <TD>'._('From').':</TD>
         <TD><SELECT Name=Period>';
	 $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	 $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $_POST['Period']){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>";
         
         echo '<TR>
         <TD>'._('To').':</TD>
         <TD><SELECT Name=LastPeriod>';
	 $sql = 'SELECT periodno, lastdate_in_period FROM periods ORDER BY periodno DESC';
	 $Periods = DB_query($sql,$db);
         $id=0;
         while ($myrow=DB_fetch_array($Periods,$db)){

            if($myrow['periodno'] == $_POST['LastPeriod']){
              echo '<OPTION SELECTED VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            $id++;
            } else {
              echo '<OPTION VALUE=' . $myrow['periodno'] . '>' . _(MonthAndYearFromSQLDate($myrow['lastdate_in_period']));
            }

         }
         echo "</SELECT></TD>
        </TR>";

echo "</TABLE><P>
<INPUT TYPE=SUBMIT NAME='Show' VALUE='"._('PDF')."'>
<INPUT TYPE=SUBMIT NAME='Excell' VALUE='"._('Show Account Transactions')."'>
</CENTER></FORM>";

if(!isset($_POST['Excell'])){
	include('includes/footer.inc');
}

}
if(isset($_POST['Show'])){
	
	if (isset($_POST['Period'])){
		$SelectedPeriod = $_POST['Period'];
	} elseif (isset($_GET['Period'])){
		$SelectedPeriod = $_GET['Period'];
	}

	$LastPeriod = $_POST['LastPeriod'];
	
	if (!isset($SelectedPeriod) OR !isset($LastPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$SelectedPeriod."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$Fecha2 = $dates['lastdate_in_period'];
	$Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
	$De = $Fecha;
	
	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$LastPeriod."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$LastFecha2 = $dates['lastdate_in_period'];
	$LastFecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
	$Hasta = $LastFecha;
	
/*Yes there are line items to start the ball rolling with a page header */

	/*Set specifically for the stationery being used -needs to be modified for clients own
	packing slip 2 part stationery is recommended so storeman can note differences on and
	a copy retained */

	$Page_Width=612; // horizontal
	$Page_Height=792; // vertical
	$Top_Margin=10;
	$Bottom_Margin=20;
	$Left_Margin=10;
	$Right_Margin=10;

	$PageSize = array(0,0,$Page_Width,$Page_Height);
	$pdf = & new Cpdf($PageSize);
	$FontSize=12;
	$pdf->selectFont('./fonts/Helvetica.afm');
	$pdf->addinfo('Author','webERP ' . $Version);
	$pdf->addinfo('Creator','webERP http://www.weberp.org - R&OS PHP-PDF http://www.ros.co.nz');
	$pdf->addinfo('Title', _('Comisiones Bancarias al Mes') );
	$pdf->addinfo('Subject', _('Comisiones Bancarias al Mes'));

	$line_height=16;

	$PageNumber = 1;
	include('includes/rh_ComBankMesHeader.inc');
	$FontSize = 9;
	
	$YPos -= ($line_height);
	$line_height = 11;
	$PeriodTotal = 0;
	$PeriodNo = -9999;
	$DebitTotal = 0;
	$CreditTotal = 0;
	$TotFinal = 0;
	$TotCtes = 0;
	
	// CUENTA DE BANCOS
	// obtener la suma de las cuentas del grupo de bancos
	$sql = "SELECT accountcode FROM chartmaster WHERE group_ = 'BANCOS'";
	$actsres = DB_query($sql,$db,'','');
	while($accounts = DB_fetch_array($actsres)){
		
	$SelectedAccount = $accounts['accountcode'];
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		
		//$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal	

			$pdf->addTextWrap(35, $YPos,50,$FontSize, $SelectedAccount,'left');
			$pdf->addTextWrap(100, $YPos,220,$FontSize, $info['accountname'],'left');
			$pdf->addTextWrap(320, $YPos,100,$FontSize, $Fecha,'left');
			
			//$pdf->addTextWrap(370, $YPos,80,$FontSize, $myrow['typename'],'left');
			
			$pdf->addTextWrap(430, $YPos,80,$FontSize, $Amount,'right');

		$j++;

		if ($j == 18){
			$j=1;
		}		
		
		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_CobranzaMesHeader.inc');
		}
	}
	$YPos -= ($line_height);
	// FIN CUENTA DE BANCOS
	
	// CUENTA DE CLIENTES
	$SelectedAccount = $_SESSION['CompanyRecord']['debtorsact'];
	// obtener saldo
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		
		//$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal	

			$pdf->addTextWrap(35, $YPos,50,$FontSize, $SelectedAccount,'left');
			$pdf->addTextWrap(100, $YPos,220,$FontSize, $info['accountname'],'left');
			$pdf->addTextWrap(320, $YPos,100,$FontSize, $Fecha,'left');
			
			//$pdf->addTextWrap(370, $YPos,80,$FontSize, $myrow['typename'],'left');
			
			$pdf->addTextWrap(500, $YPos,80,$FontSize, $Amount,'right');

		$j++;

		if ($j == 18){
			$j=1;
		}		
		
		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_CobranzaMesHeader.inc');
		}
	$YPos -= ($line_height);
	// FIN CUENTA DE CLIENTES
	
	//  CUENTAS DE IMPUESTOS ---------------------------
	// obtener saldo
	$SelectedAccount = '270001';
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		
		//$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal	

			$pdf->addTextWrap(35, $YPos,50,$FontSize, $SelectedAccount,'left');
			$pdf->addTextWrap(100, $YPos,220,$FontSize, $info['accountname'],'left');
			$pdf->addTextWrap(320, $YPos,100,$FontSize, $Fecha,'left');
			
			//$pdf->addTextWrap(370, $YPos,80,$FontSize, $myrow['typename'],'left');
			
			$pdf->addTextWrap(430, $YPos,80,$FontSize, $Amount,'right');

		$j++;

		if ($j == 18){
			$j=1;
		}		
		
		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_CobranzaMesHeader.inc');
		}
	$YPos -= ($line_height);
	// FIN IMPUESTOS
	
	//  CUENTAS DE IMPUESTOS ---------------------------
	// obtener saldo
	$SelectedAccount = '270002';
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		
		//$URL_to_TransDetail = $rootpath . '/GLTransInquiry.php?' . SID . '&TypeID=' . $myrow['type'] . '&TransNo=' . $myrow['typeno'];
		// bowikaxu - Se agrego la columna balance con el valor de $RunningTotal	

			$pdf->addTextWrap(35, $YPos,50,$FontSize, $SelectedAccount,'left');
			$pdf->addTextWrap(100, $YPos,220,$FontSize, $info['accountname'],'left');
			$pdf->addTextWrap(320, $YPos,100,$FontSize, $Fecha,'left');
			
			//$pdf->addTextWrap(370, $YPos,80,$FontSize, $myrow['typename'],'left');
			
			$pdf->addTextWrap(500, $YPos,80,$FontSize, $Amount,'right');

		$j++;

		if ($j == 18){
			$j=1;
		}		
		
		$YPos -= ($line_height);
		if($YPos <= 20){
			$PageNumber++;
			include('includes/rh_CobranzaMesHeader.inc');
			
		}
	$YPos -= ($line_height);
	// FIN IMPUESTOS
	
	// FIN - MOSTRAR TOTALES ---------------------------
      	//$pdf->addTextWrap(100, $YPos,220,$FontSize, _('Sumas Iguales'),'left');
		//$pdf->addTextWrap(430, $YPos,80,$FontSize+1, number_format($DebitTotal,2),'right');
		//$pdf->addTextWrap(500, $YPos,80,$FontSize+1, number_format($CreditTotal,2),'right');

		//$YPos -= ($line_height);
		$YPos -= ($line_height);
		if($YPos <= 40){
			$PageNumber++;
			$pdf->newPage();
			$pdf->addTextWrap(10,750,600,11,$_SESSION['CompanyRecord']['coyname'],'center');

			$pdf->addText(40, 720,$FontSize, _('P&oacute;liza').' '._('Number').": ");
			if($De == $Hasta){
				$concepto = _('Concepto').' Costo de Ventas de '.$De;
			}else {
				$concepto = _('Concepto').' Costo de Ventas de '.$De.' - '.$Hasta;
			}
				$pdf->addText(40+10, 720-$FontSize+1,$FontSize, $concepto);

			//$pdf->addText(450, 710,$FontSize,  _('Date').': '.date('Y-m-d'));
			//include('includes/rh_VtasPolicyHeader.inc');	
			
			$YPos = 638;
		}		
			// Print bottom fields
			$pdf->line(5,$YPos,690,$YPos);
			$YPos -= ($line_height);
			
			$pdf->addTextWrap(10, $YPos,50,$FontSize, '| '._('Hecho Por').':','left');
		
			$pdf->addTextWrap(100, $YPos,180,$FontSize, '| '._('Revisado Por').':','left');
			$pdf->addTextWrap(280, $YPos,170,$FontSize, '| '._('Autorizado Por').':','left');
		
			$pdf->addTextWrap(430, $YPos,80,$FontSize, '| '._('Diario No').'.','right');
			$pdf->addTextWrap(500, $YPos,80,$FontSize, '| '._('P&oacute;liza').' No.','right');
		
			$YPos -= ($line_height);
			$pdf->line(5,$YPos-$FontSize,690,$YPos-$FontSize);
			// End printing bottom fields	
			
			$YPos -= ($line_height);
			//$pdf->addText($XPos, $YPos,$FontSize, _('Impreso').': '.date('Y-m-d'));
		
		
		$pdfcode = $pdf->output();
$len = strlen($pdfcode);

if ($len<=20){
	$title = _('Comisiones Bancarias al Mes');
	include('includes/header.inc');
	echo '<p>'. _('No habia transacciones para ser mostradas').
		'<BR>'. '<A HREF="' . $rootpath . '/index.php?' . SID . '">' . _('Back to the menu') . '</A>';
	include('includes/footer.inc');
	exit;
} else {
	header('Content-type: application/pdf');
	header('Content-Length: ' . $len);
	header('Content-Disposition: inline; filename=ComBankMes.pdf');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');

	$pdf->Stream();

}
		
} /*end if there are order details to show on the order*/
// EXCEL -----------------------------------------------
if(isset($_POST['Excell'])){
	
	if (isset($_POST['Period'])){
		$SelectedPeriod = $_POST['Period'];
	} elseif (isset($_GET['Period'])){
		$SelectedPeriod = $_GET['Period'];
	}
	
	$LastPeriod = $_POST['LastPeriod'];
	
	$SelectedAccount = '530002';

	if (!isset($SelectedPeriod) OR !isset($LastPeriod)){
		prnMsg(_('A period or range of periods must be selected from the list box'),'info');
		include('includes/footer.inc');
		exit;
	}

	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$SelectedPeriod."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$Fecha2 = $dates['lastdate_in_period'];
	$Fecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
	$De = $Fecha;
	
	$sql = "SELECT lastdate_in_period FROM periods WHERE periodno  = ".$LastPeriod."";
	$dates = DB_fetch_array(DB_query($sql,$db));
	$LastFecha2 = $dates['lastdate_in_period'];
	$LastFecha = MonthAndYearFromSQLDate($dates['lastdate_in_period']);
	$Hasta = $LastFecha;
	
	$TableHeader = "<TR>
					<TD class='tableheader' ALIGN=LEFT>"._('Account')."</TD>
					<TD class='tableheader' ALIGN=LEFT>"._('Concepto')."</TD>
					<TD class='tableheader' ALIGN=RIGHT>"._('Debit')."</TD>
					<TD class='tableheader' ALIGN=RIGHT>"._('Credit')."</TD>
					</TR>";
	echo "<CENTER><TABLE>".$TableHeader;
	// CUENTA DE BANCOS
	// obtener la suma de las cuentas del grupo de bancos
	$sql = "SELECT accountcode FROM chartmaster WHERE group_ = 'BANCOS'";
	$actsres = DB_query($sql,$db,'','');
	while($accounts = DB_fetch_array($actsres)){
		
	$SelectedAccount = $accounts['accountcode'];
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		printf("<TR>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$SelectedAccount,
					$info['accountname'],
					$Amount,
					'');
	}
	// FIN CUENTA DE BANCOS
	
	// CUENTA DE CLIENTES
	$SelectedAccount = $_SESSION['CompanyRecord']['debtorsact'];
	// obtener saldo
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		printf("<TR>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$SelectedAccount,
					$info['accountname'],
					'',
					$Amount);
	// FIN CUENTA DE CLIENTES
	
	//  CUENTAS DE IMPUESTOS ---------------------------
	// obtener saldo
	$SelectedAccount = '270001';
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);
		printf("<TR>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$SelectedAccount,
					$info['accountname'],
					$Amount,
					'');
	// FIN IMPUESTOS
	
	//  CUENTAS DE IMPUESTOS ---------------------------
	// obtener saldo
	$SelectedAccount = '270002';
	$sql= "SELECT SUM(actual) AS amount FROM chartdetails WHERE accountcode = '".$SelectedAccount."' 
			AND period >= ".$SelectedPeriod."
			AND period <= ".$LastPeriod."";
	
	$ErrMsg = _('The transactions for account') . ' ' . $SelectedAccount . ' ' . _('could not be retrieved because') ;
	$TransResult = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_array($TransResult);	
	// obtener info cuenta
	$sql = "SELECT * FROM chartmaster WHERE accountcode = '".$SelectedAccount."'";
	$res = DB_query($sql,$db,'','');
	$info = DB_fetch_array($res);
	
		$RunningTotal = $myrow['amount'];

		$Amount = number_format($myrow['amount'],2);

			printf("<TR>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=LEFT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$SelectedAccount,
					$info['accountname'],
					'',
					$Amount);

	// FIN IMPUESTOS
	echo "</TABLE></CENTER>";
	include('includes/footer.inc');
}

?>