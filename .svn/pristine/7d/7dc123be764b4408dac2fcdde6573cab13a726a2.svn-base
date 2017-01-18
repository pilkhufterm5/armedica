<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Fecha_Baja').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        $('#Fecha_Cancelacion_Efectiva').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        
        $('#Cancelar').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Cancelar el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#Modal-Cancelar').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });
        
        
        $('#Reactivar').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Reactivar el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Reactivar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#Modal-Reactivar').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });
        
        $('#Create-Modal-Cancelar').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/cancelarafiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Cancelacion:{
                          folio: $('#Folio').val(),
                          fecha_baja: $('#Fecha_Baja').val(),
                          fecha_cancelacion: $('#Fecha_Cancelacion_Efectiva').val(),
                          motivo_cancelacion: $('select[id="M_Cancelacion"] :selected').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#Modal-Cancelar').modal("toggle");
                        $("#AFStatus").text("Afiliado Cancelado");
                        $("#AFStatus").removeClass("badge-success");
                        $("#AFStatus").addClass("badge-important");
                        ConfirmPDF(data.CancelNo);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
        $('#Create-Modal-Reactivar').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/reactivarafiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Reactivacion:{
                          folio: $('#Folio').val(),
                          monto_recibido: $('#monto_recibido').val(),
                          tarifa_total: $('#tarifa_total').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#Modal-Reactivar').modal("toggle");
                        $("#AFStatus").text("Afiliado Activo");
                        $("#AFStatus").removeClass("badge-important");
                        $("#AFStatus").addClass("badge-success");
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
        function ConfirmPDF(CancelNo){
            $('<div></div>').appendTo('body')
            .html('<div><h6>El Folio: <?=$_POST['Folio']?> ha sido Cancelado Correctamente.<br> Folio de Cancelacion: ' + CancelNo + ' <br> ¿Desea Imprimir el Reporte de Cancelación?</h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            OpenInNewTab("../tmp/cancelaciones/CancelNo-" + CancelNo + ".pdf");
                            $('#LSuspension').hide();
                            $('#Cancelar').hide();
                            $('#Suspender').hide();
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        }
        
        function OpenInNewTab(url ){
            var win = window.open(url, '_blank');
            win.focus();
        }
        $( "#Folio" ).change(function() {
		// Check input( $( this ).val() ) for validity here
			$( "#hiddenFolio" ).val(this.value);
		});
        //$('#AfiliacionForm').validate();
        $('#AccordionCobranza').hide();
        $('#SociosForm').hide();
        $('#UpdateData').click(function() {
            var flag = $("#AfiliacionForm").valid();
            if(!flag)
            	alert("Falta información obligatoria.");
        });
        	
        $('#SavebtnSocio').click(function() {
            var flag = $("#SociosForm").valid();
            if(!flag)
            	alert("Falta información obligatoria.");
        });
        	
        function activaTab($tab1,$tab2,$tab3){
        	if(!$tab1.hasClass('activON'))
        	 $tab1.addClass('activON');
        	if($tab2.hasClass('activON'))
        	 $tab2.removeClass('activON');
        	if($tab3.hasClass('activON'))
        	 $tab3.removeClass('activON');
        }	;
        $('#btnT').click(function(){
        	 $('#AccordionTitular').show();
        	 $('#AccordionCobranza').hide();
        	 $('#SociosForm').hide();
        	 $('#Savebtn').show();
        	 activaTab($('#btnT'),$('#btnC'),$('#btnS'));
        	/* if(!$('#btnT').hasClass('activON'))
        	 	if($('#btnC').addClass('activON'))
        	 if($('#btnC').hasClass('activON'))
        	  $('#btnC').removeClass('activON');
        	 if($('#btnS').hasClass('activON'))
        	  $('#btnS').removeClass('activON');*/
        	 
        });
        $('#btnC').click(function(){
        	 $('#AccordionTitular').hide();
        	 $('#AccordionCobranza').show();
        	 $('#SociosForm').hide();
        	 $('#Savebtn').show();
        	 activaTab($('#btnC'),$('#btnT'),$('#btnS'));
        });
        $('#btnS').click(function(){
        	 $('#AccordionTitular').hide();
        	 $('#AccordionCobranza').hide();
        	 $('#Savebtn').hide();
        	 $('#SociosForm').show();
        	 activaTab($('#btnS'),$('#btnC'),$('#btnT'));
        });
        
        $('#dataClosebtn').click(function(){
        	$('#alertBoxdialog').remove();
        });
        
        $("input#Folio").live("keyup", function( event ){

		    if(this.value.length == this.getAttribute('maxlength')) {
		        if(!$(this).data('triggered')) {
		            // set the 'triggered' data attribute to true
		            $(this).data('triggered',true); 
		            //if ($(this).valid() == true ) { zipLookup(this, "USA"); } 
		            //alert("lanza ajax");
		            var elem="<div id=\"alertBoxdialog\" style=\"padding-top:0;\"  class=\"container\"><div class=\"row-fluid\"><div class=\"span12\"><div class=\"alert alert-error\"><button id=\"dataClosebtn\"  data-dismiss=\"alert\" class=\"close\" type=\"button\">×</button><h4>Error!</h4>No Existe un Afiliado con ese N° de Folio.            </div></div></div></div> ";
		            //$('#FolioForm').before(elem);
		            $.ajax({
					type: "POST",
					url: "Validarfolio",
					data: { Folio: $("input#Folio").val() }
					})
					.done(function( msg ) {
						if(msg=="404"){
							$('#FolioForm').before(elem);
						}else
						if(msg=="200"){
							/*$.ajax({
								type: "POST",
								url: "afiliacion",
								data: { Folio: $("input#Folio").val(),Search:'Search' }
							});*/
							//$('#Search').click();
						}
					});
		            
		        }
		    } else { $(this).data('triggered',false); }
		
		});
    }); 
</script>


<div style="height: 10px;"></div>


<form class="form-horizontal" id="FolioForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/busquedaporfolio/"); ?>" >
    <div class="container-fluid">
        <div class="form-legend">&nbsp;<?php if(!empty($_POST['movimientos_afiliacion'])){ echo "<span id='AFStatus' class='badge badge-{$BadgeType}'>" . "Afiliado " . $_POST['movimientos_afiliacion']; } ?></span></div>
        <div class="control-group row-fluid">
            <div class="span12">
                <div class="controls">
                    <div class="span1">
                        <label class="control-label"><?php echo _('Folio'); ?></label>
                    </div>
                    <div class="span10">
                        <div class="controls">
                            <input type="text" maxlength="5" class="required" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width: 100px;;" />
                            <input type="submit" id="Search" name="Search" class="btn btn-small" value="Buscar" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<style>
	#tabcontainer{
		width: 100%;
	    float:left;
	    overflow:hidden; /* instead of clearfix div */
	    
	    text-align:center;
	    
	    font-size:medium;
	    vertical-align:middle;
	  
	    padding-top:10px;
	    margin-bottom:-10px;
	}
	#tabcontainer> div{
	 float:left;
    width:33%;
   
    border-radius:5px;
    background-color: rgb(230,230,230);
		/*box-sizing: border-box;*/
	}
	#tabcontainer> div:hover{
		 background-color: rgb(110,110,110);
	}
	#tabcontainer .activON{
		   background-color: rgb(170,170,170);
		   border:1px solid rgb(90,90,90);
	}
</style>
<div id="tabcontainer">
	<div class="tab activON" id="btnT">Titular</div>
	<div class="tab" id="btnC">Cobranza</div>
	<div class="tab" id="btnS">Socios</div>	
