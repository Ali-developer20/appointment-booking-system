<?php
require_once '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming JSON payload from the frontend
    $data = json_decode(file_get_contents("php://input"), true);

    $name = trim($data['name'] ?? '');
    $service = trim($data['service'] ?? '');
    $date = trim($data['booking_date'] ?? '');
    $time = trim($data['booking_time'] ?? '');

    // Basic Validation
    if(empty($name) || empty($service) || empty($date) || empty($time)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Enterprise Standard: Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO bookings (name, service, booking_date, booking_time) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $service, $date, $time);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save booking."]);
    }
    
    $stmt->close();
}
$conn->close();
?>