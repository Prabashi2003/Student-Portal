<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        header("Location: login.html?form=login&error=Username+and+password+are+required.");
        exit();
    }

    if(strpos($username, 'STU') === 0) {
        $stmt = $mysql->prepare("SELECT u.* FROM users u JOIN user_info ui ON u.id = ui.user_id WHERE ui.student_id = ?");
    }else{
        $stmt = $mysql->prepare("SELECT * FROM users WHERE username = ?");
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        //set cookie for 7 days
        if(!empty($_POST['remember'])) {
            setcookie('username', $user['username'], time() + (86400 * 7), "/"); // 86400 = 1 day
        } else {
            setcookie('username', '', time() - 3600, "/"); // delete cookie
        }


        header("Location: profile.php?login=success");
    } else {
        header("Location: login.html?form=login&error=1");
    }
    $stmt->close();
}else {
    die("Invalid request method.");
}

?>