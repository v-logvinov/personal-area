<?php 

require_once 'header.php';
require_once 'connect.php';


$query = "SELECT `id`, `name`, `lastName`, `surname` FROM `auth`";
$res = mysqli_query($link, $query);

$users = [];

while($user = mysqli_fetch_assoc($res)) {
    $users[] = $user;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Users</title>
</head>
<body>
    <div class="container">
    <h1 class="h1 mb-5">Пользователи зарегестрированные на сайте</h1>

    <?php if(!empty($users)) {?>
    
    <div class="list-group">
        <?php foreach($users as $user) {?>
        <a href="profile.php?id=<?= $user['id'] ?>" class="list-group-item list-group-item-action"><?= $user['lastName'] . ' ' . $user['name'] . ' ' . $user['surname'] ?></a>
        <?php } ?>
    </div>

    <?php } else { ?>
        <h3 class="h3 mb-5">Пользователи не найдены</h3>
    <?php } ?>
    </div>
</body>
</html>