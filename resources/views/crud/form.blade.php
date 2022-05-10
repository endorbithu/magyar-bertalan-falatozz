@include('header')
<div class="container">
    @include('status')

    <h1>{{ $title }} / {{ ($id ?? __('new')) }}</h1>

    <form method="post">
        @csrf
        @foreach($formdata as $attribute => $value)
            @if($value['type'] == 'hidden')
                <input type="{{ $value['type'] }}" class="form-control"
                       {{ $value['required'] }} id="{{ $value['name'] }}" name="{{ $value['name'] }}"
                       value="{{ $value['value'] }}">
            @elseif($value['type'] == 'textarea')
                <div class="form-group">
                    <label for="name">{{ $value['label'] }}</label>
                    <textarea class="form-control"
                              {{ $value['required'] }} id="{{ $value['name'] }}" name="{{ $value['name'] }}"
                    >{{ $value['value'] }}</textarea>
                </div>
            @else
                <div class="form-group">
                    <label for="name">{{ $value['label'] }}</label>
                    <input type="{{ $value['type'] }}" class="form-control"
                           {{ $value['required'] }} id="{{ $value['name'] }}" name="{{ $value['name'] }}"
                           value="{{ $value['value'] }}">
                </div>
            @endif
        @endforeach
        <input type="submit" class="btn btn-primary" value="@lang('Save')">
    </form>

</div>
@include('footer')
