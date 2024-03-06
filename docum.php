<!DOCTYPE html>
<html>
<head>
    <title>Загрузка документов</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        .container {
            max-width: 800px;
            padding: 20px;
            text-align: left;
            margin: 20px auto;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container h2 {
            color: #fff;
        }

        .form-container {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
            background-color: #2980b9;
        }

        .form-container input[type="file"],
        .form-container input[type="text"],
        .form-container input[type="submit"],
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #444;
            color: #fff;
        }

        .form-container input[type="submit"] {
            background-color: #337ab7;
            border: none;
            cursor: pointer;
        }

        .form-container input[type="submit"]:hover {
            background-color: #286090;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #2c3e50; /* Устанавливаем цвет фона подвала */
            padding: 10px 20px;
            text-align: center;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Загрузить файл</h2>
        <div class="form-container">
            <form action="docum.php" method="post" enctype="multipart/form-data">
                <input type="file" name="file" required><br><br>
                <label for="manual_theme">Введите тему вручную:</label>
                <input type="text" name="manual_theme" id="manual_theme"><br><br>
                <input type="submit" name="submit" value="Загрузить">
                <h2>Загруженные файлы:</h2>
        <ul id="fileList">
            <?php
            $upload_dir = "uploads/";
            $files = scandir($upload_dir);
            $files = array_diff($files, array('.', '..'));
            foreach ($files as $file) {
                echo "<li><a href='$upload_dir$file' target='_blank'>$file</a></li>";
            }
            ?>
            </form>
        </div>
    </div>
    
    <div class="footer">
        &copy;2024 OrenPlus.
    </div>
</body>
</html>
<?php
// Проверяем, был ли отправлен файл
if ($_FILES['file']) {
    $upload_dir = "uploads/"; // Папка, куда будут загружены файлы
    $target_file = $upload_dir . basename($_FILES['file']['name']); // Полный путь к файлу

    // Проверяем тип файла
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($file_type != "pdf" && $file_type != "doc" && $file_type != "docx") {
        echo "Допустимы только файлы PDF, DOC и DOCX.";
        exit;
    }

    // Перемещаем файл в папку uploads, если он прошел проверку
    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        echo "Файл успешно загружен: " . basename($_FILES['file']['name']);
    } else {
        echo "Произошла ошибка при загрузке файла.";
    }
}
?>
