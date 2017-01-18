/**
 * REALHOST
 * 4 DE MAYO DEL 2010
 * RICARDO ABULARACH GARCIA
 * SECCIONES PARA LA ADMINSITRACION DE LOS JAVASCRIPTS
 */

function value_igual_text(ids_selects, valores_selects){
	for(var j=0; j < ids_selects.length; j++){
		no_encontrado = true;
		valor = valores_selects[j];
		SELECT = document.getElementById(ids_selects[j]);
		//aqui es SELECT.length-1 porque no debemos tocar la ultima opcion ("Otro...")
		tope = SELECT.length-1;
		for(var i= 0; i < SELECT.length-1; i++){
			SELECT.options[i].value = SELECT.options[i].text;
			if(SELECT.options[i].value == valor){
				SELECT.selectedIndex = i;
				no_encontrado = false;
			}
			//si ya estamos en el ultimo valor que proviene del administrador y el valor no fue encontrado
			if(tope-1 == i && no_encontrado && valor != ""){
				SELECT.options.length = i+3;
				SELECT.options[i+2].value = "-2";
				SELECT.options[i+2].text = "Otro...";
				SELECT.options[i+1].text = valor;
				SELECT.options[i+1].value = valor;
			}
		}
	}
}


