<?php
session_start();

require_once "Auth.php";
require_once "../Util.php";

$auth = new Auth();
$db_handle = new DBController();
$util = new Util();

require_once "../authCookieSessionValidate.php";


if ($isLoggedIn) {
    $util->redirect("pages/jornadas.php");
} else {
    $util->clearAuthCookie();
}

if (!empty($_POST["login"])) {
    $isAuthenticated = false;

    $username = $_POST["member_name"];
    $password = $_POST["member_password"];

    $user = $auth->getMemberByUsername($username);
    if (password_verify($password, $user[0]["password"])) {
        $isAuthenticated = true;
    }

    if ($isAuthenticated) {
        $_SESSION["member_id"] = $user[0]["id"];

        $rol_usuario = $user[0]["id_rol"];

        // Set Auth Cookies if 'Remember Me' checked
        if (!empty($_POST["remember"])) {
            setcookie("member_login", $username, $cookie_expiration_time);
            setcookie("id_user", $user[0]["id"], $cookie_expiration_time);
            setcookie("rol", $rol_usuario, $cookie_expiration_time);

            $random_password = $util->getToken(16);
            setcookie("random_password", $random_password, $cookie_expiration_time);

            $random_selector = $util->getToken(32);
            setcookie("random_selector", $random_selector, $cookie_expiration_time);

            $random_password_hash = password_hash($random_password, PASSWORD_DEFAULT);
            $random_selector_hash = password_hash($random_selector, PASSWORD_DEFAULT);

            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);

            // mark existing token as expired
            $userToken = $auth->getTokenByUsername($username, 0);
            if (!empty($userToken[0]["id"])) {
                $auth->markAsExpired($userToken[0]["id"]);
            }
            // Insert new token
            $auth->insertToken($username, $random_password_hash, $random_selector_hash, $expiry_date);
        } else {
            $util->clearAuthCookie();
        }
        // si es admin puede entrar sino no
        if ($rol_usuario == 1) {
            $util->redirect("pages/jornadas.php");
        } else {
            $util->clearAuthCookie();
        }
    } else {
        $message = "Usuario o contraseña incorrecto";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $rnd = time(); ?>
    <?php include "links_header.php"; ?>
    <link rel="manifest" href="manifest.json">
    <!-- ios support -->
    <link rel="apple-touch-icon" href="img/icons/72.png">
    <link rel="apple-touch-icon" href="img/icons/96.png">
    <link rel="apple-touch-icon" href="img/icons/128.png">
    <link rel="apple-touch-icon" href="img/icons/144.png">
    <link rel="apple-touch-icon" href="img/icons/152.png">
    <link rel="apple-touch-icon" href="img/icons/192.png">
    <link rel="apple-touch-icon" href="img/icons/384.png">
    <link rel="apple-touch-icon" href="img/icons/512.png">
    <link rel="icon" type="image/png" sizes="72x72" href="img/icons/72.png">
    <link rel="icon" type="image/png" sizes="96x96" href="img/icons/96.png">
    <link rel="icon" type="image/png" sizes="152x152" href="img/icons/128.png">
    <link rel="icon" type="image/png" sizes="192x192" href="img/icons/144.png">
    <link rel="icon" type="image/png" sizes="144x144" href="img/icons/152.png">
    <link rel="icon" type="image/png" sizes="128x128" href="img/icons/192.png">
    <link rel="icon" type="image/png" sizes="384x384" href="img/icons/384.png">
    <link rel="icon" type="image/png" sizes="512x512"  href="img/icons/512.png">
    <meta name="apple-mobile-web-app-status-bar" content="#1B426E">
    <meta name="theme-color" content="#1B426E">

</head>
<body style="background-color: #202C3B" class="d-flex justify-content-center vh-100 align-items-center">
<div class="container text-center">
    <img width="150px" src="img/logoApta.png" alt="">
    <form action="" method="post" id="frmLogin" autocomplete="off" class="mt-5">
        <input class="form-control form-control-lg bg-transparent mb-3 text-white" required name="member_name" type="text" placeholder="Correo"
               value="<?php if (isset($_COOKIE["member_login"])) {
                   echo $_COOKIE["member_login"];
               } ?>">
        <input required name="member_password" type="password" placeholder="Contraseña"
               value="<?php if (isset($_COOKIE["member_password"])) {
                   echo $_COOKIE["member_password"];
               } ?>" class="form-control form-control-lg bg-transparent text-white">
        <input class="d-none" type="checkbox" checked name="remember"
               id="remember" <?php if (isset($_COOKIE["member_login"])) { ?><?php } ?> />
        <div class="mt-4">
            <input type="submit" name="login" value="INICIAR SESIÓN"
                   class="btn py-3 btn-primary w-100">
            <div class="error-message letraPeq mt-3" style="color: #c70000;"><?php if (isset($message)) {
                    echo $message;
                } ?></div>
        </div>
    </form>
</div>
<script src="/js/app.js"></script>
</body>
</html>