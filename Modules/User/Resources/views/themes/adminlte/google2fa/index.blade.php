@extends('core::layouts.master')
@section('title')
    {{__('user::general.Two Factor Authentication')}}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{__('user::general.Two Factor Authentication')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{__('user::general.Two Factor Authentication')}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <form method="post" action="{{ route('2fa') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="form-group" id="">
                        <label for="google_app_code"
                               class="">{{__('user::general.Google App Code')}}</label>
                        <input type="number" name="google_app_code"
                               class="form-control"
                               id="google_app_code" required>
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </section>
@endsection
@section('scripts')
    <script>

    </script>
@endsection
