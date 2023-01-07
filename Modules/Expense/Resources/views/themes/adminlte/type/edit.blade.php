@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('expense::general.type',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('expense::general.type',1) }}
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
                                    href="{{url('expense/type')}}">{{ trans_choice('expense::general.expense',1) }}  {{ trans_choice('expense::general.type',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('expense::general.type',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('expense/type/'.$expense_type->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
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
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="expense_chart_of_account_id"
                                       class="control-label">{{trans_choice('accounting::general.expense',1)}} {{trans_choice('accounting::general.account',1)}}

                                </label>
                                <v-select label="name" :options="expenses"
                                          :reduce="expense => expense.id"
                                          v-model="expense_chart_of_account_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('expense_chart_of_account_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="expense_chart_of_account_id"
                                       v-model="expense_chart_of_account_id">
                                @error('expense_chart_of_account_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="asset_chart_of_account_id"
                                       class="control-label">{{trans_choice('accounting::general.asset',1)}} {{trans_choice('accounting::general.account',1)}}

                                </label>
                                <v-select label="name" :options="assets"
                                          :reduce="asset => asset.id"
                                          v-model="asset_chart_of_account_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('asset_chart_of_account_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="asset_chart_of_account_id"
                                       v-model="asset_chart_of_account_id">
                                @error('asset_chart_of_account_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div><!-- .card-preview -->
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: '{{old('name',$expense_type->name)}}',
                expense_chart_of_account_id: parseInt("{{old('expense_chart_of_account_id',$expense_type->expense_chart_of_account_id)}}"),
                asset_chart_of_account_id: parseInt("{{old('asset_chart_of_account_id',$expense_type->asset_chart_of_account_id)}}"),
                expenses: {!! json_encode($expenses) !!},
                assets: {!! json_encode($assets) !!},
            }
        })
    </script>
@endsection