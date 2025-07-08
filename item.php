<?php
require_once __DIR__ . '/pages/db.php';
$pdo = Database::getInstance()->getConnection();

header("Content-Type: application/json");

$stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = :prod_num;");
$stmt->execute([":prod_num" => $_POST["productNumber"]]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($res);
