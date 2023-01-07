@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('core::partials.flash.modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <div class="alert alert-icon
                    alert-{{ $message['level'] }}
        {{ $message['important'] ? 'alert-important' : '' }}"
             role="alert"
        >
            @if ($message['level']=='success')
                <em class="icon ni ni-check-circle"></em>
            @elseif ($message['level']=='danger')
                <em class="icon ni ni-cross-circle"></em>
            @else
                <em class="icon ni ni-alert-circle"></em>
            @endif

            {!! $message['message'] !!}

            @if ($message['important'])
                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-hidden="true"
                >&times;
                </button>
            @endif
        </div>
    @endif
@endforeach
@if (!empty($error) || request('error'))
    <div class="alert  alert-icon alert-danger alert-dismissible">
        <em class="icon ni ni-check-circle"></em>
        {{!empty($error)?$error:request('error')}}
        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-hidden="true"
        >
        </button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-hidden="true"
        >&times
        </button>
    </div>
@endif
@if (!empty($msg) || request('msg'))
    <div class="alert  alert-icon alert-success alert-dismissible">
        <em class="icon ni ni-check-circle"></em>
        {{!empty($msg)?$msg:request('msg')}}
        <button type="button"
                class="close"
                data-dismiss="alert"
                aria-hidden="true"
        >
        </button>
    </div>
@endif

{{ session()->forget('flash_notification') }}
