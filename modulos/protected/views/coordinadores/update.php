<script type="text/javascript">
    $(document).on('ready',function() {
        //$("#source").select2();
        
    });
</script>




<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("coordinadores/updateCoordinador/"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Editar coordinador</h3></div>
         <input type="hidden" name="id" value="<?=$Coordinador['id']?>"/>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('coordina_id'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" value="<?=$Coordinador['coordina_id']?>"  id="frecuencia" name="coordina_id" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('Coordinador'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" value="<?=$Coordinador['coordinador']?>"  id="coordinador" name="coordinador" />
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