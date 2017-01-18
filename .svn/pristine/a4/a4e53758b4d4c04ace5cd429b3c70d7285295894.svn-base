<?php
/*
 * iJPe
 * realhost
 * 2010-01-07
 */

$PageSecurity=1;

include('includes/session.inc');

$title = _('Reporte por Vendedor');
include('includes/header.inc');

echo "<BR><CENTER><B>"._('Reporte por Vendedor')."</B></CENTER><BR>";
echo "<FORM NAME='menu' onSubmit='return validar()' METHOD='POST' ACTION=" . $_SERVER['PHP_SELF'] . '?' . SID . '>';
//Jaime, Realhost, 21/Ene/10 10:31
echo('<input type="hidden" id="salesmanJ" name="salesman" />');
//Termina Jaime, Realhost, 21/Ene/10 10:31
echo "<CENTER><TABLE><TR>";

$sql = "SELECT salesmancode, salesmanname FROM salesman GROUP BY salesmanname";
$res = DB_query($sql,$db,'Imposible determinar vendedores');
echo "<TD>"._('Vendedores(as)').": </TD><TD>";
echo "<SELECT NAME='vendedor'>
		<OPTION VALUE='todos' SELECTED>Todos</OPTION>";
while ($user = DB_fetch_array($res)){
	if ($_POST['vendedor']==$user['salesmancode']){
		$sel = 'SELECTED';
	}else{$sel = '';}
		
	echo "<OPTION VALUE='".$user['salesmancode']."'$sel>".$user['salesmanname']." (".$user['salesmancode'].") </OPTION>";
	
}
echo "</SELECT></TD></TR>";

if (isset($_POST['Fdesde'])){
	$fdesde = $_POST['Fdesde'];
}else{
    //Jaime, Realhost, 22/Ene/10 16:37
	$fdesde = date($_SESSION['DefaultDateFormat']);
        $fdesde = '01' . substr($fdesde, 2);
    //Jaime, Realhost, 22/Ene/10 16:37
}

if (isset($_POST['Fhasta'])){
	$fhasta = $_POST['Fhasta'];
}else{
	$fhasta = date($_SESSION['DefaultDateFormat']);
}

//iJPe Filtrado por sucursal

$sql = "SELECT locationname, loccode FROM rh_locations";
$res = DB_query($sql,$db,'Imposible determinar sucursales');
echo "<TD>"._('Sucursal').": </TD><TD>";
echo "<SELECT NAME='sucursal'>
		<OPTION VALUE='todos' SELECTED>Todos</OPTION>";
while ($user = DB_fetch_array($res)){
	if ($_POST['sucursal']==$user['loccode']){
		$sel = 'SELECTED';
	}else{$sel = '';}

	echo "<OPTION VALUE='".$user['loccode']."'$sel>".$user['locationname']." (".$user['loccode'].") </OPTION>";

}
echo "</SELECT></TD></TR>";


//Jaime, Realhost, 20/Ene/10 11:55
//Se agrego la propiedad "id" a el textbox Fdesde y Fhasta
echo "<tr><td>"._('Fecha desde:')."</td><td><input type='text' class='date' name='Fdesde' id='Fdesde' value='$fdesde'></td></tr>";
echo "<tr><td>"._('Fecha hasta:')."</td><td><input type='text' class='date' name='Fhasta' id='Fhasta' value='$fhasta'></td></tr>";
//Termina Jaime, Realhost, 20/Ene/10 11:55
echo "<tr><td>";
echo "Ordenar por: </td><td><select name='order'>";

