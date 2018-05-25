<?php

$read = new \CRUD\Read;

echo "<div class='list'>";

$read->readFull("SELECT
                        * 
                        
                        FROM appointment a 
                        INNER JOIN schedule s ON s.schedule_id = a.appointment_schedule_id");

if($read->getResult()){
    foreach($read->getResult() as $appointment){
        $appointment = (object) $appointment;
        echo "<p>{$appointment->appointment_title} - " . date('d/m/Y H:i', strtotime($appointment->schedule_tstamp)) ." - <span class='j_delete btn error' data-id='{$appointment->appointment_id}'>Deletar</span></p>";

    }
} else {
    echo "<p>Não há consultas agendadas no momento!</p>";
}
echo "</div>";
