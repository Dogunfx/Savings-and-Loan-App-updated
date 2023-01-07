@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.charge',1) }}
@endsection
@section('content')
    <div class="box box-primary" id="app">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('core::general.add',1) }} {{ trans_choice('share::general.charge',1) }}</h3>

            <div class="box-tools">
                <a href="#" onclick="window.history.back()"
                   class="btn btn-info btn-sm">{{ trans_choice('core::general.back',1) }}</a>
            </div>
        </div>
        <form method="post" action="{{ url('share/'.$share->id.'/charge/store') }}">
            {{csrf_field()}}
            <div class="box-body">
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
                    <label for="share_charge_id"
                           class="control-label">{{trans_choice('share::general.charge',1)}}</label>
                    <select class="form-control" name="share_charge_id" id="share_charge_id"
                            v-model="share_charge_id" v-on:click="change_charge" required>
                        <option value=""></option>
                        <option v-for="(charge,index) in charges" v-bind:value="index">
                            @{{ charge.name }}
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount" class="control-label">{{trans('core::general.amount')}}</label>
                    <input type="text" name="amount" value="{{ old('amount') }}" id="amount" v-model="amount"
                           class="form-control numeric" required>
                </div>
                <div class="form-group">
                    <label for="date" class="control-label">{{trans('core::general.date')}}</label>
                    <input type="text" name="date" value="{{ date("Y-m-d") }}" id="date"
                           class="form-control date-picker" required>
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
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                share_charge_id: '',
                amount: '',
                charges: charges,

            },
            methods: {
                change_charge() {
                    this.amount = charges[this.share_charge_id].amount;
                },
                onSubmit() {

                }
            }
        })
    </script>
@endsection