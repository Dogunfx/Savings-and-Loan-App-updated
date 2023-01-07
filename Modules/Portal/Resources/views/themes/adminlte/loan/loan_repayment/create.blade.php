@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}
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
                        <li class="breadcrumb-item"><a
                                    href="{{url('loan/'.$id.'/show')}}">{{ trans_choice('loan::general.loan',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{url('portal/loan/'.$id.'/repayment/store')}}" id="payment_form">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="form-group">
                        <label for="payment_amount"
                               class="control-label">{{trans_choice('loan::general.amount',1)}}</label>
                        <input type="text" name="amount" v-model="amount" id="payment_amount"
                               class="form-control numeric @error('amount') is-invalid @enderror"
                               required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="payment_type_id"
                               class="control-label">{{trans_choice('core::general.payment_method',1)}}</label>
                        <v-select label="name" :options="payment_types"
                                  :reduce="payment_type => payment_type.id"
                                  @input="change_payment_type"
                                  v-model="payment_type_id">
                            <template #search="{attributes, events}">
                                <input
                                        class="vs__search @error('payment_type_id') is-invalid @enderror"
                                        :required="!payment_type_id"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="payment_type_id" v-model="payment_type_id">
                        @error('payment_type_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div id="gateway_info">

                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right" id="pay_button"
                            disabled>{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>

        <div id="gatewayData"></div>
        <!-- /.box -->
    </section>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var app = new Vue({
                el: "#app",
                data: {
                    amount: "{{old('amount')}}",
                    payment_type_id: parseInt("{{old('payment_type_id')}}"),
                    payment_types: {!! json_encode($payment_types) !!},

                },
                methods: {
                    change_payment_type() {
                        axios.get("{{url('payment_type/get_payment_gateway?id=')}}" + this.payment_type_id + "&loan_id={{$id}}").then(response => {
                            $('#gatewayData').html(response.data)
                        }).catch(error => {

                        })
                    }
                }
            })
        })
    </script>
@endsection
