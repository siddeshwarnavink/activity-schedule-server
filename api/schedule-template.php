<?php

include_once './config/transform.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $schedule = DB::query("SELECT * FROM schedules WHERE isTemplate = 1");
    $schedule = array_map($GLOBALS['transformSchedule'], $schedule);
    $schedule = $schedule[0];

    echo json_encode($schedule);
} else if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postBody = file_get_contents("php://input");
    $postBody = json_decode($postBody);

    $scheduleId = DB::query("SELECT * FROM schedules WHERE isTemplate = 1")[0]['id'];

    DB::query("DELETE from task WHERE schedule = :id", [':id' => $scheduleId]);

    foreach ($postBody->list as $key => $post) {
        DB::query("INSERT INTO task (schedule, activity, placeTime, reason) VALUES (:schedule, :activity, :placeTime, :emptyStr)", [
            ':schedule' => $scheduleId,
            ':activity' => $post->activity,
            ':placeTime' => $post->placeTime,
            ':emptyStr' => ''
        ]);
    }

    echo json_encode([
        'message' => 'Template updated.'
    ]);
}