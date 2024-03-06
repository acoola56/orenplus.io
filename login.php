<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #2980b9; /* Изменяем цвет фона на синий */
}

.container {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    padding: 40px;
    max-width: 400px;
    width: 100%;
    text-align: center;
}
.logo {
    position: absolute;
    top: 20px; /* Выравнивание от верхнего края */
    
    width: 100px;
    height: 100px; /* Высота равна ширине, чтобы сделать круглый */
    border-radius: 50%; /* Делаем круглый логотип */
    z-index: 9999; /* Устанавливаем z-index, чтобы логотип был поверх других элементов */
}
.container h2 {
    margin-bottom: 20px;
    font-family: 'Roboto', sans-serif; /* Добавляем выбранный шрифт */
    font-weight: 700; /* Устанавливаем полужирный начертание */
    font-size: 24px; /* Увеличиваем размер шрифта */
}

input[type="text"],
input[type="password"],
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #2980b9; /* Изменяем цвет рамки на синий */
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
}

input[type="submit"] {
    background-color: #2980b9; /* Изменяем цвет кнопки на синий */
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #2c3e50; /* Изменяем цвет кнопки при наведении на темно-синий */
}
.error-message {
    color: red;
    margin-bottom: 10px;
}

</style>
</head>
<body>
<img src="2.jpg" alt="Логотип" class="logo">
<h1>OrenPlus</h1><br><br>
<div class="container">
    <h2>Авторизация</h2>
    <?php
    session_start();
    if (isset($_SESSION['login_error'])) {
        echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
        unset($_SESSION['login_error']);
    }
    ?>
    <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <input type="text" id="username" name="username" placeholder="Username" required><br>
        <input type="password" id="password" name="password" placeholder="Password" required><br>
        <input type="submit" value="Login">
    </form>
</div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "CRM";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Пользователи WHERE логин='$username' AND пароль='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['логин'];
        $_SESSION['user_role'] = $row['тип_пользователя'];
        
        if ($_SESSION['user_role'] == 'администратор') {
            header("Location: 1.php");
        } elseif ($_SESSION['user_role'] == 'начальство') {
            header("Location: 2.php");
        } elseif ($_SESSION['user_role'] == 'начальство отдела') {
            header("Location: 3.php");
        } elseif ($_SESSION['user_role'] == 'сотрудник') {
            header("Location: 4.php");
        } else {
            echo "Некорректная роль пользователя.";
        }
    } else {
        $_SESSION['login_error'] = "Неправильные логин или пароль.";
        header("Location: login.php");
    }

    $conn->close();
}
?>
</body>
</html>
