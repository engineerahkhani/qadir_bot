@extends('app')
@section('content')
    <div class="flex-center position-ref full-height">
    <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="content">
                @yield('section')
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
