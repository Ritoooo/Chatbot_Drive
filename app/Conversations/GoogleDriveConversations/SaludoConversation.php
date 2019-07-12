<?php

namespace App\Conversations\GoogleDriveConversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class SaludoConversation extends Conversation
{
    public function saludo()
    {
        $question = Question::create("Hola, soy Raphibot, en qué puedo ayudarte?")
            ->fallback('No se puede hacer la pregunta :"v')
            ->callbackId('saludo')
            ->addButtons([
                Button::create('Cuéntame un chiste')->value('chiste'),
                Button::create('Dame una cita elegante')->value('cita'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'chiste') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                } else {
                    $this->say(Inspiring::quote());
                }
            }
        $this->despedida();
        }
        );
    }

    public function despedida(){
        $buttons = [
            Button::create('Sí')->value('si'),
            Button::create('No me quiero ir sr Stark')->value('no'),
        ];
        $question = Question::create('Ya te quieres ir?')
        ->fallback('No puedo preguntar lo que quiero')
        ->callbackId('despedida')
        ->addButtons($buttons);
        return $this->ask($question, function(Answer $answer){
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue()==='si') {
                $this->say('Chao');
                }else{
                    $this->say('Me quedo!!');
                }
            }
        });
    }

    public function run()
    {
        $this->saludo();
    }
}
