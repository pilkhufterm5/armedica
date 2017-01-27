<?php
/**
*  @Todo
*  @Author erasto@realhost.com.mx
*  @var $this ContactosController
 **/

?>

<script type="text/javascript">
    $(document).on('ready',function() {
        $('#ContactsDTable').dataTable({
        	"scrollX": true
        });
    });
</script>



<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/contactos/create');?>" class="small button">Crear contacto</a></li>
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Contacto Rapido</a></li>
			</ul>
		</div>
		<h1><a href='<?php echo Yii::app()->createUrl("crm/contactos/index");?>'>Contactos</a></h1>
		<hr/>
	</div>
</div>

 <div class="row">
 	<div class="large-12 columns" role="content">
		<table id="ContactsDTable">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Apellido Paterno</th>
					<th>Correo electronico</th>
					<th>Acciones</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ContactosData as $Data) { ?>
                    <tr id="<?=$Data['idContacto']?>">
                        <td><?=$Data['nombre']?></td>
						<td><?=$Data['apellidoPaterno']?></td>
						<td><?=$Data['email']?></td>
						<td>
                            <span data-tooltip class="has-tip radius" title="Ver informacion a detalle"><a href="<?php echo Yii::app()->createUrl("crm/contactos/view", array('id'=>$Data['idContacto'])); ?>" class="fi-magnifying-glass"></a></span>&nbsp;
							<span data-tooltip class="has-tip radius" title="Editar contacto"><a href="<?php echo Yii::app()->createUrl("crm/contactos/update", array('id'=>$Data['idContacto'])); ?>" class="fi-pencil"></a></span>&nbsp;
                        </td>
                    </tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un contacto</h2>
	<?php include_once "_quickform.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>
