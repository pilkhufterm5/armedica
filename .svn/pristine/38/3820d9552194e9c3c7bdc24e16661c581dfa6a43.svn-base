<script type="text/javascript">
    $(document).on('ready', function() {
        <?php $_2COSTO = $this->ActualizaCosto($_POST['Folio'], 'NuevosSocios');
        if($_2COSTO['CostoTotal'] >0){ ?>
        $('#SociosAgregados').show();
        <?php }else{ ?>
            $('#SociosAgregados').hide();
        <?php } ?>
        $('#SaveData').click(function() {
            $("#SociosForm").valid();
        });

        $('#Emergencia_Contacto').click(function(){
            $('#Modal_Contacto_Emergencia').modal('show');
        });
    });

    function ConfirmSaveNSocios(DebtorNo,Folio){
        $('#Modal_FacturaNuevos').modal('show');
        TotalFacturar = $('#TotalFacturar').val();
        $("#FNmonto_recibido").val(TotalFacturar);
        //$("#FNtarifa_total").val(TotalFacturar);
    }

    function SaveNSocios(DebtorNo,Folio){
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl("afiliaciones/CreaFacturaNuevosSocios"); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                  GetInvoice:{
                      DebtorNo: DebtorNo,
                      Folio: Folio,
                      TotalInvoice: $('#FNmonto_recibido').val(),
                      TotalAllSocios: $('#FNtarifa_total').val(),
                      Tipo: $('#FN_TipoFactura').val()
                  },
            },
            success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                    displayNotify('success', data.message);
                    $('#SociosAgregados').show();
                    $('#SociosAgregados').text(data.message2);
                    $('#btnSaveNews').html(data.btnSaveNews);
                    $('#SociosTable #' + data.BranchCode).html(data.NewRow);
                    $('#SaveNews').hide();
                }else{
                    displayNotify('alert', data.message);
                }
            },
            error : ajaxError
        });
    }


</script>
<style type="text/css">
    input.mayusculas{
        text-transform:uppercase;
    }
</style>
<?php
if(($_2COSTO['CostoTotal'] >0) && $_2COSTO['CostoTodosSocios'] >0){
    $SaveAndGen = "<input type='button' id='SaveNews' value='Guardar y Generar Factura' class='btn-success' onclick='ConfirmSaveNSocios({$_POST['debtorno']},{$_POST['Folio']})' style='margin-top: -35px;' >";
    $Badge = "Costo total: " . number_format($_2COSTO['CostoTotal'], 2) . " N° Socios Activos: " . $_2COSTO['QtyNS'];
    if(!empty($_2COSTO['CostoInscripcion'])){
        $CostoTotalPlan = $_2COSTO['CostoTodosSocios'] + $_2COSTO['CostoInscripcion'];
    }else{
        $CostoTotalPlan = $_2COSTO['CostoTodosSocios'];
    }
    $CostoTodosSocios = number_format($CostoTotalPlan, 2);
}
FB::INFO($_2COSTO,'________________GET COST NEW 22222222ss');
FB::INFO($CostoTodosSocios,'_____________________________$CostoTodosSocios');
 ?>

<div id="Modal_FacturaNuevos" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 600px;" >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="ModalLabelReactivacion">Generar Factura para Folio <?=$_POST['Folio']?></h3>
    </div>
    <div class="modal-body" style="height: 200px;">
        <p>
            <div style="height: 20px;"></div>
            <input type="hidden" id="FN_BranchCode" />
            <input type="hidden" id="FN_DebtorNo" />
            <input type="hidden" id="FN_TipoFactura" />
            <label>Tarifas a Registrar.</label>
            <div style="height: 20px;"></div>
            <label>Confirme el Monto Total Recibido.</label>
            <div style="height: 20px;"></div>

            <?php if($_2COSTO['DiasGracia'] == 1 ){ ?>
                <div class="noty_bar noty_theme_simpla noty_layout_topRight noty_success" id="noty_error_1405719528188" style="cursor: pointer; display: block;">
                    <div class="noty_message">
                        <span class="noty_text">No se Generara Factura para este Afiliado</span>
                    </div>
                </div>
            <?php } ?>

            <div class="row _span12">
                <div class="span6 controls">
                    <label class="control-label">Monto Recibido: </label>
                    <input type="text" id="FNmonto_recibido" class="span6" />
                </div>
                <div class="span6 controls">
                    <label class="control-label"> Tarifa Total: </label>
                    <input type="text" id="FNtarifa_total" value="<?=$CostoTodosSocios?>" class="span6" />
                </div>
            </div>
        </p>
    </div>
    <div class="modal-footer">
        <button id="Close-Modal-ReactivarSocio" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Modal_GenFactura" name="Send_Modal_GenFactura" onclick="SaveNSocios(<?=$_POST['debtorno']?>,<?=$_POST['Folio']?>)" class="btn btn-success" data-dismiss="modal" >Generar</button>
    </div>
</div>


<?php FB::INFO($_POST,'___________POST_VIEW'); ?>
<div style="height: 10px;"></div>
<div class="container-fluid">
    <a href="<?php echo $this->createUrl("afiliaciones/afiliacion&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Titular" /></a>
    <a href="<?php echo $this->createUrl("afiliaciones/cobranza&Folio=" . $_POST['Folio']); ?>" ><input type="button" class="btn btn-small" value="Cobranza" /></a>
</div>
<form id="SociosForm" class="form-horizontal" method="POST" action="<?php echo $this->createUrl("afiliaciones/socios/"); ?>" >
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="height: 10px;"></div>
    <div id="Accordion" class="accordion">

        <div class="accordion-group">
            <input type="hidden" id="TotalFacturar" value="<?=number_format($_2COSTO['CostoTotal'], 2)?>">
            <div id="btnSaveNews"><?=$SaveAndGen?></div>

            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseThree">
                    <?php echo _('Socios'); ?> &nbsp; &nbsp;&nbsp;&nbsp;
                    <span id='SociosAgregados' class='badge badge-success'><?=$Badge?></span>
                </a>
            </div>

            <div id="collapseThree" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;">
                    <p>
                        <?php include("afiliacion/socio_lista.php"); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="form-legend">Agregar Socio</div>
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#Accordion" href="#collapseOne">
                    <?php echo _('Datos del Socio'); ?>
                </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse in">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                        <?php include("afiliacion/socio_contact_information.php"); ?>
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
            <div id="collapseTwo" class="accordion-body collapse">
                <div class="accordion-inner" style="padding: 0px;" >
                    <p>
                       <?php include("afiliacion/socio_antecedentes_clinicos.php"); ?>
                    </p>
                </div>
            </div>
        </div>

    </div>
    <div id="Savebtn" class="control-group row-fluid">
        <div class="span3">
            <?php if($_POST['movimientos_afiliacion'] == "Activo"){ ?>
                <input type="submit" id="SaveData" name="SaveData" class="btn btn-large btn-success" value="Guardar" style="margin-bottom: 0px;" />
            <?php } ?>
        </div>
    </div>
</form>
