<script type="text/javascript">
    $(document).on('ready', function(){

        $('#SalesOrdersDTable').dataTable({"scrollX": true});

        $('#start_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

        $('#end_date').datetimepicker({
            lang:'es',
            formatDate:'Y-m-d',
        });

         $('#guardar').click(function(event){
            event.preventDefault();
            var jqxhr = $.ajax({
                url: "<?php echo $this->createUrl('activities/createactivitie'); ?>",
                type: "POST",
                dataType : "json",
                timeout : (120 * 1000),
                data: {
                    Create:{
                        actividad: $('#tipo_actividad').val(),
                        title: $('#nombre_evento').val(),
                        debtorno: "<?=$_GET['id']?>",
                        start: $('#start_date').val(),
                        end: $('#end_date').val(),
                        contacto_id: $('#contacto_id').val(),
                        prospecto_id: "<?=$AccountData['idProspecto']?>",
                        descripcion: $('#descripcion').val(),
                        TipoLog: 'Calendario'
                    },
                },
                success : function(Response, newValue) {
                    if (Response.requestresult == 'ok') {
                        displayNotify('success', Response.message);
                        $('#tipo_actividad').val(null);
                        $('#nombre_evento').val(null);
                        $('#start_date').val(null);
                        $('#end_date').val(null);
                        $('#descripcion').val(null);
                        $('#ListActivities').prepend(Response.NewActivity);
                    }else{
                        displayNotify('alert', Response.message);
                    }
                },
                error : ajaxError
            });
        });

    });

     $(function() {
        $( ".column" ).sortable({
            connectWith: ".column",
            handle: ".portlet-header",
            cancel: ".portlet-toggle",
            placeholder: "portlet-placeholder ui-corner-all",
            stop: function( event, ui ) {
                console.log(ui.item);
            }
        });

   // $( ".column" ).sortable({ disabled: true });

        $( ".portlet" )
        .addClass( "ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" )
        .find( ".portlet-header" )
        .addClass( "ui-widget-header ui-corner-all" )
        .prepend( "<span class='ui-icon ui-icon-minusthick portlet-toggle'></span>");

        $( ".portlet-toggle" ).click(function() {
            var icon = $( this );
            icon.toggleClass( "ui-icon-minusthick ui-icon-plusthick" );
            icon.closest( ".portlet" ).find( ".portlet-content" ).toggle();
        });
    });
</script>

<style>
  body {
    min-width: 520px;
  }
  .column {
    width: 300px;
    /*float: left;*/
    padding-bottom: 100px;
    /*border:solid;*/
    border-color: #D6E9C6;
    box-shadow: 2px 5px 3px 5px #DDDDDD, 0 1px 2px 0 #FFFFFF inset;
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
  .ui-widget-header {
    border: 1px solid #aaaaaa;
    background: #008cba url("images/ui-bg_highlight-soft_75_cccccc_1x100.png") 50% 50% repeat-x;
    color: #FFFFFF;
    font-weight: bold;
    font-size: 14px;
  }
  </style>



 <?php
 FB::INFO($AccountData, 'account');

FB::INFO($ListActivities, "activities");
 ?>

<div class="large-12 small-12 columns">
        <div class="nav-bar right">
            <ul class="button-group radius">
                <li><a href="<?php echo Yii::app()->createUrl('crm/cuentas/index');?>" class="small button">Listar Cuentas</a></li>
                <li><a href="<?php echo Yii::app()->createUrl('crm/orders/createquotation',array('debtorno' =>$AccountData['debtorno']));?>" class="small button">Crear cotización</a></li>
                <!-- <li><a href="#" class="small button" data-reveal-id="Crearcuenta">Crear Cuenta</a></li>
                <li><a href="#" class="small button" data-reveal-id="crearOportunidad">Crear Oportinidad</a></li> -->
            </ul>
        </div>
        <h1><a href='<?php echo Yii::app()->createUrl("crm/leads/index");?>'>Cuenta</a></h1>
        <hr>
</div>

<div class="large-8 small-12 columns">
    <div class="large-8 small-12 columns">
        <h3>Detalle de la cuenta: <?php echo $AccountData['name']; ?>
        <span data-tooltip class="has-tip radius" title="Editar cuenta"><a href="<?=Yii::app()->createUrl("crm/cuentas/update", array('id'=>$AccountData['debtorno']))?>" class="fi-pencil"></a></span>&nbsp;
        </h3>
    </div>

    <div class="row">
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Nombre: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['name']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Calle: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address1']?></label>
            </div>
        </div>
    </div>
    <div class="row">
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Exterior: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address2']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Número Interior: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address3']?></label>
            </div>
        </div>
    </div>
    <div class="row">
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Colonia: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address4']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Localidad: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address5']?></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Referencia: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address6']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Municipio: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address7']?></label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Estado: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address8']?></label>
            </div>
        </div>
         <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">País: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address9']?></label>
            </div>
        </div>
    </div>
    <div class="class">
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">Código Postal: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['address10']?></label>
            </div>
        </div>
        <div class="large-6 small-12 columns">
        </div>
    </div>
    <div class="class">
        <div class="large-6 small-12 columns">
            <div class="large-6 small-12 columns">
                <label class="label1">RFC: </label>
            </div>
            <div class="large-6 small-12 columns">
                <label class="label2"><?=$AccountData['taxref']?></label>
            </div>
        </div>
    </div>

     <div style="height:50px;"></div>
        <hr/>
        <div class="large-12 columns small-12">
            <h3>Cotizaciones/Pedidos</h3>
            <hr/>
        </div>
        <table id="SalesOrdersDTable">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($SalesOrdesData as $SOData) {
                    if($SOData['quotation:'] == 1){
                        $SOData['quotation:'] = 'Cotización';
                    }else{
                        $SOData['quotation:'] = 'Pedido';
                    }
                ?>
                    <tr>
                        <td><?=$SOData['orderno']?></td>
                        <td><?=$SOData['quotation:']?></td>
                        <td><?=$SOData['orddate']?></td>
                        <td><?=$SOData['OrderTotal2']?></td>
                        <td style="width:50px">
                            <span data-tooltip class="has-tip radius" title="Ver Cotizacion/Pedido"><a href="<?=Yii::app()->createUrl("crm/orders/view", array('orderno' => $SOData['orderno'], 'id' => $DetalleOportunidades['id_prospecto']))?>" class="fi-magnifying-glass"></a></span>&nbsp;
                            <!-- <span data-tooltip class="has-tip radius" title="Ver Cotizacion/Pedido"><a href="<?=Yii::app()->createUrl("crm/orders/edit", array('id' => $SOData['orderno']))?>" class="fi-magnifying-glass"></a></span>&nbsp;
                            <span data-tooltip class="has-tip radius" title="Editar Cotizacion/Pedido"><a href="<?=Yii::app()->createUrl("crm/orders/edit", array('id' => $SOData['orderno']))?>" class="fi-pencil"></a></span> -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

</div>

<div class="large-4 columns">
    <div class="small-2 columns" >
        <div class="column">
            <div class="portlet dropfalse"  id="1">
                <div class="portlet-header">Agregar Actividad</div>
                <div class="portlet-content">
                    <label>Tipo de Actividad:</label>
                    <select id="tipo_actividad">
                        <option value="">Seleccione Una Opción</option>
                        <?php foreach($TipoActividad as $Actividad){ ?>
                        <option value="<?=$Actividad['id']?>"><?=$Actividad['descripcion']?></option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="contacto_id" name="contacto_id" value=0>
                    <label>Nombre de la Actividad:</label>
                    <input type="text" id="nombre_evento" name="nombre_evento" value="">
                    <label>Inicia:</label>
                    <input type="text" id="start_date" name="start_date" value="">
                    <label>Termina:</label>
                    <input type="text" id="end_date" name="end_date" value="">
                    <label>Descripción:</label>
                    <textarea id="descripcion" name="descripcion"></textarea>
                    <input type="button" class="button tiny small" value="Guardar" id="guardar">
                </div>
            </div>

            <div class="portlet" id="2">
                <div class="portlet-header">Actividades</div>
                <div class="portlet-content">
                    <div id="ListActivities" style="height:300px; overflow-y:scroll;">
                        <?php foreach ($ListActivities as $Data){ ?>
                            <div class="panel2 callout radius" style="margin-bottom: 5px;" >
                                <h4 style="margin-top: -5px;"><small><?=$Data['title']?></small></h4>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['start']?></p>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['end']?></p>
                                <p style="font-size:10px; margin-bottom:5px;"><?=$Data['Descripcion']?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="portlet" id="3">
                <div class="portlet-header">Actualizaciones</div>
                <div class="portlet-content">
                    <div id="ListUpdates" style="height:300px; overflow-y:scroll;">
                        <?php foreach ($ListUpdates as $Data) { ?>

                        <div class="panel2 callout radius" style="margin-bottom: 5px;" >
                            <h4 style="margin-top: -5px;"><small><?=$Data['userid']?></small></h4>
                            <p style="font-size:10px; margin-bottom:5px;"><?=$Data['Descripcion']?></p>
                            <p style="font-size:10px; margin-bottom:5px;"><?=$Data['start']?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
