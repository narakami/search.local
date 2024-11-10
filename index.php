<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>админка с вводом</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body class="container">
<form method="POST" class="flex">
    <label for="name">Введите название еды:</label>
    <div class="" id="tst">
    <input type="text" id="namefood" name="namefood" required placeholder="namefood" autocomplete="off">
    <div class="none secretsearch" id="secretsearch">
    <div id="results"></div>
    </div>
    </div>
    <input type="text" id="tag" name="tag" required placeholder="tag">
    <input type="int" id="count" name="count" required placeholder="count" >
    <input type="submit" name="submit" id="submit" value="сохранить">
</form>
<form action="" method="POST">
<input type="text" name="namefood" id="namefood">
<input type="submit" name="del" id="del" value="удалить">
</form>
<?php
$servername='localhost';
$username='root';
$password='';
$dbname='search';
$conn = mysqli_connect($servername,$username, $password,$dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
function createTableIfNotExists($conn,$dbname ,$tableName, $createQuery) {
    // Проверяем, существует ли таблица
    $sql = "SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$dbname' AND TABLE_NAME = '$tableName'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
    } else {
        if (mysqli_query($conn, $createQuery)) {
            echo "Таблица ($tableName) успешно создана.<br>";
        } else {
            echo "Ошибка создания таблицы ($tableName): " . mysqli_error($conn) . "<br>";
        }
    }
}

// SQL-запросы для создания таблиц
$foodTableQuery = "CREATE TABLE food (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    namefood VARCHAR(255) NOT NULL,
    tag VARCHAR(255),
    count INT(11) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) 
)";

$accountTableQuery = "CREATE TABLE accaunt (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL
)";

// Создаем таблицы, если они не существуют
createTableIfNotExists($conn, $dbname, 'food', $foodTableQuery);
createTableIfNotExists($conn, $dbname, 'accaunt', $accountTableQuery);

$autocomplete = "SELECT * FROM food";
$result = mysqli_query($conn,$autocomplete);

$data = array();
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $data[] = $row;
    }
}
$jsonData = json_encode($data);

echo "<script>var dataFromPHP = $jsonData;</script>";
session_start();
?>
<?
if (!empty($_POST['submit'])) {
    // Получение данных из формы
    $namefood = $_POST['namefood'];
    $tag = $_POST['tag'];
    $count = $_POST['count'];
    // SQL-запрос для вставки данных
    $sql = "INSERT INTO food SET namefood='$namefood',tag='$tag',count='$count'";

    $result = mysqli_query($conn,$sql);
    
}
if (isset($_POST['del'])) {
    $namefood= $_POST['namefood'];

    $sql = "DELETE FROM food where namefood='$namefood'";
    $result = mysqli_query($conn,$sql);
    }
?>
<br>

