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
	$('pdf').click(function(e){
		Tabla=$($(this).attr('title'));
		if(Tabla.find('tr').length<500){
			Tabla=$($(this).attr('title')).clone();
			Tabla.appendTo($('body'));
		}
		
		Tabla.wrap('<span></span>');
		
		//Tabla.hide();
		H=Tabla.css('height');
		Tabla.attr('xheight',H);
		
		W=Tabla.css('width');
		Tabla.attr('xwidth',W);
		Tabla.find('tr').map(function(){
			if(!$(this).hasClass('no_print')){
				H=$(this).height();
				$(this).attr('xheight',H);
				$(this).find('td,th').map(function(){
					if(!$(this).hasClass('no_print')){
						W=$(this).width()+parseFloat($(this).css('padding-right'))+parseFloat($(this).css('padding-left'));
						$(this).attr('xwidth',W);
						if($(this).find('*').length==0)
							$(this).html('<d>'+$(this).text()+'</d>');
					}else{
						$(this).attr('hiden',true);
					}
				});
			}else $(this).attr('hiden',true);
		});
		if(Tabla.find('[rowspan]').length>0){//Normalizar tabla, quitar row span
			var rowspan=0;
			var Celdas=new Array();
			Tabla.find('tr').map(function(){
				Fila=$(this).parent().children().index($(this));
				if(typeof(Celdas[Fila])!='undefined'){
					i=0;
					$(this).find('td').map(function(){
						if(typeof(Celdas[Fila][i])!='undefined'){
							$(this).before($(Celdas[Fila][i]));
							i++;
						}
						i++;
					});
					for(i=0;i<Celdas[Fila].length;i++){
						if(typeof(Celdas[Fila][i])!='undefined'){
							$(Celdas[Fila][i])
						}
					}
				}
				$(this).find('td').map(function(){
					Columna=$(this).parent().children().index($(this));
					rowspan=$(this).attr('rowspan');$(this).removeAttr('rowspan');
					if(typeof(rowspan)!='undefined'&&rowspan>1){
						for(i=1;i<rowspan;i++){
							if(typeof(Celdas[Fila+i])=='undefined')Celdas[Fila+i]=[];
							Celdas[Fila+i][Columna]='<td hiden=true></td>';
						}
					}
				});
			});
		}
		Nombre=$(this).attr('target');
		Src=$(this).attr('src');
		if(typeof(Nombre)=='undefined'||Nombre=='') Nombre=$('title').text();
		if(typeof(Src)=='undefined'||Src=='') Src='rh_tabToPDF.php';
		$.ajax({
			url:Src,
			data:{
				tabla:Tabla.parent().html().replace(/\t+/g," ").replace(/\r+/g," ").replace(/\n+/g," ").replace(/ +/g," "),
				nombre:Nombre
				},
			type: 'POST',
		    success:function(response){
				 data=eval('('+response+')');
				 
				 if(typeof(data.nombre)!='undefined'||typeof(data.data)!='undefined'){
					 var BB = get_blob();
					 saveAs(
						  new BB(
							  [decode_base64(data.data)]
							, {type: "application/pdf"}
						)
						, data.nombre
					);
				 }else{
					 if(typeof(data.error)!='undefined'){
						 response=data.error;
					 }
					 {
						 $(response).appendTo($('body'));
					 }
				 }
					 
			}
		});
		if(Tabla.find('tr').length<500)
			Tabla.parent().remove();
		return false;
	});
	},1000);
};
if(typeof($)=='undefined'||typeof($('body')[0])=='undefined')
	window.onload=Cargar;
else $(Cargar);
