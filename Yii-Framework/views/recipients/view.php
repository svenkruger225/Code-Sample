<div class="signup_message">
    <span class="main_head">Recipient Successfully Added</span><br />
    <span class="sub_head"></span>
</div>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'country',
		'payment_method',
		'city',
		'first_name',
		'last_name',
		'ic_ac_num',
		'address1',
		'address2',
		'phone',
		'notes',
	),
)); ?>

<div class="send_money_now">
    <a href="<?php echo $this->createUrl('transactions/create/'); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/send_money_now.png" /></a>
</div>
