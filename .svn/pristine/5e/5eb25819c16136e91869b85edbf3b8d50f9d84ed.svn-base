<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */


?>

<div class="row">
	<div class="large-12 columns">
		<div class="nav-bar right">
			<ul class="button-group radius">
				<li><a href="<?php echo Yii::app()->createUrl('crm/leads/index');?>" class="small button">Listar Prospectos</a></li>
				<!-- <li><a href="#" class="small button" data-reveal-id="myModal">Crear Contacto rapido</a></li>-->
			</ul>

		</div>
		<h1><a href='<?php echo Yii::app()->createUrl("crm/leads/index");?>'>Prospectos</a></h1>
		<hr/>
	</div>
</div>
 <div class="row">
 	<div class="large-12 columns" role="content">
		<h3>Crear Nuevo Prospecto</h3>
		<?php $this->renderPartial('_form', array('model'=>$model, 'SourceList'=>$SourceList)); ?>
	</div>
</div>


<div id="myModal" class="reveal-modal" data-reveal>
	<h2>Crear un Prospecto</h2>
	<?php include_once "_quickform.php"; ?>
	<a class="close-reveal-modal">&#215;</a>
</div>
