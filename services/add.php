<?php
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
include_once "$root/chat/services/connection/connection.php";


$connection = new Connection();
$stmt = $connection->prepare("
            INSERT INTO messages(
                message,
                send_date,
                pub_id,
                sub_id,
                chat_id
            )VALUES(
                :message,
                now(),
                :pub_id,
                :sub_id,
                :chat_id
            );
        ");

$resultado = $stmt->execute(
    array(
        ':message'       => $_POST["message"],
        ':pub_id'   => $_POST["pub_id"],
        ':sub_id'   => $_POST["sub_id"],
        ':chat_id'   => $_POST["chat_id"]
    )
);

// if (!empty($resultado)) {
//     echo "Registro actualizado";
// }
