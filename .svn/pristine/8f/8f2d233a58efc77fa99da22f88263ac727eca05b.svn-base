<script type="text/javascript">
    $(document).on('ready',function() {
        $("#source").select2();
        $("#parent_account").select2();
        $("#datepicker").datepicker();
    });
</script>

<div style="height: 20px;"></div>
<form class="form-horizontal" method="POST">
    <input type="hidden" name="debtorno" value="raymundo" /> 
    <input type="hidden" name="branchcode" value="rysql" /> 
    <input type="hidden" name="opportunities_id" value="1" /> 
    <input type="hidden" name="lead_id" value="1" /> 
    <input type="hidden" name="id" value="" /> 
    <input type="hidden" id="extension" name="extension" value="" /> 

    <div class="container-fluid">
        <div class="form-legend"><h3>Crear contacto</h3></div>

        <div id="Name" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo _('Nombre'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="name" name="name" />
                </div>
            </div>
       

            <div class="span3">
                <label class="control-label"><?php echo _('Apellido'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="lastname" name="lastname" />
                </div>
            </div>
        </div>

        <div id="Department" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo _('Departmento'); ?></label>
            </div>
            <div class="span9">
                <div class="controls">
                    <input type="text" id="department" name="department" />
                </div>
            </div>
        </div>

        <div id="Description" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo _('Descripciòn'); ?></label>
            </div>
            <div class="span9">
                <div class="controls">
                    <textarea id="description" name="description" rows="1" class="auto-resize"></textarea><span class="field-annotation"></span>
                    <script>
                        $("#description").charCount({
                            counterText: 'Characters left: '
                        });
                    </script>
                </div>
            </div>
        </div>

        <div id="ImportantNote" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label">Nota importante</label>
            </div>
            <div class="span9">
                <div class="controls">
                    <textarea id="important_note" name="important_note" rows="1" class="auto-resize"></textarea><span class="field-annotation"></span>
                    <script>
                        $("#important_note").charCount({
                            counterText: 'Characters left: '
                        });
                    </script>
                </div>
            </div>
        </div>
        
     

        <div class="form-legend"><h3><small>Información de contacto</small></h3></div>

        <div id="E-Mail" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('E-Mail'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="email" name="email" />
                </div>
            </div>

             <div class="span3">
                <label class="control-label"><?php echo ('E-Mail secundario'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="secondary_email" name="secondary_email" />
                </div>
            </div>
        </div>


        <!--Input Phone-->
        <div id="Phone" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label" for="inputPhone">Teléfono de oficina<a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="(###) ###-####"><i class="icon-photon info-circle"></i></a></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input id="inputPhone" type="tel" name="office_phone">
                    <script>
                        $(document).ready(function(){
                            $('#inputPhone').mask('(999) 999-9999 Ext (9999)');
                        });
                    </script>
                </div>
            </div>
             <div class="span3">
                <label class="control-label" for="inputPhone2">Ceular<a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="(###) ###-####"><i class="icon-photon info-circle"></i></a></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input id="inputPhone2" type="tel" name="mobile_phone">
                </div>
            </div>
        </div>


        <div id="Phone" class="control-group row-fluid">
             <div class="span3">
                <label class="control-label" for="inputPhone3">Teléfono de casa<a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="(###) ###-####"><i class="icon-photon info-circle"></i></a></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input id="inputPhone3" type="tel" name="home_phone">
                    <script>
                        $(document).ready(function(){
                            $('#inputPhone3').mask('(999) 999-9999 Ext (9999)');
                        });
                    </script>
                </div>
            </div>

            <div class="span3">
                <label class="control-label"><?php echo ('No llamar'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="checkbox" id="donotcall" name="donotcall" />
                </div>
            </div>
        </div>
        <!--end Input Phone-->

        <div id="Date_Birth" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label" for="datepicker">
                    Fecha de nacimiento<a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="Click to choose date."><i class="icon-photon info-circle"></i></a>
                </label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="datepicker" name="date_birth" class="span3 hasDatepicker">
                </div>
            </div>

            <div class="span3">
                <label class="control-label"><?php echo ('Titulo'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="title" name="title" />
                </div>
            </div>
        </div>

        <div class="form-legend"><h3><small></small></h3></div>

        <div class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Organizaciòn'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="organitation_name" name="organitation_name" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Lead Source'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="lead_source" name="lead_source" />
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Asistene'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="assistant" name="assistant" />
                </div>
            </div>
          
            <div class="span3">
                <label class="control-label" for="inputPhone3">Teléfono de asistente<a href="javascript:;" class="bootstrap-tooltip" data-placement="top" data-original-title="(###) ###-####"><i class="icon-photon info-circle"></i></a></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input id="inputPhone4" type="tel" name="assistant_phone">
                    <script>
                        $(document).ready(function(){
                            $('#inputPhone4').mask('(999) 999-9999 Ext (9999)');
                        });
                    </script>
                </div>
            </div>
        </div>

        <div class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Reporta a'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="reports_to" name="reports_to" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Asignado a'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="assigned_to" name="assigned_to" />
                </div>
            </div>
        </div>

        
        <div class="form-legend"><h3><small>Address</small></h3></div>
        
            <div id="Address" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Dirección'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address" name="address" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Código postal'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="zip" name="zip" />
                </div>
            </div>
        </div>


         <div id="Address5" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Estado'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="state" name="state" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Ciudad'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="city" name="city" />
                </div>
            </div>
        </div>
        
        <!--div id="Address1" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Address1'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address1" name="address1" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Address2'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address2" name="address2" />
                </div>
            </div>
        </div>
        
    
       
        
        <!--div id="Address7" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Address7'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address7" name="address7" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Address8'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address8" name="address8" />
                </div>
            </div>
        </div>
        
        <div id="Address9" class="control-group row-fluid">
            <div class="span3">
                <label class="control-label"><?php echo ('Address9'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address9" name="address9" />
                </div>
            </div>
            
            <div class="span3">
                <label class="control-label"><?php echo ('Address10'); ?></label>
            </div>
            <div class="span3">
                <div class="controls">
                    <input type="text" id="address10" name="address10" />
                </div>
            </div>
        </div-->
        
        
        
        <div id="Savebtn" class="control-group row-fluid">
            <div class="span3">
                <input type="submit" id="CreateLead" name="CreateLead" class="btn btn-large" value="Create" style="margin-bottom: 0px;" />
            </div>
        </div>
        
    </div>
</form>

