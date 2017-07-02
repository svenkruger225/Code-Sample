<?php
$this->layout='//layouts/main';
$this->breadcrumbs=array(
	'Pages',
);

$this->menu=array(
	array('label'=>'Manage Pages', 'url'=>array('admin')),
);
?>

<h1>Pages</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
