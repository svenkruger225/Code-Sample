<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pages-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'page_name'); ?>
		<?php echo $form->textField($model,'page_name',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'page_name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'page_content'); ?>
                <?php   $this->widget('ext.ckeditor.CKEditorWidget',array(
                  "model"=>$model,                 # Data-Model
                  "attribute"=>'page_content',          # Attribute in the Data-Model
                  "defaultValue"=>$model->page_content,     # Optional
                  # Additional Parameter (Check http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html)
                  "config" => array(
                          "height"=>"400px",
                          "width"=>"600px",
                          "toolbar"=>"Basic",
                          ),
                  ) ); ?>
		<?php echo $form->error($model,'page_content'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->