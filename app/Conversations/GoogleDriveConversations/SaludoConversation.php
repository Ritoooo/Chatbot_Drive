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

    public function google(){
        $question = Question::create('¿Qué documento necesitas?')
            ->fallback('Lo siento, no puedo responder por aquí')
            ->callbackId('docs');
        $this->ask($question, function(Answer $answer){
            if($answer->getValue() == 'ninguno'){
                $this->say('Ok, ningún documento entonces');
            }
            else{
                $client = new Google_Client();
                putenv("GOOGLE_APPLICATION_CREDENTIALS=".__DIR__."/client_id.json");
                $client->useApplicationDefaultCredentials();
                $client->addScope(Google_Service_Drive::DRIVE);

                $driveService = new Google_Service_Drive($client);

                $files = $driveService->files->listFiles([
                    'q' => "name contains '".$answer->gettext()."'",
                    'fields' => 'files(id, name, webViewLink, exportLinks, thumbnailLink, mimeType)'
                ]);

                if ( count($files) < 1 ) {
                 $this->say('No pude encontrar el archivo :(');
                }
                else if ( count($files) > 1) {
                    $buttons = [];
                    foreach ( $files as $index ) {
                        array_push($buttons, Button::create($index->name)->value($index->name));
                    }
                    $question = Question::create('He encontrado más de un documento que tienen el nombre con la palabra que me diste')
                        ->fallback('Lo siento mi pregunta no puede ser enviada :"v')
                        ->callbackId('files')
                        ->addButtons($buttons);
                     $this->ask($question, function(Answer $answer) use ($files) {
                        $finded = false;
                            foreach ( $files as $file ) {
                                if ($answer->getValue() === $file->name) {
                                    $this->sendFile($file);
                                    $finded = true;
                            }
                                }if ($answer->gettext() == 'ninguno') {
                                    $this->say('Ok, ninguno entonces');
                                }
                                elseif($finded ==false){
                                    $this->say('Lo siento, no te entendí');
                                }
                        }
                    );
                }
                else{
                    $this->sendFile($files[0]);
                }    
            }
        });
    }

    public function sendFile($file){

        $this->say(GenericTemplate::create()
            ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
            ->addElements([
                Element::create('$file->name')
                    ->subtitle('$file->name')
                    ->image('http://raphibot.herokuapp.com/logo.png')
                    ->addButton(ElementButton::create('Verlo en Drive')->url('$file->webViewLink'))
                    ->addButton(ElementButton::create('No descargar')
                        ->payload('tellmemore')
                        ->type('postback')
                    ),
            ])
        );
        
    }

    public function run()
    {
        $this->google();
    }
}
