@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.profile',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.profile',1) }}</h6>

            <div class="heading-elements">

            </div>
        </div>
        <form method="post" action="{{url('user/update_profile')}}" class="form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">

                {{csrf_field()}}
                @if (count($errors) > 0)
                    <div class="form-group has-feedback">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="first_name" class="control-label">{{trans('user::general.first_name')}}</label>
                    <input type="text" name="first_name" value="{{$user->first_name}}" id="first_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="control-label">{{trans('user::general.last_name')}}</label>
                    <input type="text" name="last_name" value="{{$user->last_name}}" id="last_name"
                           class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="gender" class="control-label">{{trans('user::general.gender')}}</label>
                    <select class="form-control" name="gender" id="gender" required>
                        <option value=""></option>
                        <option value="male"  @if($user->gender=="male") selected @endif>{{trans_choice("user::general.male",1)}}</option>
                        <option value="female" @if($user->gender=="female") selected @endif>{{trans_choice("user::general.female",1)}}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="phone" class="control-label">{{trans('user::general.phone')}}</label>
                    <input type="text" name="phone" id="phone" value="{{$user->phone}}" class="form-control">
                </div>
                <div class="form-group">
                    <label for="photo" class="control-label">{{trans('user::general.photo')}}</label>
                    <input type="file" name="photo" id="photo"  class="form-control">
                </div>
                <div class="form-group">
                    <label for="email" class="control-label">{{trans_choice('user::general.email',1)}}</label>
                    <input type="email" name="email" id="email" value="{{$user->email}}" class="form-control"
                           required>
                </div>
                <div class="form-group">
                    <label for="password" class="control-label">{{trans_choice('user::general.password',1)}}</label>
                    <input type="password" name="password" id="password" class="form-control" >
                </div>
                <div class="form-group">
                    <label for="password_confirmation"
                           class="control-label">{{trans_choice('user::general.password_confirmation',1)}}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                           >
                </div>
                <div class="form-group">
                    <label for="address" class="control-label">{{trans('user::general.address')}}</label>
                    <textarea type="text" name="address" id="address" class="form-control"
                              rows="3">{{$user->address}}</textarea>
                </div>

            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
    </div>
@endsection
@section('scripts')

@endsection