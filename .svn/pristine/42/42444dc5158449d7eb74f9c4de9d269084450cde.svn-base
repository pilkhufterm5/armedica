//Funcion para ajax
function create_ajaxOb(){
	var xmlHttp;
	try{// Firefox, Opera 8.0+, Safari
		xmlHttp = new XMLHttpRequest();
	}
	catch(e){
		// Internet Explorer
		try{
			xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(e){
			try{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch(e){
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}
//ORDENA
function ordenar(q){
	document.getElementById('msn').innerHTML = "";
	document.forma.ordenado.value = q;
	if(document.forma.ad.value == 'ASC'){
		document.forma.ad.value = 'DESC';
	}else{
		document.forma.ad.value = 'ASC';
	}
	xmlhttp = create_ajaxOb();
	var url = "rh_reglaprecios_ope.php";
	var posts = "rh_modo=PAGINA&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value;
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4){//cuando la respuesta llegue
			respuesta = xmlhttp.responseText;
			document.getElementById('datagrid').innerHTML = respuesta;
		}
	}
	xmlhttp.open("post", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", posts.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(posts);
}
//PAGINA
function pagina(p){
	document.getElementById('msn').innerHTML = "";
	nuevapagina = (parseInt(document.forma.actulpagina.value) + (p));
	if(nuevapagina <= parseInt(document.forma.totalpagina.value) && nuevapagina > 0){
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo=PAGINA&pagina="+nuevapagina+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				document.getElementById('datagrid').innerHTML = respuesta;
				document.forma.actulpagina.value = nuevapagina;
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);	
	}
}
//EDITAR
function edita(deb,mar,cat,art){
	document.getElementById('msn').innerHTML = '';
	xmlhttp = create_ajaxOb();
	var url = "rh_reglaprecios_ope.php";
	var posts = "rh_modo=EDITA&deb="+deb+"&mar="+mar+"&cat="+cat+"&art="+art;
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4){//cuando la respuesta llegue
			respuesta = xmlhttp.responseText;
			mi_array = new Array();
			mi_array = respuesta.split('||');
			document.forma2.cliente.value = mi_array[0];
			document.forma2.cliente.disabled = true;
			document.forma2.marca.value = mi_array[1];
			document.forma2.marca.disabled = true;
			document.forma2.categoria.value = mi_array[2];
			document.forma2.categoria.disabled = true;
			document.getElementById('arti').innerHTML = mi_array[3];
			document.forma2.descuento.value = mi_array[4];
			document.forma2.pros.value = 1;
		}
	}
	xmlhttp.open("post", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", posts.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(posts);
}
//GRABAR / ACTUALIZAR
function graba(){
	document.getElementById('msn').innerHTML = "";
	error = 1;
	if(document.forma2.pros.value == 0){
		modo = 'GRABA';
	}else{
		modo = 'ACTUALIZA';
	}
	if(document.forma2.descuento.value == '' || document.forma2.descuento.value == ' ' || document.forma2.descuento.value == '  ' || document.forma2.descuento.value <= 0){
		alert('El porcentaje de descuento tiene que ser mayor de 0.');
		error = 0;
	}
	if(document.forma2.cliente.value == 'ALL' && document.forma2.marca.value == 'ALL' && document.forma2.categoria.value == 'ALL'){
		alert('La regla de descuento no puede aplicarse a todos los clientes, con todas las marcas, con todas las categorias.');
		error = 0;
	}
	if(error == 1){
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo="+modo+"&deb="+document.forma2.cliente.value+"&mar="+document.forma2.marca.value+"&cat="+document.forma2.categoria.value+"&art="+document.forma2.articulo.value+"&des="+document.forma2.descuento.value+"&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				mi_array = new Array();
				mi_array = respuesta.split('||');
				document.getElementById('msn').innerHTML = mi_array[0];
				document.getElementById('datagrid').innerHTML = mi_array[1];
				document.forma2.cliente[0].selected = true;
				document.forma2.cliente.disabled = false;
				document.forma2.marca.value = 'ALL';
				document.forma2.marca.disabled = false;
				document.forma2.categoria.value = 'ALL';
				document.forma2.categoria.disabled = false;
				document.getElementById('arti').innerHTML = "<input type='hidden' id='actulpagina2' value='1'><input type='hidden' id='totalpagina2' value='1'><input type='hidden' id='ordenado2' value='stockid'><input type='hidden' id='ad2' value='ASC'><input type='hidden' id='mostrar' value='0'><input type='hidden' id='buscacion' value='SINARTICULOS'><input type='hidden' id='articulo' value='ALL'><a href=# onClick=marticulos()>Mostrar Articulos</a>";
				document.forma2.descuento.value = '';
				document.forma2.pros.value = 0;
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);
	}
}
//BORRAR
function borra(deb,mar,cat,art){
	document.getElementById('msn').innerHTML = "";
	if(confirm('Esta seguro de borrar esta regla de descuento?')){
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo=BORRA&deb="+deb+"&mar="+mar+"&cat="+cat+"&art="+art+"&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				mi_array = new Array();
				mi_array = respuesta.split('||');
				document.getElementById('msn').innerHTML = mi_array[0];
				document.getElementById('datagrid').innerHTML = mi_array[1];
				document.forma2.cliente[0].selected = true;
				document.forma2.cliente.disabled = false;
				document.forma2.marca.value = 'ALL';
				document.forma2.marca.disabled = false;
				document.forma2.categoria.value = 'ALL';
				document.forma2.categoria.disabled = false;
				document.getElementById('arti').innerHTML = "<input type='hidden' id='actulpagina2' value='1'><input type='hidden' id='totalpagina2' value='1'><input type='hidden' id='ordenado2' value='stockid'><input type='hidden' id='ad2' value='ASC'><input type='hidden' id='mostrar' value='0'><input type='hidden' id='buscacion' value='SINARTICULOS'><input type='hidden' id='articulo' value='ALL'><a href=# onClick=marticulos()>Mostrar Articulos</a>";
				document.forma2.descuento.value = '';
				document.forma2.pros.value = 0;
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);
	}
}
//BORRAR
function cancela(){
	document.getElementById('msn').innerHTML = "";
	document.forma2.cliente[0].selected = true;
	document.forma2.cliente.disabled = false;
	document.forma2.marca.value = 'ALL';
	document.forma2.marca.disabled = false;
	document.forma2.categoria.value = 'ALL';
	document.forma2.categoria.disabled = false;
	document.getElementById('arti').innerHTML = "<input type='hidden' id='actulpagina2' value='1'><input type='hidden' id='totalpagina2' value='1'><input type='hidden' id='ordenado2' value='stockid'><input type='hidden' id='ad2' value='ASC'><input type='hidden' id='mostrar' value='0'><input type='hidden' id='buscacion' value='SINARTICULOS'><input type='hidden' id='articulo' value='ALL'><a href=# onClick=marticulos()>Mostrar Articulos</a>";
	document.forma2.descuento.value = '';
	document.forma2.pros.value = 0;
}
//MARCA y CATEGORIA
function cambio(){
	if(document.forma2.mostrar.value == 1){
		if(document.forma2.marca.value == 'ALL' || document.forma2.categoria.value == 'ALL'){
			oarticulos();
		}else{
			q = document.forma2.marca.value;
			c = document.forma2.categoria.value;
			pagina = document.forma2.actulpagina2.value;
			ordenado = document.forma2.ordenado2.value;
			ad = document.forma2.ad2.value;
			tpagina = document.forma2.totalpagina2.value;
			buscacion = document.forma2.buscacion.value;
			xmlhttp = create_ajaxOb();
			var url = "rh_reglaprecios_ope.php";
			var posts = "rh_modo=ARTICULOS&mar="+q+"&cat="+c+"&pagina="+pagina+"&ordenado="+ordenado+"&ad="+ad+"&tpagina="+tpagina+"&buscacion="+buscacion;
			xmlhttp.onreadystatechange = function(){
				if(xmlhttp.readyState == 4){//cuando la respuesta llegue
					respuesta = xmlhttp.responseText;
					document.getElementById('arti').innerHTML = respuesta;
				}
			}
			xmlhttp.open("post", url, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", posts.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.send(posts);
		}
	}
}
//MOSTRAR ARTICULOS
function marticulos(){
	if(document.forma2.marca.value == 'ALL' || document.forma2.categoria.value == 'ALL'){
		alert('Seleccione una marca y una categoria');
	}else{
		document.forma2.mostrar.value = 1;
		cambio();
		document.getElementById('descuen').innerHTML = '';
		document.getElementById('descuen2').innerHTML = '';
		document.getElementById('btns').innerHTML = '';
	}
}
//OCULTAR ARTICULOS
function oarticulos(){
	document.forma2.mostrar.value = 0;
	document.getElementById('arti').innerHTML = "<input type='hidden' id='actulpagina2' value='1'><input type='hidden' id='totalpagina2' value='1'><input type='hidden' id='ordenado2' value='stockid'><input type='hidden' id='ad2' value='ASC'><input type='hidden' id='mostrar' value='0'><input type='hidden' id='buscacion' value='SINARTICULOS'><input type='hidden' id='articulo' value='ALL'><a href=# onClick=marticulos()>Mostrar Articulos</a>";
	document.getElementById('descuen').innerHTML = "Descuento (%)";
	document.getElementById('descuen2').innerHTML = "<INPUT TYPE='text' SIZE=2 MAXLENGTH=2 NAME='descuento'>";
	document.getElementById('btns').innerHTML = "<INPUT TYPE='button' VALUE='Aceptar' onclick=graba()><INPUT TYPE='button' VALUE='Cancelar' onclick=\"cancela()\">";	
}
//GRABA2
function grabaarticulo(n,stk){
	document.getElementById('msn').innerHTML = "";
	error = 1;
	if(document.forma2['descuento'+n].value == '' || document.forma2['descuento'+n].value == ' ' || document.forma2['descuento'+n].value == '  ' || document.forma2['descuento'+n].value <= 0){
		alert('El porcentaje de descuento tiene que ser mayor de 0');
		error = 0;
	}
	if(error == 1){
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo=GRABA&deb="+document.forma2.cliente.value+"&mar="+document.forma2.marca.value+"&cat="+document.forma2.categoria.value+"&art="+stk+"&des="+document.forma2['descuento'+n].value+"&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				mi_array = new Array();
				mi_array = respuesta.split('||');
				document.getElementById('msn').innerHTML = mi_array[0];
				document.getElementById('datagrid').innerHTML = mi_array[1];
				document.forma2['descuento'+n].value = '';
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);
	}
}
//PAGINA2
function pagina2(p){
	nuevapagina = (parseInt(document.forma2.actulpagina2.value) + (p));
	if(nuevapagina <= parseInt(document.forma2.totalpagina2.value) && nuevapagina > 0){
		q = document.forma2.marca.value;
		c = document.forma2.categoria.value;
		pagina = nuevapagina;
		ordenado = document.forma2.ordenado2.value;
		ad = document.forma2.ad2.value;
		tpagina = document.forma2.totalpagina2.value;
		buscacion = document.forma2.buscacion.value;
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo=ARTICULOS&mar="+q+"&cat="+c+"&pagina="+pagina+"&ordenado="+ordenado+"&ad="+ad+"&tpagina="+tpagina+"&buscacion="+buscacion;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				document.getElementById('arti').innerHTML = respuesta;
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);
	}
}
//BUSCAR ARTICULO
function buscararticulo(){
		q = document.forma2.marca.value;
		c = document.forma2.categoria.value;
		pagina = 1;
		ordenado = 'stockid';
		ad = 'ASC';
		tpagina = 1;
		buscacion = document.forma2.buscacion.value;
		xmlhttp = create_ajaxOb();
		var url = "rh_reglaprecios_ope.php";
		var posts = "rh_modo=ARTICULOS&mar="+q+"&cat="+c+"&pagina="+pagina+"&ordenado="+ordenado+"&ad="+ad+"&tpagina="+tpagina+"&buscacion="+buscacion;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				document.getElementById('arti').innerHTML = respuesta;
			}
		}
		xmlhttp.open("post", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", posts.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.send(posts);
}
//ORDENA2
function ordenar2(q){
	document.forma2.ordenado2.value = q;
	if(document.forma2.ad2.value == 'ASC'){
		document.forma2.ad2.value = 'DESC';
	}else{
		document.forma2.ad2.value = 'ASC';
	}
	q = document.forma2.marca.value;
	c = document.forma2.categoria.value;
	pagina = document.forma2.actulpagina2.value;
	ordenado = document.forma2.ordenado2.value;
	ad = document.forma2.ad2.value;
	tpagina = document.forma2.totalpagina2.value;
	buscacion = document.forma2.buscacion.value;
	xmlhttp = create_ajaxOb();
	var url = "rh_reglaprecios_ope.php";
	var posts = "rh_modo=ARTICULOS&mar="+q+"&cat="+c+"&pagina="+pagina+"&ordenado="+ordenado+"&ad="+ad+"&tpagina="+tpagina+"&buscacion="+buscacion;
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4){//cuando la respuesta llegue
			respuesta = xmlhttp.responseText;
			document.getElementById('arti').innerHTML = respuesta;
		}
	}
	xmlhttp.open("post", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", posts.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(posts);
}