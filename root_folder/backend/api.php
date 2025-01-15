<?php

// Konfiguratsioon andmebaasi ühendamiseks
$host = "localhost";
$username = "root";
$password = "qwerty";
$database = "auto24";

// Ühenda andmebaasiga
try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Loeme HTTP päringu parameetrid
$params = [
    'brand' => $_GET['brand'] ?? null,
    'model' => $_GET['model'] ?? null,
    'engine' => $_GET['engine'] ?? null,
    'mileage' => $_GET['mileage'] ?? null,
    'fuel' => $_GET['fuel'] ?? null,
    'year' => $_GET['year'] ?? null,
    'price' => $_GET['price'] ?? null,
];

// Loome SQL päringu koos dünaamiliste tingimustega
$sql = "SELECT * FROM autod WHERE 1=1";
$queryParams = [];

foreach ($params as $column => $value) {
    if (!empty($value)) {
        $sql .= " AND $column = :$column";
        $queryParams[$column] = $value;
    }
}

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($queryParams);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "count" => count($cars),
        "cars" => $cars,
        "query" => $sql, // Abiks silumiseks (soovi korral eemalda produktsioonis)
        "params" => $queryParams // Abiks silumiseks
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
