<?php
$this->breadcrumbs = array(
    'Pages' => array('index'),
    $model->id,
);

$this->menu = array(
    array('label' => 'Manage Pages', 'url' => array('admin')),
);
?>
<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head"><?php echo $model->page_name ?></span><br />
    <span class="sub_head"></span>
</div>
<div class="page_content">
<?php echo $model->page_content; ?>
</div>
