@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('asset::general.asset',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title"> {{ trans_choice('core::general.add',1) }} {{ trans_choice('asset::general.asset',1) }}</h3>
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
        <form method="post" action="{{ url('asset/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="branch_id"
                                       class="control-label">{{trans_choice('core::general.branch',1)}} {{trans_choice('accounting::general.account',1)}}
                                </label>
                                <v-select label="name" :options="branches"
                                          :reduce="branch => branch.id"
                                          v-model="branch_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('branch_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                                id="branch_id"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="branch_id"
                                       v-model="branch_id">
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="asset_type_id"
                                       class="control-label">{{trans_choice('core::general.type',1)}}

                                </label>
                                <v-select label="name" :options="asset_types"
                                          :reduce="asset_type => asset_type.id"
                                          v-model="asset_type_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('asset_type_id') is-invalid @enderror"
                                                v-bind="attributes"
                                                v-on="events"
                                                id="asset_type_id"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="asset_type_id"
                                       v-model="asset_type_id">
                                @error('asset_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
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
                                <label for="purchase_date"
                                       class="control-label">{{trans_choice('asset::general.purchase',1)}} {{trans_choice('core::general.date',1)}}</label>
                                <flat-pickr v-model="purchase_date" name="purchase_date" id="purchase_date"
                                            class="form-control  @error('purchase_date') is-invalid @enderror"
                                            required></flat-pickr>

                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="purchase_price"
                                       class="control-label">{{trans_choice('asset::general.cost',1)}}</label>
                                <input type="number" name="purchase_price"
                                       class="form-control  @error('purchase_price') is-invalid @enderror"
                                       value="{{old('purchase_price')}}" v-model="purchase_price" required
                                       id="purchase_price">
                                @error('purchase_price')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="life_span"
                                       class="control-label">{{trans_choice('asset::general.life_span',1)}}</label>
                                <input type="number" name="life_span"
                                       class="form-control @error('life_span') is-invalid @enderror"
                                       value="{{old('life_span')}}" v-model="life_span" required
                                       id="life_span">
                                @error('life_span')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="salvage_value"
                                       class="control-label">{{trans_choice('asset::general.salvage_value',1)}}</label>
                                <input type="number" name="salvage_value"
                                       class="form-control @error('salvage_value') is-invalid @enderror"
                                       value="{{old('salvage_value')}}" v-model="salvage_value" required
                                       id="salvage_value">
                                @error('salvage_value')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        @foreach($custom_fields as $custom_field)
                            <?php
                            $field = custom_field_build_form_field($custom_field);
                            ?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    @if($custom_field->type=='radio')
                                        <label class="control-label"
                                               for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                        {!! $field['html'] !!}
                                    @else
                                        <label class="control-label"
                                               for="field_{{$custom_field->id}}">{{$field['label']}}</label>
                                        {!! $field['html'] !!}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label">{{trans_choice('core::general.note',2)}}</label>
                                <textarea name="notes" class="form-control  @error('notes') is-invalid @enderror"
                                          placeholder="" v-model="notes"
                                          id="notes" rows="3">{{old('notes')}}</textarea>
                                @error('notes')
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
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                name: "{{old('name')}}",
                asset_type_id: "{{old('asset_type_id')}}",
                branch_id: "{{old('branch_id')}}",
                purchase_date: "{{old('purchase_date')}}",
                purchase_price: "{{old('purchase_price')}}",
                replacement_value: "{{old('replacement_value')}}",
                value: "{{old('value')}}",
                life_span: "{{old('life_span')}}",
                salvage_value: "{{old('salvage_value')}}",
                serial_number: "{{old('serial_number')}}",
                bought_from: "{{old('bought_from')}}",
                purchase_year: "{{old('purchase_year')}}",
                status: "{{old('status')}}",
                active: "{{old('active')}}",
                notes: `{{old('notes')}}`,
                branches: {!! json_encode($branches) !!},
                asset_types: {!! json_encode($asset_types) !!},
            }
        })
    </script>
@endsection