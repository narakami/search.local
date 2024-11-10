<?php
header('Content-Type: application/json');

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';

// Подключение к базе данных
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {

    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}
// Проверка наличия ID и изображения
if (isset($_POST['id']) && isset($_FILES['image'])) {
    $id = $_POST['id'];
    $image = $_FILES['image'];
    $targetDir = "uploads/";
    $imageFileName = basename($image['name']);
    $targetFile = $targetDir . $imageFileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Разрешенные форматы файлов
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($image['tmp_name'], $targetFile)) {
            // Обновляем данные в базе данных
            $stmt = $conn->prepare("UPDATE photos SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $imageFileName, $id);
            
            if ($stmt->execute()) {
                ob_end_clean(); // Очищаем буфер перед выводом JSON
                echo json_encode(['success' => true, 'message' => 'Фото успешно обновлено!']);
            } else {
                ob_end_clean(); // Очищаем буфер перед выводом JSON
                echo json_encode(['success' => false, 'message' => 'Ошибка обновления фото в базе данных', 'error' => $stmt->error]);
            }
            $stmt->close();
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Ошибка загрузки изображения']);
        }
    } else {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Недопустимый формат изображения']);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
}

$conn->close();
?>
