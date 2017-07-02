<?php
$this->breadcrumbs=array(
	'Recipients',
);

$this->menu=array(
	array('label'=>'Create Recipients', 'url'=>array('create')),
	array('label'=>'Manage Recipients', 'url'=>array('admin')),
);
?>

<h1>Recipients</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
