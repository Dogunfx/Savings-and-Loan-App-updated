@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('customfield::general.custom_field',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('customfield::general.custom_field',1) }}</h3>
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
        <form method="post" action="{{ url('custom_field/'.$custom_field->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name"
                                       class="control-label">{{trans_choice('core::general.name',1)}}</label>
                                <input type="text" name="name" v-model="name"
                                       id="name" v-on:blur="update_label"
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
                                <label for="label"
                                       class="control-label">{{trans_choice('customfield::general.label',1)}}</label>
                                <input type="text" name="label" id="label"
                                       class="form-control @error('label') is-invalid @enderror" v-model="label">
                                @error('label')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="category"
                                       class="control-label">{{trans_choice('customfield::general.category',1)}}</label>
                                <select class="form-control @error('category') is-invalid @enderror" name="category" id="category" v-model="category">
                                    <option value="" disabled selected></option>
                                    <option v-for="(category,index) in categories" v-bind:value="index">@{{category}}
                                    </option>

                                </select>
                                @error('category')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type"
                                       class="control-label">{{trans_choice('customfield::general.type',1)}}</label>
                                <select class="form-control @error('type') is-invalid @enderror" name="type" id="type" v-model="type"
                                        v-on:click="change_type">
                                    <option value="" disabled selected></option>
                                    <option v-for="(type,index) in types" v-bind:value="index">@{{type}}</option>

                                </select>
                                @error('type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="options"
                                       class="control-label">{{trans_choice('customfield::general.option',2)}}</label>
                                <input type="text" name="options" id="options"
                                       class="form-control @error('options') is-invalid @enderror" v-model="options">
                                @error('options')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" v-if="type=='select_db'">
                                <label for="db_columns"
                                       class="control-label">{{trans_choice('customfield::general.db_columns',1)}}</label>
                                <input type="text" name="db_columns" id="db_columns"
                                       class="form-control @error('db_columns') is-invalid @enderror" v-model="db_columns">
                                @error('db_columns')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="classes"
                                       class="control-label">{{trans_choice('customfield::general.class',2)}}</label>
                                <input type="text" name="classes" id="classes"
                                       class="form-control @error('classes') is-invalid @enderror" v-model="classes">
                                @error('classes')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="rules"
                                       class="control-label">{{trans_choice('customfield::general.rule',2)}}</label>
                                <input type="text" name="rules" id="rules"
                                       class="form-control @error('rules') is-invalid @enderror" v-model="rules">
                                @error('rules')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="required"
                                       class="control-label">{{trans_choice('customfield::general.required',1)}}</label>
                                <select class="form-control @error('required') is-invalid @enderror" name="required" id="required" required v-model="required">
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                                    <option value="0" selected>{{trans_choice('core::general.no',1)}}</option>
                                </select>
                                @error('required')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="active"
                                       class="control-label">{{trans_choice('core::general.active',1)}}</label>
                                <select class="form-control @error('active') is-invalid @enderror" name="active"
                                        id="active" v-model="active">
                                    <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                                </select>
                                @error('active')
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
                name:  "{{old('name',$custom_field->name)}}",
                type:  "{{old('type',$custom_field->type)}}",
                types: types,
                required:  "{{old('required',$custom_field->required)}}",
                active:  "{{old('active',$custom_field->active)}}",
                label:  "{{old('label',$custom_field->label)}}",
                options:  "{{old('options',$custom_field->options)}}",
                classes:  "{{old('classes',$custom_field->classes)}}",
                db_columns: "{{old('db_columns',$custom_field->db_columns)}}",
                rules:  "{{old('rules',$custom_field->rules)}}",
                category:  "{{old('category',$custom_field->category)}}",
                categories: categories

            },
            methods: {
                change_type() {

                },
                update_label() {
                    if (this.label == '') {
                        this.label = this.name;
                    }
                },
                onSubmit() {

                }
            }
        })
    </script>
@endsection