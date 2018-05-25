<?php
/**
 * Created by PhpStorm.
 * User: gustavoweb
 * Date: 14/12/2017
 * Time: 17:13
 */

$code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT);

if(!empty($code)){
    require __DIR__ . '/vendor/autoload.php';
    $calendar = new \Model\Calendar;

    $calendar->setAccessToken($code);
    header("Location: https://localhost/play/google_calendar");
}