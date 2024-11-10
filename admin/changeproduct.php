<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
ob_start(); // Начало буферизации

// Параметры подключения к базе данных
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';

// Подключение к базе данных
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Проверка наличия ID и изображения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $namefood = $_POST['name'] ?? null;
    $tag = $_POST['tag'] ?? null;
    $count = $_POST['count'] ?? null;
    $price = $_POST['price'] ?? null;

    // Обновление с изображением
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $targetDir = "picture/";
        $imageFileName = basename($image['name']);
        $targetFile = $targetDir . $imageFileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Проверка разрешенных форматов
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                $stmt = $conn->prepare("UPDATE food SET image = ?, namefood = ?, tag = ?, count = ?, price = ? WHERE id = ?");
                $stmt->bind_param("sssdii", $imageFileName, $namefood, $tag, $count, $price, $id);
                
                if ($stmt->execute()) {
                    ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Фото успешно обновлено!']);
                } else {
                    ob_end_clean();
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
        // Обновление без изображения
        $query = "UPDATE food SET namefood = ?, tag = ?, count = ?, price = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssdis', $namefood, $tag, $count, $price, $id);
        
        if ($stmt && mysqli_stmt_execute($stmt)) {
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Продукт обновлён успешно.']);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Ошибка при обновлении данных продукта.']);
        }
        
        mysqli_stmt_close($stmt);
    }
} else {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
}

// Закрытие соединения
mysqli_close($conn);
?>
