@extends('core::layouts.master')
@section('title')
    {{trans_choice('dashboard::general.dashboard',1)}}
@endsection
@section('styles')
    <style>
        .trash-item {
            position: absolute;
            bottom: 0;
            right: 20px;
            display: none;
        }

        .grid-stack-item-content:hover .trash-item {
            display: block;
            position: absolute;
            bottom: 0;
            right: 20px;
        }
    </style>
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans_choice('dashboard::general.dashboard',1)}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active"><a href="{{url('dashboard')}}">{{trans_choice('dashboard::general.dashboard',1)}}</a></li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div id="app">
            <div class="row">
                <div class="col-md-12">
                    <button data-toggle="modal" data-target="#add_widget"
                            class="btn btn-info margin float-right">
                        {{trans_choice('core::general.add',1)}}  {{trans_choice('dashboard::general.widget',1)}}
                    </button>
                    <div class="modal fade" id="add_widget">
                        <div class="modal-dialog">
                            <form method="post" action="{{ url('dashboard/store_widget') }}"
                                  enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{trans_choice('core::general.add',1)}}  {{trans_choice('dashboard::general.widget',1)}}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="widget_id"
                                                   class="control-label">{{trans_choice('dashboard::general.widget',1)}}</label>
                                            <select class="form-control" name="widget_id" id="widget_id" required>
                                                <option value=""></option>
                                                @foreach($available_widgets as $key=>$value)
                                                    <option value="{{$key}}">{{$value["name"]}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left"
                                                data-dismiss="modal">{{trans_choice('core::general.close',1)}} </button>
                                        <button type="submit"
                                                class="btn btn-primary">{{trans_choice('core::general.save',1)}} </button>
                                    </div>
                                </div>
                            </form>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <!-- /.modal -->
                    <div class="grid-stack">
                        @foreach($user_widgets as $user_widget)
                            @widget($user_widget->class, ['x' =>
                            $user_widget->x,"y"=>$user_widget->y,"width"=>$user_widget->width,"height"=>$user_widget->height])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highcharts/6.0.6/highcharts.js" charset="utf-8"></script>
    <script>
        var grid = GridStack.init();
        grid.on('change', function (event, items) {
            let data = [];
            if (items) {
                items.forEach(function (item, index) {
                    data[index] = {
                        "id": item.id,
                        "x": item.x,
                        "y": item.y,
                        "width": item.w,
                        "height": item.h
                    };
                });
            }
            axios.post('{{url('dashboard/update_widget_positions')}}', {
                widgets: data,
                _token: '{{csrf_token()}}'
            }).then(function (response) {
                //toastr.success("{{trans_choice("dashboard::general.successfully_rearranged", 1)}}");
            }).catch(function (error) {
                toastr.warning("{{trans_choice("dashboard::general.failed_rearrange", 1)}}");
            });
        });
    </script>
@endsection
