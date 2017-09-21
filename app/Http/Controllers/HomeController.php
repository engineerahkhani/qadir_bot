<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Setting;
use App\Stage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('users', compact('users'));
    }

    public function message(Request $request)
    {
        Validator::make($request->all(), [
            'txt' => 'required|max:4000',
            'id' => 'required|exists:users,chat_id',
        ])->validate();

        try {

             Telegram::sendMessage([
                'chat_id' => $request->id,
                'text' => mb_convert_encoding($request->txt, 'UTF-8')
            ]);
            $message = 'Sent';
            $class = 'alert-success';
        } catch (TelegramResponseException $e) {

            $errorData = $e->getResponseData();

            if ($errorData['ok'] === false) {
                $message =  $errorData['error_code'] . ' ' . $errorData['description'];
                $class = 'alert-danger';
            }
        }

        return back()->with(['message' => $message, 'class' => $class]);
    }
    public function setactive(Request $request)
    {
        Validator::make($request->all(), [
            'id' => 'required|exists:stages',

        ])->validate();
       Setting::first()->update(['active_stage'=>$request->id]);
        return back()->with(['message' => 'Sent', 'class' => 'alert-success']);
    }

    public function competition(Request $request)
    {

        Validator::make($request->all(), [
            'id' => 'required|in:1,2,3,4,5',
        ])->validate();
        $questions = Question::where('stage_id', $request->id)->get();
        $correct_answers = collect($questions->pluck('correct'));
        \DB::enableQueryLog();
        $i = 0;
        $rightAnswers = $questions->reduce(function ($query, $question) use ($correct_answers, &$i) {
            $query = $query->select('user_id', 'question_id', \DB::raw('count(*) as total_correct_answers'))->orWhere(function ($q) use ($correct_answers, $question, &$i) {
                $q->where('question_id', $question->id)->where('selected', $correct_answers[$i]);
                $i++;
            })->with('user');
            return $query;
        }, new Answer)->groupBy('user_id')->having('total_correct_answers', $questions->count())->get();
        $result = collect($rightAnswers)->map(function ($item) {
            return $item->user;
        });

        return back()->with(['class' => 'alert-success', 'result' => $result]);
    }
}
