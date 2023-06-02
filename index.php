<?php 

require_once 'header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Index</title>
</head>
<body>
    <div class="container">
        <div class="text-center"><?= $_SESSION['flash'] ?? '' ?></div>
        <?php if(!empty($_SESSION['auth'])) { ?> 
            <div class="text-center">Текст только для авторизованных пользователей</div> 
        <?php } ?>
        <?php if(empty($_SESSION['auth'])) { ?> 
            <div class="text-center">Вы не вошли в аккаунт, <a href="login.php">авторизуйся</a></div> 
            <div class="text-center">Нет аккаунта? <a href="register.php">зарегестрируйся</a></div> 
        <?php } else { ?>
            <div class="text-center"><?= $_SESSION['login'] ?? '' ?></div> 
        <?php } ?>
        <?php if(!empty($_SESSION['auth'])) { ?> 
            <div class="text-center"><a href="logout.php">Выйти!</a></div> 
        <?php } ?>
    </div>
</body>
</html>