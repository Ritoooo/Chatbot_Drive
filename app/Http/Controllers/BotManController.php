<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\GoogleDriveConversations\SaludoConversation;


class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {   //info('incoming', request()->all()); // this line was added
        $botman = app('botman');

        $botman->listen();
    }

    public function tinker()    {   return view('tinker');    }

    public function startConversation(BotMan $bot)    { $bot->startConversation(new ExampleConversation());    }
    public function saludar(BotMan $bot)    { $bot->startConversation(new ExampleConversation());    }
}
