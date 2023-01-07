<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title')</title>
        <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap/dist/css/bootstrap.min.css') }}">
        <link href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet" type="text/css"/>
        <link href="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.css') }}" rel="stylesheet" type="text/css"/>
        <style>
            .master {
                background-image: url({{asset('installer/img/background.png')}});
                background-size: cover;
                background-position: top;
                min-height: 100vh;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                -webkit-justify-content: center;
                -ms-flex-pack: center;
                justify-content: center;
                -webkit-align-items: center;
                -ms-flex-align: center;
                align-items: center
            }

            .box {
                width: 450px;
                border-radius: 0 0 3px 3px;
                overflow: hidden;
                box-sizing: border-box;
                box-shadow: 0 10px 10px rgba(0, 0, 0, .19), 0 6px 3px rgba(0, 0, 0, .23);
                margin-left: auto;
                margin-right: auto;

            }

            .main {
                margin-top: -20px;
                background-color: #fff;
                border-radius: 0 0 3px 3px;
                padding: 40px 40px 30px
            }

            .header {
                background-color: #357295;
                padding: 30px 30px 40px;
                border-radius: 3px 3px 0 0;
                text-align: center
            }

            .header__step {
                font-weight: 300;
                text-transform: uppercase;
                font-size: 14px;
                letter-spacing: 1.1px;
                margin: 0 0 10px
            }

            .header__step, .header__title {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
                color: #fff
            }

            .header__title {
                font-weight: 400;
                font-size: 20px;
                margin: 0 0 15px
            }

            .step {
                padding-left: 0;
                list-style: none;
                margin-bottom: 0;
                display: -webkit-flex;
                display: -ms-flexbox;
                display: flex;
                -webkit-flex-direction: row-reverse;
                -ms-flex-direction: row-reverse;
                flex-direction: row-reverse;
                -webkit-justify-content: center;
                -ms-flex-pack: center;
                justify-content: center;
                -webkit-align-items: center;
                -ms-flex-align: center;
                align-items: center;
                margin-top: -20px
            }

            .badge2 {
                background-color: #e97449 !important;
            }

            .badge1 {
                background-color: #118d32 !important;
            }
        </style>

    </head>
    <body class="master">
    <div class="box">
        <div class="header">
            <h1 class="header__title">@yield('title')</h1>
        </div>
        <div class="main">
            <div class="row">
                <div class="col-md-12">

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-toastr/toastr.min.js') }}" type="text/javascript"></script>
    <script>toastr.options.escapeHtml = false;</script>

    @if(Session::has('flash_notification.message'))
        <script>toastr.{{ Session::get('flash_notification.level') }}('{{ Session::get("flash_notification.message") }}', 'Response Status')</script>
    @endif
    @if(count($errors) > 0)
        <?php
        $errors_list = "<ul>";
        foreach ($errors->all() as $error) {
            $errors_list .= '<li>' . $error . '</li>';
        }
        $errors_list .= "</ul>";

        ?>
        <script>toastr.error("{!!  $errors_list !!} ")</script>
    @endif
    </body>
</html>
