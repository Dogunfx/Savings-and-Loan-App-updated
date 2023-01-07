@extends('core::layouts.master')
@section('title')
    {{ trans_choice('communication::general.sms',1) }}  {{ trans_choice('communication::general.gateway',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('communication::general.sms',1) }}  {{ trans_choice('communication::general.gateway',2) }}</h3>

            <div class="box-tools pull-right">
                @can('communication.sms_gateways.create')
                    <a href="{{ url('communication/sms_gateway/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.gateway',1) }}
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-bordered table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('core::general.active',1) }}</th>
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
            ajax: '{!! url('communication/sms_gateway/get_sms_gateways') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'active', name: 'active'},
                {data: 'action', name: 'action',searchable:false,orderable:false},
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
            "autoWidth": false,
            "drawCallback": function( settings ) {
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
    </script>
@endsection
