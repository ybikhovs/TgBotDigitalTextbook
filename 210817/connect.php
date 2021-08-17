<?php
$servername = "localhost:3306";
$database = "client_5ef44fe_9_sa";
$username = "afoninsb";
$password = "27P!ocj2";
// Устанавливаем соединение
$conn = mysqli_connect($servername, $username, $password, $database);
// Проверяем соединение
if (!$conn) {
      die("Connection failed: " . mysqli_connect_error());
}