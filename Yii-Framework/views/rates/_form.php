<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rates-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
                <?php $list = CHtml::listData($countries, 'name', 'name') ?>
		<?php echo $form->labelEx($model,'currency'); ?>
		<?php echo $form->dropDownList($model,'currency',$list); ?>
		<?php echo $form->error($model,'currency'); ?>
	</div>
        <div class="row">
		<?php echo $form->labelEx($model,'currency_symbol'); ?>
		<?php echo $form->textField($model,'currency_symbol',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'currency_symbol'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'rate'); ?>
		<?php echo $form->textField($model,'rate',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'rate'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
