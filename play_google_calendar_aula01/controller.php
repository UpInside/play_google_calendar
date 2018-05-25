<?php
/**
 * Created by PhpStorm.
 * User: gustavoweb
 * Date: 21/12/2017
 * Time: 09:53
 */
require_once __DIR__ . '/Config.inc.php';

$postData = filter_input_array(INPUT_POST, FILTER_DEFAULT);
$case = $postData['action'];

unset($postData['action']);

$postData = (object) $postData;

$read = new \CRUD\Read;
$create = new \CRUD\Create;
$update = new \CRUD\Update;
$delete = new \CRUD\Delete;

$googleCalendar = new \Model\Calendar;

switch ($case) {
    case 'add_schedule':
        break;

    case 'add_appointment':
        break;

    case 'delete_appointment':
        break;

    default:
        $json['msg'] = ['error', 'Opção selecionada inválida!'];
        break;
}

echo json_encode($json);