@extends('admin.layouts.master')
@section('title')
    {{ $user->first_name }} {{ $user->last_name }}
@endsection
@section('styles')
@stop
@section('breadcrumbs')
    <div class="kt-subheader   kt-grid__item" id="kt_subheader">
        <div class="kt-container  kt-container--fluid ">
            <div class="kt-subheader__main">
                <h3 class="kt-subheader__title">
                    {{ $user->first_name }} {{ $user->last_name }}
                </h3>
                <span class="kt-subheader__separator kt-hidden"></span>
                <div class="kt-subheader__breadcrumbs">
                    <a href="{{url('dashboard')}}" class="kt-subheader__breadcrumbs-home"><i
                                class="flaticon2-shelter"></i></a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                    <a href="#" class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">
                        {{ $user->first_name }} {{ $user->last_name }}
                    </a>
                    <span class="kt-subheader__breadcrumbs-separator"></span>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">
        @include('partials.user_profile')
        <div class="row">

            <div class="col-lg-9">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills navtab-bg nav-justified">
                            <li class="nav-item">
                                <a href="#notes" data-toggle="tab" aria-expanded="false" class="nav-link active">
                                    <span class="d-block d-sm-none"><i class="uil-home-alt"></i></span>
                                    <span class="d-none d-sm-block">{{ trans_choice('general.note',2) }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#activity_log" data-toggle="tab" aria-expanded="true" class="nav-link">
                                    <span class="d-block d-sm-none"><i class="uil-user"></i></span>
                                    <span class="d-none d-sm-block">{{ trans_choice('general.activity_log',2) }}</span>
                                </a>
                            </li>

                        </ul>
                        <div class="tab-content text-muted">
                            <div class="tab-pane active show" id="notes">
                                <p>{{$user->notes}}</p>
                            </div>
                            <div class="tab-pane" id="activity_log">
                                <table class="table  table-striped table-hover" id="activity-log-data-table">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('general.user',1) }}</th>
                                        <th>{{ trans_choice('general.description',1) }}</th>
                                        <th>{{ trans_choice('general.created_at',1) }}</th>
                                        <th>{{ trans_choice('general.action',1) }}</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $('#activity-log-data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url('activity_log/get_activity_logs?causer_id='.Auth::id()) !!}',
            columns: [
                {data: 'user', name: 'users.first_name'},
                {data: 'description', name: 'description', orderable: false},
                {data: 'created_at', name: 'created_at', orderable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            "order": [[2, "desc"]],
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
            autoWidth: false
        });
    </script>
@endsection
