<script type="text/javascript">
    $(document).on('ready', function() {
        $("#dproducto").select2();
        $("#dformap").select2();
        $("#frecuencia").select2();
        $("#empresa").select2();
    });
</script>


<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("precios/createpcomis/"); ?>" >

    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Afiliados'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="afiliados" name="afiliados" required style="width: 50px;"  />
            </div>
        </div>
        <div class="span2">
            <label class="control-label" ><?php echo ('Producto'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <select id="dproducto" name="dproducto" required="required" >
                    <option value="" >SELECCIONE</option>
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
                <select id="dformap" name="dformap" required="required" >
                    <option value="" >SELECCIONE</option>
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
                <select id="frecuencia" name="frecuencia" required="required" >
                    <option value="" >SELECCIONE</option>
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
            <label class="control-label"><?php echo ('Tarifa Inscripción'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="tarifains" name="tarifains" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2">
            <label class="control-label"><?php echo ('Tarifa'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <!-- <input type="checkbox" id="tarifa" name="tarifa" value="1" /> -->
                <input type="text" id="tarifa" name="tarifa" required="required" style="width: 100px;" />
            </div>
        </div>
        <div class="span2"></div>
    </div>

    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Empresa'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <select id="empresa" name="empresa"  >
                    <option value="" >SELECCIONE</option>
                    <?php foreach($ListaEmpresas as $key => $Value){ ?>
                        <option value="<?=$key?>" ><?=$Value?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="span2">
            <label class="control-label"><?php echo ('Comision 1'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="comision1" name="comision1" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2"></div>
    </div>

    <div class="control-group row-fluid">
        <div class="span2">
            <label class="control-label"><?php echo ('Comision 2'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="comision2" name="comision2" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2">
            <label class="control-label"><?php echo ('Comision 3'); ?></label>
        </div>
        <div class="span3">
            <div class="controls">
                <input type="text" id="comision3" name="comision3" required style="width: 100px;" />
            </div>
        </div>
        <div class="span2"></div>
    </div>

    <div class="row-fluid span12">
        <input type="submit" class="btn btn-success btn-small" value="Agregar" />
    </div>

</form>
