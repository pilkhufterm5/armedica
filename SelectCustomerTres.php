<?php
/* $Revision: 14 $ */
/* $Revision: 14 $ */

$PageSecurity = 2;
include('includes/session.inc');
$title = _('Search Customers');
include('includes/header.inc');
$afil=unserialize(GetConfig('Afiliaciones'));
if(!isset($prefijoAfiliados))
    $p=$afil['Prefijo'];
else{
    $afil['Prefijo']=$p=$prefijoAfiliados;
    UpdateConfig('Afiliaciones',serialize($afil));
}
include('includes/Wiki.php');

$msg="";

if (isset($_REQUEST['AfilFolio']) &&$_REQUEST['AfilFolio']!=""){
	$FolioTitular = $_REQUEST['AfilFolio'];

   $GetDebtorNo ="SELECT debtorno FROM rh_titular WHERE folio = '" . $FolioTitular . "'";
   $_GetDebtorNo = DB_query($GetDebtorNo, $db);
   if($_2GetDebtorNo = DB_fetch_assoc($_GetDebtorNo)){
	   $_POST['Folio'] = $FolioTitular;
	   $_POST['CustCode'] = $_2GetDebtorNo['debtorno'];
	   $_POST['Search'] = "Buscar";
   }
}


if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])||$_POST['PageOffset']='') {
  $_POST['PageOffset'] = 1;
}
if ($_POST['PageOffset']<1) {
    $_POST['PageOffset'] = 1;
  }

