<?php
$this->breadcrumbs=array(
	'Countries'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Add Country', 'url'=>array('create')),
	array('label'=>'Manage Countries', 'url'=>array('admin')),
);
?>

<h1>Update Countries <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>