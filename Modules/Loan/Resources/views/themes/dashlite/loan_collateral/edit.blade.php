@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.collateral',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('loan::general.collateral',1) }}</h3>
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
        <form method="post" action="{{ url('loan/collateral/'.$loan_collateral->id.'/update') }}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
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
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                loan_collateral_type_id: parseInt("{{ old('loan_collateral_type_id',$loan_collateral->loan_collateral_type_id) }}"),
                name: "{{ old('name',$loan_collateral->name) }}",
                value: "{{ old('value',$loan_collateral->value) }}",
                description: `{{ old('description',$loan_collateral->description) }}`,
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