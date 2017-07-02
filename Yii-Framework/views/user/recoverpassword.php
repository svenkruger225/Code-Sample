
<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Recover Password</span><br />
    <span class="sub_head">Enter Your Secret Answer</span>
</div>
<div class="spacer-1"></div>
<div class="form">

    <div class="login_from">
        <?php
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'user-recoverpassword-form',
            'enableAjaxValidation' => false,
                ));
        ?>
        <div class="secret_question_recover">
            <div>
                <div class="secret_question"> Your Question :</div>
                <div class="secret_answer"><?php echo " " . $model->secret_question; ?>?</div>
            </div>
            <div class="">
                <?php echo $form->labelEx($model, 'secret_answer'); ?>
                <?php echo $form->textField($model, 'secret_answer'); ?>
                <?php echo $form->error($model, 'secret_answer'); ?>
            </div>
        </div>


        <div style="margin-left: 218px;">
        <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/submit_button.png'); ?>
        </div>
            <?php $messages = Yii::app()->user->getFlashes(); ?>
        <div class="flash_messages">
        <?php if (isset($messages) && !empty($messages)) {
            foreach ($messages as $message) {
                echo $message;
            }
        } ?>
        </div>

<?php $this->endWidget(); ?>
    </div>
</div><!-- form -->