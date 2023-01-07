@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.gateway',2) }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.payment',1) }} {{ trans_choice('core::general.gateway',2) }}</h3>
                    <div class="nk-block-des text-soft">

                    </div>
                </div><!-- .nk-block-head-content -->
                <div class="nk-block-head-content">
                    <a href="#" onclick="window.history.back()"
                       class="btn btn-outline-light bg-white d-none d-sm-inline-flex">
                        <em class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span>
                    </a>

                </div><!-- .nk-block-head-content -->
            </div><!-- .nk-block-between -->
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-inner">
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
    </div>

@endsection
@section('scripts')

@endsection
