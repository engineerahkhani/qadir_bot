<?php

Route::get('/', 'BotController@index');
Route::post('/message', 'HomeController@message');
Route::post('/competition', 'HomeController@competition');
Route::post('/setactive', 'HomeController@setactive');
Route::get('/sendMe/{message}', function (\Illuminate\Http\Request $request) {
    $stage = \App\Stage::find($request->message);
    $messages = str_split($stage->passage, 4096);
    $response = collect($messages)->map(function ($message) {
        Telegram::sendMessage([
            'chat_id' => 57647493,
            'text' => mb_convert_encoding($message, 'UTF-8')
        ]);

    });
    return $response;
});
Route::get('/test/{message}', function (\Illuminate\Http\Request $request) {
    $response = Telegram::sendMessage([
        'chat_id' => 57647493,
        'text' => mb_convert_encoding($request->message, 'UTF-8')
    ]);
return $response;
});
Route::get('/me', 'BotController@me');
Route::get('/set', function () {
    $res = Telegram::setWebhook([
        'url' => 'https://appakdl.com/qadir/'.env('TELEGRAM_BOT_TOKEN').'/webhook'
    ]);
    dd($res);

});

Route::post(env('TELEGRAM_BOT_TOKEN').'/webhook', 'BotController@respond');

Route::get('/us',function (){
    return \App\User::with('answers')->get();
} );
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');