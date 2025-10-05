<?php
session_start();

require_once "Auth.php";
require_once "Util.php";

$auth = new Auth();
$db_handle = new DBController();
$util = new Util();

require_once "authCookieSessionValidate.php";

if ($isLoggedIn) {
    $util->redirect("pages/inicio_dashboard.php");
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
            $util->redirect("pages/inicio_dashboard.php");
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
</head>

<body style="background-color: #202C3B">

<div style="display: grid;justify-content: center;align-items: center;height: 100vh">
    <div style="width: 600px;background-color: #ffffff;border-radius: 10px;text-align: center;padding: 35px 0">
        <img width="200px" src="img/logos/logoAptaByN.png" alt="">
        <div style="padding: 20px 70px">
            <form action="" method="post" id="frmLogin" autocomplete="off">
                <input class="form-control mb-2" required name="member_name" type="text"
                       value="<?php if (isset($_COOKIE["member_login"])) {
                           echo $_COOKIE["member_login"];
                       } ?>" >
                <input required name="member_password" type="password"
                       value="<?php if (isset($_COOKIE["member_password"])) {
                           echo $_COOKIE["member_password"];
                       } ?>" class="form-control">
                <input class="d-none"  type="checkbox" checked name="remember"
                       id="remember" <?php if (isset($_COOKIE["member_login"])) { ?><?php } ?> />
                <div class="mt-4">
                    <input type="submit" name="login" value="INICIAR SESIÓN"
                           class="btn btn-lg btn-primary w-100">
                    <div class="error-message letraPeq mt-3" style="color: #c70000;"><?php if (isset($message)) {
                            echo $message;
                        } ?></div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/js/app.js"></script>
</body>
</body>
</html>