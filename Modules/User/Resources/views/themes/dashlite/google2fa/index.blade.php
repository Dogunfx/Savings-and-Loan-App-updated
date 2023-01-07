@extends('core::layouts.master')
@section('title')
    {{__('user::general.Two Factor Authentication')}}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{__('user::general.Two Factor Authentication')}}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <form method="post" action="{{ route('2fa') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
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
    </div>

@endsection
@section('footer-scripts')
    <script>

    </script>
@endsection
