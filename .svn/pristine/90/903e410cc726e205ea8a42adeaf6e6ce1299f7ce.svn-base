<?php
/* @var $this RhCrmcontactoController */
/* @var $model RhCrmcontacto */

$this->breadcrumbs=array(
	'Rh Crmcontactos'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List RhCrmcontacto', 'url'=>array('index')),
	array('label'=>'Create RhCrmcontacto', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#rh-crmcontacto-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Rh Crmcontactos</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rh-crmcontacto-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id_contacto',
		'nombre',
		'apellido_paterno',
		'apellido_materno',
		'email',
		'telefono_particular',
		/*
		'fecha_nacimiento',
		'titulo',
		'departamento',
		'no_enviar_correo',
		'no_llamar_telefono',
		'asginado_a',
		'fecha_alta',
		'fecha_ultima_actualizacion',
		'usuario_alta',
		'descripcion',
		'contacto_activo',
		'telefono_empresa',
		'cliente',
		'calle1',
		'colonia1',
		'entre_calles1',
		'ciudad1',
		'estado1',
		'calle2',
		'colonia2',
		'ciudad2',
		'estado2',
		'entre_calles2',
		'skype',
		'facebook',
		'twitter',
		'googlplus',
		'imagen',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
