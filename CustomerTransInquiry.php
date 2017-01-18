<?php


/* $Revision: 273 $ */

$PageSecurity = 2;

include('includes/session.inc');
$title = _('Customer Transactions Inquiry');

//$_POST['ExportExcel'] = true;

if (isset($_POST['ExportExcel'])){
    $_POST['ShowResults2'] = "Show Transactions";
    $_GET['ShowResults']=1;
    $_POST['ShowResultsComp'] = 1;
    ob_start();
}


include('includes/header.inc');

if (isset($_POST['ShowResultsComp']))
{
	echo '<style type="text/css">
<!--
	table td{
		font-size:11px;
	}
-->
</style>';

}


//echo "<hr>POST";
//print_r($_POST);
//echo "<hr>";

if(isset($_GET['page']) AND $_GET['page']>=0){
	$pag = $_GET['page'];
	$_POST['page'] = $pag;
	$_POST['ShowResults'] = 1;
}else {
	$pag = $_POST['page'];
}
if (!isset($pag) OR $pag<0) $pag = 1; // Por defecto, pagina 1
$tampag = 50;

if(isset($_GET['FromDate'])){
	$_POST['FromDate'] = $_GET['FromDate'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['ToDate'])){
	$_POST['ToDate'] = $_GET['ToDate'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['TransType'])){
	$_POST['TransType'] = $_GET['TransType'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['location'])){
	$_POST['location'] = $_GET['location'];
	$_POST['ShowResults'] = 1;
}
if(isset($_GET['type'])){ // ver las transacciones

	$_POST['ShowResults'] = 1;
	$_POST['FromDate'] = $_GET['FromDate'];
	$_POST['ToDate'] = $_GET['ToDate'];
	$_POST['location'] = $_GET['Location'];
	$_POST['TransType'] = $_GET['type'];

}

// rleal
// Jun 21 2010
// Se agrega el filtro de solo canceladas
// Se agregaron estas variables en varios queries
if(isset($_POST['soloCanceladas']) &&
	$_POST['soloCanceladas'] == 'Yes')
	{
		$rh_chk_soloCanceladas="checked";
		$rh_sc_sql = " AND debtortrans.rh_status='C' ";
		$rh_qry_todos = "debtortrans.rh_status = 'C'";
    }else{
        $rh_chk_soloCanceladas="";
        $rh_sc_sql = "";
        $rh_qry_todos = "debtortrans.rh_status != 'C'";
	}
//fin



/*************************FILTROS AFILIACIONES******************************************/

if(!empty($_POST['cobrador_id']) && ($_POST['cobrador_id'] != 'All')){
    $CobradorData = " AND cobranza.cobrador = '{$_POST['cobrador_id']}' ";
}

if(!empty($_POST['frecuencia_pago']) && ($_POST['frecuencia_pago'] != 'All')){
    $FrecuenciaData = " AND cobranza.frecuencia_pago = '{$_POST['frecuencia_pago']}' ";
}

if(!empty($_POST['metodo_pago']) && ($_POST['metodo_pago'] != 'All')){
    $MPagoData = " AND cobranza.paymentid = '{$_POST['metodo_pago']}' ";
}
/****************************************************************************************/


echo "<FORM NAME='menu' ACTION='" . $_SERVER['PHP_SELF'] . "' METHOD=POST>";

/*Lista de Cobradores*/
$SQLCobradores = "SELECT id, nombre FROM rh_cobradores WHERE activo = 1";
$resultCobradores=DB_query($SQLCobradores,$db);
while ( $_2ListaCobradores = DB_fetch_assoc($resultCobradores)) {
    $ListaCobradores[$_2ListaCobradores['id']] = $_2ListaCobradores['nombre'];
}
/*END Lista de Cobradores*/

/*Lista Frecuencia de Pago*/
$sucursales ="'".implode("','",array_keys($_SESSION["rh_permitionlocation"]))."'";
$SQLFrecuencia = "SELECT id, frecuencia FROM rh_frecuenciapago WHERE sucursal in($sucursales) ";
$resultFrecuencia = DB_query($SQLFrecuencia,$db);
while ( $_2ListaFrecuencia = DB_fetch_assoc($resultFrecuencia)) {
    $ListaFrecuencia[$_2ListaFrecuencia['id']] = $_2ListaFrecuencia['frecuencia'];
}
/*END Lista de Frecuencia*/

/*Lista Metodo de Pago*/
$SQLMetodosPago = "SELECT paymentid, paymentname FROM paymentmethods WHERE activo = 1 ";
$resultMetodosPago = DB_query($SQLMetodosPago,$db);
while ( $_2ListaMetodosPago = DB_fetch_assoc($resultMetodosPago)) {
    $ListaMetodosPago[$_2ListaMetodosPago['paymentid']] = $_2ListaMetodosPago['paymentname'];
}
/*END Lista de Metodo Pago*/
/* ==================================== 
    AGREGADO POR DANIEL VILLARREAL EL 26 DE NOVIEMBRE DEL 2015
======================================== */
/*Lista Motivos Notas de Credito*/
$SQLMotivosNotas = "SELECT id, descripcion FROM or_motivosnotascredito order by descripcion asc ";
$resultMotivosNotas = DB_query($SQLMotivosNotas,$db);
while ( $_2ListaMotivosNotas = DB_fetch_assoc($resultMotivosNotas)) {
    $ListaMotivosNotas[$_2ListaMotivosNotas['id']] = $_2ListaMotivosNotas['descripcion'];
}
/*END Lista Motivos Notas de Credito*/
/* ==================================== 
    TERMINA
======================================== */


echo '<CENTER><TABLE CELLPADDING=2><TR>';

echo '<TD>' . _('Type') . ":</TD><TD><SELECT name='TransType'> ";
// bowikaxu - March 2007 - Se agrego el tipo 20000 de remisiones a las busquedas
$sql = 'SELECT typeid, typename FROM systypes WHERE typeid >= 10 AND typeid <= 14 OR typeid=20000 OR typeid=20001';
$resultTypes = DB_query($sql,$db);

echo "<OPTION Value='All'>"._('Show All');
while ($myrow=DB_fetch_array($resultTypes)){
	if (isset($_POST['TransType'])){
		if ($myrow['typeid'] == $_POST['TransType']){
		     echo "<OPTION SELECTED Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
		}
	} else {
		     echo "<OPTION Value='" . $myrow['typeid'] . "'>" . $myrow['typename'];
	}
}
echo '</SELECT></TD>';

echo "<TR><TD>"._('Location').": </TD><TD><SELECT NAME='location'>";
echo "<OPTION Value='All'>"._('Show All');

$sql = "SELECT loccode, locationname FROM rh_locations ORDER BY locationname";
$reslocations = DB_query($sql,$db);

while($loc = DB_fetch_array($reslocations)){

	if($_POST['location']==$loc['loccode']){
		echo "<OPTION SELECTED VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}else {
		echo "<OPTION VALUE ='".$loc['loccode']."'>".$loc['locationname'];
	}

}