if(isset($_GET['CustomerID'])){
    $_SESSION['CustomerID'] = $_GET['CustomerID'];
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){

    if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}

    if ($_POST['Keywords'] AND (($_POST['CustCode']) OR ($_POST['CustPhone']))) {
		$msg=_('Customer name keywords have been used in preference to customer code or phone  entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}

    if (($_POST['CustCode']) AND ($_POST['CustPhone'])) {
		$msg=_('Customer code has been used in preference to the customer phone entered') . '.';
	}

    if (($_POST['RFC'])) {
		$msg=_('El RFC ha sido seleccionado como busqueda') . '.';
	}

    if (($_POST['Keywords']=="") AND ($_POST['CustCode']=="") AND ($_POST['CustPhone']=="") AND ($_POST['RFC']=="") AND ($_POST['expediente']=="")) {

        //$msg=_('At least one Customer Name keyword OR an extract of a Customer Code or Customer Phone must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster
                LEFT JOIN custbranch ON debtorsmaster.debtorno = custbranch.debtorno
                WHERE custbranch.branchcode LIKE '{$p}%'
				ORDER BY debtorsmaster.debtorno";
	}else if($_POST['RFC']) {
		// bowikaxu - april 2007
		$SQL= "SELECT debtorsmaster.debtorno,
					debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster
                LEFT JOIN custbranch ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.taxref LIKE '%".$_POST['RFC']."%'
                AND custbranch.branchcode LIKE '{$p}%'
				ORDER BY debtorsmaster.debtorno DESC";

	}else if($_POST['expediente']) {
		// bowikaxu - april 2007
		$SQL= "SELECT debtorsmaster.debtorno,
                    debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster
                LEFT JOIN custbranch ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.expediente LIKE '%".$_POST['expediente']."%'
                AND custbranch.branchcode LIKE '{$p}%'
				ORDER BY debtorsmaster.debtorno DESC";

	} else {

		if (strlen($_POST['Keywords'])>0) {
			$_POST['Keywords'] = strtoupper(trim($_POST['Keywords']));
			//insert wildcard characters in spaces
			$i=0;
			$SearchString = "%";
			while (strpos($_POST['Keywords'], " ", $i)) {
				$wrdlen=strpos($_POST['Keywords']," ",$i) - $i;
				$SearchString=$SearchString . substr($_POST['Keywords'],$i,$wrdlen) . "%";
				$i=strpos($_POST['Keywords']," ",$i) +1;
			}
			$SearchString = $SearchString . substr($_POST['Keywords'],$i)."%";
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster
                LEFT JOIN custbranch ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'
                AND custbranch.branchcode LIKE '{$p}%'
				ORDER BY debtorsmaster.debtorno DESC";

		} elseif (strlen($_POST['CustCode'])>0){
            /* SE CAMBIA WHERE Ahora pide el branccode que empieza con la T- */
			$_POST['CustCode'] = strtoupper(trim($_POST['CustCode']));
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster
                LEFT JOIN custbranch ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE custbranch.branchcode " . LIKE  . " '%{$p}" . $_POST['CustCode'] . "%'
                AND custbranch.branchcode LIKE '{$p}%'
				ORDER BY debtorsmaster.debtorno";
		} /*elseif (strlen($_POST['CustPhone'])>0){
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.expediente,
                    debtorsmaster.name,
                    debtorsmaster.name2,
					debtorsmaster.taxref,
                    debtorsmaster.rh_tel1,
                    debtorsmaster.rh_tel2,
                    debtorsmaster.rh_tel3,
                    debtorsmaster.rh_tel4,
                    custbranch.folio,
                    custbranch.branchcode,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    custbranch.email
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE custbranch.phoneno " . LIKE  . " '%" . $_POST['CustPhone'] . "%'
                AND custbranch.branchcode LIKE '{$p}%'
				ORDER BY custbranch.debtorno DESC";*/
               /* elseif (strlen($_POST['CustPhone'])>0){
            $SQL = "SELECT debtortrans.id,
                    debtortrans.transno,
                    debtortrans.type,
                    debtortrans.debtorno,
                    debtortrans.branchcode,
                    debtortrans.trandate
                FROM debtortrans 
                WHERE debtortrans.transno " . LIKE  . " '%" . $_POST['CustPhone'] . "%'
                
                ORDER BY debtortrans.transno DESC";*/
// Se comentarizo el elseif y se cambio por este, esto para filtrar por transno Angeles Perez 2016-04-04

                elseif (strlen($_POST['CustPhone'])>0){
            $SQL = "SELECT 
                    debtortrans.transno
                FROM debtortrans 
                WHERE debtortrans.transno " . $_POST['CustPhone'] ."
                
                ORDER BY debtortrans.transno DESC";
        }
    } //one of keywords or custcode or custphone was more than a zero length string
    $result = DB_query($SQL,$db,$ErrMsg);
    $ListCount=DB_num_rows($result);

    $ListPageMax=ceil($ListCount/$_SESSION['DisplayRecordsMax']);

    if (isset($_POST['Next'])) {
        if ($_POST['PageOffset'] < $ListPageMax) {
            $_POST['PageOffset'] = $_POST['PageOffset'] + 1;
        }
    }

    if (isset($_POST['Previous'])) {
        if ($_POST['PageOffset'] > 1) {
            $_POST['PageOffset'] = $_POST['PageOffset'] - 1;
        }
    }

	$limit=' limit '.((int)($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']).', '.$_SESSION['DisplayRecordsMax'];

	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL.$limit,$db,$ErrMsg);
	if ($ListCount==1){
		$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'];
		unset($result);
	} elseif ($ListCount==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}
} //end of if search



If (!isset($_POST['Select'])){
	$_POST['Select']="";
}

echo '<BR>';

If ($_POST['Select']!="" OR
	($_SESSION['CustomerID']!=""
	AND !isset($_POST['Keywords'])
	AND !isset($_POST['CustCode'])
	AND !isset($_POST['CustPhone']))) {

	If ($_POST['Select']!=""){
		$SQL = "SELECT debtorsmaster.name, rh_titular.folio FROM debtorsmaster
                LEFT JOIN rh_titular ON debtorsmaster.debtorno = rh_titular.debtorno
                WHERE debtorsmaster.debtorno='" . $_POST['Select'] . "'";
		$_SESSION['CustomerID'] = $_POST['Select'];
	} else {
		$SQL = "SELECT debtorsmaster.name, rh_titular.folio FROM debtorsmaster
                LEFT JOIN rh_titular ON debtorsmaster.debtorno = rh_titular.debtorno
                WHERE debtorsmaster.debtorno='" . $_SESSION['CustomerID'] . "'";
	}

     /*Obtengo Datos del Afiliado*/
    $_2GetAfilData = "SELECT ti.folio, cobranza.cobrador, fa.tipo_membresia
                           FROM rh_titular ti
                           LEFT JOIN rh_cobranza cobranza ON cobranza.folio = ti.folio
                           LEFT JOIN rh_foliosasignados fa ON ti.folio = fa.folio
                           WHERE ti.debtorno = '{$_SESSION['CustomerID']}'";
    $_GetAfilData=DB_query($_2GetAfilData,$db);
    $GetAfilData = DB_fetch_assoc($_GetAfilData);

        /*
         * iJPe
         * realhost
         * 2009-12-01
         *
         * Si se mando elegir el cliente desde rh_WO_Invoice.php, regresarlo a esta pagina
         */
        if (isset($_SESSION['WO']))
        {
            unset($_SESSION['WO']);
            //echo '<BR>' . _('This page is expected to be called after a supplier has been selected');
            echo "<META HTTP-EQUIV='Refresh' CONTENT='0; URL=" . $rootpath . '/rh_WO_Invoice.php?' . SID . "'>";
            exit;
        }

	$ErrMsg = _('The customer name requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);

	if ($myrow=DB_fetch_row($result)){
        $CustomerName = $myrow[0];
        $AfilNo = $myrow[1];
	}
	unset($result);


	//echo '<CENTER><FONT SIZE=3>' .$GetAfilData['tipo_membresia'] . ' - ' . $GetAfilData['folio'] . ' - ' . $_SESSION['CustomerID'] . ' - ' . $CustomerName . '</B> ' . _('has been selected') . '.</FONT><BR><BR>';

	//$_POST['Select'] = NULL;

	
    /*echo '  <TR>
                <TD WIDTH=25% valign="top">';*/
                /* Customer Inquiry Options */
                //echo '<a href="' . $rootpath . '/CustomerInquiryDos.php?CustomerID=' . $_SESSION['CustomerID'] . '">' . _('Customer Transaction Inquiries') . '</a><BR>';
               // echo '<a href="' . $rootpath . '/rh_EdoCuenta.php?CustomerID=' . $_SESSION['CustomerID'] . '">' . _('Balance').' '._('Customer'). '</a><BR>';
                //echo '<a href="' . $rootpath . '/AgedDebtors.php?Customer='. $_SESSION['CustomerID'] . '">' . _('Aged Customer Balances/Overdues Report').'</a><BR>';
                //echo '<a href="' . $rootpath . '/SelectSalesOrder.php?SelectedCustomer=' . $_SESSION['CustomerID'] . '">' . _('Modify Outstanding Sales Orders') . '</a><BR>';
                //echo '<a href="' . $rootpath . '/SelectCompletedOrder.php?SelectedCustomer=' . $_SESSION['CustomerID'] . '">' . _('Order Inquiries') . '</a><BR>';
        // echo '  </TD>
                // <TD WIDTH=25% valign="top">';
                //echo '<a href="' . $rootpath . '/Customers.php?">' . _('Add a New Customer') . '</a><br>';
                //echo '<a href="' . $rootpath . '/Customers.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('Modify Customer Details') . '</a><BR>';
                //echo '<a href="' . $rootpath . '/CustomerBranches.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('Add/Modify/Delete Customer Branches') . '</a><BR>';
                //echo '<a href="' . $rootpath . '/SelectProduct.php">' . _('Special Customer Prices') . '</a><BR>';
                //echo '<a href="' . $rootpath . '/CustEDISetup.php">' . _('Customer EDI Configuration') . '</a>';
                //echo '<a href="' . $rootpath . '/rh_datos_facturacion.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('Datos de Facturaci&oacute;n') . '</a><BR>';
//    echo '</TD><TD WIDTH=25% valign="top">';
//        echo '<a href="' . $rootpath . '/rh_agregar_picture.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('Cargar Foto de Paciente') . '</a><br>';
//        echo '<a href="' . $rootpath . '/rh_navegador_archivos_paciente.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('C&aacute;talogos de Archivos') . '</a><br>';
//        echo '<a href="' . $rootpath . '/rh_fotos_paciente.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('C&aacute;talogos de Fotograf&iacute;as') . '</a><br>';
//        echo '<a href="' . $rootpath . '/rh_consultas_paciente.php?DebtorNo=' . $_SESSION['CustomerID'] . '">' . _('Consultas') . '</a><BR>';
//    echo '</TD>';
        echo '  </td>
            </TR>
        </TABLE><BR></CENTER>';
?>
<center>

</center>


<?php
} /* else {
    echo "<CENTER><TABLE WIDTH=50% BORDER=2><TR><TD class='tableheader'>" . _('Customer Inquiries') . "</TD>
            <TD class='tableheader'>" . _('Customer Maintenance') . "</TD></TR>";

	echo '<TR><TD WIDTH=50%>';

	echo '</TD><TD WIDTH=50%>';
  	if ($_SESSION['SalesmanLogin']==''){
    	echo '<a href="' . $rootpath . '/Customers.php?">' . _('Add a New Customer') . '</a><br>';
    }
	echo '</TD></TR></TABLE><BR></CENTER>';
}*/

?>

<FORM class="footer" ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo $msg; ?></B>
    <TABLE CELLPADDING=3 COLSPAN=4>
        <TR>
           <!-- <TD>
                <B><?php echo _('# Expediente'); ?></B>: <br>
                <?php
                    if (isset($_POST['expediente'])) {
                ?>
                <INPUT TYPE="Text" NAME="expediente" value="<?php echo $_POST['expediente'] ?>" SIZE=15 MAXLENGTH=18>
                <?php } else { ?>
                <INPUT TYPE="Text" NAME="expediente" SIZE=15 MAXLENGTH=18>
                <?php } ?>
            </TD>
            <TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
    <TD>
        <B><?php echo _('Name'); ?></B>: <br />
        <?php
        if (isset($_POST['Keywords'])) {
        ?>
        <INPUT TYPE="Text" NAME="Keywords" value="<?php echo $_POST['Keywords']?>" SIZE=20 MAXLENGTH=25>
        <?php
        } else {
        ?>
        <INPUT TYPE="Text" NAME="Keywords" SIZE=20 MAXLENGTH=25>
        <?php
        }
        ?>
    </TD>

    <TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>-->

    <!--<TD>
        <B><?php echo _('Folio Afiliado'); ?>:</B><br />
        <INPUT TYPE="Text" NAME="AfilFolio" value="<?=$_POST['Folio']?>" style="width: 150px;" >
    </TD>-->

    <!--<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <B><?php echo _('Debtorno'); ?></B>: <br />
        <?php
        if (isset($_POST['CustCode'])) {
        ?>
        <INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
        <?php
        } else {
        ?>
        <INPUT TYPE="Text" NAME="CustCode" style="width: 150px;" >
        <?php
        }
        ?>
    </TD> -->

    

    <TD>
        <B><?php echo _('Número de Transacción'); ?></B>: <br />
        <?php
        if (isset($_POST['CustPhone'])) {
        ?>
        <INPUT TYPE="Text" NAME="CustPhone" value="<?php echo $_POST['CustPhone'] ?>" SIZE=15 MAXLENGTH=18 id="CustPhone">
        <?php
        } else {
        ?>
        <INPUT TYPE="Text" NAME="CustPhone" SIZE=15 MAXLENGTH=18 id="CustPhone">
        <?php
        }
        ?>
    </TD>

    <!-- <TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>

    <TD>
        <B><?php echo _('RFC'); ?></B>: <br />
        <?php
        if (isset($_POST['RFC'])) {
        ?>
        <INPUT TYPE="Text" NAME="RFC" value="<?php echo $_POST['RFC'] ?>" SIZE=15 MAXLENGTH=18>
        <?php
        } else {
        ?>
        <INPUT TYPE="Text" NAME="RFC" SIZE=15 MAXLENGTH=18>
        <?php
        }
        ?>
    </TD> -->

</TR>
</TABLE>
<!--<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>"> $myrow['transno']-->
<script >
    function AbrirFactura()
    {
        var transno = document.getElementById("CustPhone").value;
        if(transno!=null)
        {
            var url = 'PHPJasperXML/sample3.php?isTransportista=0&transno='+transno+'&&afil=true';
            var win = window.open(url, '_blank');
            win.focus();    
        }else{
            alert('Ingrese transno');
        }
        
    }
</script>

<input type="button" value="Mostrar Factura" onclick = "AbrirFactura();"/>
</CENTER>
<style type="text/css">
    .label-success[href], .badge-success[href] {
        background-color: #85a042;
    }
</style>

<?php
echo $_GET['CustPhone'];
if ($_SESSION['SalesmanLogin']!=''){
	prnMsg(_('Your account enables you to see only customers allocated to you'),'warn',_('Note: Sales-person Login'));
}

If (isset($result)) {

if ($ListPageMax >1) {
	echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';

	echo "<SELECT NAME='PageOffset' id='PageOffset' onchange=document.getElementById('PageOffset2').value=this.value>";

    $ListPage=1;
    while($ListPage <= $ListPageMax) {
        if ($ListPage == $_POST['PageOffset']) {
            echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
        } else {
            echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
        }
        $ListPage++;
    }

    echo '</SELECT>
        <INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
        <INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
        <INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
    echo '<P>';
}


    echo '<TABLE class="table table-striped table-bordered table-hover">';
    $TableHeader = '
        <thead>
            <tr>
                <th Class="tableheader">' . _('Code') . '</th>
				<th Class="tableheader">' . _('SocioID') . '</th>
                <th Class="tableheader">' . _('Folio') . '</th>
                <th Class="tableheader">' . _('# Expediente') . '</th>
                <th Class="tableheader">' . _('Titular') . '</th>
                <th Class="tableheader">' . _('Branch') . '</th>
                <th Class="tableheader">' . _('Contacto') . '</th>
                <th Class="tableheader">' . _('RFC') . '</th>
                <th Class="tableheader">' . _('Telefono') . '</th>
            </tr>
        </thead>';

	echo $TableHeader;
  $RowIndex = 0;

//   if (DB_num_rows($result)<>0){
//   	DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
//   }

	while (($myrow=DB_fetch_array($result)) //AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])
	) {

/*
echo "<pre>";
print_r($myrow);
echo "</pre>";*/
		// bowikaxu april 2007 columna de RFC
		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</FONT></td>
    			<td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
                <td><FONT SIZE=1>%s</FONT></td>
			</tr>",
			$myrow["debtorno"],
			$myrow["branchcode"],
            "<a target='_blank' class='badge badge-success' href='modulos/index.php?r=afiliaciones/afiliacion&Folio={$myrow['folio']}'>" . $myrow["folio"] . "</a>",
            $myrow["expediente"],
			$myrow["name"],
            $myrow["brname"],
            $myrow["contactname"],
            $myrow["taxref"],
            $myrow["phoneno"]);

		$j++;
        if ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
            $j=1;
            echo $TableHeader;
        }

    		$RowIndex++;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show
if ($ListPageMax >1) {
	echo "<P>&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';

	echo "<SELECT NAME='PageOffset' id='PageOffset2' onchange=document.getElementById('PageOffset').value=this.value>";

	$ListPage=1;
	while($ListPage <= $ListPageMax) {
		if ($ListPage == $_POST['PageOffset']) {
			echo '<OPTION VALUE=' . $ListPage . ' SELECTED>' . $ListPage . '</OPTION>';
		} else {
			echo '<OPTION VALUE=' . $ListPage . '>' . $ListPage . '</OPTION>';
		}
		$ListPage++;
	}
	echo '</SELECT>
		<INPUT TYPE=SUBMIT NAME="Go" VALUE="' . _('Go') . '">
		<INPUT TYPE=SUBMIT NAME="Previous" VALUE="' . _('Previous') . '">
		<INPUT TYPE=SUBMIT NAME="Next" VALUE="' . _('Next') . '">';
}
//end if results to show
echo '</FORM></CENTER>';

include('includes/footer.inc');
?>
<script language="JavaScript" type="text/javascript">
    //<![CDATA[
            <!--
            document.forms[0].CustCode.select();
            document.forms[0].CustCode.focus();
            //-->
    //]]>
</script>
