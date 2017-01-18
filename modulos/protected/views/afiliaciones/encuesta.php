<script type="text/javascript">
    $(document).on('ready',function() {
        $("#P5pq").select2();
    });
</script>


<div style="height: 20px;"></div>


<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("afiliaciones/encuesta/"); ?>" >
    <div class="container-fluid">
        
        <div class="form-legend">&nbsp;</div>
        <div class="control-group row-fluid">
            <div class="span12">
                <div class="controls">
                    
                    <div class="span4">
                        <label class="control-label"><?php echo _('Folio: '); ?></label>
                        <div class="controls">
                            <input type="text" id="Folio" name="Folio" value="<?=$Folio?>" class="span4" />
                            <input type="submit" id="Search" name="Search" value="Buscar" class="btn btn-success btn-small" style="height: 28px; margin-top: 5px;" />
                        </div>
                    </div>
                    
                    <div class="span4">
                        <label class="control-label"><?php echo _('Asesor'); ?></label>
                        <div class="controls" >
                            <input type="hidden" id="Asesor_ID" name="Asesor_ID"value="<?=$Asesor?>"  />
                            <input id="_Asesor_ID" name="_Asesor_ID" readonly="readonly" value="<?=$ListAsesores[$Asesor]?>" class="span6" />
                        </div>
                    </div>
                    <div class="span4" ></div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php

    if(isset($Search)){
        if($Search[0]['p1'] =='SI'){
            $RP1S = " checked='checked' ";
            $RP1N = "";
        }else{
            if($Search[0]['p1'] =='NO'){
                $RP1N = " checked='checked' ";
            }
            $RP1S = "";
        }
        
        if($Search[0]['p2'] =='SI'){
            $RP2S = " checked='checked' ";
            $RP2N = "";
        }else{
            if($Search[0]['p2'] =='NO'){
                $RP2N = " checked='checked' ";
            }
            $RP2S = "";
        }
        
        if($Search[0]['p3'] =='SI'){
            $RP3S = " checked='checked' ";
            $RP3N = "";
        }else{
            if($Search[0]['p3'] =='NO'){
                $RP3N = " checked='checked' ";
            }
            $RP3S = "";
        }
        
        if($Search[0]['p4'] =='SI'){
            $RP4S = " checked='checked' ";
            $RP4N = "";
        }else{
            if($Search[0]['p4'] =='NO'){
                $RP4N = " checked='checked' ";
            }
            $RP4S = "";
        }
        
        if($Search[0]['p5'] =='BUENA'){
            $RP5B = " checked='checked' ";
            $RP5M = "";
        }else{
            if($Search[0]['p5'] =='MALA'){
                $RP5M = " checked='checked' ";
            }
            $RP5B = "";
        }
        
        if(!empty($Search[0]['p5pq'])){
            $R5pq = "<option selected = selected >" . $Search[0]['p5pq'] . "</option>";
        }else{
            $R5pq = "";
        }
        
        if(!empty($Search[0]['p5otro'])){
            $R5otro = $Search[0]['p5otro'];
        }else{
            $R5otro = "";
        }
        
        if($Search[0]['p6'] =='SI'){
            $RP6S = " checked='checked' ";
            $RP6N = "";
        }else{
            if($Search[0]['p6'] =='NO'){
                $RP6N = " checked='checked' ";
            }
            $RP6S = "";
        }
// Se agrego Angeles Perez 2016-08-31
        if(!empty($Search[0]['encuestado'])){
            $Rencuestado= $Search[0]['encuestado'];
        }else{
            $Rencuestado = "";
        }

        if(!empty($Search[0]['fecha'])){
            $Rfecha= $Search[0]['fecha'];
        }else{
            $Rfecha = "";
        }    
// Termina      
        
    }

?>

