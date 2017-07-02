<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/easySlider.js'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/easySlider.packed.js'); ?>
<?php //Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/css/nivo-slider.css');  ?>
<?php Yii::app()->clientScript->registerCoreScript('jquery'); ?>
<div class="spacer-1"></div>
<div class="signup_message">
    <span class="main_head">How It Works</span><br />
    <span class="sub_head"></span>
</div>
<div class="page_content">
    <div class="slider_wrapper">
        <div class="ribbon"></div>
        <div id="slider" class="nivoSlider">
            <ul>
                <li><img src="<?php echo Yii::app()->baseUrl; ?>/gallery/step-1.jpg" alt="" /></li>
                <li><img src="<?php echo Yii::app()->baseUrl; ?>/gallery/step-2.jpg" alt="" title="" /></li>
                <li><img src="<?php echo Yii::app()->baseUrl; ?>/gallery/step-3.jpg" alt="" data-transition="" /></li>
                <li><img src="<?php echo Yii::app()->baseUrl; ?>/gallery/step-4.jpg" alt="" title="" /></li>
                <li><img src="<?php echo Yii::app()->baseUrl; ?>/gallery/step-5.jpg" alt="" title="" /></li>
            </ul>
        </div>
    </div>
</div>
<script>
    $(window).load(function() {
        $('#slider').easySlider();
    });
</script>
<style>
    /* Easy Slider */

    #slider ul, #slider li{
        margin:0;
        padding:0;
        list-style:none;
    }
    #slider, #slider li{ 
        width:506px;
        height:375px;
        overflow:hidden; 
    }
    .slider_wrapper{
        margin-left: 180px;
        margin-top: 45px;
    }
    span#prevBtn{
        
    }
    span#prevBtn a{
        padding: 9px 34px 9px 34px;
        background-image: url('../images/blank.png');
        margin-right: 268px;
    }
    span#nextBtn{
    }
    span#nextBtn a{
        padding: 9px 40px 9px 40px;
        background-image: url('../images/blank.png');
    }

    /* // Easy Slider */
</style>
