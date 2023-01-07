@extends('core::layouts.master')
@section('title')
    {{ trans_choice('savings::general.savings',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('savings::general.savings',2) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.id',1) }}</th>
                    <th>{{ trans_choice('core::general.branch',1) }}</th>
                    <th>{{ trans_choice('savings::general.savings_officer',1) }}</th>
                    <th>{{ trans_choice('savings::general.interest_rate',1) }}</th>
                    <th>{{ trans_choice('savings::general.balance',1) }}</th>
                    <th>{{ trans_choice('savings::general.status',1) }}</th>
                    <th>{{ trans_choice('savings::general.product',1) }}</th>
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
            ajax: '{!! url('portal/savings/get_savings?status='.$status) !!}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'branch', name: 'branches.name'},
                {data: 'savings_officer', name: 'users.first_name'},
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
