<?php
$this->breadcrumbs=array(
	'Rates',
);

$this->menu=array(
	array('label'=>'Create Rates', 'url'=>array('create')),
	array('label'=>'Manage Rates', 'url'=>array('admin')),
);
?>

<h1>Rates</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
