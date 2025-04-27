<?php
session_start();
header('Content-Type: application/json');

// Check if logged in
if (!isset($_SESSION['username'])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized."]);
  exit();
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['id'])) {
  echo json_encode(["status" => "error", "message" => "Missing outfit ID."]);
  exit();
}

$outfitId = intval($data['id']);

// DB connection
$conn = new mysqli("localhost", "root", "", "firefit_db");
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "DB error: " . $conn->connect_error]);
  exit();
}

// Delete outfit from DB
$stmt = $conn->prepare("DELETE FROM outfits WHERE id = ?");
$stmt->bind_param("i", $outfitId);

if ($stmt->execute()) {
  echo json_encode(["status" => "success", "message" => "Outfit deleted."]);
} else {
  echo json_encode(["status" => "error", "message" => "Failed to delete outfit."]);
}

$conn->close();
?>