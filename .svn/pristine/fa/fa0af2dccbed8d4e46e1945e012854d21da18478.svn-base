<?php
/**
*  @Todo
*  @Author erasto@realhost.com.mx
*  @var $this LeadsController
 **/
?>

<script type="text/javascript">
    $(document).on('ready',function() {
        $('#LeadsDTable').dataTable({
            "scrollX": true
        });
    });
</script>

<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/leads/create');?>" class="small button">Crear Prospecto</a></li>
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Prospecto Rapído</a></li>
			</ul>
		</div>
		<h1><a href='<?php echo Yii::app()->createUrl("crm/leads/index");?>'>Prospectos</a></h1>
		<hr/>
	</div>
</div>
 <div class="row">
 	<div class="large-12 columns" role="content">
		<table id="LeadsDTable" width="100%">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Apellido Paterno</th>
					<th>Correo electrónico</th>
					<th>Estatus</th>
                    <th>Tipo</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($LeadsData as $Data) { ?>
                    <tr>
                        <td><?=$Data['nombre']?></td>
						<td><?=$Data['apellidoPaterno']?></td>
						<td><?=$Data['email']?></td>
						<td><?=$Data['status_prospecto']?></td>
                        <td><?=$Data['tipo']?></td>
						<td>
                            <span data-tooltip class="has-tip radius" title="Ver informacion a detalle"><a href="<?php echo Yii::app()->createUrl("crm/leads/view", array('id'=>$Data['idProspecto'])); ?>" class="fi-magnifying-glass"></a></span>&nbsp;
							<span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?php echo Yii::app()->createUrl("crm/leads/update", array('id'=>$Data['idProspecto'])); ?>" class="fi-pencil"></a></span>&nbsp;
                        </td>
                    </tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un Prospecto</h2>
	<?php include_once "_quickform.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>
