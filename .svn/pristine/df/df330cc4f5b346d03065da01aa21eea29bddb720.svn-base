<script type="text/javascript">
    $(document).on('ready',function() {
        //$("#source").select2();
        
    });
</script>


<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("municipios/updateMunicipio/"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Editar municipio</h3></div>
         <input type="hidden" name="id" value="<?=$Municipio['id']?>"/>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Municipio'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" value="<?=$Municipio['municipio']?>"  id="municipio" name="municipio" />
                </div>
            </div>
        </div>
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" class="btn btn-small btn-success" value="Guardar" style="margin-bottom: 0px;" />
            </div>
        </div>
        
    </div>
</form>
</div>