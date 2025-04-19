<?php
session_start();

// 1. Make sure user is logged in
if (!isset($_SESSION['username'])) {
  echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
  exit();
}

// 2. DB config
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "firefit_db";

// 3. Connect to DB
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
if ($conn->connect_error) {
  echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
  exit();
}

// 4. Get user ID from username
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
  echo json_encode(["status" => "error", "message" => "User not found."]);
  exit();
}

$user_id = $result->fetch_assoc()['id'];

// 5. Get outfits for this user
$stmt = $conn->prepare("SELECT id, image_path, created_at FROM outfits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$outfits = [];
while ($row = $result->fetch_assoc()) {
  $outfits[] = $row;
}

echo json_encode([
  "status" => "success",
  "outfits" => $outfits
]);

$conn->close();
?>