@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.template',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.template',1) }}
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
                                    href="{{url('payroll/template')}}">{{ trans_choice('payroll::general.template',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('payroll::general.template',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('payroll/template/store') }}">
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
                        <label for="work_duration"
                               class="control-label">{{trans_choice('payroll::general.work_duration',1)}}</label>
                        <input type="text" name="work_duration" v-model="work_duration"
                               id="work_duration"
                               class="form-control @error('work_duration') is-invalid @enderror" required>
                        @error('work_duration')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="duration_unit"
                               class="control-label">{{trans_choice('payroll::general.duration_unit',1)}}</label>
                        <input type="text" name="duration_unit" v-model="duration_unit"
                               id="duration_unit"
                               class="form-control @error('duration_unit') is-invalid @enderror">
                        @error('duration_unit')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="amount_per_duration"
                               class="control-label">{{trans_choice('payroll::general.amount_per_duration',1)}}</label>
                        <input type="text" name="amount_per_duration" v-model="amount_per_duration"
                               id="amount_per_duration"
                               class="form-control @error('amount_per_duration') is-invalid @enderror" required>
                        @error('amount_per_duration')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="payroll_items"
                               class="control-label">{{trans_choice('payroll::general.item',2)}}

                        </label>
                        <v-select label="name" :options="payroll_items"
                                  :reduce="payroll_item => payroll_item.id"
                                  v-model="selected_payroll_items" multiple>
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('payroll_items') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="payroll_items[]"
                               v-model="selected_payroll_items">
                        @error('payroll_items')
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
                name: "{{old('name')}}",
                work_duration: "{{old('work_duration')}}",
                duration_unit: "{{old('duration_unit','Day')}}",
                amount_per_duration: "{{old('amount_per_duration')}}",
                description: `{{old('description')}}`,
                selected_payroll_items: [],
                payroll_items: {!! json_encode($payroll_items) !!}
            },
        })
    </script>
@endsection