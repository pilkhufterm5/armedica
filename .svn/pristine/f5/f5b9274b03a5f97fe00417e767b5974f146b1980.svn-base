<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */

?>
<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/contactos/index');?>" class="small button">Listar Contactos</a></li>
				<li><a href="<?php echo Yii::app()->createUrl('crm/contactos/create');?>" class="small button">Crear Contacto</a></li>
				<li><a href="#" class="small button" data-reveal-id="myModal">Crear Contacto Rapido</a></li>
			</ul>
		</div>
		<h3><a href='<?php echo Yii::app()->createUrl("crm/contactos/index");?>'>Contactos</a></h3>
		<hr/>
	</div>
</div>
 <div class="row">
 	<div class="large-12 columns" role="content">
	<h3>Editando a:&nbsp; <?php echo $model->nombre.' '.$model->apellidoPaterno; ?></h3>

	<?php $this->renderPartial('_form', array('model'=>$model, 'ListaLeads' => $ListaLeads)); ?>

</div>
</div>
<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un contacto</h2>
	<?php include_once "_quickform.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>
