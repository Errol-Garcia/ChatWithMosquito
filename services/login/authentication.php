<?php
session_start();

$root = realpath($_SERVER["DOCUMENT_ROOT"]);

include_once "$root/chat/services/connection/connection.php";

$username = $_POST["username"];
$password = $_POST["password"];

$connection = new Connection();

$s = $connection->prepare(
    "SELECT *
        FROM users
        WHERE
            UPPER(email_address) = UPPER(:username) AND
            password = :password
    ;"
);

$s->bindValue(':username', $username, PDO::PARAM_STR);
$s->bindValue(':password', $password, PDO::PARAM_STR);

try {
    $s->execute();
    $user = $s->fetchAll();

    if (count($user) == 1) {
        $_SESSION['id'] = $user[0]['id'];
        // Duración de la cookie en segundos (por ejemplo, 7 días)
        $cookieExpiration = time() + (7 * 24 * 60 * 60);
        setcookie('user_id', $user[0]['id'], $cookieExpiration, '/');
        if (isset($_SESSION['id'])) {
            if ($_SESSION['id'] == ' ') {
                $_SESSION['login'] = false;
            } else {
                $_SESSION['login'] = true;
                $_SESSION['name'] = $user[0]['first_names'].' '.$user[0]['last_names'];
                $_SESSION['image'] = $user[0]['image'];

                echo json_encode("OK");
            }
        }
    } else {
        $_SESSION['login'] = false;

        echo json_encode("!Usuario o contraseña incorrecta¡");
    }
} catch (Exception $e) {
    error_log("Error: " . $e);
}