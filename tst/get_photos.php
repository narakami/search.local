<?php
// Настройки подключения к базе данных
$host = 'localhost';     // Сервер базы данных
$db = 'search';    // Имя базы данных
$user = 'root';  // Имя пользователя
$password = '';  // Пароль

$conn = mysqli_connect($host, $user, $password, $db);

if ($conn) {
    $sql = "SELECT * FROM photos";
    $result = mysqli_query($conn, $sql);

    $data = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Ошибка подключения к базе данных.']);
}
mysqli_close($conn);


?>