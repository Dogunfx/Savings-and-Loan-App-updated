@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('core::general.account',1) }}
@endsection
@section('content')
    <div class="nk-block-head-content mb-4">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('core::general.account',1) }}</h3>
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
        <form method="post" action="{{url('accounting/chart_of_account/'.$chart_of_account->id.'/update')}}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="account_type"
                                       class="control-label">{{trans_choice('core::general.account',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control @error('account_type') is-invalid @enderror" name="account_type"
                                        id="account_type" v-model="account_type">
                                    <option value="asset">{{trans_choice('accounting::general.asset',1)}}</option>
                                    <option value="expense">{{trans_choice('accounting::general.expense',1)}}</option>
                                    <option value="income">{{trans_choice('accounting::general.income',1)}}</option>
                                    <option value="equity">{{trans_choice('accounting::general.equity',1)}}</option>
                                    <option value="liability">{{trans_choice('accounting::general.liability',1)}}</option>
                                </select>
                                @error('account_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="parent_id"
                                       class="control-label">{{trans_choice('core::general.parent',1)}}</label>
                                <select class="form-control @error('parent_id') is-invalid @enderror" name="parent_id"
                                        id="parent_id" v-model="parent_id">
                                    <option value=""></option>
                                    @foreach($chart_of_accounts as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
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
                                <label for="gl_code"
                                       class="control-label">{{trans_choice('accounting::general.gl_code',1)}}</label>
                                <input type="text" name="gl_code" v-model="gl_code"
                                       id="gl_code"
                                       class="form-control @error('gl_code') is-invalid @enderror" required>
                                @error('gl_code')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="allow_manual"
                                       class="control-label">{{trans_choice('accounting::general.manual_entries_allowed',1)}}</label>
                                <select class="form-control @error('allow_manual') is-invalid @enderror" name="allow_manual"
                                        id="allow_manual" v-model="allow_manual">
                                    <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>
                                </select>
                                @error('allow_manual')
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes"
                                       class="control-label">{{trans_choice('core::general.note',2)}}</label>
                                <textarea type="text" name="notes" v-model="notes"
                                          id="notes"
                                          class="form-control @error('notes') is-invalid @enderror" >
                                </textarea>
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
            el: "#app",
            data: {
                name: "{{old('name',$chart_of_account->name)}}",
                account_type: "{{old('account_type',$chart_of_account->account_type)}}",
                parent_id: "{{old('parent_id',$chart_of_account->parent_id)}}",
                gl_code: "{{old('gl_code',$chart_of_account->gl_code)}}",
                allow_manual: "{{old('allow_manual',$chart_of_account->allow_manual)}}",
                active: "{{old('active',$chart_of_account->active)}}",
                notes: `{{old('notes',$chart_of_account->notes)}}`,
            }
        })
    </script>
@endsection
