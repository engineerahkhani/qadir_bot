@if(session()->has('result'))
    <div >
        @php
        $users = session('result');
        @endphp
        <br>
        <br>
        <legend>Found <span class="badge"> {{count($users)}} </span> Users</legend>
        @foreach($users as $user)
            <span class="label label-info">{{$user->chat_id}}</span>
        @endforeach
    </div>
@endif
