<?// php echo Yii::app()->baseUrl;  exit; ?>
<html>
    <head>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <!--[if IE]>
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/default_ie.css" rel="stylesheet" type="text/css" />
        <![endif]-->
        <!--[if !IE]><!-->
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/css/default.css" rel="stylesheet" type="text/css" />
        <!--<![endif]-->
        
    </head>
    <body>
        <div class="wrapper">
            <div class="main_container">
                <div class="header_conatiner">
                    <div class="logo_wrapper">
                        <div class="logo_container"><a href="<?php echo $this->createUrl('site/index'); ?>"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/thamour-logo2.gif" alt="thamour-logo" /></a></div>
                    </div>
                    <div class="menu_wrapper">
                        <div class="login_menu">
                            <div align="right" class="login_menu_container">
                             <?php if(Yii::app()->user->isGuest == TRUE){ ?>   
                                <div class="login_menu_item"><a href="<?php echo $this->createUrl('site/userlogin'); ?>">Login</a></div>
                                <div class="login_menu_item"><a href="<?php echo $this->createUrl('user/create'); ?>">Register</a></div>
                            <?php }else {?>
                                <div class="login_menu_item">Hi! <?php echo Yii::app()->user->name; ?></a></div>
                                <div class="login_menu_item"><a href="<?php echo $this->createUrl('site/logout'); ?>">Logout</a></div>
                            <?php }?>
                            </div>
                        </div>
                        <div class="main_menu_wrapper">
                            <div class="main_menu_container">
                                <ul>
                                    
                                    <?php if(Yii::app()->user->isGuest == TRUE){ ?>
                                    <li><a href="<?php echo $this->createUrl('site/index'); ?>">Home</a></li>
                                    <li><a href="<?php echo $this->createUrl('pages/view/2'); ?>">About Us</a></li>
                                    <li><a href="<?php echo $this->createUrl('site/howitworks'); ?>">How It Works</a></li>
                                    <li><a href="<?php echo $this->createUrl('pages/view/3'); ?>">Safety</a></li>
                                    <li><a href="<?php echo $this->createUrl('pages/view/4'); ?>">FAQ</a></li>
                                    <li><a href="<?php echo $this->createUrl('pages/view/9'); ?>">Find Us</a></li>
                                    <?php } ?>
                                    <?php if(Yii::app()->user->isGuest == FALSE){ ?>  
                                    <li><a href="<?php echo $this->createUrl('rates/show'); ?>">View Rates</a></li>
                                    <li><a href="<?php echo $this->createUrl('transactions/viewtrans/id/'.Yii::app()->user->id); ?>">View History</a></li>
                                    <li><a href="<?php echo $this->createUrl('transactions/create'); ?>">Send Money</a></li>
                                    <li><a href="<?php echo $this->createUrl('recipients/create'); ?>">Add a Recipient</a></li>
                                    <li><a href="<?php echo $this->createUrl('user/update/'.Yii::app()->user->id); ?>">Edit Profile</a></li>
                                    <?php } ?>
                                </ul>
                                <!--<div style="display:inline" class="form_element">
                                        <input name="search" id="search" value="search" />
                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="homepage_content">
                    <?php echo $content; ?>	
                </div>
                <div class="main_footer">
                    <div class="main_footer_wrapper">
                        <div class="main_footer_section">
                            <div class="main_footer_head">Safety</div>
                            <div class="main_footer_text"><?php echo $this->shortSafety(); ?></div>
                            <div class="footer_read_more"><a href="<?php echo $this->createUrl('pages/view/3') ?>">...read more</a></div>
                        </div>
                        <div class="vertical_hr"></div>
                        <div class="main_footer_section">
                            <div class="main_footer_head">Testimonials</div>
                            <div class="main_footer_text"><?php echo $this->shortTesti(); ?></div>
                            <div class="footer_read_more"><a href="<?php echo $this->createUrl('pages/view/5') ?>">...read more</a></div>
                        </div>
                    </div>
                    <hr class="footer_hr">
                    <div class="footer_links">
                        <div class="footer_copyright">
                            <ul>
                                <li>&copy; All rights reserved at <strong>THAMOR</strong> - </li>
                                <li><a href="<?php echo $this->createUrl('pages/view/6'); ?>">Privacy Policy</a></li>
                                <li><a href="<?php echo $this->createUrl('pages/view/7'); ?>">Refund Policy</a></li>
                                <li><a href="<?php echo $this->createUrl('pages/view/8'); ?>">Terms and Conditions</a></li>
                            </ul>
                        </div>
                        <div align="right" class="social_links">
                            <div class="social_logo"><a href="http://www.facebook.com/pages/Thamor/129249537190833" target="_blank"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/facebook_logo.gif" /></a></div>
                            <div class="social_logo"><a href="https://twitter.com/ThamorGroup" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/twitter_logo.gif" /></a></div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script>//!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>