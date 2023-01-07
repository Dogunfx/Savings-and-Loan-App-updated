@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('client::general.identification',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('client::general.identification',1) }}
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
                                    href="{{url('client')}}">{{ trans_choice('client::general.client',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('client::general.identification',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{url('client/client_identification/'.$client_identification->id.'/update')}}"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="client_identification_type_id"
                                       class="control-label">{{trans_choice('client::general.type',1)}}</label>
                                <select class="form-control @error('client_identification_type_id') is-invalid @enderror"
                                        name="client_identification_type_id"
                                        id="client_identification_type_id" v-model="client_identification_type_id"
                                        required>
                                    <option value=""></option>
                                    @foreach($client_identification_types as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('client_identification_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="identification_value"
                                       class="control-label">{{trans_choice('core::general.id',1)}}#</label>
                                <input type="text" name="identification_value" v-model="identification_value"
                                       id="identification_value"
                                       class="form-control @error('identification_value') is-invalid @enderror"
                                       required>
                                @error('identification_value')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="photo"
                                       class="control-label">{{trans_choice('core::general.photo',1)}}</label>
                                <input type="file" name="photo"
                                       id="photo"
                                       class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
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
            el: "#app",
            data: {
                client_identification_type_id: "{{old('client_identification_type_id',$client_identification->client_identification_type_id)}}",
                identification_value: "{{old('identification_value',$client_identification->identification_value)}}",
                description: `{{old('description',$client_identification->description)}}`,
            }
        })
    </script>
@endsection