if ($_POST['order']=='E'){
    echo "<option value='E' SELECTED>N&uacute;mero de Factura Externa</option>";
}else{
    echo "<option value='E'>N&uacute;mero de Factura Externa</option>";
}
if ($_POST['order']=='I'){
    echo "<option value='I' SELECTED>N&uacute;mero de Factura Interna</option>";
}else{
    echo "<option value='I'>N&uacute;mero de Factura Interna</option>";
}
if ($_POST['order']=='F'){
    echo "<option value='F' SELECTED>Fecha</option>";
}else{
    echo "<option value='F'>Fecha</option>";
}
echo "</select>";
echo "</td></tr>";
echo "</table>";
echo "<br>";
echo "<input type='submit' name='ver' value='Ver reporte'>";
echo "</form>";
//Jaime, Realhost, 21/Ene/10 11:55
$salesmanJ = $_REQUEST["salesman"];
if (isset($_POST['ver']) || strlen($salesmanJ)>0)
//Terminar Jaime, Realhost, 21/Ene/10 11:55
{
        //Jaime, Realhost, 20/Ene/10 15:54
        //Se agrego un join con systypes, se muestra la columna systypes.typename, se agrego la condicion "or debtortrans.type=11" en la segunda sentencia select, se modifico el order by para que funcione con el union
        if(strlen($salesmanJ)>0 || $_POST['vendedor']!="todos"){//2
            echo("<br><hr>");
        //Termina Jaime, Realhost, 20/Ene/10 15:54
            $rh_total = 0;

            if($_POST['vendedor']!="todos")
                $salesmanJ = $_POST['vendedor'];
            //$vendedor = $_POST['vendedor'];
            //if($vendedor != "todos"){


            echo "<table><tr>";
            if ($_POST['vendedor']=='todos'){
                    echo "<td class='tableheader'>"._("Vendedor")."</td>";
            }
            //Jaime, Realhost, 20/Ene/10 16:28
            //Se cambio Factura por "Tipo de movimiento"
            echo "<td class='tableheader'>"._("Tipo de movimiento")."</td>";
            //Termina Jaime, Realhost, 20/Ene/10 16:28
            echo "<td class='tableheader'>"._("# Interno")."</td>";
            echo "<td class='tableheader'>"._("# Externo")."</td>";
            echo "<td class='tableheader'>"._("Fecha")."</td>";
            echo "<td class='tableheader'>"._("C&oacute;digo Cliente")."</td>";
            echo "<td class='tableheader'>"._("Nombre Cliente")."</td>";
            //Jaime, Realhost, 22/Ene/10 16:41
            //echo "<td class='tableheader'>"._("Nombre de la Sucursal")."</td>";
            //Termina Jaime, Realhost, 22/Ene/10 16:41
            echo "<td class='tableheader'>"._("Monto")."</td>";
            echo "</tr>";

            $salesman = $_POST['vendedor']!='todos'? " and custbranch.salesman = '".$_POST['vendedor']."'":'';

            /*$sqlR = "SELECT custbranch.salesman, debtortrans.transno, debtortrans.ovamount AS total, debtortrans.debtorno, debtorsmaster.name, custbranch.brname,
            debtortrans.trandate, rh_invoiceshipment.Facturado, rh_invoiceshipment.type	FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno inner join custbranch
            ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode inner join rh_invoiceshipment ON rh_invoiceshipment.Shipment = debtortrans.transno
            WHERE ".$salesman." rh_invoiceshipment.Facturado = 0 AND debtortrans.type = 20000 AND debtortrans.rh_status != 'C' AND (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59')
            GROUP BY debtortrans.transno ORDER BY rh_invoiceshipment.Shipment";

            $resR = DB_query($sqlR, $db);
            $nomTran = "Remisi&oacute;n";
            $k=0;

            while ($rowR = DB_fetch_array($resR))
            {
                    if ($k == 1){
                            echo "<TR BGCOLOR='#CCCCCC'>";
                            $k = 0;
                    } else {
                            echo "<TR BGCOLOR='#EEEEEE'>";
                            $k = 1;
                    }

                    if ($_POST['vendedor']=='todos'){
                            echo "<td>".$rowR['salesman']."</td>";
                    }
                    echo "<td>".$nomTran."</td>";
                    echo "<td>".$rowR['transno']."</td>";
                    echo "<td>".date('Y-m-d',strtotime($rowR['trandate']))."</td>";
                    echo "<td>".$rowR['debtorno']."</td>";
                    echo "<td>".$rowR['name']."</td>";
                    echo "<td>".$rowR['brname']."</td>";
                    echo "<td align='right'>".number_format($rowR['total'],2)."</td>";
                    echo "</tr>";

                    $rh_total += $rowR['total'];
            }
            */

            //Ordenar por:
            //Jaime, Realhost, 20/Ene/10 17:54
            //Se quitaron el nombre de las tablas en los campos para poder usar "order by" despues del union acontinuacion...
            if ($_POST['order']=='I')
            {
                    $order = 'transno';
            }else{
                    if ($_POST['order']=='E')
                    {
                            $order = 'extinvoice';
                    }else{
                            $order = 'trandate';
                    }
            }
           
            //iJPe 2010-03-10 Modificacion para filtrar por sucursal
            if ($_POST['sucursal'] != 'todos')
            {
                $condSucFac = "rh_invoicesreference.loccode = '".$_POST['sucursal']."' AND";
                $condSucNC = "rh_crednotesreference.loccode = '".$_POST['sucursal']."' AND";
            }
            else
            {
                $condSucFac = '';
                $condSucNC =  '';
            }


            //Facturas
            $sqlV = "SELECT debtortrans.id, systypes.typeid, systypes.typename, rh_invoicesreference.extinvoice, custbranch.salesman, debtortrans.transno, debtortrans.debtorno, debtortrans.branchcode ,debtortrans.trandate, debtortrans.type, debtorsmaster.name, custbranch.brname,
            debtortrans.ovamount+debtortrans.ovgst AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno
            INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode inner join rh_invoicesreference ON debtortrans.transno=rh_invoicesreference.intinvoice
            inner join systypes on systypes.typeid = debtortrans.type
            WHERE $condSucFac (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59') ".$salesman." and debtortrans.type=10 and debtortrans.rh_status!='C' and custbranch.salesman='$salesmanJ'";
            //Descomentarizar para incluir las notas de credito
            $sqlV .= " union " .
            "SELECT debtortrans.id, systypes.typeid, systypes.typename, rh_crednotesreference.extcn as extinvoice, custbranch.salesman, debtortrans.transno, debtortrans.debtorno, debtortrans.branchcode ,debtortrans.trandate, debtortrans.type, debtorsmaster.name, custbranch.brname,
            debtortrans.ovamount+debtortrans.ovgst AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno
            INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode left outer join rh_crednotesreference ON debtortrans.transno=rh_crednotesreference.intcn
            inner join systypes on systypes.typeid = debtortrans.type
            WHERE $condSucNC (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59') ".$salesman." and debtortrans.type=11 and debtortrans.rh_status='N' and custbranch.salesman='$salesmanJ' order by $order";
            

            $resV = DB_query($sqlV, $db);
            //Termina Jaime, Realhost, 20/Ene/10 17:54

            //Jaime, Realhost, 20/Ene/10 16:04
            //$nomTran = "Factura";
            //Termina Jaime, Realhost, 20/Ene/10 16:04
            $k=0;

            while ($rowV = DB_fetch_array($resV))
            {                    

                      /*
                       * iJPe
                       * realhost   2010-02-15
                       *
                       * Modificacion realizada para evitar que aparecieran notas de credito que estan asignadas a facturas fuera del rango
                       * de fechas
                       */
//                    if ($rowV['typeid']==11)
//                    {
//                        $sqlVerifyNote = "SELECT debtortrans.id, typename, transno, trandate, rate, ovamount+ovgst+ovfreight+ovdiscount AS total,
//                                          diffonexch, debtortrans.alloc-custallocns.amt AS prevallocs, amt, custallocns.id AS allocid
//                                          FROM debtortrans, systypes, custallocns WHERE debtortrans.type = systypes.typeid AND
//                                          debtortrans.id=custallocns.transid_allocto AND custallocns.transid_allocfrom=".$rowV['id']." AND
//                                          debtorno='".$rowV['debtorno']."'
//                                          AND debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59'";
//
//                        $resVerifyNote = DB_query($sqlVerifyNote, $db);
//
//                        if (DB_num_rows($resVerifyNote) <= 0)
//                        {
//                            continue;
//                        }
//
//                    }

                    if ($rowV['extinvoice'] < 0)
                    {
                        continue;
                    }

                    if ($k == 1){
                            echo "<TR BGCOLOR='#CCCCCC'>";
                            $k = 0;
                    } else {
                            echo "<TR BGCOLOR='#EEEEEE'>";
                            $k = 1;
                    }

                    if ($_POST['vendedor']=='todos'){
                        //Jaime, Realhost, 22/Ene/10 16:43
                            echo "<td><b>".$rowV['salesman']."</b></td>";
                        //Termina Jaime, Realhost, 22/Ene/10 16:43
                    }
                    //Jaime, Realhost, 20/Ene/10 16:15
                    echo "<td><b>".$rowV['typename']."<b></td>";
                    //Termina Jaime, Realhost, 20/Ene/10 16:15
                    echo "<td>".$rowV['transno']."</td>";
                    echo "<td>".$rowV['extinvoice']."</td>";
                    echo "<td>".date('Y-m-d',strtotime($rowV['trandate']))."</td>";
                    echo "<td>".$rowV['debtorno']."</td>";
                    echo "<td>".$rowV['name']."</td>";
                    //Jaime, Realhost, 22/Ene/10 16:42
                    //echo "<td>".$rowV['brname']."</td>";
                    //Termina Jaime, Realhost, 22/Ene/10 16:42
                    echo "<td align='right'>$".number_format($rowV['total'],2)."</td>";
                    echo "</tr>";
                    //Jaime, Realhost, 20/Ene/10 16:15
                    $rh_total += $rowV['total'];
                    //Termina Jaime, Realhost, 20/Ene/10 16:15
            }

            
            echo "<TR BGCOLOR='#F78181'>";
            echo "<td><b>Total</b></td>";
            echo "<td colspan = '8' align='right'><b>$".number_format($rh_total,2)."</b></td></tr>";            

            echo "</table>";
        }
        //Jaime, Realhost, 21/Ene/10 09:10
        else{
            //Jaime, Realhost, 22/Ene/10 16:27
            //$vendedor = $_POST['vendedor'];
            //if($vendedor != "todos"){
                //echo("<script type=\"text/javascript\">alert('$vendedor');submitFormaConSalesman('$vendedor')</script>");
            //}
            //Termina Jaime, Realhost, 22/Ene/10 16:27
            echo("<br><hr>");
            echo("<table><tr><td class='tableheader'>Vendedor</td><td class='tableheader'>Cantidad</td><td class='tableheader'>Ver</td></tr>");
            //$sqlV = "select c.salesman, sum(d.ovamount+d.ovgst) as total from  custbranch c, debtortrans d where c.debtorno = d.debtorno group by c.salesman";
            //$filtroSalesman = $_POST['vendedor']!='todos'? " and c.salesman = '".$_POST['vendedor']."'":'';
            $filtroSalesman = $_POST['vendedor']!='todos'? " and custbranch.salesman = '".$_POST['vendedor']."'":'';
            //$sqlV = "select c.salesman, sum(d.ovamount+d.ovgst) as total from custbranch c inner join debtortrans d on c.debtorno = d.debtorno where d.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND d.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59'  AND (d.type=10 or d.type=11) and d.rh_status!='C' $filtroSalesman group by c.salesman";
            //$sqlV = "SELECT custbranch.salesman, sum(debtortrans.ovamount+debtortrans.ovgst) AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode left join rh_invoicesreference ON debtortrans.transno=rh_invoicesreference.intinvoice inner join systypes on systypes.typeid = debtortrans.type WHERE debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59' and (debtortrans.type=10 or debtortrans.type=11) and debtortrans.rh_status!='C' $filtroSalesman group by custbranch.salesman";
            //$sqlV = "call rh_reporteVendedorJ()";

            //iJPe 2010-03-10 Modificacion para filtrar por sucursal
            if ($_POST['sucursal'] != 'todos')
            {
                $condSucFac = "rh_invoicesreference.loccode = '".$_POST['sucursal']."' AND";
                $condSucNC = "rh_crednotesreference.loccode = '".$_POST['sucursal']."' AND";
            }
            else
            {
                $condSucFac = '';
                $condSucNC =  '';
            }

            //Notas de credito y facturas
            $sqlV = "select salesman, sum(total) as total from(
                        (
                        SELECT custbranch.salesman as salesman, sum(debtortrans.ovamount+debtortrans.ovgst) AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode inner join rh_invoicesreference ON debtortrans.transno=rh_invoicesreference.intinvoice inner join systypes on systypes.typeid = debtortrans.type 
                        WHERE $condSucFac (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59') and debtortrans.type=10 and debtortrans.rh_status!='C' $filtroSalesman group by custbranch.salesman
                        )
                        union
                        (
                        SELECT custbranch.salesman, sum(debtortrans.ovamount+debtortrans.ovgst) AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode left outer join rh_crednotesreference ON debtortrans.transno=rh_crednotesreference.intcn inner join systypes on systypes.typeid = debtortrans.type
                        WHERE $condSucNC (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59') and debtortrans.type=11 and debtortrans.rh_status='N' $filtroSalesman group by custbranch.salesman
                        )
                        ) as X
                    group by salesman";
            
            //Solo facturas
            //$sqlV = "SELECT custbranch.salesman as salesman, sum(debtortrans.ovamount+debtortrans.ovgst) AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode inner join rh_invoicesreference ON debtortrans.transno=rh_invoicesreference.intinvoice inner join systypes on systypes.typeid = debtortrans.type WHERE (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59') and debtortrans.type=10 and debtortrans.rh_status!='C' $filtroSalesman group by custbranch.salesman";
            
            $k=0;
            $resV = DB_query($sqlV, $db);
            while ($rowV = DB_fetch_array($resV)){
                $salesman = $rowV['salesman'];
                $total = $rowV['total'];
                $botonSubmit = "<input type=\"button\" onclick=\"submitFormaConSalesman('$salesman')\"/>";
                if ($k == 1){
                    echo "<TR BGCOLOR='#CCCCCC'>";
                    $k = 0;
                }
                else{
                    echo "<TR BGCOLOR='#EEEEEE'>";
                    $k = 1;
                }
                echo("<td>". $salesman .'</td><td align="right">$'. number_format($total,2) ."</td><td>". $botonSubmit ."</td></tr>");
                $rh_total += $rowV['total'];
            }
            echo("<TR BGCOLOR='#F78181'><td><b>TOTAL</b></td><td align='right'><b>$".number_format($rh_total,2)."</b></td></tr></table>");
        }
        //Termina Jaime, Realhost, 21/Ene/10 09:10
}

/*
 * iJPe
 * realhost
 * 2010-02-12
 *
 * No se ha terminado de realizar el reporte en PDF
 */


if (isset($_POST['pdf']))
{
	echo "<br><hr>";
			
	$salesman = $_POST['vendedor']!='todos'? "custbranch.salesman = '".$_POST['vendedor']."' AND":'';
	
	$sqlR = "SELECT debtortrans.transno, debtortrans.ovamount AS total, debtortrans.debtorno, debtorsmaster.name, custbranch.brname, 
	debtortrans.trandate, rh_invoiceshipment.Facturado, rh_invoiceshipment.type	FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno inner join custbranch 
	ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode inner join rh_invoiceshipment ON rh_invoiceshipment.Shipment = debtortrans.transno 
	WHERE ".$salesman." rh_invoiceshipment.Facturado = 0 AND debtortrans.type = 20000 AND debtortrans.rh_status != 'C' AND (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59')
	GROUP BY debtortrans.transno ORDER BY rh_invoiceshipment.Shipment";
	
	$resR = DB_query($sqlR, $db);
	$nomTran = "Remisi&oacute;n";  
	$k=0;
	
	/*while ($rowR = DB_fetch_array($resR))
	{		
						
		echo "<td>".$nomTran."</td>";
		echo "<td>".$rowR['transno']."</td>";
		echo "<td>".date('Y-m-d',strtotime($rowR['trandate']))."</td>";
		echo "<td>".$rowR['debtorno']."</td>";
		echo "<td>".$rowR['name']."</td>";		
		echo "<td>".$rowR['brname']."</td>";
		echo "<td align='right'>".number_format($rowR['total'],2)."</td>";
		
	}*/
	
	$sqlV = "SELECT debtortrans.transno, debtortrans.debtorno, debtortrans.branchcode ,debtortrans.trandate, debtortrans.type, debtorsmaster.name, custbranch.brname,
	debtortrans.ovamount+debtortrans.ovgst AS total FROM debtorsmaster inner join debtortrans ON debtorsmaster.debtorno = debtortrans.debtorno 
	INNER JOIN custbranch ON debtortrans.debtorno = custbranch.debtorno AND debtortrans.branchcode = custbranch.branchcode AND (debtortrans.trandate >= '".FormatDateForSQL($_POST['Fdesde'])." 00:00:00' AND debtortrans.trandate<= '".FormatDateForSQL($_POST['Fhasta'])." 23:59:59')
	WHERE ".$salesman." debtortrans.type=10";
	
	$resV = DB_query($sqlV, $db);
	$nomTran = "Factura";
	$k=0;
	
	/*while ($rowV = DB_fetch_array($resV))
	{	
		echo "<td>".$nomTran."</td>";
		echo "<td>".$rowV['transno']."</td>";
		echo "<td>".date('Y-m-d',strtotime($rowV['trandate']))."</td>";
		echo "<td>".$rowV['debtorno']."</td>";
		echo "<td>".$rowV['name']."</td>";		
		echo "<td>".$rowV['brname']."</td>";
		echo "<td align='right'>".number_format($rowV['total'],2)."</td>";
		echo "</tr>";	
	}*/
	
	echo "</table>";
}

echo "</center>";
include('includes/footer.inc');

?>
<!-- Jaime, Realhost, 20/Ene/10 11:46 -->
<script type="text/javascript" src="js/rh_reporteVendedor.js">
</script>
<!-- Termina Jaime, Realhost, 20/Ene/10 11:46 -->
