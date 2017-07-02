<?php
$this->breadcrumbs=array(
	'Transactions'=>array('admin'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Manage Transactions', 'url'=>array('admin')),
);
?>

<h1>View Transactions #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		array(
                    'label'=>'Recipients Name',
                    'value'=>$model->recipients->first_name
                ),
		array(
                    'label'=>'Sender Name',
                    'value'=>$model->users->first_name
                ),
                'bank_name',
                'payment_reference',
                array(
                    'label'=>'Sending Amoun',
                    'value'=>"$".$model->remittance_amount
                ),
                'recieving_amount_currency',
                'recieving_amount',
		'date',
		array(
                    'label'=>'Status',
                    'value'=>$model->trans_status
                )
	),
)); ?>
