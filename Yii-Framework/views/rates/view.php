<?php
$this->breadcrumbs=array(
	'Rates'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Rates', 'url'=>array('index')),
	array('label'=>'Create Rates', 'url'=>array('create')),
	array('label'=>'Update Rates', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Rates', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Rates', 'url'=>array('admin')),
);
?>

<h1>View Rates #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'currency',
                'currency_symbol',
		'rate',
	),
)); ?>
