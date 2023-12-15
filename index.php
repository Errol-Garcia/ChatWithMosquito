<?php
session_start();
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Chat MQTT</title>

    <link rel="stylesheet" href="/chat/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/chat/plugins/sweetAlert2/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="/chat/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="/chat/css/style.css">

    <script type="text/javascript" src="/chat/js/jquery.min.js"></script>
    <script type="text/javascript" src="/chat/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/chat/js/popper.min.js"></script>
    <script type="text/javascript" src="/chat/plugins/sweetAlert2/dist/sweetalert2.min.js"></script>
</head>

<body>
    <?php
    if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
        include_once "$root/chat/pages/chat/index.php";
    } else {
        include_once "$root/chat/pages/login/index.php";
    }
    ?>
</body>

</html>