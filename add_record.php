<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'администратор') {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "CRM";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tableName = $_POST['table'];
unset($_POST['table']); // Удаляем имя таблицы из массива данных формы

// Формируем запрос на добавление записи
$sql = "INSERT INTO $tableName (";
$sql .= implode(", ", array_keys($_POST)) . ") VALUES (";
$sql .= "'" . implode("', '", array_map(function($value) use ($conn) { return $conn->real_escape_string($value); }, $_POST)) . "')";

if ($conn->query($sql) === TRUE) {
    echo "Record added successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();

// Перенаправляем обратно на страницу 1.php
header("Location: 1.php");
exit();
?>
