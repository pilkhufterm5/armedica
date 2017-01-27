<?php
/**
 * @Todo
 * Forma para Crear un Contacto Rapido
 * @Author erasto@realhost.com.mx
 *
 * */
?>
<script type="text/javascript">
    $(document).on('ready',function() {

        $("#email").blur(function(){
            emailValido($(this).val())
        });

        $("#idProspecto").select2();
        //$('#idProspectox').autocomplete(options);

        $( "#Crear" ).click(function( event ) {
            event.preventDefault();
            if(!emailValido($('#email').val())){
                return;
            }
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("contactos/quickcontact"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      QuickContact:{
                          nombre: $('#nombre').val(),
                          apellidoPaterno: $('#apellidoPaterno').val(),
                          email: $('#email').val(),
                          idProspecto: $('#idProspecto').val(),
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        $('#myModal').foundation('reveal', 'close');
                        displayNotify('success', data.message);
                        $('#ContactsDTable').append(data.NewRow);
                        //$('#ContactsDTable').insertRow(data.NewRow);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>
<form name="contactos" id="contactos">
	<fieldset>
        <legend>Datos de contacto</legend>
		<div class="row">
            <div class="small-12 large-6 columns">
                <label>Nombre(s) <small>Requerido</small>
                <input type="text" placeholder="Ingrese Nombre" id="nombre" name="Contacto[nombre]" required/>
            </div>
            <div class="small-12 large-6 columns">
                <label>Apellido(s) <small>Requerido</small>
                <input type="text" placeholder="Ingrese Apellido(s)" id="apellidoPaterno" name="Contacto[apellidoPaterno]" required/>
            </div>
        </div>

        <div class="row">
            <div class="small-12 large-6 columns">
                <label>Correo <small>Requerido</small>
                <input type="text" placeholder="Ingrese Correo Electronico" id="email" name="Contacto[email]" required>
            </div>
            <div class="small-12 large-6 columns">
                <label>Prospecto <small>Requerido</small>
                <select id="idProspecto" name="Contacto[idProspecto]" >
                    <?php foreach ($LeadsList as $Leads) { ?>
                    <option value="<?=$Leads['idProspecto']?>"><?=$Leads['nombre']?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="large-12 columns">
            <input type="button" class="button tiny small" value="Crear" id="Crear"/>
            <?php //echo CHtml::submitButton($model->isNewRecord ? 'Crear' : 'Guardar',array('class'=>'button')); ?>
        </div>
    </fieldset>
</form>

