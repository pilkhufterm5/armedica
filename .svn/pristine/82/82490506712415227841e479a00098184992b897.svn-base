<script type="text/javascript">
    $(document).on('ready',function() {
        //$("#source").select2();
        
    });
</script>

<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("empresas/updateEmpresa"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Editar empresa</h3></div>
        <input type="hidden" name="id" value="<?=$Empresa['id']?>"/> 
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Empresa'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="empresa" name="empresa" value="<?=$Empresa['empresa']?>"/>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Folio'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                	<input type="text" id="folio" name="folio" value="<?=$Empresa['folio']?>"/>
                </div>
            </div>
        </div>
        
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" class="btn btn-small btn-success" value="Editar" style="margin-bottom: 0px;" />
            </div>
        </div>
	</div>        
</form>
</div>
