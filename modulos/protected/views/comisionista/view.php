<?php
/* @var $this ComisionistaController */
/* @var $model Comisionista */

$this->breadcrumbs=array(
	'Comisionistas'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Comisionista', 'url'=>array('index')),
	array('label'=>'Create Comisionista', 'url'=>array('create')),
	array('label'=>'Update Comisionista', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Comisionista', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Comisionista', 'url'=>array('admin')),
);
?>

<h1>View Comisionista #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'comisionista',
		'coordina_id',
		'activo',
	),
)); ?>
