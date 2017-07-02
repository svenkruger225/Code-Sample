<div class="row">
    <div id="myCarousel" class="carousel slide col-md-9" data-ride="carousel">
        <ol class="carousel-indicators">
       		@foreach ( $data->carousels as $index => $item )
            <li data-target="#myCarousel" data-slide-to="{{$index}}" @if ( $index == 0 ) class="active" @endif ></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
       		@foreach ( $data->carousels as $index => $item )
            <div class="item @if ( $index == 0 ) active @endif">
                <img src="{{$item->image}}" alt="{{$item->title}}" title="{{$item->title}}" />
                <div class="container">
                    <div class="carousel-caption">
                        <h1>{{$item->title}}</h1>
                        <p>{{$item->description}}</p>
                        <p><a class="btn btn-lg btn-primary" href="online/enrol" role="button">Sign up today</a></p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
        <a id="myCarouselNext" class="right carousel-control" href="#myCarousel" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
    </div><!-- /.carousel -->
    <div class="col-md-3">

    <div id="myAd" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
       		@foreach ( $data->ads as $index => $item )
            <li data-target="#myAd" data-slide-to="{{$index}}" @if ( $index == 0 ) class="active" @endif ></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
       		@foreach ( $data->ads as $index => $item )
            <div class="item @if ( $index == 0 ) active @endif">
                <img src="{{$item->image}}" alt="{{$item->title}}" title="{{$item->title}}" />
            </div>
            @endforeach
        </div>
        <a class="left carousel-control" href="#myAd" role="button" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
        <a id="myAdNext" class="right carousel-control" href="#myAd" role="button" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
    </div>

    </div>
</div>