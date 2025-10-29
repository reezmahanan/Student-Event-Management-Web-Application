<?php
// Include database connection
require_once __DIR__ . '/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $student_id = $_POST['student_id'];
    $contact_number = $_POST['contact_number'];

    try {
        // Check if email already exists
        $query = "SELECT user_id FROM users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Email already registered!'); window.history.back();</script>";
            exit();
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO users (name, email, password, student_id, contact_number) 
                  VALUES (:name, :email, :password, :student_id, :contact_number)";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":student_id", $student_id);
        $stmt->bindParam(":contact_number", $contact_number);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please login.'); window.location.href='../login.php';</script>";
        } else {
            echo "<script>alert('Registration failed! Please try again.'); window.history.back();</script>";
        }

    } catch(PDOException $exception) {
        echo "<script>alert('Error: " . $exception->getMessage() . "'); window.history.back();</script>";
    }
}
?>