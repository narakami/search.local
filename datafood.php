<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'search';
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . mysqli_connect_error()]));
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM food";
    $result = mysqli_query($conn, $sql);

    $data = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit; // Завершаем скрипт
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $namefood = mysqli_real_escape_string($conn, $data['namefood']);
    $tag = mysqli_real_escape_string($conn, $data['tag']);
    $count = mysqli_real_escape_string($conn, $data['count']);

    $sql = "INSERT INTO food (namefood, tag, count) VALUES ('$namefood', '$tag', '$count')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'New product added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
    }
    exit; // Завершаем скрипт
}
if($_SERVER['REQUEST_METHOD']==='DELETE'){
    $data = json_decode(file_get_contents('php://input'),true);
    $id = $data['id'];
    $sql = "DELETE FROM food WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'New product added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
    }
    exit; // Завершаем скрипт
}
if($_SERVER['REQUEST_METHOD']==='PATCH'){
    $data = json_decode(file_get_contents('php://input'),true);
    $id = $data['id'];
    $namefood = $data['namefood'];
    $tag = $data['tag'];
    $count = $data['count'];
    $price = $data['price'];
    $sql = "UPDATE food SET namefood='$namefood',tag='$tag',count='$count',price='$price' WHERE id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['status' => 'success', 'message' => 'New changes added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($conn)]);
    }
    exit; // Завершаем скрипт
}
mysqli_close($conn);
?>

