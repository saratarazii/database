<?php
// 🔍 DEBUG MODE ON
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// MySQL config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "firefit_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$first = $_POST['first_name'];
$last = $_POST['last_name'];
$user = $_POST['username'];
$pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

// 🚨 Check if username already exists
$check = $conn->prepare("SELECT * FROM users WHERE username = ?");
$check->bind_param("s", $user);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
  // Redirect back with error flag
  header("Location: /fire-fit-app/pages/signup/signup.html?error=exists");
  exit();
}

$sql = $conn->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
$sql->bind_param("ssss", $first, $last, $user, $pass);

if ($sql->execute()) {
  header("Location: /fire-fit-app/pages/login/login.html");
  exit();
} else {
  echo "Error: " . $conn->error;
}

$conn->close();
?>
