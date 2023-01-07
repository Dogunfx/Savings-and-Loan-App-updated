@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.repayment',1) }}</h3>
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
        <form method="post" action="{{url('portal/loan/'.$id.'/repayment/store')}}" id="payment_form">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
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
                            class="btn btn-primary  float-right" id="pay_button" disabled >{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
    <div id="gatewayData"></div>
    <!-- /.box -->
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
