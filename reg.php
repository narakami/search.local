<?php
session_start();
header('Content-Type: application/json');

$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['status' => 'error','message' => 'Ошибка подключения к базе данных']);
    exit();
}

// Получаем данные из JSON-запроса
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['login']) && isset($data['password'])) {
    $login = $data['login'];
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Проверка, существует ли пользователь
    $stmt = $conn->prepare("SELECT * FROM accaunt WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error','message' => 'Пользователь с таким именем уже существует!']);
    } else {
        // Добавление пользователя
        $stmt = $conn->prepare("INSERT INTO accaunt (login, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $login, $password);
        if ($stmt->execute()) {
            $userId = $stmt->insert_id;
            $_SESSION['username'] = $login;
            $_SESSION['id'] = $userId;
            echo json_encode(['status' => 'success','message' => 'Пользователь успешно зарегистрирован!']);
        } else {
            echo json_encode(['status' => 'error','message' => 'Ошибка при регистрации пользователя']);
        }
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error','message' => 'Некорректные данные']);
}

$conn->close();
?>
