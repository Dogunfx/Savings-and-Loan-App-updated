@extends('core::layouts.master')
@section('title')
    {{ $client->first_name }} {{ $client->last_name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    @if(!empty($client->photo))
                        <a href="{{asset('storage/uploads/clients/'.$client->photo)}}" class="fancybox"> <img
                                    class="profile-user-img img-responsive img-circle"
                                    src="{{asset('storage/uploads/clients/'.$client->photo)}}"
                                    alt="User profile picture">
                        </a>
                    @else
                        <img class="profile-user-img img-responsive img-circle"
                             src="{{asset('assets/dist/img/user.png')}}"
                             alt="User profile picture">
                    @endif
                    <h3 class="profile-username text-center">
                        @if(!empty($client->title))
                            {{$client->title->name}}
                        @endif
                        {{$client->first_name}} {{$client->last_name}}</h3>

                    <p class="text-muted text-center">
                        @if(!empty($client->profession->name))
                            {{$client->profession->name}}
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.branch',1)}}</b>
                            <a class="pull-right">
                                @if(!empty($client->branch))
                                    {{$client->branch->name}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.status',1)}}</b>
                            <a class="pull-right">
                                @if($client->status=='pending')
                                    {{trans_choice('core::general.pending',1)}}
                                @endif
                                @if($client->status=='active')
                                    {{trans_choice('core::general.active',1)}}
                                @endif
                                @if($client->status=='inactive')
                                    {{trans_choice('core::general.inactive',1)}}
                                @endif
                                @if($client->status=='deceased')
                                    {{trans_choice('core::general.deceased',1)}}
                                @endif
                                @if($client->status=='other')
                                    {{trans_choice('core::general.other',1)}}
                                @endif
                                @if($client->status=='closed')
                                    {{trans_choice('core::general.closed',1)}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.external_id',1)}}</b>
                            <a class="pull-right">
                                {{$client->external_id}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.type',1)}}</b>
                            <a class="pull-right">
                                @if(!empty($client->client_type))
                                    {{$client->client_type->name}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.staff',1)}}</b>
                            <a class="pull-right">
                                @if(!empty($client->loan_officer))
                                    {{$client->loan_officer->first_name}} {{$client->loan_officer->last_name}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.mobile',1)}}</b>
                            <a class="pull-right">
                                {{$client->mobile}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.email',1)}}</b>
                            <a class="pull-right">
                                {{$client->email}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.dob',1)}}</b>
                            <a class="pull-right">
                                {{$client->dob}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.gender',1)}}</b>
                            <a class="pull-right">
                                @if($client->gender=='male')
                                    {{trans_choice('core::general.male',1)}}
                                @endif
                                @if($client->gender=='female')
                                    {{trans_choice('core::general.female',1)}}
                                @endif
                                @if($client->gender=='unspecified')
                                    {{trans_choice('core::general.unspecified',1)}}
                                @endif
                                @if($client->gender=='other')
                                    {{trans_choice('core::general.other',1)}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('client::general.marital_status',1)}}</b>
                            <a class="pull-right">
                                @if($client->marital_status=='single')
                                    {{trans_choice('client::general.single',1)}}
                                @endif
                                @if($client->marital_status=='married')
                                    {{trans_choice('client::general.married',1)}}
                                @endif
                                @if($client->marital_status=='divorced')
                                    {{trans_choice('client::general.divorced',1)}}
                                @endif
                                @if($client->marital_status=='widowed')
                                    {{trans_choice('client::general.widowed',1)}}
                                @endif
                                @if($client->marital_status=='other')
                                    {{trans_choice('client::general.other',1)}}
                                @endif
                                @if($client->marital_status=='unspecified')
                                    {{trans_choice('core::general.unspecified',1)}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.zip',1)}}</b>
                            <a class="pull-right">
                                {{$client->zip}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.joined_date',1)}}</b>
                            <a class="pull-right">
                                {{$client->created_date}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.activation_date',1)}}</b>
                            <a class="pull-right">
                                {{$client->activation_date}}
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans_choice('core::general.extra',1)}} {{trans_choice('core::general.detail',2)}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">

                    <strong><i class="fa fa-map-marker margin-r-5"></i> {{trans_choice('core::general.address',1)}}
                    </strong>

                    <p class="text-muted">
                        {{$client->address}}<br>
                        @if(!empty($client->country))
                            {{$client->country->name}}
                        @endif
                    </p>
                    <hr>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> {{trans_choice('core::general.note',2)}}
                    </strong>

                    <p> {{$client->notes}}</p>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#client_identification" data-toggle="tab"
                                          aria-expanded="false">{{trans_choice('client::general.identification',1)}}</a>
                    </li>
                    <li class=""><a href="#client_next_of_kin" data-toggle="tab"
                                    aria-expanded="true">{{trans_choice('client::general.next_of_kin',1)}}</a></li>
                    <li class=""><a href="#login_details" data-toggle="tab"
                                    aria-expanded="false">{{trans_choice('user::general.login',1)}} {{trans_choice('core::general.detail',2)}}</a>
                    </li>
                    <li class=""><a href="#files" data-toggle="tab"
                                    aria-expanded="false">{{trans_choice('client::general.file',2)}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="client_identification">
                        <div>
                            <a href="{{url('client/'.$client->id.'/client_identification/create')}}"
                               class="btn btn-info pull-right margin">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.identification',1)}}</a>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.type',1) }}</th>
                                    <th>{{ trans_choice('core::general.id',1) }}</th>
                                    <th>{{ trans_choice('core::general.description',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($client->identifications as $key)
                                    <tr>
                                        <td>
                                            @if(!empty($key->identification_type))
                                                {{$key->identification_type->name}}
                                            @endif
                                        </td>
                                        <td>{{$key->identification_value}}</td>
                                        <td>{{$key->description}}</td>
                                        <td>
                                            <a href="{{asset('storage/uploads/clients/'.$key->link)}}"
                                               target="_blank"><i class="fa fa-download"></i> </a>
                                            <a href="{{url('client/client_identification/'.$key->id.'/edit')}}"><i
                                                        class="fa fa-edit"></i> </a>
                                            <a href="{{url('client/client_identification/'.$key->id.'/destroy')}}"
                                               class="confirm"><i class="fa fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="client_next_of_kin">
                        <a href="{{url('client/'.$client->id.'/client_next_of_kin/create')}}"
                           class="btn btn-info pull-right margin">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.next_of_kin',1)}}</a>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('core::general.name',1) }}</th>
                                <th>{{ trans('core::general.gender') }}</th>
                                <th>{{ trans('core::general.dob') }}</th>
                                <th>{{ trans('core::general.mobile') }}</th>
                                <th>{{ trans_choice('core::general.email',1) }}</th>
                                <th>{{ trans_choice('client::general.profession',1) }}</th>
                                <th>{{ trans_choice('client::general.relationship',1) }}</th>
                                <th>{{ trans_choice('core::general.address',1) }}</th>
                                <th>{{ trans_choice('core::general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($client->next_of_kins as $key)
                                <tr>
                                    <td>{{$key->first_name}} {{$key->last_name}}</td>
                                    <td>
                                        @if($key->gender=='male')
                                            {{trans_choice('core::general.male',1)}}
                                        @endif
                                        @if($key->gender=='female')
                                            {{trans_choice('core::general.female',1)}}
                                        @endif
                                        @if($key->gender=='unspecified')
                                            {{trans_choice('core::general.unspecified',1)}}
                                        @endif
                                        @if($key->gender=='other')
                                            {{trans_choice('core::general.other',1)}}
                                        @endif
                                    </td>
                                    <td>{{$key->dob}}</td>
                                    <td>{{$key->mobile}}</td>
                                    <td>{{$key->email}}</td>
                                    <td>
                                        @if(!empty($key->profession))
                                            {{$key->profession->name}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($key->client_relationship))
                                            {{$key->client_relationship->name}}
                                        @endif
                                    </td>

                                    <td>{{$key->address}}</td>
                                    <td>
                                        <a href="{{url('client/client_next_of_kin/'.$key->id.'/edit')}}"><i
                                                    class="fa fa-edit"></i> </a>
                                        <a href="{{url('client/client_next_of_kin/'.$key->id.'/destroy')}}"
                                           class="confirm"><i class="fa fa-trash"></i> </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="login_details">
                        <a href="{{url('client/'.$client->id.'/user/create')}}"
                           class="btn btn-info pull-right margin">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.user',1)}}</a>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('core::general.name',1) }}</th>
                                <th>{{ trans_choice('core::general.email',1) }}</th>
                                <th>{{ trans_choice('core::general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($client->client_users as $key)
                                @if($key->user)
                                    <tr>
                                        <td>{{$key->user->first_name}} {{$key->user->last_name}}</td>
                                        <td>{{$key->user->email}}</td>
                                        <td>
                                            <a href="{{url('user/'.$key->user_id.'/edit')}}"
                                               class=""><i class="fa fa-edit"></i> </a>
                                            <a href="{{url('client/user/'.$key->id.'/destroy')}}"
                                               class="confirm"><i class="fa fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="files">
                        <div>
                            <a href="{{url('client/'.$client->id.'/file/create')}}"
                               class="btn btn-info pull-right margin">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.file',1)}}</a>
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans_choice('core::general.name',1) }}</th>
                                    <th>{{ trans_choice('core::general.description',1) }}</th>
                                    <th>{{ trans_choice('core::general.action',1) }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($client->files as $key)
                                    <tr>
                                        <td>{{$key->name}}</td>
                                        <td>{{$key->description}}</td>
                                        <td>
                                            <a href="{{asset('storage/uploads/clients/'.$key->link)}}"
                                               target="_blank"><i class="fa fa-download"></i> </a>
                                            <a href="{{url('client/file/'.$key->id.'/edit')}}"><i
                                                        class="fa fa-edit"></i> </a>
                                            <a href="{{url('client/file/'.$key->id.'/destroy')}}"
                                               class="confirm"><i class="fa fa-trash"></i> </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
    </div>
@endsection
@section('scripts')
    <script>
        $('.data-table').DataTable({
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                },
                "columnDefs": [
                    {"orderable": false, "targets": 0}
                ]
            },
            responsive: true
        });
    </script>
@endsection
