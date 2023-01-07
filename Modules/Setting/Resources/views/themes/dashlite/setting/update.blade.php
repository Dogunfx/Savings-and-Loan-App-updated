@extends('core::layouts.master')
@section('title')
    {{ trans_choice('setting::general.update',1) }} {{ trans_choice('setting::general.setting',2) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('setting::general.update',1) }} {{ trans_choice('setting::general.setting',2) }}</h3>
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
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <input type="hidden" value="update" name="category">
                    @foreach($data as $key)
                        <div class="row gy-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!!build_html_form_field($key)!!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label class="control-label" for="server_version">Server Version:</label>

                        <input name="server_version" id="server_version" class="form-control" disabled>

                    </div>
                    <div id="update_message"></div>
                </div>
                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right" id="check_update">Check Version</button>
                </div>
            </div><!-- .card-preview -->
    </div>
@endsection
@section('scripts')
<script>
    $('#check_update').click(function (e) {
        $.ajax({
            type: 'POST',
            url: '{{\Modules\Setting\Entities\Setting::where('setting_key','core.update_url')->first()->setting_value}}',
            dataType: 'json',
            success: function (data) {
                if ("{!! \Modules\Setting\Entities\Setting::where('setting_key','core.system_version')->first()->setting_value !!}}" < data.version) {
                    swal({
                        title: '{{trans_choice('core::general.update_available',1)}}<br>v' + data.version,
                        html: data.notes,
                        type: 'success',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '{{trans_choice('general.download',1)}}',
                        cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                    }).then(function () {
                        //curl function to download update
                        //notify user that update is in progress, do not navigate from page
                        $('#update_message').html("<div class='alert alert-warning'>{{trans_choice('core::general.do_not_navigate_from_page',1)}}</div>");
                        window.location = "{{url('setting/system_update_process/download?url=')}}" + data.url;
                    });
                    $('#serverVersion').html(data.version);
                } else {
                    swal({
                        title: '{{trans_choice('core::general.no_update_available',1)}}',
                        text: '{{trans_choice('core::general.system_is_up_to_date',1)}}',
                        type: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: '{{trans_choice('core::general.ok',1)}}',
                        cancelButtonText: '{{trans_choice('core::general.cancel',1)}}'
                    })
                }
            }
            ,
            error: function (e) {
                alert("There was an error connecting to the server")
            }
        });
    })
</script>
@endsection