<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configure database connection
$host = "localhost";
$username = "root";
$password = "qwerty";
$database = "auto24";

// Set CORS headers to allow cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// Read HTTP request parameters
$params = [
    'brand' => $_GET['brand'] ?? null,
    'model' => $_GET['model'] ?? null,
    'engine' => $_GET['engine'] ?? null,
    'mileage' => $_GET['mileage'] ?? null,
    'fuel' => $_GET['fuel'] ?? null,
    'year' => $_GET['year'] ?? null,
    'price' => $_GET['price'] ?? null,
];

// Initialize SQL query with a base condition
$sql = "SELECT * FROM autod WHERE 1=1";
$queryParams = [];

foreach ($params as $column => $value) {
    if (!empty($value)) {
        $sql .= " AND $column LIKE :$column";  // Use LIKE to allow partial matching
        $queryParams[$column] = '%' . $value . '%'; // Add wildcards for partial matching
    }
}

try {
    // Prepare and execute the SQL query
    $stmt = $conn->prepare($sql);
    $stmt->execute($queryParams);
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode([
        "count" => count($cars),
        "cars" => $cars,
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>
