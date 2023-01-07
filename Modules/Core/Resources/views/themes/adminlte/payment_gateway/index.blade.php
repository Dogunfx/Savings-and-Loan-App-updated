@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.gateway',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.gateway',2) }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                    href="{{url('dashboard')}}">{{ trans_choice('dashboard::general.dashboard',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.gateway',2) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card card-bordered card-preview">
            <div class="card-body">
                @if(count($data))

                    <ul class="nav nav-tabs">
                        <?php $count = 1; ?>
                        @foreach($data as $key)
                            <li class="nav-item">
                                <a href="#{{ $key->getName() }}"
                                   data-toggle="tab" class="nav-link @if($count==1) active @endif">
                                    {{ $key->getName() }}
                                </a>
                            </li>
                            <?php $count++; ?>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        <?php $count = 1; ?>
                        @foreach($data as $key)
                            <?php
                            $class = 'Modules\\' . $key->getName() . '\\' . $key->getName();
                            if (class_exists($class)) {
                                $gateway_class = new $class;
                            } else {
                                $gateway_class = '';
                            }
                            ?>
                            <div class="tab-pane @if($count==1) active @endif" id="{{ $key->getName() }}">
                                @if(!$gateway_class)
                                    <div class="alert alert-danger"> {{ trans_choice('core::general.payment_gateway_is_broken',1) }}</div>
                                @else
                                    <p>{{$key->getDescription()}}</p>
                                    @if(!empty($gateway_class->getLogo()))
                                        <p><img src="{{$gateway_class->getLogo()}}" width="200"/></p>
                                    @endif
                                    @if($installed_gateway=$installed_payment_gateways->where('name',$key->getName())->first())
                                        <form method="post" action="{{url('settings/payment_gateway/update')}}"
                                              class="form">
                                            {{csrf_field()}}
                                            <input type="hidden" name="module" value="{{$key->getName()}}">
                                            @foreach($gateway_class->getSettings() as $setting)
                                                <div class="row gy-4">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {!!build_html_form_field((object)$setting)!!}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="row gy-4">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <button type="submit"
                                                                class="btn btn-primary float-right">{{trans_choice('general.save',1)}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>

                                        </form>
                                    @else
                                        <div class="alert alert-warning"> {{ trans_choice('core::general.payment_gateway_not_installed',1) }}</div>
                                        <br>
                                        <a href="{{ url('settings/payment_gateway/install?module='.$key->getName()) }}"
                                           class="btn btn-info confirm">
                                            {{ trans_choice('core::general.install',1) }}
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <?php $count++; ?>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning"> {{ trans_choice('core::general.no_payment_gateway_found',1) }}</div>
                @endif
            </div>

        </div><!-- .card-preview -->
    </section>

@endsection
@section('scripts')

@endsection
