<?php
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hola', function ($bot) {
    $bot->reply('Hola! Soy Raphibot de Megaestruc, en qué puedo ayudarte?');
});
$botman->hears('Start conversation', BotManController::class.'@startConversation');
