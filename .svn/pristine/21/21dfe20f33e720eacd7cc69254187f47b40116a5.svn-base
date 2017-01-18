<?php
/**
*  @Todo
*  @Author erasto@realhost.com.mx
*  @var $this ContactosController
 **/

?>

<script type="text/javascript">
    $(document).on('ready',function() {
        $('#OportunidadesDTable').dataTable({
             "scrollX": true
        });
    });
</script>

<?php FB::INFO($Oportunidades, 'Oportunidades'); ?>

<div class="row">
    <div class="large-12 columns">
        <div class="nav-bar right">
            <ul class="button-group radius">
                <!-- <li><a href="<?php echo Yii::app()->createUrl('crm/oportunidades/create');?>" class="small button">Crear oportunidad</a></li> -->
                <li><a href="#" class="small button" data-reveal-id="crearOportunidad">Crear oportunidad</a></li>
            </ul>
        </div>
        <h1><a href='<?php echo Yii::app()->createUrl("crm/oportunidades/index");?>'>Oportunidades</a></h1>
        <hr/>
    </div>
</div>

 <div class="row">
    <div class="large-12 columns" role="content">
        <table id="OportunidadesDTable">
            <thead>
                <tr>
                    <th>Prospecto</th>
                    <th>Cuenta</th>
                    <th>Nombre</th>
                    <th>Fase de venta</th>
                    <th>Fecha esperada de cierre</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Oportunidades as $Data) { ?>
                    <tr id="<?=$Data['id']?>">
                        <td class="cantidades"><?=$Data['id_prospecto']?></td>
                        <td class="cantidades"><?=$Data['debtorno']?></td>
                        <td><?=$Data['nombre']?></td>
                        <td><?=$Data['fase_venta']?></td>
                        <td><?=$Data['closed']?></td>
                        <td class="cantidades">$ <?=number_format($Data['amount'], 2)?></td>
                        <td>
                            <span data-tooltip class="has-tip radius" title="Ver informacion a detalle"><a href="<?php echo Yii::app()->createUrl("crm/oportunidades/view", array('id'=>$Data['id'])); ?>" class="fi-magnifying-glass"></a></span>&nbsp;
                            <span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?php echo Yii::app()->createUrl("crm/oportunidades/update",array('id'=>$Data['id'])); ?>" class="fi-pencil"></a></span>&nbsp;
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<div id="crearOportunidad" class="reveal-modal" data-reveal>
    <h2>Crear un oportunidad</h2>
    <?php include_once "_crearOportunidad.php"; ?>
    <a class="close-reveal-modal">&#215;</a>
</div>
