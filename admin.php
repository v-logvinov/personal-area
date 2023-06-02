<?php 

require_once 'header.php';
require_once 'connect.php';

$query = "SELECT
        auth.*,
        statuses.name AS status
    FROM
        auth
    LEFT JOIN statuses ON
        auth.status_id = statuses.id";
$res = mysqli_query($link, $query);

$users = [];

while($user = mysqli_fetch_assoc($res)) {
    $users[] = $user;
}

if($_SESSION['auth'] && $_SESSION['status'] === 'admin') {?>

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
    
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#id</th>
                <th scope="col">Логин</th>
                <th scope="col">Статус</th>
                <th scope="col">Удаление</th>
                <th scope="col">Смена статуса</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $key => $user) {
                $colorTr = 'success';
                $flag = 'admin';
                $status_id = 2;

                if($user['status_id'] === '2') {
                    $colorTr = 'danger';
                    $flag = 'user';
                    $status_id = 1;
                }
                ?>
                <tr class="table-<?= $colorTr ?>">
                    <th scope="row"><?= $key + 1 ?></th>
                    <td><?= $user['login'] ?></td>
                    <td><?= $user['status'] ?></td>
                    <td><a href="deleteUser.php?id=<?= $user['id'] ?>">Удалить пользователя</a></td>
                    <td><a href="changeStatusUser.php?id=<?= $user['id'] ?>&status_id=<?= $status_id ?>">Установить статус <?= $flag ?></a></td>
                </tr>
            <?php } ?>
        </tbody>    
    </table>
    <?php } else { ?>
        <h3 class="h3 mb-5">Пользователи не найдены</h3>
    <?php } ?>
    </div>
</body>
</html>

<?php } else {?>

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>No access</title>
</head>
<body>
    <div class="container">
        <h1 class="h1 mb-5">Данная страница доступна только админам</h1>
        <a href="index.php">Вернуться на главную</a>
    </div>
</body>
<?php } ?>