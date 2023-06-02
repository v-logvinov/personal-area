<?php
	session_start();
	$_SESSION = null;
    
    $_SESSION['flash'] = 'Вы вышли из аккаунта!';

    header('Location: index.php')
?>