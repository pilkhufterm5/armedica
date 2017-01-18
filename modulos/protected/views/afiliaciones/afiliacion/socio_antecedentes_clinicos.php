
    <div class="span12">
        <label class="control-label" for="AntecedentesC">Seleccione Antecedentes Clinicos</label>
    </div>
    <div class="span8">
        <div class="controls">
            <select multiple name="antecedentes_clinicos[]" id="AntecedentesC">
                <?php foreach($LIstAntecedentesClinicos as $id => $Name){ ?>
                <option value="<?=$Name?>" <?=$Selected?> ><?=$Name?></option>
                <?php } ?>
                <!-- <option selected="" value="TEST">TEST</option> -->
            </select>
        </div>
        <div class="controls">
        <label>Otros Padecimientos</label>
        <!--<input type="text" name="antecedentes_clinicos[otros]" />--><!--Se comentarizo por Angeles Perez 2015-08-12-->
        <input type="text" name="otros_padecimientos" id="otros_padecimientos"/><!--Se agrego para mostrar lo que se ingresa en el campo Otros Padecimientos Angeles Perez 2016-08-12-->
        </div>
    </div>
    <div class="clearfix"></div>

    <script>
        $().ready(function(){
            $("#AntecedentesC").pickList();
        });
    </script>
    <div style="height: 50px"></div>
