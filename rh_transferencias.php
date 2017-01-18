<?php
$PageSecurity = 11;

include('includes/session.inc');
include_once('includes/SQL_CommonFunctions.inc');
$title = _('Transferencias');
include('includes/header.inc');
if(isset($_FILES['Transferencia'])){
	$type=16;
	$filename=dirname(__FILE__).'/importfiles/'.$_FILES['Transferencia']["name"];
	move_uploaded_file($_FILES['Transferencia']["tmp_name"],$filename);
	$PorTransferir=
	$errores=array();
	if(@$fh_in = fopen($filename,"r")){
        	$separador=",";
        	$fila=0;
            while(($line = fgetcsv($fh_in,0,$separador))!==false){
                if($line!= null){
                	if(count($line)==1){
                		if(strpos($line[0],'|'))$separador='|';
                		else
                		if(strpos($line[0],';'))$separador=';';
                		$line=explode($separador,$line[0]);
	                	
                	}
                	$SQL ="select * from stockmaster where stockid='".$line[0]."'";
                	if($linea[1]=='')$linea[1]=0;
                	if($line[0]!=''){
	                	$res=DB_Query($SQL,$db);
	                	if(DB_num_rows($res)){
	                		$row=DB_fetch_assoc($res);
	                		$line[0]=$row['stockid'];
	                		$PorTransferir[]=$line;
	                	}elseif($fila)
	                	{
	                		$errores[$fila+1]=$line;
	                	}
                	}else
                	if($linea[1]==0){
                		$errores[$fila+1]=$line;
                	}
                	$fila++;
                }
            }
            
            if(count($PorTransferir)){
            	$TotalRegistros=0;
            	$Trf_ID = GetNextTransNo($type,$db);
            	$DefaultDispatchDate= date($_SESSION["DefaultDateFormat"]);
            	$PeriodNo = GetPeriod($DefaultDispatchDate, $db);
            	$DefaultDispatchDate=FormatDateForSQL($DefaultDispatchDate);
            	DB_Txn_Begin($db);
            	foreach($PorTransferir as $linea){
            		$TotalRegistros++;
	            	$SQL="insert into loctransfers(reference, stockid, shipqty, recqty, shipdate, recdate, shiploc, recloc, rh_change, rh_usrrecd, rh_usrsend ) values(";
	            	$SQL.=$Trf_ID.",";
	            	$SQL.="'".DB_escape_string($linea[0])."',";
	            	$SQL.=DB_escape_string($linea[1]).",";
	            	$SQL.="0,";
	            	$SQL.="'".$DefaultDispatchDate."',";
	            	$SQL.="'0000-00-00',";
	            	$SQL.="'".DB_escape_string($_REQUEST['From'])."',";
	            	$SQL.="'".DB_escape_string($_REQUEST['To'])."',";
	            	$SQL.="'".DB_escape_string($_REQUEST['Texto'].($linea[2]!=''?'::':'').$linea[2])."',";
	            	$SQL.="'".DB_escape_string($_SESSION['UserID'])."',";
	            	$SQL.="''";
	            	$SQL.=")";
	            	DB_query($SQL,$db);
	            	$SQL='insert into stockmoves(stkmoveno, stockid, type, transno, loccode, trandate, debtorno, branchcode, price, prd, reference, qty, discountpercent, standardcost, show_on_inv_crds, hidemovt, narrative, description, rh_orderline) values(';
	            	$SQL.="0,";
	            	$SQL.="'".DB_escape_string($linea[0])."',";
	            	$SQL.="'".DB_escape_string($type)."',";
	            	$SQL.=$Trf_ID.",";
	            	$SQL.="'".DB_escape_string($_REQUEST['From'])."',";
	            	$SQL.="'".$DefaultDispatchDate."',";
	            	$SQL.="'',";
	            	$SQL.="'',";
	            	$SQL.="0,";
	            	$SQL.=$PeriodNo.",";
	            	$SQL.= "'".DB_escape_string(_('To') . ' '.$_REQUEST['To'])."',";
	            	$SQL.="'".DB_escape_string(-$linea[1])."',";
	            	$SQL.="0,";
	            	$SQL.="0,";
	            	$SQL.="1,";
	            	$SQL.="0,";
	            	$SQL.="'".DB_escape_string($_REQUEST['Texto'].($linea[2]!=''?'::':'').$linea[2])."',";
	            	$SQL.="'',";
	            	$SQL.="NULL";
	            	$SQL.=")";
	            	//DB_query($SQL,$db);
	            	//$idFrom=DB_Last_Insert_ID($db,'stockmoves','id');
	            	
	            	$SQL='insert into stockmoves(stkmoveno, stockid, type, transno, loccode, trandate, debtorno, branchcode, price, prd, reference, qty, discountpercent, standardcost, show_on_inv_crds, hidemovt, narrative, description, rh_orderline) values(';
	            	$SQL.="0,";
	            	$SQL.="'".DB_escape_string($linea[0])."',";
	            	$SQL.="'".DB_escape_string($type)."',";
	            	$SQL.=$Trf_ID.",";
	            	$SQL.="'".DB_escape_string($_REQUEST['To'])."',";
	            	$SQL.="'".$DefaultDispatchDate."',";
	            	$SQL.="'',";
	            	$SQL.="'',";
	            	$SQL.="0,";
	            	$SQL.=$PeriodNo.",";
	            	$SQL.= "'".DB_escape_string(_('From') . ' '.$_REQUEST['From'])."',";
	            	$SQL.="'".DB_escape_string($linea[1])."',";
	            	$SQL.="0,";
	            	$SQL.="0,";
	            	$SQL.="1,";
	            	$SQL.="0,";
	            	$SQL.="'".DB_escape_string($_REQUEST['Texto'].($linea[2]!=''?'::':'').$linea[2])."',";
	            	$SQL.="'',";
	            	$SQL.="NULL";
	            	$SQL.=")";
	            	//DB_query($SQL,$db);
	            	//$idTo=DB_Last_Insert_ID($db,'stockmoves','id');
	            	
            		if(false&&isset($linea[3])&&trim($linea[3])!=''){//Stockserialitems 
	            		$SQL="select * from stockserialitems where stockid='".DB_escape_string($linea[0]).
	            			"' and serialno='".DB_escape_string($linea[3])."' and loccode='".DB_escape_string($_REQUEST['To'])."'";
	            		$ress=DB_query($SQL,$db);
	            		if(!DB_num_rows($res)){
	            			$SQL="insert into stockserialitems select * from stockserialitems where stockid='".DB_escape_string($linea[0]).
	            			"' and serialno='".DB_escape_string($linea[3])."' and loccode='".DB_escape_string($_REQUEST['From'])."'";
	            			$ress=DB_query($SQL,$db);
	            		}
	            		$SQL="insert into stockserialmoves values(";
	            		$SQL.="0,";
	            		$SQL.=$idFrom.",";
	            		$SQL.="'".DB_escape_string($linea[0])."',";
	            		$SQL.="'".DB_escape_string($linea[3])."',";
	            		$SQL.="'".DB_escape_string(-$linea[1])."'";
	            		$SQL.=")";
	            		$ress=DB_query($SQL,$db);
	            		
	            		$SQL="insert into stockserialmoves values(";
	            		$SQL.="0,";
	            		$SQL.=$idTo.",";
	            		$SQL.="'".DB_escape_string($linea[0])."',";
	            		$SQL.="'".DB_escape_string($linea[3])."',";
	            		$SQL.="'".DB_escape_string($linea[1])."'";
	            		$SQL.=")";
	            		$ress=DB_query($SQL,$db);
	            		$SQL="update stockserialitems set quantity=quantity+'".$linea[1]."' where stockid='".DB_escape_string($linea[0]).
	            			"' and serialno='".DB_escape_string($linea[3])."' and loccode='".DB_escape_string($_REQUEST['To'])."'";
	            		DB_query($SQL,$db);
	            		$SQL="update stockserialitems set quantity=quantity+'".(-$linea[1])."' where stockid='".DB_escape_string($linea[0]).
	            			"' and serialno='".DB_escape_string($linea[3])."' and loccode='".DB_escape_string($_REQUEST['From'])."'";
	            		DB_query($SQL,$db);
	            		/*
	            		 * TODO Agregar pedimentos
	            		 */	
	            	}
            	}
            	prnMsg(_('Documento de transferencia '.$Trf_ID.' generado exitosamente'),'success');
            	//DB_Txn_Rollback($db);
            	DB_Txn_Commit($db);
            }
            if(count($errores)){
            	prnMsg(_('Los siguientes registros contienen errores'),'error');
            	echo '<center>';
            	echo '<table>';
            	foreach($errores as $id=>$valor){
            		echo '<tr>';
            		echo '<td>';
            		echo 'Fila '.$id;
            		echo '</td>';
            		foreach($valor as $celda){
            			echo '<td>';
            			echo htmlentities($celda);
            			echo '</td>';
            		}
            		echo '</tr>';
            	}
            	echo '</table>';
            	echo '</center>';
            }
	}
	
}
$sql = 'SELECT loccode, locationname FROM locations';
$resultStkLocs = DB_query($sql,$db);
$Locaciones=array();
while ($myrow=DB_fetch_array($resultStkLocs)){
	$Locaciones[$myrow['loccode']]=$myrow['locationname'];
}

