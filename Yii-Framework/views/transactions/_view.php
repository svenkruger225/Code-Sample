<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recipient_id')); ?>:</b>
	<?php echo CHtml::encode($data->recipient_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::encode($data->user_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('remittance_amount')); ?>:</b>
	<?php echo CHtml::encode($data->remittance_amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('coupon')); ?>:</b>
	<?php echo CHtml::encode($data->coupon); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('date')); ?>:</b>
	<?php echo CHtml::encode($data->date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('recieving_amount')); ?>:</b>
	<?php echo CHtml::encode($data->recieving_amount); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('recieving_amount_currency')); ?>:</b>
	<?php echo CHtml::encode($data->recieving_amount_currency); ?>
	<br />

	*/ ?>

</div>