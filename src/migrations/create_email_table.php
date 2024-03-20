<?php
require_once '../config/db.php';

global $pdo;

$query = $pdo->prepare(
    "CREATE TABLE emails (
    id SERIAL PRIMARY KEY,
    sender VARCHAR(50) NOT NULL,
    recepient VARCHAR(50) NOT NULL,
    subject VARCHAR(255),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP );"
);

try {
    $result = $query->execute();
    if ($result) {
        $response = array(
            'status' => 1,
            'message' => 'Migration success.'
        );
    }
    // error handler
} catch (PDOException $e) {
    $errorInfo = $e->errorInfo;
    error_log("$errorInfo");
    $response = array(
        'status' => 500,
        'message' => $errorInfo[2]
    );
}

header('Content-Type: application/json');
echo json_encode($response);
