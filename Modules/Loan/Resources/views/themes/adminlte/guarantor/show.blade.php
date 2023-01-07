@extends('core::layouts.master')
@section('title')
    {{ $loan_guarantor->first_name }} {{ $loan_guarantor->last_name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    @if(!empty($loan_guarantor->photo))
                        <a href="{{asset('storage/uploads/clients/'.$loan_guarantor->photo)}}" class="fancybox"> <img
                                    class="profile-user-img img-responsive img-circle"
                                    src="{{asset('storage/uploads/clients/'.$loan_guarantor->photo)}}"
                                    alt="User profile picture">
                        </a>
                    @else
                        <img class="profile-user-img img-responsive img-circle"
                             src="{{asset('assets/dist/img/user.png')}}"
                             alt="User profile picture">
                    @endif
                    <h3 class="profile-username text-center">
                        @if(!empty($loan_guarantor->title))
                            {{$loan_guarantor->title->name}}
                        @endif
                        {{$loan_guarantor->first_name}} {{$loan_guarantor->last_name}}</h3>

                    <p class="text-muted text-center">
                        @if(!empty($loan_guarantor->profession->name))
                            {{$loan_guarantor->profession->name}}
                        @endif
                    </p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{trans_choice('client::general.relationship',1)}}</b>
                            <a class="pull-right">
                                @if(!empty($loan_guarantor->client_relationship))
                                    {{$loan_guarantor->client_relationship->name}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.mobile',1)}}</b>
                            <a class="pull-right">
                                {{$loan_guarantor->mobile}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.email',1)}}</b>
                            <a class="pull-right">
                                {{$loan_guarantor->email}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.dob',1)}}</b>
                            <a class="pull-right">
                                {{$loan_guarantor->dob}}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.gender',1)}}</b>
                            <a class="pull-right">
                                @if($loan_guarantor->gender=='male')
                                    {{trans_choice('core::general.male',1)}}
                                @endif
                                @if($loan_guarantor->gender=='female')
                                    {{trans_choice('core::general.female',1)}}
                                @endif
                                @if($loan_guarantor->gender=='unspecified')
                                    {{trans_choice('core::general.unspecified',1)}}
                                @endif
                                @if($loan_guarantor->gender=='other')
                                    {{trans_choice('core::general.other',1)}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('client::general.marital_status',1)}}</b>
                            <a class="pull-right">
                                @if($loan_guarantor->marital_status=='single')
                                    {{trans_choice('client::general.single',1)}}
                                @endif
                                @if($loan_guarantor->marital_status=='married')
                                    {{trans_choice('client::general.married',1)}}
                                @endif
                                @if($loan_guarantor->marital_status=='divorced')
                                    {{trans_choice('client::general.divorced',1)}}
                                @endif
                                @if($loan_guarantor->marital_status=='widowed')
                                    {{trans_choice('client::general.widowed',1)}}
                                @endif
                                @if($loan_guarantor->marital_status=='other')
                                    {{trans_choice('client::general.other',1)}}
                                @endif
                                @if($loan_guarantor->marital_status=='unspecified')
                                    {{trans_choice('core::general.unspecified',1)}}
                                @endif
                            </a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('core::general.zip',1)}}</b>
                            <a class="pull-right">
                                {{$loan_guarantor->zip}}
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
                        {{$loan_guarantor->address}}<br>
                        @if(!empty($loan_guarantor->country))
                            {{$loan_guarantor->country->name}}
                        @endif
                    </p>
                    <hr>

                    <strong><i class="fa fa-file-text-o margin-r-5"></i> {{trans_choice('core::general.note',2)}}
                    </strong>

                    <p> {{$loan_guarantor->notes}}</p>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
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
