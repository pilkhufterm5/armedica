<script type="text/javascript">
    $(document).on('ready',function() {
        //$("#source").select2();
        
    });
</script>


<div style="height: 20px;"></div>
<div style="width: 80%; margin-left:10%;">
<form class="form-horizontal" method="POST" action="<?php echo $this->createUrl("paymentmethod/updatemetodo"); ?>" >
    <div class="container-fluid">
        <div class="form-legend"><h3>Editar método de pago</h3></div>
        <input type="hidden" name="paymentid" value="<?=$Paymentmethod['paymentid']?>"/> 
        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Método de pago'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="paymentname" name="paymentname" value="<?=$Paymentmethod['paymentname']?>"/>
                </div>
            </div>
        </div>
<!--Se agrego satname para la descripción que requiere el SAT Angeles Perez 2016-07-12-->        
            <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Método pago SAT'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="satname" name="satname" value="<?=$Paymentmethod['satname']?>"/>
                </div>
            </div>
<!--Termina-->
<!--Se agrego satid para el identificador que requiere el SAT Angeles Perez 2016-07-12-->
            <div class="span2">
                <label class="control-label"><?php echo _('identificador SAT'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="satid" name="satid" value="<?=$Paymentmethod['satid']?>"/>
                </div>
            </div>
        </div>
<!--Termina-->

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label"><?php echo _('Utilizar para pagos'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <?php if($Paymentmethod['paymenttype']){ ?>
                        <input type="checkbox" id="paymenttype" name="paymenttype" checked />
                    <?php } else {?>
                        <input type="checkbox" id="paymenttype" name="paymenttype"/>
                     <?php }?>
                </div>
            </div>

            <div class="span2">
                <label class="control-label"><?php echo _('Utilizar para recibos'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <?php if($Paymentmethod['receipttype']=='1'){ ?>
                        <input type="checkbox" id="receipttype" name="receipttype" checked/>
                    <?php } else {?>
                        <input type="checkbox" id="receipttype" name="receipttype"/>
                    <?php }?>
                </div>
            </div>
        </div>
        
       
        
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="Save" class="btn btn-large" value="Guardar" style="margin-bottom: 0px;" />
            </div>
        </div>
        
</form>
</div>