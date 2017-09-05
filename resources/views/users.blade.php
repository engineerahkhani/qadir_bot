@extends('home')
@section('section')
    <div class="row">
        <div class="col-xs-6 ">
            <h2 class="title ">
                {{count($users)}} Users
            </h2>
        </div>
        <div class="col-xs-6 ">
            <h2 class="title ">
                Active : <span>  {{\App\Setting::first()->active_stage}} </span>
            </h2>

            <form action={{url('https://appakdl.com/qadir/setactive')}} method="post" role="form">
                {{csrf_field()}}
                <div class="col-xs-6">
                    <div class="form-group">
                        <select name="id" class="form-control">
                            <option value="1"> 1</option>
                            <option value="2"> 2</option>
                            <option value="3"> 3</option>
                            <option value="4"> 4</option>
                            <option value="5"> 5</option>
                        </select>
                    </div>
                </div>
                <div class="col-xs-6">
                    <button type="submit" class="btn btn-success btn-block">Active It</button>
                </div>
            </form>

        </div>
    </div>
    <br>
    <div class="row">
        @include('flash')
        @include('form-validation')
        @include('result')

        <div class="col-xs-6">
            <form action={{url('https://appakdl.com/qadir/competition')}} method="post" role="form">
                {{csrf_field()}}
                <legend>competition</legend>
                <div class="form-group">
                    <label for="">User Id</label>
                    <select name="id" class="form-control">
                        <option value="1"> 1</option>
                        <option value="2"> 2</option>
                        <option value="3"> 3</option>
                        <option value="4"> 4</option>
                        <option value="5"> 5</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success btn-block">View Result</button>
            </form>
        </div>
        <div class="col-xs-6">
            <form action={{url('https://appakdl.com/qadir/message')}} method="post" role="form">
                {{csrf_field()}}
                <legend>Sent Message To User</legend>
                <div class="form-group">
                    <label for="">User Id</label>
                    <input type="text" class="form-control" name="id" required>
                </div>
                <div class="form-group">
                    <label for="">Message</label>
                    <textarea class="form-control" name="txt" required>
                    </textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send</button>
            </form>
        </div>
    </div>
    <div class="row">

    </div>
@endsection