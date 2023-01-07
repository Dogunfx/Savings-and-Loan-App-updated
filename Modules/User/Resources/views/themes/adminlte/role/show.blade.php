@extends('core::layouts.master')
@section('title')
    {{ $role->name }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ $role->name }}
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
                                    href="{{url('user/role')}}">{{ trans_choice('user::general.role',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $role->name }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <div class="card card-bordered card-preview">
            <div class="card-body">
                <div class="row gy-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="name"
                                   class="control-label">{{trans_choice('core::general.name',1)}}</label>
                            <input type="text" name="name" v-model="name"
                                   id="name" disabled
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
                        <span class="title h4"> @{{key}}</span>
                    </div>
                    <div class="row gy-4" v-for="(permission,index) in items">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" v-bind:id="permission.id" disabled
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

        </div><!-- .card-preview -->

    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                name: "{{old('name',$role->name)}}",
                guard_name: "{{old('guard_name',$role->name)}}",
                permissions: {!! json_encode($permissions) !!},
                selectedPermissions: {!! json_encode($selected_permissions) !!},
            },
            methods: {
                selectAll(module) {
                    if ($('#' + module).is(':checked')) {
                        $('[data-parent="' + module + '"]').each(function () {
                            this.checked = true;
                        });
                    } else {
                        $('[data-parent="' + module + '"]').each(function () {
                            this.checked = false;
                        });
                    }
                },
            },
        })
    </script>
@endsection