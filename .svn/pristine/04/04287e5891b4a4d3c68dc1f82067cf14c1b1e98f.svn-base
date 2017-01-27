<link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl;?>/themes/found/css/website.css" type="text/css" media="screen"/>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/themes/found/js/jquery.tinycolorpicker.js"></script>
<script type="text/javascript">

    $(document).on('ready',function() {

        var $box = $('#colorPicker');
        $box.tinycolorpicker();
        var box = $box.data("plugin_tinycolorpicker")
        box.setColor("#ff0000");

        CreateAutoComplete();
        $("#AddLead").click(function(){
            prospecto_id = $("#ProspectoID").val();
            id_fase_venta = $("#Fase_Venta").val();
            lead_name = $("#SearchName").val();
            colorpicker = $("#ColorPicker").val();

            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('kanban/cambiarfase'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Fase:{
                        prospecto_id: prospecto_id,
                        id_fase_venta: id_fase_venta,
                        lead_name: lead_name,
                        colorpicker : colorpicker
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        window.setTimeout('location.reload()', 1000);
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError
            });
        });

        $( ".column" ).sortable({
            connectWith: ".column",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            placeholder: "portlet-placeholder ui-corner-all",

            start: function( event, ui ) {/*Columna Inicial*/
                //alert(event);
            },

            receive: function( event, ui ) {/*Columna Final*/
                prospecto_id = ui.item.context.attributes.id_prospecto.nodeValue;
                id_fase_venta = $(this).attr('id_fase');
                nombre_fase = $(this).attr('name');
                nombre_prospecto = ui.item.context.attributes.nombre_prospecto.nodeValue;
                var jqxhr = $.ajax({
                    url: "<?php echo $this->createUrl('leads/cambiarfase'); ?>",
                    type: "POST",
                    dataType : "json",
                    timeout : (120 * 1000),
                    data: {
                        Fase:{
                            prospecto_id: prospecto_id,
                            id_fase_venta: id_fase_venta,
                            title:'UPDATE',
                            tipo_log:'UPDATE',
                            descripcion: 'La fase de venta cambió a: ' + nombre_fase + '',
                            nombre_fase: nombre_fase,
                            nombre_prospecto : nombre_prospecto,
                        },
                    },
                    success : function(Response, newValue) {
                        if (Response.requestresult == 'ok') {
                            displayNotify('success', Response.message);
                        }else{
                            displayNotify('alert', Response.message);
                        }
                    },
                    error : ajaxError
                });
            }
        });

        $( ".portlet" ).click(function(){
                //console.log($(this));
        })
      .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
      .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-plusthick portlet-toggle'></span>");

        $( ".portlet-toggle" ).click(function() {
          var icon = $( this );
          icon.toggleClass( "ui-icon-plusthick ui-icon-minusthick" );
          icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
        });
    });

    function CreateAutoComplete(){
        window.AutoComplete={
            select: function( event, ui ) {
                $("#SearchName").val(ui.item.value);
                $("#ProspectoID").val(ui.item.id);
                console.log(ui.item);
                return ui.item.value;
        },
        source: function( request, response ) {
            $.ajax({
                url: "<?php echo $this->createUrl('kanban/Search'); ?>",
                dataType: "jsonp",
                data: {
                    Search:{
                        prospecto: request.term
                    },
                }
                }).complete( function( data ) {
                    Response={};
                    if(data.readyState==4){
                        if (typeof(data.responseText)!='undefined')
                            Response=eval('('+data.responseText+')');
                        if (typeof(Response.requestresult)!='undefined'&&Response.requestresult == 'ok') {
                            response(Response.DataList);
                        }else{
                            //displayNotify('alert', Response);
                        }
                    }
                });
            }
        };
        $("#SearchName").autocomplete(window.AutoComplete);
    }

</script>

<style>

    body {
        min-width: 520px;
    }

    .column {
        /*width: 170px; */
        /*float: left;*/
        padding-bottom: 100px;
        /*border:solid;*/
        border-color: #D6E9C6;
        box-shadow: 2px 5px 3px 0 #DDDDDD, 0 1px 2px 0 #FFFFFF inset;
    }

    .portlet {
        margin: 0 1em 1em 0;
        padding: 0.3em;
    }

    .portlet-header {
        padding: 0.2em 0.3em;
        margin-bottom: 0.5em;
        position: relative;
    }

    .portlet-toggle {
        position: absolute;
        top: 50%;
        right: 0;
        margin-top: -8px;
    }

    .portlet-content {
        padding: 0.4em;
    }
    .portlet-placeholder {
        border: 1px dotted black;
        margin: 0 1em 1em 0;
        height: 50px;
    }

    .HeadCenter{
        text-align: center;
    }

    .ColumnsCenter{
        /*float: right; position: relative; right: 30%; */
    }

    .TDTop{
        vertical-align:top;
    }

    .portlet-content{
        display: none;
    }

    #ui-id-1{
         z-index: 1000;
    }

  </style>