</div>
<div style="height: 20px;"></div>
<script type="text/javascript">
    $(document).on('ready', function() {
        $('#Fecha_Baja').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        $('#Fecha_Cancelacion_Efectiva').datepicker({
            dateFormat : 'yy-mm-dd'
        });
        
        $('#Cancelar').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Cancelar el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#Modal-Cancelar').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });
        
        
        $('#Reactivar').click(function(){
            $('<div></div>').appendTo('body')
            .html('<div><h6>Esta seguro de Reactivar el Folio: <?=$_POST['Folio']?></h6></div>')
            .dialog({
                modal: true, title: 'Reactivar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            $('#Modal-Reactivar').modal('show');
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        });
        
        $('#Create-Modal-Cancelar').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/cancelarafiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Cancelacion:{
                          folio: $('#Folio').val(),
                          fecha_baja: $('#Fecha_Baja').val(),
                          fecha_cancelacion: $('#Fecha_Cancelacion_Efectiva').val(),
                          motivo_cancelacion: $('select[id="M_Cancelacion"] :selected').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#Modal-Cancelar').modal("toggle");
                        $("#AFStatus").text("Afiliado Cancelado");
                        $("#AFStatus").removeClass("badge-success");
                        $("#AFStatus").addClass("badge-important");
                        ConfirmPDF(data.CancelNo);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
        $('#Create-Modal-Reactivar').click(function(){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/reactivarafiliado"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Reactivacion:{
                          folio: $('#Folio').val(),
                          monto_recibido: $('#monto_recibido').val(),
                          tarifa_total: $('#tarifa_total').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#Modal-Reactivar').modal("toggle");
                        $("#AFStatus").text("Afiliado Activo");
                        $("#AFStatus").removeClass("badge-important");
                        $("#AFStatus").addClass("badge-success");
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });
        
        function ConfirmPDF(CancelNo){
            $('<div></div>').appendTo('body')
            .html('<div><h6>El Folio: <?=$_POST['Folio']?> ha sido Cancelado Correctamente.<br> Folio de Cancelacion: ' + CancelNo + ' <br> ¿Desea Imprimir el Reporte de Cancelación?</h6></div>')
            .dialog({
                modal: true, title: 'Cancelar Folio', zIndex: 10000, autoOpen: true,
                top: 800,
                width: 400,
                resizable: false,
                buttons: [{
                    text: "SI",
                        click: function() {
                            OpenInNewTab("../tmp/cancelaciones/CancelNo-" + CancelNo + ".pdf");
                            $('#LSuspension').hide();
                            $('#Cancelar').hide();
                            $('#Suspender').hide();
                            $(this).dialog("close");
                        }
                    },
                    {
                    text: "NO",
                        click: function() {
                            $(this).dialog("close");
                        }
                    }],
                close: function (event, ui) {
                    $(this).remove();
                }
            });
        }
        
        function OpenInNewTab(url ){
            var win = window.open(url, '_blank');
            win.focus();
        }
        
        //$('#AfiliacionForm').validate();
        $('#AccordionCobranza').hide();
        $('#SociosForm').hide();
        $('#UpdateData').click(function() {
            var flag = $("#AfiliacionForm").valid();
            if(!flag){
            	alert("Falta información obligatoria.");
            	if($('#Folio').val().trim()=''){
            		alert("El folio es obligatorio.");
            	}
            }
        });
        	
        $('#btnT').click(function(){
        	 $('#AccordionTitular').show();
        	 $('#AccordionCobranza').hide();
        	 $('#SociosForm').hide();
        	 $('#Savebtn').show();
        	 
        });
        $('#btnC').click(function(){
        	 $('#AccordionTitular').hide();
        	 $('#AccordionCobranza').show();
        	 $('#SociosForm').hide();
        	 $('#Savebtn').show();
        });
        $('#btnS').click(function(){
        	 $('#AccordionTitular').hide();
        	 $('#AccordionCobranza').hide();
        	 $('#Savebtn').hide();
        	 $('#SociosForm').show();
        });
    }); 
</script>
<style>
    input[type="text"]{
        margin-bottom: -20px;
    }
</style>
<?php  FB::INFO($_POST, '_____________________$_lastpsottabafiliacion');?>
<div id="Modal-Cancelar" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Cancelación</h3>
    </div>
    <div class="modal-body">
        <p>
        <div class="row span6">
            <label>Fecha Baja:</label>
            <input id="Fecha_Baja" type="text" style="width: 120px;" />
        </div>
        <div>
            <label>Cancelación Efectiva a partir de:</label>
            <input id="Fecha_Cancelacion_Efectiva" type="text" style="width: 120px;" />
        </div>
        <div>
            <label>Motivo Cancelación:</label>
            <select id="M_Cancelacion" >
            <?php foreach($ListaMotivosCancelacion as $id => $Name){ ?>
                <option value="<?=$Name?>"><?=$Name?></option>
            <?php } ?>
            </select>
        </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-Cancelar" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-Cancelar" class="btn btn-danger">Aceptar</button>
    </div>
</div>

<div id="Modal-Reactivar" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 300px;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Tarifas a Registrar</h3>
    </div>
    <div class="modal-body">
        <p>
            <div class="control-group">
                <label class="control-label" for="monto_recibido">Monto Recibido:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on" style="margin-top: 5px;"><i class=""><b>$</b></i></span>
                        <input type="text" id="monto_recibido" id="monto_recibido" class="span6" >
                    </div>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label" for="tarifa_total">Tarifa Total:</label>
                <div class="controls">
                    <div class="input-prepend">
                        <span class="add-on" style="margin-top: 5px;"><i class=""><b>$</b></i></span>
                        <input type="text" id="tarifa_total" id="tarifa_total" class="span6" >
                    </div>
                </div>
            </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-Reactivar" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Create-Modal-Reactivar" class="btn btn-success">Aceptar</button>
    </div>
</div>
<?php 
	switch ($_POST['movimientos_afiliacion']) {
		case 'Activo':
			$BadgeType = "success";
			break;
		case 'Cancelado':
	        $BadgeType = "important";
	        break;
	    case 'Suspendido':
	        $BadgeType = "warning";
	        break;
		default:
			//$BadgeType = "info";
			break;
	}
?>

<div style="height: 10px;"></div>
 <?php  if($_POST['Action'] == "UPDATE"){ ?>
        <form class="form-horizontal" id="AfiliacionForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/updatedata/"); ?>" >
        <?php } else { ?>
        <form class="form-horizontal" id="AfiliacionForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/savedata/"); ?>" >
        <?php } ?>	
	<input type="hidden"  id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width: 100px;;" />
    <div style="height: 10px;"></div>
    <div id="AccordionTitular" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionTitular" href="#collapseOneTitular">
                    <?php echo _('Datos del Titular'); ?>
                </a>
            </div>
            <div id="collapseOneTitular" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <script type="text/javascript">
						    $(document).on('ready', function() {
						        $('#PMoral').hide();
						        $('#fecha_ingreso').datepicker({
						            dateFormat : 'yy-mm-dd'
						        });
						        
						        $('#fecha_ultaum').datepicker({
						            dateFormat : 'yy-mm-dd'
						        });
						        
						        $("#tipopersona").select2();
						        $("#asesor").select2();
						        $("#sexo").select2();
						        $("#razonsocial").select2();
						        $("#nombrecomercial").select2();
						        
						        $('#tipopersona').change(function(){
						            if($('#tipopersona').val() == 'FISICA'){
						                $('#PMoral').hide();
						                $('.PFisica').show();
						            }else{
						                $('#PMoral').show();
						                $('.PFisica').hide();
						            }
						        });
						        
						    }); 
						    
						    
					        function vRfcs(){
					            
					            var elementInputTextTaxRef = document.getElementById('taxref')
					            var rfc = elementInputTextTaxRef.value
					            if(!rfc){
					                elementInputTextTaxRef.focus()
					                alert('El campo RFC es obligatorio')
					                return false
					            }
					            
					            if($('#tipopersona').val() == 'FISICA'){
					                var pf = $('#tipopersona').val();
					            }
					            
					            if($('#tipopersona').val() == 'MORAL'){
					                var pf = $('#tipopersona').val();
					            }
					            
					            
					            if(!($('#tipopersona').val())){
					                alert('Debe seleccionar un tipo de Persona')
					                return false
					            }
					            
					            if($('#tipopersona').val() == 'MORAL'){
					                if(!rfc.match(/^[A-ZÑ&]{3}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
					                    alert('El RFC no es un RFC valido para Persona Moral')
					                    return false
					                }
					            }
					            //
					            if($('#tipopersona').val() == 'FISICA'){  //VECJ880326XXX
					                if(rfc == 'XAXX010101000' || rfc == 'XEXX010101000')
					                    return true
					                if(!rfc.match(/^[A-ZÑ&]{4}(\d\d(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01]))[0-9A-ZÑ]{3}$/)){
					                    alert('El RFC no es un RFC valido para Persona Fisica')
					                    return false
					                }
					            }
					            return true
					        }
						    
						</script>
						<?php  FB::INFO($_SESSION,'__________________________________________SESSION');    ?>
					    <div class="container-fluid bootspin">
					        <!--Spinners begin-->
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Fecha de Ingreso'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Ingreso"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <?php 
					                    if(empty($_POST['t_fecha_ingreso'])){
					                        $_POST['t_fecha_ingreso'] = date('Y-m-d');
					                    }
					                    ?>
					                    <input type="text" id="fecha_ingreso" class="required" readonly="readonly" name="t_fecha_ingreso" style="width: 120px;" value="<?=$_POST['t_fecha_ingreso']?>" />
					                </div>
					            </div>
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Fecha Ult Aum.'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha ult Aum"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="fecha_ultaum"  readonly="readonly" name="t_fecha_ultaum" style="width: 120px;" value="<?=$_POST['t_fecha_ultaum']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Tipo de Persona'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Tipo de Persona"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <select id="tipopersona" class="required" name="t_tipopersona" value="<?=$_POST['t_tipopersona']?>" >
					                        <option>FISICA</option>
					                        <option>MORAL</option>
					                    </select>
					                </div>
					            </div>
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Asesor'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Asesor"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <select id="asesor" class="required" name="t_asesor" >
					                        <option value="">&nbsp;</option>
					                        <?php foreach($ListaComisionistas as $id => $Name){ 
					                            if(!empty($_POST['t_asesor']) && $_POST['t_asesor'] == $id){
					                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
					                            }
					                            ?>
					                            <option value="<?=$id?>"><?=$Name?></option>
					                        <?php } ?>
					                    </select>
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid PFisica">
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Apellidos'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Apellidos"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="apellidos" name="t_apellidos" class="required" value="<?=$_POST['t_apellidos']?>" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Nombre(s)'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre(s)"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <input type="text" id="name" name="t_name" class="required" value="<?=$_POST['t_name']?>" /> 
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Sexo'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sexo"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <select id="sexo" name="t_sexo" class="required" >
					                        <?php
					                        if(!empty($_POST['t_sexo'])){
					                            echo '<option selected="selected">'. $_POST['t_sexo'] .'</option>';
					                        }
					                        ?>
					                        <option>MASCULINO</option>
					                        <option>FEMENINO</option>
					                    </select> 
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid" id="PMoral">
					            <div class="span5">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Razón Social'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Razón Social"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="name2" name="t_name2" class="required" value="<?=$_POST['t_name2']?>" >
					                </div>
					            </div>
					            
					            <div class="span7">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Nombre Comercial'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre Comercial"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="nombre_empresa" name="t_nombre_empresa" class="required" value="<?=$_POST['t_nombre_empresa']?>" >
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('RFC'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="RFC"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="taxref" name="t_taxref" class="required" value="<?=$_POST['t_taxref']?>"  onBlur="vRfcs(this.value)" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('CURP'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Curp"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="curp" name="t_curp" value="<?=$_POST['t_curp']?>" class="required" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('E-mail'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="E-mail"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <textarea id="email" name="t_email" class="span8 required" rows="1" ><?=$_POST['t_email']?></textarea>
					                    <!--<input type="text" id="email" name="t_email" value="<?=$_POST['t_email']?>" class="required" />-->
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Contacto'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Contacto"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="contacto" name="t_contacto" value="<?=$_POST['t_contacto']?>" class="required" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Teléfono'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Teléfono"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="rh_tel" name="t_rh_tel" style="width: 150px;" value="<?=$_POST['t_rh_tel']?>" class="required" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Tel. Alterno'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Telefono Alterno"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="rh_tel2" name="t_rh_tel2" style="width: 150px;" value="<?=$_POST['t_rh_tel2']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Enfermeria'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Enfermeria"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <?php 
					                    if($_POST['t_enfermeria']==1){
					                        $chekedEnf = "checked='checked' ";
					                    }else{
					                        $chekedEnf = "";
					                    }
					                    ?>
					                    <input type="checkbox" <?=$chekedEnf?> id="enfermeria" name="t_enfermeria" value="1" /> 
					                </div>
					            </div>
					            <div class="span8">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Costo Enfermeria'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Costo de Enfermeria"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="costoenfermeria" name="t_costoenfermeria" style="width: 100px;" value="<?=$_POST['t_costoenfermeria']?>" /> 
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('¿Servicio Limitado?'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="¿Servicio Limitado?"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <?php 
					                    if($_POST['t_serviciolimitado']==1){
					                        $chekedSL = "checked='checked' ";
					                    }else{
					                        $chekedSL = "";
					                    }
					                    ?>
					                    <input type="checkbox" <?=$chekedSL?> id="serviciolimitado" name="t_serviciolimitado" value="1" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Servicios Disponibles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Servicios Disponibles"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="serviciosdisponibles" name="t_serviciosdisponibles" style="width: 50px;" value="<?=$_POST['t_serviciosdisponibles']?>" /> 
					                </div>
					            </div>
					            
					            <div class="span4">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Costo Servicio Extra'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Costo Servicio Extra"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="costo_servicioextra" name="t_costo_servicioextra" style="width: 100px;" value="<?=$_POST['t_costo_servicioextra']?>" />
					                </div>
					            </div>
					        </div>
					        
					    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionTitular" href="#collapseTwoTitular">
                    <?php echo _('Dirección'); ?>
                </a>
            </div>
            <div id="collapseTwoTitular" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <script type="text/javascript">
					    $(document).on('ready',function() {
					        $("#address7").select2();
					        $("#address8").select2();
					    });
					    
					    function IsNumeric(input){
					        //alert('TESTR');
					        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
					            //alert('OK')
					        }else{
					            alert('Solo se admiten numeros');
					        }
					        //return
					    }
						</script>
					    <div class="container-fluid bootspin">
						        
					        <!--Spinners begin--->
					        <div class="control-group row-fluid">
					            <div class="span1">
					                <label class="control-label" for="spin1">
					                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span3">
					                <div class="controls">
					                    <input type="text" id="address1" name="t_address1" class="required" style="width: 200px;" value="<?=$_POST['t_address1']?>" />
					                </div>
					            </div>
					            <div class="span1">
					                <label class="control-label" for="spin1">
					                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span3">
					                <div class="controls">
					                    <input type="text" id="address2" name="t_address2" class="required" style="width: 200px;" value="<?=$_POST['t_address2']?>" />
					                </div>
					            </div>
					            
					            <div class="span2">
					                <label class="control-label">
					                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <input type="text" id="address10" name="t_address10" class="required" style="width: 80px;" value="<?=$_POST['t_address10']?>" />
					                </div>
					            </div>
					            
					        </div>
					            
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label" for="spin2">
					                    <?php echo ('Colonia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Colonia"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <input type="text" id="address4" name="t_address4" class="required" value="<?=$_POST['t_address4']?>" /> 
					                </div>
					            </div>
					            <div class="span2">
					                <label class="control-label" for="spin2">
					                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <input type="text" id="address5" name="t_address5" class="required" value="<?=$_POST['t_address5']?>" /> 
					                </div>
					            </div>
					        </div>
					            
					            
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label" for="spin3">
					                    <?php echo _('Entre Calles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Entre Calles"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span10">
					                <div class="controls">
					                    <input type="text" id="address6" name="t_address6" class="required" value="<?=$_POST['t_address6']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label" for="spin3">
					                    <?php echo _('Cuadrante'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuadrante"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span10">
					                <div class="controls">
					                    <input type="text" id="cuadrante1" name="t_cuadrante1" class="required" style="width: 80px;" value="<?=$_POST['t_cuadrante1']?>" onBlur="IsNumeric(this.value)" />
					                    <input type="text" id="cuadrante2" name="t_cuadrante2" class="required" style="width: 80px;" value="<?=$_POST['t_cuadrante2']?>"  />
					                    <input type="text" id="cuadrante3" name="t_cuadrante3" class="required" style="width: 80px;" value="<?=$_POST['t_cuadrante3']?>" onBlur="IsNumeric(this.value)" />  
					                    <script>
					                        $(document).ready(function(){
					                            $('#cuadrante2').mask('?a');
					                            //$('#cuadrante3').mask('9?');
					                        });
					                    </script>
					                </div>
					            </div>
					        </div>
					            
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label">
					                    <?php echo ('Municipio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Municipio"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <select id="address7" name="t_address7" class="required" >
					                        <option value="">&nbsp;</option>
					                        <?php foreach($ListaMunicipios as $id => $Municipio){ 
					                            if(!empty($_POST['t_address7'])){
					                                echo '<option selected="selected" value="'. $_POST['t_address7'] .'">'. $_POST['t_address7'] .'</option>';
					                            }
					                            ?>
					                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
					                        <?php } ?>
					                    </select>
					                </div>
					            </div>
					            
					            <div class="span2">
					                <label class="control-label">
					                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <select id="address8" name="t_address8" class="required" >
					                        <option value="">&nbsp;</option>
					                        <?php foreach($ListaEstados as $id => $Estado){ 
					                            if(!empty($_POST['t_address8'])){
					                                echo '<option selected="selected" value="'. $_POST['t_address8'] .'">'. $_POST['t_address8'] .'</option>';
					                            }
					                            ?>
					                            <option value="<?=$Estado?>"><?=$Estado?></option>
					                        <?php } ?>
					                    </select>
					                </div>
					            </div>
					        </div>
					            
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label" for="spin3">
					                    <?php echo _('N° Orden de Compra'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Orden de Compra"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <input type="text" id="orderno" name="t_orderno" style="width: 120px;" value="<?=$_POST['t_orderno']?>" />
					                </div>
					            </div>
					            <div class="span2">
					                <label class="control-label" for="spin3">
					                    <?php echo _('N° Referencia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Referencia / Numero Interior"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <input type="text" id="address3" name="t_address3" style="width: 120px;" value="<?=$_POST['t_address3']?>" />
					                </div>
					            </div>
					            <div class="span2">
					                <label class="control-label" for="spin3">
					                    <?php echo _('N° Proveedor'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Proveedor"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <input type="text" id="rh_numproveedor" name="t_rh_numproveedor" style="width: 120px;" value="<?=$_POST['t_rh_numproveedor']?>" />
					                </div>
					            </div>
					        </div>
					            
					    </div>

                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionTitular" href="#collapseThreeTitular">
                    <?php echo _('Servicios Seleccionados'); ?>
                </a>
            </div>
            <div id="collapseThreeTitular" class="accordion-body collapse ">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
						<?php
						    if ($_POST['t_servicios_seleccionados']['Emergencia']==1) {
						        $SSEM = "checked='checked' ";
						    } else {
						        $SSEM = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['Urgencia']==1) {
						        $SSUR = "checked='checked' ";
						    } else {
						        $SSUR = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['CTraumatismo']==1) {
						        $SSTRA = "checked='checked' ";
						    } else {
						        $SSTRA = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['CPatologia']==1) {
						        $SSPAT = "checked='checked' ";
						    } else {
						        $SSPAT = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['TProgramado']==1) {
						        $SSTPRO = "checked='checked' ";
						    } else {
						        $SSTPRO = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['LMedica']==1) {
						        $SSLMED = "checked='checked' ";
						    } else {
						        $SSLMED = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['CExterna']==1) {
						        $SSCEXT = "checked='checked' ";
						    } else {
						        $SSCEXT = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['CBase']==1) {
						        $SSCBAS = "checked='checked' ";
						    } else {
						        $SSCBAS = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['Evento']==1) {
						        $SSEVE = "checked='checked' ";
						    } else {
						        $SSEVE = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['SEspeciales']==1) {
						        $SSESP = "checked='checked' ";
						    } else {
						        $SSESP = "";
						    }
						    
						    if ($_POST['t_servicios_seleccionados']['CEmpleados']==1) {
						        $SSEMPL = "checked='checked' ";
						    } else {
						        $SSEMPL = "";
						    }
						     
						?>
					    <div class="container-fluid bootspin">
					        <div class="control-group row-fluid">
					            <div class="span12">
					                <input type="checkbox" id="Emergencia" name="t_servicios_seleccionados[Emergencia]" <?=$SSEM?> value="1" style="margin-bottom: 5px !important;" /> 1- Emergencia &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="Urgencia" name="t_servicios_seleccionados[Urgencia]" value="1" <?=$SSUR?> style="margin-bottom: 5px !important;" /> 2- Urgencia &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="CTraumatismo" name="t_servicios_seleccionados[CTraumatismo]" <?=$SSTRA?> value="1" style="margin-bottom: 5px !important;" /> 3- Consulta de Traumatismo &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="CPatologia" name="t_servicios_seleccionados[CPatologia]" <?=$SSPAT?> value="1" style="margin-bottom: 5px !important;" /> 4- Consulta de Patologia &nbsp;&nbsp;&nbsp; 
					                <input type="checkbox" id="TProgramado" name="t_servicios_seleccionados[TProgramado]" <?=$SSTPRO?> value="1" style="margin-bottom: 5px !important;" /> 5- Traslado Programado &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="LMedica" name="t_servicios_seleccionados[LMedica]" <?=$SSLMED?> value="1" style="margin-bottom: 5px !important;" /> 6- Linea Medica<br />
					                <input type="checkbox" id="CExterna" name="t_servicios_seleccionados[CExterna]" <?=$SSCEXT?> value="1" style="margin-bottom: 5px !important;" /> 7- Consulta Externa &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="CBase" name="t_servicios_seleccionados[CBase]" value="1" <?=$SSCBAS?> style="margin-bottom: 5px !important;" /> 8- Consulta en Base &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="Evento" name="t_servicios_seleccionados[Evento]" value="1" <?=$SSEVE?> style="margin-bottom: 5px !important;" /> 9- Evento &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="SEspeciales" name="t_servicios_seleccionados[SEspeciales]" <?=$SSESP?> value="1" style="margin-bottom: 5px !important;" /> 10- Servicios Especiales &nbsp;&nbsp;&nbsp;
					                <input type="checkbox" id="CEmpleados" name="t_servicios_seleccionados[CEmpleados]" <?=$SSEMPL?> value="1" style="margin-bottom: 5px !important;" /> 11- Consulta S/Empleados &nbsp;&nbsp;&nbsp;
					            </div>
					        </div>
					    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionTitular" href="#collapseFourTitular">
                    <?php echo _('Examenes de Laboratorio'); ?>
                </a>
            </div>
            <div id="collapseFourTitular" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
					    <div class="container-fluid bootspin">
					        <div class="control-group row-fluid">
					            <div class="span12">
					                <div class="controls elrte-wrapper">
					                    <textarea id="examenes_laboratorio" name="t_examenes_laboratorio" rows="2" class="auto-resize"><?=$_POST['t_examenes_laboratorio']?></textarea>
					                    <script>
				                        $('#examenes_laboratorio').elrte({
				                            lang: "es",
				                            styleWithCSS: false,
				                            height: 200,
				                            toolbar: 'maxi'
				                        });
					                    </script>
					                </div>
					            </div>
					        </div>
					    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionTitular" href="#collapseFiveTitular">
                    <?php echo _('Servicios Impartidos | Costos | Estado de Cuenta'); ?>
                </a>
            </div>
            <div id="collapseFiveTitular" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
					    <div class="row-fluid">
					        <div class="span4">
					            <fieldset>
					                <legend>
					                    Servicios Impartidos
					                </legend>
					                <div class="span12">
					                    <label class="control-label">N° Serv. Acum </label>
					                    <div class="controls">
					                        <input type="text" id="Servicios_acumulados" name="t_servicios_acumulados" value="<?=$_POST['t_servicios_acumulados']?>" style="width: 100px;" />
					                    </div>
					                </div>
					                <div class="span12">
					                    <label class="control-label">N° Serv. Mes </label>
					                    <div class="controls">
					                        <input type="text" id="servicios_mes" name="t_servicios_mes" value="<?=$_POST['t_servicios_mes']?>" style="width: 100px;" />
					                    </div>
					                </div>
					            </fieldset>
					        </div>
					        <div class="span4">
					            <fieldset>
					                <legend>
					                    Costos
					                </legend>
					                <div class="span12">
					                    <label class="control-label">Costos Nuevos Socios</label>
					                    <div class="controls">
					                        <input type="text" id="t_costos_nuevos_socios" value="<?=$_POST['t_costos_nuevos_socios]']?>" name="t_costos_nuevos_socios" style="width: 100px;" />
					                    </div>
					                </div>
					                <div class="span12">
					                    <label class="control-label">Costo Total</label>
					                    <div class="controls">
					                        <input type="text" id="t_costo_total" value="<?=$_POST['t_costo_total]']?>" name="t_costo_total" style="width: 100px;" />
					                    </div>
					                </div>
					            </fieldset>
					        </div>
					        
					        <div class="span4">
					            <fieldset>
					                <legend>
					                    Estado de Cuenta
					                </legend>
					                <div class="span12">
					                    <label class="control-label" for="spin1">Facturas Vencidas</label>
					                    <div class="controls">
					                        <input type="text" id="t_facturas_vencidas" value="<?=$_POST['t_facturas_vencidas]']?>" name="t_facturas_vencidas" style="width: 100px;" />
					                    </div>
					                </div>
					                <div class="span12">
					                    <label class="control-label" for="spin1">Balance</label>
					                    <div class="controls">
					                        <input type="text" id="Balance" name="t_Balance" value="<?=$_POST['t_Balance]']?>" style="width: 100px;" />
					                    </div>
					                </div>
					            </fieldset>
					        </div>
					    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#_AccordionTitular" href="#collapse6Titular">
                    <?php echo _(' Movimientos de Afiliacion'); ?>
                </a>
            </div>
            <div id="collapse6Titular" class="accordion-body ">
                <div class="accordion" style="padding: 0px;" >
                    <div style="height: 10px;"></div>
                    <p style="text-align: center;">
                    <?php  ///PRINT BUTTONS
                    switch ($_POST['movimientos_afiliacion']) {
                        case 'Cancelado':
                            echo '<a id="Reactivar" name="Reactivar" class="btn btn-success btn-large"><i class="icon-star icon-white" style="margin-top: 4px;"></i> Reactivar</a>&nbsp;';
                            break;
                        case 'Suspendido':
                            echo '<a id="LSuspension" name="LSuspension" class="btn btn-info btn-large">   <i class="icon-star icon-white" style="margin-top: 4px;"></i > Levantar Suspension</a>&nbsp;';
                            break;
                        case 'Activo':
                            echo '<a id="Cancelar"  name="Cancelar"  class="btn btn-danger btn-large" ><i class="icon-remove-sign icon-white" style="margin-top: 4px;"></i> Cancelar</a>&nbsp;';
                            echo '<a id="Suspender" name="Suspender" class="btn btn-warning btn-large"><i class="icon-exclamation-sign icon-white" style="margin-top: 4px;"></i> Suspender</a>&nbsp;';
                        break;
                        default:
                            
                            break;
                    }
                    ?>
                    </p>
                </div>
            </div>
        </div>
        
    </div>
    
     <div id="AccordionCobranza" class="accordion hide">
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionCobranza" href="#collapseTwoCobranza">
                    <?php echo _('Dirección'); ?>
                </a>
            </div>
            <div id="collapseTwoCobranza" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <script type="text/javascript">
						    $(document).on('ready',function() {
						        $("#address7").select2();
						        $("#address8").select2();
						    });
						    function IsNumeric(input){
						        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
						            //alert('OK')
						        }else{
						            alert('Solo se admiten numeros');
						        }
						    }
						</script>
						
						    <div class="container-fluid bootspin">
						        
						        <!--Spinners begin-->
						        <div class="control-group row-fluid">
						            <div class="span1">
						                <label class="control-label" for="spin1">
						                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span3">
						                <div class="controls">
						                    <input type="text" id="address1" name="c_address1" class="required" style="width: 200px;" value="<?=$_POST['c_address1']?>" />
						                </div>
						            </div>
						            <div class="span1">
						                <label class="control-label" for="spin1">
						                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span3">
						                <div class="controls">
						                    <input type="text" id="address2" name="c_address2" class="required" style="width: 200px;" value="<?=$_POST['c_address2']?>" />
						                </div>
						            </div>
						            
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span2">
						                <div class="controls">
						                    <input type="text" id="address10" name="c_address10" class="required" style="width: 80px;" value="<?=$_POST['c_address10']?>" />
						                </div>
						            </div>
						            
						        </div>
						            
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin2">
						                    <?php echo ('Colonia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Colonia"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <input type="text" id="address4" name="c_address4" class="required" value="<?=$_POST['c_address4']?>" /> 
						                </div>
						            </div>
						            <div class="span2">
						                <label class="control-label" for="spin2">
						                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <input type="text" id="address5" name="c_address5" class="required" value="<?=$_POST['c_address5']?>" /> 
						                </div>
						            </div>
						        </div>
						            
						            
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin3">
						                    <?php echo _('Entre Calles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Entre Calles"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span10">
						                <div class="controls">
						                    <input type="text" id="address6" name="c_address6" class="required" value="<?=$_POST['c_address6']?>" />
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin3">
						                    <?php echo _('Cuadrante'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuadrante"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span10">
						                <div class="controls">
						                    <input type="text" id="cuadrante1" name="c_cuadrante1" class="required" style="width: 80px;" value="<?=$_POST['c_cuadrante1']?>" onBlur="IsNumeric(this.value)" />
						                    <input type="text" id="cuadrante2" name="c_cuadrante2" class="required" style="width: 80px;" value="<?=$_POST['c_cuadrante2']?>" />
						                    <input type="text" id="cuadrante3" name="c_cuadrante3" class="required" style="width: 80px;" value="<?=$_POST['c_cuadrante3']?>" onBlur="IsNumeric(this.value)" />
						                    <script>
						                        $(document).ready(function(){
						                            $('#cuadrante2').mask('?a');
						                        });
						                    </script>
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Municipio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Municipio"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <select id="address7" name="c_address7" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaMunicipios as $id => $Municipio){ 
						                            if(!empty($_POST['c_address7'])){
						                                echo '<option selected="selected" value="'. $_POST['c_address7'] .'">'. $_POST['c_address7'] .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						            
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <select id="address8" name="c_address8" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaEstados as $id => $Estado){ 
						                            if(!empty($_POST['c_address8'])){
						                                echo '<option selected="selected" value="'. $_POST['c_address8'] .'">'. $_POST['c_address8'] .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$Estado?>"><?=$Estado?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span6">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Teléfono'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Teléfono"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="rh_tel" name="c_rh_tel" class="required" style="width: 150px;" value="<?=$_POST['c_rh_tel']?>" /> 
						                </div>
						            </div>
						            
						            <div class="span6">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Tel. Alterno'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Telefono Alterno"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="rh_tel2" name="c_rh_tel2" class="required" style="width: 150px;" value="<?=$_POST['c_rh_tel2']?>" />
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span4">
						                <label class="control-label" for="spin2">
						                    <?php echo _('E-mail'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="E-mail"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <textarea id="email" name="c_email" class="span8 required" rows="1" ><?=$_POST['c_email']?></textarea>
						                    <!-- <input type="text" id="email" name="c_email" class="required" style="width: 150px;" value="<?=$_POST['c_email']?>" /> -->
						                </div>
						            </div>
						            
						            <div class="span3">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Enviar Factura'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Enviar Factura"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="checkbox" id="enviar_factura" name="c_enviar_factura" value="<?=$_POST['c_enviar_factura']?>" /> 
						                </div>
						            </div>
						            
						            <div class="span5">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Encargado de  Pagos'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Encargado de  Pagos"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="encargado_pagos" name="c_encargado_pagos" style="width: 150px;" value="<?=$_POST['c_encargado_pagos']?>" />
						                </div>
						            </div>
						        </div>
						            
						    </div>

                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionCobranza" href="#collapseThreeCobranza">
                    <?php echo _('Producto'); ?>
                </a>
            </div>
            <div id="collapseThreeCobranza" class="accordion-body collapse ">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
                        <script type="text/javascript">
						    $(document).on('ready', function() {
						        $("#Producto").select2();
						        $("#empresa").select2();
						        $("#frecuencia_pago").select2();
						        $("#convenio").select2();
						        $("#sucursal").select2();
						        $("#forma_pago").select2();
						        $("#cobrador").select2();
						    });
						</script>
						
						<style>
						.select2-container {
						    min-width: 150px; !important
						}
						</style>
						
						    <div class="container-fluid bootspin">
						        <!--Spinners begin-->
						        <div class="control-group row-fluid">
						            <div class="span6">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Producto'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Producto"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="Producto" name="c_stockid" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaPlanes as $id => $Name){
						                            if(!empty($_POST['c_stockid']) && $_POST['c_stockid'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						            <div class="span6">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Empresa'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Empresa"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="empresa" name="c_empresa">
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaEmpresas as $id => $Name){
						                            if(!empty($_POST['c_empresa']) && $_POST['c_empresa'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Frecuencia de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Frecuencia de Pago"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <select id="frecuencia_pago" name="c_frecuencia_pago" class="required" style="width: 50px;" >
						                    <option value="">&nbsp;</option>
						                    <?php foreach($ListaFrecuenciaPago as $id => $Name){
						                        if(!empty($_POST['c_frecuencia_pago']) && $_POST['c_frecuencia_pago'] == $id){
						                            echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                        }
						                        ?>
						                        <option value="<?=$id?>"><?=$Name?></option>
						                    <?php } ?>
						                </select>
						            </div>
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Convenio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Convenio"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="convenio" name="c_convenio" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaConvenios as $id => $Name){
						                            if(!empty($_POST['c_convenio']) && $_POST['c_convenio'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Sucursal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sucursal"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="sucursal" name="c_loccode" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($_SESSION['rh_permitionlocation'] as $id => $Name){
						                            if(empty($_POST['c_loccode'])){
						                                $_POST['c_loccode'] = $_SESSION['UserStockLocation'];
						                            }
						                            
						                            if($_POST['c_loccode'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span4">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Forma de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Formas de Pago"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="forma_pago" name="c_paymentid" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaFormasPago as $id => $Name){
						                            if(!empty($_POST['c_paymentid']) && $_POST['c_paymentid'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						            
						            <div class="span4">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Zona'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Zona"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <input type="text" id="zona" name="c_zona" value="<?=$_POST['c_zona']?>" style="width: 150px;" /> 
						            </div>
						            
						            <div class="span4">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Cobrador'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cobrador"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="cobrador" name="c_cobrador" class="required">
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaCobradores as $id => $Name){
						                            if(!empty($_POST['c_cobrador']) && $_POST['c_cobrador'] == $id){
						                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$id?>"><?=$Name?></option>
						                        <?php } ?>
						                    </select> 
						                </div>
						            </div>
						        </div>
						        
						    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccordionCobranza" href="#collapseFourCobranza">
                    <?php echo _('Pagos'); ?>
                </a>
            </div>
            <div id="collapseFourCobranza" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <script type="text/javascript">
					    $(document).on('ready', function() {
					        $('#fecha_corte').datepicker({
					            dateFormat : 'yy-mm-dd'
					        });
					        $('#fecha_ultaum').datepicker({
					            dateFormat : 'yy-mm-dd'
					        });
					        
					        $("#identificacion").select2();
					        $("#tipo_tarjeta").select2();
					        $("#dias_cobro_pordia").select2();
					        $("#dias_cobro_dia").select2();
					        $("#dias_revision_pordia").select2();
					        $("#dias_revision_dia").select2();
					        
					        $('#forma_pago').change(function(){
					            if($('#forma_pago').val() == 2){
					                $('.TarjetaC').hide();
					            }else{
					                $('.TarjetaC').show();
					            }
					        });
					        
					        $('#dias_cobro_pordia').change(function(){
					            if($('#dias_cobro_pordia').val() == 'Por Numero'){
					                var input = "<input name='dias_cobro_dia' id='dias_cobro_dia' style='width: 120px;' >";
					                $('#DiaORNum1').html(input);
					            }else{
					                var input = '<select id="dias_cobro_dia" name="c_dias_cobro_dia" class="required" style="width: 100px;" ><option value="">Dia</option><option>L</option><option>M</option><option>M</option><option>J</option><option>V</option></select>';
					                var js ='';
					                $('#DiaORNum1').html(input);
					            }
					        });
					        
					        $('#dias_revision_pordia').change(function(){
					            if($('#dias_revision_pordia').val() == 'Por Numero'){
					                var input = "<input name='dias_revision_dia' id='dias_revision_dia' style='width: 120px;' >";
					                $('#DiaORNum2').html(input);
					            }else{
					                var input = '<select id="dias_revision_dia" name="c_dias_revision_dia" class="required" style="width: 100px;" ><option value="">Dia</option><option>L</option><option>M</option><option>M</option><option>J</option><option>V</option></select>';
					                var js ='';
					                $('#DiaORNum2').html(input);
					            }
					        });
					    }); 
					</script>
					
					    <div class="container-fluid bootspin">
					        <!--Spinners begin-->
					        <div class="control-group row-fluid TarjetaC" >
					            <div class="span2">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Cuenta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuenta"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <input type="text" id="cuenta" name="c_cuenta" class="required" style="width: 150px;" value="<?=$_POST['c_cuenta']?>" />
					                </div>
					            </div>
					            <div class="span6">
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span2 TarjetaC">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Vencimiento'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Vencimiento"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2 TarjetaC">
					                <div class="controls">
					                    <input type="text" id="vencimiento" name="c_vencimiento" style="width: 150px;" value="<?=$_POST['c_cuenta']?>" />
					                </div>
					            </div>
					            
					            <div class="span2">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Cuenta SAT'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuenta SAT"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span6">
					                <div class="controls">
					                    <input type="text" id="cuenta_sat" name="c_cuenta_sat" style="width: 120px;" value="<?=$_POST['c_cuenta_sat']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span4 TarjetaC">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Número de Plastico'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Número de Plastico"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="num_plastico" name="c_num_plastico" style="width: 150px;" value="<?=$_POST['c_num_plastico']?>" />
					                </div>
					            </div>
					            <div class="span2">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Metodo de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Metodo de Pago"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span4">
					                <div class="controls">
					                    <input type="text" id="metodo_pago" name="c_metodo_pago" style="width: 120px;" value="<?=$_POST['c_metodo_pago']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid TarjetaC">
					            <div class="span2">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Tipo de Tarjeta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Tipo de Tarjeta"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <select id="tipo_tarjeta" name="c_tipo_tarjeta" >
					                        <option value="">&nbsp;</option>
					                        <?php foreach($ListaTipoTarjetas as $id => $Name){ 
					                            if(!empty($_POST['c_tipo_tarjeta']) && $_POST['c_tipo_tarjeta'] == $id){
					                                echo '<option selected="selected" value="'. $id .'">'. $Name .'</option>';
					                            }
					                            ?>
					                            <option value="<?=$id?>"><?=$Name?></option>
					                        <?php } ?>
					                    </select>
					                </div>
					            </div>
					            <div class="span2">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Tipo de Cuenta'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Tipo de Cuenta"><i class="icon-photon info-circle"></i></a>
					                </label>
					            </div>
					            <div class="span6">
					                <div class="controls">
					                    <input type="text" id="tipo_cuenta" name="c_tipo_cuenta" value="<?=$_POST['c_tipo_cuenta']?>" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Numero Empleado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Empleado"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <input type="text" id="num_empleado" name="c_num_empleado" value="<?=$_POST['c_num_empleado']?>" style="width: 120px;" /> 
					            </div>
					            
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Identificación'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Identificación"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <select id="identificacion" name="c_identificacion" class="required" >
					                        <?php
					                        if(!empty($_POST['c_identificacion'])){
					                            echo '<option selected="selected">'. $_POST['c_identificacion'] .'</option>';
					                        }
					                        ?>
					                        <option>IFE</option>
					                        <option>PASAPORTE</option>
					                        <option>ACTA</option>
					                    </select>
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span2">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Fecha Corte'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Corte"><i class="icon-photon info-circle"></i></a>
					                </label>
					                
					            </div>
					            <div class="span2">
					                <div class="controls">
					                    <input type="text" id="fecha_corte" name="c_fecha_corte" disabled="disabled" class="required" value="<?=$_POST['c_fecha_corte']?>" style="width: 120px;" /> 
					                </div>
					            </div>
					            <div class="span8"></div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Requiere Factura'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Requiere Factura Fisica?"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="checkbox" id="factura_fisica" name="c_factura_fisica"  value="<?=$_POST['c_factura_fisica']?>" >
					                </div>
					            </div>
					            
					            <div class="span6">
					                <label class="control-label" for="spin1">
					                    <?php echo _('Folio Asociado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Folio Asociado"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="folio_asociado" name="c_folio_asociado" class="required" value="<?=$_POST['c_folio_asociado']?>" style="width: 120px;" />
					                </div>
					            </div>
					        </div>
					        
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Dias de Cobro'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="RFC"><i class="icon-photon info-circle"></i></a>
					                </label>
					                
					                <select id="dias_cobro_pordia" name="c_dias_cobro" class="required" >
					                    <?php
					                        if(!empty($_POST['c_dias_cobro'])){
					                            echo '<option selected="selected">'. $_POST['c_dias_cobro'] .'</option>';
					                        }
					                    ?>
					                    <option value="Por Dia">Por Día</option>
					                    <option value="Por Numero">Por Número</option>
					                </select>
					                <ChangeElement id="DiaORNum1" >
					                <select id="dias_cobro_dia" name="c_dias_cobro_dia" >
					                    <?php
					                        if(!empty($_POST['c_dias_cobro_dia'])){
					                            echo '<option selected="selected">'. $_POST['c_dias_cobro_dia'] .'</option>';
					                        }
					                    ?>
					                    <option value="">Dia</option>
					                    <option>L</option>
					                    <option>M</option>
					                    <option>M</option>
					                    <option>J</option>
					                    <option>V</option>
					                </select>
					                </ChangeElement>
					            </div>
					            
					            <div class="span3">
					                <label class="control-label">De:</label>
					                <div class="controls">
					                    <input type="text" id="cobro_datefrom" name="c_cobro_datefrom" value="<?=$_POST['c_cobro_datefrom']?>" style="width: 100px;" />
					                </div>
					            </div>
					            <div class="span3">
					                <label class="control-label">a:</label>
					                <div class="controls">
					                    <input type="text" id="cobro_dateto" name="c_cobro_dateto" value="<?=$_POST['c_cobro_dateto']?>" style="width: 100px;" />
					                </div>
					            </div>
					        </div>
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Dias de Credito'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Dias de Credito"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <div class="controls">
					                    <input type="text" id="dias_credito" name="c_dias_credito" value="<?=$_POST['c_dias_credito']?>" style="width: 100px;" /> 
					                </div>
					            </div>
					            <div class="span6">
					            </div>
					        </div>
					        
					        
					        <div class="control-group row-fluid">
					            <div class="span6">
					                <label class="control-label" for="spin2">
					                    <?php echo _('Dias de Revision'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Dias de Revision"><i class="icon-photon info-circle"></i></a>
					                </label>
					                <select id="dias_revision_pordia" name="c_dias_revision" >
					                    <?php
					                        if(!empty($_POST['c_dias_revision'])){
					                            echo '<option selected="selected">'. $_POST['c_dias_revision'] .'</option>';
					                        }
					                    ?>
					                    <option value="Por Dia">Por Día</option>
					                    <option value="Por Numero">Por Número</option>
					                </select>
					                <ChangeElement id="DiaORNum2" >
					                <select id="dias_revision_dia" name="c_dias_revision_dia" >
					                    <?php
					                        if(!empty($_POST['c_dias_revision_dia'])){
					                            echo '<option selected="selected">'. $_POST['c_dias_revision_dia'] .'</option>';
					                        }
					                    ?>
					                    <option>L</option>
					                    <option>M</option>
					                    <option>M</option>
					                    <option>J</option>
					                    <option>V</option>
					                </select>
					                </ChangeElement>
					            </div>
					            
					            <div class="span3">
					                <label class="control-label">De:</label>
					                <div class="controls">
					                    <input type="text" id="revision_datefrom" name="c_revision_datefrom" value="<?=$_POST['c_revision_datefrom']?>" style="width: 100px;" />
					                </div>
					            </div>
					            <div class="span3">
					                <label class="control-label">a:</label>
					                <div class="controls">
					                    <input type="text" id="revision_dateto" name="c_revision_dateto" value="<?=$_POST['c_revision_dateto']?>" style="width: 100px;" />
					                </div>
					            </div>
					        </div>
					        
					    </div>
                    </p>
                </div>
            </div>
        </div>
        
    </div>
       
    <div id="Savebtn" class="control-group row-fluid">
        <div class="span3">
            <?php if($_POST['Action'] == "UPDATE"){ ?>
                <input type="submit" id="UpdateData" name="UpdateData" class="btn btn-large btn-success" value="Actualizar" style="margin-bottom: 0px;" />
            <?php }else{ ?>
                <input type="submit" id="UpdateData" name="SaveData" class="btn btn-large btn-success" value="Guardar y Continuar" style="margin-bottom: 0px;" />
            <?php } ?>
        </div>
    </div>
    <div style="height: 10px;"></div>
</form>

<form class="form-horizontal" id="SociosForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/sociosavedata/"); ?>" >	
    <div style="height: 10px;"></div>
    <div id="Accordion" class="accordion">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseOne">
                    <?php echo _('Datos del Socio'); ?>
                </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <script type="text/javascript">
						    $(document).on('ready', function() {
						        
						        $('#fecha_ultaum').datepicker({
						            dateFormat : 'yy-mm-dd', 
						            changeMonth: true, 
						            changeYear: true, 
						            yearRange: '-100:+5'
						        });
						        
						        $('#fecha_nacimiento').datepicker({
						            dateFormat : 'yy-mm-dd', 
						            changeMonth: true, 
						            changeYear: true, 
						            yearRange: '-100:+0'
						        });
						        
						        $("#sexo").select2();
						        $("#braddress7").select2();
						        $("#braddress8").select2();
						    });
						    
						    function IsNumeric(input){
						        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
						            //alert('OK')
						        }else{
						            alert('Solo se admiten numeros');
						        }
						    }
						</script>
						
						    <div class="container-fluid bootspin">
						        <!--Spinners begin-->
						        <div class="control-group row-fluid">
						            
						            <div class="span6">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Nombre(s)'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre(s)"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <input type="text" id="name" name="brname" value="<?=$_POST['brname']?>" class="required" /> 
						            </div>
						            
						            <div class="span6">
						                <label class="control-label" for="spin2">
						                    <?php echo _('Sexo'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sexo"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <select id="sexo" name="sexo" class="required">
						                        <?php
						                        if(!empty($_POST['sexo'])){
						                            echo '<option selected="selected">'. $_POST['sexo'] .'</option>';
						                        }
						                        ?>
						                        <option>MASCULINO</option>
						                        <option>FEMENINO</option>
						                    </select> 
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            
						            <div class="span12">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Nombre Comercial'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Nombre Comercial"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="nombre_empresa" name="nombre_empresa"  value="<?=$_POST['nombre_empresa']?>" >
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Fecha Nacimiento'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Nacimiento"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="fecha_nacimiento" name="fecha_nacimiento" class="required" style="width: 120px;" value="<?=$_POST['fecha_nacimiento']?>" />
						                </div>
						            </div>
						                    <?php 
						                    if(empty($_POST['fecha_ingreso'])){
						                        $_POST['fecha_ingreso'] = date('Y-m-d');
						                    }
						                    ?>
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Fecha de Ingreso'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha de Ingreso"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="fecha_ingreso" readonly="readonly" name="fecha_ingreso" class="required" style="width: 120px;" value="<?=$_POST['fecha_ingreso']?>" />
						                </div>
						            </div>
						            <div class="span4">
						                <label class="control-label" for="spin1">
						                    <?php echo _('Fecha Ult Aum.'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Fecha ult Aum"><i class="icon-photon info-circle"></i></a>
						                </label>
						                <div class="controls">
						                    <input type="text" id="fecha_ultaum" name="fecha_ultaum" readonly="readonly" style="width: 120px;" value="<?=$_POST['fecha_ultaum']?>" />
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span1">
						                <label class="control-label" for="spin1">
						                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span3">
						                <div class="controls">
						                    <input type="text" id="braddress1" name="braddress1" class="required" style="width: 200px;" value="<?=$_POST['braddress1']?>" />
						                </div>
						            </div>
						            <div class="span1">
						                <label class="control-label" for="spin1">
						                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span3">
						                <div class="controls">
						                    <input type="text" id="braddress2" name="braddress2" class="required" style="width: 200px;" value="<?=$_POST['braddress2']?>" />
						                </div>
						            </div>
						            
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span2">
						                <div class="controls">
						                    <input type="text" id="braddress10" name="braddress10" class="required" style="width: 80px;" value="<?=$_POST['braddress10']?>" />
						                </div>
						            </div>
						            
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin2">
						                    <?php echo ('Colonia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Colonia"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <input type="text" id="braddress4" name="braddress4" class="required" value="<?=$_POST['braddress4']?>" /> 
						                </div>
						            </div>
						            <div class="span2">
						                <label class="control-label" for="spin2">
						                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <input type="text" id="braddress5" name="braddress5" class="required" value="<?=$_POST['braddress5']?>" /> 
						                </div>
						            </div>
						        </div>
						        
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin3">
						                    <?php echo _('Entre Calles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Entre Calles"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span10">
						                <div class="controls">
						                    <input type="text" id="braddress6" name="braddress6" class="required" value="<?=$_POST['braddress6']?>" />
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin3">
						                    <?php echo _('Cuadrante'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuadrante"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span10">
						                <div class="controls">
						                    <input type="text" id="cuadrante1" name="cuadrante1" style="width: 80px;" value="<?=$_POST['cuadrante1']?>" onBlur="IsNumeric(this.value)" />
						                    <input type="text" id="cuadrante2" name="cuadrante2" style="width: 80px;" value="<?=$_POST['cuadrante2']?>" />
						                    <input type="text" id="cuadrante3" name="cuadrante3" style="width: 80px;" value="<?=$_POST['cuadrante3']?>" onBlur="IsNumeric(this.value)" />
						                    <script>
						                        $(document).ready(function(){
						                            $('#cuadrante2').mask('?a');
						                        });
						                    </script>
						                </div>
						            </div>
						        </div>
						        
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Municipio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Municipio"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <select id="braddress7" name="braddress7" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaMunicipios as $id => $Municipio){ 
						                            if(!empty($_POST['braddress7'])){
						                                echo '<option selected="selected" value="'. $_POST['braddress7'] .'">'. $_POST['braddress7'] .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						            
						            <div class="span2">
						                <label class="control-label">
						                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                    <select id="braddress8" name="braddress8" class="required" >
						                        <option value="">&nbsp;</option>
						                        <?php foreach($ListaEstados as $id => $Estado){ 
						                            if(!empty($_POST['braddress8'])){
						                                echo '<option selected="selected" value="'. $_POST['braddress8'] .'">'. $_POST['braddress8'] .'</option>';
						                            }
						                            ?>
						                            <option value="<?=$Estado?>"><?=$Estado?></option>
						                        <?php } ?>
						                    </select>
						                </div>
						            </div>
						        </div>
						        
						        <div class="control-group row-fluid">
						            <div class="span2">
						                <label class="control-label" for="spin3">
						                    <?php echo _('Telefono'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Telefono"><i class="icon-photon info-circle"></i></a>
						                </label>
						            </div>
						            <div class="span4">
						                <div class="controls">
						                      <input type="text" id="phoneno" name="phoneno" value="<?=$_POST['phoneno']?>" class="required" />
						                </div>
						            </div>
						        </div>
						        
						    </div>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseTwo">
                    <?php echo _('Antecedentes Clinicos'); ?>
                </a>
            </div>
            <div id="collapseTwo" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
					                       
					    <div class="span12">
					        <label class="control-label" for="AntecedentesC">Seleccione Antecedentes Clinicos</label>
					    </div>
					    <div class="span8">
					        <div class="controls">
					            <select multiple name="antecedentes_clinicos[]" id="AntecedentesC">
					                <?php foreach($LIstAntecedentesClinicos as $id => $Name){ 
					                if(in_array($Name, $_POST['antecedentes_clinicos'])){
					                    $Selected = "selected = 'selected'";
					                }else{
					                    $Selected = "";
					                }
					                ?>
					                
					                <option value="<?=$Name?>" <?=$Selected?> ><?=$Name?></option>
					                <?php } ?>
					                <!-- <option selected="" value="Gray">Gray</option> -->
					            </select>
					        </div>
					        <div class="controls">
					        <label>Otros Padecimientos</label>
					        <input type="text" name="antecedentes_clinicos[otros]" value="<?=$POST['antecedentes_clinicos']['otros']?>" />
					        </div>
					    </div>
					    <div class="clearfix"></div>
					
					    <script>
					        $().ready(function(){
					            $("#AntecedentesC").pickList();
					        });
					    </script>
					    <div style="height: 50px"></div>

                    </p>
                </div>
            </div>
        </div>
        
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseThree">
                    <?php echo _('Socios'); ?>
                </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse ">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
                        <script type="text/javascript">
						    $(document).on('ready', function() {
						        $('#SociosTable').dataTable( {
						            "sPaginationType": "bootstrap",
						            "aLengthMenu": [
						                [10,25, 50, 100, 200, -1],
						                [10,25, 50, 100, 200, "All"]
						            ],
						            "fnInitComplete": function(){
						                //$(".dataTables_wrapper select").select2({
						                    //dropdownCssClass: 'noSearch'
						                //});
						            }
						        });
						        
						        
						        
						        $("a[name='ViewSocio']").click(function(){
						            var DebtorNo = $(this).attr('DebtorNo');
						            var BranchCode = $(this).attr('BranchCode');
						            $('#ModalLabelView').text('Detalle del Socio N° ' + DebtorNo + '-' + BranchCode);
						            $('#Modal_ViewSocio').modal('show');
						            GetSocioData(DebtorNo,BranchCode);
						        });
						        
						        $("a[name='EditSocio']").click(function(){
						            //alert($('#EditSocio').attr('id'));
						        });
						        
						        $("a[name='SuspenderSocio']").click(function(){
						            var DebtorNo = $(this).attr('DebtoNo');
						            var BranchCode = $(this).attr('BranchCode');
						            $('#ModalLabelSuspension').text('Suspender Socio N° ' + DebtorNo + '-' + BranchCode);
						            
						            $('#Sus_BranchCode').val(BranchCode);
						            $('#Sus_DebtorNo').val(DebtorNo);
						            
						            $('#Modal_SuspenderSocio').modal('show');
						        });
						        
						        
						        //$('#Change_Status').removeAttr('style');
						        $("#colstatus").removeAttr( 'style' );
						        
						        $("select[name='SociosTable_length']").addClass('span2');
						        
						        $("select[name='SociosTable_length']").css({"height":"0px", "margin-top":"-35px"});
						        
						    });
						    
						    function ChangeStatus(Action,DebtorNo,BranchCode){
						        //alert($(this).attr('value'));
						        //var Action = $(this).attr('value');
						        //var DebtorNo = $(this).attr('DebtoNo');
						        //var BranchCode = $(this).attr('BranchCode');
						        
						        switch(Action){
						            case 'Activo':
						                alert('activo');
						            break;
						            case 'Cancelado':
						                alert('cancelado');
						                $('#ModalLabelCancelacion').text('Cancelar Socio N° ' + DebtorNo + '-' + BranchCode);
						                $('#Cancel_BranchCode').val(BranchCode);
						                $('#Cancel_DebtorNo').val(DebtorNo);
						                $('#Modal_CancelarSocio').modal('show');
						            break;
						            case 'Suspendido':
						                $('#ModalLabelSuspension').text('Suspender Socio N° ' + DebtorNo + '-' + BranchCode);
						                $('#Sus_BranchCode').val(BranchCode);
						                $('#Sus_DebtorNo').val(DebtorNo);
						                $('#Modal_SuspenderSocio').modal('show');
						            break;
						            default:
						            break;
						        }
						    }
						    
						</script>
						
						
						<style>
						    .table thead th {
						        font-size: 12px;
						        line-height: 20px;
						    }
						    
						    .table td {
						        font-size: 10px;
						    }
						    
						    .dataTables_length{
						        /*width:100px;*/
						    }
						    
						    .dataTables_filter input {
						        margin-bottom: 0 !important;
						    }
						    
						    .select2-container {
						        margin: 15px 0;
						        min-width: 100px;
						        vertical-align: top;
						    }
						    /*.select2-container .select2-choice {
						        /*width: 100px;*/
						    }*/
						    
						    #Change_Status{
						        margin-bottom: 0 !important;
						        /*margin-top:10px;*/
						    }
						    #colstatus{
						        width: 100px; !important;
						    }
						    
						</style>
						
						<?php include_once('modals/ViewSocio.modal.php'); ?>
						<?php include_once('modals/SuspenderSocio.modal.php'); ?>
						<?php include_once('modals/ChangeStatuss.modal.php'); ?>
						<table id="SociosTable" class="table table-bordered" style="width: 100%;" >
						    <thead>
						        <tr>
						            <th>N° Socio</th>
						            <th>Nombre</th>
						            <th>Sexo</th>
						            <th>Nombre Comercial</th>
						            <th>Fecha Nacimiento</th>
						            <th>Calle</th>
						            <th>N°</th>
						            <th>Telefono</th>
						            <th>Fecha Ingreso</th>
						            <th>Fecha ult. Aum</th>
						            <th id="colstatus">Status</th>
						            <th style="text-align: center; width: 50px;"><i class="icon-cog" title="Actions"></i></th>
						        </tr>
						    </thead>
						    <tbody>
						        <?php foreach($GetSocios as $Socio){ 
						        if($Socio['movimientos_socios'] =='Suspendido'){
						            $Class = "class= 'warning'";
						        }elseif($Socio['movimientos_socios'] =='Cancelado'){
						            $Class = "class= 'danger'";
						        }else{
						            $Class = "";
						        }
						        ?>
						        
						        <tr <?=$Class?>>
						            <td><?=$Socio['branchcode']?></td>
						            <td><?=$Socio['brname']?></td>
						            <td><?=$Socio['sexo']?></td>
						            <td><?=$Socio['nombre_empresa']?></td>
						            <td><?=$Socio['fecha_nacimiento']?></td>
						            <td><?=$Socio['braddress1']?></td>
						            <td><?=$Socio['braddress2']?></td>
						            <td><?=$Socio['phoneno']?></td>
						            <td><?=$Socio['fecha_ingreso']?></td>
						            <td><?=$Socio['fecha_ultaum']?></td>
						            <td>
						                <select id="Change_Status" name="Change_Status" BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" onchange="ChangeStatus(this.value,<?=$Socio['debtorno']?>,<?=$Socio['branchcode']?>)" >
						                    <option SELECTED="SELECTED" value="<?=$Socio['movimientos_socios']?>"><?=$Socio['movimientos_socios']?></option>
						                    <option value="Activo">Activo</option>
						                    <option value="Cancelado">Cancelado</option>
						                    <option value="Suspendido">Suspendido</option>
						                </select>
						            </td>
						            <td>
						                <a id="ViewSocio"   name = "ViewSocio"   BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" title="Detalles"><i class="icon-eye-open"></i></a>&nbsp;
						                <a id="EditSocio"   name = "EditSocio"   BranchCode="<?=$Socio['branchcode']?>" DebtorNo="<?=$Socio['debtorno']?>" title="Editar Socio" ><i class="icon-edit"></i></a>&nbsp;
						                <a id="SuspenderSocio" name = "SuspenderSocio" BranchCode="<?=$Socio['branchcode']?>" DebtoNo="<?=$Socio['debtorno']?>" title="Suspender Socio"><i class="icon-lock"></i></a>
						            </td>
						        </tr>
						        <?php } ?>
						    </tbody>
						</table>

                    </p>
                </div>
            </div>
        </div>
        <input type="hidden"  id="hiddenFolio" name="Folio" value="<?=$_POST['Folio']?>"/>
    </div>
    <div id="SavebtnSocio" class="control-group row-fluid">
        <div class="span3">
            <?php /*if($_POST['Action'] == "UPDATE"){ ?>
                <input type="submit" id="UpdateData" name="UpdateData" class="btn btn-large" value="Actualizar" style="margin-bottom: 0px;" />
            <?php }else{ */?>
                <input type="submit" id="SaveData" name="SaveData" class="btn btn-large" value="Guardar" style="margin-bottom: 0px;" />
            <?php //} ?>
        </div>
    </div>
     <div style="height: 10px;"></div>
</form>

