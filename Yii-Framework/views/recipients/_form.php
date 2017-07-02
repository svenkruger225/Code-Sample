
<div class="send_form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'recipients-form',
	'enableAjaxValidation'=>false,
)); ?>
    <div class="page_warning">All fields in marked by (*) are mandatory. If you don't have a piece of information put N/A. </div>
<div>
    <?php foreach(Yii::app()->user->getFlashes() as $message) { ?>
    <div class="flash_messages"><?php echo $message; ?></div>
    <?php } ?>
</div>

	<div class="form_element">
                <?php $list = Chtml::listData($countries, 'name', 'name') ?>
		<div class="form_label"><?php echo $form->labelEx($model,'country'); ?></div>
		<div class="form_input"><?php echo $form->dropDownList($model,'country', $list); ?></div>
		<?php echo $form->error($model,'country'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'payment_method'); ?></div>
		<div class="form_input"><?php echo $form->dropDownList($model,'payment_method',array('bank' => 'Bank', 'delivery' => 'Delivery', 'pickup'=> 'Pick Up'),array(
                    "empty"=>"Select Payment",
                    'ajax' => array(
                        'type' => 'Post',
                        'dataType'=>'json',
                        'data'=>array('country'=>"js:$('#Recipients_country').val()",'payment_method'=>'js:$(this).val()'),
                        'url' => CController::createUrl('recipients/choice'),
                        'success' =>'function(data){
                                $("#city_list").hide();
                                $("#bank_list").hide();
                                $("#payment_error").hide();
                                // clear html to avoid appending
                                $("#Recipients_city").html("");
                                $("#Recipients_bank").html("");
                                if(data.city)
                                {
                                    $.each(data.city,function(key,val){
                                        var option = "<option value="+key+">"+val+"</option>";
                                         $("#Recipients_city").append(option);
                                    });
                                    $("#city_list").show();
                                }
                                else if(data.bank)
                                {
                                    $.each(data.bank,function(key,val){
                                        var option = "<option value="+key+">"+val+"</option>";
                                         $("#Recipients_bank").append(option);
                                    });
                                    $("#bank_list").show();
                                }
                                else
                                {
                                    $("#payment_error").show();
                                }
                            }',
                    ))); ?></div>
		<?php echo $form->error($model,'payment_method'); ?>
                <div id="payment_error" class="errorMessage" style="display:none">Payment method not allowed in selected country</div>
	</div>
        <?php if(empty($cities)): $cities = array(); endif; ?>
	<div id="city_list" class="form_element" style="display:<?php echo empty($cities)?"none":"inline" ?>">
		<div class="form_label"><?php echo $form->labelEx($model,'city'); ?></div>
		<div class="form_input"><?php  echo $form->dropDownList($model,'city',$cities); ?></div>
		<?php echo $form->error($model,'city'); ?>
	</div>
        <?php if(empty($banks)): $banks  = array(); endif; ?>
	<div id="bank_list" class="form_element" style="display:<?php echo empty($banks)?"none":"inline" ?>">
		<div class="form_label"><?php echo $form->labelEx($model,'bank'); ?></div>
		<div class="form_input"><?php  echo $form->dropDownList($model,'bank',$banks); ?></div>
		
	</div>
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
		<div class="form_label"><?php echo $form->labelEx($model,'ic_ac_num'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'ic_ac_num',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'ic_ac_num'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'address1'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'address1',array('size'=>60,'maxlength'=>200)); ?></div>
		<?php echo $form->error($model,'address1'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'address2'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'address2',array('size'=>60,'maxlength'=>200)); ?></div>
		<?php echo $form->error($model,'address2'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'phone'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'phone',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'phone'); ?>
	</div>
        <div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'purpose'); ?></div>
		<div class="form_input"><?php echo $form->textField($model,'purpose',array('size'=>60,'maxlength'=>100)); ?></div>
		<?php echo $form->error($model,'purpose'); ?>
	</div>

	<div class="form_element">
		<div class="form_label"><?php echo $form->labelEx($model,'notes'); ?></div>
		<div class="form_input"><?php echo $form->textArea($model,'notes',array('rows'=>5, 'cols'=>45)); ?></div>
		<?php echo $form->error($model,'notes'); ?>
	</div>
        <?php echo $form->hiddenField($model,'user_id',array("value"=>Yii::app()->user->id)); ?>

	<div class="form_button buttons">
		<?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/add_recipient.png'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script>
    $(document).ready(function(){
       $("#Recipients_country").change(function(){
           $("#payment_error").hide();
           $("#city_list").hide();
           $("#bank_list").hide();
           $("#Recipients_payment_method>option:eq(0)").attr('selected', true);
       }) 
    });
</script>