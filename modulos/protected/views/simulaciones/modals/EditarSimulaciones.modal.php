<script type="text/javascript">
    var checkActive = true;
    $(document).ready(function() {
        $('#CBId').change(function() {
            if(checkActive == false) {
                $('#E-prc_aumento_tarifa').attr("readonly",true);
                checkActive = true;
            } else {
                $('#E-prc_aumento_tarifa').attr("readonly",false);
                checkActive = false;
            }
        });
    });
</script>
<script type="text/javascript">
    var checkNuevaTarifa = true;
    $(document).ready(function() {
        $('#tarifa').change(function() {
            if(checkNuevaTarifa == false) {
                $('#E-nueva_tarifa_editada').attr("readonly",true);
                checkNuevaTarifa = true;
            } else {
                $('#E-nueva_tarifa_editada').attr("readonly",false);
                checkNuevaTarifa = false;
            }
        });
    });
</script>
<script type="text/javascript">
$(document).on('ready', function() {
        $('#E-fecha_aumento_tarifa').datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '-100:+5'
        });

$("#redondearModal").click(function(event) {
                if(this.checked){
                        $('#RedondearModaltxt').val('SiAplica');
                }else{
                        $('#RedondearModaltxt').val('NoAplica');
                }
        });

        $('#Create-Modal-EditarSimulaciones').click(function(){
                var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl("simulaciones/Update"); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                        Update:{
                                id: $("#E-ID").val(),
                                folio: $('#E-folio').val(),
                                prc_aumento_tarifa: $('#E-prc_aumento_tarifa').val(),
                                fecha_aumento_tarifa: $('#E-fecha_aumento_tarifa').val(),
                                nueva_tarifa: $('#E-nueva_tarifa').val(),
                                nueva_tarifa_editada: $('#E-nueva_tarifa_editada').val(),
                                aplica_redondeo: $('#RedondearModaltxt').val(),
                                usuario: $('#E-usuario').val(),
                        },
                },
                success : function(data, newValue) {
                        if (data.requestresult == 'ok') {
                                displayNotify('success', data.message);
                        $('#ListSimulaciones #' + data.id).html(data.NewRow);
                                location.reload();
                        }else{
                                displayNotify('alert', data.message);
                        }
                },
                error : ajaxError
                });
        });
});


/*function mostrarporcentaje(txt){
        if(txt.checked) { document.porcentaje.disabled="true"; } else { document.porcentaje.disabled="true"; }
}*/

function EditarSimulaciones(id){
        $('#ModalLabelEdit').text('EDITAR SIMULACIÓN DE AUMENTOS DE PRECIO');
        $('#Modal_EditarSimulaciones').modal('show');
        $("#E-ID").val(id);
        LoadForm(id);
}
function LoadForm(id){
        var jqxhr = $.ajax({
        url: "<?php echo $this->createUrl("simulaciones/LoadForm"); ?>",
        type: "POST",
        dataType : "json",
        timeout : (120 * 1000),
        data: {
                GetData:{
                        id: id
                },
        },success : function(data, newValue) {
                if (data.requestresult == 'ok') {
                        displayNotify('success', data.message);
                        $('#E-ID').val(data.GetData.id);
                        $('#E-folio').val(data.GetData.folio);
                        $('#E-prc_aumento_tarifa').val(data.GetData.prc_aumento_tarifa);
                        $('#E-fecha_aumento_tarifa').val(data.GetData.fecha_aumento_tarifa);
                        $('#E-nueva_tarifa').val(data.GetData.nueva_tarifa_redondeada);
                        $('#E-usuario').val(data.GetData.usuario);
                }else{
                        displayNotify('alert', data.message);
                }
        },
        error : ajaxError
        });
}
</script>

<style>


h1 {
    color: #eee;
    font: 30px Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    text-shadow: 0px 1px black;
    text-align: center;
    margin-bottom: 50px;
}

/*input[type=checkbox] {
    visibility: hidden;
}*/
.redondearModal {
    width: 28px;
    height: 28px;
    background: #fcfff4;

    background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
    margin: 20px auto;

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;

    -webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    -moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    position: relative;
}

