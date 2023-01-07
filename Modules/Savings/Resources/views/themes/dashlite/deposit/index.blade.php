@extends('core::layouts.master')
@section('title') {{ trans_choice('loan::general.repayment',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{ {{ trans_choice('loan::general.repayment',2) }}</h6>

            <div class="box-tools pull-right hidden">
                <a href="{{ url('repayment/create') }}"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}</a>
            </div>
        </div>
        <div class="box-body">
            <table id="data-table" class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.entry',1) }} {{ trans_choice('core::general.id',1) }}</th>
                    <th>{{ trans_choice('core::general.branch',1) }}</th>
                    <th>{{ trans_choice('core::general.transaction',1) }} {{ trans_choice('core::general.date',1) }}</th>
                    <th>{{ trans_choice('core::general.transaction',1) }}#</th>
                    <th>{{ trans_choice('core::general.type',1) }}</th>
                    <th>{{ trans_choice('core::general.created_by',1) }}</th>
                    <th>{{ trans_choice('core::general.account',1) }}</th>
                    <th>{{ trans_choice('accounting::general.debit',1) }}</th>
                    <th>{{ trans_choice('accounting::general.credit',1) }}</th>
                    <th>{{ trans_choice('core::general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        var url = '{!! url('journal_entry/get_journal_entries') !!}';
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [
                {data: 'id', name: 'id'},
                {data: 'branch', name: 'branches.name'},
                {data: 'date', name: 'date'},
                {data: 'transaction_number', name: 'transaction_number'},
                {data: 'account_type', name: 'chart_of_accounts.account_type'},
                {data: 'created_by', name: 'users.first_name'},
                {data: 'account_name', name: 'chart_of_accounts.name'},
                {data: 'debit', name: 'debit'},
                {data: 'credit', name: 'credit'},
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
