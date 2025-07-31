<?php
session_start();
include '../db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: home.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    // Validate input
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username tidak boleh kosong";
    }
    
    if (empty($password)) {
        $errors[] = "Password tidak boleh kosong";
    }
    
    if (empty($role)) {
        $errors[] = "Role harus dipilih";
    }
    
    // Check if username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Username sudah digunakan";
    }
    
    // Check if email already exists (if email is provided)
    if (!empty($email)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = "Email sudah digunakan";
        }
    }
    
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User berhasil ditambahkan!";
        } else {
            $_SESSION['error_message'] = "Gagal menambahkan user: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = implode(", ", $errors);
    }
    
    header("Location: users.php");
    exit;
} else {
    header("Location: users.php");
    exit;
}
?> 