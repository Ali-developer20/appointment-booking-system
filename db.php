<?php
$host = 'localhost';
$db   = 'appointment_db';
$user = 'root';
$pass = ''; // Leave empty for Laragon

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}
?>