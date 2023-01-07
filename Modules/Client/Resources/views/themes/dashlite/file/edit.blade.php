@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('client::general.file',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">  {{ trans_choice('core::general.edit',1) }} {{ trans_choice('client::general.file',1) }}</h3>
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
        <form method="post" action="{{url('client/file/'.$client_file->id.'/update')}}" enctype="multipart/form-data">
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
                        <div class="col-md-12">
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
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description"
                                       class="control-label">{{trans_choice('core::general.description',1)}}</label>
                                <textarea type="text" name="description" v-model="description"
                                          id="description"
                                          class="form-control @error('description') is-invalid @enderror" >
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
    </div>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: "#app",
            data: {
                name: "{{old('name',$client_file->name)}}",
                description: "{{old('description',$client_file->description)}}",
            }
        })
    </script>
@endsection