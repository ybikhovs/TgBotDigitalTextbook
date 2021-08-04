<?php
$servername = "localhost";
$database = "client_database";
$username = "username";
$password = "password";
// Устанавливаем соединение
$conn = mysqli_connect($servername, $username, $password, $database);
// Проверяем соединение
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}