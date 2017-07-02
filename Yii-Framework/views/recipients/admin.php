
<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'recipients-grid',
    'dataProvider' => $model->search(),
    //'filter' => $model,
    'columns' => array(
        'first_name',
        'country',
        'payment_method',
        'city',
        
        array(
            'name' => 'flag',
            'header' => 'Flag',
            'value' => '$data->convertFlagToValue',
            'filter' => array(Recipients::FLAGGED => 'Flagged', Recipients::NON_FLAGGED => 'Non Flagged'),
            'htmlOptions' => array('width' => '40'),
        ),
        'purpose'
        /*
          'last_name',
          'ic_ac_num',
          'address1',
          'address2',
          'phone',
          'notes',
          'created_at',
          'updated_at',
         */
    ),
));
?>
