<script type="text/javascript">
    $(document).on('ready',function() {

         $('#closed').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $( "#CrearOportunidad" ).click(function( event ) {
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('oportunidades/create'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    crearOportunidad:{
                        idProspecto: $('#idProspecto').val(),
                        nombre: $('#nombreOportunidad').val(),
                        created:$('#created').val(),
                        closed: $('#closed').val(),
                        monto: $('#monto').val(),
                        descripcion: $('#descOportunidad').val(),
                        probability: $('#probability').val(),
                        fase_venta: $('#fase_venta').val(),
                        debtorno: $('#debtorno').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#nombreOportunidad').val(''),
                        $('#closed').val(''),
                        $('#monto').val(''),
                        $('#descOportunidad').val(''),
                        $('#probability').val(''),
                        $('#fase_venta').val('')
                         $('#crearOportunidad').foundation('reveal', 'close');
                    }else{
                        displayNotify('error', data.message);
                    }
                },
                error : ajaxError
            });
        });

    });
</script>

<?php FB::INFO($ProspectoData, 'DATOS DEL PROSPECTO'); ?>
<form name="crearOportunidad" id="crearOportunidadd">

    <fieldset>
        <legend>Datos de la oportunidad</legend>
        <input type="hidden" id="idProspecto" name="idProspecto" value="<?=$ProspectoData['idProspecto']?>">
        <input type="hidden" id="debtorno" name="debtorno" value="<?=$ProspectoData['debtorno']?>">
        <input type="hidden" id="created" name="created" value="<?=date('Y-m-d H:i:s')?>">
        <div class="large-12 small-12 columns">
             <div class="row">
                <div class="large-4 small-12 columns">
                    <h5>* Nombre: <input type="text" id="nombreOportunidad" name="nombreOportunidad"></h5>
                </div>
                 <div class="large-4 small-12 columns">
                    <h5>* Monto $: <input type="text" id="monto" name="monto"></h5>
                </div>
                <div class="large-4 small-12 columns">
                    <h5>Fecha Esperada de Cierre : <input type="text" id="closed" name="closed"></h5>
                </div>
            </div>
            <div class="row">
                <div class="large-4 small-12 columns">
                    <h5>Probabilidad %: <input type="text" id="probability" name="probability"></h5>
                </div>
                <div class="large-4 small-12 columns">
                    <h5>Fase de Venta:
                        <select name="fase_venta" id="fase_venta">
                                <option value="">Seleccione una opción</option>
                            <?php foreach($Fases_Venta as $fase){ ?>
                                <option value="<?=$fase['id']?>"><?=$fase['nombre']?></option>
                            <?php } ?>
                        </select>
                    </h5>
                </div>
                <div class="large-4 small-12 columns">
                </div>
            </div>

            <div class="row">
                 <div class="large-12 columns">
                    <h5>Descripción : <textarea id="descOportunidad" name="descripcion"></textarea> </h5>
                </div>
            </div>

            <div class="large-12 columns">
                <input type="button" class="button tiny input" value="Crear Oportunidad" id="CrearOportunidad"/>
            </div>
        </div>
    </fieldset>
</form>

