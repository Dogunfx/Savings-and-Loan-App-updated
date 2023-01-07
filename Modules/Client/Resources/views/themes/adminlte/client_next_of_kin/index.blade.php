@extends('core::layouts.master')
@section('title')
    {{ trans_choice('client::general.client',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('client::general.client',2) }}</h3>

            <div class="box-tools pull-right">

                <a href="{{ url('client/create') }}" class="btn btn-info btn-sm">
                    {{ trans_choice('core::general.add',1) }} {{ trans_choice('client::general.client',1) }}
                </a>

            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('core::general.system',1) }} {{ trans_choice('core::general.id',1) }}</th>
                    <th>{{ trans_choice('core::general.external_id',1) }}</th>
                    <th>{{ trans('core::general.gender') }}</th>
                    <th>{{ trans('core::general.mobile') }}</th>
                    <th>{{ trans_choice('core::general.status',1) }}</th>
                    <th>{{ trans_choice('core::general.branch',1) }}</th>
                    <th>{{ trans_choice('core::general.staff',1) }}</th>
                    <th>{{ trans_choice('core::general.action',1) }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url('client/get_clients') !!}',
            columns: [
                {data: 'name', name: 'clients.first_name'},
                {data: 'id', name: 'id'},
                {data: 'external_id', name: 'external_id'},
                {data: 'gender', name: 'gender'},
                {data: 'mobile', name: 'mobile'},
                {data: 'status', name: 'status'},
                {data: 'branch', name: 'branches.name'},
                {data: 'staff', name: 'users.first_name'},
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
            responsive: false
        });
    </script>
@endsection
