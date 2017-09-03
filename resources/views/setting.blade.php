@extends('home')
@section('section')
    @php
        $setting = \App\Setting::first();
    @endphp
    <form action="/setting" method="post" role="form">
        {{csrf_field()}}
        <legend>SETTING:</legend>
        <div class="form-group">
            <label for="">VERSION</label>
            <input type="text" class="form-control" name="version" id="" placeholder="version..."
                   value={{$setting->version}}>
        </div>
        <div class="form-group">
            <label for="">SERVER ID</label>
            <input type="text" class="form-control" name="server_id" id="" placeholder="server_id..."
                   value={{$setting->server_id}}>
        </div>
        <div class="form-group">
            <label for="">SHARED SECRET</label>
            <input type="text" class="form-control" name="shared_secret" id="" placeholder="shared_secret..."
                   value={{$setting->shared_secret}}>
        </div>
        <div class="form-group">
            <label for="">USER NAME</label>
            <input type="text" class="form-control" name="user_name" id="" placeholder="user_name..."
                   value={{$setting->user_name}}>
        </div>
        <div class="form-group">
            <label for="">PASSWORD</label>
            <input type="text" class="form-control" name="password" id="" placeholder="password..."
                   value={{$setting->password}}>
        </div>
        <button type="submit" class="btn btn-primary btn-block">SUBMIT</button>
    </form>
@endsection