<?php

header("Content-Type: application/json");

$data = json_encode([
    "user_id" => 1,
    "user" => "John Doe",
]);

echo $data;
