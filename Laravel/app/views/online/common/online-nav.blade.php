    <div class="panel-group" id="accordion">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseZero"><span class="glyphicon glyphicon-list-alt">
                    </span> Courses</a>  @if (isset($data->course)) <span>&nbsp;<i class="glyphicon glyphicon-arrow-right"></i>&nbsp;{{$data->course->name}}</span> @endif
                </h4>
            </div>
            <div id="collapseZero" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table">
						@foreach ( $data->courses as $index => $course_item )
                        <tr @if ($course_item->id == $data->module->course_id ) class='bg-success' @endif><td class="clickableCell">
							<a href="/online/course/{{$course_item->short_name}}">{{$course_item->name}}</a>
                        </td></tr>
						@endforeach
                    </table>
                </div>                    
			</div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="glyphicon glyphicon-th">
                    </span> Modules</a> <span>&nbsp;<i class="glyphicon glyphicon-arrow-right"></i>&nbsp;{{$data->course->name}}</span>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table">
						@foreach ( $data->course->modules as $index => $module_item )
                        <tr @if (isset($data->module) && $module_item->id == $data->module->id ) class='bg-success' @endif><td class="clickableCell">
							<a href="/online/module/{{$module_item->id}}">{{sprintf("%02d", $index + 1)}} - {{$module_item->name}}</a>
                        </td></tr>
						@endforeach
                        <tr class="bg-warning"><td class="clickableCell">
							<a href="/online/course/results/{{$data->course->id}}">Course Results</a>
                        </td></tr>
                    </table>
                </div>                    
			</div>
        </div>
		@if ( isset($data->module) && $data->module->steps->count() )
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne1"><span class="glyphicon glyphicon-th">
                    </span> Steps</a>
                </h4>
            </div>
            <div id="collapseOne1" class="panel-collapse collapse in">
                <div class="panel-body">
                    <table class="table">
						@foreach ( $data->module->steps as $index => $step_item )
                        <tr @if (isset($data->step) && $step_item->id == $data->step->id ) class='bg-success' @endif><td class="clickableCell">
							<a href="/online/step/{{$step_item->id}}">{{sprintf("%02d", $index + 1)}} - {{$step_item->name}}</a>
                        </td></tr>
						@endforeach
                        <tr class="bg-warning"><td class="clickableCell">
							<a href="/online/module/results/{{$data->module->steps->last()->id}}">Module Results</a>
                        </td></tr>
                    </table>
                </div>                    
			</div>
        </div>
		@endif
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span class="glyphicon glyphicon-folder-close">
                    </span> Content</a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table">
                        <tr>
                            <td>
                                <span class="glyphicon glyphicon-pencil text-primary"></span><a href="#">Articles</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="glyphicon glyphicon-flash text-success"></span><a href="#">News</a>
                            </td>
                        </tr>
                        <!--<tr>
                            <td>
                                <span class="glyphicon glyphicon-comment text-success"></span><a href="#">Comments</a>
                                <span class="badge">42</span>
                            </td>
                        </tr> -->
                    </table>
                </div>					
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"><span class="glyphicon glyphicon-user">
                    </span> Account</a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse">
                <div class="panel-body">
                    <table class="table">
                        <tr>
                            <td>
                                <a href="/online/profile">Profile</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="/online/course/results/{{$data->course->id}}">My Progress</a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="/account/change-password">Change Password</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
