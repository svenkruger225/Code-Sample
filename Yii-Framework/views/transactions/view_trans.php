<div style="padding: 0px 20px 0px 20px" class="view_transactions">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'transactions-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
                'recipientName',
		array(
                    'header'=>'Money Reveived',
                    'name'=>'total_charged'
                ),
            'recieving_amount_currency',
            'recieving_amount',
            'reference_question',
            'reference_answer',
                array(
                    'header'=>'Transaction Date',
                    'name'=>'date',
                    'value'=>'date("Y-m-d",strtotime($data->date))',
                    'filter'=>$this->widget('zii.widgets.jui.CJuiDatePicker',
                            array(
                                'model'=>$model,
                                'attribute'=>'date',
                                'options'=>array(
                                    'dateFormat'=>'yy-mm-dd'
                                ),
                                'language'=>'en'
                                ), true)
                ),
                array(
                    'name'=>'status',
                    'header'=>'Status',
                    'value'=>array($this,'convertStatusToValue'),
                    'filter' => array(Transactions::APPROVED_TRANSACTION=>'Approved',Transactions::PENDING_TRANSACTION=>'Pending',Transactions::REJECTED_TRANSACTION=>'Rejected'),
                    'htmlOptions'=>array('width'=>'40'),
                    ),
		
	),
        'afterAjaxUpdate'=>'function(){
                                jQuery("#'.CHtml::activeId($model, 'date').'").datepicker({dateFormat: "yy-mm-dd"});
                                $.datepicker.setDefaults({
                                });
                        }',
)); ?>

</div>