echo '<form enctype="multipart/form-data" method="post">'; 
echo '<center>';
echo '<p><h3>';
echo _('Transferir inventarios');
echo '</h3></p>';
echo '<table>';
echo '<tr>';
echo '<th>';
echo _('Desde');
echo '</th><th>';
echo _('Hasta');
echo '</th>';
echo '</tr>';
echo '<tr>';
echo '<td>';
echo '<select name="From">';
foreach($Locaciones as $id=>$val){
	echo '<option value="'.$id.'"';
	if(isset($_REQUEST['From'])&&$_REQUEST['From']==$id) echo ' selected=selected ';
	echo '>';
		echo htmlentities($val);
	echo '</option>';
}
echo '</select>';
echo '</td><td>';
echo '<select name="To">';
foreach($Locaciones as $id=>$val){
	echo '<option value="'.$id.'"';
	if(isset($_REQUEST['To'])&&$_REQUEST['To']==$id) echo ' selected=selected ';
	echo '>';
		echo htmlentities($val);
	echo '</option>';
}
echo '</select>';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<th colspan=2>';
echo '<center>';
echo '</center>';
echo _('Comentario');
echo '</th>';
echo '</tr>';

echo '<tr>';
echo '<td colspan=2>';
echo '<center>';

echo '<input type=text name="Texto">';
echo '</center>';
echo '</td>';
echo '</tr>';

echo '<tr>';
echo '<th colspan=2>';
echo '<center>';
echo _('Archivo');
echo '</center>';
echo '</th>';
echo '</tr>';

echo '<tr>';
echo '<td colspan=2>';
echo '<input type=file name="Transferencia">Stockid | Cantidad | Comentario';
echo '</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan=2><center>';
echo '<input type=submit name="Transferir" value="'._('Transferir').'">';
echo '</center></td>';
echo '</tr>';
echo '</table>';
echo '</center>';
echo '</form>';
include('includes/footer.inc');