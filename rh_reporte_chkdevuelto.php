<?php
$PageSecurity = 2;

include('includes/session.inc');
$title = _('Reporte de cobranza');
include('includes/header.inc');
?>
<script type="text/javascript" src="javascript/jquery-1.6.1.min.js" ></script>
<script src="jscalendar/src/js/jscal2.js"></script>
<script src="jscalendar/src/js/lang/en.js"></script>
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="jscalendar/src/css/steel/steel.css" />
<?

    $SQL = "SELECT bankaccountname,
			bankaccounts.accountcode
		FROM bankaccounts,
			chartmaster
		WHERE bankaccounts.accountcode=chartmaster.accountcode;";
    $ChoferRS = DB_query($SQL,$db);
    while ($rowChof=DB_fetch_array($ChoferRS)) {
        $OPT[$rowChof['accountcode']]=$rowChof['bankaccountname'];
    }

        $Option='';
        $Option.='<option value="%%%%%">Todos</option>';
        foreach($OPT as $key=>$value){
            if($key==$_POST['idbanco']){
                $Option.='<option selected="selected" value="'.$key.'">'.$value.'</option>';
            }else{
                $Option.='<option value="'.$key.'">'.$value.'</option>';
            }
        }


	echo '<center>
            <br />
            <h2>Filtros:</h2>
			<form method="post" id="frmFiltro" >
					<table>
					<tr>
							<td>Fecha Inicial: </td> <td> <input type="text" name="fecha" id="fecha" value="'.(isset($_POST['fecha'])?$_POST['fecha']:date('Y-m-d')).'"  /> </td>
					</tr>
					<tr>
							<td>Fecha Final: </td> <td> <input type="text" name="fechafin" id="fechafin" value="'.(isset($_POST['fechafin'])?$_POST['fechafin']:date('Y-m-d')).'"  /> </td>
					</tr>
					<tr>
			            <td>Banco: </td>
						<td> <select name="idbanco"> '.$Option.' </select> </td>
					</tr>
					<!---<tr>
						<td>Cliente: </td>
						<td> <select name="debtorno"> '.$Option3.' </select> </td>
					</tr>  -->
					<tr>
						<td colspan="2"><center>
                        <input type="button" name="ReportView"  value="Ver" style="width:80px;" onclick="nongoPDF();" />
                        <input type="submit" name="ReportView" value="Ver" id="noPDF" style="visibility:hidden; display:none;"/>
                        </center> </td>
					</tr>
			</table></form>
		</center>';

	if (isset($_POST['ReportView'])||(isset($_POST['sndFrm'])||isset($_POST['sndFrm2']))) {
        $DSEM = date(N, strtotime($_POST['fecha']));

    $cliente='';
    if(isset($_POST['debtorno'])&&($_POST['debtorno']!='%%%%%')){
        $cliente=" and debtorsmaster.debtorno='".$_POST['debtorno']."'";
    }

    $banco='';
    if(isset($_POST['idbanco'])&&($_POST['idbanco']!='%%%%%')){
        $banco=" AND banktrans.bankact='".$_POST['idbanco']."'";
    }



	$SQL = "select
               debtortrans.transno,
               debtorsmaster.debtorno,
               debtorsmaster.name,
               debtortrans.trandate as fecha,
               debtortrans.ovamount as total,
               accountname
            from
                debtortrans
                    join banktrans on debtortrans.transno = banktrans.transno and  debtortrans.type = banktrans.type
                    join debtorsmaster on debtortrans.debtorno = debtorsmaster.debtorno
                    join chartmaster on banktrans.bankact=chartmaster.accountcode
            where
                debtortrans.type=12
                ".$banco."
                AND date(debtortrans.trandate)>='".$_POST['fecha']."' and date(debtortrans.trandate)<='".$_POST['fechafin']."'
                AND debtortrans.reference like '%Cheque devuelto%'
            order by debtorsmaster.debtorno,debtortrans.trandate";

	$ErrMsg = _('No orders or quotations were returned by the SQL because');
	$SalesOrdersResult = DB_query($SQL,$db,$ErrMsg);
	}



	/*show a table of the orders returned by the SQL */

	echo '<TABLE CELLPADDING=2 COLSPAN=7 WIDTH=100%>';

	if (true){
		$tableheader = "<TR>
				<TD class='tableheader'>" . _('codigo') . "</TD>
				<TD class='tableheader'>" . _('Customer') . "</TD>
				<TD class='tableheader'>" . _('Transaccion') . "</TD>
                <TD class='tableheader'>" . _('Banco') . "</TD>
				<TD class='tableheader'>" . _('Fecha') . "</TD>
				<TD class='tableheader'>" . _('Monto') . "</TD>
                </TR>";
	}
	
	echo $tableheader;

	$j = 1;
	$k=0;

$EOF=false;
$firstLoop=true;
$obj=1;
while((DB_num_rows($SalesOrdersResult)>0)&&(!$EOF)){
  if($firstLoop&&(($myrow=DB_fetch_array($SalesOrdersResult))==false)){
    $EOF=true;
  }
  $firstLoop=false;
   if ($k==1){
		    $color= '#CCCCCC';
			$k=0;
		} else {
		    $color= '#EEEEEE';
			$k++;
		}
        echo "<tr bgcolor='$color'>";

		$Fecha = ConvertSQLDate($myrow['fecha']);
		$Total = number_format($myrow['total'],2);


		if (true){
			printf("<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				</tr>",
				$myrow['debtorno'],
				$myrow['name'],
				$myrow['transno'],
				$myrow['accountname'],
				$Fecha,
				$Total);
		}

		$j++;
		If ($j == 12){
			$j=1;
			echo $tableheader;
		}

  if(($myrow=DB_fetch_array($SalesOrdersResult))==false){
    $EOF=true;
  }
   $obj++;
 }
	echo '</TABLE>';

echo "<script type=\"text/javascript\">//<![CDATA[
      var cal2 = Calendar.setup({
          onSelect: function(cal2) { cal2.hide() },
          showTime: false
      });
      var cal = Calendar.setup({
          onSelect: function(cal) { cal.hide() },
          showTime: false
      });

      cal2.setLanguage('es');
      cal2.manageFields(\"fecha\", \"fecha\", \"%Y-%m-%d\");

      cal.setLanguage('es');
      cal.manageFields(\"fechafin\", \"fechafin\", \"%Y-%m-%d\");
    //]]>
</script>";
?>

<script>
    function nongoPDF(){
        document.getElementById('frmFiltro').action="";
        document.getElementById('frmFiltro').target="_self";
        document.getElementById('noPDF').click();
    }
</script>
<?php
include('includes/footer.inc');
?>