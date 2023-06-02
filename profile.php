<?php

require_once 'header.php';
require_once 'connect.php';

function getAge( $birthday ){

	$diff = date( 'Ymd' ) - date( 'Ymd', strtotime($birthday) );

	return substr( $diff, 0, -4 );
}

function declension($number, array $data): string
{
    $rest = [$number % 10, $number % 100];
    
    if($rest[1] > 10 && $rest[1] < 20) {
        return $data[2];
    } elseif ($rest[0] > 1 && $rest[0] < 5) {
        return $data[1];
    } else if ($rest[0] == 1) {
        return $data[0];
    }
    return $data[2];
}

$flag = 0;

if(!empty($_GET['id'])) {
    
    $id =  $_GET['id'];

    $query = "SELECT * FROM auth WHERE id='$id'";
    $res = mysqli_query($link, $query);
    $user = mysqli_fetch_assoc($res);

    if(!empty($user)) {
        $name = $user['name'];
        $lastName = $user['lastName'];
        $surname = $user['surname'];
        $login = $user['login'];
        $age = getAge($user['dateBirth']);
        $country = $user['country'];
        
        $fullName = "$lastName $name $surname";

        $arr = ['год', 'года', 'лет'];
        $ageYear = $age . ' ' . declension($age, $arr);

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Profile</title>
</head>
<body>
    <div class="container">
        <?php if(!empty($user)) {?>
        <img src="./assets/img/default-avatar.jpg" class="rounded float-start" alt="avatar">
        <h1 class="h1 mb-5">Профиль пользователя</h1>
        <h2 class="h3 mb-2">
            <?= $fullName ?>
            <span class="badge bg-secondary"><?= $login ?></span>
        </h2>
        <p><span class="badge rounded-pill bg-light text-dark"><?= $ageYear ?></span></p> 

        <?php } else {?>

        <img src="./assets/img/default-avatar.jpg" class="rounded float-start" alt="avatar">
        <h1 class="h1 mb-5">Пользователь не найден</h1>
        
        <?php } ?>

    </div>
</body>
</html>