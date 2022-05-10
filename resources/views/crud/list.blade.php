@include('header')
<h1>{{ $title }}</h1>

<a class="btn btn-sm btn-success"
   href="{{ route('crud.form',['eloquentModelClass' => $eloquentModelClass]) }}">@lang('new')</a>
<br><br>
@if(empty($list['data']))
    <h3>@lang('No entity available now!')</h3>
@endif

<table class="table datatable">
    <thead>
    <tr>
        <th scope="col">@lang('Operations')</th>
        @foreach($list['header'] as $headItem)
            <th scope="col">{{ $headItem  }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($list['data'] as $id => $row)
        <tr>
            <td>
                <a class="btn btn-sm btn-primary"
                   href="{{ route('crud.form',['eloquentModelClass' => $eloquentModelClass, 'id' => $id]) }}">@lang('edit')</a>
                <form style="display: inline" class="delete" method="post"
                      action="{{ route('crud.delete',['eloquentModelClass' => $eloquentModelClass]) }}">
                    @csrf
                    <input type="hidden" name="id" value="{{$id}}">
                    <button class="btn btn-sm btn-danger delete-btn">@lang('delete')</button>
                </form>
            </td>
            @foreach($row as $attr => $data)
                <td>
                    @if($attr === 'id')
                        <a href="{{ route('crud.show',['eloquentModelClass' => $eloquentModelClass, 'id' => $id]) }}"> {{ $data }}</a>
                    @else
                        {{ $data }}
                    @endif
                </td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>

</div>
<script>
    $(document).ready(function () {
        $('.datatable').DataTable();

        $('.delete').submit(function (event) {
            if (!confirm('Delete entity?')) {
                event.preventDefault();
            }
        });
    });
</script>
@include('footer')
