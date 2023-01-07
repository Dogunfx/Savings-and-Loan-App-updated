@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.payroll',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.payroll',1) }}
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
                                    href="{{url('payroll')}}">{{ trans_choice('payroll::general.payroll',1) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('payroll::general.payroll',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('payroll/'.$payroll->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="branch_id"
                                       class="control-label">{{trans_choice('branch::general.branch',1)}}
                                </label>
                                <v-select label="name" :options="branches"
                                          :reduce="branch => branch.id"
                                          v-model="branch_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('branch_id') is-invalid @enderror"
                                                :required="!branch_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="branch_id"
                                       v-model="branch_id">
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="currency_id"
                                       class="control-label">{{trans_choice('core::general.currency',1)}}
                                </label>
                                <v-select label="name" :options="currencies"
                                          :reduce="currency => currency.id"
                                          v-model="currency_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('currency_id') is-invalid @enderror"
                                                :required="!currency_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="currency_id"
                                       v-model="currency_id">
                                @error('currency_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="user_id"
                                       class="control-label">{{trans_choice('core::general.user',1)}}</label>
                                <v-select label="full_name" :options="users"
                                          :reduce="user => user.id"
                                          v-model="user_id">
                                    <template #search="{attributes, events}">
                                        <input
                                                autocomplete="off"
                                                class="vs__search @error('user_id') is-invalid @enderror"
                                                :required="!user_id"
                                                v-bind="attributes"
                                                v-on="events"
                                        />
                                    </template>
                                </v-select>
                                <input type="hidden" name="user_id"
                                       v-model="user_id">
                                @error('user_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date"
                                       class="control-label"> {{trans_choice('core::general.date',1)}}</label>
                                <flat-pickr
                                        v-model="date"
                                        class="form-control  @error('date') is-invalid @enderror"
                                        name="date" required>
                                </flat-pickr>
                                @error('date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="payroll_template_id"
                                       class="control-label">{{trans_choice('payroll::general.template',1)}}</label>
                                <select class="form-control" name="payroll_template_id" id="payroll_template_id"
                                        v-model="payroll_template_id" required v-on:change="change_payroll_template">
                                    <option value=""></option>

                                    <option v-for="(payroll_template,index) in payroll_templates"
                                            v-bind:value="index">@{{payroll_template[0].name}}
                                    </option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="work_duration"
                                       class="control-label">{{trans_choice('payroll::general.work_duration',1)}}</label>
                                <input type="text" name="work_duration" v-model="work_duration" id="work_duration"
                                       class="form-control numeric" v-on:keyup="update_amount" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="duration_unit"
                                       class="control-label">{{trans_choice('payroll::general.duration_unit',1)}}</label>
                                <input type="text" name="duration_unit" v-model="duration_unit" id="duration_unit"
                                       class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="amount_per_duration"
                                       class="control-label">{{trans_choice('payroll::general.amount_per_duration',1)}}</label>
                                <input type="text" name="amount_per_duration" v-model="amount_per_duration"
                                       id="amount_per_duration" v-on:keyup="update_amount"
                                       class="form-control numeric" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="total_duration_amount"
                                       class="control-label">{{trans_choice('payroll::general.total_duration_amount',1)}}</label>
                                <input type="text" name="total_duration_amount" v-model="total_duration_amount"
                                       id="total_duration_amount"
                                       class="form-control numeric" readonly>
                            </div>
                        </div>
                    </div>
                    <h4>{{trans_choice('payroll::general.allowance',2)}}</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>{{trans_choice('payroll::general.allowance',1)}}</th>
                                    <th>{{trans_choice('payroll::general.amount',1)}} {{trans_choice('payroll::general.type',1)}}</th>
                                    <th>{{trans_choice('payroll::general.amount',1)}}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in selected_allowances">
                                    <td>
                                        <input name="payroll_items[id][]" type="hidden" v-model="item.id"/>
                                        <input v-bind:name="'payroll_items[type][]'" type="hidden"
                                               v-model="item.type"/>
                                        <input v-bind:name="'payroll_items[name][]'"
                                               v-on:keyup="update_amount"
                                               class="form-control" v-model="item.name"/>
                                    </td>
                                    <td>
                                        <select class="form-control" v-on:change="update_amount"
                                                v-bind:name="'payroll_items[amount_type][]'"
                                                v-model="item.amount_type">
                                            <option value="fixed"
                                                    v-bind:selected="item.amount_type=='fixed'">{{trans_choice('payroll::general.fixed',1)}}</option>
                                            <option value="percentage"
                                                    v-bind:selected="item.amount_type=='percentage'">{{trans_choice('payroll::general.percentage',1)}}</option>
                                        </select>
                                    </td>
                                    <td><input v-bind:name="'payroll_items[amount][]'"
                                               v-on:keyup="update_amount"
                                               v-model="item.amount"

                                               class="form-control numeric"/></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" v-on:click="add_allowance"
                                                v-if="index==0">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs"
                                                v-on:click="remove_allowance(index)" v-else>
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="2">{{trans_choice('payroll::general.total',1)}}</th>
                                    <th colspan="2">@{{ total_allowances }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <h4>{{trans_choice('payroll::general.deduction',2)}}</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>{{trans_choice('payroll::general.deduction',1)}}</th>
                                    <th>{{trans_choice('payroll::general.amount',1)}} {{trans_choice('payroll::general.type',1)}}</th>
                                    <th>{{trans_choice('payroll::general.amount',1)}}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(item,index) in selected_deductions">
                                    <td>
                                        <input name="payroll_items[id][]" type="hidden" v-model="item.id"/>
                                        <input v-bind:name="'payroll_items[type][]'" type="hidden"
                                               v-model="item.type"/>
                                        <input v-bind:name="'payroll_items[name][]'"
                                               v-on:keyup="update_amount"
                                               v-model="item.name" class="form-control"/>
                                    </td>
                                    <td>
                                        <select class="form-control"
                                                v-bind:name="'payroll_items[amount_type][]'"
                                                v-model="item.amount_type" v-on:change="update_amount">
                                            <option value="fixed"
                                                    v-bind:selected="item.amount_type=='fixed'">{{trans_choice('payroll::general.fixed',1)}}</option>
                                            <option value="percentage"
                                                    v-bind:selected="item.amount_type=='percentage'">{{trans_choice('payroll::general.percentage',1)}}</option>
                                        </select>
                                    </td>
                                    <td><input v-bind:name="'payroll_items[amount][]'" v-model="item.amount"
                                               v-on:keyup="update_amount"
                                               class="form-control numeric"/></td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-xs" v-on:click="add_deduction"
                                                v-if="index==0">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-xs"
                                                v-on:click="remove_deduction(index)" v-else>
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="2">{{trans_choice('payroll::general.total',1)}}</th>
                                    <th colspan="2">@{{ total_deductions }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="bank_name"
                                       class="control-label">{{trans_choice('payroll::general.bank_name',1)}}</label>
                                <input type="text" name="bank_name" id="bank_name"
                                       class="form-control " v-model="bank_name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="account_number"
                                       class="control-label">{{trans_choice('payroll::general.account_number',1)}}</label>
                                <input type="text" name="account_number" id="account_number"
                                       class="form-control " v-model="account_number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="recurring"
                                       class="control-label">{{trans_choice('payroll::general.recurring',1)}}</label>
                                <select class="form-control" name="recurring" id="recurring"
                                        v-model="recurring" required>
                                    <option value="0">{{trans_choice('core::general.no',1)}}</option>
                                    <option value="1">{{trans_choice('core::general.yes',1)}}</option>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-show="recurring==1">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_frequency"
                                       class="control-label">{{trans_choice('payroll::general.recur_frequency',1)}}</label>
                                <input type="text" name="recur_frequency" id="recur_frequency"
                                       class="form-control numeric" v-model="recur_frequency"
                                       v-bind:required="recurring==1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_type"
                                       class="control-label">{{trans_choice('payroll::general.recur_type',1)}}</label>
                                <select class="form-control" name="recur_type" id="recur_type"
                                        v-model="recur_type">
                                    <option value="day">{{trans_choice('payroll::general.day',1)}}</option>
                                    <option value="week">{{trans_choice('payroll::general.week',1)}}</option>
                                    <option value="month">{{trans_choice('payroll::general.month',1)}}</option>
                                    <option value="year">{{trans_choice('payroll::general.year',1)}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_start_date"
                                       class="control-label">{{trans_choice('payroll::general.recur_start_date',1)}}</label>
                                <input type="text" name="recur_start_date" id="recur_start_date"
                                       class="form-control date-picker" v-model="recur_start_date"
                                       v-bind:required="recurring==1">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="recur_end_date"
                                       class="control-label">{{trans_choice('payroll::general.recur_end_date',1)}}</label>
                                <input type="text" name="recur_end_date" id="recur_end_date"
                                       class="form-control date-picker" v-model="recur_end_date">
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="description"
                               class="control-label">{{trans_choice('core::general.description',1)}}</label>
                        <textarea type="text" name="description" id="description"
                                  class="form-control" v-model="description"></textarea>

                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="pull-right">{{trans_choice('payroll::general.gross',1)}} {{trans_choice('payroll::general.amount',1)}}
                                : @{{ gross_amount }}</h4>
                        </div>
                    </div>
                </div>

                <div class="card-footer border-top ">
                    <button type="submit"
                            class="btn btn-primary  float-right">{{trans_choice('core::general.save',1)}}</button>
                </div>
            </div>
        </form>
    </section>
@endsection
@section('scripts')
    <script>
        var app = new Vue({
            el: '#app',
            data: {
                user_id: parseInt('{{$payroll->user_id}}'),
                payroll_template_id: parseInt('{{$payroll->payroll_template_id}}'),
                branch_id: parseInt('{{$payroll->branch_id}}'),
                currency_id: parseInt('{{$payroll->currency_id}}'),
                bank_name: '{{$payroll->bank_name}}',
                account_number: '{{$payroll->account_number}}',
                description: '{{$payroll->description}}',
                recurring: '{{$payroll->recurring}}',
                recur_frequency: '{{$payroll->recur_frequency}}',
                recur_start_date: '{{$payroll->recur_start_date}}',
                recur_end_date: '{{$payroll->recur_end_date}}',
                recur_type: '{{$payroll->recur_type}}',
                payroll_templates: payroll_templates,
                payroll_items: payroll_items,
                allowances: allowances,
                deductions: deductions,
                currencies: currencies,
                branches: branches,
                users: users,
                work_duration: '{{$payroll->work_duration}}',
                duration_unit: '{{$payroll->duration_unit}}',
                amount_per_duration: '{{$payroll->amount_per_duration}}',
                total_duration_amount: '{{$payroll->amount_per_duration*$payroll->work_duration}}',
                date: '{{$payroll->date}}',
                total_allowances: 0,
                total_deductions: 0,
                gross_amount: 0,
                selected_allowances: selected_allowances,
                selected_deductions: selected_deductions

            },
            created: function () {
                this.update_amount();

            },
            methods: {
                change_payroll_template() {
                    this.selected_allowances = [];
                    this.selected_deductions = [];
                    if (this.payroll_template_id != '') {
                        var payroll_template = payroll_templates[this.payroll_template_id][0];
                        this.work_duration = payroll_template.work_duration;
                        this.duration_unit = payroll_template.duration_unit;
                        this.amount_per_duration = payroll_template.amount_per_duration;
                        this.total_duration_amount = parseFloat(this.work_duration * this.amount_per_duration);
                        this.gross_amount = this.total_duration_amount;
                        var template_payroll_items = payroll_template.payroll_items;
                        for (let item of template_payroll_items) {
                            let payroll_item = payroll_items[item.payroll_item_id][0];
                            if (payroll_item.type == 'allowance') {
                                this.selected_allowances.push(payroll_item);
                            }
                            if (payroll_item.type == 'deduction') {
                                this.selected_deductions.push(payroll_item);
                            }
                        }
                        if (this.selected_allowances.length == 0) {
                            this.add_allowance();
                        }
                        if (this.selected_deductions.length == 0) {
                            this.add_deduction();
                        }
                        this.update_amount();
                    } else {
                        this.selected_allowances = [];
                        this.selected_deductions = [];
                        this.amount_per_duration = 0;
                        this.work_duration = 0;
                        this.total_allowances = 0;
                        this.total_deductions = 0;
                        this.total_duration_amount = parseFloat(this.work_duration * this.amount_per_duration);
                        this.gross_amount = this.total_duration_amount;
                    }

                },
                update_amount() {
                    this.total_duration_amount = parseFloat(this.work_duration * this.amount_per_duration);
                    this.gross_amount = this.total_duration_amount;
                    this.total_allowances = 0;
                    this.total_deductions = 0;
                    for (let item of this.selected_allowances) {
                        if (item.amount_type == 'fixed') {
                            this.gross_amount = this.gross_amount + parseFloat(item.amount);
                            this.total_allowances = this.total_allowances + parseFloat(item.amount);
                        }
                        if (item.amount_type == 'percentage') {
                            this.total_allowances = this.total_allowances + (this.gross_amount * parseFloat(item.amount)) / 100;
                            this.gross_amount = this.gross_amount + (this.gross_amount * parseFloat(item.amount)) / 100;

                        }
                    }
                    for (let item of this.selected_deductions) {
                        if (item.amount_type == 'fixed') {
                            this.gross_amount = this.gross_amount - parseFloat(item.amount);
                            this.total_deductions = this.total_deductions + parseFloat(item.amount);
                        }
                        if (item.amount_type == 'percentage') {
                            this.total_deductions = this.total_deductions + (this.gross_amount * parseFloat(item.amount)) / 100;
                            this.gross_amount = this.gross_amount - (this.gross_amount * parseFloat(item.amount)) / 100;

                        }
                    }
                },
                remove_allowance(id) {
                    this.selected_allowances.splice(id);
                    this.update_amount();
                },
                remove_deduction(id) {
                    this.selected_deductions.splice(id);
                    this.update_amount();
                },
                add_allowance() {
                    this.selected_allowances.push({
                        id: this.selected_allowances.length + 1,
                        name: '',
                        amount_type: 'fixed',
                        amount: 0
                    });
                    this.update_amount();
                },
                add_deduction() {
                    this.selected_deductions.push({
                        id: this.selected_allowances.length + 1,
                        name: '',
                        amount_type: 'fixed',
                        amount: 0
                    });
                    this.update_amount();
                }
            }
        });
    </script>
@endsection