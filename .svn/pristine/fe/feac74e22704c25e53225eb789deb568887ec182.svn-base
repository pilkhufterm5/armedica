<?php
/**
*  @Todo
*  Calendario Registra, Actualiza Eventos.
*  @Author erasto@realhost.com.mx
*  @var $this ActivitiesController
 **/


?>

<script>
    $(document).on('ready',function() {

        $('#contacto').select2();
        $('#Econtacto').select2();

        CreateCalendar();
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();


        $('#Estartdate').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $('#Eenddate').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $('#startdate').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $('#enddate').datetimepicker({
            lang:'es',
            formatDate:'yy-mm-dd',
        });


        function CreateCalendar(){

            var calendar = $('#calendar').fullCalendar({

                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                eventClick: function(event, element) {
                    /*Editar Actividad*/
                    $('#EditActivitie').foundation('reveal', 'open');
                    console.log(event);
                    var id = $('#E_EventID').val(event.id);

                    var title = $('#ETitulo').val(event.title);
                    var start = $('#Estartdate').val((event.start).format('YYYY/MM/DD HH:mm:ss'));
                    var end = $('#Eenddate').val((event.end).format('YYYY/MM/DD HH:mm:ss'));

                    var actividad = event.actividad_id;
                    var contacto = event.contacto_id;
                    $("#Eactividad option[value='" + actividad + "']").attr("selected",true);

                    $("#Econtacto").select2("destroy");
                    $("#Econtacto option[value='" + contacto + "']").attr("selected",true);
                    $("#Econtacto").select2();

                    $('#calendar').fullCalendar('updateEvent', event);
                },

                selectable: true,
                selectHelper: true,
                /*Agregar Actividad*/
                select: function(start, end, allDay) {
                    $('#AddActivitie').foundation('reveal', 'open');
                    var title = $('#titulo').val();
                    var end = $('#enddate').val(end.format('YYYY/MM/DD HH:mm:ss'));
                    var start = $('#startdate').val(start.format('YYYY/MM/DD HH:mm:ss'));
                },
                lang: 'es',
                timeFormat: 'YYYY-MM-DD HH:mm:ss',
                editable: true,
                /*Mover Actividad*/
                eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
                    /*Actualizo el Movimiento del Evento*/
                    window.rh_UpdateActivitie(event);
                    return true;

                },
                eventSources: [
                    {
                        url: '<?php echo $this->createUrl("activities/loadcalendar"); ?>',
                    }
                ],
            });

        }

        /*Abre Modal para Crear Actividad*/
        $('#NewActivitie').click(function() {
            $('#AddActivitie').foundation('reveal', 'open');
        });

        /*Edit Activitie Drag&Drop*/
        window.rh_UpdateActivitie =
        function (event){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("activities/UpdateActivitie"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          id: event.id,
                          title: event.title,
                          start: event.start.toString(),
                          end: event.end.toString(),
                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError,
            });
        }



        /*Crea nueva Actividad via ajax desde un Reveral Modal*/
        $('#Modal_AddEvent').click(function() {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("activities/CreateActivitie"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Create:{
                          title: $('#titulo').val(),
                          start: $('#startdate').val(),
                          end: $('#enddate').val(),
                          actividad: $('#actividad').val(),
                          contacto_id: $('#contacto').val(),
                          prospecto_id: 0,
                          descripcion:$('#descripcion').val(),
                          TipoLog: 'Calendario'
                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        $('#calendar').fullCalendar( 'destroy' )
                        displayNotify('success', Response.message);
                        $('#AddActivitie').foundation('reveal', 'close');
                        CreateCalendar();
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError,
            });
        });

        /*Actualiza Actividad via ajax desde un Reveral Modal*/
        $('#Modal_EditEvent').click(function() {
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("activities/UpdateActivitie"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      Update:{
                          id: $('#E_EventID').val(),
                          title: $('#ETitulo').val(),
                          start: $('#Estartdate').val(),
                          end: $('#Eenddate').val(),
                          actividad: $('#Eactividad').val(),
                          contacto_id: $('#Econtacto').val()

                      },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        $('#calendar').fullCalendar( 'destroy' )
                        displayNotify('success', Response.message);
                        $('#EditActivitie').foundation('reveal', 'close');
                        CreateCalendar();
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError,
            });
        });

        $('#destroy').click(function() {
            $('#calendar').fullCalendar( 'destroy' )
        });

        $('#createcalendar').click(function() {
            CreateCalendar();
        });

    });
</script>

<style>

    #TopButtons {
        margin-top: 40px;
        text-align: center;
        font-size: 14px;
        font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    }

    #calendar {
        width: 900px;
        margin: 0 auto;
    }
</style>



<div id="TopButtons">
    <input type="button" id="NewActivitie" class="button" value="Agregar Actividad" />
</div>

<?php /*Div Donde se genera en Calendario*/ ?>
<div id='calendar'></div>

<?php FB::INFO($ContactsData, 'Datos de contacto'); ?>

<?php /*Modal para Crear Nueva Actividad*/ ?>
<div id="AddActivitie" class="reveal-modal tiny" data-reveal>
    <div >
        <h3 id="myModalLabel">Agregar Actividad</h3>
    </div>
    <div >
        <p style="text-align: left;">
            <input type="hidden" id="descripcion" name="descripcion" value=""/>
            <label>Nombre de la Actividad </label>
            <input type="text" id="titulo" name="titulo"  />
            <label>Inicia </label>
            <input type="text" id="startdate" name="startdate" value="" />
            <label>Termina </label>
            <input type="text" id="enddate" name="enddate" value="" />
            <label>Tipo de Actividad </label>
            <select id="actividad" name="actividad" >
            <?php foreach($actividades as $idx => $Value){ ?>
                <option value="<?=$idx?>"><?=$Value?></option>
            <?php } ?>
            </select>
            <label>Relacionado con:</label>
             <select id="contacto" name="contacto" style="height:10px; width: 100%;">
            <?php foreach($ContactsData as $idx => $Value){ ?>
                <option value="<?=$idx?>"><?=$Value?></option>
            <?php } ?>
            </select>
        </p>
    </div>
    <div class="row" >
        <button id="Modal_AddEvent" class="button tiny">Agregar</button>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>

<?php /*Modal para Actualizar Actividad seleccionada desde el Calendario*/ ?>
<div id="EditActivitie" class="reveal-modal tiny" data-reveal>
    <div >
        <h3 id="myModalLabel">Editar Actividad</h3>
    </div>
    <div >
        <p style="text-align: left;">
            <input type="hidden" id="E_EventID" name="E_EventID"  />
            <label>Evento:</label>
            <input type="text" id="ETitulo" name="ETitulo"  />
            <label>Inicia:</label>
            <input type="text" id="Estartdate" name="Estartdate" value="" />
            <label>Termina:</label>
            <input type="text" id="Eenddate" name="Eenddate" value="" />
            <label>Tipo de actividad:</label>
            <select id="Eactividad" name="Eactividad" >
            <?php foreach($actividades as $idx => $Value){ ?>
                <option value="<?=$idx?>"><?=$Value?></option>
            <?php } ?>
            </select>
            <label>Relacionado con:</label>
            <select id="Econtacto" name="Econtacto" style="height:10px; width: 100%;">
            <?php foreach($ContactsData as $idx => $Value){ ?>
                <option value="<?=$idx?>"><?=$Value?></option>
            <?php } ?>
            </select>
        </p>
    </div>
    <div class="large-12 columns">
        <button id="Modal_EditEvent" class="button tiny">Guardar</button>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>
