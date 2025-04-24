<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "firefit_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$user = $_POST['username'];
$pass = $_POST['password'];

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();

  if (password_verify($pass, $row['password'])) {
    $_SESSION['username'] = $user;
    header("Location: ../pages/dashboard/dashboard.html");
    exit();
  } else {
    // Wrong password
    header("Location: ../pages/login/login.html?error=1");
    exit();
  }
} else {
  // User not found
  header("Location: ../pages/login/login.html?error=1");
  exit();
}

$stmt->close();
$conn->close();
?>
