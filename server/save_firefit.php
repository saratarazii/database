<?php
session_start();
header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['username'])) {
  echo json_encode(["status" => "error", "message" => "User not logged in"]);
  exit();
}

// DB config
$conn = new mysqli("localhost", "root", "", "firefit_db");
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "Connection failed"]);
  exit();
}

// Get user ID
$username = $_SESSION['username'];
$userQuery = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
if ($userResult->num_rows !== 1) {
  echo json_encode(["status" => "error", "message" => "User not found"]);
  exit();
}
$user_id = $userResult->fetch_assoc()['id'];

// Get input
$data = json_decode(file_get_contents("php://input"), true);
$outfit_ids = $data['outfit_ids'] ?? [];

if (count($outfit_ids) === 0) {
  echo json_encode(["status" => "error", "message" => "No items provided"]);
  exit();
}

// Create new fire fit
$fitStmt = $conn->prepare("INSERT INTO fire_fits (user_id) VALUES (?)");
$fitStmt->bind_param("i", $user_id);
$fitStmt->execute();
$fire_fit_id = $conn->insert_id;

// Add each item
$itemStmt = $conn->prepare("INSERT INTO fire_fit_items (fire_fit_id, outfit_id) VALUES (?, ?)");
foreach ($outfit_ids as $id) {
  $itemStmt->bind_param("ii", $fire_fit_id, $id);
  $itemStmt->execute();
}

echo json_encode(["status" => "success", "message" => "Fire Fit saved!", "fire_fit_id" => $fire_fit_id]);
?>