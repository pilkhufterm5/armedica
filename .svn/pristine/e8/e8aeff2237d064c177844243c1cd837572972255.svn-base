<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */
?>
  <script>
  $(document).on('ready', function(){

  	$('#start_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });
  	$('#end_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });
  	$('#guardar').click(function(event){

  			event.preventDefault();
        	var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl('activities/createactivitie'); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                 Create:{
                      actividad: $('#tipo_actividad').val(),
                      title: $('#nombre_evento').val(),
                      start: $('#start_date').val(),
                      end: $('#end_date').val(),
                      contacto_id: $('#contacto_id').val(),
                      prospecto_id: $('#prospecto_id').val(),
                      descripcion: $('#descripcion').val(),
                      TipoLog: 'Calendario'
                  },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                    $('#tipo_actividad').val(null);
                    $('#nombre_evento').val(null);
                    $('#start_date').val(null);
                    $('#end_date').val(null);
                    $('#descripcion').val(null);
                    $('#ListActivities').prepend(Response.NewActivity);
                }else{
                    displayNotify('alert', Response.message);
                }
            },
            error : ajaxError
        });

  	});

  	});

  $(function() {
    $( ".column" ).sortable({
      connectWith: ".column",
      handle: ".portlet-header",
      cancel: ".portlet-toggle",
      placeholder: "portlet-placeholder ui-corner-all",
      stop: function( event, ui ) {
        console.log(ui.item);
      }
    });

    $( ".portlet" )
      .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

    $( ".portlet-toggle" ).click(function() {
      var icon = $( this );
      icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
      icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
    });
  });
  </script>

  <style>
  body {
    min-width: 520px;
  }
  .column {
    width: 300px;
    /*float: left;*/
    padding-bottom: 100px;
    /*border:solid;*/
    border-color: #D6E9C6;
    box-shadow: 2px 5px 3px 5px #DDDDDD, 0 1px 2px 0 #FFFFFF inset;
  }
  .portlet {
    margin: 0 1em 1em 0;
    padding: 0.3em;
  }
  .portlet-header {
    padding: 0.2em 0.3em;
    margin-bottom: 0.5em;
    position: relative;
  }
  .portlet-toggle {
    position: absolute;
    top: 50%;
    right: 0;
    margin-top: -8px;
  }
  .portlet-content {
    padding: 0.4em;
  }
  .portlet-placeholder {
    border: 1px dotted black;
    margin: 0 1em 1em 0;
    height: 50px;
  }
  .ui-widget-header {
    border: 1px solid #aaaaaa;
    background: #008cba url("images/ui-bg_highlight-soft_75_cccccc_1x100.png") 50% 50% repeat-x;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 14px;
  }
  </style>


<?php FB::INFO($model); ?>
<?php FB::INFO($_SESSION);?>


	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/contactos/index');?>" class="small button">Listar Contactos</a></li>
				<li><a href="<?php echo Yii::app()->createUrl('crm/contactos/create');?>" class="small button">Crear Contacto</a></li>
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Contacto Rapido</a></li>
			</ul>
		</div>
		<h1><a href='<?php echo Yii::app()->createUrl("crm/contactos/index");?>'>Contactos</a></h1>
		<hr/>
	</div>


    <div class="large-8 columns">

        <h3>Detalle de Contacto: <?php echo $ContactoData['nombre'] . ' ' . $ContactoData['apellidoPaterno']; ?>
        <span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?=Yii::app()->createUrl("crm/contactos/update")?>&id=<?=$ContactoData['idContacto']?>" class="fi-pencil"></a></span>&nbsp;
        </h3>

        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <input type="hidden" id="prospecto_id" name="prospecto_id" value="<?=$ContactoData['idContacto']?>">
                <label class="label1">Nombre:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['nombre']?></label>
            </div>
        </div>

        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Apellido Paterno:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['apellidoPaterno']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Apellido Materno:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['apellidoMaterno']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Fecha de Alta:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['fechaAlta']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Ultima Actualización:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['fechaUltimaActualizacion']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Estatus:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2">
                    <?php if($ContactoData['estatus']==1){
                        echo "Activo";
                    }else{
                        echo "Inactivo";
                    } ?>
                </label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">E-mail:</label><label class="label2"><?=$ContactoData['email']?></label>
            </div>
            <div class="large-6 small-12 columns">
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Teléfono:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['tel_empresa']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Tel. Empresa:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['telefono']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Celular:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['celular']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Contactar por celular:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2">
                    <?php if($ContactoData['contactarPorCelular']==1){
                        echo "Sí";
                    }else{
                        echo "No";
                    } ?>
                </label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Contactar por e-mail:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2">
                    <?php if($ContactoData['contactarPorEmail']==1){
                            echo "Sí";
                        }else{
                            echo "No";
                        }
                    ?>
                </label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Calle:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion1']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Exterior: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion2']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Interior:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion3']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Colonia:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion4']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Localidad:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion5']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Referencia:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion6']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Municipio:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion7']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Estado:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion8']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">País:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion9']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Código Postal:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['direccion10']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Skype:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['skype']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Facebook:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['facebook']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Google plus:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['googlePlus']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Twitter:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['twitter']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Prospecto:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['prospecto']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Descripción:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ContactoData['descripcion']?></label>
            </div>
        </div>
	</div>
	<div class="large-4 columns">
        <div class="small-2 columns" >
            <div class="column">
                <div class="portlet" id="2">
                    <div class="portlet-header">Actividades</div>
                    <div class="portlet-content">
    					<div id="ListActivities" style="height:300px; overflow-y:scroll;">
                            <?php foreach ($ListActivities as $Data) { ?>
                            <div class="panel2 callout radius" style="margin-bottom: 5px;" >
                                <h4 style="margin-top: -5px;"><small><?=$Data['title']?></small></h4>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['start']?></p>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['end']?></p>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['Descripcion']?></p>
                            </div>
                            <?php } ?>
    					</div>
                    </div>
                </div>
                <div class="portlet dropfalse"  id="1">
                    <div class="portlet-header">Agregar Actividad</div>
                    <div class="portlet-content">
                        <label>Tipo de Actividad:</label>
                        <select id="tipo_actividad">
                            <option value="">Seleccione Una Opción</option>
                            <?php foreach($TipoActividad as $Actividad){ ?>
                            <option value="<?=$Actividad['id']?>"><?=$Actividad['descripcion']?></option>
                            <?php } ?>
                        </select>
                        <input type="hidden" id="contacto_id" name="contacto_id" value="<?=$ContactoData['idContacto']?>">
                        <input type="hidden" id="prospecto_id" name="prospecto_id" value="<?=$ContactoData['id_prospecto']?>">
                        <label>Nombre de la Actividad:</label>
                        <input type="text" id="nombre_evento" name="nombre_evento" value="">
                        <label>Inicia:</label>
                        <input type="text" id="start_date" name="start_date" value="">
                        <label>Termina:</label>
                        <input type="text" id="end_date" name="end_date" value="">
                        <label>Descripción:</label>
                        <textarea id="descripcion" name="descripcion"></textarea>
                        <input type="button" class="button tiny small" value="Guardar" id="guardar">
                    </div>
                </div>
            </div>
        </div>
    </div>

 <div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un contacto</h2>
	<?php include_once "_quickform.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>




