<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 'On');

?>
<?php if(!empty($_SESSION['auth'])) { ?> 
    <header class="bg-light">
        <div class="container">
            <nav id="navbar-example2" class="navbar navbar-light px-3">
                <a class="navbar-brand" href="index.php">Главная</a>
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link" href="account.php">Логин: <?= $_SESSION['login'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Статус: <?= $_SESSION['status'] ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">Все пользователи</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Выйти</a>
                    </li>
                    <li class="nav-item">
                        <?php if($_SESSION['status'] === 'admin') { ?>
                                    <a class="nav-link" href="admin.php">Админка</a>
                        <?php } ?>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Выпадающий список</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#scrollspyHeading3">Третий</a></li>
                            <li><a class="dropdown-item" href="#scrollspyHeading4">Четвертый</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#scrollspyHeading5">Пятый</a></li>
                        </ul>
                    </li> -->
                </ul>
            </nav>   
        </div>         
    </header>
<?php } ?>

<!-- 
<div class="container">
                    <div class="text-center py-5">
                        <span class="px-5 text-light">Логин: <?= $_SESSION['login'] ?></span>
                        <span class="px-5 text-light">Статус: <?= $_SESSION['status'] ?></span> 
                        <a class="text-danger px-5" href="logout.php">Выйти</a>
                        <?php if($_SESSION['status'] === 'admin') { ?>
                            <a class="text-info" href="admin.php">Админка</a>
                        <?php } ?>
                    </div>
            </div> -->