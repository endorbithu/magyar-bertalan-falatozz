@include('header')
<div class="container">
    @include('status')

    <h1>{{ $title }} {{ ($id ? ' / ' . $id : '') }}</h1>
    <div class="container">
        @foreach($model as $value)
            <div class="row">
                <span style="font-weight: bold">{{ $value['attribute_name']  }}: </span><br>
                <span class="">{{ $value['value']   }}</span>
            </div>
        @endforeach

    </div>
</div>

@include('footer')
