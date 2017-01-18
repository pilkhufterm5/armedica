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
	var url = "rh_rutas_ope.php";
	var posts = "rh_modo=PAGINA&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value+"&buscacion="+document.forma.buscacion.value;
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
		var url = "rh_rutas_ope.php";
		var posts = "rh_modo=PAGINA&pagina="+nuevapagina+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value+"&buscacion="+document.forma.buscacion.value;
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
function edita(id){
	document.getElementById('msn').innerHTML = '';
	xmlhttp = create_ajaxOb();
	var url = "rh_rutas_ope.php";
	var posts = "rh_modo=EDITA&id="+id;
	xmlhttp.onreadystatechange = function(){
		if(xmlhttp.readyState == 4){//cuando la respuesta llegue
			respuesta = xmlhttp.responseText;
			mi_array = new Array();
			mi_array = respuesta.split('||');
			document.forma2.id.value = mi_array[0];
			document.forma2.codigo.value = mi_array[1];
			document.forma2.codigo.disabled = true;
			document.forma2.nombre.value = mi_array[2];
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
	if(document.forma2.id.value == ''){
		modo = 'GRABA';
		id = '';
	}else{
		modo = 'ACTUALIZA';
		id = document.forma2.id.value;
	}
	if(document.forma2.codigo.value == '' || document.forma2.codigo.value == ' ' || document.forma2.codigo.value == '  ' || document.forma2.codigo.value == '   ' || document.forma2.codigo.value == '    '){
		alert('El Codigo no puede estar vacio');
		error = 0;
	}else{
		if(document.forma2.nombre.value == '' || document.forma2.nombre.value == ' ' || document.forma2.nombre.value == '  ' || document.forma2.nombre.value == '   ' || document.forma2.nombre.value == '    '){
			alert('El Nombre no puede estar vacio');
			error = 0;
		}
	}
	if(error == 1){
		xmlhttp = create_ajaxOb();
		var url = "rh_rutas_ope.php";
		var posts = "rh_modo="+modo+"&id="+id+"&codigo="+document.forma2.codigo.value+"&nombre="+document.forma2.nombre.value+"&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value+"&buscacion="+document.forma.buscacion.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				mi_array = new Array();
				mi_array = respuesta.split('||');
				document.getElementById('msn').innerHTML = mi_array[0];
				document.getElementById('datagrid').innerHTML = mi_array[1];
				document.forma2.id.value = '';
				document.forma2.codigo.value = '';
				document.forma2.codigo.disabled = false;
				document.forma2.nombre.value = '';
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
function borra(id){
	document.getElementById('msn').innerHTML = "";
	if(confirm('Esta seguro de borrar esta gamma?')){
		xmlhttp = create_ajaxOb();
		var url = "rh_rutas_ope.php";
		var posts = "rh_modo=BORRA&id="+id+"&pagina="+document.forma.actulpagina.value+"&ordenado="+document.forma.ordenado.value+"&ad="+document.forma.ad.value+"&tpagina="+document.forma.totalpagina.value+"&buscacion="+document.forma.buscacion.value;
		xmlhttp.onreadystatechange = function(){
			if(xmlhttp.readyState == 4){//cuando la respuesta llegue
				respuesta = xmlhttp.responseText;
				mi_array = new Array();
				mi_array = respuesta.split('||');
				document.getElementById('msn').innerHTML = mi_array[0];
				document.getElementById('datagrid').innerHTML = mi_array[1];
				document.forma2.id.value = '';
				document.forma2.codigo.value = '';
				document.forma2.codigo.disabled = false;
				document.forma2.nombre.value = '';
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
	document.forma2.id.value = '';
	document.forma2.codigo.value = '';
	document.forma2.codigo.disabled = false;
	document.forma2.nombre.value = '';
}
//BUSCAR ARTICULO
function buscararticulo(){
	document.getElementById('msn').innerHTML = "";
	xmlhttp = create_ajaxOb();
	var url = "rh_rutas_ope.php";
	var posts = "rh_modo=BUSCA&pagina=1&ordenado=codigo&ad=ASC&buscacion="+document.forma.buscacion.value;
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