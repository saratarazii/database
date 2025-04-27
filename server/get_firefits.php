<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);
// header('Content-Type: application/json');

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

// 4. Get user ID
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

// 5. Get fire fits
$stmt = $conn->prepare("SELECT id, created_at FROM fire_fits WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$firefits_result = $stmt->get_result();

$firefits = [];

while ($fit = $firefits_result->fetch_assoc()) {
  $fit_id = $fit['id'];

  // 6. Get items for each fire fit
  $item_stmt = $conn->prepare("
    SELECT outfits.id, outfits.image_path
    FROM fire_fit_items
    JOIN outfits ON fire_fit_items.outfit_id = outfits.id
    WHERE fire_fit_items.fire_fit_id = ?
  ");
  $item_stmt->bind_param("i", $fit_id);
  $item_stmt->execute();
  $items_result = $item_stmt->get_result();

  $items = [];
  while ($item = $items_result->fetch_assoc()) {
    $items[] = $item;
  }

  $firefits[] = [
    "id" => $fit_id,
    "created_at" => $fit['created_at'],
    "items" => $items
  ];
}

echo json_encode([
  "status" => "success",
  "firefits" => $firefits
]);

$conn->close();
?>
