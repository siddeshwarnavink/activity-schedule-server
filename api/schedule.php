<?php

include_once './config/transform.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $schedule = DB::query("SELECT * FROM schedules WHERE id = :id", [':id' => $_GET['id']]);
        $schedule = array_map($GLOBALS['transformSchedule'], $schedule);
        $schedule = $schedule[0];

        echo json_encode($schedule);
    } else {
        $schedules = DB::query("SELECT * FROM schedules WHERE isTemplate = 0");
        $schedules = array_map($GLOBALS['transformSchedule'], $schedules);

        echo json_encode($schedules);
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postBody = file_get_contents("php://input");
    $postBody = json_decode($postBody);

    DB::query("INSERT INTO schedules (timestampText) VALUE (:timestampText)", [':timestampText' => $postBody->timestamp]);
    $id = DB::query("SELECT id from schedules WHERE timestampText=:timestampText;", [':timestampText' => $postBody->timestamp])[0]['id'];

    foreach ($postBody->list as $key => $post) {
        DB::query("INSERT INTO task (schedule, activity, placeTime, reason) VALUES (:schedule, :activity, :placeTime, :emptyStr)", [
            ':schedule' => $id,
            ':activity' => $post->activity,
            ':placeTime' => $post->placeTime,
            ':emptyStr' => ''
        ]);
    }

    echo json_encode([
        'message' => 'Schedule created successfully.',
    ]);
    http_response_code(201);
} else if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    if (isset($_GET['id'])) {
        $postBody = file_get_contents("php://input");
        $postBody = json_decode($postBody);

        $id = $_GET['id'];

        $sqlQuery = "UPDATE schedules SET ";
        $injectionValues = [':id' => $id];

        if (isset($postBody->isFavorite)) {
            $sqlQuery .= "isFavorite = :isFavorite";
            $injectionValues[':isFavorite'] = $postBody->isFavorite ? 1 : 0;
        }

        $sqlQuery .= " WHERE id = :id";

        DB::query($sqlQuery, $injectionValues);

        echo json_encode([
            'message' => 'Schedule updated'
        ]);
    }
} else if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    if (isset($_GET['id'])) {
        DB::query("DELETE FROM schedules WHERE id = :id", [':id' => $_GET['id']]);

        echo json_encode([
            'message' => 'Schedule deleted'
        ]);
    }
}
