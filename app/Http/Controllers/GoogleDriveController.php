<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\GoogleDriveConversations\SaludoConversation;


class GoogleDriveController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {   
        $botman = app('botman');

        $botman->listen();
    }

    public function tinker()    {   return view('tinker');    }

    public function startConversation(BotMan $bot)    { $bot->startConversation(new SaludoConversation());    }
    public function saludar(BotMan $bot)    { $bot->startConversation(new ExampleConversation());    }
    public function info(BotMan $bot)    { $bot->startConversation(new ExampleConversation());    }
}
