<h2> My Registration Page </h2>

<p> Hi there, please enter your E-Mail address and drop a note about you </p>

<?php $this->breadcrumbs = array(Yum::t('Registration')); ?>

<div class="form">
<?php $activeform = $this->beginWidget('CActiveForm', array(
			'id'=>'registration-form',
			'enableAjaxValidation'=>false,
			'focus'=>array($profile,'email'),
			));
?>

<?php echo Yum::requiredFieldNote(); ?>
<?php echo CHtml::errorSummary($profile); ?>

<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'email address');
    echo $activeform->textField($profile,'email');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'password');
    echo $activeform->passwordField($profile,'password');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'first name');
    echo $activeform->textField($profile,'firstName');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'last name');
    echo $activeform->textField($profile,'lastName');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'id type(dl, passport)');
    echo $activeform->textField($profile,'idType');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'id number');
    echo $activeform->textField($profile,'idNumber');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'id place of issuance');
    echo $activeform->textField($profile,'issuancePlace');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'address');
    echo $activeform->textField($profile,'address');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'city');
    echo $activeform->textField($profile,'city');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'postal code');
    echo $activeform->textField($profile,'postalCode');
    ?> 
</div>
<div class="row"> 
    <?php
    $data = '';
    echo $activeform->labelEx($profile,'province');
    echo $activeform->textField($profile,'province');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'country');
    echo $activeform->textField($profile,'country');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'occupation');
    echo $activeform->textField($profile,'occupation');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'phone');
    echo $activeform->textField($profile,'phone');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'dob');
    echo $activeform->textField($profile,'dob');
    ?> 
</div>
<div class="row"> 
    <?php
    echo $activeform->labelEx($profile,'passwordRecovery');
    echo $activeform->textField($profile,'passwordRecovery');
    ?> 
</div>
<div class="row submit">
	<?php echo CHtml::submitButton(Yum::t('Registration')); ?>
</div>

<?php $this->endWidget(); ?>