<div class=""><?
$all = "SELECT * FROM food";
$result = mysqli_query($conn, $all);
while($row = mysqli_fetch_assoc($result)) {
    echo 
    '<form action="" method="POST">'.'id='.' '.$row['id'].' '.'namefood='.' '.
    $row['namefood'].' '.'tag='.' '.$row['tag'].' '.'count='.' '.$row['count'].
    '<input type="submit" name="idel" id="idel" value="Удалить">'.
    '<input type="hidden" name="id" value="' . $row['id'] . '"></form>'.'<br>';
}
if (isset($_POST['idel'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM food where id='$id'";
    $result = mysqli_query($conn,$sql);
    }
?>
</div>
<div class="rblock">
    <div class="rbtn" id="rbtn">
        hide
    </div>
    <div class="live" id="live">
        <br>
        <p>Название еды</p>
        <div class="porduct">
        <?
        $all = "SELECT * FROM food";
        $result = mysqli_query($conn, $all);
        $products = []; // массив для хранения уникальных продуктов
        while($row = mysqli_fetch_assoc($result)) {
            // Обрезаем пробелы и проверяем, если продукт уже добавлен
            $namefood = trim($row['namefood']);
            if (!in_array($namefood, $products)) {
                $products[] = $namefood;
                echo '<div id="products"><li>' .$namefood. '</li></div>';
            }
        }
        ?>
        <p>теги</p>
        <?
        $all = "SELECT * FROM food";
        $result = mysqli_query($conn, $all);
        $products = []; // массив для хранения уникальных продуктов
        while($row = mysqli_fetch_assoc($result)) {
            // Обрезаем пробелы и проверяем, если продукт уже добавлен
            $tag = trim($row['tag']);
            if (!in_array($tag, $products)) {
                $products[] = $tag;
                echo '<div id="products"><li>' .$tag. '</li></div>';
            }
        }
        ?>
        </div>
    </div>
</div>
<div class="bloks" id="block1">регистрация</div>
<div class="none" id="item1">
<form action="" method="POST">
<input name="login" id="login">
<br>
<input name="password" id="password">
<input type="submit" name="reg" id="reg">
</form>
<img src="cancel-vector-icon.jpg" alt="" class="crest" id="crest">
</div>
<div class="bloks" id="block2">Авторизация</div>
<div class="none" id="item2">
<form action="" method="POST">
<input name="login" id="login">
<br>
<input name="password" id="password">
<input type="submit" name="auth" id="auth">
</form>
<img src="cancel-vector-icon.jpg" alt="" class="crest" id="crest2">
</div>
<div class="logout">
<form action="" method="POST">
    <input type="submit" name="logout" id="logout" value="Выход">
</form>
</div>
<button id="fetchButton">Получить все namefood и tag</button>
<ul id="products"></ul> <!-- Элемент, куда будут добавляться результаты -->
<div id="datafod"></div>
<?
if (!empty($_POST['reg'])) {
    $login= $_POST['login'];
    $password=password_hash($_POST['password'],PASSWORD_DEFAULT);
    $checkUser = "SELECT * FROM accaunt WHERE login='$login'";
    $checkResult = mysqli_query($conn, $checkUser);
    if (mysqli_num_rows($checkResult) > 0) {
        echo 'Пользователь с таким именем уже существует!';
    } else {
        $sqk = "INSERT INTO accaunt (login, password) VALUES ('$login', '$password')";
        $result = mysqli_query($conn, $sqk);
        echo 'Пользователь успешно зарегистрирован!';
    }
}
if (!empty($_POST['auth'])) {
    $login= $_POST['login'];
    $password=$_POST['password'];
    
    $usersQuery = "SELECT * FROM accaunt WHERE login='$login'";
    $userResult = mysqli_query($conn, $usersQuery);
    
    if (mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);
        // Проверка пароля
        if (password_verify($password, $user['password'])) {
            // Здесь можно добавить логику для начала сессии
            $_SESSION['username'] = $user['login'];
        } else {
            echo 'Неверный пароль!';
        }
    } else {
        echo 'Пользователь не найден!';
    }
}
if (isset($_SESSION['username'])) {
    echo 'Добро пожаловать, ' . $_SESSION['username'] . '!';
}
if (isset($_POST['logout'])) {
    session_unset(); // Удаляет все переменные сессии
    session_destroy(); // Завершает сессию
}
?>
<br>
<a href="jsbdtest.html">BRUH слишком большой код иди js бд сюда</a>
<br>
<a href="admin/globus.html">globus</a>
<br>
<a href="admin/admin.html">admin</a>
<br>
<a href="tst/tst.html">tst</a>
<script>
    // Получаем ссылки на элементы
    const rbutton = document.getElementById('rbtn');
    const rlive = document.getElementById('live')
    const active = document.getElementsByClassName('act')
    var block1 = document.getElementById('block1')
    var itema1 = document.getElementById('item1')
    var crest1 = document.getElementById('crest')
    var block2 = document.getElementById('block2')
    var itema2 = document.getElementById('item2')
    var crest2 = document.getElementById('crest2')
    // Функция для добавления класса
    function rclass(){
        rlive.classList.toggle('none');
        rbutton.classList.toggle('mleft')
    }
    function itemf1(){
        item1.classList.toggle('none')
    }
    function itemf2(){
        item2.classList.toggle('none')
    }
    function addtxt(){
        input.append('porducts.textContent')
    }
    function crestam1(){
        item1.classList.add('none')
    }
    function crestam2(){
        item2.classList.add('none')
    }
    // Добавляем обработчики событий
    rbutton.addEventListener('click',rclass)
    block1.addEventListener('click',itemf1)
    crest1.addEventListener('click',crestam1)
    block2.addEventListener('click',itemf2)
    crest2.addEventListener('click',crestam2)
    //поясняю тут функция прогружается после загрузки всего сайта и находит все элементы 
    //и перебирает их с помощью фориач и в нем добавляет ивент клик где в нем ищет текст и вставляет его
    document.addEventListener('DOMContentLoaded', () => {
    const listItems = document.querySelectorAll('#products li');
    const inputField = document.getElementById('namefood');
    const tagitems =document.querySelectorAll('#tags li');
    const taginput =document.getElementById('tag');
    const resultitems =document.querySelectorAll('#rsr li');

    listItems.forEach(item => {
        item.addEventListener('click', () => {
            const value = item.textContent;
            inputField.value = value;
        });
    });

    // Обработчик для второго списка
    tagitems.forEach(item => {
        item.addEventListener('click', () => {
            const value = item.textContent;
            taginput.value = value;
        });
    });
    
});

console.log(dataFromPHP)
</script>
<?
mysqli_close($conn);
?>
<script>
    // Находим элемент поля ввода по его ID
    var inputFielda = document.getElementById('namefood');

    // Добавляем обработчик события на изменение текста в поле ввода
    inputFielda.addEventListener('input', function() {
        // Получаем текст из поля ввода и преобразуем его в нижний регистр
        var query = this.value.toLowerCase();

        // Фильтруем массив данных на основе введенного текста
        var results = dataFromPHP.filter(function(item) {
            // Проверяем, содержит ли имя элемента введенный текст
            return item.namefood.toLowerCase().includes(query);
        });

        // Отображаем результаты
        displayResults(results);
    });

    // Функция для отображения результатов поиска
    function displayResults(results) {
        // Находим элемент для отображения результатов
        var resultsDiv = document.getElementById('results');
        resultsDiv.innerHTML = ''; // Очищаем предыдущие результаты

        // Если нет результатов, показываем сообщение
        if (results.length === 0) {
            resultsDiv.innerHTML = '<p>Ничего не найдено.</p>';
            return; // Выходим из функции
        }

        // Проходимся по каждому найденному элементу и добавляем его в результаты
        results.forEach(function(item) {
            var div =document.createElement('div');
            var li = document.createElement('li'); // Создаем новый элемент div
            li.textContent = item.namefood; // Устанавливаем текст внутри div
            resultsDiv.appendChild(div); // Добавляем div в контейнер результатов
            div.id = 'rsr'
            div.appendChild(li);

            li.addEventListener('click', () => {
            inputField.value = item.namefood; 
        });
        });
    }
    var inputField = document.getElementById('namefood');
    var secretsearch = document.getElementById('secretsearch');
    
    document.addEventListener('click', function(event) {
    var e=document.getElementById('tst');
    var a = document.getElementById('secretsearch');
    a.classList.remove('none');
    if (!e.contains(event.target)) a.classList.add('none');
    });
</script>
<script>
    // Функция для получения данных из базы данных
function fetchFoodData() {
    fetch('datafood.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Здесь вы можете обрабатывать полученные данные
            displayFoodData(data); // Вызов функции для отображения данных на странице
        })
        .catch(error => {
            console.error('Ошибка:', error);
        });
}

// Функция для отображения полученных данных на странице
function displayFoodData(data) {
    const productsList = document.getElementById('datafod'); // Замените на ваш элемент
    productsList.innerHTML = ''; // Очищаем список перед добавлением новых элементов

    data.forEach(item => {
        const li = document.createElement('li');
        li.textContent = `Название: ${item.namefood}, Тег: ${item.tag}, Кол-во: ${item.count}`;
        productsList.appendChild(li);
    });
}

// Привязка функции к кнопке (например, создайте кнопку в вашем HTML)
document.getElementById('fetchButton').addEventListener('click', fetchFoodData);
</script>
</body>
</html>