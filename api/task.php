<?php

include_once './config/transform.php';

if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
    $postBody = file_get_contents("php://input");
    $postBody = json_decode($postBody);

    $id = $_GET['id'];

    $sqlQuery = "UPDATE task SET ";
    $injectionValues = [':id' => $id];

    if (isset($postBody->completed)) {
        $sqlQuery .= "completed = :completed";
        $injectionValues[':completed'] = $postBody->completed ? 1 : 0;
    }
    if (isset($postBody->reason)) {
        if (isset($postBody->completed)) {
            $sqlQuery .=  ", ";
        }

        $sqlQuery .= "reason = :reason";
        $injectionValues[':reason'] = $postBody->reason;
    }

    $sqlQuery .= " WHERE id = :id";

    DB::query($sqlQuery, $injectionValues);

    echo json_encode([
        'message' => 'Task updated'
    ]);
}
