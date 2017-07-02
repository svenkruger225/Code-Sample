<div class="send_form">
    

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>


	<div class="form_element">
            <div class="form_label"><?php echo $form->labelEx($model,'email'); ?></div>
            <div class="form_input"><?php echo $form->textField($model,'email',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'email'); ?>
	</div>

<!--	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'password'); ?></div>
		<div class="form_input"><?php echo $form->passwordField($model,'password',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'password'); ?>
	</div>-->

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'first_name'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'first_name'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'last_name'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'last_name',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'last_name'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'id_type'); ?></div>
		<div class="form_input"><?php echo $form->dropDownList($model, 'id_type', array("passport" => "Passport", "dl" => "DL"), array('empty' => 'Select ID Type')); ?></div>
		<?php echo $form->error($model,'id_type'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'id_number'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'id_number',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'id_number'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'id_issuance_place'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'id_issuance_place',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'id_issuance_place'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'address'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'address',array('size'=>60,'maxlength'=>250)); ?></div>
		<?php echo $form->error($model,'address'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'city'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'city',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'city'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'postal_code'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'postal_code',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'postal_code'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'province'); ?></div>
                <div class="form_input"><?php echo $form->dropDownList($model, 'province', array("nunavut" => "Nunavut", "quebec" => "Quebec", "northwest territories" => "Northwest Territories", "ontario" => "Ontario", "british olumbia" => "British Columbia", "alberta" => "Alberta", "saskatchewan" => "Saskatchewan", "manitoba" => "Manitoba", "yukon" => "Yukon", "newfoundland and labrador" => " Newfoundland and Labrador", "new brunswick" => "New Brunswick", "nova scotia" => "Nova Scotia", "prince edward island" => "Prince Edward Island"), array('empty' => 'Select Province')); ?></div>
		<?php echo $form->error($model,'province'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'country'); ?></div>
		<div class="form_input"><?php echo $form->textField($model, 'country', array('maxlength' => 100, 'disabled'=>'true')); ?></div>
		<?php echo $form->error($model,'country'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'occupation'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'occupation',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'occupation'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'phone_number'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'phone_number',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'phone_number'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'date of birth'); ?></div>
		<div class="form_input"><?php
    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
        'name' => 'User[dob]',
        'model' => $model,
        'value' => $model->dob,
        // additional javascript options for the date picker plugin
        'options' => array(
            'showAnim' => 'fold',
            'dateFormat'=>'yy-mm-dd',
            'changeYear' => TRUE,
            'yearRange' => '2012',
            'defaultDate' => '2012'
        ),
        'htmlOptions' => array(
            'style' => 'height:15px; width:382px;',
            'value' => 'date',
        ),
    ));
    ?></div>
		<?php echo $form->error($model,'dob'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'secret_question'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'secret_question',array('size'=>60,'maxlength'=>200)); ?></div>
		<?php echo $form->error($model,'secret_question'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'secret_answer'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'secret_answer',array('size'=>60,'maxlength'=>200)); ?></div>
		<?php echo $form->error($model,'secret_answer'); ?>
	</div>

	<div class="form_element buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->