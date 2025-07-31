<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "lapor_lingkungan";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
