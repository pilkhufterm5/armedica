<?php
/**
 * @Todo
 * Forma para Crear un Contacto Rapido
 * @Author erasto@realhost.com.mx
 *
 * */
?>

<script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/foundation.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/jquery.autocomplete.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/content/styles.css">

<script type="text/javascript">
    $(document).on('ready',function() {

        $( "#Crear" ).click(function( event ) {
            if(!emailValido($('#email').val())){
                return;
            }
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("leads/quicklead"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      QuickLead:{
                          nombre: $('#nombre').val(),
                          estatus: $('#estatus').val(),
                          apellidoPaterno: $('#apellidoPaterno').val(),
                          email: $('#email').val(),
                          status_prospecto: $('#status_prospecto').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#myModal').foundation('reveal', 'close');
                        $('#LeadsDTable').append(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>


	<fieldset>
        <legend>Datos del prospecto</legend>
        <input type="checkbox" value="1" style="display:none" checked="checked" id="estatus">
		<div class="row">
            <div class="small-12 large-6 columns">
                <label>Nombre(s) <small>Requerido</small></label>
                <input type="text" placeholder="Ingrese Nombre" id="nombre" required/>
            </div>
            <div class="small-12 large-6 columns">
                <label>Apellido(s) <small>Requerido</small></label>
                <input type="text" placeholder="Ingrese Apellido(s)" id="apellidoPaterno" required/>
            </div>
        </div>

        <div class="row">
            <div class="small-12 large-6 columns">
                <label>Correo <small>Requerido</small></label>
                <input type="text" placeholder="Ingrese Correo Electronico" id="email" >
            </div>
             <div class="small-12 large-6 columns">
                <label>Estatus: <small>Requerido</small></label>
                <select name="status_prospecto" id="status_prospecto">
                    <option value="ACTIVO">ACTIVO</option>
                    <option value="CERRADO GANADO">CERRADO GANADO</option>
                    <option value="CERRADO CANCELADO">CERRADO CANCELADO</option>
                    <option value="CERRADO PERDIDO">CERRADO PERDIDO</option>
                </select>
            </div>
        </div>
        <div class="large-12 columns">
            <input type="button" class="button tiny small" value="Crear" id="Crear"/>
            <?php //echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar',array('class'=>'button')); ?>
        </div>
    </fieldset>



