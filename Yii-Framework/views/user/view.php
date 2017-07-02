<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index'),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'Update User', 'url'=>array('update', 'id'=>$model->id),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'Delete User', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?'),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'Manage User', 'url'=>array('admin'),'visible'=>Yii::app()->user->isAdmin()),
);
?>

<h1>View User #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'email',
		'password',
		'first_name',
		'last_name',
		'id_type',
		'id_number',
		'id_issuance_place',
		'address',
		'city',
		'postal_code',
		'province',
		'country',
		'occupation',
		'phone_number',
		'dob',
		'secret_question',
		'secret_answer',
		'created_at',
		'updated_at',
	),
)); ?>
