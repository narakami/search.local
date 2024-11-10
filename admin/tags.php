<?
$servername='localhost';
$username='root';
$password='';
$dbname='search';
$conn = mysqli_connect($servername,$username, $password,$dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Заданные теги, по которым нужно отфильтровать товары
$requiredTags = ['milk', 'meat', 'fruits', 'vegetables', 'drinks'];

// Массив для хранения товаров, которые будут выведены
$productsToDisplay = [];

// Проходим по каждому тегу и выбираем один товар с этим тегом
foreach ($requiredTags as $tag) {
    // Запрос для получения одного товара с текущим тегом
    $sql = "SELECT * FROM food WHERE tag = '$tag' LIMIT 1";  // Ограничиваем выборку одним товаром
    $result = $conn->query($sql);

    // Если товар найден, добавляем его в массив
    if ($result->num_rows > 0) {
        // Получаем первый товар из результата
        $product = $result->fetch_assoc();
        $productsToDisplay[] = $product;
    }
}

// Выводим результаты (например, в формате JSON)
echo json_encode($productsToDisplay);

// Закрываем соединение
$conn->close();
?>