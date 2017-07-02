<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Manage'=>array('index'),
        'Pending Users',
);

$this->menu=array(
        array('label'=>'Pending Users', 'url'=>array('pending')),
        array('label'=>'Approved Users', 'url'=>array('approved')),
        array('label'=>'Banned Users', 'url'=>array('banned')),
        array('label'=>'All Users', 'url'=>array('admin')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('user-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Pending Users</h1>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'user-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'email',
		'password',
		'first_name',
		'last_name',
		'id_type',
		/*
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
		*/
                array(
                    'class' => 'CLinkColumn',
                    'label' => 'Approve User',
                    'header' => 'Change Status',
                    'urlExpression'=>'Yii::app()->createUrl("user/approveuser",array("id"=>$data->id, "src" => "pending"));'
                ),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
