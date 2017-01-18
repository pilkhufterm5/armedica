<script type="text/javascript">
    $(document).on('ready',function() {
        $("#plan_cotizado").select2();
        $("#forma_pago").select2();
        $("#frecuencia_pago").select2();
        $("#convenio_empresa").select2();
        $('#socios').mask("?99999");
    });
</script>

    <div class="container-fluid bootspin">
        
            <!--Spinners begin-->
            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label">
                        <?php echo ('Plan Cotizado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Plan Cotizado"><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span4">
                    <div class="controls">
                        <select id="plan_cotizado" name="plan_cotizado" >
                            <option value="">&nbsp;</option>
                            <option value="1">FAMILIA</option>
                            <option value="2">ZONA PROTEGIDA</option>
                            <option value="3">AUTO PROTEGIDO</option>
                        </select>
                    </div>
                </div>
                <div class="span2">
                    <label class="control-label" for="spin2">
                        <?php echo ('Socios'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Socios"><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span4">
                    <div class="controls">
                        <input type="text" id="socios" name="socios" style="width: 80px;" /> 
                    </div>
                </div>
            </div>
            
            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label" for="spin2">
                        <?php echo ('Forma de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Forma de Pago"><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span4">
                    <div class="controls">
                        <select id="forma_pago" name="forma_pago" >
                            <option value="">&nbsp;</option>
                            <?php foreach($ListaMetodosdePago as $id => $value){ ?>
                                <option value="<?=$id?>"><?=$value?></option>
                            <?php } ?>
                        </select> 
                    </div>
                </div>
                <div class="span2">
                    <label class="control-label" for="spin2">
                        <?php echo ('Frecuencia de Pago'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Forma de Pago"><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span4">
                    <div class="controls">
                        <select id="frecuencia_pago" name="frecuencia_pago" >
                            <option value="">&nbsp;</option>
                            <?php foreach($ListaFrecuenciapagos as $id => $value){ ?>
                                <option value="<?=$id?>"><?=$value?></option>
                            <?php } ?>
                        </select> 
                    </div>
                </div>
            </div>
            
            <div class="control-group row-fluid">
                <div class="span2">
                    <label class="control-label">
                        <?php echo ('Convenio Empresa'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Convenio Empresa"><i class="icon-photon info-circle"></i></a>
                    </label>
                </div>
                <div class="span10">
                    <div class="controls">
                        <select id="convenio_empresa" name="convenio_empresa" >
                            <option value="">&nbsp;</option>
                            <?php foreach($ListaConvenios as $id => $value){ ?>
                                <option value="<?=$id?>"><?=$value?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            
            
        <!--Spinners end-->
        <script>
            $('.spinner').spinner({
                min: 0,
                max: 10000
            });
        </script>
    </div><!-- end container -->
