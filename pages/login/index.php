<?php
require_once "$root/chat/services/connection/connection.php";
?>

<form action="#" method="POST" id="formLogin" class="form shadow-lg rounded">
    <div class="col-sm-12 text-center mb-2">
        <h1>Iniciar Sesión</h1>
    </div>

    <div class="form-outline mb-2">
        <div class="form-group">
            <label for="username" class="col-form-label">Usuario</label>
            <input type="text" name="username" id="username" placeholder="Ej: pepe@gmail.com" class="form-control" required='true'>
        </div>
        <label for="username" id="username-error" class="error text-danger"></label>
    </div>

    <div class="form-outline mb-2">
        <div class="form-group">
            <label for="password" class="col-form-label">Contraseña</label>
            <input type="password" name="password" id="password" placeholder="●●●●●●●●" class="form-control" required='true'>
        </div>
        <label for="password" id="password-error" class="error text-danger"></label>
    </div>

    <div class="d-flex justify-content-between mb-2">
        <div class="d-flex justify-content-center">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" name="remember" value="true" id="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>
        </div>

        <div class="text-center">
            <a href="#">¿Olvidó su contraseña?</a>
        </div>
    </div>

    <button id="btnLogin" class="btn btn-primary btn-block mb-2 w-100" type="Submit">
        Iniciar sesión
    </button>

    <div class="text-center">
        <p>¿No eres miembro? <a href="#">Regístrate</a></p>
    </div>
</form>

<script type="text/javascript" src="/chat/js/login.js"></script>