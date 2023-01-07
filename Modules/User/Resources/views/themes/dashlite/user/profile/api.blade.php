@extends('core::layouts.master')
@section('title')
    {{ __('user::general.API Keys') }}
@endsection
@section('styles')
@stop
@section('content')
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between g-3">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title">{{ __('user::general.API Keys') }} / <strong
                            class="text-primary small">{{ $user->first_name }} {{ $user->last_name }}</strong></h3>

            </div>
            <div class="nk-block-head-content">

                <a href="#" onclick="window.history.back()"
                   class="btn btn-outline-light bg-white d-none d-sm-inline-flex"><em
                            class="icon ni ni-arrow-left"></em><span>{{ trans_choice('core::general.back',1) }}</span></a>

            </div>
        </div>
    </div>
    <div class="nk-block nk-block-lg" id="app">
        <div class="row">
            <div class="col-md-3">
                @include('user::themes.dashlite.user.profile.user_profile_menu')
            </div>
            <!-- /.col -->
            <div class="col-md-9">

                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ __('user::general.Oauth Clients') }}</h4>

                                </div>
                                <div class="nk-block-head-content">
                                    <a href="#" data-toggle="modal" data-target="#new_oauth_client"
                                       class="btn btn-info btn-sm">
                                        {{ __('core::general.new') }} {{ __('user::general.Oauth Client') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('core::general.id',1) }}</th>
                                <th>{{ trans_choice('core::general.name',1) }}</th>
                                <th>{{ trans_choice('general.created_at',1) }}</th>
                                <th>{{ trans_choice('core::general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($user->clients as $client)
                                <tr>
                                    <td>{{$client->id}}</td>
                                    <td>{{$client->name}}</td>
                                    <td>{{$client->created_at}}</td>
                                    <td>
                                        <button type="button" data-toggle="modal" data-id="{{$client->id}}"
                                                data-name="{{$client->name}}"
                                                data-secret="{{$client->secret}}"
                                                data-redirect="{{$client->redirect}}"
                                                data-target="#view_oauth_client"
                                                class="btn btn-success btn-elevate-hover btn-sm btn-circle btn-icon">
                                            <i class="fa fa-info"></i>
                                        </button>
                                        <button type="button" data-toggle="modal" data-id="{{$client->id}}"
                                                data-name="{{$client->name}}"
                                                data-secret="{{$client->secret}}"
                                                data-redirect="{{$client->redirect}}"
                                                data-target="#edit_oauth_client"
                                                class="btn btn-warning btn-elevate-hover btn-sm btn-circle btn-icon">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <a href="{{url('user/profile/api/oauth_client/'.$client->id.'/destroy')}}"
                                           class="btn btn-danger btn-elevate-hover btn-sm btn-circle confirm btn-icon">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card card-bordered card-preview">
                    <div class="card-inner">
                        <div class="nk-block-head nk-block-head-lg">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h4 class="nk-block-title">{{ __('user::general.Personal Access Tokens') }}</h4>

                                </div>
                                <div class="nk-block-head-content">
                                    <a href="#" data-toggle="modal" data-target="#new_personal_key"
                                       class="btn btn-info btn-sm">
                                        {{ __('core::general.new') }} {{ __('user::general.Key') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>{{ trans_choice('core::general.name',1) }}</th>
                                <th>{{ trans_choice('general.created_at',1) }}</th>
                                <th>{{ trans_choice('core::general.action',1) }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tokens as $token)
                                <tr>
                                    <td>{{$token->name}}</td>
                                    <td>{{$token->created_at}}</td>
                                    <td>
                                        <button type="button" data-toggle="modal" data-id="{{$token->id}}"
                                                data-name="{{$token->name}}"
                                                data-target="#view_personal_key"
                                                class="btn btn-success btn-elevate-hover btn-sm btn-circle btn-icon">
                                            <i class="fa fa-info"></i>
                                        </button>
                                        <button type="button" data-toggle="modal" data-id="{{$token->id}}"
                                                data-name="{{$token->name}}"
                                                data-target="#edit_personal_key"
                                                class="btn btn-warning btn-elevate-hover btn-sm btn-circle btn-icon">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <a href="{{url('user/profile/api/personal_access_token/'.$token->id.'/destroy')}}"
                                           class="btn btn-danger btn-elevate-hover btn-sm btn-circle confirm btn-icon">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex">
                            <div class="mx-auto">
                                {{$tokens->links()}}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="new_personal_key" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('core::general.new') }} {{ __('user::general.Key') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="post" action="{{ url('user/profile/api/store_personal_access_token') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="personal_access_token_name"
                                   class="control-label">{{ trans_choice('core::general.name',1) }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="personal_access_token_name"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('core::general.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('core::general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_personal_key" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('core::general.edit') }} {{ __('user::general.Key') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="post" action="{{ url('user/profile/api/update_personal_access_token') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <input type="hidden" name="personal_token_id" id="personal_token_id"/>
                        <div class="form-group">
                            <label for="edit_personal_access_token_name" class="control-label">
                                {{ trans_choice('core::general.name',1) }}
                            </label>
                            <input type="text" name="name" value="" id="edit_personal_access_token_name"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('core::general.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('core::general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="view_personal_key" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('user::general.Personal Access Token') }}(<span id="personal_token_name"></span>)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <input type="text" readonly="readonly"
                           onfocus="if (!window.__cfRLUnblockHandlers) return false; this.select();"
                           class="form-control" value="" id="view_personal_token_view_key">
                    <p>This API Key can be used to gain an <code>access_token</code>, which can then be used to interact
                        with the API.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('core::general.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="new_oauth_client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('core::general.new') }} {{ __('user::general.Oauth Client') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="post" action="{{ url('user/profile/api/store_oauth_client') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="oauth_client_name" class="control-label">{{ trans_choice('core::general.name',1) }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" id="oauth_client_name"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="redirect"
                                   class="control-label">{{ __('user::general.Redirect Url') }}</label>
                            <input type="text" name="redirect" value="{{ old('redirect') }}" id="redirect"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('core::general.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('core::general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit_oauth_client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('core::general.edit') }} {{ __('user::general.Oauth Client') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <form method="post" action="{{ url('user/profile/api/update_oauth_client') }}">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <input type="hidden" name="oauth_client_id" id="oauth_client_id"/>
                        <div class="form-group">
                            <label for="name" class="control-label">{{ trans_choice('core::general.name',1) }}</label>
                            <input type="text" name="name" value="" id="name"
                                   class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="redirect"
                                   class="control-label">{{ __('user::general.Redirect Url') }}</label>
                            <input type="text" name="redirect" value="{{ old('redirect') }}" id="redirect"
                                   class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ __('core::general.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('core::general.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="view_oauth_client" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ __('user::general.Oauth Client') }}(<span id="oauth_client_name"></span>)
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>{{ __('user::general.Oauth Client') }} {{ trans_choice('core::general.id',1) }}</label>
                        <input type="text" readonly="readonly"
                               onfocus="if (!window.__cfRLUnblockHandlers) return false; this.select();"
                               class="form-control" value="" id="oauth_client_id">
                    </div>
                    <div class="form-group">
                        <label>{{ __('general.Secret') }} </label>
                        <input type="text" readonly="readonly"
                               onfocus="if (!window.__cfRLUnblockHandlers) return false; this.select();"
                               class="form-control" value="" id="oauth_client_view_secret">
                    </div>
                    <div class="form-group">
                        <label>{{ __('general.Redirect Url') }} </label>
                        <input type="text" readonly="readonly"
                               onfocus="if (!window.__cfRLUnblockHandlers) return false; this.select();"
                               class="form-control" value="" id="oauth_client_redirect">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('general.Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            $('#view_personal_key').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var modal = $(this)
                modal.find('#personal_token_name').html(button.data('name'))
                modal.find('#view_personal_token_view_key').val(button.data('id'))
            });
            $('#edit_personal_key').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var modal = $(this)
                modal.find('#personal_token_id').val(button.data('id'))
                modal.find('#edit_personal_access_token_name').val(button.data('name'))
            });
            $('#view_oauth_client').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var modal = $(this)
                modal.find('#oauth_client_name').html(button.data('name'))
                modal.find('#oauth_client_id').val(button.data('id'))
                modal.find('#oauth_client_view_secret').val(button.data('secret'))
                modal.find('#oauth_client_redirect').val(button.data('redirect'))
            });
            $('#edit_oauth_client').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget) // Button that triggered the modal
                var modal = $(this)
                modal.find('#oauth_client_id').val(button.data('id'))
                modal.find('#name').val(button.data('name'))
                modal.find('#redirect').val(button.data('redirect'))
            });
        });
    </script>
@endsection
