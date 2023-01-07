@extends('core::layouts.master')
@section('title')
    {{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.campaign',1) }}
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        {{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.campaign',1) }}
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
                                    href="{{url('communication/campaign')}}">{{ trans_choice('communication::general.campaign',2) }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans_choice('core::general.edit',1) }} {{ trans_choice('communication::general.campaign',1) }}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content" id="app">
        <form method="post" action="{{ url('communication/campaign/'.$communication_campaign->id.'/update') }}">
            {{csrf_field()}}
            <div class="card card-bordered card-preview">
                <div class="card-body">
                    <div class="row gy-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="name" class="control-label">{{trans_choice('core::general.name',1)}}</label>
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror" v-model="name" required>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"
                                       for="campaign_type">{{trans_choice('communication::general.campaign',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control @error('campaign_type') is-invalid @enderror"
                                        v-model="campaign_type" name="campaign_type"
                                        id="campaign_type"
                                        required>
                                    <option></option>
                                    <option value="sms">{{trans_choice('communication::general.sms',1)}}</option>
                                    <option value="email">{{trans_choice('communication::general.email',1)}}</option>
                                </select>
                                @error('campaign_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"
                                       for="trigger_type">{{trans_choice('communication::general.trigger',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control @error('trigger_type') is-invalid @enderror"
                                        v-model="trigger_type" name="trigger_type"
                                        id="trigger_type"
                                        required v-on:click="change_trigger_type">
                                    <option></option>
                                    <option value="direct">{{trans_choice('communication::general.direct',1)}}</option>
                                    <option value="schedule">{{trans_choice('communication::general.schedule',1)}}</option>
                                    <option value="triggered">{{trans_choice('communication::general.triggered',1)}}</option>
                                </select>
                                @error('trigger_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4" v-if="trigger_type=='schedule'">

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="scheduled_date"
                                       class="control-label">{{trans_choice('communication::general.schedule',1)}} {{trans_choice('core::general.date',1)}}</label>
                                <flat-pickr
                                        v-model="scheduled_date"
                                        class="form-control  @error('scheduled_date') is-invalid @enderror"
                                        name="scheduled_date" required>
                                </flat-pickr>
                                @error('scheduled_date')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="scheduled_time"
                                       class="control-label">{{trans_choice('communication::general.schedule',1)}} {{trans_choice('core::general.time',1)}}</label>
                                <flat-pickr
                                        v-model="scheduled_time"
                                        :config="{dateFormat: 'H:i',enableTime: true, noCalendar: true,}"
                                        class="form-control  @error('scheduled_time') is-invalid @enderror"
                                        name="scheduled_time" required>
                                </flat-pickr>
                                @error('scheduled_time')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="schedule_frequency"
                                       class="control-label">{{trans_choice('loan::general.schedule',1)}} {{trans_choice('loan::general.frequency',1)}}</label>
                                <input type="number" name="schedule_frequency"
                                       id="schedule_frequency" v-model="schedule_frequency"
                                       class="form-control @error('schedule_frequency') is-invalid @enderror numeric">
                                @error('schedule_frequency')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="schedule_frequency_type"
                                       class="control-label">{{trans_choice('loan::general.frequency',1)}} {{trans_choice('core::general.type',1)}}</label>
                                <select class="form-control  @error('schedule_frequency_type') is-invalid @enderror"
                                        name="schedule_frequency_type"
                                        v-model="schedule_frequency_type" id="schedule_frequency_type">
                                    <option value="days">{{trans_choice('loan::general.day',2)}}</option>
                                    <option value="weeks">{{trans_choice('loan::general.week',2)}}</option>
                                    <option value="months">{{trans_choice('loan::general.month',2)}}</option>
                                    <option value="years">{{trans_choice('loan::general.year',2)}}</option>
                                </select>
                                @error('schedule_frequency_type')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-md-12" v-if="campaign_type=='sms'">
                            <div class="form-group">
                                <label class="control-label"
                                       for="sms_gateway_id">{{trans_choice('communication::general.sms',1)}} {{trans_choice('communication::general.gateway',1)}}</label>
                                <select class="form-control @error('sms_gateway_id') is-invalid @enderror"
                                        name="sms_gateway_id"
                                        v-model="sms_gateway_id"
                                        id="sms_gateway_id" v-bind:required="campaign_type=='sms'">
                                    <option></option>
                                    <option v-for="sms_gateway in sms_gateways" v-bind:value="sms_gateway.id">
                                        @{{ sms_gateway.name }}
                                    </option>
                                </select>
                                @error('sms_gateway_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"
                                       for="communication_campaign_business_rule_id">{{trans_choice('communication::general.business_rule',1)}}</label>
                                <select class="form-control @error('communication_campaign_business_rule_id') is-invalid @enderror"
                                        name="communication_campaign_business_rule_id"
                                        v-model="communication_campaign_business_rule_id"
                                        id="communication_campaign_business_rule_id" v-on:click="change_business_rule"
                                        required>
                                    <option></option>
                                    <option v-for="business_rule in business_rules_list"
                                            v-bind:value="business_rule.id">
                                        @{{ business_rule.name }}
                                    </option>
                                </select>
                                @error('communication_campaign_business_rule_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12" v-if="campaign_type=='email'">
                            <div class="form-group">
                                <label class="control-label"
                                       for="communication_campaign_attachment_type_id">{{trans_choice('communication::general.report',1)}} {{trans_choice('communication::general.attachment',1)}}</label>
                                <select class="form-control @error('communication_campaign_attachment_type_id') is-invalid @enderror"
                                        name="communication_campaign_attachment_type_id"
                                        v-model="communication_campaign_attachment_type_id"
                                        id="communication_campaign_attachment_type_id">
                                    <option></option>
                                    <option v-for="attachment_type in attachment_types"
                                            v-bind:value="attachment_type.id">
                                        @{{ attachment_type.name }}
                                    </option>
                                </select>
                                @error('communication_campaign_attachment_type_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" id="business_rule_msg" v-show="business_rule_description">
                                <div class="alert alert-info">@{{ business_rule_description }}</div>
                            </div>
                        </div>
                        <div class="col-md-12"
                             v-if="communication_campaign_business_rule_id=='1'||communication_campaign_business_rule_id=='2'||communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='7'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='11'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'||communication_campaign_business_rule_id=='15'||communication_campaign_business_rule_id=='16'||communication_campaign_business_rule_id=='17'||communication_campaign_business_rule_id=='18'||communication_campaign_business_rule_id=='19'||communication_campaign_business_rule_id=='20'||communication_campaign_business_rule_id=='21'">
                            <div class="form-group">
                                <label class="control-label"
                                       for="branch_id">{{trans_choice('core::general.branch',1)}}</label>
                                <select class="form-control @error('branch_id') is-invalid @enderror"
                                        name="branch_id" id="branch_id" v-model="branch_id">
                                    <option></option>
                                    <option v-for="branch in branches" v-bind:value="branch.id">@{{ branch.name }}
                                    </option>
                                </select>
                                @error('branch_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12"
                             v-if="communication_campaign_business_rule_id=='1'||communication_campaign_business_rule_id=='2'||communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='7'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='11'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'||communication_campaign_business_rule_id=='15'||communication_campaign_business_rule_id=='16'||communication_campaign_business_rule_id=='17'||communication_campaign_business_rule_id=='18'||communication_campaign_business_rule_id=='19'||communication_campaign_business_rule_id=='20'||communication_campaign_business_rule_id=='21'">
                            <div class="form-group">
                                <label class="control-label"
                                       for="loan_officer_id">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.officer',1)}}</label>
                                <select class="form-control @error('loan_officer_id') is-invalid @enderror"
                                        name="loan_officer_id" id="loan_officer_id"
                                        v-model="loan_officer_id">
                                    <option></option>
                                    <option v-for="user in users" v-bind:value="user.id">
                                        @{{ user.first_name }} @{{ user.last_name }}
                                    </option>
                                </select>
                                @error('loan_officer_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div v-if="communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="from_x"
                                           class="control-label">{{trans_choice('communication::general.from_x',1)}}</label>
                                    <input type="text" name="from_x" id="from_x"
                                           v-model="from_x"
                                           class="form-control @error('from_x') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'">
                                    @error('from_x')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="to_y"
                                           class="control-label">{{trans_choice('communication::general.to_y',1)}}</label>
                                    <input type="text" name="to_y" id="to_y" v-model="to_y"
                                           class="form-control @error('to_y') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'">
                                    @error('to_y')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div v-if="communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='5'">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cycle_x"
                                           class="control-label">{{trans_choice('communication::general.cycle_x',1)}}</label>
                                    <input type="text" name="cycle_x" value="{{ old('cycle_x') }}" id="cycle_x"
                                           v-model="cycle_x"
                                           class="form-control @error('campaign_type') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='5'">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cycle_y"
                                           class="control-label">{{trans_choice('communication::general.cycle_y',1)}}</label>
                                    <input type="text" name="cycle_y" value="{{ old('cycle_y') }}" id="cycle_x"
                                           v-model="cycle_y"
                                           class="form-control @error('campaign_type') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='5'">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div v-show="communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='10'">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="overdue_x"
                                           class="control-label">{{trans_choice('communication::general.overdue_x',1)}}</label>
                                    <input type="text" name="overdue_x" value="{{ old('overdue_x') }}" id="overdue_x"
                                           v-model="overdue_x"
                                           class="form-control @error('campaign_type') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='10'">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="overdue_y"
                                           class="control-label">{{trans_choice('communication::general.overdue_y',1)}}</label>
                                    <input type="text" name="overdue_y" value="{{ old('overdue_y') }}" id="overdue_y"
                                           v-model="overdue_y"
                                           class="form-control @error('campaign_type') is-invalid @enderror"
                                           v-bind:required="communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='10'">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group"
                                 v-show="communication_campaign_business_rule_id=='3'||communication_campaign_business_rule_id=='4'||communication_campaign_business_rule_id=='5'||communication_campaign_business_rule_id=='6'||communication_campaign_business_rule_id=='8'||communication_campaign_business_rule_id=='9'||communication_campaign_business_rule_id=='10'||communication_campaign_business_rule_id=='12'||communication_campaign_business_rule_id=='13'||communication_campaign_business_rule_id=='14'||communication_campaign_business_rule_id=='15'||communication_campaign_business_rule_id=='16'||communication_campaign_business_rule_id=='17'||communication_campaign_business_rule_id=='18'||communication_campaign_business_rule_id=='19'||communication_campaign_business_rule_id=='20'||communication_campaign_business_rule_id=='21'">
                                <label class="control-label"
                                       for="loan_product_id">{{trans_choice('loan::general.loan',1)}} {{trans_choice('loan::general.product',1)}}</label>
                                <select class="form-control @error('loan_product_id') is-invalid @enderror"
                                        name="loan_product_id" id="loan_product_id"
                                        v-model="loan_product_id">
                                    <option></option>
                                    <option v-for="loan_product in loan_products" v-bind:value="loan_product.id">
                                        @{{ loan_product.name }}
                                    </option>
                                </select>
                                @error('loan_product_id')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12" v-if="campaign_type=='email'">
                            <div class="form-group">
                                <label for="subject"
                                       class="control-label">{{trans_choice('communication::general.email',1)}} {{trans_choice('communication::general.subject',1)}}</label>
                                <input type="text" name="subject" id="subject"
                                       class="form-control @error('subject') is-invalid @enderror" v-model="subject"
                                       v-bind:required="campaign_type=='email'">
                                @error('subject')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description"
                                       class="control-label">{{trans_choice('core::general.description',2)}}</label>
                                <textarea type="text" name="description" id="description"
                                          class="form-control @error('description') is-invalid @enderror"
                                          required v-model="description"></textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label"
                                       for="status">{{trans_choice('core::general.status',1)}}</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status"
                                        id="status" v-model="status" required>
                                    <option></option>
                                    <option value="pending">{{trans_choice('core::general.pending',1)}}</option>
                                    <option value="active">{{trans_choice('core::general.active',1)}}</option>
                                    <option value="inactive">{{trans_choice('core::general.inactive',1)}}</option>
                                </select>
                                @error('status')
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
            el: '#app',
            data: {
                name: '{{old('name',$communication_campaign->name)}}',
                subject: '{{old('subject',$communication_campaign->subject)}}',
                trigger_type: '{{old('trigger_type',$communication_campaign->trigger_type)}}',
                campaign_type: '{{old('campaign_type',$communication_campaign->campaign_type)}}',
                communication_campaign_business_rule_id: '{{old('communication_campaign_business_rule_id',$communication_campaign->communication_campaign_business_rule_id)}}',
                business_rule_description: '',
                sms_gateway_id: '{{old('sms_gateway_id',$communication_campaign->sms_gateway_id)}}',
                communication_campaign_attachment_type_id: '{{old('communication_campaign_attachment_type_id',$communication_campaign->communication_campaign_attachment_type_id)}}',
                branch_id: '{{old('branch_id',$communication_campaign->branch_id)}}',
                loan_officer_id: '{{old('loan_officer_id',$communication_campaign->loan_officer_id)}}',
                loan_product_id: '{{old('loan_product_id',$communication_campaign->loan_product_id)}}',
                scheduled_date: '{{old('scheduled_date',$communication_campaign->scheduled_date)}}',
                scheduled_time: '{{old('scheduled_time',$communication_campaign->scheduled_time)}}',
                schedule_frequency: '{{old('schedule_frequency',$communication_campaign->schedule_frequency)}}',
                schedule_frequency_type: '{{old('schedule_frequency_type',$communication_campaign->schedule_frequency_type)}}',
                from_x: '{{old('from_x',$communication_campaign->from_x)}}',
                to_y: '{{old('to_y',$communication_campaign->to_y)}}',
                cycle_x: '{{old('cycle_x',$communication_campaign->cycle_x)}}',
                cycle_y: '{{old('cycle_y',$communication_campaign->cycle_y)}}',
                overdue_x: '{{old('overdue_x',$communication_campaign->overdue_x)}}',
                overdue_y: '{{old('overdue_y',$communication_campaign->overdue_y)}}',
                status: '{{old('status',$communication_campaign->status)}}',
                description: '{{old('description',$communication_campaign->description)}}',
                business_rules_list: [],
                branches: branches,
                users: users,
                business_rules: business_rules,
                attachment_types: attachment_types,
                loan_products: loan_products,
                sms_gateways: sms_gateways

            },
            created: function () {
                this.business_rule_description = business_rules[this.communication_campaign_business_rule_id].description;
                this.business_rules_list = [];

                for (var key in business_rules) {
                    if (business_rules.hasOwnProperty(key)) {
                        if (this.trigger_type == 'direct' || this.trigger_type == 'schedule') {
                            if (business_rules[key].is_trigger == '0') {
                                var obj = {
                                    id: business_rules[key].id,
                                    name: business_rules[key].name,

                                };
                                this.business_rules_list.push(obj);
                            }
                        } else if (this.trigger_type == 'triggered') {
                            if (business_rules[key].is_trigger == '1') {
                                var obj = {
                                    id: business_rules[key].id,
                                    name: business_rules[key].name,

                                };
                                this.business_rules_list.push(obj);
                            }
                        }
                    }
                }
            },
            methods: {
                change_trigger_type() {
                    this.business_rules_list = [];

                    for (var key in business_rules) {
                        if (business_rules.hasOwnProperty(key)) {
                            if (this.trigger_type == 'direct' || this.trigger_type == 'schedule') {
                                if (business_rules[key].is_trigger == '0') {
                                    var obj = {
                                        id: business_rules[key].id,
                                        name: business_rules[key].name,

                                    };
                                    this.business_rules_list.push(obj);
                                }
                            } else if (this.trigger_type == 'triggered') {
                                if (business_rules[key].is_trigger == '1') {
                                    var obj = {
                                        id: business_rules[key].id,
                                        name: business_rules[key].name,

                                    };
                                    this.business_rules_list.push(obj);
                                }
                            }
                        }
                    }

                },
                change_business_rule() {
                    this.business_rule_description = '';
                    if (this.communication_campaign_business_rule_id) {
                        this.business_rule_description = business_rules[this.communication_campaign_business_rule_id].description
                    }

                },
                calculate() {

                }
            }
        });
    </script>
@endsection