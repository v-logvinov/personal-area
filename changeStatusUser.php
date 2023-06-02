<?php 

require_once 'header.php';
require_once 'connect.php';

if(!empty($_GET['id']) && !empty($_GET['status_id'])) {
    $id = $_GET['id'];
    $status_id = $_GET['status_id'];

    $query = "UPDATE `auth` SET `status_id`='$status_id' WHERE id='$id'";
    mysqli_query($link, $query);

    header('Location: admin.php');
}