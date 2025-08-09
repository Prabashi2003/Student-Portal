<!-- register.php -->
<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    //check username is already exists
    $stmt = $mysql->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultUsername = $stmt->get_result();
    $stmt->close();

    // Check if the email already exists
    $stmt = $mysql->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultEmail = $stmt->get_result();
    $stmt->close();


    if ($resultUsername->num_rows > 0) {
        header("Location: login.html?form=register&userExists=1");
        exit();
    }
    elseif ($resultEmail->num_rows > 0) {
        header("Location: login.html?form=register&emailExists=1");
        exit();
    }
    else {
        // Insert new user into the database
        $stmt = $mysql->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $userid = $stmt->insert_id;
            $stmt->close();

            $studentId = 'STU' . str_pad($userid, 3, '0', STR_PAD_LEFT);


            // Insert into user_info table
            $fullName = $username;
            $stmt = $mysql->prepare("INSERT INTO user_info (user_id, full_name, student_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $userid, $fullName, $studentId);
            $stmt->execute();
            $stmt->close();


            //send email
            $to = $email;
            $subject = "Registration Successful";
            $message = "Dear $username,\n\nYour registration was successful!. Your student ID is $studentId\n\nYou can now login using your Student ID as username and using your password.\n\nBest regards,\nStudent Portal Team";
            $headers = "From:noreply@localhost.com";
            mail($to, $subject, $message, $headers);
            
            $_SESSION['message'] = "Registration successful!";
            header("Location: login.html?form=login&success=1");
            exit();
        } else {
            die("Registration Failed. Please try again " . $stmt->error);
        }
    }
    $stmt->close();
}else{
    die("Invalid request method.");
}

?>