@extends('core::layouts.master')
@section('title')
    {{ $branch->name }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ $branch->name }}
                        <a href="#" onclick="window.history.back()"
                           class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                            <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                        </a>
                    </h1>

                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item"><a
                                    href="{{url('branch')}}">{{ trans_choice('core::general.branch',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $branch->name }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="box-title">{{ $branch->name }}</h6>

                        <div class="box-tools">

                        </div>
                    </div>
                    <div class="card-body">
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
                        </table>

                    </div>
                    <!-- /.box-body -->
                    <div class="card-footer">
                        <div class="heading-elements">
                        <span class="heading-text">{{ trans_choice('core::general.created_at',1) }}
                            : {{$branch->created_at}}</span>
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title">{{ trans_choice('core::general.user',2) }}</h6>

                        <div class="card-tools pull-right">
                            @can('branch.branches.assign_user')
                                <a href="#" data-toggle="modal" data-target="#addUser"
                                   class="btn btn-info btn-sm">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.user',1)}}</a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
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
                                            <td>{{ $key->user->id }}</td>
                                            <td>{{ $key->user->first_name }} {{ $key->user->last_name }}</td>
                                            <td>{{ $key->user->phone }}</td>
                                            <td>{{ $key->user->email }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <button href="#" class="btn btn-default dropdown-toggle"
                                                            data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-h"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        @can('branch.branches.assign_user')
                                                            <a href="{{ url('user/'.$key->user->id.'/show') }}" class="dropdown-item"><i
                                                                        class="fa fa-search"></i> {{trans_choice('general.detail',2)}}
                                                            </a>

                                                            <a href="{{ url('user/'.$key->user->id.'/edit') }}" class="dropdown-item"><i
                                                                        class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                            </a>

                                                            <a href="{{ url('branch/'.$key->id.'/remove_user') }}"
                                                               class="dropdown-item confirm"><i
                                                                        class="fa fa-trash"></i> {{ trans('general.remove') }}
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->

                </div>
                <!-- /.box -->
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
    </section>
@endsection