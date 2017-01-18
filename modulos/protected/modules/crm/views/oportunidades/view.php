<script type="text/javascript">
    $(document).on('ready',function() {

        $('#SalesOrdersDTable').dataTable({
             "scrollX": true
        });

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

    function cambiarFase(id, nombre_fase){
        fase_actual = $(".current").attr('id');
        var jqxhr = $.ajax({
            url: "<?php echo $this->createUrl('leads/cambiarFase'); ?>",
            type: "POST",
            dataType : "json",
            timeout : (120 * 1000),
            data: {
                Fase:{
                    id_fase_venta: id,
                    CurrentFase: fase_actual,
                    nombre_fase: nombre_fase,
                    prospecto_id: $('#prospecto_id').val(),
                    nombre_prospecto: $('#nombre_prospecto').val(),
                    title:'Update',
                    tipo_log:'Update',
                    descripcion: 'La fase de venta cambió a: '+nombre_fase,
                },
            },
            success : function(Response, newValue) {
                if (Response.requestresult == 'ok') {
                    displayNotify('success', Response.message);
                    $('#' + Response.LastFase).removeClass('current');
                    $('#' + Response.LastFase).addClass('unavailable');
                    $('#fase_' + Response.CurrentFase).addClass('current');
                    $('#fase_' + Response.CurrentFase).removeClass('unavailable');
                    $('#ListUpdates').prepend(Response.NewUpdate);
                }else{
                    displayNotify('alert', Response.message);
                }
            },
            error : ajaxError
        });

  }
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
  </style>
<?php
FB::INFO($DetalleOportunidades,'________________-DetalleOportunidades');

 ?>


    <div class="large-12 columns small-12">
        <div class="nav-bar right">
            <ul class="button-group radius">
                <li><a href="<?php echo Yii::app()->createUrl('crm/oportunidades/index');?>" class="small button">Listar oportunidades</a></li>
                <li><a href="#" class="small button" data-reveal-id="myModal">Crear oportunidad</a></li>
                <?php if($DetalleOportunidades['debtorno']){ ?>
                    <li><a href="<?php echo Yii::app()->createUrl('crm/orders/createquotation',array('debtorno' =>$DetalleOportunidades['debtorno']));?>" class="small button">Crear cotización</a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="large-12 columns small-12">
            <h1><a href='<?php echo Yii::app()->createUrl("crm/oportunidades/index");?>'>Oportunidades</a></h1>
            <hr/>
        </div>
    </div>

    <div class="large-12 small-12 columns">
        <div class="large-4 small-12 columns">
            <h3>Detalle de la oportunidad:</h3>
        </div>

        <div class="large-12 small-12 columns" style="text-align:left;">
            <h3>
                <?php echo $DetalleOportunidades['nombre']?>
                <span data-tooltip class="has-tip radius" title="Editar oportunidad"><a href="<?=Yii::app()->createUrl("crm/oportunidades/update", array('id'=>$DetalleOportunidades['id']))?>" class="fi-pencil"></a></span>&nbsp;
            </h3>
        </div>
    </div>

    <div class="large-12 small-12 columns">
        <div class="row">
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Nombre:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['nombre']?></label>
                </div>
            </div>
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Fecha de creación:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['created']?></label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="large-6  small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Fecha esperada de cierre:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['closed']?></label>
                </div>
            </div>
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Monto:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=number_format($DetalleOportunidades['amount'], 2)?></label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Probabilidad %:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['probability']?></label>
                </div>
            </div>
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Descripción:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['descripcion']?></label>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Prospecto:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['prospecto']." ".$DetalleOportunidades['apellidoPaterno']." ".$DetalleOportunidades['apellidoMaterno']?></label>
                </div>
            </div>
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Fase de Venta:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['fase_venta']?></label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="large-6 small-12 columns">
                <div class="large-6 small-12 columns">
                    <label class="label1">Asignada a:</label>
                </div>
                <div class="large-6 small-12 columns">
                    <label class="label2"><?=$DetalleOportunidades['assignedto']?></label>
                </div>
            </div>
        </div>


        <div style="height:50px;"></div>
        <hr/>
        <div class="large-12 columns small-12">

        <?php FB::INFO($SalesOrdesData, 'SalesOrdersDTable');?>

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
                    if($SOData['quotation'] == 1){
                        $SOData['quotation'] = 'Cotización';
                    }else{
                        $SOData['quotation'] = 'Pedido';
                    }
                ?>
                    <tr>
                        <td><?=$SOData['orderno']?></td>
                        <td><?=$SOData['quotation']?></td>
                        <td><?=$SOData['orddate']?></td>
                        <td><?=$SOData['OrderTotal2']?></td>
                        <td style="width:50px">
                            <span data-tooltip class="has-tip radius" title="Ver Cotizacion/Pedido"><a href="<?=Yii::app()->createUrl("crm/orders/view", array('orderno' => $SOData['orderno'], 'id' => $DetalleOportunidades['id_prospecto']))?>" class="fi-magnifying-glass"></a></span>&nbsp;
                            <!-- <span data-tooltip class="has-tip radius" title="Editar Cotizacion/Pedido"><a href="<?=Yii::app()->createUrl("crm/orders/edit", array('orderno' => $SOData['orderno']))?>" class="fi-pencil"></a></span> -->
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>

    <div id="myModal" class="reveal-modal" data-reveal>
        <h2>Crear un oportunidad</h2>
        <?php include_once "_crearOportunidad.php"; ?>
        <a class="close-reveal-modal">&#215;</a>
    </div>


