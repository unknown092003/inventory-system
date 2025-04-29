<?php

header("Content-Type: application/json");

$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = :prod_num;");
$stmt->execute([":prod_num" => $_POST["productNumber"]]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($res);