.redondearModal label {
    cursor: pointer;
    position: absolute;
    width: 10px;
    height: 20px;

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    left: 4px;
    top: 4px;

    -webkit-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
    -moz-box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);
    box-shadow: inset 0px 1px 1px rgba(0,0,0,0.5), 0px 1px 0px rgba(255,255,255,1);

    background: -webkit-linear-gradient(top, #222 0%, #45484d 100%);
    background: -moz-linear-gradient(top, #222 0%, #45484d 100%);
    background: -o-linear-gradient(top, #222 0%, #45484d 100%);
    background: -ms-linear-gradient(top, #222 0%, #45484d 100%);
    background: linear-gradient(top, #222 0%, #45484d 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#222', endColorstr='#45484d',GradientType=0 );
}

.redondearModal label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
    filter: alpha(opacity=0);
    opacity: 0;
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    background: #00bf00;

    background: -webkit-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -moz-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -o-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: -ms-linear-gradient(top, #00bf00 0%, #009400 100%);
    background: linear-gradient(top, #00bf00 0%, #009400 100%);

    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
    top: 2px;
    left: 2px;

    -webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    -moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
    box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
}

.redondearModal label:hover::after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
    filter: alpha(opacity=30);
    opacity: 0.3;
}

.redondearModal input[type=checkbox]:checked + label:after {
    -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
    filter: alpha(opacity=100);
    opacity: 1;
}
</style>

<div id="Modal_EditarSimulaciones" class="modal hide fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="width: 40%;" >
        <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="ModalLabelEdit"> </h3>
        </div>
        <div class="modal-body">
                
                <div class="control-group row-fluid">
                        <input type="text" id="E-ID" name="E-ID" style="display: none;" />
                </div>
                <div class="control-group row-fluid">
                        <table width="50%" align="center">
                        <tr>
                            <th>
                                <label class="control-label"><b><?php echo _('Aplicar Redondeo:'); ?></b></label>
                            </th>
                            <th>
                                <div class="redondearModal">
                                    <input type="checkbox" value="None" id="redondearModal" name="check" />
                                    <label for="redondearModal"></label>
                                </div>
                                <div class="span2">
                                            <input type="text" name="RedondearModaltxt" id="RedondearModaltxt" class="success" value="NoAplica" style="display: none;">
                                        </div>
                               <!-- <div class="span2">
                                    <input type="text" name="RedondearModaltxt" id="RedondearModaltxt" class="success " value="NoAplica">
                                </div>-->
                            </th>
                        </tr>
                        </table>
                       <!-- <div class="span6">
                               <label class="control-label"><?php //echo _('Aplicar Redondeo:'); ?></label> 
                        </div>
                        <div class="span12">
                                <div class="controls">
                                        <input type="checkbox" id="E-folio" name="E-folio" class="span3" readonly="readonly" />
                                </div>
                        </div> -->
                </div><hr>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('Folio:'); ?></label>
                        </div>
                        <div class="span4">
                                <div class="controls">
                                        <input type="text" id="E-folio" name="E-folio" class="span12" readonly="readonly" />
                                </div>
                        </div>
                </div>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('%DeAumento:'); ?></label>
                        </div>
                        <div class="span4">
                                <div class="controls">
                                        <input type="text" id="E-prc_aumento_tarifa" name="E-prc_aumento_tarifa" class="span12" readonly="true" />
                                </div>
                        </div>
            <div class="span4">
                    <div class="controls">
                            <input type="checkbox" name="CBName" id="CBId" /> <p>Editar</p>
                    </div>
            </div>
                </div>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('FechaAumento:'); ?></label>
                        </div>
                        <div class="span4">
                                <div class="controls">
                                        <input type="text" id="E-fecha_aumento_tarifa" name="E-fecha_aumento_tarifa" class="span12" />
                                </div>
                        </div>
                </div>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('TarifaNueva:'); ?></label>
                        </div>
                        <div class="span4">
                                <div class="controls">
                                        <input type="text" id="E-nueva_tarifa" name="E-nueva_tarifa" class="span12" readonly="true" />
                                </div>
                        </div>
                        <div class="span4">
                   <!-- <div class="controls">
                            <input type="checkbox" name="tarifanueva" id="tarifa" /> <p>Editar</p>
                    </div>-->
            </div>
                </div>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('TarifaNuevaEditada:'); ?></label>
                        </div>
                        <div class="span4">
                                <div class="controls">
                                        <input type="text" id="E-nueva_tarifa_editada" name="E-nueva_tarifa_editada" class="span12" readonly="true" />
                                </div>
                        </div>
                        <div class="span4">
                            <div class="controls">
                                    <input type="checkbox" name="tarifanueva" id="tarifa" /> <p>Editar</p>
                            </div>
                        </div>
                </div>
                <div class="control-group row-fluid">
                        <div class="span4">
                                <label class="control-label"><?php echo _('Usuario:'); ?></label>
                        </div>
                        <div class="span3">
                                <div class="controls">
                                        <input type="text" id="E-usuario" name="E-usuario" value="<?php echo $_SESSION['UserID'];?>" readonly="readonly" />
                                </div>
                        </div>
                </div>
                
        </div>
        <div class="modal-footer">
                <button id="Close-Modal-EditarSimulaciones" class="btn" data-dismiss="modal" aria-
                hidden="true">Cancelar</button>
                <button id="Create-Modal-EditarSimulaciones" class="btn btn-success" data-dismiss="modal" aria-hidden="true" >Aceptar</button>
        </div>
</div>