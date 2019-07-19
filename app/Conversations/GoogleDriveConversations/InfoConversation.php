<?php

namespace App\Conversations\GoogleDriveConversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class InfoConversation extends Conversation
{
 	
 	public function inicio(){
 		$question = Question::create('Tenemos servicios de:')
 		->callbackid('inicio')
 		->addButtons([
 			Button::create('Google')->value('google'),
 			Button::create('Jira')->value('jira'),
 		]);
 	}   

    public function run()
    {
        $this->inicio();
    }
}
