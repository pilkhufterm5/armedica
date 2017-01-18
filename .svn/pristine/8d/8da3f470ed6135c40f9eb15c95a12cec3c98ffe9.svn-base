<?php
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/fullcalendar.print.css');
Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . "/css/fullcalendar.css" );
?>

<script>


    $(document).on('ready',function() {
        
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        
        $('#NewActivitie').click(function() {
            $('#AddActivitie').modal('show');
        });
        
        $('#create-modal-AddEvent').click(function() {
            //Save New Activitie
            var title = $('#titulo').val();
            var start = $('#startdate').val();
            var end = $('#enddate').val();
            
            if (title) {
                calendar.fullCalendar('renderEvent',
                    {
                        title: title,
                        start: start,
                        end: end,
                    },
                    true // make the event "stick"
                );
            }
            calendar.fullCalendar('unselect');
            $('#titulo').val("");
            $('#startdate').val("");
            $('#enddate').val("");
            $('#AddActivitie').modal('toggle');
            
        });
        
        var calendar = $('#calendar').fullCalendar({
            
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            
            eventClick: function(event, element) {
                //alert(event.id);
                $('#EditActivitie').modal('show');
                var title = $('#ETitulo').val(event.title);
                var start = $('#Estartdate').val(event.start);
                var end = $('#Eenddate').val(event.end);
                
                $('#calendar').fullCalendar('updateEvent', event);
            },
            
            selectable: true,
            selectHelper: true,
            
            select: function(start, end, allDay) {
                $('#AddActivitie').modal('show');
                
                var title = $('#titulo').val();
                var end = $('#enddate').val(start);
                var start = $('#startdate').val(start);
                
            },
            
            editable: true,
            
            eventDrop: function(event,dayDelta,minuteDelta,allDay,revertFunc) {
                alert(
                    event.title + " fue Movido " +
                    dayDelta + " dias y " +
                    minuteDelta + " minutos."
                );
                if (allDay) {
                    alert("Event is now all-day");
                }else{
                    alert("Event has a time-of-day");
                }
                
                if (!confirm("Are you sure about this change?")) {
                    revertFunc();
                }
            },
            eventSources: [
                {
                    url: '<?php echo $this->createUrl("activities/loadactivities"); ?>',
                }
            ],
            
        });
        
        $('#edit-modal-EditEvent').click(function() {
            var title = $('#ETitulo').val();
            var start = $('#Estartdate').val();
            var end = $('#Eenddate').val();
            
            if (title) {
                calendar.fullCalendar('renderEvent',
                    {
                        title: event.title,
                        start: event.start,
                        end: event.end,
                    },
                    true // make the event "stick"
                );
            }
            calendar.fullCalendar('unselect');
            $('#EditActivitie').modal('toggle');
        });
        
    });
</script>

<style>

    body {
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


<input type="button" id="NewActivitie" value="Agregar Actividad" />

<div id='calendar'></div>

<div id="AddActivitie" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Agregar Actividad</h3>
    </div>
    <div class="modal-body">
        <p style="text-align: left;">
        <label>Evento </label>
        <input type="text" id="titulo" name="titulo"  />
        <label>Inicia </label>
        <input type="date" id="startdate" name="startdate" value="2013-11-12" />
        <label>Termina </label>
        <input type="date" id="enddate" name="enddate" value="2013-11-15" />
        </p>
    </div>
    <div class="modal-footer">
        <button id="close-modal-AddEvent" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button> <button id="create-modal-AddEvent" class="btn btn-success">Agregar</button>
    </div>
</div>

<div id="EditActivitie" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Editar Actividad</h3>
    </div>
    <div class="modal-body">
        <p style="text-align: left;">
        <label>Evento </label>
        <input type="text" id="ETitulo" name="ETitulo"  />
        <label>Inicia </label>
        <input type="date" id="Estartdate" name="Estartdate" value="2013-11-12" />
        <label>Termina </label>
        <input type="date" id="Eenddate" name="Eenddate" value="2013-11-15" />
        </p>
    </div>
    <div class="modal-footer">
        <button id="close-modal-EditEvent" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button> <button id="edit-modal-EditEvent" class="btn btn-success">Agregar</button>
    </div>
</div>


