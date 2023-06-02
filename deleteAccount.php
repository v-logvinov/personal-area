<?php 

require_once 'header.php';
require_once 'connect.php';

function validVal($value)
{
    $value = trim($value);
    $value = stripslashes($value);
    $value = strip_tags($value);
    $value = htmlspecialchars($value);

    return $value;
}

$errorArr = [];
$flag = 0;

if(!empty($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $query = "SELECT pass FROM auth WHERE id='$id'";

    $res = mysqli_query($link, $query);
    $user = mysqli_fetch_assoc($res);
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pass = validVal($_POST['pass']);


    if(empty($pass)) {
        $errorArr['pass'] = 'Введите текущий пароль!';
        $flag = 1;
    }


    if(!empty($user) && empty($flag)) {
        $hash = $user['pass'];

        if(!password_verify($pass, $hash)) {
            $errorArr['pass'] = 'Не верный пароль!';
            $flag = 1;
        }

        if(empty($flag)) {
            $query = "DELETE FROM auth WHERE id='$id'";
            $res = mysqli_query($link, $query);
            $_SESSION['auth'] = false;
            $_SESSION['id'] = '';
            $_SESSION['login'] = '';
            $_SESSION['flash'] = 'Аккаунт удален!';
            $errorArr['statusDelete'] = 'Аккаунт удален!';
            header('Location: index.php');
        }
       
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Change Password</title>
</head>
<body>
    <div class="container">
        <h1 class="h1 py-3">Удаление аккаунта</h1>
        <form method="POST" class="form">
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Введите пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput" placeholder="Введите пароль" name="pass" value="<?= $_POST['pass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $errorArr['pass'] ?? '' ?></div>
            </div>
            <input class="btn btn-primary" type="submit" value="Удалить">
        </form> 
        <div class="valid-feedback d-block"><?= $errorArr['statusDelete'] ?? '' ?></div>
    </div>
</body>
</html>