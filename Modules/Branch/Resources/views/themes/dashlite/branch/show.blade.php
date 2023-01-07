@extends('core::layouts.master')
@section('title')
    {{ $branch->name }}
@endsection
@section('content')

    <div class="row">
        <div class="col-md-4">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ $branch->name }}</h4>
                                </div>

                            </div>
                        </div>
                        <table class="table">
                            <tr>
                                <td>{{ trans_choice('core::general.open',1) }} {{ trans_choice('core::general.date',1) }}</td>
                                <td>{{$branch->open_date}}</td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('core::general.active',1) }} </td>
                                <td>
                                    @if($branch->active==1)
                                        {{ trans_choice('core::general.yes',1) }}
                                    @else
                                        {{ trans_choice('core::general.no',1) }}
                                    @endif
                                </td>
                            </tr>
                            @foreach($custom_fields as $custom_field)
                                <?php
                                $field = custom_field_build_form_field($custom_field, $branch->id);
                                ?>
                                <tr>
                                    <td>{{$field['label']}}</td>
                                    <td>
                                        @if($custom_field->type=='checkbox')
                                            @foreach(explode(',',$field['current'] ) as $key)
                                                {{$key}}<br>
                                            @endforeach
                                        @else
                                            {{$field['current'] }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td>{{ trans_choice('core::general.note',2) }} </td>
                                <td>{!! $branch->notes !!}</td>
                            </tr>
                            <tr>
                                <td>{{ trans_choice('core::general.created_at',1) }} </td>
                                <td>{{ $branch->created_at }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="nk-block nk-block-lg">
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ trans_choice('core::general.user',2) }}</h4>
                                </div>
                                <div class="nk-block-head-content">
                                    @can('branch.branches.assign_user')
                                        <a href="#" data-toggle="modal" data-target="#addUser"
                                           class="btn btn-info btn-sm">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.user',1)}}</a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <table id="" class="table table-striped basic-data-table table-condensed table-hover">
                            <thead>
                            <tr>
                                <th>{{trans_choice('core::general.id',1)}}</th>
                                <th>{{trans_choice('core::general.name',1)}}</th>
                                <th>{{trans_choice('core::general.phone',1)}}</th>
                                <th>{{trans_choice('core::general.email',1)}}</th>
                                <th>{{ trans_choice('core::general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($branch->users as $key)
                                @if(!empty($key->user))
                                    <tr>
                                        <td>{{ $key->user->first_name }} {{ $key->user->last_name }}</td>
                                        <td>{{ $key->user->id }}</td>
                                        <td>{{ $key->user->phone }}</td>
                                        <td>{{ $key->user->email }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                                        data-toggle="dropdown"
                                                        aria-expanded="true"><i class="fa fa-navicon"></i></button>
                                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                    @can('branch.branches.assign_user')
                                                        <li><a href="{{ url('user/'.$key->user->id.'/show') }}"><i
                                                                        class="fa fa-search"></i> {{trans_choice('general.detail',2)}}
                                                            </a></li>

                                                        <li><a href="{{ url('user/'.$key->user->id.'/edit') }}"><i
                                                                        class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ url('branch/'.$key->id.'/remove_user') }}"
                                                               class="confirm"><i
                                                                        class="fa fa-trash"></i> {{ trans('general.remove') }}
                                                            </a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addUser">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.user',1)}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">*</span></button>
                </div>
                <form method="post" action="{{url('branch/'.$branch->id.'/add_user')}}" class="">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="control-label"
                                   for="user_id">{{trans_choice('core::general.select',1)}} {{trans_choice('core::general.user',1)}}</label>
                            <select class="form-control select2" name="user_id" id="user_id" required>
                                <option value=""></option>
                                @foreach(\Modules\User\Entities\User::all() as $key)
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">{{trans_choice('core::general.save',1)}}</button>
                        <button type="button" class="btn default"
                                data-dismiss="modal">{{trans_choice('core::general.close',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection