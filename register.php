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

function generateSalt() {
    $salt = '';
    $saltLength = 8;
    
    for($i = 0; $i < $saltLength; $i++) {
        $salt .= chr(mt_rand(33, 126));
    }
    
    return $salt;
}

$arrError = [];
$patternLogin = '/[^a-zA-Z0-9]/';
$patternFullName = '/[^a-zA-Zа-яА-ЯЁё]/u';
$patternEmail = '/^[_a-z0-9-\+-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i';
$flag = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = validVal($_POST['name']);
    $lastName = validVal($_POST['lastName']);
    $surname = validVal($_POST['surname']);
    $login = validVal($_POST['login']);
    $pass = validVal($_POST['pass']);
    $conf = validVal($_POST['confirm']);
    $email = validVal($_POST['email']);
    $country = $_POST['country'];
    $dateBirth= preg_replace('/[^0-9\.]/u', '', trim($_POST['dateBirth']));
    $dateBirthArr = explode('.', $dateBirth);
    $dateRegister = date('Y-m-d h:m:s');

    if(empty($name)) {
        $arrError['name'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(preg_match($patternFullName, $name)) {
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
        if(preg_match($patternFullName, $lastName)) {
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
        if(preg_match($patternFullName, $surname)) {
            $arrError['surname'] = 'Отчество может содержать только буквы!';
            $flag = 1;
        } else {
            if(mb_strlen($surname) < 4 || mb_strlen($surname) > 16) {
                $arrError['surname'] = 'Длина отчества от 4 до 16 символов!';
                $flag = 1;
            }
        }
    }

    if(empty($login)) {
        $arrError['login'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(preg_match($patternLogin, $login)) {
            $arrError['login'] = 'Логин может содержать буквы латинского алфавита и цифры!';
            $flag = 1;
        } else {
            if(strlen($login) < 4 || strlen($login) > 10) {
                $arrError['login'] = 'Длина логина от 4 до 10 символов!';
                $flag = 1;
            }
        }
    }

    if(empty($pass)) {
        $arrError['pass'] = 'Это поле не может быть пустым!';
        $flag = 1;
    } else {
        if(strlen($pass) < 6 || strlen($pass) > 12) {
            $arrError['pass'] = 'Длина логина от 6 до 12 символов!';
            $flag = 1;
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

    $query = "SELECT * FROM auth WHERE login='$login'";
    $user = mysqli_fetch_assoc(mysqli_query($link, $query));
    
    if(!empty($user)) {
        $arrError['login'] = 'Логин занят, придумайте другой';
        $flag = 1;
    }

    if($pass !== $conf) {
        $arrError['pass'] = 'Пароли не совпадают';
        $flag = 1;
    }

    $pass = password_hash($pass, PASSWORD_DEFAULT);
    $conf = password_hash($conf, PASSWORD_DEFAULT);
    
    if(empty($flag)) {
        $query = "INSERT INTO auth SET name='$name', lastName='$lastName', surname='$surname', login='$login', pass='$pass', email='$email', dateBirth='$dateBirth', dateRegister='$dateRegister', country='$country', status_id='1'";
        mysqli_query($link, $query);
        
        $id = mysqli_insert_id($link);

        $_SESSION['flash'] = 'Успешная регистрация!';
        $_SESSION['id'] = $id;
        $_SESSION['auth'] = true;
        $_SESSION['login'] = $login;
        $_SESSION['status'] = 'user';

        header('Location: index.php');
        die();
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Register</title>
</head>
<body>
   <div class="container">
    <h1 class="h1 py-3">Регистрация</h1>
        <form method="post">
            <div class="mb-3 input-group d-flex justify-content-between">
                <div class="col-md-3">    
                    <label for="formGroupExampleInput" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Имя" name="name" value="<?= $_POST['name'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['name'] ?? '' ?></div>
                </div>
                <div class="col-md-4">
                    <label for="formGroupExampleInput" class="form-label">Фамилия</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Фамилия" name="lastName" value="<?= $_POST['lastName'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['lastName'] ?? '' ?></div>
                </div>
                <div class="col-md-4">
                    <label for="formGroupExampleInput" class="form-label">Отчество</label>
                    <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Отчество" name="surname" value="<?= $_POST['surname'] ?? '' ?>">
                    <div class="invalid-feedback d-block"><?= $arrError['surname'] ?? '' ?></div>
                </div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput" class="form-label">Логин</label>
                <input type="text" class="form-control" id="formGroupExampleInput" placeholder="Логин" name="login" value="<?= $_POST['login'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $arrError['login'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput2" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput2" placeholder="Пароль" name="pass" value="<?= $_POST['pass'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $arrError['pass'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput3" class="form-label">Повторите пароль</label>
                <input type="password" class="form-control" id="formGroupExampleInput3" placeholder="Повторите пароль" name="confirm" value="<?= $_POST['confirm'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $_SESSION['pass'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput4" class="form-label">Почта</label>
                <input type="text" class="form-control" id="formGroupExampleInput4" placeholder="Почта" name="email" value="<?= $_POST['email'] ?? '' ?>">
                <div class="invalid-feedback d-block"><?= $arrError['email'] ?? '' ?></div>
            </div>
            <div class="mb-3">
                <label for="formGroupExampleInput5" class="form-label">Дата рождения</label>
                <input type="text" class="form-control" id="formGroupExampleInput5" placeholder="ДД.ММ.ГГГГ" name="dateBirth" value="<?= $_POST['dateBirth'] ?? '' ?>">
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
        <?php if(empty($_SESSION['auth'])) {?>
        <div class="text-center">Уже есть аккаунт? <a href="login.php">авторизуйся</a></div> 
        <?php } ?>
   </div>
</body>
</html>