<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Forgot Password</span><br />
    <span class="sub_head">Enter Your Email Address</span>
</div>
<div class="spacer-1"></div>
<div class="form">
    
    <div class="login_from">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-forgotpassword-form',
	'enableAjaxValidation'=>false,
)); ?>



	<div class="form_element">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>


	<div style="margin-left:220px">
		<?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/submit_button.png'); ?>
	</div>

<?php $this->endWidget(); ?>
    </div>
</div><!-- form -->