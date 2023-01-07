@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.item',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">  {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.item',1) }}</h3>
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
        <form method="post" action="{{ url('payroll/item/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                        <div class="form-group">
                            <label for="name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
                            <input type="text" name="name" v-model="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    <div class="form-group">
                        <label for="type"
                               class="control-label">{{trans_choice('payroll::general.type',1)}}</label>
                        <select class="form-control @error('type') is-invalid @enderror" name="type"
                                id="type"
                                v-model="type" required>
                            <option value="allowance">{{trans_choice('payroll::general.allowance',1)}}</option>
                            <option value="deduction">{{trans_choice('payroll::general.deduction',1)}}</option>

                        </select>
                        @error('type')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="amount_type"
                               class="control-label">{{trans_choice('payroll::general.amount',1)}} {{trans_choice('payroll::general.type',1)}}</label>
                        <select class="form-control @error('amount_type') is-invalid @enderror" name="amount_type"
                                id="amount_type"
                                v-model="amount_type" required>
                            <option value="fixed">{{trans_choice('payroll::general.fixed',1)}}</option>
                            <option value="percentage">{{trans_choice('payroll::general.percentage',1)}}</option>
                        </select>
                        @error('amount_type')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="amount" class="control-label">{{trans_choice('payroll::general.amount',1)}}</label>
                        <input type="text" name="amount" v-model="amount"
                               id="amount"
                               class="form-control numeric @error('amount') is-invalid @enderror" required>
                        @error('amount')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
                        <textarea type="text" name="description" v-model="description"
                                  id="description"
                                  class="form-control @error('description') is-invalid @enderror">
                        </textarea>
                        @error('description')
                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>

        </form>
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: "{{old('name')}}",
                type: "{{old('type')}}",
                amount_type: "{{old('amount_type')}}",
                amount: "{{old('amount')}}",
                description: `{{old('description')}}`,
            },
        })
    </script>
@endsection