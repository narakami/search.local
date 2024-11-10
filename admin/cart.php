<?php
session_start();

// Получаем данные из запроса
$data = json_decode(file_get_contents("php://input"), true);

// Проверяем, переданы ли данные
if(isset($data) && isset($data['id'])){
    $id = $data['id'];

    // Сохраняем id товара в сессии
    $_SESSION['cart_id'] = $id;

    // Отправляем успешный ответ в формате JSON
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Товар добавлен в корзину']);
} else {
    // Если данные не были переданы, отправляем ошибку
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Товар не найден!']);
}
?>
