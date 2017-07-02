<?php
$this->breadcrumbs=array(
	'Pages'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Manage Pages', 'url'=>array('admin')),
);
?>

<h1>View Pages #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'page_name',
		'page_content',
		'created_at',
		'updated_at',
	),
)); ?>
