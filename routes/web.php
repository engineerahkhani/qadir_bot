<?php

use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

Route::get('/', 'BotController@index');
Route::post('/message', 'HomeController@message');
Route::post('/competition', 'HomeController@competition');
Route::post('/setactive', 'HomeController@setactive');
Route::get('/sendMe', function () {
    $users = \App\User::pluck('chat_id');

    $txt = 'hi ahmad';
    $me = [57647493];
    collect($me)->map(function ($item) use ($txt){
        try {

            Telegram::sendMessage([
                'chat_id' => $item,
                'text' => mb_convert_encoding($txt, 'UTF-8')
            ]);
        } catch (TelegramResponseException $e) {

            $errorData = $e->getResponseData();

            if ($errorData['ok'] === false) {
                $message =  $errorData['error_code'] . ' ' . $errorData['description'];

                \Illuminate\Support\Facades\Log::info($message);
            }
        }
    });
    return $users;
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
        'url' => 'https://modernlifecomputer.com/'.env('TELEGRAM_BOT_TOKEN').'/webhook'
    ]);
    dd($res);

});

Route::post(env('TELEGRAM_BOT_TOKEN').'/webhook', 'BotController@respond');

Route::get('/us',function (){
    return \App\User::with('answers')->get();
} );
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');