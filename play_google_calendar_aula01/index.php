<?php
require_once __DIR__ . '/Config.inc.php';

$url = filter_input(INPUT_GET, 'url', FILTER_DEFAULT);
$url = explode('/', $url);
$url = array_map('strip_tags', $url);

$url[0] = (empty($url[0]) ? 'index' : $url[0]);

?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Agendamento</title>
    <link rel="stylesheet" href="<?= BASE; ?>/system/style.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="<?= BASE; ?>/_cdn/jquery.form.min.js"></script>
    <script src="<?= BASE; ?>/system/system.js"></script>
</head>
<body>

<div class="container">
    <div class="content">

        <?php require __DIR__ . '/system/_inc/header.php'; ?>

        <main class="main_main">
            <?php require __DIR__ . '/system/' . $url[0] . '.php'; ?>
        </main>

        <?php require __DIR__ . '/system/_inc/footer.php'; ?>

    </div>
</div>
</body>
</html>
<?php

// Nova instância do Objeto Calendar
//$calendar = new \Model\Calendar;
//
//// Verificação da Autenticação
//$client = $calendar->getClient();
//
//if(!$client){
//    echo "{$calendar->getTrigger()}";
//
//    $linkAuth = $calendar->createClient();
//    echo "<a href='{$linkAuth}'>Conceder permissão para gerenciar o Google Calendar</a>";
//
//} else {
//    echo "Suas credenciais são válidas!";
//}

// Criação de um novo evento no Google Calendar
//$event = $calendar->createEvent('Teste de Sumário', 'Rua Huberto Hoden 100, Campeche, Florianópolis', 'Teste de Descrição', '2017-12-14 18:00:00', '2017-12-14 19:00:00', 'guh.web@hotmail.com');

// Deletar o evento do Google Calendar
//$event = $calendar->deleteEvent('js3ciocb6ilu2rv5qclbqklock');

// debug
//var_dump($calendar, $client, $event);