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

if(!empty($_SESSION['id'])) {
    $id = $_SESSION['id'];

    $querySelect = "SELECT
        `name`,
        `lastName`,
        `surname`,
        `email`,
        `dateBirth`,
        `country`
    FROM
        auth
    WHERE
        id='$id'";
    $res = mysqli_query($link, $querySelect);
    $user = mysqli_fetch_assoc($res);
}

$arrError = [];
$patternLogin = '/[^a-zA-Z0-9]/';
$patternFullName = '/^[A-zа-яА-ЯёЁ]+$/u';
$patternEmail = '/^[_a-z0-9-\+-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i';
$flag = 0;

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $dateBirth = preg_replace('/[^0-9\.]/u', '', trim($_POST['dateBirth']));
    $dateBirthArr = explode('.', $dateBirth);
    $country = $_POST['country'];

    if(empty($name)) {
        $arrError['name'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(!preg_match($patternFullName, $name)) {
            $arrError['name'] = 'Имя может содержать только буквы!';
            $flag = 1;
        } else {
            if(mb_strlen($name) < 2 || mb_strlen($name) > 12) {
                $arrError['name'] = 'Длина имени от 2 до 12 символов!';
                $flag = 1;
            }
        }
    }

    if(empty($lastName)) {
        $arrError['lastName'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(!preg_match($patternFullName, $lastName)) {
            $arrError['lastName'] = 'Фамилия может содержать только буквы!';
            $flag = 1;
        } else {
            if(mb_strlen($lastName) < 4 || mb_strlen($lastName) > 10) {
                $arrError['lastName'] = 'Длина фамилим от 3 до 16 символов!';
                $flag = 1;
            }
        }
    }

    if(empty($surname)) {
        $arrError['surname'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(!preg_match($patternFullName, $surname)) {
            $arrError['surname'] = 'Отчество может содержать только буквы!';
            $flag = 1;
        } else {
            if(mb_strlen($surname) < 4 || mb_strlen($surname) > 16) {
                $arrError['surname'] = 'Длина отчества от 4 до 16 символов!';
                $flag = 1;
            }
        }
    }

    if(empty($email)) {
        $arrError['email'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(!preg_match($patternEmail, $email)) {
            $arrError['email'] = 'Некорректный формат почты!';
            $flag = 1;
        }
    }
    
    if(empty($dateBirth)) {
        $arrError['dateBirth'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        $month = $dateBirthArr[1] ?? '';
        $day = $dateBirthArr[0] ?? '';
        $year = $dateBirthArr[2] ?? '';

        if(empty($month) || empty($day) || empty($year)) {
            $arrError['dateBirth'] = 'Некорректный формат даты!!';
            $flag = 1;
        } else {
            if(!checkdate($month, $day, $year)) {
                $arrError['dateBirth'] = 'Некорректный формат даты!!';
                $flag = 1;
            } else {
                $dateBirth = date('Y-m-d', strtotime($dateBirth));
    
                if(strtotime($dateBirth) > (time() - 3600 * 24 * 365 * 5)) {
                    $arrError['dateBirth'] = 'Регистрация разрешена от 5 лет!';
                    $flag = 1;
                }
    
                if(strtotime($dateBirth) <  (time() - 3600 * 24 * 365 * 100)) {
                    $arrError['dateBirth'] = 'Регистрация разрешена до 100 лет!';
                    $flag = 1;
                }
            } 
        }
       
    }

    if(empty($flag)) {
        $query = "UPDATE
            `auth`
        SET
            `name` = '$name',
            `lastName` = '$lastName',
            `surname` = '$surname',
            `email` = '$email',
            `dateBirth` = '$dateBirth',
            `country` = '$country'
        WHERE
            id = $id";


        $res = mysqli_query($link, $query); 
    }

    $user = $_POST;
}

if(!empty($user)) { ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Account</title>
</head>
<body>
    <div class="container">
    <h1 class="h1 py-3">Изменение данных</h1>
        <form method="post">
            <div class="mb-3 input-group d-flex justify-content-between">
                <div class="col-md-3">    
                    <label for="formGroupExampleInput" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Имя" name="name" value="<?= $user['name'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['name'] ?? '' ?></div>
                </div>
                <div class="col-md-4">
                    <label for="formGroupExampleInput" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Фамилия" name="lastName" value="<?= $user['lastName'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['lastName'] ?? '' ?></div>
                </div>
                <div class="col-md-4">
                    <label for="formGroupExampleInput" class="form-label">Отчество</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Отчество" name="surname" value="<?= $user['surname'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['surname'] ?? '' ?></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput4" class="form-label">Почта</label>
                <input type="text" class="form-control" id="formGroupExampleInput4" placeholder="Почта" name="email" value="<?= $user['email'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $arrError['email'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput5" class="form-label">Дата рождения</label>
                <input type="text" class="form-control" id="formGroupExampleInput5" placeholder="ДД.ММ.ГГГГ" name="dateBirth" value="<?= date('d.m.Y', strtotime($user['dateBirth'])) ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $arrError['dateBirth'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput6" class="form-label">Страна</label>
                <select name="country" id="formGroupExampleInput6" class="form-select" aria-label="Default select example">
                    <option value="russia">Россия</option>
                    <option value="kazakhstan">Казахстан</option>
                    <option value="turkey">Турция</option>
                    <option value="georgia">Грузия</option>
                    <option value="armenia">Армения</option>
                </select>
            </div>
            <input class="btn btn-primary" type="submit">
        </form> 
    </div>
</body>
</html>

<?php } ?>
