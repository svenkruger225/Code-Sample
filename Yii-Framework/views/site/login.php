<?php
//$this->pageTitle=Yii::app()->name . ' - Login';
$this->breadcrumbs=array(
	'Login',
);
?>

<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Login</span><br />
    <span class="sub_head">and Start Sending Money</span>
</div>
<div class="spacer-1"></div>
<div class="login_from">
<div>
    <?php foreach(Yii::app()->user->getFlashes() as $message) { ?>
    <div class="flash_messages"><?php echo $message; ?></div>
    <?php } ?>
</div>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>


	<div class="login_form_element">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username',array('size' => '30')); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="login_form_element">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password',array('size' => '30')); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div>
            <div class="rememberMe"><?php echo  $form->checkBox($model,'rememberMe'); ?></div>
            <div class="rememberMe_label"><?php echo $form->label($model,'rememberMe'); ?></div>
		<?php echo $form->error($model,'rememberMe'); ?>
	</div>

	
        <div class="login_button">
                 <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/login-button.png'); ?>
            <a href="<?php echo $this->createUrl('/user/forgotpassword'); ?>">Forgot Password?</a> 
        </div>
<?php $this->endWidget(); ?>
</div><!-- form -->
</div>