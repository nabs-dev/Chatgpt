<?php
require_once 'config.php';
if(isset($_SESSION['user_id'])){
    header("Location: chat.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
