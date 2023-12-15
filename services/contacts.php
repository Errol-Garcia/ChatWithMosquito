<?php
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    include_once "$root/chat/services/connection/connection.php";

    $connection = new Connection();

    $s = $connection->prepare(
        '
            SELECT * FROM view_contacts
            WHERE (user_id = '.$_SESSION['id'].' OR contact_id = '.$_SESSION['id'].') AND id <> '.$_SESSION['id'].'
            ORDER BY name;
        '
    );

    $s->execute();
    $contacts = $s->fetchAll();
?>