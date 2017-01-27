<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */
?>
<script>
    $(document).on('ready', function(){
        $('#btn_CrearOportunidad').hide();

        var table= $('#ContactsDTable').dataTable({
             "scrollX": true
        });

      	$('#start_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $('#end_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        /*Crea Actividad desde el Formulario Se logue en el Calendario y se liga al prospecto*/
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

   // $( ".column" ).sortable({ disabled: true });

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

    function cambiarFase(id, nombre_fase){
        fase_actual = $(".current").attr('id');
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl('leads/cambiarFase'); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                Fase:{
                    id_fase_venta: id,
                    CurrentFase: fase_actual,
                    nombre_fase: nombre_fase,
                    prospecto_id: $('#prospecto_id').val(),
                    nombre_prospecto: $('#nombre_prospecto').val(),
                    title:'Update',
                    tipo_log:'Update',
                    descripcion: 'La fase de venta cambió a: '+nombre_fase,
                },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                    $('#' + Response.LastFase).removeClass('current');
                    $('#' + Response.LastFase).addClass('unavailable');
                    $('#fase_' + Response.CurrentFase).addClass('current');
                    $('#fase_' + Response.CurrentFase).removeClass('unavailable');
                    $('#ListUpdates').prepend(Response.NewUpdate);
                }else{
                    displayNotify('alert', Response.message);
                }
            },
            error : ajaxError
        });
    }

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

    <ul class="breadcrumbs">
        <?php foreach($Fases_Venta as $Fases){
            if($Fases['id']==$ProspectoData['id_fase_venta']){
                $status="current";
            }else{
                $status="unavailable";
            }
        ?>
        <li class="<?=$status?>" id="fase_<?=$Fases['id']?>"><a onclick="cambiarFase(<?=$Fases['id']?>, '<?=$Fases['nombre']?>')"><?=$Fases['nombre']?></a></li>
        <?php } ?>
    </ul>

    <div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/leads/index');?>" class="small button">Listar Prospectos</a></li>
				<li><a href="<?php echo Yii::app()->createUrl('crm/leads/create');?>" class="small button">Crear Prospecto</a></li>
				<li><a href="#" class="small button" data-reveal-id="myModal">Agregar Contacto Rapído</a></li>
            <?php if($ProspectoData['tipo']=='PROSPECTO'){ ?>
				<li><a href="#" class="small button" data-reveal-id="Crearcuenta" id="btn_crearCuenta">Crear Cuenta</a></li>
            <?php }else{ ?>
                <li><a href="#" class="small button" data-reveal-id="crearOportunidad">Crear Oportinidad</a></li>
            <?php } ?>
			</ul>
		</div>
        <h1><a href='<?php echo Yii::app()->createUrl("crm/leads/index");?>'>Prospecto</a></h1>
        <hr>
	</div>

	<div class="large-8 columns">


            <?php if($ProspectoData['tipo']=='PROSPECTO'){ ?>
                <div class="large-5 small-12 columns">
                    <h3>Detalle del Prospecto: </h3>
                </div>
                <div class="large-7 small-12 columns">
                    <h3>
                        <?php echo $ProspectoData['nombre'] . ' ' . $ProspectoData['apellidoPaterno']; ?>
                        <span data-tooltip class="has-tip radius" title="Editar prospecto"><a href="<?=Yii::app()->createUrl("crm/leads/update", array('id'=>$ProspectoData['idProspecto']))?>" class="fi-pencil"></a></span>&nbsp;
                    </h3>
                </div>

            <?php }else{ ?>
            <h3>Detalle de la cuenta: <?php echo $ProspectoData['nombre'] . ' ' . $ProspectoData['apellidoPaterno']; ?>
            <span data-tooltip class="has-tip radius" title="Editar cuenta"><a href="<?=Yii::app()->createUrl("crm/leads/update", array('id'=>$ProspectoData['idProspecto']))?>" class="fi-pencil"></a></span>&nbsp;
            <?php } ?>
            </h3>


        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <input type="hidden" id="prospecto_id" name="prospecto_id" value="<?=$ProspectoData['idProspecto']?>">
                <label class="label1">Nombre:</label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['nombre']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Apellido Paterno: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['apellidoPaterno']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Apellido Materno: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['apellidoMaterno']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Fecha de Alta: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['fechaAlta']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Ultima Actualización: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['fechaUltimaActualizacion']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Estatus: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['status_prospecto']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">E-mail: </label><label class="label2"><?=$ProspectoData['email']?></label>
            </div>
            <div class="large-6 small-12 columns">
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Teléfono: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['telefono']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Celular: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['celular']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Contactar por celular: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2">
                    <?php
                        if($ProspectoData['contactarPorCelular']==1){
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
                <label class="label1">Contactar por e-mail: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2">
                    <?php
                        if($ProspectoData['contactarPorEmail']==1){
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
                <label class="label1">Calle: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion1']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Exterior: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion2']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Interior: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion3']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Colonia: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion4']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Localidad: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion5']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Referencia: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion6']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Municipio: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion7']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Estado: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion8']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">País: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion9']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Código Postal:  </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['direccion10']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Origen del Prospecto: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['source']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Empresa: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['company']?></label>
            </div>
        </div>

        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Skype:  </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['skype']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Facebook: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['facebook']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Google plus: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['googlePlus']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Twitter: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['twitter']?></label>
            </div>
        </div>

		<div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Descripción: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['descripcion']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">RFC: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['RFC']?></label>
            </div>
        </div>
          <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Tipo: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$ProspectoData['tipo']?></label>
            </div>
        </div>



        <div class="large-12 small-12 columns" style="padding-top:5%;">
            <h3>Contactos Relacionados</h3>
        </div>
        <div class="large-12 columns small-12" >
            <table id="ContactsDTable" width="100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido Paterno</th>
                        <th>Correo electronico</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ListaContactos as $Data) { ?>
                        <tr id="<?=$Data['idContacto']?>">
                            <td><?=$Data['nombre']?></td>
                            <td><?=$Data['apellidoPaterno']?></td>
                            <td><?=$Data['email']?></td>
                            <td style="width:15px;">
                                <span data-tooltip class="has-tip radius" title="Ver información a detalle"><a href="<?php echo Yii::app()->createUrl("crm/contactos/view", array('id'=>$Data['idContacto'])); ?>" class="fi-magnifying-glass"></a></span>&nbsp;
                                <span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?php echo Yii::app()->createUrl("crm/contactos/update", array('id'=>$Data['idContacto'])); ?>" class="fi-pencil"></a></span>&nbsp;
                                <!--span data-tooltip class="has-tip radius" title="Eliminar contacto"><a href='.Yii::app()->createUrl("contactos/delete").'&id='.$value->id_contacto.' class="fi-x"></a></span>&nbsp;<!--a href="#" class="fi-x"></a></span-->&nbsp;
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
	</div>



<div class="large-4 columns">
    <div class="small-2 columns" >
        <div class="column">
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
                    <input type="hidden" id="contacto_id" name="contacto_id" value=0>
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

            <div class="portlet" id="2">
                <div class="portlet-header">Actividades</div>
                <div class="portlet-content">
    				<div id="ListActivities" style="height:300px; overflow-y:scroll;">
                        <?php foreach ($ListActivities as $Data){ ?>
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

            <div class="portlet" id="3">
                <div class="portlet-header">Actualizaciones</div>
                <div class="portlet-content">
                    <div id="ListUpdates" style="height:300px; overflow-y:scroll;">
                        <?php foreach ($ListUpdates as $Data) { ?>

                        <div class="panel2 callout radius" style="margin-bottom: 5px;" >
                            <h4 style="margin-top: -5px;"><small><?=$Data['userid']?></small></h4>
                            <p style="font-size:10px; margin-bottom:5px;"><?=$Data['Descripcion']?></p>
                            <p style="font-size:10px; margin-bottom:5px;"><?=$Data['start']?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un contacto</h2>
	<?php include_once "_quickformcontacto.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>
<div id="crearOportunidad" class="reveal-modal" data-reveal>
    <h2>Crear oportunidad</h2>
    <?php include_once "_crearOportunidad.php"; ?>
    <a class="close-reveal-modal">&#215;</a>
</div>
<div id="Crearcuenta" class="reveal-modal" data-reveal>
    <h2>Crear cuenta</h2>
    <?php include_once "_crearCuenta.php"; ?>
    <a class="close-reveal-modal">&#215;</a>
</div>




