@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.item',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.item',1) }}
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
                                    href="{{url('payroll/item')}}">{{ trans_choice('payroll::general.item',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.item',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('payroll/item/'.$payroll_item->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
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
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: "{{old('name',$payroll_item->name)}}",
                type: "{{old('type',$payroll_item->type)}}",
                amount_type: "{{old('amount_type',$payroll_item->amount_type)}}",
                amount: "{{old('amount',$payroll_item->amount)}}",
                description: `{{old('description',$payroll_item->description)}}`,
            },
        })
    </script>
@endsection