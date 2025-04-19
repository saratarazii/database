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

// 5. Handle uploaded file
if (isset($_FILES['outfit']) && $_FILES['outfit']['error'] === UPLOAD_ERR_OK) {
  $uploadDir = '../uploads/';
  
  // Make sure the uploads folder exists
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $originalName = basename($_FILES['outfit']['name']);
  // Create unique filename to prevent overwriting
  $uniqueName = time() . "_" . $originalName;
  $targetFile = $uploadDir . $uniqueName;

  if (move_uploaded_file($_FILES['outfit']['tmp_name'], $targetFile)) {
    // Save relative path for database
    $imagePath = 'uploads/' . $uniqueName;

    $insert = $conn->prepare("INSERT INTO outfits (user_id, image_path) VALUES (?, ?)");
    $insert->bind_param("is", $user_id, $imagePath);

    if ($insert->execute()) {
      // Return success with the image path
      echo json_encode([
        "status" => "success", 
        "message" => "Outfit added to closet!", 
        "image_path" => $imagePath,
        "id" => $conn->insert_id
      ]);
    } else {
      echo json_encode(["status" => "error", "message" => "Database insert failed: " . $conn->error]);
    }
  } else {
    echo json_encode(["status" => "error", "message" => "Failed to move uploaded file."]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "No file uploaded or upload error."]);
}

$conn->close();
?>