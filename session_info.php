<?
session_start();

if (isset($_SESSION['username'])) {
    echo json_encode([
        'isLoggedIn' => true,
        'username' => $_SESSION['username'],
        'userId' => $_SESSION['id']
    ]);
} else {
    echo json_encode(['isLoggedIn' => false]);
}

?>