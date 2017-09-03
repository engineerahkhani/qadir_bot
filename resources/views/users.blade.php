@extends('home')
@section('section')
    <div class="title m-b-md">
        Users
    </div>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>email</th>
            <th>active</th>
            <th>activation code</th>
            <th>devices</th>
            <th>created at</th>
        </tr>
        </thead>
        <tbody>
        @foreach(\App\User::all() as $user)
            <tr>
                <td>{{$user->id}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->is_active}}</td>
                <td>{{$user->activation_code}}</td>
                <td>
                    @if(count($user->devices))
                        @foreach($user->devices as $device)
                            {{$device->uuid}}</br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
                <td>{{$user->created_at}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection