<?
session_start();

if (isset($_SESSION['cart_id'])) {
    echo json_encode([
        'cartid' => $_SESSION['cart_id']
    ]);
} else {
    echo json_encode(['cart' => false]);
}

?>