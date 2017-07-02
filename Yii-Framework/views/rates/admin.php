<?php
$this->breadcrumbs=array(
	'Rates'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Rates', 'url'=>array('index')),
	array('label'=>'Create Rates', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('rates-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Rates</h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rates-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'currency',
                'currency_symbol',
		'rate',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
