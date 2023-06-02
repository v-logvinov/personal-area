<?php 

require_once 'header.php';
require_once 'connect.php';

function debug(mixed $val): void
{
    echo '<pre>' . print_r($val) . '</pre>';
}

function validVal($value)
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}

$error = [];
$flag = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = validVal($_POST['login']);
    $pass = validVal($_POST['pass']);

    if(empty($login)) {
        $error['checkLog'] = 'Введите логин';
        $flag = 1;
    }

    if(empty($pass)) {
        $error['checkPass'] = 'Введите пароль!';
        $flag = 1;
    }

    $query = "SELECT
        auth.*,
        statuses.name AS status
    FROM
        auth
    LEFT JOIN statuses ON auth.status_id = statuses.id
    WHERE
        login = '$login'";

    $res = mysqli_query($link, $query);
    $user = mysqli_fetch_assoc($res);

    $id = $user['id'];
    
    if (empty($user)) {
        $error['checkLogPass'] = 'Пользователя с таким логином нет';
        $flag = 1;
    } else {
        $hash = $user['pass'];

        if(!password_verify($pass, $hash)) {
            $error['checkLogPass'] = 'Не верный логин или пароль';
            $flag = 1;
        } 

        if(empty($flag)) {
            $_SESSION['flash'] = 'Успешная авторизация!';
            $_SESSION['auth'] = true;
            $_SESSION['login'] = $login;
            $_SESSION['id'] = $id;
            $_SESSION['status'] = $user['status'];
            $_SESSION['status_id'] = $user['status_id'];
            header('Location: index.php');
        }
    }
}

if(empty($_SESSION['auth'])) {?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Auth</title>
</head>
<body>
    <div class="container">
        <form action="login.php" method="POST" class="form">
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Login</label>
                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="login" name="login" value="<?= $_POST['login'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $error['checkLog'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">Password</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" placeholder="password" name="pass" value="<?= $_POST['pass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $error['checkPass'] ?? '' ?></div>
            </div>
            <input class="btn btn-primary" type="submit">
        </form> 
        <div class="text-center">Нет аккаунта? <a href="register.php">зарегестрируйся</a></div> 
        <div class="invalid-feedback d-block"><?= $error['checkLogPass'] ?? '' ?></div>
   </div>
</body>
</html>

<?php } ?>