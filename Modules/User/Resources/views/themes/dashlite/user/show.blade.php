@extends('core::layouts.master')
@section('title')
    {{ $user->first_name }} {{ $user->last_name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h6 class="box-title">{{ $user->first_name }} {{ $user->last_name }}</h6>
                    <div class="heading-elements">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{ trans('general.gender') }}</td>
                            <td>
                                @if($user->gender=='male')
                                    {{ trans('core::general.male') }}
                                @endif
                                @if($user->gender=='female')
                                    {{ trans('core::general.female') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.email',1) }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.phone') }}</td>
                            <td>{{ $user->phone }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.address') }}</td>
                            <td>{!!   $user->address !!}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.created_at') }}</td>
                            <td>{{ $user->created_at }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.updated_at') }}</td>
                            <td>{{ $user->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans_choice('general.note',2) }}</h3>
                    <div class="heading-elements">

                    </div>
                </div>
                <div class="box-body">
                    {!!   $user->notes !!}
                </div>
            </div>
        </div>
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
            responsive: true,
        });
    </script>
@endsection
