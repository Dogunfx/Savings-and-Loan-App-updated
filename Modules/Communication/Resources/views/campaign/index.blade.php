@extends('core::layouts.master')
@section('title')
    {{ trans_choice('communication::general.campaign',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{ trans_choice('communication::general.campaign',2) }}</h3>

            <div class="box-tools pull-right">
                @can('communication.campaigns.create')
                    <a href="{{ url('communication/campaign/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('communication::general.campaign',1) }}
                    </a>
                @endcan
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-bordered table-hover" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('core::general.type',1) }}</th>
                    <th>{{ trans_choice('core::general.created_by',1) }}</th>
                    <th>{{ trans_choice('communication::general.campaign',1) }} {{ trans_choice('core::general.type',1) }}</th>
                    <th>{{ trans_choice('core::general.status',1) }}</th>
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
            ajax: '{!! url('communication/campaign/get_communication_campaigns') !!}',
            columns: [
                {data: 'name', name: 'name'},
                {data: 'campaign_type', name: 'campaign_type'},

                {data: 'created_by', name: 'users.first_name'},
                {data: 'trigger_type', name: 'trigger_type'},
                {data: 'status', name: 'status'},
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
