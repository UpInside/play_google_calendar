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

        $read->readFull('SELECT 
                                * 
                                
                                FROM schedule s 
                                WHERE s.schedule_tstamp = :tstamp
                                ', "tstamp={$postData->schedule_tstamp}");

        if (!$read->getResult()) {

            $schedule = [
                'schedule_tstamp' => $postData->schedule_tstamp,
            ];

            $create->create('schedule', $schedule);
            $json['msg'] = ['success', 'Show, esse horário já está disponível para consulta :)'];
        } else {
            $json['msg'] = ['error', 'Ooops, não foi possível cadastrar esse horário!'];
        }
        break;

    case 'add_appointment':

        if (empty($postData->appointment_schedule_id)) {
            $json['msg'] = ['error', 'Ooops, é necessário informar um dia e horário para a consulta!'];
            break;
        }

        $read->read('schedule', "WHERE schedule_id = :id", "id={$postData->appointment_schedule_id}");
        $schedule = ($read->getResult() ? (object) $read->getResult()[0] : null);

        if (!empty($schedule) && $schedule->schedule_status == 1) {
            $json['msg'] = ['error', 'Ooops, esse horário já está reservado :('];
            break;
        }

        if (!empty($postData->appointment_email) && filter_var($postData->appointment_email, FILTER_VALIDATE_EMAIL)) {
            $attendees = $postData->appointment_email;
        } else {
            $attendees = null;
        }

        $client = $googleCalendar->getClient();
        $event = $googleCalendar->createEvent($postData->appointment_title, $postData->appointment_location, $postData->appointment_description, $schedule->schedule_tstamp, $schedule->schedule_tstamp, $attendees);

        //create
        $appointment = [
            'appointment_schedule_id' => $postData->appointment_schedule_id,
            'appointment_title' => $postData->appointment_title,
            'appointment_description' => $postData->appointment_description,
            'appointment_location' => $postData->appointment_location,
            'appointment_email' => $postData->appointment_email,
            'appointment_event_id' => $event->id,
        ];

        $create->create('appointment', $appointment);

        $update->update('schedule', ['schedule_status' => 1], "WHERE schedule_id = :id", "id={$postData->appointment_schedule_id}");
        $json['msg'] = ['success', 'Show, a consulta foi agendada com sucesso :)'];
        break;

    case 'delete_appointment':

        $read->read('appointment', "WHERE appointment_id = :id", "id={$postData->value}");
        $appointment = ($read->getResult() ? (object) $read->getResult()[0] : null);

        if (empty($appointment)) {
            $json['msg'] = ['error', 'Ooops, não localizamos a consulta! Tente novamente. :('];
            break;
        } else {

            if(!empty($appointment->appointment_event_id)){
                $client = $googleCalendar->getClient();
                $event = $googleCalendar->deleteEvent($appointment->appointment_event_id);
            }

            $update->update('schedule', ['schedule_status' => NULL], "WHERE schedule_id = :id", "id={$appointment->appointment_schedule_id}");

            $delete->delete('appointment', "WHERE appointment_id = :id", "id={$postData->value}");
            $json['redirect'] = true;
        }
        break;

    default:
        $json['msg'] = ['error', 'Opção selecionada inválida!'];
        break;
}

echo json_encode($json);