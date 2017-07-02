<?php Yii::app()->clientScript->registerCoreScript('jquery');  ?>
<?php $this->renderPartial('_form', array('model'=>$model, 'recipients' => $recipients)); ?>