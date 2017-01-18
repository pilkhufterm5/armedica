<?php

/* $Revision: 1.10 $ */
// andres amaya diaz
$PageSecurity = 2;

include('includes/DefineCartClass2.php');
include('includes/DefineSerialItems.php');
include('includes/session.inc');

$title = _('Ver Remisiones');

include('includes/header.inc');
include('includes/SQL_CommonFunctions.inc');
include('includes/FreightCalculation.inc');
include('includes/GetSalesTransGLCodes.inc');

include('XMLFacturacionElectronica/utils/File.php');
include('rh_j_cfdFunctions.php');


if(isset($_POST["updateCliente"])){
    $_POST['Select'] = $_POST["cliente"];
    $Select = explode('$',$_POST["cliente"]);
    //echo $_POST["cliente"];
    $remisiones=$_POST["remisiones"];
    $NoRem = count($_POST["remisiones"]);
    for($i=0;$i<$NoRem;$i++){
        $sql="update debtortrans set debtorno='".$Select[0]."', branchcode='".$Select[1]."' where transno='".$remisiones[$i]."' and type=20000";
        DB_query($sql,$db);
        $sql="SELECT  debtortrans.order_ FROM rh_invoiceshipment, debtortrans, salesorders WHERE debtortrans.transno = rh_invoiceshipment.Shipment AND rh_invoiceshipment.Shipment = '".$remisiones[$i]."' AND debtortrans.type = 20000 and debtortrans.order_=salesorders.orderno GROUP BY rh_invoiceshipment.Shipment";
    	$result = DB_query($sql,$db);
		$myrow=DB_fetch_array($result);
        $sql="update salesorders set debtorno='".$Select[0]."',branchcode='".$Select[1]."' where salesorders.orderno='".$myrow[0]."'";
        DB_query($sql,$db);
    }
}

// SELECCIONAR EL CLIENTE PARA VER SUS REMISIONES
$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
                    custbranch.branchcode,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

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
                    custbranch.branchcode,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

		} elseif (strlen($_POST['CustCode'])>0){

			$_POST['CustCode'] = strtoupper($_POST['CustCode']);

			$SQL = "SELECT debtorsmaster.debtorno,
                    custbranch.branchcode,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.debtorno " . LIKE  . " '%" . $_POST['CustCode'] . "%'";
		}
	} //one of keywords or custcode was more than a zero length string
	$ErrMsg = _('The searched customer records requested cannot be retrieved because');
	$result = DB_query($SQL,$db,$ErrMsg);
	if (DB_num_rows($result)==1){
		/*$myrow=DB_fetch_array($result);
		$_POST['Select'] = $myrow['debtorno'].'$'.$myrow['branchcode'];
		unset($result);*/
        //Comentado por seguridad uso de KISS 
	} elseif (DB_num_rows($result)==0){
		prnMsg(_('No customer records contain the selected text') . ' - ' . _('please alter your search criteria and try again'),'info');
	}$msg="";
if (!isset($_SESSION['CustomerID'])){ //initialise if not already done
	$_SESSION['CustomerID']="";
}

if (!isset($_POST['PageOffset'])) {
  $_POST['PageOffset'] = 1;
} else {
  if ($_POST['PageOffset']==0) {
    $_POST['PageOffset'] = 1;
  }
}

if (isset($_POST['Search']) OR isset($_POST['Go']) OR isset($_POST['Next']) OR isset($_POST['Previous'])){
	if (isset($_POST['Search'])){
		$_POST['PageOffset'] = 1;
	}
	If ($_POST['Keywords'] AND $_POST['CustCode']) {
		$msg=_('Customer name keywords have been used in preference to the customer code extract entered') . '.';
		$_POST['Keywords'] = strtoupper($_POST['Keywords']);
	}
	If ($_POST['Keywords']=="" AND $_POST['CustCode']=="") {
		//$msg=_('At least one Customer Name keyword OR an extract of a Customer Code must be entered for the search');
		$SQL= "SELECT debtorsmaster.debtorno,
                    custbranch.branchcode,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno";
	} else {
		If (strlen($_POST['Keywords'])>0) {

			$_POST['Keywords'] = strtoupper($_POST['Keywords']);

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
                    custbranch.branchcode,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE debtorsmaster.name " . LIKE . " '$SearchString'";

}
		}
	}
}

           $Select = explode('$',$_POST['Select']);
           $selectCliente='';
           $sql_ = "Select debtorno, name FROM debtorsmaster  where name!='MOSTRADOR' and debtorno='".$Select[0]."' order by name ASC";
           if(isset($_POST['cliente'])){
                $sql_ = "Select debtorno, name FROM debtorsmaster  where name!='MOSTRADOR' and debtorno='".$Select[0]."' order by name ASC";
           }
           $result1 = DB_query($sql_,$db);
           while ($myrow1 = DB_fetch_array($result1)){
                    $selectCliente.= $myrow1['debtorno'].' '.$myrow1['name'];
           }