<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("afiliaciones/encuesta/"); ?>" >
    
    <input type="hidden" id="Folio" name="Folio" value="<?=$Folio?>" />
    <input type="hidden" id="Asesor" name="Asesor" value="<?=$Asesor?>" />
    
    <div class="container-fluid">
        <div class="form-legend"><h3>Captura de Encuesta</h3></div>
        
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('1.- ¿Considera que la información proporcionada por nuestro Asesor Comercial fue clara y precisa?'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            
                            <input type="radio" <?=$RP1S?> id="P1" name="P1" value="SI" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('SI'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP1N?> id="P1" name="P1" value="NO" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('NO'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('2.- ¿Conoce los servicios que incluye su membresía? NOTA: Verificar servicios incluídos en su membresía'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP2S?> id="P2" name="P2" value="SI" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('SI'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP2N?> id="P2" name="P2" value="NO" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('NO'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('3.- ¿Le Explicó nuestro Asesor Comercial sobre el procedimiento para solicitar un servicio de atención médica? NOTA: Que números marcar, etc.'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP3S?> id="P3" name="P3" value="SI" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('SI'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP3N?> id="P3" name="P3" value="NO" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('NO'); ?></label>
                    </div>
                    
                </div>
            </div>
            
        </div>
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('4.- ¿Le mencionó nuestro Asesor Comercial los tiempos de respuesta y la manera en que se determina la prioridad del servicio?'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP4S?> id="P4" name="P4" value="SI" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('SI'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP4N?> id="P4" name="P4" value="NO" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('NO'); ?></label>
                    </div>
                    
                </div>
            </div>
            
        </div>
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('5.- En General, ¿Cómo califica la atención proporcionada por el Asesor Comercial?'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP5B?> id="P5" name="P5" value="BUENA" />
                        </div>
                    </div>
                    <div class="span1">
                        <label class="control-label"><?php echo _('BUENA'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP5M?> id="P5" name="P5" value="MALA" />
                        </div>
                    </div>
                    <div class="span1">
                        <label class="control-label"><?php echo _('MALA'); ?></label>
                    </div>
                    
                    <div class="span1">
                        <label class="control-label"><?php echo _('¿Porque?'); ?></label>
                    </div>
                    <div class="span3" >
                        <div class="controls">
                            <select id="P5pq" name="P5pq" >
                                <option value="">&nbsp;</option>
                                <?=$R5pq?>
                                <option >Tardaba mucho</option>
                            </select>
                        </div>
                    </div>
                    <div class="span1">
                        <label class="control-label"><?php echo _('Otro'); ?></label>
                    </div>
                    <div class="span2" style="text-align: right;">
                        <div class="controls">
                            <input type="text" id="P5otro" name="P5otro" value="<?=$R5otro?>" />
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
        
        <div class="control-group row-fluid">
            <div class="span12">
                <label class="control-label"><?php echo _('6.- ¿Recibió su paquete de afiliación completo? NOTA: Verificar contenido de kit correspondiente al plan adquirido.'); ?></label>
            </div>
            <div class="span12">
                <div class="controls">
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP6S?> id="P6" name="P6" value="SI" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('SI'); ?></label>
                    </div>
                    
                    <div class="span1" style="text-align: right;">
                        <div class="controls">
                            <input type="radio" <?=$RP6N?> id="P6" name="P6" value="NO" />
                        </div>
                    </div>
                    <div class="span5">
                        <label class="control-label"><?php echo _('NO'); ?></label>
                    </div>
                </div>
            </div>
        </div>
<!--Se agrego por Angeles Perez 2016-08-31 -->       
        <div class="control-group row-fluid">
                    <div class="span1">
                        <label class="control-label"><?php echo _('Nombre Encuestado:'); ?></label>
                    </div>
                    <div class="span2" style="text-align: right;">
                        <div class="controls">
                            <input type="text" id="ENCUESTADO" name="ENCUESTADO" value="<?=$Rencuestado?>" />
                        </div>
                    </div>
                    <div class="span1">
                        <label class="control-label"><?php echo _('Fecha'); ?></label>
                    </div>
                    <div class="span2" style="text-align: right;">
                        <div class="controls">
                            <input type="date" id="FECHA" name="FECHA" value="<?=$Rfecha?>" />
                        </div>
                    </div>
                </div>
<!-- Termina -->      
        
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" name="Save" class="btn btn-large btn-success" value="Guardar" style="margin-bottom: 0px;" />
            </div>
        </div>
        
    </div>
</form>