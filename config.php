<?php
// config.php
session_start();

$servername = "localhost";
$db_username = "urkbtgxv0tn9n";
$db_password = "zon92vfrjcxu";
$dbname = "db2zcjlsmwkc3e";

// Create database connection
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set your provided API key for the Gemini API
$API_KEY = "AIzaSyCS1xSEgDXOrtJuB4F1InEQOlP0nywNB3o";
?>
