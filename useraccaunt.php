<?
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';
$conn = mysqli_connect($servername, $username, $password, $dbname);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $login= mysqli_real_escape_string($conn, $data['login']);
    $password= mysqli_real_escape_string($conn, $data['password']);

    $usersQuery = "SELECT * FROM accaunt WHERE login='$login'";
    $userResult = mysqli_query($conn, $usersQuery);

    if (mysqli_num_rows($userResult) > 0) {
        $user = mysqli_fetch_assoc($userResult);
        // Проверка пароля
        if (password_verify($password, $user['password'])) {
            // Здесь можно добавить логику для начала сессии
            
            $_SESSION['username'] = $user['login'];
            $_SESSION['id'] = $user['id'];

            header('Content-Type: application/json');
        } else {
            echo 'Неверный пароль!';
        }
    } else {
        echo 'Пользователь не найден!';
    }

    exit; // Завершаем скрипт
}


mysqli_close($conn);
?>