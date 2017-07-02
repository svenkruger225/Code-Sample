<header>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">

                <div class="container-fluid">
                        <div class="row-fluid">
                                <div class="span33">
                                        <a class="brand header-social" href="/"><img src="/images/logo.jpg" /></a>
                                </div>

                                <div class="span6 pull-left">
                                        <h1 class="logo-header">Australia's Best Hospitality Training Courses</h1>
                                </div>

                                <div id="header-social-links" class="pull-right">
                                        <a class="header-social" href="http://www.facebook.com/CoffeeRSAschool" title="facebook">
                                                <img src="/images/face_46.png" />
                                        </a>
                                        <a class="header-social" href="http://instagram.com/coffeersaschool" title="instagram">
                                                <img src="/images/instagram_46.png" />
                                        </a>
                                        <a class="header-social" href="#" data-bind="click: openSendPageToFriendForm" title="Tell a Friend">
                                                <img src="/images/tell_friend_46.png" />
                                        </a>
                                        <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                                <span class="icon-bar"></span>
                                        </button>
                                </div>


                                <div class="nav-collapse collapse">
                                        <!-- {{App::environment()}} -->

                                        <!-- Navigation -->
                                        @include('frontend/common/navigation')

                                </div><!--/.nav-collapse -->
                        </div>
                </div>
        </div>
    </div>
</header>





