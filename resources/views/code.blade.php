@extends('home')
@section('section')
    <form action="/cods" method="post" role="form">
        {{csrf_field()}}
        <legend>NEW DISCOUNT COD:</legend>
        <div class="form-group">
            <label for="">COD</label>
            <input type="text" class="form-control" name="code" id="" placeholder="code...">
        </div>
        <div class="form-group">
            <label for="">QUANTITY</label>
            <input type="text" class="form-control" name="quantity" id="" placeholder="quantity...">
        </div>
        <div class="form-group">
            <label for="">AMOUNT</label>
            <input type="text" class="form-control" name="amount" id="" placeholder="amount...">
        </div>
        <button type="submit" class="btn btn-primary btn-block">CREATE</button>
    </form>
    <br>
    <hr>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>code</th>
            <th>quantity</th>
            <th>amount</th>
            <th>user</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\Code::all() as $code)
            <form method="post" role="form" action={{"/code/update/".$code->id}} >
                {{csrf_field()}}
                <tr>
                    <td>{{$code->id}}</td>
                    <td>
                        <input style="max-width: 120px" value={{$code->code}} type="text" class="form-control"
                               name="code">
                    </td>
                    <td>
                        <input style="max-width: 50px" value={{$code->quantity}} type="text" class="form-control"
                               name="quantity">
                    <td>
                        <input style="max-width: 50px" value={{$code->amount}} type="text" class="form-control"
                               name="amount">
                    </td>
                    <td>
                        @if(count($code->users))
                            @foreach($code->users as $user)
                                {{$user->email . ' , '}}
                            @endforeach
                        @else
                            '-'
                        @endif
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </td>
                </tr>
            </form>
        @endforeach
        </tbody>
    </table>
@endsection