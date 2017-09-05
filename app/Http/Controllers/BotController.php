<?php

namespace App\Http\Controllers;

use App\Setting;
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

    public function index()
    {
        $users = User::all()->count();

        return view('index', compact('users'));
    }

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
            Log::info($update);
            if (collect($update->getMessage())->has('photo')) {
                $text = 'photo15427';
                $chatId = $update['message']['chat']['id'];
                $fileId = collect($update->getMessage())->get('photo')->last()['file_id'];
            } elseif (collect($update->getMessage())->has('document')) {
                $text = 'file';
                $chatId = $update['message']['chat']['id'];
            } else {
                $chatId = $update->getMessage()->getChat()->getId();
                $text = $update->getMessage()->getText();
            }
            User::firstOrCreate(['chat_id' => $chatId]);
            switch ($text) {
                case '/start':
                    $this->showMenu($chatId);
                    break;
                case 'متن خطبه':
                    $this->sendStage($chatId);
                    $this->showMenu($chatId,'برای نمایش سوالات از منوی زیر استفاده کنید.');
                    break;
                case 'سوالات':
                    $this->sendQuestions($chatId);
                    $this->showMenu($chatId);
                    break;
                case 'ارسال عکس':
                    $info = "عکس کودکتان را بصورتی که مشتش را گره کرده و دستش را بالا گرفته است را به ربات ارسال کنید و عکس قاب شده خروجی را با هشتگ #من_غدیریم یا #من_غدیری_ام در صفحه اینستاگرام خود منتشر کنید. توجه کنید که صفحه اینستاگرامتان باید پابلیک باشد.
به ۳ نفر از بهترین عکسها بر حسب قرعه کشی جایزی ۱ میلیون ریالی اهدا میگردد.";
                    $this->showMenu($chatId, $info);
                    break;
                case 'نتایج':
                    $info = "
با تشکر از شرکت در مسابقه بزرگ غدیر برندگان در کانال تلگرام مسابقه اعلام خواهد شد.
https://t.me/mosabegheghadir";
                    $this->showMenu($chatId, $info);
                    break;
                case 'درباره ما':
                    $info = 'خطبه غدیر محکم ترین سند بر ولایت امیرالمومنین و فرزندان معصوم ایشان علیهم السلام است. دقت در در این در این خطبه حدود 200 فضیلت برای حضرت علی بیان شده است و مفاهیم آن بصورت فردی، باعث محکم تر شدن اعتقاد ما در امامت و ولایت این بزرگواران می‌شود. نشر و فرهنگ سازی خطبه، منجر به کاهش غربت امیرالمؤمنین علیه السلام در بین مردم می‌گردد.
ین کار، در حقیقت امتثال امر پیامبر صلی الله علیه و آله و سلم است که فرمودند:"فَلْيُبَلِّغِ‏ الْحَاضِرُ الْغَائِبَ وَ الْوَالِدُ الْوَلَدَ إِلَى يَوْمِ الْقِيَامَةِ" ، "پس حاضرین به غائبین و پدران به فرزندان تا روز قیامت این خبر را برسانند..."
امیدواریم با توجه به تاکید علما و مراجع بزرگوار بتوانیم نقشی هرچند کوچک در فرهنگ سازی خطبه غدیر داشته باشیم  @mosabegheghadir';
                    $this->showMenu($chatId, $info);
                    break;
                case 'photo15427':
                    $this->uploadImg($chatId, $fileId);
                    $this->showMenu($chatId);
                    break;
                case 'file':
                    $info = 'فایل ارسال شده است. باید عکس ارسال کنید(دقت کنید Send as Photo ارسال شود)';
                    $this->showMenu($chatId, $info);
                    break;
                default:
                    $info = 'دستور وارد شده معتبر نمی باشد. لطفا از میان گزینه های منو انتخاب کنید. یا بروی /start کلیک کنید.';
                    $this->showMenu($chatId, $info);
            }
        }

    }

    public function showMenu($chatId, $info = 'برای شروع از منوی زیر یک گزینه را انتخاب کنید.')
    {

        $keyboard = [
            ['سوالات', 'متن خطبه'],
            ['درباره ما', 'نتایج'],
            [ 'ارسال عکس'],
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
        if ($id <= 5) {
            $stage = Stage::find($id);
            $messages = str_split($stage->passage, 4096);
            collect($messages)->map(function ($message) use ($chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => mb_convert_encoding($message, 'UTF-8')
                ]);
            });
        } else {
            $this->log('مسابقه غدیر از تاریخ ۱۳ شهریور۹۶ لغایت ۱۷ شهریور۹۶ برگزار می گردد.');
        }
    }

    public function sendQuestions($chatId)
    {
        $id = $this->findActiveStage();
        if ($id <= 5) {
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
            $this->log('مسابقه غدیر از تاریخ ۱۳ شهریور۹۶ لغایت ۱۷ شهریور۹۶ برگزار می گردد.');
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
        $img = Image::make('https://appakdl.com/qadir/1.png');
        $imgSource = Image::make('https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $response['file_path']);
        $imgSource->resize(500, 500);
        $img->insert($imgSource, 'top-left', 150, 150);
        $img->save('bar.png');
        $response = Telegram::sendPhoto([
            'chat_id' => $chatId,
            'photo' => 'https://appakdl.com/qadir/bar.png',
            'caption' => 'با هشتگ  #من_غدیری_ام" در صفحه اینستاگرام خود منتشر کنید تا در قرعه کشی 3 جایزه یک میلیون ریالی شرکت داده شوید'
        ]);

    }

    public function findActiveStage()
    {
        return Setting::first()->active_stage;

    }
}