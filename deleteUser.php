<?php 

require_once 'connect.php';

if(!empty($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM auth WHERE id='$id'";
    mysqli_query($link, $query);

    header('Location: admin.php');
}