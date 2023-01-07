@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.charge',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('loan::general.loan',1) }}  {{ trans_choice('loan::general.charge',2) }}</h3>

            <div class="box-tools pull-right">
                <a href="{{ url('loan/charge/create') }}" class="btn btn-info btn-sm">
                    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.charge',1) }}
                </a>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('loan::general.charge',1) }} {{ trans_choice('core::general.type',1) }}</th>
                    <th>{{ trans_choice('core::general.amount',1) }}</th>
                    <th>{{ trans('core::general.active') }}</th>
                    <th>{{ trans_choice('core::general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{$key->name}}</td>
                        <td>
                            @if($key->loan_charge_type_id==1)
                                {{ trans_choice('loan::general.disbursement',1) }}
                            @endif
                            @if($key->loan_charge_type_id==2)
                                {{ trans_choice('loan::general.specified_due_date',1) }}
                            @endif
                            @if($key->loan_charge_type_id==3)
                                {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',2) }}
                            @endif
                            @if($key->loan_charge_type_id==4)
                                {{ trans_choice('loan::general.overdue',1) }} {{ trans_choice('loan::general.installment',1) }} {{ trans_choice('loan::general.fee',1) }}
                            @endif
                            @if($key->loan_charge_type_id==5)
                                {{ trans_choice('loan::general.disbursement_paid_with_repayment',1) }}
                            @endif
                            @if($key->loan_charge_type_id==6)
                                {{ trans_choice('loan::general.loan_rescheduling_fee',1) }}
                            @endif
                            @if($key->loan_charge_type_id==7)
                                {{ trans_choice('loan::general.overdue_on_loan_maturity',1) }}
                            @endif
                            @if($key->loan_charge_type_id==8)
                                {{ trans_choice('loan::general.last_installment_fee',1) }}
                            @endif
                        </td>
                        <td>
                            @if($key->loan_charge_option_id==1)
                                {{number_format($key->amount,2)}} {{ trans_choice('loan::general.flat',1) }}
                            @endif
                            @if($key->loan_charge_option_id==2)
                                {{number_format($key->amount,2)}}
                                % {{ trans_choice('loan::general.principal_due_on_installment',1) }}
                            @endif
                            @if($key->loan_charge_option_id==3)
                                {{number_format($key->amount,2)}}
                                %  {{ trans_choice('loan::general.principal_interest_due_on_installment',1) }}
                            @endif
                            @if($key->loan_charge_option_id==4)
                                {{number_format($key->amount,2)}}
                                % {{ trans_choice('loan::general.interest_due_on_installment',1) }}
                            @endif
                            @if($key->loan_charge_option_id==5)
                                {{number_format($key->amount,2)}}
                                %  {{ trans_choice('loan::general.total_outstanding_loan_principal',1) }}
                            @endif
                            @if($key->loan_charge_option_id==6)
                                {{number_format($key->amount,2)}}
                                % {{ trans_choice('loan::general.percentage_of_original_loan_principal_per_installment',1) }}
                            @endif
                            @if($key->loan_charge_option_id==7)
                                {{number_format($key->amount,2)}}
                                % {{ trans_choice('loan::general.original_loan_principal',1) }}
                            @endif
                        </td>
                        <td>
                            @if($key->active==1)
                                {{ trans_choice('core::general.yes',1) }}
                            @else
                                {{ trans_choice('core::general.no',1) }}
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown"
                                        aria-expanded="true"><i class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li><a href="{{url('loan/charge/' . $key->id . '/edit') }}"
                                           class="">{{ trans_choice('core::general.edit', 2) }}</a></li>
                                    <li><a href="{{url('loan/charge/' . $key->id . '/destroy') }}"
                                           class="confirm">{{ trans_choice('core::general.delete', 2) }}</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $('#data-table').DataTable({
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
