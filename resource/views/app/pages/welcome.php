@extends('app.main')
@section('body')
<div class="my-5">
    <div class="text-center mb-4">
        <h1>{{ _l('lang.welcome') }}</h1>
    </div>
    <div class="card rounded-0">
        <pre class="card-body" style="height: 400px; overflow-y: auto;" id="terminal-body">you can read more information in github repository page.</pre>
    </div>
    <div class="form-group">
        <form id="terminal-form">
            {{ csrf() }}
            <input type="text" name="command" class="form-control rounded-0" placeholder="Command to Helper Terminal.">
        </form>
    </div>
</div>
@endsection

@section('footer')
<script>
    $('#terminal-form').on('submit', function(e) {
        e.preventDefault();
        let data = {};
        $(this).find('[name]').each((index, item) => data[$(item).attr('name')] = item.value);
        $.ajax({
            method: 'POST',
            url: '{{ route("store") }}',
            data: data,
            success: e => $('#terminal-body').html(e).scrollTop(99999999999),
            error: e => $('#terminal-body').html(JSON.parse(e.responseText).message)
        });
    });
</script>
@endsection