<div id="myModal" class="reveal-modal small" data-reveal>
    <div class="large-12 small-12 columns">
        <fieldset>
            <legend>Agregar/reasignar prospectos:</legend>
            <div class="row">
                <div class="large-3 small-12 columns">
                    <label>Prospecto:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <input type='text' value='' id="SearchName" name="SearchName" class='SearchName' >
                    <input type='hidden' value='' id="ProspectoID" name="ProspectoID" class='ProspectoID' >
                </div>
                <div class="large-3 small-12 columns">
                    <label>Color:</label>
                </div>
            </div>
            <div class="row">
                <div class="large-3 small-12 columns">
                    <label>Fase de Venta:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <select name="Fase_Venta" id="Fase_Venta">
                        <?php foreach ($ListFases as $idx => $value) { ?>
                            <option value="<?=$idx?>"><?=$value?></option>
                         <?php } ?>
                    </select>
                </div>

                <div class="large-3 small-12 columns">
                    <div id="colorPicker">
                        <a class="color"><div class="colorInner"></div></a>
                        <div class="track"></div>
                        <ul class="dropdown"><li></li></ul>
                        <input id="ColorPicker" type="hidden" class="colorInput"/>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="large-2 small-12 columns">
                    <input type="button" name="AddLead" id="AddLead" value="Aceptar" class="button tiny">
                </div>
            </div>
        </fieldset>
    </div>
    <a class="close-reveal-modal">&#215;</a>
</div>


<a href="#" data-reveal-id="myModal" class="button tiny">Actualizar fase de venta</a>

<div class="large-12 columns" >
    <table class="table-bordered" style="width:100%;">
        <thead>
            <tr>
                <?php foreach ($ListFases as $idx => $value) { ?>
                    <th class="HeadCenter" id="<?=$idx?>"><?=$value?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ListFases as $idx => $Name) { ?>
                <td class="TDTop" id="<?=$idx?>">
                    <div class="column ColumnsCenter" id_fase="<?=$idx?>" name="<?=$Name?>">
                        <?php foreach($LeadsGoup[$idx] as $Lead){ ?>

                            <div class="portlet" id="Lead_<?=$Lead['idProspecto']?>" id_prospecto="<?=$Lead['idProspecto']?>" nombre_prospecto="<?=$Lead['nombre']?>" >
                                <div name="LeadName" class="portlet-header" style="background-color:<?=$Lead['colorpicker']?> !important;" id_forcolor="<?=$Lead['idProspecto']?>" >
                                <?php if($Lead['tipo']=='CUENTA'){
                                    echo "Cuenta: " . $Lead['nombre'];
                                }else{
                                    echo "Prospecto: " . $Lead['nombre'];
                                }
                                ?>
                                </div>
                                <div class="portlet-content" >
                                    <div class="large-12 small-12 columns">
                                        <label class="label1">Nombre completo:</label>
                                        <input type="hidden" value="<?=$Lead['colorpicker']?>" id="header_color">
                                    </div>
                                    <div class="large-12 small-12 columns"><label class="label2"><?=$Lead['nombre'] ." ".$Lead['apellidoPaterno'] ." ". $Lead['apellidoMaterno']?></label></div>
                                    <div class="large-12 small-12 columns"><label class="label1">Teléfono:</label></div>
                                    <div class="large-12 small-12 columns"><label class="label2"><?=$Lead['telefono']?></label></div>
                                    <div class="large-12 small-12 columns"><label class="label1">Celular:</label></div>
                                    <div class="large-12 small-12 columns"><label class="label2"><?=$Lead['celular']?></label></div>
                                    <div class="large-12 small-12 columns"><label class="label1">E-mail:</label></div>
                                    <div class="large-12 small-12 columns"><label class="label2"><?=$Lead['email']?></label></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </td>
            <?php } ?>
        </tbody>
    </table>
</div>
