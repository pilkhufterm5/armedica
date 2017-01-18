<script type="text/javascript">
        function GetCustomerBalance(DebtorNo,Folio){
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("afiliaciones/GetDetailBalance"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    GetBalance:{
                        DebtorNo: DebtorNo,
                        Folio: Folio
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        $('#ModalLabelGetBalance').text('Estado de Cuenta del Folio: ' + Response.Folio);
                        $('#BalanceTableContent').html(Response.BalanceTableContent);
                        $('#Modal_GetBalance').modal('show');
                        displayNotify('success', Response.message);
                    }else{
                        displayNotify('error', Response.message);
                    }
                },
                error : ajaxError
            });
        }

        function GetServiciosAcum(DebtorNo,Folio){
            $('#ModalLabelGetServiciosAcum').text('Detalle de Servicios del Folio: ' + Folio);
            $('#GSFolio').val(Folio);
            $('#Modal_GetServiciosAcum').modal('show');
        }

</script>
<style>
    .BtnDetail{
        border-radius: 0 4px 4px 0;
        width: 100px;
        height: 35px;
        margin-bottom: -6px;
        margin-left: -6px;
    }
</style>
<?php include_once("modals/GetBalance.modal.php"); ?>
<?php include_once("modals/GetServiciosAcum.modal.php"); ?>
    <div class="row-fluid">
        <div class="span4">
            <fieldset>
                <legend>
                    Servicios Impartidos
                </legend>

                <div class="span12">
                    <label class="control-label">Servicios Acumulados </label>
                    <div class="controls">
                        
                        <button id="GetServAcum" class="btn btn-info BtnDetail" type="button" onclick="GetServiciosAcum(<?=$_POST['debtorno']?>,<?=$_POST['Folio']?>)" >Detalle</button>
                    </div>
                </div>
                <div class="span12">
                    <label class="control-label">N° Serv. Mes </label>
                    <div class="controls">
                        <input type="text" id="servicios_mes" name="servicios_mes" readonly="readonly" value="<?=$_POST['servicios_mes']?>" style="width: 100px;" />
                    </div>
                </div>
            </fieldset>
        </div>

        <?php if($TipoMembresia == "Socio"){ ?>
        <div class="span4">
            <fieldset>
                <legend>
                    Costos
                </legend>
                <div class="span12">
                    <label class="control-label">Costos Nuevos Socios</label>
                    <div class="controls">
                        <input type="text" id="costos_nuevos_socios" name="costos_nuevos_socios" readonly="readonly" value="<?=number_format($_POST['costos_nuevos_socios'],2)?>" style="width: 100px;" />
                    </div>
                </div>
                <div class="span12">
                <!-- MODIFICADO POR DANIEL VILLARREAL EL 21 DE ABRIL DEL 2016, PARA HABILITAR LA MODIFICACION DEL COSTO TOTAL. -->
                    <label class="control-label">Costo Total</label>
                    <div class="controls">
                        <input type="text" id="costo_total" name="costo_total"  value="<?=number_format($_POST['costo_total'],2)?>" style="width: 100px;" <?=($or_costo_afil!=1)?'readonly="true"':'';?> />
                    </div>
                </div>
            </fieldset>
        </div>
        <?php } ?>

        <div class="span4">
            <fieldset>
                <legend>
                    Estado de Cuenta
                </legend>
                <div class="span12">
                    <label class="control-label" for="spin1">Facturas Vencidas</label>
                    <div class="controls">
                        <input type="text" id="facturas_vencidas" name="facturas_vencidas" readonly="readonly" value="<?=number_format($_POST['facturas_vencidas'],2)?>" style="width: 100px;" />
                    </div>
                </div>
                <div class="span12">
                    <label class="control-label" for="spin1">Balance</label>
                    <div class="controls">
                        <input type="text" readonly="readonly" id="Balance" name="Balance" value="<?=number_format($_POST['Balance'],2)?>" style="width: 100px;" />
                        <button id="GetBalance" class="btn btn-info BtnDetail" type="button" onclick="GetCustomerBalance(<?=$_POST['debtorno']?>,<?=$_POST['Folio']?>)" >Detalle</button>
                    </div>
                    <div class="input-append">
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
