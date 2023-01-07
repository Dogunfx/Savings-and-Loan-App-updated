@extends('layouts.master')
@section('title')
    {{trans_choice('general.two_factor',1)}} {{trans_choice('general.authentication',1)}}
@endsection
@section('content')
    <div class="panel panel-white">
        <div class="panel-heading">
            <h6 class="panel-title">  {{trans_choice('general.two_factor',1)}} {{trans_choice('general.authentication',1)}}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <div class="panel-body ">
            <p>{{trans_choice('general.2fa_instruction',1)}} {{ $secret }}</p>
            <div>
                <img src="{{ $qr_image }}">
            </div>
            <div>
                @if(Auth::user()->enable_google2fa==1 && !empty(Auth::user()->google2fa_secret))
                    <form method="post" action="{{url('user/google_auth/disable')}}" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="password"
                                   class="col-sm-2 control-label">{{trans_choice('general.password',1)}}</label>
                            <div class="col-sm-10">
                                <input type="password" name="password" class="form-control"
                                       placeholder="{{trans_choice('general.password',1)}}"
                                       value="{{old('password')}}"
                                       required id="password">
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-danger col-md-offset-2">{{trans_choice('general.disable',1)}}</button>

                    </form>
                @else
                    <form method="post" action="{{url('user/google_auth/enable')}}" class="form-horizontal">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="google_app_code"
                                   class="col-sm-2 control-label">{{trans_choice('general.google_app_code',1)}}</label>
                            <div class="col-sm-10">
                                <input type="number" name="google_app_code" class="form-control"
                                       placeholder="{{trans_choice('general.google_app_code',1)}}"
                                       value="{{old('google_app_code')}}"
                                       required id="google_app_code">
                            </div>
                        </div>

                        <button type="submit"
                                class="btn btn-primary col-md-offset-2">{{trans_choice('general.enable',1)}}</button>

                    </form>
                @endif
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            "order": [[6, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
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
