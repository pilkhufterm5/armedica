<?
/**************************************************************************
* Jorge Garcia
* 03/Dec/2008
***************************************************************************/
//DATAGRID
if($_POST['rh_modo'] == 'PAGINA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(descripcion) AS paginas FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%'";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/20));
	if($tpagarray[1] > 0){
		$_POST['tpagina'] = $tpagarray[0] + 1;
	}else{
		if($tpagarray[1] == 0){
			$_POST['tpagina'] = 1;
		}else{
			$_POST['tpagina'] = $tpagarray[0];
		}		
	}
	if($_POST['tpagina']<$_POST['pagina']){
		$_POST['pagina'] = 1;
	}
	echo "<FORM name='forma'>
	<input type='hidden' id='actulpagina' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado' value='".$_POST['ordenado']."'><input type='hidden' id='ad' value='".$_POST['ad']."'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
	</FORM>
	<TABLE width='45%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' width='8%' onClick=ordenar('codigo')>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar('nombre')>Nombre</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%' ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['codigo']."</TD>
		<TD>".$myrow['nombre']."</TD>
		<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
		<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
		</TR>";
	}
	echo "</TABLE>";
}
//EDITA
if($_POST['rh_modo'] == 'EDITA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sql = "SELECT * FROM rh_gamma WHERE id = ".$_POST['id']."";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_array($result);
	echo $myrow['id']."||".$myrow['codigo']."||".$myrow['descripcion'];
}
//GRABAR
if($_POST['rh_modo'] == 'GRABA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(descripcion) AS paginas FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%'";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/20));
	if($tpagarray[1] > 0){
		$_POST['tpagina'] = $tpagarray[0] + 1;
	}else{
		if($tpagarray[1] == 0){
			$_POST['tpagina'] = 1;
		}else{
			$_POST['tpagina'] = $tpagarray[0];
		}		
	}
	if($_POST['tpagina']<$_POST['pagina']){
		$_POST['pagina'] = 1;
	}
	$checkSql = "SELECT count(*) FROM rh_gamma WHERE codigo = '".str_replace(' ','',strtoupper($_POST['codigo']))."'";
	$checkresult = DB_query($checkSql,$db);
	$checkrow = DB_fetch_row($checkresult);
	if ($checkrow[0] > 0) {
		echo "<TABLE width=100%><TR><TD style='background:#fddbdb;color:red;border:1px solid red'>ERROR: El Codigo ".str_replace(' ','',strtoupper($_POST['codigo']))." ya existe</TD></TR></TABLE>";
	}else{
		echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La Marca ".$_POST['nombre']." ha sido guardada</TD></TR></TABLE>";
		$sql = "INSERT INTO rh_gamma (codigo,descripcion) VALUES ('".str_replace(' ','',strtoupper($_POST['codigo']))."','".$_POST['nombre']."')";
		$result = DB_query($sql,$db);
	}
	echo "||<FORM name='forma'>
	<input type='hidden' id='actulpagina' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado' value='".$_POST['ordenado']."'><input type='hidden' id='ad' value='".$_POST['ad']."'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
	</FORM>
	<TABLE width='45%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' width='8%' onClick=ordenar('codigo')>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar('descripcion')>Nombre</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%' ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['codigo']."</TD>
		<TD>".$myrow['descripcion']."</TD>
		<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
		<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
		</TR>";
	}
	echo "</TABLE>";
}
//ACTUALIZAR
if($_POST['rh_modo'] == 'ACTUALIZA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(descripcion) AS paginas FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%'";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/20));
	if($tpagarray[1] > 0){
		$_POST['tpagina'] = $tpagarray[0] + 1;
	}else{
		if($tpagarray[1] == 0){
			$_POST['tpagina'] = 1;
		}else{
			$_POST['tpagina'] = $tpagarray[0];
		}		
	}
	if($_POST['tpagina']<$_POST['pagina']){
		$_POST['pagina'] = 1;
	}
	echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La Especie ".$_POST['nombre']." ha sido actualizada</TD></TR></TABLE>";
	$sql = "UPDATE rh_gamma SET descripcion = '".$_POST['nombre']."' WHERE id = '".$_POST['id']."'";
	$result = DB_query($sql,$db);
	echo "||<FORM name='forma'>
	<input type='hidden' id='actulpagina' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado' value='".$_POST['ordenado']."'><input type='hidden' id='ad' value='".$_POST['ad']."'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
	</FORM>
	<TABLE width='45%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' width='8%'onClick=ordenar('codigo')>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar('nombre')>Nombre</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%' ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['codigo']."</TD>
		<TD>".$myrow['descripcion']."</TD>
		<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
		<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
		</TR>";
	}
	echo "</TABLE>";
}
//BORRA
if($_POST['rh_modo'] == 'BORRA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(descripcion) AS paginas FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%'";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/20));
	if($tpagarray[1] > 0){
		$_POST['tpagina'] = $tpagarray[0] + 1;
	}else{
		if($tpagarray[1] == 0){
			$_POST['tpagina'] = 1;
		}else{
			$_POST['tpagina'] = $tpagarray[0];
		}		
	}
	if($_POST['tpagina']<$_POST['pagina']){
		$_POST['pagina'] = 1;
	}
	$sql= "SELECT COUNT(*) FROM rh_gamma_stock WHERE idGamma = '".$_POST['id']."'";
	$ErrMsg = _('The number of transactions using this customer/sales/pricelist type could not be retrieved');
	$result = DB_query($sql,$db,$ErrMsg);
	$myrow = DB_fetch_row($result);
	if ($myrow[0]>0) {
		echo "<TABLE width=100%><TR><TD style='background:#fddbdb;color:red;border:1px solid red'>ERROR: La Especie no pude ser borrada porque esta asignada a un producto</TD></TR></TABLE>";
	} else {
		echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La Especie ha sido borrada</TD></TR></TABLE>";
		$sql2 = "DELETE FROM rh_gamma WHERE id = '".$_POST['id']."'";
		$result2 = DB_query($sql2,$db);
	}
	echo "||<FORM name='forma'>
	<input type='hidden' id='actulpagina' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado' value='".$_POST['ordenado']."'><input type='hidden' id='ad' value='".$_POST['ad']."'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
	</FORM><TABLE width='45%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' width='8%'onClick=ordenar('codigo')>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar('nombre')>Nombre</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%' ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['codigo']."</TD>
		<TD>".$myrow['descripcion']."</TD>
		<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
		<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
		</TR>";
	}
	echo "</TABLE>";
}
//BUSCAR
if($_POST['rh_modo'] == 'BUSCA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(descripcion) AS paginas FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%'";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/20));
	if($tpagarray[1] > 0){
		$_POST['tpagina'] = $tpagarray[0] + 1;
	}else{
		if($tpagarray[1] == 0){
			$_POST['tpagina'] = 1;
		}else{
			$_POST['tpagina'] = $tpagarray[0];
		}		
	}
	if($_POST['tpagina']<$_POST['pagina']){
		$_POST['pagina'] = 1;
	}
	echo "<FORM name='forma'>
	<input type='hidden' id='actulpagina' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado' value='".$_POST['ordenado']."'><input type='hidden' id='ad' value='".$_POST['ad']."'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onClick=buscararticulo()>
	</FORM>
	<TABLE width='45%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=2>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' width='8%' onClick=ordenar('codigo')>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar('descripcion')>Nombre</TD>
	<TD CLASS='tableheader' width='8%'></TD>
	<TD CLASS='tableheader' width='8%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_gamma WHERE descripcion LIKE '%".$_POST['buscacion']."%' ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
	$result = DB_query($sql,$db);
	$k=0;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['codigo']."</TD>
		<TD>".$myrow['descripcion']."</TD>
		<TD align=right><a href=# onClick=\"edita(".$myrow['id'].")\">"._('Edit')."</TD>
		<TD align=right><a href=# onclick=\"borra(".$myrow['id'].")\">"._('Delete')."</TD>
		</TR>";
	}
	echo "</TABLE>";
}
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
?>