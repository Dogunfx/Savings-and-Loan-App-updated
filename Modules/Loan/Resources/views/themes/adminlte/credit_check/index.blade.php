@extends('core::layouts.master')
@section('title')
    {{ trans_choice('loan::general.credit',1) }}  {{ trans_choice('loan::general.check',2) }}
@endsection
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/plugins/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
@stop
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('loan::general.credit',1) }}  {{ trans_choice('loan::general.check',2) }}</h3>

            <div class="box-tools pull-right">


            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-striped table-hover table-condensed" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('core::general.id',1) }}</th>
                    <th>{{ trans_choice('core::general.name',1) }}</th>
                    <th>{{ trans_choice('loan::general.security_level',1) }}</th>
                    <th>{{ trans_choice('loan::general.rating_type',1) }}</th>
                    <th>{{ trans('core::general.active') }}</th>
                    <th>{{ trans_choice('core::general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>{{$key->id}}</td>
                        <td>{{$key->translated_name}}</td>
                        <td>
                            @if($key->security_level=='block')
                                {{ trans_choice('loan::general.block',1) }} {{ trans_choice('loan::general.loan',1) }}
                            @endif
                            @if($key->security_level=='pass')
                                {{ trans_choice('loan::general.pass',1) }}
                            @endif
                            @if($key->security_level=='warning')
                                {{ trans_choice('loan::general.warning',1) }}
                            @endif
                        </td>
                        <td>
                            @if($key->rating_type=='boolean')
                                {{ trans_choice('loan::general.boolean',1) }}
                            @endif
                            @if($key->rating_type=='score')
                                {{ trans_choice('loan::general.score',1) }}
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
                                    @can('loan.loans.credit_checks.edit')
                                        <li><a href="{{url('loan/credit_check/' . $key->id . '/edit') }}"
                                               class="">{{ trans_choice('core::general.edit', 2) }}</a></li>
                                    @endcan
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
            responsive: false
        });
    </script>
@endsection
