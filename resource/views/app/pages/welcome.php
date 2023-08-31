@extends('app.main')
@section('body')
<div class="my-5">
    <div class="text-center mb-4">
        <h1>{{ _l('lang.welcome') }}</h1>
    </div>
    <div class="card rounded-0">
        <pre class="card-body" style="height: 400px; overflow-y: auto;"
            id="terminal-body">you can read more information in github repository page.</pre>
    </div>
    <div class="form-group">
        <form id="terminal-form">
            {{ csrf() }}
            <input type="text" name="command" class="form-control rounded-0" placeholder="Command to Helper Terminal.">
        </form>
    </div>
</div>
@endsection