echo "</SELECT></TD></TR>";

echo "<tr><td>Asignaciones: </td><td><select name='AllocOption'>
        <option value='All' ".(($_POST['AllocOption']=='All')?'selected="selected"':'')." >Todas</option>
        <option value='0' ".(($_POST['AllocOption']=='0')?'selected="selected"':'')." >No Asignadas</option>
        <option value='1' ".(($_POST['AllocOption']=='1')?'selected="selected"':'')." >Asignadas</option>
        </select></TD></TR>";
?>

    <tr>
        <td>Cobrador</td>
        <td>
            <select id="cobrador_id" name="cobrador_id">
                <option value="All">Todos</option>
                <?php
                foreach ($ListaCobradores as $idx => $Name) {
                    echo '<option value=' . $idx . '>' . $idx . ' - ' . $Name . "</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                $("#cobrador_id option[value='<?=$_POST['cobrador_id']?>']").attr("selected",true);
            </script>
        </td>
    </tr>

    <tr>
        <td>Frecuencia Pago</td>
        <td>
            <select id="frecuencia_pago" name="frecuencia_pago">
                <option value="All">Todos</option>
                <?php
                foreach ($ListaFrecuencia as $idx => $Name) {
                    echo '<option value=' . $idx . '>' . $Name . "</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                $("#frecuencia_pago option[value='<?=$_POST['frecuencia_pago']?>']").attr("selected",true);
            </script>
        </td>
    </tr>

    <tr>
        <td>Metodo de Pago</td>
        <td>
            <select id="metodo_pago" name="metodo_pago">
                <option value="All">Todos</option>
                <?php
                foreach ($ListaMetodosPago as $idx => $Name) {
                    echo '<option value=' . $idx . '>' . $Name . "</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                $("#metodo_pago option[value='<?=$_POST['metodo_pago']?>']").attr("selected",true);
            </script>
        </td>
    </tr>

    <!-- ==================================== 
        AGREGADO POR DANIEL VILLARREAL EL 26 DE NOVIEMBRE DEL 2015
    ========================================-->
     <tr>
        <td>Motivo Nota Credito</td>
        <td>
            <select id="motivo_nota_credito" name="motivo_nota_credito">
                <option value="All">Todos</option>
                <?php
                foreach ($ListaMotivosNotas as $idx => $Name) {
                    echo '<option value=' . $idx . '>' . $Name . "</option>";
                }
                ?>
            </select>
            <script type="text/javascript">
                $("#motivo_nota_credito option[value='<?=$_POST['motivo_nota_credito']?>']").attr("selected",true);
            </script>
        </td>
    </tr>
    <!-- ==================================== 
        TERMINA
    ========================================-->

<?php

if (!isset($_POST['FromDate'])){
	$_POST['FromDate']=Date($_SESSION['DefaultDateFormat'], mktime(0,0,0,Date('m'),1,Date('Y')));
}
if (!isset($_POST['ToDate'])){
	$_POST['ToDate'] = Date($_SESSION['DefaultDateFormat']);
}
echo '<TD>' . _('From') . ":</TD><TD><INPUT TYPE=TEXT NAME='FromDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['FromDate'] . '></TD>';
echo '<TD>' . _('To') . ":</TD><TD><INPUT TYPE=TEXT NAME='ToDate' MAXLENGTH=10 SIZE=11 VALUE=" . $_POST['ToDate'] . '></TD>';
echo "</TR>";
// rleal
// Jun 21 2010
// Se agrega el filtro de solo canceladas
echo "<TR><TD>". _('Solo Canceladas')."<INPUT TYPE='checkbox' NAME='soloCanceladas' ". $rh_chk_soloCanceladas ." value='Yes'>";
echo "</TR></TABLE><INPUT TYPE=SUBMIT NAME='ShowResults2' VALUE='" . _('Show Transactions') . "'>";

echo "<INPUT TYPE=SUBMIT NAME='ShowResultsComp' VALUE='" . _('Vista Impresi&oacute;n') . "'>";
echo "<INPUT TYPE=SUBMIT NAME='ExportExcel' VALUE='" . _('Exportar a Excel') . "'>";
echo '<HR>';

echo '</FORM></CENTER>';

if ((isset($_POST['ShowResults']) && $_POST['TransType'] != '') OR ((isset($_POST['ShowResults2']) OR isset($_POST['ShowResultsComp'])) && $_POST['TransType']!='All')){
   $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
   $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

    $Alloc='';
    $Alloc2='';
    $Alloc3='';
    if($_POST['AllocOption']=='All'){
        $Alloc='';
        $Alloc2='';
        $Alloc3='';
    }elseif($_POST['AllocOption']=='0'){
        $Alloc=' and debtortrans.id not in(select distinct transid_allocfrom from custallocns)';
        $Alloc2=' and debtortrans.id not in(select distinct transid_allocto from custallocns)';
        $Alloc3=' and debtortrans.id not in(select distinct transid_allocfrom from custallocns) and debtortrans.id not in(select distinct transid_allocto from custallocns)';
    }elseif($_POST['AllocOption']=='1'){
        $Alloc=' and debtortrans.id in(select distinct transid_allocfrom from custallocns)';
        $Alloc2=' and debtortrans.id in(select distinct transid_allocto from custallocns)';
        $Alloc3=' and debtortrans.id in(select distinct transid_allocfrom from custallocns) or debtortrans.id in(select distinct transid_allocto from custallocns)';
    }

	// PAGINATE QUERY
   $sql = "SELECT COUNT(debtortrans.id) as total
    FROM debtortrans";
//rleal feb 16, 2010 se agrego && $_POST['TransType']!=11 para que las notas de credito no sean consideradas
	if  ($_POST['location']!='All' && (($_POST['TransType']!=12) OR ($_POST['TransType']!=11)))  {
		$sql .= " INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
				INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
				WHERE
				 rh_locations.loccode = '" . $_POST['location']."'
				 AND debtortrans.type = ".$_POST['TransType']."";
	}else {
		$sql .= " WHERE /*rh_locations.loccode = '" . $_POST['location']."' and*/ debtortrans.type = ".$_POST['TransType'];
	}
	$sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
	$sql .=  " ORDER BY debtortrans.id";

    $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
    $DbgMsg =  _('The SQL that failed was');
    $TotalResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);
    $num_res = DB_fetch_array($TotalResult);
    $reg1 = ($pag-1) * $tampag;
    // FIN PAGINATE QUERY

        switch($_POST['TransType']){
            //SAINTS
            //rleal May 19 2011 se agreg debtortrans.ovamount y debtortrans.ovgst
            case 10:

                            $sql = "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    c.serie,
									c.folio,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.rate,
                                    debtortrans.ovamount,
                                    debtortrans.ovgst,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                            INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
                            INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                            LEFT JOIN rh_cobranza cobranza ON debtortrans.debtorno = cobranza.debtorno
                            WHERE
                                debtortrans.type = 10".$Alloc2;
                                $sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
							$sql.= $rh_sc_sql . $CobradorData . $FrecuenciaData . $MPagoData;

							//SAINTS corrección Order by 04/02/2011
                            //$sql .=  " ORDER BY numExt";debtortrans.trandate
                            $sql .=  " ORDER BY debtortrans.trandate";
                            break;

                    //iJPe		realhost	2010-01-15
                    //Notas de Cargo
                    case 20001:

                            $sql = "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.rate,
                                    debtortrans.ovamount,
                                    debtortrans.ovgst,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                            INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
                            INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                            LEFT JOIN rh_cobranza cobranza ON debtortrans.debtorno = cobranza.debtorno
                            WHERE
                                debtortrans.type = 20001 ".$Alloc;
                            $sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
                            $sql.= $rh_sc_sql . $CobradorData . $FrecuenciaData . $MPagoData;
							$sql .=  " ORDER BY debtortrans.trandate";
                            break;


                    case 11:
    /*
     * rleal
     * Feb 16, 2010
     *se sustituye query
     *

                            $sql= "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.rate,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                                    INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
                                    INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                            WHERE
                                    debtortrans.type = 11";
                            if  ($_POST['location']!='All')  {
                                    $sql_11 .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
                            $sql= $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            $sql.=  " ORDER BY debtortrans.trandate";

                            */
    /*
     * rleal
     * feb 16, 2010
     * este query trae solo las notas de credito fiscales
     */
							//SAINTS
                            $sql= "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    c.serie,
									c.folio,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.ovamount,
                                    debtortrans.ovgst,
                                    debtortrans.rate,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename,
                                    rh_crednotesreference.extcn,
                                    or_motivosnotascredito.descripcion
                        FROM debtortrans left join rh_cfd__cfd c on c.id_debtortrans = debtortrans.id
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                            INNER JOIN rh_crednotesreference ON rh_crednotesreference.intcn = debtortrans.transno
                            INNER JOIN rh_locations ON rh_locations.loccode = rh_crednotesreference.loccode
                            LEFT JOIN rh_cobranza cobranza ON debtortrans.debtorno = cobranza.debtorno
                            LEFT JOIN or_motivosnotascredito ON debtortrans.shipvia = or_motivosnotascredito.id
                            WHERE
                                debtortrans.type = 11 ".$Alloc;
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }

                            /** ===============================
                                AGREGADO POR DANIEL VILLARREAL EL 26 DE NOVIEMBRE DEL 2015
                            =================================== **/
                            if  ($_POST['motivo_nota_credito']!='All' and isset($_POST['motivo_nota_credito']))  {
                                    $sql .= " AND debtortrans.shipvia = '" . $_POST['motivo_nota_credito']."'";
                            }
                            /** ===============================
                                TERMINA
                            =================================== **/

                            $sql= $sql . " AND debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            $sql.= $rh_sc_sql . $CobradorData . $FrecuenciaData . $MPagoData;
							//$sql.=  " ORDER BY debtortrans.trandate";
                           /* $sql.=  " UNION ";

                            $sql.= "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.rate,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename,
                                    'NA' as extcn
                        FROM debtortrans
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                                    INNER JOIN stockmoves ON stockmoves.transno = debtortrans.transno
                                    INNER JOIN rh_locations ON rh_locations.loccode = stockmoves.loccode
                            WHERE
                                    debtortrans.type = 11";
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
                            $sql= $sql . " AND debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'";

                            $sql .= " AND debtortrans.transno NOT IN (select intcn from rh_crednotesreference)";
                            $sql .= "GROUP BY stockmoves.transno ORDER BY extcn, trandate";
                            */

                            $sql .= "GROUP BY debtortrans.transno ORDER BY extcn, trandate";

                            break;

                    case 12:

                            $sql = "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.ovamount,
                                    debtortrans.ovgst,
                                    debtortrans.rate,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                            LEFT JOIN rh_cobranza cobranza ON debtortrans.debtorno = cobranza.debtorno
                            WHERE
                                debtortrans.type = 12 ".$Alloc;
                            $sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            $sql.= $rh_sc_sql . $CobradorData . $FrecuenciaData . $MPagoData;
							$sql .=  " ORDER BY debtortrans.trandate";
                            break;

                    case 20000:

                            $sql = "SELECT
                                    debtortrans.transno,
                                    debtortrans.type,
                                    debtortrans.id,
                                    debtortrans.trandate,
                                    debtortrans.debtorno,
                                    debtortrans.rh_status,
                                    debtorsmaster.name,
                                    debtortrans.branchcode,
                                    debtortrans.reference,
                                    debtortrans.invtext,
                                    debtortrans.order_,
                                    debtortrans.ovamount,
                                    debtortrans.ovgst,
                                    debtortrans.rate,
                                    debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount as totalamt,
                                    debtortrans.alloc,
                                    debtorsmaster.currcode,
                                    systypes.typename
                        FROM debtortrans
                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                            INNER JOIN systypes ON debtortrans.type = systypes.typeid
                            INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
                            INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                            LEFT JOIN rh_cobranza cobranza ON debtortrans.debtorno = cobranza.debtorno
                            WHERE
                                debtortrans.type = 20000".$Alloc2;
                            if  ($_POST['location']!='All')  {
                                    $sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
                            }
                            $sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
                            $sql.= $rh_sc_sql . $CobradorData . $FrecuenciaData . $MPagoData;
							$sql .=  " ORDER BY debtortrans.trandate";
                            break;
       }
    $sqlGlobalizar=$sql;
    if (!isset($_POST['ShowResultsComp'])){
        $sql .=  " LIMIT ".$reg1.", ".$tampag;
    }
    #echo $sql;
    ///echo $sql;
   $ErrMsg = _('The customer transactions for the selected criteria could not be retrieved because') . ' - ' . DB_error_msg($db);
   $DbgMsg =  _('The SQL that failed was');
   $TransResult = DB_query($sql, $db,$ErrMsg,$DbgMsg);

   //iJPe realhost	2010-01-14
   //Se agrego la opcion Vista Impresion (La cual es sin paginacion)
   if (!isset($_POST['ShowResultsComp']))
	{
	   // bowikaxu realhost - june 2008 - view pages as links not as selectbox
	   echo "<FORM METHOD=POST ACTION=CustomerTransInquiry.php><CENTER>"._('Page').": ";
		if($pag>1){
			echo "<A HREF='CustomerTransInquiry.php?".SID."page=".($pag-1)."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'> << </A> | ";
	   }
	   for($i=1;$i<=ceil($num_res['total']/$tampag) AND $i<=7;$i++){
			if($pag == $i){
				echo $i." | ";
			}else {
				echo "<A HREF='CustomerTransInquiry.php?".SID."page=".$i."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'>".$i."</A> | ";
			}
	   }
	   if($pag<ceil($num_res['total']/$tampag)){
			echo "<A HREF='CustomerTransInquiry.php?".SID."page=".($pag+1)."&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&TransType=".$_POST['TransType']."&location=".$_POST['location']."'> >> </A>";
	   }

	   echo "</CENTER>
	   <INPUT TYPE=HIDDEN NAME='TransType' VALUE='".$_POST['TransType']."'>
	   <INPUT TYPE=HIDDEN NAME='location' VALUE='".$_POST['location']."'>
	   <INPUT TYPE=HIDDEN NAME='FromDate' VALUE='".$_POST['FromDate']."'>
	   <INPUT TYPE=HIDDEN NAME='ToDate' VALUE='".$_POST['ToDate']."'>
	   <INPUT TYPE=HIDDEN NAME='ShowResults' VALUE='".$_POST['ShowResults']."'>
	   </FORM><BR>";
	}

    if (isset($_POST['ExportExcel'])){
        ob_clean();
         header("Content-type: application/octet-stream");
         header("Content-Disposition: filename=\"CustomerTransInquiry.xls\"");
         //header("Content-Disposition: filename=resultado.txt");
         //header("Content-length: ".strlen($html));
         header("Cache-control: private");
    }

   echo '<TABLE CELLPADDING=3 BORDER=2>';

   //Header para Depositos
   if($_POST['TransType']==12){
   	$tableheader = "<TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
            <TD class='tableheader'>" . _('Factura') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
            <TD class='tableheader'>" . _('Folio') . "</TD>
			<TD class='tableheader'>" . _('Customer') . "</TD>
            <TD class='tableheader'>" . _('Cobrador') . "</TD>
            <TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>"
			."<TD class='tableheader' style='display:none;'>" . _('Ex Rate') . "</TD>
            <TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Bank') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD></TR>';
   //rleal May 2011 se agreg este elseif para tratar las facturas por separado
   }elseif ($_POST['TransType']==10){
   	$tableheader = "
        <TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
            <TD class='tableheader'>" . _('Factura') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
            <TD class='tableheader'>" . _('Folio') . "</TD>
			<TD class='tableheader'>" . _('Customer') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
            <TD class='tableheader'>" . _('Cobrador') . "</TD>
			<TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>
			<TD class='tableheader'>" . _('Order') . "</TD>
			<TD class='tableheader' style='display:none;'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . _('Subtotal') . "</TD>
			<TD class='tableheader'>" . _('Tax') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Bank') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD>
        </TR>';
   }
   //DANIEL VILLARREAL Nov 2015 se agreg este elseif para tratar las notas de credito por separado
   elseif ($_POST['TransType']==11){
    $tableheader = "<TR>
            <TD class='tableheader'>" . _('Type') . "</TD>
            <TD class='tableheader'>" . _('Number') . "</TD>
            <TD class='tableheader'>" . _('Factura') . "</TD>
            <TD class='tableheader'>" . _('Date') . "</TD>
            <TD class='tableheader'>" . _('Folio') . "</TD>
            <TD class='tableheader'>" . _('Customer') . "</TD>
            <TD class='tableheader'>" . _('Branch') . "</TD>
            <TD class='tableheader'>" . _('Cobrador') . "</TD>
            <TD class='tableheader'>" . _('Reference') . "</TD>
            <TD class='tableheader'>" . _('Comments') . "</TD>
            <TD class='tableheader'>" . _('Motivo') . "</TD>
            <TD class='tableheader'>" . _('Order') . "</TD>
            <TD class='tableheader' style='display:none;'>" . _('Ex Rate') . "</TD>
            <TD class='tableheader'>" . _('Amount') . "</TD>
            <TD class='tableheader'>" . _('Allocated') . "</TD>
            <TD class='tableheader'>" . _('Bank') . "</TD>
            <TD class='tableheader'>" . _('Currency') . '</TD>
        </TR>';
   }
   	else {
   	$tableheader = "
        <TR>
			<TD class='tableheader'>" . _('Type') . "</TD>
			<TD class='tableheader'>" . _('Number') . "</TD>
            <TD class='tableheader'>" . _('Factura') . "</TD>
			<TD class='tableheader'>" . _('Date') . "</TD>
            <TD class='tableheader'>" . _('Folio') . "</TD>
			<TD class='tableheader'>" . _('Customer') . "</TD>
			<TD class='tableheader'>" . _('Branch') . "</TD>
            <TD class='tableheader'>" . _('Cobrador') . "</TD>
			<TD class='tableheader'>" . _('Reference') . "</TD>
			<TD class='tableheader'>" . _('Comments') . "</TD>
			<TD class='tableheader'>" . _('Order') . "</TD>
			<TD class='tableheader' style='display:none;'>" . _('Ex Rate') . "</TD>
			<TD class='tableheader'>" . _('Amount') . "</TD>
			<TD class='tableheader'>" . _('Allocated') . "</TD>
			<TD class='tableheader'>" . _('Bank') . "</TD>
			<TD class='tableheader'>" . _('Currency') . '</TD>
        </TR>';
   }
	echo $tableheader;

	$RowCounter = 1;
	$k = 0; //row colour counter

	while ($myrow=DB_fetch_array($TransResult)) {

	if($myrow['type']==10){
		// bowikaxu april 2007 - get external invoice number
		//rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
		/*
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);*/
	}

	//iJPe	notas de cargo
	if($myrow['type']==20001){
		// bowikaxu april 2007 - get external invoice number
		//rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
		/*
			$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			WHERE rh_invoicesreference.intinvoice = ".$myrow['transno']."
			AND rh_locations.loccode = rh_invoicesreference.loccode";
		    $res = DB_query($sql,$db);
		    $ExtInvoice = DB_fetch_array($res);*/
	}

	$print = 1;
	//rleal feb 16,2010 se agrego la nota de credito 11
	if(($myrow['type']!=12) AND ($myrow['type']!=11)){
		$sql = "SELECT debtortrans.order_ FROM debtortrans
				INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
				INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
				WHERE debtortrans.id = ".$myrow['id']."";
		if  ($_POST['location']!='All')  {
			$sql .= " AND rh_locations.loccode = '" . $_POST['location']."'";
		}
				$res = DB_query($sql,$db);
				if(DB_num_rows($res)>0){
					$orderarray = DB_fetch_array($res);
					$order = $orderarray['order_'];
				}else {
					$print = 0;
				}
	}else {
		$order = '-';
	}
		// bowikaxu realhost - june 30 2007 - change color on cancelled transactions
	if($myrow['type']==20000 && $myrow['rh_status']=='C'){

		    echo "<tr bgcolor='#ea6060'>";
		    		$myrow['ovamount'] = 0;
					$myrow['ovgst'] = 0;
                    $myrow['totalamt'] = 0;
                    $myrow['alloc'] = 0;

	}else if (($myrow['type']==20001 || $myrow['type']==10 || $myrow['type']==11) && $myrow['rh_status']=='C'){

		echo "<tr bgcolor='#ea6060'>";
				$myrow['ovamount'] = 0;
				$myrow['ovgst'] = 0;
                $myrow['totalamt'] = 0;
                $myrow['alloc'] = 0;

	}else if ($myrow['type']==11 && $myrow['rh_status']=='R'){ // nota de credito cancela remision

		echo "<tr bgcolor='#f3cb85'>";

	}else if ($myrow['type']==11 && $myrow['rh_status']=='F'){ // nota de credito cancela factura

		echo "<tr bgcolor='#e4f369'>";

	}else {

		if ($k==1){
			echo "<tr bgcolor='#CCCCCC'>";
			$k=0;
		} else {
			echo "<tr bgcolor='#EEEEEE'>";
			$k=1;
		}
	}

	if ($_POST['TransType']==12)
	{
		$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
                <td>%s</td>
				<td>%s</td>
                <td>%s</td>
	   			<td>%s</td>
				<td width='20'>%s</td>
				<td ALIGN=RIGHT style='display:none'>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				<td>%s</td>";

	//rleal May 2011 se agreg este elseif para tratar las facturas por separado
	}elseif ($_POST['TransType']==10){
		$format_base = "<td>%s</td>
				<td>%s</td>
                <td>%s</td>
				<td>%s</td>
				<td>%s</td>
                <td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td width='200'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT style='display:none;'>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				<td>%s</td>";
	} 
    //DANIEL VILLARREAL Nov 2015 se agreg este elseif para tratar las notas de credito por separado
        elseif ($_POST['TransType']==11){
            $format_base = "
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td width='200'>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td ALIGN=RIGHT>%s</td>
                    <td ALIGN=RIGHT style='display:none;'>%s</td>
                    <td ALIGN=RIGHT>%s</td>
                    <td>%s</td>
                    <td>%s</td>";
         
        } 
    else {
		$format_base = "<td>%s</td>
				<td>%s</td>
				<td>%s</td>
				<td>%s</td>
                <td>%s</td>
				<td>%s</td>
                <td>%s</td>
				<td>%s</td>
				<td width='200'>%s</td>
				<td>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td ALIGN=RIGHT style='display:none;'>%s</td>
				<td ALIGN=RIGHT>%s</td>
				<td>%s</td>
				<td>%s</td>";
	}

        /************************************** Obtengo Datos del Afiliado ************************************/
        $_2GetAfilData = "SELECT cobranza.folio, cob.nombre as cobrador
                               FROM rh_cobranza cobranza
                               LEFT JOIN rh_cobradores cob ON cob.id = cobranza.cobrador
                               WHERE cobranza.debtorno = '{$myrow['debtorno']}'";
        $_GetAfilData=DB_query($_2GetAfilData,$db);
        $GetAfilData = DB_fetch_assoc($_GetAfilData);
        /******************************************************************************************************/

		if ($_POST['TransType']==10 && $print){ /* invoices */

		    $sql = "SELECT custallocns.transid_allocfrom, debtortrans.*, systypes.typename FROM custallocns
		    	INNER JOIN debtortrans ON debtortrans.id = custallocns.transid_allocfrom
		    	INNER JOIN systypes ON systypes.typeid = debtortrans.type
		    	WHERE transid_allocto = ".$myrow['id']."";
		    $res2 = DB_query($sql,$db);
		    $Banks = '';
		    $AllocName = '';
		    while ($trans = DB_fetch_array($res2)){ //(SELECT debtortrans.type FROM debtortrans WHERE id = ".$trans['transid_allocfrom'].")
		    	$sql = "SELECT bankaccounts.bankaccountname, banktrans.transdate FROM bankaccounts,banktrans
		    		WHERE bankaccounts.accountcode = banktrans.bankact
		    		AND banktrans.transno = ".$trans['transno']."
		    		AND banktrans.type = ".$trans['type']."";

		    	$res3 = DB_query($sql,$db);
		    	$Bank = DB_fetch_array($res3);
		    	if(DB_num_rows($res3)>=1){
		    		$Banks = $Bank['bankaccountname'].' '.$Bank['transdate'].'<BR>'.$Banks;
		   		}else {
		   			$AllocName = $trans['typename'].' '.$trans['transno']."<BR>".$AllocName;
		   		}
		}

		    //"SELECT bankaccounts.bankaccountname FROM bankaccounts WHERE bankaccounts.accountcode =
            //(SELECT bankact FROM banktrans WHERE type = (SELECT debtortrans.type FROM debtortrans WHERE id =
            //(SELECT transid_allocfrom FROM custallocns WHERE transid_allocto = ".$myrow['id'].")) AND transno =
            //(SELECT debtortrans.transno FROM debtortrans WHERE id = (SELECT transid_allocfrom FROM custallocns WHERE transid_allocto = ".$myrow['id'].")))";



			//rleal Jul 30 2011
			if(($myrow['folio']==NULL) && ($myrow[rh_status]!='C')){
				$rh_folioequivocado.=' ' . $myrow['transno'].',';
				//echo "xx";
			}

			//SAINTS
			if($myrow['folio']!=""){
                $imgSRC = $rootpath.'/css/'.$theme.'/images/preview.gif';
				printf("$format_base
				<td><a target='_blank' href='{$rootpath}/rh_PrintCustTrans.php?%&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='{$imgSRC}' TITLE='" . _('Click to preview the invoice') . "'></a></td>
				</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
				$GetAfilData['folio'],
                $myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['ovamount'],2),
				number_format($myrow['ovgst'],2),
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$Banks.$AllocName,
				$myrow['currcode'],
				// $rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');}

			//SAINTS
			else{printf("$format_base
				<td><a target='_blank' href='{$rootpath}/rh_PrintCustTrans.php?%&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='%s' TITLE='" . _('Click to preview the invoice') . "'></a></td>
				</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$Banks.$AllocName,
				$myrow['currcode'],
				// $rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');}

		}  elseif ($_POST['TransType']==11 && $print){ /* credit notes */
        //rleal feb 16,2010 se agregó .'('.$myrow['extcn'].')' para mostrar el numeor externo
            //SAINTS
            if($myrow['folio']!=""){
                printf("$format_base
                <td><a target='_blank' href='{$rootpath}/rh_PrintCustTrans.php?%s&FromTransNo={$myrow['transno']}&InvOrCredit=Credit'><IMG SRC='%s' TITLE='" . _('Click to preview the credit') . "'></a></td>
                </tr>",
                $myrow['typename'],
                //$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
                //$myrow['transno'].'('.$myrow['extcn'].')',
                ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
                $myrow['name'].' ['.$myrow['debtorno'].']',
                $myrow['branchcode'],
                $GetAfilData['cobrador'],
                $myrow['reference'],
                $myrow['invtext'],
                $myrow['descripcion'],
                $myrow['order_'],
                $myrow['rate'],
                number_format($myrow['totalamt'],2),
                number_format($myrow['alloc'],2),
                '',
                $myrow['currcode'],
                // $rootpath,
                SID,
                $myrow['transno'],
                $rootpath.'/css/'.$theme.'/images/preview.gif');}

			//SAINTS
			else{printf("$format_base
				<td><a target='_blank' href='{$rootpath}/rh_PrintCustTrans.php?%s&FromTransNo={$myrow['transno']}&InvOrCredit=Credit'><IMG SRC='%s' TITLE='" . _('Click to preview the credit') . "'></a></td>
				</tr>",
				$myrow['typename'],
				//$myrow['transno'].'('.$myrow['extcn'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				'',
				$myrow['currcode'],
				// $rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');}

		} elseif ($_POST['TransType']==20000 && $print){ /* remisiones */
			printf("$format_base
				<td><a target='_blank' href='{$rootpath}/rh_PDFRemGde.php?%s&FromTransNo={$myrow['transno']}&InvOrCredit=Invoice'><IMG SRC='%s' TITLE='" . _('Click to preview the credit') . "'></a></td>
				</tr>",
				$myrow['typename'],
				$myrow['transno'],
                '',
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				'',
				$myrow['currcode'],
				// $rootpath,
				SID,
				$myrow['transno'],
				$rootpath.'/css/'.$theme.'/images/preview.gif');

		// bowikaxu realhost sept 07
		} elseif ($_POST['TransType']==12){ /* RECIBOS */
				$sql = "SELECT id FROM debtortrans WHERE transno = '".$myrow['transno']."' AND type = 12";
				//$recpt_id = DB_query($sql,$db);
		    	$sql = "SELECT transid_allocto FROM custallocns WHERE transid_allocfrom = ".$myrow['id']."";
		    	$res2 = DB_query($sql,$db);
			    $Banks = '';
			    $fact = '';
			    $Separador='';
			    while ($trans = DB_fetch_array($res2)){ //(SELECT debtortrans.type FROM debtortrans WHERE id = ".$trans['transid_allocfrom'].")
			    	$fact .=$Separador;
			    	$sql = "SELECT bankaccounts.bankaccountname, banktrans.transdate
			    			FROM bankaccounts,banktrans
			    			WHERE bankaccounts.accountcode = banktrans.bankact
			    			AND banktrans.type = 12
		    				AND banktrans.transno = (SELECT debtortrans.transno FROM debtortrans WHERE type = 12 AND id = ".$myrow['id'].")";
		    		$res3 = DB_query($sql,$db);
			    	$Bank = DB_fetch_array($res3);

			    	$sql = "SELECT transno FROM debtortrans WHERE id = ".$trans['transid_allocto']."";
			    	$res5 = DB_query($sql,$db);
			    	$trans_to = DB_fetch_array($res5);

			    	// bowikaxu sept 2007 - get external invoice number
			    	//rlea Jul 30 2011 se quita la llamada a rh_invoicesreference
			    	/*
				$sql = "SELECT rh_invoicesreference.extinvoice, rh_locations.rh_serie FROM rh_invoicesreference, rh_locations
			 		WHERE rh_invoicesreference.intinvoice = ".$trans_to['transno']." AND rh_locations.loccode = rh_invoicesreference.loccode";
		    	$res = DB_query($sql,$db);
		    	$ExtInvoice = DB_fetch_array($res);
			    	*/
			    //SAINTS creación de consulta para obtener series y folios de FE 29/01/2011
		    	$zql="SELECT serie, folio FROM rh_cfd__cfd where fk_transno=".$trans_to['transno'];
		    	$rez=DB_query($zql,$db);
		    	$fe=DB_fetch_array($rez);

				//SAINTS Series y folios 29/01/2011
				if($fe['folio']!="")
			    	$fact .= $fe['serie'].$fe['folio'];
			    else
					$fact .= $ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'];
				//SAINTS fin

			    	if(DB_num_rows($res3)>=1)
			    		$Banks = $Bank['bankaccountname'].' '.$trans_to['transno'].'<BR>'.$Banks;
			    	$Separador=', ';
		    	}
				printf("<TD>%s</TD>$format_base</tr>",
				$myrow['typename'],
				$myrow['transno'],
                $fact,
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
			 	$Banks,
				$myrow['currcode']);

		} else {  /* otherwise */

			if($myrow['type']==10 && $print){

			//SAINTS
			  if($myrow['folio']!=""){
		    	printf("$format_base</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
				//$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$Banks,
				$myrow['currcode']);}

			//SAINTS
			  else{printf("$format_base</tr>",
				$myrow['typename'],
				//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                $myrow['transno'],
                $myrow['serie'].$myrow['folio'],
				ConvertSQLDate($myrow['trandate']),
                $GetAfilData['folio'],
				$myrow['name'].' ['.$myrow['debtorno'].']',
				$myrow['branchcode'],
                $GetAfilData['cobrador'],
				$myrow['reference'],
				$myrow['invtext'],
				$myrow['order_'],
				$myrow['rate'],
				number_format($myrow['totalamt'],2),
				number_format($myrow['alloc'],2),
				$Banks,
				$myrow['currcode']);}

			}else{

				if($myrow['type']==10 && $print){
				  //SAINTS
				  if($myrow['folio']!=""){
					printf("$format_base</tr>",
					$myrow['typename'],
					//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
					//$myrow['serie'].$myrow['folio'].'('.$myrow['transno'].')',
                    $myrow['transno'],
                    $myrow['serie'].$myrow['folio'],
					ConvertSQLDate($myrow['trandate']),
                    $GetAfilData['folio'],
					$myrow['name'].' ['.$myrow['debtorno'].']',
					$myrow['branchcode'],
                    $GetAfilData['cobrador'],
					$myrow['reference'],
					$myrow['invtext'],
					$myrow['order_'],
					$myrow['rate'],
					number_format($myrow['totalamt'],2),
					number_format($myrow['alloc'],2),
					$Banks,
					$myrow['currcode']);}

				  //SAINTS
				  else{printf("$format_base</tr>",
					$myrow['typename'],
					//$ExtInvoice['rh_serie'].$ExtInvoice['extinvoice'].'('.$myrow['transno'].')',
                    $myrow['transno'],
                    $myrow['serie'].$myrow['folio'],
					ConvertSQLDate($myrow['trandate']),
                    $GetAfilData['folio'],
					$myrow['name'].' ['.$myrow['debtorno'].']',
					$myrow['branchcode'],
                    $GetAfilData['cobrador'],
					$myrow['reference'],
					$myrow['invtext'],
					$myrow['order_'],
					$myrow['rate'],
					number_format($myrow['totalamt'],2),
					number_format($myrow['alloc'],2),
					$Banks,
					$myrow['currcode']);}

				}else if($print) {

				printf("$format_base</tr>",
					$myrow['typename'],
					$myrow['transno'],
                    '',
					ConvertSQLDate($myrow['trandate']),
                    $GetAfilData['folio'],
					$myrow['name'].' ['.$myrow['debtorno'].']',
					$myrow['branchcode'],
                    $GetAfilData['cobrador'],
					$myrow['reference'],
					$myrow['invtext'],
					$myrow['order_'],
					$myrow['rate'],
					number_format($myrow['totalamt'],2),
					number_format($myrow['alloc'],2),
					'',
					$myrow['currcode']);
				}

			}

		}
	//end of page full new headings if
	}
	//end of while loop

 echo '</TABLE>';
}

    if (isset($_POST['ExportExcel'])){
        //echo ob_get_contents();
        //ob_clean();
        //exit;
        //ob_start();
    }

// Motrar resumen por transaccion
if ((isset($_POST['ShowResults2']) || isset($_POST['ShowResultsComp']) && $_POST['TransType'] != '') || isset($_GET['page']) || isset($_GET['ShowResults'])){


    if (isset($_GET['TransType']))
    {
        $_POST['TransType'] = $_GET['TransType'];

        $_POST['FromDate'] = $_GET['FromDate'];
        $_POST['ToDate'] = $_GET['ToDate'];
    }

     $SQL_FromDate = FormatDateForSQL($_POST['FromDate']);
     $SQL_ToDate = FormatDateForSQL($_POST['ToDate']);

	//rleal feb 16,2010 se quita las notas de credito de aqui y se calculan por separado
        if ($_POST['TransType']==10 || $_POST['TransType']==20000 || $_POST['TransType']==20001)
        {
        	//rleal feb 16,2010 si son todas las Locations es otro query
          	if (($_POST['location']) =='All') {
            $sql = "SELECT debtortrans.transno, systypes.typeid,
                                            systypes.typename,
                                            		SUM(debtortrans.ovamount/rate) as sumovamount,
                                            		SUM(debtortrans.ovgst/rate) as sumovgst,
                                                    SUM(debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) AS amnt,
                                                    SUM(debtortrans.alloc/rate) AS alloc
                                            FROM debtortrans
                                            INNER JOIN systypes ON systypes.typeid = debtortrans.type
                                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
				INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
				INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                                            WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'";
           									$sql .= " AND debtortrans.type = '" . $_POST['TransType']."'".$Alloc3;
                                            $sql .= " AND ".$rh_qry_todos;
											$sql .= " GROUP BY debtortrans.type";
        	} else {
        		$sql = "SELECT debtortrans.transno, systypes.typeid,
                                            systypes.typename,
                                            SUM(debtortrans.ovamount/rate) as sumovamount,
                                            		SUM(debtortrans.ovgst/rate) as sumovgst,
                                                    SUM(debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) AS amnt,
                                                    SUM(debtortrans.alloc/rate) AS alloc
                                            FROM debtortrans
                                            INNER JOIN systypes ON systypes.typeid = debtortrans.type
                                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
				INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_
				INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                                            WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'
                                            AND rh_locations.loccode = '".$_POST['location']."' ". (($_POST['TransType']='20001')?$Alloc:$Alloc2);
                                            $sql .= " AND debtortrans.type = '" . $_POST['TransType']."'";
                                            $sql .= " AND ".$rh_qry_todos;
											$sql .= " GROUP BY debtortrans.type";


        	}

            $res = DB_query($sql,$db);
        }
		//rleal feb 16,2010 aqui se calculan las notas de credito
		if ($_POST['TransType'] == 11)
        {
//            $sql = "SELECT typename, SUM(amnt) AS amnt, SUM(alloc) AS alloc FROM (SELECT debtortrans.transno, systypes.typeid,
//                                            systypes.typename,
//                                                    (debtortrans.ovamount+debtortrans.ovgst+debtortrans.ovfreight+debtortrans.ovdiscount) AS amnt,
//                                                    (debtortrans.alloc) AS alloc
//                                            FROM debtortrans
//                                            INNER JOIN systypes ON systypes.typeid = debtortrans.type
//                                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
//											INNER JOIN stockmoves ON stockmoves.transno = debtortrans.transno
//											INNER JOIN rh_locations ON rh_locations.loccode = stockmoves.loccode
//                                            WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'
//                                            AND debtortrans.rh_status != 'C'  AND (NOT stockmoves.loccode != (select loccode from rh_crednotesreference WHERE intcn = debtortrans.transno)
//                                            OR (debtortrans.transno NOT IN (select intcn from rh_crednotesreference)))";
//                                            if ($_POST['location'] != 'All')
//                                            	$sql .= " AND rh_locations.loccode = '".$_POST['location']."'";
//                                            $sql .= " AND debtortrans.type = '" . $_POST['TransType']."'";
//                                            $sql .= " GROUP BY stockmoves.transno) AS tmp GROUP BY typeid";

                    $sql = "SELECT debtortrans.transno, systypes.typeid,
                                            systypes.typename,
                                            SUM(debtortrans.ovamount/rate) as sumovamount,
                                            		SUM(debtortrans.ovgst/rate) as sumovgst,
                                                    SUM(debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) AS amnt,
                                                    SUM(debtortrans.alloc/rate) AS alloc
                                            FROM debtortrans
                                            INNER JOIN systypes ON systypes.typeid = debtortrans.type
                                            INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
											INNER JOIN rh_crednotesreference ON rh_crednotesreference.intcn = debtortrans.transno
											INNER JOIN rh_locations ON rh_locations.loccode = rh_crednotesreference.loccode
                                            LEFT JOIN or_motivosnotascredito ON debtortrans.shipvia = or_motivosnotascredito.id
                                            WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'";
                                            if ($_POST['location'] != 'All')
                                            	$sql .= " AND rh_locations.loccode = '".$_POST['location']."'";
                                            /** ===============================
                                                AGREGADO POR DANIEL VILLARREAL EL 26 DE NOVIEMBRE DEL 2015
                                            =================================== **/
                                            if  ($_POST['motivo_nota_credito']!='All' and isset($_POST['motivo_nota_credito']))  {
                                                    $sql .= " AND debtortrans.shipvia = '" . $_POST['motivo_nota_credito']."'";
                                            }
                                            /** ===============================
                                                TERMINA
                                            =================================== **/
                                            $sql .= " AND ".$rh_qry_todos;
											$sql .= " AND debtortrans.type = '" . $_POST['TransType']."' ".$Alloc;
                                            $sql .= " GROUP BY debtortrans.type";

            $res = DB_query($sql,$db);
        }

        if ($_POST['TransType'] == 12)
        {
            $sql = "SELECT
				debtortrans.transno,
		   		debtortrans.type,
		   		SUM(debtortrans.ovamount/rate) as sumovamount,
                SUM(debtortrans.ovgst/rate) as sumovgst,
				SUM(debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) as amnt,
				SUM(debtortrans.alloc/rate) AS alloc,
				debtorsmaster.currcode,
				systypes.typename
		    FROM debtortrans
		      	INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
		        INNER JOIN systypes ON debtortrans.type = systypes.typeid
			WHERE
				debtortrans.type = '".$_POST['TransType']."' ".$Alloc;
			$sql = $sql . " AND trandate >='" . $SQL_FromDate . " 00:00:00' AND trandate <= '" . $SQL_ToDate . " 23:59:59'";
			$sql .=  " GROUP BY debtortrans.type";

            $res = DB_query($sql,$db);
        }

        if ($_POST['TransType']=='All')
        {
            $sql = "SELECT systypes.typeid,
                    systypes.typename,
                    		SUM(debtortrans.ovamount/rate) as sumovamount,
							SUM(debtortrans.ovgst/rate) as sumovgst,
                            SUM(debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) AS amnt,
                            SUM(debtortrans.alloc/rate) AS alloc
                    FROM debtortrans
                    INNER JOIN systypes ON systypes.typeid = debtortrans.type
                    INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
                    INNER JOIN salesorders ON salesorders.orderno = debtortrans.order_ INNER JOIN rh_locations ON rh_locations.loccode = salesorders.fromstkloc_virtual
                    WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'";
                        if ($_POST['location'] != 'All')
                            $sql .= " AND salesorders.fromstkloc_virtual = '".$_POST['location']."'";
                    $sql.=" AND ". $rh_qry_todos. " AND systypes.typeid != 11 GROUP BY systypes.typeid";

            $sql .= " UNION ";

            $sql .= "SELECT typeid, typename, SUM(sumovamount) as sumovamount, SUM(sumovgst) as sumovgst,  SUM(amnt) AS amnt, SUM(alloc) AS alloc FROM (SELECT debtortrans.transno, systypes.typeid,
                    systypes.typename,
                            debtortrans.ovamount/rate as sumovamount,
                            debtortrans.ovgst/rate as sumovgst,
                            (debtortrans.ovamount/rate+debtortrans.ovgst/rate+debtortrans.ovfreight/rate+debtortrans.ovdiscount/rate) AS amnt,
                            (debtortrans.alloc/rate) AS alloc
                    FROM debtortrans
                    INNER JOIN systypes ON systypes.typeid = debtortrans.type
                    INNER JOIN debtorsmaster ON debtortrans.debtorno=debtorsmaster.debtorno
					INNER JOIN stockmoves ON stockmoves.transno = debtortrans.transno
					INNER JOIN rh_locations ON rh_locations.loccode = stockmoves.loccode
                    WHERE debtortrans.trandate >='" . $SQL_FromDate . " 00:00:00' AND debtortrans.trandate <= '" . $SQL_ToDate . " 23:59:59'
                    AND ". $rh_qry_todos. "  AND (NOT stockmoves.loccode != (select loccode from rh_crednotesreference WHERE intcn = debtortrans.transno))";
                        if ($_POST['location'] != 'All')
                            $sql .= " AND rh_locations.loccode = '".$_POST['location']."' ".$Alloc;
                        /** ===============================
                           AGREGADO POR DANIEL VILLARREAL EL 26 DE NOVIEMBRE DEL 2015
                        =================================== **/
                        if  ($_POST['motivo_nota_credito']!='All' and isset($_POST['motivo_nota_credito']))  {
                            $sql .= " AND debtortrans.shipvia = '" . $_POST['motivo_nota_credito']."'";
                        }
                        /** ===============================
                            TERMINA
                        =================================== **/
                    $sql .= " AND debtortrans.type = '11' GROUP BY stockmoves.transno) AS tmp GROUP BY typeid";

            $res = DB_query($sql,$db);



        }
        $sql="select                sum(ovamount/rate) sumovamount,
                                    sum(ovgst/rate) sumovgst,
                                    sum((totalamt)/rate) as amnt,
                                    sum(alloc/rate) as alloc from (".$sqlGlobalizar.") d";
        if($sqlGlobalizar!='')
        $res = DB_query($sql,$db);

	if(!isset($_POST['ExportExcel']))
    {
        $tableheader = "<TR>
            <TD class='tableheader'>" . _('Type') . "</TD>
            <TD class='tableheader'>" . _('Subtotal') . "</TD>
            <TD class='tableheader'>" . _('Tax') . "</TD>
            <TD class='tableheader'>" . _('Amount') . "</TD>
            <TD class='tableheader'>" . _('Allocated') . "</TD>
            <TD></TD>
            </TR>";

        echo "<FORM NAME='resume' METHOD='GET' ACTION='CustomerTransInquiry.php'>
                <TABLE ALIGN=CENTER>\n";
        echo $tableheader;
        $k=0;
        while($myrow = DB_fetch_array($res)){

            if ($k==1){
                echo "<tr bgcolor='#CCCCCC'>";
                $k=0;
            } else {
                echo "<tr bgcolor='#EEEEEE'>";
                $k=1;
            }

            echo "  <TD>".$myrow['typename']."</TD>
                    <TD>".number_format($myrow['sumovamount'],2)."</TD>
                    <TD>".number_format($myrow['sumovgst'],2)."</TD>
                    <TD>".number_format($myrow['amnt'],2)."</TD>
                    <TD>".number_format($myrow['alloc'],2)."</TD>
                    <TD><A HREF='CustomerTransInquiry.php?type=".$myrow['typeid']."&ShowResults=1&FromDate=".$_POST['FromDate']."&ToDate=".$_POST['ToDate']."&Location=".$_POST['location']."'>"._('Show')."</a></TD>
            </TR>";
        }
        echo "</TABLE></FORM>";
    }
     if (isset($_POST['ExportExcel'])){
        //echo ob_get_contents();
        //ob_clean();
        exit;
    }


}

include('includes/footer.inc');
if (strlen($rh_folioequivocado)>1){
require("PHPMailer_v5.1/class.phpmailer.php");
				$mail = new PHPMailer();
				$mail->IsSMTP(); // send via SMTP
				$mail->Host = 'ssl://smtp.gmail.com';
				$mail->Port = 465;//587
				$mail->SMTPAuth = true; // turn on SMTP authentication
				$mail->Username = "tractoref@realhost.com.mx"; // SMTP username
				$mail->Password = "74RL63xX"; // SMTP password
				$mail->SetFrom('floresca@realhost.com.mx', 'Error de Folio Floresca');

				$mails=explode(";","rleal@realhost.com.mx");
				foreach($mails as $value){
    				if(strlen($value)>3){
        			$mail->AddAddress($value);
    			}
			}
				//$mail->AddReplyTo($webmaster_email,$webmaster_name);
				//$mail->WordWrap = 50; // set word wrap
				//$mail->IsHTML(true); // send as HTML
				$mail->Subject = 'Error de Folios Floresca:'.$_SESSION['DatabaseName'];
				$mail->Body = $rh_folioequivocado; //HTML Body
				//$mail->AltBody = "This is the body when user views in plain text format"; //Text Body
				$mail_success = $mail->Send();
}
?>