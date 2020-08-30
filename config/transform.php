<?php

$GLOBALS['transformTask'] = function ($singleTask) {
    $singleTask['completed'] =  $singleTask['completed'] == '1';
    if(trim($singleTask['reason']) == '') {
        unset($singleTask['reason']);
    }

    return $singleTask;
};


$GLOBALS['transformSchedule'] = function ($singleSchedule) {
    $tasks =  DB::query("SELECT * FROM task WHERE schedule = :scheduleId", ['scheduleId' => $singleSchedule['id']]);

    $singleSchedule['timestamp'] =  $singleSchedule['timestampText']; 
    $singleSchedule['isTemplate'] = $singleSchedule['isTemplate'] == '1';
    $singleSchedule['isFavorite'] = $singleSchedule['isFavorite'] == '1';
    $singleSchedule['list'] =  array_map($GLOBALS['transformTask'], $tasks);

    unset($singleSchedule['timestampText']);
    return $singleSchedule;
};
