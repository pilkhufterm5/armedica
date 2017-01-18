<?
/**************************************************************************
* Jorge Garcia
* 08/Dec/2008
***************************************************************************/
//DATAGRID
if($_POST['rh_modo'] == 'PAGINA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	echo "<TABLE width='90%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=3>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right colspan=3><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' onClick=ordenar('debtorno')>Cliente</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('id_marca')>Marca</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('categoryid')>Categoria</TD>
	<TD CLASS='tableheader' width='24%' onClick=ordenar('stockid')>Articulo</TD>
	<TD CLASS='tableheader' width='4%'>Descuento(%)</TD>
	<TD CLASS='tableheader' width='4%'></TD>
	<TD CLASS='tableheader' width='4%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_reglaprecios ORDER BY rh_reglaprecios.".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
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
		if($myrow['debtorno'] == 'ALL'){
			$name = 'Todos';
		}else{
			$sql2 = "SELECT name FROM debtorsmaster WHERE debtorno = '".$myrow['debtorno']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$name = $myrow2['name'];
		}
		if($myrow['id_marca'] == 'ALL'){
			$nombre = 'Todos';
		}else{
			$sql2 = "SELECT nombre FROM rh_marca WHERE id = '".$myrow['id_marca']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$nombre = $myrow2['nombre'];
		}
		if($myrow['categoryid'] == 'ALL'){
			$categorydescription = 'Todos';
		}else{
			$sql2 = "SELECT categorydescription FROM stockcategory WHERE categoryid = '".$myrow['categoryid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$categorydescription = $myrow2['categorydescription'];
		}
		if($myrow['stockid'] == 'ALL'){
			$description = 'Todos';
		}else{
			$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$description = $myrow2['description'];
		}
		echo "<TR ".$color.">
		<TD align=left>"."[".$myrow['debtorno']."] ".$name."</TD>
		<TD>"."[".$myrow['id_marca']."] ".$nombre."</TD>
		<TD>"."[".$myrow['categoryid']."] ".$categorydescription."</TD>
		<TD>"."[".$myrow['stockid']."] ".$description."</TD>
		<TD align=right>".$myrow['descuento']." %</TD>
		<TD align=right><a href=# onClick=\"edita('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Edit')."</a></TD>
		<TD align=right><a href=# onclick=\"borra('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Delete')."</a></TD>
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
	$sql = "SELECT * FROM rh_reglaprecios WHERE debtorno = '".$_POST['deb']."' AND id_marca = '".$_POST['mar']."' AND categoryid = '".$_POST['cat']."' AND stockid = '".$_POST['art']."'";
	$result = DB_query($sql,$db);
	$myrow = DB_fetch_array($result);
	if($myrow['stockid'] == 'ALL'){
		$description = 'Todos';
	}else{
		$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
		$result2 = DB_query($sql2,$db);
		$myrow2 = DB_fetch_array($result2);
		$description = $myrow2['description'];
	}
	echo $myrow['debtorno']."||".$myrow['id_marca']."||".$myrow['categoryid']."||Articulo: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<SELECT DISABLED name='articulo'><OPTION value='".$myrow['stockid']."'>".$description."</OPTION></SELECT>||".$myrow['descuento'];
}
//GRABAR
if($_POST['rh_modo'] == 'GRABA'){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$checkSql = "SELECT count(*) FROM rh_reglaprecios WHERE debtorno = '".$_POST['deb']."' AND id_marca = '".$_POST['mar']."' AND categoryid = '".$_POST['cat']."' AND stockid = '".$_POST['art']."'";
	$checkresult = DB_query($checkSql,$db);
	$checkrow = DB_fetch_row($checkresult);
	if ($checkrow[0] > 0) {
		echo "<TABLE width=100%><TR><TD style='background:#fddbdb;color:red;border:1px solid red'>ERROR: Esta regla de descuento ya existe</TD></TR></TABLE>";
	}else{
		echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La regla de descuento ha sido guardada</TD></TR></TABLE>";
		$sql = "INSERT INTO rh_reglaprecios (debtorno,id_marca,categoryid,stockid,descuento) VALUES ('".$_POST['deb']."','".$_POST['mar']."','".$_POST['cat']."','".$_POST['art']."','".$_POST['des']."')";
		$result = DB_query($sql,$db);
	}
	echo "||<TABLE width='90%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=3>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right colspan=3><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' onClick=ordenar('debtorno')>Cliente</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('id_marca')>Marca</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('categoryid')>Categoria</TD>
	<TD CLASS='tableheader' width='24%' onClick=ordenar('stockid')>Articulo</TD>
	<TD CLASS='tableheader' width='4%'>Descuento(%)</TD>
	<TD CLASS='tableheader' width='4%'></TD>
	<TD CLASS='tableheader' width='4%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_reglaprecios ORDER BY rh_reglaprecios.".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
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
		if($myrow['debtorno'] == 'ALL'){
			$name = 'Todos';
		}else{
			$sql2 = "SELECT name FROM debtorsmaster WHERE debtorno = '".$myrow['debtorno']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$name = $myrow2['name'];
		}
		if($myrow['id_marca'] == 'ALL'){
			$nombre = 'Todos';
		}else{
			$sql2 = "SELECT nombre FROM rh_marca WHERE id = '".$myrow['id_marca']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$nombre = $myrow2['nombre'];
		}
		if($myrow['categoryid'] == 'ALL'){
			$categorydescription = 'Todos';
		}else{
			$sql2 = "SELECT categorydescription FROM stockcategory WHERE categoryid = '".$myrow['categoryid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$categorydescription = $myrow2['categorydescription'];
		}
		if($myrow['stockid'] == 'ALL'){
			$description = 'Todos';
		}else{
			$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$description = $myrow2['description'];
		}
		echo "<TR ".$color.">
		<TD align=left>"."[".$myrow['debtorno']."] ".$name."</TD>
		<TD>"."[".$myrow['id_marca']."] ".$nombre."</TD>
		<TD>"."[".$myrow['categoryid']."] ".$categorydescription."</TD>
		<TD>"."[".$myrow['stockid']."] ".$description."</TD>
		<TD align=right>".$myrow['descuento']." %</TD>
		<TD align=right><a href=# onClick=\"edita('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Edit')."</a></TD>
		<TD align=right><a href=# onclick=\"borra('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Delete')."</a></TD>
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
	echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La regla de descuento ha sido actualizada</TD></TR></TABLE>";
	$sql = "UPDATE rh_reglaprecios SET descuento = '".$_POST['des']."' WHERE debtorno = '".$_POST['deb']."' AND id_marca = '".$_POST['mar']."' AND categoryid = '".$_POST['cat']."' AND stockid = '".$_POST['art']."'";
	$result = DB_query($sql,$db);
	echo "||<TABLE width='90%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=3>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right colspan=3><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' onClick=ordenar('debtorno')>Cliente</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('id_marca')>Marca</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('categoryid')>Categoria</TD>
	<TD CLASS='tableheader' width='24%' onClick=ordenar('stockid')>Articulo</TD>
	<TD CLASS='tableheader' width='4%'>Descuento(%)</TD>
	<TD CLASS='tableheader' width='4%'></TD>
	<TD CLASS='tableheader' width='4%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_reglaprecios ORDER BY rh_reglaprecios.".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
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
		if($myrow['debtorno'] == 'ALL'){
			$name = 'Todos';
		}else{
			$sql2 = "SELECT name FROM debtorsmaster WHERE debtorno = '".$myrow['debtorno']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$name = $myrow2['name'];
		}
		if($myrow['id_marca'] == 'ALL'){
			$nombre = 'Todos';
		}else{
			$sql2 = "SELECT nombre FROM rh_marca WHERE id = '".$myrow['id_marca']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$nombre = $myrow2['nombre'];
		}
		if($myrow['categoryid'] == 'ALL'){
			$categorydescription = 'Todos';
		}else{
			$sql2 = "SELECT categorydescription FROM stockcategory WHERE categoryid = '".$myrow['categoryid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$categorydescription = $myrow2['categorydescription'];
		}
		if($myrow['stockid'] == 'ALL'){
			$description = 'Todos';
		}else{
			$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$description = $myrow2['description'];
		}
		echo "<TR ".$color.">
		<TD align=left>"."[".$myrow['debtorno']."] ".$name."</TD>
		<TD>"."[".$myrow['id_marca']."] ".$nombre."</TD>
		<TD>"."[".$myrow['categoryid']."] ".$categorydescription."</TD>
		<TD>"."[".$myrow['stockid']."] ".$description."</TD>
		<TD align=right>".$myrow['descuento']." %</TD>
		<TD align=right><a href=# onClick=\"edita('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Edit')."</a></TD>
		<TD align=right><a href=# onclick=\"borra('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Delete')."</a></TD>
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
	echo "<TABLE width=100%><TR><TD style='background:#b9ecb4;color:darkgreen;border:1px solid darkgreen'>MENSAJE: La Marca ha sido borrada</TD></TR></TABLE>";
	$sql2 = "DELETE FROM rh_reglaprecios WHERE debtorno = '".$_POST['deb']."' AND id_marca = '".$_POST['mar']."' AND categoryid = '".$_POST['cat']."' AND stockid = '".$_POST['art']."'";
	$result2 = DB_query($sql2,$db);
	echo "||<TABLE width='90%'>
	<TR><TD align=left><A HREF=# onClick=pagina(-1)>Atras</A></TD><TD align=center colspan=3>pagina: ".$_POST['pagina']." / ".$_POST['tpagina']."</TD><TD align=right colspan=3><A HREF=# onClick=pagina(1)>Adelante</A></TD><TR>
	<TR>
	<TD CLASS='tableheader' onClick=ordenar('debtorno')>Cliente</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('id_marca')>Marca</TD>
	<TD CLASS='tableheader' width='18%' onClick=ordenar('categoryid')>Categoria</TD>
	<TD CLASS='tableheader' width='24%' onClick=ordenar('stockid')>Articulo</TD>
	<TD CLASS='tableheader' width='4%'>Descuento(%)</TD>
	<TD CLASS='tableheader' width='4%'></TD>
	<TD CLASS='tableheader' width='4%'></TD>
	</TR>";
	$sql = "SELECT * FROM rh_reglaprecios ORDER BY rh_reglaprecios.".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*20).",20";
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
		if($myrow['debtorno'] == 'ALL'){
			$name = 'Todos';
		}else{
			$sql2 = "SELECT name FROM debtorsmaster WHERE debtorno = '".$myrow['debtorno']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$name = $myrow2['name'];
		}
		if($myrow['id_marca'] == 'ALL'){
			$nombre = 'Todos';
		}else{
			$sql2 = "SELECT nombre FROM rh_marca WHERE id = '".$myrow['id_marca']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$nombre = $myrow2['nombre'];
		}
		if($myrow['categoryid'] == 'ALL'){
			$categorydescription = 'Todos';
		}else{
			$sql2 = "SELECT categorydescription FROM stockcategory WHERE categoryid = '".$myrow['categoryid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$categorydescription = $myrow2['categorydescription'];
		}
		if($myrow['stockid'] == 'ALL'){
			$description = 'Todos';
		}else{
			$sql2 = "SELECT description FROM stockmaster WHERE stockid = '".$myrow['stockid']."'";
			$result2 = DB_query($sql2,$db);
			$myrow2 = DB_fetch_array($result2);
			$description = $myrow2['description'];
		}
		echo "<TR ".$color.">
		<TD align=left>"."[".$myrow['debtorno']."] ".$name."</TD>
		<TD>"."[".$myrow['id_marca']."] ".$nombre."</TD>
		<TD>"."[".$myrow['categoryid']."] ".$categorydescription."</TD>
		<TD>"."[".$myrow['stockid']."] ".$description."</TD>
		<TD align=right>".$myrow['descuento']." %</TD>
		<TD align=right><a href=# onClick=\"edita('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Edit')."</a></TD>
		<TD align=right><a href=# onclick=\"borra('".$myrow['debtorno']."','".$myrow['id_marca']."','".$myrow['categoryid']."','".$myrow['stockid']."')\">"._('Delete')."</a></TD>
		</TR>";
	}
	echo "</TABLE>";
}
//ARTICULOS
if($_POST['rh_modo'] == 'ARTICULOS'){
	if($_POST['buscacion'] == 'SINARTICULOS'){
		$_POST['buscacion'] = '';
	}
	if($_POST['mar'] == 'ALL'){
		$marca = " LIKE '%' ";
	}else{
		$marca = " = '".$_POST['mar']."' ";
	}
	if($_POST['cat'] == 'ALL'){
		$categoria = " LIKE '%' ";
	}else{
		$categoria = " = '".$_POST['cat']."' ";
	}
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	$PageSecurity = 1;
	include('includes/session.inc');
	include('includes/SQL_CommonFunctions.inc');
	$sqlcount = "SELECT COUNT(stockid) AS paginas FROM stockmaster WHERE (stockid LIKE '%".$_POST['buscacion']."%' OR description LIKE '%".$_POST['buscacion']."%') AND rh_marca ".$marca." AND categoryid ".$categoria."";
	$resultcount = DB_query($sqlcount,$db);
	$rowcount = DB_fetch_array($resultcount);
	$tpagarray = explode('.',($rowcount['paginas']/10));
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
	echo "<a href=# onClick=oarticulos()>Ocultar Articulos</a><BR><CENTER>
	<input type='hidden' id='actulpagina2' value='".$_POST['pagina']."'><input type='hidden' id='totalpagina2' value='".$_POST['tpagina']."'><input type='hidden' id='ordenado2' value='".$_POST['ordenado']."'><input type='hidden' id='ad2' value='".$_POST['ad']."'><input type='hidden' id='mostrar' value='1'>
	<input type='text' id='buscacion' value='".$_POST['buscacion']."'><INPUT TYPE='button' VALUE='Buscar' onclick=\"buscararticulo()\"><br>
	<TABLE width='100%'>
	<TR>
	<TD><A HREF=# onClick=pagina2(-1)>Atras</A></TD><TD align='center' colspan='2'>Pagina: ".$_POST['pagina']."/".$_POST['tpagina']."</TD><TD align='right'><A HREF=# onClick=pagina2(1)>Adelante</A></TD>
	</TR>
	<TR>
	<TD CLASS='tableheader' onClick=ordenar2('stockid') width='25%'>Codigo</TD>
	<TD CLASS='tableheader' onClick=ordenar2('description')>Descripcion</TD>
	<TD CLASS='tableheader' width='10%'>Descuento(%)</TD>
	<TD CLASS='tableheader' width='10%'></TD>
	</TR>";
	$sql = "SELECT stockid, description FROM stockmaster WHERE (stockid LIKE '%".$_POST['buscacion']."%' OR description LIKE '%".$_POST['buscacion']."%') AND rh_marca ".$marca." AND categoryid ".$categoria." ORDER BY ".$_POST['ordenado']." ".$_POST['ad']." LIMIT ".(($_POST['pagina']-1)*10).",10";
	$result = DB_query($sql,$db);
	$k = 0;
	$rh = 1;
	while ($myrow = DB_fetch_array($result)) {
		if ($k==1){
			$color = "BGCOLOR='#CCCCCC'";
			$k=0;
		} else {
			$color = "BGCOLOR='#EEEEEE'";
			$k=1;
		}
		echo "<TR ".$color.">
		<TD align=right>".$myrow['stockid']."</TD>
		<TD>".$myrow['description']."</TD>
		<TD align='center'><INPUT TYPE='text' SIZE=2 MAXLENGTH=2 NAME='descuento".$rh."'></TD>
		<TD align=right><a href=# onclick=grabaarticulo('".$rh."','".$myrow['stockid']."')>Grabar</a></TD>
		</TR>";
		$rh++;
	}
	echo "</TABLE>
	</CENTER>";
}
/**************************************************************************
* Jorge Garcia Fin Modificacion
***************************************************************************/
?>