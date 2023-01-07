@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.role',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('user::general.role',1) }}</h3>
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
        <form method="post" action="{{ url('user/role/store') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name"
                                       class="control-label">{{trans_choice('core::general.name',1)}}</label>
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
                    </div>
                    <div v-for="(items,key) in permissions">
                        <div class="custom-control custom-checkbox mb-2 mt-2">
                            <input type="checkbox" v-bind:id="key"
                                   v-bind:value="key" class="custom-control-input" v-on:click="selectAll(key)">
                            <label class="custom-control-label"
                                   v-bind:for="key">
                                <span class="title h4"> @{{key}}</span>
                            </label>
                        </div>
                        <div class="row gy-4" v-for="(permission,index) in items">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" v-bind:id="permission.id"
                                               name="permissions[]" v-model="selectedPermissions"
                                               v-bind:value="permission.id" v-bind:data-parent="key"
                                               class="custom-control-input">
                                        <label class="custom-control-label"
                                               v-bind:for="permission.id"> @{{permission.display_name}}</label>
                                    </div>
                                </div>
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
            el: "#app",
            data: {
                name: "",
                guard_name: "web",
                permissions: {!! json_encode($permissions) !!},
                selectedPermissions: [],
            },
            methods: {
                selectAll(module) {
                    if($('#'+module).is(':checked')){
                        $('[data-parent="'+module+'"]').each(function(){
                            this.checked = true;
                        });
                    }else{
                        $('[data-parent="'+module+'"]').each(function(){
                            this.checked = false;
                        });
                    }
                },
            },
        })
    </script>
@endsection