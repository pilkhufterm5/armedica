<script type="text/javascript">
    $(document).on('ready',function() {
        $("#coordina_id").select2();
        
    });
</script>


<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("comisionista/updateComisionista"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Agregar Comisionista</h3></div>
          <input type="hidden" name="id" value="<?=$Comisionista['id']?>"/>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('comisionista'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" value="<?=$Comisionista['comisionista']?>" id="comisionista" name="comisionista" />
                </div>
            </div>
            
            <div class="span2">
                <label class="control-label"><?php echo _('Coordinador'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    
                    <select id="coordina_id" name="coordina_id" >
                        <?php 
                            echo "<option>Seleccione una opcion</option>";
                            $sselected = false;
                            foreach ($CooordinadoresData as $Coordinador) {
                                echo "<option value='".$Coordinador['coordina_id']."'";
                                if($Comisionista['coordina_id']==$Coordinador['coordina_id'] && !$selected){
                                    echo "selected";
                                    $selected=true;
                                }
                                echo ">".$Coordinador['coordinador']."</option>";
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Estado'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="checkbox" id="activo" name="activo" <?php if($Comisionista['activo']) echo "checked='checked'";?> />
                </div>
            </div>
        </div>
         <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" name="Save" class="btn btn-small btn-success" value="Guardar" style="margin-bottom: 0px;" />
            </div>
        </div>

    </div>
        
</form>
</div>