<?php
    session_start();

    if (isset($_SESSION['login'])) {
        if ($_SESSION['login'] == true) {
            session_destroy();
        }
    }

    header("Location: /chat/");
?>