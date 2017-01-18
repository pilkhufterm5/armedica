<script type="text/javascript">
    $(document).on('ready', function() {
        
        $("#stockid").select2();
        $("#paymentid").select2();
        $("#frecpagoid").select2();
        
    });
</script>
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("precios/create/"); ?>" >

    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Afiliados'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="nafiliados" name="nafiliados" required style="width: 50px;"  />
            </div>
        </div>
        <div class="span2">
            <label class="control-label" ><?php echo ('Producto'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <select id="stockid" name="stockid" required="required" >
                    <?php foreach($ListaPlanes as $key => $Value){ ?>
                        <option value="<?=$key?>" ><?=$Value?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="span2"></div>
    </div>
        
    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label" for="spin1"><?php echo ('Metodo de Pago'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <select id="paymentid" name="paymentid" required="required" >
                    <?php foreach($ListaFormasPago as $key => $Value){ ?>
                        <option value="<?=$key?>" ><?=$Value?></option>
                    <?php } ?>
                </select>
                
            </div>
        </div>
        <div class="span2">
            <label class="control-label" ><?php echo ('Frecuencia de Pago'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <select id="frecpagoid" name="frecpagoid" required="required" >
                    <?php foreach($ListaFrecuenciaPago as $key => $Value){ ?>
                        <option value="<?=$key?>" ><?=$Value?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="span2"></div>
    </div>
        
        
    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Porcentage de Descuento'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="porcdesc" name="porcdesc" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2">
            <label class="control-label"><?php echo ('Aplica Descuento'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="checkbox" id="aplicadesc" name="aplicadesc" value="1" />
            </div>
        </div>
        <div class="span2"></div>
    </div>
    
    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Costo1'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="costouno" name="costouno" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2">
            <label class="control-label"><?php echo ('Costo2'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="costodos" name="costodos" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2"></div>
    </div>
    
    
    <div class="row-fluid span12">
        <input type="submit" class="btn btn-success btn-small" value="Agregar" />
    </div>
        
</form>
