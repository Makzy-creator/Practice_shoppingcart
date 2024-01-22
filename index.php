<?php
session_start();
// Include functions and connect to the database using PDO MySQL
include 'functions.php';
$pdo = pdo_connect_mysql();

//basic routing
// Page is set to home (home.php) by default, so when the visitor visits, that will be the page they see.
$page = isset($_GET['page']) && file_exists($_GET['page'] . '.php') ? $_GET['page'] : 'home';
// The basic routing method used above checks if the GET request variable ($_GET['page']) exists. If not, the default page will be set to the home page, whereas if it exists, it will be the requested page.

// Include and show the requested page
include $page . '.php';
?>

