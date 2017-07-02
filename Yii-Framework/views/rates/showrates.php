<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">Rates</span><br />
    <span class="sub_head">Latest Currency Rates</span>
</div>
<div class="page_content">
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'rates-grid',
	'dataProvider'=>$model->search(),
	'columns'=>array(
		'currency',
                'currency_symbol',
                'rate',
	),
)); ?>
    
</div>
