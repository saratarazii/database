<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['username'])) {
  echo json_encode(["status" => "error", "message" => "User not logged in"]);
  exit();
}

// DB config
$conn = new mysqli("localhost", "root", "", "firefit_db");
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
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

// Get fire_fit_id from request
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['fire_fit_id'])) {
  echo json_encode(["status" => "error", "message" => "Missing fire_fit_id"]);
  exit();
}
$fire_fit_id = intval($data['fire_fit_id']);

// Verify this fire_fit belongs to the current user
$verifyStmt = $conn->prepare("SELECT id FROM fire_fits WHERE id = ? AND user_id = ?");
$verifyStmt->bind_param("ii", $fire_fit_id, $user_id);
$verifyStmt->execute();
$verifyResult = $verifyStmt->get_result();

if ($verifyResult->num_rows !== 1) {
  echo json_encode(["status" => "error", "message" => "Unauthorized: This outfit doesn't belong to you or doesn't exist"]);
  exit();
}

// Delete the fire_fit
// Note: We don't need to explicitly delete fire_fit_items because of the ON DELETE CASCADE
$deleteStmt = $conn->prepare("DELETE FROM fire_fits WHERE id = ?");
$deleteStmt->bind_param("i", $fire_fit_id);

if ($deleteStmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Outfit deleted successfully"]);
} else {
  echo json_encode(["status" => "error", "message" => "Failed to delete outfit: " . $conn->error]);
}

$conn->close();
?>