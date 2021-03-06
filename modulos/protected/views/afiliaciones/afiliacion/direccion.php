<script type="text/javascript">
    $(document).on('ready',function() {
        $("#address7").select2();
        $("#address8").select2();
    });

    function IsNumeric(input){//valida que el dato ingresado sea numerico
        if((input - 0) == input && (''+input).replace(/^\s+|\s+$/g, "").length > 0){
        }else{
            alert('Solo se admiten numeros');
        }
    }
</script>
    <div class="container-fluid bootspin">

        <!--Spinners begin-->
        <div class="control-group row-fluid">
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Calle'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Calle"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address1" name="address1" class="required" style="width: 200px;" value="<?=$_POST['address1']?>" />
                </div>
            </div>
            <div class="span1">
                <label class="control-label" for="spin1">
                    <?php echo ('Número'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address2" name="address2" class="required" style="width: 200px;" value="<?=$_POST['address2']?>" />
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Codigo Postal'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Codigo Postal"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="address10" name="address10" class="required" style="width: 80px;" value="<?=$_POST['address10']?>" />
                </div>
            </div>

        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo ('Colonia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Colonia"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="address4" name="address4" class="required" value="<?=$_POST['address4']?>" />
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin2">
                    <?php echo ('Sector'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Sector"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <input type="text" id="address5" name="address5" class="required" value="<?=$_POST['address5']?>" />
                </div>
            </div>
        </div>


        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('Entre Calles'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Entre Calles"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span10">
                <div class="controls">
                    <input type="text" id="address6" name="address6" class="required" value="<?=$_POST['address6']?>" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('Cuadrante'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Cuadrante"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span10">
                <div class="controls">
                    <input type="text" id="cuadrante1" name="cuadrante1" class="required mayusculas" style="width: 80px;" value="<?=$_POST['cuadrante1']?>" onBlur="IsNumeric(this.value)" />
                    <input type="text" id="cuadrante2" name="cuadrante2" class="required mayusculas" style="width: 80px;" value="<?=$_POST['cuadrante2']?>"  />
                    <input type="text" id="cuadrante3" name="cuadrante3" class="required mayusculas" style="width: 80px;" value="<?=$_POST['cuadrante3']?>" onBlur="IsNumeric(this.value)" />
                    <script>
                        $(document).ready(function(){
                            $('#cuadrante2').mask('?a');
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label">
                    <?php echo ('Municipio'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Municipio"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="address7" name="address7" class="required" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaMunicipios as $id => $Municipio){ ?>
                            <option value="<?=$Municipio?>"><?=$Municipio?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#address7 option[value='<?=$_POST['address7']?>']").attr("selected",true);
                    </script>
                </div>
            </div>

            <div class="span2">
                <label class="control-label">
                    <?php echo ('Estado'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Estado"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span4">
                <div class="controls">
                    <select id="address8" name="address8" class="required" >
                        <option value="">SELECCIONE</option>
                        <?php foreach($ListaEstados as $id => $Estado){ ?>
                            <option value="<?=$Estado?>"><?=$Estado?></option>
                        <?php } ?>
                    </select>
                    <script type="text/javascript">
                        $("#address8 option[value='<?=$_POST['address8']?>']").attr("selected",true);
                    </script>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('N° Orden de Compra'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Orden de Compra"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="orderno" name="orderno" style="width: 120px;" value="<?=$_POST['orderno']?>" />
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('N° Referencia'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Referencia / Numero Interior"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="address3" name="address3" style="width: 120px;" value="<?=$_POST['address3']?>" />
                </div>
            </div>
            <div class="span2">
                <label class="control-label" for="spin3">
                    <?php echo _('N° Proveedor'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Numero de Proveedor"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="rh_numproveedor" name="rh_numproveedor" style="width: 120px;" value="<?=$_POST['rh_numproveedor']?>" />
                </div>
            </div>
        </div>
        <!--Se agrego el campo alfanumerico para la orden de compra Angeles Perez 2016/01/27 -->
 
            <div class="span2">
                <label class="control-label" for="spin3">
                <?php echo _('O.C. Texto'); ?><a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Orden de Compra Alfanumerica"><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span2">
                <div class="controls">
                    <input type="text" id="OC_Texto" name="OC_Texto" style="width: 120px;" value="<?=$_POST['OC_Texto']?>" />
                </div>
            </div>
<!-- termina -->

    </div>
