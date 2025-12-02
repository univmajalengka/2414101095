<?php
include "./database/db.php";
session_start();
$login_message = "";

if (isset($_SESSION["is_login"])) {
  header("location: admin/index.php");
}

if (isset($_POST["login"])) {
  $username = $_POST["username"];
  $password = $_POST["password"];
  $enc_password = hash("sha256", $password);

  $sql = "SELECT * FROM users WHERE username='$username' AND password='$enc_password'";

  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    $_SESSION["username"] = $data["username"];
    $_SESSION["is_login"] = true;

    header("location: admin/index.php");
  } else {
    $login_message = "Username atau Password salah!";
  }
  $conn->close();
}