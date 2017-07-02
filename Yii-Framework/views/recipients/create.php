<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Add Recipients</span><br />
    <span class="sub_head"></span>
</div>
<?php echo $this->renderPartial('_form', array('model'=>$model, 'countries' => $countries,'banks'=>$banks,'cities'=>$cities)); ?>