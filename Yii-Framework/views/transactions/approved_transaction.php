<h5>Following Transaction Has Been Entered</h5>
<a href="<?php echo $this->createUrl('/transactions/admin') ?>">Go Back</a><br /><br />
<div class="reciept_head">
    <div class="left">
        <div class="element">
            <span class="head">Transaction#: </span><span class="text"><?php echo $trans->id; ?></span>
        </div>
        <div class="element">
            <span class="head">CAD$#: </span><span class="text"><?php echo $trans->remittance_amount; ?></span>
        </div>
        <div class="element">
            <span class="head">Service Fee$: </span><span class="text">10</span>
        </div>
        <div id="total_cad">TOTAL CAD$: <?php echo $trans->total_charged; ?></div>
    </div>
    <div class="right">
        <div class="element">
            <span class="head">Reciept Date: </span><span class="text"><?php echo $trans->date; ?></span>
        </div>
        <div class="element">
            <span class="head">Rate: </span><span class="text"><?php echo $trans->recieving_amount_currency; ?></span>
        </div>
        <div align="bottom" id="recieve_amount">RECIEVING AMOUNT: <?php echo $trans->recieving_amount; ?></div>
    </div>
</div>
<hr>
<div class="sender_info">
    <div class="left">
        <div class="element">
            <span class="head">Name: </span><span class="text"><?php echo $user->first_name." ".$user->last_name; ?></span>
        </div>
        <div class="element">
            <span class="head">Address: </span><span class="text"><?php echo $user->address; ?></span>
        </div>
        <div class="element">
            <span class="head">Tel: </span><span class="text"><?php echo $user->phone_number; ?></span>
        </div>
    </div>
    <div class="right">
        <div class="element">
            <span class="head">ID: </span><span class="text"><?php echo $user->id_number; ?></span>
        </div>
        <div class="element">
            <span class="head">Place of Issue: </span><span class="text"><?php echo $user->province; ?></span>
        </div>
        <div class="element">
            <span class="head">DOB: </span><span class="text"><?php echo $user->dob; ?></span>
        </div>
        <div class="element">
            <span class="head">Occupation: </span><span class="text"><?php echo $user->occupation; ?></span>
        </div>
        <div class="element">
            <span class="head">Purpose: </span><span class="text"><?php ?></span>
        </div>
    </div>
</div>
<hr>
<div class="reciever_info">
    <div class="left">
        <div class="element">
            <span class="head">Name: </span><span class="text"><?php echo $recip->first_name." ".$recip->last_name;  ?></span>
        </div>
        <div class="element">
            <span class="head">Address: </span><span class="text"><?php echo $recip->address1; ?></span>
        </div>
        <div class="element">
            <span class="head">Tel: </span><span class="text"><?php echo $recip->phone; ?></span>
        </div>
    </div>
</div>
<style>
	.left{
		float:left;
		width:300px;
	}
	.right{
		float:left;
		width:300px;
	}
	.reciept_head{
		width: 700px;
		height:100px;
	}
	.sender_info{
		width:700px;
		height: 100px;
	}
	#recieve_amount{
		font-weight: bold;
		font-size: 15px;
		margin-top: 46px;
	}
	#total_cad{
		font-weight: bold;
		font-size: 16px;
		margin-top: 24px;
	}
	.element .head{
		font-weight: bold;
	}
</style>