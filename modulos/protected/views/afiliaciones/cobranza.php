<script type="text/javascript">
    $(document).on('ready', function() {
        var fecha_ingreso = '<?=$_POST['fecha_ingreso']?>';
        $('#SaveData').click(function() {
            $("#CobranzaForm").valid();
        });
        $("#cobrador").select2();


        <?php if($_POST['paymentid'] == 7 || $_POST['paymentid'] == 9 || $_POST['paymentid'] == 10) { ?>
            $('.TarjetaC').show();
        <?php } else { ?>
            $('.TarjetaC').hide();
        <?php } ?>


        $('#forma_pago').change(function(){
            if(($('#forma_pago').val() == 7) || ($('#forma_pago').val() == 9) || ($('#forma_pago').val() == 10)){
                $('.TarjetaC').show();
            }else{
                $('.TarjetaC').hide();
            }
        });

        $('#frecuencia_pago').change(function(){
            var frecuencia_pagoID = $('#frecuencia_pago').val();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/Getfcorte"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetFechaCorte:{
                          frecuencia_pago: frecuencia_pagoID,
                          fecha_ingreso: fecha_ingreso
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#fecha_corte').val(data.fecha_corte);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
        });

        $('#forma_pago').change(function(){
            var frecuencia_pagoID = $('#frecuencia_pago').val();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/Getfcorte"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                      GetFechaCorte:{
                          frecuencia_pago: frecuencia_pagoID,
                          fecha_ingreso: fecha_ingreso,
                          folio: $('#Folio').val()
                      },
                },
                success : function(data, newValue) {
                    if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#fecha_corte').val(data.fecha_corte);
                    }else{
                        displayNotify('alert', data.message);
                    }
                },
                error : ajaxError
            });
            VerificaPlan();
        });
    });


    function VerificaPlan(){
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("afiliaciones/VerificaPlan"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  Verifica:{
                      stockid: $("#Producto").val(),
                      frecuencia_pago: $("#frecuencia_pago").val(),
                      paymentid: $("#forma_pago").val(),
                      empresa: $("#empresa").val(),
                  },
            },
            success : function(req, newValue) {
                if (req.requestresult == 'ok') {
                    displayNotify('success', req.message);
                    $("#UpdateData").show();
                    $("#SaveData").show();
                    //$('#fecha_corte').val(data.fecha_corte);
                }else{
                    displayNotify('error', req.message);
                    $("#UpdateData").hide();
                    $("#SaveData").hide();
                }
            },
            error : ajaxError
        });
    }

</script>
<style>
    .select2-container {
        min-width: 180px; !important
    }
    input.mayusculas{
        text-transform:uppercase;
    }
    input[type="text"]{
        margin-bottom: -20px;
    }
    #Search{
        margin-bottom: -25px;
        height: 35px;
    }
</style>
<?php FB::INFO($_POST,'__________________________SSSSSS'); ?>
<div style="height: 10px;"></div>
<div class="container-fluid">
    <a href="<?php echo $this->createUrl("afiliaciones/afiliacion&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Titular" /></a>
    <?php if($TipoMembresia == "Socio"){ ?>
    <a href="<?php echo $this->createUrl("afiliaciones/socios&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Socios" /></a>
    <?php } ?>
</div>
<form class="form-horizontal" id="CobranzaForm" method="POST" action="<?php echo $this->createUrl("afiliaciones/cobranza/"); ?>" >
    <div class="container-fluid">
        <div class="form-legend">&nbsp;</div>
        <div class="control-group row-fluid">
            <div class="span12">
                <div class="controls">
                    <div class="span1">
                        <label class="control-label"><?php echo _('Folio'); ?></label>
                    </div>
                    <div class="span10">
                        <div class="controls">
                            <input type="text" id="Folio" name="Folio" value="<?=$_POST['Folio']?>" style="width: 100px;;" />
                            <input type="submit" id="Search" name="Search" class="btn btn-small" value="Buscar" />
                            <input type="hidden" name="debtorno" value="<?=$_POST['debtorno']?>" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 10px;"></div>
    <div id="Accordion" class="accordion">

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseTwo">
                    <?php echo _('Dirección'); ?>
                </a>
            </div>
            <div id="collapseTwo" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <?php include("afiliacion/cobranza_direccion.php"); ?>
                    </p>
                </div>
            </div>
        </div>

        <?php /*  Si es Tipo Cliente No se Muestra la Pestaña de Productos */
        if($TipoMembresia == "Socio"){ ?>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseThree">
                    <?php echo _('Producto'); ?>
                </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse ">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
                        <?php include("afiliacion/cobranza_productos.php"); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseFour">
                    <?php echo _('Pagos'); ?>
                </a>
            </div>
            <div id="collapseFour" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <?php include("afiliacion/cobranza_pagos.php"); ?>
                    </p>
                </div>
            </div>
        </div>

    </div>
    <div id="Savebtn" class="control-group row-fluid">
        <div class="span2">
            <?php if($_POST['Action'] =="UPDATE"){ ?>
                <input type="submit" id="UpdateData" name="UpdateData" class="btn btn-large btn-success" value="Actualizar" style="margin-bottom: 0px;" />
            <?php }else{ ?>
                <input type="submit" id="SaveData" name="SaveData" class="btn btn-large btn-success" value="Guardar y Continuar" style="margin-bottom: 0px;" />
            <?php } ?>
        </div>
        <div class="span2">
            <a href="<?php echo $this->createUrl("afiliaciones/cobranza"); ?>"><input type="button" id="_Limpiar" name="_Limpiar" class="btn btn-large btn-success" value="Limpiar" style="margin-bottom: 0px;" /></a>
        </div>
    </div>
</form>
