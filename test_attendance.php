<?php
require_once '/home/monsur/Documents/farm-glow-backend/bootstrap/app.php';

$app = require '/home/monsur/Documents/farm-glow-backend/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = \Illuminate\Http\Request::create(
    '/api/v1/attendance/record',
    'POST',
    [
        "worker_id" => 1,
        "attendance_date" => date("Y-m-d"),
        "check_in_time" => date("H:i:s"),
        "status" => "absent",
    ]
);
$response = $kernel->handle($request);
echo $response->getContent();
