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
        $question = Question::create('¿Qué documento necesitas?')
            ->fallback('Lo siento, no puedo responder por aquí')
            ->callbackId('docs');
        return $this->ask($question, function(Answer $answer){
            if($answer->getValue() === 'ninguno'){
                $this->say('Ok, ningún documento entonces');
            }
            else{
                $client = new Google_Client();
                putenv("GOOGLE_APPLICATION_CREDENTIALS=".__DIR__."/client_id.json");
                $client->useApplicationDefaultCredentials();
                $client->addScope(Google_Service_Drive::DRIVE);

                $driveService = new Google_Service_Drive($client);

                $files = $driveService->files->listFiles([
                    'q' => "name contains '".$answer->getValue()."' and mimeType = 'application/vnd.google-apps.document'",
                    'fields' => 'files(id,size)'
                ]);

                if ( count($files) < 1 ) {
                 $this->say('No pude encontrar el archivo :(');
                }
                else if ( count($files) > 1) {
                    $buttons = [];
                    foreach ( $files as $index ) {
                        $file = $driveService->files->get($index->id);
                        array_push($buttons, Button::create($file->name)->value($file->name));
                    }
                    $question = Question::create('He encontrado más de un documento que tienen el nombre con la palabra que me diste')
                        ->fallback('Lo siento mi pregunta no puede ser enviada :"v')
                        ->callbackId('files')
                        ->addButtons($buttons);
                     $this->ask($question, function(Answer $answer){                        
                            if ($answer->getValue()==='si') {
                                $this->say('Chao');
                            }else{
                                $this->say('Me quedo!!');
                            }
                        }
                    );
                }
                else{
                    $fileId = $files[0]->id;
                    $fileSize = intval($files[0]->size);
                    $http = $client->authorize();
                    $fp = fopen('texto.docx', 'w');
                    $fileID = $files[0]->id;
                      $response = $driveService->files->export($fileID, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', array(
                        'alt' => 'media'
                    ));
                    $content = $response->getBody()->getContents();
                      fwrite($fp, $content);
                    fclose($fp);
                    $metadata = $driveService->files->get($fileID);
                    $this->say(GenericTemplate::create()
                        ->addImageAspectRatio(GenericTemplate::RATIO_HORIZONTAL)
                        ->addElements([
                            Element::create($metadata->name)
                                ->subtitle('All about BotMan')
                                ->image('http://raphibot.herokuapp.com/logo.png')
                                ->addButton(ElementButton::create('Descargar')
                                    ->url('http://raphibot.herokuapp.com/texto.docx')
                                )
                                ->addButton(ElementButton::create('Verlo en Drive')
                                    ->url('https://docs.google.com/document/d/'.$files[0]->id.'/edit')
                                )
                                ->addButton(ElementButton::create('No descargar')
                                    ->payload('tellmemore')
                                    ->type('postback')
                                ),
                        ])
                    );
                    // Create attachment
                    $attachment = new File('http://raphibot.herokuapp.com/texto.docx', [
                        'custom_payload' => true,
                    ]);

                    // Build message object
                    $message = OutgoingMessage::create($metadata->name)
                                ->withAttachment($attachment);

                    // Reply message object
                    $this->say($message);    
                }    
            }
        });
    }

    public function run()
    {
        $this->google();
    }
}
