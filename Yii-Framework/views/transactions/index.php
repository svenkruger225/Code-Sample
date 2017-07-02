<?php
$this->breadcrumbs=array(
	'Transactions',
);

$this->menu=array(
	array('label'=>'Create Transactions', 'url'=>array('create')),
	array('label'=>'Manage Transactions', 'url'=>array('admin')),
);
?>

<h1>Transactions</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
