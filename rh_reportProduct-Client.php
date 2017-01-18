<?php

/* $Revision: 273 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Reporte Venta de Producto');
include('includes/header.inc');

if (isset($_GET['product']))
{
    $_POST['product'] = $_GET['product'];
}



echo "<FORM NAME='menu' ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Producto') . ':</TD><TD><INPUT TYPE=TEXT NAME="product" MAXLENGTH=30 VALUE=' . $_POST['product'] . '></TD></TR>';

echo "<TR><TD>"._('Location').": </TD><TD><SELECT NAME='location'>";
echo "<OPTION Value='All'>"._('Show All');

$sql = "SELECT loccode, locationname FROM locations ORDER BY locationname";
$reslocations = DB_query($sql,$db);

while($loc = DB_fetch_array($reslocations)){

	if($_POST['location']==$loc['loccode']){
		echo "<OPTION SELECTED VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}else {
		echo "<OPTION VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}

}

echo "</SELECT></TD></TR>";

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('From') . ":</TD><TD><INPUT TYPE=TEXT NAME='fromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('To') . ":</TD><TD><INPUT TYPE=TEXT NAME='toDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '></TD>';
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='view' VALUE='" . _('Ver Ventas a Clientes') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

echo '<CENTER>';

if (isset($_POST['view']))
{
    $productoID = $_POST['product'];

    $SQL_FromDate = FormatDateForSQL($_POST['fromDate']);
    $SQL_ToDate = FormatDateForSQL($_POST['toDate']);

    $sqlVer = "SELECT stockid FROM stockmaster WHERE stockid = '".$productoID."'";
    $resVer = DB_query($sqlVer,$db);

    if (DB_num_rows($resVer) > 0)
    {

        $loccode = '';

        if ($_POST['location'] != 'All'){
            $loccode = " AND stockmoves.loccode = '".$_POST['location']."'";
        }

        $sqlGetMov = "SELECT locations.rh_serie, rh_invoicesreference.extinvoice, debtorsmaster.debtorno, debtorsmaster.name, stockmoves.loccode, systypes.typename, stockmoves.transno, stockmoves.trandate, (stockmoves.qty*-1) AS qty
                      FROM stockmoves INNER JOIN systypes ON stockmoves.type = systypes.typeid INNER JOIN debtorsmaster ON
                      stockmoves.debtorno = debtorsmaster.debtorno LEFT JOIN rh_invoicesreference ON stockmoves.transno = rh_invoicesreference.intinvoice AND stockmoves.type = 10
                      LEFT JOIN locations ON stockmoves.loccode = locations.loccode AND stockmoves.type = 10
                      WHERE stockid = '".$productoID."' AND stockmoves.trandate >='" . $SQL_FromDate . "' AND stockmoves.trandate <= '" . $SQL_ToDate . "'
                      AND stockmoves.qty != 0 ".$loccode." ORDER BY stockmoves.type, rh_invoicesreference.extinvoice";
       $resGetMov = DB_query($sqlGetMov, $db);

       if (DB_num_rows($resGetMov) > 0)
       {
            $tableheader = "<TR>
                            <TD class='tableheader'>" . _('Codigo Cliente') . "</TD>
                            <TD class='tableheader'>" . _('Nombre') . "</TD>
                            <TD class='tableheader'>" . _('Almacen') . "</TD>
                            <TD class='tableheader'>" . _('Tipo de Movimiento') . "</TD>
                            <TD class='tableheader'>" . _('# Movimiento') . "</TD>
                            <TD class='tableheader'>" . _('Fecha') . "</TD>
                            <TD class='tableheader'>" . _('Cantidad') . "</TD>
                            </TR>";

            echo "<br>";

            echo "<TABLE>";
            echo $tableheader;
            $k=0;
            while($myrow = DB_fetch_array($resGetMov)){

                    if ($k==1){
                            echo "<tr bgcolor='#CCCCCC'>";
                            $k=0;
                    } else {
                            echo "<tr bgcolor='#EEEEEE'>";
                            $k=1;
                    }

                    echo "<TD>".$myrow['debtorno']."</TD>
                        <TD>".$myrow['name']."</TD>
                        <TD>".$myrow['loccode']."</TD>
                        <TD>".$myrow['typename']."</TD>
                        <TD>".$myrow['rh_serie'].$myrow['extinvoice']."(".$myrow['transno'].")</TD>
                        <TD>".ConvertSQLDate($myrow['trandate'])."</TD>
                        <TD align='right'>".$myrow['qty']."</TD>
                        </TR>";
            }
            echo "</TABLE>";
       }
       else
       {
           prnMsg("No se han encontrado resultados para el producto ".$productoID." en el rango de fechas solicitado","info");
       }
    }
    else
    {
        //No existe el producto con el id exacto
        if ($_POST['product'] == '')
        {
            prnMsg('Favor de ingresar el codigo del producto','error');
            include('includes/footer.inc');
            exit();
        }

        prnMsg('No se ha encontrado el producto a buscar, a continuacion se muestran posibles busquedas','info');

        $sqlVer = "SELECT stockid, description FROM stockmaster WHERE stockid like '%".$productoID."%' ORDER BY stockid";
        $resVer = DB_query($sqlVer,$db);
        
	$tableheader = "<TR>
			<TD class='tableheader'>" . _('Producto') . "</TD>
			<TD class='tableheader'>" . _('Descripcion') . "</TD>
			</TR>";

        echo "<br>";

        echo "<TABLE>";
	echo $tableheader;
	$k=0;
	while($myrow = DB_fetch_array($resVer)){

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}

		echo "<TD><A HREF=\"rh_reportProduct-Client.php?product=".$myrow['stockid']."\">".$myrow['stockid']."</TD>
                    <TD>".$myrow['description']."</TD>
                    </TR>";
	}
	echo "</TABLE>";
    }

}

include('includes/footer.inc');

?>