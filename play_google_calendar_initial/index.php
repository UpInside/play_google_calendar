<?php

// Autoload gerado pelo Composer
require __DIR__ . '/vendor/autoload.php';

// Nova instância do Objeto Calendar
$calendar = new \Model\Calendar;

// Verificação da Autenticação
$client = $calendar->getClient();

if(!$client){
    echo "{$calendar->getTrigger()}";

    $linkAuth = $calendar->createClient();
    echo "<a href='{$linkAuth}'>Conceder permissão para gerenciar o Google Calendar</a>";

} else {
    echo "Suas credenciais são válidas!";
}

// Criação de um novo evento no Google Calendar
//$event = $calendar->createEvent('Teste de Sumário', 'Rua Huberto Hoden 100, Campeche, Florianópolis', 'Teste de Descrição', '2017-12-14 18:00:00', '2017-12-14 19:00:00', 'guh.web@hotmail.com');

// Deletar o evento do Google Calendar
//$event = $calendar->deleteEvent('js3ciocb6ilu2rv5qclbqklock');

// debug
var_dump($calendar, $client, $event);