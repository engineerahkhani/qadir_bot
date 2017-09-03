<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Monolog\Handler\DynamoDbHandler;

class Restore extends Mailable
{
    use Queueable, SerializesModels;


    public $user,$code;
    public function __construct(User $user,$code)
    {

    $this->user = $user;
    $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('restore-email',['user'=>$this->user,'code'=>$this->code]);
    }

}
