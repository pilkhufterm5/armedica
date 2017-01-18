
<div id="Modal_Info_Empresa" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"  >
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Información de Empresa</h3>
    </div>
    <div class="modal-body" _style="height: 200px;">
        <p>
            <div style="height: 20px;"></div>
            <input type="hidden" value="<?=$_POST['Folio']?>" name="Info_Empresa[folio]">
            <div class="control-group">
<!--Se agrego para mostrar las opciones de clasificaciones de la empresa a insertar en la tabla rh_info_empresa  estas se toman del catalogo creado para este fin Angeles Perez 2016-06-16-->
                 <div class="span6">
                    <label class="control-label" >Clasificación:</label>
                    <div class="controls">
                        <select id="clasificacion" name="Info_Empresa[clasificacion]" >
                    <option value="">SELECCIONE</option>
                    <?php foreach($ListaClasificacion as $id => $Name){ ?>
                        <option value="<?=$id?>"><?=$Name?></option>
                    <?php } ?>
                </select>
                        <script type="text/javascript">
                            $("#clasificacion option[value='<?=$_POST['Info_Empresa']['clasificacion']?>']").attr("selected",true);
                        </script>
                    </div>
                </div>
             </div>
 <!--Termina-->

            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >Medico de Planta:</label>
                    <div class="controls">
                        <select name="Info_Empresa[medico_planta]" value="<?=$_POST['Info_Empresa']['medico_planta']?>" >
                            <option value="1">SI</option>
                            <option value="0">NO</option>
                        </select>
                    </div>
                </div>
                <div class="span6">
                    <label class="control-label" >Visitantes:</label>
                    <div class="controls">
                        <input type="text" name="Info_Empresa[visitantes]" value="<?=$_POST['Info_Empresa']['visitantes']?>" >
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >N° Empleados:</label>
                    <div class="controls">
                        <input type="number" name="Info_Empresa[nempleados]"  min="0" value="<?=$_POST['Info_Empresa']['nempleados']?>" >
                    </div>
                </div>
                <div class="span6">
                    <label class="control-label" >Turnos:</label>
                    <div class="controls">
                        <select id="turnos" name="Info_Empresa[turnos]" >
                            <option></option>
                            <option value="0"></option>
                            <option value="1">MATUTINO</option>
                            <option value="2">NOCTURNO</option>
                            <option value="3">VESPERTINO</option>
                        </select>
                        <script type="text/javascript">
                            $("#turnos option[value='<?=$_POST['Info_Empresa']['turnos']?>']").attr("selected",true);
                        </script>
                    </div>
                </div>
            </div>

            <div class="control-group row-fluid">
                <div class="span6">
                    <label class="control-label" >Dias Publicos:</label>
                    <div class="controls">
                        <select id="dias_publicos" name="Info_Empresa[dias_publicos]" >
                            <option></option>
                            <option value="L-D">L-D</option>
                            <option value="L-V">L-V</option>
                            <option value="L-S">L-S</option>
                        </select>
                        <script type="text/javascript">
                            $("#dias_publicos option[value='<?=$_POST['Info_Empresa']['dias_publicos']?>']").attr("selected",true);
                        </script>
                    </div>
                </div>
                <div class="span6">
                    <label class="control-label" >Tiempo Permanencia:</label>
                    <div class="controls">
                        <input type="text" name="Info_Empresa[tiempo_permanencia]" value="<?=$_POST['Info_Empresa']['tiempo_permanencia']?>" >
                    </div>
                </div>
            </div>

        </p>

    </div>
    <div class="modal-footer">
        <button id="Close-Send_Info_Empresa" class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button id="Send_Info_Empresa" name="Send_Info_Empresa" class="btn btn-success" data-dismiss="modal" >Aceptar</button>
    </div>
</div>

