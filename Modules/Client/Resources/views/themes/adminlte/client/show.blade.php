@extends('core::layouts.master')
@section('title')
    {{ $client->first_name }} {{ $client->last_name }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ $client->first_name }} {{ $client->last_name }}
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
                                    href="{{url('client')}}">{{ trans_choice('client::general.client',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $client->first_name }} {{ $client->last_name }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="row">
            <div class="col-md-3">
                <div class="card card-bordered card-preview">
                    <div class="card-body box-profile">

                        <div class="text-center">
                            @if(!empty($client->photo))
                                <a href="{{asset('storage/uploads/clients/'.$client->photo)}}"
                                   class="fancybox">
                                    <img
                                            class="profile-user-img img-fluid img-circle"
                                            src="{{asset('storage/uploads/clients/'.$client->photo)}}"
                                            alt="User profile picture">
                                </a>
                            @else
                                <img class="profile-user-img img-fluid img-circle"
                                     src="{{asset('themes/adminlte/img/user.png')}}"
                                     alt="User profile picture">
                            @endif
                        </div>
                        <h3 class="profile-username text-center">
                            @if(!empty($client->title))
                                {{$client->title->name}}
                            @endif
                            {{$client->name}}
                        </h3>
                        @if(!empty($client->profession->name))
                            <p class="text-muted text-center">{{$client->profession->name}}</p>
                        @endif
                        <p class="text-muted text-center">#{{$client->id}}</p>
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.branch',1)}}
                                </b>
                                <a class="float-right">
                                    @if(!empty($client->branch))
                                        {{$client->branch->name}}
                                    @endif
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.status',1)}}
                                </b>
                                <a class="float-right">
                                    <a class="float-right" data-toggle="modal"
                                       data-target="#change_status_modal" href="#">
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
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.external_id',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->external_id}}
                                </a>
                            </li>
                             <li class="list-group-item">
                                <b>
                                    {{trans_choice('savings::general.account_number',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->account_number}}
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.type',1)}}
                                </b>
                                <a class="float-right">
                                    @if(!empty($client->client_type))
                                        {{$client->client_type->name}}
                                    @endif
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.staff',1)}}
                                </b>
                                <a class="float-right">
                                    @if(!empty($client->loan_officer))
                                        {{$client->loan_officer->first_name}} {{$client->loan_officer->last_name}}
                                    @endif
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.mobile',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->mobile}}
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.email',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->email}}
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.dob',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->dob}}
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.gender',1)}}
                                </b>
                                <a class="float-right">
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
                                <b>
                                    {{trans_choice('client::general.marital_status',1)}}
                                </b>
                                <a class="float-right">
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
                                <b>
                                    {{trans_choice('core::general.zip',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->zip}}
                                </a>
                            </li>
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.joined_date',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->created_date}}
                                </a>
                            </li>
                            @foreach($custom_fields as $custom_field)
                                <?php
                                $field = custom_field_build_form_field($custom_field, $client->id);
                                ?>
                                <li class="list-group-item">
                                    <b>
                                        {{$field['label']}}
                                    </b>
                                    <a class="float-right">
                                        @if($custom_field->type=='checkbox')
                                            @foreach(explode(',',$field['current'] ) as $key)
                                                {{$key}}<br>
                                            @endforeach
                                        @else
                                            {{$field['current'] }}
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                            <li class="list-group-item">
                                <b>
                                    {{trans_choice('core::general.activation_date',1)}}
                                </b>
                                <a class="float-right">
                                    {{$client->activation_date}}
                                </a>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-center">
                            @can('client.clients.activate')
                                <a href="#" data-toggle="modal" class="btn btn-primary btn-sm  m-1"
                                   data-target="#change_status_modal">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{trans_choice('client::general.change',1)}} {{trans_choice('core::general.status',1)}}</span>
                                </a>
                            @endcan
                            @can('client.clients.edit')
                                <a href="{{url('client/' . $client->id . '/edit')}}"
                                   class="btn btn-primary btn-sm  m-1">
                                    <i class="fas fa-edit"></i>
                                    <span>{{trans_choice('core::general.edit',1)}}</span>
                                </a>

                                <a href="#" data-toggle="modal"
                                   data-target="#transfer_client_modal" class="btn btn-primary btn-sm m-1"><i
                                            class="fas fa-forward"></i>
                                    <span>{{trans_choice('client::general.transfer',1)}}</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card">
                    <!-- /.card-header -->
                    <div class="card-body">

                        <strong><i class="fas fa-map-marker-alt mr-1"></i> {{trans_choice('core::general.address',1)}}
                        </strong>

                        <p class="text-muted">
                            {{$client->address}}<br>
                            @if(!empty($client->country))
                                {{$client->country->name}}
                            @endif
                        </p>

                        <hr>

                        <strong><i class="far fa-file-alt mr-1"></i> {{trans_choice('core::general.note',2)}}</strong>

                        <p class="text-muted"> {{$client->notes}}</p>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#accounts" data-toggle="tab"
                                   aria-expanded="false">{{trans_choice('client::general.account',2)}}
                                </a>
                            </li>
                            @can('client.clients.identification.index')
                                <li class="nav-item">
                                    <a class="nav-link" href="#client_identification" data-toggle="tab"
                                       aria-expanded="false">{{trans_choice('client::general.identification',1)}}</a>
                                </li>
                            @endcan
                            @can('client.clients.next_of_kin.index')
                                <li class="nav-item">
                                    <a class="nav-link" href="#client_next_of_kin" data-toggle="tab"
                                       aria-expanded="true">{{trans_choice('client::general.next_of_kin',1)}}</a>
                                </li>
                            @endcan
                            @can('client.clients.index')
                                <li class="nav-item">
                                    <a class="nav-link" href="#login_details" data-toggle="tab"
                                       aria-expanded="false">{{trans_choice('user::general.login',1)}} {{trans_choice('core::general.detail',2)}}</a>
                                </li>
                            @endcan
                            @can('client.clients.files.index')
                                <li class="nav-item">
                                    <a class="nav-link" href="#files" data-toggle="tab"
                                       aria-expanded="false">{{trans_choice('client::general.file',2)}}</a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="accounts">
                                @can('loan.loans.index')
                                    <h4>{{ trans_choice('loan::general.loan',2) }}</h4>
                                    <table class="table  table-striped table-hover table-condensed"
                                           id="loan-data-table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.id',1) }}</th>
                                            <th>{{ trans_choice('core::general.amount',1) }}</th>
                                            <th>{{ trans_choice('loan::general.balance',1) }}</th>
                                            <th>{{ trans('loan::general.disbursed') }}</th>
                                            <th>{{ trans_choice('loan::general.status',1) }}</th>
                                            <th>{{ trans_choice('loan::general.product',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                @endcan
                                @can('savings.savings.index')
                                    <h4>{{ trans_choice('savings::general.savings',2) }}</h4>
                                    <table class="table  table-striped table-hover table-condensed"
                                           id="savings-data-table">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('core::general.id',1) }}</th>
                                            <th>{{ trans_choice('savings::general.interest_rate',1) }}</th>
                                            <th>{{ trans_choice('savings::general.balance',1) }}</th>
                                            <th>{{ trans_choice('savings::general.status',1) }}</th>
                                            <th>{{ trans_choice('savings::general.product',1) }}</th>
                                            <th>{{ trans_choice('core::general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                @endcan
                            </div>
                            @can('client.clients.identification.index')
                                <div class="tab-pane" id="client_identification">
                                    <div>
                                        @can('client.clients.identification.create')
                                            <a href="{{url('client/'.$client->id.'/client_identification/create')}}"
                                               class="btn btn-info float-right mb-2">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.identification',1)}}</a>
                                        @endcan
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
                                                        @can('client.clients.identification.edit')
                                                            <a href="{{url('client/client_identification/'.$key->id.'/edit')}}"><i
                                                                        class="fas fa-edit"></i> </a>
                                                        @endcan
                                                        @can('client.clients.identification.destroy')
                                                            <a href="{{url('client/client_identification/'.$key->id.'/destroy')}}"
                                                               class="confirm"><i class="fas fa-trash"></i> </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            @endcan
                        <!-- /.tab-pane -->
                            @can('client.clients.next_of_kin.index')
                                <div class="tab-pane" id="client_next_of_kin">
                                    @can('client.clients.next_of_kin.create')
                                        <a href="{{url('client/'.$client->id.'/client_next_of_kin/create')}}"
                                           class="btn btn-info float-right mb-2">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.next_of_kin',1)}}</a>
                                    @endcan
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
                                                    @can('client.clients.next_of_kin.edit')
                                                        <a href="{{url('client/client_next_of_kin/'.$key->id.'/edit')}}"><i
                                                                    class="fas fa-edit"></i> </a>
                                                    @endcan
                                                    @can('client.clients.next_of_kin.destroy')
                                                        <a href="{{url('client/client_next_of_kin/'.$key->id.'/destroy')}}"
                                                           class="confirm"><i class="fas fa-trash"></i> </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endcan
                            <div class="tab-pane" id="login_details">
                                @can('client.clients.user.create')
                                    <a href="{{url('client/'.$client->id.'/user/create')}}"
                                       class="btn btn-info float-right mb-2">{{trans_choice('core::general.add',1)}} {{trans_choice('core::general.user',1)}}</a>
                                @endcan
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
                                                    @can('client.clients.user.create')
                                                        <a href="{{url('user/'.$key->user_id.'/edit')}}"
                                                           class=""><i class="fas fa-edit"></i> </a>
                                                    @endcan
                                                    @can('client.clients.user.destroy')
                                                        <a href="{{url('client/user/'.$key->id.'/destroy')}}"
                                                           class="confirm"><i class="fas fa-trash"></i> </a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @can('client.clients.files.index')
                                <div class="tab-pane" id="files">
                                    <div>
                                        @can('client.clients.files.index')
                                            <a href="{{url('client/'.$client->id.'/file/create')}}"
                                               class="btn btn-info float-right mb-2">{{trans_choice('core::general.add',1)}} {{trans_choice('client::general.file',1)}}</a>
                                        @endcan
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
                                                        @can('client.clients.files.edit')
                                                            <a href="{{url('client/file/'.$key->id.'/edit')}}"><i
                                                                        class="fas fa-edit"></i> </a>
                                                        @endcan
                                                        @can('client.clients.files.destroy')
                                                            <a href="{{url('client/file/'.$key->id.'/destroy')}}"
                                                               class="confirm"><i class="fas fa-trash"></i> </a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.col -->
        </div>
        <div class="modal fade in" id="change_status_modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{ trans_choice('client::general.change',1) }} {{ trans_choice('core::general.status',1) }}</h4>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>×</span></button>
                    </div>
                    <form method="post"
                          action="{{ url('client/'.$client->id.'/change_status') }}"
                          class="form-horizontal">
                        {{csrf_field()}}
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="status"
                                       class="control-label">{{ trans_choice('core::general.status',1) }}</label>
                                <select class="form-control" name="status" id="status">
                                    <option value=""></option>
                                    <option value="pending"
                                            @if($client->status=="pending") selected @endif>{{trans_choice("client::general.pending",1)}}</option>
                                    <option value="active"
                                            @if($client->status=="active") selected @endif>{{trans_choice("client::general.active",1)}}</option>
                                    <option value="inactive"
                                            @if($client->status=="inactive") selected @endif>{{trans_choice("client::general.inactive",1)}}</option>
                                    <option value="closed"
                                            @if($client->status=="closed") selected @endif>{{trans_choice("client::general.closed",1)}}</option>
                                    <option value="deceased"
                                            @if($client->status=="deceased") selected @endif>{{trans_choice("client::general.deceased",1)}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status_date"
                                       class="control-label">{{ trans_choice('core::general.date',1) }}</label>
                                <input type="text" name="date"
                                       class="form-control date-picker"
                                       value="{{date("Y-m-d")}}"
                                       required=""
                                       id="status_date">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default float-left"
                                    data-dismiss="modal">
                                {{ trans_choice('core::general.close',1) }}
                            </button>
                            <button type="submit"
                                    class="btn btn-primary float-right">{{ trans_choice('core::general.save',1) }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

    <script>
        $(document).ready(function () {


            $('#savings-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! url('savings/get_savings?client_id='.$client->id) !!}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'interest_rate', name: 'interest_rate'},
                    {data: 'balance', name: 'balance'},
                    {data: 'status', name: 'status'},
                    {data: 'savings_product', name: 'savings_products.name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "order": [[0, "desc"]],
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
                    }
                },
                responsive: false,
                "autoWidth": false,
                "drawCallback": function (settings) {
                    $('.confirm').on('click', function (e) {
                        e.preventDefault();
                        var href = $(this).attr('href');
                        swal({
                            title: 'Are you sure?',
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'Cancel'
                        }).then(function () {
                            window.location = href;
                        })
                    });
                }
            });
            $('#loan-data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! url('loan/get_loans?client_id='.$client->id) !!}',
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'principal', name: 'principal'},
                    {data: 'balance', name: 'balance', orderable: false},
                    {data: 'disbursed_on_date', name: 'disbursed_on_date'},
                    {data: 'status', name: 'status'},
                    {data: 'loan_product', name: 'loan_products.name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "order": [[0, "desc"]],
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
                    }
                },
                responsive: false,
                "drawCallback": function (settings) {
                    $('.confirm').on('click', function (e) {
                        e.preventDefault();
                        var href = $(this).attr('href');
                        swal({
                            title: 'Are you sure?',
                            text: '',
                            type: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            cancelButtonText: 'Cancel'
                        }).then(function () {
                            window.location = href;
                        })
                    });
                }
            });
        })
    </script>

@endsection
