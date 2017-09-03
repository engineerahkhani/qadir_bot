<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Answer;
use App\Stage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram;
use Telegram\Bot\Keyboard\Keyboard;

use Intervention\Image\ImageManagerStatic as Image;

class BotController extends Controller
{
    public function me()
    {
        $response = Telegram::getMe();
        return $response;
    }

//$update['message']['photo'][1]['file_id']
//$update['message']['chat']['id']
    public function respond(Request $request)
    {
        Log::useDailyFiles(storage_path() . '/logs/webhook.log');
        if (isset($request['callback_query'])) {
            Log::info($request->all());
            $data = $request['callback_query']['data'];
            $chatId = $request['callback_query']['message']['chat']['id'];
            $callback_query_id = $request['callback_query']['id'];
            $user = User::firstOrCreate(['chat_id' => $chatId]);
            $this->getUserAnswer($chatId, $callback_query_id, $data, $user);
        } else {
            $update = Telegram::getWebhookUpdates();
            $chatId = $update->getMessage()->getChat()->getId();
            $text = $update->getMessage()->getText();
            User::firstOrCreate(['chat_id' => $chatId]);
            switch ($text) {
                case '/start':
                    $this->showMenu($chatId);
                    break;
                case 'متن خطبه':
                    $this->sendStage($chatId);
                    break;
                case 'سوالات':
                    $this->sendQuestions($chatId);
                    break;
                case 'نتایج':
                    $info = "بعد از عید سعید غدیر خم اعلام خواهد شد";
                    $this->showMenu($chatId, $info);
                    break;
                case 'درباره ما':
                    $info = 'درباره ما...';
                    $this->showMenu($chatId, $info);
                    break;
                default:
                    $info = 'دستور وارد شده معتبر نمی باشد. لطفا از میان گزینه های منو انتخاب کنید. یا بروی /start کلیک کنید.';
                    $this->showMenu($chatId, $info);
            }
        }

    }

    public function showMenu($chatId, $info = 'برای شروع از منوی زیز یک گزینه را انتخاب کنید.')
    {

        $keyboard = [
            ['سوالات', 'متن خطبه'],
            ['درباره ما', 'نتایج'],
        ];

        $reply_markup = Telegram::replyKeyboardMarkup([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);
        Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $info,
            'reply_markup' => $reply_markup
        ]);

    }

    public function sendStage($chatId)
    {
        $id = $this->findActiveStage();
        if ($id < 10) {
            $stage = Stage::find($id);
            $this->log($stage->title);
            $messages = str_split($stage->passage, 4096);
            collect($messages)->map(function ($message) use ($chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => mb_convert_encoding($message, 'UTF-8')
                ]);
            });
        } else {
            $this->log('خطبه ایی یافته نشد');
        }
    }

    public function sendQuestions($chatId)
    {

        $id = $this->findActiveStage();
        if ($id < 10) {
            $stage = Stage::find($id);
            $stage->questions->map(function ($question) use ($chatId) {
                $inlineLayout = [
                    [
                        Keyboard::inlineButton(['text' => 'گزینه ج', 'callback_data' => 'c_' . $question->id]),
                        Keyboard::inlineButton(['text' => 'گزینه ب', 'callback_data' => 'b_' . $question->id]),
                        Keyboard::inlineButton(['text' => 'گزینه الف', 'callback_data' => 'a_' . $question->id]),
                    ]
                ];
                $keyboard = Telegram::replyKeyboardMarkup([
                    'inline_keyboard' => $inlineLayout
                ]);
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $question->id . ' - ' . $question->title,
                ]);
                $txt = '';
                $txt .= 'الف ' . $question->option_a . chr(10);
                $txt .= 'ب ' . $question->option_b . chr(10);
                $txt .= 'ج ' . $question->option_c . chr(10);
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $txt,
                    'reply_markup' => $keyboard
                ]);

            });
        } else {
            $this->log('خطبه ایی یافته نشد');
        }
    }

    public function getUserAnswer($chatId, $callback_query_id, $text, $user)
    {
        $option = explode("_", $text);
        $answer = Answer::updateOrCreate(['user_id' => $user->id, 'question_id' => $option[1]], ['selected' => $option[0]]);

        switch ($option[0]) {
            case 'a';
                $this->showReply($chatId, $callback_query_id, 'الف', $option[1]);
                break;
            case 'b';
                $this->showReply($chatId, $callback_query_id, 'ب', $option[1]);
                break;
            case 'c';
                $this->showReply($chatId, $callback_query_id, 'ج', $option[1]);
                break;
            default:
                $info = 'جواب شما ثبت نگردید. مجددا تلاش نمایید.';
                $this->showMenu($chatId, $info);
        }

    }

    public function showReply($chatId, $cbid, $choise, $questionId)
    {
        $message = 'جواب سوال ' . $questionId . ' گزینه ' . $choise . ' ثبت شد. ';
        $response = Telegram::sendMessage([
            'callback_query_id' => $cbid,
            'chat_id' => $chatId,
            'text' => $message
        ]);
    }

    public function log($error = 'hii')
    {
        $response = Telegram::sendMessage([
            'chat_id' => 57647493,
            'text' => mb_convert_encoding($error, 'UTF-8')
        ]);
    }

    public function uploadImg($chatId, $fileId)
    {
        $response = Telegram::getFile(['file_id' => $fileId]);

        $img = Image::make('https://appakdl.com/qadir/1.jpg');
        $imgSource = Image::make('https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $response['file_path']);
        $imgSource->resize(320, 300);
        $img->insert($imgSource, 'top-right', 170, 300);
        $img->save('bar.jpg');

    }

    public function findActiveStage()
    {
        $carbon = new Carbon();

        return $carbon->addDay(1)->dayOfWeek;
    }
}