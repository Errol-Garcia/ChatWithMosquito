<?php
    session_start();
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require "$root/chat/services/connection/connection.php";

    $query = '';
    $salida = array();
    $query = '
        SELECT * FROM view_contacts
        WHERE (user_id = '.$_SESSION['id'].' OR contact_id = '.$_SESSION['id'].') AND id <> '.$_SESSION['id'].'
        ORDER BY name;
    ';

    $connection = new Connection();
    $stmt = $connection->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll();
    $datas = array();

    foreach ($result as $fila) {
        $sub_array      = array();
        $sub_array[]    = $fila["chat"];
        $sub_array[]    = $fila["name"];
        $sub_array[]    = $fila["image"];
        $datas[]        = $sub_array;
    }

    $salida = array(
        'data' => $datas
    );

    echo json_encode($salida, JSON_UNESCAPED_UNICODE);