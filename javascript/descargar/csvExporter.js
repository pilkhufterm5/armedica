function get_blob(){
	return self.Blob;
}
Cargar=
function(){
	//if(typeof(Blob)=='undefined')
	{
		script=
		'<script type="text/javascript" src="javascript/descargar/Blob.js"></script>'+
		'<script type="text/javascript" src="javascript/descargar/canvas-toBlob.js"></script>'+
		'<script type="text/javascript" src="javascript/descargar/FileSaver.js"></script>';
		$(script).appendTo($('body'));
	}
	setTimeout(function(){
		
	$('csv').click(function(e){
		CSV="";
		$($(this).attr('title')).find('tr').map(function(){
			if(!$(this).hasClass('no_print')){
				$(this).find('td,th').map(function(){
					if(!$(this).hasClass('no_print')){
						Data=$(this).text();
						if(Data.indexOf('+')!=-1||Data.indexOf('-')!=-1||Data.indexOf('=')!=-1)
							Data=" "+Data;
						CSV+="\""+Data+"\",";
						columnas=parseInt($(this).attr('colspan'));
						if(isNaN(columnas))columnas=1;
						if(columnas>1){
							columnas--;
							while(columnas--)
							CSV+="\""+"\",";
						}
					}
				});
				CSV+="\r\n";
			}
		});
		var BB = get_blob();
		var Nombre="";
		Nombre=$(this).attr('target');
		if(typeof(Nombre)=='undefined'||Nombre=='') Nombre=$('title').text();
		saveAs(
			  new BB(
				  [CSV]
				, {type: "text/plain;charset=" + document.characterSet}
			)
			, Nombre+".csv"
		);
		return false;
	});
	},1000);
};
if(typeof($)=='undefined'||typeof($('body')[0])=='undefined')
	window.onload=Cargar;
else $(Cargar);
