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

    $oldPass = validVal($_POST['oldPass']);
    $newPass = validVal($_POST['newPass']);
    $confNewPass = validVal($_POST['confNewPass']);


    if(empty($oldPass)) {
        $errorArr['oldPass'] = 'Введите текущий пароль!';
        $flag = 1;
    }

    if(empty($newPass)) {
        $errorArr['newPass'] = 'Введите новый пароль!';
        $flag = 1;
    }

    if(empty($confNewPass)) {
        $errorArr['confNewPass'] = 'Повторите новый пароль!';
        $flag = 1;
    }

    if(empty($flag) && ($oldPass === $newPass)) {
        $errorArr['newPass'] = 'Невозможно сменить на такой же пароль!';
        $flag = 1;
    }

    if(empty($flag) && ($confNewPass !== $newPass)) {
        $errorArr['confNewPass'] = 'Пароли не совпадают!';
        $flag = 1;
    }


    if(!empty($user)) {
        $hash = $user['pass'];

        if(!password_verify($oldPass, $hash)) {
            $errorArr['oldPass'] = 'Не верный пароль!';
            $flag = 1;
        }

        $newPassHash = password_hash($newPass, PASSWORD_DEFAULT);
        $query = "UPDATE `auth` SET `pass`='$newPassHash' WHERE id='$id'";

        if(empty($flag)) {
            $res = mysqli_query($link, $query);
            $errorArr['changePass'] = 'Пароль успешно изменен!';
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
        <h1 class="h1 py-3">Смена пароля</h1>
        <form method="POST" class="form">
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Текущий пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput" placeholder="Текущий пароль" name="oldPass" value="<?= $_POST['oldPass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $errorArr['oldPass'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">Новый пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" placeholder="Новый пароль" name="newPass" value="<?= $_POST['newPass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $errorArr['newPass'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">Повторите новый пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" placeholder="Повторите новый пароль" name="confNewPass" value="<?= $_POST['confNewPass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $errorArr['confNewPass'] ?? '' ?></div>
            </div>
            <input class="btn btn-primary" type="submit">
        </form> 
        <div class="valid-feedback d-block"><?= $errorArr['changePass'] ?? '' ?></div>
    </div>
</body>
</html>