if(isset($_POST['Select'])||isset($_POST['POSubmit'])){
            if(isset($_POST['cliente'])){
                $Select = explode('$',$_POST['cliente']);
            }else{
                $Select = explode('$',$_POST['Select']);
            }
           $selectCliente='';
           $sql_ = "Select debtorno, name FROM debtorsmaster  where name!='MOSTRADOR' and debtorno='".$Select[0]."' order by name ASC";
           if(isset($_POST['cliente'])){
                $sql_ = "Select debtorno, name FROM debtorsmaster  where name!='MOSTRADOR' and debtorno='".$Select[0]."' order by name ASC";
           }
           $result1 = DB_query($sql_,$db);
           while ($myrow1 = DB_fetch_array($result1)){
                    $selectCliente.= $myrow1['debtorno'].' '.$myrow1['name'];
           }

	    echo "<FORM ACTION=".$_SERVER['PHP_SELF']. " METHOD=POST>";
        if(isset($_POST['cliente'])){
            echo "<input type='hidden' name='cliente' value='".$_POST['cliente']."' />";
        }else{
            echo "<input type='hidden' name='cliente' value='".$_POST['Select']."' />";
        }
        echo "<center><H2><B>Ticket's Venta publico General </B></H2><br />";
        echo "<B>Cliente Seleccionado:".$selectCliente." </B></center>";
        $sql2='SELECT
                        rh_pos_terminales.Terminal
  			            , custbranch.branchcode
                    FROM
                        locations
                        INNER JOIN rh_pos_terminales
                            ON (locations.loccode = rh_pos_terminales.Sucursal)
                        INNER JOIN debtorsmaster
                            ON (debtorsmaster.debtorno = rh_pos_terminales.debtorno)
			            INNER JOIN custbranch
                            ON (debtorsmaster.debtorno = custbranch.debtorno);';

        echo '<center><table><tr><td colspan="2" align="center"><h2>Punto de venta</h2></td></tr>
        <tr><td>Sucursal</td><td><select name="PO"><option value="00">Seleccione uno</option>';
            $mostrador = DB_query($sql2,$db);
            while($myrow=DB_fetch_array($mostrador)){
                echo '<option '.($myrow['branchcode']==$_POST['PO']?'selected=selected':'').' value="'.$myrow['branchcode'].'">'.$myrow['Terminal'].'</option>';
            }
        echo '</select></td></tr>
        <tr><td colspan="2" align="center"><input type="submit" name="POSubmit"/></td></tr>
        </table></center><BR />';
        //echo "<br /><br /><center><table><tr><td>Tickets que se facturaran a".$_POST['Select']."</td></tr></table></center><br /><br />";

        if(isset($_POST['PO'])&&($_POST['PO']!='00')){
			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    debtorsmaster.taxref
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE ((custbranch.branchcode='".$_POST['PO']."') AND (debtorsmaster.taxref = 'XAXX010101000' or debtorsmaster.taxref = 'XEXX010101000') and (debtorsmaster.name like 'MOSTRADOR%'))" ;
        }else{
 			$SQL = "SELECT debtorsmaster.debtorno,
					debtorsmaster.name,
					custbranch.brname,
					custbranch.contactname,
					custbranch.phoneno,
					custbranch.faxno,
                    debtorsmaster.taxref
				FROM debtorsmaster LEFT JOIN custbranch
					ON debtorsmaster.debtorno = custbranch.debtorno
				WHERE ((debtorsmaster.taxref = 'XAXX010101000' or debtorsmaster.taxref = 'XEXX010101000') and (debtorsmaster.name like 'MOSTRADOR%'))" ;

        }
            $mostrador = DB_query($SQL,$db);
            $Mot='';
            while($myrow=DB_fetch_array($mostrador)){
                $Mot[]="'".$myrow["debtorno"]."'";
            }
            $mostrador = implode(',',$Mot);

// TERMINA SELECCIONAR CLIENTE PARA VER REMISIONES
		$SQL = "SELECT debtortrans.transno,
					debtortrans.order_,
					debtortrans.ovamount AS ttot,
					debtortrans.debtorno,
					debtortrans.type,
					debtorsmaster.name,
					debtorsmaster.address1,
					custbranch.brname,
					rh_invoiceshipment.Invoice,
					rh_invoiceshipment.Shipment,
					rh_invoiceshipment.Fecha,
					rh_invoiceshipment.Facturado,
					rh_invoiceshipment.type AS RType,
					debtortrans.ovamount AS ordervalue,
					debtortrans.ovfreight AS orderfreight
				FROM debtortrans,  
					debtorsmaster,
					custbranch,
					rh_invoiceshipment
				WHERE rh_invoiceshipment.Shipment = debtortrans.transno
				AND rh_invoiceshipment.Facturado = 0
				AND debtortrans.type = 20000
                AND  debtorsmaster.debtorno = debtortrans.debtorno
                AND  debtortrans.debtorno = custbranch.debtorno
				AND debtortrans.rh_status != 'C'
				AND debtorsmaster.debtorno in (".$mostrador .")
				GROUP BY debtortrans.transno
				ORDER BY rh_invoiceshipment.Shipment";
				// debtortrans.transno = rh_invoiceshipment.Shipment
// bowikaxu realhost sept 2007 - no mostrar remisiones canceladas
		$ErrMsg =_('CRITICAL ERROR') . '! ' . _('NOTE DOWN THIS ERROR AND SEEK ASSISTANCE') . ': ' . _('Imposible obtener valores de remisiones');
		$DbgMsg = _('Fallo el query de la base de datos');
 		$RemOrders = DB_query($SQL,$db,$ErrMsg,$DbgMsg,true);
/*show a table of the orders returned by the SQL */

	echo '<TABLE CELLPADDING=2 COLSPAN=6 WIDTH=100%>';

	$tableheader = "<TR><TD class='tableheader' size='2'>" . _('Facturar') . "</TD>
			<TD class='tableheader'>".('Rem')." #</TD>
			<TD class='tableheader'>" . _('Debtor') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
			<TD class='tableheader'>" . _('Address') . " #</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
			<TD class='tableheader'>" . _('Tipo') . "</TD>
			<TD class='tableheader'>" . _('Total') . "</TD></TR>";

	echo $tableheader;

	$j = 1;
	$k=0; //row colour counter
	while ($myrow=DB_fetch_array($RemOrders)) {


		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		$ViewPage = $rootpath . '/rh_PDFRemGde.php?' .SID . '&FromTransNo=' . $myrow['transno'].'&InvOrCredit=Invoice';
		$FomatedFecha = ConvertSQLDate($myrow['Fecha']);

		$Sub = $myrow['ordervalue'] + $myrow['orderfreight'];
		$FormatedOrderValue = number_format($Sub,2);
		if($estado==0){
			if($myrow['RType']==0){
				$RType = 'Normal';
			}else if($myrow['RType']==1){
				$RType = 'Muestra';
			}else if($myrow['RType']==2){
				$RType = 'Punto de Venta';
			}
        	printf("<td><input type='checkbox' name='remisiones[]' value='%s'></input></td>
			<td><A target='_blank' HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['transno'],
			$ViewPage,
			$myrow['transno'],
			$myrow['debtorno'], 
			$myrow['brname'],
			$myrow['address1'],
			$myrow['Fecha'],
			$RType,
			"$ ".number_format($myrow['ttot'],2));
		} else {
			printf("<td><input type='checkbox' name='remisiones[]' value='%s' CHECKED></input></td>
			<td><A target='_blank' HREF='%s'>%s</A></td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td>%s</td>
			<td ALIGN=RIGHT>%s</td>
			</tr>",
			$myrow['transno'], 
			$ViewPage, 
			$myrow['transno'], 
			$myrow['debtorno'], 
			$myrow['brname'], 
			$myrow['address1'],
			$myrow['Fecha'],  
			"$ ".$FormatedOrderValue);
		}
		
		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}
     }
	echo '</TABLE>';
	?>
	<input type="submit" name='updateCliente' value="Seleccionar Tickets"></input>
	</FORM><BR><BR>
