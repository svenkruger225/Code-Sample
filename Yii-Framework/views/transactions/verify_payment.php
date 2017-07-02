<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Verify</span><br />
    <span class="sub_head">Verify the Following Information</span>
</div>

<div class="verify_payment">
    <form method="post" action="">
        <div class="verify_info" id="sender_info">
            <div class="verify_element"><div class="verify_label"><strong>Name: </strong></div><div class="verify_data"><?php echo $sender->first_name . " " . $sender->last_name; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Telephone: </strong></div><div class="verify_data"><?php echo $sender->phone_number; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Address: </strong></div><div class="verify_data"><?php echo $sender->address; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>City: </strong></div><div class="verify_data"><?php echo $sender->city; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Province: </strong></div><div class="verify_data"><?php echo $sender->province; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Country: </strong></div><div class="verify_data"><?php echo $sender->country; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Occupation: </strong></div><div class="verify_data"><?php echo $sender->occupation; ?></div></div>
        </div>
        <hr>
        <div class="verify_info" id="recipient_info">
            <div class="verify_element"><div class="verify_label"><strong>Sending To: </strong></div><div class="verify_data"><?php echo $recip->first_name . " " . $recip->last_name; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Telephone: </strong></div><div class="verify_data"><?php echo $recip->phone; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Address1: </strong></div><div class="verify_data"><?php echo $recip->address1; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Address2: </strong></div><div class="verify_data"><?php echo $recip->address2; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>City: </strong></div><div class="verify_data"><?php echo $recip->city; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Country: </strong></div><div class="verify_data"><?php echo $recip->country; ?></div></div>
        </div>
        <hr>
        <div class="verify_info" id="trans_info">
            <div class="verify_element"><div class="verify_label"><strong>Sending Amount: </strong></div><div class="verify_data"><?php echo $trans->remittance_amount; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Rate: </strong></div><div class="verify_data"><?php echo $trans->recieving_amount_currency; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Receiving Amount: </strong></div><div class="verify_data"><?php echo $trans->recieving_amount; ?></div></div>
            <div class="verify_element"><div class="verify_label"><strong>Service Fee$: </strong></div><div class="verify_data"><?php echo 10; ?></div></div><br />
            <div class="verify_element"><div class="verify_label"><strong>Total Canadian Charged: </strong></div><div class="verify_data"><span class="total_charged"><?php echo $trans->total_charged; ?>$</span></div></div><br />
        </div>
        <hr>
        <div class="info">
            Email: transfer@thamorgroup.com<br />
            Use password <strong><?php echo $trans->payment_reference; ?></strong><br /><br />
            Pay Now using any of these banks for Email Transfer:<br />
            Need Help? - <a target="_blank" href="<?php echo $this->createUrl('site/howitworks'); ?>">Read Instructions </a> or <a href="#" onclick="window.open('http://www.interac.ca/popups/eTransfer_video.html', 'poop', 'height=500,width=700,modal=yes,alwaysRaised=yes')">Watch Demo Video</a>
        </div>
        <br><br>
        <div id="payment_reference">
            <div class="row">
                <div class="form_label"><label for="reference_question">Reference Question</label></div>
                <?php if(empty($sender->reference_question)): ?>
                <div class="form_input"><?php echo CHtml::textField('verify[referecne_question]', ""); ?></div>
                <?php else: ?>
                <div class="form_input"><?php echo CHtml::textField('verify[referecne_question]', $sender->reference_question, array("readonly" => "readonly")); ?></div>
                <?php endif; ?>
                <div class="errorMessage"><?php echo $questionError; ?></div>
            </div>
            <div class="row">
                <div class="form_label"><label for="reference_answer">Reference Answer</label></div>
                <?php if(empty($sender->reference_answer)): ?>
                <div class="form_input"><?php echo CHtml::textField('verify[reference_answer]', ""); ?></div>
                <?php else: ?>
                <div class="form_input"><?php echo CHtml::textField('verify[reference_answer]', $sender->reference_answer, array("readonly" => "readonly")); ?></div>
                <?php endif; ?>
                <div class="errorMessage"><?php echo $answerError; ?></div>
                
            </div>
        </div>
        <?php // echo CHtml::hiddenField("verify[payment_reference]",$code) ?>
        <div class="checkbox"><?php echo CHtml::checkBox('verify[agree]'); ?>&nbsp;&nbsp;I have completed the email transfer to transfer@thamorgroup.com for the amount of <strong><?php echo $trans->total_charged; ?>$</strong></div>
        <div class="errors"><?php echo $aError; ?></div>
        <div class="verify_submit">
            <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/verify_button.png'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php echo CHtml::imageButton(Yii::app()->request->baseUrl . '/images/cancel_button.png', array('submit' => array('transactions/delete/id/' . $trans->id))); ?>
        </div>

    </form>

</div>

<style>
    .verify_payment{
        width: 700px;
        min-height: 40%;
        margin:auto;
        font-family: arial;
        font-size: 13px;
    }
    .verify_label{
        color:#1e6eff;
        font-size: 14px;
        width:150px;
        float:left;
    }
    .verify_data{
        width:200px;
        float:left;
    }
    .verify_element{
        height: 25px;
        width:500px;
    }
    .verify_info{
        margin-top: 10px;
    }
    .info{
        margin-top: 10px;
    }
    .banks{
        width:500px;
        height:170px;
    }
    .bank{
        float:left;
        margin-bottom:10px;
        margin-top:10px;
        width:200px;
    }
    .bankradio{
        float:left;
        padding-top: 7px;
    }.submit
    {
        margin: auto;
    }
    .verify_submit{
        margin: 10px 10px 0px 100px;
        padding-bottom: 10px;
    }
    form{
        padding:0;
        margin:0
    }
    .checkbox{
        font-size: 12px;
        width:800px;
    }
    #payment_reference{
        height: 70px;
    }
    .form_label{
        color: #1E6EFF;
        font-family: Arial;
        font-size: 15px;
        font-weight: bold;
    }
</style>