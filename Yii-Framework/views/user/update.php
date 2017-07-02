<?php
$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List User', 'url'=>array('index'),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'View User', 'url'=>array('view', 'id'=>$model->id),'visible'=>Yii::app()->user->isAdmin()),
	array('label'=>'Manage User', 'url'=>array('admin'),'visible'=>Yii::app()->user->isAdmin()),
);
?>

<?php if(Yii::app()->user->isAdmin()){ ?>
<?php echo $this->renderPartial('_adminform', array('model'=>$model));} else { ?>

<?php echo $this->renderPartial('_form', array('model' => $model,
            'oldPassError' => $oldPassError,
            'newPassError' => $newPassError,
            'confirmPassError' => $confirmPassError,));} ?>