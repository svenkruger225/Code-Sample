<h1>Manage Transactions</h1>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'transactions-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'id',
        'senderEmail',
        'total_charged',
        'bank_name',
        'reference_question',
        'reference_answer',
        array(
            'header' => 'Transaction Date',
            'name' => 'date',
            'value' => 'date("Y-m-d",strtotime($data->date))',
            'filter' => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model' => $model,
                'attribute' => 'date',
                'options' => array(
                    'dateFormat' => 'yy-mm-dd'
                ),
                'language' => 'en'
                    ), true)
        ),
        array(
            'name' => 'status',
            'header' => 'Status',
            'value' => array($this, 'convertStatusToValue'),
            'filter' => array(Transactions::APPROVED_TRANSACTION => 'Approved', Transactions::PENDING_TRANSACTION => 'Pending', Transactions::REJECTED_TRANSACTION => 'Rejected'),
            'htmlOptions' => array('width' => '40'),
        ),
        'recieving_amount_currency',
        array(
            'type' => 'raw',
            'name' => 'Approve',
            'filter' => false,
            'value' => '$data->status == Transactions::PENDING_TRANSACTION ? CHtml::link("Approve", "' . Yii::app()->baseUrl . '/transactions/approve/$data->id"): ""',
        ),
        array(
            'type' => 'raw',
            'name' => 'Reject',
            'filter' => false,
            'value' => '$data->status == Transactions::PENDING_TRANSACTION ? CHtml::link("Reject", "' . Yii::app()->baseUrl . '/transactions/reject/$data->id"): ""',
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{view}'
        )
    ),
    'afterAjaxUpdate' => 'function(){
                                jQuery("#' . CHtml::activeId($model, 'date') . '").datepicker({dateFormat: "yy-mm-dd"});
                                $.datepicker.setDefaults({
                                });
                        }',
));
?>
