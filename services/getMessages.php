<?php
    session_start();
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/chat/services/connection/connection.php";

    $id = $_POST["id"];
    $query = '';
    $salida = array();
    $query = '
        SELECT * FROM messages where chat_id='.$id.'
    ';

    $connection = new Connection();
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $datas = array();

    foreach ($result as $fila) {
        $sub_array      = array();
        $sub_array[]    = $fila["message"];
        $sub_array[]    = $fila["send_date"];
        $sub_array[]    = $fila["pub_id"];
        $sub_array[]    = $fila["sub_id"];
        $datas[]        = $sub_array;
    }

    $salida = array(
        'data' => $datas
    );

    echo json_encode($salida, JSON_UNESCAPED_UNICODE);