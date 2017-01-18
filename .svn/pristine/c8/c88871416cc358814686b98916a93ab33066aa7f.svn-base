<script type="text/javascript">
    $(document).on('ready',function() {
        //$("#source").select2();
        
    });
</script>


<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("cobradores/update2/"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Editar cobrador</h3></div>
        <input type="hidden" name="id" value="<?=$Cobrador['id']?>"/>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Nombre'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="nombre" name="nombre" value="<?=$Cobrador['nombre']?>" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('Comisión'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="comision" name="comision" value="<?=$Cobrador['comision']?>" />
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Zona'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="zona" name="zona" value="<?=$Cobrador['zona']?>" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('Activo'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="checkbox" id="activo" name="activo" <?php if($Cobrador['activo']==1){echo 'checked'; } ?> />
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Reasigna'); ?></label>
            </div>
            
            <div class="span3">
                <div class="controls">
                    <input type="text" id="reasigna" name="reasigna" value="<?=$Cobrador['reasigna']?>" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('transfe'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="checkbox" id="transfe" name="transfe" <?php if($Cobrador['transfe']==1){echo 'checked'; } ?> />
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('cobori'); ?></label>
            </div>
            
            <div class="span3">
                <div class="controls">
                    <input type="text" id="cobori" name="cobori" value="<?=$Cobrador['cobori']?>" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('Empresa'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="checkbox" id="empresa" name="empresa" <?php if ($Cobrador['empresa']==1){echo 'checked'; } ?>/>
                </div>
            </div>
        </div>
        
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" class=" btn btn-small btn-success" value="Guardar" style="margin-bottom: 0px;" />
            </div>
        </div>
        
    </div>
</form>
</div>  