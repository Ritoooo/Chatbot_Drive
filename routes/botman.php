<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\GoogleDriveController;
use BotMan\BotMan\Middleware\ApiAi;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Attachments\File;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use App\Product;
use App\TypeProduct;


$botman = resolve('botman');

$botman->hears('Hola', function ($bot) {
    $bot->reply('Hola! Soy Raphibot, en qué puedo ayudarte?');
});
$botman->hears('ayuda', function ($bot) {
    $bot->reply('Tenemos un gran catálogo de servicios');
});

$botman->hears('carga_paletizada', function ($bot) {
    $bot->reply('Elejiste carga_paletizada');
});
$botman->hears('picking', function ($bot) {
    $bot->reply('Elejiste');
});
$botman->hears('mueble_metalico', function ($bot) {
    $bot->reply('Elejiste mueble_metalico');
});
$botman->hears('estructura_especial', function ($bot) {
    $bot->reply('Elejiste estructura_especial');
});




$botman->hears('hilos', GoogleDriveController::class.'@startConversation');







$botman->hears('Start conversation', BotManController::class.'@startConversation');









$botman->hears('google',function(BotMan $bot){

$bot->reply(GenericTemplate::create()
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


});







$dialogflow = ApiAi::create('0f9adfb0ad9549adaccb8069b24eba9d')->listenForAction();

$botman->middleware->received($dialogflow);
$botman->hears('input.help', function (BotMan $bot) {
    
    $extras = $bot->getMessage()->getExtras();
    $apiReply = $extras['apiReply'];
    $apiAction = $extras['apiAction'];
    $apiIntent = $extras['apiIntent'];
    $producto = Product::where('type_id',1)->get();
    $bot->reply(Question::create('Te presento esta lista de categorías, seleccona el tipo de producto que buscas')->addButtons([
        Button::create($producto[0]['nombre'])->value($producto[0]['nombre']),
        Button::create($producto[1]['nombre'])->value($producto[1]['nombre']),
        Button::create($producto[2]['nombre'])->value($producto[2]['nombre']),
    ]));
    
     
})->middleware($dialogflow);

//$botman->hears('input.user_ask_info', GoogleDriveController::class.'infoConversation');

$botman->hears('input.user_ask_info', function (BotMan $bot) {

    $extras = $bot->getMessage()->getExtras();
    $apiReply = $extras['apiReply'];
    $apiAction = $extras['apiAction'];
    $apiIntent = $extras['apiIntent'];
     
    $bot->reply(Question::create('Tengo datos de:')->addButtons([
        Button::create('Google')->value('google'),
        Button::create('Jira')->value('jira'),
    ]));

})->middleware($dialogflow);

$botman->hears('input.cotizar', function (BotMan $bot) {
    
    $attachment = new Image('http://www.megaestruc.com/images/RACK%20CONVENCIONAL/rc1.jpg');

    $message = OutgoingMessage::create('')
                ->withAttachment($attachment);

    $extras = $bot->getMessage()->getExtras();
    $apiReply = $extras['apiReply'];
    $apiAction = $extras['apiAction'];
    $apiIntent = $extras['apiIntent'];

    $apiParameters = $extras['apiParameters'];
    if ($extras['apiParameters']) {
        
        if ($apiParameters['phone-number']) {
            $telefono = $apiParameters['phone-number'];
            $bot->reply("Tu número de celular es {$telefono}");
        }
    }
    
    $bot->reply($message);
     
})->middleware($dialogflow);