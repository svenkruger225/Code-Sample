<div class="transaction_form">
<div>
    <?php foreach(Yii::app()->user->getFlashes() as $message) { ?>
    <div class="flash_messages"><?php echo $message; ?></div>
    <?php } ?>
</div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'transactions-form',
	'enableAjaxValidation'=>false,
        'htmlOptions' => array(
            'onsubmit' => 'calcAmount()'
        ),
        
)); 
?>
    <?php foreach(Yii::app()->user->getFlashes() as $message) { ?>
    <div class="flash_messages"><?php echo $message; ?></div>
    <?php } ?>
       <div class="step_one">
           <div class="step_head">1. Select the Recipient</div>
            <div class="select_recipient_dropdown">
                    <?php $list = CHtml::listdata($recipients, 'id', 'recipientInfo')?>
                    <div class="transaction_form_label"><label for="recipient">Select Recipient</label></div>
                    <div id="recipient_dropDown" class="transaction_form_input"><?php echo $form->dropDownList($model,'recipient_id',$list, array(
                        'empty' => array('0'=>'Select Recipient')
                        )); ?></div>&nbsp;<a href="<?php echo $this->createUrl('recipients/create'); ?>">New</a><?php if(isset($model->recipient_id)) {?>&nbsp;<a href="<?php echo $this->createUrl('recipients/update/'.$model->recipient_id); ?>">Edit</a><?php } ?>
                    <div class="errorMessage"><?php echo $form->error($model,'recipient_id'); ?></div>
            </div>
        </div>
        <div class="step_two">
            <div class="step_head">2. Remittance Amount</div>
            <div class="left_step_two">
                <div class="row">
                    <div class="transaction_form_label"><?php echo $form->labelEx($model, 'remittance_amount'); ?></div>
                    <div class="form_input"><?php echo $form->textField($model, 'remittance_amount', array('onblur' => 'calcAmount()')); ?></div>
                    <div class="errorMessage"><?php echo $form->error($model, 'remittance_amount'); ?></div>
                </div>
                <div class="row">
                    <div class="transaction_form_label"><?php echo $form->labelEx($model, 'recieving_amount_currency'); ?></div>
                    <div class="form_input"><?php echo $form->textField($model, 'recieving_amount_currency', array('readonly' => 'readonly')); ?></div>
                    <div class="errorMessage"><?php echo $form->error($model, 'recieving_amount_currency'); ?></div>
                </div>
                
                <div class="row">
                    <div class="transaction_form_label"><?php echo $form->labelEx($model, 'recieving_amount'); ?></div>
                    <div class="transaction_form_input"><?php echo $form->textField($model, 'recieving_amount', array( )); ?></div>
                    <div class="errorMessage"><?php echo $form->error($model, 'recieving_amount'); ?></div>
                </div>
            </div>
            <div class="right_step_two">
                <div class="row"><div class="transaction_form_label"><label for="service_fee">Service Fee</label></div><div class="transaction_form_input"><input type="text" id="service_fee" readonly="readonly" /></div><div class="errorMessage"></div></div>
                <div class="row">
                    <div class="transaction_form_label"><?php echo $form->labelEx($model, 'total_charged'); ?></div>
                    <div class="transaction_form_input"><?php echo $form->textField($model, 'total_charged', array( )); ?></div>
                    <div class="errorMessage"><?php echo $form->error($model, 'total_charged'); ?></div>
                </div>
                <div class="row">
                    <div class="transaction_form_label"><?php echo $form->labelEx($model, 'coupon'); ?></div>
                    <div class="transaction_form_input"><?php echo $form->textField($model, 'coupon', array( )); ?></div>
                    <div class="errorMessage"><?php echo $form->error($model, 'coupon'); ?></div>
                </div>
            </div>
            
        </div>

	
    


	

	

	<div class="submit_transaction_form">
		<?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/confirm_and_pay.png'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<style>
    .row{
        margin-bottom: 5px;
    }    
</style>
<script>
    $(document).ready(function(){
       $('#Transactions_recipient_id').change(function(){
           var id = $(this).attr('value');
           if(id == 0){
               return;
           }
           url = "<?php echo $this->createUrl('/transactions/create'); ?>";
           $(location).attr('href',url+"/id/"+id);
       });
    });
    
    function calcAmount()
    {
        var remittance = document.getElementById("Transactions_remittance_amount").value;
        var rate = "<?php echo $model->recieving_amount_currency; ?>";
        
        var amount = remittance * rate;
        
        document.getElementById("Transactions_recieving_amount").value = amount;
        document.getElementById("service_fee").value = "$10";
        document.getElementById("Transactions_total_charged").value = parseFloat(10.00)+parseFloat(remittance);
    }
</script>