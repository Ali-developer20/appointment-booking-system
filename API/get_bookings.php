<?php
require_once '../db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch bookings ordered by the newest dates first
    $sql = "SELECT id, name, service, booking_date, booking_time FROM bookings ORDER BY booking_date ASC, booking_time ASC";
    $result = $conn->query($sql);
    
    $bookings = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    
    // Return standard JSON response
    echo json_encode(["status" => "success", "data" => $bookings]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
$conn->close();
?>