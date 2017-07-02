<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Sign Up</span><br />
    <span class="sub_head">Its Free and Anyone Can Join</span>
</div>
<?php
$this->menu=array(
	array('label'=>'List User', 'url'=>array('index'),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'Manage User', 'url'=>array('admin'),'visible'=>Yii::app()->user->isAdmin()),
);
?>

<?php if(Yii::app()->user->isAdmin()){ ?>
<?php echo $this->renderPartial('_adminform', array('model'=>$model));} else { ?>
<?php echo $this->renderPartial('_form', array('model'=>$model));} ?>

