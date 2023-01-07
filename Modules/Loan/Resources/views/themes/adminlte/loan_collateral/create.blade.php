@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.collateral',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.collateral',1) }}
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
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.add',1) }} {{ trans_choice('loan::general.collateral',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('loan/'.$id.'/collateral/store') }}" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="form-group">
                        <label for="loan_collateral_type_id"
                               class="control-label">{{trans_choice('loan::general.type',1)}}</label>
                        <v-select label="name" :options="loan_collateral_types"
                                  :reduce="loan_collateral_type => loan_collateral_type.id"
                                  v-model="loan_collateral_type_id">
                            <template #search="{attributes, events}">
                                <input
                                        autocomplete="off"
                                        class="vs__search @error('loan_collateral_type_id') is-invalid @enderror"
                                        v-bind="attributes"
                                        v-bind:required="!loan_collateral_type_id"
                                        v-on="events"
                                />
                            </template>
                        </v-select>
                        <input type="hidden" name="loan_collateral_type_id"
                               v-model="loan_collateral_type_id">
                        @error('loan_collateral_type_id')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="value" class="control-label">{{trans_choice('loan::general.value',1)}}</label>
                        <input type="number" name="value" v-model="value"
                               id="value"
                               class="form-control  @error('value') is-invalid @enderror numeric">
                        @error('value')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="file" class="control-label">{{trans_choice('client::general.file',1)}}</label>
                        <input type="file" name="file"
                               id="file"
                               class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
                        <textarea type="text" name="description" id="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3" v-model="description"></textarea>
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
                loan_collateral_type_id: parseInt("{{ old('loan_collateral_type_id') }}"),
                name: "{{ old('name') }}",
                value: "{{ old('value') }}",
                description: `{{ old('description') }}`,
                loan_collateral_types: {!! json_encode($loan_collateral_types) !!}

            },
            methods: {
                change_charge() {
                    this.amount = charges[this.loan_charge_id].amount;
                },
                onSubmit() {

                }
            }
        })
    </script>
@endsection