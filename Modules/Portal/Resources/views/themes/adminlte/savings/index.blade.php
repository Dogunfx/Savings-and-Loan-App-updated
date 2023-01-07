@extends('core::layouts.master')
@section('title')
    {{ trans_choice('savings::general.savings',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('savings::general.savings',2) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('savings::general.savings',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card ">
            <div class="card-body table-responsive">
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
    </section>
@endsection
@section('scripts')
  <script>
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! url('portal/savings/get_savings?status='.request('status')) !!}',
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
    </script>
@endsection
