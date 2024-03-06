<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Панель администратора</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: linear-gradient(to bottom, #2980b9, #2c3e50);
            color: #fff;
            min-height: 100vh; /* Устанавливаем минимальную высоту body для подвала */
            position: relative;
        }

.footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-image: linear-gradient(to bottom, #2980b9, #2c3e50); /* Градиент от светло-синего к темно-синему */
    padding: 10px 20px;
    text-align: center;
    color: #fff;
}

.container {
    max-width: 1100px;
    padding: 20px;
    margin: 20px auto;
    border: 1px solid #ccc;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    background-color: rgba(255, 255, 255, 0.1);
}

.table-container {
    margin-top: 20px;
    overflow-x: auto;
}

.table-container:not(:first-child) {
    border-top: 4px solid #b92980; /* Изменяем цвет и толщину границы */
    margin-top: 30px; /* Увеличиваем верхний отступ для более широкой полосы */
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    border: 1px solid #fff;
    padding: 12px;
    text-align: left;
}

table th {
    background-color: #2980b9;
    color: #fff;
}

table tr:nth-child(even) {
    background-color: rgba(255, 255, 255, 0.1);
}

.form-container {
    border-radius: 10px;
    padding: 20px;
    margin-top: 20px;
    background-color: rgba(255, 255, 255, 0.1);
}

.form-container input[type="text"],
.form-container input[type="submit"] {
    width: calc(100% - 22px);
    padding: 12px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    background-color: #fff;
    color: #333;
    transition: background-color 0.3s, color 0.3s;
}

.form-container input[type="submit"] {
    background-color: #2980b9;
    border: none;
    cursor: pointer;
    color: #fff;
}

.form-container input[type="submit"]:hover {
    background-color: #2c3e50;
}

.logo {
    position: absolute;
    top: 20px; /* Выравнивание от верхнего края */
    right: 20px; /* Выравнивание с небольшим отступом от правого края */
    width: 100px;
    height: 100px; /* Высота равна ширине, чтобы сделать круглый */
    border-radius: 50%; /* Делаем круглый логотип */
    z-index: 9999; /* Устанавливаем z-index, чтобы логотип был поверх других элементов */
}

.btn {
    background-color: #2980b9;
    color: white;
    padding: 12px 24px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
    border-radius: 4px;
    border: none;
    transition: background-color 0.3s;
}

.btn:hover {
    background-color: #2c3e50;
}

    </style>
</head>

<body>
<img src="2.jpg" alt="Логотип" class="logo">
<a href="login.php" class="btn">Выйти </a>
    <div class="container">
    
        <h2>Панель администратора</h2>
        
        <?php
        session_start();

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'администратор') {
            header("Location: login.php");
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

        if (isset($_POST['delete'])) {
            $table = $_POST['table'];
            $id = $_POST['id'];

            // Получаем информацию о столбцах таблицы
            $stmt = $conn->prepare("DESCRIBE $table");
            $stmt->execute();
            $columns = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            // Ищем первичный ключ (PRIMARY KEY) в столбцах таблицы
            $primaryKey = null;
            foreach ($columns as $column) {
                if ($column['Key'] == 'PRI') {
                    $primaryKey = $column['Field'];
                    break;
                }
            }

            if ($primaryKey) {
                $sql = "DELETE FROM $table WHERE $primaryKey = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $id); // Предполагаем, что первичный ключ имеет строковый тип

                if ($stmt->execute()) {
                    echo "Запись успешно удалена";
                } else {
                    echo "Ошибка при удалении записи: " . $conn->error;
                }
            } else {
                echo "Первичный ключ (PRIMARY KEY) не найден в таблице $table";
            }
        }

        if (isset($_POST['add'])) {
            $table = $_POST['table'];
            $columns = [];
            $values = [];

            // Собираем названия столбцов и их значения
            foreach ($_POST as $key => $value) {
                if ($key !== 'table' && $key !== 'add') {
                    $columns[] = $key;
                    $values[] = "'" . $conn->real_escape_string($value) . "'";
                }
            }

            $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

            if ($conn->query($sql) === TRUE) {
                echo "Новая запись успешно добавлена";
            } else {
                echo "Ошибка при добавлении записи: " . $conn->error;
            }
        }

        $sql = "SHOW TABLES";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<div class='btn-container'>"; // Добавляем div для кнопок
            while ($row = $result->fetch_assoc()) {
                $tableName = $row['Tables_in_crm'];
                echo "<button class='btn' onclick='showTable(\"$tableName\")'>$tableName</button>";
            }
            echo "</div>"; // Закрываем div для кнопок
            mysqli_data_seek($result, 0); // Сбрасываем указатель результата запроса, чтобы начать сначала

            while ($row = $result->fetch_assoc()) {
                $tableName = $row['Tables_in_crm'];
                echo "<div class='table-container' id='$tableName' style='display: none;'>";
                $sqlTable = "SELECT * FROM $tableName";
                $tableResult = $conn->query($sqlTable);
                if ($tableResult->num_rows > 0) {
                    echo "<table>";
                    echo "<tr>";
                    $headers = $tableResult->fetch_fields();
                    foreach ($headers as $header) {
                        echo "<th>$header->name</th>";
                    }
                    echo "<th>Действие</th>"; // Добавим столбец для удаления записей
                    echo "</tr>";
                    while ($row = $tableResult->fetch_assoc()) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>$value</td>";
                        }
                        echo "<td>";
                        echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                        echo "<input type='hidden' name='table' value='$tableName'>";
                        echo "<input type='hidden' name='id' value='" . $row[$headers[0]->name] . "'>"; // Передаем первичный ключ записи для удаления
                        echo "<input type='submit' name='delete' value='Удалить'>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                    echo "<div class='form-container'>";
                    echo "<h3>Добавить новую запись</h3>";
                    echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
                    foreach ($headers as $header) {
                        echo "<input type='text' name='$header->name' placeholder='$header->name'><br>";
                    }
                    echo "<input type='hidden' name='table' value='$tableName'>";
                    echo "<input type='submit' name='add' value='Добавить запись'>";
                    echo "</form>";
                    echo "</div>";
                } else {
                    echo "Данные в таблице $tableName отсутствуют";
                }
                echo "</div>";
            }
        } else {
            echo "Таблицы не найдены.";
        }

        $conn->close();
        ?>
    </div>
    <script>
        function showTable(tableName) {
            var tables = document.querySelectorAll(".table-container");
            tables.forEach(function(table) {
                table.style.display = 'none';
            });
            var selectedTable = document.getElementById(tableName);
            if (selectedTable) {
                selectedTable.style.display = 'block';
            }
        }
    </script>
    <div class="footer">
        &copy; 2024 OrenPlus.
    </div>
</body>

</html>
