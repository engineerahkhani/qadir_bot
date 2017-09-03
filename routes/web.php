<?php


use Intervention\Image\ImageManagerStatic as Image;



Route::get('/', function () {

    $carbon = new Carbon();

    return $carbon->addDay(1)->dayOfWeek;
    //  return  Storage::url('1.jpg');
  $response = Telegram::getFile(['file_id' => 'AgADBAADCaoxG1VhWVEiBOOKY7Wp6PZQ4BkABPndaLSl6BwkJ0sCAAEC']);

    $img = Image::make('https://appakdl.com/qadir/1.jpg');
    $imgSource = Image::make('https://api.telegram.org/file/bot'.env('TELEGRAM_BOT_TOKEN').'/'.$response['file_path']);

// now you are able to resize the instance
    $imgSource->resize(320, 300);

// and insert a watermark for example
    $img->insert($imgSource,'top-right',170,300);

// finally we save the image as a new file
    $img->save('bar.jpg');
 //  dd($response);
    /*    $stage = \App\Stage::find(1);
        $messages = str_split($stage->passage, 4096);

        collect($messages)->map(function ($message)  {
            $response = Telegram::sendMessage([
                'chat_id' => 57647493,
                'text' => mb_convert_encoding($message, 'UTF-8')
            ]);

        });*/

    return 'home';
});
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
        'url' => 'https://appakdl.com/qadir/436118027:AAF1fRvLFdsxuiZ2kiz2X2Rz_cZNo9dXai0/webhook'
    ]);
    dd($res);

});

Route::post('436118027:AAF1fRvLFdsxuiZ2kiz2X2Rz_cZNo9dXai0/webhook', 'BotController@respond');