<div class="send_form">
    <?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>    

    <?php
    $form = $this->beginWidget('CActiveForm', array(
        'id' => 'form',
        'enableAjaxValidation' => false,
            ));
    ?>
    <div>
        <?php foreach (Yii::app()->user->getFlashes() as $message) { ?>
            <div class="flash_messages"><?php echo $message; ?></div>
        <?php } ?>
    </div>


    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'email:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'email', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'email'); ?>
    </div>
    <?php if (Yii::app()->user->isGuest == TRUE): ?>
        <div class="form_element">
            <div class="form_label"><?php echo $form->labelEx($model, 'password:'); ?></div>
            <div class="form_input"><?php echo $form->passwordField($model, 'password', array('maxlength' => 100)); ?></div>
            <?php echo $form->error($model, 'password'); ?>
        </div>
    <?php else: ?>
        <div class="form_element">
            <div class="form_label"><label for="old_pass">Old Password</label></div>
            <div class="form_input"><input type="password" name="User[old_pass]" id="user_confirm_password" /></div>
            <div class="errorMessage"><?php echo $oldPassError;  ?></div>
        </div>
        <div class="form_element">
            <div class="form_label"><label for="new_pass">New Password</label></div>
            <div class="form_input"><input type="password" name="User[new_pass]" id="user_confirm_password" /></div>
            <div class="errorMessage"><?php echo $newPassError;  ?></div>
        </div>
        <div class="form_element">
            <div class="form_label"><label for="confirm_pass">Confirm Password</label></div>
            <div class="form_input"><input type="password" name="User[confirm_pass]" id="user_confirm_password" /></div>
            <div class="errorMessage"><?php echo $confirmPassError;  ?></div>
        </div>
    <?php endif; ?>
    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'first_name:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'first_name', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'first_name'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'last_name:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'last_name', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'last_name'); ?>
    </div>
    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'id_type:'); ?></div>
        <div class="form_input"><?php echo $form->dropDownList($model, 'id_type', array("passport" => "Passport", "dl" => "DL"), array('empty' => 'Select ID Type')); ?></div>
        <?php echo $form->error($model, 'id_type'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'id_number:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'id_number', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'id_number'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'id_issuance_place:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'id_issuance_place', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'id_issuance_place'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'address:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'address', array('maxlength' => 250)); ?></div>
        <?php echo $form->error($model, 'address'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'city:'); ?></div>
        <!--<div class="form_input"><?php echo $form->dropDownList($model, 'city', array('SriLanka' => array('Colombo', 'Jaffna', 'Vavuniya', 'Trinco'), 'India' => array('Chennai', 'Trichy'), 'United Kingdom' => array('Tooting', 'Wembley'), 'France' => array('Paris'), 'Switzerland' => array('Burn', 'Zurich')), array('maxlength' => 100)); ?></div>-->
        <div class="form_input"><?php echo $form->textField($model, 'city') ?></div>
        <?php echo $form->error($model, 'city'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'postal_code:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'postal_code', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'postal_code'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'province:'); ?></div>
        <div class="form_input"><?php echo $form->dropDownList($model, 'province', array("alberta" => "Alberta", "british columbia" => "British Columbia", "manitoba" => "Manitoba", "new brunswick" => "New Brunswick", "newfoundland and labrador" => "Newfoundland and Labrador", "nova scotia" => "Nova Scotia", "northwest territories" => "Northwest Territories", "nunavut" => "Nunavut", "ontario" => "Ontario", "quebec" => "Quebec", "prince edward island" => "Prince Edward Island", "saskatchewan" => "Saskatchewan", "yukon" => "Yukon"), array('empty' => 'Select Province')); ?></div>
        <?php echo $form->error($model, 'province'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'country:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'country', array('maxlength' => 100, 'disabled' => 'true')); ?></div>
        <?php echo $form->error($model, 'country'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'occupation:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'occupation', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'occupation'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'phone_number:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'phone_number', array('maxlength' => 100)); ?></div>
        <?php echo $form->error($model, 'phone_number'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'date of birth:'); ?></div>
        <div class="form_input"><?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
            'model' => $model,
            'value' => $model->dob,
            'name' => 'User[dob]',
            // additional javascript options for the date picker plugin
            'options' => array(
                'showAnim' => 'fold',
                'dateFormat' => 'yy-mm-dd',
                'changeYear' => TRUE,
                'yearRange' => 'c-112:c+10'
            ),
            'htmlOptions' => array(
                'style' => 'height:33px; width:382px;',
                'value' => 'text',
            ),
        ));
        ?></div>
        <?php echo $form->error($model, 'dob'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'secret_question:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'secret_question', array('maxlength' => 200)); ?></div>
        <?php echo $form->error($model, 'secret_question'); ?>
    </div>

    <div class="form_element">
        <div class="form_label"><?php echo $form->labelEx($model, 'secret_answer:'); ?></div>
        <div class="form_input"><?php echo $form->textField($model, 'secret_answer', array('maxlength' => 200)); ?></div>
        <?php echo $form->error($model, 'secret_answer'); ?>
    </div>
    <?php if ($model->isNewRecord): ?>
        <div class="agree_checbox">
            <?php echo $form->checkBox($model, 'agree', $checked = false); ?> I have read the <a href="<?php echo $this->createUrl('pages/view/8'); ?>">Terms and Conditions</a>, <a href="<?php echo $this->createUrl('pages/view/7'); ?>">Refund Policy</a>, <a href="<?php echo $this->createUrl('pages/view/6'); ?>">Privacy Policy</a> and agree with them
            <?php echo $form->error($model, 'agree'); ?>
        </div>
    <?php endif; ?>
    <div class="form_button buttons">
        <?php if (Yii::app()->user->isGuest) { ?>
            <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/register-button.png'); ?>
        <?php } else { ?>
            <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/update_button.png'); ?>
        <?php } ?>
        <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/reset-button.png', array('type' => 'reset', 'onClick' => 'this.form.reset();return false;')); ?>
    </div>


    <?php $this->endWidget(); ?>

</div>