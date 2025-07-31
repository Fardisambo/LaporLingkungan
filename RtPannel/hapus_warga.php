<?php
include 'auth.php';
include '../db.php';

$id = $_GET['id'];
$conn->query("DELETE FROM warga WHERE id = $id");
header("Location: warga.php");