<?php }?>

<FORM ACTION="<?php echo $_SERVER['PHP_SELF'] . '?' . SID; ?>" METHOD=POST>
<CENTER>
<B><?php echo "<H2><B>Seleccione el cliente para asignar Ticket's </B></H2>".$msg; ?></B>
<TABLE CELLPADDING=3 COLSPAN=4>
<TR>
<TD><?php echo _('Text in the'); ?> <B><?php echo _('name'); ?></B>:</TD>
<TD>
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
<TD><FONT SIZE=3><B><?php echo _('OR'); ?></B></FONT></TD>
<TD><?php echo _('Text extract in the customer'); ?> <B><?php echo _('code'); ?></B>:</TD>
<TD>
<?php
if (isset($_POST['CustCode'])) {
?>
<INPUT TYPE="Text" NAME="CustCode" value="<?php echo $_POST['CustCode'] ?>" SIZE=15 MAXLENGTH=18>
<?php
} else {
?>
<INPUT TYPE="Text" NAME="CustCode" SIZE=15 MAXLENGTH=18>
<?php
}
?>
</TD>
</TR>
</TABLE>
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Show All'); ?>">
<INPUT TYPE=SUBMIT NAME="Search" VALUE="<?php echo _('Search Now'); ?>">
<INPUT TYPE=SUBMIT ACTION=RESET VALUE="<?php echo _('Reset'); ?>"></CENTER>
<?php
If (isset($result)) {
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

  echo "&nbsp;&nbsp;" . $_POST['PageOffset'] . ' ' . _('of') . ' ' . $ListPageMax . ' ' . _('pages') . '. ' . _('Go to Page') . ': ';
?>

  <select name="PageOffset">

<?php
  $ListPage=1;
  while($ListPage<=$ListPageMax) {
	  if ($ListPage==$_POST['PageOffset']) {
?>

  		<option value=<?php echo($ListPage); ?> selected><?php echo($ListPage); ?></option>

<?php
	  } else {
?>

		  <option value=<?php echo($ListPage); ?>><?php echo($ListPage); ?></option>

<?php
	  }
	  $ListPage=$ListPage+1;
  }
?>

  </select>
  <INPUT TYPE=SUBMIT NAME="Go" VALUE="<?php echo _('Go'); ?>">
  <INPUT TYPE=SUBMIT NAME="Previous" VALUE="<?php echo _('Previous'); ?>">
  <INPUT TYPE=SUBMIT NAME="Next" VALUE="<?php echo _('Next'); ?>">

<?php

  echo '<BR><BR>';

	echo '<TABLE CELLPADDING=2 COLSPAN=7 BORDER=2>';
	$TableHeader = '<TR>
				<TD Class="tableheader">' . _('Code') . '</TD>
				<TD Class="tableheader">' . _('Customer Name') . '</TD>
				<TD Class="tableheader">' . _('Branch') . '</TD>
				<TD Class="tableheader">' . _('Contact') . '</TD>
				<TD Class="tableheader">' . _('Phone') . '</TD>
				<TD Class="tableheader">' . _('Fax') . '</TD>
			</TR>';

	echo $TableHeader;
	$j = 1;
	$k = 0; //row counter to determine background colour
  $RowIndex = 0;

  if (DB_num_rows($result)<>0){
  	DB_data_seek($result, ($_POST['PageOffset']-1)*$_SESSION['DisplayRecordsMax']);
  }

	while (($myrow=DB_fetch_array($result)) AND ($RowIndex <> $_SESSION['DisplayRecordsMax'])) {

		if ($k==1){
			echo '<tr bgcolor="#CCCCCC">';
			$k=0;
		} else {
			echo '<tr bgcolor="#EEEEEE">';
			$k=1;
		}

		printf("<td><FONT SIZE=1><INPUT TYPE=SUBMIT NAME='Select' VALUE='%s'</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td>
			<td><FONT SIZE=1>%s</FONT></td></tr>",
			$myrow["debtorno"].'$'.$myrow['branchcode'],
			$myrow["name"],
			$myrow["brname"],
			$myrow["contactname"],
			$myrow["phoneno"],
			$myrow["faxno"]);

		$j++;
		If ($j == 11 AND ($RowIndex+1 != $_SESSION['DisplayRecordsMax'])){
			$j=1;
			echo $TableHeader;
		}

    $RowIndex = $RowIndex + 1;
//end of page full new headings if
	}
//end of while loop

	echo '</TABLE>';

}
//end if results to show
echo '</FORM></CENTER>';
// </FORM>
echo "<br>";

$ii = 0;
$NoRem = count($remisiones);

while($ii < $NoRem){

//print_r($_SESSION[$remisiones[$ii]]);
//echo "<br>";

$ii++;

}

include('includes/footer.inc');

?>