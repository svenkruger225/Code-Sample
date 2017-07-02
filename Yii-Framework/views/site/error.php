<?php
$this->pageTitle=Yii::app()->name . ' - Error';
$this->breadcrumbs=array(
	'Error',
);
?>

<h2>Error <?php echo $code; ?></h2>

<div class="error">
<?php //echo CHtml::encode($message); ?>
    OOPS !!! Some error occured please contact site administrator
</div>