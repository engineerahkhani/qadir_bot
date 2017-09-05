@if(session()->has('message'))
    <div class="alert  {{session('class')}}">

        <h4><i class="icon fa fa-check"></i>&nbsp;{{session('message')}}</h4>
    </div>
@endif
