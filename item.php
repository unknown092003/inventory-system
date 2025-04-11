<?php

header("Content-Type: application/json");

echo json_encode($_POST);

ob_flush();
flush();


// Error In Fetching Data