<?php
header('Content-Type: application/json');
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';

// Подключение к базе данных
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Проверка, получены ли данные
if (isset($_POST['name'], $_POST['tag'], $_POST['price'], $_POST['count']) && isset($_FILES['image'])) {
    $name = $_POST['name'];
    $tag = $_POST['tag'];
    $price = $_POST['price'];
    $count = $_POST['count'];  // Получаем количество продукта

    // Проверяем и сохраняем файл изображения
    $image = $_FILES['image'];
    $targetDir = "picture/";
    $imageFileName = basename($image['name']);
    $targetFile = $targetDir . $imageFileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Разрешенные форматы файлов
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Вставляем данные в базу данных
            $stmt = $conn->prepare("INSERT INTO food (namefood, tag, price, count, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdis", $name, $tag, $price, $count, $imageFileName);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Продукт успешно добавлен!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Ошибка добавления продукта в базу данных', 'error' => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка загрузки изображения']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Недопустимый формат изображения']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
}

$conn->close();
?>
