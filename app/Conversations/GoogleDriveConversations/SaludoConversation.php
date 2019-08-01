<?php

namespace App\Conversations\GoogleDriveConversations;

use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Google_Client;
use Google_Service_Drive;

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

    public function google(){
        $client = new Google_Client();
        putenv("GOOGLE_APPLICATION_CREDENTIALS=".__DIR__."/client_id.json");
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google_Service_Drive::DRIVE);

        $driveService = new Google_Service_Drive($client);

      $files = $driveService->files->listFiles([
         'q' => "name='Estructura_Del_Avance_del_Informe_Final'",
         'fields' => 'files(id,size)'
         ]);
       // $this->say($files[0]->id);

        $this->say(GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
            ->addElements([
                Element::create('BotMan Documentation')
                    ->subtitle('All about BotMan')
                    ->image('http://raphibot.herokuapp.com/logo.png')
                    ->addButton(ElementButton::create('visit')
                        ->url('http://botman.io')
                    )
                    ->addButton(ElementButton::create('tell me more')
                        ->payload('tellmemore')
                        ->type('postback')
                    ),
                Element::create('BotMan Laravel Starter')
                    ->subtitle('This is the best way to start with Laravel and BotMan')
                    ->image('http://raphibot.herokuapp.com/logo.png')
                    ->addButton(ElementButton::create('visit')
                        ->url('https://github.com/mpociot/botman-laravel-starter')
                    ),
            ])
        );
        // Create attachment
        $attachment = new File('http://raphibot.herokuapp.com/logo.png', [
            'custom_payload' => true,
        ]);

        // Build message object
        $message = OutgoingMessage::create('This is my text')
                    ->withAttachment($attachment);

        // Reply message object
        $this->say($message);        
    }

    public function run()
    {
        $this->google();
    }
}
